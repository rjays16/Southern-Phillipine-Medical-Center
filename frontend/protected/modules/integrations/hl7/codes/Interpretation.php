<?php

/**
 * Interpretation.php
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

class Interpretation
{

    const LOW = 'L';
    const HIGH = 'H';
    const VERY_LOW = 'LU';
    const VERY_HIGH = 'HU';
    const CRITICALLY_LOW = 'LL';
    const CRITICALLY_HIGH = 'HH';
    const OFF_SCALE_LOW = '<';
    const OFF_SCALE_HIGH = '>';
    const NORMAL = 'N';
    const ABNORMAL = 'A';
    const CRITICALLY_ABNORMAL = 'AA';
    const SIGNIFICANT_CHANGE_UP = 'U';
    const SIGNIFICANT_CHANGE_DOWN = 'D';
    const BETTER = 'B';
    const WORSE = 'W';
    const SUSCEPTIBLE = 'S';
    const RESISTANT = 'R';
    const INTERMEDIATE = 'I';
    const NON_SUSCEPTIBLE = 'NS';
    const SUSCEPTIBLE_DOSE_DEPENDENT = 'SDD';
    const INSUFFICIENT_EVIDENCE = 'IE';
    const SYNERGY_RESISTANT = 'SYN-R';
    const SYNERGY_SUSCEPTIBLE = 'SYN-S';
    const POSITIVE = 'POS';
    const NEGATIVE = 'NEG';
    const INDETERMINATE = 'IND';
    const DETECTED = 'DET';
    const NOT_DETECTED = 'ND';
    const REACTIVE = 'RR';
    const WEAKLY_REACTIVE = 'WR';
    const NON_REACTIVE = 'NR';

}
