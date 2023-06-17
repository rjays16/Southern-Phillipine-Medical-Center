<?php
/* Created by Gervie 08/09/2015 */
require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;
# added by: syboy 03/15/2016 : meow
$paramsarr = explode(",",$param);
$exploded_paramsarr1 = explode("--",$paramsarr[0]);
$exploded_paramsarr2 = explode("--",$paramsarr[1]);
# ended syboy
$sql = "SELECT type_name, alt_name FROM seg_grant_account_type WHERE id = ". $db->qstr($exploded_paramsarr1[1]);

$sql2 = $db->Execute($sql);
$res = $sql2->FetchRow();

$params->put('hosp_country',$hosp_country);
$params->put('hosp_agency',$hosp_agency);
$params->put('hosp_name',$hosp_name);
$params->put('title', strtoupper($res['alt_name']) . " GRANTS FOR HOSPITAL BILLS");
$params->put('dateRange',"From " . date('M d, Y',$from_date) . " to " . date('M d, Y',$to_date));

$cond1 = "DATE(cc.approved_date)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond2 = "AND cc.pay_type IN ('". $res['type_name'] ."')";
# added by: syboy 03/15/2016 : meow
if ($exploded_paramsarr2[1] == 'all' || $exploded_paramsarr2[1] == '') {
    $cond3 = "";
}else{
    $cond3 = "AND cc.account_nr = '".$exploded_paramsarr2[1]."' ";
}
# ended syboy

# query updated by : syboy #edit by Marvin Cortes 05/31/2016
$query = "SELECT
              cc.approved_date AS bill_date,
              ga.name guarantor,
              cc.control_nr control_nr,
              e.encounter_nr encounter_nr,
              UPPER(fn_get_person_name (e.pid)) patient,
              cc.amount AS amount
              
            FROM
              seg_billing_encounter fb
              INNER JOIN care_encounter e
                ON e.encounter_nr = fb.encounter_nr
              LEFT JOIN seg_pay_request pr
                ON pr.ref_source = 'FB'
                AND pr.service_code = fb.bill_nr
              LEFT JOIN seg_credit_collection_ledger cc
                ON cc.encounter_nr = fb.encounter_nr
                AND cc.is_deleted = 0
                AND cc.`id` NOT IN 
                (SELECT 
                  ccl.`ref_no` 
                FROM
                seg_credit_collection_ledger AS ccl 
                WHERE ccl.entry_type = 'credit') 
                AND cc.entry_type = 'debit' 
              LEFT JOIN seg_grant_accounts ga
                ON ga.id = cc.account_nr
            WHERE ". $cond1 . $cond2 . $cond3 ."
              AND cc.amount NOT IN ('0.00')
              AND fb.is_deleted IS NULL
              AND fb.is_final = 1
              AND cc.entry_type != 'credit'
            ORDER BY cc.approved_date ASC ";
// var_dump($query); exit;
$rs = $db->Execute($query);

if($rs){
    if($rs->RecordCount() > 0){
        $i = 0;
        $j = 0;
        $k = 0;

        while($row = $rs->FetchRow()){
            if($res['type_name'] == 'fund_checks' || $res['type_name'] == 'private-companies'){
                $grant = $row['guarantor'];
            }
            elseif($res['type_name'] == 'nbb'){
                $grant = 'NBB';
            }
            else{
                $grant = strtoupper($res['alt_name']);
            }
            if($row['amount'] != '0.00') {
                $i++;
                // Added and Commented by Mats 08202016
                $nrow = $i==29?30:29;
                if($k == $nrow){
                // if($k == 30){
                  $j++;
                  $k = -8;
                }

                $data[$i] = array(
                    'num' => $i,
                    'bill_date' => date('m/d/Y', strtotime($row['bill_date'])),
                    'grant' => $grant,
                    'cont_num' => $row['control_nr'],
                    'case_num' => $row['encounter_nr'],
                    'patient_name' => utf8_decode(trim($row['patient'])),
                    'amount' => $row['amount'],
                    'group_no' => $j
                );

                $k++;
            }
        }
        
        $params->put('final',$j);
    }
    else{
        $data = array(
            array(
                'bill_date' => 'No Data',
            )
        );
    }
}
else{
    $data = array(
        array(
            'bill_date' => 'No Data',
        )
    );
}

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

// $data[0]['dmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
// $data[0]['doh'] = $baseurl . "img/doh.png";

$params->put('dmc', $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put('doh', $baseurl . "img/doh.png");