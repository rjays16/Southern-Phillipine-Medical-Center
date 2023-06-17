<?php
//added by Nick 2-6-2015
//todo refactor
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_seg_dependents.php');
require_once($root_path.'include/care_api_classes/class_personell.php'); // Added by: Arco - 06/03/2016 

global $db;
$db->StartTrans();

$db->debug = true;

$sql = "SELECT
          nr,
          (SELECT
            login_id
          FROM
            care_users AS cu
          WHERE cu.personell_nr = cpl.nr
          LIMIT 1) AS login_id,
          cpl.pid
        FROM
          care_personell AS cpl
        WHERE DATE_FORMAT(contract_end, '%Y-%m-%d') <= DATE_FORMAT(NOW(), '%Y-%m-%d')
          AND DATE_FORMAT(contract_end, '%Y-%m-%d') <> '0000-00-00'
          AND status <> 'deleted'";

$rows = $db->GetAll($sql);
if (!empty($rows)) {

    echo "<h4>Updating " . count($rows) . " rows</h4>";

    foreach ($rows as $row) {

        $history = "CONCAT(history,'Locked Personnel: " . date('Y-m-d H:i:s') . " [System]\n')";
        $ok = $db->Execute("UPDATE
                              care_personell
                            SET status='deleted',
                                history={$history},
                                modify_id=?,
                                modify_time=NOW()
                            WHERE nr = ?", array('Administrator', $row['nr']));
        if (!$ok) {
            $db->FailTrans();
            break;
        }

        if ($row['login_id']) {
            $history = "CONCAT(history,'Locked Personnel: " . date('Y-m-d H:i:s') . " [System]\n')";
            $lock_audit="INSERT INTO seg_areas_duration_time (pid,duration,mode,create_id,create_dt) SELECT cp.pid,'00:00:00 00','LOCK','Administrator',".$db->qstr(date('Y-m-d H:i:s'))." FROM care_personell cp LEFT JOIN care_users cu on cp.nr=cu.personell_nr WHERE cu.personell_nr=".$db->qstr($row['nr']);
            // die($lock_audit);
            $db->Execute($lock_audit);
            $ok = $db->Execute("UPDATE care_users SET lockflag=1,lock_duration='00:00:00',modify_id=?,modify_time=NOW(),history={$history} WHERE personell_nr=?", array(
                'Administrator',
                $row['nr'],
            ));
            if (!$ok) {
                $db->FailTrans();
                break;
            }
        }

        $history = "CONCAT(history,'Locked Personnel: " . date('Y-m-d H:i:s') . " [System]\n')";
        $ok = $db->Execute("UPDATE
                              care_personell_assignment
                            SET date_end=NOW(),
                                status='deleted',
                                history = {$history},
                                modify_id = ?,
                                modify_time = NOW()
                            WHERE personell_nr=?", array('Administrator', $row['nr']));
        if (!$ok) {
            $db->FailTrans();
            break;
        }

        // Added by Gervie 05/02/2016
        // For Dependents Monitoring
        $sql = "SELECT sd.* FROM seg_dependents sd WHERE sd.parent_pid = ".$db->qstr($row['pid'])." AND sd.status = 'member'";
        $dependent = $db->Execute($sql);
        $objDependent = new SegDependents();

        while($dep = $dependent->FetchRow()) {
            $data['parent_pid'] = $dep['parent_pid'];
            $data['dependent_pid'] = $dep['dependent_pid'];
            $data['relationship'] = $dep['relationship'];
            $data['create_id'] = 'System';

            $objDependent->dependentMonitoring($data, 'deactivated');
        }

        // Added by: Arco - 06/03/2016
        // For Employee Monitoring Deactivation     
        $objPersonell = new Personell();
        $data['employee_nr'] = $row['nr'];
        $data['employee_pid'] = $row['pid'];
        $data['checker_for_new_employee'] = 0;
        $data['remarks'] = 'Auto-Locked';
        if (isset($data['employee_nr'])&&isset($data['employee_pid'])) {
            $objPersonell->employeeMonitoring($data, 'deactivated');
        }
        // end arcute

        $history = "CONCAT(history,'Locked Personnel: " . date('Y-m-d H:i:s') . " [System]\n')";
        $ok = $db->Execute("UPDATE
                              seg_dependents
                            SET status='expired',
                                history = {$history},
                                modify_id = ?,
                                modify_dt = NOW()
                            WHERE parent_pid=?", array('Administrator', $row['pid']));
        if (!$ok) {
            $db->FailTrans();
            break;
        }

    }//end foreach
}//end if
// For Dependents Over 21 OR Dependents of retired personnels
$sql_over_21 = "SELECT 
                          sdp.* 
                        FROM
                          seg_dependents sdp 
                          LEFT JOIN `care_personell` cpl 
                            ON sdp.`parent_pid` = cpl.`pid` 
                          LEFT JOIN `care_personell_assignment` cpa 
                            ON cpl.`nr` = cpa.`personell_nr` 
                          LEFT JOIN care_person cp 
                            ON sdp.`dependent_pid` = cp.`pid` 
                          LEFT JOIN seg_phs_job_status spjs 
                            ON cpl.`category` = spjs.`id` 
                          LEFT JOIN `seg_phs_category` spc 
                            ON spjs.`category` = spc.`id` 
                        WHERE (
                              sdp.`relationship` IN ('SON', 'CHILD', 'DAUGHTER') 
                              AND (
                                `fn_get_age_days` (DATE(NOW()), cp.`date_birth`) / 365.25
                              ) > 21
                          ) 
                          AND sdp.`status` = 'member' ";
$dependent_over_21 = $db->Execute($sql_over_21);
$objDependent2 = new SegDependents();

while($dep_over_21 = $dependent_over_21->FetchRow()) {
    unset($data2);
    $data2['parent_pid'] = $dep_over_21['parent_pid'];
    $data2['dependent_pid'] = $dep_over_21['dependent_pid'];
    $data2['relationship'] = $dep_over_21['relationship'];
    $data2['create_id'] = 'System';

    $objDependent2->dependentMonitoring($data2, 'Locked');
    echo "<br/> Deactivated Dependent - ".$dep_over_21['dependent_pid'];
    $history_over_21 = "CONCAT(history,'Locked Personnel: " . date('Y-m-d H:i:s') . " [System]\n')";
    $db->Execute("UPDATE
                              seg_dependents
                            SET status='expired',
                                history = {$history_over_21},
                                modify_id = ?,
                                modify_dt = NOW()
                            WHERE dependent_pid=?", array('Administrator', $dep_over_21['dependent_pid']));
}
//$db->FailTrans();
$db->CompleteTrans();