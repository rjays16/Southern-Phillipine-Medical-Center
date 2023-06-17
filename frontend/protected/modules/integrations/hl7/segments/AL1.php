<?php

/**
 * AL1.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of AL1
 *
 */

class AL1 extends Segment
{
    const INDEX_SEQUENCE_ID = 1;
    const INDEX_ALLERGEN_TYPE_CODE = 2;
    const INDEX_ALLERGEN_CODE_MNEMONIC_DESCRIPTION = 3;
    const INDEX_ALLERGY_SEVERITY_CODE = 4;
    const INDEX_ALLERGY_REACTION_CODE = 5;
    const INDEX_IDENTIFICATION_DATE = 6;

    /** @var string  */
    public $name = 'AL1';

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
    public function setAllergenTypeCode($value)
    {
        $this->setField(self::INDEX_ALLERGEN_TYPE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAllergenTypeCode()
    {
        return $this->getField(self::INDEX_ALLERGEN_TYPE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAllergenCodeMnemonicDescription($value)
    {
        $this->setField(self::INDEX_ALLERGEN_CODE_MNEMONIC_DESCRIPTION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAllergenCodeMnemonicDescription()
    {
        return $this->getField(self::INDEX_ALLERGEN_CODE_MNEMONIC_DESCRIPTION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAllergySeverityCode($value)
    {
        $this->setField(self::INDEX_ALLERGY_SEVERITY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAllergySeverityCode()
    {
        return $this->getField(self::INDEX_ALLERGY_SEVERITY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAllergyReactionCode($value)
    {
        $this->setField(self::INDEX_ALLERGY_REACTION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAllergyReactionCode()
    {
        return $this->getField(self::INDEX_ALLERGY_REACTION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setIdentificationDate($value)
    {
        $this->setField(self::INDEX_IDENTIFICATION_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentificationDate()
    {
        return $this->getField(self::INDEX_IDENTIFICATION_DATE);
    }

}