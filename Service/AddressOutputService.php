<?php

namespace Daften\Bundle\AddressingBundle\Service;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Locale;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Daften\Bundle\AddressingBundle\FieldHelper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class AddressOutputService.
 *
 * A service that will provide several output possibilities for Addresses.
 */
class AddressOutputService
{
    /**
     * @var CountryRepositoryInterface
     */
    protected $countryRepository;

    /**
     * @var AddressFormatRepositoryInterface
     */
    protected $addressFormatRepository;

    /**
     * @var SubdivisionRepositoryInterface
     */
    protected $subdivisionRepository;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        AddressFormatRepositoryInterface $addressFormatRepository,
        SubdivisionRepositoryInterface $subdivisionRepository,
        RequestStack $requestStack
    ) {
        $this->countryRepository = $countryRepository;
        $this->addressFormatRepository = $addressFormatRepository;
        $this->subdivisionRepository = $subdivisionRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * The string which is the rendered address with spans for each property value.
     *
     * @param AddressEmbeddable $addressEmbeddable
     *   The address to be rendered.
     *
     * @return string
     */
    public function getAddressDefault(AddressEmbeddable $addressEmbeddable): string
    {
        $country_code = $addressEmbeddable->getCountryCode();
        $address_format = $this->addressFormatRepository->get($country_code);

        if (Locale::matchCandidates($address_format->getLocale(), $addressEmbeddable->getLocale())) {
            $format_string = '%country' . "\n" . $address_format->getLocalFormat();
        }
        else {
            $format_string = $address_format->getFormat() . "\n" . '%country';
        }

        $replacements = $this->getAddressReplacements($addressEmbeddable);
        // Put a span around each replacement value.
        foreach ($replacements as $field => $value) {
            if (!$value) {
                continue;
            }

            $property = FieldHelper::getPropertyName($field);
            $class = str_replace('_', '-', $property);

            $replacements[$field] = "<span class=\"$class\">".$replacements[$field].'</span>';
        }

        // Replace the placeholders and replace \n with linebreaks.
        $content = FieldHelper::replacePlaceholders($format_string, $replacements);
        $content = nl2br($content, FALSE);

        return $content;
    }

    /**
     * The values used to render the address as a plain address.
     *
     * @param AddressEmbeddable $addressEmbeddable
     *   The address to be rendered.
     *
     * @return array
     */
    public function getAddressPlain(AddressEmbeddable $addressEmbeddable): array
    {
        $country_code = $addressEmbeddable->getCountryCode();
        $countries = $this->countryRepository->getList();
        $address_format = $this->addressFormatRepository->get($country_code);
        $values = $this->getValuesForPlain($addressEmbeddable, $address_format);

        return [
            'given_name' => $values['givenName'],
            'additional_name' => $values['additionalName'],
            'family_name' => $values['familyName'],
            'organization' => $values['organization'],
            'address_line1' => $values['addressLine1'],
            'address_line2' => $values['addressLine2'],
            'postal_code' => $values['postalCode'],
            'sorting_code' => $values['sortingCode'],
            'administrative_area' => $values['administrativeArea'],
            'locality' => $values['locality'],
            'dependent_locality' => $values['dependentLocality'],
            'country' => [
                'code' => $country_code,
                'name' => $countries[$country_code],
            ],
        ];
    }

    /**
     * The string which is the rendered address with spans for each property value.
     *
     * @param AddressEmbeddable $addressEmbeddable
     *   The address to be rendered.
     *
     * @return string
     */
    public function getAddressInline(AddressEmbeddable $addressEmbeddable): string
    {
        $country_code = $addressEmbeddable->getCountryCode();
        $address_format = $this->addressFormatRepository->get($country_code);

        if (Locale::matchCandidates($address_format->getLocale(), $addressEmbeddable->getLocale())) {
            $format_string = '%country' . "\n" . $address_format->getLocalFormat();
        }
        else {
            $format_string = $address_format->getFormat() . "\n" . '%country';
        }

        $replacements = $this->getAddressReplacements($addressEmbeddable);

        // Replace the placeholders and replace \n with linebreaks.
        $content = FieldHelper::replacePlaceholders($format_string, $replacements);
        $content = preg_replace('/\n/', ', ', $content);

        return $content;
    }

    /**
     * A helper function that gets the replacements for address values.
     *
     * @param AddressEmbeddable $addressEmbeddable
     *   The address to be rendered.
     *
     * @return array
     */
    protected function getAddressReplacements(AddressEmbeddable $addressEmbeddable): array
    {
        $country_code = $addressEmbeddable->getCountryCode();
        $countries = $this->countryRepository->getList();
        $address_format = $this->addressFormatRepository->get($country_code);
        $values = $this->getValuesForDefault($addressEmbeddable, $address_format);

        $replacements['country'] = htmlspecialchars($countries[$country_code], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        foreach ($address_format->getUsedFields() as $field) {
            $property = FieldHelper::getPropertyName($field);
            $class = str_replace('_', '-', $property);

            $replacements[$field] = htmlspecialchars($values[$field], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        return $replacements;
    }

    /**
     * Gets the address values used for rendering.
     *
     * @param AddressEmbeddable $addressEmbeddable
     *   The address.
     * @param AddressFormat $address_format
     *   The address format.
     *
     * @return array
     *   The values, keyed by address field.
     */
    protected function getValuesForDefault(AddressEmbeddable $addressEmbeddable, AddressFormat $address_format) {
        $values = [];
        foreach (AddressField::getAll() as $field) {
            $getter = 'get' . ucfirst($field);
            $values[$field] = $addressEmbeddable->$getter();
        }

        $original_values = [];
        $subdivision_fields = $address_format->getUsedSubdivisionFields();
        $parents = [];
        foreach ($subdivision_fields as $index => $field) {
            if (empty($values[$field])) {
                // This level is empty, so there can be no sublevels.
                break;
            }
            $parents[] = $index ? $original_values[$subdivision_fields[$index - 1]] : $addressEmbeddable->getCountryCode();
            $subdivision = $this->subdivisionRepository->get($values[$field], $parents);
            if (!$subdivision) {
                break;
            }

            // Remember the original value so that it can be used for $parents.
            $original_values[$field] = $values[$field];
            // Replace the value with the expected code.
            $use_local_name = Locale::matchCandidates($address->getLocale(), $subdivision->getLocale());
            $values[$field] = $use_local_name ? $subdivision->getLocalCode() : $subdivision->getCode();
            if (!$subdivision->hasChildren()) {
                // The current subdivision has no children, stop.
                break;
            }
        }

        return $values;
    }

    /**
     * Gets the address values used for rendering.
     *
     * @param AddressEmbeddable $addressEmbeddable
     *   The address.
     * @param AddressFormat $address_format
     *   The address format.
     *
     * @return array
     *   The values, keyed by address field.
     */
    protected function getValuesForPlain(AddressEmbeddable $addressEmbeddable, AddressFormat $address_format) {
        $values = [];
        foreach (AddressField::getAll() as $field) {
            $getter = 'get' . ucfirst($field);
            $values[$field] = $addressEmbeddable->$getter();
        }

        $original_values = [];
        $usedFields = $address_format->getUsedFields();
        $unused_fields = array_diff_key($values, array_flip($usedFields));
        foreach ($unused_fields as $name => $value) {
            $values[$name] = NULL;
        }
        $subdivision_fields = $address_format->getUsedSubdivisionFields();

        $parents = [];
        foreach ($subdivision_fields as $index => $field) {
            $value = $values[$field];
            // The template needs access to both the subdivision code and name.
            $values[$field] = [
                'code' => $value,
                'name' => '',
            ];

            if (empty($value)) {
                // This level is empty, so there can be no sublevels.
                break;
            }
            $parents[] = $index ? $original_values[$subdivision_fields[$index - 1]] : $addressEmbeddable->getCountryCode();
            $subdivision = $this->subdivisionRepository->get($value, $parents);
            if (!$subdivision) {
                break;
            }

            // Remember the original value so that it can be used for $parents.
            $original_values[$field] = $value;
            // Replace the value with the expected code.
            if (Locale::matchCandidates($addressEmbeddable->getLocale(), $subdivision->getLocale())) {
                $values[$field] = [
                    'code' => $subdivision->getLocalCode(),
                    'name' => $subdivision->getLocalName(),
                ];
            }
            else {
                $values[$field] = [
                    'code' => $subdivision->getCode(),
                    'name' => $subdivision->getName(),
                ];
            }

            if (!$subdivision->hasChildren()) {
                // The current subdivision has no children, stop.
                break;
            }
        }

        return $values;
    }
}
