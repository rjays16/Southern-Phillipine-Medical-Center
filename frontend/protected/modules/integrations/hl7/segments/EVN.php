<?php

/**
 * EVN.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of EVN
 *
 */

class EVN extends Segment
{

    const INDEX_EVENT_TYPE_CODE = 1;
    const INDEX_RECORDED_DATE_TIME = 2;
    const INDEX_DATE_TIME_PLANNED_EVENT = 3;
    const INDEX_EVENT_REASON_CODE = 4;
    const INDEX_OPERATOR_I_D = 5;
    const INDEX_EVENT_OCCURRED = 6;
    const INDEX_EVENT_FACILITY = 7;

    /** @var string */
    public $name = 'EVN';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEventTypeCode($value)
    {
        $this->setField(self::INDEX_EVENT_TYPE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEventTypeCode()
    {
        return $this->getField(self::INDEX_EVENT_TYPE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRecordedDateTime($value)
    {
        $this->setField(self::INDEX_RECORDED_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRecordedDateTime()
    {
        return $this->getField(self::INDEX_RECORDED_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDateTimePlannedEvent($value)
    {
        $this->setField(self::INDEX_DATE_TIME_PLANNED_EVENT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDateTimePlannedEvent()
    {
        return $this->getField(self::INDEX_DATE_TIME_PLANNED_EVENT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEventReasonCode($value)
    {
        $this->setField(self::INDEX_EVENT_REASON_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEventReasonCode()
    {
        return $this->getField(self::INDEX_EVENT_REASON_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOperatorID($value)
    {
        $this->setField(self::INDEX_OPERATOR_I_D, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOperatorID()
    {
        return $this->getField(self::INDEX_OPERATOR_I_D);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEventOccurred($value)
    {
        $this->setField(self::INDEX_EVENT_OCCURRED, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEventOccurred()
    {
        return $this->getField(self::INDEX_EVENT_OCCURRED);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEventFacility($value)
    {
        $this->setField(self::INDEX_EVENT_FACILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEventFacility()
    {
        return $this->getField(self::INDEX_EVENT_FACILITY);
    }


}