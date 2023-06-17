<?php
/*
 * @author Gervie 03/02/2016
 */

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$monthAndYr = date("F Y", strtotime($from_date_format) );
$params->put('Month',$monthAndYr);
$params->put('Name_Social_Worker', strtoupper($_SESSION['sess_user_name']));
	
global $db;

$date_from = date('Y-m-d',$_GET['from_date']);
$date_to = date('Y-m-d',$_GET['to_date']);
$data = array();

$sql = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.coordination AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$sql2 = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.outgoing AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$sql3 = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.case_con AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$sql4 = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.leading_reasons AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$sql5 = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.discharge_services AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$sql6 = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.documentation AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$sql7 = "SELECT 
  			'1-cms' AS group_id,
  			DAY(ssp.date_interview) AS aDay,
  			scm.incoming AS services,
  			COUNT(*) AS tcount
		FROM
			seg_social_case_management scm 
  		INNER JOIN seg_socserv_patient ssp 
    		ON scm.`encounter_nr` = ssp.`encounter_nr` 
    		AND scm.`pid` = ssp.`pid` 
		WHERE ssp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND ssp.`is_deleted` = 0
		GROUP BY aDay, ssp.`encounter_nr`";

$rs = $db->Execute($sql);
$services = array();
if (is_object($rs)) {
    if($rs->RecordCount()){
        while ($row = $rs->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != ''){
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		$service_name = $db->GetOne("SELECT CASE 
												 		WHEN sc.desc LIKE 'Other%' THEN CONCAT('1.7.5 ', sc.desc)
												  		WHEN sc.desc LIKE 'Management%' THEN CONCAT('1.7.6 ', sc.desc)
												  		WHEN sc.desc LIKE 'Chaplain%' THEN CONCAT('1.7.7 ', sc.desc)
												  		ELSE CONCAT('1.7.', sc.id, ' ', sc.desc)
												  		END 
										  		  FROM seg_social_coordination sc 
										  		  WHERE sc.id = {$exploded_services[$i]}");

	        		$service_order = $db->GetOne("SELECT CASE 
														   WHEN sc.desc LIKE 'Other%' THEN '07A5'
														   WHEN sc.desc LIKE 'Management%' THEN '07A6'
														   WHEN sc.desc LIKE 'Chaplain%' THEN '07A7'
														   ELSE CONCAT('07A', sc.id) 
														 END
										  		  FROM seg_social_coordination sc 
										  		  WHERE sc.id = {$exploded_services[$i]}");

	        		$services[] = array(
		            	'group_id' => $row['group_id'],
		                'aDay' => intval($row['aDay']),
		        		'services' => ucwords($service_name),
		        		'order_id' => $service_order,
		        		'tcount' => (int)$row['tcount'],
	            	);
	        	}
	        }
        }
    }
} else {
    $services[0]['aDay'] = null;
}

