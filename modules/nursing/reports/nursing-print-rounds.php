<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('roots.php');
require_once($root_path.'include/inc_jasperReporting.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_ward.php');

global $db;
$pers_obj = new Personell;

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

$seg_encounter = new Encounter();
$discharged = new Ward;

$shift_data = $seg_encounter->getShiftTime($time);
$shift_from =date('Y-m-d')." ".$shift_data['shift_form'];
$shift_to = date('Y-m-d')." ".$shift_data['shift_to'];
$shift1 =$shift_data['time_from'];
$shift2 = $shift_data['time_to'];

$dischargedlist = $discharged->getDischargedNursingRounds($ward_nr);
// $nursing_data = $seg_encounter->getNursingRoundsInfo($ward_nr);
$beds = $seg_encounter->getClosedBeds($_GET['ward_nr']);
$nursing_ward_name = $seg_encounter->getWardName($_GET['ward_nr'])->FetchRow();

    while ($room = $beds->FetchRow()) {
        $split = explode('/', $room['closed_beds']);
        for($bed_nr=1;$bed_nr <= $room['nr_of_beds'];$bed_nr++){
            if(!in_array($bed_nr, $split)){
                $check = $seg_encounter->checkOccupiedRooms($room['room_nr'], $bed_nr, $_GET['ward_nr']);
                if($check->RecordCount() > 0 ){
                    while ($row = $check->FetchRow()) {
                        if($bed_nr == $row['bedNo']){
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
                                
                                $data[] = array
                                  (
                                  "room_number"             => $row['RoomName'],
                                  "room_name"               => ucwords($row['description']),
                                  "bed_number"              => $row["bedNo"],
                                  "hrn"                     => $row["pid"],
                                  "age"                     => $age ." / ".strtoupper($row["gender"]),
                                  "patient_name"            => strtoupper($row["uname"]),
                                  "impression_diagnosis"    => ($row["notes"] ? $row["notes"] : ""),  
                                  "diet"                    =>  ($row["diet_list"] ? $row["diet_list"] : $row['diet'])."\n".$row["nRemarks"]." ",
                                  "IVF"                     => ($row["IVF"] ? $row["IVF"] : ""),
                                  "available_meds"          => ($row["avail_meds"] ? $row["avail_meds"] : ""),
                                  "other_gadgets"           => ($row["gadgets"] ? $row["gadgets"] : ""),
                                  "problem_meds_msg_others" => ($row["problems"] ? $row["problems"] : ""),
                                  "actions"                 => ($row["actions"] ? $row["actions"] : ""),
                                  "room_text1"              => "ROOM: "
                                  );
                            }
                        }                
                    }
                }else{
                    $data[] = array
                              (
                              "room_number"             => $room['room_nr'],
                              "room_name"               => ucwords($room['info']),
                              "bed_number"              => $bed_nr,
                              "hrn"                     => "",
                              "age"                     => "",
                              "patient_name"            => "",
                              "impression_diagnosis"    => "",  
                              "diet"                    => "",
                              "IVF"                     => " ",
                              "available_meds"          => " ",
                              "other_gadgets"           => " ",
                              "problem_meds_msg_others" => " ",
                              "actions"                 => " ",
                              "room_text1"              => "ROOM: "
                              );
                }
            }
        }
    }

    if($dischargedlist->RecordCount()){
      while ($row = $dischargedlist->FetchRow()) 
      {
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

          $data[] = array
            (
            "room_number"             => " ",
            "discharge_list_label"    => "MAY GO HOME (MGH) LIST",
            "bed_number"              => " ",
            "hrn"                     => $row["pid"],
            "age"                     => $age." / ".strtoupper($row["sex"]),
            "patient_name"            => $row["uname"],
            "impression_diagnosis"    => ($row["notes"] ? $row["notes"] : ""),
            "diet"                    =>  ($row["diet_list"] ? $row["diet_list"] : $row['diet'])."\n".$row["nRemarks"]." ",
            "IVF"                     => ($row["IVF"] ? $row["IVF"] : ""),
            "available_meds"          => ($row["avail_meds"] ? $row["avail_meds"] : ""),
            "other_gadgets"           => ($row["gadgets"] ? $row["gadgets"] : ""),
            "problem_meds_msg_others" => ($row["problems"] ? $row["problems"] : ""),
            "actions"                 => ($row["actions"] ? $row["actions"] : ""),
            "room_text1"              => ""
            );
          $i++;
        }
      }
    }

  $params = array(
    'r_doh' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR ."doh.png",
    'r_spmc' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."gui".DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR."dmc_logo.jpg",
    "ward_name"   => $nursing_ward_name['ward_name']." ",
    "room_text"   => "",
    "date_today"  => date("F d, Y"),
    "shift"       => $shift1."-".$shift2,
    "nod_here"         => " "
    );

showReport('Nursing_Rounds_2',$params,$data,'PDF'); 

?>
