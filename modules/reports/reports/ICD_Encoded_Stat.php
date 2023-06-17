<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    require_once($root_path.'include/care_api_classes/class_user.php');

    include('parameters.php');
    
    if(isset($_GET['dept_nr'])){
      $dept_nr = $_GET['dept_nr'];
    }

    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $dept_label);
    $sql_type_nr = "";
    if($with_icd10_class){
       $sql_type_nr = " AND type_nr IN (".$type_nr.")";
    }
    if(isset($dept_nr) && $dept_nr == IPBM_DEP){
       $enc_dept_cond = " AND (e.current_dept_nr IN (". IPBM_DEP .")) \n";


        if($patient_type == '' || $patient_type == NULL || empty($patient_type)){
            $sql_patient_type = " AND e.encounter_type IN (".IPBM_patient_type.")";
            $patient_type_label = "ALL PATIENTS";
        }else{

          $sql_patient_type = " AND e.encounter_type IN (".$patient_type.")";
        }

        $params->put('ptype',$patient_type_label);
        $params->put('code',$icd_class);
        
        if($with_encoder){
          $user_obj = new SegUser;
          $enc = $user_obj->getUserName($encoder);
          
          $encoder_fullname = ucwords(strtolower($enc["name"]));
          $sql_encoder_fullname = "AND ced.create_id = '".$encoder_fullname."'";
          $params->put('encoder_name', $encoder_fullname);
        }else{
          $psql = "SELECT choices FROM seg_rep_params WHERE param_id = 'PSY_mr_encoder'";
          $tmpipbmMedRedPersonellSql = $db->getOne($psql);
          $regex = '/^[\0-\377]+(?=FROM)/';
          $select = 'SELECT u.name ';
          
          $ipbmMedRedPersonellSql = preg_replace($regex, $select, $tmpipbmMedRedPersonellSql);
          $sql_encoder_fullname = "AND ced.create_id IN (". $ipbmMedRedPersonellSql .")";
           $params->put('encoder_name', 'ALL MEDICAL RECORDS PERSON');
        }
        $baseurl = sprintf(
                "%s://%s%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
                $_SERVER['SERVER_ADDR'],
                substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
            );
        
        $params->put("dmc", $baseurl . "gui/img/logos/dmc_logo.jpg");
        $params->put("ipbm_logo", $baseurl . "img/ipbm_new.png"); 
    }

    // $sql = "SELECT d.name_formal AS department ,
    //               SUM(CASE WHEN cpi.hcare_id='18' THEN 1 ELSE 0 END) AS PHIC,
    //               SUM(CASE WHEN cpi.hcare_id!='18' OR cpi.hcare_id IS NULL THEN 1 ELSE 0 END) AS NPHIC
    //         FROM care_person_insurance AS cpi
    //         INNER JOIN care_encounter AS e ON e.pid = cpi.pid
    //         INNER JOIN care_department AS d ON d.nr = e.current_dept_nr
    //         RIGHT JOIN (SELECT DISTINCT encounter_nr FROM care_encounter_diagnosis 
    //             WHERE code IS NOT NULL 
    //             AND status NOT IN('deleted', 'void', 'hidden', 'cancelled')) AS ced
    //         ON e.encounter_nr = ced.encounter_nr
    //         WHERE   
    //         AND e.status NOT IN ('deleted', 'void', 'hidden', 'cancelled')
    //         AND e.encounter_type IN ($patient_type)
    //         $enc_dept_cond
    //         AND DATE(ced.create_time) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)." 
    //         GROUP BY d.name_formal;";

    $sql = "SELECT d.name_formal AS department ,SUM(CASE WHEN cpi.encounter_nr IS NOT NULL then 1 else 0 end) AS PHIC,
            SUM(CASE WHEN cpi.encounter_nr IS NULL then 1 else 0 end) AS NPHIC
            " . ($with_encoder ? ',ced.create_id as create_id' : '') . "
            FROM care_encounter_diagnosis AS ced
            INNER JOIN care_encounter AS e ON e.encounter_nr = ced.encounter_nr
            LEFT JOIN seg_encounter_insurance AS cpi ON cpi.encounter_nr = e.encounter_nr
            INNER JOIN care_department AS d ON d.nr = e.current_dept_nr
            WHERE ced.code IS NOT NULL
            AND ced.status NOT IN('deleted', 'void', 'hidden', 'cancelled')
            AND e.status NOT IN ('deleted', 'void', 'hidden', 'cancelled')
            $sql_patient_type
            $enc_dept_cond
            $sql_encoder_fullname
            $sql_type_nr
            AND DATE(ced.create_time) BETWEEN '".$from_date_format."' AND '".$to_date_format."'
            GROUP BY d.name_formal;";
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $data = array();
    if (is_object($rs)){
        if($rs->RecordCount() > 0){
          $params->put('Total_Records', (string)$rs->RecordCount());
          while($row=$rs->FetchRow()){
              $total_nphic += $row['NPHIC'];
              $total_phic += $row['PHIC'];
              $data[$rowindex]=array(
                                'department' => mb_strtoupper($row['department']),
                                'PHIC' => (int) $row['PHIC'],
                                'NPHIC' => (int) $row['NPHIC'],
                                'Total_NPHIC' => (int) $total_nphic,
                                'Total_PHIC' => (int) $total_phic,
                                );  

             $rowindex++;
          }  
        }else{
          $params->put('Total_Records', '1');
          $data[0]=array(
                          'department' => 'IPBM',
                          'PHIC' => 0,
                          'NPHIC' => 0,
                          'Total_NPHIC' => 0,
                          'Total_PHIC' => 0,
                          );  
        }
    }else{
        $params->put('Total_Records', '1');
        $data[0]=array(
                          'department' => 'IPBM',
                          'PHIC' => 0,
                          'NPHIC' => 0,
                          'Total_NPHIC' => 0,
                          'Total_PHIC' => 0,
                          ); 
    }       
