<?php

/**
 * OBR.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of OBR
 *
 */

class OBR extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_PLACER_ORDER_NUMBER = 2;
    const INDEX_FILLER_ORDER_NUMBER = 3;
    const INDEX_UNIVERSAL_SERVICE_IDENTIFIER = 4;
    const INDEX_PRIORITY = 5;
    const INDEX_REQUESTED_DATE_TIME = 6;
    const INDEX_OBSERVATION_DATE_TIME = 7;
    const INDEX_OBSERVATION_END_DATE_TIME = 8;
    const INDEX_COLLECTION_VOLUME = 9;
    const INDEX_COLLECTOR_IDENTIFIER = 10;
    const INDEX_SPECIMEN_ACTION_CODE = 11;
    const INDEX_DANGER_CODE = 12;
    const INDEX_RELEVANT_CLINICAL_INFORMATION = 13;
    const INDEX_SPECIMEN_RECEIVED_DATE_TIME = 14;
    const INDEX_SPECIMEN_SOURCE = 15;
    const INDEX_ORDERING_PROVIDER = 16;
    const INDEX_CALLBACK_PHONE_NUMBER = 17;
    const INDEX_PLACER_FIELD1 = 18;
    const INDEX_PLACER_FIELD2 = 19;
    const INDEX_FILLER_FIELD1 = 20;
    const INDEX_FILLER_FIELD2 = 21;
    const INDEX_RESULT_DATE_TIME = 22;
    const INDEX_CHARGE_TO_PRACTICE = 23;
    const INDEX_DIAGNOSTIC_SERVICE_SECTION_ID = 24;
    const INDEX_RESULT_STATUS = 25;
    const INDEX_PARENT_RESULT = 26;
    const INDEX_QUANTITY_TIMING = 27;
    const INDEX_RESULT_COPIES_TO = 28;
    const INDEX_PARENT_RESULTS_OBSERVATION_IDENTIFIER = 29;
    const INDEX_TRANSPORTATION_MODE = 30;
    const INDEX_REASON_FOR_STUDY = 31;
    const INDEX_PRINCIPAL_RESULT_INTERPRETER = 32;
    const INDEX_ASSISTANT_RESULT_INTERPRETER = 33;
    const INDEX_TECHNICIAN = 34;
    const INDEX_TRANSCRIPTIONIST = 35;
    const INDEX_SCHEDULED_DATE_TIME = 36;
    const INDEX_NUMBER_OF_SAMPLE_CONTAINERS = 37;
    const INDEX_TRANSPORT_LOGISTICS_OF_COLLECTED_SAMPLE = 38;
    const INDEX_COLLECTOR_COMMENT = 39;
    const INDEX_TRANSPORT_ARRANGEMENT_RESPONSIBILITY = 40;
    const INDEX_TRANSPORT_ARRANGED = 41;
    const INDEX_ESCORT_REQUIRED = 42;
    const INDEX_PLANNED_PATIENT_TRANSPORT_COMMENT = 43;
    const INDEX_PROCEDURE_CODE = 44;
    const INDEX_PROCEDURE_CODE_MODIFIER = 45;
    const INDEX_PLACER_SUPPLEMENTAL_SERVICE_INFORMATION = 46;
    const INDEX_FILLER_SUPPLEMENTAL_SERVICE_INFORMATION = 47;
    const INDEX_MEDICALLY_NECESSARY_DUPLICATE_PROCEDURE_REASON = 48;
    const INDEX_RESULT_HANDLING = 49;
    const INDEX_PARENT_UNIVERSAL_SERVICE_IDENTIFIER = 50;
    const INDEX_OBSERVATION_GROUP_ID = 51;
    const INDEX_PARENT_OBSERVATION_GROUP_ID = 52;
    const INDEX_ALTERNATE_PLACER_ORDER_NUMBER = 53;
    const INDEX_PARENT_ORDER = 54;

    /** @var string */
    public $name = 'OBR';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSequenceId($value)
    {
        $this->setField(self::INDEX_SEQUENCE_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSequenceId()
    {
        return $this->getField(self::INDEX_SEQUENCE_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlacerOrderNumber($value)
    {
        $this->setField(self::INDEX_PLACER_ORDER_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacerOrderNumber()
    {
        return $this->getField(self::INDEX_PLACER_ORDER_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFillerOrderNumber($value)
    {
        $this->setField(self::INDEX_FILLER_ORDER_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFillerOrderNumber()
    {
        return $this->getField(self::INDEX_FILLER_ORDER_NUMBER);
    }

    /**
     * @param string $identifier
     * @param string $text
     * @param string $codingSystem
     *
     * @return static
     *
     */
    public function setUniversalServiceIdentifier($identifier, $text, $codingSystem='')
    {
        $values = array(
            $identifier,
            $text,
            $codingSystem
        );
        $this->setField(self::INDEX_UNIVERSAL_SERVICE_IDENTIFIER, HL7::encodeValues($values));
        return $this;
    }

    /**
     * @return string
     */
    public function getUniversalServiceIdentifier()
    {
        return $this->getField(self::INDEX_UNIVERSAL_SERVICE_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPriority($value)
    {
        $this->setField(self::INDEX_PRIORITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->getField(self::INDEX_PRIORITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRequestedDateTime($value)
    {
        $this->setField(self::INDEX_REQUESTED_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestedDateTime()
    {
        return $this->getField(self::INDEX_REQUESTED_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationDateTime($value)
    {
        $this->setField(self::INDEX_OBSERVATION_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationDateTime()
    {
        return $this->getField(self::INDEX_OBSERVATION_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationEndDateTime($value)
    {
        $this->setField(self::INDEX_OBSERVATION_END_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationEndDateTime()
    {
        return $this->getField(self::INDEX_OBSERVATION_END_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCollectionVolume($value)
    {
        $this->setField(self::INDEX_COLLECTION_VOLUME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCollectionVolume()
    {
        return $this->getField(self::INDEX_COLLECTION_VOLUME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCollectorIdentifier($value)
    {
        $this->setField(self::INDEX_COLLECTOR_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCollectorIdentifier()
    {
        return $this->getField(self::INDEX_COLLECTOR_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSpecimenActionCode($value)
    {
        $this->setField(self::INDEX_SPECIMEN_ACTION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecimenActionCode()
    {
        return $this->getField(self::INDEX_SPECIMEN_ACTION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDangerCode($value)
    {
        $this->setField(self::INDEX_DANGER_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDangerCode()
    {
        return $this->getField(self::INDEX_DANGER_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRelevantClinicalInformation($value)
    {
        $this->setField(self::INDEX_RELEVANT_CLINICAL_INFORMATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRelevantClinicalInformation()
    {
        return $this->getField(self::INDEX_RELEVANT_CLINICAL_INFORMATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSpecimenReceivedDateTime($value)
    {
        $this->setField(self::INDEX_SPECIMEN_RECEIVED_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecimenReceivedDateTime()
    {
        return $this->getField(self::INDEX_SPECIMEN_RECEIVED_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSpecimenSource($value)
    {
        $this->setField(self::INDEX_SPECIMEN_SOURCE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecimenSource()
    {
        return $this->getField(self::INDEX_SPECIMEN_SOURCE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderingProvider($value)
    {
        $this->setField(self::INDEX_ORDERING_PROVIDER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderingProvider()
    {
        return $this->getField(self::INDEX_ORDERING_PROVIDER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCallbackPhoneNumber($value)
    {
        $this->setField(self::INDEX_CALLBACK_PHONE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCallbackPhoneNumber()
    {
        return $this->getField(self::INDEX_CALLBACK_PHONE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlacerField1($value)
    {
        $this->setField(self::INDEX_PLACER_FIELD1, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacerField1()
    {
        return $this->getField(self::INDEX_PLACER_FIELD1);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlacerField2($value)
    {
        $this->setField(self::INDEX_PLACER_FIELD2, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacerField2()
    {
        return $this->getField(self::INDEX_PLACER_FIELD2);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFillerField1($value)
    {
        $this->setField(self::INDEX_FILLER_FIELD1, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFillerField1()
    {
        return $this->getField(self::INDEX_FILLER_FIELD1);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFillerField2($value)
    {
        $this->setField(self::INDEX_FILLER_FIELD2, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFillerField2()
    {
        return $this->getField(self::INDEX_FILLER_FIELD2);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setResultDateTime($value)
    {
        $this->setField(self::INDEX_RESULT_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getResultDateTime()
    {
        return $this->getField(self::INDEX_RESULT_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setChargeToPractice($value)
    {
        $this->setField(self::INDEX_CHARGE_TO_PRACTICE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getChargeToPractice()
    {
        return $this->getField(self::INDEX_CHARGE_TO_PRACTICE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosticServiceSectionId($value)
    {
        $this->setField(self::INDEX_DIAGNOSTIC_SERVICE_SECTION_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosticServiceSectionId()
    {
        return $this->getField(self::INDEX_DIAGNOSTIC_SERVICE_SECTION_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setResultStatus($value)
    {
        $this->setField(self::INDEX_RESULT_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getResultStatus()
    {
        return $this->getField(self::INDEX_RESULT_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentResult($value)
    {
        $this->setField(self::INDEX_PARENT_RESULT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentResult()
    {
        return $this->getField(self::INDEX_PARENT_RESULT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setQuantityTiming($value)
    {
        $this->setField(self::INDEX_QUANTITY_TIMING, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getQuantityTiming()
    {
        return $this->getField(self::INDEX_QUANTITY_TIMING);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setResultCopiesTo($value)
    {
        $this->setField(self::INDEX_RESULT_COPIES_TO, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getResultCopiesTo()
    {
        return $this->getField(self::INDEX_RESULT_COPIES_TO);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentResultsObservationIdentifier($value)
    {
        $this->setField(self::INDEX_PARENT_RESULTS_OBSERVATION_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentResultsObservationIdentifier()
    {
        return $this->getField(self::INDEX_PARENT_RESULTS_OBSERVATION_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransportationMode($value)
    {
        $this->setField(self::INDEX_TRANSPORTATION_MODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransportationMode()
    {
        return $this->getField(self::INDEX_TRANSPORTATION_MODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReasonForStudy($value)
    {
        $this->setField(self::INDEX_REASON_FOR_STUDY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReasonForStudy()
    {
        return $this->getField(self::INDEX_REASON_FOR_STUDY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPrincipalResultInterpreter($value)
    {
        $this->setField(self::INDEX_PRINCIPAL_RESULT_INTERPRETER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrincipalResultInterpreter()
    {
        return $this->getField(self::INDEX_PRINCIPAL_RESULT_INTERPRETER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAssistantResultInterpreter($value)
    {
        $this->setField(self::INDEX_ASSISTANT_RESULT_INTERPRETER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAssistantResultInterpreter()
    {
        return $this->getField(self::INDEX_ASSISTANT_RESULT_INTERPRETER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTechnician($value)
    {
        $this->setField(self::INDEX_TECHNICIAN, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTechnician()
    {
        return $this->getField(self::INDEX_TECHNICIAN);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTranscriptionist($value)
    {
        $this->setField(self::INDEX_TRANSCRIPTIONIST, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTranscriptionist()
    {
        return $this->getField(self::INDEX_TRANSCRIPTIONIST);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setScheduledDateTime($value)
    {
        $this->setField(self::INDEX_SCHEDULED_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getScheduledDateTime()
    {
        return $this->getField(self::INDEX_SCHEDULED_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNumberOfSampleContainers($value)
    {
        $this->setField(self::INDEX_NUMBER_OF_SAMPLE_CONTAINERS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNumberOfSampleContainers()
    {
        return $this->getField(self::INDEX_NUMBER_OF_SAMPLE_CONTAINERS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransportLogisticsOfCollectedSample($value)
    {
        $this->setField(self::INDEX_TRANSPORT_LOGISTICS_OF_COLLECTED_SAMPLE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransportLogisticsOfCollectedSample()
    {
        return $this->getField(self::INDEX_TRANSPORT_LOGISTICS_OF_COLLECTED_SAMPLE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCollectorComment($value)
    {
        $this->setField(self::INDEX_COLLECTOR_COMMENT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCollectorComment()
    {
        return $this->getField(self::INDEX_COLLECTOR_COMMENT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransportArrangementResponsibility($value)
    {
        $this->setField(self::INDEX_TRANSPORT_ARRANGEMENT_RESPONSIBILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransportArrangementResponsibility()
    {
        return $this->getField(self::INDEX_TRANSPORT_ARRANGEMENT_RESPONSIBILITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransportArranged($value)
    {
        $this->setField(self::INDEX_TRANSPORT_ARRANGED, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransportArranged()
    {
        return $this->getField(self::INDEX_TRANSPORT_ARRANGED);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEscortRequired($value)
    {
        $this->setField(self::INDEX_ESCORT_REQUIRED, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEscortRequired()
    {
        return $this->getField(self::INDEX_ESCORT_REQUIRED);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlannedPatientTransportComment($value)
    {
        $this->setField(self::INDEX_PLANNED_PATIENT_TRANSPORT_COMMENT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlannedPatientTransportComment()
    {
        return $this->getField(self::INDEX_PLANNED_PATIENT_TRANSPORT_COMMENT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureCode($value)
    {
        $this->setField(self::INDEX_PROCEDURE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureCode()
    {
        return $this->getField(self::INDEX_PROCEDURE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureCodeModifier($value)
    {
        $this->setField(self::INDEX_PROCEDURE_CODE_MODIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureCodeModifier()
    {
        return $this->getField(self::INDEX_PROCEDURE_CODE_MODIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlacerSupplementalServiceInformation($value)
    {
        $this->setField(self::INDEX_PLACER_SUPPLEMENTAL_SERVICE_INFORMATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacerSupplementalServiceInformation()
    {
        return $this->getField(self::INDEX_PLACER_SUPPLEMENTAL_SERVICE_INFORMATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFillerSupplementalServiceInformation($value)
    {
        $this->setField(self::INDEX_FILLER_SUPPLEMENTAL_SERVICE_INFORMATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFillerSupplementalServiceInformation()
    {
        return $this->getField(self::INDEX_FILLER_SUPPLEMENTAL_SERVICE_INFORMATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMedicallyNecessaryDuplicateProcedureReason($value)
    {
        $this->setField(self::INDEX_MEDICALLY_NECESSARY_DUPLICATE_PROCEDURE_REASON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMedicallyNecessaryDuplicateProcedureReason()
    {
        return $this->getField(self::INDEX_MEDICALLY_NECESSARY_DUPLICATE_PROCEDURE_REASON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setResultHandling($value)
    {
        $this->setField(self::INDEX_RESULT_HANDLING, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getResultHandling()
    {
        return $this->getField(self::INDEX_RESULT_HANDLING);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentUniversalServiceIdentifier($value)
    {
        $this->setField(self::INDEX_PARENT_UNIVERSAL_SERVICE_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentUniversalServiceIdentifier()
    {
        return $this->getField(self::INDEX_PARENT_UNIVERSAL_SERVICE_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationGroupId($value)
    {
        $this->setField(self::INDEX_OBSERVATION_GROUP_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationGroupId()
    {
        return $this->getField(self::INDEX_OBSERVATION_GROUP_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentObservationGroupId($value)
    {
        $this->setField(self::INDEX_PARENT_OBSERVATION_GROUP_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentObservationGroupId()
    {
        return $this->getField(self::INDEX_PARENT_OBSERVATION_GROUP_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAlternatePlacerOrderNumber($value)
    {
        $this->setField(self::INDEX_ALTERNATE_PLACER_ORDER_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAlternatePlacerOrderNumber()
    {
        return $this->getField(self::INDEX_ALTERNATE_PLACER_ORDER_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentOrder($value)
    {
        $this->setField(self::INDEX_PARENT_ORDER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentOrder()
    {
        return $this->getField(self::INDEX_PARENT_ORDER);
    }



}