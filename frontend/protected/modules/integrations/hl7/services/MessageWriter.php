<?php

/**
 * MessageWriter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\services;

use SegHEIRS\modules\integrations\hl7\Message;
use SegHEIRS\modules\integrations\hl7\exceptions\HL7FileTransferException;

/**
 *
 * Description of MessageWriter
 *
 */

class MessageWriter
{
    /**
     * @var string
     */
    public $directoryPath;

    /**
     * @var string
     */
    public $messageFileExt = 'HL7';

    /**
     * @var string
     */
    public $semaphoreFileExt = 'SEM';

    /**
     * MessageWriter constructor.
     */
    public function __construct($directoryPath, $messageFileExt = 'HL7', $semaphoreFileExt = 'SEM')
    {
        $this->directoryPath = $directoryPath;
        $this->messageFileExt = $messageFileExt;
        $this->semaphoreFileExt = $semaphoreFileExt;
    }

    /**
     *
     * @param Message $message
     * @param string $fileName
     *
     * @throws HL7FileTransferException
     */
    public function write(Message $message, $fileName = '')
    {
        $logger = new MessageLogger();
        $logger->log($message);

        if (substr($this->directoryPath, -1) !== DIRECTORY_SEPARATOR) {
            $this->directoryPath.= DIRECTORY_SEPARATOR;
        }

//        $fileName = $this->getFileName($message);

        $messageStr = iconv('UTF-8', 'ISO-8859-1//IGNORE', $message->toString());

        if (@file_put_contents($this->directoryPath.$fileName.'.' . $this->messageFileExt, $messageStr) !== false) {

            if ($this->semaphoreFileExt) {
                if (@file_put_contents($this->directoryPath.$fileName.'.'.$this->semaphoreFileExt, ' ') !== false) {
                    return;
                } else {
                    throw new HL7FileTransferException(
                        'Failed to write semaphore file to outbound directory: '.
                        $this->directoryPath.$fileName.'.'.$this->semaphoreFileExt
                    );
                }
            }

        } else {
            throw new HL7FileTransferException(
                'Failed to write HL7 file to outbound directory: ' .
                $this->directoryPath.$fileName.'.' . $this->messageFileExt
            );
        }
    }

    /**
     * @param Message $message
     *
     * @return string
     */
//    protected function getFileName(Message $message)
//    {
//        $msh = MSH::createFromSegment(current($message->getSegmentsByName('MSH')));
//        $id = $msh->getMessageControlId();
//
//        $messageType = substr(implode('', $msh->getMessageType()), 0, 7);
//
//        return strtr('{timestamp}-{id}-{type}', [
//            '{timestamp}' => date('YmdHis'),
//            '{id}' => $id,
//            '{type}' => $messageType
//        ]);
//    }
}
