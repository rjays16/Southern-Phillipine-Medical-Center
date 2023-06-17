<?php

/**
 * HL7FileTransferException.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\exceptions;
use SegHEIRS\modules\integrations\hl7\CodeTable;

/**
 *
 * Description of HL7FileTransferException
 *
 */

class HL7FileTransferException extends HL7Exception
{

    /** @var int */
    public $hl7ErrorCode = CodeTable::ERROR_APPLICATION_INTERNAL_ERROR;
}
