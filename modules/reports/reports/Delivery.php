<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", "");

    DEFINE ('ADMITTED','3,4');
    
    $sql = "SELECT ed.CODE AS icd10code, d.icd_10 AS CODE, c.description AS descr, d.diagnosis AS description,
            $age_bracket
            FROM care_encounter_diagnosis AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE
            INNER JOIN care_person AS p ON p.pid=e.pid
            INNER JOIN seg_icd_10_deliveries AS d ON (d.icd_10=ed.CODE
              OR d.icd_10 = (SELECT IF(INSTR(ed.CODE,'.'), 
                                SUBSTRING(ed.CODE, 1, 3), 
                                    IF(INSTR(ed.CODE,'/'), 
                                    SUBSTRING(ed.CODE, 1, 5), 
                                        IF(INSTR(ed.CODE,','), 
                                        SUBSTRING(ed.CODE, 1, 3), 
                                            IF(INSTR(ed.CODE,'-'), 
                                            SUBSTRING(ed.CODE, 1, 3),ed.CODE))))))
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN (".ADMITTED.")
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
                        c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]' 
            GROUP BY d.icd_10
            ORDER BY d.diagnosis";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $male_total = (int) $row['male_below1'] + (int) $row['male_1to4'] + (int) $row['male_5to9']
                          + (int) $row['male_10to14'] + (int) $row['male_15to19'] +  (int) $row['male_20to44']
                          + (int) $row['male_45to59'] + (int) $row['male_60up'];
            $female_total = (int) $row['female_below1'] + (int) $row['female_1to4'] + (int) $row['female_5to9']
                          + (int) $row['female_10to14'] + (int) $row['female_15to19'] +  (int) $row['female_20to44']
                          + (int) $row['female_45to59'] + (int) $row['female_60up'];
            $total = $male_total + $female_total;
            
            $grand_total += $total;
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'description' => $row['description'], 
                              'code' => $row['CODE'], 
                              'male_below1' => (int) $row['male_below1'],
                              'female_below1' => (int) $row['female_below1'],
                              'male_1to4' => (int) $row['male_1to4'],
                              'female_1to4' => (int) $row['female_1to4'],
                              'male_5to9' => (int) $row['male_5to9'],
                              'female_5to9' => (int) $row['female_5to9'],
                              'male_10to14' => (int) $row['male_10to14'],
                              'female_10to14' => (int) $row['female_10to14'],
                              'male_15to19' => (int) $row['male_15to19'],
                              'female_15to19' => (int) $row['female_15to19'],
                              'male_20to44' => (int) $row['male_20to44'],
                              'female_20to44' => (int) $row['female_20to44'],
                              'male_45to59' => (int) $row['male_45to59'],
                              'female_45to59' => (int) $row['female_45to59'],
                              'male_60up' => (int) $row['male_60up'],
                              'female_60up' => (int) $row['female_60up'],
                              'male_total' => (int) $male_total,
                              'female_total' => (int) $female_total,
                              'total' => (int) $total
                              );
                              
           $rowindex++;
        }  
          
    }else{
        $data[0]['code'] = NULL; 
    }     