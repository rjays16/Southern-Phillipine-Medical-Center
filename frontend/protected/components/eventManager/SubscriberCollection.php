<?php

/**
 * SubscriberCollectionInterface.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace SegHis\components\eventManager;

/**
 * Description
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @package Segworks.EventManager
 */


abstract class SubscriberCollection
{

    /**
     *
     * @return mixed
     */
    abstract public function getSuscribers();
}