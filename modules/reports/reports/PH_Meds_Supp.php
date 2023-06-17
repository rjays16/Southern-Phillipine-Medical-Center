<?php 
/*
 * @author : Darryl 12/29/2016
 */ 
require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path.'include/care_api_classes/class_product.php');
/*include($root_path."include/care_api_classes/class_order.php");*/
require_once($root_path.'include/care_api_classes/class_order.php');
include 'parameters.php';


global $db;
$pharma_obj = new SegOrder;
$order = new SegOrder('pharma');

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);

$params->put('date_span',"From " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));

        $paramsnotexploded = $_GET['param'];
        $explodedparams = explode(',', $paramsnotexploded);



if (!isset($patient_type) ) {
$get_patient_type = " ";
$area_param = "All Areas & Department";
}elseif($patient_type == 8){
  $get_patient_type = "AND ISNULL(ce.encounter_type) ";
  $area_param = "WALK-IN";
}
else{
  if ($patient_type == 1) {
    $area_param = "ER";
  }elseif($patient_type == 2){
      $area_param = "OPD";
  }elseif($patient_type == 3 || 4){
      $area_param = "IPD";
  }
  $get_patient_type = " AND ce.encounter_type IN (".$db->qstr($patient_type).") ";
}
#die($patient_type);

$date_from = strftime('%H:%M',strtotime($param_time_from));
$date_to = strftime('%H:%M',strtotime($param_time_to));

if($from_date == $to_date){
if (isset($param_time_from) || isset($param_time_to)){
 $where_date="(o.orderdate between ".$db->qstr($from_date_format." ".$date_from)." and ".$db->qstr($to_date_format." ".$date_to).") ";
   $params->put('time_span',"From " .strftime("%I:%M %p",strtotime($param_time_from)). " to " . strftime("%I:%M %p",strtotime($param_time_to)));
}
else {
 $where_date="o.orderdate LIKE ".$db->qstr($from_date_format."%")." ";

}
}else{
  if (!isset($param_time_from) || !isset($param_time_to)){
$where_date="(DATE(o.orderdate) between ".$db->qstr($from_date_format)." and ".$db->qstr($to_date_format).") ";
}
else {
 $where_date="(o.orderdate between ".$db->qstr($from_date_format." ".$date_from)." and ".$db->qstr($to_date_format." ".$date_to).") ";

}
}

//get Department or
if (isset($dept_ward) ){
        $exploded_count_6_1 = explode('--', $explodedparams[1]);
if ($exploded_count_6_1[1] == 'ward') {
  $phar_area = "AND cw.nr IN (".$db->qstr($exploded_count_6_1[2]).") ";
   $phars = $order->getWardInfo($exploded_count_6_1[2]);
}else{
    $phar_area = "AND ce.`current_dept_nr` IN (".$db->qstr($exploded_count_6_1[1]).") ";
     $phar = $order->getDeptAllInfo($exploded_count_6_1[1]);
    }
}else{
  $phar_area = " ";
}

//get Encoder
if (!isset($phar_encoder) ){
  $encoder = " ";
  $enc_name = "All Encoder";
}
elseif($phar_encoder != '-All'){

  $encoder =" AND cu.personell_nr = ".$db->qstr($phar_encoder);
  $sql_encoder = "SELECT fn_get_personellname_lastfirstmi(".$db->qstr($phar_encoder).") AS enc_name FROM 
  care_users AS ps WHERE ps.personell_nr = ".$db->qstr($phar_encoder)."";
   $result = $db->Execute($sql_encoder);
  if($result->RecordCount() > 0){
    while ($row = $result->FetchRow()) {
      $enc_name = $row['enc_name'];
          }
  }else{
    $encoder = " ";
    $enc_name = "All Encoder";
  }
}
else{
#$exploded_encoder_params = explode('--', $explodedparams[2]);
  $encoder = " ";
  $enc_name = "All Encoder";
}
/*die($exploded_encoder_params[0]);*/

if (is_null($enc_name)) {
  $enc_name = "All Encoder";
}


$params->put("area",$area_param);
$params->put("encoder", $enc_name);

   
/*
        $where_date="";

            $where_date="(o.orderdate between ".$db->qstr($from_date_format." ".$cr_start_from)." and ".$db->qstr($to_date_format." ".$cr_start_to).") ";*/
        
$sql =   "SELECT  o.orderdate,o.refno,o.pid, fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) `name`,\n".
        "o.is_cash,o.charge_type,a.area_name AS `area_full`,o.is_urgent,ce.current_room_nr AS `current_room` ,ce.current_ward_nr AS `current_ward`,cw.ward_id AS `wardname`,\n".
        "ce.encounter_type AS enctype, ce.er_location AS erloc, ce.er_location_lobby AS erloclob,ce.current_dept_nr AS curdept,prod.artikelname AS items,oi.quantity AS qty,fn_get_encoder_name(o.create_id) `create_name`,fn_get_encoder_name(oi.serve_id) `serve_name` \n".
         "FROM seg_pharma_orders AS o\n".
         "LEFT join seg_pharma_order_items AS oi ON o.refno = oi.refno \n".
        "INNER JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
        "LEFT JOIN care_encounter AS ce
          ON ce.`encounter_nr` = o.`encounter_nr` AND ce.encounter_nr != '' \n".
          "  LEFT JOIN care_ward AS cw
         ON ce.`current_ward_nr` = cw.`nr`\n".
          "LEFT JOIN seg_pharma_areas AS a ON a.area_code=o.pharma_area\n".
          "LEFT JOIN care_department AS cd ON cd.nr = ce.current_dept_nr\n".
          "LEFT JOIN care_users `cu` ON cu.`login_id` = o.`modify_id`\n".
         "WHERE oi.serve_status IN ('S') AND  \n".
         $where_date.
          $get_patient_type.
         $phar_area.
         $encoder.
         #$where_area.
       # $where_ward.
        "ORDER BY orderdate DESC\n";

#die($sql);
        $result = $db->Execute($sql);
        $i = 1;
        if($result){
      
    if ($result->RecordCount() > 0){
    
         while($row=$result->FetchRow()){

      if ($row['enctype']==1){
      
        $erLoc = $order->getERLocation($row['erloc'], $row['erloclob']);
    
        if($erLoc['area_location'] != '')
            $location = "ER - " . $erLoc['area_location'] . " (" . $erLoc['lobby_name'] . ")";
        else
            $location = "EMERGENCY ROOM";
      }elseif ($row['enctype']==2){
        $dept = $order->getDeptAllInfo($row['current_dept_nr']);

        $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));

      }elseif(($row['enctype']==4)|| ($row['enctype']==3)){

        $dward = $order->getWardInfo($row['current_ward']);

        $location = strtoupper(strtolower(stripslashes($dward['ward_id'])))." Rm # :" .$row['current_room'];


      }elseif ($row['enctype']==6){      
        $location = "Industrial clinic";
      }else{
        $location = 'WALK-IN';
      }
           $data[$i]=array(
                           'no' =>$i,
                           'patient_name' => utf8_decode(trim($row['name'])),
                           'item_name' =>$row['items'],
                           'qty' => $row['qty'],
                           'location' =>$location,
                            'order_date' =>date("m/d/Y",strtotime($row['orderdate'])),
                            'order_time' => date("h:i:A",strtotime($row['orderdate'])),
                            'date' =>$row['orderdate']
                  );   
$i++;
             }

             
        }
      }
        else
        {
            print_r($sql);
            print_r($db->ErrorMsg());
            exit;
            # Error
        }
    

/*$baseurl = sprintf("%s://%s%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_ADDR'],substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir)));
$data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";*/
 ?>