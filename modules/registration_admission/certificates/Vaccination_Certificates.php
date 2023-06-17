<?php
#created by Borj, 2/8/2014
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');

include_once($root_path . 'include/inc_date_format_functions.php');

require_once($root_path . '/include/care_api_classes/class_drg.php');
$objDRG = new DRG;
include_once($root_path . 'include/care_api_classes/class_encounter.php');
$enc_obj = new Encounter;
include_once($root_path . 'include/care_api_classes/class_cert_med.php');
include_once($root_path . 'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;
include_once($root_path . 'include/care_api_classes/class_personell.php');
$pers_obj = new Personell;
include_once($root_path . 'include/care_api_classes/class_cert_death.php');
$obj_deathCert = new DeathCertificate($pid);

$pid = $_GET['pid'];

//      if($_GET['encounter_nr']){
//     if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
//         echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
//         exit();
//     }
//     echo 
//     extract($encInfo);
//     //echo json_encode($enc_obj->getEncounterInfo($_GET['encounter_nr']));
// }else{
//     echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
//     exit();
// }


global $db;

$sql = $db->Prepare("SELECT cp.`name_first`, cp.`name_last`, cp.`suffix`, cp.`date_birth`,
                        fn_get_person_name(cp.pid) AS 'lnamefname', spv.`vac_details`, spv.`vac_date`
    FROM care_person cp
    INNER JOIN  `seg_person_vaccination` spv
    ON cp.`pid`=spv.`pid`
    WHERE cp.`pid`='$pid'");

$vac_data = $db->Execute($sql);
while ($row = $vac_data->FetchRow()) {
    $lnamefname = $row['lnamefname'];
    $date_birth = $row['date_birth'];
    $vac_details = $row['vac_details'];
    $vac_date = $row['vac_date'];

}
// if($vac_data)
//cho '<pre>'.$sql.'</pre>';exit();


$sig_info = $pers_obj->get_Signatory('medcert');
$name_officer = mb_strtoupper($sig_info['name']);
$officer_position = $sig_info['signatory_position'];
$officer_title = $sig_info['signatory_title'];


$sentence = '         This to certify that ' . strtoupper($lnamefname) . ' was born on, ' . date("F d, Y", strtotime($date_birth)) . ' in this hospital and was given ' . strtoupper($vac_details) . ' on ' . date("F d, Y", strtotime($vac_date)) . '.';

$sentence1 = '         Done this ' . date('jS') . ' of ' . date("F, Y") . ' in Davao City, Philippines.';


$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hosp_agency'] = strtoupper($row['hosp_agency']);
    $row['hospital_name'] = strtoupper($row['hosp_name']);
} else {
    $row['hosp_country'] = "Republic of the Philippines";
    $row['hosp_agency'] = "DEPARTMENT OF HEALTH";
    $row['hospital_name'] = "SOUTHERN PHILIPPINES MEDICAL CENTER";
    $row['hosp_addr1'] = "J.P. Laurel Bajada, Davao City";


}

$params = array("hosp_country" => $row['hosp_country'],
    "hosp_agency" => $row['hosp_agency'],
    "hospital_name" => $row['hosp_name'],
    "hosp_addr1" => $row['hosp_addr1'],
    "transmit_no" => $_GET['encounter_nr'],

    "name" => $sentence,
    "datenow" => $sentence1,
    "assignee" => '<b>' . mb_strtoupper($sig_info['name']) . ', ' . $sig_info['title'] . '</b>',
    "position" => '' . $sig_info['signatory_title']);


$data[0] = array();


$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['image_02'] = $baseurl . "img/doh.png";

showReport('Vaccination_Certificate', $params, $data, 'PDF');
?>
