<?php
/**
 * created by Nick 07-15-2014
 */
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_caserate_icd_icp.php');

switch($_REQUEST['action']){
    case "get_special_procedures":
        getSpecialProcedures();
        break;
    case "update_special_procedure":
        setSpecialProcedureDetail();
        break;
}

function getSpecialProcedures(){
    $specialProcedures = new Icd_Icp($_REQUEST);
    $response = $specialProcedures->getPatientSpecialProcedures(getRequestData('encounter_nr'));
    echo json_encode($response);
}//end function getSpecialProcedures

function setSpecialProcedureDetail(){
    $code = getRequestData('code');
    $specialProcedures = new Icd_Icp($_REQUEST);
    $rs = $specialProcedures->setPatientSpecialProcedureDetails($code);
    if($rs){
        echo json_encode(array('result' => true));
    }else{
        echo json_encode(array('result' => false));
    }
}//end function setSpecialProcedureDetail

function getRequestData($key){
    if(isset($_REQUEST[$key])){
        return $_REQUEST[$key];
    }else{
        return null;
    }
}