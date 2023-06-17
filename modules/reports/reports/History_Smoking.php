<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');

    if($_GET['dept_nr']==182){
      $enc_dept_cond = " AND (e.current_dept_nr IN (".IPBM_DEP.") \n".
                                        " OR e.current_dept_nr IN ( \n".
                                            " SELECT nr FROM care_department AS d WHERE d.parent_dept_nr IN (".IPBM_DEP."))) ";
      $dept_label = $ipbm_header;
      if(empty($patient_type_ipbm)){
        $patient_type = IPBM_patient_type;
        $patient_type_label = "ALL PATIENTS";
      }
      else{
        $patient_type = $patient_type_ipbm;
      }
    }
    else{
        $patient_type = '1,2,3,4,6';
        $patient_type_label = "ALL PATIENTS";
    }
  
    #TITLE of the report
    $params->put("hospital_name", mb_strtoupper($hosp_name));
    $params->put("header", $report_title);
    $params->put("department", $dept_label .' - '.$patient_type_label);
        
    //$patient_type = '3,4';
    $date_based = 'e.encounter_date';
     /*edited by art 01/28/15
    * fix parameter in query (dept,patient_type)
    */
    $sql = "SELECT DISTINCT e.pid AS hrn,
            CONCAT(IF (TRIM(p.name_last) IS NULL,'',TRIM(p.name_last)),', ',
            IF(TRIM(p.name_first) IS NULL ,'',TRIM(p.name_first)),' ',
            IF(TRIM(p.name_middle) IS NULL,'',TRIM(p.name_middle))) AS 'Full_Name',
            e.admission_dt AS 'Date_Admitted', MAX(e.encounter_date) AS 'Date_Consultation', 
            UPPER(p.sex) AS Sex,
            IF(p.date_birth!='0000-00-00',p.date_birth,NULL) AS date_birth,
            IF (fn_calculate_age(NOW(),p.date_birth),fn_get_age(NOW(),p.date_birth),age) AS Age,
            d.name_formal AS department,
            CONCAT(IF (TRIM(p.street_name) IS NULL,'',TRIM(p.street_name)),' ',
                IF (TRIM(sb.brgy_name) IS NULL,'',TRIM(sb.brgy_name)),' ',
                IF (TRIM(sm.mun_name) IS NULL,'',TRIM(sm.mun_name)),' ',
                IF (TRIM(sm.zipcode) IS NULL,'',TRIM(sm.zipcode)),' ',
                IF (TRIM(sp.prov_name) IS NULL,'',TRIM(sp.prov_name)),' ',
                IF (TRIM(sr.region_name) IS NULL,'',TRIM(sr.region_name))) AS 'Complete_Address',
            UPPER(IF (MIN(e.current_att_dr_nr),
            fn_get_personell_name(e.current_att_dr_nr),NULL)) AS 'Attending_Physician',
            MAX(e.encounter_type) AS 'encounter_type', MAX(e.smoker_history) as smoking_history, MAX(e.drinker_history) AS 'drinker_history'
            
            FROM care_encounter AS e
            INNER JOIN care_person AS p ON p.pid=e.pid
            LEFT JOIN care_department AS d 
                ON d.nr=IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr)
            LEFT JOIN seg_encounter_result AS ser ON ser.encounter_nr = e.encounter_nr

            LEFT JOIN seg_encounter_insurance AS ins ON ins.encounter_nr=e.encounter_nr

            LEFT JOIN seg_barangays AS sb ON sb.brgy_nr=p.brgy_nr
            LEFT JOIN seg_municity AS sm ON sm.mun_nr=p.mun_nr
            LEFT JOIN seg_provinces AS sp ON sp.prov_nr=sm.prov_nr
            LEFT JOIN seg_regions AS sr ON sr.region_nr=sp.region_nr
            WHERE e.STATUS NOT IN ('deleted','hidden','inactive','void')
            AND DATE($date_based) BETWEEN ".$db->qstr($from_date_format)." AND ".$db->qstr($to_date_format)."
            AND e.encounter_type IN ($patient_type)
            $enc_dept_cond  
            GROUP BY IF(e.current_dept_nr,e.current_dept_nr,e.consulting_dept_nr),e.pid,e.encounter_nr ORDER BY encounter_date";
           
    #echo $sql; 
    #exit();
    $rs = $db->Execute($sql);
    
    $rowindex = 0;
    $grand_total = 0;
    $data = array();
    if (is_object($rs)){
        if($rs->RecordCount()){
          while($row=$rs->FetchRow()){
              
              if ($row['encounter_type']==2)
                  $patient_type = 'Outpatient';
              elseif ($row['encounter_type']==1)
                  $patient_type = 'ER Patient';
              elseif (($row['encounter_type']==3) || ($row['encounter_type']==4))
                  $patient_type = 'Inpatient';
              elseif ($row['encounter_type'] == 6){
                  $patient_type = 'HSSC';
                  $row['department'] = "FAMILY MEDICINE";
              }
              elseif($row['encounter_type']=='14'){
                $patient_type = 'OPD';
              }
              elseif($row['encounter_type']=='13'){
                 $patient_type = 'IPD';
              }
              else
                  $patient_type = 'Walkin';
                  
              if ($row['smoking_history']=='yes')
                $smoking_history = "SMOKER";
                
              elseif ($row['smoking_history']=='no')
                $smoking_history = "NON-SMOKER";
              elseif ($row['smoking_history']=='na')
                $smoking_history = "N/A";
              else
                $smoking_history = "UNSPECIFIED";
                
              if ($row['drinker_history']=='yes')
                $drinker_history = "DRINKER";
              elseif ($row['drinker_history']=='no')
                $drinker_history = "NON-DRINKER";
              elseif ($row['drinker_history']=='na')
                $drinker_history = "N/A";
              else
                $drinker_history = "UNSPECIFIED";      
              #added by art 01/28/15       
              $smoker_count = $row['smoking_history']=='yes' ? 1:0;
              $non_smoker_count = $row['smoking_history']=='no' ? 1:0;
              $na_smoker_count = $row['smoking_history']=='na'? 1:0;
              $unspecied_smoker_count = $row['smoking_history']=='' ? 1:0;
              $drinker_count = $row['drinker_history']=='yes' ? 1:0;
              $non_drinker_count = $row['drinker_history']=='no' ? 1:0;
              $na_drinker_count = $row['drinker_history']=='na'? 1:0;
              $unspecied_drinker_count = $row['drinker_history']=='' ? 1:0;

              /*if ($row['encounter_type']==1) {
                $date = $row['Date_Consultation'];
              }else{
                $date = $row['Date_Admitted'];
              }
               * 
               */
              $date = $row['Date_Consultation'];
              #end
              $data[$rowindex] = array('rowindex' => $rowindex+1,
                                'hrn' => $row['hrn'],
                                'Full_Name' => utf8_decode(trim($row['Full_Name'])),
                                'Date_Admitted' => date("m/d/Y h:i A",strtotime($date)),
                                'Age' => $row['Age'],
                                'Sex' => $row['Sex'],
                                'Complete_Address' => $row['Complete_Address'],
                                'department' => $row['department'],
                                'patient_type' => $patient_type,
                                'is_smoking' => $smoking_history,
                                'smoker_count' => $smoker_count,
                                'non_smoker_count' => $non_smoker_count,
                                'na_smoker_count' => $na_smoker_count,
                                'unspecied_smoker_count' => $unspecied_smoker_count,
                                'drinker_count' => $drinker_count,
                                'non_drinker_count' => $non_drinker_count,
                                'na_drinker_count' => $na_drinker_count,
                                'unspecied_drinker_count' => $unspecied_drinker_count,
                                'is_drinking' => $drinker_history,
                                'Attending_Physician' => $row['Attending_Physician'],
                                );
                                
             $rowindex++;
          }  
        }else{
          $data[0] = array(
                          'Full_Name' => 'No records',
                          'smoker_count' => 0,
                          'non_smoker_count' => 0,
                          'na_smoker_count' => 0,
                          'unspecied_smoker_count' => 0,
                          'drinker_count' => 0,
                          'non_drinker_count' =>0,
                          'na_drinker_count' => 0,
                          'unspecied_drinker_count' => 0,
                          );
        }
    }else{
        $data[0]['Full_Name'] = 'No records'; 
    }   
     $baseurl = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_ADDR'],
        substr(dirname($_SERVER["REQUEST_URI"]), 0, strpos($_SERVER["REQUEST_URI"], $top_dir))
    );
$params->put("spmc_logo", $baseurl . "gui/img/logos/dmc_logo.jpg");
$params->put("ipbm_logo", $baseurl . "img/ipbm.png");       