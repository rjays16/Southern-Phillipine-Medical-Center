<?php

/**
 * PidFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\jnj\factories\segments;

use DateTime;
use Person;
use SegHEIRS\modules\integrations\hl7\segments\PID;

/**
 *
 * Description of PidFactory
 *
 */

class PIDFactory
{

    /**
     * @return Pid
     */
    public function create(Person $p)
    {
        $pid = new PID();                

//        $p = $patient->p;
        // Trim first 2 digits of SPIN
//        $pid->setPatientIdentifierList(substr($p->pid, 2));
        $pid->setPatientIdentifierList($p->pid);

        $pid->setPatientName(
            '',
            $p->getFullname()
        );
                  
        if ($p->date_birth) {
            $dt = new DateTime($p->date_birth);
            if ($dt) {
                $pid->setDateTimeOfBirth($dt->format('Ymd'));
            }
        }

        if ($p->sex) {
            $pid->setSex($p->sex);
        } else {
            $pid->setSex('U');
        }        
                         
        // Put company information in patient alias
        $pid->setPatientAddress(
            trim($p->getFullAddress()),
            '',
            $p->municipality ? trim($p->municipality->mun_name) : null,
            $p->municipality->parent ? trim($p->municipality->parent->prov_name) : null,
            $p->municipality ? trim($p->municipality->zipcode) : null,
            'PH'
        );             

        return $pid;
    }

}
