<?php

/**
 * ViewRenderedEvent.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\components\event\view;
use League\Event\AbstractEvent;

/**
 *
 * Description of ViewRenderedEvent
 *
 */

class ViewRenderedEvent extends AbstractEvent
{

    /** @var Object|null */
    protected $view = null;

    /** @var string|null */
    protected $output = null;

    /** @var bool  */
    protected $changed = false;

    /**
     * ViewRenderedEvent constructor.
     */
    public function __construct($view, $output)
    {
        $this->view = $view;
        $this->output = $output;
        $this->changed = false;
    }

    /**
     * @return Object|null
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $output
     */
    public function setOutput($output)
    {
        if ($output !== $output) {
            $this->output = $output;
            $this->changed = true;
        }

    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }


    /**
     * @return bool
     */
    public function isChanged()
    {
        return $this->changed;
    }
}
