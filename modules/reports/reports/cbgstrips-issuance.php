<?php

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

$wardId = $param['wardId'];
$wardName = $param['wardName'];
$fromDtTm = $param['fromDtTm'];
$toDtTm = $param['toDtTm'];

$fromDtTm = strftime("%Y-%m-%d", strtotime($fromDtTm));
$toDtTm = strftime("%Y-%m-%d", strtotime($toDtTm));

$params->put("rprt_title", "BLOOD GLUCOSE STRIPS ISSUANCE REPORT");
$params->put("rprt_fromDtTm", $fromDtTm);
$params->put("rprt_toDtTm", $toDtTm);
$params->put("rprt_wardId", $wardId);
$params->put("rprt_wardName", $wardName);
$params->put("rprt_imagePath", java_resource);

$strSQL = "SELECT pid, pname,
                'NOVA STRIP' supply_name, SUM(qty) qty, is_cash, settlement_type, readby_name FROM 
             (
             SELECT cbg.pid, fn_get_person_name(cbg.pid) pname, 1 qty, oh.`is_cash`, oh.`settlement_type`, cbg.`readby_name` 
                     FROM (seg_cbg_reading cbg INNER JOIN seg_hl7_message_log hl7 ON cbg.`log_id` = hl7.`log_id`) 
                     LEFT JOIN (seg_poc_order_detail o INNER JOIN seg_poc_order oh ON o.refno = oh.refno) ON o.`refno` = hl7.`ref_no`
                     WHERE (CASE WHEN cbg.`ward_id` IS NULL THEN oh.`ward_id` = '{$wardId}' ELSE cbg.`ward_id` = '{$wardId}' END) AND (DATE(cbg.`reading_dt`) BETWEEN '{$fromDtTm}' AND '{$toDtTm}')
                     ORDER BY cbg.`reading_dt`) t
             GROUP BY pid, is_cash, settlement_type, readby_name
             ORDER BY pname";   
                                                               
$result = $db->Execute($strSQL);

$data = array();
if ($result) {
    while ($row = $result->FetchRow()) {                               
        $data[] = array('pid' => $row['pid'],
                        'pname' => utf8_decode(trim($row['pname'])),
                        'supply_name' => $row['supply_name'],
                        'qty' => intval($row['qty']),
                        'is_cash' => ($row['is_cash'] == 1),
                        'reader' => strtoupper($row['readby_name']),
                        'settlement_type' => $row['settlement_type']
            );                        
    }    
}
