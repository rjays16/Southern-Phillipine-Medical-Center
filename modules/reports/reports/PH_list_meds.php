<?php 
require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path.'include/care_api_classes/class_product.php');
require_once($root_path.'include/care_api_classes/class_order.php');
include 'parameters.php';

global $db;
$pharma_obj = new SegOrder;
$order = new SegOrder('pharma');

$params->put('med_encoded',($list_med_caption != null) ? $list_med_caption : "LIST OF MEDICINES AND SUPPLIES ENCODED");
$params->put('charge_type',($list_charge_caption != null) ? $list_charge_caption : "ALL CHARGE TYPE");
$params->put('date_span',"FROM " . date('F d, Y',$from_date) . " T0 " . date('F d, Y',$to_date));
$params->put('brand_name',($list_med_caption1 != null) ? $list_med_caption1 : "MEDICINE NAME / \n SUPPLIES");

    $paramsnotexploded = $_GET['param'];
    $explodedparams = explode(',', $paramsnotexploded);

    if (!isset($patient_type) ) {
        $get_patient_type = " ";
        $area_param = "All Areas & Department";
    }elseif($patient_type == WALK_IN){
        // $get_patient_type = "AND ISNULL(o.pid) ";
        $area_param = "WALK-IN";
    }
    else{
        if ($patient_type == ER) {
            $area_param = "ER";
        }elseif($patient_type == OPD){
            $area_param = "OPD";
        }elseif($patient_type == ER_ADM || $patient_type == OPD_ADM){
            $area_param = "IPD";
        }
        $get_patient_type = " AND ce.encounter_type IN (".$db->qstr($patient_type).") ";
    }

    $date_from = strftime('%H:%M',strtotime($param_time_from));
    $date_to = strftime('%H:%M',strtotime($param_time_to));

    if($from_date == $to_date){
        if (isset($param_time_from) || isset($param_time_to)){
            $where_date="(o.orderdate between ".$db->qstr($from_date_format." ".$date_from)." and ".$db->qstr($to_date_format." ".$date_to).") ";
        }
        else {
            $where_date="o.orderdate LIKE ".$db->qstr("%".$from_date_format."%")." ";
        }
    }else{
        if (!isset($param_time_from) || !isset($param_time_to)){
            $where_date="(DATE(o.orderdate) between ".$db->qstr($from_date_format)." and ".$db->qstr($to_date_format).") ";
        }
        else{
            $where_date="(o.orderdate between ".$db->qstr($from_date_format." ".$date_from)." and ".$db->qstr($to_date_format." ".$date_to).") ";
        }
    }

    if (!isset($param_time_from) && !isset($param_time_to)) {
        $params->put('time_span',"ALL TIME");
    }else{
        $params->put('time_span',"FROM " .strftime("%I:%M %p",strtotime($param_time_from)). " TO " . strftime("%I:%M %p",strtotime($param_time_to)));
    }
    

//get Encoder
if (!isset($phar_encoder) ){
    $encoder = " ";
    $enc_name = "All Encoder";
}
elseif($phar_encoder != 'ALL encoders'){
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
  $encoder = " ";
  $enc_name = "All Encoder";
}

if (is_null($enc_name)) {
  $enc_name = "All Encoder";
}

$params->put("encoder", $enc_name);
$params->put("type_stock", $stock_title);
   
$sql =   "SELECT  o.orderdate,o.refno,o.pid,o.walkin_pid, fn_get_person_name(IFNULL(o.pid,CONCAT('W',o.walkin_pid))) `name`,\n".
        "ce.encounter_type AS enctype,ce.current_dept_nr AS curdept,prod.artikelname AS items,
         oi.quantity - IFNULL((SELECT SUM(quantity) FROM seg_pharma_return_items WHERE ref_no = oi.refno AND oi.bestellnum = bestellnum),0) AS qty,
         oi.`pricecharge` AS charge, fn_get_encoder_name(o.create_id) `create_name`,fn_get_encoder_name(oi.serve_id) `serve_name` \n".
         "FROM seg_pharma_orders AS o\n".
         "LEFT join seg_pharma_order_items AS oi ON o.refno = oi.refno \n".
        "LEFT JOIN care_pharma_products_main AS prod ON prod.bestellnum=oi.bestellnum\n".
        "LEFT JOIN care_encounter AS ce
          ON ce.`encounter_nr` = o.`encounter_nr`\n".
          "LEFT JOIN care_users `cu` ON cu.`login_id` = o.`modify_id`\n".
         "WHERE oi.serve_status IN ('S','N') AND o.is_cash = 0 AND \n".
         $where_date.
         $get_patient_type.
         $prod_class.
         $patient_charge_type.
         $encoder.
         $sql_type_stock.
        " HAVING qty > 0\n
         ORDER BY orderdate DESC\n";

        $result = $db->Execute($sql);
        $i = 1;
        if($result){
            if ($result->RecordCount() > 0){
                while($row=$result->FetchRow()){
                    $total = $row['charge'] * $row['qty'];
                    $data[$i]=array(
                    'no' =>$i,
                    'order_date' =>date("m/d/Y",strtotime($row['orderdate'])),
                    'order_time' => date("h:i:A",strtotime($row['orderdate'])),
                    'pid' => $row['pid'] ? $row['pid'] : $row['walkin_pid'],
                    'patient_name' => utf8_decode(trim($row['name'])),
                    'item_name' =>$row['items'],
                    'qty' => $row['qty'],
                    'price' =>number_format($row['charge'],2),
                    'amount' =>number_format($total,2)
                    );   
                    $i++;
                }
            }
        }else{
            print_r($sql);
            print_r($db->ErrorMsg());
            exit;
            # Error
        }
 ?>