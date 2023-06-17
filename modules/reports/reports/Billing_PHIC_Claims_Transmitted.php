<?php
/**
 * @author Arco - 06/15/2016
 */

require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$params->put('hosp_name', "SOUTHERN PHILIPPINES MEDICAL CENTER");
$params->put('title', "SUMMARY OF PHIC CLAIMS TRANSMITTED ");
$params->put('date_span',"Period: " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));


$cond1 = "DATE(st.transmit_dte)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond_status = '';
if($PHIC_claims_status == 'mapped'){
  $rep_parameter = "MAPPED";
  $cond_status = " AND sete.`is_mapped` = 1 AND sete.`is_uploaded` = 1 ";
}
elseif($PHIC_claims_status == 'notuploaded'){
  $rep_parameter = "NOT UPLOADED";
  $cond_status = " AND (sete.`is_uploaded` = 0 OR sete.`is_uploaded` IS NULL) ";
}else{
  $rep_parameter = "ALL";
}

$params->put('rep_parameter', $rep_parameter);

$sql = "SELECT 
st.`transmit_dte` AS transmittal_date,
st.`transmit_no` AS transmittal_no,
sete.`is_mapped`,
sete.`is_uploaded`,
ce.`pid` AS pid,
ce.encounter_nr AS encounter_no,
(SELECT
            rduTransaction.transaction_date
          FROM
            seg_dialysis_request AS rduRequest
            INNER JOIN seg_dialysis_prebill AS rduPreBill
            ON rduRequest.encounter_nr = rduPreBill.encounter_nr
            INNER JOIN seg_dialysis_transaction AS rduTransaction
            ON rduPreBill.bill_nr = rduTransaction.transaction_nr
          WHERE rduRequest.encounter_nr = ce.encounter_nr
          ORDER BY rduTransaction.transaction_date
          LIMIT 1) AS ADMISSION_DATE2,
        (SELECT 
            rduTransaction.`datetime_out` 
          FROM
            seg_dialysis_request AS rduRequest 
            INNER JOIN seg_dialysis_prebill AS rduPreBill 
              ON rduRequest.encounter_nr = rduPreBill.encounter_nr 
              AND rduPreBill.bill_type IN ('PH','NPH')  
            INNER JOIN seg_dialysis_transaction AS rduTransaction 
              ON rduPreBill.bill_nr = rduTransaction.transaction_nr 
          WHERE rduRequest.encounter_nr = ce.encounter_nr 
          ORDER BY rduTransaction.datetime_out DESC 
          LIMIT 1 )AS DISCHARGE_DATE2,
sbe.bill_nr,
IF(seim.insurance_nr='',siro.title,seim.insurance_nr) AS insurance_no,
IF(seim.`relation` = 'M', '1','0') AS is_member,
memcategory_desc AS category,
sn.name_last AS last_name,
sn.name_first AS first_name,
  sn.name_middle AS middle_name,
  sbe.bill_dte,
  DATE_FORMAT(
     IF(admission_dt IS NULL 
        OR admission_dt = '', encounter_date, admission_dt),
    '%b %e, %Y %l:%i%p'
  ) AS admission_date,
    IF(
    STR_TO_DATE(
      ce.mgh_setdte,
      '%Y-%m-%d %H:%i:%s'
    ) != '0000-00-00 00:00:00',
    DATE_FORMAT(
      STR_TO_DATE(
        ce.mgh_setdte,
        '%Y-%m-%d %H:%i:%s'
      ),
      '%m/%e/%Y %H:%i'
    ),
    DATE_FORMAT(
      STR_TO_DATE(sbe.bill_dte, '%Y-%m-%d %H:%i:%s'),
      '%m/%e/%Y %H:%i'
    )
  ) AS discharge_date,
  IF(
    STR_TO_DATE(
      ce.mgh_setdte,
      '%Y-%m-%d %H:%i:%s'
    ) != '0000-00-00 00:00:00',
    UNIX_TIMESTAMP(
      STR_TO_DATE(ce.mgh_setdte, '%Y-%m-%d')
    ),
    UNIX_TIMESTAMP(STR_TO_DATE(sbe.bill_dte, '%Y-%m-%d'))
  ) AS order_date,
  IF(SUM(hci_amount),SUM(hci_amount),(sbc.total_acc_coverage+sbc.total_med_coverage+sbc.total_sup_coverage+sbc.total_srv_coverage+sbc.total_ops_coverage)) AS hci,
  IF(SUM(pf_amount),SUM(pf_amount),(sbc.`total_d1_coverage`+sbc.`total_d2_coverage`+sbc.`total_d3_coverage`+sbc.`total_d4_coverage`)) AS pf,
  (IF(SUM(hci_amount),SUM(hci_amount),(sbc.total_acc_coverage+sbc.total_med_coverage+sbc.total_sup_coverage+sbc.total_srv_coverage+sbc.total_ops_coverage))
  +IF(SUM(pf_amount),SUM(pf_amount),(sbc.`total_d1_coverage`+sbc.`total_d2_coverage`+sbc.`total_d3_coverage`+sbc.`total_d4_coverage`)))
   AS total_coverage
