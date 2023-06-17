
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

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/class_consultation_referral.php');
include_once($root_path.'include/care_api_classes/class_person.php');
$root_path = dirname(dirname(dirname(__FILE__))) . '/';
$obj_conRef = new ConsultationReferral($encounter_nr);
$person_obj = new Person;
$dept_obj=new Department;

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


$conRefInfo = $obj_conRef->getConReferral();

if(!$conRefInfo){

    $conRefInfo = $obj_conRef->fld_seg_consultation_referral;
    // $conRefInfo = array();
    // foreach ($conRefInfoField as $key => $value) {
    //     $conRefInfo[$value] = "";
    // }
    $conRefInfo['date'] = date("F j, Y");
    $conRefInfo['time'] = date("g:ia");
}else{

    $conRefInfo['create_id'] = $conRefInfo['modify_id'] ?  $conRefInfo['modify_id'] : $conRefInfo['create_id'];
    $conRefInfo['create_dt'] = $conRefInfo['modify_dt'] ?  getFullTimeFormatDate($conRefInfo['modify_dt']) : getFullTimeFormatDate($conRefInfo['create_dt']);

    $arrDate = explode(' ', $conRefInfo['DATE__']);
    $conRefInfo['date'] = date("F j, Y",strtotime($arrDate[0]));
    $conRefInfo['time'] = date("g:ia",strtotime($arrDate[1]));

}

$params = array_merge($conRefInfo,$personInfo);
if($params['others'] != ''){
    $params['agency_to'] = $params['others'];
}else{
    $params['agency_to'] = $dept_obj->getDeptNameByNr($params['agency_to']) ? $dept_obj->getDeptNameByNr($params['agency_to']) : '';
}

if(strlen($params['create_id']) > 30){
    $params['create_id2'] = $params['create_id'];
    $params['create_id'] = '';

}

$params["r_doh"] = appendBasePath('modules/registration_admission/image/logo_doh.jpg');
$params["r_spmc"] = appendBasePath('modules/registration_admission/image/dmc_logo.jpg');
$params["is_emergency"] = $conRefInfo['is_emergency'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_routine"] = $conRefInfo['is_routine'] == "1" ? appendBasePath('images/check2.gif') : NULL;

showReport('consultation_referral_ipbm',$params,array(0 => ""),'pdf');
?>