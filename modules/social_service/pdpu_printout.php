<?php
/*added by art 08/28/2014*/
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_social_service.php');


global $db;
global $HTTP_SESSION_VARS;
#--------------------------------------------------------------------------------------
;
$pers_obj=new Personell;
$objInfo = new Hospital_Admin();
$objSS = new SocialService;

if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hosp_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}
$title = 'PDPU ASSESSMENT AND REFERRAL FORM';

$pdpu = $objSS->getPdpu($_GET['mss']);
$detail =$objSS->getPatientDetails($_GET['mss']);

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$params = array("hosp_country"=>$row['hosp_country'],
              "hosp_agency"=>$row['hosp_agency'],
              "hosp_name"=>$row['hosp_name'],
              "hosp_addr1"=>$row['hosp_addr1'],
              "title" => $title,
              "name" =>$detail['pname'],
              "hrn" =>$detail['pid'],
              "age" =>floor((time() - strtotime($detail['date_birth']))/31556926).' years old',
              "sex" =>($detail['sex'] == 'f'? 'Female' : 'Male'),
              "civilstat" =>$detail['civil_status'],
              "addrs" =>$detail['address'],
              "dx" =>$pdpu['dx'],
              "ward" =>$pdpu['ward'],
              "physician" =>$detail['physician'],
              "class" =>$pdpu['class'],
              "intervention" =>$pdpu['intervention'],
              "remarks" =>$pdpu['remarks'],
              "staff" =>$pdpu['create_id'],
              "iso" => 'SPMC-F-PDPU-14',
              "effectivity" =>'Effectivity: October 1, 2013',
              "rev" =>'Rev.0',
              "image_01" => $baseurl . 'gui/img/logos/dmc_logo.jpg',
              "image_02" => $baseurl . 'img/doh.png',
              "date" =>'Date: '.date('Y-m-d',strtotime($pdpu['create_time'])),
             );

$data[0] =array();
showReport('pdpu',$params,$data,'PDF');
?>