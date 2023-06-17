<?php

/**
 * IntegrationStartedEvent.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\events;
use SegHEIRS\components\event\Event;
use React\EventLoop\LoopInterface;
use React\Stream\Stream;
use SegHEIRS\components\event\EventNames;

/**
 *
 * Description of IntegrationStartedEvent
 *
 */

class IntegrationStartedEvent extends Event
{

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var Stream
     */
    protected $errorStream;

    /**
     * Event constructor.
     *
     * @param LoopInterface $eventLoop
     */
    public function __construct(LoopInterface $eventLoop, Stream $err)
    {
        $this->eventLoop = $eventLoop;
        $this->errorStream = $err;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return EventNames::EVENT_INTEGRATIONS_STARTED;
    }

    /**
     * @return LoopInterface
     */
    public function getEventLoop()
    {
        return $this->eventLoop;
    }

    /**
     * @param LoopInterface $eventLoop
     */
    public function setEventLoop($eventLoop)
    {
        $this->eventLoop = $eventLoop;
    }

    /**
     * @return Stream
     */
    public function getErrorStream()
    {
        return $this->errorStream;
    }
}
