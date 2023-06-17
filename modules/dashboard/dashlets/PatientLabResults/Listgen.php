<?php
/**
* ListGen.php
*
*
*/

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require "./roots.php";
require_once $root_path."include/inc_environment_global.php";
require_once $root_path."include/care_api_classes/dashboard/DashletSession.php";
require_once $root_path."classes/json/json.php";

header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);

$page = (int) $_REQUEST['page'];
$maxRows = (int) $_REQUEST['mr'];
$offset = ($page-1) * $maxRows;

$sortName = $_REQUEST['sort'];
if (!$sortName)
	$sortName = 'date';
$sortDir = $_REQUEST['dir']=='1' ? 'ASC':'DESC';
$sortMap = array(
	//'date' => 'r.serv_dt DESC, r.serv_tm',
	'date' => 'date_received',
);
//if (!$sortMap[$sortName]) $sort = 'serv_dt, serv_tm DESC';
if (!$sortMap[$sortName]) $sort = 'date_received DESC';
else	$sort = $sortMap[$sortName]." ".$sortDir;

global $db;

$session = DashletSession::getInstance(DashletSession::SCOPE_DASHBOARD, $_SESSION['activeDashboard']);
$encounter_nr = $session->get('ActivePatientFile');

$query = "SELECT pid, encounter_type, encounter_date, admission_dt, discharge_date, is_discharged 
            FROM care_encounter WHERE encounter_nr=".$db->qstr($encounter_nr);

#$pid = $db->GetOne($query);
$enc_row = $db->GetRow($query);
$pid = $enc_row['pid'];

if (($enc_row['encounter_type'] == '1') || ($enc_row['encounter_type'] == '2')){
    $encounter_date = date("Y-m-d",strtotime($enc_row['encounter_date']));
    $discharged_date = date("Y-m-d",strtotime($enc_row['encounter_date']));
}else{
    $encounter_date = date("Y-m-d",strtotime($enc_row['admission_dt']));
    if (!$enc_row['is_discharged'])
       $enc_row['discharge_date'] = date("Y-m-d"); 
    $discharged_date = date("Y-m-d",strtotime($enc_row['discharge_date']));
}

//disregard lab results from 12/14/2015 to 12/20/2015
#$special_cond = ' AND (h.`date_update` < CAST("2015-12-14" AS DATE) or h.`date_update` > CAST("2015-12-20" AS DATE)) ';
$special_cond = " AND (h.`date_update` < DATE_FORMAT(STR_TO_DATE('2015-12-14', '%Y-%m-%d'), '%Y-%m-%d') 
                       OR h.`date_update` > DATE_FORMAT(STR_TO_DATE('2015-12-20', '%Y-%m-%d'), '%Y-%m-%d')) ";

