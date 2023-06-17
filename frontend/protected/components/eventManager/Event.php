<?php

/**
 * Event.php
 *
 * @copyright (c) 2014, Segworks Technologies Corporation
 */

namespace SegHis\components\eventManager;
use Segworks\EventManager\EventInterface;

/**
 * Base class for all events used in the HIS application
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @package application.components
 */

class Event extends \CEvent implements EventInterface
{
    /**
     * @var string A unique name assigned to this particular event
     */
    protected $name;

    /**
     * Constructor.
     * @param string $name name of the event
     * @param mixed $sender sender of the event
     * @param mixed $params additional parameters for the event
     */
    public function __construct($name, $sender=null,$params=null)
    {
        $this->setName($name);
        parent::__construct($sender, $params);
    }

    /**
     * @see EventInterface::getName
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @see EventInterface::getTarget
     */
    public function getTarget()
    {
        return $this->sender;
    }

    /**
     * @see EventInterface::getParams
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @see EventInterface::getParam
     */
    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ?
            $this->params[$name] :
            $default;
    }

    /**
     * @see EventInterface::setTarget
     */
    public function setTarget($target)
    {
        $this->sender = $target;
    }

    /**
     * @see EventInterface::setParams
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @see EventInterface::setParam
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * @see EventInterface::hasName
     * @todo Implement dynamic typing support for Event Manager
     */
    public function hasName($eventName)
    {
        /**
         * Only respond to events matching the same name for now...
         */
        return $eventName == static::getName();
    }

    /**
     * @see EventInterface::stopPropagataion
     */
    public function stopPropagation($flag = true)
    {
        $this->handled = $flag;
    }

    /**
     * @see EventInterface::isPropagationStopped
     */
    public function isPropagationStopped()
    {
        return $this->handled;
    }

}