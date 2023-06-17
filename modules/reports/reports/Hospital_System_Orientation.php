<?php
/**
 * @author Paulo(4-16-2018)
 */

require_once('roots.php');
require_once $root_path.'include/care_api_classes/class_hospital_admin.php';
require_once $root_path.'include/inc_environment_global.php';
include 'parameters.php';

global $db;

$get_all_km = $db->GetAll("SELECT cu.`name` as name FROM care_personell cp LEFT JOIN care_users cu ON cp.nr = cu.personell_nr WHERE cp.is_KM = 1");

if($km_ihomp != 'others' && $km_ihomp != ''){
    $get_km_name = $db->GetOne("SELECT cu.`name` as name FROM care_personell cp LEFT JOIN care_users cu ON cp.nr = cu.personell_nr WHERE cp.is_KM = 1 AND cp.nr =".$db->qstr($km_ihomp));

    $cond_km = " AND sol.`added_by`=".$db->qstr($get_km_name);

    $params->put('km_name', $get_km_name);

}elseif($km_ihomp == 'others'){
    foreach($get_all_km as $key => $value){
        $kmstaffs[$key] = $value['name'];
    }

    $cond_km = " AND sol.`added_by` NOT IN ('".implode("','", $kmstaffs)."')";

    $params->put('km_name', "OTHERS");
}else {
    $cond_km = '';

    foreach($get_all_km as $key => $value){
         $kmstaffs[$key] = $value['name'];
    }

};


$params->put('date_span',"Period: " . date('F d, Y',$from_date) . " to " . date('F d, Y',$to_date));
$params->put('user',$_SESSION['sess_user_name']);

$cond1 = "DATE(sol.date_of_orientation)
               BETWEEN
                    DATE(".$db->qstr(date('Y-m-d',$from_date)).")
                    AND
                    DATE(".$db->qstr(date('Y-m-d',$to_date)).") ";

switch ($mod_orient) {
    case 'admision':
        $modules_orientation = " AND sol.`module_orientation`='Admission' ";
        break;
    case 'er':
        $modules_orientation = " AND sol.`module_orientation`='ER' ";
        break;
    case 'opd':
        $modules_orientation = " AND sol.`module_orientation`='OPD' ";
        break;
    case 'phs':
        $modules_orientation = " AND sol.`module_orientation`='PHS' ";
        break;
    case 'ipbm':
        $modules_orientation = " AND sol.`module_orientation`='IPBM' ";
        break;
    case 'mr':
        $modules_orientation = " AND sol.`module_orientation`='Medical Records' ";
        break;
    case 'doc':
        $modules_orientation = " AND sol.`module_orientation`='Doctors' ";
        break;
    case 'nursing':
        $modules_orientation = " AND sol.`module_orientation`='Nursing' ";
        break;
    case 'or':
        $modules_orientation = " AND sol.`module_orientation`='OR' ";
        break;
    case 'laboratories':
        $modules_orientation = " AND sol.`module_orientation`='Laboratories' ";
        break;
    case 'Bbank':
        $modules_orientation = " AND sol.`module_orientation`='Blood Bank' ";
        break;
    case 'radiology':
        $modules_orientation = " AND sol.`module_orientation`='Radiology' ";
        break;
    case 'obgyne':
        $modules_orientation = " AND sol.`module_orientation`='Ob Gyne' ";
        break;
    case 'pharmacy':
        $modules_orientation = " AND sol.`module_orientation`='Pharmacy' ";
        break;
    case 'dialysis':
        $modules_orientation = " AND sol.`module_orientation`='Dialysis' ";
        break;
    case 'sservice':
        $modules_orientation = " AND sol.`module_orientation`='Social Service' ";
        break;
    case 'pdpu':
        $modules_orientation = " AND sol.`module_orientation`='PDPU' ";
        break;
    case 'hssc':
        $modules_orientation = " AND sol.`module_orientation`='HSSC' ";
        break;
    case 'billing':
        $modules_orientation = " AND sol.`module_orientation`='Billing' ";
        break;
    case 'pad':
        $modules_orientation = " AND sol.`module_orientation`='PAD' ";
        break;
    case 'eclaims':
        $modules_orientation = " AND sol.`module_orientation`='eClaims' ";
        break;
    case 'cashier':
        $modules_orientation = " AND sol.`module_orientation`='Cashier' ";
        break;
    case 'reports':
        $modules_orientation = " AND sol.`module_orientation`='Reports' ";
        break;
    case 'sysadmin':
        $modules_orientation = " AND sol.`module_orientation`='System Admin' ";
        break;
    case 'special':
        $modules_orientation = " AND sol.`module_orientation`='Special Tools' ";
    default:    
        $modules_orientation = '';
        break;
}
#end of count

