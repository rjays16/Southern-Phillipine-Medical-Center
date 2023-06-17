<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');

class LaboratoryEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    //@ modules\laboratory\seg_hl7\seg-lab-hl7-cron.php
    function sendLabHl7($lis_refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/laboratory/results/new',
            'method' => 'POST',
            // 'data' => $data,
            'api' => 'LABORATORY',
            // 'refno' => $data['refno'],
            // 'pid' => $data['HISPatientId'],
            'stagingType' => 'LAB RESULT',
            'stagingId' => $lis_refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    // public function createLabRequest($data) {
    public function createLabRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/laboratory/request/new',
            'method' => 'POST',
            // 'data' => $data,
            'api' => 'LABORATORY',
            // 'refno' => $data['ReferenceBatchNumber'],
            'refno' => $refno,
            'stagingType' => 'LAB REQUEST',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function updateLabRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/laboratory/request/update/'.$refno,
            'method' => 'PUT',
            'api' => 'LABORATORY',
            'refno' => $refno,
            'stagingType' => 'LAB REQUEST',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function removeLabRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/laboratory/request/delete/'.$refno,
            'method' => 'DELETE',
            'api' => 'LABORATORY',
            'refno' => $refno,
            'stagingType' => 'LAB REQUEST',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\bloodBank\seg-blood-request-new.php
    //@ modules\laboratory\seg-lab-request-new.php
    //@ modules\special_lab\seg-splab-request-new.php
    public function saveLabRequest($refno, $update = 0){
        $emr = new EMR();
        $orderInfo = $emr->getLaboratoryInfo($refno);

        if($orderInfo){
            if ($update){
                $this->updateLabRequest($refno);
            }
            else{
                $this->createLabRequest($refno);
            }
            // $labDataArray = $emr->getLabRaddataArray($orderInfo);

            // try {
            //     $results = $this->createLabRequest($labDataArray);
            //     $results = json_decode($results);

            //     if($results->Status != "Failed")
            //         $emr->changeLaboratoryFlag($labDataArray);
            // } catch (Exception $e) { 
            //     $response_code = $e->getCode();
            // }
        }
        else
            return FALSE;
        

        return TRUE;
    }

    //@ modules\bloodBank\ajax\blood-request-list.server.php
    //@ modules\clinics\ajax\clinic-requests.server.php
    //@ modules\laboratory\ajax\lab-new.server.php
    //@ modules\special_lab\ajax\splab-request-list.server.php
    public function deleteLabRequest($refno) {
        $this->removeLabRequest($refno);
        return TRUE;
    }
}

?>