<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", "");
    $params->put("column_name","OPERATIONS PERFORMED");
    
    $patient_type = '3,4';
    
    $sql = "SELECT DISTINCT IF (type_nr=1,'Primary','Secondary') AS icd_type, ed.code_parent,ed.CODE AS ICD, c.description AS 'ICD_Description', e.encounter_nr AS 'Case_No',
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ', IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
            e.er_opd_diagnosis AS 'Admitting_Diagnosis',
            e.admission_dt AS 'Date_Admitted',
            e.discharge_date AS 'Date_Discharged',
            IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth,
            IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            UPPER(p.sex) AS Sex,
            CONCAT(IF (TRIM(p.street_name) IS NULL,'',TRIM(p.street_name)),' ',
                IF (TRIM(sb.brgy_name) IS NULL,'',TRIM(sb.brgy_name)),' ',
                IF (TRIM(sm.mun_name) IS NULL,'',TRIM(sm.mun_name)),' ',
                IF (TRIM(sm.zipcode) IS NULL,'',TRIM(sm.zipcode)),' ',
                IF (TRIM(sp.prov_name) IS NULL,'',TRIM(sp.prov_name)),' ',
                IF (TRIM(sr.region_name) IS NULL,'',TRIM(sr.region_name))) AS 'Complete_Address',
            '' AS 'If Dengue', IF(d.death_date,d.death_date,IF(p.death_date!='0000-00-00' AND (p.death_encounter_nr=e.encounter_nr),p.death_date,NULL)) AS 'Fatality'
            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE
            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
            LEFT JOIN seg_cert_death AS d ON d.pid=e.pid
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type)
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
            AND IF(INSTR(c.diagnosis_code,'.'),
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) IN (SELECT IF(INSTR(icd_code,'.'),
            SUBSTR(icd_code,1,IF(INSTR(icd_code,'.'),INSTR(icd_code,'.')-1,0)),
            icd_code) AS cpd
            FROM seg_notifiable_code)
            ORDER BY ed.CODE, p.name_last, p.name_first, p.name_middle, DATE(e.admission_dt) ASC";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if(($row['date_birth']!='0000-00-00') && ($row['date_birth']!=NULL))
                $date_birth = date("m/d/Y",strtotime($row['date_birth']));
                
            if(($row['Date_Discharged']!='0000-00-00') && ($row['Date_Discharged']!=NULL))
                $Date_Discharged = date("m/d/Y",strtotime($row['Date_Discharged']));    
                
            if(($row['Fatality']!='0000-00-00') && ($row['Fatality']!=NULL))
                $row['Fatality'] = date("m/d/Y",strtotime($row['Fatality']));        
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'code_parent' => $row['code_parent'],
                              'ICD' => $row['ICD'],
                              'ICD_Description' => $row['ICD_Description'],
                              'Case_No' => $row['Case_No'],
                              'Full_Name' => utf8_decode(trim($row['Full_Name'])),
                              'Admitting_Diagnosis' => $row['Admitting_Diagnosis'],
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $Date_Discharged,
                              'date_birth' => $date_birth,
                              'Age' => $row['Age'],
                              'Sex' => $row['Sex'],
                              'Complete_Address' => $row['Complete_Address'],
                              'If_Dengue' => $row['If_Dengue'],
                              'Fatality' => $row['Fatality'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Case_No'] = NULL; 
    }       