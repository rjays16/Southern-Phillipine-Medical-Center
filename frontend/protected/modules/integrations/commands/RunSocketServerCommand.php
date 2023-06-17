<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//namespace \SegHEIRS\modules\integrations\commands;

use React\EventLoop\Factory;
use React\Socket\Server;
use React\Socket\ConnectionInterface;
use SegHEIRS\modules\integrations\hl7\factories\messages\ACKFactory;
use HL7\Message;
use SegHEIRS\modules\integrations\hl7\segments\MSH;
use SegHis\modules\poc\models\Hl7MessageLog;
use SegHis\modules\poc\models\CbgReading;
use SegHis\modules\poc\models\PocOrder;
use SegHis\modules\phic\models\AppliedCoverage;
use SegHEIRS\modules\integrations\events\CbgOrderedEvent;
//use Yii;

/**
 * Description of RunSocketServer
 *
 * @author Bong
 */
class RunSocketServerCommand extends CConsoleCommand {
       
    const RESULT_MSG = "ORU^R01";
    
    /**
     * @return string the help information for the shell command
     */
    public function getHelp()
    {
            return <<<EOD
USAGE
yiic runsocketserver

DESCRIPTION
This command allows you to run a socket server for HL7 communication

EOD;
    }    

    public static function ping() {
        try {
            Yii::app()->getDb()->createCommand('SELECT 1')->query();
        } catch (\Exception $exception) {
            Yii::app()->getDb()->setActive(false);
            Yii::app()->getDb()->setActive(true);            
        }
    }    

    /**
     * @param array $args
     */
    public function run($args)
    {        
        $loop = Factory::create();
        
//        $stdErr = new Stream(STDERR, $loop);
//        $stdErr->pause();        
        $listener = Yii::app()->getModule('integrations');
        $server = new Server(isset($args[1]) ? $args[1] : $listener->listeningIPAddress.':'.$listener->listeningIPPort, $loop, array(
            'tls' => array(
                'local_cert' => isset($args[2]) ? $args[2] : (__DIR__ . '/localhost.pem')
            )
        ));        

        $server->on('connection', function (ConnectionInterface $conn) {                                    
            fwrite(STDERR, "[" . $conn->getRemoteAddress() . " connected]\n"); 
            
            $conn->on('data', function ($data) use ($conn) {
                try {                        
                    // Generate acknowledgement message ...
                    $module = Yii::app()->getModule('integrations')->getModule('jnj');                                   
                    if (!$module->enableIntegration) {
                        return;
                    }
                    
                    RunSocketServerCommand::ping();
                    
                    try {
                        $message = new Message($data);
                        if (!empty($message) && Hl7MessageLog::isValidHl7Msg($message)) {
                            // Log the HL7 message received ...
                            $hl7Log = Hl7MessageLog::logHl7Message($message);

                            // Check if from expected sending application and valid result ...
                            $segments = $message->getSegmentsByName('MSH');
                            $forMSH = MSH::createFromSegment($segments[0]); 

                            $sendingApp = $forMSH->getSendingApplication();
                            if (is_array($sendingApp)) {
                                $sendingApp = implode("^",$sendingApp);
                            }
                            if ( substr_count($sendingApp, $module->receivingApplication) > 0 ) {
                                $msgType = $forMSH->getMessageType();
                                if (is_array($msgType)) {
                                    $msgType = implode("^",$msgType);
                                }                            
                                if ( $msgType === RunSocketServerCommand::RESULT_MSG ) {
                                    // Acknowledge sent HL7 message ...
                                    $ack = new ACKFactory();
                                    $ackmsg = $ack->createSuccess($message);
                                    $conn->write($ackmsg->toString());

                                    // Log acknowledgement to sending application ...
                                    Hl7MessageLog::logAcknowledgement($ackmsg);                                

                                    if ($hl7Log) {
                                        // Process the HL7 message received ...
                                        CbgReading::postCbgReading($message, $hl7Log->log_id);                                                                         

                                        $pocOrder = new PocOrder();
                                        $cbgOrder = $pocOrder->getPocOrder($hl7Log->ref_no);
                                        if (strtoupper($cbgOrder->settlement_type) == 'PHIC') {
                                            $coverage = new AppliedCoverage();
                                            $coverage->applyCoverage($cbgOrder);
                                        }
                                        
                                        // If order is CASH, check if readings has reached the no. of readings ordered ...
                                        if ($cbgOrder->is_cash) {
                                            $orderD = $cbgOrder->pocOrderDetails[0];
                                            if (CbgReading::getCbgReadingsCount($hl7Log->ref_no) >= $orderD->quantity) {
                                                // Send ADT Discharge HL7 message ...
                                                $cbgOrder->order_type = PocOrder::STOP;
                                                $emitter = Yii::app()->emitter;
                                                $emitter->emit(new CbgOrderedEvent($cbgOrder, $orderD));
                                            }                                            
                                        }
                                    }
                                }
                            }
                        }
                        else {
                            fwrite(STDERR, "Not a valid HL7 message: " . $data ."\n");
                        }

                    } catch (\Exception $e) {
                        fwrite(STDERR, 'INTEGRATION ERROR: ' . $e->getMessage() ."\n");
                    }                    
                                                            
                } catch (RuntimeException $ex) {
                    fwrite(STDERR, 'RUNTIME ERROR: ' . $ex->getMessage() ."\n");
                }                                                
            });		
        });

        $server->on('error', function (Exception $e) {
            fwrite(STDERR, 'ERROR: ' . $e->getMessage() ."\n");
        });        

//        $stdErr->write("Listening on " . $server->getAddress() ."\n"); 
        fwrite(STDERR, "Listening on " . $server->getAddress() ."\n");
        
        $loop->run();
    }    
}
