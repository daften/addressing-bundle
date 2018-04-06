<?php

namespace Daften\Bundle\AddressingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class AddressEmbeddable extends \CommerceGuys\Addressing\Model\Address
{
    /**
     * @ORM\Column(type="string")
     */
    protected $locale;

    /**
     * @ORM\Column(type="string")
     */
    protected $recipient;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $organization;

    /**
     * @ORM\Column(type="string")
     */
    protected $addressLine1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $addressLine2;

    /**
     * @ORM\Column(type="string")
     */
    protected $postalCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $sortingCode;

    /**
     * @ORM\Column(type="string")
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
     * @ORM\Column(type="string", length=2)
     */
    protected $countryCode;

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient($recipient): void
    {
        $this->recipient = $recipient;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     */
    public function setAddressLine1($addressLine1): void
    {
        $this->addressLine1 = $addressLine1;
    }

    /**
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * @param string $addressLine2
     */
    public function setAddressLine2($addressLine2): void
    {
        $this->addressLine2 = $addressLine2;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getSortingCode()
    {
        return $this->sortingCode;
    }

    /**
     * @param string $sortingCode
     */
    public function setSortingCode($sortingCode): void
    {
        $this->sortingCode = $sortingCode;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality): void
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getDependentLocality()
    {
        return $this->dependentLocality;
    }

    /**
     * @param string $dependentLocality
     */
    public function setDependentLocality($dependentLocality): void
    {
        $this->dependentLocality = $dependentLocality;
    }

    /**
     * @return string
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * @param string $administrativeArea
     */
    public function setAdministrativeArea($administrativeArea): void
    {
        $this->administrativeArea = $administrativeArea;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     */
    public function setCountryCode($countryCode): void
    {
        $this->countryCode = $countryCode;
    }


}
