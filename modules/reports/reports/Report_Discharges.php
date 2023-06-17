<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_encounter.php');
    
    include('parameters.php');
    
    $obj_enc = new Encounter;

    #TITLE of the report
    $params->put('ipbm',IPBM_HEADER);
    $params->put("country",$hosp_country);
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    if($_GET['dept_nr']==IPBM_DEP){
        $patient_type ='13';
         $enc_dept_cond = " AND (e.current_dept_nr IN ('".IPBM_DEP."') \n".
                                        " OR e.current_dept_nr IN ( \n".
                                            " SELECT nr FROM care_department AS d WHERE d.parent_dept_nr IN ('".IPBM_DEP."'))) ";


    }
    else{
         $patient_type = '3,4';
    }
    #$params->put("department", $sub_caption);# remove All Minor and Major Operations then default All Patient 
    $params->put("department", "All Patients");
   
    $date_based = 'e.discharge_date';
    
    $sql = "SELECT DISTINCT e.pid AS hrn,
            e.encounter_nr AS 'Case_No',
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
            IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
            IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
            e.admission_dt AS 'Date_Admitted', 
            CONCAT(e.discharge_date, ' ', e.discharge_time) AS 'Date_Discharged',
            IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth,
            IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            e.er_opd_diagnosis,
            UPPER(p.sex) AS Sex,
            res.result_desc AS 'Remarks',
            IF(p.fromtemp, 'Newborn (Born Alive)', d.name_formal) AS department,
            e.received_date, ins.hcare_id, IF(ins.hcare_id=18,'P','NP') AS insurance,
            fn_get_mode_of_discharge(e.discharge_date,e.admission_dt) as mode_of_discharge

            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = e.encounter_nr
            INNER JOIN seg_results AS res ON res.result_code=ser.result_code
            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.discharge_date IS NOT NULL
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            AND e.encounter_type IN ($patient_type)
            $cond_classification
            $cond_mode_chart
            $cond_status
            $enc_dept_cond
            ORDER BY 
            p.death_date ASC, p.death_time ASC
            #p.death_date ASC, p.death_time ASC, Date_Discharged ASC, Full_Name ASC
            #p.name_last, p.name_first, p.name_middle, DATE(e.admission_dt) ASC";
           
    // echo $sql; 
    // exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $total_ciu = 0;
    $total_chronic = 0;
    $total_acute = 0;
    $total_custodial = 0;
   
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            if ($_GET['dept_nr']==IPBM_DEP) {
                $mod_res = $obj_enc->getPTypeClassification($row['Case_No']);
                if ($mod_res) {
                    if (isset($mod_res[0]['classification_name'])) {
                        $row['mode_of_discharge'] = strtoupper($mod_res[0]['classification_name']);
                    }
                }
            }
            
            $row_mode_discharged = $row['mode_of_discharge'];

            if(($mode_discharged==$row['mode_of_discharge'] && $_GET['dept_nr']==IPBM_DEP)  OR ($_GET['dept_nr']==IPBM_DEP && $mode_discharged=='ALL') OR ($_GET['dept_nr']!=IPBM_DEP)){

            if(($row['date_birth']!='0000-00-00') && ($row['date_birth']!=NULL))
                $date_birth = date("m/d/Y",strtotime($row['date_birth']));
                
            if(($row['Date_Discharged']!='0000-00-00') && ($row['Date_Discharged']!=NULL))
                $Date_Discharged = date("m/d/Y",strtotime($row['Date_Discharged']));    
                
            if(($row['received_date']!='0000-00-00') && ($row['received_date']!=NULL))
                $Date_Received = date("m/d/Y",strtotime($row['received_date']));        
            else
                $Date_Received = 'Not yet';
                
            if ($row['insurance']=='P')    
                $insurance = 'Yes';
            else    
                $insurance = 'None';
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'hrn' => $row['hrn'],
                              'Case_No' => $row['Case_No'],
                              'Full_Name' => utf8_decode(trim($row['Full_Name'])),
                              'Date_Admitted' => date("m/d/Y h:i A",strtotime($row['Date_Admitted'])),
                              'Date_Discharged' => $Date_Discharged,
                              'Date_Received' => $Date_Received,
                              'Age' => $row['Age'],
                              'er_opd_diagnosis' => $row['er_opd_diagnosis'],
                              'Sex' => $row['Sex'],
                              'Remarks' => $row['Remarks'],
                              'insurance' => $row['insurance'],
                              'department' => $row['department'],
                              'insurance' => $row['insurance'],
                              'mod' => $row['mode_of_discharge']
                              );
                if($row_mode_discharged == 'CIU'){
                    $total_ciu++;
                }elseif ($row_mode_discharged=='CHRONIC') {
                    $total_chronic++;
                }if ($row_mode_discharged=='ACUTE') {
                    $total_acute++;
                }elseif ($row_mode_discharged=='CUSTODIAL') {
                    $total_custodial++;
                }
           $rowindex++; 
           }
           
            $params->put("total_CIU", $total_ciu);
            $params->put("total_chronic", $total_chronic);
            $params->put("total_acute", $total_acute);
            $params->put("total_custodial", $total_custodial);
        }  
        
          #print_r($data);
    }else{
        $data[0]['Case_No'] = NULL; 
    }       