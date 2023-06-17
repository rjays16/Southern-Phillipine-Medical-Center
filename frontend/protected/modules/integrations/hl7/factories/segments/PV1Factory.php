<?php

/**
 * PV1Factory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 * @modifications:
 *      02/01/2019 - removed lines not applicable in SegHIS. - LST
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\segments;

use Encounter;
use EncounterType;
use SegHis\modules\poc\models\PocOrder;
use SegHEIRS\modules\integrations\hl7\segments\PV1;
use SegHEIRS\modules\integrations\hl7\codes\PatientClass;

/**
 *
 * Description of PV1Factory
 *
 */

class PV1Factory
{
    /**
     * @param Encounter $encounter
     *
     * @return PV1
     */
    public function create(Encounter $encounter, PocOrder $order = null)
    {
        $pv1 = new PV1();
        
        $pv1->setSequenceId(1);
        $pv1->setVisitNumber($encounter->encounter_nr);
        $pv1->setVisitIndicator('V');

        // Store the reference number of order ...
        if ($order != null) {
            $pv1->setPreadmitNumber($order->refno);
        }
        
        $pv1->setAttendingDoctor($value);

        $encType = $encounter->type;
        switch ($encType->type_nr) {
            case EncounterType::TYPE_OUTPATIENT:
                $pv1->setPatientClass(PatientClass::OUTPATIENT);
                $pv1->setPatientType(PatientClass::OUTPATIENT);
                $pv1->setAdmitDateTime(date('YmdHis', strtotime($encounter->encounter_date)));
                if ($encounter->department) {
                    $pv1->setAssignedPatientLocation(implode('^', array(
                        $order->ward_id,
                        '',
                        '',
                        \Yii::app()->params['FACILITY_NAME']
                    )));
                }
                break;

            case EncounterType::TYPE_EMERGENCY:
                $pv1->setPatientClass(PatientClass::EMERGENCY);
                $pv1->setPatientType(PatientClass::EMERGENCY);
                $pv1->setAdmitDateTime(date('YmdHis', strtotime($encounter->encounter_date)));
                if ($encounter->department) {
                    $pv1->setAssignedPatientLocation(implode('^', array(
                        $order->ward_id,
                        '',
                        '',
                        \Yii::app()->params['FACILITY_NAME']
                    )));
                }
                break;

            case EncounterType::TYPE_OP_INPATIENT:
            case EncounterType::TYPE_ER_INPATIENT:
            case EncounterType::TYPE_INPATIENT:
                $pv1->setPatientClass(PatientClass::INPATIENT);
                $pv1->setPatientType(PatientClass::INPATIENT);
                $pv1->setAdmitDateTime(date('YmdHis', strtotime($encounter->admission_dt)));
                if (!is_null($order)) {
                    if ($order->ward) {
                        $pv1->setAssignedPatientLocation(implode('^', array(
                            $order->ward_id,
                            \EncounterLocation::getRoomNo($encounter->encounter_nr),
                            '',
                            \Yii::app()->params['FACILITY_NAME']
                        )));
                    } 
                }
                break;

            default:
                // Unknown
                $pv1->setPatientClass(PatientClass::UNKNOWN);
                $pv1->setPatientType(PatientClass::UNKNOWN);
        }

        return $pv1;
    }

}
