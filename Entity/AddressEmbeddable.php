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
    protected $locale;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $recipient;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $organization;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $addressLine1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $addressLine2;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $postalCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sortingCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $locality;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $dependentLocality;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $administrativeArea;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    protected $countryCode;

    /**
     * @return string
     */
    public function getLocale(): ?string
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
    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient(string $recipient = null): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getOrganization(): ?string
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
    public function getAddressLine1(): ?string
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
    public function getAddressLine2(): ?string
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
    public function getPostalCode(): ?string
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
    public function getSortingCode(): ?string
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
    public function getLocality(): ?string
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
    public function getDependentLocality(): ?string
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
    public function getAdministrativeArea(): ?string
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
    public function getCountryCode(): ?string
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
