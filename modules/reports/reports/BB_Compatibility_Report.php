<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');

global $db;

define(BLUE, 3);
define(revM, 2);
define(revN, 0);
define(rev, 1);

$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('adult_age');
$adult_age = $GLOBAL_CONFIG['adult_age'];

// added by carriane 02/20/18
$glob_obj->getConfig('ENovember');
$ENovember = $GLOBAL_CONFIG['ENovember'];
$glob_obj->getConfig('EMarch');
$EMarch = $GLOBAL_CONFIG['EMarch'];
// end carriane

$units = $_GET['id'];
// $unitLast = substr($units, -1);
$unitLast = preg_replace("/[^0-9,.]/", "", $units);
$type_request = $_GET['types'];
$serials = $_GET['serials'];
$ward = $_GET['depts'];
$component = $_GET['components']=="WB_PRBC"?"WB & PRBC":$_GET['components'];
$sources = $_GET['sources'];
$drecieved = $_GET['dreceived'];
$trecieved = $_GET['treceived'];
$rmeridian = $_GET['mreceived'];
$started = $_GET['dstarted'];
$stime = $_GET['tstarted'];
$mstarted = $_GET['mstarted'];
$done = $_GET['ddone'];
$dtime = $_GET['tdone'];
$mdone = $_GET['mdone'];
$result = $_GET['result'];
$pat_name = $_GET['pat_name'];
$hrn = $_GET['hrn'];
$sex = $_GET['sex'];
$qty = $_GET['qty'];
$refno = $_GET['refno'];
$age = substr($_GET['age'],0,2);
$test_code = $_GET['test_code'];
$caption = "COMPATIBILITY TEST RESULT";

