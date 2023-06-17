<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'modules/bloodBank/ajax/blood-waiver.server.php');



$pers_obj = new Personell;

$objInfo = new Hospital_Admin();

if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hospital_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hospital_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}

$orfeetotal = 0;
$proffeetotal = 0;
$rowindex = 0;
$data = array();

if ($_GET['enc']) {
  # code...

$enc_nr = $_GET['enc'];

$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($_GET['enc']);
$patient_name = $encounter_details['name_last'].", ".$encounter_details['name_first']." ".substr($encounter_details['name_middle'],0,1).".";
$patient_address = $encounter_details['street_name']." ".$encounter_details['brgy_name'].", ".$encounter_details['mun_name'].", ".$encounter_details['prov_name'];
$ward_name;
$age = $encounter_details['age'];
$sex = $encounter_details['sex'];
$dept = $encounter_details['er_opd_admitting_dept_name'];
$encounter_types = array("1"=>"ER PATIENT"/*ER*/,
             "2"=>'OUTPATIENT'/*OPD*/, 
             "3"=>'INPATIENT ER'/*Admitted through Emergency Room*/,
             "4"=>'INPATIENT OPD',
             "5"=>'DIALYSIS',
             "6"=>'Health Service and Specialty Clinic');

switch ($encounter_details['encounter_type']) {
  case 3:
      $ward_name = $encounter_details['ward_name'];

    break;
  case 4:
      $ward_name = $encounter_details['ward_name'];
    break;
    case 1:

    $key = $seg_encounter->getERlocationNew($encounter_details['er_location']);
    $key2 = $seg_encounter->getERlocationLobbyNew($encounter_details['er_location_lobby']);
      $ward_name = $key['area_location']."(".$key2['lobby_name'].")";
      break;
  case 2:
      $ward_name =$encounter_types[2];
    break;
   case 6:
      $ward_name =$encounter_types[6];
     break;
  }
  if ($encounter_details['date_birth'] != '0000-00-00') {
    $date_of_birth = date('F j, Y', strtotime($encounter_details['date_birth'])) . '/';
  }
  else
    $date_of_birth = '';
}else  {
  $seg_PID = new Encounter();
  $patient_info =$seg_PID->pidInfoWaiver($_GET['pid']);
  $patient_name =$patient_info['pname'];
  $patient_address =$patient_info['address'];
  $ward_name ="";
  $age = $patient_info['age'];
  $sex = $patient_info['sextype'];
  $$encounter_details['informant_name'] =" ";
  if ($patient_info['date_birth'] != '0000-00-00') {
    $date_of_birth = date('F j, Y', strtotime($patient_info['date_birth'])) . '/';
}
  else
    $date_of_birth = '';
}
  
$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

# updated by carriane 04/13/18
#added by raymond for waiver information
$fullinfo = stripslashes($_GET['fullinfo']);
$infos = json_decode($fullinfo, true);
$pid = $_GET['pid'];
$refno = $_GET['refno'];
$enc = $_GET['enc'];

// if(empty($refno)){
// $refno = "T".$pid;
// }

if(!hasAlreadyPrinted($refno))
  saveWaiverInfo($refno, $enc, $pid, $infos);
else
  updateWaiverInfo($refno, $infos);
#end raymond
// die($baseurl . "gui/img/logos/dmc_logo.png");
// var_dump($key2['lobby_name']); die();
$params = array(
  'r_doh' => $baseurl . "img/doh.png",
  'r_spmc' => $baseurl . "gui/img/logos/dmc_logo.jpg",
  'pname'=>$patient_name." / ".$_GET['ages']." / ".(($sex=='f') ? "Female" : "Male")." / HRN: ".$_GET['pid'],
  'address'=>$patient_address,
  'ward_dpt'=>$ward_name,
  'dept'=>(($dept=="") ?  $encounter_details['name_formal'] : $dept),
  'informant_name'=>$encounter_details['informant_name'],
  'hosp_name' =>  strtoupper($row['hosp_name']),
  'user_login'=>$_SESSION["sess_user_name"],
  'realtime'=>date("h:i A"),
  'date'=>date("M-d-Y")
  );

$data = array();

$i = 0;

foreach($infos as $info){
  $data[$i] = array(
      'group'       => 'first',
      'unitno'      => $info['unitno'],
      'bloodgroup'  => $info['bloodgrp'],
      'donorunit'  => $info['donorunit'],
      'expiry'      => $info['expiry'],
      'component'   => $info['component'],
      'source'      => $info['source']
    );
    $i++;
}

$dt = array(
  'group'       => '',
  'unitno'      => '',
  'bloodgroup'  => '',
  'donorunit'  => '',
  'expiry'      => '',
  'component'   => '',
  'source'      => ''
);

if(empty($refno)){
  $ctr = 10 - $i;
  for ($x = 0; $x < $ctr; $x++) {
    $finalData[] = $dt;
  }
}else{
  $ctr = 5 - $i;
  for ($x = 0; $x < $ctr; $x++) {
      $finalData[] = $dt;
  }
}


$output = array_merge((array)$data, (array)$finalData);

showReport('BB_Waiver_of_Deposit',$params,$output,'PDF'); 
?>
