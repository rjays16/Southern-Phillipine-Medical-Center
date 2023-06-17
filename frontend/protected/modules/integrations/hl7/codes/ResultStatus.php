<?php

/**
 * ResultStatus.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * Description of ResultStatus
 *
 */

class ResultStatus
{

    /** 0123 - Result Status */
    const ORDER_RECEIVED = 'O';
    const INCOMPLETE = 'I';
    const SCHEDULED = 'S';
    const PARTIAL = 'A';
    const PRELIMINARY = 'P';
    const CORRECTION = 'C';
    const UNVERIFIED = 'R';
    const FINALIZED = 'F';
    const ORDER_CANCELED = 'X';
    const NO_ORDER = 'Y';
    const NO_RECORD = 'Z';

}
