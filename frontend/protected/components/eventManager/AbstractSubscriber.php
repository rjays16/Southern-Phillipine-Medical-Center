<?php

/**
 * AbstractSubscriber.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace SegHis\components\eventManager;
use Segworks\EventManager\SubscriberInterface;

/**
 * Base class for all event subscribers in HIS
 *
 * @package SegHis.components.eventManager
 */
abstract class AbstractSubscriber
    extends \Component
    implements SubscriberInterface
{
}