$sql = "SELECT fn_get_person_lastname_first (cp.`pid`) AS name,
                cp.`job_position` AS position,
                fn_get_department_name(cpa.`location_nr`) AS dept,
                sol.`date_of_orientation` AS orientation_date,
                TIME_FORMAT(
                    sol.`starting_time_of_orientation`,
                    '%h:%i %p'
                ) AS time_fr,
                TIME_FORMAT(
                    sol.`end_time_of_orientation`,
                '   %h:%i %p'
                ) AS time_to,
                IF(
                    (CAST(sol.venue AS DECIMAL) <> 0),
                    (SELECT 
                      orientation_venue_name 
                    FROM
                      seg_orientation_venue 
                    WHERE orientation_venue_id = sol.`venue`),
                    sol.`venue`
                  ) AS venue,
                sol.`module_orientation` AS module,
                sol.`title_orientation` AS title,
                sol.`added_by` AS conduct 
        FROM
        seg_orientation_list AS sol 
        LEFT JOIN care_personell AS cp 
            ON sol.`employee_number` = cp.`nr` 
        LEFT JOIN care_personell_assignment AS cpa
            ON sol.`employee_number` = cpa.`personell_nr`
        WHERE ".$cond1.$cond_km.$modules_orientation." 
        AND sol.`is_deleted` = 0 ORDER BY sol.`date_of_orientation` ASC";

$res = $db->Execute($sql);
$i = 0;

$get_signatory = $db->GetAll("SELECT fn_get_personell_firstname_last (ss.`personell_nr`) AS signatory_name, ss.signatory_title FROM seg_signatory ss LEFT JOIN care_personell cp ON ss.personell_nr = cp.nr WHERE ss.document_code=".$db->qstr('hosp_sys_orientation'));

$params->put('signatory', ucwords(strtolower($get_signatory[0]['signatory_name'])));
$params->put('signatory_title', $get_signatory[0]['signatory_title']);

if($res){
    if($res->RecordCount() > 0){
        while($row = $res->FetchRow()){
            if(!empty($row['dept'])) {
                $dept = $row['dept'];
            }else {
                $dept = 'No Assign Department';
            }

            if(!empty($row['position']) && trim($row['position'])) {
                $position = mb_strtoupper($row['position']);
            }else {
                $position = 'No Assign Position';
            }

            switch($row['action_taken']){
                case 'activated':
                    $status = 'Active';
                    break;
                case 'deactivated':
                    $status = 'Inactive';
                    break;
                case 'deleted':
                    $status = 'Deleted';
                    break;
            }

            if(!$km_ihomp){
                if(in_array($row['conduct'], $kmstaffs)){
                    $count[$row['conduct']]++;
                }else $count['OTHERS']++;
            }


            $data[$i] = array(
                'num' => $i + 1,
                'name' => utf8_decode(trim($row['name'])),
                'position' => $position,
                'dept' =>$dept,
                'orientation_date' => $row['orientation_date'] ,
                'time_fr' => $row['time_fr'],
                'time_to' => $row['time_to'],
                'venue' => $row['venue'],
                'module' => $row['module'],
                'title' => utf8_decode(mb_strtoupper($row['title'])),
                'conduct' => $row['conduct']
            );

            $i++;
        }

        $sub_totals = '';
        if(!$km_ihomp){
            foreach ($kmstaffs as $key => $value) {
                $sub_totals .= "Sub Total (".$value.") : ".$count[$value]."\n";
            }

            $sub_totals .= "Sub Total (OTHERS) : ".$count['OTHERS']."\n";
            $sub_totals .= "Total no. : ".$i;
        }
        
        $params->put('sub_totals',$sub_totals);
        $params->put('totalno',$i);
    }
    else{
        $data[0] = array();
    }
}