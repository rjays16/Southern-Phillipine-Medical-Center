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

$params->put('date_span',"From: " . date('F d, Y',$from_date) . " to: " . date('F d, Y',$to_date));
$params_arr = explode(",",$param);
$val_arr = explode("param_pharma_dept--", $params_arr[0]);
if ($params_arr[0] == '') {
  $phar_area = 'All Pharmacy Department';
  $pharma_department = 'All Pharmacy Department';

}else if ($val_arr[1] != '') {
  $phar_area = $val_arr[1];
  $pharma_area = "SELECT spa.area_name from seg_pharma_areas spa WHERE spa.area_code=" . $db->qstr($phar_area);
    $pharma_area_desc = $db->GetRow($pharma_area);
    // var_dump($icd_desc);die();
    $pharma_department = $pharma_area_desc['area_name'];
    // $params->put('code_type', $code_label . " - " . $icd_code ." (" . $icd_class . ")");
}

      $order = new SegOrder('pharma');
        $where_area="";
        $where_area_2="";
        $where_date="";
      if ($phar_area != 'All Pharmacy Department') {
         $where_area="and o.pharma_area= '$phar_area' ";
         $where_area_2=" and spws.pharma_area= '$phar_area' ";
      }
            
    
            $where_date="WHERE (DATE(i.serve_dt) between DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")) ";
            $where_date_2="WHERE DATE(spws.stock_date) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") and DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";
        
//         $sql="SELECT 
//   i.serve_dt AS serve_date,
//   o.pid AS pid,
//   i.bestellnum AS item_code,
//   CONCAT(
//     IF (
//       TRIM(p.name_last) IS NULL,
//       '',
//       TRIM(p.name_last)
//     ),
//     ', ',
//     IF(
//       TRIM(p.name_first) IS NULL,
//       '',
//       TRIM(p.name_first)
//     ),
//     ' ',
//     IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS fullname,
//   c.artikelname AS med_name,
//   c.generic AS gen_name,

//   SUM(i.quantity) AS qty,
//   SUM(CASE WHEN o.`charge_type`='PHIC' THEN i.quantity ELSE 0 END) AS iss_phic,
//   SUM(CASE WHEN o.`charge_type`='PAY' THEN i.quantity ELSE 0 END) AS iss_pay,
//   SUM(CASE WHEN o.`charge_type`='PERSONAL' THEN i.quantity ELSE 0 END) AS iss_tpl,
//   SUM(CASE WHEN o.`charge_type`='CHARITY' THEN i.quantity ELSE 0 END) AS iss_charity,
//   SUM(CASE WHEN o.`charge_type`='CMAP' THEN i.quantity ELSE 0 END) AS iss_map,
//   SUM(CASE WHEN o.`charge_type`='LINGAP' THEN i.quantity ELSE 0 END) AS iss_lpm,
//   SUM(CASE WHEN o.`charge_type`='POPCOM' THEN i.quantity ELSE 0 END) AS iss_pcm,
//   SUM(CASE WHEN o.`charge_type`='EP' THEN i.quantity ELSE 0 END) AS iss_ep,
//   SUM(CASE WHEN o.`charge_type`='DSWD' THEN i.quantity ELSE 0 END) AS iss_dswd,
//   SUM(CASE WHEN o.`charge_type`='PCSO' THEN i.quantity ELSE 0 END) AS iss_pcso,
//   (
//   SELECT SUM(spwsi.quantity) FROM seg_pharma_ward_stocks spws 
//   LEFT JOIN seg_pharma_ward_stock_items spwsi 
//     ON spws.`stock_nr` = spwsi.`stock_nr` 
//     WHERE DATE(spws.stock_date) BETWEEN DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ") 
//     AND DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ") AND i.`bestellnum` = spwsi.`bestellnum` 
//     AND o.`pharma_area`=spws.pharma_area
//     ) AS ward_stock, SUM(i.quantity) AS issued
// FROM
//   seg_pharma_orders AS o 
//   JOIN seg_pharma_order_items AS i 
//     ON o.refno = i.refno AND i.is_fs='1'
//   INNER JOIN care_person AS p 
//     ON o.pid = p.pid 
//   INNER JOIN care_pharma_products_main AS c 
//     ON c.bestellnum = i.bestellnum ".
//         $where_date.
//         $where_area.
//         " group by med_name order by med_name ASC";



        $sql="SELECT 
  unified.serve_date,
  unified.pid,
  unified.item_code,
  unified.fullname,
  unified.med_name,
  unified.gen_name,
  SUM(unified.qty) AS qty,
  SUM(unified.iss_phic) AS iss_phic,
  SUM(unified.iss_pay) AS iss_pay,
  SUM(unified.iss_tpl) AS iss_tpl,
  SUM(unified.iss_charity) AS iss_charity,
  SUM(unified.iss_map) AS iss_map,
  SUM(unified.iss_lpm) AS iss_lpm,
  SUM(unified.iss_pcm) AS iss_pcm,
  SUM(unified.iss_ep) AS iss_ep,
  SUM(unified.iss_ep) AS iss_ep,
  SUM(unified.iss_dswd) AS iss_dswd,
  SUM(unified.iss_pcso) AS iss_pcso,
  SUM(unified.iss_others) AS iss_others,
  SUM(unified.ward_stock) AS ward_stock,
  SUM(unified.issued) AS issued 
