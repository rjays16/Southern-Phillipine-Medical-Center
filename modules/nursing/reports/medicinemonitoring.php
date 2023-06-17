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
$person = new Person;

$dischargedlist = $discharged->getDischargedNursingRounds($ward_nr);
$nursing_data = $seg_encounter->getNursingRoundsInfo($ward_nr);
$nursing_ward_name = $seg_encounter->getWardName($_GET['ward_nr'])->FetchRow();
$beds = $seg_encounter->getClosedBeds($_GET['ward_nr']);
$i=0;

    while ($room = $beds->FetchRow()) {
      $split = explode('/', $room['closed_beds']);
      for($bed_nr=1;$bed_nr <= $room['nr_of_beds'];$bed_nr++){
        if(!in_array($bed_nr, $split)){
          $check = $seg_encounter->checkOccupiedRooms($room['room_nr'], $bed_nr, $_GET['ward_nr']);

          while ($row = $check->FetchRow()) {
          if ($row["bedNo"] !=0 AND  !empty($row["RoomName"])) {
            $age = '';
            if(strpos($row["age"], 'months') !== false){
              $age = str_replace(' months', '', $row["age"]);
            }elseif(strpos($row["age"], 'years') !== false){
              $age = str_replace(' years', '', $row["age"]);
            }elseif(strpos($row["age"], 'days') !== false){
              $age = str_replace(' days', '', $row["age"]);
            }

            if(strpos($age, 'days') !== false){
              $age = str_replace(' days', '', $age);
            }elseif(strpos($age, 'months') !== false){
              $age = str_replace(' months', '', $age);
            }

            $getReligion = $person->encReligion($row['encounter_nr']);

            $data[$i] = array(
              "room_number"             => $row['RoomName']."-".$row["bedNo"],
              "room_name"               => $row['description'],
              "first_name"              => $row["name_first"],
              "last_name"               => $row["name_last"],
              "wt"                      => $row["nWeight"],
              "ht"                      => $row["nHeight"],
              "age"                     => $age,
              "patient_name"            => strtoupper($row["uname"]),
              "impression_diagnosis"    => ($row["notes"] ? $row["notes"] : ""),  
              "diet"                    => ($row["diet"] ? $row["diet"] : ""),
              "IVF"                     => ($row["IVF"] ? $row["IVF"] : ""),
              "religion"                => ($getReligion ? $getReligion : ""),
              "other_gadgets"           => " ",
              "problem_meds_msg_others" => " ",
              "actions"                 => " "
            );
          }
          $i++;
        }
      }
    }
  }

    while ($row = $dischargedlist->FetchRow()) 
    {
      if ($row["encounter_nr"] != 0) {
        $data[$i] = array
        (
          "room_number"             => "MGH",
          "patient_name"            => strtoupper($row["uname"])
        );

        $i++;
      }
    }


  // echo "<pre>";
  // print_r($nursing_data);
  // die();
//     
    // var_dump($data[0]);die();
  $params = array(
    'r_doh' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR ."doh.png",
    'r_spmc' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."gui".DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR."dmc_logo.jpg",
    "ward_name"   => $nursing_ward_name['ward_name']." ",
    "room_text"   => "ROOM: ",
    "date_today"  => date("m/d/y")
    );



showReport('Medicine_Monitoring',$params,$data,'PDF'); 

?>
