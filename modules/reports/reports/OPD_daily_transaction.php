<?php 
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require_once('./roots.php');
    require_once($root_path.'include/inc_environment_global.php');
    
    include('parameters.php');
    $dateFrom = explode("-", $from_date_format);
    $dateTo = explode("-", $to_date_format);
    $dateFromFormat = $dateFrom[1]."/".$dateFrom[2]."/".$dateFrom[0];
    $dateToFormat = $dateTo[1]."/".$dateTo[2]."/".$dateFrom[0];
    if($_GET['dept_nr']==IPBM_DEP){
      $dept = IPBM_DEP;
      $enc_type = ' ce.`encounter_type` IN (14)';
      $params->put("title","OPD Daily Transaction");
      $report_title  =IPBM_HEADER;
      $dateFromFormat =  date("M-d-Y",strtotime($from_date_format));
      $dateToFormat =  date("M-d-Y",strtotime($to_date_format));
      $params->put('hospital_name',$hosp_name);
      $params->put('hospital_country',$hosp_country);

    }
    else{
      $dept =$dept_nr;
      $enc_type = ' ce.`encounter_type` IN (2)';
    }
    
    #TITLE of the report
    $params->put("header", $report_title);
   
    

    $params->put("timespan","From: ".$dateFromFormat." to ".$dateToFormat);
    $orderby = "";
    // var_dump($_GET['dept_nr']);exit();
  

    if(!empty($dept)){
    $deptWhere = "AND ce.`current_dept_nr` = ". $dept;
  }
    #added by: syboy 08/30/2015
    if($order=='ascending'){
     $orderby = "ORDER BY cp.name_last ASC,ce.encounter_date ASC";
    }
    elseif ($order=='descending') {
      $orderby = "ORDER BY cp.name_last DESC,ce.encounter_date DESC";
    }
    else{
      $orderby = "ORDER BY ce.encounter_date";
    }

    if($footer=='1'){
        $spmc_f_him_24 = IPBM_FOOTER;
        $effectivity = IPBM_EFFECTIVITY;
        $revision = IPBM_REVISION;
    }
    else{
       $spmc_f_him_24 = "";
       $effectivity = "";
       $revision = "";
    }
    #end
   
    $data = array();
    $from_date_format .= " 00:00:01";
    $to_date_format .= " 23:59:59";

    $sql = "SELECT ce.pid as pid,
                `fn_get_person_lastname_first`(ce.pid) AS patient_name,
                cp.name_first AS fname,
                cp.name_last AS lname,
                cp.name_middle AS mname,
                DATE_FORMAT(ce.encounter_date, '%I:%i %p') AS encounterTime,
                DATE_FORMAT(ce.encounter_date,'%m-%d-%Y %I:%i %p') AS encounterdate,
                IF(cp.sex = 'f', 'F', 'M') AS gender,
                IF(fn_calculate_age (NOW(), cp.date_birth),fn_get_age ( CAST(encounter_date AS DATE),cp.date_birth),age) AS age,
                cp.street_name,
                sb.brgy_name,
                sm.mun_name,
                sp.prov_name,
                cp.name_last,
                cp.civil_status as cstatus,
                fn_get_icd_encounter(ce.encounter_nr) AS icd_code,
                fn_get_icd_name_encounter(ce.encounter_nr) AS icd_name,
                fn_get_personell_name(fn_get_icd_dr_encounter(ce.encounter_nr)) AS diagnosing_clinician,
                `fn_get_department_name`(ce.`current_dept_nr`) AS dept,
                ce.`DEPOvaccine_history`
            FROM care_encounter `ce`
            INNER JOIN care_person `cp`
            ON ce.pid = cp.pid
            LEFT JOIN seg_barangays `sb`
            ON sb.brgy_nr = cp.brgy_nr
            LEFT JOIN `seg_municity` `sm`
            ON cp.mun_nr = sm.mun_nr
            LEFT JOIN seg_provinces AS sp 
            ON sp.prov_nr=sm.prov_nr
            LEFT JOIN seg_regions AS sr 
            ON sr.region_nr= sp.region_nr
            WHERE ce.`encounter_date` BETWEEN (".$db->qstr($from_date_format).") AND (".$db->qstr($to_date_format).")
            AND ce.`encounter_status` NOT IN ('cancelled')
            AND $enc_type
            ".$deptWhere." ".$orderby;


