<?php

/**
 * Helper.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Helpers;

/**
 * Base class for all helper class for the HIS application.
 *
 * @package segworks.his
 */
abstract class Helper extends \Segworks\HIS\Base\Singleton
{
    protected $config = array();


    /**
     * Sets a configuration parameter for the helper. This allows the
     * application to modify the configuration of the helper at runtime.
     *
     * @param string $key The name of the parameter to be set
     * @param mixed $value Value to be assigned to the parameter
     */
    public static function setConfig($key, $value)
    {
        $instance = self::getInstance();
        $instance->config[$key] = $value;
    }

    /**
     * Retrieves the current value of the helper's configuration parameter.
     *
     * @param string $key The name of the parameter to be retrieved
     * @param mixed $default The default value returned if no configuration has been set
     * @return mixed
     */
    public static function getConfig($key, $default=null)
    {
        $instance = self::getInstance();
        return isset($instance->config[$key]) ? $instance->config[$key] : $default;
    }
}