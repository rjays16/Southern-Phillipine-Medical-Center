<?php

/**
 * EventServiceInterface.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace Segworks\EventManager;

/**
 * Description
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @package Segworks.EventManager
 * @todo Implement Event filters
 */
interface EventServiceInterface
{

    /**
     *
     * @param string $eventName
     * @param callable $callback
     * @param int $priority
     */
    public function attachListener($eventName, $callback, $priority=1);

    /**
     *
     *
     * @param string $eventName
     * @param callable $callback
     */
    public function detachListener($eventName, $callback);

    /**
     *
     * @param EventInterface $event
     * @return null|ResponseCollection Returns a ResponseCollection object or
     * null if no subscribers are interested in the event
     */
    public function publish(EventInterface $event);

    /**
     * Registers a
     *
     * @param SubscriberInterface $subscriber
     */
    public function subscribe(SubscriberInterface $subscriber);

    /**
     *
     * @param SubscriberInterface $subscriber
     */
    public function unsubscribe(SubscriberInterface $subscriber);

}