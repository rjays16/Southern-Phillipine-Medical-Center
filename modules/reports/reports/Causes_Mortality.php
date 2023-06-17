<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", "");
    $params->put("column_name","ICD DESCRIPTION");
    $params->put("column_code","ICD CODE");
    

    if(GET_DEPT==IPBM_DEP){
            $patient_type = $psy_patienttype_mortality;
              $baseurl = sprintf(
          "%s://%s%s",
          isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
          $_SERVER['SERVER_ADDR'],
          substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
      );
       $params->put("header", IPBM_HEADER);
        $params->put("department", $date_based_label.$icd_class);
         $params->put("title", $report_title);
        $params->put("hospital_country", mb_strtoupper($hosp_country));
        $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
        $params->put("ipbm_logo", $baseurl . "img/ipbm_new.png");
      $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
      $params->put("ipbm_logo", $baseurl . "img/ipbm.png"); 
      $seg_billing_encounter = ' INNER JOIN  seg_billing_encounter fb on fb.encounter_nr = e.encounter_nr ' ;
      $is_final = " AND fb.is_final ='1'  AND fb.is_deleted IS NULL ";
    }else{
              $patient_type = '3,4';
    }
  
    
    $sql = "SELECT (SELECT IF(INSTR(ed.code,'.'), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'/'), 
                                    SUBSTRING(ed.code, 1, 5), 
                                    IF(INSTR(ed.code,','), 
                                        SUBSTRING(ed.code, 1, 3), 
                                        IF(INSTR(ed.code,'-'), 
                                    SUBSTRING(ed.code, 1, 3),ed.code))))) AS CODE,
            /*COUNT(ed.code) AS occurrence, */ 
            c.description,
            SUM(CASE WHEN ins.hcare_id=18 THEN 1 ELSE 0 END) AS phic_occurrence,
            SUM(CASE WHEN ins.hcare_id<>18 OR ins.hcare_id IS NULL THEN 1 ELSE 0 END) AS nonphic_occurrence
            FROM  care_encounter_diagnosis AS ed
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr
           $seg_billing_encounter
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'/'), 
                                    SUBSTRING(ed.code, 1, 5), 
                                    IF(INSTR(ed.code,','), 
                                        SUBSTRING(ed.code, 1, 3), 
                                        IF(INSTR(ed.code,'-'), 
                                        SUBSTRING(ed.code, 1, 3),ed.code)))))
            INNER JOIN care_person AS cp ON cp.pid=e.pid AND cp.death_encounter_nr=e.encounter_nr
            LEFT JOIN seg_encounter_result AS r ON r.encounter_nr = e.encounter_nr
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND r.result_code IN (4,8,9,10) $is_final
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type)
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
            GROUP BY 
                        (SELECT IF(INSTR(ed.code,'.'), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'/'), 
                                SUBSTRING(ed.code, 1, 5), 
                                    IF(INSTR(ed.code,','), 
                                    SUBSTRING(ed.code, 1, 3), 
                                        IF(INSTR(ed.code,'-'), 
                                        SUBSTRING(ed.code, 1, 3),ed.code))))) 
            ORDER BY COUNT(ed.code) DESC";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $occurrence = (int) $row['phic_occurrence'] + (int) $row['nonphic_occurrence'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'CODE' => $row['CODE'],
                              'occurrence' => (int) $occurrence,
                              'description' => $row['description'],
                              'phic_occurrence' => (int) $row['phic_occurrence'],
                              'nonphic_occurrence' => (int) $row['nonphic_occurrence'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['CODE'] = NULL; 
    }       