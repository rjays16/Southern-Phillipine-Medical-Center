<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SegHEIRS\modules\integrations\events;

use SegHEIRS\components\event\Event;
use SegHEIRS\components\event\EventNames;
use HL7\Message;
use SegHis\modules\poc\models\PocOrder;
use SegHis\modules\poc\models\PocOrderDetail;

/**
 * Description of CbgOrderedEvent
 *
 * @author Bong
 */
class CbgOrderedEvent extends Event {
    //put your code here
    private $poc_order_h;
    private $poc_order_d;
    private $message;
    
    public function __construct(PocOrder $orderh, PocOrderDetail $orderd) {                
        $this->poc_order_h = $orderh;
        $this->poc_order_d = $orderd;
    }
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return EventNames::EVENT_CBG_ORDERED;
    }    

    public function getPocOrderRequest() {
        return $this->poc_order_h;
    }
    
    public function getPocOrderItems() {
        return $this->poc_order_d;
    }
    
    public function getMessage() {
        $msgStr = "MSH|^~\&|HIS|SPMC|Bio-Connect|SPMC|20181030150114||ORM^O01|MSGID20181030150114|P|2.3
PID|||1467541||MA^MARIELEA^DOMINGO^Ms.||00000000|F|||123 MC ARTGUR H-WAY MATINA^Davao City^8000^Philippines|||||||
PV1||O|OB1^OB WARD 1^||||2342^Jones^Bob|||OP|||||||||2|||||||||||||||||||||||||20181030150114|
ORC|NW|20181030150114
OBR|1|20181030150114||41653-7^CAPILLARY BLOOD GLUCOSE (CBG) TEST^L|||20181030150114";
        
        $this->message = new Message($msgStr);
        return $this->message;
    }
    
    public function getAdtMessage() {
        $msgStr = "MSH|^~\&|HIS|SPMC|Bio-Connect|SPMC|20180709133000||ADT^A01|MSGID20180709133000|P|2.3
EVN|A01|20180709133000
PID|1||N123456||WOMAN^FIRSTNAME||19800905|F|^^^^^|WB|ADDRESS|E18|TELNO|||||H00005285522
PV1|1|I|DOO^DOO^83||||MWIFE^Care^DEFG^L^^^|SMITH^A^JANE^M^^^||MLC||||MA|||MWIFE^Care^DEFG^L^^^|IN||NHS|||||||||||||||||||SPMC||HIS|||20180709133000";
        
        $this->message = new Message($msgStr);
        return $this->message;        
    }
}
