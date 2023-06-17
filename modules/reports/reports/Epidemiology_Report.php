<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    define("ULTRASOUND_DEPT", 165);
    define("OB_ULTRASOUND_DEPT", 209);
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $sub_caption);
        
    //$patient_type = '3,4';
    $date_based = 'ce.encounter_date';
$cond = "DATE(ce.encounter_date)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
$sql = "SELECT 
  DATE AS DATE,
  Hospital_Num AS Hospital_Num,
  encounter AS encounter,
  t.CATEGORY AS CATEGORY,
  cats.description AS CATDESC,
  cats.category AS CATEGORYNUM,
  categ.description AS CATEGORYROMANNUM,
  PHILHEALTH AS PHILHEALTH,
  NAME_PATIENT AS NAME_PATIENT,
  Gender AS Gender,
  Age AS Age,
  dependent_pid AS dependent_pid,
  SUM(CBC) AS CBC,
  SUM(TOTAL_CBC) AS TOTAL_CBC,
  SUM(URNALYSIS) AS URNALYSIS,
  SUM(TOTAL_URNALYSIS) AS TOTAL_URNALYSIS,
  SUM(FBS) AS FBS,
  SUM(TOTAL_FBS) AS TOTAL_FBS,
  SUM(SUA) AS SUA,
  SUM(TOTAL_SUA) AS TOTAL_SUA,
  SUM(CREA) AS CREA,
  SUM(TOTAL_CREA) AS TOTAL_CREA,
  SUM(TCHOLE) AS TCHOLE,
  SUM(TOTAL_TCHOLE) AS TOTAL_TCHOLE,
  SUM(SGPT) AS SGPT,
  SUM(TOTAL_SGPT) AS TOTAL_SGPT,
  SUM(NA) AS NA,
  SUM(TOTAL_NA) AS TOTAL_NA,
  SUM(K) AS K,
  SUM(TOTAL_K) AS TOTAL_K,
  SUM(LIPID_PROFILE) AS LIPID_PROFILE,
  SUM(LIPID_PROFILE_TOTAL) AS LIPID_PROFILE_TOTAL,
  SUM(TSH) AS TSH,
  SUM(TOTAL_TSH) AS TOTAL_TSH,
  SUM(FT4) AS FT4,
  SUM(TOTAL_FT4) AS TOTAL_FT4,
  SUM(HBSAG) AS HBSAG,
  SUM(TOTAL_HBSAG) AS TOTAL_HBSAG,
  SUM(A_HBS) AS A_HBS,
  SUM(TOTAL_A_HBS) AS TOTAL_A_HBS,
  SUM(ECG) AS ECG,
  SUM(TOTAL_ECG) AS TOTAL_ECG,
  SUM(PSA) AS PSA,
  SUM(TOTAL_PSA) AS TOTAL_PSA,
  SUM(PROTIME) AS PROTIME,
  SUM(TOTAL_PROTIME) AS TOTAL_PROTIME,
  SUM(APTT) AS APTT,
  SUM(TOTAL_APTT) AS TOTAL_APTT,
  SUM(Others_lab) AS Others_lab,
  SUM(TOTAL_Others_lab) AS TOTAL_Others_lab,
  SUM(CHEST_xray) AS CHEST_xray,
  SUM(TOTAL_CHEST_xray) AS TOTAL_CHEST_xray,
  SUM(LS_Xray) AS LS_Xray,
  SUM(TOTAL_LS_Xray) AS TOTAL_LS_Xray,
  SUM(OtherUltrasound) AS OtherUltrasound,
  SUM(TOTAL_OtherUltrasound) AS TOTAL_OtherUltrasound,
  SUM(Others_xray) AS Others_xray,
  SUM(TOTAL_Others_xray) AS TOTAL_Others_xray,
  SUM(
    TOTAL_Others_lab + TOTAL_APTT + TOTAL_PROTIME + TOTAL_PSA + TOTAL_ECG + TOTAL_A_HBS + TOTAL_HBSAG + TOTAL_TSH + LIPID_PROFILE_TOTAL + TOTAL_K + TOTAL_NA + TOTAL_TCHOLE + TOTAL_CREA + TOTAL_SUA + TOTAL_FBS + TOTAL_URNALYSIS + TOTAL_CBC + TOTAL_Others_xray + TOTAL_OtherUltrasound + TOTAL_LS_Xray + TOTAL_CHEST_xray
  ) AS total_availment 
