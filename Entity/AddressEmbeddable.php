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
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $locale;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $givenName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $additionalName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $familyName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $organization;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $addressLine1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $addressLine2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $postalCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $sortingCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $locality;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $dependentLocality;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected string $administrativeArea;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    protected string $countryCode;

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

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale = null): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getAdditionalName(): string
    {
        return $this->additionalName;
    }

    /**
     * @param string $additionalName
     */
    public function setAdditionalName(string $additionalName = null): void
    {
        $this->additionalName = $additionalName;
    }

    /**
     * @return string
     */
    public function getGivenName(): string
    {
        return $this->givenName;
    }

    /**
     * @param string $givenName
     */
    public function setGivenName(string $givenName = null): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    /**
     * @param string $familyName
     */
    public function setFamilyName(string $familyName = null): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     */
    public function setOrganization(string $organization = null): void
    {
        $this->organization = $organization;
    }

    /**
     * @return string
     */
    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1($addressLine1 = null): void
    {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return string
     */
    public function getAddressLine2(): string
    {
        return $this->addressLine2;
    }

    /**
     * @param string $addressLine2
     */
    public function setAddressLine2(string $addressLine2 = null): void
    {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode = null): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getSortingCode(): string
    {
        return $this->sortingCode;
    }

    /**
     * @param string $sortingCode
     */
    public function setSortingCode(string $sortingCode = null): void
    {
        $this->sortingCode = $sortingCode;
    }

    /**
     * @return string
     */
    public function getLocality(): string
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality(string $locality = null): void
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getDependentLocality(): string
    {
        return $this->dependentLocality;
    }

    /**
     * @param string $dependentLocality
     */
    public function setDependentLocality(string $dependentLocality = null): void
    {
        $this->dependentLocality = $dependentLocality;
    }

    /**
     * @return string
     */
    public function getAdministrativeArea(): string
    {
        return $this->administrativeArea;
    }

    /**
     * @param string $administrativeArea
     */
    public function setAdministrativeArea(string $administrativeArea = null): void
    {
        $this->administrativeArea = $administrativeArea;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode(string $countryCode = NULL): void
    {
        $this->countryCode = $countryCode;
    }

}