// var_dump($sql);exit();
    $result = $db->GetAll($sql);
    $countRecords = 0;
    if($result){
      $index = 0;
      $countRecords = count($result);
      foreach ($result as $key) {

        //address
        if (trim($key['street_name'])){
          if (trim($key["brgy_name"])!="NOT PROVIDED"){
            $street_name = trim($key['street_name']).", ";
          }else{
            $street_name = trim($key['street_name']).", ";  
          }
        }else{
            $street_name = "";  
        } 


        if ((!(trim($key["brgy_name"]))) || (trim($key["brgy_name"])=="NOT PROVIDED")){
          $brgy_name = "";
        }else{
          $brgy_name  = trim($key["brgy_name"]).", "; 
        }
        
          
      if ((!(trim($key["mun_name"]))) || (trim($key["mun_name"])=="NOT PROVIDED")){
        $mun_name = "";  
      }else{ 
        if ($brgy_name){
          $mun_name = trim($key["mun_name"]);
        }else{
          $mun_name = trim($key["mun_name"]);
        }   
      }

      if ((!(trim($key["prov_name"]))) || (trim($key["prov_name"])=="NOT PROVIDED")){
        $prov_name = ""; 
      }else{
        $prov_name = trim($key["prov_name"]); 
      }
            
      if(stristr(trim($key["mun_name"]), 'city') === FALSE){
        if ((!empty($key["mun_name"]))&&(!empty($key["prov_name"]))){
          if ($prov_name!="NOT PROVIDED"){
            $prov_name = ", ".trim($prov_name);
          }else{
            $prov_name = trim($prov_name);  
          }
        }else{
          $prov_name = "";
        }
      }else
        $prov_name = "";  
        
      $addr = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);
     //end of address
      #Modified by MAtsuu 12062017
      //for age
      // $explodeage = explode(" ",$key['age']);

      // if($explodeage[1] =='years' || $explodeage[1] == 'year'){
      //   $age = $explodeage[0]." y";
      // }
      // else{
      //     $age = "0 y";
      // }
      if (stristr($key['age'],'years')){
          $age = substr($key['age'],0,-5);
          $age = floor($age).' y';
        }elseif (stristr($key['age'],'year')){
          $age = substr($key['age'],0,-4);
          $age = floor($age).' y';
        }elseif (stristr($key['age'],'months')){
          $age = substr($key['age'],0,-6);
          $age = floor($age).' m';
        }elseif (stristr($key['age'],'month')){
          $age = substr($key['age'],0,-5);
          $age = floor($age).' m';
        }elseif (stristr($key['age'],'days')){
          $age = substr($key['age'],0,-4);

          if ($age>30){
            $age = $age/30;
            $label = 'm';
          }else
            $label = 'd';

          $age = floor($age).' '.$label;
        }elseif (stristr($key['age'],'day')){
          $age = substr($key['age'],0,-3);
          $age = floor($age).' d';
        }else{
          $age = floor($key['age']).' y';
        }
      //end of age
        //Ended here ... 

      if($key['DEPOvaccine_history'] == 'yes')
        $depoVaccine = 'Y';
      elseif($key['DEPOvaccine_history'] == 'no')
        $depoVaccine = 'N';
      else
        $depoVaccine = '';

      //place data
        $data[$index]['date'] = $key['encounterdate'];
        $data[$index]['patient_id'] = $key['pid'];
        $data[$index]['fullname'] = utf8_decode(trim($key['patient_name']));
        $data[$index]['fname']= utf8_decode(trim($key['fname']));
        $data[$index]['lname']= utf8_decode(trim($key['lname']));
        $data[$index]['mname']= utf8_decode(trim($key['mname']));
        $data[$index]['status'] = $key['cstatus'];
        $data[$index]['time'] = $key['encounterTime'];
        $data[$index]['age'] = $age;
        $data[$index]['gender'] = $key['gender'];
        $data[$index]['address'] = $addr;
        $data[$index]['icd'] = $key['icd_code'];
        $data[$index]['Physician'] = utf8_decode(trim($key['diagnosing_clinician']));
        $data[$index]['diagnosis'] = $key['icd_name'];
        $data[$index]['department'] = $key['dept'];
        $data[$index]['depo'] = $depoVaccine;


        $data[$index]['no.'] = $index + 1;
        $index++;

      }     
    }else{
      $data[0]['patient_id'] = "No Record";
      $data[0]['fullname'] = "Found...";
    }

    $params->put("spmc_f_him_24", $spmc_f_him_24);
    $params->put("effectivity", $effectivity);
    $params->put("revision", $revision);
    $params->put("recordsfound", "Number of Records : ".$countRecords);
?>