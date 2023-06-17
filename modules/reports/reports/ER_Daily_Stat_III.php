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
$data = array();

$sql = "SELECT
		 '1-place' AS group_id,
		 DAY(ssp.date_interview) AS aDay, 
		 LCASE(IF((mun.mun_nr = 24 OR mun.mun_nr = 98 OR mun.mun_nr = 233), 
		 	IF(mun.mun_nr = 98, 'Zamboanga', mun.mun_name), 
				CASE prv.prov_nr
					WHEN 2 THEN 'Davao Province (Norte)'
					WHEN 1 THEN 'Comval'
					WHEN 22 THEN 'Surigao Sur'
					WHEN 6 THEN 'Zamboanga'
					WHEN 7 THEN 'Zamboanga'
					WHEN 8 THEN 'Zamboanga'
					ELSE prv.prov_name
				END
		 )) AS municity,	
		 IF((mun.mun_nr = 24 OR mun.mun_nr = 98 OR mun.mun_nr = 233), 
			CASE mun.mun_nr
				WHEN 24 THEN '1'
				WHEN 233 THEN '7'
				WHEN 98 THEN '9H'
				ELSE '9Z'
			END,
			CASE prv.prov_nr
				WHEN 3 THEN '2'
				WHEN 2 THEN '3'
				WHEN 4 THEN '4'
				WHEN 1 THEN '5' 
				WHEN 16 THEN '6'
				WHEN 97 THEN '8'
				WHEN 17 THEN '9A'
				WHEN 22 THEN '9B'
				WHEN 19 THEN '9C'
				WHEN 9 THEN '9D'
				WHEN 20 THEN '9E'
				WHEN 25 THEN '9F'
				WHEN 18 THEN '9G'
				WHEN 6 THEN '9H'
				WHEN 7 THEN '9H'
				WHEN 8 THEN '9H'
				ELSE '9Z'
			END
		 ) AS order_id,
		 COUNT(*) AS tcount
		FROM 
		 seg_provinces prv
		INNER JOIN seg_municity mun ON mun.prov_nr = prv.prov_nr
		INNER JOIN care_person cp ON cp.mun_nr = mun.mun_nr
		INNER JOIN care_encounter ce ON ce.pid = cp.pid
		INNER JOIN seg_socserv_patient ssp ON ssp.`encounter_nr` = ce.`encounter_nr` AND ssp.pid = ce.pid
		WHERE 
		 ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
		 AND ssp.is_deleted = 0
		 AND prv.prov_nr IN (1,2,3,4,16,97,17,22,19,9,20,25,18,6,7,8)
		GROUP BY aDay, municity

		UNION ALL

		SELECT
			'2-sex' AS group_id,
			DAY(ssp.date_interview) AS aDay,
			IF(sex = 'f', 'Female', 'Male') AS municity,
			IF(sex = 'f', '2', '1') AS order_id,
			COUNT(*) AS tcount
		FROM
			care_person cp 
		INNER JOIN care_encounter ce ON ce.pid = cp.`pid`
		INNER JOIN seg_socserv_patient ssp ON ssp.`encounter_nr` = ce.`encounter_nr` AND ssp.pid = ce.pid
		WHERE
			ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
				AND ssp.is_deleted = 0
		GROUP BY aDay, municity

		UNION ALL 

		SELECT
		 '3-status' AS group_id,
		 DAY(ssp.date_interview) AS aDay, 
		 CASE cs.id 
		  WHEN 'child' THEN 'Children'
		  WHEN 'widowed' THEN 'Widow'
		  WHEN 'divorced' THEN 'Separated'
		  WHEN 'infact_separated' THEN 'Separated'
		  WHEN 'legal_separated' THEN 'Separated'
		  ELSE cs.name
		 END AS municity,
		 CASE cs.id 
		  WHEN 'child' THEN '2'
		  WHEN 'single' THEN '3'
		  WHEN 'married' THEN '4'
		  WHEN 'widowed' THEN '5'
		  WHEN 'divorced' THEN '6'
		  WHEN 'infact_separated' THEN '6'
		  WHEN 'legal_separated' THEN '6'
		  ELSE '7'
		 END AS order_id,
		 COUNT(*) AS tcount
		FROM 
		 seg_social_civilstatus cs
		INNER JOIN seg_socserv_patient ssp ON ssp.status = cs.id
		INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
		INNER JOIN care_person cp ON cp.pid = ce.pid
		WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)}) 
		 AND ssp.is_deleted = 0
		GROUP BY aDay, municity

		UNION ALL

		SELECT
		 '4-age' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 IF(
		  cp.age, 
		 CASE 
		  WHEN cp.age >=0 AND cp.age < 1 THEN '0-1 Infant'
		  WHEN cp.age >=1 AND cp.age <3 THEN '1-3 Early Childhood'
		  WHEN cp.age BETWEEN 3 AND 5 THEN '3-5 Free Schooler'
		  WHEN cp.age >=6 AND cp.age <12 THEN '6-12 School Age Child'
		  WHEN cp.age >=12 AND cp.age <18 THEN '12-18 Adolescent'
		  WHEN cp.age >=18 AND cp.age <35 THEN '18-35 Young Adult'
		  WHEN cp.age BETWEEN 35 AND 59 THEN '35-59 Middle Age Adult'
		  WHEN cp.age >=60 THEN '60-65 and Above Late Adult'
		  ELSE ''
		 END,
		 CASE 
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '0-1' THEN '0-1 Infant'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '1-3' THEN '1-3 Early Childhood'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '3-5' THEN '3-5 Free Schooler'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '6-12' THEN '6-12 School Age Child'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '12-18' THEN '12-18 Adolescent'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '18-35' THEN '18-35 Young Adult'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '35-59' THEN '35-59 Middle Age Adult'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '>60' THEN '60-65 and Above Late Adult'
		  ELSE ''
		 END
		 )AS municity,
		IF(cp.age,
		 CASE 
		  WHEN cp.age >=0 AND cp.age < 1 THEN '1'
		  WHEN cp.age >=1 AND cp.age <3 THEN '2'
		  WHEN cp.age BETWEEN 3 AND 5 THEN '3'
		  WHEN cp.age >=6 AND cp.age <12 THEN '4'
		  WHEN cp.age >=12 AND cp.age <18 THEN '5'
		  WHEN cp.age >=18 AND cp.age <35 THEN '6'
		  WHEN cp.age BETWEEN 35 AND 59 THEN '7'
		  WHEN cp.age >=60 THEN '8'
		  ELSE '9'
		 END,
		 CASE 
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '0-1' THEN '1'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '1-3' THEN '2'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '3-5' THEN '3'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '6-12' THEN '4'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '12-18' THEN '5'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '18-35' THEN '6'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '35-59' THEN '7'
		  WHEN fn_get_age_category(cp.date_birth, CURDATE()) = '>60' THEN '8'
		  ELSE '9'
		 END
		) AS order_id,
		COUNT(*) AS tcount
	   FROM
		care_person cp 
	   INNER JOIN care_encounter ce ON ce.pid = cp.pid
	   INNER JOIN seg_socserv_patient ssp ON ssp.encounter_nr = ce.encounter_nr AND ssp.pid = ce.pid
	   WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
		AND ssp.is_deleted = 0
	   GROUP BY aDay, order_id

		UNION ALL 

		SELECT
		 '5-edu' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 CASE 
		  WHEN edu.educ_attain_nr = 1 OR edu.educ_attain_nr = 2 THEN 'Undergraduate'
		  WHEN edu.educ_attain_nr = 3 OR edu.educ_attain_nr = 4 THEN 'Elementary'
		  WHEN edu.educ_attain_nr = 5 OR edu.educ_attain_nr = 6 THEN 'High School'
		  WHEN edu.educ_attain_nr = 7 OR edu.educ_attain_nr = 8 THEN 'Vocational'
		  WHEN edu.educ_attain_nr = 9 OR edu.educ_attain_nr = 10 THEN 'College'
		  ELSE ''
		 END AS municity,
		 CASE 
		  WHEN edu.educ_attain_nr = 1 OR edu.educ_attain_nr = 2 THEN '1'
		  WHEN edu.educ_attain_nr = 3 OR edu.educ_attain_nr = 4 THEN '2'
		  WHEN edu.educ_attain_nr = 5 OR edu.educ_attain_nr = 6 THEN '3'
		  WHEN edu.educ_attain_nr = 7 OR edu.educ_attain_nr = 8 THEN '5'
		  WHEN edu.educ_attain_nr = 9 OR edu.educ_attain_nr = 10 THEN '4'
		  ELSE '9'
		 END AS order_id,
		 COUNT(*) AS tcount
		FROM
	     seg_educational_attainment edu
		INNER JOIN seg_socserv_patient ssp ON ssp.educational_attain = edu.educ_attain_nr
		INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
		INNER JOIN care_person cp ON cp.pid = ce.pid
		WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
		 AND ssp.is_deleted = 0
		 AND edu.educ_attain_nr NOT IN (0, 11, 12, 13, 14)
		GROUP BY aDay, municity

		UNION ALL

		SELECT
		 '6-reg' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 CASE religion_nr
		  WHEN 28 THEN 'Four Square' 
		  ELSE religion_name 
		 END AS municity,
		 CASE religion_nr
		  WHEN 62 THEN '1'
		  WHEN 3 THEN '2'
		  WHEN 43 THEN '3'
		  WHEN 9 THEN '4'
		  WHEN 13 THEN '5'
		  WHEN 28 THEN '6'
		  WHEN 35 THEN '7'
		  WHEN 25 THEN '8'
		  WHEN 79 THEN '9'
		  WHEN 11 THEN '9A'
		  WHEN 33 THEN '9B'
		  WHEN 12 THEN '9C'
		  WHEN 16 THEN '9D'
		 END AS order_id,
		 COUNT(*) AS tcount
		FROM
		 seg_religion reg
		INNER JOIN seg_socserv_patient ssp ON ssp.religion = reg.religion_nr
		INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
		INNER JOIN care_person cp ON cp.pid = ce.pid
		WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
		 AND ssp.is_deleted = 0
		 AND religion_nr IN (62,3,43,9,13,28,35,25,79,11,33,12,16)
		GROUP BY aDay, religion_nr

		UNION ALL

		SELECT
		 '7-inc' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 CASE 
		  WHEN CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 1435 AND 2009 THEN '1,435.00 - 2,009.00'
		  WHEN CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 2010 AND 2583 THEN '2,010.00 - 2,583.00'
		  WHEN CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 2584 AND 3157 THEN '2,584.00 - 3,157.00'
		  ELSE ''
		 END AS municity,
		 CASE 
		  WHEN CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 1435 AND 2009 THEN '1'
		  WHEN CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 2010 AND 2583 THEN '2'
		  WHEN CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 2584 AND 3157 THEN '3'
		  ELSE '4'
		 END AS order_id,
		 COUNT(*) AS tcount
		FROM
		 seg_socserv_patient ssp 
		INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
		INNER JOIN care_person cp ON cp.pid = ce.pid
		WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
		 AND ssp.is_deleted = 0
		 AND CAST(REPLACE(ssp.monthly_income, ',','') AS DECIMAL) BETWEEN 1435 AND 3157
		GROUP BY aDay, order_id

		UNION ALL

		SELECT
		'7A-exp' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 ht.house_description AS municity,
		 ht.house_type_nr AS order_id,
		 COUNT(*) AS tcount
	   FROM
	   	seg_social_house_type ht
	   INNER JOIN seg_socserv_patient ssp ON ssp.house_type = ht.house_type_nr
	   INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
	   INNER JOIN care_person cp ON cp.pid = ce.pid
	   WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)}) 
		 AND ssp.is_deleted = 0
		 AND ht.house_type_nr NOT IN (6,7)
	   GROUP BY aDay, ht.house_type_nr

	   UNION ALL

	   SELECT
		 '8-fuel' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 fs.name AS municity,
		 CASE fs.id
		  WHEN 'CH' THEN '1'
		  WHEN 'FW' THEN '2'
		  WHEN 'GS' THEN '3'
		  WHEN 'KR' THEN '4'
		  WHEN 'EL' THEN '5'
		  ELSE '9'
		 END AS order_id,
		 COUNT(*) AS tcount
	   FROM
	   	seg_social_fuel_source fs
	   INNER JOIN seg_socserv_patient ssp ON ssp.fuel_source = fs.id
	   INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
	   INNER JOIN care_person cp ON cp.pid = ce.pid
	   WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)}) 
		AND ssp.is_deleted = 0
	   GROUP BY aDay, fs.id

	   UNION ALL

	   SELECT
		'9-light' AS group_id,
		 DAY(ssp.date_interview) AS aDay,
		 ls.name AS municity,
		 ls.id AS order_id,
		 COUNT(*) AS tcount
	   FROM
	   	seg_social_light_source ls
	   INNER JOIN seg_socserv_patient ssp ON ssp.light_source = ls.id
	   INNER JOIN care_encounter ce ON ce.encounter_nr = ssp.encounter_nr AND ce.pid = ssp.pid
	   INNER JOIN care_person cp ON cp.pid = ce.pid
	   WHERE ssp.date_interview BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)}) 
		 AND ssp.is_deleted = 0
		 AND ls.id != ''
	   GROUP BY aDay, ls.id";

