
<?php
require_once('roots.php');
require_once($root_path.'include'.DIRECTORY_SEPARATOR.'inc_jasperReporting.php');

?>

<script language="javascript">
<?php
    require_once($root_path.'include'.DIRECTORY_SEPARATOR.'inc_checkdate_lang.php'); 
    include_once($root_path.'include/care_api_classes/class_personell.php');
    $pers_obj=new Personell;
?>
</script>


<?php

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}

if (isset($_GET['pid']) && $_GET['pid']){
    $pid = $_GET['pid'];
}

if (isset($_GET['encoder']) && $_GET['encoder']){
    $personell_title = $pers_obj->getPersonellTitle($_SESSION['sess_login_personell_nr']);
    $encoder = urldecode($_GET['encoder']) . ", " . $personell_title;
}


include_once($root_path.'include'.DIRECTORY_SEPARATOR.'care_api_classes'.DIRECTORY_SEPARATOR.'class_patient_referral.php');
include_once($root_path.'include'.DIRECTORY_SEPARATOR.'care_api_classes'.DIRECTORY_SEPARATOR.'class_department.php');
include_once($root_path.'include'.DIRECTORY_SEPARATOR.'care_api_classes'.DIRECTORY_SEPARATOR.'class_person.php');
include_once($root_path.'include'.DIRECTORY_SEPARATOR.'care_api_classes'.DIRECTORY_SEPARATOR.'class_personell.php');
include_once($root_path.'include'.DIRECTORY_SEPARATOR.'care_api_classes'.DIRECTORY_SEPARATOR.'class_ward.php');

$root_path = dirname(dirname(dirname(__FILE__))) . '/';
$obj_patRef = new PatientReferral($encounter_nr);
$person_obj = new Person;
$dept_obj = new Department;
$personell_obj = new Personell;
$encounter_obj = new Encounter;
$ward_obj = new Ward;

if($encounter_nr){

    if(!($personInfo=$person_obj->getPatientInfoByPid($pid))){
        
        echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
        exit();
    }
    
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


$patRefInfo = $obj_patRef->getPatientReferral();

if(!$patRefInfo){

    $patRefInfo = $obj_patRef->fld_seg_patient_referral;
    $patRefInfo['date'] = date("F j, Y");
    $patRefInfo['time'] = date("g:ia");
}else{
    if($patRefInfo['referral_to']){
        $deptRes = $dept_obj->getDeptAllInfo($patRefInfo['referral_to']);
        if($deptRes){
            $patRefInfo['referral_to'] = $deptRes['name_formal'];
        }
    }
    
    $patRefInfo['create_id'] = $patRefInfo['modify_id'] ?  $patRefInfo['modify_id'] : $patRefInfo['create_id'];
    $patRefInfo['create_dt'] = $patRefInfo['modify_dt'] ?  getFullTimeFormatDate($patRefInfo['modify_dt']) : getFullTimeFormatDate($patRefInfo['create_dt']);

    $patRefInfo['date'] = date("F j, Y",strtotime($patRefInfo['create_dt']));
    $patRefInfo['time'] = date("g:ia",strtotime($patRefInfo['create_dt']));
}
$encounterInfo = $encounter_obj->getEncounterInfo($encounter_nr);

$params = array_merge($patRefInfo,$personInfo);
if($encounterInfo){
    $params["ward"] = $encounterInfo['ward_name'];
    $bed = $ward_obj->getCurrentBedNr($encounter_nr);
    if($bed)
       
    $params['bed'] = $bed;
}

$params["r_doh"] = appendBasePath('modules'.DIRECTORY_SEPARATOR.'registration_admission'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'logo_doh.jpg');
$params["r_spmc"] = appendBasePath('modules'.DIRECTORY_SEPARATOR.'registration_admission'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'dmc_logo.jpg');
$params["r_ipbm"] = appendBasePath('img'.DIRECTORY_SEPARATOR.'ipbm.png');
$params["birth_date"] = getFullTimeFormatDate($params["birth_date"]);
$params['reason_referral'] = 'Reason for Referral'.DIRECTORY_SEPARATOR.' Current Concerns: ' . $params['reason_referral'];
$params['diagnosis'] = $params['diagnosis'];
$params["is_evaluation"] = $patRefInfo['is_evaluation'] == "1" ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_clearance"] = $patRefInfo['is_clearance'] == "1" ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_comanage"] = $patRefInfo['is_comanage'] == "1" ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_transferserv"] = $patRefInfo['is_transferserv'] == "1" ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_others"] = $patRefInfo['others'] ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_emergency"] = $patRefInfo['is_emergency'] ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_urgent"] = $patRefInfo['is_urgent'] ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
$params["is_routine"] = $patRefInfo['is_routine'] ? appendBasePath('images'.DIRECTORY_SEPARATOR.'check2.gif') : NULL;
if(strlen($encoder) > 30){
    $params["encoder2"] = $encoder;
    $params["encoder"] = "";
}else{
    $params["encoder"] = $encoder;
}


if($personellInfo = $personell_obj->getPersonellInfo($_SESSION['sess_login_personell_nr']))
$params['job_position'] = $personellInfo['job_position'];
showReport('patient_referral',$params,array(0 => ""),'pdf');
?>