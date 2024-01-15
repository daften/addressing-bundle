<?php

namespace Daften\Bundle\AddressingBundle\Entity;

use CommerceGuys\Addressing\Address;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class AddressEmbeddable extends Address
{
    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $locale = 'und';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $givenName = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $additionalName = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $familyName = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $organization = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $addressLine1 = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $addressLine2 = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $addressLine3 = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $postalCode = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $sortingCode = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $locality = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $dependentLocality = '';

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected string $administrativeArea = '';

    /**
     * @ORM\Column(type="string", length=2, nullable=false)
     */
    protected string $countryCode = '';

    public function __toString(): string
    {
        return implode(', ', array_filter([
            implode(' ', \array_filter([
                $this->getGivenName(),
                $this->getAdditionalName(),
                $this->getFamilyName(),
            ])),
            $this->getAddressLine1(),
            $this->getAddressLine2(),
            implode(' ', \array_filter([
                $this->getPostalCode(),
                $this->getLocality(),
            ])),
            $this->getCountryCode(),
        ]));
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function setAdditionalName(?string $additionalName): void
    {
        $this->additionalName = $additionalName ?? '';
    }

    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName ?? '';
    }

    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName ?? '';
    }

    public function setOrganization(?string $organization): void
    {
        $this->organization = $organization ?? '';
    }

    public function setAddressLine1(?string $addressLine1): void
    {
        $this->addressLine1 = $addressLine1 ?? '';
    }

    public function setAddressLine2(?string $addressLine2): void
    {
        $this->addressLine2 = $addressLine2 ?? '';
    }

    public function setAddressLine3(?string $addressLine3): void
    {
        $this->addressLine3 = $addressLine3 ?? '';
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode ?? '';
    }

    public function setSortingCode(?string $sortingCode): void
    {
        $this->sortingCode = $sortingCode ?? '';
    }

    public function setLocality(?string $locality): void
    {
        $this->locality = $locality ?? '';
    }

    public function setDependentLocality(?string $dependentLocality): void
    {
        $this->dependentLocality = $dependentLocality ?? '';
    }

    public function setAdministrativeArea(?string $administrativeArea): void
    {
        $this->administrativeArea = $administrativeArea ?? '';
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }
}
