<?php

/**
 * MshFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\jnj\factories\segments;

use SegHEIRS\modules\integrations\hl7\factories\segments\MSHFactory as BaseMSHFactory;
use SegHEIRS\modules\integrations\modules\jnj\JnjModule;
use Yii;

/**
 *
 * Description of MshFactory
 *
 */

class MSHFactory extends BaseMSHFactory
{

    /**
     *
     */
    public function create()
    {
        $msh = parent::create();

        /** @var JnjModule $jnjModule */
        $jnjModule = Yii::app()->getModule('integrations')->getModule('jnj');
        $msh
            ->setSendingApplication('HIS')
            ->setSendingFacility('SPMC')
            ->setReceivingApplication($jnjModule->receivingApplication)
            ->setReceivingFacility($jnjModule->receivingFacility);
        return $msh;
    }

}
