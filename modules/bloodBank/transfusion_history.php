<?php
/**
 * @author Nick B. Alcala 06-30-2014
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/inc_jasperReporting.php');

$objInfo = new Hospital_Admin();
$person_obj=new Person;
$pid = $_GET['pid'];
$encounter_nr = $_GET['encounter_nr'];

if ($row = $objInfo->getAllHospitalInfo()) {
    $hosp_country = strtoupper($row['hosp_country']);
    $hosp_agency = strtoupper($row['hosp_agency']);
    $hosp_name   = strtoupper($row['hosp_name']);
    $hosp_addr1   = strtoupper($row['hosp_addr1']);
}else {
    $hosp_country = "Republic of the Philippines";
    $hosp_agency  = "DEPARTMENT OF HEALTH";
    $hosp_name    = "DAVAO MEDICAL CENTER";
    $hosp_addr1   = "JICA Bldg., JP Laurel Avenue, Davao City";
}

$insurance = $person_obj->getInsurance_nr($pid);
$person = $person_obj->BasicDataArray($pid);

$insurance_nr = ($insurance['insurance_nr']) ? $insurance['insurance_nr'] : 'Not a Member';
$request_name = $person['name_first']." ".$person['name_middle']." ".$person['name_last'];
$request_name = ucwords(strtolower($request_name));
$request_name = htmlspecialchars($request_name);
$gender = ($person['sex']=='m') ? "MALE" : "FEMALE";

$street_name = $person['street_name'];
$brgy_name = $person['brgy_name'];
$mun_name = $person['mun_name'];
$prov_name = $person['prov_name'];

if ($street_name){
    if ($brgy_name!="NOT PROVIDED")
        $street_name = $street_name.", ";
    else
        $street_name = $street_name.", ";
}

if ((!($brgy_name)) || ($brgy_name=="NOT PROVIDED"))
    $brgy_name = "";
else
    $brgy_name  = $brgy_name.", ";

if ((!($mun_name)) || ($mun_name=="NOT PROVIDED"))
    $mun_name = "";
else{
    if ($brgy_name)
        $mun_name = $mun_name;
}

if ((!($prov_name)) || ($prov_name=="NOT PROVIDED"))
    $prov_name = "";

if(stristr(trim($mun_name), 'city') === FALSE){
    if ((!empty($mun_name))&&(!empty($prov_name))){
        if ($prov_name!="NOT PROVIDED")
            $prov_name = ", ".trim($prov_name);
        else
            $prov_name = "";
    }else{
        $prov_name = "";
    }
}else
    $prov_name = ", ".trim($prov_name);

$request_address = $street_name.$brgy_name.$mun_name.$prov_name;

$params = array(
    'hosp_country' => $hosp_country,
    'hosp_agency' => $hosp_agency,
    'hosp_name' => $hosp_name,
    'hosp_addr1' => $hosp_addr1,
    'hosp_number' => $pid,
    'phic_number' => $insurance_nr,
    'patient_name' => mb_strtoupper($request_name),
    'age' => $person['age'],
    'gender' => $gender,
    'civil_status' => mb_strtoupper($person['civil_status']),
    'address' => $request_address,
    'case_number' => $encounter_nr
);

$pid = $db->qstr($pid);

$sql = "SELECT
          DATE_FORMAT(sbrs.issuance_date, '%M %d,%Y') AS issuance_date,
          sbrd.serial_no AS serial_nr,
          sbrd.component AS component,
          sbrd.blood_source AS source,
          (SELECT
              name
           FROM seg_blood_type AS sbt
           WHERE sbtp.blood_type = sbt.id) AS blood_type,
          (
            CASE
              (sbrd.result)
              WHEN 'noresult'
              THEN 'No Result'
              WHEN 'compat'
              THEN 'Compatible'
              WHEN 'incompat'
              THEN 'Incmpatible'
              WHEN 'retype'
              THEN 'Re-typing'
              WHEN NULL
              THEN ''
            END
          ) AS result,
          (
            IF(
              sbrs.date_return,
              DATE_FORMAT(sbrs.date_return, '%M %d,%Y'),
              DATE_FORMAT(sbrs.date_consumed, '%M %d,%Y')
            )
          ) AS con_ret_date
        FROM
          care_encounter AS ce
          INNER JOIN seg_lab_serv AS sls
            ON ce.encounter_nr = sls.encounter_nr
          INNER JOIN seg_blood_received_details AS sbrd
            ON sls.refno = sbrd.refno
            AND sbrd.status <> 'not yet'
          LEFT JOIN seg_blood_received_status AS sbrs
            ON sls.refno = sbrs.refno
            AND sbrd.ordering = sbrs.ordering
          LEFT JOIN seg_blood_type_patient AS sbtp
            ON sbtp.pid = ce.pid
        WHERE ce.pid = $pid";

$rs = $db->Execute($sql);
if($rs){
    if($rs->RecordCount()){
        $data = $rs->GetRows();
        $params['blood_type'] = $data[0]['blood_type'];
    }else{
        $data[0]['issuance_date'] = "";
    }
}else{
    $data[0]['issuance_date'] = "";
}

showReport('BB_Transfusion_History',$params,$data,'pdf');
?>