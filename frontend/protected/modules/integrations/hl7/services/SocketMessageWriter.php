<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SegHEIRS\modules\integrations\hl7\services;

use HL7\Message;
use SegHEIRS\modules\integrations\hl7\Socket;
use SegHEIRS\modules\integrations\hl7\exceptions\SocketException;
use SegHEIRS\modules\integrations\hl7\factories\messages\ACKFactory;
use SegHis\modules\poc\models\Hl7MessageLog;

//use React\EventLoop\Factory;
//use React\Socket\Connector;
//use React\Socket\ConnectionInterface;

/**
 * Description of SocketMessageWriter
 *
 * @author Bong
 */
class SocketMessageWriter {
    //put your code here
    private $socketPort;
    private $socketIpAddress;
    
    /**
     * SocketMessageWriter constructor.
     */
    public function __construct($socketIp, $socketPort)
    {
        $this->socketPort = $socketPort;
        $this->socketIpAddress = $socketIp;
    }    
    
    /**
     *
     * @param Message $message
     * @param string $fileName
     *
     * @throws SocketException
     */
    public function write(Message $message)
    {        
        try {            
            $socket = new Socket();
            $socket->setAddress($this->socketIpAddress);
            $socket->setPort($this->socketPort);
                        
            if ($socket->connect()) {
                if (!$socket->isBlocking()) {
                    $socket->setBlocking(true);                
                }
                $socket->write($message->toString());                                
                $response = $socket->receive();                                
                if ($response != "") {
                    // Log acknowledgement message ...                                                            
                    $message = new Message($response);
                    Hl7MessageLog::logAcknowledgement($message);                                                            
                }
                else {
                    \CVarDumper::dump("NO Acknowledgement Received!");
                }
            }
            else {
                throw new SocketException("Cannot connect to socket in ".$this->socketIpAddress.":".$this->socketPort);
            }
        } catch (SocketException $ex) {
            \CVarDumper::dump("ERROR: ".$ex->getMessage());
        }        
    }    
    
    public function writeAck(Message $message) {
        
        try {
            $factory = new ACKFactory();
            $ack = $factory->createSuccess($message);
                                             
            $socket = new Socket();
            $socket->setAddress($this->socketIpAddress);
            $socket->setPort($this->socketPort);
                        
            if ($socket->connect()) {                                                
                $socket->write($ack->toString());
            }
            else {
                throw new SocketException("Cannot connect to socket in ".$this->socketIpAddress.":".$this->socketPort);
            }
        } catch (SocketException $ex) {
            \CVarDumper::dump("ERROR: ".$ex->getMessage());
        }         
        
    }    
    
}
