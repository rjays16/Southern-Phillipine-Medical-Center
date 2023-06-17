<?php

/**
 * OBX.php
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
 * Description of OBX
 *
 */

class OBX extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_VALUE_TYPE = 2;
    const INDEX_OBSERVATION_IDENTIFIER = 3;
    const INDEX_OBSERVATION_SUB_ID = 4;
    const INDEX_OBSERVATION_VALUE = 5;
    const INDEX_UNITS = 6;
    const INDEX_REFERENCES_RANGE = 7;
    const INDEX_INTERPRETATION_CODES = 8;
    const INDEX_PROBABILITY = 9;
    const INDEX_NATURE_OF_ABNORMAL_TEST = 10;
    const INDEX_OBSERVATION_RESULT_STATUS = 11;
    const INDEX_EFFECTIVE_DATE_OF_REFERENCE_RANGE = 12;
    const INDEX_USER_DEFINED_ACCESS_CHECKS = 13;
    const INDEX_OBSERVATION_DATE_TIME = 14;
    const INDEX_PRODUCER_ID = 15;
    const INDEX_RESPONSIBLE_OBSERVER = 16;
    const INDEX_OBSERVATION_METHOD = 17;
    const INDEX_EQUIPMENT_INSTANCE_IDENTIFIER = 18;
    const INDEX_ANALYSIS_DATE_TIME = 19;
    const INDEX_OBSERVATION_SITE = 20;
    const INDEX_OBSERVATION_INSTANCE_IDENTIFIER = 21;
    const INDEX_MOOD_CODE = 22;
    const INDEX_PERFORMING_ORGANIZATION_NAME = 23;
    const INDEX_PERFORMING_ORGANIZATION_ADDRESS = 24;
    const INDEX_PERFORMING_ORGANIZATION_MEDICAL_DIRECTOR = 25;
    const INDEX_PATIENT_RESULTS_RELEASE_CATEGORY = 26;
    const INDEX_ROOT_CAUSE = 27;
    const INDEX_LOCAL_PROCESS_CONTROL = 28;
    const INDEX_OBSERVATION_TYPE = 29;
    const INDEX_OBSERVATION_SUB_TYPE = 30;

    /** @var string */
    public $name  = 'OBX';

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
    public function setValueType($value)
    {
        $this->setField(self::INDEX_VALUE_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getValueType()
    {
        return $this->getField(self::INDEX_VALUE_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationIdentifier($value)
    {
        $this->setField(self::INDEX_OBSERVATION_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationIdentifier()
    {
        return $this->getField(self::INDEX_OBSERVATION_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationSubId($value)
    {
        $this->setField(self::INDEX_OBSERVATION_SUB_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationSubId()
    {
        return $this->getField(self::INDEX_OBSERVATION_SUB_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationValue($value)
    {
        $this->setField(self::INDEX_OBSERVATION_VALUE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationValue()
    {
        $value = $this->getField(self::INDEX_OBSERVATION_VALUE);
        if (is_array($value)) {
            return $value;
        }

        return HL7::decode($value);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setUnits($value)
    {
        $this->setField(self::INDEX_UNITS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getUnits()
    {
        return $this->getField(self::INDEX_UNITS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReferencesRange($value)
    {
        $this->setField(self::INDEX_REFERENCES_RANGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReferencesRange()
    {
        return $this->getField(self::INDEX_REFERENCES_RANGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInterpretationCodes($value)
    {
        $this->setField(self::INDEX_INTERPRETATION_CODES, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInterpretationCodes()
    {
        return $this->getField(self::INDEX_INTERPRETATION_CODES);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProbability($value)
    {
        $this->setField(self::INDEX_PROBABILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProbability()
    {
        return $this->getField(self::INDEX_PROBABILITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNatureOfAbnormalTest($value)
    {
        $this->setField(self::INDEX_NATURE_OF_ABNORMAL_TEST, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNatureOfAbnormalTest()
    {
        return $this->getField(self::INDEX_NATURE_OF_ABNORMAL_TEST);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationResultStatus($value)
    {
        $this->setField(self::INDEX_OBSERVATION_RESULT_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationResultStatus()
    {
        return $this->getField(self::INDEX_OBSERVATION_RESULT_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEffectiveDateOfReferenceRange($value)
    {
        $this->setField(self::INDEX_EFFECTIVE_DATE_OF_REFERENCE_RANGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEffectiveDateOfReferenceRange()
    {
        return $this->getField(self::INDEX_EFFECTIVE_DATE_OF_REFERENCE_RANGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setUserDefinedAccessChecks($value)
    {
        $this->setField(self::INDEX_USER_DEFINED_ACCESS_CHECKS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getUserDefinedAccessChecks()
    {
        return $this->getField(self::INDEX_USER_DEFINED_ACCESS_CHECKS);
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
    public function setProducerId($value)
    {
        $this->setField(self::INDEX_PRODUCER_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProducerId()
    {
        return $this->getField(self::INDEX_PRODUCER_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setResponsibleObserver($value)
    {
        $this->setField(self::INDEX_RESPONSIBLE_OBSERVER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getResponsibleObserver()
    {
        return $this->getField(self::INDEX_RESPONSIBLE_OBSERVER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationMethod($value)
    {
        $this->setField(self::INDEX_OBSERVATION_METHOD, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationMethod()
    {
        return $this->getField(self::INDEX_OBSERVATION_METHOD);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEquipmentInstanceIdentifier($value)
    {
        $this->setField(self::INDEX_EQUIPMENT_INSTANCE_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEquipmentInstanceIdentifier()
    {
        return $this->getField(self::INDEX_EQUIPMENT_INSTANCE_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAnalysisDateTime($value)
    {
        $this->setField(self::INDEX_ANALYSIS_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAnalysisDateTime()
    {
        return $this->getField(self::INDEX_ANALYSIS_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationSite($value)
    {
        $this->setField(self::INDEX_OBSERVATION_SITE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationSite()
    {
        return $this->getField(self::INDEX_OBSERVATION_SITE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationInstanceIdentifier($value)
    {
        $this->setField(self::INDEX_OBSERVATION_INSTANCE_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationInstanceIdentifier()
    {
        return $this->getField(self::INDEX_OBSERVATION_INSTANCE_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMoodCode($value)
    {
        $this->setField(self::INDEX_MOOD_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMoodCode()
    {
        return $this->getField(self::INDEX_MOOD_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPerformingOrganizationName($value)
    {
        $this->setField(self::INDEX_PERFORMING_ORGANIZATION_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPerformingOrganizationName()
    {
        return $this->getField(self::INDEX_PERFORMING_ORGANIZATION_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPerformingOrganizationAddress($value)
    {
        $this->setField(self::INDEX_PERFORMING_ORGANIZATION_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPerformingOrganizationAddress()
    {
        return $this->getField(self::INDEX_PERFORMING_ORGANIZATION_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPerformingOrganizationMedicalDirector($value)
    {
        $this->setField(self::INDEX_PERFORMING_ORGANIZATION_MEDICAL_DIRECTOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPerformingOrganizationMedicalDirector()
    {
        return $this->getField(self::INDEX_PERFORMING_ORGANIZATION_MEDICAL_DIRECTOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientResultsReleaseCategory($value)
    {
        $this->setField(self::INDEX_PATIENT_RESULTS_RELEASE_CATEGORY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientResultsReleaseCategory()
    {
        return $this->getField(self::INDEX_PATIENT_RESULTS_RELEASE_CATEGORY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRootCause($value)
    {
        $this->setField(self::INDEX_ROOT_CAUSE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRootCause()
    {
        return $this->getField(self::INDEX_ROOT_CAUSE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setLocalProcessControl($value)
    {
        $this->setField(self::INDEX_LOCAL_PROCESS_CONTROL, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getLocalProcessControl()
    {
        return $this->getField(self::INDEX_LOCAL_PROCESS_CONTROL);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationType($value)
    {
        $this->setField(self::INDEX_OBSERVATION_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationType()
    {
        return $this->getField(self::INDEX_OBSERVATION_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setObservationSubType($value)
    {
        $this->setField(self::INDEX_OBSERVATION_SUB_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getObservationSubType()
    {
        return $this->getField(self::INDEX_OBSERVATION_SUB_TYPE);
    }



}