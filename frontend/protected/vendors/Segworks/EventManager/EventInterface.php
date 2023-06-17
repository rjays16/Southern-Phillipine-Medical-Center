<?php

/**
 * EventInterface.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace Segworks\EventManager;

/**
 * Description
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @package Segworks.EventManager
 */
interface EventInterface
{
    /**
     * Get the event name
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the event name
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Whether this event will trigger subscribers that are subscribed to
     * $eventName.
     *
     * This allows dynamic event typing as PHP currently does not provide
     * metaclass functionality.
     *
     * @param string $eventName
     * @return bool
     */
    public function hasName($eventName);

    /**
     * Get target/context from which event was triggered
     *
     * @return null|string|object
     */
    public function getTarget();

    /**
     * Set the event target/context
     *
     * @param  null|string|object $target
     * @return void
     */
    public function setTarget($target);

    /**
     * Get parameters passed to the event
     *
     * @return array|\ArrayAccess
     */
    public function getParams();

    /**
     * Set event parameters
     *
     * @param  string $params
     * @return void
     */
    public function setParams($params);

    /**
     * Get a single parameter by name
     *
     * @param  string $name
     * @param  mixed $default Default value to return if parameter does not exist
     * @return mixed
     */
    public function getParam($name, $default = null);

    /**
     * Set a single parameter by key
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function setParam($name, $value);

    /**
     * Indicate whether or not the parent EventManagerInterface should stop propagating events
     *
     * @param  bool $flag
     * @return void
     */
    public function stopPropagation($flag = true);

    /**
     * Has this event indicated event propagation should stop?
     *
     * @return bool
     */
    public function isPropagationStopped();
} 