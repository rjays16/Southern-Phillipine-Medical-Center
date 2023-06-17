<?php 
/*
 * Author : syboy
 * Date : 05/209/2015
 */

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

#_________________________________________________
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
	
global $db;

$date_from = date('Y-m-d',$_GET['from_date']);
$date_to = date('Y-m-d',$_GET['to_date']);


$sql = "SELECT 
		  numberMonth,
		  MONTH AS MONTH,
		  SUM(CBC) AS CBC,
		  SUM(UA) AS UA,
		  SUM(FA) AS FA,
		  SUM(LIPID_PROFILE) AS LIPID,
		  SUM(FBS) AS FBS,
		  SUM(XRAY) AS XRAY,
		  SUM(ECG) AS ECG,
		  SUM(USD) AS USD,
		  SUM(Others) AS Others 
		FROM
		  (SELECT 
		    EXTRACT(MONTH FROM sls.`create_dt`) AS numberMonth,
		    DATE_FORMAT(sls.`create_dt`, '%M') AS MONTH,
		    SUM(
		      CASE
		        WHEN slsd.`service_code` LIKE '%CBC%' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS CBC,
		    SUM(
		      CASE
		        WHEN slsd.`service_code` = 'URINE' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS UA,
		    SUM(
		      CASE
		        WHEN service.`name` LIKE '%FECALYSIS%' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS FA,
		    SUM(
		      CASE
		        WHEN slsd.`service_code` = 'LIPID' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS LIPID_PROFILE,
		    SUM(
		      CASE
		        WHEN slsd.`service_code` = 'GLUFBS' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS FBS,
		    0 AS XRAY,
		    SUM(
		      CASE
		        WHEN slsd.`service_code` = 'ECG' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS ECG,
		    0 AS USD,
		    SUM(
		      CASE
		        WHEN slsd.`service_code` NOT IN ('GLUFBS', 'LIPID', 'ECG') 
		        AND slsd.`service_code` NOT LIKE '%CBC%' 
		        AND service.`name` NOT LIKE '%FECALYSIS%' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS Others 
		  FROM
		    care_encounter AS ce 
		    INNER JOIN care_person AS cp 
		      ON cp.pid = ce.`pid` 
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
		    EXTRACT(MONTH FROM srs.`request_date`) AS numberMonth,
		    DATE_FORMAT(srs.`request_date`, '%M') AS MONTH,
		    0 AS CBC,
		    0 AS UA,
		    0 AS FA,
		    0 AS LIPID_PROFILE,
		    0 AS FBS,
		    SUM(
		      CASE
		        WHEN srsg.`group_code` LIKE '%XRAY%' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS XRAY,
		    0 AS ECG,
		    SUM(
		      CASE
		        WHEN srsg.`department_nr` IN ('165', '209') 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS USD,
		    SUM(
		      CASE
		        WHEN srsg.`department_nr` NOT IN ('165', '209') 
		        AND srsg.`group_code` NOT LIKE '%XRAY%' 
		        THEN 1 
		        ELSE 0 
		      END
		    ) AS Others 
		  FROM
		    care_encounter AS ce 
		    INNER JOIN care_person AS cp 
		      ON cp.pid = ce.`pid` 
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
		GROUP BY t.MONTH 
		ORDER BY t.numberMonth";

		$i = 0;
		$data = array();		
        $scur = $db->Execute($sql);

        if ($scur) {

        	if ($scur->RecordCount()) {

        		while ($row = $scur->FetchRow()) {

        			$data[$i] = array(
		        			'month' => $row['MONTH'],
		        			'cbc' => $row['CBC'],
		        			'ua' => $row['UA'],
		        			'fa' => $row['FA'],
		        			'l_profile' => $row['LIPID'],
		        			'fbs' => $row['FBS'],
		        			'xray' => $row['XRAY'],
		        			'ecg' => $row['ECG'],
		        			'usd' => $row['USD'],
		        			'others' => $row['Others']
		        			);
        			$i++;
        		}

        		
        	} else {
        		$data[0] = array('month'=>'No Data');
        	}
        } else {
        		$data[0]['month'] = 'No records';
       		}