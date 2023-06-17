<?php
//created by Nick 3-20-2015
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

$params->put('hosp_name',mb_strtoupper($hosp_name));
$from = date("F j, Y", strtotime($from_date_format) );
$to = date("F j, Y", strtotime($to_date_format) );
$params->put('date_span',$from . ' to ' . $to);

global $db;

$from = date('Y-m-d',$_GET['from_date']);
$to = date('Y-m-d',$_GET['to_date']);

$sql = <<<sql
SELECT
  CONCAT(
    UPPER(SUBSTRING(person.name_last, 1, 1)),
    UPPER(SUBSTRING(person.name_first, 1, 1)),
    IF(
      person.name_middle IS NULL,
      '',
      UPPER(SUBSTRING(person.name_middle, 1, 1))
    )
  ) AS patient_initial,
  (
    IF((SELECT COUNT(encounter_nr) FROM care_encounter WHERE pid = encounter.pid) > 1,
          IF((SELECT encounter_date FROM care_encounter WHERE pid = encounter.pid ORDER BY encounter_date ASC LIMIT 1) < encounter.encounter_date,'OLD','NEW'),
          'NEW'
    )
  ) as type_of_patient,
  UPPER(person.sex) AS sex,
  vitalSign.weight AS weight,
  DATE_FORMAT(person.date_birth,'%m/%d/%Y') AS date_of_birth,
  DATE_FORMAT(encounter.admission_dt,'%m/%d/%Y') AS date_admitted,
  DATE_FORMAT(encounter.discharge_date,'%m/%d/%Y') AS date_discharged,
  /*result.result_desc AS outcome*/
  IF(person.death_date<>'0000-00-00' AND person.death_encounter_nr=encounter.encounter_nr,'Died','Discharged') AS outcome,
  GROUP_CONCAT(IF(diagnosis.type_nr=1,icd.description,'') SEPARATOR '') AS primary_diag,
  GROUP_CONCAT(IF(diagnosis.type_nr=1,diagnosis.code,'') SEPARATOR '') AS primary_icd,
  GROUP_CONCAT(IF(diagnosis.type_nr<>1,icd.description,'') SEPARATOR '') AS secondary_diag,
  GROUP_CONCAT(IF(diagnosis.type_nr<>1,diagnosis.code,'') SEPARATOR '') AS secondary_icd
FROM care_encounter AS encounter
  INNER JOIN care_person AS person ON person.pid = encounter.pid
  LEFT JOIN seg_encounter_vitalsigns AS vitalSign ON encounter.encounter_nr = vitalSign.encounter_nr
  /*LEFT JOIN seg_encounter_result AS caseResult ON caseResult.encounter_nr = encounter.encounter_nr
  LEFT JOIN seg_results AS result ON caseResult.result_code = result.result_code*/
  LEFT JOIN care_encounter_diagnosis AS diagnosis ON encounter.encounter_nr = diagnosis.encounter_nr
    AND diagnosis.status NOT IN('deleted','hidden','inactive','void')
  LEFT JOIN care_icd10_en AS icd ON icd.diagnosis_code = diagnosis.code
  LEFT JOIN care_department AS department ON department.nr = encounter.current_dept_nr
WHERE encounter.is_discharged = 1 AND
      (
        STR_TO_DATE(encounter.discharge_date, '%Y-%m-%d') BETWEEN
        STR_TO_DATE(?, '%Y-%m-%d') AND STR_TO_DATE(?, '%Y-%m-%d')
      )
      AND fn_get_ageyr(NOW(),person.date_birth) <= 19
      AND department.name_formal LIKE '%pedia%'
      AND encounter.encounter_type IN (3,4)
GROUP BY encounter.encounter_nr
ORDER BY encounter.discharge_date DESC
sql;

$data = $db->GetAll($sql,array(
  $from,
  $to
));

if(empty($data)){
  $data[0]['patient_initial'] = 'No records';
}

