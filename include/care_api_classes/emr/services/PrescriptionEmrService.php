<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');

class PrescriptionEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    public function createPrescriptionRequest($refno) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/prescription/request/new',
            'method' => 'POST',
            'api' => 'PRESCRIPTION',
            'refno' => $refno,
            'stagingType' => 'PRESCRIPTION',
            'stagingId' => $refno
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\prescription\ajax\seg-prescription.server.php
    public function savePrescriptionRequest($refno) {
        $this->createPrescriptionRequest($refno);
        return TRUE;
    }
}

?>