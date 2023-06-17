<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    $enctypecond = '';
    
    #TITLE of the report
    if($_GET['dept_nr']==182){
        $patient_type = $patient_type_ipbm;

        if($patient_type == IPBM_patient_type){
             if(strtolower($date_based_label) == 'based on consultation date'){
                $patient_type = IPBM_OPD; 
          }elseif (strtolower($date_based_label) == 'based on admission date') {
              $patient_type = IPBM_IPD; 
          }
        }
        
      $enc_dept_cond = " AND (e.current_dept_nr IN (".IPBM_DEP.") \n".
                                        " OR e.current_dept_nr IN ( \n".
                                            " SELECT nr FROM care_department AS d WHERE d.parent_dept_nr IN (".IPBM_DEP."))) ";
      $dept_label = $ipbm_header;
      if(empty($patient_type)){
          if(strtolower($date_based_label) == 'based on consultation date'){
                $patient_type = IPBM_OPD; 
          }elseif (strtolower($date_based_label) == 'based on admission date') {
              $patient_type = IPBM_IPD; 
          }else {
            $patient_type = IPBM_IPD; 
          }
        $patient_type = IPBM_IPD;
        $header_patient = "ALL Patients";
      }
      if(strtolower($date_based_label) == 'based on consultation date'){
          if($patient_type == IPBM_OPD){
            $patient_type = IPBM_OPD; 
          }else{
            $patient_type = 'invalid'; 
          }
      }
       if(strtolower($date_based_label) == 'based on admission date'){
        if($patient_type == IPBM_IPD){
            $patient_type = IPBM_IPD; 
          }else{
            $patient_type = 'invalid'; 
          }
      }
      
      $enctypecond = " AND ed.encounter_type IN (".$patient_type.")";
      

  
            $params->put('p_type', $patient_type_label);
            $params->put("hospital_country", mb_strtoupper($hosp_country));
            $params->put("hospital_name", mb_strtoupper($hosp_name));
            $params->put("header", IPBM_HEADER);
            $params->put("icd_code","ICD CODES ".$icd_class);
            $params->put("title", $report_title);
            $params->put("date_based",(($date_based_label) ? ucfirst($date_based_label) : "Based on  Admission Date"));
             $params->put("icd_class", $icd_class);
    }
    else{
        $date_based = 'e.discharge_date';
        $params->put("hospital_name", mb_strtoupper($hosp_name));
        $params->put("header", $report_title);
        $params->put("department", $dept_label);
        $params->put("icd_class", $icd_class);

    }
    
    
    
    $sql = "SELECT ed.code AS subcode,  
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
            SUM(CASE WHEN (sr.result_code IN (1,5)) AND sd.disp_code IN (2,7) AND p.sex = 'm' THEN 1 ELSE 0 END) AS Recovered_M,
            SUM(CASE WHEN (sr.result_code IN (1,5)) AND sd.disp_code IN (2,7) AND p.sex = 'f' THEN 1 ELSE 0 END) AS Recovered_F,
            SUM(CASE WHEN (sr.result_code IN (2,6)) AND sd.disp_code IN (2,7) AND p.sex = 'm' THEN 1 ELSE 0 END) AS Improved_M,
            SUM(CASE WHEN (sr.result_code IN (2,6)) AND sd.disp_code IN (2,7) AND p.sex = 'f' THEN 1 ELSE 0 END) AS Improved_F,
            SUM(CASE WHEN (sr.result_code IN (3,7)) AND sd.disp_code IN (2,7) AND p.sex = 'm' THEN 1 ELSE 0 END) AS Unimproved_M,
            SUM(CASE WHEN (sr.result_code IN (3,7)) AND sd.disp_code IN (2,7) AND p.sex = 'f' THEN 1 ELSE 0 END) AS Unimproved_F,
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
            
            LEFT JOIN seg_icd_10_morbidity_tabular t ON t.diagnosis_code=(SELECT IF(INSTR(ed.code,'.'), 
                    SUBSTRING(ed.code, 1, 3), 
                        IF(INSTR(ed.code,'/'), 
                        SUBSTRING(ed.code, 1, 5), 
                            IF(INSTR(ed.code,','), 
                            SUBSTRING(ed.code, 1, 3), 
                                IF(INSTR(ed.code,'-'), 
                                SUBSTRING(ed.code, 1, 3),ed.code)))))
                                
            WHERE ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND (DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).")
            AND ed.type_nr IN ($type_nr) 
            ".$enctypecond." 
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
            ORDER BY SUM(CASE WHEN 1 THEN 1 ELSE 0 END) DESC LIMIT $limit";
           
    // echo $sql; 
    // exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $Total_M = (int) $row['Recovered_M'] + (int) $row['Improved_M'] + (int) $row['Unimproved_M']; 
            $Total_F = (int) $row['Recovered_F'] + (int) $row['Improved_F'] + (int) $row['Unimproved_F'];
            $Total = $Total_M + $Total_F;
            
            if ($row['tab_index'])
                $tab_index = $row['tab_index'];
            else    
                $tab_index = $row['code'];
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'diagnosis' => $row['description'],
                              'code' => $row['code'],
                              'ICD' => $row['subcode'],
                              'Recovered_M' => (int) $row['Recovered_M'],
                              'Recovered_F' => (int) $row['Recovered_F'],
                              'Improved_M' => (int) $row['Improved_M'],
                              'Improved_F' => (int) $row['Improved_F'],
                              'Unimproved_M' => (int) $row['Unimproved_M'],
                              'Unimproved_F' => (int) $row['Unimproved_F'],
                              'Total_M' => (int) $Total_M,
                              'Total_F' => (int) $Total_F,
                              'Total' => (int) $Total,
                              'tab_index' => $tab_index,
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['CODE'] = NULL; 
    }       
    $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );

$params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put("ipbm_logo", $baseurl . "img/ipbm_new.png"); 