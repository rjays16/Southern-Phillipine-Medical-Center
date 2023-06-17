<?php

/**
 * RunIntegrations.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\commands;
use CConsoleCommand;
//use React\EventLoop\Factory;
//use React\Stream\Stream;
//use SegHEIRS\components\event\Emitter;
//use SegHEIRS\modules\integrations\events\IntegrationStartedEvent;
//use Yii;

/**
 *
 * Description of RunIntegrations
 *
 */

class RunIntegrations extends CConsoleCommand
{
    
    public function run($args)
    {
//        $stderr = fopen('php://stderr', 'w');        
//        fwrite($stderr, "Testing RunSocketServer:  hello, world\n");
//        fclose($stderr);
        
//        $this->usageError("Testing RunSocketServer:  hello, world\n");        
        fwrite(STDERR, "Testing RunSocketServer:  hello, world\n"); 
    }    

    /**
     * @param array $args
     */
//    public function run($args)
//    {
//        Yii::getLogger()->autoFlush = 1;
//        Yii::getLogger()->autoDump = true;
//
//        $loop = Factory::create();
//
//        $stdOut = new Stream(STDOUT, $loop);
//        $stdOut->pause();
//        $stdErr = new Stream(STDERR, $loop);
//        $stdErr->pause();
//
//        $stdErr->write("Initializing integrations ...\n");
//
//        /** @var Emitter $emitter */
//        $emitter = Yii::app()->emitter;
//        $emitter->emit(new IntegrationStartedEvent($loop, $stdErr));
//
//        $stdErr->write("Integrations started!\n");
//        $loop->run();
//    }


}