/*
Created by Borj, 05/07/2014 09:00 AM
*/
//error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
//require_once('./roots.php');
//require_once($root_path.'include/inc_environment_global.php');
//include('parameters.php');
//
//#_________________________________________________
//$params->put('hosp_name',mb_strtoupper($hosp_name));
//$from = date("F j, Y", strtotime($from_date_format) );
//$to = date("F j, Y", strtotime($to_date_format) );
//$params->put('date_span',$from . ' to ' . $to);
//
//#_________________________________________________
//global $db;
//
//$from = date('Y-m-d',$_GET['from_date']);
//$to = date('Y-m-d',$_GET['to_date']);
//
//$sql="SELECT
//  CONCAT(
//    UPPER(SUBSTRING(a.`name_last`, 1, 1)),
//    UPPER(SUBSTRING(a.`name_first`, 1, 1)),
//    IF(
//      a.`name_middle` IS NULL,
//      '',
//      UPPER(SUBSTRING(a.`name_middle`, 1, 1))
//    )
//  ) patient_initial,
//  IF(
//    (SELECT
//      COUNT(*)
//    FROM
//      care_encounter
//    WHERE pid = a.pid
//      AND DATE(encounter_date) <= '$db->qstr($from)' = 1),
//    'New',
//    'Old'
//  ) type_of_patient,
//  UPPER(fn_get_gender (a.pid)) sex,
//  c.`weight` weight,
//  a.`date_birth` date_of_birth,
//  b.`admission_dt` date_admitted,
//  b.`discharge_date` date_discharged,
//  IF(
//    a.death_date != '0000-00-00'
//    AND (
//      a.death_encounter_nr = b.encounter_nr
//    ),
//    'Died',
//    'Discharge'
//  ) outcome,
//  (SELECT
//    e.description
//  FROM
//    `care_encounter_diagnosis` a
//    LEFT JOIN `care_icd10_en` e
//      ON a.`code` = e.`diagnosis_code`
//  WHERE a.encounter_nr = b.encounter_nr
//    AND a.type_nr = 1
//    AND a.`status` NOT IN (
//      'deleted',
//      'hidden',
//      'inactive',
//      'void'
//    )
//  LIMIT 1) primary_diag,
//  (SELECT
//    a.code
//  FROM
//    `care_encounter_diagnosis` a
//  WHERE a.encounter_nr = b.encounter_nr
//    AND a.type_nr = 1
//    AND a.`status` NOT IN (
//      'deleted',
//      'hidden',
//      'inactive',
//      'void'
//    )
//  LIMIT 1) primary_icd,
//  (SELECT
//    e.description
//  FROM
//    `care_encounter_diagnosis` a
//    LEFT JOIN `care_icd10_en` e
//      ON a.`code` = e.`diagnosis_code`
//  WHERE a.encounter_nr = b.encounter_nr
//    AND a.type_nr = 0
//    AND a.`status` NOT IN (
//      'deleted',
//      'hidden',
//      'inactive',
//      'void'
//    )
//  LIMIT 1) secondary_diag,
//  (SELECT
//    a.code
//  FROM
//    `care_encounter_diagnosis` a
//  WHERE a.encounter_nr = b.encounter_nr
//    AND a.type_nr = 0
//    AND a.`status` NOT IN (
//      'deleted',
//      'hidden',
//      'inactive',
//      'void'
//    )
//  LIMIT 1) secondary_icd
//FROM
//  care_person a
//  LEFT JOIN care_encounter b
//    ON a.`pid` = b.`pid`
//  LEFT JOIN `seg_encounter_vitalsigns` c
//    ON b.`encounter_nr` = c.`encounter_nr`
//    AND b.`pid` = c.`pid`
//  LEFT JOIN `care_encounter_diagnosis` d
//    ON b.`encounter_nr` = d.`encounter_nr`
//  LEFT JOIN `care_icd10_en` e
//    ON d.`code` = e.`diagnosis_code`
//WHERE b.`discharge_date` NOT IN ('', '0000-00-00')
//  AND b.encounter_type IN (3, 4)
//  AND (
//    b.current_dept_nr IN (125)
//    OR b.current_dept_nr IN
//    (SELECT
//      nr
//    FROM
//      care_department AS d
//    WHERE d.parent_dept_nr = '125')
//  )
//  AND b.status NOT IN (
//    'deleted',
//    'hidden',
//    'inactive',
//    'void'
//  )
//  AND d.status NOT IN (
//    'deleted',
//    'hidden',
//    'inactive',
//    'void'
//  )
//  AND DATE(b.discharge_date) BETWEEN
//  DATE(".$db->qstr($from).")
//  AND
//  DATE(".$db->qstr($to).")
//GROUP BY d.`encounter_nr`";
//
//
//
//                  $i = 0;
//                  $data = array();
//                  $rs = $db->Execute($sql);
//
//
//                                  if($rs){
//                                        if($rs->RecordCount()){
//                                              while($row=$rs->FetchRow()){
//                                                    $data[$i] = array(
//                                                          'patient_initial' => strtoupper($row['patient_initial']),
//                                                          'type_of_patient' => $row['type_of_patient'],
//                                                          'sex' => $row['sex'],
//                                                          'weight' => $row['weight'],
//                                                          'date_of_birth' => date("m/d/Y",strtotime($row['date_of_birth'])),
//                                                          'date_admitted' => date("m/d/Y",strtotime($row['date_admitted'])),
//                                                          'date_discharged' => date("m/d/Y",strtotime($row['date_discharged'])),
//                                                          'outcome' => $row['outcome'],
//                                                          'primary_diag' => $row['primary_diag'],
//                                                          'primary_icd' => $row['primary_icd'],
//                                                          'secondary_diag' => $row['secondary_diag'],
//                                                          'secondary_icd' => $row['secondary_icd']
//                                                         );
//                                                    $i++;
//                                              }
//
//                                        }else{
//                                              $data[0]= array('patient_initial'=>'No Data');
//                                        }
//                                        }else{
//                                        $data[0]['patient_initial'] = 'No records';
//                                  }