<?php

/**
 * Message.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7;

use HL7\Message as BaseMessage;
use SplFileInfo;

/**
 *
 * Description of Message
 *
 */

class Message extends BaseMessage
{

    /**
     * @var SplFileInfo
     */
    protected $fileInfo;

    /**
     * Message constructor.
     *
     * @param string $msgStr
     * @param array $hl7Globals
     * @param SplFileInfo|null $fileInfo
     */
    public function __construct($msgStr = '', $hl7Globals = array(), $fileInfo = null)
    {
        parent::__construct($msgStr, $hl7Globals);
        $this->fileInfo = $fileInfo;
    }

    /**
     * @return SplFileInfo|null
     */
    public function getFileInfo()
    {
        return $this->fileInfo;
    }

    /**
     * @param string $name
     * @param int $offset
     *
     * @return \HL7\Segment|null
     */
    public function getNextOccurringSegment($name, $offset = 0)
    {
        for ($i=$offset; $i < count($this->_segments); $i++) {
            $segment = $this->getSegmentByIndex($i);
            if ($segment->getName() === $name) {
                return $segment;
            }
        }

        return null;
    }
}
