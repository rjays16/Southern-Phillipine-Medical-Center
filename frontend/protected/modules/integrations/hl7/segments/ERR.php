<?php

/**
 * ERR.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of ERR
 *
 */

class ERR extends Segment
{

    const INDEX_ERROR_CODE_AND_LOCATION = 1;
    const INDEX_ERROR_LOCATION = 2;
    const INDEX_HL7_ERROR_CODE = 3;
    const INDEX_SEVERITY = 4;
    const INDEX_APPLICATION_ERROR_CODE = 5;
    const INDEX_APPLICATION_ERROR_PARAMETER = 6;
    const INDEX_DIAGNOSTIC_INFORMATION = 7;
    const INDEX_USER_MESSAGE = 8;
    const INDEX_INFORM_PERSON_INDICATOR = 9;
    const INDEX_OVERRIDE_TYPE = 10;
    const INDEX_OVERRIDE_REASON_CODE = 11;
    const INDEX_HELP_DESK_CONTACT_POINT = 12;

    public $name = 'ERR';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setErrorCodeAndLocation($value)
    {
        $this->setField(self::INDEX_ERROR_CODE_AND_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorCodeAndLocation()
    {
        return $this->getField(self::INDEX_ERROR_CODE_AND_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setErrorLocation($value)
    {
        $this->setField(self::INDEX_ERROR_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorLocation()
    {
        return $this->getField(self::INDEX_ERROR_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setHl7ErrorCode($value)
    {
        $this->setField(self::INDEX_HL7_ERROR_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getHl7ErrorCode()
    {
        return $this->getField(self::INDEX_HL7_ERROR_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSeverity($value)
    {
        $this->setField(self::INDEX_SEVERITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSeverity()
    {
        return $this->getField(self::INDEX_SEVERITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setApplicationErrorCode($value)
    {
        $this->setField(self::INDEX_APPLICATION_ERROR_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationErrorCode()
    {
        return $this->getField(self::INDEX_APPLICATION_ERROR_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setApplicationErrorParameter($value)
    {
        $this->setField(self::INDEX_APPLICATION_ERROR_PARAMETER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationErrorParameter()
    {
        return $this->getField(self::INDEX_APPLICATION_ERROR_PARAMETER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosticInformation($value)
    {
        $this->setField(self::INDEX_DIAGNOSTIC_INFORMATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosticInformation()
    {
        return $this->getField(self::INDEX_DIAGNOSTIC_INFORMATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setUserMessage($value)
    {
        $this->setField(self::INDEX_USER_MESSAGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getUserMessage()
    {
        return $this->getField(self::INDEX_USER_MESSAGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInformPersonIndicator($value)
    {
        $this->setField(self::INDEX_INFORM_PERSON_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInformPersonIndicator()
    {
        return $this->getField(self::INDEX_INFORM_PERSON_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOverrideType($value)
    {
        $this->setField(self::INDEX_OVERRIDE_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOverrideType()
    {
        return $this->getField(self::INDEX_OVERRIDE_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOverrideReasonCode($value)
    {
        $this->setField(self::INDEX_OVERRIDE_REASON_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOverrideReasonCode()
    {
        return $this->getField(self::INDEX_OVERRIDE_REASON_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setHelpDeskContactPoint($value)
    {
        $this->setField(self::INDEX_HELP_DESK_CONTACT_POINT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getHelpDeskContactPoint()
    {
        return $this->getField(self::INDEX_HELP_DESK_CONTACT_POINT);
    }



}