<?php
/*added by art 08/13/2014*/
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once $root_path . 'include/inc_environment_global.php';
require_once($root_path . "include/care_api_classes/industrial_clinic/class_ic_transactions.php");
require_once($root_path . "include/care_api_classes/industrial_clinic/class_ic_med_cert.php");
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . 'include/care_api_classes/class_department.php');
include_once($root_path . 'include/care_api_classes/class_encounter.php');

global $db;
global $HTTP_SESSION_VARS;
#--------------------------------------------------------------------------------------
$enc_obj = new Encounter;
$objIC = new SegICTransaction();
$objIC_Cert = new SegICCertMed();
$pers_obj = new Personell;
$dept_obj = new Department;


$enc_nr = $_GET['enc'];
$encInfo = $enc_obj->getEncounterInfo($enc_nr);
$landline = $encInfo['phone_1_nr'];
$mobile = $encInfo['cellphone_1_nr'];
if ($landline != '' && $mobile != '') {
    $contact = $lanline . ' ' . $mobile;
} elseif ($landline != '') {
    $contact = $lanline;
} else {
    $contact = $mobile;
}

$position = strtoupper($encInfo['occupation']);
$civilstatus = strtoupper($encInfo['civil_status']);
$pid = $encInfo['pid'];
$dateExamined = date('Y-m-d', strtotime($encInfo['encounter_date']));
$dob = $encInfo['date_birth'];
$bplace = $encInfo['place_birth'];
$brgy_name = $encInfo['brgy_name'];
if ($encInfo['brgy_name'] == 'NOT PROVIDED') {
    $brgy_name = '';
}
if($encInfo['mun_name'] == 'NOT PROVIDED') {
    $encInfo['mun_name'] = '';
}

$address = $encInfo['street_name'] . ' ' . $brgy_name . ' ' . $encInfo['mun_name'];

// added by carriane 12/12/18;
// adjust font size if length exceeds to 59 characters
if(strlen($address) > 80){
    $address_short = $address;
    $address = '';
}
// end carriane

$personData = $objIC->getPersonData($encInfo['pid']);
$person_name = $personData['full_name'];
$sex = ($personData['sex'] == 'f' ? 'FEMALE' : 'MALE');
$age = floor((time() - strtotime($personData['date_birth'])) / 31556926);

$icTransInfo = $objIC->getICtransDetails($enc_nr);
$agency = ($icTransInfo['agency_charged'] != '0') ? $icTransInfo['agency'] : '';
// $agency = strtoupper($icTransInfo['agency']);


$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hosp_name'] = strtoupper($row['hosp_name']);
} else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency'] = "DEPARTMENT OF HEALTH";
    $row['hosp_name'] = "DAVAO MEDICAL CENTER";
    $row['hosp_addr1'] = "JICA Bldg., JP Laurel Avenue, Davao City";
}
$title = 'HEALTH SERVICES AND SPECIALTY CLINIC (HSSC) MEDICAL EXAMINATION CHART';


$i = 'I, <u>' . $person_name . '</u> certify that my answer to the foregoing questions are true, complete and correct to the best of my knowledge and belief. I am aware that deliberate misstatements will disqualify me from my employment and future claims.';
/*------------------vital signs-----------------------------*/
$vital = $objIC_Cert->getVitalSignsMedchart($pid, $enc_nr);

$histSm = $objIC_Cert->getHistorySmoker($pid);
$histDr = $objIC_Cert->getHistoryDrinker($pid);

/*----------------------content------------------------------*/
for ($cn = 1; $cn <= 7; $cn++) {
    $cont = $objIC_Cert->getContentMedchart($pid, $enc_nr, $cn);
    ${'content' . $cn} = $cont;
}
/*----------------------chartinfo--------------------*/
$refno = $_GET['refno'];
$medinfo = $objIC_Cert->getMedChartInfo($pid, $refno);

$physician = $pers_obj->get_Person_name($medinfo[0]['physician_nr']);
$iso = 'SPMC-F-MRO-15';

