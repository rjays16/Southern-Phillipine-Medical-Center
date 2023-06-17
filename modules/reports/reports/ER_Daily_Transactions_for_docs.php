<?php
/*
 * @author Gervie 03/01/2016
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';
include_once($root_path.'include/care_api_classes/class_personell.php');

$pers_obj = new Personell();
$is_doctor = $pers_obj->isDoctor($_SESSION['sess_login_personell_nr']);
$deptNR=null;
$addconditions="";
if($is_doctor) {
	$output = $pers_obj->get_Dept_name($_SESSION['sess_login_personell_nr']);
	$dept_nr=$output['nr'];
}
global $db;
$dept = $db->GetOne("SELECT name_formal FROM care_department WHERE nr={$dept_nr}");

$params->put('date_span', date('F d, Y',$from_date) . " - " . date('F d, Y',$to_date));
$params->put('department', ($dept) ? $dept : 'All Departments');


$time_from = "";
$time_to = "";
if(substr($param_time_to,6)=='PM') {
	if(((int)(substr($param_time_to,0,2))<12)) $time_to=((int)(substr($param_time_to,0,2))+12).substr($param_time_to,2,3).":00";
	else $time_to=((int)(substr($param_time_to,0,2))).substr($param_time_to,2,3).":00";
}
else{
	if(((int)(substr($param_time_to,0,2)))==12) $time_to="00".substr($param_time_to,2,3).":00";
	else $time_to=substr($param_time_to,0,5).":00";
}
if(substr($param_time_from,6)=='PM') {
	if(((int)(substr($param_time_from,0,2))<12)) $$time_from=((int)(substr($param_time_from,0,2))+12).substr($param_time_from,2,3).":00";
	else $$time_from=((int)(substr($param_time_from,0,2))).substr($param_time_from,2,3).":00";
}
else{
	if(((int)(substr($param_time_from,0,2)))==12) $time_from="00".substr($param_time_from,2,3).":00";
	else $time_from=substr($param_time_from,0,5).":00";
}

// var_dump($time_to);die();

$cond1 = "ce.`encounter_date`
               BETWEEN
                    '".date('Y-m-d',$from_date)." ".$time_from."'
                    AND
                    '".date('Y-m-d',$to_date)." ".$time_to."' ";

if($dept_nr)
	$cond2 = " AND ce.current_dept_nr = {$dept_nr}";
else
	$cond2 = "";


$sql = "SELECT 
		  ce.`pid`,
		  ce.`encounter_date`,
		  fn_get_person_lastname_first (ce.`pid`) AS p_name,
		  cp.`date_birth`,
		  fn_get_age (ce.`encounter_date`,cp.`date_birth`) AS age,
		  cp.`sex`,
		  cp.`civil_status`,
		  fn_get_complete_address2 (cp.`pid`) AS address,
		  fn_get_department_name (ce.`current_dept_nr`) AS department,
		  GROUP_CONCAT(ed.`code`) AS code,
		  IF(ce.`is_discharged` = 1, '', 'Admitted') AS disposition 
		FROM
		  care_encounter ce 
		  INNER JOIN care_person cp 
		    ON cp.`pid` = ce.`pid`
		  LEFT JOIN seg_encounter_diagnosis ed 
		    ON ed.`encounter_nr` = ce.`encounter_nr` 
		WHERE ". $cond1 . $cond2 ." 
		  AND ce.`encounter_type` = 1 
		  AND (ed.`is_deleted` = 0 OR ed.`is_deleted` IS NULL)
		  AND ce.`status` NOT IN ('deleted', 'hidden','inactive','void')
	  	GROUP BY ce.`encounter_nr` 
		ORDER BY encounter_date, p_name ";
// var_dump($sql);die();
$res = $db->Execute($sql);

$i = 0;

if($res) {
	if($res->RecordCount() > 0) {
		while($row = $res->FetchRow()){

			$age = explode(" ", $row['age']);
			if($age[1] == 'year' || $age[1] == 'years')
				$p_age = $age[0] . 'y';
			else if($age[1] == 'month' || $age[1] == 'months')
				$p_age = $age[0] . 'm';
			else if($age[1] == 'day' || $age[1] == 'days')
				$p_age = $age[0] . 'd';

			$data[$i] = array(
							'f_num' => $i + 1,
							'f_hrn' => $row['pid'],
							/*'f_datetime' => date('m-d-Y h:i A', strtotime($row['encounter_date'])),*/
							'f_datetime' => date("h:i A",strtotime($row['encounter_date'])),
							'f_name' => utf8_decode(trim(strtoupper($row['p_name']))),
							'f_bday' => ($row['date_birth'] == '0000-00-00') ? 'Not Specified' : date('m-d-Y', strtotime($row['date_birth'])),
							'f_age' => ($row['age'] != null) ? $p_age : 'N/A',
							'f_sex' => strtoupper($row['sex']),
							'f_status' => ($row['civil_status']) ? ucwords($row['civil_status']) : 'N/A',
							'f_address' => utf8_decode(trim($row['address'])),
							'f_department' => $row['department'],
							'f_icd' => str_replace(',', ', ', $row['code']),
							'f_disposition' => $row['disposition']
						);

			$i++;
		}
	} else {
		$data = array(
					array('f_hrn' => 'No Data')
				);
	}
} else {
	$data = array(
				array('f_hrn' => 'No Data')
			);
}
$params->put('num', $i);
$params->put('param_time_from_to', "From ".$param_time_from." to ".$param_time_to);
