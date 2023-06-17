<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/15/2019
 * Time: 3:24 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;


use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4ApiService;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class CourseWardService extends XmlWriter
{

    public $document;

    public $encounter;

    public $data;

    /* Initializes Class for Courseward Service*/
    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter,
        $data)
    {
        $this->data = $data;
        $this->document = $document;
        $this->encounter = $encounter;
    }

    public function generateHeader()
    {
        $header = $this->_createNode(
            $this->document,
            'COURSEWARDS',
            array()
        );

        return $header;
    }

    public function generateNode()
    {
        $header = $this->generateHeader();

        $coursewards = $this->data->course_wards;

        if (empty($coursewards)) {
            $coursewards[] = (object)array();
        }

        foreach ($coursewards as $courseward) {
            /* Generate COURSEWARD NODE*/
            $this->appendNode(
                $header,
                $courseward,
                'COURSEWARD',
                array(
                    'pHciCaseNo' => $this->encounter->encounter_nr,
                    'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
                    'pDateAction' => empty($courseward->order_date) ? null :
                        date('Y-m-d', strtotime($courseward->order_date)),
                    'pDoctorsAction' => empty($courseward->action) ? null : $courseward->action,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => '',
                )
            );
        }

        /*APPEND THE WHOLE DOCUMENT */
        $this->document->appendChild($header);

        return $header;
    }


}