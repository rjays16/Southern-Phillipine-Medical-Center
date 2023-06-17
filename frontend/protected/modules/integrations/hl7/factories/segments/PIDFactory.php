<?php

/**
 * PidFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\segments;

use DateTime;
use Encounter;
use SegHEIRS\modules\integrations\hl7\segments\PID;
use SegHis\modules\poc\models\PocOrder;

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
    public function create(Encounter $encounter, PocOrder $order = null)
    {
        $pid = new PID();

        $p = $encounter->person;
        
        $pid->setPatientIdentifierList($p->pid);
        $pid->setPatientId($encounter->encounter_nr);
        $pid->setPatientName(
            trim($p->name_last),
            trim($p->name_first) . ' ' . trim($p->name_middle),
            trim($p->suffix) ?: null
        );

//        $pid->setMotherMaidenName();

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
        
        $pid->setPatientAccountNumber($encounter->encounter_nr);

        return $pid;
    }

}
