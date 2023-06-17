<?php
/**
 * @author Gervie 01-18-2016
 *
 * Transmittal for machine fee and reading fee for EEG/EMG procedure.
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_personell.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$personellObj = new Personell();

//-------------- HEADER -------------//
$params->put('title', "Transmittal for Machine Fee And Reading Fee Rate For " . $con_title . " Procedure");
$params->put('or_type', "Masterlist");
$params->put('dateRange', strtoupper(date('F d, Y',$from_date) . " - " . date('F d, Y',$to_date)));
$params->put('mf_disc', $mf);
$params->put('pf_disc', $pf);

if($eeg_reader){
	$reader_label = "DR. " . $db->GetOne("SELECT fn_get_personell_name({$eeg_reader})");

	if($service_code == 'EEG')
		$doc_cond = " AND lr.consult_doctor IN ({$eeg_reader}) ";
	else
		$doc_cond = "";
}
else{
	$reader_label = "ALL READERS";
	/*$doc_reader = $db->GetOne("SELECT GROUP_CONCAT(doctor_id) FROM seg_lab_eeg_reader");

	if($service_code == 'EEG')
		$doc_cond = "AND lr.consult_doctor IN ({$doc_reader}) ";
	else
		$doc_cond = "";*/
    $doc_cond = "";
}

