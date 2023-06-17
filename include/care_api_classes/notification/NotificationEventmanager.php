<?php
/**
 * Created by PhpStorm.
 * User: Deboner Dulos <deboner.dulos.wise@gmail.com>
 * Date: 3/4/2019
 * Time: 3:44 AM
 */

require_once __DIR__ .'/../../inc_init_main.php';


class NotificationEventmanager
{

    // private static $ehr;


    // do not delete this function
    // this will be called every request instance to validate user requesting
    // public  function validateRequstUser($personelID)
    // {
    //     require_once __DIR__ . '/controller/User.php';
    //     $user = new User();
    //     $res = $user->getOne("
    //         select * from care_personell cp 
    //         where cp.nr = :nr 
    //         and cp.status not in ('expired','deleted')
    //         and job_function_title in ('Anesthesia nurse','Nurse', 'Admitting doctor','Attending doctor','Consulting doctor', 'Doctor', 'Doctor On Call')
    //     ", array( 'nr' => $personelID));
    //     return $res ? true : false;
    // }


}
