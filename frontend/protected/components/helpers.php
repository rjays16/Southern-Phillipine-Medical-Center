<?php

/**
 * helpers.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016,
 */

/**
 * Global helpers
 */


/**
 * Define the default values for a model when it is passed on a form
 */
function defineDefaultsOf($variable,$array){
    foreach ($array as $attribute => $value){
        if($variable->hasAttribute($attribute)){
            if($variable->$attribute==null){
                $variable->$attribute = $value;
            }
        }
    }
}

function showDump($var){
    echo '<pre>',CVarDumper::dump($var),'</pre>';
}

/**
 * Retrieves the
 */
if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function env($key, $default = null)
    {
        return isset($_ENV[$key]) ? $_ENV[$key] : $default;
        
//        $value = getenv($key);
//        if ($value === false) {
//            return $default;
//        }
//        switch (strtolower($value)) {
//            case 'true':
//            case '(true)':
//                return true;
//            case 'false':
//            case '(false)':
//                return false;
//            case 'empty':
//            case '(empty)':
//                return '';
//            case 'null':
//            case '(null)':
//                return null;
//        }
//
//        if (strlen($value) > 1 && substr($value, 0, 1) == '"' && substr($value, -1)  == '"') {
//            return substr($value, 1, -1);
//        }
//
//        return $value;
    }
}
