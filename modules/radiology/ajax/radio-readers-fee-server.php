<?php
//added by: Borj Radiology Readers Fee 2014-10-17
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/radiology/ajax/radio-readers-fee-common.php");
require($root_path.'include/care_api_classes/class_medocs.php');
require($root_path.'include/care_api_classes/class_icd10.php');
require($root_path.'include/care_api_classes/class_icpm.php');
require($root_path.'include/care_api_classes/class_drg.php');
//require($root_path.'include/care_api_classes/class_notes');
include_once($root_path.'include/care_api_classes/class_encounter.php');   # burn added : April 28, 2007
require_once($root_path.'include/care_api_classes/class_ward.php');
/* Create the helper class for the personell table */
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/class_person.php');
include_once($root_path.'include/inc_date_format_functions.php');

$dept_obj=new Department;
$pers_obj=new Personell;

function savereaders($encounter_nr3,$doc_list,$datepickers,$amount,$dr_role_type_nr,$create_dt, $service_code, $_is_cash){
         //savereaders(encounter_nr, doc_list, datepickers, amount, is_served, dr_role_type_nr
    global $db, $HTTP_SESSION_VARS;
    $objResponse = new xajaxResponse();
    $objPerson = new Person;



    $data = array(
        'encounter_nr3' => $encounter_nr3,
        'dr_nr' => $doc_list,
        'modify_dt' => date("Y-m-d", strtotime($datepickers))." ".date("H:i:s"),
        'modify_id' => $_SESSION['sess_user_fullname'],
        'dr_charge' => $amount,
        //'is_served' => $is_served,
        'dr_role_type_nr' => $dr_role_type_nr,
        'create_dt' => date("Y-m-d", strtotime($datepickers))." ".date("H:i:s"),
        'create_id' => $_SESSION['sess_user_fullname'],
        //'batch_nr' => $batch_nr,
        'service_code' => $service_code
    );
    
   // $objResponse->alert($encounter_nr3."dwada");

    if($ok = $objPerson->insertReaders($data)){
        
    }else{
       $objResponse->alert("Failed");
    }

    return $objResponse;
}

function savereadersOB($encounter_nr3,$doc_list,$datepickers,$amount,$dr_role_type_nr,$create_dt, $service_code, $_is_cash){
    global $db, $HTTP_SESSION_VARS;
    $objResponse = new xajaxResponse();
    $objPerson = new Person;

    $data = array(
        'encounter_nr3' => $encounter_nr3,
        'dr_nr' => $doc_list,
        'modify_dt' => date("Y-m-d", strtotime($datepickers))." ".date("H:i:s"),
        'modify_id' => $_SESSION['sess_user_fullname'],
        'dr_charge' => $amount,
        'dr_role_type_nr' => $dr_role_type_nr,
        'create_dt' => date("Y-m-d", strtotime($datepickers))." ".date("H:i:s"),
        'create_id' => $_SESSION['sess_user_fullname'],
        'service_code' => $service_code
    );
    
   // $objResponse->alert($encounter_nr3."dwada");

    if($ok = $objPerson->insertReadersOB($data)){
        
    }else{
       $objResponse->alert("Failed");
    }

    return $objResponse;
}

function savedoctorspf($refno,$doc_list,$amount,$accomodation_type,$service_code,$datepickers){
    global $db, $HTTP_SESSION_VARS;
    $objResponse = new xajaxResponse();   
    $objPerson = new Person;

    $data = array(
        'refno'=>$refno,
        'dr_no'=> $doc_list,
        'amount'=>$amount,
        'accomodation_type'=>$accomodation_type,
        'service_code'=>$service_code,
        'create_dt'=>date("Y-m-d", strtotime($datepickers))." ".date("H:i:s")
    );

    if($ok = $objPerson->saveDoctorPf($data)){
        $objResponse->alert("Save");
    }else{
        $objResponse->alert("Failed");
    }
    return $objResponse;
}

$xajax->processRequests();


?>