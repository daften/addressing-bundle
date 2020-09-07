<?php

namespace Daften\Bundle\AddressingBundle;

use CommerceGuys\Addressing\AddressFormat\AddressField;

/**
 * Provides property names and helper functions for AddressEmbeddable values.
 */
class FieldHelper {

  /**
   * Gets the property name matching the given AddressField value.
   *
   * @param string $field
   *   An AddressField value.
   *
   * @return string
   *   The property name.
   */
  public static function getPropertyName($field) {
    $property_mapping = [
      AddressField::ADMINISTRATIVE_AREA => 'administrative_area',
      AddressField::LOCALITY => 'locality',
      AddressField::DEPENDENT_LOCALITY => 'dependent_locality',
      AddressField::POSTAL_CODE => 'postal_code',
      AddressField::SORTING_CODE => 'sorting_code',
      AddressField::ADDRESS_LINE1 => 'address_line1',
      AddressField::ADDRESS_LINE2 => 'address_line2',
      AddressField::ORGANIZATION => 'organization',
      AddressField::GIVEN_NAME => 'given_name',
      AddressField::ADDITIONAL_NAME => 'additional_name',
      AddressField::FAMILY_NAME => 'family_name',
    ];

    return isset($property_mapping[$field]) ? $property_mapping[$field] : NULL;
  }

    /**
     * Replaces placeholders in the given string.
     *
     * @param string $string
     *   The string containing the placeholders.
     * @param array $replacements
     *   An array of replacements keyed by their placeholders.
     *
     * @return string
     *   The processed string.
     */
    public static function replacePlaceholders($string, array $replacements) {
        // Make sure the replacements don't have any unneeded newlines.
        $replacements = array_map('trim', $replacements);
        // Prepend each key with '%' if that wasn't the case yet.
        foreach ($replacements as $key => $value) {
            if ('%' === substr($key, 0, 1)) {
                continue;
            }
            $replacements['%'.$key] = $value;
            unset($replacements[$key]);
        }
        $string = strtr($string, $replacements);
        // Remove noise caused by empty placeholders.
        $lines = explode("\n", $string);
        foreach ($lines as $index => $line) {
            // Remove leading punctuation, excess whitespace.
            $line = trim(preg_replace('/^[-,]+/', '', $line, 1));
            $line = preg_replace('/\s\s+/', ' ', $line);
            $lines[$index] = $line;
        }
        // Remove empty lines.
        $lines = array_filter($lines);

        return implode("\n", $lines);
    }

}
