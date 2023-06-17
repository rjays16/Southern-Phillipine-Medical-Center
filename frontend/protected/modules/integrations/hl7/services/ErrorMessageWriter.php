<?php

/**
 * MessageWriter.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\services;

use SegHEIRS\modules\integrations\hl7\exceptions\HL7FileTransferException;
use SegHEIRS\modules\integrations\hl7\Message;

/**
 *
 * Description of MessageWriter
 *
 */

class ErrorMessageWriter
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
    public $ackFileExt = 'ACK';

    /**
     * MessageWriter constructor.
     */
    public function __construct($directoryPath, $messageFileExt = 'HL7', $ackFileExt = 'ACK')
    {
        $this->directoryPath = $directoryPath;
        $this->messageFileExt = $messageFileExt;
        $this->ackFileExt = $ackFileExt;
    }

    /**
     *
     * @param Message $message
     * @param Message $errorMessage
     * @param string $fileName
     *
     * @throws HL7FileTransferException
     */
    public function write(Message $message, Message $errorMessage, $fileName)
    {
        if (substr($this->directoryPath, -1) !== DIRECTORY_SEPARATOR) {
            $this->directoryPath.= DIRECTORY_SEPARATOR;
        }
        if (@file_put_contents($this->directoryPath.$fileName.'.'.$this->messageFileExt, $message->toString()) !== false) {
            if (@file_put_contents($this->directoryPath.$fileName.'.'.$this->ackFileExt, $errorMessage->toString()) !== false) {
                return;
            } else {
                throw new HL7FileTransferException(
                    'Failed to write acknowledge file to outbound directory: '.
                    $this->directoryPath.$fileName.'.'.$this->ackFileExt
                );
            }

        } else {
            throw new HL7FileTransferException(
                'Failed to write HL7 file to outbound directory: ' .
                $this->directoryPath.$fileName.'.'.$this->messageFileExt
            );
        }
    }

}
