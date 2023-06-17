<?php 
/*
 * Author : gelie
 * Date : 11/11/2015
 */

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

define(IPBMIPD_enc, 13);
define(IPBMOPD_enc, 14);

$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );

global $db;
$date_from = date('Y-m-d',$_GET['from_date']);
$date_to = date('Y-m-d',$_GET['to_date']);

/*Added By Mark 07-19-16*/
 $params_data = explode(",",$_GET['param']);
 $condition = "";
 $condition2 ="AND DATE(sls.create_dt) BETWEEN ({$db->qstr($date_from)}) AND ({$db->qstr($date_to)})";
 $condition3 ="";

 if ($params_data[0] ==="param_patienttype_CPS--all" && $params_data[1] ==='param_test_type_CPS--all')
         $condition="ORDER BY create_dt DESC";
elseif($params_data[0] ==="param_patienttype_CPS--walkin" && $params_data[1] !='param_test_type_CPS--all')
    $condition = "AND slss.service_code IN('$test_type_CPS')AND (ce.encounter_type IS NULL) ORDER BY create_dt DESC";
elseif($params_data[0] ==="param_patienttype_CPS--all" && $params_data[1] !='param_test_type_CPS--all')
    $condition = "AND slss.service_code IN('$test_type_CPS')AND (ce.encounter_type IN(".$patient_type.") OR ce.encounter_type IS NULL) ORDER BY create_dt DESC";
 else {
    if ($params_data[0] ==="param_patienttype_CPS--walkin" && $params_data[1] ==='param_test_type_CPS--all')
           $condition = "AND (ce.encounter_type IN (".$patient_type.") OR ce.encounter_type IS NULL) ORDER BY create_dt DESC";
    else{
        if ($params_data[1] ==='param_test_type_CPS--all')
           $condition = "AND ce.encounter_type IN (".$patient_type.") ORDER BY create_dt DESC";
        else
            $condition = "AND slss.service_code IN('$test_type_CPS') AND ce.encounter_type IN (".$patient_type.") ORDER BY create_dt DESC"; 
      }
 }
 $params->put('date_range',$from . ' to ' . $to);
 $params->put('Selected_Params',$patient_type_label." , ".$test_type_CPS." test type");
 

