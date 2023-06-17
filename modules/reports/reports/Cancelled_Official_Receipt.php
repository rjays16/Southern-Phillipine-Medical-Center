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
$params->put('date_span','From  ' . $from . ' to ' . $to);
$params->put('header',$cr_header ? $cr_header : 'MONTHLY COLLECTION (ALL)');

$params->put('encoder',$cr_encoder);


#_________________________________________________
global $db;

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);
// exit($from);

$sql="SELECT cancel_date, or_no, or_name, cancel_reason FROM seg_pay WHERE DATE(cancel_date) BETWEEN " . $db->qstr($from) . " AND " . $db->qstr($to)." ORDER BY cancel_date";
      
        // var_dump($sql);exit();
      // echo $sql;exit();

  $i = 0;
  $data = array();
  $rs = $db->Execute($sql);

  if($rs){
    if($rs->RecordCount()){
      while($row=$rs->FetchRow()){
        $data[$i] = array(
          'no' => " ".$i + 1,
          'date'=> " ".date("m/d/Y h:iA",strtotime($row['cancel_date'])),
          'or_no'       => " ".$row['or_no'],
           'name'   => " ".utf8_decode(trim(strtoupper($row['or_name']))),
          'reason'     => " ".$row['cancel_reason']
        );
        $i++;
      }
    }else{
      $data[0]= array('date'=>'No Data');
    }
  }else{
    $data[0]['date'] = 'No records';
  }
?>
