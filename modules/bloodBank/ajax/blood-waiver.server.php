<?php 
#added by raymond - for blood waiver request
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');

function getBloodComponents(){
	global $db;
	$components = array();
	$sql = "SELECT id,name,long_name FROM seg_blood_component";

	$result = $db->Execute($sql);
	if($result->RecordCount()){
		while($row = $result->FetchRow()){
			array_push($components,array(
				'id' =>	$row['id'],
				'name' => $row['name']
			));
		}
	}
	return $components;
}
function getBloodSource(){
	global $db;
	$source = array();
	$sql = "SELECT id, name FROM seg_blood_source";
	$result = $db->Execute($sql);
	if($result->RecordCount()){
		while($row = $result->FetchRow()){
			array_push($source,array(
				'id' =>	$row['id'],
				'name' => $row['name']
			));
		}
	}
	return $source;
}
function hasAlreadyPrinted($batchnr){
	global $db;
	$sql = "SELECT * FROM seg_blood_waiver_details WHERE batch_nr =". $db->qstr($batchnr);
	$result = $db->Execute($sql);
	if($result->RecordCount())
		return true;
	else
		return false;
}
function updateWaiverInfo($batchnr, $details){
	global $db;
	$info = json_encode($details);
	$modify_time =  date('Y-m-d H:i:s');
	$sql = "UPDATE seg_blood_waiver_details SET details =" . $db->qstr($info) .", modify_id =".$db->qstr($_SESSION["sess_user_name"]).",modify_time=".$db->qstr($modify_time)." WHERE batch_nr=".$db->qstr($batchnr);
	if($db->Execute($sql))
		return true;
	else
		return false;
}
function saveWaiverInfo($batchnr, $enc, $pid, $details){
	$create_time = date('Y-m-d H:i:s');
	global $db;
	$info = json_encode($details);
	$sql = "INSERT INTO seg_blood_waiver_details (batch_nr, encounter_nr, pid, details, create_time, create_id) VALUES(".
			$db->qstr($batchnr).",".$db->qstr($enc).",".$db->qstr($pid).",".$db->qstr($info).",".$db->qstr($create_time).",".$db->qstr($_SESSION["sess_user_name"]).")";
	if($db->Execute($sql))
		return true;
	else
		return false;
}
function fetchWaiverInformation($batchnr,$pid,$encounter_nr){
	global $db;
	$batchnrCondi = '';
	$pidCondi = '';
	$caseCondi = '';
	$condition = '';

	if($batchnr != '')
		$batchnrCondi = 'batch_nr = '.$db->qstr($batchnr);
	else
		$batchnrCondi = 'batch_nr IS NULL';

	if($pid != '')
		$pidCondi = ' AND pid = '.$db->qstr($pid);
	else
		$pidCondi = ' AND pid IS NULL';

	// if($encounter_nr != '')
	// 	$caseCondi = ' AND encounter_nr = '.$db->qstr($encounter_nr);
	// else
	// 	$caseCondi = ' AND encounter_nr IS NULL';

	$condition .= $batchnrCondi;
	$condition .= $pidCondi;
	$condition .= $caseCondi;

	$sql = "SELECT details FROM seg_blood_waiver_details WHERE ".$condition;

	// die($sql);
	$result = $db->Execute($sql);
	if($result->RecordCount()){
		$row = $result->FetchRow();
		return json_decode($row['details'], true);
	}
	return false;
}
function redrawTable($info){
	global $db;
	// $rows = "<table id='wavier_details'><thead><tr><th></th></tr</thead><tbody>";
	$bldgrpTemp = '';
	$i = 0;

	foreach ($info as $value) {
		$bldgrpTemp = getBloodType("WHERE name = ".$db->qstr($value['bloodgrp'])." ", 1);

		$rows .= '<tr id="'.$i.'" class="detailcontent">'.
				 '<td style="padding:3px;" class="data">'.$value['unitno'].'</td>'.
				 '<td style="padding:3px;" class="data">'.$value['bloodgrp'].'</td>'.
				 '<td style="padding:3px;" class="data">'.$value['donorunit'].'</td>'.
				 '<td style="padding:3px;" class="data">'.$value['expiry'].'</td>'.
				 '<td style="padding:3px;" class="data">'.$value['component'].'</td>'.
				 '<td style="padding:3px;" class="data">'.$value['source'].'</td>'.
				 '<td style="padding:3px;"><button type="button" style="color:green" class="btn btn-small" title="Edit Information" onclick="editRow(\''.$i.'\',\''.$value['unitno'].'\',\''.$bldgrpTemp.'\',\''.$value['donorunit'].'\',\''.$value['expiry'].'\',\''.$value['component'].'\',\''.$value['source'].'\',this)">&orarr;</button><button type="button" style="color:red" class="btn btn-small" title="Delete Information" onclick="removeRow(this)" id="unit_no'.$i.'">&times;</button></td></tr>';
		$i++;
	}
	// $rows .= "</tbody></table>";

	return $rows;
}
function getBloodType($condition='',$fordisplay=0){
	global $db;
	$sql = "SELECT * FROM seg_blood_type ".$condition."ORDER BY ordering";
	$result = $db->Execute($sql);
	// die($sql);
	if($result->RecordCount())
		if($condition != '' && !$fordisplay){
			while($row = $result->FetchRow()){
				return $row['id'];
			}
		}elseif($condition != '' && $fordisplay){
			while($row = $result->FetchRow()){
				return $row['name'];
			}
		}
		else
			return $result;
	else
		return false;
}
#end raymond
?>
