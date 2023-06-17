<?php
/*
Addded By devon
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'modules/bloodBank/ajax/blood-waiver.server.php');
require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');

include('parameters.php');


$params->put('hosp_name',mb_strtoupper($hosp_name));
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);
$params->put("transactions", $transactions);

global $db;
$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']+1);

    $addCondition = "";

    if($blood_encoder == "all" || $blood_encoder == "") {
        $addCondition = "";
    }else {
        $addCondition = "AND sbm.`create_id` = (SELECT login_id FROM care_users WHERE personell_nr = ".$db->qstr($blood_encoder)." LIMIT 1)";
    }

    $sql = "SELECT 
          sbm.`pid`,
          sbm.`refno`,
          (SELECT 
            cu.name 
          FROM
            care_users cu 
          WHERE cu.login_id = sbm.`create_id`) AS users_name,
          sbm.`service_code`,
          sbt.`name` AS blood_type,
          sbm.`component`,
          sbm.`ordered_qty`,
          sbm.`serial_no`,
          sbm.`status`,
          sbm.`date_created`,
          sbm.`date_released`,
          sbm.`date_received`,
          sbm.`date_started`,
          sbm.`date_done`,
          sbm.`date_issuance`,
          sbm.`date_return`,
          sbm.`date_reissue`,
          sbm.`date_consumed`,
          sbm.`create_id`,
          sbm.`create_dt` 
        FROM
          seg_blood_monitoring sbm 
        LEFT JOIN seg_blood_type sbt
          ON sbm.`blood_type` = sbt.`id` OR sbm.`blood_type` = sbt.`name`
        WHERE sbm.`create_dt` BETWEEN DATE(".$db->qstr($from).") 
          AND DATE_ADD(DATE(".$db->qstr($to)."), INTERVAL 1 DAY) ".$addCondition."
        ORDER BY sbm.`create_dt` ASC

    ";//ORDER BY sbm.`date_created`
   //var_dump($sql);die();

   
              $i = 0;
                  $data = array();
                  $rs = $db->Execute($sql);

            if($rs){
                  if($rs->RecordCount()){
                        while($row=$rs->FetchRow()){

                          if($row['status'] == 'none') {
                            $row['status'] = 'No Sample';
                          }else if($row['status'] == 'complete'){
                            $row['status'] = 'Complete';
                          }else {
                            $row['status'] = 'Lack';
                          }

                          $date_do_format = str_replace("-", "/", $date_do[0]);
                              $data[$i] = array(
                                    'count' => $rowIndex+1,
                                    'pid' => $row['pid'],
                                    'refno' => $row['refno'],
                                    'pat_name' => utf8_decode(trim(strtoupper($row['users_name']))),
                                    'service_code' => $row['service_code'],
                                    'blood_type' => $row['blood_type'],
                                    'component' => $row['component'],
                                    'ordering' => $row['ordered_qty'],
                                    'serial_no' => $row['serial_no'],
                                    'status' => $row['status'],
                                    'date_created' => $row['date_created'],
                                    'create_dt' => $row['date_received'],
                                    'started_date' => $row['date_started'],
                                    'done_date' => $row['date_done'],
                                    'released_date' => $row['date_released'],
                                    'issuance_date' => $row['date_issuance'],
                                    'date_return' => $row['date_return'],
                                    'date_reissue' => $row['date_reissue'],
                                    'date_consumed' => $row['date_consumed']

                                   );
                              $rowIndex++;
                              $i++;
                        }
                        
                  }else{
                        $data[0]= array('pat_name'=>'No Data');
                  }
                  }else{
                  $data[0]['pat_name'] = 'No records';
            }
?>
