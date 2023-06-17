<?php 
/*
 * Author : gelie
 * Date : 10/04/2015
 */

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

#_________________________________________________
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
$params->put('prepared_by', strtoupper($_SESSION['sess_login_username']));
	
global $db;

$date_from = date('Y-m-d',$_GET['from_date']);
$date_to = date('Y-m-d',$_GET['to_date']);


$sql = "SELECT
			HRN,
			CASE_NO,
			ENCOUNTER_DATE,
			UCASE(FullName) AS FullName,
			AgeSex,
			SUM(HEMA) AS HEMA,
			SUM(FBS) AS FBS,
			SUM(LIPID) AS LIPID,
			SUM(CHOL) AS CHOL,
			SUM(NA) AS NA,
			SUM(K) AS K,
			SUM(CREA) AS CREA,
			SUM(CHEM) AS CHEM,
			SUM(URINE) AS URINE,
			SUM(ECG) AS ECG,
			SUM(XRAY) AS XRAY,
			SUM(OTHER_XRAY) AS OTHER_XRAY,
			SUM(Others) AS Others
		FROM 
		  (SELECT 
		   	cp.`pid` AS HRN,
		   	ce.`encounter_nr` AS CASE_NO,
		   	ce.`encounter_date` AS ENCOUNTER_DATE,
		   	CONCAT(
		   		cp.`name_last`,',', cp.`name_first`
		   	) AS FullName,
		   	CONCAT(
		   		REPLACE(
		   	  		`fn_get_age`( 
		         		ce.`encounter_date`,   
		         		cp.`date_birth`
		       		), 'years', ''
				), '/ ', UCASE(cp.`sex`)
		    ) AS AgeSex,      				
			SUM(
			  CASE
			  	WHEN service.`group_code` = 'H' 
			  	AND slsd.`is_served` = 1
			  	THEN 1
			  	ELSE 0
			  END
			) AS HEMA,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'GLUFBS' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS FBS,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'LIPID' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS LIPID,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'CHOL' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS CHOL,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'NA' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS NA,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'K+' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS K,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'CREA' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS CREA,
			SUM(
			  CASE
			  	WHEN slsd.`service_code` NOT IN ('GLUFBS', 'LIPID', 'CHOL', 'CREA') AND service.`group_code` = 'C' 
			  	AND slsd.`is_served` = 1 
			  	THEN 1
			  	ELSE 0
			  END
			) AS CHEM,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'URINE' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS URINE,
			SUM(
		      CASE
		        WHEN slsd.`service_code` = 'ECG' 
		        AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS ECG,
			0 AS XRAY,
			0 AS OTHER_XRAY,
			SUM(
		      CASE
		        WHEN slsd.`service_code` NOT IN ('URINE', 'ECG') 
		        AND service.`group_code` NOT IN ('C', 'H')   #Covers GLUFBS, LIPID, CHOL, CREA 
		 		AND slsd.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS Others
		  FROM
		    care_encounter AS ce 
		    INNER JOIN care_person AS cp 
		      ON cp.`pid` = ce.`pid` 
		    INNER JOIN `seg_lab_serv` AS sls 
		      ON sls.`encounter_nr` = ce.`encounter_nr` 
		    INNER JOIN `seg_lab_servdetails` AS slsd 
		      ON sls.`refno` = slsd.`refno` 
		    INNER JOIN `seg_lab_services` AS service 
		      ON slsd.`service_code` = service.`service_code` 
		  WHERE ce.`status` NOT IN (
		      'deleted',
		      'hidden',
		      'inactive',
		      'void'
		    ) 
		    AND sls.`status` NOT IN (
		      'deleted',
		      'hidden',
		      'inactive',
		      'void'
		    ) 
		    AND `fn_get_age` (
		      sls.`create_dt`,
		      cp.`date_birth`
		    ) >= 60 
		    AND DATE(sls.`create_dt`) BETWEEN ({$db->qstr($date_from)})
		    AND ({$db->qstr($date_to)}) 
		    AND ce.`encounter_type` = 2 
		  GROUP BY sls.`refno` 
		  UNION
		  ALL 
		  SELECT
		  	cp.`pid` AS HRN, 
		  	ce.`encounter_nr` AS CASE_NO,
		  	ce.`encounter_date` AS ENCOUNTER_DATE,
		   	CONCAT(
		   		cp.`name_last`,',', cp.`name_first`
		   	) AS FullName,
		   	CONCAT(
		   		REPLACE(
		   	  		`fn_get_age`(
		         		ce.`encounter_date`,   
		         		cp.`date_birth`
		         	), 'years', ''
		       ), '/ ', UCASE(cp.`sex`)
		    ) AS AgeSex,  
		    0 AS HEMA,     				 
		  	0 AS FBS,
		    0 AS LIPID,
		    0 AS CHOL,
		    0 AS NA,
		    0 AS K,
		    0 AS CREA,
		    0 AS CHEM,
		    0 AS URINE,
		    0 AS ECG,
		    SUM(
		      CASE
		        WHEN srsg.`group_code` = 'XRAY-C' 
		        AND ctrr.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS XRAY,
			SUM(
		      CASE
		        WHEN srsg.`group_code` IN ('XRAY-A', 'XRAY-AS', 'XRAY-COB', 'XRAY-BE', 'XRAY-LE', 'XRAY-RE', 'XRAY-S') 
		        	AND ctrr.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS OTHER_XRAY,
			SUM(
		      CASE
		        WHEN srsg.`group_code` NOT IN ('XRAY-C', 'XRAY-A', 'XRAY-AS', 'XRAY-COB', 'XRAY-BE', 'XRAY-LE', 'XRAY-RE', 'XRAY-S')
		        	AND ctrr.`is_served` = 1
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS Others
		  FROM
		    care_encounter AS ce 
		    INNER JOIN care_person AS cp 
		      ON cp.`pid` = ce.`pid` 
		    INNER JOIN `seg_radio_serv` AS srs 
		      ON srs.`encounter_nr` = ce.`encounter_nr` 
		    INNER JOIN care_test_request_radio AS ctrr 
		      ON ctrr.`refno` = srs.`refno` 
		    INNER JOIN `seg_radio_services` AS service 
		      ON ctrr.`service_code` = service.`service_code` 
		    INNER JOIN `seg_radio_service_groups` AS srsg 
		      ON service.`group_code` = srsg.`group_code` 
		  WHERE ce.`status` NOT IN (
		      'deleted',
		      'hidden',
		      'inactive',
		      'void'
		    ) 
		    AND ctrr.`status` NOT IN (
		      'deleted',
		      'hidden',
		      'inactive',
		      'void'
		    ) 
		    AND `fn_get_age` (
		      srs.`request_date`,
		      cp.`date_birth`
		    ) >= 60 
		    AND DATE(srs.`request_date`) BETWEEN ({$db->qstr($date_from)})
		    AND ({$db->qstr($date_to)}) 
		    AND ce.`encounter_type` = 2 
		  GROUP BY srs.`refno`) AS t
		  GROUP BY t.CASE_NO
		  ORDER BY t.ENCOUNTER_DATE DESC";

//		 echo $sql;
//    	 exit();

		$i = 0;
		$data = array();		
        $scur = $db->Execute($sql);

        if ($scur) {

        	if ($scur->RecordCount()) {

        		while ($row = $scur->FetchRow()) {

        			$data[$i] = array(
		        			'hrn' => $row['HRN'],
		        			'name' => utf8_decode(trim($row['FullName'])),
		        			'age_sex' => $row['AgeSex'],
		        			'hematology' => $row['HEMA'],
		        			'fbs' => $row['FBS'],
		        			'lipid' => $row['LIPID'],
		        			'cholesterol' => $row['CHOL'],
		        			'sodium' => $row['NA'],
		        			'potassium' => $row['K'],
		        			'creatinine' => $row['CREA'],
		        			'chem' => $row['CHEM'],
		        			'urinalysis' => $row['URINE'],
		        			'ecg' => $row['ECG'],
		        			'xray' => $row['XRAY'],
		        			'other_xray' => $row['OTHER_XRAY'],
		        			'others' => $row['Others']
		        			);
        			$i++;
        		}

        		
        	} else {
        		$data[0] = array('hrn'=>'No Data');
        	}
        } else {
        		$data[0]['hrn'] = 'No records';
       		}