FROM
  ((SELECT 
    i.serve_dt AS serve_date,
    o.pid AS pid,
    i.bestellnum AS item_code,
    CONCAT(
      IF (
        TRIM(p.name_last) IS NULL,
        '',
        TRIM(p.name_last)
      ),
      ', ',
      IF(
        TRIM(p.name_first) IS NULL,
        '',
        TRIM(p.name_first)
      ),
      ' ',
      IF(
        TRIM(p.name_middle) IS NULL,
        '',
        TRIM(p.name_middle)
      )
    ) AS fullname,
    c.artikelname AS med_name,
    c.generic AS gen_name,
    SUM(i.quantity) AS qty,
    SUM(
      CASE
        WHEN o.`charge_type` = 'PHIC' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_phic,
    SUM(
      CASE
        WHEN (sp.or_no IS NOT NULL OR o.`charge_type` = 'PAY') 
        THEN i.quantity 
        ELSE 0 
      END
    )/IF((sp.or_no IS NOT NULL),COUNT(i.`bestellnum`),1) AS iss_pay,
    SUM(
      CASE
        WHEN (o.`charge_type` = 'PERSONAL' AND o.is_cash='0')
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_tpl,
    SUM(
      CASE
        WHEN o.`charge_type` = 'CHARITY' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_charity,
    SUM(
      CASE
        WHEN o.`charge_type` = 'CMAP' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_map,
    SUM(
      CASE
        WHEN o.`charge_type` = 'LINGAP' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_lpm,
    SUM(
      CASE
        WHEN o.`charge_type` = 'POPCOM' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_pcm,
    SUM(
      CASE
        WHEN o.`charge_type` = 'EP' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_ep,
    SUM(
      CASE
        WHEN o.`charge_type` = 'DSWD' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_dswd,
    SUM(
      CASE
        WHEN o.`charge_type` = 'PCSO' 
        THEN i.quantity 
        ELSE 0 
      END
    ) AS iss_pcso,
    SUM(
      CASE
        WHEN !(o.`charge_type` = 'PCSO' OR o.`charge_type` = 'DSWD' OR o.`charge_type` = 'EP' OR o.`charge_type` = 'POPCOM' OR o.`charge_type` = 'LINGAP' OR o.`charge_type` = 'CMAP' OR o.`charge_type` = 'CHARITY' OR (o.`charge_type` = 'PERSONAL' AND o.is_cash='0') OR (sp.or_no IS NOT NULL) OR o.`charge_type` = 'PHIC' OR o.`charge_type` = 'PAY')
        THEN i.quantity 
        ELSE 0 
      END
    )/IF((sp.or_no IS NOT NULL),COUNT(i.`bestellnum`),1) AS iss_others,
    0 AS ward_stock,
    SUM(i.quantity)/IF((sp.or_no IS NOT NULL),COUNT(i.`bestellnum`),1) AS issued 
  FROM
    seg_pharma_orders AS o 
    JOIN seg_pharma_order_items AS i 
      ON o.refno = i.refno 
      AND i.is_fs = '1' 
    INNER JOIN care_person AS p 
      ON o.pid = p.pid 
    INNER JOIN care_pharma_products_main AS c 
      ON c.bestellnum = i.bestellnum 
    LEFT JOIN seg_pay_request spr ON o.refno=spr.ref_no AND spr.ref_source='PH'
    LEFT JOIN seg_pay sp ON spr.or_no = sp.or_no
  ".$where_date.$where_area."
  GROUP BY o.refno,i.bestellnum
  ORDER BY med_name ASC) 
  UNION
  (SELECT 
    spws.stock_date AS serve_date,
    '' AS pid,
    spwsi.bestellnum AS item_code,
    '' AS fullname,
    cppm.artikelname AS med_name,
    cppm.generic AS gen_name,
    0 AS qty,
    0 AS iss_phic,
    0 AS iss_pay,
    0 AS iss_tpl,
    0 AS iss_charity,
    0 AS iss_map,
    0 AS iss_lpm,
    0 AS iss_pcm,
    0 AS iss_ep,
    0 AS iss_dswd,
    0 AS iss_pcso,
    0 AS iss_others,
    SUM(spwsi.quantity) AS ward_stock,
    SUM(spwsi.quantity) AS issued 
  FROM
    seg_pharma_ward_stocks spws 
    LEFT JOIN seg_pharma_ward_stock_items spwsi 
      ON spws.`stock_nr` = spwsi.`stock_nr` 
    LEFT JOIN `care_pharma_products_main` cppm 
      ON spwsi.bestellnum = cppm.bestellnum 
  ".$where_date_2.$where_area_2."
    AND cppm.is_fs = '1' 
    AND cppm.`bestellnum` = spwsi.`bestellnum`)) AS unified WHERE unified.serve_date IS NOT NULL GROUP BY unified.med_name";
        // var_dump($sql);die();
        $result = $db->Execute($sql);
        $i = 0;
        if($result){
        
    if ($result->RecordCount() > 0){
         while($row=$result->FetchRow()){
          if($row['med_name']==null){
            $data[$i]=array('serve_date'=>'',
                           'pid' => '',
                           'fullname' =>'',
                           'med_name' =>'No result',
                           'qty' => '',
                           'location' =>'',
                           'phic' =>'',
                           'item_code' =>'',
                           'pay' =>'',
                           'tpl' =>'',
                           'charity' =>'',
                           'map' =>'',
                           'lpm' =>'',
                           'pcm' =>'',
                           'ep' =>'',
                           'dswd' =>'',
                           'ward_stock' =>'',
                           'issued' =>'',
                           'pcso' =>'',
                           'others' =>'',
                  );  
          }else{
           $data[$i]=array('serve_date'=>$row['serve_date'],
                           'pid' => $row['pid'],
                           'fullname' =>utf8_decode($row['fullname']),
                           'med_name' =>utf8_decode($row['med_name']." / ".$row['gen_name']),
                           'qty' => (int)$row['qty'],
                           'location' =>$location,
                           'phic' =>(int)$row['iss_phic'],
                           'pay' =>(int)$row['iss_pay'],
                           'tpl' =>(int)$row['iss_tpl'],
                           'item_code' =>(int)$row['item_code'],
                           'charity' =>(int)$row['iss_charity'],
                           'map' =>(int)$row['iss_map'],
                           'lpm' =>(int)$row['iss_lpm'],
                           'pcm' =>(int)$row['iss_pcm'],
                           'ep' =>(int)$row['iss_ep'],
                           'dswd' =>(int)$row['iss_dswd'],
                           'issued' =>(int)$row['issued'],
                           'ward_stock' =>(int)($row['ward_stock']?$row['ward_stock']:0),
                           'pcso' =>(int)$row['iss_pcso'],
                           'others' =>(int)$row['iss_others']
                  );   
         }
         $i++;
        }
      }
      else{
           $data[$i]=array('serve_date'=>'',
                           'pid' => '',
                           'fullname' =>'',
                           'med_name' =>'No result',
                           'qty' => '',
                           'location' =>'',
                           'phic' =>'',
                           'pay' =>'',
                           'tpl' =>'',
                           'item_code' =>'',
                           'charity' =>'',
                           'map' =>'',
                           'lpm' =>'',
                           'pcm' =>'',
                           'ep' =>'',
                           'dswd' =>'',
                           'issued' =>'',
                           'ward_stock' =>'',
                           'pcso' =>'',
                           'others'=>''
                  );  
        }
      }
        else
        {
            print_r($sql);
            print_r($db->ErrorMsg());
            exit;
            # Error
        }
    
/*echo $sql; die();*/
/*$data = array();*/

/*$data = $db->GetAll($sql);*/

  


$params->put('department',$pharma_department);
$baseurl = sprintf("%s://%s%s",isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',$_SERVER['SERVER_ADDR'],substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir)));
// $data[0]['image_01'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
 ?>