<?php

namespace Daften\Bundle\AddressingBundle\Validator\Constraints;

use CommerceGuys\Addressing\Validator\Constraints\AddressFormatConstraint;
use CommerceGuys\Addressing\Validator\Constraints\AddressFormatConstraintValidator;

/**
 * @Annotation
 *
 * @codeCoverageIgnore
 */
class EmbeddedAddressFormatConstraint extends AddressFormatConstraint
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return AddressFormatConstraintValidator::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
