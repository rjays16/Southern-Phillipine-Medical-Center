<?php

/**
 * Formatter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Helpers;

/**
 * The Formatter helper is responsible for converting values from and to various
 * formats. The helper loads the configuration currently set in the
 * config_global table.
 *
 * @package Segworks.HIS.
 */
class Formatter extends Helper
{

    /**
     * Initialization routine. Loads all possible values from care_config_global
     */
    protected function init() {
        $this->config = array(
            'dateFormat' => 'm/d/Y',
            'timeFormat' => 'h:i:s A',
            'datetimeFormat' => 'Y/m/d h:i:s A'
        );
    }

    /**
     * Formats the value as a date.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see dateFormat
     */
    public static function formatDate($value)
    {
        return date(self::getConfig('dateFormat'),self::normalizeDateValue($value));
    }

    /**
     * Formats the value as a time.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see timeFormat
     */
    public static function formatTime($value)
    {
        return date(self::getConfig('timeFormat'), self::normalizeDateValue($value));
    }

    /**
     * Formats the value as a date and time.
     * @param mixed $value the value to be formatted
     * @return string the formatted result
     * @see datetimeFormat
     */
    public static function formatDatetime($value)
    {
        return date(self::getConfig('datetimeFormat'),self::normalizeDateValue($value));
    }

    /**
     * Normalize a time string expression to get its equivalent value in the
     * Unix timestamp format.
     *
     * @param mixed $time
     * @return int
     */
    protected static function normalizeDateValue($time)
    {
        if(is_string($time))
        {
            if(ctype_digit($time) || ($time{0}=='-' && ctype_digit(substr($time, 1))))
                return (int)$time;
            else
                return strtotime($time);
        }
        return (int)$time;
    }
}