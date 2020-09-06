<?php

namespace Daften\Bundle\AddressingBundle\Service;

use CommerceGuys\Addressing\AddressFormat\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Country\CountryRepositoryInterface;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class GmapsAutocompleteService.
 *
 * Provides the possibility to inject the GMaps API key and the toString method for an address to prepopulate an
 * autocomplete address field.
 */
class GmapsAutocompleteService
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
     * @var string
     */
    protected $gmapsApiKey;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        AddressFormatRepositoryInterface $addressFormatRepository,
        SubdivisionRepositoryInterface $subdivisionRepository,
        string $gmapsApiKey,
        RequestStack $requestStack
    ) {
        $this->countryRepository = $countryRepository;
        $this->addressFormatRepository = $addressFormatRepository;
        $this->subdivisionRepository = $subdivisionRepository;
        $this->gmapsApiKey = $gmapsApiKey;
        $this->requestStack = $requestStack;
    }

    /**
     * @return string
     */
    public function getGmapsApiKey(): string
    {
        return $this->gmapsApiKey;
    }

    /**
     * @param string $gmapsApiKey
     */
    public function setGmapsApiKey(string $gmapsApiKey): void
    {
        $this->gmapsApiKey = $gmapsApiKey;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->requestStack->getCurrentRequest()->getLocale();
    }

    public function addressAutocompleteDefault(AddressEmbeddable $address): string
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
}
