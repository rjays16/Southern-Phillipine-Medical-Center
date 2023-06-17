<?php

/**
 * OrderReason.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * Description of OrderReason
 *
 */

class OrderReason
{
    /** Order Reason Codes */
    const ORDERED = 'O';
    const CANCELLED = 'C';
    const SCHEDULED = 'S';
    const ARRIVED = 'A';
}
