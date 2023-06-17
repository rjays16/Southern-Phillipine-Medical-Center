<?php

$returnfile=$HTTP_SESSION_VARS['sess_file_return'];

$HTTP_SESSION_VARS['sess_file_return']=$thisfile;

global $ptype, $allow_patient_register, $allow_newborn_register, $allow_er_user, $allow_opd_user, $allow_ipd_user, $allow_medocs_user, $allow_update;

#edited by VAN 04-16-08
/*
if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path."main/startframe.php".URL_APPEND;
	else $breakfile = $breakfile.URL_APPEND."&target=entry";
*/
if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path."modules/registration_admission/patient_register_show.php".URL_APPEND."&ptype=$ptype";
	else $breakfile = $breakfile.URL_APPEND."&target=entry&ptype=$ptype";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 $isIPBM = ($_GET['from']=='ipbm'||$_GET['ptype']=='ipbm')?1:0;
 $IPBMextend = $isIPBM?'&from=ipbm':'';
 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 #-----added by VAN 04-18-08
 require_once($root_path.'include/care_api_classes/class_encounter.php');
 $enc_obj=new Encounter;

 $enc_type = $enc_obj->EncounterType($current_encounter);

 if (($enc_type==3)||($enc_type==4))
	$page_title = 'Admission';
 else
	$page_title = 'Consultation';
 #------------

if($parent_admit) $sTitleNr= ($HTTP_SESSION_VARS['sess_full_en']);
	else $sTitleNr = ($HTTP_SESSION_VARS['sess_full_pid']);

# Title in the toolbar
 #$smarty->assign('sToolbarTitle',"$page_title ($sTitleNr)");
 $smarty->assign('sToolbarTitle',"$page_title ($sTitleNr)");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 #$smarty->assign('title',"$page_title ( $sTitleNr)");
 $smarty->assign('title',"$page_title ( $sTitleNr)");

 # Onload Javascript code
 $smarty->assign('sOnLoadJs',"if (window.focus) window.focus();");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('notes_router.php','echo $notestype','".strtr($subtitle,' ','+')."','$mode','$rows')");

	# href for return button
 $smarty->assign('pbBack',$returnfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&target='.$target.'&mode=show&type_nr='.$type_nr);

	#added by VAN 04-16-08
	include_once($root_path.'include/care_api_classes/class_department.php');
	$dept_obj=new Department;
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);
	#---------------------------

# Start buffering extra javascript output
ob_start();

?>

<script  language="javascript">
<!--

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

function popRecordHistory(table,pid) {
	urlholder="./record_history.php<?php echo URL_REDIRECT_APPEND; ?>&table="+table+"&pid="+pid;
	HISTWIN<?php echo $sid ?>=window.open(urlholder,"histwin<?php echo $sid ?>","menubar=no,width=400,height=550,resizable=yes,scrollbars=yes");
}
-->
</script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>


<?php
if($parent_admit) include($root_path.'include/inc_js_barcode_wristband_popwin.php');

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);


/* Load the tabs */
if($parent_admit) {
	$tab_bot_line='#66ee66';
	include('./gui_bridge/default/gui_tabs_patadmit.php');
	$smarty->assign('sTabsFile','registration_admission/admit_tabs.tpl');
	$smarty->assign('sClassItem','class="adm_item"');
	$smarty->assign('sClassInput','class="adm_input"');
}else{
	$tab_bot_line='#66ee66';
	include('./gui_bridge/default/gui_tabs_patreg.php');
	$smarty->assign('sTabsFile','registration_admission/reg_tabs.tpl');
	$smarty->assign('sClassItem','class="reg_item"');
	$smarty->assign('sClassInput','class="reg_input"');
}

# If encounter is already discharged, show warning

if($parent_admit&&$is_discharged){

	$smarty->assign('is_discharged',TRUE);
	$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle',TRUE).">");
	if($current_encounter) $smarty->assign('sDischarged',$LDEncounterClosed);
		else $smarty->assign('sDischarged',$LDPatientIsDischarged);
}

if($parent_admit) $smarty->assign('LDCaseNr',$LDAdmitNr);
	else $smarty->assign('LDCaseNr',$LDRegistrationNr);

if($parent_admit) $smarty->assign('sEncNrPID',$HTTP_SESSION_VARS['sess_full_en']);
	else $smarty->assign('sEncNrPID',$HTTP_SESSION_VARS['sess_full_pid']);

// added by carriane 08/09/18
if($suffix)
	$name_first = str_replace(' '.$suffix, ', '.$suffix, $name_first);
// end carriane

$smarty->assign('img_source',"<img $img_source>");

$smarty->assign('LDTitle',$LDTitle);
$smarty->assign('title',$title);
$smarty->assign('LDLastName',$LDLastName);
$smarty->assign('name_last',$name_last);
$smarty->assign('LDFirstName',$LDFirstName);
$smarty->assign('name_first',$name_first);


