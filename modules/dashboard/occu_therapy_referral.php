
<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

?>

<script language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>


<?php

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}

if (isset($_GET['pid']) && $_GET['pid']){
    $pid = $_GET['pid'];
}

include_once($root_path.'include/care_api_classes/class_occu_therapy_referral.php');
include_once($root_path.'include/care_api_classes/class_person.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
include_once($root_path.'include/care_api_classes/class_encounter.php');
                     
$obj_enc = new Encounter;            
$root_path = dirname(dirname(dirname(__FILE__))) . '/';
$ob_occuTherapy = new OccuTherapyReferral($encounter_nr);
$person_obj = new Person;
$pers_obj = new Personell;

if($encounter_nr){

    if(!($personInfo=$person_obj->getPatientInfoByPid($pid))){
        
        echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
        exit();
    }
    $personInfo['ward'] = $obj_enc->getWardNameByEncounter($encounter_nr) ? $obj_enc->getWardNameByEncounter($encounter_nr) : '';
    
}else{
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
    exit();
}

function appendBasePath($path = ''){
    global $root_path;

    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );

    $basePath = $baseurl . $path;

    return (file_exists($basePath) ? $basePath : $root_path . $path);
}



function getFullTimeFormatDate($date){
    $fulltime_format = "F j, Y g:ia";
    return ($date && $date != "0000-00-00 00:00:00") ? date($fulltime_format,strtotime($date)) : date($fulltime_format);
}


$occuTheraInfo = $ob_occuTherapy->getOccuTherapyReferral();

if(!$occuTheraInfo){

    $occuTheraInfo = $ob_occuTherapy->fld_seg_occupational_therapy_referral;
    $occuTheraInfo['date'] = date("F j, Y");
    $occuTheraInfo['time'] = date("g:ia");
}else{

    $occuTheraInfo['create_id'] = $occuTheraInfo['modify_id'] ?  $occuTheraInfo['modify_id'] : $occuTheraInfo['create_id'];
    $occuTheraInfo['create_dt'] = $occuTheraInfo['modify_dt'] ?  getFullTimeFormatDate($occuTheraInfo['modify_dt']) : getFullTimeFormatDate($occuTheraInfo['create_dt']);

    $occuTheraInfo['date'] = date("F j, Y",strtotime($occuTheraInfo['create_dt']));
    $occuTheraInfo['time'] = date("g:ia",strtotime($occuTheraInfo['create_dt']));
}

$params = array_merge($occuTheraInfo,$personInfo);
$params["r_doh"] = appendBasePath('modules/registration_admission/image/logo_doh.jpg');
$params["r_spmc"] = appendBasePath('modules/registration_admission/image/dmc_logo.jpg');
$params["r_ipbm"] = appendBasePath('img/ipbm.png');
$params["birth_date"] = date("F j, Y",strtotime($params["birth_date"]));
$params["is_male"] = $params['gender'] == "Male" ? appendBasePath('images/check2.gif') : NULL;
$params["is_female"] = $params['gender'] == "Female" ? appendBasePath('images/check2.gif') : NULL;
$params['reason_referral'] = '<b>Reason for Referral/ Current Concerns:</b> ' . $params['reason_referral'];
$params['diagnosis'] = '<b>Diagnosis/Presenting Condition:</b> ' . $params['diagnosis'];
$params['precautions'] = '<b>Precautions:</b> ' . $params['precautions'];
$params["is_physical_fit"] = $occuTheraInfo['is_physical_fit'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_leisure_explo"] = $occuTheraInfo['is_leisure_explo'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_thera_gardening"] = $occuTheraInfo['is_thera_gardening'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_creative_express"] = $occuTheraInfo['is_creative_express'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_adl_giadl"] = $occuTheraInfo['is_adl_giadl'] ? appendBasePath('images/check2.gif') : NULL;
$params["is_work_explo"] = $occuTheraInfo['is_work_explo'] ? appendBasePath('images/check2.gif') : NULL;
$params["is_social_skill"] = $occuTheraInfo['is_social_skill'] ? appendBasePath('images/check2.gif') : NULL;
$params["is_others"] = $occuTheraInfo['others'] ? appendBasePath('images/check2.gif') : NULL;
$personell_title = $pers_obj->getPersonellTitle($_SESSION['sess_login_personell_nr']);
$encoder = $_SESSION['sess_login_username'] . ", " . $personell_title;
if(strlen($encoder) > 30){
    $params["create_id2"] = $encoder;
    $params["create_id"] = ""; 
}else{
    $params["create_id"] = $encoder;
}

showReport('occu_therapy_referral',$params,array(0 => ""),'pdf');
?>