<?php

/**
 * NTE.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of NTE
 *
 */

class NTE extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_SOURCE_OF_COMMENT = 2;
    const INDEX_COMMENT = 3;
    const INDEX_COMMENT_TYPE = 4;
    const INDEX_ENTERED_BY = 5;
    const INDEX_ENTERED_DATE_TIME = 6;
    const INDEX_EFFECTIVE_START_DATE = 7;
    const INDEX_EXPIRATION_DATE = 8;


    /** @var string */
    public $name = 'NTE';

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
    public function setSourceOfComment($value)
    {
        $this->setField(self::INDEX_SOURCE_OF_COMMENT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSourceOfComment()
    {
        return $this->getField(self::INDEX_SOURCE_OF_COMMENT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setComment($value)
    {
        $this->setField(self::INDEX_COMMENT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->getField(self::INDEX_COMMENT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCommentType($value)
    {
        $this->setField(self::INDEX_COMMENT_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCommentType()
    {
        return $this->getField(self::INDEX_COMMENT_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEnteredBy($value)
    {
        $this->setField(self::INDEX_ENTERED_BY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEnteredBy()
    {
        return $this->getField(self::INDEX_ENTERED_BY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEnteredDateTime($value)
    {
        $this->setField(self::INDEX_ENTERED_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEnteredDateTime()
    {
        return $this->getField(self::INDEX_ENTERED_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEffectiveStartDate($value)
    {
        $this->setField(self::INDEX_EFFECTIVE_START_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEffectiveStartDate()
    {
        return $this->getField(self::INDEX_EFFECTIVE_START_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpirationDate($value)
    {
        $this->setField(self::INDEX_EXPIRATION_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpirationDate()
    {
        return $this->getField(self::INDEX_EXPIRATION_DATE);
    }

}