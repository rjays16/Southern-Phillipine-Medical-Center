<?php 
/*
 * @author : Darryl 12/29/2016
 */ 
require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path.'include/care_api_classes/class_product.php');
include($root_path."include/care_api_classes/class_order.php");
include 'parameters.php';

global $db;
$pharma_obj = new SegOrder;
$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('date_span',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));

$params_arr = explode(",",$param);
$val_arr = explode("param_pharma_dept--", $params_arr[0]);
if ($params_arr[0] == ''){
  $phar_area = 'All Pharmacy Department';
}else if ($val_arr[1] != ''){
  $phar_area = $val_arr[1];
}

if ($phar_area != 'All Pharmacy Department'){
  $phar = $pharma_obj->getPharArea($phar_area)->FetchRow();
  $params->put("department",$phar['area_name']);
}else{
   $params->put("department","All Pharmacy Department");
}

$order = new SegOrder('pharma');
$where_area="";
$where_date="";
if ($phar_area != 'All Pharmacy Department') {
  $where_area=" and dertab.pharma_area= '$phar_area' ";
  $isoFooter = $pharma_obj->getPharAreaISO($phar_area);
  if($isoFooter) $params->put("rep_footer",$isoFooter);
  else $params->put("rep_footer","");
}else $params->put("rep_footer","");
$where_date=" DATE(spo.orderdate) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";
$where_date2=" spoi.`refno` IN (SELECT 
  spr.`ref_no` 
FROM
  `seg_pay_request` spr 
  LEFT JOIN seg_pay sp 
    ON spr.`or_no` = sp.`or_no` 
  LEFT JOIN `seg_pharma_order_items` spoi ON spr.`ref_no`=spoi.`refno`
WHERE spr.`ref_source` = 'PH' AND sp.`cancel_date` IS NULL
  AND DATE(sp.`or_date`) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . "))";
$sql="SELECT 
  bestellnum,
  serve_date,
  orderdate,
  serve_date_cash,
  pid,
  wpid,
  fullname,
  wfullname,
  med_name,
  SUM(qty) as qty,
  erloc,
  area_full,
  erloclob,
  current_dept_nr,
  enctype,
  current_ward,
  is_cash,
  current_room_nr FROM ((SELECT 
  spoi.bestellnum,
  spoi.serve_dt AS serve_date,
  spo.create_time AS orderdate,
  sp.or_date AS serve_date_cash,
  spo.pid AS pid,
  sw.pid AS wpid,
  CONCAT(
    IF (
      TRIM(cp.name_last) IS NULL,
      '',
      TRIM(cp.name_last)
    ),
    ', ',
    IF(
      TRIM(cp.name_first) IS NULL,
      '',
      TRIM(cp.name_first)
    ),
    ' ',
    IF(
      TRIM(cp.name_middle) IS NULL,
      '',
      TRIM(cp.name_middle)
    )
  ) AS fullname,
  CONCAT(
    IF (
      TRIM(sw.name_last) IS NULL,
      '',
      TRIM(sw.name_last)
    ),
    ', ',
    IF(
      TRIM(sw.name_first) IS NULL,
      '',
      TRIM(sw.name_first)
    ),
    ' ',
    IF(
      TRIM(sw.name_middle) IS NULL,
      '',
      TRIM(sw.name_middle)
    )
  ) AS wfullname,
(SELECT 
  IF(
    prod.generic = '',
    prod.artikelname,
    prod.generic
  ) AS med_name FROM care_pharma_products_main AS prod WHERE prod.bestellnum =spoi.bestellnum ) AS med_name,
  SUM(spoi.quantity) AS qty,
  ce.er_location AS erloc,
  spa.area_name AS area_full,
  ce.er_location_lobby AS erloclob,
  ce.current_dept_nr,
  ce.encounter_type AS enctype,
  ce.current_ward_nr AS current_ward,
  spo.is_cash,
  ce.current_room_nr,
  spo.refno,spo.pharma_area
FROM
  seg_pharma_order_items spoi 
  LEFT JOIN seg_pharma_orders spo 
    ON spoi.refno = spo.refno 
  LEFT JOIN seg_pay_request spr 
    ON spo.refno = spr.ref_no 
  LEFT JOIN seg_pay sp 
    ON spr.or_no = sp.or_no 
  LEFT JOIN care_person AS cp 
    ON spo.pid = cp.pid 
  LEFT JOIN seg_walkin AS sw 
    ON spo.walkin_pid = sw.pid 
  LEFT JOIN care_encounter AS ce 
    ON spo.encounter_nr = ce.encounter_nr 
    AND ce.encounter_nr!=''
  LEFT JOIN seg_pharma_areas AS spa 
    ON spo.pharma_area = spa.area_code
    WHERE "
    .$where_date."
  AND !(spoi.`serve_status`='N' AND spoi.`requested_qty`=spoi.quantity)
  AND spo.`serve_status`='S'
  AND spo.`is_deleted`='0'
 GROUP BY  spo.refno,med_name ORDER BY spo.create_time,med_name ASC)
 UNION
 (SELECT 
   spoi.bestellnum,
  spoi.serve_dt AS serve_date,
  spo.create_time AS orderdate,
  sp.or_date AS serve_date_cash,
  spo.pid AS pid,
  sw.pid AS wpid,
  CONCAT(
    IF (
      TRIM(cp.name_last) IS NULL,
      '',
      TRIM(cp.name_last)
    ),
    ', ',
    IF(
      TRIM(cp.name_first) IS NULL,
      '',
      TRIM(cp.name_first)
    ),
    ' ',
    IF(
      TRIM(cp.name_middle) IS NULL,
      '',
      TRIM(cp.name_middle)
    )
  ) AS fullname,
  CONCAT(
    IF (
      TRIM(sw.name_last) IS NULL,
      '',
      TRIM(sw.name_last)
    ),
    ', ',
    IF(
      TRIM(sw.name_first) IS NULL,
      '',
      TRIM(sw.name_first)
    ),
    ' ',
    IF(
      TRIM(sw.name_middle) IS NULL,
      '',
      TRIM(sw.name_middle)
    )
  ) AS wfullname,
(SELECT 
  IF(
    prod.generic = '',
    prod.artikelname,
    prod.generic
  ) AS med_name FROM care_pharma_products_main AS prod WHERE prod.bestellnum =spoi.bestellnum ) AS med_name,
  ROUND(SUM(spoi.quantity)/COUNT(spo.refno)) AS qty,
  ce.er_location AS erloc,
  spa.area_name AS area_full,
  ce.er_location_lobby AS erloclob,
  ce.current_dept_nr,
  ce.encounter_type AS enctype,
  ce.current_ward_nr AS current_ward,
  spo.is_cash,
  ce.current_room_nr,
  spo.refno,spo.pharma_area
FROM
  seg_pharma_order_items spoi 
  LEFT JOIN seg_pharma_orders spo 
    ON spoi.refno = spo.refno 
  LEFT JOIN seg_pay_request spr 
    ON spo.refno = spr.ref_no 
  LEFT JOIN seg_pay sp 
    ON spr.or_no = sp.or_no 
  LEFT JOIN care_person AS cp 
    ON spo.pid = cp.pid 
  LEFT JOIN seg_walkin AS sw 
    ON spo.walkin_pid = sw.pid 
  LEFT JOIN care_encounter AS ce 
    ON spo.encounter_nr = ce.encounter_nr 
    AND ce.encounter_nr!=''
  LEFT JOIN seg_pharma_areas AS spa 
    ON spo.pharma_area = spa.area_code
    WHERE "
    .$where_date2."
 GROUP BY  spo.refno,med_name ORDER BY spo.create_time,med_name ASC)) as dertab
 WHERE DATE(dertab.serve_date) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")
