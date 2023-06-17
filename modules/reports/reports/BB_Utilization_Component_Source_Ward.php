<?php
/*
 * Created: August 03, 2014 (VANESSA A. SAREN)
 * Modified: August 03, 2014 (VANESSA A. SAREN)
 * USE CROSSTAB in generating reports
 * JASPER REPORT TEMPLATES
*/

require_once('./roots.php');
require_once($root_path . 'include/inc_environment_global.php');

include('parameters.php');

#TITLE of the report
$params->put("hospital_name", mb_strtoupper($hosp_name));
$params->put("header", $report_title);
$params->put("department", 'Blood Bank');
$params->put("transaction", $transaction);

$cond = '';
if ($transaction == 'DEPOSITED')
    $cond = 'AND h.is_cash=0';

$sql = "SELECT
            IF (c.long_name IS NULL OR c.long_name='', 'Others', c.long_name) AS blood_component,
            IF (cg.name IS NULL OR cg.name='', 'Others', cg.name) AS component_group,
            IF (bs.long_name IS NULL OR bs.long_name='', 'Others', bs.long_name) AS blood_source,
            IF (bd.long_name IS NULL OR bd.long_name='Unspecified', 'Others', bd.long_name) AS ward_long,
            IF (bd.name IS NULL OR bd.name='Unspecified', 'Others', bd.name) AS ward_name,
            COUNT(*) AS tcount, bs.category_id
            FROM seg_blood_component c
            INNER JOIN seg_blood_received_details d ON d.component = c.id
            LEFT JOIN seg_blood_received_status s ON s.refno=d.refno AND s.service_code=d.service_code AND s.ordering=d.ordering
            INNER JOIN seg_lab_serv h ON h.refno=d.refno
            inner JOIN seg_blood_source bs ON bs.id = d.blood_source
            LEFT JOIN seg_blood_dept bd ON bd.id=d.dept
            LEFT JOIN seg_blood_component_group cg ON cg.id=c.component_group
            WHERE d.STATUS IN ('received') AND h.status NOT IN ('deleted','hidden','inactive','void')
            $cond
            AND (DATE($bb_based_date) BETWEEN " . $db->qstr($from_date_format) . " AND " . $db->qstr($to_date_format) . " )
            GROUP BY component_group, blood_component, blood_source, d.dept
            ORDER BY component_group, blood_component, category_id";
// die($sql);
$rs = $db->Execute($sql);
$data = array();
if (is_object($rs)) {
    if($rs->RecordCount()){
        while ($row = $rs->FetchRow()) {
            $data[] = array(
                'blood_component' => $row['blood_component'],
                'component_group' => $row['component_group'],
                'blood_source' => $row['blood_source'],
                'ward_long' => $row['ward_long'],
                'ward_name' => $row['ward_name'],
                'tcount' => (int)$row['tcount'],
                'category_id' => $row['category_id'],
            );
        }
    }
} else {
    $data[0]['blood_component'] = NULL;
}

//added by Nick 1-25-2015, temporary fix to show all data
$temp = $db->GetAll("SELECT
                          IF (a.long_name IS NULL OR a.long_name='', 'Others', a.long_name) AS blood_source,
                          IF (b.long_name IS NULL OR b.long_name='', 'Others', b.long_name) AS blood_component,
                          IF (c.name IS NULL OR c.name='Unspecified', 'Others', c.name) AS ward_name,
                          IF (c.long_name IS NULL OR c.long_name='Unspecified', 'Others', c.long_name) AS ward_long,
                          0 AS tcount,
                          (SELECT IF (d.name IS NULL OR d.name='', 'Others', d.name) FROM seg_blood_component_group AS d WHERE id=b.component_group) AS component_group,
                          a.category_id AS category_id
                    FROM
                      seg_blood_source AS a,
                      seg_blood_component AS b,
                      seg_blood_dept AS c;");

for($i = 0; $i < count($temp); $i++){
    $temp[$i]['tcount'] = intval($temp[$i]['tcount']);
}

$data = array_merge($data,$temp);
