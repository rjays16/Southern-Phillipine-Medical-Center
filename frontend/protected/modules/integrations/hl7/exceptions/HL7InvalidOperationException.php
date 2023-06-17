<?php

/**
 * HL7InvalidOperationException.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\exceptions;
use SegHEIRS\modules\integrations\hl7\CodeTable;

/**
 *
 * Description of HL7InvalidOperationException
 *
 */

class HL7InvalidOperationException extends HL7ValidationException
{

    /** @var int */
    public $hl7ErrorCode = CodeTable::ERROR_UNSUPPORTED_MESSAGE_TYPE;
}
