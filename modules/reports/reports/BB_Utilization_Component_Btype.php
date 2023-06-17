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
           
    $sql = "SELECT
            IF (c.long_name IS NULL OR c.long_name='', 'Others', c.long_name) AS blood_component,
            IF (cg.name IS NULL OR cg.name='', 'Others', cg.name) AS component_group,
            IF (t.group IS NULL OR t.group='', 'Others', t.group) AS blood_type,
            IF (t.name IS NULL OR t.name='', 'Others', t.name) AS blood_type_rh,

            SUM(CASE WHEN (DATE(d.received_date) 
              BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." AND h.is_cash=0) 
                THEN 1 ELSE 0 END) AS tcount_deposited,
            SUM(CASE WHEN (DATE(s.done_date) 
              BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." ) 
                THEN 1 ELSE 0 END) AS tcount_crossmatched,
            SUM(CASE WHEN (DATE(s.issuance_date) 
              BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." ) 
                THEN 1 ELSE 0 END) AS tcount_transfused

            FROM seg_blood_component c
            INNER JOIN seg_blood_received_details d ON d.component = c.id
            LEFT JOIN seg_blood_received_status s ON s.refno=d.refno
                AND s.service_code=d.service_code AND s.ordering=d.ordering
            INNER JOIN seg_lab_serv h ON h.refno=d.refno
            LEFT JOIN seg_blood_type_patient bp ON bp.pid=h.pid
            LEFT JOIN seg_blood_type t ON t.id=bp.blood_type
            LEFT JOIN seg_blood_component_group cg ON cg.id=c.component_group
            WHERE d.STATUS IN ('received')
            GROUP BY blood_component, blood_type, component_group
            ORDER BY blood_component";       
           
    #echo $sql; 
    #exit();
    
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){

        while($row=$rs->FetchRow()){
            
            $data[$rowindex] = array(
                                      'blood_component'     => $row['blood_component'], 
                                      'component_group'     => $row['component_group'], 
                                      'blood_type'          => $row['blood_type'], 
                                      'blood_type_rh'       => $row['blood_type_rh'],
                                      'tcount_deposited'    => (int) $row['tcount_deposited'],
                                      'tcount_crossmatched' => (int) $row['tcount_crossmatched'],
                                      'tcount_transfused'   => (int) $row['tcount_transfused'],
                                );
                          
           $rowindex++;
        }  

          #print_r($data);
          #exit();
    }else{
        $data[0]['blood_component'] = NULL; 
    }  
