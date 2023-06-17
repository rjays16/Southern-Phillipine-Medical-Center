<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace SegHEIRS\modules\integrations\modules\jnj\events\handlers;

use SegHEIRS\modules\integrations\events\AckHl7Event;
use Yii;

/**
 * Description of AckHl7MessageHandler
 *
 * @author Bong
 */
class AckHl7MessageHandler {

    /**
     * @param EventInterface $event
     */
    public function handle($event)
    {
        if ($event instanceof AckHl7Event) {
            /** @var LisHl7Module $module */
            $module = Yii::app()->getModule('integrations')->getModule('jnj');                                   
            if (!$module->enableIntegration) {
                return;
            }
            try {
                // Code to handle HL7 Acknowledgement Message ...
                fwrite(STDERR, "Acknowledgement Received!\n");
                
            } catch (\Exception $e) {
                Yii::log('LIS Integration Error -- ' . $e->getMessage(), \CLogger::LEVEL_ERROR);
            }
        }
    }    
    
}
