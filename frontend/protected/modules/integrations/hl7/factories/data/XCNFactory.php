<?php

/**
 * XCNFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\data;

use PersonCatalog;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;
use User;

/**
 *
 * Factory for XCN (Extended Composite ID Number for Persons) as defined
 * in HL7 format.
 */
class XCNFactory
{

    /**
     * Generates an XCN value from the Person model
     *
     * @param PersonCatalog $person
     *
     * @return string
     */
    public function createFromPerson($person)
    {
        $pid = $person->pid;
        if (is_numeric($pid) && strlen($pid) === 15) {
            // If the PID is in the format of YYYY + 11 other digits
            // We get the last two digits of the year part and add the last 10
            // digits of the reference number to get the condensed format
            // (12 digits total)
            $pid = substr($pid, 2, 2) . substr($pid, -10);
        }
        $values = [
            $pid,
            $person->name_last,
            $person->name_first,
            $person->name_middle,
            $person->suffix
        ];

        return HL7::encodeValues($values);
    }

}
