<?php
/**
 * @author Gervie 10/25/2015
 * OPD Census of patients
 */
require_once './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path.'include/care_api_classes/class_encounter.php';
include_once($root_path.'include/care_api_classes/class_globalconfig.php');
include('parameters.php');

# Added by JEFF 12-13-17
define(abtcPHIC,85);
define(seniorCitizen,77);
define(FAMABTC,236);

global $db;

$encounter = new Encounter();

$params->put('date_span',date('F d, Y',strtotime($from_date_format))." to ".date('F d, Y',strtotime($to_date_format)));
$params->put('department', "Family Medicine - Animal Bite");

$GLOBAL_CONFIG = array();
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('cashier_consultation_fee');
$consult_fee = explode(",",$GLOBAL_CONFIG['cashier_consultation_fee']);
$glob_obj->getConfig('cashier_injection_fee');
$injection_fee = explode(",",$GLOBAL_CONFIG['cashier_injection_fee']);

$cond1 = "DATE(ce.encounter_date)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";
$cond2 = " AND ce.consulting_dept_nr = ".FAMABTC;

$consult_fee_sql = "spr.service_code LIKE '%".implode("%' OR spr.`service_code` LIKE '%", $consult_fee)."%' OR spr.`service_code` IS NULL";

$inject_fee_sql = "b.`service_code` LIKE '%".implode("%' OR b.`service_code` LIKE '%", $injection_fee)."%'";

$inject = " (SELECT  
              b.`amount_due`
            FROM
              seg_pay_request b
            WHERE b.or_no = ce.official_receipt_nr AND (
              ".$inject_fee_sql."))";

$query = "SELECT
          ce.pid AS pid,
          CONCAT(
              (SELECT 
                IF(COUNT(pid) <= 1, 'NEW', 'OLD') 
              FROM
                care_encounter 
              WHERE pid = ce.`pid`)
            ) AS patient_status,
          UPPER(fn_get_person_lastname_first (ce.pid)) AS fullname,
          DATE_FORMAT(ce.encounter_date,'%m-%d-%Y %r') AS enc_date,
          DATE_FORMAT(cp.date_reg,'%Y-%m-%d %H:%i:%s') AS date_reg,
          DATE_FORMAT(ce.encounter_date,'%Y-%m-%d %H:%i:%s') AS date_enc,
          ce.consulting_dept_nr AS dept,
          UPPER(fn_get_age (NOW(), cp.date_birth)) AS age,
          UPPER(cp.sex) AS gender,
          UPPER(fn_get_complete_address2 (cp.pid)) AS address,
          /*IFNULL($inject,0.00) AS inject,*/
          (CASE
            WHEN (
              ce.`official_receipt_nr` REGEXP '^-?[0-9]+$'
            ) 
            THEN 
            CASE
              WHEN (
                (SELECT 
                  sot.`or_desc` 
                FROM
                  `seg_opd_or_temp` AS sot 
                WHERE sot.`or_id` = ce.`official_receipt_nr`) IS NOT NULL
              ) 
              THEN (
                (SELECT 
                  sot.`or_desc` 
                FROM
                  `seg_opd_or_temp` AS sot 
                WHERE sot.`or_id` = ce.`official_receipt_nr`)
              ) 
              ELSE ce.`official_receipt_nr` 
            END 
            ELSE ce.`official_receipt_nr`
          END
          ) AS ofnr,
          spr.`amount_due` AS consultation
          FROM
            care_encounter AS ce
            INNER JOIN care_person AS cp
              ON cp.pid = ce.pid
            LEFT JOIN seg_pay AS sp
              ON sp.or_no = ce.official_receipt_nr
            LEFT JOIN seg_pay_request AS spr 
              ON spr.or_no = ce.official_receipt_nr 
          WHERE ". $cond1 . $cond2 ."
            AND ce.encounter_type = '2'
            AND ce.encounter_status NOT IN ('deleted', 'void', 'cancelled', 'hidden')
            AND (".$consult_fee_sql.")
          ORDER BY ce.encounter_date ASC";

