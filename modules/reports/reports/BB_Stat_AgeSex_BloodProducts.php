<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
        
    $sql = "SELECT pr.bestellnum, pr.artikelname,
            SUM(CASE WHEN p.sex = 'm' AND (fn_get_ageyr (NOW(), p.date_birth) <= 5) 
                THEN 1 ELSE 0 END) AS male_below1,
            SUM(CASE WHEN p.sex = 'f' AND (fn_get_ageyr (NOW(), p.date_birth) <= 5) 
                THEN 1 ELSE 0 END) AS female_below1,
            SUM(CASE WHEN p.sex = 'm' AND (fn_get_ageyr (NOW(), p.date_birth) BETWEEN 6 AND 14) 
                THEN 1 ELSE 0 END) AS male_1to4,
            SUM(CASE WHEN p.sex = 'f' AND (fn_get_ageyr (NOW(), p.date_birth) BETWEEN 6 AND 14) 
                THEN 1 ELSE 0 END) AS female_1to4,
            SUM(CASE WHEN p.sex = 'm' AND (fn_get_ageyr (NOW(), p.date_birth) BETWEEN 15 AND 44) 
                THEN 1 ELSE 0 END) AS male_5to9,
            SUM(CASE WHEN p.sex = 'f' AND (fn_get_ageyr (NOW(), p.date_birth) BETWEEN 15 AND 44) 
                THEN 1 ELSE 0 END) AS female_5to9, 
/*              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.orderdate)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.orderdate),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS male_10to14, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.orderdate)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.orderdate),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS female_10to14, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.orderdate)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.orderdate),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS male_15to19, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.orderdate)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.orderdate),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS female_15to19, 
              SUM(CASE WHEN p.sex='m' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.orderdate)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.orderdate),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS male_20to44, 
              SUM(CASE WHEN p.sex='f' AND (IF (p.age, p.age, 
                  FLOOR((YEAR(DATE(h.orderdate)) - YEAR(p.date_birth)) - 
                  (RIGHT(DATE(h.orderdate),5)<RIGHT(p.date_birth,5)))) 
                  BETWEEN 20 AND 44) THEN 1 ELSE 0 END) AS female_20to44, */
            SUM(CASE WHEN p.sex = 'm' AND (fn_get_ageyr (NOW(), p.date_birth) BETWEEN 45 AND 59) 
                THEN 1 ELSE 0 END) AS male_45to59,
            SUM(CASE WHEN p.sex = 'f' AND (fn_get_ageyr (NOW(), p.date_birth) BETWEEN 45 AND 59) 
                THEN 1 ELSE 0 END) AS female_45to59,
            SUM(CASE WHEN p.sex = 'm' AND (fn_get_ageyr (NOW(), p.date_birth) >= 60) 
                THEN 1 ELSE 0 END) AS male_60up,
            SUM(CASE WHEN p.sex = 'f' AND (fn_get_ageyr (NOW(), p.date_birth) >= 60) 
                THEN 1 ELSE 0 END) AS female_60up, 
            SUM(CASE WHEN p.sex='m' THEN 1 ELSE 0 END) AS male_total, 
            SUM(CASE WHEN p.sex='f' THEN 1 ELSE 0 END) AS female_total, COUNT(h.refno) AS total
          FROM seg_pharma_orders h
          INNER JOIN care_person AS p ON p.pid=h.pid
          INNER JOIN seg_pharma_order_items d ON d.refno=h.refno
          INNER JOIN care_pharma_products_main pr ON pr.bestellnum=d.bestellnum 
          INNER JOIN seg_blood_products_item i ON i.item_code = d.bestellnum
          LEFT  JOIN seg_blood_received_status sb ON sb.refno = h.refno
          WHERE DATE(h.orderdate) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
          AND (d.pharma_area = 'BB' OR h.pharma_area = 'BB')
          AND d.serve_status='S'
          AND pr.is_deleted=0
          AND d.is_deleted=0
          AND h.is_deleted=0
          GROUP BY i.service_code
          ORDER BY COUNT(d.refno) DESC";        
           
    #echo $sql; 
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
                          'male_below1' => (int) $row['male_below1'],
                          'female_below1' => (int) $row['female_below1'],
                          'male_1to4' => (int) $row['male_1to4'],
                          'female_1to4' => (int) $row['female_1to4'],
                          'male_5to9' => (int) $row['male_5to9'],
                          'female_5to9' => (int) $row['female_5to9'],
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
                          'total' => (int) $row['total']
                          );
                          
           $grand_total += $row['total'];
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['id'] = NULL; 
    }  
