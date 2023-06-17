<?php
/**
 * @author Leira - 02/07/2018
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

define('ER_CONSULTATION', '1');

$params->put('header1', "REPUBLIC OF THE PHILIPPINES");
$params->put('header2', "DEPARTMENT OF HEALTH");
$params->put('header3', "Southern Philippines Medical Center");
$params->put('header4', "J.P. Laurel Avenue, Davao City");
$params->put('title',"HOSPITAL REFERRAL (In-Referral)");
$params->put('date_span',"Period: " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));

$cond1 = "DATE(ce.`encounter_date`)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";
// var_dump(date('Y-m-d H:i:s', $from_date));die();
$cond2 = " AND ce.`encounter_type` = " .$db->qstr(ER_CONSULTATION);
$sql = "SELECT 
  ce.`encounter_date` AS pDate,
  CONCAT(
    COALESCE(cp.`name_last`,''),
    ',',
    COALESCE(cp.`name_first`,''),
    ',',
    COALESCE(cp.`name_middle`,'')
  ) AS name,
  IF(
    fn_calculate_age (NOW(), cp.date_birth),
    fn_get_age (NOW(), cp.date_birth),
    age
  ) AS age,
  UPPER(cp.`sex`) AS sex,
  `fn_get_complete_address2`(cp.`pid`) AS address,
  ce.`referrer_diagnosis` AS diagnosis,
  ce.`referrer_dr` AS referrer,
  ce.`referrer_institution` AS institution,
  ce.`referrer_notes` AS notes
FROM
  care_person AS cp 
    LEFT JOIN care_encounter AS ce 
    ON ce.`pid` = cp.`pid`
    WHERE ".$cond1.$cond2." AND (ce.`referrer_dr` != '' OR ce.`referrer_institution` != '' )
    ORDER BY ce.`encounter_date` DESC";
// WHERE ".$cond1." AND (ce.`referrer_dr` != '' OR ce.`referrer_institution` != '' 
// OR LENGTH(REPLACE(ce.`referrer_diagnosis`, ' ', ''))>0 OR LENGTH(REPLACE(ce.`referrer_notes`, ' ', ''))>0
// )
// die($sql);
$res = $db->Execute($sql);
$i = 0;

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){

            if (!empty($row['diagnosis']) || 
                !empty($row['institution']) || 
                !empty($row['notes']) || 
                !empty($row['referrer']) ) 
            {
                $clinical = $row['diagnosis'];

                $institution = $row['institution'];

                if (!empty($row['referrer'])) {

                    $institution = $row['institution'] . ' / ' . $row['referrer'];

                    if (trim($row['institution']) == ''){
                        $institution = $row['referrer'];
                    } 

                    if (trim($row['referrer']) == '') {
                        $institution = $row['institution'];
                    }
                }

                if ( !empty($row['notes']) ) {
                    $clinical = $row['diagnosis'] . ' / ' . $row['notes'];
                    
                    if (trim($row['diagnosis']) == ''){
                        $clinical = $row['notes'];
                    } 

                    if (trim($row['notes']) == '') {
                        $clinical = $row['diagnosis'];
                    }

                }




                $data[$i] = array(
                    'Date' => date('m/d/Y', strtotime($row['pDate'])),
                    'Name' => utf8_decode(trim($row['name'])),
                    'Age' => $row['age'],
                    'Sex' => $row['sex'],
                    'Address' => utf8_decode(trim($row['address'])),
                    'Clinical' => $clinical,
                    'Institution' => utf8_decode(trim($institution))
                );


            }

            $i++;
        }
    }
    else{
        $data = array(
            array(
                'Date' => 'No Data',
                'Name' => 'No Data',
                'Age' => 'No Data',
                'Sex' => 'No Data',
                'Address' => 'No Data',
                'Clinical' => 'No Data',
                'Referral' => 'No Data',
                'Institution' => 'No Data',
                'Physician' => 'No Data',
            )
        );
    }
}
else{
    $data = array(
        array(
            'Date' => 'No Data',
            'Name' => 'No Data',
            'Age' => 'No Data',
            'Sex' => 'No Data',
            'Address' => 'No Data',
            'Clinical' => 'No Data',
            'Referral' => 'No Data',
            'Institution' => 'No Data',
            'Physician' => 'No Data',
        )
    );
}

