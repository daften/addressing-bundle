<?php

namespace Daften\Bundle\AddressingBundle\Validator\Constraints;

use CommerceGuys\Addressing\AddressFormat\AddressFormat;
use CommerceGuys\Addressing\Validator\Constraints\AddressFormatConstraintValidator;

class EmbeddedAddressFormatConstraintValidator extends AddressFormatConstraintValidator
{
    /**
     * Adds a violation on the good path.
     *
     * @param string $field          The field.
     * @param string        $message        The error message.
     * @param mixed         $invalidValue   The invalid, validated value.
     * @param AddressFormat $addressFormat The address format.
     */
    protected function addViolation($field, $message, $invalidValue, AddressFormat $addressFormat): void
    {
        $this->context->buildViolation($message)
            ->atPath($field)
            ->setInvalidValue($invalidValue)
            ->addViolation();
    }
}
