<?php
   /**
	* @author : syboy 09/14/2015
	* Report for Referral
    */ 

   error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
   require_once('./roots.php');
   require_once($root_path.'include/inc_environment_global.php');
   
   include('parameters.php');

   $from = date('Y-m-d',$_GET['from_date']);
   $to = date('Y-m-d',$_GET['to_date']);

   if(empty($patient_type) && $_GET['dept_nr']==IPBM_DEP){
   	$patient_type = "13,14";
   	$age_label_yr= "y.o.";
	$age_label_m = "m.o.";
   }
   elseif (empty($patient_type) && $_GET['dept_nr']!=IPBM_DEP) {
   	    $patient_type = '1,2,3,4,6';
   }
   // var_dump($patient_type);exit();
   # Title Report Begins Here
   $params->put("rotp", mb_strtoupper($hosp_country));
   $params->put("doh", mb_strtoupper($hosp_agency));
   $params->put("spmc_title", "SOUTHERN PHILIPPINES MEDICAL CENTER");
   $params->put("address", mb_strtoupper($hosp_addr1));
   $params->put("title_report", "REPORT OF REFERRAL");
   $params->put("date_span", $from.' to '.$to);

   global $db;
   $sql = "SELECT 
		  ce.encounter_date AS date_admitted,
		  UPPER(
		    fn_get_person_lastname_first (cp.pid)
		  ) AS fullname,
		  UPPER(fn_get_age (ce.encounter_date, cp.date_birth)) AS age,
		  cp.`sex`,
		  UPPER(fn_get_complete_address (cp.pid)) AS fullAddress,
		  srf.`referral` AS referral,
		  srr.`reason` AS reason,
		  ce.`referrer_dr`,
		  ce.`reason_dr`,
		  ce.referrer_dr_other AS referrer_dr_other,
		  ce.reason_dr_other AS reason_dr_other
		FROM
		  care_person cp 
		  INNER JOIN care_encounter ce 
		    ON ce.`pid` = cp.`pid` 
		  INNER JOIN seg_referral_from srf 
		    ON srf.`id` = ce.`referrer_dr` 
		  INNER JOIN seg_referral_reason srr 
		    ON srr.`id` = ce.`reason_dr` 
		WHERE ce.encounter_type IN ($patient_type)
		  AND STR_TO_DATE(ce.encounter_date, '%Y-%m-%d') >= STR_TO_DATE('$from', '%Y-%m-%d') 
		  AND STR_TO_DATE(ce.encounter_date, '%Y-%m-%d') <= STR_TO_DATE('$to', '%Y-%m-%d')";

	$rs = $db->Execute($sql);

	$i = 0;
	$data = array();
	if ($rs) {
		if($rs->RecordCount()){
			while ($row = $rs->FetchRow()) {

				

				$date_format = date('m/d/Y \ h:i A',strtotime($row["date_admitted"]));

				if ($row['referrer_dr'] == 601) {
					$referral = $row['referrer_dr_other'];
				}else{
					$referral = $row['referral'];
				}

				if ($row['reason_dr'] == 142) {
					$reason = $row['reason_dr_other'];
				}else{
					$reason = $row['reason'];
				}

				
				if($_GET['dept_nr']==IPBM_DEP){
					if (stristr($row['age'],'years')){
					$age = substr($row['age'],0,-5);
					$age = floor($age)." ".$age_label_yr;
					}elseif (stristr($row['age'],'year')){
						$age = substr($row['age'],0,-4);
						$age = floor($age)." ".$age_label_yr;
					}elseif (stristr($row['age'],'months')){
						$age = substr($row['age'],0,-6);
						$age = floor($age)." ".$age_label_m;
					}elseif (stristr($row['age'],'month')){
						$age = substr($row['age'],0,-5);
						$age = floor($age)." ".$age_label_m;
					}elseif (stristr($row['age'],'days')){
						$age = substr($row['age'],0,-4);

						if ($age>30){
							$age = $age/30;
							$label =$age_label_m;
						}else
							$label = 'd.o.';

						$age = floor($age).' '.$label;
					}elseif (stristr($row['age'],'day')){
						$age = substr($row['age'],0,-3);
						$age = floor($age).' d';
					}else{
						$age = floor($row['age'])."".$age_label_yr;
					}
					$gender = strtoupper($row['sex']);
				}else{
					if ($row['age'] == null) {
					$age = '0 DAY OLD';
					}else{
					$age = $row['age']." OLD";
					}

					if ($row['sex'] == "m") {
					$gender = "MALE";
					}else{
						$gender = "FEMALE";
					}
				}

				

				$data[$i] = array('date_admitted' => $date_format,
								'patient_name' => utf8_decode(trim($row['fullname'])),
								'age' => $age,
								'sex' => $gender,
								'address_patient' => utf8_decode(trim($row['fullAddress'])),
								'rf' => $referral,
								'rr' => $reason
							);
				$i++;
			}
		}else{
			$data[0] = array('date_admitted' => 'No Data');
		}
	}else{
		$data[0] = array('date_admitted' => 'No Data');
	}

$total = $rs->RecordCount();
$params->put("total_cases", " {$total}");