<?php
/**
 * @author Gervie 11/23/2015
 *
 * Billed Patients with TEMP Remarks in PHIC
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db, $HTTP_SESSION_VARS;

$params->put('date_span',"From " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));
$params->put('biller', $HTTP_SESSION_VARS['sess_login_username']);

$user_id = $HTTP_SESSION_VARS['sess_login_userid'];

//Get patient type
$ptype_param = $_GET['param'];
$patient_type = substr($ptype_param, strrpos($ptype_param, "-") + 1);

if($patient_type == '1'){
    $label = "OUTPATIENTS";
    $cond2 = " ce.encounter_type = 2";			//opd
}
else if($patient_type == '2'){
    $label = "ER PATIENTS";
    $cond2 = " ce.encounter_type = 1";			//er
}
else if($patient_type == '3'){
    $label = "INPATIENTS";
    $cond2 = " ce.encounter_type IN (3,4)";	    //inpatient
}
else if($patient_type == 'all'){
    $label = "ALL PATIENTS";
    $cond2 = " ce.encounter_type IN (1,2,3,4)"; //all
}
else {
    $label = "INPATIENTS";
    $cond2 = " ce.encounter_type IN (3,4)";     //default
}

$params->put('p_type', $label);

$cond1 = "DATE(sbt.action_date) BETWEEN
            DATE(".$db->qstr(date('Y-m-d',$from_date)).") AND
            DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
//$cond2 = " ce.encounter_type IN (" . $patient_type . ") ";

$sql = "SELECT DISTINCT fn_get_person_name_first_mi_last (ce.pid) AS patient,
          ce.pid AS hrn, sbt.encounter_nr, sbt.bill_nr,
          IF(ce.current_ward_nr = '0',
            IF(
              ce.current_dept_nr = '0',
              fn_get_department_name (ce.consulting_dept_nr),
              fn_get_department_name (ce.current_dept_nr)
            ),
            fn_get_ward_name (ce.current_ward_nr)
          ) AS ward,
          sbt.action_date,
          sm.memcategory_desc AS category,
          sbt.biller
        FROM
          seg_billing_temp_phic sbt 
        INNER JOIN care_encounter ce
          ON sbt.`encounter_nr` = ce.`encounter_nr`
        INNER JOIN seg_memcategory sm
          ON sbt.`phic_type` = sm.`memcategory_code`
        WHERE " . $cond1 . "
          AND " . $cond2 . "
          AND ce.status NOT IN ('deleted', 'void', 'cancelled', 'hidden')
        ORDER BY sbt.action_date ASC";

//echo $sql; die;

$res = $db->Execute($sql);
$i = 0;
//var_dump($res);

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){
            $data[$i] = array(
                'num' => $i + 1,
                'name' => utf8_decode(trim($row['patient'])),
                'hrn' => $row['hrn'],
                'encounter' => $row['encounter_nr'],
                'bill' => $row['bill_nr'],
                'ward' => $row['ward'],
                'encoder' => $row['biller'],
                'encoded' => date('m-d-Y h:i:s A', strtotime($row['action_date'])),
                'category' => $row['category']
            );

            $i++;
        }
    }
    else{
        $data = array(
            array(
                'name' => 'No Data'
            )
        );
    }
}
else{
    $data = array(
        array(
            'name' => 'No Data'
        )
    );
}