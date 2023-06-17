<?php

require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');

class RadiologyService {
    protected static $baseDirectory;

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }

    public function createRadOrder($data) {

        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/radiology/create',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function createRadResult($data) {

        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/radiology/createResult',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function removeRadRequest($data) {

        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/radiology/deleteRequest',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    //@ modules\radiology\seg-radio-request-new.php
    public function saveRadRequest($refno) {
        $mobility_obj = new DoctorsMobility();
        $radInfo = $mobility_obj->getRadiologyInfo($refno);
        
        if (!$radInfo) {
            return false;
        }

        $radArr = $mobility_obj->getRadDataArray($radInfo);
        $res = $this->createRadOrder($radArr);

        //TODO: check if POST successful
        return true;
    }

    //@ modules\radiology\seg-radio-request-new.php
    public function sendRadResult($batchNr, $manual = false){
        $mobility_obj = new DoctorsMobility();
        $radResultInfo = $mobility_obj->getRadResultInfo($batchNr, $manual);
        
        if (!$radResultInfo) {
            return false;
        }

        $radArr = $mobility_obj->getRadResultDataArray($radResultInfo);
        $res = $this->createRadResult($radArr);

        //TODO: check if POST successful
        return true;
    }

    //@ modules\clinics\ajax\clinic-requests.server.php
    //@ modules\radiology\ajax\radio-request-list.server.php
    public function deleteRadRequest($refno) {
        $details = array(
            'ref_no' => $refno,
        );
        
        $res = $this->removeRadRequest($details);
        return TRUE;
    }
}

?>
