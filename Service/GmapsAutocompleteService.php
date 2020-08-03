<?php

namespace Daften\Bundle\AddressingBundle\Service;

use CommerceGuys\Addressing\Repository\AddressFormatRepositoryInterface;
use CommerceGuys\Addressing\Repository\SubdivisionRepositoryInterface;
use CommerceGuys\Intl\Country\CountryRepositoryInterface;
use Daften\Bundle\AddressingBundle\Entity\AddressEmbeddable;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        AddressFormatRepositoryInterface $addressFormatRepository,
        SubdivisionRepositoryInterface $subdivisionRepository,
        string $gmapsApiKey
    ) {
        $this->countryRepository = $countryRepository;
        $this->addressFormatRepository = $addressFormatRepository;
        $this->subdivisionRepository = $subdivisionRepository;
        $this->gmapsApiKey = $gmapsApiKey;
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

    public function addressAutocompleteDefault(AddressEmbeddable $address) {
        $countries = $this->countryRepository->getAll();
        $address_default = implode(', ', array_filter([
            $address->getRecipient(),
            $address->getAddressLine1(),
            $address->getAddressLine2(),
            $address->getPostalCode().' '.$address->getLocality(),
            $countries[$address->getCountryCode()]->getName(),
        ]));
    }

}
