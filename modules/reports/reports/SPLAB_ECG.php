<?php
/**
 * @author Gervie 10/05/2015
 * Report for ECG in CMAP, LINGAP, CASH, CLASS D Transactions
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_personell.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$personellObj = new Personell();

//-------------- HEADER -------------//
$params->put('name_report1', 'SPECIAL LABORATORY (OPCC)');
$params->put('name_report2', 'ELECTROCARDIOGRAPHY (ECG)');
$params->put('title_report', $charge_title);
$params->put('or_type', $or_type);
$params->put('dateRange', strtoupper(date('F d, Y',$from_date) . " - " . date('F d, Y',$to_date)));
$params->put('dept_reader', $classification_dept);


$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

/*$data[0]['img_spmc'] = $baseurl . "gui/img/logos/dmc_logo.jpg";
$data[0]['img_doh'] = $baseurl . "img/doh.png";*/
$params->put('img_spmc', $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put('img_doh', $baseurl . "img/doh.png");
//-----------------------------------//

$cond1 = "DATE(ls.serv_dt)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond2 = " ORDER BY serv_dt, p_name ASC";

$cmap_sql = "SELECT
                ls.serv_dt, fn_get_person_name_first_mi_last(ls.pid) AS p_name,
                ls.pid, 'CMAP' AS or_type, IFNULL(cm.amount, lsd.price_cash) AS amount,
                IF(ls.encounter_nr IS NULL, p.age, fn_get_ageyr(DATE(ce.encounter_date), p.date_birth)) AS age
             FROM
                seg_lab_serv ls
             INNER JOIN seg_lab_servdetails lsd
                ON lsd.refno = ls.refno
             LEFT JOIN seg_cmap_entries_laboratory cm
                ON cm.ref_no = ls.refno
             LEFT JOIN seg_payment_workaround pw 
                ON ls.refno = pw.refno
                AND pw.type = 'cmap'
             INNER JOIN care_person p
                ON ls.pid = p.pid
             LEFT JOIN care_encounter ce
                ON ls.encounter_nr = ce.encounter_nr
            WHERE ". $cond1 ."
              AND (lsd.service_code IN ('ECG') AND lsd.request_flag = 'cmap' OR cm.service_code IN ('ECG'))";

$lingap_sql = "SELECT
                 ls.serv_dt, fn_get_person_name_first_mi_last(ls.pid) AS p_name,
                 ls.pid, 'LINGAP' AS or_type, IFNULL(le.amount, lsd.price_cash) AS amount,
                 IF(ls.encounter_nr IS NULL, p.age, fn_get_ageyr(DATE(ce.encounter_date), p.date_birth)) AS age
               FROM
                 seg_lab_serv ls
               INNER JOIN seg_lab_servdetails lsd
                 ON lsd.refno = ls.refno
               LEFT JOIN seg_lingap_entries_laboratory le 
                 ON le.ref_no = ls.refno 
               LEFT JOIN seg_payment_workaround pw 
                 ON ls.refno = pw.refno
                 AND pw.type = 'lingap'
               INNER JOIN care_person p
                 ON ls.pid = p.pid
               LEFT JOIN care_encounter ce
                 ON ls.encounter_nr = ce.encounter_nr
               WHERE ". $cond1 ."
                 AND (lsd.service_code IN ('ECG') AND lsd.request_flag = 'lingap' OR le.`service_code` IN ('ECG'))";

$charity_sql = "SELECT
                  ls.serv_dt,fn_get_person_name_first_mi_last(ls.pid) AS p_name,
                  ls.pid,'CLASS D' AS or_type,0 AS amount,
                  IF(ls.encounter_nr IS NULL, p.age, fn_get_ageyr(DATE(ce.encounter_date), p.date_birth)) AS age
                FROM
                  seg_lab_serv ls
                INNER JOIN seg_lab_servdetails AS lsd
                  ON lsd.refno = ls.refno
                INNER JOIN care_person p
                  ON ls.pid = p.pid
                LEFT JOIN care_encounter ce
                  ON ls.encounter_nr = ce.encounter_nr
                WHERE ". $cond1 . "
                  AND lsd.request_flag IN ('charity')
                  AND lsd.service_code IN ('ECG')";

$dwsd_sql = "SELECT
               ls.serv_dt, fn_get_person_name_first_mi_last (ls.pid) AS p_name,
               ls.pid,'DSWD' AS or_type,lsd.price_charge AS amount,
               IF(ls.encounter_nr IS NULL, p.age, fn_get_ageyr(DATE(ce.encounter_date), p.date_birth)) AS age
             FROM
               seg_lab_serv ls
             INNER JOIN seg_lab_servdetails lsd
               ON ls.refno = lsd.refno
             INNER JOIN care_person p
               ON ls.pid = p.pid
             LEFT JOIN care_encounter ce
               ON ls.encounter_nr = ce.encounter_nr
             WHERE ". $cond1 ."
               AND lsd.service_code IN ('ECG')
               AND ls.grant_type IN ('dswd')";

$pcso_sql = "SELECT
               ls.serv_dt, fn_get_person_name_first_mi_last (ls.pid) AS p_name,
               ls.pid,'PCSO' AS or_type,lsd.price_charge AS amount,
               IF(ls.encounter_nr IS NULL, p.age, fn_get_ageyr(DATE(ce.encounter_date), p.date_birth)) AS age
             FROM
               seg_lab_serv ls
             INNER JOIN seg_lab_servdetails lsd
               ON ls.refno = lsd.refno
             INNER JOIN care_person p
               ON ls.pid = p.pid
             LEFT JOIN care_encounter ce
               ON ls.encounter_nr = ce.encounter_nr
             WHERE ". $cond1 ."
               AND lsd.service_code IN ('ECG')
               AND ls.grant_type IN ('pcso')";

$cash_sql = "SELECT
               ls.serv_dt, fn_get_person_name_first_mi_last(ls.pid) AS p_name,
               ls.pid, IFNULL(pr.or_no, pw.control_no) AS or_type, IFNULL(pr.amount_due, lsd.price_cash) AS amount,
               IF(ls.encounter_nr IS NULL, p.age, fn_get_ageyr(DATE(ce.encounter_date), p.date_birth)) AS age
             FROM
               seg_lab_serv ls
             INNER JOIN seg_lab_servdetails lsd
               ON lsd.refno = ls.refno
             LEFT JOIN seg_pay_request pr
               ON pr.ref_no = ls.refno
               AND pr.service_code = 'ECG'
             INNER JOIN care_person p
               ON ls.pid = p.pid
             LEFT JOIN care_encounter ce
               ON ls.encounter_nr = ce.encounter_nr
             LEFT JOIN seg_payment_workaround pw 
               ON ls.refno = pw.refno
             WHERE ". $cond1 ."
               AND lsd.request_flag IN ('paid')
               AND lsd.service_code IN ('ECG')";
               #echo $cash_sql; die;

$all_sql = $cash_sql . " UNION ALL " . $cmap_sql . " UNION ALL " . $lingap_sql . " UNION ALL " . $dwsd_sql . " UNION ALL " . $pcso_sql . " UNION ALL " . $charity_sql;

if($charge_type == 'cmap'){
    $cmap_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $cmap_sql .") t ".$age_cond);
}
else if($charge_type == 'lingap'){
    $lingap_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $lingap_sql .") t ".$age_cond);
}
else if($charge_type == 'charity'){
    $charity_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $charity_sql .") t ".$age_cond);
}
else if($charge_type == 'paid'){
    $cash_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $cash_sql .") t ".$age_cond);
}
else if($charge_type == 'dswd'){
    $dwsd_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $dwsd_sql .") t ".$age_cond);
}
else if($charge_type == 'pcso'){
    $pcso_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $pcso_sql .") t ".$age_cond);
}
else if($charge_type == 'all'){
    $all_sql .= $cond2;
    $result = $db->Execute("SELECT * FROM (". $all_sql .") t ".$age_cond);
}


//-------------- DATA ---------------//
if($result){
    if($result->RecordCount() > 0){
        $i = 0;

        while($row = $result->FetchRow()){
            $mf = $row['amount'] * 0.7;
            $pf = $row['amount'] - $mf;

            $data[$i] = array(
                'f_num' => $i+1,
                'f_date' => date('M-d-Y', strtotime($row['serv_dt'])),
                'f_ward' => 'OP',
                'f_hrn' => $row['pid'],
                'f_name' => utf8_decode(trim(mb_strtoupper($row['p_name']))),
                'f_or' => $row['or_type'],
                'f_amt' => $row['amount'],
                'f_mf' => $mf,
                'f_pf' => $pf,
            );
            $i++;
        }
    }
    else{
        $data = array(
            array(
                'f_date' => 'No Data',
            ),
        );
    }
}
else{
    $data = array(
        array(
            'f_date' => 'No Data',
        ),
    );
}



//-------------- FOOTER -------------//
/*$noted = $personellObj->get_Signatory('spl_ecg');

$params->put('prepare_report', strtoupper($_SESSION['sess_login_username']));
$params->put('noted_report', $noted['name']);
$params->put('noted_position', $noted['signatory_position']);*/
//-----------------------------------//