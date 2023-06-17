<?php
namespace SegHis\models;

/**
 * @package SegHis\models
 * @author Nick B. Alcala 3-18-2016
 */
class LoggedInUser
{

    const PERMISSION_SYSAD = 'System_Admin';
    const PERMISSION_ALL = '_a_0_all';

    public static $user;

    private function __construct() {
    }

    /**
     * Determine if user has permission.
     *
     * @param $permission
     * @return bool
     * @see include/care_api_classes/class_acl.php
     */
    public static function isPermittedTo($permission)
    {
        static::setUser();
        $permissions = static::$user['permission'];
        if ((strpos($permissions, self::PERMISSION_SYSAD) !== false) ||
            (strpos($permissions, self::PERMISSION_ALL) !== false)) {
            return true;
        }

        if (is_array($permission)) {
            foreach ($permission as $_permission) {
                if (static::isPermittedTo($_permission)) {
                    return true;
                }
            }
            return false;
        } else {
            return strpos($permissions, $permission) !== false;
        }
    }

    protected static function setUser()
    {
        if(!static::$user) {
            static::$user = \User::model()->findByAttributes(array(
                'login_id' => $_SESSION['sess_login_userid']
            ));
        }
    }

}