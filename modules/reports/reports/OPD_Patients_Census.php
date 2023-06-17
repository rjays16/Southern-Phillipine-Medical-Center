<?php
/**
 * @author Nick B. Alcala 7-24-2015
 * OPD Census of patients
 */
require_once './roots.php';
require_once $root_path.'include/inc_environment_global.php';
require_once $root_path . 'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/care_api_classes/class_encounter.php';
include('parameters.php');

global $db;

/*$personnel = new Personell();
$personnelInfo = $personnel->getPersonellInfo($_SESSION['sess_login_personell_nr']);*/

$encounter = new Encounter();

$params->put('date_span',date('F d, Y',strtotime($from_date_format))." to ".date('F d, Y',strtotime($to_date_format)));
//$params->put('department',$personnelInfo['dept_name']);
$params->put('department', $dept_label);

/**
 * Updated by Gervie 08/22/2015
 */

/*$sql = <<<SQL
SELECT
  encounter.pid AS pid,
  UPPER(fn_get_person_name_first_mi_last(encounter.pid)) AS fullname,
  DATE_FORMAT(encounter.encounter_date,'%m-%d-%Y %h:%m %p') AS `datetime`,
  UPPER(fn_get_age(NOW(),person.date_birth)) AS age,
  UPPER(person.sex) AS gender,
  UPPER(fn_get_complete_address(person.pid)) AS address,
  IFNULL(pay.amount_due,encounter.official_receipt_nr) AS consultation,
  fn_get_personell_lastname_first(encounter.consulting_dr_nr) AS physician,
  '' AS diagnosis
FROM care_encounter AS encounter
INNER JOIN care_person AS person
  ON person.pid = encounter.pid
LEFT JOIN seg_pay AS pay
  ON pay.or_no = encounter.official_receipt_nr
WHERE encounter.encounter_type=?
AND (
    encounter.current_dept_nr=? OR
    encounter.current_dept_nr IN (
      SELECT nr FROM care_department WHERE parent_dept_nr=?
    )
)
AND (
    STR_TO_DATE(encounter.encounter_date,'%Y-%m-%d') >= STR_TO_DATE(?,'%Y-%m-%d') AND
    STR_TO_DATE(encounter.encounter_date,'%Y-%m-%d') <= STR_TO_DATE(?,'%Y-%m-%d')
)
ORDER BY encounter.encounter_date DESC
SQL;

$data = $db->GetAll($sql,array(
    OUT_PATIENT,
    $personnelInfo['location_nr'],
    $personnelInfo['location_nr'],
    $from_date_format,
    $to_date_format
));

if(empty($data))
    $data = array(array());*/

$cond1 = "DATE(ce.encounter_date)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
               AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ")";
if($dept_list == ''){
    $cond2 = "";
}
else {
    $cond2 = " AND ce.consulting_dept_nr = " . $db->qstr($dept_list);
}

$query = "SELECT
          ce.pid AS pid,
          UPPER(fn_get_person_lastname_first (ce.pid)) AS fullname,
          DATE_FORMAT(ce.encounter_date,'%l:%i%p') AS enc_date,
          DATE_FORMAT(cp.date_reg,'%Y-%m-%d %H:%i:%s') AS date_reg,
          DATE_FORMAT(ce.encounter_date,'%Y-%m-%d %H:%i:%s') AS date_enc,
          ce.consulting_dept_nr AS dept,
          UPPER(REPLACE(fn_get_age (NOW(), cp.date_birth), 'years', 'y.o')) AS age,
          UPPER(cp.sex) AS gender,
          LOWER(fn_get_address_wout_prov_zip (cp.pid)) AS address,
          IFNULL(SUBSTRING(sp.amount_due, 1, CHAR_LENGTH(sp.amount_due)-2),
            REPLACE(REPLACE(UPPER(ce.official_receipt_nr), 'CHARITY FROM SOCIAL SERVICE', 'CH'), 'SENIOR CITIZEN', 'SC')) AS consultation,
          UPPER(cie.description) AS diagnosis,
          UPPER(fn_get_personell_name (ce.consulting_dr_nr)) AS physician
          FROM
            care_encounter AS ce
            INNER JOIN care_person AS cp
              ON cp.pid = ce.pid
            LEFT JOIN seg_pay AS sp
              ON sp.or_no = ce.official_receipt_nr
            LEFT JOIN care_encounter_diagnosis AS ced
              ON ced.encounter_nr = ce.encounter_nr
            LEFT JOIN care_icd10_en AS cie
              ON cie.diagnosis_code = ced.code
          WHERE ". $cond1 . $cond2 ."
            AND ce.encounter_type = '2'
            AND ce.encounter_status NOT IN ('deleted', 'void', 'cancelled', 'hidden')
          ORDER BY ce.encounter_date ASC ";

//added by Justin 10/27/2015
$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
    $row['hospital_name']   = strtoupper($row['hosp_name']);
}
else {
    $row['hospital_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
}
$params->put('hospital_name',$row['hospital_name']);
//end

$rs = $db->Execute($query);
$totalRecord = 0;
if($rs){
    if($rs->RecordCount() > 0){
        $i = 0;

        while($row = $rs->FetchRow()){
            $status = $encounter->isPatientNew($row['pid'],$row['date_reg'], $row['date_enc']);

            $data[$i] = array(
                'num_field' => $i + 1,
                'pid' => $row['pid'],
                'fullname' => utf8_decode(trim($row['fullname'])),
                'datetime' => $row['enc_date'],
                'age' => $row['age'],
                'gender' => $row['gender'],
                'address' => utf8_decode(trim(ucwords($row['address']))),
                'consultation' => $row['consultation'],
                'status' => ($status <= 1) ? 'New' : 'Old',
                'diagnosis' => $row['diagnosis'],
                'physician' => (utf8_decode(trim($row['physician'])) == null) ? '' : 'DR. ' . utf8_decode(trim($row['physician']))
            );
            $totalRecord += 1;
            $i++;
        }
    }
    else{
        $data[0]['fullname'] = 'No Data';
    }
}
else{
    $data[0]['fullname'] = 'No Data';
}

$data[0]['num_records'] = $totalRecord;