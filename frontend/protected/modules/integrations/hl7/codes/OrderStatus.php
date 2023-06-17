<?php

/**
 * OrderStatus.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * Description of OrderStatus
 *
 */

class OrderStatus
{
    /** Order Statuses  */
    const PARTIAL_RESULTS = 'A';
    const CANCELLED = 'CA';
    const COMPLETED = 'CM';
    const DISCONTINUED = 'DC';
    const ERROR = 'ER';
    const ON_HOLD = 'HD';
    const IN_PROCESS = 'IP';
    const REPLACED = 'RP';
    const SCHEDULED = 'SC';
}
