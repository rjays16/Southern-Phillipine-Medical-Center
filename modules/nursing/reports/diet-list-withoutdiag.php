<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require($root_path.'include/care_api_classes/class_person.php');
require($root_path.'include/care_api_classes/class_notes_nursing.php');
require_once($root_path.'include/care_api_classes/class_notes.php');
require_once($root_path . '/frontend/bootstrap.php');

global $db;
$pers_obj = new Personell;
define(HALAL_LIST, 'HL');
define(NOURISH_LIST,'NL');
define(ORAL_LIST, 'ODL');
define(TUBEFEEDING, 'TFL');
define(WARD_LIST, 'WLD');
define(BAJAO,'33');
define(SDA, '43'); #Seventh Day Adventist 
define(ISLAM,'16');
define(NO_BMI_AGE, 5);
define(NO_CATEGORY_AGE, 18);


$cutoff_time_lunch_from = Config::get('dietary_cutoff_lunch_from');
$cutoff_time_lunch_to= Config::get('dietary_cutoff_lunch_to');
$cutoff_time_dinner_from = Config::get('dietary_cutoff_dinner_from');
$cutoff_time_dinner_to= Config::get('dietary_cutoff_dinner_to');

$objInfo = new Hospital_Admin();
if ($row = $objInfo->getAllHospitalInfo()) {
  $row['hosp_agency'] = strtoupper($row['hosp_agency']);
  $row['hospital_name']   = strtoupper($row['hosp_name']);
}
else {
  $row['hosp_country'] = "Republic of the Philippines";
  $row['hosp_agency']  = "DEPARTMENT OF HEALTH";
  $row['hospital_name']    = "DAVAO MEDICAL CENTER";
  $row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
}


$orfeetotal = 0;
$proffeetotal = 0;
$rowindex = 0;
$data = array();
$updateDiet = false;
a:
	$sqlTime = "SELECT TIME_FORMAT(CURTIME(), '%H:%i') AS CURTIME ";
		$exeTime = $db->GetRow($sqlTime);
		$time = $exeTime['CURTIME'];
	  if($time >= $cutoff_time_lunch_from->value && $time <= $cutoff_time_lunch_to->value) {
      $cutOffTime = 'Lunch';
    }else if($time >= $cutoff_time_dinner_from->value && $time <= $cutoff_time_dinner_to->value) {
      $cutOffTime = 'Dinner';
    }else {
      $cutOffTime = 'BreakFast';
    }

  // $cutOffTime = 'BreakFast'; 

$seg_encounter = new Encounter();
$discharged = new Ward;
$person = new Person;
$nursing  = new NursingNotes;
$notes_obj = new Notes;
$oral_list = array();
$tubefeeding_list = array();
$category=$_GET["diet_type"] ? $_GET["diet_type"] : 'NONE';
if($category==HALAL_LIST){
  $category = 'NONE';
  $religion = "'".ISLAM."','".BAJAO."','".SDA."'";
}elseif ($category==ORAL_LIST) {
  $oral_diet = $notes_obj->getOralDietList();
  while ($oral = $oral_diet->FetchRow()) {
     array_push($oral_list, $oral['diet_code']);
  }
}elseif ($category==TUBEFEEDING) {
 $tubefeeding_diet = $notes_obj->getTubeFeedingList();
  while ($tubefeeding = $tubefeeding_diet->FetchRow()) {
     array_push($tubefeeding_list, $tubefeeding['diet_code']);
  }
}


