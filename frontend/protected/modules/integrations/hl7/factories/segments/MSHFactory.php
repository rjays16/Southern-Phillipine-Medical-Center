<?php

/**
 * MshFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\segments;

use SegHEIRS\modules\integrations\hl7\segments\MSH;
use Yii;

/**
 *
 * Description of MshFactory
 *
 */

class MSHFactory
{

    /**
     *
     */
    public function create()
    {
        $msh = new MSH();                        
        $msh
            ->setSendingApplication(Yii::app()->params['APP_NAME'])
            ->setSendingFacility(Yii::app()->params['FACILITY_NAME'])
            ->setDateTimeOfMessage(date('YmdHis'))
            ->setVersionId('2.3')
            ->setMessageControlId($this->generateControlId());
        return $msh;
    }

    /**
     * @return string
     */
    public function generateControlId()
    {
        return uniqid('', true);
    }
}
