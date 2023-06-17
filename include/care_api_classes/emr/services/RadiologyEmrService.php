<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');

class RadiologyEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    //@ modules\radiology\ajax\radio-finding.server.php
    public function createRadResult($batch_nr){
        $options = array(
            'method' => 'POST',
            'stagingType' => 'RAD RESULT',
            'stagingId' => $batch_nr
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    // public function createRadRequest($data) {
    public function createRadRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/radiology/request/new',
            'method' => 'POST',
            // 'data' => $data,
            'api' => 'RADIOLOGY',
            // 'refno' => $data['ReferenceBatchNumber'],
            'refno' => $refno,
            // 'pid' => $data['PatientId'],
            'stagingType' => 'RAD REQUEST',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function updateRadRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/radiology/request/update/'.$refno,
            'method' => 'PUT',
            'api' => 'RADIOLOGY',
            'refno' => $refno,
            'stagingType' => 'RAD REQUEST',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function removeRadRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/radiology/request/delete/'.$refno,
            'method' => 'DELETE',
            'api' => 'RADIOLOGY',
            'refno' => $refno,
            'stagingType' => 'RAD REQUEST',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\radiology\seg-radio-request-new.php:
    //@ modules\radiology\ajax\radio-schedule-server.php:
    public function saveRadRequest($refno, $update = 0) {
        $emr = new EMR();
        $orderInfo = $emr->getRadiologyInfo($refno);

        if($orderInfo){
            if ($update){
                $this->updateRadRequest($refno);
            }
            else{
                $this->createRadRequest($refno);
            }

            // $radDataArray = $emr->getLabRaddataArray($orderInfo, TRUE);

            // try { 
            //     $results = $this->createRadRequest($radDataArray);
            // } catch (Exception $e) { 
            //     $response_code = $e->getCode();
            // }
        }
        else
            return FALSE;
        
        return TRUE;
    }

    //@ modules\clinics\ajax\clinic-requests.server.php
    //@ modules\radiology\ajax\radio-request-list.server.php
    public function deleteRadRequest($refno) {
        $this->removeRadRequest($refno);
        return TRUE;
    }
}

?>