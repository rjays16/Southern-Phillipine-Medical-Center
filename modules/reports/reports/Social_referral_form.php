<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once($root_path . 'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

global $db;
$pers_obj = new Personell;


// $baseurl = sprintf(
//     "%s://%s%s",
//     isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
//     $_SERVER['SERVER_ADDR'],
//     substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
// );

$baseDir = dirname(dirname(dirname(dirname(__FILE__)))).'/';

$id = $_GET['id'];
$enc_nr = $_GET['enc_nr'];

if($enc_nr == 0)
{
  $sql = "SELECT
             sr.*,
             fn_get_person_name_first_mi_last (sr.pid) AS p_name,
             fn_get_age (NOW(), cp.date_birth) AS age,
             cp.sex,
             cp.civil_status,
             fn_get_complete_address2 (sr.pid) AS address,
             IFNULL(sr.modify_id, sr.create_id) AS prepared_by
           FROM
             seg_social_referrals sr
             INNER JOIN care_person cp
               ON cp.pid = sr.pid
           WHERE sr.refer_id = " . $db->qstr($id);
}
else
  $sql = "SELECT
             sr.*,
             fn_get_person_name_first_mi_last (sr.pid) AS p_name,
             fn_get_age (NOW(), cp.date_birth) AS age,
             cp.sex,
             cp.civil_status,
             fn_get_ward_name (ce.current_ward_nr) AS ward,
             fn_get_department_name (ce.current_dept_nr) AS dept,
             fn_get_complete_address2 (sr.pid) AS address,
             ce.er_opd_diagnosis AS diagnosis,
             ce.current_att_dr_nr AS physician,
             ce.consulting_dr_nr AS consulting,
             IFNULL(sr.modify_id, sr.create_id) AS prepared_by
           FROM
             seg_social_referrals sr
             INNER JOIN care_person cp
               ON cp.pid = sr.pid
             INNER JOIN care_encounter ce
               ON ce.encounter_nr = sr.encounter_nr
           WHERE sr.refer_id = " . $db->qstr($id) ."
             AND sr.encounter_nr = " . $db->qstr($enc_nr);

$result = $db->Execute($sql)->FetchRow();
//var_dump($result); die;
//var_dump($_SESSION); die;

if(($result['physician'] != NULL && $result['physician'] != 0)){
    $physician_nr = $result['physician'];
}
else{
    if(($result['consulting'] != NULL) && ($result['consulting'] != 0)){
        $physician_nr = $result['consulting'];
    }
    else{
        $physician_nr = NULL;
    }
}

if($physician_nr != NULL)
    $physician = $pers_obj->get_Person_name3($physician_nr)->FetchRow();
else
    $physician['dr_name'] = NULL;

$signatory = $pers_obj->get_Signatory('social_referral');
$sig_lic = $pers_obj->get_Person_name3($signatory['personell_nr'])->FetchRow();

$params = array(
    'r_doh' => $baseDir . "img/doh.png",
    'r_spmc' => $baseDir . "gui/img/logos/dmc_logo.jpg",
    'r_date' => date("F d, Y", strtotime($result['refer_dt'])),
    'r_to' => $result['refer_to'],
    'r_patient' => $result['p_name'],
    'r_hrn' => $result['pid'],
    'r_age' => $result['age'],
    'r_sex' => ($result['sex'] == 'm') ? 'Male' : 'Female',
    'r_status' => ucfirst($result['civil_status']),
    'r_ward' => ($result['ward']) ? $result['ward'] : $result['dept'],
    'r_address' => $result['address'],
    'r_admitting' => $result['diagnosis'],
    'r_final' => $result['refer_diagnosis'],
    'r_physician' => ($physician['dr_name']) ? 'DR. ' . utf8_decode(trim(mb_strtoupper($physician['dr_name']))) : '',
    'r_reason' => $result['refer_reason'],
    'r_prepared' => mb_strtoupper($_SESSION['sess_user_name']). ', ' . $signatory['title'],
    'r_approved' => trim($signatory['name']) . ', ' . $signatory['title'],
    'r_position' => $signatory['signatory_position'],
    'r_license' => "Lic. No. " . $sig_lic['license_nr']
);

$data[0] = array();

showReport('Social_referral_form', $params, $data, 'PDF');