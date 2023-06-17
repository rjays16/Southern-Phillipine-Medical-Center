<?php
/**
 * @author : Syross P. Algabre 11/26/2015 : meow
 * Report for Dental Procedure
 */

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
include('parameters.php');
// define('CONSULTATION_FEE', 00002324);
$GLOBAL_CONFIG = array();
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig();

$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);

$encounter_obj=new Encounter($encounter_nr);

if(substr_count($GLOBAL_CONFIG['dental_procedure_scaling_and_polishing'], ',')>0) {
  $dental_procedure_sp = explode(",",$GLOBAL_CONFIG['dental_procedure_scaling_and_polishing']);
  $dental_procedure_sp_n = "'".implode("','",$dental_procedure_sp)."'";
}
else {
  $dental_procedure_sp_n = "'".$GLOBAL_CONFIG['dental_procedure_scaling_and_polishing']."'";
}

if(substr_count($GLOBAL_CONFIG['dental_procedure_extraction'], ',')>0) {
  $dental_procedure_e = explode(",",$GLOBAL_CONFIG['dental_procedure_extraction']);
  $dental_procedure_e_n = "'".implode("','",$dental_procedure_e)."'";
}
else {
  $dental_procedure_e_n = "'".$GLOBAL_CONFIG['dental_procedure_extraction']."'";
}

if(substr_count($GLOBAL_CONFIG['dental_procedure_restoration'], ',')>0) {
  $dental_procedure_r = explode(",",$GLOBAL_CONFIG['dental_procedure_restoration']);
  $dental_procedure_r_n = "'".implode("','",$dental_procedure_r)."'";
}
else {
  $dental_procedure_r_n = "'".$GLOBAL_CONFIG['dental_procedure_restoration']."'";
}

$consultFees = explode(",", $GLOBAL_CONFIG['consultation_fee']);
$consultFee = "'".implode("','", $consultFees)."'";

global $db;
// added by Kenneth 04-17-2016
/*$sql = "SELECT ce.encounter_nr enr,
  sms.refno,
  cp.pid AS hrn,
  fn_get_person_name (cp.pid) AS p_name,
  IF(fn_calculate_age(DATE(ce.encounter_date),cp.date_birth),fn_get_age(DATE(ce.encounter_date),cp.date_birth),cp.age) AS encounter_age,
  cp.sex AS sex,
  fn_get_complete_address (cp.pid) AS address,
  fn_get_personell_name (sdcm.doctor_nr) AS dr_name,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,sos.name, NULL) AS procedure_desc,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,smsd.service_code, NULL) AS icd_code,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,smsd.clinical_info, NULL) AS diagnosis,
  ce.official_receipt_nr AS non_paying,
  IF(sp.cancel_date IS NOT NULL, 'CANCELLED',sp.or_no) AS consultation_or,
  IF(sp.cancel_date IS NOT NULL, 'CANCELLED',sp.amount_due) AS consultation_fee,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,IF(smsd.request_flag='paid',sp2.or_no,UCASE(sd.discountdesc)), NULL) AS procedure_or,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,IF(smsd.request_flag='paid',sp2.amount_due,smsd.chrg_amnt), NULL) AS procedure_fee
FROM care_person cp
  INNER JOIN care_encounter ce ON ce.pid = cp.pid AND ce.status NOT IN ('deleted', 'void') AND ce.consulting_dept_nr = '134' AND ce.official_receipt_nr IS NOT NULL
  LEFT JOIN seg_doctors_co_manage AS sdcm ON ce.encounter_nr = sdcm.encounter_nr AND sdcm.is_deleted = 0 
  LEFT JOIN seg_pay sp ON ce.official_receipt_nr = sp.or_no
  LEFT JOIN seg_misc_service sms ON ce.encounter_nr = sms.encounter_nr AND ce.pid = sms.pid
  LEFT JOIN seg_misc_service_details smsd ON sms.refno = smsd.refno AND smsd.is_deleted = 0 AND smsd.service_code IN (".$dental_procedure_sp_n.",".$dental_procedure_e_n.",".$dental_procedure_r_n.") AND (smsd.request_flag IS NOT NULL)
  LEFT JOIN seg_other_services sos ON sos.alt_service_code = smsd.service_code
  LEFT JOIN seg_pay_request spr ON sms.refno = spr.ref_no
  LEFT JOIN seg_pay sp2 ON spr.or_no = sp2.or_no
  LEFT JOIN seg_discount sd ON sms.discountid=sd.discountid
WHERE (DATE(ce.encounter_date) BETWEEN '$from_date_format' AND '$to_date_format') AND sp2.cancel_date IS NULL GROUP BY refno,icd_code,enr ORDER BY p_name";*/

