<?php

/**
 * EmitterComponent.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\event;
use League\Event\Emitter as LeagueEventEmitter;

/**
 *
 * Description of EmitterComponent
 * 
 */

class Emitter extends LeagueEventEmitter
{
    /**
     * Required by CModule::getComponent
     */
    public function init()
    {
        
    }
}