FROM
  (SELECT 
    ce.encounter_date AS DATE,
    ce.`pid` AS Hospital_Num,
    ce.`encounter_nr` AS encounter,
    IF(
      TRIM(cpi.`insurance_nr`) IS NULL,
      'NO PHIC',
      TRIM(cpi.`insurance_nr`)
    ) AS PHILHEALTH,
    cpl.category AS CATEGORY,
    `fn_get_person_name` (ce.`pid`) AS NAME_PATIENT,
    `fn_get_gender` (ce.`pid`) AS Gender,
    `fn_get_age` (
      NOW(),
      (SELECT 
        cp.`date_birth` 
      FROM
        care_person cp 
      WHERE cp.pid = ce.`pid`)
    ) AS Age,
    IF(
      sd.`dependent_pid` IS NULL,
      'PHS',
      'DEPENDENT'
    ) AS dependent_pid,
    SUM(
      CASE
        WHEN slsd.service_code LIKE '%CBC%' 
        THEN 1 
        ELSE 0 
      END
    ) AS CBC,
    SUM(
      CASE
        WHEN slsd.service_code LIKE '%CBC%' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_CBC,
    SUM(
      CASE
        WHEN slsd.service_code = 'URINE' 
        THEN 1 
        ELSE 0 
      END
    ) AS URNALYSIS,
    SUM(
      CASE
        WHEN slsd.service_code = 'URINE' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_URNALYSIS,
    SUM(
      CASE
        WHEN slsd.service_code = 'GLUFBS' 
        THEN 1 
        ELSE 0 
      END
    ) AS FBS,
    SUM(
      CASE
        WHEN slsd.service_code = 'GLUFBS' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_FBS,
    SUM(
      CASE
        WHEN slsd.service_code = 'SUA' 
        THEN 1 
        ELSE 0 
      END
    ) AS SUA,
    SUM(
      CASE
        WHEN slsd.service_code = 'SUA' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_SUA,
    SUM(
      CASE
        WHEN slsd.service_code = 'CREA' 
        THEN 1 
        ELSE 0 
      END
    ) AS CREA,
    SUM(
      CASE
        WHEN slsd.service_code = 'CREA' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_CREA,
    SUM(
      CASE
        WHEN slsd.service_code = 'CHOL' 
        THEN 1 
        ELSE 0 
      END
    ) AS TCHOLE,
    SUM(
      CASE
        WHEN slsd.service_code = 'CHOL' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_TCHOLE,
    SUM(
      CASE
        WHEN slsd.service_code = 'SGPT' 
        THEN 1 
        ELSE 0 
      END
    ) AS SGPT,
    SUM(
      CASE
        WHEN slsd.service_code = 'SGPT' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_SGPT,
    SUM(
      CASE
        WHEN slsd.service_code = 'NA' 
        THEN 1 
        ELSE 0 
      END
    ) AS NA,
    SUM(
      CASE
        WHEN slsd.service_code = 'NA' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_NA,
    SUM(
      CASE
        WHEN slsd.service_code = 'K+' 
        THEN 1 
        ELSE 0 
      END
    ) AS K,
    SUM(
      CASE
        WHEN slsd.service_code = 'K+' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_K,
    SUM(
      CASE
        WHEN slsd.service_code = 'LIPID' 
        THEN 1 
        ELSE 0 
      END
    ) AS LIPID_PROFILE,
    SUM(
      CASE
        WHEN slsd.service_code = 'LIPID' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS LIPID_PROFILE_TOTAL,
    SUM(
      CASE
        WHEN slsd.service_code = 'TSH' 
        THEN 1 
        ELSE 0 
      END
    ) AS TSH,
    SUM(
      CASE
        WHEN slsd.service_code = 'TSH' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_TSH,
    SUM(
      CASE
        WHEN slsd.service_code = 'IFT4' 
        THEN 1 
        ELSE 0 
      END
    ) AS FT4,
    SUM(
      CASE
        WHEN slsd.service_code = 'IFT4' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_FT4,
    SUM(
      CASE
        WHEN slsd.service_code = 'HBsAg'
        THEN 1 
        ELSE 0 
      END
    ) AS HBSAG,
    SUM(
      CASE
        WHEN slsd.service_code = 'HBsAg'
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_HBSAG,
    SUM(
      CASE
        WHEN slsd.service_code = 'A-HBs' 
        THEN 1 
        ELSE 0 
      END
    ) AS A_HBS,
    SUM(
      CASE
        WHEN slsd.service_code = 'A-HBs' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_A_HBS,
    SUM(
      CASE
        WHEN slsd.service_code = 'ECG' 
        THEN 1 
        ELSE 0 
      END
    ) AS ECG,
    SUM(
      CASE
        WHEN slsd.service_code = 'ECG' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_ECG,
    SUM(
      CASE
        WHEN slsd.service_code = 'PSA' 
        THEN 1 
        ELSE 0 
      END
    ) AS PSA,
    SUM(
      CASE
        WHEN slsd.service_code = 'PSA' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_PSA,
    SUM(
      CASE
        WHEN slsd.service_code = 'PT' 
        THEN 1 
        ELSE 0 
      END
    ) AS PROTIME,
    SUM(
      CASE
        WHEN slsd.service_code = 'PT' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_PROTIME,
    SUM(
      CASE
        WHEN slsd.service_code = 'APTTT' 
        THEN 1 
        ELSE 0 
      END
    ) AS APTT,
    SUM(
      CASE
        WHEN slsd.service_code = 'APTTT' 
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_APTT,
    SUM(
      CASE
        WHEN (slsd.service_code NOT IN ('URINE', 'GLUFBS', 'SUA', 'CREA', 'CHOL', 'NA', 'K+', 'LIPID',
                  'TSH', 'IFT4', 'HBsAg', 'A-HBs', 'ECG', 'PSA', 'PT', 'APTTT')
                  AND slsd.service_code NOT LIKE '%CBC%')

        THEN 1 
        ELSE 0 
      END
    ) AS Others_lab,
    SUM(
      CASE
        WHEN (slsd.service_code NOT IN ('URINE', 'GLUFBS', 'SUA', 'CREA', 'CHOL', 'NA', 'K+', 'LIPID',
                  'TSH', 'IFT4', 'HBsAg', 'A-HBs', 'ECG', 'PSA', 'PT', 'APTTT')
                  AND slsd.service_code NOT LIKE '%CBC%')
        THEN (
          IF((slsd.price_charge=slsd.price_cash),slsd.price_charge,(slsd.price_charge-slsd.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_Others_lab,
    0 AS CHEST_xray,
    0 AS TOTAL_CHEST_xray,
    0 AS LS_Xray,
    0 AS TOTAL_LS_Xray,
    0 AS OtherUltrasound,
    0 AS TOTAL_OtherUltrasound,
    0 AS Others_xray,
    0 AS TOTAL_Others_xray,
    sls.refno as REFER_NO
  FROM
    care_encounter AS ce 
    INNER JOIN `care_personell` AS cpl 
      ON cpl.pid = ce.`pid` 
    LEFT JOIN `seg_dependents` AS sd 
      ON sd.dependent_pid = ce.`pid` 
    INNER JOIN `seg_lab_serv` AS sls 
      ON sls.`encounter_nr` = ce.`encounter_nr` 
    INNER JOIN `seg_lab_servdetails` AS slsd 
      ON sls.`refno` = slsd.`refno` 
    LEFT JOIN `care_person_insurance` AS cpi 
      ON cpi.`pid` = ce.`pid` 
      AND cpi.`hcare_id` = '18' 
    LEFT JOIN seg_lab_services slsvs 
      ON slsd.`service_code` = slsvs.`service_code` 
  WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
            AND sls.status NOT IN ('deleted','hidden','inactive','void')
            AND cpl.status NOT IN ('deleted','hidden','inactive','void')
            AND slsd.is_served = '1'
            AND ". $cond ."
          GROUP BY ce.encounter_nr
  UNION
  ALL 
  SELECT 
    ce.encounter_date AS DATE,
    ce.`pid` AS Hospital_Num,
    ce.`encounter_nr` AS encounter,
    cpl.category AS CATEGORY,
    IF(
      TRIM(cpi.`insurance_nr`) IS NULL,
      'NO PHIC',
      TRIM(cpi.`insurance_nr`)
    ) AS PHILHEALTH,
    `fn_get_person_name` (ce.`pid`) AS NAME_PATIENT,
    `fn_get_gender` (ce.`pid`) AS Gender,
    `fn_get_age` (
      NOW(),
      (SELECT 
        cp.`date_birth` 
      FROM
        care_person cp 
      WHERE cp.pid = ce.`pid`)
    ) AS Age,
    IF(
      sd.`dependent_pid` IS NULL,
      'PHS',
      'DEPENDENT'
    ) AS dependent_pid,
    0 AS CBC,
    0 AS URNALYSIS,
    0 AS FBS,
    0 AS SUA,
    0 AS CREA,
    0 AS CHOL,
    0 AS SGPT,
    0 AS NA,
    0 AS K,
    0 AS LIPID_PROFILE,
    0 AS TSH,
    0 AS FT4,
    0 AS HBSAG,
    0 AS A_HBS,
    0 AS ECG,
    0 AS PSA,
    0 AS PROTIME,
    0 AS APTT,
    0 AS Others_lab,
    0 AS TOTAL_CBC,
    0 AS TOTAL_URNALYSIS,
    0 AS TOTAL_FBS,
    0 AS TOTAL_SUA,
    0 AS TOTAL_CREA,
    0 AS TOTAL_TCHOLE,
    0 AS TOTAL_SGPT,
    0 AS TOTAL_NA,
    0 AS TOTAL_K,
    0 AS LIPID_PROFILE_TOTAL,
    0 AS TOTAL_TSH,
    0 AS TOTAL_FT4,
    0 AS TOTAL_HBSAG,
    0 AS TOTAL_A_HBS,
    0 AS TOTAL_ECG,
    0 AS TOTAL_PSA,
    0 AS TOTAL_PROTIME,
    0 AS TOTAL_APTT,
    0 AS TOTAL_Others_lab,
    SUM(
      CASE
        WHEN ctrr.service_code = 'CPA'
        THEN 1 
        ELSE 0 
      END
    ) AS CHEST_xray,
    SUM(
      CASE
        WHEN ctrr.service_code = 'CPA'
        THEN (
          IF((ctrr.price_charge=ctrr.price_cash),ctrr.price_charge,(ctrr.price_charge-ctrr.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_CHEST_xray,
    SUM(
      CASE
        WHEN ctrr.service_code = 'LSFLEX'
        THEN 1 
        ELSE 0 
      END
    ) AS LS_Xray,
    SUM(
      CASE
        WHEN ctrr.service_code = 'LSFLEX'
        THEN (
          IF((ctrr.price_charge=ctrr.price_cash),ctrr.price_charge,(ctrr.price_charge-ctrr.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_LS_Xray,
    SUM(
      CASE
        WHEN (ctrr.service_code LIKE '%USD%' OR srsg.department_nr IN (".$db->qstr(ULTRASOUND_DEPT).",".$db->qstr(OB_ULTRASOUND_DEPT)."))
        THEN 1 
        ELSE 0 
      END
    ) AS OtherUltrasound,
    SUM(
      CASE
        WHEN (ctrr.service_code LIKE '%USD%' OR srsg.department_nr IN (".$db->qstr(ULTRASOUND_DEPT).",".$db->qstr(OB_ULTRASOUND_DEPT)."))
        THEN (
          IF((ctrr.price_charge=ctrr.price_cash),ctrr.price_charge,(ctrr.price_charge-ctrr.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_OtherUltrasound,
    SUM(
      CASE
        WHEN (ctrr.service_code NOT IN ('CPA', 'LSFLEX') AND ctrr.service_code NOT LIKE '%USD%' AND srsg.department_nr NOT IN (".$db->qstr(ULTRASOUND_DEPT).",".$db->qstr(OB_ULTRASOUND_DEPT)."))
        THEN 1 
        ELSE 0 
      END
    ) AS Others_xray,
    SUM(
      CASE
        WHEN (ctrr.service_code NOT IN ('CPA', 'LSFLEX') AND ctrr.service_code NOT LIKE '%USD%' AND srsg.department_nr NOT IN (".$db->qstr(ULTRASOUND_DEPT).",".$db->qstr(OB_ULTRASOUND_DEPT)."))
        THEN (
          IF((ctrr.price_charge=ctrr.price_cash),ctrr.price_charge,(ctrr.price_charge-ctrr.price_cash))
        ) 
        ELSE 0 
      END
    ) AS TOTAL_Others_xray,
    srs.refno as REFER_NO
  FROM
    care_encounter AS ce 
    INNER JOIN `care_personell` AS cpl 
      ON cpl.pid = ce.`pid` 
    LEFT JOIN `seg_dependents` AS sd 
      ON sd.dependent_pid = ce.`pid` 
    INNER JOIN `seg_radio_serv` AS srs 
      ON srs.`encounter_nr` = ce.`encounter_nr` 
    INNER JOIN care_test_request_radio AS ctrr 
      ON ctrr.`refno` = srs.`refno` 
    LEFT JOIN care_person_insurance AS cpi 
      ON cpi.`pid` = ce.`pid` 
      AND cpi.`hcare_id` = '18' 
    LEFT JOIN seg_radio_services AS srsvs 
      ON ctrr.service_code = srsvs.service_code 
    LEFT JOIN seg_radio_service_groups AS srsg
      ON srsvs.group_code = srsg.group_code 
  WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
            AND ctrr.status NOT IN ('deleted','hidden','inactive','void')
            AND cpl.status NOT IN ('deleted','hidden','inactive','void')
            AND ctrr.is_served = '1'
            AND ". $cond ."
          GROUP BY ce.encounter_nr) AS t 
  LEFT JOIN seg_phs_job_status AS cats 
    ON t.CATEGORY = cats.id 
  LEFT JOIN seg_phs_category AS categ 
    ON cats.category = categ.id 
  GROUP BY t.encounter
  ORDER BY t.DATE ";
   
    // var_dump($sql);
    // die($sql);
    $rs = $db->Execute($sql);
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if ($rs->RecordCount() > 0){ // edited by: syboy 07/11/2015
        while($row=$rs->FetchRow()){     

            
            $data[$rowindex] = array('rowindex' => $rowindex+1,
                              'DATE' => date("m/d/Y",strtotime($row['DATE'])),
                              'Hospital_Num' => $row['Hospital_Num'],
                              'PHILHEALTH' => $row['PHILHEALTH'],
                              'NAME PATIENT' => utf8_decode(trim($row['NAME_PATIENT'])),
                              'Gender' => $row['Gender'],
                              'Age' => $row['Age'],
                              'Dependent' => $row['dependent_pid'],
                              'CBC' => (int) $row['CBC'],
                              'total_cbc' => (float) $row['TOTAL_CBC'],
                              'URNALYSIS' => (int) $row['URNALYSIS'],
                              'total_urinalysis' => (float) $row['TOTAL_URNALYSIS'],
                              'FBS' => (int) $row['FBS'],
                              'total_fbs' => (float) $row['TOTAL_FBS'],
                              'SUA' => (int) $row['SUA'],
                              'total_sua' => (float) $row['TOTAL_SUA'],
                              'CREA' => (int) $row['CREA'],
                              'total_crea' => (float) $row['TOTAL_CREA'],
                              'TCHOLE' => (int) $row['TCHOLE'],
                              'total_tchole' => (float) $row['TOTAL_TCHOLE'],
                              'SGPT' => (int) $row['SGPT'],
                              'total_sgpt' => (float) $row['TOTAL_SGPT'],
                              'NA' => (int) $row['NA'],
                              'total_na' => (float) $row['TOTAL_NA'],
                              'K' => (int) $row['K'],
                              'total_k' => (float) $row['TOTAL_K'],
                              'LIPID_PROFILE' => (int) $row['LIPID_PROFILE'],
                              'total_lipid_prof' => (float) $row['LIPID_PROFILE_TOTAL'],
                              'TSH' => (int) $row['TSH'],
                              'total_tsh' => (float) $row['TOTAL_TSH'],
                              'FT4' => (int) $row['FT4'],
                              'total_ft4' => (float) $row['TOTAL_FT4'],
                              'HBSAG' => (int) $row['HBSAG'],
                              'total_hbsag' => (float) $row['TOTAL_HBSAG'],
                              'A_HBS' => (int) $row['A_HBS'],
                              'total_ahbs' => (float) $row['TOTAL_A_HBS'],
                              'CHEST_xray' => (int) $row['CHEST_xray'],
                              'total_chest_xray' => (float) $row['TOTAL_CHEST_xray'],
                              'LS_Xray' => (int) $row['LS_Xray'],
                              'total_ls_xray' => (float) $row['TOTAL_LS_Xray'],
                              'Ultra_Sound' => (int) $row['OtherUltrasound'],
                              'total_other_ultra_sound' => (float) $row['TOTAL_OtherUltrasound'],
                              'ECG' => (int) $row['ECG'],
                              'total_ecg' => (float) $row['TOTAL_ECG'],
                              'PSA' => (int) $row['PSA'],
                              'total_psa' => (float) $row['TOTAL_PSA'],
                              'Other_xray' => (int) $row['Others_xray'],
                              'total_other_xray' => (float) $row['TOTAL_Others_xray'],
                              'Protime' => (int) $row['PROTIME'],
                              'total_protime' => (float) $row['TOTAL_PROTIME'],
                              'APIT' => (int) $row['APTT'],
                              'total_aptt' => (float) $row['TOTAL_APTT'],
                              'Others_lab' => (int) $row['Others_lab'],
                              'total_Others_lab' => (float) $row['TOTAL_Others_lab'],
                              'total_availment' => (float) $row['total_availment'],
                              'CATEGORY' => $row['CATDESC']."\n".$row['CATEGORYROMANNUM'],
                              );
                              
           $rowindex++;
        }  
        
         # print_r($data);
    }else{
       $data[0]['NAME PATIENT'] = 'No Data'; 
    }
    
    $params->put("start_date", date('F d, Y', strtotime($from_date_format)));
    $params->put("end_date", date('F d, Y', strtotime($to_date_format)));