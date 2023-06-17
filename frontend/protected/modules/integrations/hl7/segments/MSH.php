<?php

/**
 * MSH.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use HL7\Segment;
use HL7\Segments\MSH as BaseMSH;


/**
 *
 * Description of MSH
 *
 */

class MSH extends BaseMSH
{

    const INDEX_SENDING_APPLICATION = 3;
    const INDEX_SENDING_FACILITY = 4;
    const INDEX_RECEIVING_APPLICATION = 5;
    const INDEX_RECEIVING_FACILITY = 6;
    const INDEX_DATE_TIME_OF_MESSAGE = 7;
    const INDEX_SECURITY = 8;
    const INDEX_MESSAGE_TYPE = 9;
    const INDEX_MESSAGE_CONTROL_ID = 10;
    const INDEX_PROCESSING_ID = 11;
    const INDEX_VERSION_ID = 12;
    const INDEX_SEQUENCE_NUMBER = 13;
    const INDEX_CONTINUATION_POINTER = 14;
    const INDEX_ACCEPT_ACKNOWLEDGEMENT_TYPE = 15;
    const INDEX_APPLICATION_ACKNOWLEDGMENT_TYPE = 16;
    const INDEX_COUNTRY_CODE = 17;
    const INDEX_CHARACTER_SET = 18;
    const INDEX_RECEIVING_NETWORK_ADDRESS = 24;
    const INDEX_SENDING_NETWORK_ADDRESS = 25;

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSendingApplication($value)
    {
        $this->setField(self::INDEX_SENDING_APPLICATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSendingApplication()
    {
        return $this->getField(self::INDEX_SENDING_APPLICATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSendingFacility($value)
    {
        $this->setField(self::INDEX_SENDING_FACILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSendingFacility()
    {
        return $this->getField(self::INDEX_SENDING_FACILITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReceivingApplication($value)
    {
        $this->setField(self::INDEX_RECEIVING_APPLICATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReceivingApplication()
    {
        return $this->getField(self::INDEX_RECEIVING_APPLICATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReceivingFacility($value)
    {
        $this->setField(self::INDEX_RECEIVING_FACILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReceivingFacility()
    {
        return $this->getField(self::INDEX_RECEIVING_FACILITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDateTimeOfMessage($value)
    {
        $this->setField(self::INDEX_DATE_TIME_OF_MESSAGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDateTimeOfMessage()
    {
        return $this->getField(self::INDEX_DATE_TIME_OF_MESSAGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSecurity($value)
    {
        $this->setField(self::INDEX_SECURITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSecurity()
    {
        return $this->getField(self::INDEX_SECURITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMessageType($value)
    {
        $this->setField(self::INDEX_MESSAGE_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->getField(self::INDEX_MESSAGE_TYPE);
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
    public function setProcessingId($value)
    {
        $this->setField(self::INDEX_PROCESSING_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcessingId()
    {
        return $this->getField(self::INDEX_PROCESSING_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVersionId($value)
    {
        $this->setField(self::INDEX_VERSION_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVersionId()
    {
        return $this->getField(self::INDEX_VERSION_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSequenceNumber($value)
    {
        $this->setField(self::INDEX_SEQUENCE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSequenceNumber()
    {
        return $this->getField(self::INDEX_SEQUENCE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setContinuationPointer($value)
    {
        $this->setField(self::INDEX_CONTINUATION_POINTER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getContinuationPointer()
    {
        return $this->getField(self::INDEX_CONTINUATION_POINTER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAcceptAcknowledgementType($value)
    {
        $this->setField(self::INDEX_ACCEPT_ACKNOWLEDGEMENT_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAcceptAcknowledgementType()
    {
        return $this->getField(self::INDEX_ACCEPT_ACKNOWLEDGEMENT_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setApplicationAcknowledgmentType($value)
    {
        $this->setField(self::INDEX_APPLICATION_ACKNOWLEDGMENT_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationAcknowledgmentType()
    {
        return $this->getField(self::INDEX_APPLICATION_ACKNOWLEDGMENT_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCountryCode($value)
    {
        $this->setField(self::INDEX_COUNTRY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getField(self::INDEX_COUNTRY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCharacterSet($value)
    {
        $this->setField(self::INDEX_CHARACTER_SET, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCharacterSet()
    {
        return $this->getField(self::INDEX_CHARACTER_SET);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSendingNetworkAddress($value)
    {
        $this->setField(self::INDEX_SENDING_NETWORK_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSendingNetworkAddress()
    {
        return $this->getField(self::INDEX_SENDING_NETWORK_ADDRESS);
    }

    /**
     * @param Segment $segment
     *
     * @return static
     */
    public static function createFromSegment(Segment $segment)
    {
        $new = new static();
        $new->_fields = $segment->_fields;
        return $new;
    }
}
