<?php

/**
 * PR1.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of PR1
 *
 */

class PR1 extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_PROCEDURE_CODING_METHOD = 2;
    const INDEX_PROCEDURE_CODE = 3;
    const INDEX_PROCEDURE_DESCRIPTION = 4;
    const INDEX_PROCEDURE_DATE_TIME = 5;
    const INDEX_PROCEDURE_FUNCTIONAL_TYPE = 6;
    const INDEX_PROCEDURE_MINUTES = 7;
    const INDEX_ANESTHESIOLOGIST = 8;
    const INDEX_ANESTHESIA_CODE = 9;
    const INDEX_ANESTHESIA_MINUTES = 10;
    const INDEX_SURGEON = 11;
    const INDEX_PROCEDURE_PRACTITIONER = 12;
    const INDEX_CONSENT_CODE = 13;
    const INDEX_PROCEDURE_PRIORITY = 14;
    const INDEX_ASSOCIATED_DIAGNOSIS_CODE = 15;
    const INDEX_PROCEDURE_CODE_MODIFIER = 16;
    const INDEX_PROCEDURE_DRG_TYPE = 17;
    const INDEX_TISSUE_TYPE_CODE = 18;
    const INDEX_PROCEDURE_IDENTIFIER = 19;
    const INDEX_PROCEDURE_ACTION_CODE = 20;
    const INDEX_DRG_PROCEDURE_DETERMINATION_STATUS = 21;
    const INDEX_DRG_PROCEDURE_RELEVANCE = 22;
    const INDEX_TREATING_ORGANIZATIONAL_UNIT = 23;
    const INDEX_RESPIRATORY_WITHIN_SURGERY = 24;
    const INDEX_PARENT_PROCEDURE_ID = 25;

    public $name = 'PR1';

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
    public function setProcedureCodingMethod($value)
    {
        $this->setField(self::INDEX_PROCEDURE_CODING_METHOD, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureCodingMethod()
    {
        return $this->getField(self::INDEX_PROCEDURE_CODING_METHOD);
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
    public function setProcedureDescription($value)
    {
        $this->setField(self::INDEX_PROCEDURE_DESCRIPTION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureDescription()
    {
        return $this->getField(self::INDEX_PROCEDURE_DESCRIPTION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureDateTime($value)
    {
        $this->setField(self::INDEX_PROCEDURE_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureDateTime()
    {
        return $this->getField(self::INDEX_PROCEDURE_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureFunctionalType($value)
    {
        $this->setField(self::INDEX_PROCEDURE_FUNCTIONAL_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureFunctionalType()
    {
        return $this->getField(self::INDEX_PROCEDURE_FUNCTIONAL_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureMinutes($value)
    {
        $this->setField(self::INDEX_PROCEDURE_MINUTES, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureMinutes()
    {
        return $this->getField(self::INDEX_PROCEDURE_MINUTES);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAnesthesiologist($value)
    {
        $this->setField(self::INDEX_ANESTHESIOLOGIST, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAnesthesiologist()
    {
        return $this->getField(self::INDEX_ANESTHESIOLOGIST);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAnesthesiaCode($value)
    {
        $this->setField(self::INDEX_ANESTHESIA_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAnesthesiaCode()
    {
        return $this->getField(self::INDEX_ANESTHESIA_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAnesthesiaMinutes($value)
    {
        $this->setField(self::INDEX_ANESTHESIA_MINUTES, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAnesthesiaMinutes()
    {
        return $this->getField(self::INDEX_ANESTHESIA_MINUTES);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSurgeon($value)
    {
        $this->setField(self::INDEX_SURGEON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSurgeon()
    {
        return $this->getField(self::INDEX_SURGEON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedurePractitioner($value)
    {
        $this->setField(self::INDEX_PROCEDURE_PRACTITIONER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedurePractitioner()
    {
        return $this->getField(self::INDEX_PROCEDURE_PRACTITIONER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setConsentCode($value)
    {
        $this->setField(self::INDEX_CONSENT_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getConsentCode()
    {
        return $this->getField(self::INDEX_CONSENT_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedurePriority($value)
    {
        $this->setField(self::INDEX_PROCEDURE_PRIORITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedurePriority()
    {
        return $this->getField(self::INDEX_PROCEDURE_PRIORITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAssociatedDiagnosisCode($value)
    {
        $this->setField(self::INDEX_ASSOCIATED_DIAGNOSIS_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAssociatedDiagnosisCode()
    {
        return $this->getField(self::INDEX_ASSOCIATED_DIAGNOSIS_CODE);
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
    public function setProcedureDrgType($value)
    {
        $this->setField(self::INDEX_PROCEDURE_DRG_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureDrgType()
    {
        return $this->getField(self::INDEX_PROCEDURE_DRG_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTissueTypeCode($value)
    {
        $this->setField(self::INDEX_TISSUE_TYPE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTissueTypeCode()
    {
        return $this->getField(self::INDEX_TISSUE_TYPE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureIdentifier($value)
    {
        $this->setField(self::INDEX_PROCEDURE_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureIdentifier()
    {
        return $this->getField(self::INDEX_PROCEDURE_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProcedureActionCode($value)
    {
        $this->setField(self::INDEX_PROCEDURE_ACTION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProcedureActionCode()
    {
        return $this->getField(self::INDEX_PROCEDURE_ACTION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgProcedureDeterminationStatus($value)
    {
        $this->setField(self::INDEX_DRG_PROCEDURE_DETERMINATION_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgProcedureDeterminationStatus()
    {
        return $this->getField(self::INDEX_DRG_PROCEDURE_DETERMINATION_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDrgProcedureRelevance($value)
    {
        $this->setField(self::INDEX_DRG_PROCEDURE_RELEVANCE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDrgProcedureRelevance()
    {
        return $this->getField(self::INDEX_DRG_PROCEDURE_RELEVANCE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTreatingOrganizationalUnit($value)
    {
        $this->setField(self::INDEX_TREATING_ORGANIZATIONAL_UNIT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTreatingOrganizationalUnit()
    {
        return $this->getField(self::INDEX_TREATING_ORGANIZATIONAL_UNIT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRespiratoryWithinSurgery($value)
    {
        $this->setField(self::INDEX_RESPIRATORY_WITHIN_SURGERY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRespiratoryWithinSurgery()
    {
        return $this->getField(self::INDEX_RESPIRATORY_WITHIN_SURGERY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentProcedureId($value)
    {
        $this->setField(self::INDEX_PARENT_PROCEDURE_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentProcedureId()
    {
        return $this->getField(self::INDEX_PARENT_PROCEDURE_ID);
    }


}