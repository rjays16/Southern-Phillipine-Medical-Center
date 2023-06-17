<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SegHEIRS\modules\integrations\events;

use SegHEIRS\components\event\Event;
use SegHEIRS\components\event\EventNames;

/**
 * Description of AckHl7Event
 *
 * @author Bong
 */
class AckHl7Event extends Event {
    //put your code here
    private $messageControlId;
    
    public function __construct($msg_id) {
        $this->messageControlId = $msg_id;        
    }
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return EventNames::EVENT_HL7_ACKNOWLEDGEMENT;
    }
    
    public function setMessageId($msg_id) {
        $this->messageControlId = $msg_id;
    }

    public function getMessageId() {
        return $this->messageControlId;
    }
}
