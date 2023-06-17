<?php 
 /*
  * Author : syboy 07/15/2015
  * Description : Reports for follow up form
  */

 require_once('roots.php');
 require_once($root_path.'include/inc_jasperReporting.php');
 require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
 require_once $root_path.'include/inc_environment_global.php';
 require_once($root_path."include/care_api_classes/industrial_clinic/class_ic_transactions.php");
 require_once($root_path."include/care_api_classes/industrial_clinic/class_ic_med_cert.php");
 require_once($root_path.'include/care_api_classes/class_personell.php');
 require_once($root_path.'include/care_api_classes/class_department.php');
 include_once($root_path.'include/care_api_classes/class_encounter.php');
 
 global $db;
 global $HTTP_SESSION_VARS;

 $enc_obj      = new Encounter;
 $objIC        = new SegICTransaction();
 $objIC_Cert   = new SegICCertMed();

 # patient information 
 $enc_nr       = $_GET['enc'];
 $pid       = $_GET['pid'];
 $encInfo = $enc_obj->getEncounterInfo($enc_nr);
 $position     = strtoupper($encInfo['occupation']);
 $civilstatus  = strtoupper($encInfo['civil_status']);
 // $pid          = $encInfo['pid'];
 if ($encInfo['brgy_name'] == 'NOT PROVIDED') {
   $brgy_name = '';
 }
 if ($encInfo['mun_name'] == 'NOT PROVIDED') {
  $encInfo['mun_name'] = '';
 }

 $address      = $encInfo['street_name'].' '. $brgy_name.' '.$encInfo['mun_name'];
 #$address = $db->GetOne("SELECT fn_get_complete_address('".$encInfo['pid']."')");
 $personData   = $objIC->getPersonData($encInfo['pid']);
// var_dump($personData); die;
 $person_name  = $encInfo['name_last'];

// added by carriane 12/12/18;
// adjust font size if length exceeds to 59 characters
if(strlen($address) > 90){
    $address_short = $address;
    $address = '';
}
// end carriane

 if ($encInfo['name_first'] != null) {
   $firstname = $encInfo['name_first'];
 }else{
   $firstname = $encInfo['name_2'];
 }
 if ($encInfo['name_middle'] != null) {
   $middlename = $encInfo['name_middle'];
 }else{
   $middlename = $encInfo['name_3'];
 }
// var_dump($personData); die();
 // added by Kenneth 04-06-2016
$age="";
if($personData['date_birth']=="0000-00-00"){
    $age=$personData['age'];
}
else{
    $age = floor((time() - strtotime($personData['date_birth']))/31556926); #added by art 02/20/2014;
}
// end Kenneth
 $sex          = ($personData['sex'] == 'f' ? 'FEMALE' : 'MALE');

 $objInfo = new Hospital_Admin();
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
 $chddr = 'Center for Health Development Davao Region';
 $title = 'FOLLOW-UP EXAMINATION FORM';
 $title_1 = 'HEALTH SERVICE AND SPECIALTY CLINIC';
 $iso = 'SPMC-F-MRO-15B';

 $params = array("hosp_country" 	=> $row['hosp_country'],
                 "hosp_agency" 		=> $row['hosp_agency'],
                 "chddr" => $chddr,
                 "hosp_name" 		=> $row['hosp_name'],
                 "hosp_addr1" 		=> $row['hosp_addr1'],
                 "title" 			=> $title,
                 "title_1" 			=> $title_1,
                 "person_name" 		=> $person_name,
                 "first_name" => $firstname,
                 "middle_name" => $middlename,
                 "hrn" => $encInfo['pid'],
                 "occupation" 		=> $position,
                 "address"      => $address,
                 "address_short" 			=> $address_short,
                 "civilstatus" 		=> $civilstatus,
                 "age" 				=> (String)$age,
                 "sex" 				=> $sex,
                 "iso" 				=> $iso,
 				);

 $follow_ip_data = $objIC_Cert->getIcCertMedXsamFollowUp($pid, $enc_nr);
 foreach ($follow_ip_data as $Fdata) {
 	
  	$data[] = array("dateR" => $Fdata['date_request'],
  					  "vshtwt" => $Fdata['vshtwt'],
  					  "hxpe" => $Fdata['hxpe'],
  					  "remarks" => $Fdata['remarks'],
  					);
  }
  /*
  * Added by : Kenneth 04/06/2016
  * Description : Add template if there is no encoded data instead of blank upon print
  */
  if($data==null){
    $data[] = array("dateR" => "None",
              "vshtwt" => "None",
              "hxpe" => "None",
              "remarks" => "None",
              );
  }
  /*
  * end of Kenneth
  */
// var_dump($data); die();
 $params[] = array();
 $top_dir = 'modules';
 $baseurl = sprintf(
     "%s://%s%s",
     isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
     $_SERVER['SERVER_ADDR'],
     substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
 );
 
 $params['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
 $params['image_02'] = $baseurl . "img/doh.png";
 // var_dump($data); die();
 showReport('ic_medchart_follow_up_form', $params, $data,'PDF');

 ?>