$rs = $db->Execute($sql);
$place = array();
if (is_object($rs)) {
    if($rs->RecordCount()){
        while ($row = $rs->FetchRow()) {
            $place[] = array(
            	'group_id' => $row['group_id'],
                'aDay' => intval($row['aDay']),
        		'municity' => ucwords($row['municity']),
        		'order_id' => $row['order_id'],
        		'tcount' => (int)$row['tcount'],
            );
        }
    }
} else {
    $place[0]['aDay'] = null;
}

/* Get all days of the month all table data*/

$all_place = $db->GetAll("SELECT
					  	'1-place' AS group_id,
		 				DAY(selected_date) AS aDay, 
		 				LCASE(IF((mun.mun_nr = 24 OR mun.mun_nr = 98 OR mun.mun_nr = 233), 
		 				 IF(mun.mun_nr = 98, 'Zamboanga', mun.mun_name), 
						  CASE prv.prov_nr
						   WHEN 2 THEN 'Davao Province (Norte)'
						   WHEN 1 THEN 'Comval'
						   WHEN 22 THEN 'Surigao Sur'
						   WHEN 6 THEN 'Zamboanga'
						   WHEN 7 THEN 'Zamboanga'
						   WHEN 8 THEN 'Zamboanga'
						   ELSE prv.prov_name
						  END
		 				)) AS municity,	
		 				IF((mun.mun_nr = 24 OR mun.mun_nr = 98 OR mun.mun_nr = 233), 
						 CASE mun.mun_nr
						  WHEN 24 THEN '1'
						  WHEN 233 THEN '7'
						  WHEN 98 THEN '9H'
						  ELSE '9Z'
						 END,
						 CASE prv.prov_nr
						  WHEN 3 THEN '2'
						  WHEN 2 THEN '3'
						  WHEN 4 THEN '4'
						  WHEN 1 THEN '5' 
						  WHEN 16 THEN '6'
						  WHEN 97 THEN '8'
						  WHEN 17 THEN '9A'
						  WHEN 22 THEN '9B'
						  WHEN 19 THEN '9C'
						  WHEN 9 THEN '9D'
						  WHEN 20 THEN '9E'
						  WHEN 25 THEN '9F'
						  WHEN 18 THEN '9G'
						  WHEN 6 THEN '9H'
						  WHEN 7 THEN '9H'
						  WHEN 8 THEN '9H'
						  ELSE '9Z'
						 END
		 				) AS order_id,
						0 AS tcount
					  FROM
						(SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
						 (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
						  (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
						  (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
						  (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
						  (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v,
						seg_provinces prv, seg_municity mun
					  WHERE selected_date BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})
					  AND prv.prov_nr IN (1,2,3,4,16,97,17,22,19,9,20,25,18,6,7,8)
					  GROUP BY aDay, order_id

					  UNION ALL

					  SELECT
		 				'3-status' AS group_id,
		 				1 AS aDay, 
		 				CASE cs.id 
		 				 WHEN 'child' THEN 'Children'
		  				 WHEN 'widowed' THEN 'Widow'
		  				 WHEN 'divorced' THEN 'Separated'
		  				 WHEN 'infact_separated' THEN 'Separated'
		  				 WHEN 'legal_separated' THEN 'Separated'
		  				 ELSE cs.name
		 				END AS municity,
		 				CASE cs.id 
		  				 WHEN 'child' THEN '2'
		  				 WHEN 'single' THEN '3'
		  				 WHEN 'married' THEN '4'
		  				 WHEN 'widowed' THEN '5'
		  				 WHEN 'divorced' THEN '6'
		  				 WHEN 'infact_separated' THEN '6'
		  				 WHEN 'legal_separated' THEN '6'
		  				 ELSE '7'
		 				END AS order_id,
		 				0 AS tcount
					  FROM 
		 				seg_social_civilstatus cs

		 			  UNION ALL

		 			  SELECT DISTINCT 
						'2-sex' AS group_id,
						1 AS aDay,
						IF(sex = 'f', 'Female', 'Male') AS municity, 
						IF(sex = 'f', '2', '1') AS order_id,
						0 AS tcount
					  FROM care_person 
					  WHERE sex IN ('f','m')

					  UNION ALL

					  SELECT
		 			    '5-edu' AS group_id,
		 				1 AS aDay,
		 				CASE 
		  				 WHEN edu.educ_attain_nr = 1 OR edu.educ_attain_nr = 2 THEN 'Undergraduate'
		  				 WHEN edu.educ_attain_nr = 3 OR edu.educ_attain_nr = 4 THEN 'Elementary'
		  				 WHEN edu.educ_attain_nr = 5 OR edu.educ_attain_nr = 6 THEN 'High School'
		  				 WHEN edu.educ_attain_nr = 7 OR edu.educ_attain_nr = 8 THEN 'Vocational'
		  				 WHEN edu.educ_attain_nr = 9 OR edu.educ_attain_nr = 10 THEN 'College'
		  				 ELSE ''
		 				END AS municity,
		 				CASE 
		  				 WHEN edu.educ_attain_nr = 1 OR edu.educ_attain_nr = 2 THEN '1'
		  				 WHEN edu.educ_attain_nr = 3 OR edu.educ_attain_nr = 4 THEN '2'
		  				 WHEN edu.educ_attain_nr = 5 OR edu.educ_attain_nr = 6 THEN '3'
		  				 WHEN edu.educ_attain_nr = 7 OR edu.educ_attain_nr = 8 THEN '5'
		  				 WHEN edu.educ_attain_nr = 9 OR edu.educ_attain_nr = 10 THEN '4'
		  				 ELSE '9'
		 				END AS order_id,
		 				0 AS tcount
					  FROM
	     				seg_educational_attainment edu
	     			  WHERE edu.educ_attain_nr NOT IN (0, 11, 12, 13, 14)

					  UNION ALL 

					  SELECT
						'6-reg' AS group_id,
						1 AS aDay,
						CASE religion_nr
						 WHEN 28 THEN 'Four Square' 
						 ELSE religion_name 
						END AS municity,
						CASE religion_nr
						 WHEN 62 THEN '1'
						 WHEN 3 THEN '2'
						 WHEN 43 THEN '3'
						 WHEN 9 THEN '4'
						 WHEN 13 THEN '5'
						 WHEN 28 THEN '6'
						 WHEN 35 THEN '7'
						 WHEN 25 THEN '8'
						 WHEN 79 THEN '9'
						 WHEN 11 THEN '9A'
						 WHEN 33 THEN '9B'
						 WHEN 12 THEN '9C'
						 WHEN 16 THEN '9D'
						END AS order_id,
						0 AS tcount
					  FROM
						seg_religion
					  WHERE religion_nr IN (62,3,43,9,13,28,35,25,79,11,33,12,16)
					  
					  UNION ALL

					  SELECT
						'7A-exp' AS group_id,
		 				1 AS aDay,
		 				ht.house_description AS municity,
		 				ht.house_type_nr AS order_id,
		 				0 AS tcount
	   				  FROM
	   					seg_social_house_type ht
	   				  WHERE ht.house_type_nr NOT IN (6,7)

	   				  UNION ALL

	   				  SELECT
		 				'8-fuel' AS group_id,
		 				1 AS aDay,
		 				fs.name AS municity,
		 				CASE fs.id
		  				 WHEN 'CH' THEN '1'
		  				 WHEN 'FW' THEN '2'
		  				 WHEN 'GS' THEN '3'
		  				 WHEN 'KR' THEN '4'
		  				 WHEN 'EL' THEN '5'
		  				 ELSE '9'
		 				END AS order_id,
		 				0 AS tcount
	   				  FROM
	   					seg_social_fuel_source fs
	   					 
	   				  UNION ALL
	   					 
	   				  SELECT
						'9-light' AS group_id,
		 				1 AS aDay,
		 				ls.name AS municity,
		 				ls.id AS order_id,
		 				0 AS tcount
	   				  FROM
	   					seg_social_light_source ls
	   				  WHERE ls.id != ''");

for($i = 0; $i < count($all_place); $i++){
	$all_place[$i]['aDay'] = intval($all_place[$i]['aDay']);
	$all_place[$i]['municity'] = ucwords($all_place[$i]['municity']);
    $all_place[$i]['tcount'] = intval($all_place[$i]['tcount']);
}

$label_place[0] = array(
					'group_id' => '1-place',
					'aDay' => 1,
					'municity' => 'III. PLACE OF ORIGIN',
					'order_id' => '0',
					'tcount' => 0
				  );

$label_gender[0] = array(
					'group_id' => '2-sex',
					'aDay' => 1,
        			'municity' => 'SEX',
        			'order_id' => '0',
        			'tcount' => 0
				   );
$label_status[0] = array(
					'group_id' => '3-status',
					'aDay' => 1,
        			'municity' => 'CIVIL STATUS',
        			'order_id' => '1',
        			'tcount' => 0
				   );
$row_age[0] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '0-1 Infant',
        			'order_id' => '1',
        			'tcount' => 0
					);
$row_age[1] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '1-3 Early Childhood',
        			'order_id' => '2',
        			'tcount' => 0
					);
$row_age[2] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '3-5 Free Schooler',
        			'order_id' => '3',
        			'tcount' => 0
					);
$row_age[3] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '6-12 School Age Child',
        			'order_id' => '4',
        			'tcount' => 0
					);
$row_age[4] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '12-18 Adolescent',
        			'order_id' => '5',
        			'tcount' => 0
					);
