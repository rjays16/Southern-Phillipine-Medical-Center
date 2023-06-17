<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
      
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("column_name",$column_name_ave);
    if(GET_DEPT==IPBM_DEP){
       $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))

    );
      $ave_based_date = 'e.admission_dt';
      $ave_patient_type = IPBM_IPD;
            $params->put('ptype', 'INPATIENTS');
            $params->put("hospital_country", mb_strtoupper($hosp_country));
            $params->put("header", IPBM_HEADER);
            $params->put("title", $report_title);
            $params->put("dmc", $baseurl ."gui/img/logos/dmc_logo.jpg");
            $params->put("ipbm_logo", $baseurl ."img/ipbm_new.png");
        $sql_total_days = "SELECT (DATEDIFF(".$db->qstr($to_date_format).",DATE_FORMAT(".$db->qstr($from_date_format).", '%Y-%m-%d'))+1) AS total_days";
    }else{
      
    $params->put("header", $report_title);
    #$params->put("department", $dept_label);
    $params->put("department", $area_type);
        $sql_total_days = "SELECT (DATEDIFF(".$db->qstr($to_date_format).",DATE_FORMAT(".$db->qstr($from_date_format).", '%Y-%m-01'))+1) AS total_days";
    }

   

    
    if ($area_type == "ER Patient"){
        $start_date = '2011-12-01';
        $cond_start = "AND DATE($ave_based_date) >= ".$db->qstr($start_date);
    }
    
    $sql = "SELECT 
              d.nr,
              d.name_formal AS Type_Of_Service,
              SUM(CASE WHEN (DATE($ave_based_date) < ".$db->qstr($from_date_format).") THEN 1 ELSE 0 END) initial_admitted, 
              SUM(CASE WHEN (DATE(e.discharge_date) < ".$db->qstr($from_date_format)." AND e.discharge_date IS NOT NULL) THEN 1 ELSE 0 END) initial_discharges, 
              SUM(CASE WHEN (DATE($ave_based_date) < ".$db->qstr($from_date_format).") THEN 1 ELSE 0 END) - SUM(CASE WHEN (DATE(e.discharge_date) < ".$db->qstr($from_date_format)." AND e.discharge_date IS NOT NULL) THEN 1 ELSE 0 END) initial,
              SUM((DATE($ave_based_date) < ".$db->qstr($from_date_format).") AND (DATE(e.discharge_date) >= ".$db->qstr($from_date_format)." OR e.discharge_date IS NULL)) initial_census,
              
              SUM(CASE WHEN (DATE($ave_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") THEN 1 ELSE 0 END) admission, 
              SUM(CASE WHEN (DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") THEN 1 ELSE 0 END) discharges,
              
              SUM(CASE WHEN (DATE($ave_based_date) = DATE(e.discharge_date) AND DATE($ave_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") THEN 1 ELSE 0 END) admitted_disc_sameday,

              SUM(CASE WHEN ((DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") AND (SELECT SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20) AS result_code 
            FROM seg_encounter_result AS ser
            WHERE encounter_nr=e.encounter_nr) NOT IN (4,8,9,10)) THEN 1 ELSE 0 END) discharges_alive,
              SUM(CASE WHEN ((DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") AND (SELECT SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20) AS result_code 
            FROM seg_encounter_result AS ser
            WHERE encounter_nr=e.encounter_nr) IN (4,8,9,10)) THEN 1 ELSE 0 END) discharges_died,
              SUM(CASE WHEN ((DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") AND (SELECT SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20) AS result_code 
            FROM seg_encounter_result AS ser
            WHERE encounter_nr=e.encounter_nr) IS NULL) THEN 1 ELSE 0 END) discharges_noresult,
              
              SUM(CASE WHEN ((DATE($ave_based_date) <= ".$db->qstr($from_date_format).") AND (DATE(e.discharge_date) > ".$db->qstr($to_date_format)." OR e.discharge_date IS NULL)) THEN 1 ELSE 0 END) final_census,
              
              SUM(CASE
                    WHEN (
                      (
                        DATE($ave_based_date) <= ".$db->qstr($to_date_format)." 
                      ) 
                      AND (
                        DATE(e.discharge_date) > DATE_FORMAT(".$db->qstr($to_date_format)." , '%Y-%m-01')
                        OR 
                        e.discharge_date IS NULL
                      )
                    ) 
                    THEN DATEDIFF(
                                  IF(e.discharge_date IS NULL, ".$db->qstr($to_date_format).", 
                                    IF(e.discharge_date<=".$db->qstr($to_date_format).",DATE_SUB(e.discharge_date, INTERVAL 1 DAY),".$db->qstr($to_date_format).")) ,
                                  IF(DATE($ave_based_date) < DATE_FORMAT(".$db->qstr($from_date_format).",'%Y-%m-01'), DATE_FORMAT(".$db->qstr($from_date_format).",'%Y-%m-01'), DATE($ave_based_date))
                                ) + 1 ELSE 0 END) service_days
              
            FROM
              care_encounter e 
              LEFT JOIN care_department AS d 
                ON d.nr = IF(
                  e.current_dept_nr,
                  e.current_dept_nr,
                  e.consulting_dept_nr
                ) 
            WHERE e.STATUS NOT IN (
                'deleted',
                'hidden',
                'inactive',
                'void'
              ) 
              AND e.encounter_type IN ($ave_patient_type) 
              AND d.type = 1 
              AND d.nr IS NOT NULL 
              AND d.is_inactive=0
              AND DATE($ave_based_date) <= ".$db->qstr($to_date_format)."
              AND (e.discharge_date >= DATE_FORMAT(".$db->qstr($from_date_format).",'%Y-%m-01') OR e.discharge_date IS NULL)
              ".$cond_start."
            GROUP BY d.name_formal 
            -- HAVING final_census>0  
            ORDER BY d.name_formal ";        
    
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $admissions = $row['admission'];
            $discharges =  (int) $row['discharges_alive'] + (int) $row['discharges_noresult'] +  (int) $row['discharges_died'];
            #$initial_census = $row['initial_census'];
            $initial_census = $row['initial'];
            $daily_census = ($initial_census + $admissions) - $discharges;
            #$daily_census =  $row['final_census'];

            #In-Patient Service Days (Bed Days) = [ ( In-patients remaining at midnight… + Total Admission) - (Total discharges/deaths) + (Admitted and discharge on the same day)]
            #$inpatient_days = $daily_census + $row['admitted_disc_sameday'];
            $inpatient_days = (int) $row['service_days'] ; //+ (int) $row['service_days'];

            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'Type_Of_Service' => $row['Type_Of_Service'],
                          'initial_census' => (int) $initial_census,
                          'admissions' => (int) $row['admission'],
                          'discharges_alive' => (int) $row['discharges_alive']+(int) $row['discharges_noresult'],
                          'discharges_died' => (int) $row['discharges_died'],
                          //'total_no_days' => (int) $row['total_no_days'],
                          'total_no_days' => (int) $inpatient_days,
                          'discharges' => (int) $discharges,
                          'still_not_discharge' => 0,
                          'daily_census' => (int) $daily_census,
                          'admitted_disc_sameday' => (int) $row['admitted_disc_sameday'],
                          );
       
            $rowindex++;

        }  
        
          #$sql_total_days = "SELECT (DATEDIFF(".$db->qstr($to_date_format).",DATE_FORMAT(".$db->qstr($from_date_format).", '%Y-%m-01'))+1) AS total_days";
          $total_days = $db->GetOne($sql_total_days);
      
          $params->put("total_no_days", (int) $total_days); 
          $params->put("ft_start_date", date("m/d/Y", strtotime($start_date)));
          #print_r($data);   
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
