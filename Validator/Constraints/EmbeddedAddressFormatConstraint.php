<?php

namespace Daften\Bundle\AddressingBundle\Validator\Constraints;

use CommerceGuys\Addressing\AddressFormat\FieldOverrides;
use CommerceGuys\Addressing\Validator\Constraints\AddressFormatConstraint;
use CommerceGuys\Addressing\Validator\Constraints\AddressFormatConstraintValidator;

/**
 * @Annotation
 *
 * @codeCoverageIgnore
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class EmbeddedAddressFormatConstraint extends AddressFormatConstraint
{
  public function __construct($options = null, $fieldOverrides = [])
  {
    $this->fieldOverrides = new FieldOverrides($fieldOverrides);

    parent::__construct($options);
  }

  /**
   * {@inheritdoc}
   */
  public function getTargets()
  {
    return self::PROPERTY_CONSTRAINT;
  }
}
