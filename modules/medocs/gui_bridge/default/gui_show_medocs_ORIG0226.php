<?php
$returnfile=$HTTP_SESSION_VARS['sess_file_return'];

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
//require($root_path.'modules/medocs/ajax/medocs_common.php');

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

if($parent_admit) $sTitleNr= ($HTTP_SESSION_VARS['sess_full_en']);
	else $sTitleNr = ($HTTP_SESSION_VARS['sess_full_pid']);

# Title in the toolbar
 $smarty->assign('sToolbarTitle',"$page_title $encounter_nr");

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDPatientRegister')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',"$page_title $encounter_nr");

 # Onload Javascript code
 $onLoadJs='onLoad="if (window.focus) window.focus();"';
 $smarty->assign('sOnLoadJs',$onLoadJs);
 
 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('medocs_entry.php')");

  # href for return button
 $smarty->assign('pbBack',$returnfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&target='.$target.'&mode=show&type_nr='.$type_nr);


# Buffer extra javascript code

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
<script language="javascript" src="<?=$root_path?>js/yui/yahoo/yahoo-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/event/event-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/dom/dom.js"></script>

<?php 
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/medocs_function.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/medocs_combo.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/ICDCodeParticulars.js"></script>';
	$xajax->printJavascript($root_path.'classes/xajax');
	
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

require('./gui_bridge/default/gui_tabs_medocs.php');

if($enc_obj->Is_Discharged()){

	$smarty->assign('is_discharged',TRUE);
	$smarty->assign('sWarnIcon',"<img ".createComIcon($root_path,'warn.gif','0','absmiddle').">");
	$smarty->assign('sDischarged',$LDPatientIsDischarged);
}

# Set the table columns� classes
$smarty->assign('sClassItem','class="adm_item"');
$smarty->assign('sClassInput','class="adm_input"');

$smarty->assign('LDCaseNr',$LDAdmitNr);

$smarty->assign('sEncNrPID',$HTTP_SESSION_VARS['sess_en']);

$smarty->assign('img_source',"<img $img_source>");

$smarty->assign('LDTitle',$LDTitle);
$smarty->assign('title',$title);
$smarty->assign('LDLastName',$LDLastName);
$smarty->assign('name_last',$name_last);
$smarty->assign('LDFirstName',$LDFirstName);
$smarty->assign('name_first',$name_first);

# If person is dead show a black cross and assign death date

if($death_date && $death_date != DBF_NODATE){
	$smarty->assign('sCrossImg','<img '.createComIcon($root_path,'blackcross_sm.gif','0').'>');
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

#echo "burn (04-28-2007): encounter_type = '".$encounter_type."' <br> \n";
#echo "burn (04-28-2007): enc_Info['encounter_type'] = '".$enc_Info['encounter_type']."' <br> \n";
#echo "enc_Info : <br> \n"; print_r($enc_Info); echo "<br> \n";
	# burn added : April 28, 2007
if ($encounter_type==1){
	$segEncounterType="ER";
}elseif ($encounter_type==2){
	$segEncounterType="OPD";
}elseif ($encounter_type==3){

		# burn added : May 24, 2007
	if ($enc_Info['encounter_status']=='direct_admission')
		$segEncounterType="Inpatient (Direct Admission)";
	else
		$segEncounterType="Inpatient (ER)";

}elseif ($encounter_type==4){
	$segEncounterType="Inpatient (OPD)";
}
$smarty->assign('segEncounterTypeLabel','Encounter Type');
if($encounter_type){
	$smarty->assign('segEncounterType',$segEncounterType);
}

$smarty->assign('LDDate',$LDDate);
$smarty->assign('LDDiagnosis',$LDDiagnosis);
//$smarty->assign('LDTherapy',$LDTherapy);
$smarty->assign('LDTherapy',$segIcpmDesc);

$smarty->assign('LDDetails',$LDDetails);
$smarty->assign('LDBy',$LDBy);

//Add by Mark on March 29, 2007
$smarty->assign('segDept_nr','Department');

$smarty->assign('LDExtraInfo',$LDExtraInfo);
$smarty->assign('LDInsurance',$LDInsurance);
$smarty->assign('LDGotMedAdvice',$LDGotMedAdvice);
$smarty->assign('LDYes',$LDYes);
$smarty->assign('LDNo',$LDNo);

//TODO: fix show list of documents
#Show list of documents 
#echo "encounter_nr = ".$encounter_nr;
#added by VAN 02-19-08
$discharged = $enc_obj->Is_Discharged($encounter_nr);
$patient_result = $enc_obj->getPatientEncounterResult($encounter_nr);
#echo "sql = ".$enc_obj->sql;
#echo "<br>result = ".$patient_result['result_code'];

#added by VAN
if (($patient_result['result_code']==4)||($patient_result['result_code']==8))
	$isDied = 1;
else
	$isDied = 0;
#echo "fromtemp = ".$result['fromtemp'];		

if($mode=='show'){	
#echo "<br>hello<br>";
	#----added by VAN 
	if (($fromtemp) || ($isDied) || ($discharged)){
		$source = 'medocs';
		ob_start();
			require($root_path.'modules/registration_admission/gui_bridge/default/gui_temporary_patient_reg_options.php');
			$sTemp = ob_get_contents();
			#$target = 'search';
		ob_end_clean();
		$smarty->assign('sRegOptions',$sTemp);
	}	
	$smarty->assign('sShow',TRUE);
	/*
	$sTemprow = '<td width="78%">
						{{include file="registration_admission/basic_data.tpl"}}				
					</td>
					<td width="22%">{{$sRegOptions}}</td>';
	$smarty->assign('sTrow',$sTemprow);				
	*/
	#---------------
	
	if($rows){
		# Set the document list template file
		
		$smarty->assign('sDocsBlockIncludeFile','medocs/docslist_frame.tpl');
	
		$smarty->assign('LDDetails',$LDDetails);
		
		$sTemp = '';
		$toggle=0;
		$row=$result;
	
#		$smarty->assign('segSetHeadingPrincipal',FALSE);
#		$smarty->assign('segSetHeadingOthers',FALSE);
	
		$smarty->assign('segHeadingPrincipal','Principal:');
		$smarty->assign('segHeadingOthers','Others:');

		if ( (!empty($result['diagnosis_principal']) && isset($result['diagnosis_principal'])) ||
			  (!empty($result['therapy_principal']) && isset($result['therapy_principal'])) ){
#			$smarty->assign('segSetHeadingPrincipal',TRUE);
			$smarty->assign('sRowClass','class="wardlistrow1" id="principal" name="principal"');
	
#			$smarty->assign('sDiagnosis',substr($result['diagnosis_principal'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');	
#			$smarty->assign('sTherapy',substr($result['therapy_principal'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
#			$smarty->assign('sDiagnosis','<span id="diagnosis_principal" name="diagnosis_principal">'.$result['diagnosis_principal'].'</span>');	
#			$smarty->assign('sTherapy','<span id="therapy_principal" name="therapy_principal">'.$result['therapy_principal'].'</span>');
			$smarty->assign('sDiagnosis',$result['diagnosis_principal']);	
			$smarty->assign('sTherapy',$result['therapy_principal']);
	
			ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
#			$smarty->assign('sDocsListRowsPrincipal',$sTemp);
		}else{
			$sTemp =	'		<tr class="wardlistrow1" id="principal" name="principal">
			<td colspan="2" align="center"><font color="red">No Principal Diagnosis/Procedure</font></td>
		</tr> 
';
		}
		$smarty->assign('sDocsListRowsPrincipal',$sTemp);

		$sTemp = '';
		if ( (!empty($result['diagnosis_others']) && isset($result['diagnosis_others'])) ||
			  (!empty($result['therapy_others']) && isset($result['therapy_others'])) ){
#			$smarty->assign('segSetHeadingOthers',TRUE);
			$smarty->assign('sRowClass','class="wardlistrow2" id="others" name="others"');
	
#			$smarty->assign('sDiagnosis',substr($result['diagnosis_others'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');	
#			$smarty->assign('sTherapy',substr($result['therapy_others'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
#			$smarty->assign('sDiagnosis','<span id="diagnosis_others" name="diagnosis_others">'.$result['diagnosis_others'].'</span>');	
#			$smarty->assign('sTherapy','<span id="therapy_others" name="therapy_others" >'.$result['therapy_others'].'</span>');
			$smarty->assign('sDiagnosis',$result['diagnosis_others']);	
			$smarty->assign('sTherapy',$result['therapy_others']);
	
			ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
#			$smarty->assign('sDocsListRowsOthers',$sTemp);
		}else{
			$sTemp =	'		<tr class="wardlistrow2" id="others" name="others">
			<td colspan="2" align="center"><font color="red">No Other Diagnosis/Procedure</font></td>
		</tr> 
';
		}
		$smarty->assign('sDocsListRowsOthers',$sTemp);
		
		//$smarty->assign('sDetailsIcon','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=details&type_nr='.$type_nr.'&nr='.$row['nr'].'"><img '.createComIcon($root_path,'info3.gif','0').'></a>');
		
		/*//check if user is from admitting section
		if($userDeptInfo['dept_nr'] == 148){
			if($encounter_type == 2){
				$setHidden = true; //hide  "Enter new records"
			}
		}*/
	}else{
	
		# Show no record prompt

		$smarty->assign('bShowNoRecord',TRUE);
	
		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>');
		$smarty->assign('norecordyet',$norecordyet);
	
	}
}elseif($mode=='details'){
	
	#echo " START mode = '".$mode."' <br> \n";
	#added by VAN 02-18-08
	/*
	$sTemprow = '<td width="840">
						{{include file="registration_admission/basic_data.tpl"}}				
					';
	$smarty->assign('sTrow',$sTemprow);				
	*/
	$smarty->assign('sShow',FALSE);
	
	$row=$result;

	#echo " row : "; print_r($row); echo " <br><br> \n";
	#echo " result_icp : "; print_r($result_icp); echo " <br><br> \n";
	#echo " enc_Info : "; print_r($enc_Info); echo " <br><br> \n";
	#echo " rResult : "; print_r($rResult); echo " <br><br> \n";
	#echo " rDisp : "; print_r($rDisp); echo " <br><br> \n";
	
	# Show the record details

	# Set the include file

	$smarty->assign('sDocsBlockIncludeFile','medocs/form.tpl');
	
	$smarty->assign('sExtraInfo',nl2br($row['aux_notes']));

	if(stristr($row['short_notes'],'got_medical_advice')) $smarty->assign('sYesNo',$LDYes);
		else $smarty->assign('sYesNo',$LDNo);
	
	$smarty->assign('sDiagnosis',nl2br($row['diagnosis']));
#	$smarty->assign('sTherapy',nl2br($row['therapy']));
	$smarty->assign('sTherapy',nl2br($result_icp['therapy']));
	
	//encounter_type=3&4 show Result Disposition
	if($enc_Info['encounter_type']=='3' || $enc_Info['encounter_type']=='4'){
		$smarty->assign('sSetResult',TRUE);
		$smarty->assign('sResult',$rResult['description']);
		$smarty->assign('sDisposition',$rDisp['descrip']);
	}else{
		$smarty->assign('sSetResult',FALSE);
	}
	
	$smarty->assign('sDate',formatDate2Local($row['date'],$date_format));
#	$smarty->assign('sAuthor',$row['personell_name']);
	$smarty->assign('sAuthor',$row['create_id']);

	#echo " END mode = '".$mode."' <br> \n";
	
# Create a new form for data entry################### 
}else {

	# Create a new entry form
	
	#added by VAN 02-18-08
	/*
	$sTemprow = '<td width="840">
						{{include file="registration_admission/basic_data.tpl"}}				
					</td>';
					
	$smarty->assign('sTrow',$sTemprow);
	*/
	$smarty->assign('sShow',FALSE);
	# Set the include file

	$smarty->assign('sDocsBlockIncludeFile','medocs/form.tpl');
	
	# Set form table as active form
	$smarty->assign('bSetAsForm',TRUE);
		
	//For ICD and ICP control Add by  Mark on March 29, 2007
	ob_start();
		require("gui_medocs_icd.inc.php");
	   $sCodeControl1= ob_get_contents();
	ob_end_clean();
	$smarty->assign('codeControl1',$sCodeControl1);
	
	//Operation Interface
	ob_start();
		require("gui_medocs_icp.inc.php");
		$sCodeControl2= ob_get_contents();
	ob_end_clean();
	$smarty->assign('codeControl2',$sCodeControl2);

	/*//populate diagnosis and procedure xajax
	if($mode=="new" && $is_discharged==0){
		$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
	}
	*/	
	$patient_enc = $enc_obj->getPatientEncounter($encounter_nr);
	$patient_enc_cond = $enc_obj->getPatientEncounterCond($encounter_nr);
	$patient_enc_disp = $enc_obj->getPatientEncounterDisp($encounter_nr);
	$patient_enc_res = $enc_obj->getPatientEncounterRes($encounter_nr);
		
	
	
	
	/*if(!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
	
	$userDeptInfo = $dept_obj->getUserDeptInfo($seg_user_name);
*/
	#echo "<br>gui_show_medocs : dept info = ".$userDeptInfo['dept_nr'];
	#echo "<br>gui_show_medocs : encounter_type = ".$encounter_type." - ".$enc_Info['encounter_type'];
	//user is from Medical Records
	if($userDeptInfo['dept_nr'] == 151){
		//Admission patient from OPD || patient from ER
		if(($encounter_type == 4 || $encounter_type == 3) && ($encounter_class_nr == 2 || $encounter_class_nr == 1)) {
			$rowResult=$objResDisp->_getResult("A");
			$rowDisp=$objResDisp->_getDisp("A");
			
			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			$setHidden = false; //show image save
						
			$smarty->assign('sSetResult',true); //Show Result row and Disposition row
			$smarty->assign('sSetCon',false); //Hide Condition row
			$smarty->assign('sSetDeptDiagnosis',false); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
			$smarty->assign('sSetDeptDischarged',true); //discharged department
			$smarty->assign('sSetDischarged',true); //show discharged time
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharde button
			
		//Admitted patient from OPD | same encounter 
		}elseif($encounter_type == 2 && $encounter_type_a == 4){
			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			$setHidden = true; //Hide image save

			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.   # burn added : June 4, 2007
			
			$smarty->assign('sSetDeptDiagnosis',false); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
#			$smarty->assign('sSetDeptDischarged',true); //discharged department   # burn commented : June 4, 2007
			$smarty->assign('sSetDischarged',false); //show discharged time
			$smarty->assign('sAdmittedOpd_a',true);
			$smarty->assign('sAdmittedOpd_b',false);
			# added by VAN 02-18-08
			$enableSave = 0;   #show save and discharde button
			
		//OPD patient only
		}elseif($encounter_type == 2 && $encounter_type_a == 2 ){
			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			$setHidden = false; //show image save
			
			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.   # burn added : June 4, 2007

			$smarty->assign('sDiagnosisNotes', false);
			$smarty->assign('sSetResult',FALSE); //Hide Result row and Disposition row
			$smarty->assign('sSetCon',FALSE); //Hide Condition row
			$smarty->assign('sSetDeptDiagnosis',false); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
//			$smarty->assign('sSetDeptDischarged',true); //discharged department  # burn commented : June 4, 2007
			#$smarty->assign('sSetDept',true);
			$smarty->assign('sSetDischarged',true); //show discharged time
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharde button
			
		}elseif($encounter_type == 2){
			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			$setHidden = false; //show image save
						
			$smarty->assign('sSetResult',FALSE); //Hide Result row and Disposition row
			$smarty->assign('sSetCon',FALSE); //Hide Condition row
			$smarty->assign('sSetDeptDiagnosis',false); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
			$smarty->assign('sSetDeptDischarged',true); //discharged department
			#$smarty->assign('sSetDept',true);
			$smarty->assign('sSetDischarged',true); //show discharged time
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharde button
		}
		
	//User is from Admitting section department	
	}elseif($userDeptInfo['dept_nr'] == 148){
		//ER patient
		if($encounter_type ==1 && $encounter_class_nr == 1){
			$rowCond=$objResDisp->_getCondition("E");
			$rowResult=$objResDisp->_getResult("E");
			$rowDisp=$objResDisp->_getDisp("E");
			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			
			$smarty->assign('sDiagnosisNotes', true); //show admitting diagnosis
			$smarty->assign('txtAreaDiagnosis','<textarea name="aux_notes" id="aux_notes" cols="70" rows="3" wrap="physical" readonly="readonly">'.trim($aux_notes_d).'</textarea>');
			
			$setHidden = false; //show image save
			
			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.
			$smarty->assign('sSetCon',false); //hide Condition row
			$smarty->assign('sSetResult',false); //Show Result row and Disposition row
			$smarty->assign('sSetDeptDiagnosis',true); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
			$smarty->assign('sSetDeptdischarged',true); //discharged department
			$smarty->assign('sSetDischarged',true); //show discharged time		
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharde button
		//Admission from ER (patient from ER)
		}elseif($encounter_type == 3 && $encounter_class_nr == 1 ){
			$rowCond=$objResDisp->_getCondition("E");
			$rowResult=$objResDisp->_getResult("E");
			$rowDisp=$objResDisp->_getDisp("E");
			
			//populate diagnosis and procedure xajax
			if($mode=="new" && $is_discharged==0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			$smarty->assign('sDiagnosisNotes', true); //show admitting diagnosis
			//echo "patient_enc_diagnosis =".$patient_enc['er_opd_diagnosis'];
			
			//$smarty->assign('txtAreaDiagnosis','<textarea name="aux_notes" id="aux_notes" cols="80" rows="3" wrap="physical" readonly="readonly">'.ucwords(strtolower(trim($aux_notes_d))).'</textarea>');
			$smarty->assign('txtAreaDiagnosis','<textarea name="aux_notes" id="aux_notes" cols="80" rows="3" wrap="physical" readonly="readonly">'.trim($patient_enc['er_opd_diagnosis']).'</textarea>');
			$setHidden = true; //hide image save
			
			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.
			$smarty->assign('sSetResult',false); //Show Result row and Disposition row
			$smarty->assign('sSetCon',false); //Hide Condition row
			$smarty->assign('sSetDeptDiagnosis',true); //show Select Doctors and Departments for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
			$smarty->assign('sSetDischarged',false); //show discharged time
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 0;   #show save and discharde button
		}//end elseif 
	
	//user from ER-triage Department
	}elseif($userDeptInfo['dept_nr'] == 149){
		if($encounter_type == 1 && $encounter_class_nr == 1){
			$rowCond=$objResDisp->_getCondition("E");
			$rowResult=$objResDisp->_getResult("E");
			$rowDisp=$objResDisp->_getDisp("E");
			
			if($mode == "new" && $is_discharged == 0){
				$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
			}
			
			$setHidden = false; //show image save
			
			$smarty->assign('sSetConsult',true); //show consulting doctors & departments.
			$smarty->assign('sSetResult',true); // show result row and Disposition row
			$smarty->assign('sSetCon',true); // show condition row
			$smarty->assign('sSetDeptDiagnosis', true); // show Doctor and Departement combobox for diagnosis
			$smarty->assign('sSetDeptTherapy',true); //show # 2 Select Doctors and Departments for therapy
			$smarty->assign('sSetDischarged', true); // show discharged time
			$smarty->assign('sAdmittedOpd_a',false);
			$smarty->assign('sAdmittedOpd_b',true);
			# added by VAN 02-18-08
			$enableSave = 1;   #show save and discharde button
			
		}// end if (encounter_type)
		
	}else{
		//no permission
		 
	}
		
	//Display condition, result, disposition checkbox/radio
	if($encounter_type!=2){
		//Display Condition for ER admission only if encounter_type = 1
		if(($encounter_type == 1 ||$encounter_type == 3) && $encounter_class_nr != 2){ 
			if(is_object($rowCond)){
				$sTmp ='';
				$c=0;
				$cond_code = $patient_enc_cond['cond_code'];
				while($cond=$rowCond->FetchRow()){
					$sTmp =$sTmp.'<input name="cond_code" id="cond_code" type="radio" value="'.$cond['cond_code'].'" ';
					if($cond_code == $cond['cond_code']) $sTmp = $sTmp.'checked';
					#if($cond_code == $cond['cond_code']) $sTmp = $sTmp.'checked';
					$sTmp = $sTmp.'>';
					$sTmp = $sTmp.$cond['cond_desc']."<br>";
					//if(isset($$tmpCond) &&!empty($$tmpCond)) $sTmp.$tmpCond;
					//else $sTmp = $sTmp.$cond['cond_desc']."<br />";
					if($c<=2){
						$rowConditionA = $sTmp;
						if($c==2){ $sTmp='';}
					}else{ $rowConditionB = $sTmp;}
					$c++;
				}
			}
			$smarty->assign('rowConditionA',$rowConditionA);
			$smarty->assign('rowConditionB',$rowConditionB);
		}

		//Display option for Result/ Inpatient 
		if(is_object($rowResult)){ 
			$sTmp = '';
			$count=0;	
			while($result=$rowResult->FetchRow()){
				$sTmp=$sTmp.'<input name="result_code" id="result_code" type="radio" value="'.$result['result_code'].'" ';
				if($result_code == $result['result_code']) $sTmp= $sTmp.'checked';
				$sTmp = $sTmp.'>';
				$sTmp = $sTmp.$result['result_desc']."<br>";
					
					if($count<=2){
						$rowResultA =$sTmp;
						if($count==2){$sTmp='';}
					}else{ $rowResultB =$sTmp; }
				$count++;
			} 
		}
		$smarty->assign('rowResultA',$rowResultA);
		$smarty->assign('rowResultB',$rowResultB);
		
		//Display Disposition  
		if(is_object($rowDisp)){
			$sTmp = '';
			$count=0;
			while($result=$rowDisp->FetchRow()){
				$sTmp = $sTmp.'<input name="disp_code" id="disp_code" type="radio" value="'.$result['disp_code'].'" ';
				if($disp_code == $result['disp_code']) $sTmp = $sTmp.'checked';
				$sTmp = $sTmp.'>';
				$sTmp = $sTmp.$result['disp_desc']."<br>";
				
				if($count<=2){
					$rowDispA = $sTmp;
					if($count==2) $sTmp = '';
				}else{ $rowDispB = $sTmp; }
				$count++;
			}
		}
		$smarty->assign('rowDispA',$rowDispA);
		$smarty->assign('rowDispB',$rowDispB);
	
	}//End of if Statement encounter_type!=2
		
	# Collect extra javascript
	
	ob_start();
	
?>
	<script language="javascript">
	<!-- Script Begin

	function chkForm(d) {
		/*
		if(!d.short_notes[0].checked&&!d.short_notes[1].checked){
			alert("<?php echo $LDPlsMedicalAdvice ?>");
			d.short_notes[0].focus();
			return false;
		}else if(d.date.value==""){
			alert("<?php echo $LDPlsEnterDate ?>");
			d.date.focus();
			return false;
		}else if(d.personell_name.value==""){
			alert("<?php echo $LDPlsEnterFullName ?>");
			d.personell_name.focus();
			return false;
		}else{
			return true;
		} */
		alert("save");
	}
	
	function validate_f(){
		trimString($('date_text_d"'));
		trimString($('time_text_d'));
		trimString($('current_doc_nr_f'));
		trimString($('current_dept_nr_f'));
		alert("inside function validate_f! ");
		if ($F('date_text_d"')==''){
			alert("Enter the Discharge Date!");
			$('date_text_d"').focus();
			return false;
		}else if ($F('time_text_d')==''){
			alert("Enter the Discharge Time!");
			$('time_text_d').focus();
			return false;
		}else if ($F('current_doc_nr_f')==0){
			alert("Select an Attending Physician!");
			$('current_doc_nr_f').focus();
			return false;
		}else if ($F('current_dept_nr_f')==0){
			alert("Select an Attending Department!");
			$('current_dept_nr_f').focus();
			return false;
		}else{
			return true;
		}
	}/* end of function validate_f */
	
	//  Script End -->
	</script>
	
<?php	

	$sTemp = ob_get_contents();
	ob_end_clean();

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	$smarty->assign('sDocsJavaScript',$sTemp);
	
	
	#echo "encounter_date->".$enc_Info['er_opd_datetime']."<br>";
	#echo "admission_date->".$enc_Info['admission_dt'];
	
	// show ER encounter date/time and OPD encounter date/time 
	if($encounter_type == 1 || $encounter_type == 2){
		//show er encounter date & time
		$smarty->assign('sAdmissionDate',@formatDate2Local($enc_Info['er_opd_datetime'],$date_format));
		$smarty->assign('sAdmissionTime',@formatDate2Local($enc_Info['er_opd_datetime'],$date_format,FALSE,TRUE));
	// show admitting date & time 
	}else{
		//show admitting date & time  
		$smarty->assign('sAdmissionDate',@formatDate2Local($enc_Info['admission_dt'],$date_format));
		$smarty->assign('sAdmissionTime',@formatDate2Local($enc_Info['admission_dt'],$date_format,FALSE,TRUE));
	}

	if($is_discharged==0){
#		$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');   # burn commented : June 6, 2007
		$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');    # burn added : June 6, 2007
	}else{
#		$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local($patient_enc['discharge_date'],$date_format).'" id="date_text_d" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');   # burn commented : June 6, 2007
		$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local($patient_enc['discharge_date'],$date_format).'" id="date_text_d" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');    # burn added : June 6, 2007
	}
	$smarty->assign('sDateValidateJs_p',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_p" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');
	
#	comment by mark on April 2, 2007	
#	$smarty->assign('sYesRadio',"<input type='radio' name='short_notes' value='got_medical_advice'>");
#	$smarty->assign('sNoRadio',"<input type='radio' name='short_notes' value=''>");
	
	$TP_href_date="javascript:show_calendar('entryform.date','".$date_format."')";
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$TP_date_format=$$dfbuffer;
	
	//$TP_img_calendar='<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_trigger" style ="cursor:pointer">';
	//$smarty->assign('sDateMiniCalendar','<a href="'.$TP_href_date.'">'.$TP_img_calendar.'</a> <font size=1>['.$TP_date_format.']</font>');
	
	$smarty->assign('sDateMiniCalendar_d','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger_d" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
	$smarty->assign('sDateMiniCalendar_p','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger_p" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
	
	/*$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
			inputField : \"date_text\", ifFormat : \"$phpfd\", showsTime : false, button : \"date_trigger\", singleClick : true, step : 1
	});
	</script>
	";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
	 */
	//$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');
	
	#set format time onkeyup event
#	$smarty->assign('sFormatTime','onblur="setFormatTime()"');   # burn commented : June 6, 2007
	$smarty->assign('sFormatTime','onChange="setFormatTime(this,\'selAMPM\')"');   # burn added : June 6, 2007
	//$smarty->assign('sFormatTime','onblur="setFTime()"');
	
	if($enc_Info['encounter_type']=="4"){
		$smarty->assign('bSetEntry',TRUE);
	}
	
	#-----------------edited by VAN 02-18-08
	#USe this for ER discharged consultant to select doctors
		#if(($encounter_type == 1)||($encounter_type == 2)){
			$sDoc = '';
			#uncommented by VAN 02-18-08
				$sDoc = $sDoc.'<select id="current_doc_nr_c" name="current_doc_nr_c" onChange="jsGetDepartment_c();" >
							<option value="0">-Select a Doctor-</option>';
				$sDoc = $sDoc.'</select>';

	#}else{
		#$sDoc = $enc_Info['er_opd_admitting_physician_name'];
	#}
	$smarty->assign('consultingDoc', $sDoc);
	
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_c" name="current_dept_nr_c" onChange="jsGetDoctors_c();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';

	$smarty->assign('consultingDept',$sDept);
	#-----------------------------------------------
	
	//Use this for ER discharged diagnosis to select doctors
	$sDoc ='';
	$sDoc = $sDoc.'<select id="current_doc_nr_d" name="current_doc_nr_d" onChange="jsGetDepartment_d();" >
							<option value="0">-Select a Doctor-</option>';
	$sDoc = $sDoc.'</select>';
	$smarty->assign('sDoctorInputD',$sDoc);
	
	//Display combo for Doctors & Departments //Use this ER discharged diagnosis code to select department
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_d" name="current_dept_nr_d" onChange="jsGetDoctors_d();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';
	$smarty->assign('sDeptInputD',$sDept);
	
	//Use this for ER discharged procedure to select doctors
	$sDoc ='';
	$sDoc = $sDoc.'<select id="current_doc_nr_p" name="current_doc_nr_p" onChange="jsGetDepartment_p();" >
							<option value="0">-Select a Doctor-</option>';
	$sDoc = $sDoc.'</select>';
	$smarty->assign('sDoctorInputP',$sDoc);
	
	//Use this for ER discharged procedure code to select department
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_p" name="current_dept_nr_p" onChange="jsGetDoctors_p();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';
	$smarty->assign('sDeptInputP',$sDept);
	
	
	
	//Time of performed procedure
	$stime = '';
	$stime = $stime.'<input type="text" id="time_text_p" name="time_text_p" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM_p\')" />&nbsp;';
	$stime = $stime.'<select id="selAMPM_p" name="selAMPM_p">
						<option value="A.M.">A.M.</option>
						<option value="P.M.">P.M.</option>';
	$stime = $stime.'</select>';
	$smarty->assign('sTimeP',$stime);

########################## START ###########################
	//Use this for Final discharged diagnosis and procedure to select department || doctors
	$sDoc ='';
#	$sDoc = $sDoc.'<select id="current_doc_nr_f" name="current_doc_nr_f" onChange="jsGetDepartment_f();" >
#							<option value="0">-Select a Doctor-</option>';   # burn commented : June 4, 2007
	$sDoc = $sDoc.'<select id="current_doc_nr_f" name="current_doc_nr_f">
							<option value="0">-Select a Doctor-</option>';   # burn added : June 4, 2007
	$sDoc = $sDoc.'</select>';
	$smarty->assign('sDoctorInputF',$sDoc);

	//Display combo for Doctors & Departments //Use this ER discharged diagnosis code to select department
/*
	      # burn commented : June 4, 2007
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_f" name="current_dept_nr_f" onChange="jsGetDoctors_f();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';
*/
	if(($encounter_type == 1)||($encounter_type == 2)){
		$sDept = "\n (".$enc_Info['er_opd_admitting_dept_name'].")";
		$sDept = $sDept.' <input type="hidden" name="current_dept_nr_f" id="current_dept_nr_f"  value ="'.$enc_Info['er_opd_admitting_dept_nr'].'">';
	}else{
		$sDept = "\n (".$enc_Info['name_formal'].")";
		$sDept = $sDept.' <input type="hidden" name="current_dept_nr_f" id="current_dept_nr_f"  value ="'.$enc_Info['current_dept_nr'].'">';
	}
	
	$smarty->assign('sDeptInputF',$sDept);
######################### END ###############################
	
#echo "gui_show_medocs.php : enc_Info['er_opd_admitting_physician_name'] = '".$enc_Info['er_opd_admitting_physician_name']."' <br> \n";
#	$add_preset_c="";    #commented by VAN 02-18-08

/*
	if(($encounter_type == 1)||($encounter_type == 2)){
	#echo "hello";
		$sDoc = '';
		#uncommented by VAN 02-18-08
		$sDoc = $sDoc.'<select id="current_doc_nr_c" name="current_doc_nr_c" onChange="jsGetDepartment_c();">
						<option value="0">-Selct a Doctor-</option>';   # burn commented : June 2, 2007
		#$add_preset_c="preset_c();";
		#commented by VAN 02-18-08
		
		#$sDoc = $sDoc.'<select id="current_doc_nr_c" name="current_doc_nr_c">
		#				<option value="0">-Select a Doctor-</option>';   # burn added : June 2, 2007
						
		$sDoc = $sDoc.'</select>';
		#$add_preset_c="preset_c();";
				
	}else{
		$sDoc = $enc_Info['er_opd_admitting_physician_name'];
	}
	$smarty->assign('consultingDoc', $sDoc);
	
   # burn commented : June 2, 2007
	#uncommented by VAN 02-18-08
	$sDept = '';
	$sDept = $sDept.'<select id="current_dept_nr_c" name="current_dept_nr_c" onChange="jsGetDoctors_c();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';

#echo "gui_show_medocs.php : enc_Info['name_formal'] = '".$enc_Info['name_formal']."' <br> \n";
	#commented by VAN 02-18-08
	
	#$sDept = "\n (".$enc_Info['er_opd_admitting_dept_name'].")";
	#$sDept = $sDept.' <input type="hidden" name="current_dept_nr_c" id="current_dept_nr_c"  value ="'.$enc_Info['er_opd_admitting_dept_nr'].'">';
	
	$smarty->assign('consultingDept',$sDept);
	//load Dept. / Doctors combo box.
*/ 	

	$smarty->assign('sTailScripts2','<script language="javascript">preset_d();preset_p();preset_f();preset_c();loadConDispResData();</script>');	
#commented by VAN 02-18-08
/*
	$smarty->assign('sTailScripts2',"<script language='javascript'>preset_d();preset_p();preset_f(); $add_preset_c loadConDispResData();</script>");	
*/	
		$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');
	//Add yahoo script here
     #onSelectR();
	/* $smarty->assign('sTailScripts','<script>addKeysListener();</script>'); */
	
	ob_start();

?>
			<!--EDITED: SEGWORKS -->
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_text_d", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_d", singleClick : true, step : 1
			});	
			</script>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_text_p", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_p", singleClick : true, step : 1
			});	
			</script>
	<?php
			
		$sDateJS .= $calendarSetup;
		$smarty->assign('TP_user_name',$HTTP_SESSION_VARS['sess_user_name']);

	# Collect hidden inputs
	//ob_start();
?>
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?php echo $HTTP_SESSION_VARS['sess_en']; ?>">
<input type="hidden" name="pid" value="<?php echo $HTTP_SESSION_VARS['sess_pid']; ?>">
<input type="hidden" name="modify_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="create_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="create_time" value="null">
<input type="hidden" name="mode" value="create">
<input type="hidden" name="target" value="<?php echo $target; ?>">
<input type="hidden" name="edit" value="<?php echo $edit; ?>">
<input type="hidden" name="is_discharged" value="<?php if(!empty($is_discharged)) echo $is_discharged; else echo $enc_obj->Is_Discharged($encounter_nr); ?>">

<input type="hidden" name="current_dept_nr" id="current_dept_nr"  value ="<?php echo $enc_Info['current_dept_nr']; ?>">
<input type="hidden" name="current_att_dr_nr" id="current_att_dr_nr"  value ="<?php echo $enc_Info['attending_physician_nr']; ?>">

<input type="hidden" name="consulting_dept_nr" id="consulting_dept_nr"  value ="<?php echo $enc_Info['er_opd_admitting_dept_nr']; ?>">
<input type="hidden" name="consulting_dr_nr" id="consulting_dr_nr"  value ="<?php echo $enc_Info['er_opd_admitting_physician_nr']; ?>">

<input type="hidden" name="encounter_class_nr" id="encounter_class_nr" value="<?= $enc_Info['encounter_class_nr']?>">
<input type="hidden" name="encounter_type" id="encounter_type" value="<?php if(!empty($encounter_type)) echo $encounter_type; else echo $patient['encounter_type']; ?>">

<input type="hidden" name="dob" id="dob" value="<?=@formatDate2Local($date_birth,$date_format)?>">
<input type="hidden" name="gender" id="gender" value="<?=$sex?>">

<input type="hidden" name="cond_code_h" id="cond_code_h" value="<?=$patient_enc_cond['cond_code']?>">
<input type="hidden" name="disp_code_h" id="disp_code_h" value="<?=$patient_enc_disp['disp_code']?>">
<input type="hidden" name="result_code_h" id="result_code_h" value="<?= $patient_enc_res['result_code']?>">
<input type="hidden" name="discharge_time_h" id="discharge_time_h" value="<?=$patient_enc['discharge_time']?>">

<?php 
	#added by VAN 02-18-08
	if($mode=="new" && $enableSave){
?>
		<input type="<?php if($setHidden) echo "hidden"; else echo "image"; ?>" onclick="if (setFrmSubmt()){ document.entryform.submit(); }" title="Save and Discharge" <?php echo createLDImgSrc($root_path,'savedisc2.gif','0'); ?>>
<?php } ?>
<script>
	
(function(){	
	var inputa = function (e){
		e = e || window.event.e;
		if(e.keyCode == '123'){
			inputCodeHandler("icdCode", "<?=$HTTP_SESSION_VARS['sess_en'] ?>", "<?=$encounter_type ?>","<?=$encounter_type_a ?>", "<?= $HTTP_SESSION_VARS['sess_user_name']?>");			
		}
	}
	YAHOO.util.Event.on("icdCode","keypress", inputa);
	var inputb = function (e){
		e = e || window.event.e;
		if(e.keyCode == '123'){
			inputCodeHandler("icpCode", "<?=$HTTP_SESSION_VARS['sess_en'] ?>", "<?=$encounter_type ?>", "<?=$encounter_type_a ?>", "<?= $HTTP_SESSION_VARS['sess_user_name']?>");			
		}
	}
	YAHOO.util.Event.on("icpCode","keypress", inputb);
		
})();

</script>
<?php
																									//$enc_obj->Is_Discharged($encounter_nr)
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sHiddenInputs',$sTemp);
	
} 

//if(($mode=='show'||$mode=='details')&&!$enc_obj->Is_Discharged()){
if ($mode=='show'||$mode=='details'){
	if($enc_diagnosis=='') $enc_diagnosis=TRUE;
	//&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'
	if(($mode=='show'||$mode=='details')&&!$enc_obj->Is_Discharged()){
		
		if($userDeptInfo['dept_nr'] == 148){
			if($encounter_type == 2) $smarty->assign('sHideNewRecLink',false);
			else $smarty->assign('sHideNewRecLink',true);
		}else{
			$smarty->assign('sHideNewRecLink',true);
		}
				
		$smarty->assign('sNewLinkIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>');
		//$smarty->assign('sNewRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=new&type_nr='.$type_nr.'">'.$LDEnterNewRecord.'</a>');
		$lnk="<a href=".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr.">".$LDEnterNewRecord."</a>";
		$smarty->assign('sNewRecLink','<span id="enterNewRecord">'.$lnk.'</span>');
		
	}else{
		$smarty->assign('bSetAsForm',TRUE);
	/*	$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icd");xajax_populateCode("'.$encounter_nr.'","'.$encounter_type.'","icp");</script>');*/
		#edited by VAN 02-19-08
		/*
		$smarty->assign('sHideNewRecLink',true);
		$smarty->assign('sNewLinkIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>');
		$lnk="<a href=".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr.">Edit Record </a>";
		$smarty->assign('sNewRecLink','<span id="enterNewRecord">'.$lnk.'</span>');
		*/
		if (!$discharged){
			$smarty->assign('sHideNewRecLink',true);
			$smarty->assign('sNewLinkIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>');
			$lnk="<a href=".$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr.">Edit Record </a>";
			$smarty->assign('sNewRecLink','<span id="enterNewRecord">'.$lnk.'</span>');
		}	
	}
	
	//for OPD and Inpatient View Mode
	if($enc_Info['encounter_type']==4 || $encounter_type == 4){	
		$smarty->assign('isOpdInpatient', TRUE);
	}

	$lnk=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=2&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr;
	$smarty->assign('segOpdBtn','<input type="image" onclick="xajax_showDiagnosisTherapy('.$encounter_nr.',2,\''.$lnk.'\')" '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>OPD '); // tabs images.. 

	$lnk=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=4&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr;
	$smarty->assign('segInpatientBtn', '<input type="image" onclick="xajax_showDiagnosisTherapy('.$encounter_nr.',4,\''.$lnk.'\')" '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>INPATIENT '); // tabs images..
	
	if($mode=='details'){
		$smarty->assign('sPdfLinkIcon','<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'>');
		$smarty->assign('sMakePdfLink','<a href="'.$root_path."modules/pdfmaker/medocs/report.php".URL_APPEND."&enc=".$HTTP_SESSION_VARS['sess_en']."&mnr=".$nr.'&target='.$target.'" target=_blank>'.$LDPrintPDFDoc.'</a>');
	}
}

	ob_start();
?>
	<script>
	function redirectWindow(){	
		window.location.href ="<?=$thisfile.URL_APPEND."&pid=".$HTTP_SESSION_VARS['sess_pid']."&encounter_nr=".$HTTP_SESSION_VARS['sess_en']."&target=".$target."&tabs=".$tabs."&mode=new&type_nr=".$type_nr."&is_discharged=".$enc_obj->Is_Discharged($encounter_nr)."&encounter_type=".$encounter_type."&encounter_type_a=".$encounter_type_a."&encounter_class_nr=".$encounter_class_nr;?>";
	}
	(function(){
		var init = function (e){
			e = e || window.event.e;
			if(e.keyCode == '121'){
				redirectWindow();
			}
		}
		YAHOO.util.Event.addListener(window, "keypress", init);
	})();
	
	</script>
	
<?php		
	$sTmp = ob_get_contents();
	ob_end_clean();
	
	$smarty->assign('sKeyListener',$sTmp);

if(($mode!='show'&&!$nolist) ||($mode=='show'&&$nolist&&$rows>1)){
	//&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'
	$smarty->assign('sListLinkIcon','<img '.createComIcon($root_path,'l-arrowgrnlrg.gif','0','absmiddle').'>');
	$smarty->assign('sListRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=show&type_nr='.$type_nr.'&encounter_class_nr = '.$encounter_class_nr.'">'.$LDShowDocList.'</a>');
	
}

$smarty->assign('pbBottomClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancelClose.'"  align="absmiddle"></a>');
# if discharged do the ff codes
# begin
#commented by VAN 02-19-08
#$discharged = $enc_obj->Is_Discharged($encounter_nr);
#echo "discharged = '".$discharged."'<br> \n";
#echo "mode = '".$mode."'<br> \n";
		# burn added : April 28, 2007
	if ( $enc_obj->Is_Discharged($encounter_nr) && ($mode=="show")){
			# set print form if the encounter is already discharged
			# and it is in view mode

			$segPrintIcon = '<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'  title="Print this form."  align="absmiddle">';
			if ($encounter_type==1){   # Clinical Cover Sheet for ER patient
				#echo "<br>hello<br>";
	#			$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>".$segPrintIcon."ER Clinical Form Sheet</a>";
				$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_er_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>ER Clinical Form Sheet</a>";
			}elseif ($encounter_type==2){   # Clinical Cover Sheet for Outpatient
	#			$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_opd_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>".$segPrintIcon."OPD Clinical Form Sheet</a>";
				$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_opd_clinical_form.php?encounter_nr=$encounter_nr\" target=_blank>OPD Clinical Form Sheet</a>";
			}elseif (($encounter_type==3)||($encounter_type==4)){   # Clinical Cover Sheet for Inpatient
	#			$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>".$segPrintIcon."Inpatient Clinical Cover Sheet</a>";
				$formToPrint = "<a href=\"".$root_path."modules/registration_admission/show_cover_sheet.php?encounter_nr=$encounter_nr\" target=_blank>Inpatient Clinical Cover Sheet</a>";
			}	
		$smarty->assign('segPrint','&nbsp;&nbsp;&nbsp;&nbsp;'.$segPrintIcon.'<span id="printForm">'.$formToPrint.'</span>');   # burn added : April 28, 2007
	}

$smarty->assign('sMainBlockIncludeFile','medocs/main.tpl');

$smarty->display('common/mainframe.tpl');

?>