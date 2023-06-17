<?php
/**
 * @author 
 */
// Leira - 1/24/2018
require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';
define('PHIC', '18');

global $db;


$params->put('title', "PHIC Accreditation with Expiry Date");
$params->put('title1', "REPUBLIC OF THE PHILIPPINES");
$params->put('title2', "DEPARTMENT OF HEALTH");
$params->put('title3', "Center of Health Development-Southern Mindanao");
$params->put('title4', "Southern Philippines Medical Center");
$params->put('title5', "J.P. Laurel Avenue, Davao City");
$params->put('date_span',"". date('F d, Y',$from_date) . " - " . date('F d, Y',$to_date));


$get_department_name =$db->GetOne("SELECT name_formal from care_department where nr=".$db->qstr($value));
// $res1 = $db->Execute($get_department_name);
// die(var_dump($res1));

$sql2 = "SELECT nr from care_department where parent_dept_nr =".$db->qstr($value);
$arr=array();
$exe = $db->Execute($sql2);

if($exe){
  if($exe->RecordCount() > 0){
    while($rc = $exe->FetchRow()){
      array_push($arr, $rc['nr']);
    }
    // die($a[1]);
    $getData = implode(",",$arr);
    // var_dump($z);die()
  }
}
// Leira 02/08/2018
if (!isset($value)||$value=='all') {
   $get_department_name = "All";
#  $where[] = "sec.is_deleted NOT IN ('1')";
}else{
  // if(!empty($value)){
  //   $get_dept = "cpa.`location_nr` IN((SELECT `nr` FROM care_department WHERE `parent_dept_nr` = ".$db->qstr($value).") )AND ";
  // }
  if(!empty($value)){
    $get_dept = "cpa.`location_nr` IN(".$getData.",".$value.")AND ";
  }
  else{
    $get_department_name ='';
  }
}


// End 02/08/2018
  $params->put('department_name', $get_department_name);



$cond1 = "DATE(expiration)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
// var_dump($value);die();

$sql = "SELECT 
  sda.`accreditation_nr` AS accr_no,
  sda.`expiration` AS exp_date,
  `fn_get_person_lastname_first` (cp.`pid`) AS full_name,
  cpl.`job_position`,
  IF(
    cpa.`status` != 'deleted',
    (SELECT 
      d.name_formal 
    FROM
      care_department AS d 
    WHERE d.nr =
      (SELECT 
        s.location_nr 
      FROM
        `care_personell_assignment`  AS s
      WHERE s.personell_nr = (cpa.`personell_nr`) LIMIT 1)),
    ''
  ) AS department
FROM
  seg_dr_accreditation AS sda 
  LEFT JOIN care_personell AS cpl 
    ON sda.`dr_nr` = cpl.`nr` 
  INNER JOIN care_person AS cp 
    ON cp.`pid` = cpl.`pid` 
  LEFT JOIN care_personell_assignment AS cpa 
    ON cpl.`nr` = cpa.`personell_nr` 
  LEFT JOIN care_department AS cd 
    ON cd.`parent_dept_nr` = cpa.`nr`
    WHERE ".$get_dept.$cond1." AND sda.`hcare_id` = ".PHIC."
    GROUP BY cp.`pid`,sda.`expiration` 
ORDER BY full_name ASC ";
// die(var_dump($sql));

$res = $db->Execute($sql);

$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){
            $data[$i] = array(
                'no' => $i + 1,
                'full_name' => utf8_decode(trim($row['full_name'])),
                'position' => $row['job_position'],
                'accreditation' => $row['accr_no'],
                'expiry' => date('m/d/Y', strtotime($row['exp_date'])),  
                'department' => $row['department'],                            
            );

            $i++;
        }
    }
    else{
        $data = array(
            array(
                'no' => 'No Data',
                'full_name' => 'No Data',
                'position' => 'No Data',
                'accreditation' => 'No Data',
                'expiry' => 'No Data',
                'department' => 'No Data',
            )
        );
    }
}
else{
    $data = array(
        array(
            	'no' => 'No Data',
                'full_name' => 'No Data',
                'position' => 'No Data',
                'accreditation' => 'No Data',
                'expiry' => 'No Data',
                'department' => 'No Data',
        )
    );
}


