<?php
include 'roots.php';
include '../../include/inc_environment_global.php';

global $db; $db->debug = 1;

$sql = <<<SQL
SELECT 
	encounter.encounter_nr,
	{DISCHARGE_DATE_FIELD}
FROM care_encounter AS encounter
{BILL_JOIN}
WHERE encounter.encounter_type IN ({ENCOUNTER_TYPE})
	AND is_discharged = 0
	{DATE_CONDITION}
SQL;



echo "<h3>Querying In-Patient</h3>";
$inPatientSql = strtr($sql,array(
	'{DISCHARGE_DATE_FIELD}' => 'bill.bill_dte AS dischargeDate',
	'{BILL_JOIN}' => 'INNER JOIN seg_billing_encounter AS bill ON encounter.encounter_nr = bill.encounter_nr AND bill.is_deleted IS NULL AND bill.is_final = 1',
	'{ENCOUNTER_TYPE}' => '3,4',
	'{DATE_CONDITION}' => 'AND (STR_TO_DATE(encounter.encounter_date,"%Y-%m-%d") <= STR_TO_DATE("2014-12-31","%Y-%m-%d"))'
));
$inPatients = $db->GetAll($inPatientSql);
echo '<b>In-Patient Count : ' . count($inPatients) . '</b><br/>';



echo "<h3>Querying OPD</h3>";
$opdSql = strtr($sql,array(
	'{DISCHARGE_DATE_FIELD}' => 'encounter.encounter_date AS dischargeDate',
	'{BILL_JOIN}' => '',
	'{ENCOUNTER_TYPE}' => '2',
	'{DATE_CONDITION}' => 'AND (STR_TO_DATE(encounter.encounter_date,"%Y-%m-%d") <= STR_TO_DATE("2015-04-30","%Y-%m-%d"))'
));
$opds = $db->GetAll($opdSql);
echo '<b>OPD Count : ' . count($opds) . '</b><br/>';



echo "<h3>Querying ER</h3>";
$erSql = strtr($sql,array(
	'{DISCHARGE_DATE_FIELD}' => 'bill.bill_dte AS dischargeDate',
	'{BILL_JOIN}' => 'INNER JOIN seg_billing_encounter AS bill ON encounter.encounter_nr = bill.encounter_nr AND bill.is_deleted IS NULL AND bill.is_final = 1',
	'{ENCOUNTER_TYPE}' => '1',
	'{DATE_CONDITION}' => 'AND (STR_TO_DATE(encounter.encounter_date,"%Y-%m-%d") <= STR_TO_DATE("2015-04-30","%Y-%m-%d"))'
));
$ers = $db->GetAll($erSql);
echo '<b>OPD Count : ' . count($ers) . '</b><br/>';

$user = 'Administrator';//$_SESSION['sess_login_username'];
$username = 'admin';//$_SESSION['sess_temp_userid'];
$date = date('Y-m-d H:i:s');

$dischargeSql = "UPDATE care_encounter AS encounter SET is_discharged=1,
discharge_date=DATE({DISCHARGE_DATE_SQL}),discharge_time=TIME({DISCHARGE_DATE_SQL}),
history=CONCAT(history,'Discharged by {$user}[{$username}] at {$date}\n')
WHERE encounter_nr IN ({ENCOUNTERS})";

$db->StartTrans();

echo '<h3>Discharging IPD</h3>';
if(!empty($inPatients)) {
	$encounters = array();
	foreach ($inPatients as $key => $inPatient) {
		$encounters[] = $inPatient['encounter_nr'];
	}
	$ok = $db->Execute(strtr($dischargeSql,array(
		'{DISCHARGE_DATE_SQL}' => '(SELECT bill_dte FROM seg_billing_encounter AS b WHERE b.encounter_nr = encounter.encounter_nr AND is_final=1 AND is_deleted IS NULL LIMIT 1)',
		'{ENCOUNTERS}' => '"'.implode('","', $encounters).'"'
	)));
	if(!$ok)
		$db->FailTrans();
}

echo '<h3>Discharging OPD</h3>';
if(!empty($opds)) {
	$encounters = array();
	foreach ($opds as $key => $opd) {
		$encounters[] = $opd['encounter_nr'];
	}
	$ok = $db->Execute(strtr($dischargeSql,array(
		'{DISCHARGE_DATE_SQL}' => 'encounter.encounter_date',
		'{ENCOUNTERS}' => '"'.implode('","', $encounters).'"'
	)));
	if(!$ok)
		$db->FailTrans();
}

echo '<h3>Discharging ER</h3>';
if(!empty($ers)) {
	$encounters = array();
	foreach ($ers as $key => $er) {
		$encounters[] = $er['encounter_nr'];
	}
	$ok = $db->Execute(strtr($dischargeSql,array(
		'{DISCHARGE_DATE_SQL}' => '(SELECT bill_dte FROM seg_billing_encounter AS b WHERE b.encounter_nr = encounter.encounter_nr AND is_final=1 AND is_deleted IS NULL LIMIT 1)',
		'{ENCOUNTERS}' => '"'.implode('","', $encounters).'"'
	)));
	if(!$ok)
		$db->FailTrans();
}

if($_GET['commit'] == 1) {
	$db->CompleteTrans();
}