# If person is dead show a black cross and assign death date

if($death_date && $death_date != DBF_NODATE){
	$smarty->assign('sCrossImg','<img '.createComIcon($root_path,'blackcross_sm.gif','0','',TRUE).'>');
	$smarty->assign('sDeathDate',@formatDate2Local($death_date,$date_format));
}

	# Set a row span counter, initialize with 7
	$iRowSpan = 7;

	if($GLOBAL_CONFIG['patient_name_2_show']&&$name_2){
		$smarty->assign('LDName2',$LDName2);
		$smarty->assign('name_2',$name_2);
		$iRowSpan++;
	}

	if($GLOBAL_CONFIG['patient_name_3_show']&&$name_3){
		$smarty->assign('LDName3',$LDName3);
		$smarty->assign('name_3',$name_3);
		$iRowSpan++;
	}

	if($GLOBAL_CONFIG['patient_name_middle_show']&&$name_middle){
		$smarty->assign('LDNameMid',$LDNameMid);
		$smarty->assign('name_middle',$name_middle);
		$iRowSpan++;
	}

$smarty->assign('sRowSpan',"rowspan=\"$iRowSpan\"");

$smarty->assign('LDBday',$LDBday);
$smarty->assign('sBdayDate',@formatDate2Local($date_birth,$date_format));

$smarty->assign('LDSex',$LDSex);
if($sex=='m') $smarty->assign('sSexType',$LDMale);
	elseif($sex=='f') $smarty->assign('sSexType',$LDFemale);

$smarty->assign('LDBloodGroup',$LDBloodGroup);
if($blood_group){
	$buf='LD'.$blood_group;
	$smarty->assign('blood_group',$$buf);
}

/* Buffer and load the options table  */

ob_start();
	 $encounter_obj = $enc_obj;
	if($parent_admit)
		include('./gui_bridge/default/gui_patient_encounter_showdata_options.php');
	else{
			#edited by VAN 04-16-08
			#if (($dept_belong['id'] == "OPD-Triage"))
			#if (($allow_opd_user)&&($ptype=='opd')) {
			#if ($ptype=='opd')
								$encounter_obj = $enc_obj;      // Added by LST to correct error reported 02.18.2009
				include('./gui_bridge/default/gui_patient_reg_options.php');
					 # }
	}

	$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sOptionsMenu',$sTemp);

# If mode = show then display data
/*
echo "gui_show.php : mode = '".$mode."' <br> \n";
echo "gui_show.php : thisfile = '".$thisfile."' <br> \n";
echo "gui_show.php : rows = '".$rows."' <br> \n";
*/

if($mode=='show'){

	if($parent_admit) $bgimg='tableHeaderbg3.gif';
		else $bgimg='tableHeader_gr.gif';

	$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/'.$bgimg.'"';

	if($rows){
		#echo "thisfile = $thisfile";
		# Buffer the option block
		ob_start();
			include('./gui_bridge/default/gui_'.$thisfile);
			$sTemp = ob_get_contents();
		ob_end_clean();
		$smarty->assign('sOptionBlock',$sTemp);

	}else{

		$smarty->assign('bShowNoRecord',TRUE);

		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>');
		$smarty->assign('norecordyet',$norecordyet);

		if($parent_admit && !$is_discharged && $thisfile!='show_diagnostics_result.php'){
			$smarty->assign('sPromptIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle',TRUE).'>');
			$smarty->assign('sPromptLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&target='.$target.'&mode=new">'.$LDEnterNewRecord.'</a>');
		}else{
			if(file_exists('./gui_bridge/default/gui_person_createnew_'.$thisfile)) include('./gui_bridge/default/gui_person_createnew_'.$thisfile);
		}
	}
}else {

	# Buffer the option input block
	ob_start();
		 include('./gui_bridge/default/gui_input_'.$thisfile);
		$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->assign('sOptionBlock',$sTemp);
}

# Buffer the bottom controls
#echo "gui_show.php : parent_admit = '".$parent_admit."' <br> \n";
ob_start();

	if($parent_admit) {
		include('./include/bottom_controls_admission_options.inc.php');
	}else{
#		echo "gui_show.php : parent_admit 2 = '".$parent_admit."' <br> \n";
		include('./include/bottom_controls_registration_options.inc.php');
	}

	# Get buffer contents and stop buffering

	$sTemp= ob_get_contents();
ob_end_clean();

$smarty->assign('sBottomControls',$sTemp);

$breakfile = 'patient_register_show.php'.URL_APPEND.'&pid='.$pid.'&ptype='.$ptype.$IPBMextend; #----------added by van------
$smarty->assign('pbBottomClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');


$smarty->assign('sMainBlockIncludeFile','registration_admission/common_option.tpl');


$smarty->display('common/mainframe.tpl');

?>
