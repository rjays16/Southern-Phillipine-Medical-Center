<?php
#Created by: Borj
#Date/Time: 2014-07-22
#Bloob Bank Utilization
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", 'Blood Bank');
    $params->put("transaction", $transaction);

    if(!$with_bb_trxn){
      $noparamsql = "OR (DATE(s.done_date) BETWEEN " . $db->qstr($from_date_format) . " AND " . $db->qstr($to_date_format) . ") OR (DATE(s.issuance_date) BETWEEN " . $db->qstr($from_date_format) . " AND " . $db->qstr($to_date_format) . ")";
    }

    $sql1 ="SELECT
            IF (c.long_name IS NULL OR c.long_name='', 'Others', c.long_name) AS blood_component,
            IF (cg.name IS NULL OR cg.name='', 'Others', cg.name) AS component_group,
            IF (bs.long_name IS NULL OR bs.long_name='', 'Others', bs.long_name) AS blood_source,
            IF (t.name IS NULL OR t.name = '','Others', t.name) AS blood_group,
            IF (bd.long_name IS NULL OR bd.long_name='Unspecified', 'OTHERS', bd.long_name) AS ward_long,
            IF (bd.name IS NULL OR bd.name='Unspecified', 'OTHERS', bd.name) AS ward_name,
            COUNT(*) AS tcount, bs.category_id
            FROM seg_blood_component c
            INNER JOIN seg_blood_received_details d ON d.component = c.id
            LEFT JOIN seg_blood_received_status s ON s.refno=d.refno AND s.service_code=d.service_code AND s.ordering=d.ordering
            INNER JOIN seg_lab_serv h ON h.refno=d.refno
            LEFT JOIN seg_blood_type_patient bp ON bp.pid = h.pid 
            LEFT JOIN seg_blood_type t ON t.id = bp.blood_type
            inner JOIN seg_blood_source bs ON bs.id = d.blood_source
            LEFT JOIN seg_blood_dept bd ON bd.id=d.dept
            LEFT JOIN seg_blood_component_group cg ON cg.id=c.component_group
            WHERE d.STATUS IN ('received') AND h.status NOT IN ('deleted','hidden','inactive','void')
            $cond
            AND (DATE($bb_based_date) BETWEEN " . $db->qstr($from_date_format) . " AND " . $db->qstr($to_date_format) . " )".$noparamsql."
            GROUP BY blood_component, blood_source, blood_group, d.dept
            ORDER BY component_group, blood_component, category_id";

// die($sql1);
     $rs1 = $db->Execute($sql1);
     #echo $sql1;
     #exit();
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if ($rs1->RecordCount()){
        while($row=$rs1->FetchRow()){
           $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'blood_component' => $row['blood_component'], 
                          'blood_source'    => $row['blood_source'],
                          'ward_name'       => $row['ward_name'],
                          'blood_type'      => $row['blood_group'],
                          'tcount'          => (int) $row['tcount']
                          );
           $rowindex++;
        }
         
    }else{
       $data[0]['blood_component'] = 'No Records'; 
    }
    // echo "<pre>" . print_r($data,true) . "</pre>";exit();
        
