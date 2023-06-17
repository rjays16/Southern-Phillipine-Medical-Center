<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require "{$root_path}include/inc_environment_global.php";
require "{$root_path}classes/json/json.php";
require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj = new Ward;

global $db;

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json; charset=ISO-8859-1");

define(IPBMIPD_enc, 13);
define(IPBMOPD_enc, 14);

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$searchkey = $_REQUEST['search'];

$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
    'date_received' => 'date_received',
);

$sortName = $_REQUEST['sort'];

if (!$sortName || !array_key_exists($sortName, $sortMap))
    $sortName = 'date_received';

$filters = array(
    'sort' => $sortMap[$sortName]." ".$sortDir
);
$data = array();
if(is_array($filters))
{
    foreach ($filters as $i=>$v) {
        switch (strtolower($i)) {
            case 'sort': $sort_sql = $v; break;
        }
    }
}

# convert * and ? to % and &
$searchkey=strtr($searchkey,'*?','%_');
$searchkey=trim($searchkey);
#$suchwort=$searchkey;
$searchkey = str_replace("^","'",$searchkey);
$suchwort=addslashes($searchkey);

$date_now = " AND  h.date_update 
                    BETWEEN 
                        DATE_FORMAT(CONCAT(STR_TO_DATE(DATE(NOW()), '%Y-%m-%d'), ' 00:00:00'),'%Y-%m-%d %H:%i:%s') 
                    AND 
                        DATE_FORMAT(CONCAT(STR_TO_DATE(DATE(NOW()), '%Y-%m-%d'), ' 23:59:59'),'%Y-%m-%d %H:%i:%s') ";

if(is_numeric($suchwort)) {
    $pid = $suchwort;   
    $sql2_cond = " AND h.pid = ".$db->qstr($pid);
} else {

    if(stristr($searchkey, '/') === FALSE){

        # Try to detect if searchkey is composite of first name + last name
        if(stristr($searchkey,',')){
                $lastnamefirst=TRUE;
        }else{
                $lastnamefirst=FALSE;
        }

        $cbuffer=explode(',',$searchkey);

        # Remove empty variables
        for($x=0;$x<sizeof($cbuffer);$x++){
                $cbuffer[$x]=trim($cbuffer[$x]);
                if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
        }

        # Arrange the values, ln= lastname, fn=first name, rd = request date
        if($lastnamefirst){
                $fn=$comp[1];
                $ln=$comp[0];
                $rd=$comp[2];
        }else{
                $fn=$comp[0];
                $ln=$comp[1];
                $rd=$comp[2];
        }
        
        $sql2_cond =" AND (p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%' AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%')";
    
    }else{

        if (preg_match("/(0[1-9]|1[012])\/(0[1-9]|[12]\d|3[01])\/(2\d{2})/",$searchkey)){

            $searchdate = date('Y-m-d',strtotime($searchkey));

            /*$sql2_cond = " AND  h.date_update 
                                BETWEEN 
                                    DATE_FORMAT(CONCAT(STR_TO_DATE(".$db->qstr($searchdate).", '%Y-%m-%d'), ' 00:00:00'),'%Y-%m-%d %H:%i:%s') 
                                AND 
                                    DATE_FORMAT(CONCAT(STR_TO_DATE(".$db->qstr($searchdate).", '%Y-%m-%d'), ' 23:59:59'),'%Y-%m-%d %H:%i:%s') ";*/

            $sql2_cond = " AND  h.date_update
                                BETWEEN 
                                    DATE_FORMAT(CONCAT(STR_TO_DATE(".$db->qstr($searchdate).", '%Y-%m-%d'), ' 00:00:00'),'%Y-%m-%d %H:%i:%s') 
                                AND 
                                    DATE_FORMAT(CONCAT(STR_TO_DATE(".$db->qstr($searchdate).", '%Y-%m-%d'), ' 23:59:59'),'%Y-%m-%d %H:%i:%s') ";                        
        }else{
            $sql2_cond = $date_now;
        }
    }
    

}
    

