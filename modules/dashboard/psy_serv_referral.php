
<?php
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');

?>

<script language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
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

include_once($root_path.'include/care_api_classes/class_psychological_serv_referral.php');
include_once($root_path.'include/care_api_classes/class_person.php');
$root_path = dirname(dirname(dirname(__FILE__))) . '/';
$obj_psyServ = new PsychologicalServReferral($encounter_nr);
$person_obj = new Person;

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
    $fulltime_format = "F j, Y";
    return ($date && $date != "0000-00-00 00:00:00") ? date($fulltime_format,strtotime($date)) : date($fulltime_format);
}


$psyServRefInfo = $obj_psyServ->getPsychologicalServReferral();

if(!$psyServRefInfo){

    $psyServRefInfo = $obj_psyServ->fld_psychological_serv_referral;

}else{

    $psyServRefInfo['create_id'] = $psyServRefInfo['modify_id'] ?  $psyServRefInfo['modify_id'] : $psyServRefInfo['create_id'];
    $psyServRefInfo['create_dt'] = $psyServRefInfo['modify_dt'] ?  getFullTimeFormatDate($psyServRefInfo['modify_dt']) : getFullTimeFormatDate($psyServRefInfo['create_dt']);
}

$params = array_merge($psyServRefInfo,$personInfo);

$params["r_doh"] = appendBasePath('modules/registration_admission/image/logo_doh.jpg');
$params["r_spmc"] = appendBasePath('modules/registration_admission/image/dmc_logo.jpg');
$params["r_ipbm"] = appendBasePath('img/ipbm.png');
$params["is_opd"] = $psyServRefInfo['is_opd'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_ciu"] = $psyServRefInfo['is_ciu'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_fw"] = $psyServRefInfo['is_fw'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_mw"] = $psyServRefInfo['is_mw'] == "1" ? appendBasePath('images/check2.gif') : NULL;
$params["is_others"] = $psyServRefInfo['others'] ? appendBasePath('images/check2.gif') : NULL;
if(strlen($encoder) > 30){
   $params["encoder2"] = $encoder;
   $params["encoder"] = ""; 
}else{
    $params["encoder"] = $encoder;
}

showReport('psychological_serv_referral',$params,array(0 => ""),'pdf');
?>