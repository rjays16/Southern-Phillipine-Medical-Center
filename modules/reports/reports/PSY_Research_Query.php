<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

global $db;


#TITLE of the report
    $params->put("hospital_country", $hosp_country);
    $params->put("hospital_agency", $hosp_agency);
    $params->put("hospital_address", $hosp_addr1);
    $params->put("ipbm", IPBM_HEADER);
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $TitleBaseDate = "Based on " . (($base_date) ? ucfirst($base_date) : "Encounter") . " Date";
    $params->put("department", $TitleBaseDate);

// $params->put('date_span',"From " . date('F d, Y',$from_date) . " To " . date('F d, Y',$to_date));
    $params->put('p_type', $patient_type_label);



if(isset($icd_code)){


    $icd_sql = "SELECT description from care_icd10_en cie WHERE cie.diagnosis_code=" . $db->qstr($icd_code);
    $icd_desc = $db->GetRow($icd_sql);
    // var_dump($icd_desc);die();
    $params->put('code_type', $code_label . " - " . $icd_code . " ". $icd_desc['description'] ." (" . $icd_class . ")");
    // $params->put('code_type', $code_label . " - " . $icd_code ." (" . $icd_class . ")");
    }
else
    $params->put('code_type', $code_label . " (" . $icd_class . ")");



if($base_date=='encounter') {
    $cond = "DATE(ce.encounter_date)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
                    AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ") ";
}
else if ($base_date=='discharge'){
    $cond = "DATE(ce.discharge_date)
               BETWEEN
                    DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
                    AND
                    DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ") ";
}else{
    $cond = "DATE(ce.encounter_date)
             BETWEEN
                  DATE(" . $db->qstr(date('Y-m-d', $from_date)) . ")
                  AND
                  DATE(" . $db->qstr(date('Y-m-d', $to_date)) . ") ";
}
$cond2 = "ce.encounter_type IN (". $patient_type .")";
$cond3 = "ced.type_nr IN (". $type_nr .")";
$cond4 = "cp.type_nr IN (". $type_nr .")";

if(isset($icd_code)){
    if($code_type == 'icd'){
        $icd_cond = " AND ced.code = " . $db->qstr($icd_code);
    }
    else if($code_type == 'icp'){
        $icd_cond = " AND cp.code = " . $db->qstr($icd_code);
    }
}

//print_r($cond . " | " . $cond2 . " | " . $cond3); die;

$icd_sql = "SELECT DISTINCT
              ce.pid, ce.encounter_nr AS enc_nr, ce.discharge_date AS discharge_dt,
              IF(ce.admission_dt IS NULL, ce.encounter_date, ce.admission_dt) AS admission_dt,
              UPPER(fn_get_person_lastname_first (ce.pid)) AS p_name,
              IF(fn_calculate_age (ce.encounter_date,c.date_birth),fn_get_age (ce.encounter_date,c.date_birth),age) AS age,
              UPPER(c.sex) AS sex, fn_get_complete_address2 (ce.pid) AS address,
              IF(res.result_desc IS NULL, 'N/A', res.result_desc) AS result,
              cd.name_formal AS dept,
              UPPER(IF (ce.current_att_dr_nr,fn_get_personell_name (ce.current_att_dr_nr),fn_get_personell_name (ce.consulting_dr_nr))) AS doctor,
              ced.code
            FROM
              care_encounter AS ce
              INNER JOIN care_person AS c
                ON c.pid = ce.pid
              INNER JOIN care_encounter_diagnosis AS ced
                ON ce.encounter_nr = ced.encounter_nr
              INNER JOIN care_icd10_en AS ci
                ON ci.diagnosis_code = ced.code
              LEFT JOIN care_department AS cd
                ON cd.nr = ce.consulting_dept_nr
              LEFT JOIN
                (SELECT DISTINCT ser.encounter_nr, SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
                  FROM seg_encounter_result AS ser
                    INNER JOIN care_encounter AS em
                      ON em.encounter_nr = ser.encounter_nr
                  GROUP BY ser.encounter_nr
                  ORDER BY ser.encounter_nr
                ) AS sr
                ON sr.encounter_nr = ce.encounter_nr
              LEFT JOIN seg_results AS res
                ON res.result_code = sr.result_code
            WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
              AND cd.status NOT IN ('deleted','hidden','inactive','void')
              AND ced.status NOT IN ('deleted','hidden','inactive','void')
              AND ". $cond ."
              AND ". $cond2 ."
              AND ". $cond3 . $icd_cond;

