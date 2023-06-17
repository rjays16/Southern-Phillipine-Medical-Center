<?php
/**
 * Created by PhpStorm.
 * User: segworks-bonix
 * Date: 3/9/2019
 * Time: 9:38 PM
 */

require_once __DIR__.'/lib/telemed/TelemedActiveResource.php';

class Telemed extends TelemedActiveResource
{
    public static $inst;

    /**
     * Call this method to get singleton
     * @var String $personelID
     * @return Telemed
     */
    public static function instance()
    {
        if (self::$inst == null) {
//            echo "<pre>";
            $root_path = '/../../../';
            require_once __DIR__ . '/../../../include/inc_environment_global.php';
//            var_dump($_SESSION);
            self::$inst = new Telemed($_SESSION['sess_user_personell_nr'], $_SESSION['sess_login_userid']);
        }
        return self::$inst;
    }
}