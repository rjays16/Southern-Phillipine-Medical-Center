<?php

/**
 * ObservationResultStatus.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * HL7 Code Table - 0078
 *
 */

class ObservationResultStatus
{

    const AMENDED = 'A';
    const APPENDED_REPORT = 'B';
    const CORRECTION = 'C';
    const DELETED = 'D';
    const FINAL_RESULT = 'F';
    const SPECIMEN_IN_LAB = 'I';
    const NOT_ASKED = 'N';
    const NO_RESULT = 'O';
    const PRELIMINARY_RESULT = 'P';
    const NOT_VERIFIED = 'R';
    const PARTIAL_RESULT = 'S';
    const VERIFIED = 'V';
    const CANNOT_BE_OBTAINED = 'X';
    const STATUS_CHANGED = 'U';
    const WRONG = 'W';

}
