<?php 
/*
 * Author : gelie
 * Date : 10/31/2015
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

$que = "SELECT 
			'1-refer' AS group_id,
			DAY(ssp.date_interview) AS aDay, 
			CASE 
				WHEN ref.source_nr IN ('GC','GH') THEN 'Government Hospital'
				WHEN ref.source_nr IN ('PC','PH') THEN 'Private Hospitals/Clinics'
				WHEN ref.source_nr = 'HCT' THEN 'Health Team Care'
				WHEN ref.source_nr IN ('NGO','PWA') THEN 'NGOs/Private Welfare Agencies'
				WHEN ref.source_nr IN ('DSWD','DOH') THEN 'Government Agencies(DSWD,DOH)'
				WHEN ref.source_nr = 'OT' THEN 'Others (Employees, Former Patients, Colleagues, Friends)'
				ELSE ref.source
			END AS discountid,
			CASE
				WHEN ref.source_nr IN ('GC','GH') THEN '2'
				WHEN ref.source_nr IN ('PC','PH') THEN '3'
				WHEN ref.source_nr = 'P' THEN '4'
				WHEN ref.source_nr = 'M' THEN '5'
				WHEN ref.source_nr = 'HCT' THEN '6'
				WHEN ref.source_nr IN ('NGO','PWA') THEN '7'
				WHEN ref.source_nr IN ('DSWD','DOH') THEN '8'
				WHEN ref.source_nr = 'WLK' THEN '9'
				WHEN ref.source_nr = 'OT' THEN '9A'
				ELSE '9Z'
			END AS order_id,
			COUNT(*) AS tcount
		FROM seg_social_source_referral ref
		INNER JOIN seg_socserv_patient ssp ON  ssp.source_referral = ref.source_nr
		INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
		INNER JOIN care_person cp ON cp.pid = ce.pid
		WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)}) 
			AND ssp.is_deleted = 0
		GROUP BY aDay, discountid

		UNION ALL 

		SELECT
			'2-phic' AS group_id,
			DAY(scg.grant_dte) AS aDay, 
			IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), 'Others: SMPMC/DOH Personnel/Dep.', 
				CASE dis.discountid
			    	WHEN 'SC' THEN 'Senior Citizen'
			    	WHEN 'Brgy' THEN 'Bgry. Officials'
			    	WHEN 'Indi' THEN 'Indigenous'
			    	ELSE dis.`discountid`
				END
			) AS discountid,
			IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), '9D', 
			CASE dis.discountid
			    WHEN 'A' THEN '1'
			    WHEN 'B' THEN '2'
			    WHEN 'C1' THEN '3'
			    WHEN 'C2' THEN '4'
			    WHEN 'C3' THEN '5'
			    WHEN 'D' THEN '6'
			    WHEN 'SC' THEN '8'
			    WHEN 'Brgy' THEN '9'
			    WHEN 'PWD' THEN '9A'
			    WHEN 'BHW' THEN '9B'
			    WHEN 'Indi' THEN '9C'
			    ELSE '9E'
			END 
			 ) AS order_id,
			COUNT(*) AS tcount
		FROM
			seg_discount dis
		INNER JOIN seg_charity_grants AS scg ON scg.discountid = dis.discountid
		INNER JOIN care_encounter ce ON ce.encounter_nr = scg.encounter_nr
		INNER JOIN seg_encounter_insurance ins ON ins.`encounter_nr` = ce.`encounter_nr` AND ins.`hcare_id` = 18
		WHERE 
			DATE(scg.grant_dte) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND scg.status IN ('valid', 'expired')
		GROUP BY aDay, dis.`discountid`

		UNION ALL

		(SELECT 
			group_id, aDay, discountid, order_id, SUM(tcount) AS tcount
		FROM(
			SELECT
			'3-non_phic' AS group_id,
			DAY(np.grant_dte) AS aDay, 
			IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), 'Others: SMPMC/DOH Personnel/Dep.', 
				CASE dis.discountid
			    	WHEN 'SC' THEN 'Senior Citizen'
			    	WHEN 'Brgy' THEN 'Bgry. Officials'
			    	WHEN 'Indi' THEN 'Indigenous'
			    	ELSE dis.`discountid`
				END
			) AS discountid,
			IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), '9D', 
			CASE dis.discountid
			    WHEN 'A' THEN '1'
			    WHEN 'B' THEN '2'
			    WHEN 'C1' THEN '3'
			    WHEN 'C2' THEN '4'
			    WHEN 'C3' THEN '5'
			    WHEN 'D' THEN '6'
			    WHEN 'SC' THEN '8'
			    WHEN 'Brgy' THEN '9'
			    WHEN 'PWD' THEN '9A'
			    WHEN 'BHW' THEN '9B'
			    WHEN 'Indi' THEN '9C'
			    ELSE '9E'
			END 
			 ) AS order_id,
			COUNT(*) AS tcount
		FROM
			(SELECT * FROM seg_charity_grants scg 
  				WHERE DATE(scg.grant_dte) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
    			AND scg.status IN ('valid', 'expired') 
  				GROUP BY scg.encounter_nr DESC
			) np
  		INNER JOIN seg_discount dis 
    		ON np.discountid = dis.discountid 
  		INNER JOIN care_encounter ce 
    		ON ce.encounter_nr = np.encounter_nr 
  		LEFT JOIN seg_encounter_insurance ins 
    		ON ins.encounter_nr = ce.encounter_nr 
    		AND ins.hcare_id != 18 
		GROUP BY aDay, dis.discountid
		
		UNION ALL
		
		SELECT
			'3-non_phic' AS group_id,
			DAY(np2.grant_dte) AS aDay, 
			IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), 'Others: SMPMC/DOH Personnel/Dep.', 
				CASE dis.discountid
			    	WHEN 'SC' THEN 'Senior Citizen'
			    	WHEN 'Brgy' THEN 'Bgry. Officials'
			    	WHEN 'Indi' THEN 'Indigenous'
			    	ELSE dis.`discountid`
				END
			) AS discountid,
			IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), '9D', 
			CASE dis.discountid
			    WHEN 'A' THEN '1'
			    WHEN 'B' THEN '2'
			    WHEN 'C1' THEN '3'
			    WHEN 'C2' THEN '4'
			    WHEN 'C3' THEN '5'
			    WHEN 'D' THEN '6'
			    WHEN 'SC' THEN '8'
			    WHEN 'Brgy' THEN '9'
			    WHEN 'PWD' THEN '9A'
			    WHEN 'BHW' THEN '9B'
			    WHEN 'Indi' THEN '9C'
			    ELSE '9E'
			END 
			 ) AS order_id,
			COUNT(*) AS tcount
		FROM
			(SELECT * FROM seg_charity_grants_pid scp 
  				WHERE DATE(scp.grant_dte) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)}) 
    			AND scp.status IN ('valid', 'expired') 
    			AND scp.`grant_dte` NOT IN (SELECT scg.grant_dte FROM seg_charity_grants scg 
    					WHERE DATE(scg.`grant_dte`) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})) 
  				GROUP BY scp.pid DESC
			) np2 
  		INNER JOIN seg_discount dis 
    		ON np2.`discountid` = dis.`discountid` 
		GROUP BY aDay, dis.`discountid`) AS t 
		GROUP BY t.aDay, t.discountid)

		UNION ALL

		SELECT 
			'4-total' AS group_id,
			DAY(date_interview) AS aDay,
			'New' AS discountid,
			'1' AS order_id,
			COUNT(*) AS tcount
		FROM(SELECT 
				sp.pid, sp.`encounter_nr`, 
				sp.`date_interview`
     		FROM seg_socserv_patient sp 
	 		INNER JOIN care_encounter ce ON ce.encounter_nr = sp.encounter_nr AND ce.pid = sp.pid
	 		INNER JOIN care_person cp ON cp.pid = ce.pid
     		WHERE 
				sp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
				AND sp.`is_deleted` = 0
				AND sp.pid NOT IN 
					(SELECT p.pid FROM seg_charity_grants_pid p WHERE p.pid = sp.pid )
     		GROUP BY sp.`pid`, sp.`encounter_nr` ) t
		GROUP BY aDay

		UNION ALL

		SELECT 
			'4-total' AS group_id,
			DAY(date_interview) AS aDay,
			'Old' AS discountid,
			'2' AS order_id,
			COUNT(*) AS tcount
		FROM(SELECT 
				sp.pid, sp.`encounter_nr`, 
				sp.`date_interview`, 
				COUNT(DISTINCT p.`discountid`) AS num_grants
	 		FROM seg_charity_grants_pid p
	 		INNER JOIN seg_socserv_patient sp ON sp.`pid` = p.`pid`
	 		INNER JOIN care_encounter ce ON ce.encounter_nr = sp.encounter_nr AND ce.pid = sp.pid
	 		INNER JOIN care_person cp ON cp.pid = ce.pid
	 		WHERE 
				sp.`date_interview` BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
				AND sp.`is_deleted` = 0
	 		GROUP BY sp.`pid`, sp.`encounter_nr` ) t
		GROUP BY aDay

		/*UNION ALL

		SELECT 
			'4-total' AS group_id,
			DAY(date_interview) AS aDay,
			'TOTAL No. of Patient Served' AS discountid,
			'3' AS order_id,
			COUNT(*) AS tcount 
		FROM seg_socserv_patient sp
		INNER JOIN care_encounter ce ON ce.encounter_nr = sp.encounter_nr AND ce.pid = sp.pid
		INNER JOIN care_person cp ON cp.pid = ce.pid
		WHERE
			date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND is_deleted = 0
		GROUP BY aDay*/

		UNION ALL

		SELECT 
			'5-close' AS group_id,
			DAY(grant_dte) AS aDay,
			'Closed Cases' AS discountid,
			'4' AS order_id,
			COUNT(*) AS tcount
		FROM seg_charity_grants_pid sp
		WHERE 
			DATE(grant_dte) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
			AND sp.status IN ('expired')
		GROUP BY aDay";

