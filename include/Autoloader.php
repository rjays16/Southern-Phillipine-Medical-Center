<?php

/**
 * Autoloader.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Base;

/**
 * Autoloader
 *
 */
class Autoloader
{

    const BASE_NS = 'Segworks\\HIS';

    public static $classMap = array();

    /**
     * Class autoload loader.
     * This method is invoked automatically when PHP sees an unknown class.
     * The method will attempt to include the class file according to the following procedure:
     *
     */
    public static function autoload($className)
    {
        global $config;

        if (strpos($className, self::BASE_NS.'\\') === 0) {
            /**
              * Handles HIS API Classes
              */

            /**
             * Retrieve the subnamespace of the class by first removing the base
             * namespace and then finding the next '\' character,
             */

            $relativeNameSpace = str_replace(self::BASE_NS.'\\', '', $className);
            $nextToken = strpos($relativeNameSpace, '\\');
            $subNameSpace = substr($relativeNameSpace, 0, $nextToken);
            $path = substr($relativeNameSpace, strlen($subNameSpace) + 1);
            if (isset(self::$classMap['@'.$subNameSpace])) {
                $classPath = self::$classMap['@'.$subNameSpace].str_replace('\\', '/', $path) . '.php';
            } else {
                return;
            }
        } elseif (strpos($className, '\\') !== false) {
            /**
             * @todo Handle PS4-4 compliant namespaces
             */
            return;

            // $classPath = self::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            // if ($classPath === false || !is_file($classPath)) {
            //     return;
            // }
        } else {
            // No namespace
            return;
        }

        include($classPath);

        if ($config['debug'] && !class_exists($className, false) && !interface_exists($className, false))
        {
            throw new UnknownClassException("Unable to find '$className' in path: $classPath");
        }

    }

}