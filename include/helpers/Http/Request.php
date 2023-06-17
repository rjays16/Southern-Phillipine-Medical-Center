<?php

/**
 * Request.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014. Segworks Technologies Corporation
 */

namespace Segworks\HIS\Helpers\Http;

use Segworks\HIS\Helpers\Helper;

/**
 * Helper class for
 *
 * @package Segworks.HIS.Helpers
 */
class Request extends Helper
{
    /**
     * Returns TRUE if the current request is an AJAX request
     */
    public static function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest';
    }
}
