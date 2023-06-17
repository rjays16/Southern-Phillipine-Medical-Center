<?php

/**
 * ORMO01Factory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\jnj\factories\messages;

use DateTime;
use SegHis\modules\laboratory\models\LaboratoryRequest;
use SegHis\modules\laboratory\models\LaboratoryRequestItem;
use SegHEIRS\modules\integrations\hl7\codes\AllergenType;
use SegHEIRS\modules\integrations\hl7\codes\OrderControl;
use SegHEIRS\modules\integrations\hl7\codes\OrderStatus;
use SegHEIRS\modules\integrations\hl7\codes\PatientClass;
use SegHEIRS\modules\integrations\hl7\codes\PersonLocationType;
use SegHEIRS\modules\integrations\hl7\codes\Priority;
use SegHEIRS\modules\integrations\hl7\codes\TransportationMode;
use SegHEIRS\modules\integrations\hl7\factories\data\XCNFactory;
use SegHEIRS\modules\integrations\hl7\factories\segments\DG1Factory;
use SegHEIRS\modules\integrations\hl7\factories\segments\PV1Factory;
use SegHEIRS\modules\integrations\hl7\Message;
use SegHEIRS\modules\integrations\hl7\segments\AL1;
use SegHEIRS\modules\integrations\hl7\segments\OBR;
use SegHEIRS\modules\integrations\hl7\segments\ORC;
use SegHEIRS\modules\integrations\hl7\segments\PID;
use SegHEIRS\modules\integrations\modules\jnj\factories\segments\MSHFactory;
use SegHEIRS\modules\integrations\modules\jnj\factories\segments\PIDFactory;
use SegHEIRS\modules\triage\services\TracerFieldMapper;
use SegHEIRS\modules\packageManager\models\LabServices;

/**
 *
 * Description of ORMO01Factory
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ORMO01Factory
{

    /**
     * @param LaboratoryRequest $header
     * @param LaboratoryRequestItem[] $details
     *
     * @return Message
     */
    public function createFromDetails(LaboratoryRequest $header, $details = array())
    {
        $message = new Message();
                               
        $mshFactory = new MSHFactory();                         
        $message->addSegment($mshFactory->create()->setMessageType('ORM^O01'));
                
        $this->addPIDToMessage($message, $header);        
                      
        if ($header->encounter) {
            $this->addPV1ToMessage($message, $header);
        }
              
        $detail = current($details);

        $orc = $this->addORCToMessage($message, $header, $detail);
        $orc->setOrderControl(OrderControl::NEW_ORDER)
            ->setOrderStatus(OrderStatus::IN_PROCESS);
        $obr = $this->addOBRToMessage($message, 1, $header, $detail);
        $obr->setPlacerOrderNumber($orc->getPlacerOrderNumber());

        $orderNumbers = array();
        foreach ($details as $detail) {
            // Exclude service which is not CBG.
            if ($detail->service && ($detail->service_code === LabServices::CBG)) {
                $orderNumbers[] = $detail->service_code . '^' . $detail->service->name;
            }
        }

        // Universal Identifier
        $obr->setField(4, implode('~', $orderNumbers));
        return $message;
    }


    /**
     * @param LaboratoryRequestItem $detail
     *
     * @return Message
     */
    public function createFromOrderDetail(LaboratoryRequestItem $detail)
    {
        $message = new Message();
        
        $mshFactory = new MSHFactory();
        $message->addSegment($mshFactory->create()->setMessageType('ORM^O01'));

        $lab = $detail->request;

        $this->addPIDToMessage($message, $lab);

        if ($lab->encounter) {
            $this->addPV1ToMessage($message, $lab);
        }

        $orc = $this->addORCToMessage($message, $lab, $detail);
        $orc->setOrderControl(OrderControl::NEW_ORDER)
            ->setOrderStatus(OrderStatus::IN_PROCESS);

        $obr = $this->addOBRToMessage($message, 1, $lab, $detail);
        $obr->setPlacerOrderNumber($orc->getPlacerOrderNumber());

        return $message;
    }

    /**
     * @param LaboratoryRequest $lab
     *
     * @return Message
     */
    public function createOrder(LaboratoryRequest $lab)
    {
        $message = new Message();
        
        $mshFactory = new MSHFactory();
        $message->addSegment($mshFactory->create()->setMessageType('ORM^O01'));

        $this->addPIDToMessage($message, $lab);

        if ($lab->encounter) {
            $this->addPV1ToMessage($message, $lab);
        }

        foreach ($lab->items as $i => $detail) {
            $orc = $this->addORCToMessage($message, $lab, $detail);
            $orc->setOrderControl(OrderControl::NEW_ORDER)
                ->setOrderStatus(OrderStatus::IN_PROCESS);
            $obr = $this->addOBRToMessage($message, $i + 1, $detail->request, $detail);
            $obr->setPlacerOrderNumber($orc->getPlacerOrderNumber());
        }

        return $message;
    }

    /**
     * @param LaboratoryRequest $lab
     *
     * @return Message
     */
    public function updateOrder(LaboratoryRequest $lab)
    {
        $message = new Message();
        
        $mshFactory = new MSHFactory();
        $message->addSegment($mshFactory->create()->setMessageType('ORM^O01'));

        $this->addPIDToMessage($message, $lab);

        if ($lab->encounter) {
            $this->addPV1ToMessage($message, $lab);
        }

        foreach ($lab->items as $i => $detail) {
            $orc = $this->addORCToMessage($message, $lab, $detail);
            $orc->setOrderControl(OrderControl::ORDER_REPLACED)
                ->setOrderStatus(OrderStatus::IN_PROCESS);
            $obr = $this->addOBRToMessage($message, $i + 1, $detail->request, $detail);
            $obr->setPlacerOrderNumber($orc->getPlacerOrderNumber());
        }

        return $message;
    }

    /**
     * @param LaboratoryRequestItem $detail
     *
     * @return Message
     */
    public function cancelOrder(LaboratoryRequestItem $detail)
    {
        $message = new Message();
        $lab = $detail->request;
        
        $mshFactory = new MSHFactory();
        $message->addSegment($mshFactory->create()->setMessageType('ORM^O01'));

        $this->addPIDToMessage($message, $lab);

        if ($lab->encounter) {
            $this->addPV1ToMessage($message, $lab);
        }

        $orc = $this->addORCToMessage($message, $lab, $detail);
        $orc->setOrderControl(OrderControl::CANCEL_ORDER)
            ->setOrderStatus(OrderStatus::CANCELLED);

        $obr = $this->addOBRToMessage($message, 1, $detail->request, $detail);
        $obr->setPlacerOrderNumber($orc->getPlacerOrderNumber());

        return $message;
    }

    /**
     * @param Message $message
     * @param LaboratoryRequest $header
     *
     * @return PID
     */
    private function addPIDToMessage(Message $message, LaboratoryRequest $header)
    {        
        $pidFactory = new PIDFactory();                    
        $pid = $pidFactory->create($header->person);                
        $message->addSegment($pid);
        return $pid;
    }

    /**
     * @param Message $message
     * @param LaborderH $header
     *
     * @return null|static
     */
    private function addPV1ToMessage(Message $message, LaboratoryRequest $header)
    {
        $encounter = $header->encounter;

        $pv1Factory = new PV1Factory();
        $pv1 = $pv1Factory->create($encounter)
            ->setSequenceId(1)
            ->setVisitIndicator('V');


        $pv1->setAssignedPatientLocation(implode('^', array(
            $encounter->department->name_formal, 
            '',
            '',
            \Yii::app()->params['FACILITY_NAME'],
            '',
            PersonLocationType::CLINIC
        )));        
                                
        $deptEncounter = $encounter->getCurrentDeptEncounter();
        if ($deptEncounter->deptenc_code === \DeptEncounter::TYPE_EMERGENCY) {
            $pv1->setPatientClass(PatientClass::EMERGENCY);

            if ($deptEncounter->erArea) {
                $pv1->setAssignedPatientLocation(implode('^', array(
                        $deptEncounter->erArea->area_desc,
                        '',
                        '',
                        \Yii::app()->params['FACILITY_NAME'],
                        '',
                        PersonLocationType::CLINIC
                    ) 
                ));
            }
        } elseif ($deptEncounter->deptenc_code === \DeptEncounter::TYPE_OUTPATIENT) {

            $pv1->setPatientClass(PatientClass::OUTPATIENT);
            if ($deptEncounter->opArea) {
                $pv1->setAssignedPatientLocation(implode('^', array(
                    $deptEncounter->opArea->area_desc,
                    '',
                    '',
                    \Yii::app()->params['FACILITY_NAME'],
                    '',
                    PersonLocationType::CLINIC
                )));
            }

        } elseif ($deptEncounter->deptenc_code === \DeptEncounter::TYPE_INPATIENT) {
            $pv1->setPatientClass(PatientClass::INPATIENT);
        } else {
            $pv1->setPatientClass(PatientClass::UNKNOWN);
        }

        $message->addSegment($pv1);

        $dg1Factory = new DG1Factory();
        foreach ($encounter->diagnoses as $i => $diagnosis) {            
            $message->addSegment(
                $dg1Factory->create($diagnosis)
                    ->setSequenceId($i+1)
            );
        }

        $tracer = new TracerFieldMapper($encounter->spin, 'PatientPreassessment');
        $drugAllergy = $tracer->getField('drug_allergy_text');

        $seqId = 1;
        if ($drugAllergy) {
            $al1 = new AL1();
            $al1->setSequenceId($seqId++)
                ->setAllergenTypeCode(AllergenType::DRUG_ALLERGY.'^Drug Allergy')
                ->setAllergenCodeMnemonicDescription('^'.$drugAllergy);

            $message->addSegment($al1);
        }

        $foodAllergy = $tracer->getField('food_allergy_text');
        if ($foodAllergy) {
            $al1 = new AL1();
            $al1->setSequenceId($seqId)
                ->setAllergenTypeCode(AllergenType::FOOD_ALLERGY.'^Food Allergy')
                ->setAllergenCodeMnemonicDescription('^'.$foodAllergy);
            $message->addSegment($al1);
        }

        return $pv1;
    }

    /**
     * Generate Common Order Entry (ORC) segment
     *
     * @param Message $message
     * @param LaboratoryRequest $lab
     * @param LaboratoryRequestItem $detail
     *
     * @return ORC
     */
    private function addORCToMessage(Message $message, LaboratoryRequest $lab, LaboratoryRequestItem $detail)
    {
        $transDateTime = new DateTime($lab->create_dt);
        
        $xcnFactory = new XCNFactory();
        $createdBy = $xcnFactory->createFromPerson($lab->createdBy->p);

        $refNo = $lab->ref_no;
        if (is_numeric($refNo) && strlen($refNo) === 15) {
            // If the rReference number is in the format of YYYY + 11 other digits
            // We get the last two digits of the year part and add the last 10
            // digits of the reference number to get the condensed format (12 digits
            // total)
            $refNo = substr($refNo, 2, 2) . substr($refNo, -10);
        }

        $newORC = new ORC();
        $orc = $newORC
            ->setPlacerOrderNumber($refNo)
//            ->setPlacerGroupNumber($detail->ref_no)
            ->setQuantityTiming(
                null,
                null,
                null,
                null,
                null,
                $detail->is_stat == 1 ? Priority::STAT : Priority::ROUTINE
            )->setOrderingProvider(trim($lab->prescriber));

        if ($transDateTime !== false) {
            $orc->setTransactionDateTime($transDateTime->format('YmdHis'));
        }

        if ($createdBy) {
            $orc->setEnteredBy($createdBy);
        }

        $message->addSegment($orc);
        return $orc;
    }

    /**
     * Generate Observation Request (OBR) segment
     *
     * @param Message $message
     * @param int $sequenceId
     * @param LaborderH $header
     * @param LaborderD $detail
     *
     * @return OBR
     */
    private function addOBRToMessage(Message $message, $sequenceId, LaboratoryRequest $header, LaboratoryRequestItem $detail)
    {
        /**
         *
         */
        $transDateTime = new DateTime($detail->serve_dt);
        
        $objOBR = new OBR();
        $obr = $objOBR
            ->setSequenceId($sequenceId)
            ->setPlacerOrderNumber($detail->lis_ref_no)
            ->setUniversalServiceIdentifier(
                $detail->service_id,
                $detail->service->service_name,
                null
            )->setPriority($detail->is_stat == 1 ? Priority::STAT : Priority::ROUTINE)
            ->setOrderingProvider($header->prescriber)
            ->setRequestedDateTime($transDateTime->format('YmdHis'))
            ->setTransportationMode(TransportationMode::WALKING);
        $message->addSegment($obr);
        return $obr;
    }
}
