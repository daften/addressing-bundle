<?php

namespace Daften\Bundle\AddressingBundle\Service;

use CommerceGuys\Addressing\AddressFormat\AddressField;
use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;
use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_intersect_key;

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
     * @return string
     */
    public function getLocale(): string
    {
        return $this->requestStack->getCurrentRequest()->getLocale();
    }

    /**
     * @return array
     */
    public function getAddressPlain(AddressEmbeddable $addressEmbeddable): array
    {
        $country_code = $addressEmbeddable->getCountryCode();
        $countries = $this->countryRepository->getList();
        $address_format = $this->addressFormatRepository->get($country_code);
        $values = $this->getValues($addressEmbeddable, $address_format);

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

    public function addressAutocompleteDefault(AddressEmbeddable $addressEmbeddable): string
    {
        $countries = $this->countryRepository->getAll();

        return trim(implode(
            ', ',
            array_filter(
                [
                    $address->getGivenName().' '.$address->getAdditionalName().' '.$address->getFamilyName(),
                    $address->getAddressLine1(),
                    $address->getAddressLine2(),
                    $address->getPostalCode().' '.$address->getLocality(),
                    !empty($address->getCountryCode()) ? $countries[$address->getCountryCode()]->getName() : '',
                ]
            )
        ), ' ,');
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
    protected function getValues(AddressEmbeddable $addressEmbeddable, AddressFormat $address_format) {
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
