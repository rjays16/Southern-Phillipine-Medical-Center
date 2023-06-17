<?php 
/*
 * @author : syboy 07/22/2015
 */ 
require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
require_once($root_path.'include/care_api_classes/class_product.php');
include($root_path."include/care_api_classes/class_order.php");
include 'parameters.php';

/*$year = $_GET['year'];
$month = $_GET['month'];

$start = strtotime($year . '-' . $month . '-01');
$end = strtotime('+1 month', $start);
$month_year = date('M',$start) . " " . date('Y',$start);

$start = date('Y-m-d',$start);
$end = date('Y-m-d',$end);
*/
// die('wew');

global $db;

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);

$params->put('date_span',"From: " . date('F d, Y',$from_date) .' -'. " To:" . date('F d, Y',$to_date));
$params_arr = explode(",",$param);
$val_arr = explode("param_pharma_dept--", $params_arr[0]);
if ($params_arr[0] == '') {
  $phar_area = 'All Pharmacy Department';
  $pharma_department = 'All Pharmacy Department';

}else if ($val_arr[1] != '') {
  $phar_area = $val_arr[1];
  $pharma_area = "SELECT spa.area_name from seg_pharma_areas spa WHERE spa.area_code=" . $db->qstr($phar_area);
    $pharma_area_desc = $db->GetRow($pharma_area);
    // var_dump($pharma_area_desc);die();
    $pharma_department = $pharma_area_desc['area_name'];
    // $params->put('code_type', $code_label . " - " . $icd_code ." (" . $icd_class . ")");
}

      $order = new SegOrder('pharma');
        $where_area="";
        $where_area_2="";
        $where_date="";
      if ($phar_area != 'All Pharmacy Department') {
     $where_area=" and o.pharma_area= '$phar_area' ";
     $where_area_2=" and spws.pharma_area= '$phar_area' ";
      }
            
    
            $where_date="WHERE DATE(o.orderdate) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";
            $where_date_2="WHERE DATE(spws.stock_date) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";
        
//         $sql="SELECT
// (SELECT 
//   CONCAT(
//     prod.`artikelname`,
//     ' / \n',
//     prod.`generic`
//   ) FROM care_pharma_products_main AS prod WHERE prod.bestellnum =oi.bestellnum ) AS med_name,
//   SUM(oi.quantity) as qty, oi.bestellnum AS item_code,
//         (
//   SELECT SUM(spwsi.quantity) FROM seg_pharma_ward_stocks spws 
//   LEFT JOIN seg_pharma_ward_stock_items spwsi 
//     ON spws.`stock_nr` = spwsi.`stock_nr` 
//     ".$where_date_2." AND  oi.`bestellnum` = spwsi.`bestellnum` 
//     AND o.`pharma_area` = spws.pharma_area
//     ) AS ward_stock 
// FROM
//   seg_pharma_orders AS o 
//   LEFT JOIN seg_pharma_order_items AS oi 
//     ON o.refno = oi.refno "
//     .$where_date.$where_area."
//     AND oi.is_fs = '0'
//   AND !(oi.`serve_status`='N' AND oi.`requested_qty`=oi.quantity)
//   AND (o.`serve_status` = 'S' OR oi.`serve_status`='S')
//  GROUP BY  oi.`bestellnum` ORDER BY med_name ASC ";
        
        $sql="SELECT unified.med_name,SUM(unified.qty) AS qty,unified.item_code,SUM(unified.ward_stock) AS ward_stock FROM ((SELECT 
  (SELECT 
    CONCAT(
      prod.`artikelname`,
      ' / ',
      prod.`generic`
    ) 
  FROM
    care_pharma_products_main AS prod 
  WHERE prod.bestellnum = oi.bestellnum) AS med_name,
  SUM(oi.quantity) AS qty,
  oi.bestellnum AS item_code,
  '' AS ward_stock
FROM
  seg_pharma_orders AS o 
  LEFT JOIN seg_pharma_order_items AS oi 
    ON o.refno = oi.refno 
".$where_date.$where_area."
  AND oi.is_fs = '0' 
  AND (
    o.`serve_status` = 'S' 
    OR oi.`serve_status` = 'S'
  ) 
GROUP BY oi.`bestellnum`,o.`refno`)
UNION
(SELECT (SELECT 
    CONCAT(
      prod.`artikelname`,
      ' / ',
      prod.`generic`
    ) 
  FROM
    care_pharma_products_main AS prod
  WHERE prod.bestellnum = spwsi.bestellnum) AS med_name,'' AS qty,spwsi.bestellnum AS item_code,
    SUM(spwsi.quantity) ward_stock
  FROM
    seg_pharma_ward_stocks spws 
    LEFT JOIN seg_pharma_ward_stock_items spwsi 
      ON spws.`stock_nr` = spwsi.`stock_nr` 
    LEFT JOIN `care_pharma_products_main` cppm 
      ON spwsi.bestellnum = cppm.bestellnum 
  ".$where_date_2.$where_area_2."
    AND cppm.is_fs = '0' 
    GROUP BY spwsi.`bestellnum`)) AS unified WHERE unified.item_code IS NOT NULL GROUP BY item_code ORDER BY med_name ASC";
    // var_dump($sql);die();
        $result = $db->Execute($sql);
        $i = 0;
  if($result){
    if ($result->RecordCount() > 0){
      while($row=$result->FetchRow()){
        if($row['med_name']==null){
          $data[$i]=array(
            'med_name' =>'No Data',
            'no_issued_meds' =>'',
            'ward_stock' =>'',
            'no_issued_meds' =>'',
            'total_issued_meds' =>''
          );  
        }else{
          $data[$i]=array(
          'med_name' =>utf8_decode($row['med_name']),
          'item_code' =>" ".utf8_decode($row['item_code']),
          'ward_stock' =>(int)($row['ward_stock']?$row['ward_stock']:0),
          'no_issued_meds' =>(int)($row['qty']?$row['qty']:0),
          'total_issued_meds' =>((int)($row['qty']?$row['qty']:0))+((int)($row['ward_stock']?$row['ward_stock']:0))
        ); 
        }
          
        $i++;
      }
    }
    else{
      $data[$i]=array(
        'med_name' =>'No Data',
        'no_issued_meds' =>'',
        'ward_stock' =>'',
        'item_code' =>'',
        'total_issued_meds' =>''
      );  
    }
  }else{
    print_r($sql);
    print_r($db->ErrorMsg());
    exit;
  }
    
/*echo $sql; die();*/
/*$data = array();*/

/*$data = $db->GetAll($sql);*/

  


$params->put('department',$pharma_department);
$baseurl = sprintf("%s://%s%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_ADDR'],substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir)));
// $data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
 ?>