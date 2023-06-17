<?php
/**
 * MessageLogger.php
 *
 * @author Jolly Caralos <jadcaralos@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 */

namespace SegHEIRS\modules\integrations\hl7\services;


use CDbException;
//use SegHEIRS\modules\integrations\hl7\Message;
use HL7\Message;
use SegHEIRS\modules\integrations\hl7\segments\MSH;
use SegHEIRS\modules\integrations\models\IntegrationHl7Logs;

/**
 * Class MessageLogger
 * @package SegHEIRS\modules\integrations\hl7\services
 */
class MessageLogger
{

    const MAX_LOG_SIZE = 4096;

    /**
     * @param Message $message
     * @throws CDbException
     */
    public function log(Message $message)
    {
        $mshSegment = $message->getSegmentsByName('MSH');
        $msh = MSH::createFromSegment($mshSegment[0]);

        $model = new IntegrationHl7Logs();

        $model->attributes = array(
            'message' => substr($message->toString(), 0, static::MAX_LOG_SIZE),
            'source_application' => $msh->getSendingApplication(),
            'destination_application' => $msh->getReceivingApplication()
        );

        if (!$model->save())
            throw new CDbException('Failed on saving IntegrationHl7Logs', 500, $model->errors);
    }

}