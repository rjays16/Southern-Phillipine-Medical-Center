<?php

require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

include_once($root_path.'include/care_api_classes/class_encounter.php');
include_once($root_path.'include/care_api_classes/class_person.php');
include_once($root_path.'include/care_api_classes/class_poc.php');
include_once($root_path.'include/care_api_classes/class_globalconfig.php');

/**
 *
 * @return string
 */
function getFullName($name_last, $name_first, $name_middle, $suffix) {
    $name = '';
    if ($name_last) {
        $name .= $name_last;
    }

    if ($name_first) {
        $name .= ', ' . $name_first;
    }

    if ($name_middle) {
        $name .= ' ' . substr($name_middle,0,1) . '.';
    }

    if ($suffix) {
        $name .= ' ' . $suffix;
    }
    return ($name) ? strtoupper($name) : null;
}

$enc_no = $param['enc_no'];

$pid = "";
$pt_name = "";
$enctype_name = "";

$enc_obj = new Encounter;
$encounter = $enc_obj->getPatientEncounter($enc_no);

if ($encounter) {
    $pid = $encounter['pid'];
    $pt_name = getFullName($encounter['name_last'], $encounter['name_first'], $encounter['name_middle'], $encounter['suffix']);
    $enctype_name = $db->GetOne("SELECT name FROM care_type_encounter WHERE type_nr = {$encounter['encounter_type']} AND status <> 'deleted'");
}

$params->put("rprt_title", "BLOOD GLUCOSE LEVEL MONITORING");
$params->put("rprt_pid", $pid);
$params->put("rprt_encounterNo", $enc_no);
$params->put("rprt_encounterType", $enctype_name);
$params->put("rprt_patientName", $pt_name);

//Check if categorized as PEDIA yet or already an ADULT ...
$person = new Person();
$age = $person->getPatientAgeYrwDays($pid);
$strindex = ( ($age['y'] < Person::PEDIA_AGE_LIMIT) || (($age['y'] == Person::PEDIA_AGE_LIMIT) && ($age['d'] == 0)) ) ? 'poc_cbg_normal_range_pedia' : 'poc_cbg_normal_range_adult';

// Default minimum and maximum normal CBG reading from the config table.
$gc = new GlobalConfig($GLOBAL_CONFIG);
$gc->getConfig($strindex);
if ( $GLOBAL_CONFIG[$strindex] ) {
    $strRange = $GLOBAL_CONFIG[$strindex];
    $normRange = explode(',', $strRange);
}

if (isset($normRange)) {
    $params->put("min_normal", doubleval($normRange[0]));
    $params->put("max_normal", doubleval($normRange[1]));    
}
else {
    // Default normal range ... hard-coded.
    $params->put("min_normal", ($strindex == 'poc_cbg_normal_range_pedia') ? Poc::CBG_MIN_NORMAL_PEDIA : Poc::CBG_MIN_NORMAL_ADULT );
    $params->put("max_normal", ($strindex == 'poc_cbg_normal_range_pedia') ? Poc::CBG_MAX_NORMAL_PEDIA : Poc::CBG_MAX_NORMAL_ADULT );
}

$strSQL = "SELECT CONCAT(DATE_FORMAT(post_dt, '%l:%i %p'), '\n', DATE_FORMAT(post_dt, '%c/%d/%y')) post_dt, reading_level "
            . "FROM seg_cbg_reading cbg "
            . "WHERE encounter_nr = '{$enc_no}' "
            . "ORDER BY cbg.post_dt";
$result = $db->Execute($strSQL);

$data = array();
if ($result) {
    while ($row = $result->FetchRow()) {
        $data[] = array('post_dt' => $row['post_dt'],
                        'reading_level' => doubleval($row['reading_level'])
            );        
    }
}
