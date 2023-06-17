<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", IPBM_HEADER);
    $params->put("title", mb_strtoupper("Summary of Readmitted Patients within 7 days from Date Discharged"));

    
    $patient_type = IPBM_IPD;
    $date_based = 'ce.`admission_dt`';
    $sql = "SELECT 
  ce.pid as hrn,
  fn_get_pid_lastfirstmi (ce.pid) AS full_name,
  ce.`admission_dt` AS Date_Admitted,
  ce.`discharge_date` AS Date_Discharged,
  ce2.`admission_dt` as readmission_dt,
  ABS(
    DATEDIFF(
      DATE(ce.`discharge_date`),
      DATE(ce2.`admission_dt`)
    )
  ) AS days_readmission,
  MONTHNAME(ce.`admission_dt`)  AS month_name

           FROM
  care_encounter ce 
  LEFT JOIN care_encounter ce2 
    ON ce.pid = ce2.pid 
    AND ce.`encounter_nr` != ce2.`encounter_nr` 
    AND ce.`admission_dt` < ce2.`admission_dt` 
  INNER JOIN care_person AS p 
    ON p.pid = ce.pid 
  LEFT JOIN seg_encounter_result AS ser 
    ON ser.encounter_nr = ce.encounter_nr 
  INNER JOIN seg_results AS res 
    ON res.result_code = ser.result_code 
            WHERE DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ce.encounter_type IN ($patient_type)  AND ce2.encounter_type IN ($patient_type)
            AND ce.STATUS NOT IN ('deleted','hidden','inactive','void')   AND ABS(
    DATEDIFF(
      DATE(ce.`discharge_date`),
      DATE(ce2.`admission_dt`)
    )
  ) <= 7  ORDER BY DATE(ce.`admission_dt`)
           ";
           
//     echo $sql;
//     exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if(($row['Date_Discharged']!='0000-00-00') && ($row['Date_Discharged']!=NULL))
                $Date_Discharged = date("m/d/Y",strtotime($row['Date_Discharged']));    
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'hrn' => $row['hrn'],
                              'Full_Name' => utf8_decode(trim($row['full_name'])),
                              'Date_Admitted' => date("m/d/Y",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $Date_Discharged,
                              'readmission' =>   date("m/d/Y",strtotime($row['readmission_dt'])),
                              'day_before_readmission' => $row['days_readmission'],
                              'month' => $row['month_name'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['hrn'] = NULL; 
    }     