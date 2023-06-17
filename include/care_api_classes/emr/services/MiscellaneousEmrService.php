<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');

class MiscellaneousEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    public function createMiscRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/miscellaneous/request/new',
            'method' => 'POST',
            'api' => 'MISCELLANEOUS',
            'refno' => $refno,
            'stagingType' => 'MISCELLANEOUS',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function updateMiscRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/miscellaneous/request/update/'.$refno,
            'method' => 'PUT',
            'api' => 'MISCELLANEOUS',
            'refno' => $refno,
            'stagingType' => 'MISCELLANEOUS',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function removeMiscRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/miscellaneous/request/delete/'.$refno,
            'method' => 'DELETE',
            'api' => 'MISCELLANEOUS',
            'refno' => $refno,
            'stagingType' => 'MISCELLANEOUS',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\dialysis\seg-misc-request-new.php
    public function saveMiscRequest($refno, $update = 0) {
        if ($update){
            $this->updateMiscRequest($refno);
        }
        else{
            $this->createMiscRequest($refno);
        }
        
        return TRUE;
    }

    //@ modules\clinics\ajax\clinic-requests.server.php
    public function deleteMiscRequest($refno) {
        $this->removeMiscRequest($refno);
        return TRUE;
    }
}

?>