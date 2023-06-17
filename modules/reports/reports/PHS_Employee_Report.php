<?php
/**
 * @author Arco - 06/02/2016
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$params->put('header1', "Integrated Hospital Operations and Management Program (IHOMP)");
$params->put('header2', "PHS ADMIN REPORT");
$params->put('date_span',"Period: " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));

$cond1 = "DATE(sem.action_date)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

if($phs_status == 'active'){
    $cond2 = " sem.action_taken IN ('activated')";
    $params->put('title', "PHS ".$phs_label." EMPLOYEES");
}
else if($phs_status == 'inactive'){
    $cond2 = " sem.action_taken IN ('deactivated')";
    $params->put('title', "PHS ".$phs_label." EMPLOYEES");
}else if ($phs_status == 'all'){
    $cond2 = " sem.action_taken IN ('activated','deactivated')";
    $params->put('title', "PHS ".$phs_label." EMPLOYEES");
}

$sql = "SELECT
          sem.employee_pid AS hrn,
          cpl.nr AS emp_nr,
          fn_get_person_lastname_first (sem.employee_pid) AS employee,
          cpl.job_position AS position,
          (SELECT category
            FROM seg_phs_job_status 
            WHERE id = cpl.category) AS category,
          cpl.bio_nr AS bio_nr,
          cpl.id_nr AS id_nr,
          (SELECT name_formal 
            FROM care_department 
            WHERE nr = cpa.location_nr) AS department,
          sem.remarks AS remarks,
          cpl.create_id AS create_id,
          cpl.create_time AS create_time,
          sem.action_personnel,
          sem.action_date,
          sem.action_taken AS status,
          sem.is_new
        FROM
          seg_employees_monitoring sem
          INNER JOIN care_personell cpl
            ON cpl.nr = sem.employee_nr
          LEFT JOIN care_personell_assignment cpa
            ON cpa.personell_nr = sem.employee_nr
        WHERE " . $cond1 .
            "AND " . $cond2 .
            "ORDER BY sem.action_date ASC";

$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){

            if ($row['position'] == "" || $row['position'] == NULL){
              $pos = "";
            } else {
              $pos = mb_strtoupper($row['position']);
            }            
            if ($row['category'] == "" || $row['category'] == NULL){
              $cat = "";
            } else {
              if ($row['category'] == "1"){
                $cat = "Category I";
              } else if ($row['category'] == "2"){
                $cat = "Category II";
              } else if ($row['category'] == "3"){
                $cat = "Category III";
              } else if ($row['category'] == "4"){
                $cat = "Category IV";
              }
            }
            if ($row['bio_nr'] == "" || $row['bio_nr'] == NULL){
              $bio = "";
            } else {
              $bio = $row['bio_nr'];
            }if ($row['id_nr'] == "" || $row['id_nr'] == NULL){
              $id = "";
            } else {
              $id = $row['id_nr'];
            }if ($row['department'] == "" || $row['department'] == NULL){
              $dep = "";
            } else {
              $dep = $row['department'];
            }
            if ($row['remarks'] == "" || $row['remarks'] == NULL){
              $rem = "";
            } else {
              $rem = $row['remarks'];
            }
            if ($row['is_new'] == "0"){
              $rec_by = mb_strtoupper($row['action_personnel']);
              $rec_date = date('m/d/Y h:i A', strtotime($row['action_date']));
            } else {
              $rec_by = "";
              $rec_date = "";
            }
            if ($row['status'] == "" || $row['status'] == NULL){
              $stat = "";
            } else {
              $stat = mb_strtoupper($row['status']);
            }

            $data[$i] = array(
                'num' => $i + 1,
                'hrn' => $row['hrn'],
                'emp_nr' => $row['emp_nr'],
                'employee' => utf8_decode(trim(mb_strtoupper($row['employee']))),
                'position' => $pos,
                'category' => $cat,
                'bio_nr' => $bio,
                'id_nr' => $id,
                'department' => $dep,
                'remarks' => $rem,
                'create_id' => $row['create_id'],
                'create_time' => date('m/d/Y h:i A', strtotime($row['create_time'])),                                
                'recorded_by' => utf8_decode(trim($rec_by)),
                'record_date' => $rec_date,
                'status' => $stat
            );

            $i++;
        }
    }
    else{
        $data = array(
            array(
                'num' => '',
                'hrn' => 'No Data',
                'emp_nr' => 'No Data',
                'employee' => 'No Data',
                'position' => 'No Data',
                'category' => 'No Data',
                'bio_nr' => 'No Data',
                'id_nr' => 'No Data',
                'department' => 'No Data',
                'remarks' => 'No Data',
                'create_id' => 'No Data',
                'create_time' => 'No Data',                                
                'recorded_by' => 'No Data',
                'record_date' => 'No Data',
                'status' => 'No Data'
            )
        );
    }
}
else{
    $data = array(
        array(
            'num' => '',
            'hrn' => 'No Data',
            'emp_nr' => 'No Data',
            'employee' => 'No Data',
            'position' => 'No Data',
            'category' => 'No Data',
            'bio_nr' => 'No Data',
            'id_nr' => 'No Data',
            'department' => 'No Data',
            'remarks' => 'No Data',
            'create_id' => 'No Data',
            'create_time' => 'No Data',                                
            'recorded_by' => 'No Data',
            'record_date' => 'No Data',
            'status' => 'No Data'
        )
    );
}

