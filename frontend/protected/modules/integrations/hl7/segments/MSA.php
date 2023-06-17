<?php

/**
 * MSA.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of MSA
 *
 */

class MSA extends Segment
{

    public $name = 'MSA';

    const INDEX_ACKNOWLEDGMENT_CODE = 1;
    const INDEX_MESSAGE_CONTROL_ID = 2;
    const INDEX_TEXT_MESSAGE = 3;
    const INDEX_EXPECTED_SEQUENCE_NUMBER = 4;
    const INDEX_DELAYED_ACKNOWLEDGMENT_TYPE = 5;
    const INDEX_ERROR_CONDITION = 6;
    const INDEX_MESSAGE_WAITING_NUMBER = 7;
    const INDEX_MESSAGE_WAITING_PRIORITY = 8;

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAcknowledgmentCode($value)
    {
        $this->setField(self::INDEX_ACKNOWLEDGMENT_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAcknowledgmentCode()
    {
        return $this->getField(self::INDEX_ACKNOWLEDGMENT_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMessageControlId($value)
    {
        $this->setField(self::INDEX_MESSAGE_CONTROL_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageControlId()
    {
        return $this->getField(self::INDEX_MESSAGE_CONTROL_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTextMessage($value)
    {
        $this->setField(self::INDEX_TEXT_MESSAGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTextMessage()
    {
        return $this->getField(self::INDEX_TEXT_MESSAGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedSequenceNumber($value)
    {
        $this->setField(self::INDEX_EXPECTED_SEQUENCE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedSequenceNumber()
    {
        return $this->getField(self::INDEX_EXPECTED_SEQUENCE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDelayedAcknowledgmentType($value)
    {
        $this->setField(self::INDEX_DELAYED_ACKNOWLEDGMENT_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDelayedAcknowledgmentType()
    {
        return $this->getField(self::INDEX_DELAYED_ACKNOWLEDGMENT_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setErrorCondition($value)
    {
        $this->setField(self::INDEX_ERROR_CONDITION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCondition()
    {
        return $this->getField(self::INDEX_ERROR_CONDITION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMessageWaitingNumber($value)
    {
        $this->setField(self::INDEX_MESSAGE_WAITING_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageWaitingNumber()
    {
        return $this->getField(self::INDEX_MESSAGE_WAITING_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMessageWaitingPriority($value)
    {
        $this->setField(self::INDEX_MESSAGE_WAITING_PRIORITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageWaitingPriority()
    {
        return $this->getField(self::INDEX_MESSAGE_WAITING_PRIORITY);
    }


}