$rs2 = $db->Execute($sql2);
$services2 = array();
if (is_object($rs2)) {
    if($rs2->RecordCount()){
        while ($row = $rs2->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != '') {
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		$service_name = $db->GetOne("SELECT CASE 
													 	  WHEN crd.desc LIKE 'Med%' THEN 'a. Medical Assistance'
													 	  WHEN crd.desc LIKE 'Disc%' THEN 'b. Discount on Procedure/Hospital Expenses'
													 	  WHEN crd.desc LIKE 'Transpo%' THEN 'c. Transportation Fare'
													 	  WHEN crd.desc LIKE 'Institution%' THEN 'd. Institutional Placement'
													 	  WHEN crd.desc LIKE 'Temporary%' THEN 'e. Temporary Shelter'
													 	  WHEN crd.desc LIKE 'Funeral%' THEN 'f. Funeral Assistance/Paupers Burial'
													 	END 
										  		  FROM seg_social_concrete_referral_details crd
				 	   							  WHERE crd.id = {$exploded_services[$i]} 
				 	   							  AND crd.desc NOT LIKE 'Food%'");

	        		$service_order = $db->GetOne("SELECT CASE 
													 	   WHEN crd.desc LIKE 'Med%' THEN '11A1'
													 	   WHEN crd.desc LIKE 'Disc%' THEN '11A2'
													 	   WHEN crd.desc LIKE 'Transpo%' THEN '11A3'
													 	   WHEN crd.desc LIKE 'Institution%' THEN '11A4'
													 	   WHEN crd.desc LIKE 'Temporary%' THEN '11A5'
													 	   WHEN crd.desc LIKE 'Funeral%' THEN '11A6'
													 	 END
										  		  FROM seg_social_concrete_referral_details crd
				 	   							  WHERE crd.id = {$exploded_services[$i]} 
				 	   							  AND crd.desc NOT LIKE 'Food%'");

	        		$services2[] = array(
		            	'group_id' => $row['group_id'],
		                'aDay' => intval($row['aDay']),
		        		'services' => ucwords($service_name),
		        		'order_id' => $service_order,
		        		'tcount' => (int)$row['tcount'],
	            	);
	        	}
	        }
        }
    }
} else {
    $services2[0]['aDay'] = null;
}

$rs3 = $db->Execute($sql3);
$services3 = array();
if (is_object($rs3)) {
    if($rs3->RecordCount()){
        while ($row = $rs3->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != ''){
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		$service_name = $db->GetOne("SELECT CONCAT('1.22.', scc.id, ' ', scc.desc) 
										  		  FROM seg_social_case_con scc
				 	   							  WHERE scc.id = {$exploded_services[$i]}");

	        		$service_order = $db->GetOne("SELECT CONCAT('22A', scc.id)
										  		  FROM seg_social_case_con scc
				 	   							  WHERE scc.id = {$exploded_services[$i]}");

	        		$services3[] = array(
		            	'group_id' => $row['group_id'],
		                'aDay' => intval($row['aDay']),
		        		'services' => ucwords($service_name),
		        		'order_id' => $service_order,
		        		'tcount' => (int)$row['tcount'],
	            	);
	        	}
	        }
        }
    }
} else {
    $services3[0]['aDay'] = null;
}

$rs4 = $db->Execute($sql4);
$services4 = array();
if (is_object($rs4)) {
    if($rs4->RecordCount()){
        while ($row = $rs4->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != ''){
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		if($exploded_services[$i] == '10'){
		        		$service_name = $db->GetOne("SELECT CONCAT('1.25.1 ', spc.desc) 
											  		  FROM seg_social_psycho_counselling_details spc
					 	   							  WHERE spc.id = {$exploded_services[$i]}");

		        		$services4[] = array(
		            	'group_id' => $row['group_id'],
		                'aDay' => intval($row['aDay']),
		        		'services' => ucwords($service_name),
		        		'order_id' => '25A1',
		        		'tcount' => (int)$row['tcount'],
	            	);
		        	}
	        	}
	        }
        }
    }
} else {
    $services4[0]['aDay'] = null;
}

$rs5 = $db->Execute($sql5);
$services5 = array();
if (is_object($rs5)) {
    if($rs5->RecordCount()){
        while ($row = $rs5->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != ''){
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		$service_name = $db->GetOne("SELECT CASE
        												WHEN spc.desc LIKE 'Fascilitating%' THEN '1.25.2 Facilitation of Discharge'
        												WHEN spc.desc LIKE 'Pre-Termination%' THEN '1.25.4 Pre-Termination Counselling'
        												WHEN spc.desc LIKE 'Home Conduction%' THEN CONCAT('1.25.5 ', spc.desc)
        												END
										  		  FROM seg_social_psycho_counselling_details spc
				 	   							  WHERE spc.id = {$exploded_services[$i]}");

	        		$service_order = $db->GetOne("SELECT CASE
        												WHEN spc.desc LIKE 'Fascilitating%' THEN '25A2'
        												WHEN spc.desc LIKE 'Pre-Termination%' THEN '25A4'
        												WHEN spc.desc LIKE 'Home Conduction%' THEN '25A5'
        												END
										  		  FROM seg_social_psycho_counselling_details spc
				 	   							  WHERE spc.id = {$exploded_services[$i]}");

	        		$services5[] = array(
		            	'group_id' => $row['group_id'],
		                'aDay' => intval($row['aDay']),
		        		'services' => ucwords($service_name),
		        		'order_id' => $service_order,
		        		'tcount' => (int)$row['tcount'],
	            	);
	        	}
	        }
        }
    }
} else {
    $services5[0]['aDay'] = null;
}