$rs = $db->Execute($query);
$totalRecord = 0;
if($rs){
  $i = 0;

  while($row = $rs->FetchRow()){
    // Added by JEFF 12-12-17
    $hiddenvalue = $encounter->getOrAmountDue($row['pid']);
    
    if($row['ofnr'] == 'CHARITY FROM SOCIAL SERVICE'){
        $consultation = "CH FROM SC";
    }else if($row['ofnr'] == 'Senior Citizen'){
        $consultation = 'SENIOR CITIZEN';
    }else if($row['ofnr'] == abtcPHIC){
        $consultation = 'ABTC-PHIC';
    }else if($row['ofnr'] == 'CLASS D'){
        $consultation = 'CLASS D';
    }else if($row['consultation'] != 0.00 && $row['ofnr'] == 'ABTC-PHIC' || $row['consultation'] != 0.00 && $row['ofnr'] == 'SENIOR CITIZEN'){
        $consultation = $row['ofnr'];    
    }else if ((($row['consultation'] == 0.00 && $row['ofnr'] == 'ABTC-PHIC') || ($row['consultation'] == 0.00 && $row['ofnr'] == 'SENIOR CITIZEN'))) {
        $consultation = $row['ofnr'];
    }
    else{
        if(is_numeric($row['ofnr'])){
          $consultation = $row['consultation'];
          if ($consultation == 0.00) {
            $consultation = number_format($hiddenvalue, 2, '.', '');
          }else{
            $consultation = number_format($consultation, 2, '.', '');
          }
        }else $consultation = $row['ofnr'];
    }
    // end JEFF

    $if_count = $db->GetAll("SELECT b.amount_due FROM seg_pay_request b WHERE or_no = ".$db->qstr($row['ofnr'])." AND (".$inject_fee_sql.")");

    if(count($if_count) >= 1){ // start if count
      $data[$i] = array(
        'num_field' => $i + 1,
        'pid' => $row['pid'],
        'fullname' => utf8_decode(trim($row['fullname'])),
        'datetime' => $row['enc_date'],
        'age' => $row['age'],
        'gender' => $row['gender'],
        'address' => utf8_decode(trim($row['address'])),
        'consultation' => $consultation,
        'status' => $row['patient_status'],
        'inj' => number_format($if_count[0]["amount_due"], 2, '.', '')
      );

      $totalRecord += 1;
      $i++;

      if(count($if_count) > 1){
        for($a = 1; $a < count($if_count); $a++){
          $data[$i] = array(
            'num_field' => $i + 1,
            'pid' => $row['pid'],
            'fullname' => utf8_decode(trim($row['fullname'])),
            'datetime' => $row['enc_date'],
            'age' => $row['age'],
            'gender' => $row['gender'],
            'address' => utf8_decode(trim($row['address'])),
            'consultation' => number_format(0, 2, '.', ''),
            'status' => $row['patient_status'],
            'inj' => number_format($if_count[$a]["amount_due"], 2, '.', '')
          );
          $totalRecord += 1;
          $i++;
        }
      }
    }else{
      $final_inject = number_format(0, 2, '.', '');

      $encounters = $encounter->getEncounterOnDept(2,FAMABTC, $row['pid'],$row['date_enc']);
      $prev_enc_sql = '';

      if($encounters){
        foreach($encounters as $enc){
          $prev_enc_date = $enc['encounter_date'];
        }
        $prev_enc_sql = " AND DATE(sp.create_dt) > DATE(".$db->qstr($prev_enc_date).")";
      }

      $has_inject_fee = $db->GetAll("SELECT b.amount_due FROM seg_pay sp LEFT JOIN seg_pay_request b ON sp.or_no = b.or_no WHERE pid = ".$db->qstr($row['pid'])." AND (DATE(sp.create_dt) BETWEEN DATE(".$db->qstr($row['date_enc'])." - INTERVAL 5 DAY) AND DATE(".$db->qstr($row['date_enc'])."))".$prev_enc_sql." AND (".$inject_fee_sql.") ORDER BY sp.or_date");

      if(count($has_inject_fee) > 1){
        for($a = 0; $a < count($has_inject_fee); $a++){
          $final_inject = number_format($has_inject_fee[$a]["amount_due"], 2, '.', '');

          $data[$i] = array(
            'num_field' => $i + 1,
            'pid' => $row['pid'],
            'fullname' => utf8_decode(trim($row['fullname'])),
            'datetime' => $row['enc_date'],
            'age' => $row['age'],
            'gender' => $row['gender'],
            'address' => utf8_decode(trim($row['address'])),
            'consultation' => $consultation,
            'status' => $row['patient_status'],
            'inj' => $final_inject
          );
          $totalRecord += 1;
          $i++;
        }
      }else{
        $final_inject = number_format($has_inject_fee[0]["amount_due"], 2, '.', '');

        $data[$i] = array(
          'num_field' => $i + 1,
          'pid' => $row['pid'],
          'fullname' => utf8_decode(trim($row['fullname'])),
          'datetime' => $row['enc_date'],
          'age' => $row['age'],
          'gender' => $row['gender'],
          'address' => utf8_decode(trim($row['address'])),
          'consultation' => $consultation,
          'status' => $row['patient_status'],
          'inj' => $final_inject
        );
        $totalRecord += 1;
        $i++;
      }
    }
  }
  // die;
}
else{
    $data[0]['fullname'] = 'No Data';
}

$data[0]['num_records'] = $totalRecord;