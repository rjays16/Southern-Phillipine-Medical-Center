<?php

/**
 * SubscriberInterface.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace Segworks\EventManager;
use \Segworks\EventManager\EventInterface;
use \Segworks\EventManager\EventServiceInterface;

/**
 * Description
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @package Segworks.EventManager
 */

interface SubscriberInterface
{

    /**
     * Returns the list of events that this subscriber is interested in.
     *
     * This method should return
     *
     * @return array
     */
    public function getListeners();

}