$rs6 = $db->Execute($sql6);
$services6 = array();
if (is_object($rs6)) {
    if($rs6->RecordCount()){
        while ($row = $rs6->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != ''){
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		if($exploded_services[$i] <= 5){
		        		$service_name = $db->GetOne("SELECT CASE
											 	     	  	WHEN ssd.desc LIKE 'MSWD%' THEN '1.26.1 Profile'
											 	     	  	WHEN ssd.desc LIKE 'Progress%' THEN '1.26.2 Progress Note'
											 	     	  	WHEN ssd.desc LIKE 'Group Work%' THEN '1.26.3 Group Work Recording'
											 	     	  	WHEN ssd.desc LIKE 'Social Case Study%' THEN '1.26.4 Social Case Study Report'
											 	     	  	WHEN ssd.desc LIKE 'Home Visit%' THEN '1.26.6 Home Visit Report'
										 	     	    	END
											  		  FROM seg_social_documentation ssd
					 	   							  WHERE ssd.id = {$exploded_services[$i]}");

		        		$service_order = $db->GetOne("SELECT CASE
											 	     	  	 WHEN ssd.desc LIKE 'MSWD%' THEN '26A1'
											 	     	  	 WHEN ssd.desc LIKE 'Progress%' THEN '26A2'
											 	     	  	 WHEN ssd.desc LIKE 'Group Work%' THEN '26A3'
											 	     	  	 WHEN ssd.desc LIKE 'Social Case Study%' THEN '26A4'
											 	     	  	 WHEN ssd.desc LIKE 'Home Visit%' THEN '26A6'
										 	     	    	 END
											  		  FROM seg_social_documentation ssd
					 	   							  WHERE ssd.id = {$exploded_services[$i]}");

		        		$services6[] = array(
			            	'group_id' => $row['group_id'],
			                'aDay' => intval($row['aDay']),
			        		'services' => ucwords($service_name),
			        		'order_id' => $service_order,
			        		'tcount' => (int)$row['tcount'],
		            	);
		        	}
	        	}
	        }
        }
    }
} else {
    $services6[0]['aDay'] = null;
}

$rs7 = $db->Execute($sql7);
$services7 = array();
if (is_object($rs7)) {
    if($rs7->RecordCount()){
        while ($row = $rs7->FetchRow()) {
        	$exploded_services = explode(',', $row['services']);
        	$cnt_explode = sizeof($exploded_services);

        	if($exploded_services[0] != ''){
	        	for($i = 0; $i < $cnt_explode; $i++){
	        		$services7[] = array(
		            	'group_id' => $row['group_id'],
		                'aDay' => intval($row['aDay']),
		        		'services' => ucwords('1.11.2 Incoming Referrals'),
		        		'order_id' => '11B',
		        		'tcount' => (int)$row['tcount'],
	            	);
	        	}
	        }
        }
    }
} else {
    $services7[0]['aDay'] = null;
}