$row_age[5] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '18-35 Young Adult',
        			'order_id' => '6',
        			'tcount' => 0
					);
$row_age[6] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '35-59 Middle Age Adult',
        			'order_id' => '7',
        			'tcount' => 0
					);
$row_age[7] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => '60-65 And Above Late Adult',
        			'order_id' => '8',
        			'tcount' => 0
					);
$label_age[0] = array(
					'group_id' => '4-age',
					'aDay' => 1,
        			'municity' => 'Age Bracket',
        			'order_id' => '0',
        			'tcount' => 0
					);
$label_edu[0] = array(
					'group_id' => '5-edu',
					'aDay' => 1,
        			'municity' => 'EDUCATIONAL ATTAINMENT',
        			'order_id' => '0',
        			'tcount' => 0
					);
$label_reg[0] = array(
					'group_id' => '6-reg',
					'aDay' => 1,
        			'municity' => 'RELIGION',
        			'order_id' => '0',
        			'tcount' => 0
					);
$row_inc[0] = array(
					'group_id' => '7-inc',
					'aDay' => 1,
        			'municity' => '1,435.00 - 2,009.00',
        			'order_id' => '1',
        			'tcount' => 0
					);
$row_inc[1] = array(
					'group_id' => '7-inc',
					'aDay' => 1,
        			'municity' => '2,010.00 - 2,583.00',
        			'order_id' => '2',
        			'tcount' => 0
					);
