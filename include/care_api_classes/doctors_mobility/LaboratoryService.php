<?php

require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class LaboratoryService {
    protected static $baseDirectory;

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }

    public function createLabResult($data){
        
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/laboratory/createResult',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function createLabOrder($data) {

        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/laboratory/create',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function removeLabRequest($data) {

        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/laboratory/deleteRequest',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    //@ modules\bloodBank\seg-blood-request-new.php
    //@ modules\laboratory\seg-lab-request-new.php
    //@ modules\special_lab\seg-splab-request-new.php
    public function saveLabRequest($refno) {
        $mobility_obj = new DoctorsMobility();
        $labInfo = $mobility_obj->getLaboratoryInfo($refno);
        
        if (!$labInfo) {
            return false;
        }

        $labArr = $mobility_obj->getLabDataArray($labInfo);
        $res = $this->createLabOrder($labArr);

        //TODO: check if POST successful
        return true;
    }

    public function sendLabResult($lis_no){
        $mobility_obj = new DoctorsMobility();
        $labInfo = $mobility_obj->getLaboratoryInfoByLisno($lis_no);
        
        if (!$labInfo) {
            return false;
        }

        $labArr = $mobility_obj->getLabResultDataArray($labInfo);
        $res = $this->createLabResult($labArr);

        //TODO: check if POST successful
        return true;
    }

    //@ modules\bloodBank\ajax\blood-request-list.server.php
    //@ modules\clinics\ajax\clinic-requests.server.php
    //@ modules\laboratory\ajax\lab-new.server.php
    //@ modules\special_lab\ajax\splab-request-list.server.php
    public function deleteLabRequest($refno, $source) {
        $details = array(
            'ref_no' => $refno,
            'ref_source' => $source
        );
        
        $res = $this->removeLabRequest($details);
        return TRUE;
    }
}

?>