FROM care_encounter ce
LEFT JOIN seg_transmittal_details stdtls
ON ce.`encounter_nr`=stdtls.`encounter_nr`
LEFT JOIN seg_transmittal st
ON st.`transmit_no`=stdtls.`transmit_no`
LEFT JOIN seg_eclaims_transmittal_ext sete
ON st.`transmit_no` = sete.`transmit_no`
LEFT JOIN care_person cp
ON ce.pid=cp.`pid`
LEFT JOIN care_person_insurance cpi
ON ce.`pid`=cpi.`pid`
LEFT JOIN seg_encounter_insurance_memberinfo seim
ON ce.`encounter_nr`=seim.`encounter_nr` AND seim.hcare_id = st.hcare_id AND ce.`pid`=seim.`pid`
LEFT JOIN seg_encounter_memcategory sem
ON ce.`encounter_nr`=sem.`encounter_nr`
LEFT JOIN seg_memcategory sm
ON sem.`memcategory_id`=sm.memcategory_id
LEFT JOIN seg_billing_encounter sbe 
ON ce.encounter_nr=sbe.encounter_nr AND sbe.is_deleted IS NULL
LEFT JOIN seg_billing_coverage sbc
ON sbe.bill_nr=sbc.bill_nr
LEFT JOIN seg_billing_caserate sbcr
ON sbe.bill_nr=sbcr.bill_nr
LEFT JOIN seg_encounter_name AS sn
ON ce.encounter_nr = sn.encounter_nr
LEFT JOIN seg_encounter_insurance AS sei
ON ce.encounter_nr = sei.encounter_nr
LEFT JOIN seg_insurance_remarks_options AS siro
ON sei.remarks = siro.id

WHERE " . $cond1 . $cond_status.
            "GROUP BY ce.encounter_nr ORDER BY order_date, last_name";

$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){

          if ($row['is_member'] == 1) {
            $isMember = "Member";
          }
          if ($row['is_member'] == 0) {
            $isMember = "Dependent";
          }

            $data[$i] = array(
                'num' => $i + 1,
                'transmittal_date' => date('m/d/Y h:i A', strtotime($row['transmittal_date'])),
                'transmittal_no' => $row['transmittal_no'],
                'encounter_no' => $row['encounter_no'],
                'insurance_no' => $row['insurance_no'],
                'category' => mb_strtoupper($row['category']),
                'last_name' => utf8_decode(trim(mb_strtoupper($row['last_name']))),
                'first_name' => utf8_decode(trim(mb_strtoupper($row['first_name']))),
                'middle_name' => utf8_decode(trim(mb_strtoupper($row['middle_name']))),
                'admission_date' => date('m/d/Y h:i A', strtotime($row['ADMISSION_DATE2']?$row['ADMISSION_DATE2']:$row['admission_date'])),
                'discharge_date' => date('m/d/Y h:i A', strtotime($row['DISCHARGE_DATE2']?$row['DISCHARGE_DATE2']:$row['discharge_date'])),
                'hci' => $row['hci'],
                'pf' => $row['pf'],
                'total_coverage' => $row['total_coverage']
            );

            $i++;
        }

    }
    else{
        $data = array(
            array(
                'num' => '',
                'transmittal_date' => 'No Data',
                'transmittal_no' => 'No Data',
                'encounter_no' => 'No Data',
                'insurance_no' => 'No Data',
                'category' => 'No Data',
                'last_name' => 'No Data',
                'first_name' => 'No Data',
                'middle_name' => 'No Data',
                'admission_date' => 'No Data',
                'discharge_date' => 'No Data'
            )
        );
    }
}
else{
    $data = array(
        array(
            'num' => '',
            'transmittal_date' => 'No Data',
            'transmittal_no' => 'No Data',
            'encounter_no' => 'No Data',
            'insurance_no' => 'No Data',
            'category' => 'No Data',
            'last_name' => 'No Data',
            'first_name' => 'No Data',
            'middle_name' => 'No Data',
            'admission_date' => 'No Data',
            'discharge_date' => 'No Data'
        )
    );
}