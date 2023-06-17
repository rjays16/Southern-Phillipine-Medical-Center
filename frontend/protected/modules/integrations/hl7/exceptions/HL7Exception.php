<?php

/**
 * HL7Exception.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\exceptions;
use CException;

/**
 *
 * Description of HL7Exception
 *
 */

class HL7Exception extends CException
{

    /** @var int */
    public $hl7ErrorCode;

    /** @var string|null  */
    public $errorSegment;

    /** @var int|null  */
    public $errorSegmentSequence;

    /** @var int|null  */
    public $errorFieldPosition;

    /** @var int|null  */
    public $errorFieldRepetition;

    /** @var int|null  */
    public $errorComponentNumber;

    /** @var int|null  */
    public $errorSubComponentNumber;



    /**
     * HL7Exception constructor.
     *
     * @param string $message
     * @param null $errorSegment
     * @param null $errorFieldPosition
     * @param null $errorFieldRepetition
     * @param null $errorComponentNumber
     * @param null $errorSubComponentNumber
     */
    public function __construct(
        $message,
        $errorSegment = null,
        $errorFieldPosition = null,
        $errorFieldRepetition = null,
        $errorComponentNumber = null,
        $errorSubComponentNumber = null
    ) {
        $this->errorSegment = $errorSegment;
        $this->errorFieldPosition = $errorFieldPosition;
        $this->errorFieldRepetition = $errorFieldRepetition;
        $this->errorComponentNumber = $errorComponentNumber;
        $this->errorSubComponentNumber = $errorSubComponentNumber;

        parent::__construct($message, $this->hl7ErrorCode);
    }

    /**
     * @return null|string
     */
    public function getErrorSegment()
    {
        return $this->errorSegment;
    }

    /**
     * @return int|null
     */
    public function getErrorSegmentSequence()
    {
        return $this->errorSegmentSequence;
    }

    /**
     * @return int|null
     */
    public function getErrorFieldPosition()
    {
        return $this->errorFieldPosition;
    }

    /**
     * @return int|null
     */
    public function getErrorFieldRepetition()
    {
        return $this->errorFieldRepetition;
    }

    /**
     * @return int|null
     */
    public function getErrorComponentNumber()
    {
        return $this->errorComponentNumber;
    }

    /**
     * @return int|null
     */
    public function getErrorSubComponentNumber()
    {
        return $this->errorSubComponentNumber;
    }

}
