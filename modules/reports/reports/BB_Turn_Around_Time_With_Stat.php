<?php
/*
Created by Borj, 05/09/2014 09:00 AM
Blood Bank Report Turn Around Time With Stat
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');


     #TITLE of the report
     $params->put("hospital_name", mb_strtoupper($hosp_name));
     $params->put("header", $report_title);
     $params->put("department", "Blood Bank");
    
     global $db;

     $from = date('Y-m-d',$_GET['from_date']);
     $to = date('Y-m-d',$_GET['to_date']);


    $sql = "SELECT
    		    a.ordername AS pat_name,
				b.serial_no AS unit_test,
				(
				 CASE 
				  b.result
				  WHEN 'noresult' THEN 'NO RESULT'
				  WHEN 'compat' THEN 'COMPATIBLE'
				  WHEN 'incompat' THEN 'INCOMPATIBLE'
				  WHEN 'retype' THEN 'RETYPING'
				  ELSE b.result
				 END
				) AS result,
	 			DATE_FORMAT(a.create_dt,'%m-%d-%Y %r') AS date_recorded,
	 			DATE_FORMAT(b.received_date,'%m-%d-%Y %r') AS date_received,
	 			DATE_FORMAT(c.done_date,'%m-%d-%Y %r') AS date_done,				
	 			TIMEDIFF(c.done_date,b.`received_date`) AS turn_around_time
			FROM 
			seg_lab_serv AS a
			INNER JOIN seg_blood_received_details AS b
			 ON a.refno = b.refno
	 		LEFT JOIN seg_blood_received_status AS c
	 		 ON a.refno = c.refno
	 		 AND b.`ordering` = c.`ordering`
	 		WHERE a.is_urgent = 1 
	 		AND 
	 		DATE(a.create_dt)
	 		BETWEEN DATE(".$db->qstr($from).")
	 		AND
	 		DATE(".$db->qstr($to).")";

$rs = $db->Execute($sql);
$date = array();
$rowIndex = 0;

if(is_object($rs)){
	while($row=$rs->FetchRow()){
		$data[$rowIndex] = array('pat_name' => utf8_decode(trim($row['pat_name'])),
								 'unit_test' => $row['unit_test'],
								 'result' => $row['result'],
								 'date_recorded' => $row['date_received'],
								 'date_done' => $row['date_done'],
								 'turn_around_time' => $row['turn_around_time']);
		$rowIndex++;
	}
}else{
	$data[0]['pat_name'] = 'No records';
}

?>