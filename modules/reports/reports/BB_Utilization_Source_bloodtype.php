<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
    $params->put("transaction", $transaction);
        
    $sql = "SELECT c.long_name AS blood_component, 

              SUM(CASE WHEN (t.GROUP='O' AND d.blood_source='DBC')  
                THEN 1 ELSE 0 END) AS o_DBC,
              SUM(CASE WHEN (t.GROUP='O' AND d.blood_source='DRHBB')  
                THEN 1 ELSE 0 END) AS o_DRHBB,
              SUM(CASE WHEN (t.GROUP='O' AND d.blood_source='KCBB')  
                THEN 1 ELSE 0 END) AS o_KCBB,
              SUM(CASE WHEN (t.GROUP='O' AND d.blood_source='PHOBB')  
                THEN 1 ELSE 0 END) AS o_PHOBB,
              SUM(CASE WHEN (t.GROUP='O' AND d.blood_source='PRC')  
                THEN 1 ELSE 0 END) AS o_PRC,
              SUM(CASE WHEN (t.GROUP='O' AND d.blood_source='OTHERS')  
                THEN 1 ELSE 0 END) AS o_OTHERS, 
                
              SUM(CASE WHEN (t.GROUP='A' AND d.blood_source='DBC')  
                THEN 1 ELSE 0 END) AS a_DBC,
              SUM(CASE WHEN (t.GROUP='A' AND d.blood_source='DRHBB')  
                THEN 1 ELSE 0 END) AS a_DRHBB,
              SUM(CASE WHEN (t.GROUP='A' AND d.blood_source='KCBB')  
                THEN 1 ELSE 0 END) AS a_KCBB,
              SUM(CASE WHEN (t.GROUP='A' AND d.blood_source='PHOBB')  
                THEN 1 ELSE 0 END) AS a_PHOBB,
              SUM(CASE WHEN (t.GROUP='A' AND d.blood_source='PRC')  
                THEN 1 ELSE 0 END) AS a_PRC,
              SUM(CASE WHEN (t.GROUP='A' AND d.blood_source='OTHERS')  
                THEN 1 ELSE 0 END) AS a_OTHERS,
                
              SUM(CASE WHEN (t.GROUP='B' AND d.blood_source='DBC')  
                THEN 1 ELSE 0 END) AS b_DBC,
              SUM(CASE WHEN (t.GROUP='B' AND d.blood_source='DRHBB')  
                THEN 1 ELSE 0 END) AS b_DRHBB,
              SUM(CASE WHEN (t.GROUP='B' AND d.blood_source='KCBB')  
                THEN 1 ELSE 0 END) AS b_KCBB,
              SUM(CASE WHEN (t.GROUP='B' AND d.blood_source='PHOBB')  
                THEN 1 ELSE 0 END) AS b_PHOBB,
              SUM(CASE WHEN (t.GROUP='B' AND d.blood_source='PRC')  
                THEN 1 ELSE 0 END) AS b_PRC,
              SUM(CASE WHEN (t.GROUP='B' AND d.blood_source='OTHERS')  
                THEN 1 ELSE 0 END) AS b_OTHERS,
                
              SUM(CASE WHEN (t.GROUP='AB' AND d.blood_source='DBC')  
                THEN 1 ELSE 0 END) AS ab_DBC,
              SUM(CASE WHEN (t.GROUP='AB' AND d.blood_source='DRHBB')  
                THEN 1 ELSE 0 END) AS ab_DRHBB,
              SUM(CASE WHEN (t.GROUP='AB' AND d.blood_source='KCBB')  
                THEN 1 ELSE 0 END) AS ab_KCBB,
              SUM(CASE WHEN (t.GROUP='AB' AND d.blood_source='PHOBB')  
                THEN 1 ELSE 0 END) AS ab_PHOBB,
              SUM(CASE WHEN (t.GROUP='AB' AND d.blood_source='PRC')  
                THEN 1 ELSE 0 END) AS ab_PRC,
              SUM(CASE WHEN (t.GROUP='AB' AND d.blood_source='OTHERS')  
                THEN 1 ELSE 0 END) AS ab_OTHERS,
                
              SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND d.blood_source='DBC')  
                THEN 1 ELSE 0 END) AS other_DBC,
              SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND d.blood_source='DRHBB')  
                THEN 1 ELSE 0 END) AS other_DRHBB,
              SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND d.blood_source='KCBB')  
                THEN 1 ELSE 0 END) AS other_KCBB,
              SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND d.blood_source='PHOBB')  
                THEN 1 ELSE 0 END) AS other_PHOBB,
              SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND d.blood_source='PRC')  
                THEN 1 ELSE 0 END) AS other_PRC ,
              SUM(CASE WHEN (t.GROUP NOT IN ('O', 'A', 'B', 'AB') AND d.blood_source='OTHERS')  
                THEN 1 ELSE 0 END) AS other_OTHERS    
                                      
              FROM seg_blood_component c
              INNER JOIN seg_blood_received_details d ON d.component = c.id
              LEFT JOIN seg_blood_received_status s ON s.refno=d.refno
                AND s.service_code=d.service_code AND s.ordering=d.ordering       
              INNER JOIN seg_lab_serv h ON h.refno=d.refno
              LEFT JOIN seg_blood_type_patient bp ON bp.pid=h.pid
              LEFT JOIN seg_blood_type t ON t.id=bp.blood_type
              LEFT JOIN seg_blood_source bs ON bs.id = d.blood_source
              WHERE d.STATUS IN ('received')

              AND h.is_cash=0 AND (DATE($bb_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )

              AND d.blood_source IS NOT NULL
              GROUP BY d.component
              ORDER BY c.long_name";        
           
    #echo $sql; 
    #exit();
    
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){

        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array(
                          'blood_component' => $row['blood_component'], 
                          
                          'a_DBC'        => (int) $row['a_DBC'],
                          'a_DRHBB'      => (int) $row['a_DRHBB'],
                          'a_KCBB'       => (int) $row['a_KCBB'],
                          'a_PHOBB'      => (int) $row['a_PHOBB'],
                          'a_PRC'        => (int) $row['a_PRC'],
                          'a_OTHERS'     => (int) $row['a_OTHERS'],

                          'b_DBC'        => (int) $row['b_DBC'],
                          'b_DRHBB'      => (int) $row['b_DRHBB'],
                          'b_KCBB'       => (int) $row['b_KCBB'],
                          'b_PHOBB'      => (int) $row['b_PHOBB'],
                          'b_PRC'        => (int) $row['b_PRC'],
                          'b_OTHERS'     => (int) $row['b_OTHERS'],

                          'o_DBC'        => (int) $row['o_DBC'],
                          'o_DRHBB'      => (int) $row['o_DRHBB'],
                          'o_KCBB'       => (int) $row['o_KCBB'],
                          'o_PHOBB'      => (int) $row['o_PHOBB'],
                          'o_PRC'        => (int) $row['o_PRC'],
                          'o_OTHERS'     => (int) $row['o_OTHERS'],

                          'ab_DBC'       => (int) $row['ab_DBC'],
                          'ab_DRHBB'     => (int) $row['ab_DRHBB'],
                          'ab_KCBB'      => (int) $row['ab_KCBB'],
                          'ab_PHOBB'     => (int) $row['ab_PHOBB'],
                          'ab_PRC'       => (int) $row['ab_PRC'],
                          'ab_OTHERS'    => (int) $row['ab_OTHERS'],

                          'other_DBC'    => (int) $row['other_DBC'],
                          'other_DRHBB'  => (int) $row['other_DRHBB'],
                          'other_KCBB'   => (int) $row['other_KCBB'],
                          'other_PHOBB'  => (int) $row['other_PHOBB'],
                          'other_PRC'    => (int) $row['other_PRC'],
                          'other_OTHERS' => (int) $row['other_OTHERS'],

                          );
                          
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['blood_component'] = NULL; 
    }  
