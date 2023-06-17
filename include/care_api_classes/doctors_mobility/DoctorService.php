<?php

require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class DoctorService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }

    public function createDoctor($data) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/doctor/create',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function deactivateAccount($data) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/doctor/deactivate',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    //@ modules\system_admin\edv_user_access_edit.php:
    //@ modules\doctors\doctors-list-add.php
    //@ modules\personell_admin\ajax\accre-insurance.server.php:
    //@ modules\personell_admin\personell_register.php:
    public function saveDoctor($doctorId) {
        $mobility_obj = new DoctorsMobility();
        $is_doctor = $mobility_obj->isDoctor($doctorId);
        if (!$is_doctor) {
            $this->deactivateDoctor($doctorId);
            return false;
        }

        $doctorInfo = $mobility_obj->getDoctorInfo($doctorId);
        if (!$doctorInfo) {
            return false;
        }

        $doctorDataArray = $mobility_obj->getDoctordataArray($doctorInfo);
        $res = $this->createDoctor($doctorDataArray);
        //TODO: check if POST successful
        return true;
    }

    //@ modules\personell_admin\ajax\accre-insurance.server.php:
    public function deactivateDoctor($doctorId) {
        $details = array(
            'personnel_id' => $doctorId
        );
        
        $res = $this->deactivateAccount($details);
        return TRUE;
    }

}

?>
