<?php

/**
 * HL7.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\helpers;

/**
 *
 * Description of HL7
 *
 */

class HL7
{

    const DEFAULT_FIELD_SEPARATOR = '|';
    const DEFAULT_COMPONENT_SEPARATOR = '^';
    const DEFAULT_REPETITION_SEPARATOR = '~';
    const DEFAULT_ESCAPE_CHARACTER = '\\';
    const DEFAULT_SUBCOMPONENT_SEPARATOR = '&';
    const DEFAULT_HL7_VERSION = '2.8';

    const ENCODE_VALUES_COMPONENT = 'component';
    const ENCODE_VALUES_REPETITION = 'repetititon';
    const ENCODE_VALUES_SUBCOMPONENT = 'subcomponent';

    /**
     * Encodes an array of values to the proper encoding format based on the
     * given encoding mode.
     *
     * @param array $values
     * @param string $mode
     * @param bool $isRaw
     *
     * @return string
     *
     */
    public static function encodeValues(array $values, $mode = self::ENCODE_VALUES_COMPONENT, $isRaw = false)
    {
        $sep = self::DEFAULT_COMPONENT_SEPARATOR;
        if ($mode == self::ENCODE_VALUES_REPETITION) {
            $sep = self::DEFAULT_REPETITION_SEPARATOR;
        } elseif ($mode == self::ENCODE_VALUES_SUBCOMPONENT) {
            $sep = self::DEFAULT_SUBCOMPONENT_SEPARATOR;
        }

        // Remove trailing null values at the end of the array
        for ($i=sizeof($values)-1; $i >=0; $i--) {
            if ($values[$i] === null) {
                unset($values[$i]);
            } else {
                break;
            }
        }

        return implode($sep, array_map(function(&$value) use ($isRaw) {
            return $isRaw ? $value : HL7::encode($value);
        }, $values));

    }

    /**
     * @param string $value
     *
     * @return string
     */
    public static function encode($value)
    {
        return strtr($value, array(
            self::DEFAULT_FIELD_SEPARATOR => '\\F\\',
            self::DEFAULT_COMPONENT_SEPARATOR => '\\S\\',
            self::DEFAULT_SUBCOMPONENT_SEPARATOR => '\\T\\',
            self::DEFAULT_REPETITION_SEPARATOR => '\\R\\',
            self::DEFAULT_ESCAPE_CHARACTER => '\\E\\',
            PHP_EOL => '\\X0D\\\\X0A\\'
        ));
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public static function decode($value)
    {
        return strtr($value, array(
            '\\F\\' => self::DEFAULT_FIELD_SEPARATOR,
            '\\S\\' => self::DEFAULT_COMPONENT_SEPARATOR,
            '\\T\\' => self::DEFAULT_SUBCOMPONENT_SEPARATOR,
            '\\R\\' => self::DEFAULT_REPETITION_SEPARATOR,
            '\\E\\' => self::DEFAULT_ESCAPE_CHARACTER,
            '\\X0D\\\\X0A\\' => PHP_EOL,
            '\\.br\\' => PHP_EOL
        ));
    }
}
