<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
define("ULTRASOUND_DEPT", 165);
define("OB_ULTRASOUND_DEPT", 209);
include('parameters.php');

global $db;

$params->put('date_span', "From: " . date('F d, Y',$from_date) . " To: " . date('F d, Y',$to_date));

$cond = "DATE(ce.encounter_date)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

$sql = "SELECT
          DATE AS DATE, Hospital_Num AS Hospital_Num, encounter AS encounter,
          NAME_PATIENT AS NAME_PATIENT, Department AS Department, Gender AS Gender,
          Age AS Age, Diagnosis AS Diagnosis, Category AS Category,
          SUM(CBC) AS CBC, SUM(URNALYSIS) AS URNALYSIS, SUM(FBS) AS FBS,
          SUM(SUA) AS SUA, SUM(CREA) AS CREA, SUM(TCHOLE) AS TCHOLE,
          SUM(NA) AS NA, SUM(K) AS K, SUM(LIPID_PROFILE) AS LIPID_PROFILE,
          SUM(TSH) AS TSH, SUM(HBSAG) AS HBSAG, SUM(A_HBS) AS A_HBS,
          SUM(ECG) AS ECG, SUM(PSA) AS PSA, SUM(PROTIME) AS PROTIME,
          SUM(APTT) AS APTT, SUM(Others_lab) AS Others_lab,
          SUM(CHEST_xray) AS CHEST_xray, SUM(LS_Xray) AS LS_Xray,
          SUM(OtherUltrasound) AS OtherUltrasound, SUM(Others_xray) AS Others_xray
        FROM
          (SELECT
            ce.encounter_date AS DATE, ce.pid AS Hospital_Num,
            ce.encounter_nr AS encounter, fn_get_person_name (ce.pid) AS NAME_PATIENT,
            fn_get_department_name (IF(ce.current_dept_nr != 0, ce.current_dept_nr, ce.consulting_dept_nr)) AS Department,
            fn_get_gender (ce.pid) AS Gender,
            fn_get_age (ce.encounter_date,(SELECT cp.date_birth FROM care_person cp WHERE cp.pid = ce.pid)) AS Age,
            ce.er_opd_diagnosis AS Diagnosis, cpl.contract_class AS Category,
            SUM(CASE WHEN slsd.service_code LIKE '%CBC%' THEN 1 ELSE 0 END) AS CBC,
            SUM(CASE WHEN slsd.service_code = 'URINE' THEN 1 ELSE 0 END) AS URNALYSIS,
            SUM(CASE WHEN slsd.service_code = 'GLUFBS' THEN 1 ELSE 0 END) AS FBS,
            SUM(CASE WHEN slsd.service_code = 'SUA' THEN 1 ELSE 0 END) AS SUA,
            SUM(CASE WHEN slsd.service_code = 'CREA' THEN 1 ELSE 0 END) AS CREA,
            SUM(CASE WHEN slsd.service_code = 'CHOL' THEN 1 ELSE 0 END) AS TCHOLE,
            SUM(CASE WHEN slsd.service_code = 'NA' THEN 1 ELSE 0 END) AS NA,
            SUM(CASE WHEN slsd.service_code = 'K+' THEN 1 ELSE 0 END) AS K,
            SUM(CASE WHEN slsd.service_code = 'LIPID' THEN 1 ELSE 0 END) AS LIPID_PROFILE,
            SUM(CASE WHEN slsd.service_code = 'TSH' THEN 1 ELSE 0 END) AS TSH,
            SUM(CASE WHEN slsd.service_code = 'HBsAg' THEN 1 ELSE 0 END) AS HBSAG,
            SUM(CASE WHEN slsd.service_code = 'A-HBs' THEN 1 ELSE 0 END) AS A_HBS,
            SUM(CASE WHEN slsd.service_code = 'ECG' THEN 1 ELSE 0 END) AS ECG,
            SUM(CASE WHEN slsd.service_code = 'PSA' THEN 1 ELSE 0 END) AS PSA,
            SUM(CASE WHEN slsd.service_code = 'PT' THEN 1 ELSE 0 END) AS PROTIME,
            SUM(CASE WHEN slsd.service_code = 'APTTT' THEN 1 ELSE 0 END) AS APTT,
            SUM(CASE WHEN (slsd.service_code NOT IN ('URINE', 'GLUFBS', 'SUA', 'CREA', 'CHOL', 'NA', 'K+', 'LIPID',
                  'TSH', 'IFT4', 'HBsAg', 'A-HBs', 'ECG', 'PSA', 'PT', 'APTTT')
                  AND slsd.service_code NOT LIKE '%CBC%') THEN 1 ELSE 0 END) AS Others_lab,
            0 AS CHEST_xray, 0 AS LS_Xray, 0 AS OtherUltrasound, 0 AS Others_xray
          FROM
            care_encounter AS ce
            INNER JOIN care_personell AS cpl
              ON cpl.pid = ce.pid
            INNER JOIN seg_lab_serv AS sls
              ON sls.encounter_nr = ce.encounter_nr
            INNER JOIN seg_lab_servdetails AS slsd
              ON sls.refno = slsd.refno
          WHERE ce.status NOT IN ('deleted','hidden','inactive','void')
            AND sls.status NOT IN ('deleted','hidden','inactive','void')
            AND cpl.status NOT IN ('deleted','hidden','inactive','void')
            AND slsd.is_served = '1'
            AND ". $cond ."
          GROUP BY ce.encounter_nr
          UNION
          ALL
          SELECT
            ce.encounter_date AS DATE, ce.pid AS Hospital_Num,
            ce.encounter_nr AS encounter, fn_get_person_name (ce.pid) AS NAME_PATIENT,
            fn_get_department_name (IF(ce.current_dept_nr != 0, ce.current_dept_nr, ce.consulting_dept_nr)) AS Department,
            fn_get_gender (ce.pid) AS Gender,
            fn_get_age (ce.encounter_date,(SELECT cp.date_birth FROM care_person cp WHERE cp.pid = ce.pid)) AS Age,
            ce.er_opd_diagnosis AS Diagnosis,cpl.contract_class AS Category,
            0 AS CBC, 0 AS URNALYSIS, 0 AS FBS, 0 AS SUA, 0 AS CREA, 0 AS CHOL, 0 AS NA, 0 AS K, 0 AS LIPID_PROFILE,
            0 AS TSH, 0 AS HBSAG, 0 AS A_HBS, 0 AS ECG, 0 AS PSA, 0 AS PROTIME, 0 AS APTT, 0 AS Others_lab,
            SUM(CASE WHEN ctrr.service_code = 'CPA' THEN 1 ELSE 0 END) AS CHEST_xray,
            SUM(CASE WHEN ctrr.service_code = 'LSFLEX' THEN 1 ELSE 0 END) AS LS_Xray,
            SUM(CASE WHEN (ctrr.service_code LIKE '%USD%' OR srsg.department_nr IN (".$db->qstr(ULTRASOUND_DEPT).",".$db->qstr(OB_ULTRASOUND_DEPT).")) THEN 1 ELSE 0 END) AS OtherUltrasound,
            SUM(CASE WHEN (ctrr.service_code NOT IN ('CPA', 'LSFLEX') AND ctrr.service_code NOT LIKE '%USD%' AND srsg.department_nr NOT IN (".$db->qstr(ULTRASOUND_DEPT).",".$db->qstr(OB_ULTRASOUND_DEPT).")) THEN 1 ELSE 0 END) AS Others_xray
          FROM
            care_encounter AS ce
            INNER JOIN care_personell AS cpl
              ON cpl.pid = ce.pid
            INNER JOIN seg_radio_serv AS srs
              ON srs.encounter_nr = ce.encounter_nr
            INNER JOIN care_test_request_radio AS ctrr
              ON ctrr.refno = srs.refno
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
        GROUP BY t.encounter
        ORDER BY t.DATE ";
// die($sql);
$rs = $db->Execute($sql);


$i = 1;
$sum_cbc = 0;
$sum_urinalysis = 0;
$sum_fbs = 0;
$sum_sua = 0;
$sum_crea = 0;
$sum_tchole = 0;
$sum_na = 0;
$sum_k = 0;
$sum_lipid = 0;
$sum_tsh = 0;
$sum_hbsag = 0;
$sum_ahbs = 0;
$sum_chest = 0;
$sum_ls = 0;
$sum_ecg = 0;
$sum_other_ultra = 0;
$sum_other_xray = 0;
$sum_psa = 0;
$sum_protime = 0;
$sum_aptt = 0;
$sum_other_lab = 0;

if($rs){
    if($rs->RecordCount() > 0){
        while($row = $rs->FetchRow()){

            $sum_cbc = $sum_cbc + (int)$row['CBC'];
            $sum_urinalysis = $sum_urinalysis + (int)$row['URNALYSIS'];
            $sum_fbs = $sum_fbs + (int)$row['FBS'];
            $sum_sua = $sum_sua + (int)$row['SUA'];
            $sum_crea = $sum_crea + (int)$row['CREA'];
            $sum_tchole = $sum_tchole + (int)$row['TCHOLE'];
            $sum_na = $sum_na + (int)$row['NA'];
            $sum_k = $sum_k + (int)$row['K'];
            $sum_lipid = $sum_lipid + (int)$row['LIPID_PROFILE'];
            $sum_tsh = $sum_tsh + (int)$row['TSH'];
            $sum_hbsag = $sum_hbsag + (int)$row['HBSAG'];
            $sum_ahbs = $sum_ahbs + (int)$row['A_HBS'];
            $sum_chest = $sum_chest + (int)$row['CHEST_xray'];
            $sum_ls = $sum_ls + (int)$row['LS_Xray'];
            $sum_other_ultra = $sum_other_ultra + (int)$row['OtherUltrasound'];
            $sum_ecg = $sum_ecg + (int)$row['ECG'];
            $sum_psa = $sum_psa + (int)$row['PSA'];
            $sum_other_xray = $sum_other_xray + (int)$row['Others_xray'];
            $sum_protime = $sum_protime + (int)$row['PROTIME'];
            $sum_aptt = $sum_aptt + (int)$row['APTT'];
            $sum_other_lab = $sum_other_lab + (int)$row['Others_lab'];

            $data[$i] = array(
                'date' => date('m/d/Y', strtotime($row['DATE'])),
                'rep_cnt' => $i,
                'patient' => utf8_decode(trim($row['NAME_PATIENT'])),
                'dept' => $row['Department'],
                'sex' => mb_strtoupper($row['Gender']),
                'age' => $row['Age'],
                'diagnosis' => $row['Diagnosis'],
                'category' => $row['Category'],
                'cbc' => $row['CBC'],
                'urinalysis' => $row['URNALYSIS'],
                'fbs' => $row['FBS'],
                'sua' => $row['SUA'],
                'crea' => $row['CREA'],
                'tchloe' => $row['TCHOLE'],
                'na' => $row['NA'],
                'k' => $row['K'],
                'lipid' => $row['LIPID_PROFILE'],
                'tsh' => $row['TSH'],
                'hbsag' => $row['HBSAG'],
                'a_hbs' => $row['A_HBS'],
                'chest' => $row['CHEST_xray'],
                'ls' => $row['LS_Xray'],
                'other_ultra' => $row['OtherUltrasound'],
                'ecg' => $row['ECG'],
                'psa' => $row['PSA'],
                'other_xray' => $row['Others_xray'],
                'protime' => $row['PROTIME'],
                'aptt' => $row['APTT'],
                'other_lab' => $row['Others_lab']
            );

            $i++;
        }
        //var_dump($data);
        $params->put('sum_cbc', (int)$sum_cbc);
        $params->put('sum_urinalysis', (int)$sum_urinalysis);
        $params->put('sum_fbs', (int)$sum_fbs);
        $params->put('sum_sua', (int)$sum_sua);
        $params->put('sum_crea', (int)$sum_crea);
        $params->put('sum_tchole', (int)$sum_tchole);
        $params->put('sum_na', (int)$sum_na);
        $params->put('sum_k', (int)$sum_k);
        $params->put('sum_lipid', (int)$sum_lipid);
        $params->put('sum_tsh', (int)$sum_tsh);
        $params->put('sum_hbsag', (int)$sum_hbsag);
        $params->put('sum_ahbs', (int)$sum_ahbs);
        $params->put('sum_chest', (int)$sum_chest);
        $params->put('sum_ls', (int)$sum_ls);
        $params->put('sum_other_ultra', (int)$sum_other_ultra);
        $params->put('sum_ecg', (int)$sum_ecg);
        $params->put('sum_psa', (int)$sum_psa);
        $params->put('sum_other_xray', (int)$sum_other_xray);
        $params->put('sum_protime', (int)$sum_protime);
        $params->put('sum_aptt', (int)$sum_aptt);
        $params->put('sum_other_lab', (int)$sum_other_lab);
    }
    else{
        $data = array(
            array(
                'date' => 'No Data'
            )
        );
    }
}
else{
    $data = array(
        array(
            'date' => 'No Data'
        )
    );
}