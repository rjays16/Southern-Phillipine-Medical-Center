<?php

/**
 * MessageRouter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\jnj\services;

use React\Socket\ConnectionInterface;
use SegHEIRS\modules\integrations\hl7\exceptions\HL7Exception;
use SegHEIRS\modules\integrations\hl7\exceptions\HL7FileTransferException;
use SegHEIRS\modules\integrations\hl7\exceptions\HL7InvalidOperationException;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;
use HL7\Message;
use SegHEIRS\modules\integrations\hl7\segments\MSH;
//use SegHEIRS\modules\integrations\hl7\services\MessageLogger;
use SegHEIRS\modules\integrations\modules\jnj\factories\messages\ACKFactory;
use SegHEIRS\modules\integrations\events\AckHl7Event;

/**
 *
 * Description of MessageRouter
 *
 */

class SocketMessageRouter
{
    private $connection = null;
    
    public function __construct() {
        
    }
    
    public function setSocketConnection(ConnectionInterface $conn) {
        $this->connection = $conn;
    }
    
    /**
     * @param Message $message
     *
     * @throws HL7FileTransferException
     */
    public function processMessage(Message $message)
    {
        $segments = $message->getSegmentsByName('MSH');
        $msh = MSH::createFromSegment($segments[0]);

        try {
//            (new MSHValidator())->validate($msh);

            $messageType = $msh->getMessageType();
            try {
//                if ($messageType[0] === 'ORM' && $messageType[1] === 'O01') {
//                    $service = new ORMO01Handler();
//                    $service->processMessage($message);
//                } else
                if ($messageType[0] === 'ORU' && $messageType[1] === 'R01') {
                    $service = new ORUR01Handler();
                    $service->processMessage($message);
                } elseif (is_string($messageType) && $messageType === 'ACK') {
//                    if ($this->connection) {
//                        $ack = new ACKFactory();
//                        $ackmsg = $ack->createSuccess($message);
//                        $this->connection->write($ackmsg->toString());
                    
//                        fwrite(STDERR, "Acknowledgement Received!");

                        $emitter = \Yii::app()->emitter;
                        $emitter->emit(new AckHl7Event());                        
//                    }
                } else {
                    throw new HL7InvalidOperationException('Message type cannot be handled at this time');
                }

                /**
                 * Log the processed message
                 */
//                $logger = new MessageLogger();
//                try {
//                    $logger->log($message);
//                } catch (\Exception $e) {
//
//                }
            } catch (HL7Exception $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new HL7Exception($e->getMessage());
            }
        } catch (HL7Exception $e) {

            $factory = new ACKFactory();
            $ack = $factory->createError(
                $message,
                $e->getCode(),
                $e->getMessage(),
                HL7::encodeValues(array(
                    $e->getErrorSegment(),
                    $e->getErrorSegmentSequence(),
                    $e->getErrorFieldPosition(),
                    $e->getErrorFieldRepetition(),
                    $e->getErrorComponentNumber(),
                    $e->getErrorSubComponentNumber()
                ))
            );

//            $writer = new IncomingErrorMessageWriter();
//            $writer->write(
//                $message,
//                $ack,
//                str_replace('.'.$module->messageFileExt, '', $message->getFileInfo()->getFilename())
//            );

        } 
        
//        finally {

            // Delete message
//            $path = $message->getFileInfo()->getRealPath();
//            @unlink($path);
//            @unlink(str_replace(
//                '.'.$module->messageFileExt,
//                '.'.$module->semaphoreFileExt,
//                $path
//            ));
            
//        }


    }

}
