<?php

require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class PatientEmrService {
    protected static $baseDirectory;

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    public function getPatientInfo($pid) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/' . $pid,
            'method' => 'GET',
            'api' => 'PATIENT',
            'pid' => $pid,
        );
        $service = new ServiceContext($options);
        
        return $service->execute();
    }

    // public function createPatientInfo($data) {
    public function createPatientInfo($pid) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/new',
            'method' => 'POST',
            // 'data' => $data,
            'api' => 'PATIENT',
            // 'pid' => $data['HISId'],
            'pid' => $pid,
            'stagingType' => 'PATIENT',
            'stagingId' => $pid
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    // public function updatePatientInfo($pid, $data) {
    public function updatePatientInfo($pid) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/update/' . $pid,
            'method' => 'PUT',
            // 'data' => $data,
            'api' => 'PATIENT',
            'pid' => $pid,
            'stagingType' => 'PATIENT',
            'stagingId' => $pid
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ include\care_api_classes\class_gui_input_person.php
    //@ modules\registration_admission\certificates\cert_birth_interface.php
    //@ modules\registration_admission\certificates\cert_birth_interface_new.php
    //@ modules\registration_admission\certificates\cert_death_fetal_interface.php
    //@ modules\registration_admission\certificates\cert_death_fetal_interface_new.php
    //@ modules\registration_admission\certificates\cert_death_interface.php
    //@ modules\registration_admission\certificates\cert_death_interface_new.php
    //@ modules\registration_admission\patient_er_encounter.php
    public function savePatient($pid, $update = 0) {
        $person = new Person();
        // $emr = new EMR();
        $personInfo = $person->getPersonInfo($pid);
        
        if (!$personInfo) {
            return false;
        }

        if ($update){
            // try {
            $this->updatePatientInfo($pid);
            // } catch (Exception $e) {
            //     $response_code = $e->getCode();
            // }
        }
        else{
            $this->createPatientInfo($pid);
        }

        // $patientDataArray = $emr->getPatientdataArray($personInfo);

        // try { 
        //     $emrPatientInfo = $this->getPatientInfo($pid);
        //     $emrPatientInfo = json_decode($emrPatientInfo);
        // } catch (Exception $e) { 
        //     #echo $e->getTraceAsString();
        //     $response_code = $e->getCode();
        // }
        
        #HTTP ERROR: 504 : Gateway Timeout
        // if ($response_code!='504'){
            #if ($emrPatientInfo == 'null') {
            // if ($emrPatientInfo->Status == 'Failed' || $emrPatientInfo->Code == '202') {
            //     try { 
            //         $this->createPatientInfo($patientDataArray);
            //     } catch (Exception $e) { 
            //         $response_code = $e->getCode();
            //     }    
            // } else {
                // if ($update){
                //     try { 
                //         $this->updatePatientInfo($pid, $patientDataArray);
                //     } catch (Exception $e) { 
                //         $response_code = $e->getCode();
                //     }    
                // }    
            // }
        // }
        
        return true;
    }

}

?>
