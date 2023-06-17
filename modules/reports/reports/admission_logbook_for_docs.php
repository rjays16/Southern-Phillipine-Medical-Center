<?php 
/**
 * @author : syboy 02/23/2015 : meow
 * old report of admission transfer in report launcher in admission.
 * Jira # : SPMC-557
 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj = new Personell();
$is_doctor = $pers_obj->isDoctor($_SESSION['sess_login_personell_nr']);
define(OBGYNE, 155);
define(OBHIGH, 275);
$deptNR=null;
$addconditions="";
if($_GET['dept_nr']==IPBM_DEP){
	$enc_type = 'ce.encounter_type IN (13,14)';
	$age_label_yr= "y.o.";
	$age_label_m = "m.o.";
	$age_label_d ="d.o.";
}else{
	$enc_type = 'ce.encounter_type IN (3,4)';
	if($is_doctor && $_GET['dept_nr']!=IPBM_DEP) {
		$deptNR = $pers_obj->get_Dept_name($_SESSION['sess_login_personell_nr']);
		if($deptNR['nr'] == OBGYNE) {
			$addconditions="AND cd.nr IN (SELECT nr FROM care_department WHERE (parent_dept_nr ='".OBGYNE."' OR nr='".OBGYNE."') OR (nr = '".OBHIGH."'))";
		}else{
			$addconditions="AND (cd.nr = ". $deptNR['nr']." OR cd.parent_dept_nr =". $deptNR['nr']." )";	
		}
		
	}
	$age_label_yr= "y";
	$age_label_m = "m";
	$age_label_d ="d";
}
include('parameters.php');

$params->put("host_country",$hosp_country);
$params->put("host_agency", $hosp_agency);
$params->put("host_name", mb_strtoupper($hosp_name));
$params->put("department",IPBM_HEADER);
$params->put("datefrom", date("F j, Y", strtotime($from_date_format)));
$params->put("dateto",  date("F j, Y", strtotime($to_date_format)));
$params->put("user", strtoupper($_SESSION['sess_user_name']));

$sql = "SELECT 
		  ce.encounter_nr,
		  cp.pid,
		  ce.admission_dt,
		  CONCAT(
		    IFNULL(name_last, ''),
		    ', ',
		    IFNULL(name_first, ''),
		    ' ',
		    IFNULL(name_middle, '')
		  ) AS patientname,
		  fn_get_complete_address2(ce.pid) AS address,
		  cp.date_birth,
		  IF(
		    fn_calculate_age (NOW(), cp.date_birth),
		    fn_get_age (
		      CAST(encounter_date AS DATE),
		      cp.date_birth
		    ),
		    age
		  ) AS age,
		  UPPER(cp.sex) AS p_sex,
		  cp.civil_status,
		  cd.name_formal,
		  ce.current_att_dr_nr,
		  ce.consulting_dr_nr,
		  addr_str,
		  cd.id,
		  ce.er_opd_diagnosis,
		  IFNULL(sep.civil_status,cp.civil_status) as status
		FROM
		  (
		    care_encounter AS ce 
		    INNER JOIN care_person AS cp 
		      ON ce.pid = cp.pid
		  )
		  LEFT JOIN seg_encounter_profile as sep 
		  ON ce.encounter_nr = sep.encounter_nr
		  LEFT JOIN care_department AS cd 
		    ON ce.current_dept_nr = cd.nr 
		  LEFT JOIN seg_barangays AS sb 
		    ON sb.brgy_nr = cp.brgy_nr 
		  LEFT JOIN seg_municity AS sm 
		    ON sm.mun_nr = cp.mun_nr 
		  LEFT JOIN seg_provinces AS sp 
		    ON sp.prov_nr = sm.prov_nr 
		  LEFT JOIN seg_regions AS sr 
		    ON sr.region_nr = sp.region_nr 
		WHERE (
		    ce.admission_dt >= ".$db->qstr($from_date_format)." 
		    AND CONCAT(
		      CAST(ce.admission_dt AS DATE),
		      ' 00:00:00'
		    ) < DATE_ADD(".$db->qstr($to_date_format).", INTERVAL 1 DAY)
		  ) 
		 AND $enc_type
		  AND ce.status NOT IN (
		    'deleted',
		    'hidden',
		    'inactive',
		    'void'
		  ) ".$addconditions."
		ORDER BY ce.admission_dt ASC";
		  /*ce.encounter_nr,
		  name_last,
		  name_first,
		  name_middle,*/
	 //var_dump($sql); die();

 $ok_cases = $db->Execute("TRUNCATE seg_report_admission_census"); 
	

	$rs = $db->Execute($sql);
	$num_rows = $db->GetOne("SELECT FOUND_ROWS()");
	$data = array();
	$i = 0;
	$count_new = 0;
	$count_old= 0;
	if (is_object($rs)) {
		if ($rs->RecordCount()) {
			while ($row = $rs->FetchRow()) {

				#Added by Matsuu
			
				$sql_no_encounter = "INSERT INTO seg_report_admission_census (encounter_nr,no_encounter) VALUES(
                            ".$db->qstr($row['encounter_nr']).",(SELECT  COUNT(*) AS no_encounters
                            FROM care_encounter ce
                            WHERE ( CONCAT(CAST(IFNULL(ce.admission_dt,ce.encounter_date) AS DATE),' 00:00:00') < DATE_ADD(".$db->qstr($row['admission_dt']).", INTERVAL 1 MINUTE)) AND ce.pid=".$db->qstr($row['pid'])."AND ce.status NOT IN ('deleted','hidden','inactive','void') ".$addconditions."ORDER BY ce.admission_dt ASC))"; 
    				     // var_dump($sql_no_encounter);exit();          
      				  $ok_cases = $db->Execute($sql_no_encounter);



      					$no_enc = $db->GetAll("SELECT no_encounter,encounter_nr from seg_report_admission_census WHERE encounter_nr=".$db->qstr($row['encounter_nr']));
      					foreach ($no_enc as $key => $info) {
  							  $enc = $info['encounter_nr'];
  							  $no_enc = $info['no_encounter'];
							}

							if($enc==$row['encounter_nr']){
								if($no_enc <= 1){
								$classication =	 "NEW";
								$count_new++;
								}
								else{
								$classication ="OLD";
								$count_old++;
							}
								}
      					// var_dump($no_enc['no_encounter']);
      					// var_dump($no_enc['encounter_nr']);
      				

				if ($row["consulting_dr_nr"])
					$docInfo = $pers_obj->getPersonellInfo($row["consulting_dr_nr"]);
				else
					$docInfo = $pers_obj->getPersonellInfo($row["consulting_dr_nr"]);

				$dr_middleInitial = "";
				if (trim($docInfo['name_middle']) != ""){
					$thisMI = split(" ",$docInfo['name_middle']);
					foreach($thisMI as $value){
						if (!trim($value) == "")
							$dr_middleInitial .= $value[0];
					}
					if (trim($dr_middleInitial) != "")
						$dr_middleInitial = " ".$dr_middleInitial.".";
				}
				if ($docInfo['name_last'] == '' && $docInfo['name_first'] == '' && $dr_middleInitial == '') {
					$name_doctor = "";
				}else{
					$name_doctor = $docInfo['name_last'].", ".$docInfo['name_first']." ".$dr_middleInitial;
				}

				$age ='';
				if (($row['date_birth']) && ($row['date_birth']!='0000-00-00') ){
					$bdate = date("m/d/Y",strtotime($row['date_birth']));
				}else{
					$bdate = 'unknown';
				}

				if (stristr($row['age'],'years')){
					$age = substr($row['age'],0,-5);
					$age = floor($age).' '.$age_label_yr;
				}elseif (stristr($row['age'],'year')){
					$age = substr($row['age'],0,-4);
					$age = floor($age).' '.$age_label_yr;
				}elseif (stristr($row['age'],'months')){
					$age = substr($row['age'],0,-6);
					$age = floor($age).' '.$age_label_m;
				}elseif (stristr($row['age'],'month')){
					$age = substr($row['age'],0,-5);
					$age = floor($age).' '.$age_label_m;
				}elseif (stristr($row['age'],'days')){
					$age = substr($row['age'],0,-4);

					if ($age>30){
						$age = $age/30;
						$label = $age_label_m;
					}else
						$label = $age_label_d;

					$age = floor($age).' '.$label;
				}elseif (stristr($row['age'],'day')){
					$age = substr($row['age'],0,-3);
					$age = floor($age).' d';
				}else{
					$age = floor($row['age']).' '.$age_label_yr;
				}
				$civil_status =  $_GET['dept_nr'] == IPBM_DEP ?  $row['status'] :  $row['civil_status']  ;

				if (trim($civil_status)=='married')
					$cstatus = "M";
				elseif (trim($civil_status)=='single')
					$cstatus = "S";
				elseif (trim($civil_status)=='child')
					$cstatus = "CH";
				elseif (trim($civil_status)=='divorced')
					$cstatus = "D";
				elseif (trim($civil_status)=='widowed')
					$cstatus = "W";
				elseif (trim($civil_status)=='separated')
					$cstatus = "SP";
				else
					$cstatus = "";

				$data[$i] = array(
					'num' => $i+1,
					'admission_no' => $row['encounter_nr'],
					'hrn' => $row['pid'],
					'admitted' => date('m/d/Y \ h:i A',strtotime($row["admission_dt"])),
					'patient_name' => utf8_decode(trim($row['patientname'])),
					'bday' => $bdate,
					'age' => $age,
					'gender' => $row['p_sex'],
					'status' => $cstatus,
					'address' => utf8_decode(trim($row['address'])),
					'department' => $row['id'],
					'adm_doctor' => utf8_decode(trim(strtoupper($name_doctor))),
					'adm_diagnosis' => trim($row['er_opd_diagnosis']),
					'classification' =>$classication,
					'old' =>$count_old,
					'new'=>$count_new
				);
			$i++;
			}
			// exit();
		}else{
			$data[0]['admission_no'] = "No data.";
		}
	}else{
		$data[0]['admission_no'] = "No data.";
	}
	$params->put("no_of_records", $num_rows); 

 ?>