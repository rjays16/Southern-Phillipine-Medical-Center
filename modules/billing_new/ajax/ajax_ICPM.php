<?php

#Created by Jarel 02/02/2013
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/billing/class_ops.php');
require_once($root_path . "include/care_api_classes/class_hospital_admin.php");

global $db;

$term = $_GET['term'];
$iscode = strtoupper($_GET['iscode']);
$enc = $_GET['enc_nr'];
$hospObj = new Hospital_Admin();
$srv = new SegOps;

if ($iscode == "TRUE") {
    $where = "cp.code LIKE '$term%'";
} else {
    $where = "cp.description LIKE '$term%'";
}

if ($srv->isHouseCase($enc_nr))
    $nPCF = HOUSE_CASE_PCF;
else
    $nPCF = $hospObj->getDefinedPCF();

$sql1 = "SELECT
            ce.encounter_date
          FROM
            care_encounter ce
          WHERE ce.encounter_nr = '{$enc}'";
if($res2 = $db->Execute($sql1)){
    $res = $res2->FetchRow();
}

//Modified by EJ 12/11/2014
$sql = "SELECT
          cp.code,
          cp.description,
          op.rvu,
          {$nPCF} AS multiplier,
          cp.for_laterality,
          cp.special_case,
          cp.is_delivery,
          cp.is_prenatal,
          cp.for_infirmaries
        FROM seg_case_rate_packages AS cp
        INNER JOIN seg_ops_rvs AS op
          ON cp.code = op.code
        WHERE op.is_active <> '0'
        AND cp.case_type = 'p'
        AND $where
        AND cp.date_from <= '{$res['encounter_date']}'
        AND cp.date_to > '{$res['encounter_date']}'
        ORDER BY cp.package_id DESC
        LIMIT 1";

#print_r($_GET['is_phic']); exit;

$data = array();

if ($result = $db->Execute($sql)) {
    if ($result->RecordCount()) {
        while ($row = $result->FetchRow()) {

            if ($iscode == "TRUE") {
                $data[] = array(
                    'id' => trim($row['code']),
                    'description' => trim($row['description']),
                    'label' => trim($row['code']) . " " . trim($row['description']),
                    'value' => trim($row['code']),
                    'rvu' => trim($row['rvu']),
                    'laterality' => trim($row['for_laterality']),
                    'multiplier' => trim($row['multiplier']),
                    'special_case' => trim($row['special_case']),
                    'is_delivery' => trim($row['is_delivery']), //Added by EJ 12/11/2014
                    'is_prenatal' => trim($row['is_prenatal']), //Added by EJ 12/11/2014
                    'for_infirmaries' => trim($row['for_infirmaries']), //Added by JEFF 06/26/2018
                );
            } else {
                    $data[] = array(
                        'id' => trim($row['code']),
                        'description' => trim($row['description']),
                        'label' => trim($row['code']) . " " . trim($row['description']),
                        'value' => trim($row['description']),
                        'rvu' => trim($row['rvu']),
                        'laterality' => trim($row['for_laterality']),
                        'multiplier' => trim($row['multiplier']),
                        'special_case' => trim($row['special_case']),
                        'is_delivery' => trim($row['is_delivery']), //Added by EJ 12/11/2014
                        'is_prenatal' => trim($row['is_prenatal']), //Added by EJ 12/11/2014
                        'for_infirmaries' => trim($row['for_infirmaries']), //Added by JEFF 06/26/2018
                    );
                }

        }
    } else {
        $data[] = array(
            'id' => 'No ICP Found!',
            'label' => 'No ICP Found!',
            'laterality' => 0,
            'value' => 'No ICP Found!'
        );
    };
} else {
    return FALSE;
}

echo json_encode($data);