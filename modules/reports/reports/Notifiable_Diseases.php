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

    if(GET_DEPT==IPBM_DEP){
        // $patient_type = IPBM_IPD;
        $baseurl = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_ADDR'],
            substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
        );
        $params->put('ipbm_header',IPBM_HEADER);
        $params->put('date_based',$date_based_label);

        $patient_type = $psy_patient_type;

        $params->put("icd_code","ICD CODES ".$icd_class);
        $params->put("doh", $baseurl ."gui/img/logos/dmc_logo.jpg");
        $params->put("ipbm_logo", $baseurl ."img/ipbm_new.png");

        $seg_billing_encounter = '';
        $is_final = '';

        if($patient_type==IPBMIPD_enc){
          $admitted_column= "e.admission_dt AS 'Date_Admitted'";
          $seg_billing_encounter = ' INNER JOIN  seg_billing_encounter fb on fb.encounter_nr = e.encounter_nr ' ;
          $is_final = " AND fb.is_final = '1' AND fb.is_deleted IS NULL";
        }elseif ($patient_type==IPBMOPD_enc) {
          $admitted_column= "e.encounter_date AS 'Date_Admitted'";
        }
      
        if($date_based=='e.discharge_date'){
          $admitted_column= " IFNULL(e.admission_dt,e.encounter_date) AS 'Date_Admitted' ";
          $patient_type =  IPBMIPD_enc;
        }
           // var_dump($patient_type);exit();
        $profile_join = " LEFT JOIN `seg_encounter_profile` AS sep ON sep.encounter_nr = e.encounter_nr";
        $profile_old_join = " LEFT JOIN `seg_encounter_profile_old` AS sep1 ON sep.encounter_nr = sep1.encounter_nr";
        $date_birth_column = "IF(p.date_birth!='0000-00-00',IFNULL(sep.date_birth,p.date_birth),NULL) AS date_birth";
        $sex_column = 'UPPER(IFNULL(sep1.`sex`,sep.sex)) AS Sex';
        $age_column = 'IF (fn_calculate_age(NOW(),IFNULL(sep.date_birth,p.date_birth)),fn_get_age(NOW(),IFNULL(sep.date_birth,p.date_birth)),age) AS Age';
        $address_join = ' LEFT JOIN seg_municity mun ON mun.mun_nr=IFNULL(sep.`mun_nr`,sep1.mun_nr) LEFT JOIN seg_provinces sp ON sp.prov_nr=IFNULL(sep.`prov_nr`,sep1.`prov_nr`) LEFT JOIN seg_regions sr ON sr.region_nr=IFNULL(sep.`region_nr` , sep1.`region_nr`)';
        $address_column = 'IF(sep.is_new,CONCAT(IF (TRIM(sep.street_name) IS NULL,\'\',TRIM(sep.street_name)),\' \',
                  IF (TRIM(sb.brgy_name) IS NULL,\'\',TRIM(sb.brgy_name)),\' \',
                  IF (TRIM(mun.mun_name) IS NULL,\'\',TRIM(mun.mun_name)),\' \',
                  IF (TRIM(mun.zipcode) IS NULL,\'\',TRIM(mun.zipcode)),\' \',
                  IF (TRIM(sp.prov_name) IS NULL,\'\',TRIM(sp.prov_name)),\' \',
                  IF (TRIM(sr.region_name) IS NULL,\'\',TRIM(sr.region_name))),
                  
                  CONCAT(IF (TRIM(sep1.street_name) IS NULL,\'\',TRIM(sep1.street_name)),\' \',
                  IF (TRIM(sb.brgy_name) IS NULL,\'\',TRIM(sb.brgy_name)),\' \',
                  IF (TRIM(mun.mun_name) IS NULL,\'\',TRIM(mun.mun_name)),\' \',
                  IF (TRIM(mun.zipcode) IS NULL,\'\',TRIM(mun.zipcode)),\' \',
                  IF (TRIM(sp.prov_name) IS NULL,\'\',TRIM(sp.prov_name)),\' \',
                  IF (TRIM(sr.region_name) IS NULL,\'\',TRIM(sr.region_name)))) AS \'Complete_Address\'';
        $brgy_column = 'LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=IFNULL(sep.brgy_nr,sep1.brgy_nr)';
        $result_join = ' LEFT JOIN seg_results AS res ON res.result_code=ser.result_code';

    }else{
        $patient_type = '3,4';
        $date_birth_column = "IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth";
        $sex_column = 'UPPER(p.sex) AS Sex';
        $admitted_column= "e.admission_dt AS 'Date_Admitted'";
        $age_column = 'IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age';
        $address_join = ' LEFT JOIN seg_municity mun ON mun.mun_nr=p.mun_nr LEFT JOIN seg_provinces sp ON sp.prov_nr= mun.`prov_nr` LEFT JOIN seg_regions sr ON sr.region_nr=sp.`region_nr`';
        $address_column = 'CONCAT(IF (TRIM(p.street_name) IS NULL,\'\',TRIM(p.street_name)),\' \',
                  IF (TRIM(sb.brgy_name) IS NULL,\'\',TRIM(sb.brgy_name)),\' \',
                  IF (TRIM(mun.mun_name) IS NULL,\'\',TRIM(mun.mun_name)),\' \',
                  IF (TRIM(mun.zipcode) IS NULL,\'\',TRIM(mun.zipcode)),\' \',
                  IF (TRIM(sp.prov_name) IS NULL,\'\',TRIM(sp.prov_name)),\' \',
                  IF (TRIM(sr.region_name) IS NULL,\'\',TRIM(sr.region_name))) AS \'Complete_Address\'';
        $brgy_column = 'LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr';
        $result_join = ' INNER JOIN seg_results AS res ON res.result_code=ser.result_code';
    }
    

    $sql = "SELECT DISTINCT IF (type_nr=1,'Primary','Secondary') AS icd_type, 
            ed.code_parent,ed.CODE AS ICD, c.description AS 'ICD_Description',
            e.encounter_nr AS 'Case_No',
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
            IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
            IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
           $admitted_column, CONCAT(e.discharge_date, ' ', e.discharge_time) AS 'Date_Discharged',
            $date_birth_column,
            $age_column,
            e.er_opd_diagnosis,
           $sex_column,
            $address_column,
            res.result_desc AS 'Remarks',
            UPPER(IF (e.current_att_dr_nr,
            fn_get_personell_name(e.current_att_dr_nr),fn_get_personell_name(e.consulting_dr_nr))) AS 'Attending_Physician'
            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            $seg_billing_encounter
            LEFT JOIN care_encounter_diagnosis AS ed ON ed.encounter_nr=e.encounter_nr
            INNER JOIN care_icd10_en AS c ON c.diagnosis_code=ed.CODE
         	$profile_join
          $profile_old_join
            $brgy_column
            $address_join

            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = ed.encounter_nr
            $result_join
            $condition
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND (DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).")
            AND ed.type_nr IN ($type_nr) 
            AND ed.encounter_type IN ($patient_type)
            $is_final
            AND IF(INSTR(c.diagnosis_code,'.'), 
            SUBSTR(c.diagnosis_code,1,IF(INSTR(c.diagnosis_code,'.'),INSTR(c.diagnosis_code,'.')-1,0)),
            c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
            ORDER BY ed.CODE, p.name_last, p.name_first, p.name_middle, DATE(e.admission_dt) ASC";
           
   // echo $sql;
   //  exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if(($row['date_birth']!='0000-00-00') && ($row['date_birth']!=NULL))
                $date_birth = date("m/d/Y",strtotime($row['date_birth']));
                
            if(($row['Date_Discharged']!='0000-00-00') && ($row['Date_Discharged']!=NULL)){
                if(GET_DEPT==IPBM_DEP){
                    $Date_Discharged = date("m/d/Y h:i A",strtotime($row['Date_Discharged']));
                }else{
                    $Date_Discharged = date("m/d/Y",strtotime($row['Date_Discharged']));
                }
            }

            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'code_parent' => $row['code_parent'],
                              'ICD' => $row['ICD'],
                              'ICD_Description' => $row['ICD_Description'],
                              'Case_No' => $row['Case_No'],
                              'Full_Name' => utf8_decode(trim($row['Full_Name'])),
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $Date_Discharged,
                              'date_birth' => $date_birth,
                              'Age' => $row['Age'],
                              'er_opd_diagnosis' => $row['er_opd_diagnosis'],
                              'Sex' => $row['Sex'],
                              'Complete_Address' => $row['Complete_Address'],
                              'Remarks' => $row['Remarks'],
                              'Attending_Physician' => $row['Attending_Physician'],
                              );
                              
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Case_No'] = NULL; 
    }       