$params = array("hosp_country" => $row['hosp_country'],
    "hosp_agency" => $row['hosp_agency'],
    "hosp_name" => $row['hosp_name'],
    "hosp_addr1" => $row['hosp_addr1'],
    "title" => $title,
    "hrn" => $pid,
    "dateExamined" => $dateExamined,
    "person_name" => $person_name,
    "agency" => $agency,
    "occupation" => $position,
    "address" => $address,
    "address_short" => $address_short,
    "civilstatus" => $civilstatus,
    "bplace" => $bplace,
    "contact" => $contact,
    "age" => (string)$age,
    "sex" => $sex,
    "bday" => $dob,
    "height" => $vital['height'],
    "weight" => $vital['weight'],
    "blood_pressure" => $vital['blood_pressure'],
    "pulse_rate" => $vital['pulse_rate'],
    "resp_rate" => $vital['resp_rate'],
    "bmi" => $vital['bmi'],
    "smokerYES" => ($histSm['smoker_history'] == 'yes' ? 'x' : ''),
    "smokerNO" => ($histSm['smoker_history'] == 'no' ? 'x' : ''),
    "smokerNA" => ($histSm['smoker_history'] == 'na' ? 'x' : ''),
    "drinkerYES" => ($histDr['drinker_history'] == 'yes' ? 'x' : ''),
    "drinkerNO" => ($histDr['drinker_history'] == 'no' ? 'x' : ''),
    "drinkerNA" => ($histDr['drinker_history'] == 'na' ? 'x' : ''),
    "ishihara1" => ($vital['ishihara'] == 1 ? 'x' : ''),
    "ishihara2" => ($vital['ishihara'] == 2 ? 'x' : ''),
    "hearing1" => ($vital['hearing'] == 1 ? 'x' : ''),
    "hearing2" => ($vital['hearing'] == 2 ? 'x' : ''),
    "speech1" => ($vital['speech'] == 1 ? 'x' : ''),
    "speech2" => ($vital['speech'] == 2 ? 'x' : ''),
   // "personalHist" => $content1,
    "personalHist" => utf8_decode($content1),   #updated by gelie 09/11/2015
    // "familyHist" => $content2,
    "familyHist" => utf8_decode($content2),     
    // "immunizationHist" => $content3,
    "immunizationHist" => utf8_decode($content3), 
     "historyPresentIllness" => utf8_decode($content7), #updated by jamen 4/25/2017
    //"historyIllness" => utf8_encode($content7), #updated by glory 4/17/2017
    // "dental" => $content4,
    "dental" => utf8_decode($content4),
    // "optha" => $content5,
    "optha" => utf8_decode($content5),
    // "ent" => $content6,
    "ent" => utf8_decode($content6),
    // "diagnosis" => $medinfo['diagnosis'],
    "diagnosis" => utf8_decode($medinfo[0]['diagnosis']),
    // "treatment" => $medinfo['treatment'],
    "treatment" => utf8_decode($medinfo[0]['treatment']),
    "remarks" => utf8_decode($medinfo[0]['remarks']), # added by: syboy 10/26/2015 : meow
    "physician" => htmlentities($physician['dr_name']),     #updated by gelie 09/16/2015
    "physician_license" => $physician['license_nr'],
    "datenow" => date('Y-m-d'),

    "i" => $i,
    "iso" => $iso,
    "tel_no" => $db->GetOne("SELECT value FROM care_config_global WHERE type=?",'main_info_phone'),
    "local_no" => '4144'
);

for ($c = 1; $c <= 30; $c++) {
    $cond = $objIC_Cert->getConditionsMedchart($pid, $enc_nr, $c);
    $params['conditionYes' . $c] = ($cond == 1 ? 'x' : '');
    $params['conditionNo' . $c] = ($cond == 2 ? 'x' : '');
}

for ($p = 1; $p <= 19; $p++) {
    $phy = $objIC_Cert->getPhysicalMedchart($pid, $enc_nr, $p);
    $params['physicalYes' . $p] = ($phy['is_selected'] == 1 ? 'x' : '');
    $params['physicalNo' . $p] = ($phy['is_selected'] == 2 ? 'x' : '');
    // $params['physicalRemarks' . $p] = $phy["remarks"];
    $params['physicalRemarks' . $p] = utf8_decode($phy["remarks"]);
}

for ($d = 1; $d <= 24; $d++) {
    $diag = $objIC_Cert->getDiagnosticMedchart($pid, $enc_nr, $d);
    $params['diagnosticYes' . $d] = ($diag["is_selected"] == 1 ? 'x' : '');
    $params['diagnosticNo' . $d] = ($diag["is_selected"] == 2 ? 'x' : '');
    // $params['diagnosticRemarks' . $d] = $diag["remarks"];
    $params['diagnosticRemarks' . $d] = utf8_decode($diag["remarks"]);
}

for ($r = 1; $r <= 5; $r++) {
    $params['recommendation' . $r] = ($medinfo[0]['recommendation'] == $r ? 'x' : '');
}

for ($v = 1; $v <= 4; $v++) {
    $params['visual_acuity' . $v] = ($vital['visual_acuity'] == $v ? 'x' : '');
}

//added by Nick 7-11-2015
$otherClinicalFindings = SegICCertMed::getPersonOtherClinicalFindings($pid, $enc_nr);
foreach($otherClinicalFindings as $examId => $finding){
    foreach($finding as $field => $value){
    #added by gelie 09/16/2015
        if(mb_detect_encoding($value,"UTF-8, ISO-8859-1") == "UTF-8"){
            $params["exam_{$examId}_{$field}"] = utf8_decode($value);
        }
        else{
            $params["exam_{$examId}_{$field}"] = $value;
        }
    #end gelie
    }
}
//end Nick

$top_dir = 'modules';
// $baseurl = sprintf(
//     "%s://%s%s",
//     isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
//     $_SERVER['HTTP_HOST'],
//     substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
// );

$baseDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';

$data[0]['image_01'] = $baseDir . "gui/img/logos/dmc_logo.jpg";
$data[0]['image_02'] = $baseDir . "img/doh.png";
$data[0]['image_03'] = $baseDir . "images/check2.png";

showReport('ic_medchart', $params, $data, 'PDF');