<?php
/**
 * Created by PhpStorm.
 * User: JC
 * Date: 11/13/2018
 * Time: 3:49 PM
 */

require_once('roots.php');
require_once $root_path . 'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$from = date('Y-m-d', $from_date);
$to = date('Y-m-d', $to_date);

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);
$spmc_logo = $baseurl . 'gui/img/logos/dmc_logo.jpg';
$doh_logo = $baseurl . 'img/doh.jpg';

$userNote = userNote($db);
$person = personell($db);

$params->put('hosp_name', $hosp_name);
$params->put('address', $hosp_addr1);
$params->put('title', $report_title);
$params->put('generate_system', "[ eClaims ]");
$params->put('date_span', strtoupper(date('F', $from_date)) . " TO " . strtoupper(date('F', $to_date)) . " " . strtoupper(date('Y', $to_date)));
$params->put("spmc_logo", $spmc_logo);
$params->put("doh_logo", $doh_logo);
$params->put("user_note", $userNote['name'] . ' ' . $userNote['title']);
$params->put("user_note_position", $userNote['signatory_position']);
$params->put("user_prepared", $person['name_last'] . ', ' . $person['name_first'] . ' ' . ($person['name_middle'] ? substr($person['name_middle'], 0, 1) . '.' : ''));
$params->put("user_prepared_position", $person['jobtitle']);

$sql = "SELECT 
          sm.`memcategory_arr` AS category_id,
          sm.`memcategory_desc` AS category_name,
          MONTHNAME(t.`transmit_dte`) AS `month_name`,
          MONTH(t.`transmit_dte`) AS `month`,
          COUNT(*) AS tcount 
        FROM
          seg_memcategory sm 
          LEFT JOIN seg_encounter_insurance_memberinfo seim 
            ON seim.`member_type` = sm.`memcategory_code` 
          LEFT JOIN seg_eclaims_claim sec 
            ON sec.`encounter_nr` = seim.`encounter_nr` 
          LEFT JOIN seg_transmittal t 
            ON t.transmit_no = sec.transmit_no 
          LEFT JOIN seg_eclaims_transmittal_ext sete 
            ON sete.transmit_no = sec.transmit_no 
        WHERE sec.`claim_series_lhio` IS NOT NULL 
          AND sete.`is_mapped` = 1 
          AND (MONTH(t.`transmit_dte`) BETWEEN MONTH('$from') AND MONTH('$to'))
          AND (YEAR(t.`transmit_dte` ) BETWEEN YEAR('$from') AND YEAR('$to'))
        GROUP BY category_name,
          month_name 
        UNION
        SELECT 
          sm1.`memcategory_arr`,
          sm1.`memcategory_desc`,
          MONTHNAME(t1.`transmit_dte`),
          MONTH(t1.`transmit_dte`),
          0 AS tcount1 
        FROM
          seg_transmittal t1 
          JOIN seg_memcategory sm1 
        ORDER BY category_name,
          `month`";

$data = array();

foreach ($db->GetAll($sql) as $key => $datum) {
    $array = array(
        'category_name' => $datum['category_name'],
        'category_id' => (int)$datum['category_id'],
        'month_name' => $datum['month_name'],
        'tcount' => (int)$datum['tcount'],
    );
    array_push($data, $array);
}

function userNote($db)
{
    $sql = "SELECT 
              ss.`signatory_position`,
              ss.`signatory_title`,
              ss.`title`,
              CONCAT(p.`name_last`, ', ', p.name_first, ' ', SUBSTRING(p.`name_middle`, 1, 1), '.') AS `name`
            FROM
              seg_signatory ss 
              LEFT JOIN `care_personell` cp 
                ON cp.`nr` = ss.`personell_nr` 
              LEFT JOIN care_person p 
                ON p.`pid` = cp.`pid` 
            WHERE ss.`signatory_position` = 'Billing Section Incharge' LIMIT 1";
    $result = $db->execute($sql);
    return $result->FetchRow();
}

function personell($db)
{
    $sql = "SELECT 
              cu.login_id AS username,
              cp.job_function_title AS jobtitle,
              p.`name_last`,
              p.`name_first`,
              p.`name_middle`
            FROM
              care_person p
              LEFT JOIN care_personell cp 
                ON cp.pid = p.`pid` 
              LEFT JOIN care_users cu 
                ON cu.personell_nr = cp.nr 
            WHERE cp.`nr` = " . $_SESSION['sess_user_personell_nr'] . "
            LIMIT 1 ";

    $result = $db->execute($sql);

    return $result->FetchRow();
}