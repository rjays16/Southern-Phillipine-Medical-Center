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
<?php 
	
	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/medocs_function.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'modules/medocs/js/medocs_combo.js"></script>';
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
if($mode=='show'){	
	
	if($rows){
		# Set the document list template file
		
		$smarty->assign('sDocsBlockIncludeFile','medocs/docslist_frame.tpl');

		$smarty->assign('LDDetails',$LDDetails);

		$sTemp = '';
		$toggle=0;
		$row=$result;

		$smarty->assign('segSetHeadingPrincipal',FALSE);
		$smarty->assign('segSetHeadingOthers',FALSE);

		$smarty->assign('segHeadingPrincipal','Principal:');
		$smarty->assign('segHeadingOthers','Others:');

		if (!empty($result['diagnosis_principal']) && isset($result['diagnosis_principal'])){
			$smarty->assign('segSetHeadingPrincipal',TRUE);
			$smarty->assign('sRowClass','class="wardlistrow1"');
	
#			$smarty->assign('sDiagnosis',substr($result['diagnosis_principal'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');	
#			$smarty->assign('sTherapy',substr($result['therapy_principal'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
			$smarty->assign('sDiagnosis',$result['diagnosis_principal']);	
			$smarty->assign('sTherapy',$result['therapy_principal']);
	
			ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
	
			$smarty->assign('sDocsListRowsPrincipal',$sTemp);
		}
		$sTemp = '';
		if (!empty($result['diagnosis_others']) && isset($result['diagnosis_others'])){
			$smarty->assign('segSetHeadingOthers',TRUE);
			$smarty->assign('sRowClass','class="wardlistrow2"');
	
#			$smarty->assign('sDiagnosis',substr($result['diagnosis_others'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');	
#			$smarty->assign('sTherapy',substr($result['therapy_others'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
			$smarty->assign('sDiagnosis',$result['diagnosis_others']);	
			$smarty->assign('sTherapy',$result['therapy_others']);

			ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
			ob_end_clean();
	
			$smarty->assign('sDocsListRowsOthers',$sTemp);
		}
		//$smarty->assign('sDetailsIcon','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=details&type_nr='.$type_nr.'&nr='.$row['nr'].'"><img '.createComIcon($root_path,'info3.gif','0').'></a>');
	}else{
	
		# Show no record prompt

		$smarty->assign('bShowNoRecord',TRUE);

		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>');
		$smarty->assign('norecordyet',$norecordyet);

	}
}elseif($mode=='details'){
	
	echo " START mode = '".$mode."' <br> \n";

	$row=$result;

	echo " row : "; print_r($row); echo " <br><br> \n";
	echo " result_icp : "; print_r($result_icp); echo " <br><br> \n";
	echo " enc_Info : "; print_r($enc_Info); echo " <br><br> \n";
	echo " rResult : "; print_r($rResult); echo " <br><br> \n";
	echo " rDisp : "; print_r($rDisp); echo " <br><br> \n";
	
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

	echo " END mode = '".$mode."' <br> \n";
# Create a new form for data entry
}else {

	# Create a new entry form

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
	
	ob_start();
		require("gui_medocs_icp.inc.php");
		$sCodeControl2= ob_get_contents();
	ob_end_clean();
	$smarty->assign('codeControl2',$sCodeControl2);


//========================== FIXS THIS AREA========================	// March 29, 2007
	//populate diagnosis and procedure xajax
	if($mode=="new" && $is_discharged==0 && ($encounter_type==1 || $encounter_type==2 || $encounter_type==3 ||$encounter_type==4 )){
		$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","icd");xajax_populateCode("'.$encounter_nr.'","icp");</script>');
	}
	
	//Display ER condition/Results/Disposition
	if($encounter_type==1){
		$rowCond=$objResDisp->_getCondition("E");
		$rowResult=$objResDisp->_getResult("E");
		$rowDisp=$objResDisp->_getDisp("E");
		
		//note insert code here to retrieve condition, disposition and result
		//$condition_classes=$enc_obj->AllConditionClassesObject();
		//$results_classes=$enc_obj->AllResultsClassesObject();
		//$disposition_classes=$enc_obj->AllDispositionClassesObject();
				
		$smarty->assign('sSetCon',TRUE); //Show Condition row
		$smarty->assign('sSetResult',TRUE); //Show Result row and Disposition row
	
	//Note: fix this for direct admission: March 29, 2007 identify class_encounter
	//Display Admission  Result/Disposition  
	}elseif($encounter_type==3 || $encounter_type==4){
		$rowResult=$objResDisp->_getResult("A");
		$rowDisp=$objResDisp->_getDisp("A");
		
		$smarty->assign('sSetResult',TRUE); //Show Result row and Disposition row
	// Hide Condition/Result/Disposition
	// For OPD encoding for ICD and ICP 
	}else{
		$smarty->assign('sSetResult',FALSE); //Hide Result row and Disposition row
		$smarty->assign('sSetCon',FALSE); //Hide Condition row
	}
    
	//Display condition, result, disposition checkbox/radio
	if($encounter_type!=2){
		//Display Condition for ER admission only if encounter_type = 1
		if($encounter_type==1){ 
			if(is_object($rowCond)){
				$sTmp ='';
				$c=0;
				while($cond=$rowCond->FetchRow()){
					$tmpCond = $cond['cond_desc'];
					$sTmp .='<input name="cond_code" type="radio" value="'.$cond['cond_code'].'">';
					if(isset($$tmpCond) &&!empty($$tmpCond)) $sTmp.$tmpCond;
					else $sTmp = $sTmp.$cond['cond_desc']."<br />";
					
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

		//Display Result 
		if(is_object($rowResult)){ 
			$sTmp = '';
			$count=0;	
			while($result=$rowResult->FetchRow()){
				$sTmp=$sTmp.'<input name="result_code" id="result_code" type="radio" value"'.$result['result_code'].'" ';
				if($result_code == $result['result_code']) $sTemp= $sTemp.'checked';
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
				if($disp_code == $result['disp_code']) $sTemp = $sTemp.'checked';
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
	
	
	//  Script End -->
	</script>
<?php

	$sTemp = ob_get_contents();
	ob_end_clean();

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	$smarty->assign('sDocsJavaScript',$sTemp);
	
	$smarty->assign('sAdmissionDate',@formatDate2Local($enc_Info['admission_dt'],$date_format));
	$smarty->assign('sAdmissionTime',@formatDate2Local($enc_Info['admission_dt'],$date_format,FALSE,TRUE));
	
	$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');
#	$smarty->assign('sDateValidateJs_p',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_p" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');

#comment by mark on April 2, 2007	
#	$smarty->assign('sYesRadio',"<input type='radio' name='short_notes' value='got_medical_advice'>");
#	$smarty->assign('sNoRadio',"<input type='radio' name='short_notes' value=''>");
	
	$TP_href_date="javascript:show_calendar('entryform.date','".$date_format."')";
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$TP_date_format=$$dfbuffer;
	
	//$TP_img_calendar='<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="date_trigger" style ="cursor:pointer">';
	
	//$smarty->assign('sDateMiniCalendar','<a href="'.$TP_href_date.'">'.$TP_img_calendar.'</a> <font size=1>['.$TP_date_format.']</font>');
	
	$smarty->assign('sDateMiniCalendar_d','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger_d" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
#	$smarty->assign('sDateMiniCalendar_p','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger_p" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
	
	/*$jsCalScript = "<script type=\"text/javascript\">
	Calendar.setup ({
			inputField : \"date_text\", ifFormat : \"$phpfd\", showsTime : false, button : \"date_trigger\", singleClick : true, step : 1
	});
	</script>
	";
	$smarty->assign('jsCalendarSetup', $jsCalScript);
	 */
	//$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');
	$smarty->assign('sFormatTime','onkeyup="setFormatTime()"');
	 
	$smarty->assign('bSetEntry',TRUE);
		
	//For Diagnosis 
	$sDept = $sDept.'<select id="current_dept_nr_d" name="current_dept_nr_d" onChange="jsGetDoctors_d();" >
							<option value="0">-Select a Department-</option>';
	$sDept = $sDept.'</select>';
	$smarty->assign('sDeptInputD',$sDept);
	$sDoc = $sDoc.'<select id="current_doc_nr_d" name="current_doc_nr_d" onChange="jsGetDepartment_d();" >
							<option value="0">-Select a Doctor-</option>';
	$sDoc = $sDoc.'</select>';
	$smarty->assign('sDoctorInputD',$sDoc);
	
	//For Procedure
/*	
	$sDeptp = $sDeptp.'<select id="current_dept_nr_p" name="current_dept_nr_p" onChange="jsGetDoctors_p();" >
							<option value="0">-Select a Department-</option>';
	$sDeptp = $sDeptp.'</select>';
	$smarty->assign('sDeptInputP',$sDeptp);
	$sDocp = $sDocp.'<select id="current_doc_nr_p" name="current_doc_nr_p" onChange="jsGetDepartment_p();" >
							<option value="0">-Select a Doctor-</option>';
	$sDocp = $sDocp.'</select>';
	$smarty->assign('sDoctorInputP',$sDocp);
*/	
#	$smarty->assign('sTailScripts2','<script language="javascript">preset_d();preset_p();</script>');
	$smarty->assign('sTailScripts2','<script language="javascript">preset_d();</script>');	
	
	ob_start();
	?>
	
			<!--EDITED: SEGWORKS -->
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_text_d", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_d", singleClick : true, step : 1
			});
						
			</script>
	<?php
			
		$sDateJS .= $calendarSetup;
	
		$smarty->assign('TP_user_name',$HTTP_SESSION_VARS['sess_user_name']);

	# Collect hidden inputs
	
	//ob_start();
	
?>
<input type="hidden" name="encounter_nr" value="<?php echo $HTTP_SESSION_VARS['sess_en']; ?>">
<input type="hidden" name="pid" value="<?php echo $HTTP_SESSION_VARS['sess_pid']; ?>">
<input type="hidden" name="modify_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="create_id" value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>">
<input type="hidden" name="create_time" value="null">
<input type="hidden" name="mode" value="create">
<input type="hidden" name="target" value="<?php echo $target; ?>">
<input type="hidden" name="edit" value="<?php echo $edit; ?>">
<input type="hidden" name="is_discharged" value="<?php if(!empty($is_discharged)) echo $is_discharged; else echo $enc_obj->Is_Discharged($encounter_nr); ?>">
<input type="hidden" nam="encounter_type" value="<?php if(!empty($encounter_type)) echo $encounter_type; else echo $patient['encounter_type']; ?>">
<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0'); ?>>
<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','1'); ?>>
<?php
																									//$enc_obj->Is_Discharged($encounter_nr)
	$sTemp = ob_get_contents();
	ob_end_clean();

	$smarty->assign('sHiddenInputs',$sTemp);
	
} 


if(($mode=='show'||$mode=='details')&&!$enc_obj->Is_Discharged()){
	if($enc_diagnosis=='') $enc_diagnosis=TRUE;
	//&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'
	$smarty->assign('sNewLinkIcon','<img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','absmiddle').'>');
	//$smarty->assign('sNewRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=new&type_nr='.$type_nr.'">'.$LDEnterNewRecord.'</a>');
	$smarty->assign('sNewRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&tabs='.$tabs.'&mode=new&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$enc_Info['encounter_type'].'">'.$LDEnterNewRecord.'</a>');
	//$patient_enc['encounter_type']
	
	//tabs for Dept. Diagnosis & Clinical Diagnosis  #mark added March 23, 2007
	// default refers to interdepartamental diagnosis
	//$smarty->assign('sDeptDiagnosis','<a href="'.$thisfile.URL_APPEND.'&from=such&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target=entry&tabs=0&mode=show&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">Other Diagnosis</a>');
	//final diagnosis & procedure
	//$smarty->assign('sFinalDiagnosis','<a href="'.$thisfile.URL_APPEND.'&from=such&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target=entry&tabs=1&mode=show&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">Principal Diagnosis</a>');
	
	if($mode=='details'){
		$smarty->assign('sPdfLinkIcon','<img '.createComIcon($root_path,'icon_acro.gif','0','absmiddle').'>');
		$smarty->assign('sMakePdfLink','<a href="'.$root_path."modules/pdfmaker/medocs/report.php".URL_APPEND."&enc=".$HTTP_SESSION_VARS['sess_en']."&mnr=".$nr.'&target='.$target.'" target=_blank>'.$LDPrintPDFDoc.'</a>');
	}
} 
if(($mode!='show'&&!$nolist) ||($mode=='show'&&$nolist&&$rows>1)){
	//&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'
	$smarty->assign('sListLinkIcon','<img '.createComIcon($root_path,'l-arrowgrnlrg.gif','0','absmiddle').'>');
	$smarty->assign('sListRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=show&type_nr='.$type_nr.'">'.$LDShowDocList.'</a>');

}

$smarty->assign('pbBottomClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancelClose.'"  align="absmiddle"></a>');

$smarty->assign('sMainBlockIncludeFile','medocs/main.tpl');

$smarty->display('common/mainframe.tpl');

?>