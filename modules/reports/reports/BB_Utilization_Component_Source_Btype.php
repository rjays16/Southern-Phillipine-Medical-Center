<?php
    /*
     * Created: August 03, 2014 (VANESSA A. SAREN)
     * Modified: August 03, 2014 (VANESSA A. SAREN)
     * USE CROSSTAB in generating reports
     * JASPER REPORT TEMPLATES
    */
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
    $params->put("transaction", $transaction);

    $cond = '';
    if ($transaction=='DEPOSITED')
      $cond = 'AND h.is_cash=0';
           
    $sql = "SELECT 
            IF (c.long_name IS NULL OR c.long_name='', 'Others', c.long_name) AS blood_component, 
            IF (bs.name IS NULL OR bs.name='', 'Others', bs.name) AS blood_source, 
            IF (t.name IS NULL OR t.name='', 'Others', t.name) AS blood_type, 
            IF (t.group IS NULL OR t.group='', 'Others', t.group) AS blood_group, 
            
            COUNT(*) AS tcount

            FROM seg_blood_component c
            INNER JOIN seg_blood_received_details d ON d.component = c.id
            LEFT JOIN seg_blood_received_status s ON s.refno=d.refno
              AND s.service_code=d.service_code AND s.ordering=d.ordering
            INNER JOIN seg_lab_serv h ON h.refno=d.refno
            LEFT JOIN seg_blood_type_patient bp ON bp.pid=h.pid
            LEFT JOIN seg_blood_type t ON t.id=bp.blood_type
            LEFT JOIN seg_blood_source bs ON bs.id = d.blood_source
            WHERE d.STATUS IN ('received')

            $cond
            AND (DATE($bb_based_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." )

            GROUP BY blood_component, blood_source, blood_type
            ORDER BY blood_component";       
           // die($sql);
    #echo $sql; 
    #exit();
    
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){

        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array(
                                      'blood_component' => $row['blood_component'], 
                                      'blood_source'    => $row['blood_source'], 
                                      'blood_type'      => $row['blood_type'], 
                                      'blood_group'     => $row['blood_group'],
                                      'tcount'          => (int) $row['tcount'],
                                );
                          
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['blood_component'] = NULL; 
    }  
