<?php

/**
 * ADTA01Factory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\messages;

use HL7\Message;
use SegHis\modules\poc\models\PocOrder;
use SegHEIRS\modules\integrations\hl7\factories\segments\EVNFactory;
use SegHEIRS\modules\integrations\hl7\factories\segments\MSHFactory;
use SegHEIRS\modules\integrations\hl7\factories\segments\PIDFactory;
use SegHEIRS\modules\integrations\hl7\factories\segments\PV1Factory;

/**
 *
 * Description of ADTA01Factory
 *
 */

class ADTA01Factory
{

    /**
     * @param PocOrder $order
     *
     * @return $message
     */
    public function create(PocOrder $order)
    {
        $message = new Message();
        
        $encounter = $order->encounterNr;
        
        $msh = new MSHFactory();
        $message->addSegment($msh->create()->setMessageType('ADT^A01'));
        
        $evn = new EVNFactory();
        $message->addSegment($evn->create('A04'));
        
        $pid = new PIDFactory();
        $message->addSegment($pid->create($encounter->person));
                
        $pvf = new PV1Factory();
        $message->addSegment($pvf->create($encounter, $order));        
                
        return $message;
    }

}
