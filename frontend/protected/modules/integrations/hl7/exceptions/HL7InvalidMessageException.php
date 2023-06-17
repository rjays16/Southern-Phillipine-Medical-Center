<?php

/**
 * HL7SegmentSequenceException.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\exceptions;
use SegHEIRS\modules\integrations\hl7\CodeTable;

/**
 *
 * Description of HL7SegmentSequenceException
 *
 */

class HL7SegmentSequenceException extends HL7ValidationException
{

    /** @var int */
    public $hl7ErrorCode = CodeTable::ERROR_SEGMENT_SEQUENCE_ERROR;
}
