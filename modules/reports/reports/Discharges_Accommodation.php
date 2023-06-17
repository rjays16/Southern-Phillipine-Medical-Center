<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
        $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );
    #TITLE of the report
    if($_GET['dept_nr']==IPBM_DEP){
       #query for Discharges based on Accommodation 
         $query_sub_accom = "SELECT d.name_formal AS Type_Of_Service,
                                    SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_non_phic, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS pay_phic_memdep, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS pay_phic_indigent, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=2) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS pay_phic_owwa, 
                                    SUM(CASE WHEN (w.accomodation_type=2) THEN 1 ELSE 0 END) AS pay_total,
                                    SUM(CASE WHEN ((i.hcare_id IS NULL) OR (i.hcare_id<>18)) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_non_phic, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND (em.memcategory_id IN (".PHIC_MEMBER.") OR em.memcategory_id IS NULL) THEN 1 ELSE 0 END) AS charity_phic_memdep, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_INDIGENT.") THEN 1 ELSE 0 END) AS charity_phic_indigent, 
                                    SUM(CASE WHEN (i.hcare_id=18) AND (w.accomodation_type=1 OR w.accomodation_type IS NULL) AND em.memcategory_id IN (".PHIC_OWWA.") THEN 1 ELSE 0 END) AS charity_phic_owwa, 
                                    SUM(CASE WHEN (w.accomodation_type=1 OR w.accomodation_type IS NULL) THEN 1 ELSE 0 END) AS charity_total,
                                    SUM(CASE WHEN 1 THEN 1 ELSE 0 END) AS total,
                                    SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_len_stay
                                    FROM care_department AS d
                                    INNER JOIN care_encounter AS e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)=d.nr
                                    INNER JOIN care_person AS cp ON cp.pid = e.pid
                                    LEFT JOIN care_ward AS w ON w.nr = IF(fn_get_encounter_location_billing (e.encounter_nr),fn_get_encounter_location_billing (e.encounter_nr),e.current_ward_nr) 
                                    LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr
                                    #LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid
                                    LEFT JOIN seg_encounter_memcategory AS em ON em.encounter_nr=e.encounter_nr
                                    LEFT JOIN seg_memcategory AS m ON m.memcategory_id=em.memcategory_id
                                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                                    AND e.discharge_date IS NOT NULL
                                    AND e.encounter_type IN (".IPBMIPD_enc.")";
            $params->put('p_type', $area_type);
            $params->put("hospital_country", mb_strtoupper($hosp_country));
            $params->put("hospital_name", mb_strtoupper($hosp_name));
            $params->put("header", IPBM_HEADER);
            $params->put("title", $report_title);
            $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
            $params->put("ipbm_logo", $baseurl . "img/ipbm_new.png"); 
    }else{
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $area_type);

    }
  
    
    $sql = " $query_sub_accom
            AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
            GROUP BY d.name_formal
            ORDER BY d.name_formal";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            $total_pay = (int) $row['pay_non_phic'] + (int) $row['pay_phic_memdep'] + (int) $row['pay_phic_indigent'] + (int) $row['pay_phic_owwa'];
            $total_charity = (int) $row['charity_non_phic'] + (int) $row['charity_phic_memdep'] + (int) $row['charity_phic_indigent'] + (int) $row['charity_phic_owwa'];
            $total_discharge = $total_pay + $total_charity;
            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'Type_Of_Service' => $row['Type_Of_Service'],
                              'pay_non_phic' => (int) $row['pay_non_phic'],
                              'pay_phic_memdep' => (int) $row['pay_phic_memdep'],
                              'pay_phic_indigent' => (int) $row['pay_phic_indigent'],
                              'pay_phic_owwa' => (int) $row['pay_phic_owwa'],
                              'total_pay' => (int) $total_pay,
                              'charity_non_phic' => (int) $row['charity_non_phic'],
                              'charity_phic_memdep' => (int) $row['charity_phic_memdep'],
                              'charity_phic_indigent' => (int) $row['charity_phic_indigent'],
                              'charity_phic_owwa' => (int) $row['charity_phic_owwa'],
                              'total_charity'  => (int) $total_charity,
                              'total_discharge' => (int) $total_discharge,
                              'total_len_stay' => (int) $row['total_len_stay'],
                              );
                             
           $rowindex++;
        }  
        
          #print_r($data);
    }else{
        $data[0]['Type_Of_Service'] = NULL; 
    }
