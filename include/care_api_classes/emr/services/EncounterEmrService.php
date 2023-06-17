<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EncounterEmrService
 *
 * @author SegworksM-PC
 */
require_once($root_path . 'include/care_api_classes/emr/services/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');

class EncounterEmrService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EMR_directory');
    }

    public function getPatientEncounter($pid, $encNr) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/' . $pid . '/case/' . $encNr,
            'method' => 'GET',
            'api' => 'ENCOUNTER',
            'encounter_nr' => $encNr,
        );
        $service = new ServiceContext($options);
        $output = $service->execute();
        return $output ? json_decode($output) : $output;
    }

    // public function createPatientEncounter($data) {
    public function createPatientEncounter($encNr) {
        
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/newcase',
            'method' => 'POST',
            // 'data' => $data,
            'api' => 'ENCOUNTER',
            // 'encounter_nr' => $data['CaseNumber'],
            'encounter_nr' => $encNr,
            'stagingType' => 'ENCOUNTER',
            'stagingId' => $encNr
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    // public function updatePatientEncounter($encNr, $data) {
    public function updatePatientEncounter($encNr) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/updatecase/' . $encNr,
            'method' => 'PUT',
            // 'data' => $data,
            'api' => 'ENCOUNTER',
            'encounter_nr' => $encNr,
            'stagingType' => 'ENCOUNTER',
            'stagingId' => $encNr
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\registration_admission\aufnahme_start.php
    //@ modules\registration_admission\patient_er_encounter.php
    public function saveEncounter($pid, $encNr, $update = 0) {
        // $emr_obj = new EMR();

        if ($update){
            $this->updatePatientEncounter($encNr);
        }
        else{
            $this->createPatientEncounter($encNr);
        }

        // try {
        // $encInfo = $this->getPatientEncounter($pid, $encNr);
        // } catch (Exception $e) { 
        //     #echo $e->getTraceAsString();
        //     $response_code = $e->getCode();
        // }
        
        // #HTTP ERROR: 504 : Gateway Timeout
        // if ($response_code!='504'){    
        //     $data = $emr_obj->getEncounterInfo($encNr);
        //     if (!$data) {
        //         return false;
        //     }

        //     $data['dept_nr'] = isset($data['current_dept_nr']) ? $data['current_dept_nr'] : $data['consulting_dept_nr'];
                
        //     if ($data['dept_nr']){
        //         $encDataArray = $emr_obj->getEncounterdataArray($data);

        //         #if ($encInfo->ResponseMessage == 'Case cannot be found.') {
        //         if ($encInfo->Code == '202' || $encInfo->Status == 'Failed') {
        //             try {
        //                 $this->createPatientEncounter($encDataArray);
        //             } catch (Exception $e) { 
        //                 $response_code = $e->getCode();
        //             }     
        //         #} else if ($encInfo->ResponseMessage == 'Case found.') {
        //         } else {    
        //             try {
        //                 $this->updatePatientEncounter($encNr, $encDataArray);
        //             } catch (Exception $e) { 
        //                 $response_code = $e->getCode();
        //             }
        //         }         
        //         #} else {
        //         #    return false;
        //         #}
                
        //         return true;
        //     }else{
        //         return false;    
        //     }    
        // }   

        // return false;
    }

    public function closeEncounter($pid, $encNr) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/' . $pid . '/closeCase/' . $encNr,
            'method' => 'PUT',
            'api' => 'ENCOUNTER',
            'encounter_nr' => $encNr,
            'pid' => $pid,
            'stagingType' => 'ENCOUNTER',
            'stagingId' => $encNr
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    public function deleteEncounter($pid, $encNr) {

        /*
          * added by BONIX 4/28/19
          * integration of EHRv2
          * cancel encounter @  EHR app
          * */

        require_once __DIR__ .'/../../../../include/care_api_classes/ehrhisservice/Ehr.php';
        $ehr = Ehr::instance();
        $resp = $ehr->cancelEncounter(array(
            'patient_encounter_cancel' => array(
                'encounter_nr' => $encNr,
            )
        ));
//        var_dump($ehr->getResponseData());
//        if(!$resp->status){
//            /*
//             * rollback cant be implement from due to HIS code structuring
//             * */
//        }


        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/api/patients/' . $pid . '/closeCase/' . $encNr,
            'method' => 'DELETE',
            'api' => 'ENCOUNTER',
            'encounter_nr' => $encNr,
            'stagingType' => 'ENCOUNTER',
            'stagingId' => $encNr
        );
        $service = new ServiceContext($options);
        return $service->execute();
    }

    //@ modules\medocs\show_medocs.php
    //@ modules\registration_admission\aufnahme_start.php
    public function closePatientEncounter($pid, $encNr) {
        $emr_obj = new EMR();
        $this->closeEncounter($pid, $encNr);
        // try {
        //     $encInfo = $this->getPatientEncounter($pid, $encNr);
        //     #$encInfo = json_decode($encInfo);
        // } catch (Exception $e) { 
        //     #echo $e->getTraceAsString();
        //     $response_code = $e->getCode();
        // }

        // #HTTP ERROR: 504 : Gateway Timeout
        // if ($response_code!='504'){
        //     #if ($encInfo->ResponseMessage == 'Case found.') {
        //     if ($encInfo->PatientId) {
        //         try {
        //             $this->closeEncounter($pid, $encNr);
        //         } catch (Exception $e) { 
        //             $response_code = $e->getCode();
        //         }     
        //     }

        //     return true;
        // }
        // return false;
    }

    //@ modules\registration_admission\aufnahme_cancel.php
    public function cancelPatientEncounter($pid, $encNr){
        $this->deleteEncounter($pid, $encNr);
    }
}

?>
