<?php

/**
 * OrderControl.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\codes;

/**
 *
 * Description of OrderControl
 *
 */

class OrderControl
{

    const REFILL_APPROVED = 'AF';
    const CANCEL_ORDER = 'CA';
    const CHILD_ORDER = 'CH';
    const ORDER_COMBINED_RESULT = 'CN';
    const CANCEL_PROCESS_STEP = 'CP';
    const ORDER_CANCELLED_AS_REQUESTED = 'CR';
    const DISCONTINUE_ORDER = 'DC';
    const DATA_ERRORS = 'DE';
    const ORDER_REFILL_DENIED = 'DF';
    const ORDER_DISCONTINUED_AS_REQUESTED = 'DR';
    const ORDER_REFILLED_UNSOLICITED = 'FU';
    const HOLD_ORDER = 'HD';
    const ORDER_ON_HOLD_AS_REQUESTED = 'HR';
    const LINK_ORDER = 'LI';
    const ORDER_NUMBER_ASSIGNED = 'NA';
    const NEW_ORDER = 'NW';
    const ORDER_CANCELLED = 'OC';
    const ORDER_DISCONTINUED = 'OD';
    const ORDER_RELEASED = 'OE';
    const ORDER_REFILLED = 'OF';
    const ORDER_HELD = 'OH';
    const ORDER_ACCEPTED = 'OK';
    const OUTSIDE_DISPENSE = 'OP';
    const ORDER_RELEASED_AS_REQUESTED = 'OR';
    const PARENT_ORDER = 'PA';
    const PREVIOUS_RESULTS = 'PR';
    const REPLACE_ORDER_FOR_OUTSIDE_DISPENSE = 'PY';
    const OBSERVATION_PERFORMED = 'RE';
    const REFILL_ORDER = 'RF';
    const RELEASE_PREVIOUS_HOLD_ORDER = 'RL';
    const REPLACE_ORDER = 'RO';
    const ORDER_REPLACED = 'RP';
    const ORDER_REPLACED_AS_REQUESTED = 'RQ';
    const REQUEST_RECEIVED = 'RR';
    const ORDER_REPLACED_UNSOLICITED = 'RU';
    const ORDER_STATUS_CHANGED = 'SC';
    const SEND_ORDER_NUMBER = 'SN';
    const SEND_ORDER_RESPONSE = 'SR';
    const SEND_ORDER_STATUS = 'SS';
    const UNABLE_TO_ACCEPT = 'UA';
    const UNABLE_TO_CANCEL = 'UC';
    const UNABLE_TO_DISCONTINUE = 'UD';
    const UNABLE_TO_REFILL = 'UF';
    const UNABLE_TO_HOLD = 'UH';
    const UNABLE_TO_REPLACE = 'UM';
    const UNABLE_TO_RELEASE = 'UR';
    const UNABLE_TO_CHANGE = 'UX';
    const CHANGE_ORDER = 'XO';
    const ORDER_CHANGED_AS_REQUESTED = 'XR';
    const ORDER_CHANGED_UNSOLICITED = 'XX';
    const MISCELLANEOUS_CHARGE = 'MC';

}
