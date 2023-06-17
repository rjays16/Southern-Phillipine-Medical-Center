<?php
/**
 * @author Gervie 11/24/2015
 *
 * List of Billed Patients.
 */

require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db, $HTTP_SESSION_VARS;

if(!$with_ptype){
    $patient_type_label = "ALL PATIENTS";
    $patient_type = '1,2,3,4,6';
}

$params->put('date_span',"From " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));
$params->put('p_type', $patient_type_label . " " . $ins_label);
$params->put('biller', $HTTP_SESSION_VARS['sess_login_username']);

$user_id = $HTTP_SESSION_VARS['sess_login_userid'];

$cond1 = "DATE(sbe.bill_time_ended)
           BETWEEN
                DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                AND
                DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond2 = " ce.encounter_type IN (" . $patient_type . ") ";
$cond3 = " sbe.modify_id = " . $db->qstr($user_id);

$sql = "SELECT
          fn_get_person_name_first_mi_last (ce.pid) AS name,
          IF(ins.hcare_id = 18, 'PHIC', 'NPHIC') AS phic,
          ce.pid AS hrn,
          sbe.encounter_nr,
          sbe.bill_nr,
          sbe.bill_time_started,
          sbe.bill_time_ended
        FROM
          seg_billing_encounter sbe
          INNER JOIN care_encounter ce
            ON sbe.encounter_nr = ce.encounter_nr
    	  LEFT JOIN seg_encounter_insurance ins 
    		ON ins.encounter_nr = ce.encounter_nr
        WHERE ". $cond1 ."
          AND ". $cond2 ."
          AND ". $cond3 . $cond_classification ."
          AND sbe.is_deleted IS NULL
          AND sbe.is_final = '1'
        ORDER BY sbe.bill_dte ASC";

$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){
            $data[$i] = array(
                'num' => $i + 1,
                'name' => utf8_decode(trim(mb_strtoupper($row['name']))),
                'phic' => $row['phic'],
                'hrn' => $row['hrn'],
                'encounter' => $row['encounter_nr'],
                'bill' => $row['bill_nr'],
                'started' => ($row['bill_time_started']) ? date('m/d/Y h:i:s A',strtotime($row['bill_time_started'])) : '',
                'ended' => ($row['bill_time_ended']) ? date('m/d/Y h:i:s A',strtotime($row['bill_time_ended'])) : ''
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