".$where_area."
 GROUP BY pid,med_name
 ORDER BY orderdate,med_name ASC";
// die($sql);die();
$result = $db->Execute($sql);
$i = 0;
if($result){
  if($result->RecordCount() > 0){
    while($row=$result->FetchRow()){
      if($row['pid'] == NULL){
        $pName =$row['wfullname'];
        $pPID = $row['wpid'];
      }else{
        $pName =$row['fullname'];
        $pPID = $row['pid'];
      }
      if($row['serve_date'] == NULL){
        $sDate = date('Y-m-d h:i A', strtotime($row['orderdate']));
      }else{
        $sDate = date('Y-m-d h:i A', strtotime($row['serve_date']));
      }
      if($row['enctype']==1){
        $erLoc = $order->getERLocation($row['erloc'], $row['erloclob']);
        if($erLoc['area_location'] != ''){
          $location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
        }
        else{
          $location = "EMERGENCY ROOM";
        }
      }elseif($row['enctype']==2){
        $dept = $order->getDeptAllInfo($row['current_dept_nr']);
        $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
      }elseif(($row['enctype']==4)|| ($row['enctype']==3)){
        $dward = $order->getWardInfo($row['current_ward']);
        $location = strtoupper(strtolower(stripslashes($dward['ward_id'])))." Rm # :" .$row['current_room_nr'];
      }elseif ($row['enctype']==6){      
        $location = "Industrial clinic";
      }else{
        $location = 'WALK-IN';
      }
      $data[$i]=array('serve_date'=>$sDate,
                      'pid' => $pPID,                         
                      'fullname' => utf8_decode(trim($pName)),                         
                      'med_name' => $row['med_name'],
                      'qty' => $row['qty'],
                      'location' =>$location
                      );   
      $i++;
    }
  }else{
    $data[0]=array('fullname' =>'No records found for this report');
  }
}else{
  print_r($sql);
  print_r($db->ErrorMsg());
  exit;
}
/*$baseurl = sprintf("%s://%s%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_ADDR'],substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir)));
$data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";*/
 ?>