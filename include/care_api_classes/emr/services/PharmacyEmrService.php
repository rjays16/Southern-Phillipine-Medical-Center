<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');

class PharmacyEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    public function createPharmaRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/pharmacy/request/new',
            'method' => 'POST',
            'api' => 'PHARMACY',
            'refno' => $refno,
            'stagingType' => 'PHARMACY',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function updatePharmaRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/pharmacy/request/update/'.$refno,
            'method' => 'PUT',
            'api' => 'PHARMACY',
            'refno' => $refno,
            'stagingType' => 'PHARMACY',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function removePharmaRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/pharmacy/request/delete/'.$refno,
            'method' => 'DELETE',
            'api' => 'PHARMACY',
            'refno' => $refno,
            'stagingType' => 'PHARMACY',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\pharmacy\seg-pharma-order-edit.php
    //@ modules\pharmacy\seg-pharma-order-new.php
    public function savePharmaRequest($refno, $update = 0) {
        if ($update){
            $this->updatePharmaRequest($refno);
        }
        else{
            $this->createPharmaRequest($refno);
        }
        
        return TRUE;
    }

    //@ modules\clinics\ajax\clinic-requests.server.php
    //@ modules\pharmacy\ajax\order.server.php
    public function deletePharmaRequest($refno) {
        $this->removePharmaRequest($refno);
        return TRUE;
    }
}

?>