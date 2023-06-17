<?php
/*
Created by Borj, 10/29/2014 01:22 AM
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

#_________________________________________________
global $db;
$paramsnotexploded = $_GET['param'];
$explodedparams = explode(',', $paramsnotexploded);
$where = "";
$extraJoin = "";

if($explodedparams[0] == ""){

$transactions = "ALL COMPONENTS - Routine and Stat";

}else if(count($explodedparams) == 1){ # edeited by: syboy 08/14/2015

    $explodedparamspart2 = explode("--", $explodedparams[0]);

    if($explodedparamspart2[0] =="param_bb_trxnp"){
      $transactions_1 = "All COMPONENTS - ";
      if($explodedparamspart2[1] != "all"){
        if($explodedparamspart2[1] == "routine"){
          $where .="AND sbrd.is_urgents = '0'";
          $transactions = "Routine";
        }else if($explodedparamspart2[1] == "stat"){
          $where .="AND sbrd.is_urgents = '1'";
          $transactions = "Stat";
        }
      }else{
        $transactions = "Routine and Stat";
      }
    }else if ($explodedparamspart2[0] == 'param_bb_trxnb') {
      if ($explodedparamspart2[1] != "all") {
        if ($explodedparamspart2[1] == "redcell") {
          $where .= "AND sbrd.component IN ('ALIQUOT', 'PRBC', 'WB', 'WB_PRBC')";
          $transactions_1 = "RED CELL PRODUCTS - Routine and Stat";
        }else if ($explodedparamspart2[1] == "plasma") {
          $where .= "AND sbrd.component IN ('CRYO', 'FFP', 'PC')";
          $transactions_1 = "PLASMA CELL PRODUCTS - Routine and Stat";
        }
      }else{
          $transactions_1 = "ALL COMPONENTS - Routine and Stat";
      }
    }else{
      $transactions_1 = "ALL COMPONENTS - Routine and Stat";
      if($explodedparamspart2[1] != "all"){
        $extraJoin .= "LEFT JOIN care_users `cu` ON cu.`name` = sbrs.`modify_id`";
        $where .=" AND cu.personell_nr = ".$db->qstr($explodedparamspart2[1]);
      }
    }

}else if (count($explodedparams) == 2){ # edited by: syboy 08/14/2015
  $transactions_1 = "All COMPONENTS - ";
  $explodeTransactionArray = explode("--", $explodedparams[0]);
  $explodeTransactionArray_2 = explode("--", $explodedparams[1]);
  // var_dump($explodeTransactionArray); die();
  if ($explodeTransactionArray[0] == "param_bb_trxnp") {
    if($explodeTransactionArray[1]=="routine"){
      $where .="AND sbrd.is_urgents = '0'";
      $transactions = "Routine";
    }else if($explodeTransactionArray[1] == "stat"){
      $where .="AND sbrd.is_urgents = '1'";
      $transactions = "Stat";
    }else{
      $transactions = "Routine and Stat";
    }
  }else if ($explodeTransactionArray[0] == "param_bb_trxnb") {
    if ($explodeTransactionArray[1] != "all") {
      if ($explodeTransactionArray[1] == "redcell") {
        $where .= "AND sbrd.component IN ('ALIQUOT', 'PRBC', 'WB', 'WB_PRBC')";
        $transactions_1 = "RED CELL PRODUCTS - Routine and Stat";
      }else if ($explodeTransactionArray[1] == "plasma") {
        $where .= "AND sbrd.component IN ('CRYO', 'FFP', 'PC')";
        $transactions_1 = "PLASMA CELL PRODUCTS - Routine and Stat";
      }
    }else{
        $transactions_1 = "All COMPONENTS - Routine and Stat";
    }
  }else if ($explodeTransactionArray[0] == "param_bb_encoder") {
    if($explodeTransactionArray[1] != "all"){
       $extraJoin .= "LEFT JOIN care_users `cu` ON cu.`name` = sbrs.`modify_id`";
       $where .=" AND cu.personell_nr = ".$db->qstr($explodeTransactionArray[1]);
    }else{
      $transactions_1 = "All COMPONENTS - ";
    }
  }
  
  if ($explodeTransactionArray_2[0] == "param_bb_trxnp") {
    if($explodeTransactionArray_2[1]=="routine"){
      $where .="AND sbrd.is_urgents = '0'";
      $transactions = "Routine";
    }else if($explodeTransactionArray_2[1] == "stat"){
      $where .="AND sbrd.is_urgents = '1'";
      $transactions = "Stat";
    }else{
      $transactions = "Routine and Stat";
    }
  }else if ($explodeTransactionArray_2[0] == "param_bb_trxnb") {
    if ($explodeTransactionArray_2[1] != "all") {
      if ($explodeTransactionArray_2[1] == "redcell") {
        $where .= "AND sbrd.component IN ('ALIQUOT', 'PRBC', 'WB', 'WB_PRBC')";
        $transactions_1 = "RED CELL PRODUCTS - ";
      }else if ($explodeTransactionArray_2[1] == "plasma") {
        $where .= "AND sbrd.component IN ('CRYO', 'FFP', 'PC')";
        $transactions_1 = "PLASMA CELL PRODUCTS - ";
      }
    }else{
        $transactions_1 = "All COMPONENTS - ";
    }
  }else if ($explodeTransactionArray_2[0] == "param_bb_encoder") {
    // $transactions_1 .= "All COMPONENTS - ";
    if($explodeTransactionArray_2[1] != "all"){
       $extraJoin .= "LEFT JOIN care_users `cu` ON cu.`name` = sbrs.`modify_id`";
       $where .=" AND cu.personell_nr = ".$db->qstr($explodeTransactionArray_2[1]);
    }
  }

}else if (count($explodedparams) == 3) { # added by: syboy 08/14/2015

  $explodeTransactionArray = explode("--", $explodedparams[0]);
  $explodeTransactionArray_2 = explode("--", $explodedparams[1]);
  $explodeTransactionArray_3 = explode("--", $explodedparams[2]);

  if ($explodeTransactionArray[0] == "param_bb_trxnp") {
    if($explodeTransactionArray[1]=="routine"){
      $where .="AND sbrd.is_urgents = '0'";
      $transactions = "Routine";
    }else if($explodeTransactionArray[1] == "stat"){
      $where .="AND sbrd.is_urgents = '1'";
      $transactions = "Stat";
    }else{
      $transactions = "Routine and Stat";
    }
  }

  if ($explodeTransactionArray_2[0] == "param_bb_trxnb") {
    if ($explodeTransactionArray_2[1] != "all") {
      if ($explodeTransactionArray_2[1] == "redcell") {
        $where .= "AND sbrd.component IN ('ALIQUOT', 'PRBC', 'WB', 'WB_PRBC')";
        $transactions_1 = "RED CELL PRODUCTS - ";
      }else if ($explodeTransactionArray_2[1] == "plasma") {
        $where .= "AND sbrd.component IN ('CRYO', 'FFP', 'PC')";
        $transactions_1 = "PLASMA CELL PRODUCTS - ";
      }
    }else{
        $transactions_1 = "All COMPONENTS - ";
    }
  }

  if ($explodeTransactionArray_3[0] == "param_bb_encoder") {
    if($explodeTransactionArray_3[1] != "all"){
       $extraJoin .= "LEFT JOIN care_users `cu` ON cu.`name` = sbrs.`modify_id`";
       $where .=" AND cu.personell_nr = ".$db->qstr($explodeTransactionArray_3[1]);
    }
  }

} # end

$params->put("transactions", $transactions_1.$transactions);
$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

$sql = "SELECT fn_get_person_name(slv.pid) as  pat_name,
              sbrd.serial_no AS unit_test,
              (CASE sbrd.result 
              WHEN 'noresult' 
                THEN 'NO RESULT' 
              WHEN 'compat' 
                THEN 'COMPATIBLE' 
              WHEN 'incompat' 
                THEN 'INCOMPATIBLE' 
              WHEN 'retype' 
                THEN 'RETYPING' 
              ELSE sbrd.result 
              END) AS result,
              sbrd.is_urgents AS defining,
    DATE_FORMAT(sbrs.started_date,'%m-%d-%Y %r') AS date_started,
    IF (sbrs.done_date = '0000-00-00 00:00:00','',
    DATE_FORMAT(sbrs.done_date,'%m-%d-%Y %r')) AS date_done,
  TIMEDIFF(sbrs.done_date, sbrs.started_date) AS processing_time
  FROM seg_blood_received_status `sbrs`
  INNER JOIN seg_blood_received_details `sbrd`
  ON sbrs.`refno` = sbrd.`refno`
  and sbrs.`service_code` = sbrd.`service_code`
  INNER join seg_lab_serv `slv`
  on sbrs.`refno` = slv.`refno`
  ".$extraJoin."
  WHERE sbrs.`started_date` <> ''
  and sbrs.`ordering` = sbrd.`ordering`
  and date(sbrs.`done_date`) between (".$db->qstr($from).") and (".$db->qstr($to).")
  AND slv.`status` NOT IN ('deleted', 'hidden', 'inactive', 'void')".$where;

              $i = 0;
                  $data = array();
                  $rs = $db->Execute($sql);
                  // var_dump($rs->FetchRow());
                  // die();
            if($rs){
                  if($rs->RecordCount()){
                        while($row=$rs->FetchRow()){
                              $date_redo = explode(" ", $row['date_started']);
                              $data_redo_format = str_replace("-", "/", $date_redo[0]);

                              $date_do = explode(" ", $row['date_done']);
                              $date_do_format = str_replace("-", "/", $date_do[0]);

                              //  added by: syboy; 05/16/2015
                              $extraction = explode("--", $explodedparams[0]);
                              if ($extraction[1] == "routine") {
                                if (strtotime($row['processing_time']) > strtotime("01:30:00")) {
                                  $result = "<span style='color:red;'>{$row['processing_time']}</sanp>";
                                } else {
                                  $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                }
                              } elseif($extraction[1] == "stat") {
                                if (strtotime($row['processing_time']) > strtotime("01:00:00")) {
                                  $result = "<span style='color:red'>{$row['processing_time']}</sanp>";
                                } else {
                                  $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                }
                              }elseif ($extraction[1] == "all") {

                                if ($row['defining'] == '1') {

                                    if (strtotime($row['processing_time']) > strtotime("01:00:00")) {
                                    $result = "<span style='color:red'>{$row['processing_time']}</sanp>";
                                  } else {
                                    $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                  }

                                } elseif ($row['defining'] == '0') {

                                    if (strtotime($row['processing_time']) > strtotime("01:30:00")) {
                                    $result = "<span style='color:red;'>{$row['processing_time']}</sanp>";
                                  } else {
                                    $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                  }

                                } else {
                                  $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                }
                              } else{ # added by: syboy 08/14/2015
                                if ($row['defining'] == '1') {
                                    if (strtotime($row['processing_time']) > strtotime("01:00:00")) {
                                    $result = "<span style='color:red'>{$row['processing_time']}</sanp>";
                                  } else {
                                    $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                  }
                                } elseif ($row['defining'] == '0') {
                                    if (strtotime($row['processing_time']) > strtotime("01:30:00")) {
                                    $result = "<span style='color:red;'>{$row['processing_time']}</sanp>";
                                  } else {
                                    $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                  }

                                } else {
                                  $result = "<span style='color:black;'>{$row['processing_time']}</sanp>";
                                }
                              }                             
                              // end

                              $data[$i] = array(
                                    'pat_name' => utf8_decode(trim(strtoupper($row['pat_name']))),
                                    'unit_test' => $row['unit_test'],
                                    'result' => $row['result'],
                                    'date_started' => $data_redo_format."\n".$date_redo[1]." ".$date_redo[2],
                                    'date_done' => $date_do_format."\n".$date_do[1]." ".$date_do[2],
                                    'processing_time' => $result
                                   );
                              $i++;

                              // var_dump($extraction);
                              // die();
                        }
                        
                        
                  }else{
                        $data[0]= array('pat_name'=>'No Data');
                  }
                  }else{
                  $data[0]['pat_name'] = 'No records';
            }
            // var_dump($data);
            // die();
           
?>
