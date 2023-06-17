<?php
include 'roots.php';
include '../../include/inc_environment_global.php';

global $db;

$start = microtime();

$results = $db->GetAll(<<<SQL
SELECT
  filename,
  hl7_msg
FROM
  seg_hl7_hclab_msg_receipt
WHERE lab_no IS NULL AND msg_type_id='ORU'
AND STR_TO_DATE(date_update,'%Y-%m-%d') >= STR_TO_DATE('2015-12-17','%Y-%m-%d')
LIMIT 100
SQL
);

if(!empty($results)) {
    $db->debug = 1;
    $db->StartTrans();
    $updateCount = 0;
    foreach ($results as $result) {
        $matches = array();
        $hasMatch = preg_match('/(?:OBR\|.\|\d*\|)(\d*)(?:\|)/',$result['hl7_msg'],$matches);
        $labNo = $matches[1];
        $success = $db->Execute('UPDATE seg_hl7_hclab_msg_receipt SET lab_no=? WHERE filename=?', array(
            $labNo,
            $result['filename']
        ));
        if(!$success) {
            $db->FailTrans();
        } else {
            $updateCount++;
        }
    }

    $db->CompleteTrans();
    $end = microtime();
    $elapsed = $end - $start;
    echo "<h3>Updated $updateCount records in {$elapsed} seconds.</h3>";
} else {
    echo 'No record to update :D';
}