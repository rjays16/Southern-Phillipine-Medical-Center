<?php

/**
 * TQ.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\datatypes;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;

/**
 *
 * Description of TQ
 *
 */

class TQ
{

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $interval;

    /**
     * @var string
     */
    protected $startDateTime;

    /**
     * @var string
     */
    protected $endDateTime;

    /**
     * @var string
     */
    protected $priority;

    /**
     * TQ constructor.
     *
     * @param array $value
     */
    public function __construct(array $value = [])
    {
        @list(
            $this->quantity,
            $this->interval,
            $this->startDateTime,
            $this->endDateTime,
            $this->priority
        ) = $value;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * @param string $interval
     */
    public function setInterval($interval)
    {
        $this->interval = $interval;
    }

    /**
     * @return string
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * @param string $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return string
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }

    /**
     * @param string $endDateTime
     */
    public function setEndDateTime($endDateTime)
    {
        $this->endDateTime = $endDateTime;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return HL7::encode([
            $this->quantity,
            $this->interval,
            $this->startDateTime,
            $this->endDateTime,
            $this->priority
        ]);
    }
}
