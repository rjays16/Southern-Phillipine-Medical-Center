<?php

/**
 * PatientClass.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * HL7 Table 0004
 *
 */
class PatientClass
{

    const EMERGENCY = 'E';
    const INPATIENT = 'I';
    const OUTPATIENT = 'O';
    const WALKIN = 'W';
    const PREADMIT = 'P';
    const RECURRING_PATIENT = 'R';
    const OBSTETRICS = 'B';
    const COMMERCIAL_ACCOUNT = 'C';
    const NOT_APPLICABLE = 'N';
    const UNKNOWN = 'U';

}
