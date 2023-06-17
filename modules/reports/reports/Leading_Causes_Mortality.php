<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $dept_label);
    #$params->put("area", $patient_type_label." (".$date_based_label.") from ".trim(mb_strtoupper($area)));
    $params->put("icd_class", $icd_class);
    
    #$base_date = 'DATE(e.admission_dt)';
    #$age_bdate = 'FLOOR((YEAR('.$base_date.') - YEAR(p.date_birth)) - (RIGHT('.$base_date.',5)<RIGHT(p.date_birth,5)))';
    #override value
    
    
    
    if (GET_DEPT == IPBM_DEP) {

      $params->put("hospital_country", mb_strtoupper($hosp_country));
      $params->put('p_type', $patient_type_label);
      $params->put("department", $ipbm_header);
      $params->put("icd_class","ICD CODES ".$icd_class);
      $params->put("date_based",(($date_based_label) ? ucfirst("Based on " . $date_based_label) : "Based on  Admission Date"));
      $condition = " LEFT JOIN seg_encounter_profile as sep ON e.encounter_nr = sep.encounter_nr";
      $patient_type = IPBM_IPD;
      $age_bracket = $age_bracket_ipbm;
      $baseurl = sprintf(
          "%s://%s%s",
          isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
          $_SERVER['SERVER_ADDR'],
          substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
      );
      $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
      $params->put("ipbm_logo", $baseurl . "img/ipbm.png"); 
    }else{
      $date_based = 'e.discharge_date';
    }
    $age_bdate = 'IF(p.date_birth="0000-00-00",0,FLOOR((YEAR('.$date_based.') - YEAR(p.date_birth)) - (RIGHT('.$date_based.',5)<RIGHT(p.date_birth,5))))';
    $sql = "SELECT c.description as descr, 
            ed.code AS subcode,  
              (SELECT IF(INSTR(ed.code,'.'), 
                SUBSTRING(ed.code, 1, 3), 
                    IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                        IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                            IF(INSTR(ed.code,'-'), 
                            SUBSTRING(ed.code, 1, 3),ed.code))))) AS code, 
            IF(t.description IS NOT NULL,t.description,
            (SELECT description FROM care_icd10_en ic WHERE ic.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), IF(INSTR(ed.code,'/'), 
                    SUBSTRING(ed.code, 1, 5), IF(INSTR(ed.code,','), 
                    SUBSTRING(ed.code, 1, 3), IF(INSTR(ed.code,'-'), 
                    SUBSTRING(ed.code, 1, 3),ed.code))))))) AS description, 
            
            $age_bracket,
            
            t.tab_code AS tab_index
            FROM care_encounter_diagnosis AS ed
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code = (SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                            SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                                SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code)))))
            INNER JOIN care_encounter AS e ON e.encounter_nr = ed.encounter_nr
            INNER JOIN care_person AS p ON p.pid = e.pid
            LEFT JOIN seg_encounter_result AS sr ON sr.encounter_nr = ed.encounter_nr
            LEFT JOIN seg_encounter_disposition AS sd ON sd.encounter_nr = ed.encounter_nr
            
            LEFT JOIN $table_tab_code t ON t.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code)))))
            $condition
            WHERE ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type) 
            AND sr.result_code IN (".DIED_E.",".DIED_A.",".AUTOPSY.",".NONAUTOPSY.")
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]' 
            $enc_dept_cond 
            GROUP BY   
               IF(t.tab_code IS NOT NULL, t.tab_code,
                            (SELECT IF(INSTR(ed.code,'.'), 
                                SUBSTRING(ed.code, 1, 3), 
                                    IF(INSTR(ed.code,'/'), 
                                    SUBSTRING(ed.code, 1, 5), 
                                        IF(INSTR(ed.code,','), 
                                        SUBSTRING(ed.code, 1, 3), 
                                            IF(INSTR(ed.code,'-'), 
                                            SUBSTRING(ed.code, 1, 3),ed.code))))))
            ORDER BY total DESC LIMIT $limit";
           
    // echo $sql; exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){

            #comment by KENTOOT ---------
            /*$male_total = (int) $row['male_below1'] + (int) $row['male_1to4'] + (int) $row['male_5to9']
                          + (int) $row['male_10to14'] + (int) $row['male_15to19'] +  (int) $row['male_20to44']
                          + (int) $row['male_45to59'] + (int) $row['male_60up'];
            $female_total = (int) $row['female_below1'] + (int) $row['female_1to4'] + (int) $row['female_5to9']
                          + (int) $row['female_10to14'] + (int) $row['female_15to19'] +  (int) $row['female_20to44']
                          + (int) $row['female_45to59'] + (int) $row['female_60up'];
            $total = $male_total + $female_total;*/
            #----------------------------

            #update by KENTOOT 08/20/2014 -------------------------------------------------------------------
            $male_total = (int) $row['male_days_in'] + (int) $row['male_days_out'] 
                          + (int) $row['male_below1'] + (int) $row['male_1to4'] + (int) $row['male_5to9']
                          + (int) $row['male_10to14'] + (int) $row['male_15to19'] +  (int) $row['male_20to44']
                          + (int) $row['male_45to59'] + (int) $row['male_60up'];
            $female_total = (int) $row['female_days_in'] + (int) $row['female_days_out'] 
                          + (int) $row['female_below1'] + (int) $row['female_1to4'] 
                          + (int) $row['female_below1'] + (int) $row['female_1to4'] + (int) $row['female_5to9']
                          + (int) $row['female_10to14'] + (int) $row['female_15to19'] +  (int) $row['female_20to44']
                          + (int) $row['female_45to59'] + (int) $row['female_60up'];
            $total = $male_total + $female_total;
            
            if ($row['tab_index'])
                $tab_index = $row['tab_index'];
            else    
                $tab_index = $row['code'];
            
            if($row['male_days_in']=='')
              $row['male_days_in'] =='0';
            elseif($row['female_days_in']=='')
              $row['female_days_in'] =='0';
            elseif($row['male_days_out']=='')
              $row['male_days_out'] =='0';
            elseif($row['female_days_out']=='')
              $row['female_days_out'] =='0';
                
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'code' => $row['code'],
                              'description' => $row['description'], 
                              'male_days_in' => (int) $row['male_days_in'],
                              'female_days_in' => (int) $row['female_days_in'],
                              'male_days_out' => (int) $row['male_days_out'],
                              'female_days_out' => (int) $row['female_days_out'],
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
                              'male_total' => (int) $row['male_total'],
                              'female_total' => (int) $row['female_total'],
                              'total' => (int) $row['total'],
                              'tab_index' => $tab_index,
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['code'] = NULL; 
    }     