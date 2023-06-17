<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');


    $paramurl = $_GET['param'];
    #Added by Matsuu 02142018
    if($_GET['dept_nr']==IPBM_DEP){
      if(empty($patient_type)){
        $patient_type = IPBM_patient_type;
        $patient_type_label = "ALL PATIENTS";
      }
     $report_title = "Number of Patients encoded with ICD 10";
    }
    #Ended here...
    #TITLE of the report
    $params->put("hospital_country", $hosp_country);
    $params->put("hospital_agency", $hosp_agency);
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("hospital_address", $hosp_addr1);
    $params->put("header", strtoupper($report_title) . " FOR " . $patient_type_label);
    $params->put("diagnosis", $icd_class . ' Diagnosis');
    //$params->put("range", "From ".$from_date_format." To ".$to_date_format);
    $params->put("range", "From " . strftime("%B %d, %Y", $_GET['from_date']) . " To " . strftime("%B %d, %Y", $_GET['to_date']));
    $params->put("dept_nr",$_GET['dept_nr']);
    $params->put("ipbm",IPBM_HEADER);
$where = "";
$encodername = "ALL MEDICAL RECORDS PERSONNEL";
if($paramurl != ""){
    $paramurlexplode = explode(",", $paramurl);
    foreach ($paramurlexplode as $paramvalue) {
        $explodeparamvalue = explode("--", $paramvalue);
        if($explodeparamvalue[0] == "param_encoder" || $explodeparamvalue[0]=="param_psy_encoder"){
            $personelname = $db->GetOne("SELECT name FROM care_users WHERE personell_nr =".$db->qstr($explodeparamvalue[1]));
            if($personelname){
                 $wherepersonell .="AND ced.`create_id` = ".$db->qstr($personelname);
                $encodername = $personelname;
             }else{
                 $personelname2 = $db->GetRows("SELECT name_first, 
                                                        name_last 
                                                FROM care_person `cp`
                                                INNER JOIN care_personell `cpl`
                                                ON cp.`pid` = cpl.`nr`
                                                WHERE cpl.`nr` =".$db->qstr($explodeparamvalue[1]));
                 $personellname3 = $personelname2['name_first']." ".$personelname2['name_last'];
                 $wherepersonell .="AND ced.`create_id` = ".$db->qstr($personellname3);
                 $encodername = $personellname3;
             }
        }   
    }
}
$params->put("encoder", $encodername);
$data = array();

/*$departmentQuery = "SELECT nr, name_formal FROM care_department cd where cd.`is_inactive` = 0 and  cd.`type` = '1'";
$departmentResult = $db->GetAll($departmentQuery);*/

$x = 0;
$No_off_records = 0;
$total_nphic = 0;
$total_phic = 0;

$query = "SELECT
              DEPT AS DEPT, SUM(PHIC) AS PHIC, SUM(NPHIC) AS NPHIC
            FROM
              (SELECT
                cd.name_formal AS DEPT, COUNT(ced.code) AS PHIC, 0 AS NPHIC
              FROM
                care_encounter AS ce
                LEFT JOIN care_encounter_diagnosis AS ced
                  ON ced.encounter_nr = ce.encounter_nr
                LEFT JOIN seg_encounter_insurance_memberinfo AS seim
                  ON seim.encounter_nr = ce.encounter_nr
                LEFT JOIN care_department AS cd
                  ON cd.nr = ce.current_dept_nr
                LEFT JOIN seg_billing_encounter AS sbe
                  ON ce.encounter_nr = sbe.encounter_nr
              WHERE DATE(ced.create_time) BETWEEN ". $db->qstr($from_date_format) ."
                AND ". $db->qstr($to_date_format) ."
                AND ced.type_nr IN (". $type_nr . ") " . $wherepersonell ."
                AND ce.is_discharged = '1'
                AND ce.encounter_type IN (". $patient_type .")
                AND seim.hcare_id = '18'
                AND sbe.is_final = '1'
                AND sbe.is_deleted IS NULL
                AND ced.status NOT IN ('deleted','hidden','inactive','void','added')
                AND ce.status NOT IN ('deleted','hidden','inactive','void')
              GROUP BY cd.name_formal
              UNION
              ALL
              SELECT
                cd.name_formal AS DEPT, 0 AS PHIC, COUNT(ced.code) AS NPHIC
              FROM
                care_encounter AS ce
                LEFT JOIN care_encounter_diagnosis AS ced
                  ON ced.encounter_nr = ce.encounter_nr
                LEFT JOIN seg_encounter_insurance_memberinfo AS seim
                  ON seim.encounter_nr = ce.encounter_nr
                LEFT JOIN care_department AS cd
                  ON cd.nr = ce.current_dept_nr
                LEFT JOIN seg_billing_encounter AS sbe
                    ON ce.encounter_nr = sbe.encounter_nr
              WHERE DATE(ced.create_time) BETWEEN ". $db->qstr($from_date_format) ."
                AND ". $db->qstr($to_date_format) ."
                AND ced.type_nr IN (". $type_nr . ") " . $wherepersonell ."
                AND ce.is_discharged = '1'
                AND ce.encounter_type IN (". $patient_type .")
                AND (ISNULL(seim.hcare_id) OR (seim.hcare_id = '18' AND sbe.is_final = '0'))
                AND sbe.is_deleted IS NULL
                AND ced.status NOT IN ('deleted','hidden','inactive','void','added')
                AND ce.status NOT IN ('deleted','hidden','inactive','void')
              GROUP BY cd.name_formal) AS t
            GROUP BY t.DEPT ";


#var_dump($query);exit();
$result = $db->Execute($query);

if($result){
    if($result->RecordCount() > 0){
        while($row = $result->FetchRow()){
            $total_phic = $total_phic + $row['PHIC'];
            $total_nphic = $total_nphic + $row['NPHIC'];

            $data[$x] = array(
                'department' => ($_GET['dept_nr']==IPBM_DEP?strtoupper(IPBM_HEADER):$row['DEPT']),
                'PHIC' => $row['PHIC'],
                'non_phic' => $row['NPHIC']
            );

            $x++;
        }
    }
}

$No_off_records = $total_phic + $total_nphic;

/*foreach ($departmentResult as $key) {
    if($diag_type == '0'){
        $Query = "SELECT SUM(
                          (SELECT COUNT(ced.code) FROM care_encounter_diagnosis AS ced
                              LEFT JOIN `seg_encounter_insurance_memberinfo` AS seim ON seim.encounter_nr = ced.encounter_nr
                                WHERE ced.encounter_nr = ce.`encounter_nr` " . $wherepersonell . " AND ced.status != 'deleted' AND seim.hcare_id = '18')
                         ) AS phic,
                         SUM(
                          (SELECT COUNT(ced.code) FROM care_encounter_diagnosis AS ced
                             LEFT JOIN `seg_encounter_insurance_memberinfo` AS seim ON seim.encounter_nr = ced.encounter_nr
                                WHERE ced.encounter_nr = ce.`encounter_nr` " . $wherepersonell . " AND ced.status != 'deleted')
                         ) AS nphic
                 FROM care_encounter AS ce
                 WHERE ce.`status` NOT IN ('deleted','void','hidden','cancelled')
                 AND DATE(ce.`encounter_date`) BETWEEN ". $db->qstr($from_date_format) . " AND " . $db->qstr($to_date_format) . "
                 AND ce.`is_discharged` = '1'
                 AND ce.`current_dept_nr` =" . $db->qstr($key['nr']) . "
                 AND ce.`encounter_type` IN(" . $ptype . ")";
    }
    else{
        $Query = "SELECT SUM(IF(
                        (SELECT encounter_nr FROM care_encounter_diagnosis ced WHERE ced.`encounter_nr` = ce.`encounter_nr` " . $wherepersonell . " AND ced.status != 'deleted' LIMIT 1),
                          IF((SELECT encounter_nr FROM `seg_encounter_insurance_memberinfo` WHERE encounter_nr = ce.`encounter_nr` AND hcare_id = '18'), 
                          '1', 
                          '0'), 
                        '0')) AS phic,
                        SUM(IF(
                          (SELECT encounter_nr FROM care_encounter_diagnosis ced WHERE ced.`encounter_nr` = ce.`encounter_nr` " . $wherepersonell . " AND ced.status != 'deleted' LIMIT 1),
                          
                          IF((SELECT encounter_nr FROM `seg_encounter_insurance_memberinfo` WHERE encounter_nr = ce.`encounter_nr` AND hcare_id = '18'), 
                          0, 
                          1), 
                        '0')) AS nphic
            FROM care_encounter AS ce
            WHERE ce.`status` NOT IN ('deleted', 'void', 'hidden', 'cancelled')
            AND DATE(ce.`encounter_date`) BETWEEN " . $db->qstr($from_date_format) . " AND " . $db->qstr($to_date_format) . "
            AND ce.is_discharged = '1'
            AND ce.current_dept_nr =" . $db->qstr($key['nr']) . "
            AND ce.encounter_type IN(" . $ptype . ")";
    }


    //echo $Query; exit();

   $queryResult = $db->GetRow($Query);
    $queryResult2 = $db->GetRow($Query2);

    if($queryResult2['phic'] != 0 || $queryResult['nphic'] != 0){
        $data[$x]['department'] = $key['name_formal'];
        $data[$x]['PHIC'] = $queryResult2['phic'];
        $data[$x]['non_phic'] = $queryResult['nphic'];
        $x++;
        $No_off_records = $No_off_records + $queryResult2['phic'];
        $No_off_records = $No_off_records + $queryResult['nphic'];
        $total_nphic = $total_nphic + $queryResult['nphic'];
        $total_phic = $total_phic + $queryResult2['phic'];
    }
   
}*/

$baseurl = sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_ADDR'],
    substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
);

$params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put("ipbm_logo", $baseurl . "img/ipbm.png");
$params->put("records_no", $No_off_records);
$params->put("total_nphic", $total_nphic);
$params->put("total_phic", $total_phic);