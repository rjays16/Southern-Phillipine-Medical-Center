<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SegHEIRS\modules\integrations\modules\jnj\events\handlers;

use SegHEIRS\modules\integrations\events\CbgOrderedEvent;
use SegHEIRS\modules\integrations\hl7\services\SocketMessageWriter;
use SegHEIRS\modules\integrations\modules\jnj\factories\messages\ADTA01Factory;
use SegHEIRS\modules\integrations\modules\jnj\factories\messages\ADTA03Factory;
use SegHis\modules\poc\models\PocOrder;
use SegHis\modules\poc\models\Hl7MessageLog;
use Yii;

/**
 * Description of CbgOrderedMessageHandler
 *
 * @author Bong
 */
class CbgOrderedMessageHandler {
    //put your code here
    
    /**
     * @param EventInterface $event
     */
    public function handle($event)
    {                
        if ($event instanceof CbgOrderedEvent) {                                    
            /** @var PocHl7Module $module */
            $module = Yii::app()->getModule('integrations')->getModule('jnj');                                   
            if (!$module->enableIntegration) {
                return;
            }
            try {                                
                // Code to handle HL7 CBG Order Message ...                                                
                $socket = new SocketMessageWriter($module->receivingIPAddress, $module->receivingIPPort);
                
                // Check what order has been issued .... 
                $pocOrder = $event->getPocOrderRequest();
                $adtFactory = ($pocOrder->order_type == PocOrder::START) ? new ADTA01Factory() : new ADTA03Factory();
                $message = $adtFactory->create($pocOrder);
                
                // Log HL7 message and send to 3rd party MLLP socket server ...
                Hl7MessageLog::logHl7Message($message);                
                $socket->write($message);                
                
            } catch (\Exception $e) {
                Yii::log('JnJ Integration Error -- ' . $e->getMessage(), \CLogger::LEVEL_ERROR);
            }            
        }
    }     
}
