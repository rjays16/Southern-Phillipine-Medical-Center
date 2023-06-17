<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report | Modified by JEFF @ 10-20-17;recoded to use function
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT sbcg.name AS group_name ,d.component as bestellnum, c.long_name as artikelname, 

             SUM(CASE WHEN p.sex='m' AND
                (floor(fn_get_ageyr(d.received_date,p.date_birth))>=0 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=5) 
                  THEN 1 ELSE 0 END) AS male_below5,

              SUM(CASE WHEN p.sex='f' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth))>=0 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=5) 
                  THEN 1 ELSE 0 END) AS female_below5, 

              SUM(CASE WHEN p.sex='m' AND 
                  (floor(fn_get_ageyr(d.received_date,p.date_birth))>=6 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=14) 
                  THEN 1 ELSE 0 END) AS male_6to14, 

              SUM(CASE WHEN p.sex='f' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth))>=6 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=14) 
                  THEN 1 ELSE 0 END) AS female_6to14, 

              SUM(CASE WHEN p.sex='m' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth))>=15 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=44) 
                  THEN 1 ELSE 0 END)  AS male_15to44, 

              SUM(CASE WHEN p.sex='f' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth))>=15 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=44) 
                  THEN 1 ELSE 0 END)  AS female_15to44,

              SUM(CASE WHEN p.sex='m' AND
                 (floor(fn_get_ageyr(d.received_date,p.date_birth))>=45 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=59) 
                  THEN 1 ELSE 0 END) AS male_45to59, 

              SUM(CASE WHEN p.sex='f' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth))>=45 AND 
                  floor(fn_get_ageyr(d.received_date,p.date_birth))<=59) 
                  THEN 1 ELSE 0 END) AS female_45to59, 

              SUM(CASE WHEN p.sex='m' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth)))>=60
                  THEN 1 ELSE 0 END) AS male_60up, 

              SUM(CASE WHEN p.sex='f' AND
                  (floor(fn_get_ageyr(d.received_date,p.date_birth)))>=60
                  THEN 1 ELSE 0 END) AS female_60up, 

              SUM(CASE WHEN p.sex='m' THEN 1 ELSE 0 END) AS male_total, 
              SUM(CASE WHEN p.sex='f' THEN 1 ELSE 0 END) AS female_total, COUNT(h.refno) AS total,
              SUM(CASE WHEN d.result = 'retype' THEN 1 ELSE 0 END) AS total_retype,
              SUM(CASE WHEN d.result = 'compat' THEN 1 ELSE 0 END ) AS compat,
              SUM(CASE WHEN d.result = 'incompat' THEN 1 ELSE 0 END ) AS incompat ,
              SUM(CASE WHEN c.component_group = 'redcell' THEN 1 ELSE 0 END) AS redcell,
              SUM(CASE WHEN c.component_group ='plasma' THEN 1 ELSE 0 END) AS plasma
             FROM seg_blood_received_details d
             INNER JOIN seg_blood_component c ON c.id=d.component
             INNER JOIN seg_lab_serv h ON h.refno=d.refno
             INNER JOIN care_person p ON p.pid=h.pid
             INNER JOIN seg_blood_component_group sbcg ON sbcg.id = c.component_group


            WHERE DATE(d.received_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
              AND h.ref_source='BB'
              AND h.STATUS NOT IN ('deleted','hidden','inactive','void')
              AND d.STATUS = 'received'
              AND d.result NOT IN ('noresult')
              GROUP BY c.id
              ORDER BY artikelname/*,COUNT(d.refno)*/ ASC";       
           
   // echo $sql; die();
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        while($row=$rs->FetchRow()){
            
            $total_row = $row['male_total'] + $row['female_total'];
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'bestellnum' => $row['bestellnum'],
                          'artikelname' => $row['artikelname'], 
                          'male_below1' => (int) $row['male_below5'],
                          'female_below1' => (int) $row['female_below5'],
                          'male_1to4' => (int) $row['male_6to14'],
                          'female_1to4' => (int) $row['female_6to14'],
                          'male_5to9' => (int) $row['male_15to44'],
                          'female_5to9' => (int) $row['female_15to44'],
                         /* 'male_10to14' => (int) $row['male_10to14'],
                          'female_10to14' => (int) $row['female_10to14'],
                          'male_15to19' => (int) $row['male_15to19'],
                          'female_15to19' => (int) $row['female_15to19'],
                          'male_20to44' => (int) $row['male_20to44'],
                          'female_20to44' => (int) $row['female_20to44'],*/
                          'male_45to59' => (int) $row['male_45to59'],
                          'female_45to59' => (int) $row['female_45to59'],
                          'male_60up' => (int) $row['male_60up'],
                          'female_60up' => (int) $row['female_60up'],
                          'male_total' => (int) $row['male_total'],
                          'female_total' => (int) $row['female_total'],
                          'total' => (int) $row['total'],
                          'total_retype' => (int) $row['total_retype'] ,
                          'total_compatible' => (int) $row['compat'] ,
                          'total_incompatible'  => (int) $row['incompat'] ,
                          'group_component_redcell' =>(int) $row['redcell'] ,
                          'group_component_plasma' =>(int) $row['plasma'] 
                          );
                          
           $grand_total += $row['total'];
           $rowindex++;
        } 
          #print_r($data);
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
