<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    if(GET_DEPT == IPBM_DEP){
      $invalid_params = false;
      if($date_based == 'e.encounter_date'){
          if($patient_type_ipbm == IPBM_IPD){
             $invalid_params = true;
          }
          elseif(IPBM_patient_type == $patient_type_ipbm){
              $patient_type_ipbm = IPBM_OPD;  
          }
      }
      
      $enc_dept_cond = " AND (e.current_dept_nr IN ('". IPBM_DEP."') \n)";
       $sql = "SELECT c.description as description, 
            ed.CODE AS code,
            $age_bracket
            FROM care_encounter_diagnosis AS ed 
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr 
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code= ed.code
            INNER JOIN care_person AS p ON p.pid=e.pid 
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr 
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type_ipbm) 
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
            $enc_dept_cond
            $area_cond
            GROUP BY ed.code   
            ORDER BY COUNT(ed.CODE) DESC LIMIT $limit ";
        if($invalid_params){
          $sql = "";
        }
    }
    else{
      $sql = "SELECT c.description as descr, 
            ed.CODE AS subcode,  
              (SELECT IF(INSTR(ed.code,'.'), 
                SUBSTRING(ed.code, 1, 3), 
                    IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                        IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                            IF(INSTR(ed.code,'-'), 
                            SUBSTRING(ed.code, 1, 3),ed.code))))) AS code, 
            (SELECT description FROM care_icd10_en ic WHERE ic.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), IF(INSTR(ed.code,'/'), 
                    SUBSTRING(ed.code, 1, 5), IF(INSTR(ed.code,','), 
                    SUBSTRING(ed.code, 1, 3), IF(INSTR(ed.code,'-'), 
                    SUBSTRING(ed.code, 1, 3),ed.code)))))) AS description, 
            $age_bracket
            FROM care_encounter_diagnosis AS ed 
            INNER JOIN care_encounter AS e ON e.encounter_nr=ed.encounter_nr 
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                            SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                                SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code))))) 
            INNER JOIN care_person AS p ON p.pid=e.pid 
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr 
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr 
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr 
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr 
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void') 
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type) 
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
            $enc_dept_cond 
            $area_cond 
            GROUP BY 
                (SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code)))))
            ORDER BY COUNT(ed.CODE) DESC LIMIT $limit ";

    }

    // print_r($sql);die;
    #TITLE of the report
    $params->put("hospital_country", $hosp_country);  
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("area", $patient_type_label." (".$date_based_label.") from ".trim(mb_strtoupper($area)));
    $params->put("ipbm", IPBM_HEADER);
    // $params->put("area", $patient_type_label." (".$date_based_label.") from ".trim(mb_strtoupper($area)));
    $params->put("icd_class", $icd_class);
    $patient_type = $patient_type == null ? IPBM_IPD . "," . IPBM_OPD : $patient_type;

    #$base_date = 'DATE(e.encounter_date)';
    #$age_bdate = 'FLOOR((YEAR('.$base_date.') - YEAR(p.date_birth)) - (RIGHT('.$base_date.',5)<RIGHT(p.date_birth,5)))';
    // var_dump($type_nr);die;
    
           
    // echo $sql; 
    // exit();
    // print_r($sql);die;
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
            #$total_row = $row['male_total'] + $row['female_total'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'code' => $row['code'],
                              'description' => $row['description'], 
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
                              'male_total' => $male_total,
                              'female_total' => $female_total,
                              'total' => $total
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['code'] = NULL; 
    }     
    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );
    // $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
    // $params->put("ipbm_logo", $baseurl . "img/ipbm.png");   