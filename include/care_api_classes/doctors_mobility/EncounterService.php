<?php

require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_person.php');

class EncounterService {

    function __construct() {
        $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }

    public function createPatientEncounter($data) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/patient',
            'method' => 'POST',
            'data' => $data
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function closeEncounter($data) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/patient/discharge',
            'method' => 'POST',
            'data' => $data
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function referralPatient($data) {
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/encounter/referral',
            'method' => 'POST',
            'data' => $data
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    //@ modules\registration_admission\patient_er_encounter.php
    public function saveEncounter($pid, $enc_nr) {
        $person = new Person();
        $personInfo = $person->getAllInfoArray($pid);
        if (!$personInfo) {
            return false;
        }

        $mobility_obj = new DoctorsMobility();
        $personResultArray = $personInfo;
        $patientDataArray = $mobility_obj->formatPatientData($personResultArray); //patientsObject

        $encounterInfo = $mobility_obj->getEncounterInfo($enc_nr);
        if (!$encounterInfo) {
            return false;
        }

        $encounterResultArray = $mobility_obj->formatEncounterData($encounterInfo);
        $res = $this->createPatientEncounter( array_merge($patientDataArray, $encounterResultArray));

        //TODO: check if POST successful, and if failed rollback
        if ($res['code'] == 200)
            return true;
    }

    //@ modules\registration_admission\aufnahme_start.php
    //@ modules\registration_admission\aufnahme_cancel.php
    //@ modules\medocs\show_medocs.php:
    public function closePatientEncounter($encNr) {
        $mobility_obj = new DoctorsMobility();
        $dischangeInfo = $mobility_obj->getDischargeInfoByEncNo($encNr);
        
        if (!$dischangeInfo) {
            return false;
        }

        $dischargeArr = $mobility_obj->getDischargeDataArray($dischangeInfo);
        $res = $this->closeEncounter($dischargeArr);

        //TODO: check if POST successful
        return true;
    }

    //@ modules\dashboard\dashlets\PatientInformation\PatientInformation.php:
    public function referPatient($referral_nr){
        $mobility_obj = new DoctorsMobility();
        $referralInfo = $mobility_obj->getReferralInfoByPk($referral_nr);

        if (!$referralInfo) {
            return false;
        }

        $referralArr = $mobility_obj->getReferralDataArray($referralInfo);
        $res = $this->referralPatient($referralArr);

        //TODO: check if POST successful
        return $referralArr;
    }
}

?>
