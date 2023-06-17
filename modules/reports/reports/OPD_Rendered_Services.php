<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $orientation_header?$orientation_header:'ALL');
    $ipbm_opd = ' AND d.name_formal != ("IPBM") ';
    $patient_type = '2';
    
     $sql_view_cases = "INSERT INTO seg_report_cases_census
                            SELECT e.pid, COUNT(*) AS no_encounters
                            FROM care_encounter e
                            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                            AND e.encounter_type IN ($patient_type) 
                            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
                            GROUP BY e.pid"; 
                        
     $ok_cases = $db->Execute("TRUNCATE seg_report_cases_census");                
     if ($ok_cases)
        $ok_cases = $db->Execute($sql_view_cases);
        
    #no of patients
    $sql_no_patient = "SELECT COUNT(*) AS no_patients FROM seg_report_cases_census";                          
    $no_patients = $db->GetOne($sql_no_patient);
    
    #no of patients admitted from opd
    $sql_no_patient_ipdopd = "SELECT COUNT(*) AS no_patient_ipdopd
                                FROM care_person p
                                INNER JOIN care_encounter e ON e.pid=p.pid
                                LEFT JOIN care_department AS d 
                                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
                                LEFT JOIN seg_report_cases_census c ON c.pid=e.pid
                                WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                ".$consul_insti."
                                ".$ipbm_opd."
                                AND e.encounter_type IN (4) 
                                AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format);
    $no_patient_ipdopd = $db->GetOne($sql_no_patient_ipdopd);                            
    
    $sql_no_weekdays = "SELECT 5 * (DATEDIFF(".$db->qstr($to_date_format).", ".$db->qstr($from_date_format).") DIV 7) + 
                        MID('0123444401233334012222340111123400012345001234550', 7 * WEEKDAY(".$db->qstr($from_date_format).") + WEEKDAY(".$db->qstr($to_date_format).") + 1, 1) AS no_weekdays";                                
    $no_weekdays = $db->GetOne($sql_no_weekdays);
    $no_weekdays = (int)$no_weekdays;
    $sql = "SELECT  d.name_formal AS Type_Of_Service, 
            SUM(CASE WHEN c.no_encounters <= 1 THEN 1 ELSE 0 END) AS new_patient,
            SUM(CASE WHEN c.no_encounters > 1 THEN 1 ELSE 0 END) AS revisit            
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
              ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_report_cases_census c ON c.pid=e.pid
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type)
            ".$consul_insti." 
            ".$ipbm_opd." 
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            GROUP BY d.name_formal
            ORDER BY d.name_formal";
     
    #exit();
    $rs = $db->Execute($sql);

    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $total = (int) $row['new_patient'] + (int) $row['revisit'];
            $grand_total += $total;
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Type_Of_Service' => $row['Type_Of_Service'], 
                          'new' => (int) $row['new_patient'],
                          'revisit' => (int) $row['revisit'],
                          'total' => (int) $total,
                          );
            
           
            $rowindex++;
        }  
          #$grand_total = (int) $grand_total;
          $avg = round($grand_total / ($no_weekdays-$no_holidays));
          $params->put("avg", (int)$avg);
          $params->put("grand_total", (int) $grand_total);
          $params->put("no_weekdays", (int) $no_weekdays);
          $params->put("no_holidays", (int) $no_holidays);
          $params->put("no_patients", (int) $no_patients);
          $params->put("no_patient_ipdopd", (int) $no_patient_ipdopd);
    }else{
        $data[0]['id'] = NULL; 
    }  
