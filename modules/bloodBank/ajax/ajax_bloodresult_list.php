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


if(is_numeric($suchwort)) {

    if(strlen($suchwort)=='10'){
        $sql2_cond = " AND tr.refno = ".$db->qstr($suchwort);
    }else{
        $pid = $suchwort;   
        $sql2_cond = " AND h.pid = ".$db->qstr($pid);
    }    
}/* else {

    if(stristr($searchkey, '/') === FALSE){

        if(stristr($searchkey, ',') === FALSE){
            $sql2_cond = " AND REPLACE(tr.serial_no,' ','') = ". $db->qstr(str_replace(" ", "", $suchwort));
            
        }else{

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
        }    
    
    }else{

        if (preg_match("/(0[1-9]|1[012])\/(0[1-9]|[12]\d|3[01])\/(2\d{2})/",$searchkey)){

            $searchdate = date('Y-m-d',strtotime($searchkey));

            $sql2_cond = " AND  h.date_update
                                BETWEEN 
                                    DATE_FORMAT(CONCAT(STR_TO_DATE(".$db->qstr($searchdate).", '%Y-%m-%d'), ' 00:00:00'),'%Y-%m-%d %H:%i:%s') 
                                AND 
                                    DATE_FORMAT(CONCAT(STR_TO_DATE(".$db->qstr($searchdate).", '%Y-%m-%d'), ' 23:59:59'),'%Y-%m-%d %H:%i:%s') ";                        
        }else{
            $sql2_cond = $date_now;
        }
    }
    

}*/
    

if ($searchkey){
    /*$query = "SELECT SQL_CALC_FOUND_ROWS s.encounter_nr,
                  e.current_ward_nr,
                  e.current_room_nr,
                  e.current_dept_nr,
                  e.encounter_type,
                  s.pid,
                  fn_get_person_name (s.pid) AS patient,
                  IF(
                    fn_calculate_age (
                      IF(
                        s.serv_dt,
                        s.serv_dt,
                        DATE(h.date_update)
                      ),
                      p.date_birth
                    ),
                    fn_get_age (
                      IF(
                        s.serv_dt,
                        s.serv_dt,
                        DATE(h.date_update)
                      ),
                      p.date_birth
                    ),
                    age
                  ) AS age,
                  UPPER(p.sex) AS sex,
                  tr.refno,
                  br.lis_order_no,
                  CONCAT(
                    fn_get_labtest_request_all (tr.refno),
                    ' #',
                    REPLACE(tr.serial_no,' ','')
                  ) AS services,
                  tr.service_code AS test_code,
                  CONCAT(tr.service_code, ordering) service_code,
                  h.date_update,
                  h.date_update AS request_date,
                  REPLACE(tr.serial_no,' ','') serial_no 
                FROM
                  seg_blood_received_details tr 
                  LEFT JOIN seg_lab_serv s 
                    ON s.refno = tr.refno  AND ref_source='BB'
                  INNER JOIN care_person p 
                    ON p.pid = s.pid   
                  LEFT JOIN care_encounter e 
                    ON e.encounter_nr = s.encounter_nr 
                  LEFT JOIN seg_hl7_bloodbank_tracker br ON br.pid=s.pid AND br.encounter_nr=s.encounter_nr AND br.refno=tr.refno  
                  INNER JOIN seg_hl7_hclab_msg_receipt h ON h.pid=s.pid AND h.lis_order_no=br.lis_order_no  
                WHERE s.status NOT IN ('deleted','hidden','inactive','void')
                ".$sql2_cond." GROUP BY CONCAT(tr.service_code) ";*/     

    $query = "SELECT 
              SQL_CALC_FOUND_ROWS s.encounter_nr,
              s.refno,
              e.current_ward_nr,
              e.current_room_nr,
              e.current_dept_nr,
              e.encounter_type,
              s.pid,
              fn_get_person_name (s.pid) AS patient,
              IF(
                fn_calculate_age (
                  IF(
                    s.serv_dt,
                    s.serv_dt,
                    DATE(h.date_update)
                  ),
                  p.date_birth
                ),
                fn_get_age (
                  IF(
                    s.serv_dt,
                    s.serv_dt,
                    DATE(h.date_update)
                  ),
                  p.date_birth
                ),
                age
              ) AS age,
              UPPER(p.sex) AS sex,
              br.lis_order_no,
              fn_get_labtest_request_all (s.refno) AS services,
              br.service_code AS test_code,
              CONCAT(br.service_code) service_code,
              h.date_update,
              h.date_update AS request_date
            FROM
              seg_lab_serv s
                
              INNER JOIN care_person p 
                ON p.pid = s.pid 
              LEFT JOIN care_encounter e 
                ON e.encounter_nr = s.encounter_nr 
              LEFT JOIN seg_hl7_bloodbank_tracker br 
                ON br.pid = s.pid 
                AND br.encounter_nr = s.encounter_nr 
                AND br.refno = s.refno 
              INNER JOIN seg_hl7_hclab_msg_receipt h 
                ON h.pid = s.pid 
                AND h.lis_order_no = br.lis_order_no 
            WHERE s.status NOT IN (
                'deleted',
                'hidden',
                'inactive',
                'void'
              ) 
              AND ref_source = 'BB' 
              ".$sql2_cond."
            GROUP BY pid, refno";                   
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
                
            $services = $row['services'];
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

           
           $data[] = array(
                'date' => ($row["request_date"])?nl2br(date("M-d-Y\nh:ia", strtotime($row["request_date"]))):'No Result Yet',
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
                'withresult' => $withresult,
                'test_code' => $row["test_code"],
                'service_code' => $row['service_code'],
                'serial_no' => $row['serial_no'],
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