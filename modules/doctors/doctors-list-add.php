<?php

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');

/**
 * CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
 * GNU General Public License
 * Copyright 2002,2003,2004,2005 Elpidio Latorilla
 * elpidio@care2x.org, 
 *
 * See the file "copy_notice.txt" for the licence notice
 */
//define('LANG_FILE','doctors.php');
if ($HTTP_SESSION_VARS['sess_user_origin'] == 'personell_admin') {
    $local_user = 'aufnahme_user';
} else {
    $local_user = 'ck_doctors_dienstplan_user';
}
require_once($root_path . 'include/inc_front_chain_lang.php');

require_once($root_path . 'include/care_api_classes/class_personell.php');

$pers_obj = new Personell;

#added by VAN 11/19/2013
require_once($root_path . 'include/care_api_classes/class_department.php');
$dept_obj = new Department;

require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
$emr_obj = new EMR;

require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

$row_hosp = $objInfo->getAllHospitalInfo();
$EMR_address = $row_hosp['EMR_address'];
$EMR_directory = $row_hosp['EMR_directory'];

$is_doctor = $emr_obj->isDoctor($nr);
#======================

$pers_obj->useAssignmentTable();
$data = array();
$listOfdoctors=array();

if ($mode != 'delete') {

    $data['personell_nr'] = $nr;

    #-----------add 02-24-07-----------
    $role_nr = $pers_obj->getRole_type($nr, $job_fxn);
    $loc_type = $pers_obj->getDeptInfo($dept_nr);
    #----------------------------------

    $data['role_nr'] = $role_nr['nr'];      //17; // 17 = doctor (role person)  -- edited 02-24-07
    $data['location_type_nr'] = $loc_type['type'];  // 1 = dept (location type)  --- edited 02-24-07
    $data['location_nr'] = $dept_nr;
    $data['date_start'] = date('Y-m-d');
}

$data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
#echo "mode = $mode <br>";
require_once($root_path . 'include/care_api_classes/emr/services/DoctorEmrService.php');
require_once($root_path . 'include/care_api_classes/doctors_mobility/DoctorService.php');
$doctorService = new DoctorEmrService();
$doctorEHRService = new DoctorService();

switch ($mode) {

    case 'save':
        $data['history'] = "Add: " . date('Y-m-d H:i:s') . " = " . $HTTP_SESSION_VARS['sess_user_name'] . "\n";
        $data['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
        $data['create_time'] = date('YmdHis');
        $pers_obj->setDataArray($data);
        // die();
                $assignDoctor = array(
                    "personnel_nr"   =>  $data['personell_nr'],
                    "groupName"     =>  $groupName,
                    "dept_id" =>  $dept_nr,
                    "role_id"   => $data['role_nr']
                );
                require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
                $ehr = Ehr::instance();
                $patient = $ehr->doctor_postCreatePersonnelAssignment($assignDoctor);
                $asd = $ehr->getResponseData();
                $EHRstatus = $patient->status;
                if(!$EHRstatus){
                    // echo "<pre>";
                    // var_dump($patient->status);
                    // var_dump($assignDoctor);
                    // var_dump($patient->asd);
                    // die();
                }
        
        if (!$pers_obj->insertDataFromInternalArray()){
            echo "$obj->sql<br>$LDDbNoSave";

        }
        else {
            # added by VAN 11/19/2013
            # integration to EMR starts here
            # Post corresponding Doctor information in EMR
            
            if ($is_doctor) {
                try {
                    $doctorService->saveDoctor($nr);
                } catch (Exception $exc) {
                    //echo $exc->getTraceAsString();
                }

                //FOR EHR 9/2/2015
                try {
                    $doctorEHRService->saveDoctor($nr);
                } catch (Exception $exc) {
                    //echo $exc->getTraceAsString();
                }
                //END FOR EHR
            }
            #==============================
        }
        break;
    case 'update':
        $data['date_end'] = '0000-00-00';
        $data['history'] = $pers_obj->ConcatHistory("Update: " . date('Y-m-d H:i:s') . " = " . $HTTP_SESSION_VARS['sess_user_name'] . "\n");
        $data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
        $data['modify_time'] = date('YmdHis');
        #-------------------
        $data['status'] = " ";
        $personell_nr = $pers_obj->get_Person_name($nr);

        $assign_nr = $personell_nr['nr'];
        #-------------------
        $pers_obj->setDataArray($data);

        #------------------gui/html_template/default/tp_rightcolumn_menu.htm
        if (!$pers_obj->updateDataFromInternalArray($assign_nr))
            echo "$obj->sql<br>$LDDbNoUpdate";
        else {
            $dr_data= $data['personell_nr'];
            array_push($listOfdoctors,  array('personnel_nr' =>$data['personell_nr'], 'status' => 'active' ));
            $groupName  = $pers_obj->getDepartmentGroup($data['personell_nr']);
            $remoteGroupId = $pers_obj->getDepartmentGroupId($data['personell_nr']);

                $assignDoctor = array(
                    "personnel_nr"   =>  $data['personell_nr'],
                    "groupName"     =>  $groupName,
                    "dept_id" =>  $pers_obj->getDepartmentGroupId($data['personell_nr']),
                    "role_id"   => $data['role_nr'],
                );
            try{
                require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
                $ehr = Ehr::instance();
                $patient = $ehr->doctor_postCreatePersonnelAssignment($assignDoctor);
                $asd = $ehr->getResponseData();
                $EHRstatus = $patient->status;
                // var_dump($asd); die();
                
                if(!$EHRstatus){
                    // echo "<pre>";
                    // var_dump($patient->status);
                    // var_dump($assignDoctor);
                    // var_dump($patient->asd);
                    // die();
                }
            }catch (Exception $e){

            }
            # added by VAN 11/19/2013
            # integration to EMR starts here
            # Post corresponding Doctor information in EMR



            // if($response = $curl_ehr->assignDoctorDepartment($assignDoctor)){
            //     // print_r($response); die();
            // }else{
            //     die("No response");
            // }
                try {
                    $doctorService->saveDoctor($nr, 1);
                } catch (Exception $exc) {
                    //echo $exc->getTraceAsString();
                }

                //FOR EHR 9/2/2015
                try {
                    $doctorEHRService->saveDoctor($nr);
                } catch (Exception $exc) {
                    //echo $exc->getTraceAsString();
                }
                //END FOR EHR
            
            #==============================
        }

        break;
    case 'delete':
        $data['status'] = 'deleted';
        $data['date_end'] = date('Y-m-d');
        $data['history'] = $pers_obj->ConcatHistory("Deleted: " . date('Y-m-d H:i:s') . " = " . $HTTP_SESSION_VARS['sess_user_name'] . "\n");
        $data['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
        $data['modify_time'] = date('YmdHis');
        $pers_obj->setDataArray($data);
        if (!$pers_obj->updateDataFromInternalArray($item_nr))
            echo "$obj->sql<br>$LDDbNoUpdate";
        break;
}

header("location:doctors-dienst-personalliste.php" . URL_REDIRECT_APPEND . "&saved=1&retpath=$retpath&ipath=$ipath&dept_nr=$dept_nr&user_origin=$user_origin&nr=$nr&item_nr=$item_nr");
exit;
?>