$rs = $db->Execute($que);
$arr = array();
if (is_object($rs)) {
    if($rs->RecordCount()){
        while ($row = $rs->FetchRow()) {
            $arr[] = array(
            	'group_id' => $row['group_id'],
                'aDay' => intval($row['aDay']),
        		'discountid' => ucwords($row['discountid']),
        		'order_id' => $row['order_id'],
        		'tcount' => (int)$row['tcount'],
            );
        }
    }
} else {
    $arr[0]['aDay'] = null;
}

 /* Get all days of the month and source of referrals and get subheader for the table */
 
$rowHeader = $db->GetAll("SELECT
							'1-refer' AS group_id,
							DAY(selected_date) AS aDay, 
							CASE 
								WHEN ref.source_nr IN ('GC','GH') THEN 'Government Hospital'
								WHEN ref.source_nr IN ('PC','PH') THEN 'Private Hospitals/Clinics'
								WHEN ref.source_nr = 'HCT' THEN 'Health Team Care'
								WHEN ref.source_nr IN ('NGO','PWA') THEN 'NGOs/Private Welfare Agencies'
								WHEN ref.source_nr IN ('DSWD','DOH') THEN 'Government Agencies(DSWD,DOH)'
								WHEN ref.source_nr = 'OT' THEN 'Others (Employees, Former Patients, Colleagues, Friends)'
								ELSE ref.source
							END AS discountid,
							CASE
								WHEN ref.source_nr IN ('GC','GH') THEN '2'
								WHEN ref.source_nr IN ('PC','PH') THEN '3'
								WHEN ref.source_nr = 'P' THEN '4'
								WHEN ref.source_nr = 'M' THEN '5'
								WHEN ref.source_nr = 'HCT' THEN '6'
								WHEN ref.source_nr IN ('NGO','PWA') THEN '7'
								WHEN ref.source_nr IN ('DSWD','DOH') THEN '8'
								WHEN ref.source_nr = 'WLK' THEN '9'
								WHEN ref.source_nr = 'OT' THEN '9A'
								ELSE '9Z'
							END AS order_id,
							0 AS tcount
						FROM
							(SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
								(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
								(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
								(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
								(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
								(SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v,
							seg_social_source_referral ref
						WHERE selected_date BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
						GROUP BY aDay, order_id

						UNION ALL

						SELECT
						'2-phic' AS group_id,
						1 AS aDay, 
						IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), 'Others: SMPMC/DOH Personnel/Dep.', 
							CASE dis.discountid
			    				WHEN 'SC' THEN 'Senior Citizen'
			    				WHEN 'Brgy' THEN 'Bgry. Officials'
			    				WHEN 'Indi' THEN 'Indigenous'
			    				ELSE dis.`discountid`
							END 
						) AS discountid,
						IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), '9D', 
							CASE dis.discountid
			    				WHEN 'A' THEN '1'
			    				WHEN 'B' THEN '2'
			    				WHEN 'C1' THEN '3'
			    				WHEN 'C2' THEN '4'
			    				WHEN 'C3' THEN '5'
			    				WHEN 'D' THEN '6'
			   	 				WHEN 'SC' THEN '8'
			    				WHEN 'Brgy' THEN '9'
			    				WHEN 'PWD' THEN '9A'
			    				WHEN 'BHW' THEN '9B'
			    				WHEN 'Indi' THEN '9C'
			    				ELSE '9E'
							END 
			 			) AS order_id,
						0 AS tcount
						FROM
						 seg_discount AS dis

						UNION ALL

						SELECT
						'3-non_phic' AS group_id,
						1 AS aDay, 
						IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), 'Others: SMPMC/DOH Personnel/Dep.', 
							CASE dis.discountid
			    				WHEN 'SC' THEN 'Senior Citizen'
			    				WHEN 'Brgy' THEN 'Bgry. Officials'
			    				WHEN 'Indi' THEN 'Indigenous'
			    				ELSE dis.`discountid`
							END 
						) AS discountid,
						IF((dis.discountid NOT IN ('A','B','C1','C2','C3','D','SC','Brgy','PWD','BHW','Indi')), '9D', 
							CASE dis.discountid
			    				WHEN 'A' THEN '1'
			    				WHEN 'B' THEN '2'
			    				WHEN 'C1' THEN '3'
			    				WHEN 'C2' THEN '4'
			    				WHEN 'C3' THEN '5'
			    				WHEN 'D' THEN '6'
			   	 				WHEN 'SC' THEN '8'
			    				WHEN 'Brgy' THEN '9'
			    				WHEN 'PWD' THEN '9A'
			    				WHEN 'BHW' THEN '9B'
			    				WHEN 'Indi' THEN '9C'
			    				ELSE '9E'
							END 
			 			) AS order_id,
						0 AS tcount
						FROM
						 seg_discount AS dis ");

for($i = 0; $i < count($rowHeader); $i++){
	$rowHeader[$i]['aDay'] = intval($rowHeader[$i]['aDay']);
	$rowHeader[$i]['discountid'] = ucwords($rowHeader[$i]['discountid']);
    $rowHeader[$i]['tcount'] = intval($rowHeader[$i]['tcount']);
}

$label_refer[0] = array(
					'group_id' => '1-refer',
					'aDay' => 1,
					'discountid' => 'I. SOURCE OF REFERRAL <br>&nbsp;&nbsp;&nbsp;Referring Party',
					'order_id' => '1',
					'tcount' => 0
					);
$label_caseload[0] = array(
					'group_id' => '2-phic',
					'aDay' => 1,
					'discountid' => 'II. CASE LOAD ACCORDING TO CATEGORY AND CLASSIFICATION',
					'order_id' => '0',
					'tcount' => 0
					);
$label_phic[0] = array(
					'group_id' => '2-phic',
					'aDay' => 1,
					'discountid' => '<span style="color: red">Sectoral Groupings:</span>',
					'order_id' => '7',
					'tcount' => 0
					);
$label_phic[1] = array(
					'group_id' => '2-phic',
					'aDay' => 1,
					'discountid' => '<span style="color: blue">PHILHEALTH</span>',
					'order_id' => '1',
					'tcount' => 0
					);
$label_non_phic[0] = array(
					'group_id' => '3-non_phic',
					'aDay' => 1,
					'discountid' => '<span style="color: blue">NON PHILHEALTH</span>',
					'order_id' => '0',
					'tcount' => 0
					);
$label_non_phic[1] = array(
					'group_id' => '3-non_phic',
					'aDay' => 1,
					'discountid' => '<span style="color: red">Sectoral Groupings:</span>',
					'order_id' => '7',
					'tcount' => 0
					);
$row_total[0] = array(
					'group_id' => '4-total',
					'aDay' => 1,
					'discountid' => 'New',
					'order_id' => '1',
					'tcount' => 0
					);
$row_total[1] = array(
					'group_id' => '4-total',
					'aDay' => 1,
					'discountid' => 'Old',
					'order_id' => '2',
					'tcount' => 0
					);
/*$row_total[2] = array(
					'group_id' => '4-total',
					'aDay' => 1,
					'discountid' => 'TOTAL No. of Patient Served',
					'order_id' => '3',
					'tcount' => 0
					);*/
$label_close[0] = array(
					'group_id' => '5-close',
					'aDay' => 1,
					'discountid' => 'Closed Cases',
					'order_id' => '4',
					'tcount' => 0
					);

//Merge all
$data = array();
$data = array_merge($data,$arr);
$data = array_merge($data, $rowHeader);
$data = array_merge($data, $label_refer);
$data = array_merge($data, $label_caseload);
$data = array_merge($data, $label_phic);
$data = array_merge($data, $label_non_phic);
$data = array_merge($data, $row_total);
$data = array_merge($data, $label_close);