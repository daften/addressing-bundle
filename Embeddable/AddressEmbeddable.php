<?php

namespace Daften\Bundle\AddressingBundle\Embeddable;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class AddressEmbeddable extends \CommerceGuys\Addressing\Model\Address
{
    /**
     * @ORM\Column(type="string", length=2)
     */
    protected $countryCode;

    /**
     * @ORM\Column(type="string")
     */
    protected $administrativeArea;

    /**
     * @ORM\Column(type="string")
     */
    protected $locality;

    /**
     * @ORM\Column(type="string")
     */
    protected $dependentLocality;

    /**
     * @ORM\Column(type="string")
     */
    protected $postalCode;

    /**
     * @ORM\Column(type="string")
     */
    protected $sortingCode;

    /**
     * @ORM\Column(type="string")
     */
    protected $addressLine1;

    /**
     * @ORM\Column(type="string")
     */
    protected $addressLine2;

    /**
     * @ORM\Column(type="string")
     */
    protected $organization;

    /**
     * @ORM\Column(type="string")
     */
    protected $recipient;

    /**
     * @ORM\Column(type="string")
     */
    protected $locale;
}
