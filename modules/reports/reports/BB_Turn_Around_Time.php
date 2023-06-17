<?php
/*
Created by Borj, 05/07/2014 09:00 AM
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');
$paramsnotexploded = $_GET['param'];
$explodedparams = explode(',', $paramsnotexploded);
$where = "";
$extraJoin = "";
if($explodedparams[0] == ""){
$transactions = "All";
}else if(count($explodedparams) == 1){
    $explodedparamspart2 = explode("--", $explodedparams[0]);
    if($explodedparamspart2[0] =="param_bb_trxnp"){
      if($explodedparamspart2[1] != "all"){
        if($explodedparamspart2[1] == "routine"){
          $where .="AND sbrd.is_urgents = '0'";
          $transactions = "Routine";
        }else if($explodedparamspart2[1] == "stat"){
          $where .="AND sbrd.is_urgents = '1'";
          $transactions = "Stat";
        }
      }else{
        $transactions = "All";
      }
    }else{
      if($explodedparamspart2[1] != "all"){
        $extraJoin .= "LEFT JOIN care_users `cu` ON cu.`name` = sbrs.`modify_id`";
        $where .=" AND cu.personell_nr = ".$db->qstr($explodedparamspart2[1]);
      }
    }
}else if (count($explodedparams) == 2){
  $explodeTransactionArray = explode("--", $explodedparams[0]);
  $explodeEncoderArray = explode("--", $explodedparams[1]);
  $transactions = "All";
  if($explodeTransactionArray[1]=="routine"){
    $where .="AND sbrd.is_urgents = '0'";
    $transactions = "Routine";
  }else if($explodeTransactionArray[1] == "stat"){
    $where .="AND sbrd.is_urgents = '1'";
    $transactions = "Stat";
  }
  if($explodeEncoderArray[1] != "all"){
     $extraJoin .= "LEFT JOIN care_users `cu` ON cu.`name` = sbrs.`modify_id`";
     $where .=" AND cu.personell_nr = ".$db->qstr($explodeEncoderArray[1]);
  }

}

#_________________________________________________
$params->put('hosp_name',mb_strtoupper($hosp_name));
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
$params->put("transactions", $transactions);
#_________________________________________________
global $db;

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

// $sql ="SELECT
//       fn_get_person_name (d.pid) as pat_name,
//       b.`serial_no` as unit_test,
//       (
//       CASE
//        b.`result` 
//        WHEN 'noresult' 
//        THEN 'NO RESULT' 
//        WHEN 'compat' 
//        THEN 'COMPATIBLE' 
//        WHEN 'incompat' 
//        THEN 'INCOMPATIBLE' 
//        WHEN 'retype' 
//        THEN 'RETYPING' 
//        ELSE b.`result` 
//       END
//       ) AS result,
//       DATE_FORMAT(b.`received_date`, '%m-%d-%Y %r') AS date_received,
//       IF ((a.done_date='0000-00-00 00:00:00'),'',DATE_FORMAT(a.`done_date`, '%m-%d-%Y %r')) AS date_done,
//       TIMEDIFF(a.done_date,b.received_date) AS turn_around_time

     
// FROM seg_blood_received_status a INNER JOIN seg_blood_received_details b 
// ON (a.`refno` = b.refno AND a.`service_code`=b.`service_code`)
// inner join seg_lab_serv c on a.`refno` = c.`refno`
// and $bb_based_datess
// inner join care_person d on c.`pid` = d.pid
// WHERE a.done_date <> '' and a.`ordering` = b.`ordering` AND
// DATE($bb_based_dates) BETWEEN 
// (".$db->qstr($from).") 
// AND (".$db->qstr($to).")
// AND  c.`status` NOT IN (
//     'deleted',
//     'hidden',
//     'inactive',
//     'void'
//   )".$where;  


$sql = "SELECT fn_get_person_name (sls.pid) AS pat_name,
sbrd.`serial_no` AS unit_test,
(CASE sbrd.`result` 
  WHEN 'noresult' 
    THEN 'NO RESULT' 
  WHEN 'compat' 
    THEN 'COMPATIBLE' 
  WHEN 'incompat' 
    THEN 'INCOMPATIBLE' 
  WHEN 'retype' 
    THEN 'RETYPING' 
  ELSE sbrd.`result` 
 END) AS result,
 DATE_FORMAT(sbrd.`received_date`, '%m-%d-%Y %r') AS date_received,
 IF ((sbrs.done_date='0000-00-00 00:00:00'),'',DATE_FORMAT(sbrs.`done_date`, '%m-%d-%Y %r')) AS date_done,
 TIMEDIFF(sbrs.done_date,sbrd.received_date) AS turn_around_time
FROM seg_blood_received_status `sbrs`
INNER JOIN seg_blood_received_details `sbrd`
ON sbrs.`refno` = sbrd.`refno`
AND sbrs.`service_code` = sbrd.`service_code`
INNER JOIN seg_lab_serv `sls`
ON sbrs.`refno` = sls.`refno`
".$extraJoin."
WHERE sbrs.`done_date` <> ''
AND sbrs.`ordering` = sbrd.`ordering`
AND DATE(sbrs.`done_date`) BETWEEN (".$db->qstr($from).") AND (".$db->qstr($to).")
AND sls.`status` NOT IN (
    'deleted',
    'hidden',
    'inactive',
    'void')".$where;

              $i = 0;
                  $data = array();
                  $rs = $db->Execute($sql);

            if($rs){
                  if($rs->RecordCount()){
                        while($row=$rs->FetchRow()){
                          $date_redo = explode(" ", $row['date_received']);
                          $data_redo_format = str_replace("-", "/", $date_redo[0]);

                          $date_do = explode(" ", $row['date_done']);

                          // added by: syboy; 05/17/2015
                          $extraction = explode("--", $explodedparams[0]);
                          if ($extraction[1] == "routine") {
                            if (strtotime($row['turn_around_time']) > strtotime("05:00:00")) {
                            $result = "<span style='color:red;'>{$row['turn_around_time']}</sanp>";
                            } else {
                              $result = "<span style='color:black;'>{$row['turn_around_time']}</sanp>";
                            }
                          } else {
                              $result = "<span style='color:black;'>{$row['turn_around_time']}</sanp>";
                          }
                          // end
                          $date_do_format = str_replace("-", "/", $date_do[0]);
                              $data[$i] = array(
                                    'pat_name' => utf8_decode(trim(strtoupper($row['pat_name']))),
                                    'unit_test' => $row['unit_test'],
                                    'result' => $row['result'],
                                    'date_recorded' => $data_redo_format."\n ".$date_redo[1]." ".$date_redo[2],
                                    'date_done' => $date_do_format."\n".$date_do[1]." ".$date_do[2],
                                    'turn_around_time' => $result
                                   );
                              $i++;
                        }
                        
                  }else{
                        $data[0]= array('pat_name'=>'No Data');
                  }
                  }else{
                  $data[0]['pat_name'] = 'No records';
            }
?>
