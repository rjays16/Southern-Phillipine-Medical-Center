<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
// require_once('roots.php');

// require_once($root_path . 'include/inc_environment_global.php');

// require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
// require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');
 // include('parameters.php');
 require_once('./roots.php');
 require_once($root_path.'include/inc_environment_global.php');
 include_once($root_path . 'include/care_api_classes/class_personell.php');
    include('parameters.php');
     global $db;
// require_once($root_path.'include/care_api_classes/class_radioservices_transaction.php');
//     // 
// $objLab = new SegRadio;
// $objInfo = new Hospital_Admin();
$pers_obj = new Personell;


  $params->put("hosp_country", mb_strtoupper($hosp_country));
  $params->put("hosp_agency", mb_strtoupper($hosp_agency));
  $params->put("hosp_name", mb_strtoupper($hosp_name));
  $params->put("params","From : ". date('m/d/Y',$from_date)." To : " .date('m/d/Y',$to_date) );
  $sql = "SELECT 
  
  charge_name 
FROM
  seg_type_charge 
  WHERE id = 'cao'
ORDER BY charge_name ";
    #$report_title = $db->GetOne($sql);
    $report_info = $db->GetRow($sql);

$sql = "SELECT 
 		 srs.`request_date`,
  		srs.`request_time`,
 		 srs.`pid`,
  		srs.`encounter_nr`,
 		 fn_get_person_name (srs.`pid`) AS patient_name,
  		srs.`refno`,
 		 sr_serv.`name` AS request_items,
  		ctrr.`price_charge` AS price,
 		ctrr.`encoder`FROM seg_radio_serv AS srs LEFT JOIN care_test_request_radio AS ctrr
		ON ctrr.`refno` = srs.`refno`
		LEFT JOIN seg_radio_services AS sr_serv
		ON sr_serv.`service_code` = ctrr.`service_code`
		WHERE ctrr.`is_served`
  		$rad_sql
  		AND ctrr.status != 'deleted' 
  		AND (
    	srs.`request_date` BETWEEN DATE(".$db->qstr(date($from_date_format)).")
                    AND
                    DATE(".$db->qstr(date($to_date_format)).")
		 )";
 		 // var_dump($sql);exit();

 		$sql1 = "SELECT charge_name FROM seg_type_charge WHERE id = ".$db->qstr($rad_charge_type)." ORDER BY charge_name";
   		$charge_name = $db->GetOne($sql1);
 		$params->put("department","List of Served Radiology Services with charge ".$charge_name);
 		

// var_dump($sql);exit();

 		 // var_dump($rad_charge_type);exit();

// $from_date = date('Y-m-d', strtotime($from_date_format));
// $to_date = date('Y-m-d', strtotime($to_date_format));

// $result = $objLab->getLabServedServicesNscm($from_date,$to_date,$charge_type);

 		 $rs = $db->Execute($sql);
         $rowindex = 0;
          if ($rs->RecordCount() > 0){ 
      		  while($row=$rs->FetchRow()){ 
        	$date = date('m/d/Y', strtotime($row['request_date']));
			$time = date('h:i:A', strtotime($row['request_time']));
			$request_date_time = $date . " " . $time;
			$hrn = $row['pid'];
			if($row['encounter_nr']==''){
				$encounter_nr = 'WALK-IN';
			}
			else{
				$encounter_nr= $row['encounter_nr'];
			}
				$patient_name = strtoupper($row['patient_name']);
			$refno = $row['refno'];
			$request_items = strtoupper($row['request_items']);
			$price = $row['price'];
			// Added by Matsuu for old data.
			$temp_encoder = $pers_obj->getUserFullName($row['encoder']);
			if($temp_encoder != false)
				$encoder = strtoupper($temp_encoder);
			else
				$encoder = strtoupper($row['encoder']);
			// Ended here..

			$temp_encoder = $personell_obj->getUserFullName($scheduled_by);

			if($temp_encoder != false)
				$encoder = $temp_encoder;
			else
				$encoder =$scheduled_by;
			
				$total += $price;
			$data[$rowindex] = array(
				'request_datetime' => $request_date_time,
				'hrn' => $hrn,
				'encounter' => $encounter_nr,
				'name' => utf8_decode(trim($patient_name)),
				'reference' => $refno,
				'request' => utf8_decode($request_items),
				'price' => number_format($price, 2, '.', ','),
				'encoder' => strtoupper($encoder),
				'total_price' => number_format($total, 2, '.', ',')
			);
			$rowindex++;


        }
    }

// if($result) {
// 	if($result->RecordCount() > 0) {
// 		$i = 0;
// 		while ($row = $result->FetchRow()) {
// 			$date = date('m/d/Y', strtotime($row['']));
// 			$time = date('h:i:A', strtotime($row['']));
// 			$request_date_time = $date . " " . $time;
// 			$hrn = $row['pid'];
// 			if ($row['encounter_nr'] == '') {
// 				$encounter_nr = 'WALK-IN';
// 			} else {
// 				$encounter_nr = $row['encounter_nr'];
// 			}
// 			$patient_name = strtoupper($row['patient_name']);
// 			$refno = $row['refno'];
// 			$request_items = strtoupper($row['request_items']);
// 			$price = $row['price'];
// 			$encoder = strtoupper($row['encoder']);

// 			$total += $price;

// 			$data[$i] = array(
// 				'request_date_time' => $,
// 				'hrn' => $,
// 				'encounter_nr' => $,
// 				'patient_name' => $,
// 				'refno' => $,
// 				'request_items' => $,
// 				'price' => $,
// 				'encoder' => $,
// 				'total' => number_format($total, 2, '.', '')
// 			);
// 			$i++;
// 		}
// 	}
// 	else{
// 		$data = array(
// 			array(
// 				'request_datetime' => 'No Results Found'
// 			)
// 		);
// 	}
// }
// else{
// 	$data = array(
// 		array(
// 			'request_datetime' => 'No Results Found'
// 		)
// 	);
// }

?>