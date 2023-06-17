<?php

/**
 * Url.php
 *
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Helpers\Http;

use Segworks\HIS\Helpers\Helper;

/**
 * Helper class for generating URLs for SegHIS
 *
 * @package Segworks.HIS.Helpers
 */
class Url extends Helper
{
    /**
     * Constructs a Yii URL.
     *
     * @param string $route the controller and the action (e.g. article/read)
     * @param array $params list of GET parameters (name=>value). Both the name and value will be URL-encoded.
     * If the name is '#', the corresponding value will be treated as an anchor
     * and will be appended at the end of the URL.
     * @param string $ampersand the token separating name-value pairs in the URL. Defaults to '&'.
     * @return string the constructed URL
     */
    public static function createFrontendUrl($route,$params=array(),$ampersand='&')
    {
        return Yii::app()->createUrl($route, $params, $ampersand);
    }
}
