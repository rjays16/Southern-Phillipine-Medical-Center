<?php
include("roots.php");
include_once($root_path."include/inc_environment_global.php");
include_once($root_path."include/inc_jasperReporting.php");
require_once($root_path.'frontend/bootstrap.php');
require_once($root_path.'include/care_api_classes/class_progress_notes.php');
$notes= new ProgressNotes;

    $data[0] = array();
    $parameters = array();
    $id = $_GET['id'];
    $enc_nr = $_GET['enc_nr'];
    $pid = $_GET['pid'];
    $sex="";
$sql = "SELECT *,SUBSTRING(sp.progress_date_time,1,10) AS datesp,
            SUBSTRING(sp.progress_date_time,11,6) AS timesp
            FROM seg_pdpu_progress_notes sp 
            JOIN care_person cp ON sp.pid=cp.pid 
            JOIN care_encounter ce ON sp.encounter_nr=ce.encounter_nr 
            WHERE sp.pid=".$db->qstr($pid)." AND sp.is_deleted = '0' AND sp.encounter_nr=".$db->qstr($enc_nr)."
            ORDER BY sp.progress_date_time DESC";

    $info=$notes->getPersonalInfo($pid,$enc_nr);
    $att=$notes->getAttendingPhysician($pid,$enc_nr);
                    

$classic="SELECT DISTINCT discountid FROM seg_pdpu_progress_notes sp
            JOIN care_encounter ce ON sp.pid=ce.pid
            JOIN seg_charity_grants sg ON ce.encounter_nr=sg.encounter_nr
            WHERE ce.pid=".$db->qstr($pid)." AND sg.status='valid' AND sg.encounter_nr=".$db->qstr($enc_nr)."
            ORDER BY grant_dte DESC";
            

    $result =$db->Execute($sql);
    $i = 0;
    $class= $db->Execute($classic)->FetchRow();

    $address=$info['street_name'].", ".$info['brgy_name']." ".$info['mun_name'];
    $classification=$class['discountid'];

    $admittdate=$info['admitdate'];
    $dbirth=strtotime($info["date_birth"]);

    $ybday=(int)date("Y",$dbirth);
    $mbday=(int)date("m",$dbirth);
    $dbday=(int)date("d",$dbirth);

$age=(int)date('Y')-$ybday;

if($mbday<date('m') || $dbday<date('m')){
    $age=$age-1;
}
if($admittdate=="" || $admittdate==null){
    $condt=strtotime($info['encounter_date']);
    $admittdate=date('Y-m-d',$condt);
}


if(empty($classification) || $classification==""){
$classic1="SELECT discountid FROM seg_charity_grants sg
        JOIN care_encounter ce ON sg.encounter_nr=ce.encounter_nr 
        WHERE ce.pid=".$db->qstr($pid)." AND sg.status='valid' 
        ORDER BY grant_dte DESC";

$class1= $db->Execute($classic1)->FetchRow();

$classification=$class1['discountid'];

}

$params = array(
    'p_name'=> utf8_decode(trim($info['fullname'])),
    'p_pid'=>$pid,
    'p_ward_name'=>$info['ward_name'],
    'p_age'=>$age,
    'p_date_addmtd'=>$admittdate,
    'p_sex'=>$info['gender'],
    'p_civil_status'=>$info['civil_status'],
    'p_attending_physician'=> utf8_decode(trim($att['AttDrName'])),
    'p_address'=> utf8_decode(trim($address)),
    'p_final_diagnosis'=>$info['final_diagnosis'],
    'p_class'=>$classification
);
$i=0;
while($row=$result->FetchRow()){
    $data[$i]=array(
        'date'=>$row['datesp'],
        'time' => date("g:i A",strtotime($row['timesp'])),
        'informant' => $row['informant'],
        'purpose_reasons' => $row['purpose_reasons'],
        'action_taken' => $row['action_taken'],
        'problem_encountered' => $row['problem_encountered'],
        'plan' => $row['plan'],
        'venue' => $row['venue'],
    );
    $i++;

}
showReport('pdpu_progress_notes_form', $params, $data, 'PDF');