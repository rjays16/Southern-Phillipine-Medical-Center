<?php
/**
 * @author Gervie 11/12/2015
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$params->put('title', "PHS ".$phs_label." DEPENDENTS");
$params->put('date_span',"Period: " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));

$cond1 = "DATE(sd.action_date)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

if($phs_status == 'active'){
    $cond2 = " sd.action_taken IN ('activated')";
    $params->put('action_label', "Recorded By");
}
else if($phs_status == 'inactive'){
    $cond2 = " sd.action_taken NOT IN ('activated')";
    $params->put('action_label', "Deactivated/Deleted By");
}
else{
    $cond2 = " sd.action_taken IN ('activated','deleted','deactivated')";
}

$sql = "SELECT
          sd.dependent_pid AS hrn,
          fn_get_person_lastname_first (sd.dependent_pid) AS dependent,
          IF(
            fn_calculate_age (cp.date_birth, NOW()),
            fn_get_age (
              sd.action_date,
              cp.date_birth
            ),
            cp.age
          ) AS age,
          cp.date_birth AS birthdate,
          sd.relationship,
          cpl.pid AS emp_hrn,
          cpl.nr AS emp_nr,
          fn_get_person_lastname_first (sd.parent_pid) AS personnel,
          cpl.job_function_title AS job,
          cpl.job_position AS position,
          sd.action_personnel,
          sd.action_date,
          sd.action_taken,
          cpl.status AS emp_status
        FROM
          seg_dependents_monitoring sd
          INNER JOIN care_person cp
            ON cp.pid = sd.dependent_pid
            INNER JOIN care_personell cpl
            ON cpl.pid = sd.parent_pid
        WHERE " . $cond1 .
            "AND " . $cond2 .
            "ORDER BY sd.action_date ASC";

$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){

            switch($row['action_taken']){
                case 'activated':
                    $status = 'Active';
                    break;
                case 'deactivated':
                    $status = 'Inactive';
                    break;
                case 'deleted':
                    $status = 'Deleted';
                    break;
            }

            $data[$i] = array(
                'num' => $i + 1,
                'hrn' => $row['hrn'],
                'dependent' => utf8_decode(trim(mb_strtoupper($row['dependent']))),
                'age' => ($row['age']) ? $row['age'] : 'N/A',
                'birthdate' => ($row['birthdate'] == '0000-00-00') ? 'NO DOB' : date('m/d/Y', strtotime($row['birthdate'])),
                'relationship' => mb_strtoupper($row['relationship']),
                'emp_nr' => $row['emp_nr'],
                'emp_hrn' => $row['emp_hrn'],
                'personnel' => utf8_decode(trim(mb_strtoupper($row['personnel']))),
                'job' => mb_strtoupper($row['job']),
                'position' => mb_strtoupper($row['position']),
                'recorded_by' => utf8_decode(trim(mb_strtoupper($row['action_personnel']))),
                'record_date' => date('m/d/Y h:i A', strtotime($row['action_date'])),
                'status' => mb_strtoupper($status)
            );

            $i++;
        }
    }
    else{
        $data = array(
            array(
                'emp_hrn' => 'No Data',
            )
        );
    }
}
else{
    $data = array(
        array(
            'emp_hrn' => 'No Data',
        )
    );
}

