<?php

/**
 * ACKFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\jnj\factories\messages;

use SegHEIRS\modules\integrations\hl7\CodeTable;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;
use HL7\Message;
use SegHEIRS\modules\integrations\hl7\segments\ERR;
use SegHEIRS\modules\integrations\hl7\segments\MSA;
use SegHEIRS\modules\integrations\hl7\segments\MSH;
use SegHEIRS\modules\integrations\modules\jnj\factories\segments\MSHFactory;

/**
 *
 * The ACKFactory for J and J glucose meter integration
 *
 */
class ACKFactory
{

    /**
     * @param Message $for
     *
     * @return Message
     */
    public function createSuccess(Message $for)
    {
        // Extract MSH from $for message
        $segments = $for->getSegmentsByName('MSH');
        $forMSH = MSH::createFromSegment($segments[0]);

        $message = new Message();
        
        $mshfactory = new MSHFactory();
        $message->addSegment(
            $mshfactory->create()->setMessageType('ACK')
        );

        $ack = new MSA();
        $ack->setAcknowledgmentCode(CodeTable::ACK_CODE_ACCEPT);
        $ack->setMessageControlId($forMSH->getMessageControlId());

        $message->addSegment($ack);
        return $message;
    }    
    
    /**
     * @param Message $for
     * @param string $hl7ErrorCode
     * @param string $errorMessage
     * @param string|null $errorLocation
     * @param string|null $appErrorCode
     * @param string|null|string $severity
     *
     * @return Message
     */
    public function createError($for, $hl7ErrorCode, $errorMessage, $errorLocation = null,  $appErrorCode = null, $severity = CodeTable::SEVERITY_ERROR)
    {
        // Extract MSH from $for message
        $segments = $for->getSegmentsByName('MSH');
        $forMSH = MSH::createFromSegment($segments[0]);

        $message = new Message();
        
        $mshfactory = new MSHFactory();
        $message->addSegment(
            $mshfactory->create()->setMessageType('ACK')
        );

        $ack = new MSA();
        $ack->setAcknowledgmentCode(CodeTable::ACK_CODE_ERROR);
        $ack->setMessageControlId($forMSH->getMessageControlId());
        $ack->setTextMessage($errorMessage); // DEPRECATED
        $ack->setErrorCondition($errorMessage); // DEPRECATED

        $message->addSegment($ack);

        $err = new ERR();
        $err->setHl7ErrorCode(HL7::encodeValues(array(
            $hl7ErrorCode,
            $errorMessage
        )));
        $err->setUserMessage($errorMessage);

        if ($severity) {
            $err->setSeverity($severity);
        }

        if ($errorLocation) {
            $err->setErrorLocation($errorLocation);
        }

        if ($appErrorCode) {
            $err->setApplicationErrorCode($appErrorCode);
        }

        $message->addSegment($err);

        return $message;
    }
    
    

}
