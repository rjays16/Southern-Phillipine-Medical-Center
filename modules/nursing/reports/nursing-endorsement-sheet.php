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
$shift1 = date_format(date_create($shift_data['time_from']),'ga');
$shift2 = date_format(date_create($shift_data['time_to']),'ga');


$beds = $seg_encounter->getClosedBeds($_GET['ward_nr']);
$nursing_ward_name = $seg_encounter->getWardName($_GET['ward_nr'])->FetchRow();

    while ($room = $beds->FetchRow()) {
        $split = explode('/', $room['closed_beds']);
        for($bed_nr=1;$bed_nr <= $room['nr_of_beds'];$bed_nr++){
            if(!in_array($bed_nr, $split)){
                $check = $seg_encounter->getEndorsementList($room['room_nr'], $bed_nr, $_GET['ward_nr']);
                // var_dump("<pre>");
                // var_dump($seg_encounter->sql);
                // var_dump("</pre>");

                if($check->RecordCount() > 0 ){
                    while ($row = $check->FetchRow()) {
                        if($bed_nr == $row['bedNo']){
                            if ($row["bedNo"] !=0 AND  !empty($row["RoomName"])) {
                                // $age = '';

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

                                    "first_name"             => $row['name_first'],
                                    "middle_name"             => $row['name_middle'],
                                    "last_name"             => $row['name_last'],
                                    "patient_hrn"             => $row['pid'],
                                    "age"             => $row['age'],
                                    "gender"             => strtoupper($row["gender"]),
                                    "height"             => $row['nHeight'],
                                    "weight"             => $row['nWeight'],
                                    "full_diagnosis"       =>($row["notes"] ? $row["notes"] : ""),
                                    "bed_num"             => $row['bedNo'],
                                    "dr_name"             => $row['dr_name'],
                                    "services"             => $row['services'],
                                    "diet"                    => ($row["diet_list"] ? $row["diet_list"] : $row['diet'])."\n".$row["nRemarks"]." ",
                                    "IVF"                     => $row["IVF"],
                                    "gadget"                     => $row["other"],
                                    "diagnostic"                     => $row["diagnostic"],
                                    "special"                     => $row["special"],
                                    "vs"                     => $row["vs"],
                                    "additional"                     => $row["additional"],
                                  );
                            }
                        }
                    }
                }else{
                    $data[] = array
                              (
                                "first_name"             =>"",
                                "middle_name"             =>"",
                                "first_name"             =>"",
                                "last_name"             =>"",
                                "age"             =>"",
                                "gender"             =>"",
                                "height"             =>"",
                                "weight"             =>"",
                                "full_diagnosis"       =>"",
                                "bed_num"             =>"",
                                "dept_br"             =>"",
                                "services"             =>"",
                                "diet"             =>"",
                                "IVF"             =>"",
                                "gadget"             =>"",
                                "diagnostic"             =>"",
                                "special"             =>"",
                                "vs"             =>"",
                                "additional"             =>"",
                           
                              );
                }
            }
        }
        
        #die();
    }



  $params = array(
    'doh' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR ."doh.png",
    'dmc_logo' => dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR ."gui".DIRECTORY_SEPARATOR ."img".DIRECTORY_SEPARATOR."logos".DIRECTORY_SEPARATOR."dmc_logo.jpg",
    "ward_name"   => $nursing_ward_name['ward_name']." ",
    "room_text"   => "",
    "date_today"  => date("F d, Y"),
    "shift"       => $shift1."-".$shift2,
    "nod_here"         => " "
    );

showReport('Endorsement_sheet',$params,$data,'PDF'); 

?>
