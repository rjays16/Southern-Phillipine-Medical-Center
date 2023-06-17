<?php
/*
 * @author Matss 07/26/2017
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    define(notime,'08:00');
    #TITLE of the report
	if(empty($lab_patient_type)){
		$lab_patient_type = "ALL PATIENT";
		$enctype=" ";
	}
    $params->put("hosp_country", mb_strtoupper($hosp_country));
    $params->put("hosp_agency", mb_strtoupper($hosp_agency));
    $params->put("hosp_name", mb_strtoupper($hosp_name));
    $params->put("hosp_address", mb_strtoupper($hosp_addr1));
    $params->put("department", mb_strtoupper("DEPARTMENT OF PATHOLOGY AND CLINICAL LABORATORIES"));
    $params->put("params",$lab_patient_type." with Repeat Collection ". date('F d, Y',$from_date)." ".$param_time_from." To ". date('F d, Y',$to_date)." ".$param_time_to);

    global $db;
$date_from = strftime('%H:%M',strtotime($param_time_from));
$date_to = strftime('%H:%M',strtotime($param_time_to));

$condition_date = "AND 
    DATE(ls.`serv_dt`)
               BETWEEN
                    DATE(".$db->qstr(date($from_date_format)).")
                    AND
                    DATE(".$db->qstr(date($to_date_format)).")";
            if($date_from!=notime &&$date_to!=notime){
            	 $condition_time = "AND 
    			TIME(ls.`serv_tm`)
               BETWEEN
                    TIME(".$db->qstr($date_from).")
                    AND
                    TIME(".$db->qstr($date_to).")";

            }
            else{
            	$condition_time = " ";
            }

$sql ="SELECT ce.encounter_nr,
  CONCAT(ls.`serv_dt`, ' ', ls.`serv_tm`) AS serv_datetime,
  ls.`refno` AS refno,
  fn_get_person_lastname_first (ls.`pid`) AS patient_name,
  ls.`pid` AS HRN,
  fn_get_age (
   now(),
    cp.`date_birth`

  ) AS age,
  cp.`sex` AS sex,
  (SELECT 
    CONCAT(
      cw.`name`,
      ' ',
      SUBSTRING_INDEX(
        GROUP_CONCAT(cel.location_nr SEPARATOR '|'),
        ',',
        - 2
      )
    ) 
  FROM
    care_encounter_location AS cel 
    LEFT JOIN care_ward AS cw 
      ON cel.group_nr = cw.`nr` 
  WHERE cel.encounter_nr = ls.encounter_nr AND cel.status <> 'discharged' AND cel.nr <> 0
  ORDER BY cel.`nr` ) AS cel_position,
  /* cel.location_nr AS location, */
  GROUP_CONCAT(sls.`name`) AS patient_procedure,
  ls.`comments` AS remarks,
  GROUP_CONCAT(
  IF(lsd.`date_served` = '0000-00-00 00:00:00','0000-00-00 00:00:00',
    DATE_FORMAT(
      lsd.`date_served`,
      '%b %d,%Y %r'
    )) SEPARATOR '|'
  ) AS date_served,ce.`encounter_type`,
  fn_get_er_location_name(ce.`er_location`) as location,
  fn_get_department_name(ce.`consulting_dept_nr`) AS department 
FROM
  seg_lab_serv AS ls 
  LEFT JOIN seg_lab_servdetails AS lsd 
    ON ls.`refno` = lsd.`refno` 
  LEFT JOIN care_person AS cp 
    ON ls.`pid` = cp.`pid` 
  LEFT JOIN care_encounter AS ce 
    ON ls.`encounter_nr` = ce.`encounter_nr` 
  LEFT JOIN seg_lab_services AS sls 
    ON lsd.`service_code` = sls.`service_code` 
  LEFT JOIN 
    (SELECT 
      * 
    FROM
      care_encounter_location AS cels 
    LIMIT 1) AS cel 
    ON ls.encounter_nr = cel.`encounter_nr` 
WHERE 
ls.`is_repeatcollection` = 1 
  AND ls.`status` NOT IN ('void', 'deleted') ".$condition_date.$condition_time.$enctype." GROUP BY refno 
    ";
   
     // var_dump($sql);exit();
         $rs = $db->Execute($sql);
         $rowindex = 0;
         $z = 0;
   if ($rs->RecordCount() > 0){ 

        while($row=$rs->FetchRow()){ 
        	$position = explode('|', $row['cel_position']);
        	$date_served = str_replace("|", "\n", $row['date_served']);
        	$date_serv = str_replace("0000-00-00 00:00:00","Not Served", $date_served);
         if(!empty($row['encounter_nr'])){
            if($row['encounter_type']=='1'){
              $location = $row['location'];
            }
            elseif($row['encounter_type']=='2') {
              $location = $row['department'];
            }
            elseif ($row['encounter_type']=='5') {
              $location = "RDU";
              # code...
            }
            else{
              $location = $position[0]." Rm.#".$position[1].", Bed#".$position[2];
            }
          }
          else{
            $location = "Walk-in";
          }
             $data[$rowindex] =array(
             						 'no'=>$rowindex+1,
             						 'req_datetime' =>date("F d, Y h:i:A",strtotime($row['serv_datetime'])),
                                  	 'reference' =>$row['refno'],
                                 	 'patient_name' => utf8_decode(trim($row['patient_name'])),
                                  	 'HRN' =>$row['HRN'],
                                 	 'age' =>($row['age'] != null) ? $row['age'] : 'N/A',
                                  	 'sex' =>strtoupper($row['sex']),
                                     'location' =>$location,
                                  	 'procedure' => str_replace(",", "\n", $row['patient_procedure']),
                                  	 'remarks'=>$row['remarks'],
                                  	 // 'date_served'=>$row['date_served']=='0000-00-00 00:00:00'? "" :date("F d, Y h:i:A",strtotime($row['date_served']))
                                  	 'date_served'=>$date_serv
                                  );
              $rowindex++;
        	}
        	$params->put("total_numbers",$rowindex);
      }
      else{
      	 $data = array(
              array(
                'req_datetime' => 'No Data'
            )
        );
      }
    //   // var_dump($sql);exit();

   


 ?>