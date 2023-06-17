<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class DoctorEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    public function getDoctorInfo($doctorId) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/doctors/' . $doctorId,
            'method' => 'GET',
            'api' => 'DOCTOR',
            'personell_nr' => $doctorId,
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    // public function createDoctor($data) {
    public function createDoctor($doctorId) {
//        echo json_encode($data);
//        exit;
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/doctors/new',
            'method' => 'POST',
            // 'data' => $data,
            'api' => 'DOCTOR',
            // 'personell_nr' => $data['PhysicianNumber'],
            'personell_nr' => $doctorId,
            'stagingType' => 'DOCTOR',
            'stagingId' => $doctorId
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    // public function updateDoctor($doctorId, $data) {
    public function updateDoctor($doctorId) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/doctors/update/' . $doctorId,
            'method' => 'PUT',
            // 'data' => $data,
            'api' => 'DOCTOR',
            'personell_nr' => $doctorId,
            'stagingType' => 'DOCTOR',
            'stagingId' => $doctorId
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ include\care_api_classes\class_gui_input_person.php
    //@ modules\doctors\doctors-list-add.php
    public function saveDoctor($doctorId, $update = 0) {
        $emr_obj = new EMR();
        $doctorInfo = $emr_obj->getDoctorInfo($doctorId);
        if (!$doctorInfo) {
            return false;
        }

        if ($update){
            $this->updateDoctor($doctorId);
        }
        else{
            $this->createDoctor($doctorId);
        }

        return true;
        // $doctorDataArray = $emr_obj->getDoctordataArray($doctorInfo);
        
        // try {
        //     $emrDoctorInfo = $this->getDoctorInfo($doctorId);
        //     $emrDoctorInfo = json_decode($emrDoctorInfo);
        // } catch (Exception $e) { 
        //     #echo $e->getTraceAsString();
        //     $response_code = $e->getCode();
        // }

        // #HTTP ERROR: 504 : Gateway Timeout
        // if ($response_code!='504'){
        //     if ($emrDoctorInfo->Status == 'Failed' || $emrDoctorInfo->Code == '202') {
        //         try {
        //             $this->createDoctor($doctorDataArray);
        //         } catch (Exception $e) { 
        //             $response_code = $e->getCode();
        //         }    
        //     } else {
        //         try {
        //             $this->updateDoctor($doctorId, $doctorDataArray);
        //         } catch (Exception $e) { 
        //             $response_code = $e->getCode();
        //         }
        //     }
        //     return true;
        // }    
        // return false;
    }

}

?>
