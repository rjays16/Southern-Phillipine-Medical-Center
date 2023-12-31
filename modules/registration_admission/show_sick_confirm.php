<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/

$logo_ht_limit=50; # Maximum deparment logo�s height in pixels

# Load the encounter class
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter($HTTP_SESSION_VARS['sess_full_en']);
$thisfile=basename(__FILE__);

if(!isset($mode)||empty($mode)){
	
	$sickconfirm_obj=&$enc_obj->allSicknessConfirm();
	if($rows=$enc_obj->LastRecordCount()){
		$mode='show';
		# If $get_nr is non-empty, get the  single record 
		if(isset($get_nr)&&$get_nr){
			if(!$single_obj=&$enc_obj->getSicknessConfirm($get_nr)) $get_nr=0;
		}else{
			$get_nr=0;
		}
	}else{
		$mode='';
	}
	
}elseif($mode=='create'||$mode=='update') {
	//$db->debug=true;
	include_once($root_path.'include/inc_date_format_functions.php');
	# Convert date to standard format
	$HTTP_POST_VARS['date_end']=formatDate2STD($HTTP_POST_VARS['date_end'],$date_format);
	$HTTP_POST_VARS['date_start']=formatDate2STD($HTTP_POST_VARS['date_start'],$date_format);
	$HTTP_POST_VARS['date_confirm']=formatDate2STD($HTTP_POST_VARS['date_confirm'],$date_format);
	
	$HTTP_POST_VARS['encounter_nr']=$HTTP_SESSION_VARS['sess_en'];
	$HTTP_POST_VARS['history']="Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n";
	$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
	$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
	$HTTP_POST_VARS['create_time']=date('YmdHis');

	
	if($enc_obj->saveSicknessConfirm($HTTP_POST_VARS)) {
		$get_nr=$db->Insert_ID();
		header("location:".$thisfile.URL_REDIRECT_APPEND."&get_nr=$get_nr&dept_nr=$dept_nr&target=$target&type_nr=$type_nr&pid=".$HTTP_SESSION_VARS['sess_pid']);
		exit;
	} else echo "$obj->sql<br>$LDDbNoSave";
}

if($mode=='new'){
	# Load the department class 
	include_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department($dept_nr);
	$dept=&$dept_obj->getDeptAllInfo($dept_nr);
}
# Get the insurance data of the encounter
if($insure_obj=$enc_obj->EncounterInsuranceData()){
	$insurance=$insure_obj->FetchRow();
}else{
	$insurance=false;
}

$lang_tables=array('departments.php','legal.php','prompt.php');
require('./include/init_show.php');

$subtitle=$LDSickReport;
$notestype='sickness';

$buffer=str_replace('~tag~',$title.' '.$name_last,$LDNoRecordFor);
$norecordyet=str_replace('~obj~',strtolower($subtitle),$buffer); 
$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

/* Load all  medical depts info */
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
$dept_med=$dept_obj->getAllMedical();

/* Load GUI page */
require('./gui_bridge/default/gui_show.php');
?>