// -----modified by julius 01-16-2016
  $sql = "SELECT ce.encounter_nr enr,
  sms.refno,
  cp.pid AS hrn,
  fn_get_person_name (cp.pid) AS p_name,
  IF(fn_calculate_age(DATE(ce.encounter_date),cp.date_birth),fn_get_age(DATE(ce.encounter_date),cp.date_birth),cp.age) AS encounter_age,
  cp.sex AS sex,
  fn_get_complete_address (cp.pid) AS address,
  fn_get_personell_name (sdcm.doctor_nr) AS dr_name,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,sos.name, NULL) AS procedure_desc,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,smsd.service_code, NULL) AS icd_code,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0,smsd.clinical_info, NULL) AS diagnosis,
  ce.official_receipt_nr AS non_paying,
  IF(sp.cancel_date IS NOT NULL, 'CANCELLED',sp.or_no) AS consultation_or,
  IF(sp.cancel_date IS NOT NULL, 'CANCELLED',(SELECT SUM(amount_due) FROM seg_pay_request 
    											WHERE or_no = sp.or_no AND service_code IN (".$consultFee."))) AS consultation_fee,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0, IF(smsd.request_flag = 'paid', sp2.or_no, IF(smsd.request_flag = 'lingap' OR smsd.request_flag = 'cmap' OR smsd.request_flag = 'map', UCASE(smsd.request_flag), UCASE(sd.discountdesc))), NULL) AS procedure_or,
  IF(smsd.request_flag IS NOT NULL OR smsd.request_flag != 0, IF(smsd.request_flag = 'paid' OR smsd.request_flag = 'lingap' OR smsd.request_flag = 'cmap' OR smsd.request_flag = 'map', IF(smsd.adjusted_amnt > 0, smsd.adjusted_amnt, smsd.chrg_amnt), sp2.amount_due), NULL) AS procedure_fee
FROM care_person cp
  INNER JOIN care_encounter ce ON ce.pid = cp.pid AND ce.status NOT IN ('deleted', 'void') AND ce.consulting_dept_nr = '134' AND ce.official_receipt_nr IS NOT NULL
  LEFT JOIN seg_doctors_co_manage AS sdcm ON ce.encounter_nr = sdcm.encounter_nr AND sdcm.is_deleted = 0 
  LEFT JOIN seg_pay sp ON ce.official_receipt_nr = sp.or_no
  LEFT JOIN seg_misc_service sms ON ce.encounter_nr = sms.encounter_nr AND ce.pid = sms.pid AND sms.is_cash = 1
  LEFT JOIN seg_misc_service_details smsd ON sms.refno = smsd.refno AND smsd.is_deleted = 0 AND smsd.service_code IN (".$dental_procedure_sp_n.",".$dental_procedure_e_n.",".$dental_procedure_r_n.") AND (smsd.request_flag IS NOT NULL)
  LEFT JOIN seg_other_services sos ON sos.alt_service_code = smsd.service_code
  LEFT JOIN seg_pay_request spr ON sms.refno = spr.ref_no AND smsd.service_code = spr.service_code
  LEFT JOIN seg_pay sp2 ON spr.or_no = sp2.or_no AND smsd.service_code = spr.service_code
  LEFT JOIN seg_discount sd ON sms.discountid=sd.discountid
WHERE (DATE(ce.encounter_date) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format).") 
  AND sp.cancel_date IS NULL AND sp2.cancel_date IS NULL  GROUP BY refno,icd_code,enr ORDER BY p_name ASC";
// end
$i = 0;
$data = array();
$result = $db->Execute($sql);

// added by Kenneth 04-17-2016
$sql2 = "SELECT COUNT(CASE WHEN icd_code IN (".$dental_procedure_sp_n.") THEN 1 ELSE NULL END) AS tnsp, COUNT(CASE WHEN icd_code IN (".$dental_procedure_e_n.") THEN 1 ELSE NULL END) AS tne, COUNT(CASE WHEN icd_code IN (".$dental_procedure_r_n.") THEN 1 ELSE NULL END) AS tnr, COUNT(DISTINCT(enr)) AS tnc, COUNT(CASE WHEN icd_code IS NOT NULL THEN 1 ELSE NULL END) AS tnp FROM ($sql) AS derived_table";
$result2 = $db->GetRow($sql2);
// var_dump($sql2);die();
//end kenneth


