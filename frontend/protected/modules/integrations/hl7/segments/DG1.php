<?php

/**
 * DG1.php
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
 * Description of DG1
 *
 */

class DG1 extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_DIAGNOSIS_CODING_METHOD = 2;
    const INDEX_DIAGNOSIS_CODE = 3;
    const INDEX_DIAGNOSIS_DESCRIPTION = 4;
    const INDEX_DIAGNOSIS_DATE_TIME = 5;
    const INDEX_DIAGNOSIS_TYPE = 6;
    const INDEX_MAJOR_DIAGNOSTIC_CATEGORY = 7;
    const INDEX_DIAGNOSTIC_RELATED_GROUP = 8;
    const INDEX_DRG_APPROVAL_INDICATOR = 9;
    const INDEX_DRG_GROUPER_REVIEW_CODE = 10;
    const INDEX_OUTLIER_TYPE = 11;
    const INDEX_OUTLIER_DAYS = 12;
    const INDEX_OUTLIER_COST = 13;
    const INDEX_GROUPER_VERSION_AND_TYPE = 14;
    const INDEX_DIAGNOSIS_PRIORITY = 15;
    const INDEX_DIAGNOSING_CLINICIAN = 16;
    const INDEX_DIAGNOSIS_CLASSIFICATION = 17;
    const INDEX_CONFIDENTIAL_INDICATOR = 18;
    const INDEX_ATTESTATION_DATE_TIME = 19;
    const INDEX_DIAGNOSIS_IDENTIFIER = 20;
    const INDEX_DIAGNOSIS_ACTION_CODE = 21;
    const INDEX_PARENT_DIAGNOSIS = 22;
    const INDEX_DRG_CCL_VALUE_CODE = 23;
    const INDEX_DRG_GROUPING_USAGE = 24;
    const INDEX_DRG_DIAGNOSIS_DETERMINATION_STATUS = 25;
    const INDEX_PRESENT_ON_ADMISSION_INDICATOR = 26;

    /** @var string */
    public $name = 'DG1';

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
    public function setDiagnosisCodingMethod($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_CODING_METHOD, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisCodingMethod()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_CODING_METHOD);
    }

    /**
     * @param string|null $originalText
     * @param string $identifier
     * @param string|null $text
     * @param string|null $codingSystem
     * @param string|null $altIdentifier
     * @param string|null $altText
     * @param string|null $altCodingSystem
     * @param string|null $codingSystemVersionId
     * @param string|null $altCdingSystemVersionId
     *
     * @return static
     */
    public function setDiagnosisCode(
        $originalText=null,
        $identifier=null,
        $text=null,
        $codingSystem=null,
        $altIdentifier=null,
        $altText=null,
        $altCodingSystem=null,
        $codingSystemVersionId=null,
        $altCdingSystemVersionId=null
    )
    {
        $values = [
            $identifier,
            $text,
            $codingSystem,
            $altIdentifier,
            $altText,
            $altCodingSystem,
            $codingSystemVersionId,
            $altCdingSystemVersionId,
            $originalText,
        ];
        $this->setField(self::INDEX_DIAGNOSIS_CODE, HL7::encodeValues($values));
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisCode()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisDescription($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_DESCRIPTION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisDescription()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_DESCRIPTION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisDateTime($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisDateTime()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisType($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisType()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMajorDiagnosticCategory($value)
    {
        $this->setField(self::INDEX_MAJOR_DIAGNOSTIC_CATEGORY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMajorDiagnosticCategory()
    {
        return $this->getField(self::INDEX_MAJOR_DIAGNOSTIC_CATEGORY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosticRelatedGroup($value)
    {
        $this->setField(self::INDEX_DIAGNOSTIC_RELATED_GROUP, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosticRelatedGroup()
    {
        return $this->getField(self::INDEX_DIAGNOSTIC_RELATED_GROUP);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgApprovalIndicator($value)
    {
        $this->setField(self::INDEX_DRG_APPROVAL_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgApprovalIndicator()
    {
        return $this->getField(self::INDEX_DRG_APPROVAL_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgGrouperReviewCode($value)
    {
        $this->setField(self::INDEX_DRG_GROUPER_REVIEW_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgGrouperReviewCode()
    {
        return $this->getField(self::INDEX_DRG_GROUPER_REVIEW_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOutlierType($value)
    {
        $this->setField(self::INDEX_OUTLIER_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOutlierType()
    {
        return $this->getField(self::INDEX_OUTLIER_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOutlierDays($value)
    {
        $this->setField(self::INDEX_OUTLIER_DAYS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOutlierDays()
    {
        return $this->getField(self::INDEX_OUTLIER_DAYS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOutlierCost($value)
    {
        $this->setField(self::INDEX_OUTLIER_COST, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOutlierCost()
    {
        return $this->getField(self::INDEX_OUTLIER_COST);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setGrouperVersionAndType($value)
    {
        $this->setField(self::INDEX_GROUPER_VERSION_AND_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getGrouperVersionAndType()
    {
        return $this->getField(self::INDEX_GROUPER_VERSION_AND_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisPriority($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_PRIORITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisPriority()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_PRIORITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosingClinician($value)
    {
        $this->setField(self::INDEX_DIAGNOSING_CLINICIAN, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosingClinician()
    {
        return $this->getField(self::INDEX_DIAGNOSING_CLINICIAN);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisClassification($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_CLASSIFICATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisClassification()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_CLASSIFICATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setConfidentialIndicator($value)
    {
        $this->setField(self::INDEX_CONFIDENTIAL_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getConfidentialIndicator()
    {
        return $this->getField(self::INDEX_CONFIDENTIAL_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAttestationDateTime($value)
    {
        $this->setField(self::INDEX_ATTESTATION_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAttestationDateTime()
    {
        return $this->getField(self::INDEX_ATTESTATION_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisIdentifier($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisIdentifier()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDiagnosisActionCode($value)
    {
        $this->setField(self::INDEX_DIAGNOSIS_ACTION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDiagnosisActionCode()
    {
        return $this->getField(self::INDEX_DIAGNOSIS_ACTION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentDiagnosis($value)
    {
        $this->setField(self::INDEX_PARENT_DIAGNOSIS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentDiagnosis()
    {
        return $this->getField(self::INDEX_PARENT_DIAGNOSIS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgCclValueCode($value)
    {
        $this->setField(self::INDEX_DRG_CCL_VALUE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgCclValueCode()
    {
        return $this->getField(self::INDEX_DRG_CCL_VALUE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgGroupingUsage($value)
    {
        $this->setField(self::INDEX_DRG_GROUPING_USAGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgGroupingUsage()
    {
        return $this->getField(self::INDEX_DRG_GROUPING_USAGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgDiagnosisDeterminationStatus($value)
    {
        $this->setField(self::INDEX_DRG_DIAGNOSIS_DETERMINATION_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgDiagnosisDeterminationStatus()
    {
        return $this->getField(self::INDEX_DRG_DIAGNOSIS_DETERMINATION_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPresentOnAdmissionIndicator($value)
    {
        $this->setField(self::INDEX_PRESENT_ON_ADMISSION_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPresentOnAdmissionIndicator()
    {
        return $this->getField(self::INDEX_PRESENT_ON_ADMISSION_INDICATOR);
    }

}