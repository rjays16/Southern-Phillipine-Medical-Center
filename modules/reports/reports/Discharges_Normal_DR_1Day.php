<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", "");
    
    $patient_type = '3,4';
      //edited by kenneth 04-28-2016
    $sql = "SELECT DISTINCT 
  p.pid AS hrn,
  CONCAT(
    IF (
      TRIM(p.name_last) IS NULL,
      '',
      TRIM(p.name_last)
    ),
    ', ',
    IF(
      TRIM(p.name_first) IS NULL,
      '',
      TRIM(p.name_first)
    ),
    ' ',
    IF(
      TRIM(p.name_middle) IS NULL,
      '',
      TRIM(p.name_middle)
    )
  ) AS 'Full_Name',
  e.admission_dt AS 'Date_Admitted',
  CONCAT(
    e.discharge_date,
    ' ',
    e.discharge_time
  ) AS 'Date_Discharged',
  TIMESTAMPDIFF(
    HOUR,
    e.admission_dt,
    CONCAT(
      e.discharge_date,
      ' ',
      e.discharge_time
    )
  ) / 24 AS admission_duration,
  IF (
    fn_calculate_age (NOW(), p.date_birth),
    fn_get_age (NOW(), p.date_birth),
    age
  ) AS Age,
  e.er_opd_diagnosis,
  UPPER(p.sex) AS Sex,
  UPPER(
    IF (
      e.current_att_dr_nr,
      fn_get_personell_name (e.current_att_dr_nr),
      fn_get_personell_name (e.consulting_dr_nr)
    )
  ) AS 'Attending_Physician',
  res.result_desc AS 'Remarks',
  DATEDIFF(
    e.discharge_date,
    e.admission_dt
  ) + 1 AS 'Length_of_Stay',
  ed.CODE AS 'ICD 10' 
FROM
  care_encounter AS e 
  INNER JOIN care_person AS p 
    ON p.pid = e.pid 
  LEFT JOIN care_encounter_diagnosis AS ed 
    ON ed.encounter_nr = e.encounter_nr 
  INNER JOIN care_icd10_en AS c 
    ON c.diagnosis_code = ed.CODE 
  LEFT JOIN care_encounter_procedure AS ep 
    ON ep.encounter_nr = e.encounter_nr 
  LEFT JOIN seg_encounter_result AS ser 
    ON ser.encounter_nr = e.encounter_nr 
  INNER JOIN seg_results AS res 
    ON res.result_code = ser.result_code 
WHERE DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
  AND ed.CODE IN ($delivery_type) 
  AND (
    TIMESTAMPDIFF(
      HOUR,
      e.admission_dt,
      CONCAT(
        e.discharge_date,
        ' ',
        e.discharge_time
      )
    ) IS NOT NULL 
    AND TIMESTAMPDIFF(
      HOUR,
      e.admission_dt,
      CONCAT(
        e.discharge_date,
        ' ',
        e.discharge_time
      )
    ) > 0
  ) 
  AND TIMESTAMPDIFF(HOUR, e.admission_dt, CONCAT(e.discharge_date, ' ', e.discharge_time))/24 $discharge_days
  AND ed.status != 'deleted'
  AND ed.type_nr in ($type_nr)
ORDER BY DATE(e.admission_dt) ASC,
  p.name_last,
  p.name_first,
  p.name_middle";
  //end kenneth
  // var_dump($sql);die();
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
                              'Full_Name' => utf8_decode(trim($row['Full_Name'])),
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $Date_Discharged,
                              'Age' => $row['Age'],
                              'Sex' => $row['Sex'],
                              'Attending_Physician' => utf8_decode(trim($row['Attending_Physician'])),
                              'Remarks' => $row['Remarks'],
                              'Length_of_Stay' => number_format($row['admission_duration'], 2)." days",
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['hrn'] = NULL; 
    }       