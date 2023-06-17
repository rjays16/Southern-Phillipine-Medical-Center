<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    $ipbm_opd = ' AND d.name_formal != ("IPBM") ';
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    #$params->put("area", $patient_type_label." (".$date_based_label.") from ".trim(mb_strtoupper($area)));
    $params->put("icd_class", $icd_class);

   if($_GET['dept_nr']==IPBM_DEP){
      $column_header= "OPD Department/ Clinic"; 
      $patient_type = '14';
      $dept_label = $ipbm_header;
    }
    else{
      $column_header = "OPD Department/ Clinic (All)";
      $patient_type = '2';
    }
    // var_dump($type_personnel);exit();
    $params->put("department", $orientation_header?$orientation_header:'ALL');
    $params->put("column_name",$orientation_column?$orientation_column:'DEPARTMENT (All)');
    $params->put("staff",$type_personnel);
    $params->put("doctor",$type_personnel);
if($type_personnel=='staff'){
    $sql = "SELECT  d.name_formal AS Type_Of_Service, 
            $age_bracket
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN care_department AS d ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type) 
            ".$consul_insti."
            ".$ipbm_opd."
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY d.name_formal
            ORDER BY d.name_formal";

}
else{
   $sql = "SELECT 
            $field_age_per_enc
            FROM care_encounter e
            INNER JOIN care_person p ON p.pid=e.pid
            LEFT JOIN care_department AS d ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND e.encounter_type IN ($patient_type)
            ".$consul_insti." 
            ".$ipbm_opd."
            AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            ";

}
    // echo $sql; 
    // exit();
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

            
            $old_male_total =  (int)$row['old_male_2below']+(int) $row['old_male_2to5'] + (int)$row['old_male_6to11']
                                  +(int)$row['old_male_12to18']+ (int)$row['old_male_19to59']
                                  +(int)$row['old_male_60'];
            $new_male_total = (int)$row['new_male_2below']+ (int) $row['new_male_2to5'] + (int)$row['new_male_6to11']
                                  +(int)$row['new_male_12to18']+(int)$row['new_male_19to59']
                                  +(int)$row['new_male_60'] ;
                                  
            $old_female_total = (int)$row['old_female_2below']  +(int) $row['old_female_2to5'] + (int)$row['old_female_6to11']+(int)$row['old_female_12to18']+ (int)$row['old_female_19to59']+ (int)$row['old_female_60'];
            $new_female_total = (int)$row['new_female_2below']+(int) $row['new_female_2to5'] +(int)$row['new_female_6to11']+(int)$row['new_female_12to18']+(int)$row['new_female_19to59']+(int)$row['new_female_60'];

            $total_old_2below =  (int) $row['old_male_2below'] +(int) $row['old_female_2below'];
            $total_new_2below = (int) $row['new_male_2below']+(int) $row['new_female_2below'];
            
            $total_old_2to5 =  (int) $row['old_male_2to5'] +(int) $row['old_female_2to5'];
            $total_new_2to5 = (int) $row['new_male_2to5']+(int) $row['new_female_2to5'];
            $total_old_6to11 =  (int) $row['old_male_6to11'] +(int) $row['old_female_6to11'];
            $total_new_6to11 = (int) $row['new_male_6to11']+(int) $row['new_female_6to11'];
            $total_old_12to18 =  (int) $row['old_male_12to18'] +(int) $row['old_female_12to18'];
            $total_new_12to18 = (int) $row['new_male_12to18']+(int) $row['new_female_12to18'];
            $total_old_19to59 =  (int) $row['old_male_19to59'] +(int) $row['old_female_19to59'];
            $total_new_19to59 = (int) $row['new_male_19to59']+(int) $row['new_female_19to59'];
            $total_old_60 = (int)$row['old_male_60'] + (int)$row['old_female_60'];
            $total_new_60 = (int)$row['new_male_60'] + (int)$row['new_female_60'];
            $grant_total_old = $old_male_total + $old_female_total;
            $grant_total_new = $new_male_total + $new_female_total;

            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'Type_Of_Service' => $row['Type_Of_Service'], 
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
                              'total' => (int) $total,
                              'mo_2to5'=>(int) $row['old_male_2to5'],
                              'mo_2b'=> (int)$row['old_male_2below'],
                              'mn_2b'=> (int)$row['new_male_2below'],
                              'fo_2b'=> (int)$row['old_female_2below'],
                              'fn_2b'=> (int)$row['new_female_2below'],
                              'to_2b'=>(int)$total_old_2below,
                              'tn_2b'=>(int)$total_new_2below,
                              'mn_2to5' =>(int) $row['new_male_2to5'],
                              'mo_6to11' => (int)$row['old_male_6to11'],
                              'mn_6to11' => (int)$row['new_male_6to11'],
                              'mo_12to18' => (int)$row['old_male_12to18'],
                              'mn_12to18' => (int)$row['new_male_12to18'],
                              'mo_19to59' => (int)$row['old_male_19to59'],
                              'mn_19to59' => (int)$row['new_male_19to59'],
                              'mo_60' => (int)$row['old_male_60'],
                              'mn_60' => (int)$row['new_male_60'],
                              'mo_t' => (int)$old_male_total,
                              'mn_t' => (int)$new_male_total,
                              'fo_2to5'=>(int) $row['old_female_2to5'],
                              'fn_2to5' =>(int) $row['new_female_2to5'],
                              'fo_6to11' => (int)$row['old_female_6to11'],
                              'fn_6to11' => (int)$row['new_female_6to11'],
                              'fo_12to18' => (int)$row['old_female_12to18'],
                              'fn_12to18' => (int)$row['new_female_12to18'],
                              'fo_19to59' => (int)$row['old_female_19to59'],
                              'fn_19to59' => (int)$row['new_female_19to59'],
                              'fo_60' => (int)$row['old_female_60'],
                              'fn_60' => (int)$row['new_female_60'],
                              'fo_t' => (int)$old_female_total,
                              'fn_t' => (int)$new_female_total,
                              'to_2to5'=>(int)$total_old_2to5,
                              'tn_2to5'=>(int)$total_new_2to5,
                              'to_6to11'=>(int)$total_old_6to11,
                              'tn_6to11'=>(int)$total_new_6to11,
                              'to_12to18'=>(int)$total_old_12to18,
                              'tn_12to18'=>(int)$total_new_12to18,
                              'to_19to59'=>(int)$total_old_19to59,
                              'tn_19to59'=>(int)$total_new_19to59,
                              'to_60'=> (int)$total_old_60,
                              'tn_60'=>(int)$total_new_60,
                              'to_t'=>(int)$grant_total_old,
                              'tn_t'=>(int)$grant_total_new,
                              );
                              
           $rowindex++;
        }  
          
    }else{
        $data[0]['code'] = NULL; 
    }     