if ($searchkey){
    $query = "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
                s.encounter_nr, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, 
                e.encounter_type, h.pid, 
                fn_get_person_name(h.pid) AS patient, IF(fn_calculate_age(IF(s.serv_dt,s.serv_dt,DATE(h.date_update)),p.date_birth),fn_get_age(IF(s.serv_dt,s.serv_dt,DATE(h.date_update)),p.date_birth),age) AS age, 
                UPPER(p.sex) AS sex, o.refno, h.lis_order_no, 
                IF(fn_get_labtest_request_all(o.refno)<>'', fn_get_labtest_request_all(o.refno), CONCAT('MANUALLY ENCODED with Order No. ', h.lis_order_no)) AS services, 
                o.refno, sr.nth_take, sr.service_code 
                FROM seg_hl7_hclab_msg_receipt h
                LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=h.lis_order_no
                LEFT JOIN seg_lab_serv_serial sr ON sr.refno=o.refno AND sr.lis_order_no=o.lis_order_no 
                INNER JOIN care_person p ON p.pid=h.pid
                LEFT JOIN seg_lab_serv s ON s.refno=o.refno LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr
                WHERE s.status NOT IN ('deleted','hidden','inactive','void')" 
                . $sql2_cond .
                " AND (h.`date_update` < DATE_FORMAT(STR_TO_DATE('2015-12-14', '%Y-%m-%d'), '%Y-%m-%d') 
                       OR h.`date_update` > DATE_FORMAT(STR_TO_DATE('2015-12-20', '%Y-%m-%d'), '%Y-%m-%d')) "
                #' AND (h.`date_update` < CAST("2015-12-14" AS DATE) or h.`date_update` > CAST("2015-12-20" AS DATE)) '
                ;            
    #echo $query;

    if($sort_sql) {
        $query.=" ORDER BY h.date_update DESC";
    }
    if($maxRows) {
        $query.=" LIMIT $offset, $maxRows";
    }

    #echo $query;
    $db->SetFetchMode(ADODB_FETCH_ASSOC);
    $rs = $db->Execute($query);


    $data = Array();
    if ($rs !== false){
        $total = 0;
        $total = $db->GetOne("SELECT FOUND_ROWS()");
        $rows = $rs->GetRows();
        foreach ($rows as $row){
                if ($row['nth_take']==1){
                   $services = $row['services'].'<font color="BLUE"> (First Take)</font>'; 
                }elseif ($row['nth_take'] > 1){
                   $service_code = $db->qstr($row['service_code']); 
                   $sql_l = "SELECT name FROM seg_lab_services WHERE service_code=$service_code"; 
                   $services = $db->GetOne($sql_l);
                   
                   switch($row['nth_take']){
                        case '1' :  
                                    $nth_take = 'First'; 
                                    break;
                        case '2' :  
                                    $nth_take = 'Second'; 
                                    break;
                        case '3' :  
                                    $nth_take = 'Third'; 
                                    break;
                        case '4' :  
                                    $nth_take = 'Fourth'; 
                                    break;
                        case '5' :  
                                    $nth_take = 'Fift'; 
                                    break;
                        case '6' :  
                                    $nth_take = 'Sixth'; 
                                    break;
                        case '7' :  
                                    $nth_take = 'Seventh'; 
                                    break;
                        case '8' :  
                                    $nth_take = 'Eighth'; 
                                    break;
                        case '9' :  
                                    $nth_take = 'Ninth'; 
                                    break;
                        case '10' : 
                                    $nth_take = 'Tenth'; 
                                    break;
                    }

                   $services =  $row['services'].'<font color="BLUE"> ('.$nth_take.' Take)</font>';
                }else{
                   $services = $row['services'];
                }
                
                $filename = $row["pid"].'_'.$row["lis_order_no"].'.pdf';
                
                $withresult = 0;
                if ($filename)
                    $withresult = 1;
                    
            if ($row['encounter_type']==1){
                $enctype = "ERPx";
                $location = "EMERGENCY ROOM";
            }elseif (($row['encounter_type']==2)||($row['encounter_type']==5)||($row['encounter_type']==IPBMOPD_enc)){
                if ($row['encounter_type']==2)
                    $enctype = "OPDx";
                elseif ($row['encounter_type']==IPBMOPD_enc)
                    $enctype = "OPDx (IPBM)";
                else
                    $enctype = "PHSx";

                $dept = $dept_obj->getDeptAllInfo($row['current_dept_nr']);
                $location = strtoupper(strtolower(stripslashes($dept['name_formal'])));
            }elseif (($row['encounter_type']==3)||($row['encounter_type']==4)||($row['encounter_type']==IPBMIPD_enc)){
                if ($row['encounter_type']==3)
                        $enctype = "INPx (ER)";
                elseif ($row['encounter_type']==4)
                        $enctype = "INPx (OPD)";
                elseif ($row['encounter_type']==IPBMIPD_enc)
                        $enctype = "INPx (IPBM)";

                $ward = $ward_obj->getWardInfo($row['current_ward_nr']);
                $location = strtoupper(strtolower(stripslashes($ward['ward_id'])))." Rm # : ".$row['current_room_nr'];
            # Added by James 2/27/2014
            }elseif ($row['encounter_type']==6){
                $enctype = "IC";
                $location = "Industrial Clinic";
            }else{
                $enctype = "WPx";
                $location = 'WALK-IN';
            }   

            #edited by VAS 01/13/2017
            #remove seg_hl7_file_received in the query
            $sql_date = "SELECT h.date_update, h.date_update as request_date
                            FROM seg_hl7_hclab_msg_receipt h
                            #INNER JOIN seg_hl7_file_received f ON f.filename=h.filename
                            WHERE h.pid = ".$db->qstr($row["pid"])." 
                            AND h.lis_order_no=".$db->qstr($row["lis_order_no"])." LIMIT 1";
            $row_dt = $db->GetRow($sql_date); 
            
            $data[] = array(
                'date' => nl2br(date("M-d-Y\nh:ia", strtotime($row_dt["request_date"]))),
                'service' => $services ,
                'refno' => ($row["refno"]) ? $row["refno"] : 'Manual',
                'pid' => $row["pid"],
                'lis_order_no' => $row["lis_order_no"],
                'filename' => $filename,
                'patient' => $row["patient"],
                'age' => $row["age"],
                'sex' => $row["sex"],
                'patient_type' => ($row["refno"]) ? $enctype : '',
                'location' => ($row["refno"]) ? $location : '',
                'withresult' => $withresult
            );
        }
    }
}

$response = array(
    'currentPage'=>$page,
    'total'=>$total,
    'data'=>$data
 );

$json = new Services_JSON;
print $json->encode($response);