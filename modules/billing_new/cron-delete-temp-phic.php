<?php
/**
 * Created by Nick 07-24-2014
 * delete phic if remarks includes the word "temp"
 */
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

global $db;

$sql = "DELETE FROM seg_encounter_insurance WHERE remarks LIKE '%temp%' AND DATEDIFF(NOW(),modify_dt) >= 2;";

$rs = $db->Execute($sql);

if($rs){
    echo "DELETED ROWS: " . $db->affected_rows();
}else{
    echo "ERROR: " . $db->ErrorMsg();
}