<?php
/**
 * @author Nick B. Alcala 1-25-2015
 * Outpatient Preventive Care Center Daily Transaction
 */
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
include('parameters.php');

$parameters = array($from_date_format,$to_date_format);

$opd_staff = $_GET['param'];
$opd_staff = explode("--", $opd_staff);

if($opd_staff[1]){
    $encoder = $db->GetOne("SELECT fn_get_personell_firstname_last(".$db->qstr($opd_staff[1]).")");

    $encoderName = $db->GetRow("SELECT 
                                  name_first,
                                  name_last 
                                FROM
                                  care_person AS person
                                  INNER JOIN care_personell AS personnel
                                  ON personnel.pid = person.pid
                                WHERE personnel.nr = ?",$opd_staff[1]);

    $encoderCondition = " AND ce.create_id REGEXP ?";
    $parameters[] = $encoderName['name_first'] . '|' . $encoderName['name_last'];

}else{
    $encoder = 'All';
}

global $db;
$sql = "SELECT
          ce.pid,
          UPPER(fn_get_person_lastname_first(ce.pid)) AS fullName,
          UPPER(DATE_FORMAT(ce.create_time,'%m-%d-%Y %h:%i:%s %p')) AS create_time,
          UPPER(fn_get_age(NOW(),cp.date_birth)) AS age,
          cp.sex,
          UPPER(fn_get_complete_address(cp.pid)) AS fullAddress,
          UPPER(cd.name_formal) AS department,
          UPPER(ce.create_id) AS encoder
        FROM
          care_encounter AS ce
          INNER JOIN care_person AS cp
            ON cp.pid = ce.pid
          INNER JOIN care_department AS cd
            ON cd.nr = ce.current_dept_nr
        WHERE ce.encounter_type = 2
        AND ce.status NOT IN('deleted','hidden','inactive','void')
        AND DATE_FORMAT(ce.create_time,'%Y-%m-%d') BETWEEN DATE(?) AND DATE(?)
        {$encoderCondition}
        ORDER BY ce.encounter_date DESC";

// $parameters = array_merge(array("%{$encoderName['name_first']}%","%{$encoderName['name_last']}%"),$parameters);

$rs = $db->GetAll($sql,$parameters);

if(empty($rs)){
    $data[0]['pid'] = 'NO DATA';
    $count = 0;
}else{
    $index = 1;
    foreach($rs as $item){
        $data[] = array_merge(array('n' => $index),$item);
        $index++;
    }
    $count = count($data);
}

$params->put('date_from',date('F d, Y',strtotime($from_date_format)));
$params->put('date_to',date('F d, Y',strtotime($to_date_format)));
$params->put('encoder',$encoder);
$params->put('count',$count);