$all_services = $db->GetAll("SELECT 
								'1-cms' AS group_id,
								DAY(selected_date) AS aDay,
								CASE 
								  WHEN sc.desc LIKE 'Other%' THEN CONCAT('1.7.5 ', sc.desc)
								  WHEN sc.desc LIKE 'Management%' THEN CONCAT('1.7.6 ', sc.desc)
								  WHEN sc.desc LIKE 'Chaplain%' THEN CONCAT('1.7.7 ', sc.desc)
								  ELSE CONCAT('1.7.', sc.id, ' ', sc.desc) 
								END AS services,
								CASE 
								  WHEN sc.desc LIKE 'Other%' THEN '07A5'
								  WHEN sc.desc LIKE 'Management%' THEN '07A6'
								  WHEN sc.desc LIKE 'Chaplain%' THEN '07A7'
								  ELSE CONCAT('07A', sc.id) 
								END AS order_id,
								0 AS tcount 
							 FROM 
					 		   (SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
							   (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
							   (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
							   (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
							   (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
							   (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v,
							   seg_social_coordination sc
							  WHERE selected_date BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
							  GROUP BY aDay, order_id

						 UNION ALL
						 
						 SELECT 
						 	'1-cms' AS group_id,
						 	1 AS aDay,
						 	CASE 
						 	  WHEN crd.desc LIKE 'Med%' THEN 'a. Medical Assistance'
						 	  WHEN crd.desc LIKE 'Disc%' THEN 'b. Discount on Procedure/Hospital Expenses'
						 	  WHEN crd.desc LIKE 'Transpo%' THEN 'c. Transportation Fare'
						 	  WHEN crd.desc LIKE 'Institution%' THEN 'd. Institutional Placement'
						 	  WHEN crd.desc LIKE 'Temporary%' THEN 'e. Temporary Shelter'
						 	  WHEN crd.desc LIKE 'Funeral%' THEN 'f. Funeral Assistance/Paupers Burial'
						 	END AS services,
						 	CASE 
						 	  WHEN crd.desc LIKE 'Med%' THEN '11A1'
						 	  WHEN crd.desc LIKE 'Disc%' THEN '11A2'
						 	  WHEN crd.desc LIKE 'Transpo%' THEN '11A3'
						 	  WHEN crd.desc LIKE 'Institution%' THEN '11A4'
						 	  WHEN crd.desc LIKE 'Temporary%' THEN '11A5'
						 	  WHEN crd.desc LIKE 'Funeral%' THEN '11A6'
						 	END AS order_id,
						 	0 AS tcount
					 	 FROM seg_social_concrete_referral_details crd
					 	   WHERE crd.concrete_id = 2 AND crd.desc NOT LIKE 'Food%'

				 	     UNION ALL

				 	     SELECT
				 	     	'1-cms' AS group_id,
				 	     	1 AS aDay,
				 	     	CONCAT('1.22.', scc.id, ' ', scc.desc) AS services,
				 	     	CONCAT('22A', scc.id) AS order_id,
				 	     	0 AS tcount
			 	     	 FROM seg_social_case_con scc

			 	     	 UNION ALL

				 	     SELECT
				 	     	'1-cms' AS group_id,
				 	     	1 AS aDay,
				 	     	CONCAT('1.25.1 ', spc.desc) AS services,
				 	     	'25A1' AS order_id,
				 	     	0 AS tcount
			 	     	 FROM seg_social_psycho_counselling_details spc
			 	     	 WHERE spc.psycho_id = '1' AND spc.desc LIKE 'Discharged%'

			 	     	 UNION ALL

				 	     SELECT
				 	     	'1-cms' AS group_id,
				 	     	1 AS aDay,
				 	     	'1.25.2 Facilitation of Discharge' AS services,
				 	     	'25A2' AS order_id,
				 	     	0 AS tcount
			 	     	 FROM seg_social_psycho_counselling_details spc
			 	     	 WHERE spc.psycho_id = '3' AND spc.desc LIKE 'Fascilitating%'

			 	     	 UNION ALL

				 	     SELECT
				 	     	'1-cms' AS group_id,
				 	     	1 AS aDay,
				 	     	'1.25.4 Pre-Termination Counselling' AS services,
				 	     	'25A4' AS order_id,
				 	     	0 AS tcount
			 	     	 FROM seg_social_psycho_counselling_details spc
			 	     	 WHERE spc.psycho_id = '3' AND spc.desc LIKE 'Pre-Termination%' 

			 	     	 UNION ALL

				 	     SELECT
				 	     	'1-cms' AS group_id,
				 	     	1 AS aDay,
				 	     	CONCAT('1.25.5 ', spc.desc) AS services,
				 	     	'25A5' AS order_id,
				 	     	0 AS tcount
			 	     	 FROM seg_social_psycho_counselling_details spc
			 	     	 WHERE spc.psycho_id = '3' AND spc.desc LIKE 'Home Conduction%'

			 	     	 UNION ALL

				 	     SELECT
				 	     	'1-cms' AS group_id,
				 	     	1 AS aDay,
				 	     	CASE
				 	     	  WHEN ssd.desc LIKE 'MSWD%' THEN '1.26.1 Profile'
				 	     	  WHEN ssd.desc LIKE 'Progress%' THEN '1.26.2 Progress Note'
				 	     	  WHEN ssd.desc LIKE 'Group Work%' THEN '1.26.3 Group Work Recording'
				 	     	  WHEN ssd.desc LIKE 'Social Case Study%' THEN '1.26.4 Social Case Study Report'
				 	     	  WHEN ssd.desc LIKE 'Home Visit%' THEN '1.26.6 Home Visit Report'
			 	     	    END AS services,
				 	     	CASE
				 	     	  WHEN ssd.desc LIKE 'MSWD%' THEN '26A1'
				 	     	  WHEN ssd.desc LIKE 'Progress%' THEN '26A2'
				 	     	  WHEN ssd.desc LIKE 'Group Work%' THEN '26A3'
				 	     	  WHEN ssd.desc LIKE 'Social Case Study%' THEN '26A4'
				 	     	  WHEN ssd.desc LIKE 'Home Visit%' THEN '26A6'
			 	     	    END AS order_id,
				 	     	0 AS tcount
			 	     	 FROM seg_social_documentation ssd 
			 	     	 WHERE ssd.id <= 5 ");

for($i = 0; $i < count($all_services); $i++){
	$all_services[$i]['aDay'] = intval($all_services[$i]['aDay']);
	$all_services[$i]['services'] = ucwords($all_services[$i]['services']);
    $all_services[$i]['tcount'] = intval($all_services[$i]['tcount']);
}

$label_services[0] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1. Case Management Services',
						'order_id' => '00B',
						'tcount' => 0
					 );
$label_services[1] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.1 Pre admission Counseling',
						'order_id' => '01',
						'tcount' => 0
					 );
$label_services[2] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.2 Intake Interview',
						'order_id' => '02',
						'tcount' => 0
					 );
$label_services[3] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.3 Collateral Interview',
						'order_id' => '03',
						'tcount' => 0
					 );
$label_services[4] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.4 Orientation of Policies',
						'order_id' => '04',
						'tcount' => 0
					 );
$label_services[5] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.5 Psychological Assessment',
						'order_id' => '05',
						'tcount' => 0
					 );
$label_services[6] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.6 Psychosocial Counseling',
						'order_id' => '06',
						'tcount' => 0
					 );