// var_dump($oral_list);exit();
$dischargedlist = $discharged->getDischargedNursingRounds($ward_nr,$religion);
$nursing_data = $seg_encounter->getNursingRoundsInfo($ward_nr);
$nursing_ward_name = $seg_encounter->getWardName($_GET['ward_nr'])->FetchRow();
$beds = $seg_encounter->getClosedBeds($_GET['ward_nr']);
$nourish_data = $notes_obj->getNourishpatient();
$i=0;
$count= 0;
$diet_list_order = array();
  while ($room = $beds->FetchRow()) {

    $split = explode('/', $room['closed_beds']);
    for($bed_nr=1;$bed_nr <= $room['nr_of_beds'];$bed_nr++){
      if(!in_array($bed_nr, $split)){
        $check = $seg_encounter->checkOccupiedRooms($room['room_nr'], $bed_nr, $_GET['ward_nr'],$religion);       
        // echo "<pre>";
        //   var_dump($seg_encounter->sql);
        //    echo "</pre>";

          while ($row = $check->FetchRow()) {
             $diet_category_list = array();
            $update_diet_name = array();
            $diet_list_type = $row["diet_list"] ? $row["diet_list"] : $row['diet'];
            $diet_type_summary = explode(',', $diet_list_type);   
            
            $update_diet_list = $nursing->UpdateDietList($row['encounter_nr'],$cutOffTime);
            if($category==ORAL_LIST){
              foreach ($oral_list as $key => $value) {
                foreach (array_keys($diet_type_summary, $value) as $key) {
                 unset($diet_type_summary[$key]);
                }
              }
            }elseif ($category==TUBEFEEDING) {
              $count_tubefeeding=0;
              foreach ($tubefeeding_list as $key => $value) {
            
                if(in_array($value, $diet_type_summary)){
                    $count_tubefeeding++;
                }
              }
          }
        
          if((count($diet_type_summary)>0 && $category==ORAL_LIST) || ($count_tubefeeding>0 && $category==TUBEFEEDING) ||($category=='NONE') || ($category==WARD_LIST) || ($category==NOURISH_LIST)) {    
  
            $name = explode(',', $row['uname']);
            if ($row["bedNo"] !=0 AND  !empty($row["RoomName"])) {
              $age = '';
              if(strpos($row["age"], 'months') !== false) $age = str_replace(' months', 'm', $row["age"]);
              elseif(strpos($row["age"], 'months') !== false) $age = str_replace(' month', 'm', $row["age"]);
              elseif(strpos($row["age"], 'years') !== false) $age = str_replace(' years', 'y', $row["age"]);
              elseif(strpos($row["age"], 'year') !== false) $age = str_replace(' year', 'y', $row["age"]);
              elseif(strpos($row["age"], 'days') !== false) $age = str_replace(' days', 'd', $row["age"]);
              elseif(strpos($row["age"], 'day') !== false) $age = str_replace(' day', 'd', $row["age"]);

              if(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
              elseif(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
              elseif(strpos($age, 'months') !== false) $age = str_replace(' months', 'm', $age);
              elseif(strpos($age, 'month') !== false) $age = str_replace(' month', 'm', $age);

              $getReligion = $person->encReligion($row['encounter_nr']);
                 $row['diet_list'] = explode(",", $row['diet_list']);
              $row['b'] = explode(",", $row['b']);
              $row['l'] = explode(",", $row['l']);
              $row['d'] = explode(",", $row['d']);
              $getDietNames = $notes_obj->getDietNames();
       
               while($diet_name_list =  $getDietNames->FetchRow()){
                    array_push($diet_category_list, $diet_name_list['diet_code']);
                    array_push($update_diet_name,$diet_name_list['alt_code']);
                  }


                 for ($j=0; $j < count($diet_category_list); $j++) { 
                    for ($dietlist_counter=0; $dietlist_counter < count($row['diet_list']); $dietlist_counter++) { 
                        if($diet_category_list[$j] == $row['diet_list'][$dietlist_counter]){
                            $row['diet_list'][$dietlist_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($b_counter=0; $b_counter < count($row['b']); $b_counter++) { 
                        if($diet_category_list[$j] == $row['b'][$b_counter]){
                            $row['b'][$b_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($l_counter=0; $l_counter < count($row['l']); $l_counter++) { 
                        if($diet_category_list[$j] == $row['l'][$l_counter]){
                            $row['l'][$l_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($d_counter=0; $d_counter < count($row['d']); $d_counter++) { 
                        if($diet_category_list[$j] == $row['d'][$d_counter]){
                            $row['d'][$d_counter] = $update_diet_name[$j];
                        }
                    }
                  }
              // $row['diet_list'] =str_replace($diet_category_list, $update_diet_name, $row['diet_list']);
              // $row['b'] =str_replace($diet_category_list, $update_diet_name, $row['b']);
              // $row['l'] =str_replace($diet_category_list, $update_diet_name, $row['l']);
              // $row['d'] =str_replace($diet_category_list, $update_diet_name, $row['d']);


              $row['diet_list'] = implode(',', $row['diet_list']);
              $row['b'] = implode(',', $row['b']);
              $row['l'] = implode(',', $row['l']);
              $row['d'] = implode(',', $row['d']);


            if((((int)substr($age, 0, -1) < NO_BMI_AGE) && substr($age,-1) == 'y' ) || (substr($age, -1)=='m') || (substr($age, -1)=='d')){
              $bmi_status = FALSE;
              $bmi_status_category = FALSE;
            }elseif (((int)substr($age, 0, -1) < NO_CATEGORY_AGE) && ((int)substr($age, 0, -1) >= NO_BMI_AGE) && substr($age,-1) == 'y') {
              $bmi_status_category = FALSE;
              $bmi_status = TRUE;
            }else{
              $bmi_status = TRUE;
              $bmi_status_category = TRUE;
            }

             $height = number_format($row['nHeight'],2);
               $weight = number_format($row['nWeight'],2);
               $metric = ( $weight / ($height * $height) * 10000 );
               $row["nBmi"] = round($metric,2);
               $getBMI = $notes_obj->getBMI($row["nBmi"]);
               #var_dump($notes_obj->sql); die();
               if ($getBMI)
                $row['nBmi_name'] = $getBMI;
               else{
                $getBMI = $notes_obj->getBMI2($row["nBmi"]);
                $row['nBmi_name'] = $getBMI;
               }

               if(!$row['nBmi']){
                $row["nBmi"]  = '';
               }

              $data[$i] = array(
                "room_number"             => $row['RoomName'],
                "room_name"               => $row['description'],
                "bed_number"              => $row["bedNo"],
                "first_name"              => $name[1],
                "last_name"               => $name[0],
                "wt"                      => $row["nWeight"],
                "ht"                      => $row["nHeight"],
                "bmi"                     =>($bmi_status ? $row["nBmi"] :"" )."\n".($bmi_status_category ? $row['nBmi_name'] :"" ),
                "age"                     => $age,
                "patient_name"            => strtoupper($row["uname"]),
                "impression_diagnosis"    => ($row["notes"] ? $row["notes"] : ""),  

                 "B"                    =>  ($row["diet_list"] ?  ($row["b"] ? $row["b"]."\n\n".$row["nRemarks"]." " : $row["b"]  )  : $row['diet']."\n\n".$row["nRemarks"]." "),
                 "L"                    =>  ($row["diet_list"] ?  ($row["l"] ? $row["l"]."\n\n".$row["nRemarks"]." " : $row["l"]  ) : $row['diet']."\n\n".$row["nRemarks"]." "),
                 "D"                    =>  ($row["diet_list"] ?  ($row["d"] ? $row["d"]."\n\n".$row["nRemarks"]." " : $row["d"]  ) : $row['diet']."\n\n".$row["nRemarks"]." "),
                "IVF"                     => ($row["IVF"] ? $row["IVF"] : ""),
                "religion"                => ($getReligion ? $getReligion : ""),
                "group_label"             => "ROOM:  "
              );
              $diet_list = $row["diet_list"] ? $row["diet_list"] : $row['diet'];
           
              $diet_summary = explode(',', $diet_list);   
              foreach ($diet_summary as $diet_key => $diet_value) {
                if($diet_value!=''){
                  $diet_list_order[$diet_value]++;
                }       
              }
            }
            $i++;
          } #note

        }
      }
      }
    }
    // exit();
     
    while ($row = $dischargedlist->FetchRow()) 
    {
        $diet_category_list = array();
            $update_diet_name = array();


       $diet_list_type = $row["diet_list"] ? $row["diet_list"] : $row['diet'];
       $diet_type_summary = explode(',', $diet_list_type);   
      $update_diet_list = $nursing->UpdateDietList($row['encounter_nr'],$cutOffTime);
       if($category==ORAL_LIST){
              foreach ($oral_list as $key => $value) {
                foreach (array_keys($diet_type_summary, $value) as $key) {
                 unset($diet_type_summary[$key]);
                }
              }
            }elseif ($category==TUBEFEEDING) {
              $count_tubefeeding=0;
              foreach ($tubefeeding_list as $key => $value) {
            
                if(in_array($value, $diet_type_summary)){
                    $count_tubefeeding++;
                }
              }
          }
        
      if((count($diet_type_summary)>0 && $category==ORAL_LIST) || ($count_tubefeeding>0 && $category==TUBEFEEDING) ||($category=='NONE') || ($category==WARD_LIST) || ($category==NOURISH_LIST)) {    
      if ($row["encounter_nr"] != 0) {
        $age = '';
        if(strpos($row["age"], 'months') !== false) $age = str_replace(' months', 'm', $row["age"]);
        elseif(strpos($row["age"], 'months') !== false) $age = str_replace(' month', 'm', $row["age"]);
        elseif(strpos($row["age"], 'years') !== false) $age = str_replace(' years', 'y', $row["age"]);
        elseif(strpos($row["age"], 'year') !== false) $age = str_replace(' year', 'y', $row["age"]);
        elseif(strpos($row["age"], 'days') !== false) $age = str_replace(' days', 'd', $row["age"]);
        elseif(strpos($row["age"], 'day') !== false) $age = str_replace(' day', 'd', $row["age"]);

        if(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
        elseif(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
        elseif(strpos($age, 'months') !== false) $age = str_replace(' months', 'm', $age);
        elseif(strpos($age, 'month') !== false) $age = str_replace(' month', 'm', $age);

        $getReligion = $person->encReligion($row['encounter_nr']);
          $row['diet_list'] = explode(",", $row['diet_list']);
              $row['b'] = explode(",", $row['b']);
              $row['l'] = explode(",", $row['l']);
              $row['d'] = explode(",", $row['d']);
              $getDietNames = $notes_obj->getDietNames();
       
               while($diet_name_list =  $getDietNames->FetchRow()){
                    array_push($diet_category_list, $diet_name_list['diet_code']);
                    array_push($update_diet_name,$diet_name_list['alt_code']);
                  }


                 for ($j=0; $j < count($diet_category_list); $j++) { 
                    for ($dietlist_counter=0; $dietlist_counter < count($row['diet_list']); $dietlist_counter++) { 
                        if($diet_category_list[$j] == $row['diet_list'][$dietlist_counter]){
                            $row['diet_list'][$dietlist_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($b_counter=0; $b_counter < count($row['b']); $b_counter++) { 
                        if($diet_category_list[$j] == $row['b'][$b_counter]){
                            $row['b'][$b_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($l_counter=0; $l_counter < count($row['l']); $l_counter++) { 
                        if($diet_category_list[$j] == $row['l'][$l_counter]){
                            $row['l'][$l_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($d_counter=0; $d_counter < count($row['d']); $d_counter++) { 
                        if($diet_category_list[$j] == $row['d'][$d_counter]){
                            $row['d'][$d_counter] = $update_diet_name[$j];
                        }
                    }
                  }
              // $row['diet_list'] =str_replace($diet_category_list, $update_diet_name, $row['diet_list']);
              // $row['b'] =str_replace($diet_category_list, $update_diet_name, $row['b']);
              // $row['l'] =str_replace($diet_category_list, $update_diet_name, $row['l']);
              // $row['d'] =str_replace($diet_category_list, $update_diet_name, $row['d']);


              $row['diet_list'] = implode(',', $row['diet_list']);
              $row['b'] = implode(',', $row['b']);
              $row['l'] = implode(',', $row['l']);
              $row['d'] = implode(',', $row['d']);

            if((((int)substr($age, 0, -1) < NO_BMI_AGE) && substr($age,-1) == 'y' ) || (substr($age, -1)=='m') || (substr($age, -1)=='d')){
              $bmi_status = FALSE;
              $bmi_status_category = FALSE;
            }elseif (((int)substr($age, 0, -1) < NO_CATEGORY_AGE) && ((int)substr($age, 0, -1) >= NO_BMI_AGE) && substr($age,-1) == 'y') {
              $bmi_status_category = FALSE;
              $bmi_status = TRUE;
            }else{
              $bmi_status = TRUE;
              $bmi_status_category = TRUE;
            }

             $height = number_format($row['height'],2);
               $weight = number_format($row['weight'],2);
               $metric = ( $weight / ($height * $height) * 10000 );
               $row["nBmi"] = round($metric,2);
               $getBMI = $notes_obj->getBMI($row["nBmi"]);
               #var_dump($notes_obj->sql); die();
               if ($getBMI)
                $row['nBmi_name'] = $getBMI;
               else{
                $getBMI = $notes_obj->getBMI2($row["nBmi"]);
                $row['nBmi_name'] = $getBMI;
               }

               if(!$row['nBmi']){
                $row["nBmi"]  = '';
               }

        $data[$i] = array
          (
          "room_number"             => " ",
          "group_label"             => "MAY GO HOME (MGH) LIST",
          "bed_number"              => " ",
          "hrn"                     => $row["pid"],
          "sex"                     => strtoupper($row["sex"]),
          "age"                     => $age,
          "wt"                      => $row["weight"],
          "ht"                      => $row["height"],
            "bmi"                     => ($bmi_status ? $row["nBmi"] :"" )."\n".($bmi_status_category ? $row['nBmi_name'] :"" ),
          "first_name"              => $row["name_first"],
          "last_name"               => $row["name_last"],
          "impression_diagnosis"    => ($row["notes"] ? $row["notes"] : ""),
          "B"                    => ($row["diet_list"] ?  $row["b"] : $row['diet'])."\n\n".$row["nRemarks"]." ",
          "L"                    => ($row["diet_list"] ?  $row["l"] : $row['diet'])."\n\n".$row["nRemarks"]." ",
          "D"                    => ($row["diet_list"] ?  $row["d"] : $row['diet'])."\n\n".$row["nRemarks"]." ",
          "religion"                => ($getReligion ? $getReligion : ""),
          );
            $diet_list = $row["diet_list"] ? $row["diet_list"] : $row['diet'];
           
              $diet_summary = explode(',', $diet_list);   
              foreach ($diet_summary as $diet_key => $diet_value) {
                if($diet_value!=''){
                  $diet_list_order[$diet_value]++;
                }       
              }
        $i++;
      }
    }
    }

    while ($nourish = $nourish_data->FetchRow()) 
    {
        $diet_category_list = array();
      $update_diet_name = array();
       $diet_list_type = $nourish["diet_list"] ? $nourish["diet_list"] : $nourish['diet'];
       $diet_type_summary = explode(',', $diet_list_type);   
      $update_diet_list = $nursing->UpdateDietList($row['encounter_nr'],$cutOffTime);
      if(($category==NOURISH_LIST)) {    
      if ($nourish["encounter_nr"] != 0) {
        $age = '';
        if(strpos($nourish["age"], 'months') !== false) $age = str_replace(' months', 'm', $nourish["age"]);
        elseif(strpos($nourish["age"], 'months') !== false) $age = str_replace(' month', 'm', $nourish["age"]);
        elseif(strpos($nourish["age"], 'years') !== false) $age = str_replace(' years', 'y', $nourish["age"]);
        elseif(strpos($nourish["age"], 'year') !== false) $age = str_replace(' year', 'y', $nourish["age"]);
        elseif(strpos($nourish["age"], 'days') !== false) $age = str_replace(' days', 'd', $nourish["age"]);
        elseif(strpos($nourish["age"], 'day') !== false) $age = str_replace(' day', 'd', $nourish["age"]);

        if(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
        elseif(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
        elseif(strpos($age, 'months') !== false) $age = str_replace(' months', 'm', $age);
        elseif(strpos($age, 'month') !== false) $age = str_replace(' month', 'm', $age);

        $getReligion = $person->encReligion($nourish['encounter_nr']);

           $nourish['diet_list'] = explode(",", $nourish['diet_list']);
              $nourish['b'] = explode(",", $nourish['b']);
              $nourish['l'] = explode(",", $nourish['l']);
              $nourish['d'] = explode(",", $nourish['d']);
              $getDietNames = $notes_obj->getDietNames();
       
               while($diet_name_list =  $getDietNames->FetchRow()){
                    array_push($diet_category_list, $diet_name_list['diet_code']);
                    array_push($update_diet_name,$diet_name_list['alt_code']);
                  }


                  
                 for ($j=0; $j < count($diet_category_list); $j++) { 
                    for ($dietlist_counter=0; $dietlist_counter < count($nourish['diet_list']); $dietlist_counter++) { 
                        if($diet_category_list[$j] == $nourish['diet_list'][$dietlist_counter]){
                            $nourish['diet_list'][$dietlist_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($b_counter=0; $b_counter < count($nourish['b']); $b_counter++) { 
                        if($diet_category_list[$j] == $nourish['b'][$b_counter]){
                            $nourish['b'][$b_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($l_counter=0; $l_counter < count($nourish['l']); $l_counter++) { 
                        if($diet_category_list[$j] == $nourish['l'][$l_counter]){
                            $nourish['l'][$l_counter] = $update_diet_name[$j];
                        }
                    }

                    for ($d_counter=0; $d_counter < count($nourish['d']); $d_counter++) { 
                        if($diet_category_list[$j] == $nourish['d'][$d_counter]){
                            $nourish['d'][$d_counter] = $update_diet_name[$j];
                        }
                    }
                  }
              // $nourish['diet_list'] =str_replace($diet_category_list, $update_diet_name, $nourish['diet_list']);
              // $nourish['b'] =str_replace($diet_category_list, $update_diet_name, $nourish['b']);
              // $nourish['l'] =str_replace($diet_category_list, $update_diet_name, $nourish['l']);
              // $nourish['d'] =str_replace($diet_category_list, $update_diet_name, $nourish['d']);


              $nourish['diet_list'] = implode(',', $nourish['diet_list']);
              $nourish['b'] = implode(',', $nourish['b']);
              $nourish['l'] = implode(',', $nourish['l']);
              $nourish['d'] = implode(',', $nourish['d']);


           if((((int)substr($age, 0, -1) < NO_BMI_AGE) && substr($age,-1) == 'y' ) || (substr($age, -1)=='m') || (substr($age, -1)=='d')){
              $bmi_status = FALSE;
              $bmi_status_category = FALSE;
            }elseif (((int)substr($age, 0, -1) <= NO_CATEGORY_AGE) && ((int)substr($age, 0, -1) > NO_BMI_AGE) && substr($age,-1) == 'y') {
              $bmi_status_category = FALSE;
              $bmi_status = TRUE;
            }else{
              $bmi_status = TRUE;
              $bmi_status_category = TRUE;
            }

             $height = number_format($nourish['height'],2);
               $weight = number_format($nourish['weight'],2);
               $metric = ( $weight / ($height * $height) * 10000 );
               $nourish["nBmi"] = round($metric,2);
               $getBMI = $notes_obj->getBMI($nourish["nBmi"]);
               #var_dump($notes_obj->sql); die();
               if ($getBMI)
                $nourish['nBmi_name'] = $getBMI;
               else{
                $getBMI = $notes_obj->getBMI2($nourish["nBmi"]);
                $nourish['nBmi_name'] = $getBMI;
               }

               if(!$row['nBmi']){
                $nourish["nBmi"]  = '';
               }

        $data[$i] = array
          (
          "room_number"             => "   ",
          "group_label"             => "NOURISHMENT LIST",
          "bed_number"              => " ",
          "hrn"                     => $nourish["pid"],
          "sex"                     => strtoupper($nourish["sex"]),
          "age"                     => $age,
          "wt"                      => $nourish["weight"],
          "ht"                      => $nourish["height"],
            "bmi"                     => ($bmi_status ? $nourish["nBmi"] :"" )."\n".($bmi_status_category ? $nourish['nBmi_name'] :"" ),
          "first_name"              => $nourish["name_first"],
          "last_name"               => $nourish["name_last"],
          "impression_diagnosis"    => ($nourish["notes"] ? $nourish["notes"] : ""),
          "B"                    => ($nourish["diet_list"] ?  $nourish["b"] : $nourish['diet'])."\n\n".$nourish["nRemarks"]." ",
          "L"                    => ($nourish["diet_list"] ?  $nourish["l"] : $nourish['diet'])."\n\n".$nourish["nRemarks"]." ",
          "D"                    => ($nourish["diet_list"] ?  $nourish["d"] : $nourish['diet'])."\n\n".$nourish["nRemarks"]." ",
          "religion"                => ($getReligion ? $getReligion : ""),
          );
            $diet_list = $nourish["diet_list"] ? $nourish["diet_list"] : $nourish['diet'];
           
              $diet_summary = explode(',', $diet_list);   
              foreach ($diet_summary as $diet_key => $diet_value) {
                if($diet_value!=''){
                  $diet_list_order[$diet_value]++;
                }       
              }
        $i++;
      }
    }
    }

        $count_diet= 0 ;
     foreach ($diet_list_order as $list_key => $list_value) {
            $getDietName = $notes_obj->getNameDiet($list_key);
            $list.= $getDietName['diet_code']."=".$list_value." ";
            $count_diet++;
            if($count_diet==8){
                // $summary = rtrim($list,", ");
                $list.= "\n";
                $count_diet = 0;
            }

      }
if(strtolower($cutOffTime)=="breakfast" && !$updateDiet){
  $updateDiet=true;
  $count_diet=0;
  $list="";
   goto a;
} 
  $params = array(
    'r_doh' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR ."doh.png",
    'r_spmc' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."gui".DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR."dmc_logo.jpg",
    "ward_name"   => $nursing_ward_name['ward_name']." ",
    "room_text"   => " ",
    "date_today"  => date("F d, Y"),
    "dietorder" => $cutOffTime,
    "diet_list"=>rtrim($list,', ')
    );


showReport('Diet_List_noDiag',$params,$data,'PDF');

?>
