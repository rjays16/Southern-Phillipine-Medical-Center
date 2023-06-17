<?php
/**
 * @author Nick B. Alcala 06-04-2014
 * Generate Blood Bank reports :
 * -Daily Summary Report for Blood Transactions CONSUMED-RETURNED
 * -Daily Summary Report for Blood Transactions RETURNED-REISSUED
 */
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

$from = date('M d,Y',$_GET['from_date']);
$to = date('M d,Y',$_GET['to_date']);

$params->put('hospital_name',mb_strtoupper($hosp_name));
$params->put("header", $report_title);
$params->put("department", "Laboratory");

$data = getAllReturnedReissued();

/*******************************************/

function getAllReturnedReissued(){
    global $db;

    if($_GET['reportid'] == 'BB_Daily_Summary_Report'){
        $cond[0] =  'c.date_return';
        $cond[1] =  'c.date_reissue';
        $conjunction = 'OR';
    }else{
        $cond[0] =  'c.date_consumed';
        $cond[1] =  'c.date_return';
        $conjunction = 'OR';
    }

    $cond[2] =  "ORDER BY $cond[0] ASC";

    $sql = "SELECT
              d.pid AS hrn,
              fn_get_person_name (d.pid) AS patient_name,
              b.serial_no AS serialno,
              b.component AS component,
              b.blood_source AS blood_source,
              f.name AS blood_type,
              (
                CASE
                  (b.result)
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
              DATE_FORMAT(c.issuance_date, '%M %d,%Y %h:%i %p') AS issuance_date_time,
              IFNULL(DATE_FORMAT(c.date_reissue, '%M %d,%Y %h:%i %p'),DATE_FORMAT(c.date_return, '%M %d,%Y %h:%i %p')) AS reissue_date_time,
              DATE_FORMAT(c.date_return, '%M %d,%Y %h:%i %p') AS returned_date_time,
              IFNULL(DATE_FORMAT(c.date_consumed, '%M %d,%Y %h:%i %p'),DATE_FORMAT(c.date_return, '%M %d,%Y %h:%i %p'))   AS consumed_date_time
            FROM
              seg_lab_serv AS a
              LEFT JOIN seg_blood_received_details AS b
                ON a.refno = b.refno
              LEFT JOIN seg_blood_received_status AS c
                ON a.refno = c.refno
                AND b.ordering = c.ordering
                AND b.service_code = c.service_code
              LEFT JOIN care_encounter AS d
                ON a.encounter_nr = d.encounter_nr
              LEFT JOIN seg_blood_type_patient AS e
                ON e.pid = d.pid
              LEFT JOIN seg_blood_type AS f
                ON e.blood_type = f.id
            WHERE (
                DATE_FORMAT($cond[0], '%Y-%m-%d')
                  BETWEEN DATE_FORMAT(?, '%Y-%m-%d') AND DATE_FORMAT(?, '%Y-%m-%d')
                $conjunction 
                DATE_FORMAT($cond[1], '%Y-%m-%d') 
                  BETWEEN DATE_FORMAT(?, '%Y-%m-%d') AND DATE_FORMAT(?, '%Y-%m-%d')
              ) $cond[2]";

    $params = array(
        date('Y-m-d',$_GET['from_date']),
        date('Y-m-d',$_GET['to_date']),
        date('Y-m-d',$_GET['from_date']),
        date('Y-m-d',$_GET['to_date']),
    );

    $rs = $db->Execute($sql,$params);
    if($rs){
        if($rs->RecordCount()){
            return $rs->GetRows();
        }else{
            return array(
                'hrn' => ''
            );
        }
    }else{
        return array(
            'hrn' => ''
        );
    }
}