if ($result) {
  if ($result->RecordCount()) {
    
    $new_array = array();
    $filtered_array = array();
    $hasOne = array();
    $combined_array = array();
    $idx = 0;
    $idx2 = 0;

    while ($row = $result->FetchRow()) {

      if($row['non_paying']){
        // $non_paying=null;
        $nopay=null;
        $consultation_or=null;

        if($row['non_paying']==$row['consultation_or']){

          $consultation_or=$row['consultation_or'];
        }
        else{

            $non_paying=$row['non_paying'];
          # Added by: JEFF
          # Date: 10-08-17
          # Purpose: for fetching non paying data with new saving from or_desc to or_no

          $nopay_fetch = $encounter_obj->getOPDTempDesc($non_paying);

          if ($nopay_fetch) {
                  $nopay = $nopay_fetch;
                }else{
                  $nopay = $non_paying;
                }  
              $consultation_or=$row['consultation_or'];
            }
          }
     
      $new_array[$row['enr']][$row['refno']][$i++] = array(
          'p_name' =>utf8_decode(trim(ucwords($row['p_name']))),
          'hrn' => $row['hrn'],
          'enc_nr' => $row['enr'],
          'age' => $row['encounter_age'],
          'sex' => ucwords($row['sex']),
          'address' => utf8_decode(trim($row['address'])),
          'diagnosis' => $row['diagnosis'],
          'dentist' => utf8_decode(trim(ucwords($row['dr_name']))),
          'dental_proced' => strtoupper($row['procedure_desc']),
          // 'non_paying' => $non_paying,
          'non_paying' =>$nopay,
          'or1' => ucwords($consultation_or),
          'c_fee' => number_format($row['consultation_fee'], 2),
          'or2' => (ucwords($row['procedure_or']) == 'CMAP') ? 'MAP' : ucwords($row['procedure_or']),
          'proced_fee' => number_format($row['procedure_fee'], 2)
        );
    }

    $temp = array();

    foreach($new_array as $key => $value) {
      if(count($value) > 1) {
        $temp_enc = $key;
        foreach ($value as $k => $val) {
          foreach ($val as $k2 => $v) {

            if($val[$k2]['dental_proced'] != "") {
              $filtered_array[] = $v;
            }
            else {
              if(!in_array($v, $temp)) {
                $temp[] = $v;
              }
            }
          }
        }
      }
      else {
        foreach ($value as $k => $val) {
          foreach ($val as $k2 => $v) {
            $hasOne[$idx2] = $v;
            $idx2++;
          }
        }
      }
    }

    function in_array_r($needle, $haystack) {
      foreach ($haystack as $item) {
          if (($item == $needle) || (is_array($item) && in_array_r($needle, $item))) {
              return true;
          }
      }

      return false;
    }

    for($i=0; $i < count($temp); $i++) {
      if(!in_array_r($temp[$i]['enc_nr'], $filtered_array)) {
        array_push($filtered_array, $temp[$i]);
      }
    }

    // $filtered_array = array_unique($filtered_array, SORT_REGULAR);
    $combined_array = array_merge($hasOne, $filtered_array);

    $countTotals = array();
    $temp_tnc = array();

    $extraction_regexp = '/extract/i';
    $restoration_regexp = '/resto/i';
    $scaling_regexp = '/scal/i';

    $tnp = 1;
    $tne = 1;
    $tnsp = 1;
    $tnr = 1;

    for($i = 0; $i < count($combined_array); $i++) {
      if(!in_array_r($combined_array[$i]['hrn'], $temp_tnc)) {
        array_push($temp_tnc, $combined_array[$i]);
      }

      if(preg_match($extraction_regexp, $combined_array[$i]['dental_proced'])) {
        $countTotals['tne'] = $tne++;
      }

      if (preg_match($restoration_regexp, $combined_array[$i]['dental_proced'])) {
        $countTotals['tnr'] = $tnr++;
      }

      if (preg_match($scaling_regexp, $combined_array[$i]['dental_proced'])) {
        $countTotals['tnsp'] = $tnsp++;
      }

      $countTotals['tnc'] = count($temp_tnc);
    }

    $countTotals['tnp'] = $countTotals['tne'] + $countTotals['tnr'] + $countTotals['tnsp'];

    for($i = 0; $i < count($combined_array); $i++) {
      $combined_array[$i]['tnc'] = $countTotals['tnc'];
      $combined_array[$i]['tnp'] = $countTotals['tnp'];
      $combined_array[$i]['tne'] = $countTotals['tne'];
      $combined_array[$i]['tnr'] = $countTotals['tnr'];
      $combined_array[$i]['tnsp'] = $countTotals['tnsp'];
    }

    sort($combined_array);
    $data = $combined_array;

  }else{
    $data[0] = array('hrn' => 'No Data.');
  }
}

else{
  $data[0]['hrn'] = "No Data.";
} 