$label_services[7] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.7 Coordination w/ multidisciplinary team',
						'order_id' => '07',
						'tcount' => 0
					 );
$label_services[8] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.9 Crisis Intervention',
						'order_id' => '09',
						'tcount' => 0
					 );
$label_services[9] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.10 Concrete Services',
						'order_id' => '10',
						'tcount' => 0
					 );
$label_services[10] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.10.1 Facilitation/Provision as cost medicines per procedure',
						'order_id' => '10A1',
						'tcount' => 0
					 );
$label_services[11] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.10.2 Transportation Assistance',
						'order_id' => '10A2',
						'tcount' => 0
					 );
$label_services[12] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.10.3 Material Assistance (Food, Clothing, etc.)',
						'order_id' => '10A3',
						'tcount' => 0
					 );
$label_services[13] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.10.4 Financial Assistance (with in MSS Resource)',
						'order_id' => '10A4',
						'tcount' => 0
					 );
$label_services[14] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.11 Facilitating Referrals:',
						'order_id' => '11',
						'tcount' => 0
					 );
$label_services[15] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.11.1 Outgoing Referrals',
						'order_id' => '11A',
						'tcount' => 0
					 );
$label_services[16] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'G. Insurance Benifits',
						'order_id' => '11A7',
						'tcount' => 0
					 );
