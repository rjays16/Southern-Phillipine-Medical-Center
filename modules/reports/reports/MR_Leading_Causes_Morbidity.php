<?php
/**
 * @author Gervie 11/28/2015
 *
 * Top Leading Causes of Morbidity.
 */

require_once('roots.php');
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$params->put('title', 'Ten Leading Causes of Morbidity ('.$dept_label.')');
$params->put('date_span',"From " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));
$params->put('diagnosis', $icd_class . " Diagnosis (".$patient_type_label.')');

//$base_date = 'e.discharge_date';
$bdate_age = "(FLOOR(IF(fn_calculate_age(DATE(.".$date_based."),p.date_birth),(fn_get_ageyr(DATE(".$date_based."),p.date_birth)),p.age))";

$cond1 = "DATE(".$date_based.")
               BETWEEN DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

$cond2 = " ed.type_nr IN (".$type_nr.")";
$cond3 = " ed.encounter_type IN (".$patient_type.")";

$sql = "SELECT
          c.description AS descr,
          ed.code AS subcode,
          (SELECT
            IF(INSTR(ed.code, '.'), SUBSTRING(ed.code, 1, 3),
              IF(INSTR(ed.code, '/'), SUBSTRING(ed.code, 1, 5),
                IF(INSTR(ed.code, ','), SUBSTRING(ed.code, 1, 3),
                  IF(INSTR(ed.code, '-'), SUBSTRING(ed.code, 1, 3),
                    ed.code
                  )
                )
              )
            )) AS CODE,
          IF(t.description IS NOT NULL,
            t.description,
            (SELECT description FROM care_icd10_en ic
                WHERE ic.diagnosis_code = (SELECT IF(INSTR(ed.code, '.'), SUBSTRING(ed.code, 1, 3),
                                                    IF(INSTR(ed.code, '/'), SUBSTRING(ed.code, 1, 5),
                                                      IF(INSTR(ed.code, ','), SUBSTRING(ed.code, 1, 3),
                                                        IF(INSTR(ed.code, '-'), SUBSTRING(ed.code, 1, 3),
                                                          ed.code
                                                          )
                                                        )
                                                      )
                                                    )
                                            )
            )
          ) AS description,
          /*SUM(CASE WHEN (p.fromtemp = '1' AND p.sex = 'm') AND (fn_calculate_age (p.date_birth, p.death_date)) <= 0.076 THEN 1 ELSE 0 END) AS male_days_in,
          SUM(CASE WHEN (p.fromtemp = '1' AND p.sex = 'f') AND (fn_calculate_age (p.date_birth, p.death_date)) <= 0.076 THEN 1 ELSE 0 END) AS female_days_in,
          SUM(CASE WHEN (p.fromtemp = '0' AND p.sex = 'm') AND (fn_calculate_age (p.date_birth, p.death_date)) <= 0.076 THEN 1 ELSE 0 END) AS male_days_out,
          SUM(CASE WHEN (p.fromtemp = '0' AND p.sex = 'f') AND (fn_calculate_age (p.date_birth, p.death_date)) <= 0.076 THEN 1 ELSE 0 END) AS female_days_out,
          SUM(CASE WHEN p.sex = 'm' AND (fn_calculate_age (p.date_birth, p.death_date)) > 0.076 AND (fn_calculate_age (p.date_birth, p.death_date)) < 1 THEN 1 ELSE 0 END) AS m_1,
          SUM(CASE WHEN p.sex = 'f' AND (fn_calculate_age (p.date_birth, p.death_date)) > 0.076 AND (fn_calculate_age (p.date_birth, p.death_date)) < 1 THEN 1 ELSE 0 END) AS f_1,*/
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 0 AND 0.99) THEN 1 ELSE 0 END) AS m_1,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 0 AND 0.99) THEN 1 ELSE 0 END) AS f_1,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS m_1_4,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 1 AND 4) THEN 1 ELSE 0 END) AS f_1_4,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS m_5_9,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 5 AND 9) THEN 1 ELSE 0 END) AS f_5_9,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS m_10_14,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 10 AND 14) THEN 1 ELSE 0 END) AS f_10_14,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS m_15_19,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 15 AND 19) THEN 1 ELSE 0 END) AS f_15_19,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 20 AND 24) THEN 1 ELSE 0 END) AS m_20_24,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 20 AND 24) THEN 1 ELSE 0 END) AS f_20_24,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 25 AND 29) THEN 1 ELSE 0 END) AS m_25_29,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 25 AND 29) THEN 1 ELSE 0 END) AS f_25_29,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 30 AND 34) THEN 1 ELSE 0 END) AS m_30_34,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 30 AND 34) THEN 1 ELSE 0 END) AS f_30_34,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 35 AND 39) THEN 1 ELSE 0 END) AS m_35_39,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 35 AND 39) THEN 1 ELSE 0 END) AS f_35_39,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 40 AND 44) THEN 1 ELSE 0 END) AS m_40_44,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 40 AND 44) THEN 1 ELSE 0 END) AS f_40_44,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 45 AND 49) THEN 1 ELSE 0 END) AS m_45_49,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 45 AND 49) THEN 1 ELSE 0 END) AS f_45_49,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 50 AND 54) THEN 1 ELSE 0 END) AS m_50_54,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 50 AND 54) THEN 1 ELSE 0 END) AS f_50_54,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 55 AND 59) THEN 1 ELSE 0 END) AS m_55_59,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 55 AND 59) THEN 1 ELSE 0 END) AS f_55_59,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 60 AND 64) THEN 1 ELSE 0 END) AS m_60_64,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 60 AND 64) THEN 1 ELSE 0 END) AS f_60_64,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." BETWEEN 65 AND 69) THEN 1 ELSE 0 END) AS m_65_69,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." BETWEEN 65 AND 69) THEN 1 ELSE 0 END) AS f_65_69,
          SUM(CASE WHEN p.sex = 'm' AND ".$bdate_age." > 69) THEN 1 ELSE 0 END) AS m_70,
          SUM(CASE WHEN p.sex = 'f' AND ".$bdate_age." > 69) THEN 1 ELSE 0 END) AS f_70,
          SUM(CASE WHEN p.sex = 'm' THEN 1 ELSE 0 END) AS m_sub,
          SUM(CASE WHEN p.sex = 'f' THEN 1 ELSE 0 END) AS f_sub,
          SUM(CASE WHEN (p.sex = 'f' OR p.sex = 'm') THEN 1 ELSE 0 END) AS total,
          t.tabular_code AS tab_index
        FROM
          care_encounter_diagnosis AS ed
          INNER JOIN care_encounter AS e
            ON e.encounter_nr = ed.encounter_nr
          INNER JOIN care_icd10_en AS c
            ON c.diagnosis_code = (SELECT IF(INSTR(ed.code, '.'), SUBSTRING(ed.code, 1, 3),
                                            IF(INSTR(ed.code, '/'), SUBSTRING(ed.code, 1, 5),
                                                IF(INSTR(ed.code, ','), SUBSTRING(ed.code, 1, 3),
                                                    IF(INSTR(ed.code, '-'), SUBSTRING(ed.code, 1, 3),
                                                        ed.code)
                                                    )
                                                )
                                            )
                                    )
          INNER JOIN care_person AS p
            ON p.pid = e.pid
          LEFT JOIN seg_icd_10_latest_tabular t
            ON t.diagnosis_code = (SELECT IF(INSTR(ed.code, '.'), SUBSTRING(ed.code, 1, 3),
                                            IF(INSTR(ed.code, '/'), SUBSTRING(ed.code, 1, 5),
                                                IF(INSTR(ed.code, ','), SUBSTRING(ed.code, 1, 3),
                                                    IF(INSTR(ed.code, '-'), SUBSTRING(ed.code, 1, 3),
                                                        ed.code)
                                                    )
                                                )
                                            )
                                    )
        WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
          AND ed.STATUS NOT IN ('deleted','hidden','inactive','void')
          AND ".$cond1."
          AND ".$cond2."
          AND ".$cond3. $enc_dept_cond."
          AND IF(INSTR(c.diagnosis_code, '.'),SUBSTR(c.diagnosis_code,1,
                IF(INSTR(c.diagnosis_code, '.'),INSTR(c.diagnosis_code, '.') - 1,0)),
                    c.diagnosis_code) REGEXP '^[[:alpha:]][[:digit:]]'
        GROUP BY IF(t.tabular_code IS NOT NULL, t.tabular_code,
            (SELECT IF(INSTR(ed.code, '.'), SUBSTRING(ed.code, 1, 3),
                        IF(INSTR(ed.code, '/'), SUBSTRING(ed.code, 1, 5),
                            IF(INSTR(ed.code, ','), SUBSTRING(ed.code, 1, 3),
                                IF(INSTR(ed.code, '-'), SUBSTRING(ed.code, 1, 3),
                                    ed.code)
                                )
                            )
                        )
                     )
            )
        ORDER BY COUNT(*) DESC";

