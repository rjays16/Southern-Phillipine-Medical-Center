<?php
/*
Created by Borj, 05/07/2014 09:00 AM
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');


#_________________________________________________
$params->put('hosp_name',mb_strtoupper($hosp_name));
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
$params->put('header',$cr_header ? $cr_header : 'MONTHLY COLLECTION (ALL)');

$params->put('encoder',$cr_encoder);


#_________________________________________________
global $db;

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
$borj = 'adawdawd';

$where = array();

//account type
if (!isset($cr_type) || $cr_type == 'all') {
} else if ($cr_type == 'affiliation') {
    $where[] = "fn_get_pay_account_type_monthly_report(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) = " . $db->qstr($cr_type);
} else if ($cr_type == 'ctscan') {
    $where[] = "fn_get_pay_account_type_monthly_report(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) = " . $db->qstr($cr_type);
} else if ($cr_type == 'hi') {
    $where[] = "fn_get_pay_account_type_monthly_report(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) = " . $db->qstr($cr_type);
} else if ($cr_type == 'mri') {
    $where[] = "fn_get_pay_account_type_monthly_report(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) = " . $db->qstr($cr_type);
} else if ($cr_type == 'payw') {
    $where[] = "fn_get_pay_account_type_monthly_report(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) = " . $db->qstr($cr_type);
}

//encoder
if (!isset($cr_encoder) || $cr_encoder == 'ALL encoders') {

} else {
    $where[] = "cu.personell_nr = " . $db->qstr($cr_encoder);
}

//date and shifts
if (!isset($cr_start_from) || !isset($cr_start_to)){
    $where[] = "DATE(pay.`or_date`) 
               BETWEEN
                    DATE(" . $db->qstr($from) . ")
               AND
                    DATE(" . $db->qstr($to) . ")"; 
}else {
  $where[] = "DATE(pay.`or_date`) 
               BETWEEN
                    DATE(" . $db->qstr($from. " " .$cr_start_from) . ")
               AND
                    DATE(" . $db->qstr($to. " " .$cr_start_to) . ")";
     
}

//or
if (!isset($cr_or_from) || !isset($cr_or_to)){

}else{
    $where[] = "pay.or_no BETWEEN (".$db->qstr($cr_or_from).") AND (".$db->qstr($cr_or_to).")";

}

$condition = implode(') AND (', $where);


$sql="SELECT
        fn_get_pay_account_type_monthly_report(pr.ref_source, pr.ref_no, pr.service_code, pr.or_no) AS account,
        pay.or_date AS date_time,
        pay.or_no AS or_no,
        
        IF(pay.cancel_date IS NULL,pay.amount_due,0) AS amount,
        IF(pay.cancel_date IS NULL,'','Cancelled') AS notes,
        pay.or_name AS name,
        pay.`create_id` AS create_id,
        cu.`name` AS encoder
        
        FROM seg_pay_request AS pr
        INNER JOIN seg_pay AS pay ON pr.or_no=pay.or_no
        LEFT JOIN care_users AS cu ON pay.`create_id`=cu.`login_id`
        WHERE ($condition)
        group by pay.or_no
        order by pay.`or_date`";
      
        //var_dump($from);
      //echo $sql;exit();

                  $i = 0;
                  $data = array();
                  $rs = $db->Execute($sql);

                                  if($rs){
                                        if($rs->RecordCount()){
                                              while($row=$rs->FetchRow()){
                                                    $data[$i] = array(
                                                          'date_time'=> date("m/d/Y",strtotime($row['date_time'])),
                                                          'or'       => $row['or_no'],
                                                          'amount'   => (float)$row['amount'],
                                                          //'amount' => number_format($amount, 2),
                                                          'name'     => mb_convert_encoding(strtoupper($row['name']),"ISO-8859-1",'utf-8'),
                                                          'header'   => $header,
                                                          'encoder'  => $encoder,
                                                          'notes'   => $row['notes']

                                                           
                                                          
                                                          
                                                          

                                                         );
                                                    $i++;
                                              }
                                              
                                        }else{
                                              $data[0]= array('date_time'=>'No Data');
                                        }
                                        }else{
                                        $data[0]['date_time'] = 'No records';
                                  }
?>
