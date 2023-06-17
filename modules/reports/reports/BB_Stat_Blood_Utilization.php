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

    $sql1 ="SELECT
              sbc.`long_name` as blood_component,
              SUM(sbt.GROUP='O' AND (DATE(a.`serv_dt`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS o_deposited,
              SUM(sbt.GROUP='O' AND (DATE(c.`done_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS o_crossmatched,
              SUM(sbt.GROUP='O' AND (DATE(c.`issuance_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS o_transfused,
                
              SUM(sbt.GROUP='A' AND (DATE(a.`serv_dt`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS a_deposited,
              SUM(sbt.GROUP='A' AND (DATE(c.`done_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS a_crossmatched,
              SUM(sbt.GROUP='A' AND (DATE(c.`issuance_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS a_transfused,
              
              SUM(sbt.GROUP='B' AND (DATE(a.`serv_dt`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS b_deposited,
              SUM(sbt.GROUP='B' AND (DATE(c.`done_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS b_crossmatched,
              SUM(sbt.GROUP='B' AND (DATE(c.`issuance_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS b_transfused,
              
              SUM(sbt.GROUP='AB' AND (DATE(a.`serv_dt`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS ab_deposited,
              SUM(sbt.GROUP='AB' AND (DATE(c.`done_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS ab_crossmatched,
              SUM(sbt.GROUP='AB' AND (DATE(c.`issuance_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS ab_transfused,
                  
              SUM(a.`pid` AND (DATE(a.`serv_dt`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS pat_deposited,
              SUM(a.`pid` AND (DATE(c.`done_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS pat_crossmatched,
              SUM(a.`pid` AND (DATE(c.`issuance_date`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format)).")) AS pat_transfused,
              
              SUM(a.`is_urgent` = '1') AS batch_stat,
              SUM(a.`is_urgent` = '0') AS batch_routine 
             
            FROM
              seg_lab_serv AS a 
              LEFT JOIN seg_blood_received_details AS b 
                ON a.`refno` = b.`refno`
              LEFT JOIN seg_blood_received_status AS c 
                ON b.`refno` = c.`refno` 
                AND b.`service_code` = c.`service_code` 
                AND b.`ordering` = c.`ordering` 
              LEFT JOIN seg_blood_component AS sbc 
                ON sbc.`id` = b.`component` 
              LEFT JOIN seg_blood_type_patient sbtp 
                ON a.`pid` = sbtp.`pid` 
              LEFT JOIN seg_blood_type sbt 
                ON sbtp.`blood_type` = sbt.`id` 
              WHERE DATE(a.`serv_dt`) BETWEEN ".$db->qstr(DATE($from_date_format))." AND ".$db->qstr(DATE($to_date_format))."
              AND a.`status` NOT IN (
                'deleted',
                'hidden',
                'inactive',
                'void'
              ) 
              AND a.`ref_source` = 'BB'
              GROUP BY b.`component`";

     $rs1 = $db->Execute($sql1);

     #echo $sql1;
     #exit();
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs1)){
        while($row=$rs1->FetchRow()){
           $data[$rowindex] = array('rowindex' => $rowindex+1,
                          'blood_component' => $row['blood_component'], 
                          'o_deposited'     => (int) $row['o_deposited'],
                          'o_crossmatched'  => (int) $row['o_crossmatched'],
                          'o_transfused'    => (int) $row['o_transfused'],
                          'a_deposited'     => (int) $row['a_deposited'],
                          'a_crossmatched'  => (int) $row['a_crossmatched'],
                          'a_transfused'    => (int) $row['a_transfused'],
                          'b_deposited'     => (int) $row['b_deposited'],
                          'b_crossmatched'  => (int) $row['b_crossmatched'],
                          'b_transfused'    => (int) $row['b_transfused'],
                          'ab_deposited'     => (int) $row['ab_deposited'],
                          'ab_crossmatched'  => (int) $row['ab_crossmatched'],
                          'ab_transfused'    => (int) $row['ab_transfused'],

                          'pat_deposited'     => (int) $row['pat_deposited'],
                          'pat_crossmatched'  => (int) $row['pat_crossmatched'],
                          'pat_transfused'    => (int) $row['pat_transfused'],

                          'batch_stat'        => (int) $row['batch_stat'],
                          'batch_routine'     => (int) $row['batch_routine'],


                          );
           $rowindex++;
        }
         
    }else{
       $data[0]['blood_component'] = 'No Records'; 
    }
        