$sql = $db->Prepare("SELECT sbrd.`service_code`,sbrd.`is_urgents` AS urgents, 
    cp.`date_birth` AS birth,
    IF(cp.`date_birth` = '0000-00-00', cp.`age`, fn_get_age(NOW(), cp.`date_birth`)) as age,
    sbrd.`result` AS resultcompa, slsd.`clinical_info` AS clinical_info, 
    slsd.`request_doctor`, sbrd.`service_code`,  
    CONCAT('Dr','.',`fn_get_person_name_first_mi_last`(cpl.`pid`)) AS doc_name,
    IF(sbrd.`modify_dt` > sbrs.`modify_dt`, sbrd.`modify_id`, sbrs.`modify_id`) AS modify_id,
    bt.NAME AS blood_type 
FROM seg_lab_serv sls
LEFT JOIN `seg_lab_servdetails` slsd ON sls.`refno` = slsd.`refno`
LEFT JOIN  seg_blood_received_details sbrd 
    ON slsd.`refno` = sbrd.`refno` AND slsd.`service_code` = sbrd.`service_code`
LEFT JOIN seg_blood_received_status sbrs 
    ON sbrs.`refno` = sbrd.`refno` AND sbrs.`service_code` = sbrd.`service_code` AND sbrs.`ordering` = sbrd.`ordering`
LEFT JOIN care_person cp ON sls.`pid` = cp.`pid`
LEFT JOIN `care_personell` cpl ON slsd.`request_doctor` = cpl.`nr`
LEFT JOIN seg_blood_type_patient b ON b.pid=cp.pid
LEFT JOIN seg_blood_type bt ON bt.id=b.blood_type
WHERE sbrd.`refno`='$refno' AND sbrd.`service_code`='$test_code'
AND sbrd.`ordering` = ".$db->qstr($unitLast));

// Added By Matsuu 0602017
// Change By Leira 02/12/2018
$sql_getcompatibility = $db->Prepare("SELECT sbc.adult,sbc.pedia,sbc.child from seg_blood_compatibility as sbc");
$getcompatibility= $db->Execute($sql_getcompatibility);
$compatibility = $db->Execute($sql);
while ($rows = $getcompatibility->FetchRow()) {
    $pedia = $rows['pedia'];
    $adult = $rows['adult'];
    $child = $rows['child'];
}
// Change Ended By Leira
// Ended by Matsuu


while ($row = $compatibility->FetchRow()) {
    $urgents = $row['urgents'];
    if($row['birth'] == '0000-00-00')
        $birth = "UNKNOWN";
    else
        $birth = date('m/d/Y', strtotime($row['birth']));
    $age = $row['age'];
    $resultcompa = $row['resultcompa'];
    $clinical_info = $row['clinical_info'];
    $doc_name = $row['doc_name'];
    $encoder = $row['modify_id'];
    $blood_type = $row['blood_type'];
}

if ($urgents == 1){
    $urgents = 'STAT';
}else{
    $urgents = 'ROUTINE';
}
$getAge = explode(" ", $age);
if((int)$getAge[0]>=$adult_age && $getAge[1]=='years') {
    $footer = $adult;
    $effectivity = $EMarch;
    $rev = rev;
}
elseif (((int)$getAge[0]>=5 && $getAge[1]=='months') || ((int)$getAge[0]<19 && ($getAge[1]=='year' || $getAge[1]=='years') ) ){
    $footer = $child;
    $effectivity = $EMarch;
    $rev = rev;
}
else {
    $footer = $pedia;
    $color = BLUE;
    $effectivity = $ENovember;
    $rev = revN;
}

// Added by Matsuu 
// $getAge = explode(" ", $age);
//     if((int)$getAge[0]>=$adult_age){
//     $footer = $adult;
//     }
//     else{
//     $footer = $pedia;
//     }
// Ended by Matsuu

$compa = 0;
$notcompa = 0;
$re_typ = 0;
if($resultcompa == 'compat'){
    $compa = 1;
}elseif($resultcompa == 'incompat') {
    $notcompa = 1;
}elseif ($resultcompa == 'retype') {
    $re_typ = 1;
}


$sql = $db->Prepare("SELECT value
    FROM seg_define_config
    WHERE param = 'BB_COMPATIBILITY_AGE'");

$ageBracket = $db->Execute($sql);
$row2 = $ageBracket->FetchRow();
$ageArr = explode(" ", $age);
if($ageArr['1'] !== "years")
    $compa_header = $caption;
else{
    if($ageArr['0'] < $row2['value']){
        $compa_header = $caption;
    }else{
        $compa_header = $caption;
    }
}


$sql = $db->Prepare("SELECT value FROM seg_define_config WHERE id =".$db->qstr($color));

$ageBracket = $db->Execute($sql);
$row3 = $ageBracket->FetchRow();
$ageArr = explode(" ", $age);

// var_dump($age);die();
$color_code = $row3['value'];


// $sig_info = $pers_obj->get_Signatory('medcert');
// $name_officer = mb_strtoupper($sig_info['name']);
// $officer_position = $sig_info['signatory_position'];
// $officer_title = $sig_info['signatory_title'];

$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hospital_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hosp_header']  = "Center for Health Development";
  $row['hospital_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}

//added by Kiefher Chavez 5/25/2021 para ma limit ang char and d mag overlap inig print
$clinical_info = substr($clinical_info, 0,450);
//kiefher end

$params = array(
    "hosp_country" => $row['hosp_country'],
    "hosp_agency" => $row['hosp_agency'],
    "hosp_header" => $row['hosp_header'],
    "hosp_name" => $row['hosp_name'],
    "hosp_addr1" => $row['hosp_addr1'],

    "source" => $sources,
    "ward" => $ward,
    "component" => $component,
    "units" => $unitLast,
    "type_request" => $urgents,
    
    "recieved" => $drecieved,
    "rtime" => $trecieved,
    "rmeridian" => $rmeridian,
    "started" => $dstarted,
    "stime" => $tstarted,
    "smeridian" => $mstarted, 
    "done" => $ddone,
    "dtime" => $tdone,
    "dmeridian" => $mdone,
    "compa" => $compa,
    "notcompa" => $notcompa,
    "re_typ" => $re_typ,
    "serial" => $serials,
    "personnel" => $encoder,
    "patient" => utf8_decode(trim($pat_name)),
    "hrn" => $hrn,
    "age" => $age,
    "gender" => $sex,
    "blood" => $blood_type,
    "qtys" => $qty,
    "physician" => utf8_decode(trim($doc_name)),
    //edited by Kiefher Chavez 5/18/2021 to avoid overflowing of diagnosis sa lain fields
    "diagnosis" => (strlen($clinical_info) > 280 ? "" : $clinical_info = str_replace(array("\n", "\r"), ' ', $clinical_info = wordwrap(strtoupper($clinical_info), 100, "\n"))),
    "diagnosis2" => (strlen($clinical_info) > 280 ? $clinical_info = str_replace(array("\n", "\r"), ' ', $clinical_info = wordwrap(strtoupper($clinical_info), 100, "\n")) : ""),
    //kiefher end
    "date_birth" => $birth,
    "compa_header" => $compa_header,
    // Added By: Leira
    "color_code" => $color_code,
    "effectivity" => $effectivity,
    "rev" => $rev,
    // Ended By: Leira
    // Added by Matsu
    "footer"=>$footer
    // Ended by Matsu
                );
$data[0] = array();

$strdatedone = date('Y-m-d H:i:s',strtotime($ddone." ".$tdone));

$pathologist = $db->GetAll("SELECT `fn_get_personell_name` (s.personell_nr) fullname, s.* FROM seg_signatory s WHERE s.document_code='pathologist' AND s.end_date > ".$db->qstr($strdatedone));

$sp = explode("-", $pathologist[0]['section']);

$patFullname = utf8_decode(trim(mb_strtoupper($pathologist[0]['fullname']))).", ".mb_strtoupper($pathologist[0]['title']);

// $baseurl = sprintf(
//     "%s://%s%s",
//     isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
//     $_SERVER['SERVER_ADDR'],
//     substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
// );
$baseDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';

if(!$pathologist[0]['is_default'])
    $data[0]['image_03'] = $baseDir . "images/laboratory/blood_com_signature.jpg";
else $data[0]['image_03_1'] = $baseDir . $pathologist[0]['seg_signatory'];

$data[0]['pathologist'] = $patFullname;

$data[0]['image_01'] = $baseDir . "gui/img/logos/dmc_logo.jpg";
$data[0]['image_02'] = $baseDir . "img/doh.png";

$data[0]['image_04'] = $baseDir . "gui/img/common/default/check2.gif";
$data[0]['image_05'] = $baseDir . "images/space.gif";

showReport('BB_Compatibility_Report', $params, $data, 'PDF');
?>