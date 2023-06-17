<?php

/**
 * EVNFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\segments;
use SegHEIRS\modules\integrations\hl7\segments\EVN;

/**
 *
 * Description of EVNFactory
 *
 */

class EVNFactory
{

    /**
     * @param string $eventTypeCode
     *
     * @return EVN
     */
    public function create($eventTypeCode)
    {
        $evn = new EVN();
        $evn->setEventTypeCode($eventTypeCode);
        $evn->setRecordedDateTime(date('YmdHis'));
        return $evn;
    }

}
