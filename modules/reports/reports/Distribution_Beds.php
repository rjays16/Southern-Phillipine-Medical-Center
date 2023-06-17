<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Distribution of Beds and Bed Occupancy Rate');
    if(GET_DEPT == IPBM_DEP){
        $ipbmWardsSql = "select a.id,a.dept_name,a.service_allocated_bed,w.nr FROM seg_report_dept_bed_allocation AS a INNER JOIN care_ward AS w ON a.id = w.ward_id WHERE a.dept_nr = ".$db->qstr(IPBM_DEP)." ORDER BY ordering";
        $ipbmWards = $db->GetAll($ipbmWardsSql);
       
        $baseurl = sprintf(
            "%s://%s%s",
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
            $_SERVER['SERVER_ADDR'],
            substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
        );


        $patient_type = IPBM_IPD;
    }else{
        $patient_type = IPD;
    }

    
    #no of admissions
    $sql_census = "SELECT SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).")  THEN 1 ELSE 0 END) AS admitted,
                    SUM(CASE WHEN (DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )  THEN 1 ELSE 0 END) AS discharges,
                    (DATEDIFF(".$db->qstr($to_date_format).",".$db->qstr($from_date_format).")+1) AS total_days
                    FROM care_encounter e
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    AND e.encounter_type IN ($patient_type)";
    $census = $db->GetRow($sql_census);
    
    if (GET_DEPT == IPBM_DEP) {
       foreach ($ipbmWards as $key => $value) {
          $sql_ipbmcensus = "SELECT SUM(CASE WHEN (DATE(e.admission_dt) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )  THEN 1 ELSE 0 END) AS admitted,
                    SUM(CASE WHEN (DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )  THEN 1 ELSE 0 END) AS discharges,
                    (DATEDIFF(".$db->qstr($to_date_format).",".$db->qstr($from_date_format).")+1) AS total_days
                    FROM care_encounter e
                    WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
                    AND e.current_ward_nr = ".$db->qstr($value['nr'])."
                    AND e.encounter_type IN ($patient_type)";
          $ipbmcensus[$value['id']] = $db->GetRow($sql_ipbmcensus);
          
       }
    }
 
    // echo $sql_census; 
    // exit();
    #get census
    $sql_initial_census = "SELECT SUM(initial_census) AS census
                            FROM seg_report_ipd_census";

    $initial_census = $db->GetOne($sql_initial_census);

    if (GET_DEPT == IPBM_DEP) {

      $data = array();

      foreach ($ipbmWards as $key => $value) {
        $sql_ipbminitial_census = "
        SELECT 
          (admitted - discharges) as census
        FROM
        (
          SELECT 
            SUM(
              CASE
                WHEN (
                  DATE(e.admission_dt) < ".$db->qstr($from_date_format)."
                ) 
                THEN 1 
                ELSE 0  
              END
            ) AS admitted,
            SUM(
              CASE
                WHEN (
                  DATE(e.discharge_date) < ".$db->qstr($from_date_format)."
                ) 
                THEN 1 
                ELSE 0 
              END
            ) AS discharges 
          FROM
            care_encounter e 
          WHERE e.STATUS NOT IN (
              'deleted',
              'hidden',
              'inactive',
              'void'
            ) 
            AND e.current_ward_nr = ". $db->qstr($value['nr'])." 
            AND e.encounter_type IN (".$db->qstr(IPBM_IPD).") ) AS a 
        ";
     

        $ipbmInitial_Census[$value['id']] = $ipbmInitial_Census[$value['id']] + (($r = $db->GetOne($sql_ipbminitial_census)) ?  $r : 0);
        
      $sql = "SELECT a.dept_name AS Type_Of_Service, 
        a.pay_allocated_bed AS alloc_pay, 
        a.service_allocated_bed AS alloc_service,
        SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS nphic_pay, 
        SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS nphic_service, 
        SUM(CASE WHEN i.hcare_id=18 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS phic_pay, 
        SUM(CASE WHEN i.hcare_id=18 AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS phic_service,
        SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_ipd_days
        FROM seg_report_dept_bed_allocation a
        INNER JOIN care_encounter e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (a.dept_nr)
        LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr 
        LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
        LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
        INNER JOIN seg_billing_encounter AS sbe ON e.encounter_nr = sbe.encounter_nr
        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
        AND (sbe.is_deleted != '1' OR sbe.is_deleted IS NULL)
        AND sbe.is_final = '1'
        AND e.is_discharged = '1'
        AND e.current_ward_nr = ".$db->qstr($value['nr'])."
        AND a.id = ".$db->qstr($value['id'])."
        AND e.encounter_type IN ($patient_type) 
        /*AND e.in_ward=1 */
        AND DATE(e.discharge_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
        GROUP BY a.id
        ORDER BY ordering";
        $rs = $db->Execute($sql);
      
        if (is_object($rs) && $rs->RecordCount()){
            
            while($row=$rs->FetchRow()){
                
                $alloc_total = (int) $row['alloc_pay'] + (int) $row['alloc_service'];
                $total_pay = (int) $row['nphic_pay'] + (int) $row['phic_pay'];
                $total_service = (int) $row['nphic_service'] + (int) $row['phic_service'];
                $total = (int) $total_pay + (int) $total_service;

                $bor = (float) (($row['total_ipd_days']/($alloc_total * $census['total_days'])) * 100);

                $data[$key] = array('rowindex' => $rowindex+1,
                              'Type_Of_Service' => $row['Type_Of_Service'], 
                              'alloc_pay'=> (float) $row['alloc_pay'],
                              'alloc_service' => (float) $row['alloc_service'],
                              'alloc_total' => (float) $alloc_total,
                              'alloc_total'.$value['id'] => (float) $alloc_total,
                              'nphic_pay' => (float) $row['nphic_pay'],
                              'nphic_service' => (float) $row['nphic_service'],
                              'phic_pay' => (float) $row['phic_pay'],
                              'phic_service' => (float) $row['phic_service'],
                              'total_pay' => $total_pay,
                              'total_service' =>  $total_service,
                              'total' => (float) $total,
                              'total_ipd_days' => (float) $row['total_ipd_days'],
                              'total_ipd_days'.$value['id']  => (float) $row['total_ipd_days'],
                              'bor' => $bor,
                              );
                   break;
            }  
          
              
        }else{
            
            $data[$key] = array('rowindex' => $rowindex+1,
                            'Type_Of_Service' => $value['dept_name'], 
                            'alloc_pay'=> (float) 0,
                            'alloc_service' => (float) $value['service_allocated_bed'],
                            'alloc_total' => (float) $value['service_allocated_bed'],
                            'alloc_total'.$value['id'] => (float) $value['service_allocated_bed'],
                            'nphic_pay' => (float) 0,
                            'nphic_service' => (float) 0,
                            'phic_pay' => (float) 0,
                            'phic_service' => (float) 0,
                            'total_pay' => 0,
                            'total_service' =>  (float) 0,
                            'total' => (float) 0,
                            'total_ipd_days' => (float) 0,
                            'total_ipd_days'.$value['id']  => (float) 0,
                            'bor' => 0,
                            );
        }


        $params->put("total_admitted".$value['id'], (float) $ipbmcensus[$value['id']]['admitted']);
        $params->put("total_discharges".$value['id'], (float) $ipbmcensus[$value['id']]['discharges']);
        $params->put("total_initial_census".$value['id'], (float) $ipbmInitial_Census[$value['id']]);
         
        

      } //END FOREACH IPBMWARDS
      
    }else{
      $sql = "SELECT a.dept_name AS Type_Of_Service, 
      a.pay_allocated_bed AS alloc_pay, 
      a.service_allocated_bed AS alloc_service,
      SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS nphic_pay, 
      SUM(CASE WHEN (i.hcare_id<>18 OR i.hcare_id IS NULL) AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS nphic_service, 
      SUM(CASE WHEN i.hcare_id=18 AND (w.accomodation_type=2) THEN 1 ELSE 0 END) AS phic_pay, 
      SUM(CASE WHEN i.hcare_id=18 AND (w.accomodation_type=1) THEN 1 ELSE 0 END) AS phic_service,
      SUM(DATEDIFF(e.discharge_date,e.admission_dt)+1) AS total_ipd_days
      FROM seg_report_dept_bed_allocation a
      INNER JOIN care_encounter e ON IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr) IN (a.dept_nr)
      LEFT JOIN care_ward AS w ON e.current_ward_nr=w.nr 
      LEFT JOIN seg_encounter_insurance AS i ON i.encounter_nr=e.encounter_nr 
      LEFT JOIN care_person_insurance AS pti ON pti.pid=e.pid 
      WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
      AND e.encounter_type IN ($patient_type) 
      /*AND e.in_ward=1 */
      AND DATE(e.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
      GROUP BY a.id
      ORDER BY ordering";
      $rs = $db->Execute($sql);
    
      $rowindex = 0;
      $data = array();

      if (is_object($rs)){
          
          while($row=$rs->FetchRow()){
              
              $alloc_total = (int) $row['alloc_pay'] + (int) $row['alloc_service'];
              $total_pay = (int) $row['nphic_pay'] + (int) $row['phic_pay'];
              $total_service = (int) $row['nphic_service'] + (int) $row['phic_service'];
              $total = (int) $total_pay + (int) $total_service;

              $bor = (float) (($row['total_ipd_days']/($alloc_total * $census['total_days'])) * 100);

              
              $data[$rowindex] = array('rowindex' => $rowindex+1,
                            'Type_Of_Service' => $row['Type_Of_Service'], 
                            'alloc_pay' => (float) $row['alloc_pay'],
                            'alloc_service' => (float) $row['alloc_service'],
                            'alloc_total' => (float) $alloc_total,
                            'nphic_pay' => (float) $row['nphic_pay'],
                            'nphic_service' => (float) $row['nphic_service'],
                            'phic_pay' => (float) $row['phic_pay'],
                            'phic_service' => (float) $row['phic_service'],
                            'total_pay' => $total_pay,
                            'total_service' =>  $total_service,
                            'total' => (float) $total,
                            'total_ipd_days' => (float) $row['total_ipd_days'],
                            'bor' => $bor,
                            );
                  $rowindex++;
          }  
        

            
            
            
      }else{
          $data[0]['id'] = NULL; 
      }

    }
    $params->put("total_initial_census", (int) $initial_census);
    $params->put("total_admitted", (int) $census['admitted']);
    $params->put("total_discharges", (int)$census['discharges']); 
    $params->put("total_days", (int)$census['total_days']);

$params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put("ipbm_logo", $baseurl . "img/ipbm.png");