$label_services[17] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'H. Others Specify',
						'order_id' => '11A8',
						'tcount' => 0
					 );
$label_services[18] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.11.2 Incoming Referrals',
						'order_id' => '11B',
						'tcount' => 0
					 );
$label_services[19] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.12 Ward Rounds',
						'order_id' => '12',
						'tcount' => 0
					 );
$label_services[20] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'A. Team',
						'order_id' => '12A',
						'tcount' => 0
					 );
$label_services[21] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'B. Individual',
						'order_id' => '12B',
						'tcount' => 0
					 );
$label_services[22] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.13 Home Visitation',
						'order_id' => '13',
						'tcount' => 0
					 );
$label_services[23] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.14 Advocacy Role for Patients',
						'order_id' => '14',
						'tcount' => 0
					 );
$label_services[24] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.15 Family Life Education',
						'order_id' => '15',
						'tcount' => 0
					 );
$label_services[25] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.16 Therapeutic Social Work Services',
						'order_id' => '16',
						'tcount' => 0
					 );
$label_services[26] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.16.1 Protective Services',
						'order_id' => '16A',
						'tcount' => 0
					 );
$label_services[27] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.16.2 Grief Work',
						'order_id' => '16B',
						'tcount' => 0
					 );
$label_services[28] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.16.3 Behavioral Modification',
						'order_id' => '16C',
						'tcount' => 0
					 );
$label_services[29] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.17 Networking(meeting with other institutions/Group of organization)',
						'order_id' => '17',
						'tcount' => 0
					 );
$label_services[30] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.18 Education of Community and Influetial',
						'order_id' => '18',
						'tcount' => 0
					 );
$label_services[31] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.20 Coordination with Mass Media',
						'order_id' => '20',
						'tcount' => 0
					 );
$label_services[32] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.21 Consultative/Advisory Services',
						'order_id' => '21',
						'tcount' => 0
					 );
$label_services[33] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.21.1 Documentation',
						'order_id' => '21A',
						'tcount' => 0
					 );
$label_services[34] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'A. Profile',
						'order_id' => '21A1',
						'tcount' => 0
					 );
$label_services[35] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'B. Progress Note',
						'order_id' => '21A2',
						'tcount' => 0
					 );
$label_services[36] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'C. Group Work Recording',
						'order_id' => '21A3',
						'tcount' => 0
					 );
$label_services[37] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'D. Social Case Study Report',
						'order_id' => '21A4',
						'tcount' => 0
					 );
$label_services[38] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '&nbsp;&nbsp;&nbsp; Social Case Summary',
						'order_id' => '21A5',
						'tcount' => 0
					 );
$label_services[39] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'E. Home Visits Report',
						'order_id' => '21A6',
						'tcount' => 0
					 );
$label_services[40] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.22 Attendance to Case Conference',
						'order_id' => '22',
						'tcount' => 0
					 );
$label_services[41] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.23 Clinical Commitee Meetings',
						'order_id' => '23',
						'tcount' => 0
					 );
$label_services[42] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.24 Attendance to Clinical Commitees',
						'order_id' => '24',
						'tcount' => 0
					 );
$label_services[43] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.25 Discharge Services',
						'order_id' => '25',
						'tcount' => 0
					 );
