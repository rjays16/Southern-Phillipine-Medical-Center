<?php
error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require_once('roots.php');
require_once($root_path . 'include/inc_jasperReporting.php');
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once($root_path . 'include/inc_environment_global.php');

global $db;

$encounter_nr = $_GET['encounter'];

$sql = "SELECT 
  			`fn_get_person_name_first_mi_last` (cml.`pid`) AS p_name,
  			`fn_get_personell_firstname_last` (cml.`physician`) AS dr_name,
  			cml.* 
		FROM
  			`seg_industrial_cert_med_lto` cml 
		WHERE cml.`encounter_nr` =".$db->qstr($encounter_nr);

/*$sql2 = "SELECT c_p.other_title 
            FROM seg_industrial_cert_med_lto AS s_i_c
                LEFT JOIN care_personell AS c_p
                    ON s_i_c.`physician` = c_p.`nr` ";*/


$result = $db->Execute($sql)->FetchRow();
//$result2 = $db->Execute($sql2)->FetchRow();

//$title = ($result2['other_title'] == '') ? 'MD' : $result2['other_title'];
//$comma = ($title == '') ? '' : ', ';



$params = array(
		'med_cert' => date('F d, Y', strtotime($result['create_dt'])),
		'driver_name' => utf8_decode(trim(strtoupper($result['p_name']))),
		'fit_yes' => ($result['physical_fit'] == 'yes') ? 'X' : '',
		'fit_no' => ($result['physical_fit'] == 'no') ? 'X' : '',
		'upper_limbs' => ($result['upper_limbs'] != NULL || $result['upper_limbs'] != '') ? 'X' : '',
		'upper_left' => ($result['upper_limbs'] == 'left') ? 'X' : '',
		'upper_right' => ($result['upper_limbs'] == 'right') ? 'X' : '',
		'lower_limbs' => ($result['lower_limbs'] != NULL || $result['lower_limbs'] != '') ? 'X' : '',
		'lower_left' => ($result['lower_limbs'] == 'left') ? 'X' : '',
		'lower_right' => ($result['lower_limbs'] == 'right') ? 'X' : '',
		'paralyze' => ($result['paralyzed_leg'] != NULL || $result['paralyzed_leg'] != '') ? 'X' : '',
		'paralyze_left' => ($result['paralyzed_leg'] == 'left') ? 'X' : '',
		'paralyze_right' => ($result['paralyzed_leg'] == 'right') ? 'X' : '',
		'paraplegic' => ($result['paraplegic'] == '1') ? 'X' : '',
		'eye_yes' => ($result['clear_eyesight'] == 'yes') ? 'X' : '',
		'eye_no' => ($result['clear_eyesight'] == 'no') ? 'X' : '',
		'eye_partial' => ($result['eye_defect'] == 'partial') ? 'X' : '',
		'eye_color' => ($result['eye_defect'] == 'color') ? 'X' : '',
		'eye_glasses' => ($result['eye_defect'] == 'glass') ? 'X' : '',
		'hear_yes' => ($result['clear_hearing'] == 'yes') ? 'X' : '',
		'hear_no' => ($result['clear_hearing'] == 'no') ? 'X' : '',
		'hear_speech' => ($result['hearing_defect'] == 'speech') ? 'X' : '',
		'hear_device' => ($result['hearing_defect'] == 'device') ? 'X' : '',
		'other_findings' => $result['other_findings'],
		'physician' => strtoupper(trim($result['dr_name'])) .', MD',
		'address' => 'HEALTH SERVICE AND SPECIALTY CLINIC (SPMC)',
		'address2' => 'J.P. LAUREL AVENUE, BAJADA, DAVAO CITY',
		'control_num' => $result['control_num'],
	);

showReport('IC_Lto_MedCert', $params, $data, 'PDF');
?>