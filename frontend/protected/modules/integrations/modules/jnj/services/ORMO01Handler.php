<?php

/**
 * ORMO01Handler.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2017, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\modules\lishl7\services;
use Exception;
use LaborderD;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;
use SegHEIRS\modules\lab\services\LisResultService;
use SegHEIRS\modules\integrations\hl7\exceptions\HL7SegmentSequenceException;
use SegHEIRS\modules\integrations\hl7\Message;
use SegHEIRS\modules\integrations\hl7\segments\MSH;
use SegHEIRS\modules\integrations\hl7\segments\OBR;
use SegHEIRS\modules\integrations\hl7\segments\OBX;
use SegHEIRS\modules\integrations\hl7\segments\PID;
use SegHEIRS\modules\integrations\hl7\validators\PIDValidator;
use SegHEIRS\modules\integrations\modules\lishl7\validators\MSHValidator;
use Yii;

/**
 *
 * Description of ORMO01Handler
 *
 */

class ORMO01Handler
{

    /**
     * Processes the ORM^O01 message
     *
     * @param Message $message
     *
     * @throws Exception
     * @throws \CDbException
     */
    public function processMessage(Message $message)
    {
        try {
            $transaction = Yii::app()->getDb()->beginTransaction();

            // Validate MSH segment
            $msh = MSH::createFromSegment($message->getSegmentByIndex(0));
            (new MSHValidator())->validate($msh);

            // Validate PID segment
            $pid = PID::createFromSegment($message->getSegmentsByName('PID')[0]);
            (new PIDValidator($pid))->validate();

//            $this->routeOrders($message);
            $this->extractOrders($message);
            $transaction->commit();
        } catch (Exception $e) {
            if (!empty($transaction)) {
                $transaction->rollback();
            }

            // Re-throw exception so that callers can handle
            throw $e;
        }
    }

    /**
     * @param Message $message
     *
     * @throws HL7SegmentSequenceException
     */
    protected function extractOrders(Message $message)
    {
        $index = 0;
        while (($segment = $message->getSegmentByIndex($index)) !== null) {


            if ($segment->getName() === 'OBR') {
                $obr = OBR::createFromSegment($segment);

                // OBXes
                $obx = [];
                while ($segment = $message->getSegmentByIndex(++$index)) {

                    // Allow NTE/DG1 but ignore for now
                    if ($segment->getName() === 'NTE' || $segment->getName() === 'DG1') {
                        continue;
                    }

                    if ($segment->getName() === 'OBX') {
                        $obx[] = OBX::createFromSegment($segment);
                    } else {
                        // Not OBX, NTE or DG1
                        break;
                    }
                }

                //
                $this->processResults($obr, $obx);
            } else {
                $index++;
            }
        }

    }

    /**
     *
     * @param OBR $obr
     * @param OBX[] $obx
     *
     */
    protected function processResults(OBR $obr, $obx = [])
    {

        $refNo = $obr->getPlacerOrderNumber();

        /** @var LaborderD $detail */

        /**
         * Transform truncated reference number to the original format
         */
        if (is_numeric($refNo)) {
            $refNo = '20' . substr($refNo, 0, 2) . '0' . substr($refNo, 2);
        }

        $serviceId = $obr->getUniversalServiceIdentifier();
        if (is_array($serviceId)) {
            $serviceId = $serviceId[0];
        }

        $detail = LaborderD::model()->findByAttributes([
            'ref_no' => $refNo,
            'service_id' => $serviceId
        ]);

        if (!$detail) {
            return;
        }

        $observer = null;
        $postDate = $obr->getResultDateTime();

        $results = [];
        foreach ($obx as $_obx) {
            if (!$observer) {
                $observer = $_obx->getResponsibleObserver();
                $observer = $observer[1];
            }

            $value = HL7::decode($_obx->getObservationValue());
            if ($value === '""') {
                $value = '';
            }

            /**
             * Assume that OBX's with type ST and empty values are
             * headers. LIS printout logic treats observations with
             * null values as headers
             *
             */
            if ($_obx->getValueType() === 'ST' && $value === '') {
                $value = null;
            }

            $unit = HL7::decode($_obx->getUnits());

            /**
             * Remove trailing 0's from reference range values to make the
             * number format more uniform with converted SI reference range
             * values
             */
            $range = $_obx->getReferencesRange();
            if ($range) {
                $hasMatch = preg_match("/(.*)(-|>=|>|<=|<)(.*)/", $range, $matches);
                if ($hasMatch) {
                    array_shift($matches);
                    $delimeter = $matches[1];
                    unset($matches[1]);

                    // Convert values to float
                    array_walk($matches, function(&$value) use ($unit) {
                        $value = trim($value);
                        if (trim($value) !== '') {
                            $value = (float) trim($value);
                        }
                    });
                    $range = trim(implode(' ' . $delimeter . ' ', $matches));
                }
            }

            $id = $_obx->getObservationIdentifier();
            $results[] = [
                'id' => @$id[0],
                'observation' => @$id[1],
                'value' => $value,
                'unit' => $unit,
                'referenceRange' => $range,
                'interpretationCode' => $_obx->getInterpretationCodes(),
                'resultStatus' => $_obx->getObservationResultStatus(),
                'dateTime' => $_obx->getObservationDateTime(),
                'producer' => $_obx->getProducerId(),
                'responsibleObserver' => $_obx->getResponsibleObserver(),
            ];
        }

        $lisService = new LisResultService($detail);
        $lisService->setLisResults($postDate, $observer, $results);
    }



}