$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){
            $data[$i] = array(
                'desc' => $row['description'],
                'm_1' => $row['m_1'],
                'f_1' => $row['f_1'],
                'm_1_4' => $row['m_1_4'],
                'f_1_4' => $row['f_1_4'],
                'm_5_9' => $row['m_5_9'],
                'f_5_9' => $row['f_5_9'],
                'm_10_14' => $row['m_10_14'],
                'f_10_14' => $row['f_10_14'],
                'm_15_19' => $row['m_15_19'],
                'f_15_19' => $row['f_15_19'],
                'm_20_24' => $row['m_20_24'],
                'f_20_24' => $row['f_20_24'],
                'm_25_29' => $row['m_25_29'],
                'f_25_29' => $row['f_25_29'],
                'm_30_34' => $row['m_30_34'],
                'f_30_34' => $row['f_30_34'],
                'm_35_39' => $row['m_35_39'],
                'f_35_39' => $row['f_35_39'],
                'm_40_44' => $row['m_40_44'],
                'f_40_44' => $row['f_40_44'],
                'm_45_49' => $row['m_45_49'],
                'f_45_49' => $row['f_45_49'],
                'm_50_54' => $row['m_50_54'],
                'f_50_54' => $row['f_50_54'],
                'm_55_59' => $row['m_55_59'],
                'f_55_59' => $row['f_55_59'],
                'm_60_64' => $row['m_60_64'],
                'f_60_64' => $row['f_60_64'],
                'm_65_69' => $row['m_65_69'],
                'f_65_69' => $row['f_65_69'],
                'm_70' => $row['m_70'],
                'f_70' => $row['f_70'],
                'm_sub' => $row['m_sub'],
                'f_sub' => $row['f_sub'],
                'total' => $row['total'],
                'icd_tab' => $row['tab_index']
            );

            $i++;
        }
    }
    else{
        $data = array(
            array(
                'desc' => 'No Data Available.'
            )
        );
    }
}
else{
    $data = array(
        array(
            'desc' => 'No Data Available.'
        )
    );
}