$sql = "SELECT 
        sls.pid,
        sls.refno,
        UCASE(fn_get_person_lastname_first(sls.pid)) AS name,
        DATE_FORMAT(sls.create_dt, '%m-%d-%Y %h:%i %p') AS create_dt,
        IF(sls.is_cash, sls.discountid, '') AS classification,
        ce.encounter_type,
        UCASE(IF(sls.is_cash,
            IF((SELECT spr.or_no FROM seg_pay_request spr
                  INNER JOIN seg_pay sp ON spr.or_no = sp.or_no
                  WHERE spr.ref_no = sls.refno
                    AND sp.pid = sls.pid
                    AND spr.service_code = slsd.service_code
                    LIMIT 1
            ),
            'PAID',
            IF(slsd.request_flag IS NULL,
                  'unpaid',
                  'paid'
            )),
              'charge'
            )) AS STATUS,
        fn_get_department_name (slsd.request_dept) department,
        IF(sls.is_cash, slsd.price_cash_orig, slsd.price_charge) AS orig_price,
        IF(sls.is_cash, slsd.price_cash, slsd.price_charge) AS net_price,
        IF(sls.is_cash, IF((SELECT spr.or_no FROM seg_pay_request spr
                      INNER JOIN seg_pay sp ON spr.or_no = sp.or_no
                      WHERE spr.ref_no = sls.refno
                        AND sp.pid = sls.pid
                        AND spr.service_code = slsd.service_code
                        LIMIT 1
                ), slsd.price_cash,
                   '0.00'
                ),
                 '0.00'
        ) AS paid_amount,
        IF(sls.is_cash, IF((SELECT spr.or_no FROM seg_pay_request spr
                      INNER JOIN seg_pay sp ON spr.or_no = sp.or_no
                      WHERE spr.ref_no = sls.refno
                        AND sp.pid = sls.pid
                        AND spr.service_code = slsd.service_code
                        LIMIT 1
                ), '0.00',
                       IF(slsd.request_flag IS NULL, slsd.price_cash, '0.00')
                   ),
                 IF(slsd.request_flag IS NULL, slsd.price_charge, IF(slsd.request_flag = 'paid', slsd.price_charge, '0.00'))
        ) AS balance_amount,
        slss.name AS test,
        slss.service_code AS code,
        IF((sls.discountid != ''), 'YES', 'NO') AS SOCIAL,
        UCASE(IF(sls.is_cash, IF((SELECT spr.or_no FROM seg_pay_request spr
                        INNER JOIN seg_pay sp ON spr.or_no = sp.or_no
                        WHERE spr.ref_no = sls.refno
                          AND sp.pid = sls.pid
                          AND spr.service_code = slsd.service_code
                          LIMIT 1
                    ), (SELECT spr.or_no FROM seg_pay_request spr
                          INNER JOIN seg_pay sp ON spr.or_no = sp.or_no
                          WHERE spr.ref_no = sls.refno
                            AND sp.pid = sls.pid
                            AND spr.service_code = slsd.service_code
                            LIMIT 1
                      ),
                        COALESCE(slsd.request_flag, '')
                    ),
                    IF((sls.grant_type != ''), IF(sls.grant_type = 'phic',
                                    CONCAT(sls.grant_type, '/',
                                    (SELECT mem.memcategory_desc FROM seg_memcategory mem
                                     INNER JOIN seg_encounter_memcategory sem ON sem.memcategory_id = mem.memcategory_id
                                     WHERE sem.encounter_nr = sls.encounter_nr
                                     LIMIT 1
                                    )),
                                    COALESCE(sls.grant_type, 'PERSONAL')
                                ),
                                    'PERSONAL'
                    )
        )) AS mode_of_payment,
        IF(cu.name != '', cu.name,
                        IF(sls.modify_id != '', sls.modify_id,
                                                sls.create_id
                        )
        ) AS encoder,
        /*Added by Mark 07-18-16 IF manual payment*/
        (SELECT reason FROM seg_payment_workaround WHERE refno = sls.refno /*LIMIT must add*/ LIMIT 1) AS mode_reasons 
    FROM
        seg_lab_serv AS sls
      INNER JOIN seg_lab_servdetails AS slsd
        ON sls.refno = slsd.refno
      LEFT JOIN care_encounter AS ce
        ON sls.encounter_nr = ce.encounter_nr
      LEFT JOIN care_users cu
        ON cu.login_id = IFNULL(sls.modify_id, sls.create_id)
      INNER JOIN seg_lab_services AS slss
        ON slsd.service_code = slss.service_code
    WHERE slss.group_code = 'SPC'
        AND sls.status NOT IN ('deleted')
        AND slsd.status NOT IN ('deleted')
       ".$condition2."".$condition;

$rs = $db->Execute($sql);
$num_rows = $db->GetOne("SELECT FOUND_ROWS()");

$data = array();
if (is_object($rs)) {
    if($rs->RecordCount()){
        while ($row = $rs->FetchRow()) {

          if($row['encounter_type'] == NULL){
            $enc_type = 'WALK-IN';
          }
          else{
            switch($row['encounter_type']){
              case 1: $enc_type = 'ER'; break;
              case 2: $enc_type = 'OPD'; break;
              case 3:
              case 4: $enc_type = 'IPD'; break;
              case 5: $enc_type = 'DIALYSIS'; break;
              case 6: $enc_type = 'HSSC'; break;
              case 8: $enc_type = 'WALK-IN'; break;
              case 12: $enc_type = 'WELL BABY'; break;
              case IPBMIPD_enc: $enc_type = 'IPBM - IPD'; break;
              case IPBMOPD_enc: $enc_type = 'IPBM - OPD'; break;
            }
          }

          $data[] = array(
            'hrn' => intval($row['pid']),
            'batch_no' => $row['refno'],
            'patient' => utf8_decode(trim($row['name'])),
            'date' => $row['create_dt'],
            'classification' => $row['classification'],
            'type' => $enc_type,
            'status' => $row['STATUS'],
            'dept' => $row['department'],
            'orig_price' => $row['orig_price'],
            'net_price' => $row['net_price'],
            'paid_amnt' => $row['paid_amount'],
            'bal_amnt' => $row['balance_amount'],
            'test' => $row['test'],
            'code' => $row['code'],
            'social' => $row['SOCIAL'],
            'mode' =>($row['mode_reasons'] != null ? $row['mode_reasons'] : $row['mode_of_payment']),
            'encoder' => utf8_decode(trim(mb_strtoupper($row['encoder'])))
          );
        }
    }
   // $params->put('num_records', $condition2."  ".$condition." ".$params_data[1]."\n\n");
    $params->put('num_records',$num_rows);
} else {
    $data[0] = array('hrn' => 'No data');
    $params->put('num_records', '0'.$condition);
}
//end line