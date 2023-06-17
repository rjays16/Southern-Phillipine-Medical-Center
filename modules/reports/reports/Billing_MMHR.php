<?php
/**
 * @author Gervie 02/06/2016
 *
 * Computerized MMHR
 */


require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db, $HTTP_SESSION_VARS;

$params->put('date_span',"From " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));

$cond1 = "DATE(ce01.discharge_date)
           BETWEEN
                DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                AND
                DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

$cond2 = "DATE(ce02.discharge_date)
           BETWEEN
                DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                AND
                DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

$cond3 = "DATE(ce03.encounter_date)
           BETWEEN
                DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                AND
                DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

$cond4 = "DATE(ce04.encounter_date)
           BETWEEN
                DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                AND
                DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

$sql = "SELECT ref_date, SUM(d_nhip_count) d_nhip, SUM(d_nonnhip_count) d_nonnhip, SUM(a_nhip_count) a_nhip, SUM(a_nonnhip_count) a_nonnhip
        FROM (
        SELECT DATE(ce01.discharge_date) ref_date, COUNT(*) d_nhip_count, 0 d_nonnhip_count, 0 a_nhip_count, 0 a_nonnhip_count
            FROM care_encounter ce01 INNER JOIN seg_encounter_insurance sei
            ON ce01.encounter_nr = sei.encounter_nr
            WHERE hcare_id = 18 AND priority = 1
               AND ". $cond1 ."
               AND NOT UPPER(TRIM(ce01.encounter_status)) IN ('CANCELLED','DELETED')
               AND ce01.encounter_type IN (3,4)
        GROUP BY DATE(ce01.discharge_date)
        UNION ALL
        SELECT DATE(ce02.discharge_date) ref_date, 0 d_nhip_count, COUNT(*) d_nonnhip_count, 0 a_nhip_count, 0 a_nonnhip_count
            FROM care_encounter ce02 LEFT JOIN seg_encounter_insurance sei
            ON ce02.encounter_nr = sei.encounter_nr
            WHERE (NOT (hcare_id = 18 AND priority = 1) OR sei.encounter_nr IS NULL)
               AND ". $cond2 ."
               AND NOT UPPER(TRIM(ce02.encounter_status)) IN ('CANCELLED','DELETED')
               AND ce02.encounter_type IN (3,4)
        GROUP BY DATE(ce02.discharge_date)
        UNION ALL
        SELECT DATE(ce03.encounter_date) ref_date, 0 d_nhip_count, 0 d_nonnhip_count, COUNT(*) a_nhip_count, 0 a_nonnhip_count
            FROM care_encounter ce03 INNER JOIN seg_encounter_insurance sei
            ON ce03.encounter_nr = sei.encounter_nr
            WHERE hcare_id = 18 AND priority = 1
               AND ". $cond3 ."
               AND NOT UPPER(TRIM(ce03.encounter_status)) IN ('CANCELLED','DELETED')
               AND ce03.encounter_type IN (3,4)
        GROUP BY DATE(ce03.encounter_date)
        UNION ALL
        SELECT DATE(ce04.encounter_date) ref_date, 0 d_nhip_count, 0 d_nonnhip_count, 0 a_nhip_count, COUNT(*) a_nonnhip_count
            FROM care_encounter ce04 LEFT JOIN seg_encounter_insurance sei
            ON ce04.encounter_nr = sei.encounter_nr
            WHERE (NOT (hcare_id = 18 AND priority = 1) OR sei.encounter_nr IS NULL)
               AND ". $cond4 ."
               AND NOT UPPER(TRIM(ce04.encounter_status)) IN ('CANCELLED','DELETED')
               AND ce04.encounter_type IN (3,4)
        GROUP BY DATE(ce04.encounter_date)) t GROUP BY ref_date
        ORDER BY ref_date";

$res = $db->Execute($sql);
$i = 0;

if($res)
{
    if($res->RecordCount() > 0)
    {
        while($row = $res->FetchRow())
        {
            $data[$i] = array(
                'd_ref_date'    => date('F d, Y', strtotime($row['ref_date'])),
                'd_nhip'        => (int) $row['d_nhip'],
                'd_nnhip'       => (int) $row['d_nonnhip'],
                'a_nhip'        => (int) $row['a_nhip'],
                'a_nnhip'       => (int) $row['a_nonnhip']
            );
            $i++;
        }
    }
    else
    {
        $data = array(
            array(
                'd_ref_data' => 'No Data'
            )
        );
    }
}
else
{
    $data = array(
        array(
            'd_ref_data' => 'No Data'
        )
    );
}