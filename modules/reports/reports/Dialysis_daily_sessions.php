<?php
/**
 * @author Nick B. Alcala 06-17-2014
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

$params->put('hospital_name',mb_strtoupper($hosp_name));
$params->put("header", $report_title);

$sql = $db->Prepare("SELECT
                          sdm.machine_nr AS machine_no,
                          fn_get_person_name (cp.pid) AS patient_name,
                          IF(
                            sdt.dialyzer_reuse = 1
                            AND sdd.dialyzer_type = 'high',
                            1,
                            ''
                          ) AS hiflux_new,
                          IF(
                            sdd.dialyzer_type = 'high',
                            (sdt.dialyzer_reuse - 1),
                            ''
                          ) AS hiflux_reuse,
                          IF(
                            sdt.dialyzer_reuse = 1
                            AND sdd.dialyzer_type = 'low',
                            1,
                            ''
                          ) AS lowflux_new,
                          IF(
                            sdd.dialyzer_type = 'low',
                            (sdt.dialyzer_reuse - 1),
                            ''
                          ) AS lowflux_reuse
                        FROM
                          seg_dialysis_machine AS sdm
                          LEFT JOIN seg_dialysis_transaction AS sdt
                            ON sdm.machine_nr = sdt.machine_nr
                          LEFT JOIN seg_dialysis_prebill AS sdp
                            ON sdt.transaction_nr = sdp.bill_nr
                          LEFT JOIN seg_dialysis_dialyzer AS sdd
                            ON sdd.dialyzer_serial_nr = sdt.dialyzer_serial_nr
                          LEFT JOIN care_person AS cp
                            ON cp.pid = sdt.pid
                          LEFT JOIN seg_misc_ops AS smo
                            ON smo.encounter_nr = sdp.encounter_nr
                          LEFT JOIN seg_misc_ops_details AS smop
                            ON smo.refno = smop.refno
                            AND (
                              SUBSTR(
                                sdt.transaction_nr,
                                LENGTH(sdt.transaction_nr),
                                1
                              )
                            ) = smop.entry_no
                            AND DATE_FORMAT(smop.op_date, '%Y-%m-%d') = DATE_FORMAT(
                              sdt.transaction_date,
                              '%Y-%m-%d'
                            )
                        WHERE DATE_FORMAT(
                            sdt.transaction_date,
                            '%Y-%m-%d'
                          ) BETWEEN STR_TO_DATE(?, '%Y-%m-%d')
                          AND STR_TO_DATE(?, '%Y-%m-%d')");

$rs = $db->Execute($sql,array(
    date('Y-m-d',$from_date),
    date('Y-m-d',$to_date)
));

if($rs){
    if($rs->RecordCount()){
        $data = $rs->GetRows();
    }else{
        $data[0]['machine_no'] = '1';
    }
}else{
    $data[0]['machine_no'] = '2';
}

?>