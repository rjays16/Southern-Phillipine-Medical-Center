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

$cond1 = " DATE(ce.`encounter_date`)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

if($dept_nr)
	#$cond2 = " AND ce.current_dept_nr = {$dept_nr}";
  $cond2 = " AND (ce.current_dept_nr IN ({$dept_nr}) OR ce.current_dept_nr IN (SELECT nr FROM care_department AS d WHERE d.parent_dept_nr=".$db->qstr($dept_nr).")) ";
else
	$cond2 = "";

// added by carriane 10/251/7
if($sort=='1'){
  $sortby = " p_name, encounter_date";
}
elseif ($sort=='0') {
  # code...
   $sortby = " encounter_date,p_name";
}
else{
  $sortby = " encounter_date,p_name";
}
// end carriane


$sql = "SELECT 
          ce.`pid`,
          ce.encounter_nr,
          ce.`is_discharged`,
          ce.`encounter_date`,
          fn_get_person_lastname_first (ce.`pid`) AS p_name,
          cp.`date_birth`,
          fn_get_age (
            ce.`encounter_date`,
            cp.`date_birth`
          ) AS age,
          cp.`sex`,
          cp.`civil_status`,
          fn_get_complete_address2 (cp.`pid`) AS address,
          fn_get_department_name (ce.`current_dept_nr`) AS department,
          IF(
            fn_get_admitted_enc_ercase (ce.encounter_nr),
            fn_get_admitted_diagnosis_ercase (ce.encounter_nr),
            sdsi.diagnosis
          ) AS 'diagnosis',
          IF(
            fn_get_admitted_enc_ercase (ce.encounter_nr),
            'Admitted',
            NULL
          ) AS er_admission,
          UPPER(
            IFNULL(
              fn_get_personell_name (
                IF(
                  fn_get_admitted_enc_ercase (ce.encounter_nr),
                  fn_get_admitted_doctor_ercase (ce.encounter_nr),
                  sdsi.personnel_nr
                )
              ),
              sdsi.create_id
            )
          ) admitting_dr 
        FROM
          care_encounter ce 
          INNER JOIN care_person cp 
            ON cp.`pid` = ce.`pid` 
          LEFT JOIN seg_discharge_slip_info sdsi 
            ON ce.encounter_nr = sdsi.encounter_nr 
        WHERE ".$cond1. $cond2 ."
          AND ce.`encounter_type` = '1' 
          AND ce.`status` NOT IN (
            'deleted',
            'hidden',
            'inactive',
            'void'
          ) 
          
        GROUP BY ce.`encounter_nr` 
        ORDER BY ".$sortby;

/*repush branch new*/
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
      
      $row['civil_status'] = str_replace(' ', '', $row['civil_status']);
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
							'f_icd' => htmlentities($row['diagnosis']),
							'f_admit_dr' => utf8_decode(trim($row['admitting_dr'])),
							'f_disposition' => $row['er_admission']
						);

			$i++;
		}
	} else {
		$data = array(
					array('f_hrn' => 'No Data',
            'f_address' => '',
            'f_name' => '')
				);
	}
} else {
	$data = array(
				array('f_hrn' => 'No Data',
            'f_address' => '',
            'f_name' => '')
			);
}

$params->put('num', $i);