$label_services[44] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.25.3 Post Discharge Services',
						'order_id' => '25A3',
						'tcount' => 0
					 );
$label_services[45] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.26 Documentation',
						'order_id' => '26',
						'tcount' => 0
					 );
$label_services[46] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.26.5 Social Case Summary',
						'order_id' => '26A5',
						'tcount' => 0
					 );
$label_services[47] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27 Follow up Services',
						'order_id' => '27',
						'tcount' => 0
					 );
$label_services[48] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.1 Home Visits',
						'order_id' => '27A1',
						'tcount' => 0
					 );
$label_services[49] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.2 Letter Sent',
						'order_id' => '27A2',
						'tcount' => 0
					 );
$label_services[50] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.3 Telegram Sent',
						'order_id' => '27A3',
						'tcount' => 0
					 );
$label_services[51] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.4 Contact Relatives by Telephone',
						'order_id' => '27A4',
						'tcount' => 0
					 );
$label_services[52] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.5 Contact Relatives through Mass Media',
						'order_id' => '27A5',
						'tcount' => 0
					 );
$label_services[53] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.6 Follow up of the Treatment Plan',
						'order_id' => '27A6',
						'tcount' => 0
					 );
$label_services[54] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.27.7 Follow up of the Rehabilitation Plans',
						'order_id' => '27A7',
						'tcount' => 0
					 );
$label_services[55] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.28 Rehabilitation Services',
						'order_id' => '28',
						'tcount' => 0
					 );
$label_services[56] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.28.1 Skills Training',
						'order_id' => '28A1',
						'tcount' => 0
					 );
$label_services[57] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.28.2 Job Placement',
						'order_id' => '28A2',
						'tcount' => 0
					 );
$label_services[58] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.28.3 Capital Assistance',
						'order_id' => '28A3',
						'tcount' => 0
					 );
$label_services[59] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => '1.29 Community Outreach',
						'order_id' => '29',
						'tcount' => 0
					 );
$label_services[60] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '10 Leading Reasons for Psycho-social Counseling',
						'order_id' => '00',
						'tcount' => 0
					 );
$label_services[61] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '1. Anxiety on Health Cost',
						'order_id' => '01',
						'tcount' => 0
					 );
$label_services[62] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '2. Stress of the Family',
						'order_id' => '02',
						'tcount' => 0
					 );
$label_services[63] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '3. Emotional Problem',
						'order_id' => '03',
						'tcount' => 0
					 );
$label_services[64] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '4. Adjustment Problem',
						'order_id' => '04',
						'tcount' => 0
					 );
$label_services[65] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '5. Marital Problem',
						'order_id' => '05',
						'tcount' => 0
					 );
$label_services[66] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '6. Neglected Children',
						'order_id' => '06',
						'tcount' => 0
					 );
$label_services[67] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '7. Refusal of Patient for Treatment',
						'order_id' => '07',
						'tcount' => 0
					 );
$label_services[68] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '8. Unbecoming Attitude',
						'order_id' => '08',
						'tcount' => 0
					 );
$label_services[69] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '9. Refusal of Patient to take home',
						'order_id' => '09',
						'tcount' => 0
					 );
$label_services[70] = array(
						'group_id' => '2-leading',
						'aDay' => 1,
						'services' => '10. Sexually Abused',
						'order_id' => '10',
						'tcount' => 0
					 );
$label_services[71] = array(
						'group_id' => '1-cms',
						'aDay' => 1,
						'services' => 'IV. SERVICES',
						'order_id' => '00A',
						'tcount' => 0
					 );


$services = array_merge($services, $all_services);
$data = array_merge($data, $label_services);
$data = array_merge($data, $services);
$data = array_merge($data, $services2);
$data = array_merge($data, $services3);
$data = array_merge($data, $services4);
$data = array_merge($data, $services5);
$data = array_merge($data, $services6);
$data = array_merge($data, $services7);