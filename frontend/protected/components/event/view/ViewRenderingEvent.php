<?php

/**
 * ViewRenderingEvent.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\event\view;
use League\Event\AbstractEvent;

/**
 *
 * Description of ViewRenderingEvent
 *
 */

class ViewRenderingEvent extends AbstractEvent
{

    /** @var Object|null */
    protected $view = null;

    /** @var bool  */
    protected $cancelled = false;

    /**
     * ViewRenderingEvent constructor.
     */
    public function __construct($view)
    {
        $this->view = $view;
    }

    /**
     * @return Object|null
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Cancels the render event
     */
    public function cancel()
    {
        $this->cancelled = true;
    }

    /**
     * Checks whether the event has been cancelled
     */
    public function isCancelled()
    {
        return $this->cancelled;
    }

}
