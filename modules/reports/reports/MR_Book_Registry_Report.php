<?php
/**
 * @author Gervie 10/25/2015
 * OPD Census of patients
 */
require_once './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_encounter.php';
include('parameters.php');

# Added by JEFF 12-13-17
define(abtcPHIC,85);
define(seniorCitizen,77);

global $db;

$baseurl = sprintf(
          "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
        );
$params->put('r_spmc', $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put('l_spmc', $baseurl . "img/doh.jpg");

$encounter = new Encounter();

$params->put("hosp_country", $hosp_country);
$params->put("hosp_agency", mb_strtoupper($hosp_agency));
$params->put("hosp_name", mb_strtoupper($hosp_name));
$params->put("hosp_addr", $hosp_addr1);

$params->put('date_span'," From ".date('F d, Y',strtotime($from_date_format))." to ".date('F d, Y',strtotime($to_date_format)));


$cond1 = "DATE(cp.date_birth)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";

if($order=='descending'){
  $cond3="  fullname DESC";
}elseif($order=='ascending'){
  $cond3="  fullname ASC";
}else{
  $cond3=" date_birth ASC"; 
}

$query = "SELECT 
  cp.pid AS pid,
  CONCAT(
    cp.name_last,
    ', ',
    cp.name_first,
    ' ',
    cp.name_middle
  ) AS fullname,
  scb.birth_type AS birth_type,
  scb.birth_order AS birth_order,
  scb.birth_type_others AS birth_order_other,
  CONCAT(cp.date_birth,' ', cp.`birth_time`) AS date_birth,
  CONCAT(
    scb.m_name_last,
    ', ',
    scb.m_name_first,
    ' ',
    scb.m_name_middle
  ) AS m_name,
  UPPER(cp.sex) AS gender,
  CONCAT(
    scb.f_name_last,
    ', ',
    scb.f_name_first,
    ' ',
    scb.f_name_middle
  ) AS f_name,
  `fn_get_personellname_lastfirstmiddle`(scb.attendant_name) AS attendant
FROM care_person AS cp  
  INNER JOIN seg_cert_birth AS scb 
    ON scb.pid = cp.pid 
WHERE ".$cond1.$sex.$birthtype."  
ORDER BY ".$cond3." ";

$rs = $db->Execute($query);

$totalRecord = 0;
if($rs){
    if($rs->RecordCount() > 0){
        $i = 0;

        while($row = $rs->FetchRow()){

          if($row['birth_type']=='1'){
          $row['birth_type']='Single';
          }elseif ($row['birth_type']=='2') {
          $row['birth_type']='Twins';
          }elseif ($row['birth_type']=='3') {
          $row['birth_type']='Triplets';
          }else{
          $row['birth_type']=$row['birth_order_other'];
          }
            $data[$i] = array(
                'num_field' => $i + 1,
                'pid' => $row['pid'],
                'name' => utf8_decode(trim(strtoupper($row['fullname']))),
                'birth_type' => strtoupper($row['birth_type']),
                'birth_order' => strtoupper($row['birth_order']),
                'gender' => $row['gender'],
                'date_birth' => date('F j, Y h:i A',strtotime($row['date_birth'])),
                'm_name' => utf8_decode(trim(strtoupper($row['m_name']))),
                'f_name' => utf8_decode(trim(strtoupper($row['f_name']))),
                'a_name' => utf8_decode(trim(strtoupper($row['attendant']))),
                
            );
            $totalRecord += 1;
            $i++;
                 
        }
    }
    else{
        $data[0]['fullname'] = 'No Data';
    }
}
else{
    $data[0]['fullname'] = 'No Data';
}

$data[0]['num_records'] = $totalRecord;