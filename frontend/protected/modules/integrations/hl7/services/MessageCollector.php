<?php

/**
 * MessageCollector.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\services;
use FilesystemIterator;
use GlobIterator;
use SegHEIRS\modules\integrations\hl7\Message;
use SplFileInfo;

/**
 *
 * Description of MessageCollector
 *
 */

class MessageCollector
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $messageFileExt;

    /**
     * @var string
     */
    protected $semaphoreFileExt;

    /**
     * MessageCollector constructor.
     *
     * @param string $path
     */
    public function __construct($path, $messageFileExt = 'HL7', $semaphoreFileExt = 'SEM')
    {
        $this->path = $path;
        $this->messageFileExt = $messageFileExt;
        $this->semaphoreFileExt = $semaphoreFileExt;
    }

    /**
     * @return Message[]
     */
    public function collect()
    {
        $path = $this->path;

        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }

        $path .= '*.' . $this->messageFileExt;

        $iterator = new GlobIterator($path, FilesystemIterator::KEY_AS_PATHNAME);

        $messages = [];
        foreach ($iterator as $path => $file) {
            /** @var SplFileInfo $file */
            if ($this->semaphoreFileExt) {
                $checkPath = str_replace('.'.$this->messageFileExt, '.'.$this->semaphoreFileExt, $path);
            } else {
                // If we don't use semaphore files
                $checkPath = true;
            }

            if ($checkPath === true || file_exists($checkPath)) {
                $messageStr = @file_get_contents($path);
                $messageStr = mb_convert_encoding(
                    $messageStr,
                    'UTF-8',
                    mb_detect_encoding($messageStr, 'UTF-8, ISO-8859-1', true)
                );
                $message = new Message($messageStr, [], $file);
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getMessageFileExt()
    {
        return $this->messageFileExt;
    }

    /**
     * @param string $messageFileExt
     */
    public function setMessageFileExt($messageFileExt)
    {
        $this->messageFileExt = $messageFileExt;
    }

    /**
     * @return string
     */
    public function getSemaphoreFileExt()
    {
        return $this->semaphoreFileExt;
    }

    /**
     * @param string $semaphoreFileExt
     */
    public function setSemaphoreFileExt($semaphoreFileExt)
    {
        $this->semaphoreFileExt = $semaphoreFileExt;
    }
}