$data = Array();
if($pid) {

        $query = "SELECT DISTINCT SQL_CALC_FOUND_ROWS 
                                s.encounter_nr, 
                                e.current_ward_nr, 
                                e.current_room_nr, 
                                e.current_dept_nr, 
                                e.encounter_type, 
                                h.pid, 
                                fn_get_person_name(h.pid) AS patient, 
                                IF(fn_calculate_age(IF(s.serv_dt,s.serv_dt,DATE(h.date_update)),p.date_birth),fn_get_age(IF(s.serv_dt,s.serv_dt,DATE(h.date_update)),p.date_birth),age) AS age, 
                                UPPER(p.sex) AS sex, 
                                o.refno, 
                                h.lis_order_no, 
                                
                                IF(
                                    fn_get_labtest_request_all (o.refno) <> '',
                                    fn_get_labtest_request_all (o.refno),
                                    CONCAT(
                                      'MANUALLY ENCODED with Order No. ',
                                      h.lis_order_no
                                    )
                                  ) AS services,                                       
                                
                                o.refno, 
                                sr.nth_take, 
                                sr.service_code, 
								(SELECT MAX(h2.date_update) FROM seg_hl7_hclab_msg_receipt h2 WHERE h2.lis_order_no = h.lis_order_no) date_update
                            FROM seg_hl7_hclab_msg_receipt h
                            LEFT JOIN seg_lab_services sls 
                            ON ( h.test = sls.`service_code` 
                              OR h.test = sls.`oservice_code` 
                              OR h.test = sls.`icservice_code` 
                              OR h.test = sls.`ipdservice_code` 
                              OR h.test = sls.`erservice_code`
                            )
                            LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=h.lis_order_no
                            LEFT JOIN seg_lab_serv_serial sr ON sr.refno=o.refno AND sr.lis_order_no=o.lis_order_no 
                            INNER JOIN care_person p ON p.pid=h.pid
                            LEFT JOIN seg_lab_serv s ON s.refno=o.refno LEFT JOIN care_encounter e ON e.encounter_nr=s.encounter_nr
                            WHERE h.pid = " . $db->qstr($pid) . $special_cond ." AND sls.`group_code` NOT IN ('B')".
			" UNION ".
			"SELECT DISTINCT  
				o.encounter_nr, e.current_ward_nr, e.current_room_nr, e.current_dept_nr, e.encounter_type, o.pid, 
				fn_get_person_name(o.pid) patient,
				IF(fn_calculate_age(o.reading_dt,p.date_birth),fn_get_age(o.reading_dt,p.date_birth),age) AS age, 
				UPPER(p.sex) sex,
				l.ref_no, 
				'' lis_order_no,
				s.name, 
				l.ref_no, 
				(SELECT COUNT(*) FROM seg_cbg_reading o2 WHERE o2.reading_dt <= o.reading_dt AND o2.encounter_nr = o.encounter_nr) nth_take,
				s.service_code, 
			    o.reading_dt
				FROM (seg_cbg_reading o INNER JOIN 
				   (seg_hl7_message_log l 
				   INNER JOIN (seg_poc_order poch INNER JOIN seg_poc_order_detail pocd ON poch.refno = pocd.refno) 
				   ON l.ref_no = poch.refno)
				   ON o.log_id = l.log_id) 
				INNER JOIN (care_encounter e INNER JOIN care_person p ON e.pid = p.pid) ON e.encounter_nr = o.encounter_nr
				INNER JOIN seg_lab_services s ON s.service_code = pocd.service_code 
				WHERE o.pid = ".$db->qstr($pid).
				"   AND o.reading_dt = (SELECT MAX(o3.reading_dt) FROM seg_cbg_reading o3 WHERE o3.pid = ".$db->qstr($pid)." AND o3.encounter_nr = o.encounter_nr) ".							
			" ORDER BY date_update DESC LIMIT $offset, $maxRows";

	      /*$query = "SELECT SQL_CALC_FOUND_ROWS o.refno, date_received AS request_date, 
                    SUBSTR(h.filename,INSTR(h.filename, '_')+1,LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4) `lis_order_no`,
                    SUBSTR(h.filename,1,INSTR(h.filename, '_')-1) `pid`,
                    IF(fn_get_labtest_request_all(o.refno)<>'',
                       fn_get_labtest_request_all(o.refno),
                       CONCAT('MANUALLY ENCODED with Order No. ',
                               SUBSTR(h.filename,INSTR(h.filename, '_')+1,
                                   LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4))) AS services, 
                    o.refno, sr.nth_take, sr.service_code, h.*
                    FROM seg_hl7_pdffile_received h
                    LEFT JOIN seg_lab_hclab_orderno o ON o.lis_order_no=(SUBSTR(h.filename,INSTR(h.filename, '_')+1,LENGTH(SUBSTR(h.filename,INSTR(h.filename, '_')+1))-4))
                    LEFT JOIN seg_lab_serv_serial sr ON sr.refno=o.refno AND sr.lis_order_no=o.lis_order_no
                    WHERE filename LIKE '$pid%'
                    AND date_received BETWEEN ".$db->qstr($encounter_date)." AND ".$db->qstr($discharged_date)." + INTERVAL 1 MONTH
                    ORDER BY date_received DESC
                    LIMIT $offset, $maxRows";*/       
//		echo "<pre>";
//		print_r($query);
//		echo "</pre>"; die();
	$db->SetFetchMode(ADODB_FETCH_ASSOC);
	$rs = $db->Execute($query);

	$data = Array();
	if ($rs !== false)
	{
		$total = 0;
		$total = $db->GetOne("SELECT FOUND_ROWS()");
		$rows = $rs->GetRows();
		foreach ($rows as $row)
		{
            
            //added by VAN 02-06-2013
            if ($row['nth_take']==1){
               $services = $row['services'].'<font color="BLUE"> (First Take)</font>'; 
            }elseif ($row['nth_take'] > 1){
               $service_code = $db->qstr($row['service_code']); 
               $sql_l = "SELECT name FROM seg_lab_services WHERE service_code=$service_code"; 
               $services = $db->GetOne($sql_l);
               
               switch($row['nth_take']){
                    case '1' :  
                                $nth_take = 'st'; 
                                break;
                    case '2' :  
                                $nth_take = 'nd'; 
                                break;
                    case '3' :  
                                $nth_take = 'rd'; 
                                break;
//                    case '4' :  
					default  :
                                $nth_take = 'th'; 
/*                                break;
                    case '5' :  
                                $nth_take = 'Fifth'; 
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
*/								
                }

               $services = $services.'<font color="BLUE"> ('.$row['nth_take'].$nth_take.' Take)</font>'; 
            }else{
               $services = $row['services'];
            }
            //----------------------
            
            
            
            //added by Nick, 3/13/2014  
            #edited by VAS 01/13/2017
            #remove seg_hl7_file_received in the query           
            $sql_date = "SELECT h.date_update, h.date_update as request_date
                                        FROM seg_hl7_hclab_msg_receipt h
                                        #INNER JOIN seg_hl7_file_received f ON f.filename=h.filename
                                        WHERE h.pid = ".$db->qstr($row["pid"])." 
                                        AND h.lis_order_no=".$db->qstr($row["lis_order_no"])." LIMIT 1";
            $row_dt = $db->GetRow($sql_date); 
            $resultDate = isset($row_dt['request_date']) ? date("M d, Y h:i A",strtotime($row_dt['request_date'])) : null;

            // $sql_file = "SELECT 
            //                 filename 
            //             FROM seg_hl7_pdffile_received 
            //             WHERE filename LIKE ".$db->qstr($row["pid"].'%');
            // $filename = $db->GetRow($sql_file);

            // $withresult = 0;
            // if ($filename['filename'])
            $withresult = 1;

            //end nick

            $data[] = Array(
                'date' => nl2br(date("M-d-Y\nh:ia", strtotime(is_null($resultDate) ? $row['date_update'] : $resultDate))),//edited by Nick, 3/13/2014
															// checked for null result date by LST.
				        'service' => $services ,
				        'refno' => $row["refno"],
                'pid' => $row["pid"],
                'lis_order_no' => $row["lis_order_no"],
				'encounter_nr' => $row["encounter_nr"],
                // 'filename' => $filename['filename'], //edited by Nick, 3/13/2014
                'withresult' => $withresult
			);
		}
	}
}

if (!$data)
{
	$total = 0;
}

$response = array(
	'currentPage'=>$page,
	'total'=>$total,
	'data'=>$data
);

/**
* Convert data to JSON and print
*
*/

$json = new Services_JSON;
print $json->encode($response);