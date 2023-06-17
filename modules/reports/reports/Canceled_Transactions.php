<?php
require_once 'roots.php';
require_once $root_path . 'include/inc_environment_global.php';
include 'parameters.php';

global $db;//$db->debug = 1;

$params->put('hosp_country', $hosp_country);
$params->put('hosp_agency', $hosp_agency);
$params->put('hosp_name', $hosp_name);
$params->put('hosp_addr1', $hosp_addr1);
$params->put('base_url', dirname(java_resource));

$params->put('date_span', "From " . date('M d, Y', $from_date) . " to " . date('M d, Y', $to_date));
$title = 'LIST OF CANCELED ';

if($_GET['param'] == "")
    $patient_type = '1,2,3,4,6';

switch ($patient_type) {
    case '1,2,3,4,6':
        $area = "REGISTRATION";
        $areaCode = array(1,2,3,4,6);
        break;
    case '3,4':
        $area = "ADMISSION";
        $areaCode = array(3,4);
        break;
    case '1':
        $area = "ER REGISTRATION";
        $areaCode = array(1);
        break;
    case '2':
        $area = "OUT-PATIENT REGISTRATION";
        $areaCode = array(2);
        break;
    case '6':
        $area = "HSSC REGISTRATION";
        $areaCode = array(6);
        break;
}

$areaParam = trim(str_repeat('?,',count($areaCode)),',');

$params->put('title', $title . $area . 'S');
$params->put('area', $area);

$sql = <<<SQL
SELECT
  DATE_FORMAT(
    encounter.encounter_date,
    '%m/%d/%Y'
  ) AS encounterDate,
  DATE_FORMAT(
    encounter.encounter_date,
    '%h:%i %p'
  ) AS encounterTime,
  encounter.pid,
  encounter.encounter_nr,
  fn_get_person_name_first_mi_last (encounter.pid) AS patientName,
  department.name_formal AS departmentName,
  encounter.modify_id AS canceled_by,
  DATE_FORMAT(encounter.modify_time,'%m-%d-%Y %h:%i %p') AS canceled_date, encounter.`history`
FROM
  care_encounter AS encounter
  LEFT JOIN care_department AS department
    ON encounter.current_dept_nr = department.nr
WHERE encounter_type IN ({$areaParam})
  AND encounter.status IN (
    'deleted',
    'hidden',
    'inactive',
    'void'
  )
  AND (
    STR_TO_DATE(
      encounter.encounter_date,
      '%Y-%m-%d'
    ) >= STR_TO_DATE(?, '%Y-%m-%d')
    AND STR_TO_DATE(
      encounter.encounter_date,
      '%Y-%m-%d'
    ) <= STR_TO_DATE(?, '%Y-%m-%d')
  )
ORDER BY encounter.encounter_date ASC
SQL;

$data = $db->GetAll($sql, array_merge(
    $areaCode,
    array(date('Y-m-d', $from_date), date('Y-m-d', $to_date))
));
array_walk($data, 'addCount');

if (empty($data))
    $data = array(array());

function addCount(&$val, $key)
{
    $val['count'] = $key+1;
    $values=explode("\n",$val['history']);
    foreach($values as $value)
    {
        if (!(strpos ($value, "Cancelled") === false))
        {
            $val['canceled_by'] = substr($value,(strpos($value, "by")+2),strpos($value, ",")-(strpos($value, "by")+2));
            break;
        }
    }
}