$row_inc[2] = array(
					'group_id' => '7-inc',
					'aDay' => 1,
        			'municity' => '2,584.00 - 3,157.00',
        			'order_id' => '3',
        			'tcount' => 0
					);
$label_inc[0] = array(
					'group_id' => '7-inc',
					'aDay' => 1,
        			'municity' => 'Income Bracket',
        			'order_id' => '0',
        			'tcount' => 0
					);
$label_exp[0] = array(
					'group_id' => '7A-exp',
					'aDay' => 1,
        			'municity' => 'Monthly Expenses',
        			'order_id' => '0',
        			'tcount' => 0
					);
$label_exp[1] = array(
					'group_id' => '7A-exp',
					'aDay' => 1,
        			'municity' => 'House and Lot',
        			'order_id' => '0B',
        			'tcount' => 0
					);
$label_fuel[0] = array(
					'group_id' => '8-fuel',
					'aDay' => 1,
        			'municity' => 'FUEL',
        			'order_id' => '0',
        			'tcount' => 0
						);
$label_light[0] = array(
					'group_id' => '9-light',
					'aDay' => 1,
        			'municity' => 'LIGHT SOURCE',
        			'order_id' => '0',
        			'tcount' => 0
						);

// Merge all
$place = array_merge($place, $all_place);
$data = array_merge($data, $label_place);
$data = array_merge($data,$place);
$data = array_merge($data, $label_gender);
$data = array_merge($data, $label_status);
$data = array_merge($data, $label_age);
$data = array_merge($data, $row_age);
$data = array_merge($data, $label_edu);
$data = array_merge($data, $label_reg);
$data = array_merge($data, $row_inc);
$data = array_merge($data, $label_inc);
$data = array_merge($data, $label_exp);
$data = array_merge($data, $label_fuel);
$data = array_merge($data, $label_light);