$params->put('reader', $reader_label);

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$params->put('img_spmc', $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put('img_doh', $baseurl . "img/doh.png");
//-----------------------------------//

$cond1 = "DATE(ls.serv_dt)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$cond2 = " ORDER BY serv_dt, p_name ASC";

$sql = "SELECT DISTINCT
          ls.`serv_dt`,
          CASE
            WHEN ce.`encounter_type` = '2' THEN 'OP'
            WHEN ce.encounter_type = '8' THEN 'WALK-IN'
            ELSE IF(
              ce.current_ward_nr != '0',
              fn_get_ward_name (ce.current_ward_nr),
              IF(
                ce.current_dept_nr != '0',
                fn_get_department_name (ce.current_dept_nr),
                fn_get_department_name (ce.consulting_dept_nr)
              )
            )
          END AS p_type,
          ls.`pid`,
          fn_get_person_lastname_first (ls.`pid`) AS p_name,
          pr.or_no AS or_type,
          lsd.price_cash AS price,
          ls.refno
        FROM
          seg_lab_serv ls
          INNER JOIN seg_lab_servdetails lsd
            ON ls.`refno` = lsd.`refno`
          LEFT JOIN care_encounter ce
            ON ls.`encounter_nr` = ce.`encounter_nr`
          LEFT JOIN seg_pay_request pr
            ON ls.refno = pr.ref_no
          ". $join . "
        WHERE ". $cond1 . $doc_cond ."
          AND lsd.request_flag = 'paid'
          AND lsd.service_code LIKE '$service_code'
          AND (lsd.status = 'done' OR (lsd.status = 'pending' AND lsd.is_served = 1))
        UNION
        ALL
        SELECT DISTINCT
          ls.`serv_dt`,
          CASE
            WHEN ce.`encounter_type` = '2'
            THEN 'OP'
            WHEN ce.encounter_type = '8'
            THEN 'WALK-IN'
            ELSE IF(
              ce.current_ward_nr != '0',
              fn_get_ward_name (ce.current_ward_nr),
              IF(
                ce.current_dept_nr != '0',
                fn_get_department_name (ce.current_dept_nr),
                fn_get_department_name (ce.consulting_dept_nr)
              )
            )
          END AS p_type,
          ls.`pid`,
          fn_get_person_lastname_first (ls.`pid`) AS p_name,
          IF(ls.grant_type != '', IFNULL(ls.grant_type, 'CHARGED'), 'CHARGED') AS or_type,
          lsd.price_charge AS price,
          ls.refno
        FROM
          seg_lab_serv ls
          INNER JOIN seg_lab_servdetails lsd
            ON ls.`refno` = lsd.`refno`
          LEFT JOIN care_encounter ce
            ON ls.`encounter_nr` = ce.`encounter_nr`
    	  ". $join . "
        WHERE ". $cond1 . $doc_cond ."
            AND ls.is_cash = '0'
            AND lsd.service_code LIKE '$service_code'
            AND (lsd.status = 'done' OR (lsd.status = 'pending' AND lsd.is_served = 1))
        UNION
        ALL
        SELECT DISTINCT
          ls.`serv_dt`,
          CASE
            WHEN ce.`encounter_type` = '2'
            THEN 'OP'
            WHEN ce.encounter_type = '8'
            THEN 'WALK-IN'
            ELSE IF(
              ce.current_ward_nr != '0',
              fn_get_ward_name (ce.current_ward_nr),
              IF(
                ce.current_dept_nr != '0',
                fn_get_department_name (ce.current_dept_nr),
                fn_get_department_name (ce.consulting_dept_nr)
              )
            )
          END AS p_type,
          ls.`pid`,
          fn_get_person_lastname_first (ls.`pid`) AS p_name,
          'LINGAP' AS or_type,
          IFNULL(le.amount, lsd.price_cash) AS price,
          ls.refno
        FROM
          seg_lab_serv ls
          INNER JOIN seg_lab_servdetails lsd
            ON ls.`refno` = lsd.`refno`
          LEFT JOIN care_encounter ce
            ON ls.`encounter_nr` = ce.`encounter_nr`
          LEFT JOIN seg_lingap_entries_laboratory le
            ON ls.refno = le.ref_no
          ". $join . "
        WHERE ". $cond1 . $doc_cond ."
            AND ((lsd.service_code LIKE '$service_code' AND lsd.request_flag = 'lingap') OR (le.service_code LIKE '$service_code'))
            AND (lsd.status = 'done' OR (lsd.status = 'pending' AND lsd.is_served = 1))
        UNION
        ALL
        SELECT DISTINCT
          ls.`serv_dt`,
          CASE
            WHEN ce.`encounter_type` = '2'
            THEN 'OP'
            WHEN ce.encounter_type = '8'
            THEN 'WALK-IN'
            ELSE IF(
              ce.current_ward_nr != '0',
              fn_get_ward_name (ce.current_ward_nr),
              IF(
                ce.current_dept_nr != '0',
                fn_get_department_name (ce.current_dept_nr),
                fn_get_department_name (ce.consulting_dept_nr)
              )
            )
          END AS p_type,
          ls.`pid`,
          fn_get_person_lastname_first (ls.`pid`) AS p_name,
          'CMAP' AS or_type,
          IFNULL(cm.amount, lsd.price_cash) AS price,
          ls.refno
        FROM
          seg_lab_serv ls
          INNER JOIN seg_lab_servdetails lsd
            ON ls.`refno` = lsd.`refno`
          LEFT JOIN care_encounter ce
            ON ls.`encounter_nr` = ce.`encounter_nr`
          LEFT JOIN seg_cmap_entries_laboratory cm
            ON ls.refno = cm.ref_no
          ". $join . "
        WHERE ". $cond1 . $doc_cond ."
            AND ((lsd.service_code LIKE '$service_code' AND lsd.request_flag = 'cmap') OR (cm.service_code LIKE '$service_code'))
            AND (lsd.status = 'done' OR (lsd.status = 'pending' AND lsd.is_served = 1)) " . $cond2;

#echo $sql; die;

$result = $db->Execute($sql);

$i = 0;

if($result){
    if($result->RecordCount() > 0){
        while($row = $result->FetchRow()){
            $data[$i] = array(
                'date_performed' => date('m/d/Y', strtotime($row['serv_dt'])),
                'p_type' => ($row['p_type']) ? $row['p_type'] : 'WALK-IN',
                'hrn' => $row['pid'],
                'name' => utf8_decode(trim(mb_strtoupper($row['p_name']))),
                'transact' => mb_strtoupper($row['or_type']),
                'amount' => (int) $row['price'],
                'mf' => (int) $row['price'] * $mf_disc,
                'pf' => (int) $row['price'] * $pf_disc,
            );

            $i++;
        }
    }
    else{
        $data[0] = array('date_performed' => 'No Data');
    }
}
else{
    $data[0] = array('date_performed' => 'No Data');
}

$params->put('prepared_by', mb_strtoupper($_SESSION['sess_user_name']));
$params->put('prepared_footer', $con_title . " Technician");

$noted = $personellObj->get_Signatory('spl_eeg_emg');

$params->put('noted_by', $noted['name']);
$params->put('noted_footer', $con_title . " " . $noted['signatory_position']);