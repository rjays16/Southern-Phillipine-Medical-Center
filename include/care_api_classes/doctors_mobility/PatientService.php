<?php

require_once($root_path . 'include/care_api_classes/doctors_mobility/ServiceContext.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/class_mobility.php');
require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_personell.php');

class PatientService {
    protected static $baseDirectory;

    function __construct() {
       $this->baseDirectory = Hospital_Admin::get('EHR_directory');
    }

    public function createPatientInfo($data) {

        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/patient',
            'method' => 'POST',
            'data' => $data,
        );
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    public function createPersonnel($data) {
      
        $options = array(
            'endpoint' => '/'.$this->baseDirectory.'/doctor',
            'method' => 'POST',
            'data' => $data,
        );
       # print_r(json_encode($options)); die;
        $service = new EHRServiceContext($options);
        return $service->execute();
    }

    //@ include\care_api_classes\class_gui_input_person.php
    //@ modules\registration_admission\certificates\cert_birth_interface.php
    //@ modules\registration_admission\certificates\cert_birth_interface_new.php
    //@ modules\registration_admission\certificates\cert_death_fetal_interface.php
    //@ modules\registration_admission\certificates\cert_death_fetal_interface_new.php
    //@ modules\registration_admission\certificates\cert_death_interface.php
    //@ modules\registration_admission\certificates\cert_death_interface_new.php
    //@ modules\personell_admin\personell_register.php:
    public function savePatient($pid) {
        $person = new Person();
        $mobility_obj = new DoctorsMobility();
        $personInfo = $person->getAllInfoArray($pid);
        
        if (!$personInfo) {
            return false;
        }

        $personResultArray = $personInfo;
        $patientDataArray = $mobility_obj->formatPatientData($personResultArray);
        #print_r(json_encode($patientDataArray)); die;
        $res = $this->createPatientInfo($patientDataArray);

        //TODO: check if POST successful
        return true;
    }

    /**
     * Entry point for creating personnel info
     *
     * @param $pid
     * @return bool
     */
    public function savePersonnel($pid) {

        $person = new Person();
        $mobility_obj = new DoctorsMobility();
        $personInfo = $person->getAllInfoArray($pid);
        $personnelInfo = $mobility_obj->getPersonnelInfoByPid($pid);

        if (!$personInfo)
            return false;

        if (!$personnelInfo)
            return false;

        $personResultArray = $personInfo;
        $patientDataArray = $mobility_obj->formatPatientData($personResultArray);
        $personnelDataArray = $mobility_obj->formatPersonnelData($mobility_obj->getPersonnelInfoByNr($personnelInfo['nr']));

        $res = $this->createPersonnel(array_merge($patientDataArray, $personnelDataArray));

        //TODO: check if POST successful
        return true;
    }

}

?>
