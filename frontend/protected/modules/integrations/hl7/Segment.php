<?php

/**
 * Segment.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7;

use HL7\Segment as BaseSegment;

/**
 *
 * Description of Segment
 *
 */

class Segment extends BaseSegment
{
    public $name = '';

    /**
     * Segment constructor.
     *
     * @param array $fields
     */
    public function __construct(array $fields = array())
    {
        parent::__construct($this->name, $fields);

//        if (!empty($hl7Globals) && is_array($hl7Globals)) {
//            if ($hl7Globals['FIELD_SEPARATOR']) {
//                $this->FIELD_SEPARATOR = $hl7Globals['FIELD_SEPARATOR'];
//            }
//
//            if ($hl7Globals['COMPONENT_SEPARATOR']) {
//                $this->COMPONENT_SEPARATOR = $hl7Globals['COMPONENT_SEPARATOR'];
//            }
//
//            if ($hl7Globals['REPETITION_SEPARATOR']) {
//                $this->REPETITION_SEPARATOR = $hl7Globals['REPETITION_SEPARATOR'];
//            }
//
//            if ($hl7Globals['ESCAPE_CHARACTER']) {
//                $this->ESCAPE_CHARACTER= $hl7Globals['ESCAPE_CHARACTER'];
//            }
//
//            if ($hl7Globals['SUBCOMPONENT_SEPARATOR']) {
//                $this->SUBCOMPONENT_SEPARATOR = $hl7Globals['SUBCOMPONENT_SEPARATOR'];
//            }
//
//            if ($hl7Globals['HL7_VERSION']) {
//                $this->HL7_VERSION = $hl7Globals['HL7_VERSION'];
//            }
//        }
    }

    /**
     * @param BaseSegment $segment
     *
     * @return static
     */
    public static function createFromSegment(BaseSegment $segment)
    {
        $new = new static();
        $new->_fields = $segment->_fields;
        return $new;
    }
}
