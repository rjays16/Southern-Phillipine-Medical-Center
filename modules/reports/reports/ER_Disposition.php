<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put('header',ER_DISP_HEADER);
    $params->put("hosp_country",$hosp_country);
    $params->put("doh",$hosp_agency);
    $params->put("hosp_addr",$hosp_agency);

    

    $dept_name = "SELECT fn_get_department_name(".$get_dept.")";                          
    $name = $db->GetOne($dept_name);

    $params->put("department",!empty($get_dept)? $name : 'All Department');
    
    
  $sql = "SELECT 
         e.pid AS hrn,
      fn_get_pid_lastfirstmi (e.pid) AS full_name,
      fn_get_department_name (e.current_dept_nr) AS attending_dept,
      stc.roman_id AS category,
      e.encounter_date,
    IFNULL(e.discharge_date,sdsi.discharge_date) as discharge_date,
    IFNULL(e.discharge_time,sdsi.discharge_time) as discharge_time,
         CASE
    WHEN (
      e.discharge_time IS NOT NULL 
      AND sed.disp_code NOT IN ('1')
    ) 
    OR sdsi.discharge_time IS NOT NULL 
    THEN 'DISCHARGED'
    WHEN 
      e.discharge_time IS NULL 
    AND  sdsi.discharge_time IS  NULL 
    THEN ''
    ELSE 'ADMITTED' 
  END AS type_dispo,
       IFNULL(
    FORMAT(
      
        ROUND(
          TIMESTAMPDIFF(
            SECOND,
            e.encounter_date,
            CONCAT(
              e.discharge_date,
              ' ',
              e.discharge_time
            )
          ) / 3600.0,
          2
        ),
      2
    ),
    FORMAT(
    
        ROUND(
          TIMESTAMPDIFF(
            SECOND,
            e.encounter_date,
            CONCAT(
              sdsi.discharge_date,
              ' ',
              sdsi.discharge_time
            )
          ) / 3600.0,
          2
        ),
      2
    )
  ) AS los 
                FROM care_encounter AS e 
              LEFT JOIN seg_triage_category AS stc 
                   ON e.category = stc.category_id 
              LEFT JOIN seg_encounter_disposition AS sed 
                   ON sed.encounter_nr = e.`encounter_nr` 
             LEFT JOIN `seg_discharge_slip_info` AS sdsi 
                   ON sdsi.`encounter_nr` = e.`encounter_nr` 
              WHERE e.STATUS NOT IN (
                    'deleted',
                    'hidden',
                    'inactive',
                    'void'
                  ) $category_triage $attending_dept
                  AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                  AND e.encounter_type IN (".$db->qstr(ER).") ORDER BY full_name";
                  // var_dump($sql);exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
              if(!empty($row['discharge_time'])){
                $discharge_datetime = date("m/d/Y h:i A",strtotime($row['discharge_date']." ".$row['discharge_time']));

              }
              else{
                $discharge_datetime = '';
              }
            $data[$rowindex] = array('rowindex' => $rowindex,
                                    'hrn' => $row['hrn'],
                                    'Full_Name'=>utf8_decode(strtoupper($row['full_name'])),
                                    'AttendingDept'=>strtoupper($row['attending_dept']),
                                    'ESI'=>strtoupper($row['category']),
                                    'Date_Admitted'=> date("m/d/Y h:i A",strtotime($row['encounter_date'])),
                                    'dt_disposition'=> $discharge_datetime ,
                                    'type_dispo'=>strtoupper($row['type_dispo']),
                                    'los'=>$row['los']
                              
                              );
                              
           $rowindex++;
        }  
    }  
