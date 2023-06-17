<?php

namespace SegHEIRS\modules\integrations\modules\jnj\factories\messages;

use HL7\Message;
use SegHis\modules\poc\models\PocOrder;
use SegHEIRS\modules\integrations\hl7\factories\segments\EVNFactory;
use SegHEIRS\modules\integrations\modules\jnj\factories\segments\MSHFactory;
use SegHEIRS\modules\integrations\hl7\factories\segments\PIDFactory;
use SegHEIRS\modules\integrations\hl7\factories\segments\PV1Factory;

/**
 * ADTA03Factory - Message Factory for Discharge of Patient
 *
 * @author Bong
 */
class ADTA03Factory {
   
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
        $message->addSegment($msh->create()->setMessageType('ADT^A03'));
        
        $evn = new EVNFactory();
        $message->addSegment($evn->create('A03'));
        
        $pid = new PIDFactory();
        $message->addSegment($pid->create($encounter, $order));
                
        $pvf = new PV1Factory();
        $message->addSegment($pvf->create($encounter, $order));        
                
        return $message;
    }    
    
}
