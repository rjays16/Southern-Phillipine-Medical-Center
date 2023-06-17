<?php
/**
 * @author Gervie 03/31/2016
 *
 * List of Billed Patients with Payward Settlements.
 */

require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db, $HTTP_SESSION_VARS;

$params->put('date_span',"From " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));
$params->put('p_type', $ins_label2);
$params->put('biller', $HTTP_SESSION_VARS['sess_login_username']);

$user_id = $HTTP_SESSION_VARS['sess_login_userid'];

$cond1 = "DATE(bt.action_date_finished)
           BETWEEN
                DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                AND
                DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond2 = " bt.biller = " . $db->qstr($user_id);

$sql = "SELECT
          fn_get_person_name_first_mi_last (ce.pid) AS name,
          IF(ins.hcare_id = '18', 'PHIC', 'NPHIC') AS phic,
          ce.pid AS hrn,
          bt.encounter_nr,
          bt.bill_nr,
          bt.action_date,
          bt.action_date_finished,
          bt.action_taken
        FROM
          seg_billing_transactions bt 
          INNER JOIN care_encounter ce 
            ON bt.encounter_nr = ce.encounter_nr 
          LEFT JOIN seg_encounter_insurance ins 
            ON ins.encounter_nr = ce.encounter_nr
          LEFT JOIN seg_billing_encounter be
           ON bt.bill_nr = be.bill_nr
        WHERE ". $cond1 ."
          AND ". $cond2 . $cond_classification ."
          AND bt.action_taken IN ('payward_settle','tentative','rebilled')
          AND be.accommodation_type = '2'
        ORDER BY bt.action_date_finished ASC";
$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){
          if($row['action_taken']=="payward_settle")$row['action_taken']="Payward Settled";
            $data[$i] = array(
                'num' => $i + 1,
                'name' => utf8_decode(trim(mb_strtoupper($row['name']))),
                'phic' => $row['phic'],
                'hrn' => $row['hrn'],
                'encounter' => $row['encounter_nr'],
                'bill' => $row['bill_nr'],
                'started' => ($row['action_date']) ? date('m/d/Y h:i:s A',strtotime($row['action_date'])) : '',
                'ended' => ($row['action_date_finished']) ? date('m/d/Y h:i:s A',strtotime($row['action_date_finished'])) : '',
                'remarks' => mb_strtoupper($row['action_taken'])

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