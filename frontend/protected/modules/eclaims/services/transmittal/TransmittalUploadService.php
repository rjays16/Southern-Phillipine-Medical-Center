<?php
/**
 * Created by PhpStorm.
 * User: STAR LORD
 * Date: 8/29/2018
 * Time: 7:41 AM
 */

namespace SegHis\modules\eclaims\services\transmittal;

use ClaimAttachment;
use ClaimAttachmentForm;
use SegHis\modules\eclaims\models\EclaimsConfig;
use SegHis\modules\eclaims\services\claims\attachments\ClaimAttachmentService;
use ServiceExecutor;
use TransmittalDetails;

class TransmittalUploadService
{

    public $model;

    public $details;

    public $service;

    public $data;

    public $attachment;

    public function __construct(TransmittalDetails $details)
    {

        $this->model = new ClaimAttachmentForm();

        $this->details = $details;
    }

    public function uploadTransmittal($data, $return = false)
    {

        $types = $data['type'];

        foreach ($types as $key => $type) {

            $this->model->type = $types[$key];

            $this->model->encounter_nr = $this->details->encounter_nr;
            $this->model->transmit_no = $this->details->transmit_no;

            $this->model->attachment = \CUploadedFile::getInstance(
                $this->model,
                "attachment[{$key}]"
            );

            if (!$this->model->attachment) {
                continue;
            }

            /* The specific service is tried to be executed */
            $this->service = new ServiceExecutor(
                array(
                    'endpoint' => 'hie/document/upload',
                    'method' => 'POST',
                    'data' => $this->model->getUploadParams(),
                )
            );

            $result = $this->service->execute();

            if ($result['success']) {
                $this->saveAttachment($result['data'], $return);
                $resultData[] = array(
                    'name' => $this->attachment->filename,
                    'type' => 'plain/pdf',
                    'size' => 1000,
                    'attachment_type' => $this->attachment->getAttachmentType(),
                );
                $this->data = $resultData;
            } else {
                preg_match('~{([^{]*)}~i', trim($result['message']), $match);
                $error = str_replace('"', '', $match[1]);
                throw new \CException(stripslashes($error), $result['code']);
            }
        }
    }

    public function saveAttachment($data, $return = 0)
    {

        //   Eclaims config that checks if cloud storage is enabled

        $cloudStorage = EclaimsConfig::model()->cloudStorageEnabled();
        $model = new ClaimAttachment;
        $model->extractUploadResult($data);
        $model->transmit_no = $this->details->transmit_no;
        $model->encounter_nr = $this->details->encounter_nr;
        $model->attachment_type = $this->model->type;
        $model->is_return = $return;

        if ($cloudStorage) {
            $attachmentService = new ClaimAttachmentService();
            $storageFileName = $attachmentService->cloudStorageFormat($this->details, $this->model->type);
            $model->cloud_storage_filename = $storageFileName;
        }

        if (!$model->save()) {
            throw new CException("Failed to Save Attachment");
        }
        $this->attachment = $model;
    }
}
