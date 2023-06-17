<?php
     #Update 2014-06-06
#created by Borj, 2/8/2014
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_personell.php');
$pers_obj = new Personell;

include_once($root_path . 'include/care_api_classes/billing/class_billing.php');
$bill_obj = new Billing;


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

$trans_no = $_GET['trans_no'];

if (isset($_GET['trdte']) && $_GET['trdte'])
  $rep->transmit_date = strftime("%B %d, %Y", $_GET['trdte']);
else
  $rep->transmit_date = strftime("%B %d, %Y");

$sig_info = $pers_obj->get_Signatory('transmittal_bill');
$sig_info_1 = $pers_obj->get_Signatory('transmittal_admin');
$sig_info_1_1 = $pers_obj->get_Signatory('transmittal_clerical');

if ($sig_info['title']) {
  $prov_name = mb_strtoupper($sig_info['name']).','.mb_strtoupper($sig_info['title']).'';
}
else {
  $prov_name = mb_strtoupper($sig_info['name']);
}

if ($sig_info_1['title']) {
  $prov_name_1 = mb_strtoupper($sig_info_1['name']).','.mb_strtoupper($sig_info_1['title']).'';
}
else {
  $prov_name_1 = mb_strtoupper($sig_info_1['name']);
}

if ($sig_info_1_1['title']) {
  $prov_name_1_1 = mb_strtoupper($sig_info_1_1['name']).','.mb_strtoupper($sig_info_1_1['title']).'';
}
else {
  $prov_name_1_1 = mb_strtoupper($sig_info_1_1['name']);
}

$orfeetotal = 0;
$proffeetotal = 0;
$rowindex = 0;
$data = array();

$result = $bill_obj->getSummaryTransmittal($trans_no);
while($row=$result->FetchRow()){

  $data[$rowIndex] = array('category' => $row['category'],
    'noofclaims' => (int)$row['no_of_claims'],
    'orfee' => number_format($row['hosp_charge'], 2),
    'proffee' => number_format($row['prof_charge'], 2),
    'total' => number_format($row['total'], 2));

  $orfeetotal += $row['hosp_charge'];
  $proffeetotal += $row['prof_charge'];
  $overall_total += $row['total'];

  $rowIndex++;
}

$params = array(
  'hosp_country'=>$row['hosp_country'],
  'hosp_agency'=>$row['hosp_agency'],
  'hospital_name'=>$row['hosp_name'],
  'hosp_addr1'=>$row['hosp_addr1'],
  'status'=>mb_strtoupper($_GET['status']),
  'orfee_total' => number_format($orfeetotal, 2),
  'proffee_total' => number_format($proffeetotal, 2),
  'overall_total' => number_format($overall_total, 2),
  'prov_name' => $prov_name,
  'prov_pos' => mb_strtoupper($sig_info['signatory_title']),
  'prov_name_1' => $prov_name_1,
  'prov_pos_1' => mb_strtoupper($sig_info_1['signatory_title']),
  'prov_name_1_1' => $prov_name_1_1,
  'prov_pos_1_1' => mb_strtoupper($sig_info_1_1['signatory_title']),
  'transmit_no' => $_GET['trans_no'],
  'trans_date' => strftime("%B %d, %Y", $_GET['trans_date']),
  );

showReport('Classification_Summary_Transmittal',$params,$data,'PDF'); 
?>