$icp_sql = "SELECT DISTINCT
              ce.pid, ce.encounter_nr AS enc_nr, ce.discharge_date AS discharge_dt,
              IF(ce.admission_dt IS NULL, ce.encounter_date, ce.admission_dt) AS admission_dt,
              UPPER(fn_get_person_lastname_first (ce.pid)) AS p_name,
              IF(fn_calculate_age (ce.encounter_date,c.date_birth),fn_get_age (ce.encounter_date,c.date_birth),age) AS age,
              UPPER(c.sex) AS sex, fn_get_complete_address2 (ce.pid) AS address, res.result_desc AS result,
              cd.name_formal AS dept,
              UPPER(IF (ce.current_att_dr_nr,fn_get_personell_name (ce.current_att_dr_nr),fn_get_personell_name (ce.consulting_dr_nr))) AS doctor,
              cp.code
            FROM
              care_encounter AS ce
            INNER JOIN care_person AS c
              ON c.pid = ce.pid
            INNER JOIN care_encounter_procedure AS cp
              ON ce.encounter_nr = cp.encounter_nr
            INNER JOIN care_ops301_en AS co
              ON co.code = cp.code
            LEFT JOIN care_department AS cd
              ON cd.nr = ce.consulting_dept_nr
            LEFT JOIN
              (SELECT DISTINCT ser.encounter_nr, SUBSTRING(MAX(CONCAT(ser.modify_time,ser.result_code)),20,1) AS result_code
                FROM seg_encounter_result AS ser
                  INNER JOIN care_encounter AS em
                    ON em.encounter_nr = ser.encounter_nr
                GROUP BY ser.encounter_nr
                ORDER BY ser.encounter_nr
              ) AS sr
              ON sr.encounter_nr = ce.encounter_nr
            LEFT JOIN seg_results AS res
              ON res.result_code = sr.result_code
            WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
              AND cp.status NOT IN ('deleted','hidden','inactive','void')
              AND cd.status NOT IN ('deleted','hidden','inactive','void')
              AND ". $cond ."
              AND ". $cond2 ."
              AND ". $cond4 . $icd_cond;

if($code_type == 'icd'){
    $icd_sql .= " ORDER BY ce.admission_dt, c.name_last, c.name_first, c.name_middle";
    $rs = $db->Execute($icd_sql);
}
elseif($code_type == 'icp'){
    $icp_sql .= " ORDER BY ce.admission_dt, c.name_last, c.name_first, c.name_middle";
    $rs = $db->Execute($icp_sql);
}
elseif($code_type == 'all'){
    $all_sql = $icd_sql .
               " UNION ALL " .
               $icp_sql .
               " ORDER BY admission_dt, p_name";
               // die($all_sql);
    $rs = $db->Execute($all_sql);
}

if($rs){
    if($rs->RecordCount() > 0){

        $i = 0;

        while($row = $rs->FetchRow()){
            $data[$i] = array(
                'hrn' => $row['pid'],
                'admit_dt' => date('m/d/Y', strtotime($row['admission_dt'])),
                'discharge_dt' => date('m/d/Y', strtotime($row['discharge_dt'])),
                'name' => utf8_decode(trim($row['p_name'])),
                'age' => ($row['age']) ? $row['age'] : 'No DOB',
                'sex' => $row['sex'],
                'address' => utf8_decode(trim($row['address'])),
                'result' => $row['result'],
                'code' => $row['code'],
                // 'dept' => $row['dept'],
                'doctor' =>utf8_decode(trim( $row['doctor']))
            );

            $i++;
        }

        $params->put('total_cases', $i);

    }
    else{
        $data = array(
            array(
                'hrn' => 'No Data',
            )
        );
    }
}
else{
    $data = array(
        array(
            'hrn' => 'No Data',
        )
    );
}

$baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );

$params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put("ipbm_logo", $baseurl . "img/ipbm.png"); 

