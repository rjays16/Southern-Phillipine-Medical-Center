<?php

/**
 * Singleton.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Base;

/**
 * Description
 *
 */
abstract class Singleton
{

    private static $_instances = array();

    /**
     * Constructor
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * This method gets invoked upon the instantiation of the singleton instance.
     * Ideally, this should be overridden by subclasses that want to execute
     * additional constructor logic.
     *
     */
    protected function init() { }


    /**
     * Description
     * @return Singleton
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            // $args = func_get_args();
            // $reflection = new ReflectionClass($class);
            // self::$_instances[$class] = $reflection->newInstanceArgs($args);
            self::$_instances[$class] = new $class;
        }
        return self::$_instances[$class];
    }

    /**
     *
     * @param mixed $instance
     * @throws ErrorException
     */
    public static function setInstance($instance)
    {
        $instanceClass = get_class($instance);
        $singletonClass = get_called_class();
        if ($instanceClass !== $singletonClass) {
            throw new ErrorException(strtr('Cannot assign object of type `{instance}` to single instance for class `{singleton}`', array(
                '{instance}' => $instanceClass,
                '{singleton}' => $singletonClass
            )));
        }

        self::$_instances[$singletonClass] = $instance;
    }

    /**
     *
     */
    final private function __clone() { }
}