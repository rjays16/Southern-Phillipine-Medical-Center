<?php
require_once('roots.php');
require_once $root_path . 'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);
$params->put("gyne_spmc", $baseurl . "img/gyne_spmc.png");
$params->put("gyne_logo", $baseurl . "img/gyne_logo.jpg");   


/* Note if you edit this getAllSonolgist make sure nakaarrange para NO IR will happen Thank you.*/
$getAllSonologist = $db->GetRow("SELECT ccg.`value` FROM care_config_global AS ccg WHERE ccg.`type` ='all_sonologist'");

$getPersonnelInfo = $db->GetRow("SELECT job_function_title FROM care_personell as cp WHERE cp.nr = ".$db->qstr($_SESSION['sess_user_personell_nr']));


// print_r($_SESSION);die();

// die($order);

if($order=='descending'){
  $orderby = 'ORDER BY srdp.`create_dt`  DESC';
}else{
  $orderby = 'ORDER BY srdp.`create_dt`  ASC';
}
$sql = "SELECT 
srs.pid,
srs.refno,
   srdp.`create_dt` AS request_date,
  srdp.`service_code`,
  srs.`pid` as patient_name,
  (SELECT seg_radio_services.`name` FROM seg_radio_services WHERE service_code = srdp.service_code) AS name,
  SUM(srdp.pf_amount) as pf_amount,
  srdp.`dr_nr` as doctor_name,
  `fn_get_person_lastname_first`(srs.`pid`) as patient_name,
  cttr.request_flag as request_flag,
  srs.is_cash,
  stc.charge_name
FROM
  seg_radio_serv as srs 
  INNER JOIN care_test_request_radio as cttr 
   ON cttr.refno = srs.refno 
  INNER JOIN `seg_radio_doctors_pf` as srdp 
   ON srs.`refno` = srdp.`refno`AND cttr.`service_code` = srdp.`service_code`
   LEFT JOIN seg_type_charge as stc 
   ON cttr.request_flag = stc.id
  WHERE srs.`status` NOT IN ('deleted') 
  AND srs.`fromdept` = 'OBGUSD' AND  
          DATE(srdp.create_dt)
               BETWEEN
                    DATE(".$db->qstr(date($from_date_format)).")
                    AND
                    DATE(".$db->qstr(date($to_date_format)).") 

    $procedure_code
    $gyne_doctor
 GROUP BY patient_name,
  srdp.`create_dt`,
  srdp.`service_code`,
  srdp.`dr_nr`,
  stc.charge_name $orderby";


// var_dump($sql);exit();
$data = array();
$dataSonologist = array();
 $count=1;
foreach ($db->GetAll($sql) as $key => $datum) {
  // $or_no =
//   echo "<pre>";
// print_r($datum['charge_name']);
// echo "</pre>";
       if ($datum['is_cash']==0){
              if(is_null($datum['charge_name'])){
                  $or_no="Charge";
              }else{
                   $or_no=$datum['charge_name'];
              }
             
              #$paid = 0;
           }else{
                     if ($datum["request_flag"]=='paid'){
                        $sql_paid = "SELECT pr.or_no, pr.ref_no,pr.service_code
                                          FROM seg_pay_request AS pr
                                          INNER JOIN seg_pay AS p ON p.or_no=pr.or_no AND p.pid='".$datum['pid']."'
                                          WHERE pr.ref_source = 'OB'  AND pr.ref_no = '".trim($datum["refno"])."'
                                          AND (ISNULL(p.cancel_date) OR p.cancel_date='0000-00-00 00:00:00') LIMIT 1";
                            $rs_paid = $db->Execute($sql_paid);
                            if ($rs_paid){
                                $result2 = $rs_paid->FetchRow();
                                $or_no = $result2['or_no'];
                            }
                            // die("x");
                           
                            #for temp workaround
                            if (!$or_no){
                              $sql_manual = "SELECT * FROM seg_payment_workaround WHERE service_area='RD' AND refno='".trim($datum["refno"])."' AND is_deleted=0";

                              $res_manual=$db->Execute($sql_manual);
                              $row_manual_count=$res_manual->RecordCount();
                              $row_manual = $res_manual->FetchRow();
        
                              $or_no = $row_manual['control_no'];
      
                            }
      
                     }elseif ($datum["request_flag"]=='charity'){
                        $sql_paid = "SELECT pr.grant_no AS or_no, pr.ref_no,pr.service_code
                                          FROM seg_granted_request AS pr
                                          WHERE pr.ref_source = 'OB' AND pr.ref_no = '".trim($datum["refno"])."'
                                          LIMIT 1";
                        $rs_paid = $db->Execute($sql_paid);
                        if ($rs_paid){
                            $result2 = $rs_paid->FetchRow();
                            $or_no = 'CLASS D';
                        }
                     }elseif (($datum["request_flag"]!=NULL)||($datum["request_flag"]!="")){
                       if ($withOR)
                          $or_no = $off_rec;
      else  
                          $or_no = $datum["charge_name"]== "CMAP" ? "MAP" :$datum["charge_name"];
                     }
                   }


    // array_push($dataSonologist, $datum['doctor_name']);


    $array = array(
        'count' => $count,
        'pf_amount' =>(int)$datum['pf_amount'],
        'date_request' => $datum['request_date'],
        'procedure' => $datum['name'],
        'patient_name' => utf8_decode(trim($datum['patient_name'])),
        'doctor_name' => utf8_decode(trim($datum['doctor_name'])),
        'or_no' => $or_no,
        'sub_amount' =>(double)$datum['pf_amount'],

    );
     if(!empty($datum['request_date'])){
     $count++; 
     
    }
    array_push($data, $array);
   
}
// var_dump($count);die();
$params->put("total_rows",$count); 
$params->put("user_loginid",$_SESSION['sess_user_name']); 
$params->put("user_position",$getPersonnelInfo['job_function_title']); 
$params->put("sonologist",$getAllSonologist['value']);
$params->put("title", $report_title);
// exit(); 
  // echo "<pre>";
  // print_r($data);
  // echo "</pre>";
  // die();
