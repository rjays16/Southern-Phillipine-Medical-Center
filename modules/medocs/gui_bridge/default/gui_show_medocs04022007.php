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
		
	
	//echo "<br>\n (mode=show) row in gui_show_medocs = <br> ";
	//print_r($rows);
	//echo "code=>".print_r($row);
	//echo " <br> \n";
	
	if($tabs==0){		
		echo "<br>";		
		print_r($result);
		while($dept_array=$segdept->FetchRow()){
			$dept_no = $dept_array['seg_dept'];
			$tmpresult = $result;
	
			echo "<br>dept_no->".$dept_no;
			$seg_diagnosis="";
			$seg_therapy="";
			$seg_date="";
			$seg_author="";
			if($toggle) $smarty->assign('sRowClass','class="wardlistrow2"');
				else $smarty->assign('sRowClass','class="wardlistrow1"');
			$toggle=!$toggle;
			while($row=$result->FetchRow()){
				echo "<br>while(row->)".$row['dept_nr'];
				if($row['dept_nr'] == $dept_no){
#					if($toggle) $smarty->assign('sRowClass','class="wardlistrow2"');
#						else $smarty->assign('sRowClass','class="wardlistrow1"');
#					$toggle=!$toggle;
#					if(!empty($row['date'])) $smarty->assign('sDate',@formatDate2Local($row['date'],$date_format));
#						else $smarty->assign('sDate','?');
		
					if($row['type']==1){
						//if(!empty($row['diagnosis'])) $smarty->assign('sDiagnosis',substr($row['diagnosis'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');						
						if(!empty($row['diagnosis'])){
							$seg_diagnosis.=$row['diagnosis']."<br>\n";
							$seg_date = $row['date'];
							$seg_author = $row['create_id'];
						}
					}
					if(!empty($row['short_notes'])) $smarty->assign('sShortNotes','[ '.$row['short_notes'].' ]');
					
		#			if(!empty($row['therapy'])) $smarty->assign('sTherapy',substr($row['therapy'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
					if($row['type']==2){
						//if(!empty($result_icp['therapy'])) $smarty->assign('sTherapy',substr($result_icp['therapy'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
						if(!empty($row['diagnosis'])){
							$seg_therapy.=$row['diagnosis']."<br>\n";
							$seg_date = $row['date'];
							$seg_author = $row['create_id'];
						}
					}
					//temp fields
					//if(!empty($rResult['description'])) $smarty->assign('sResult',$rResult['description']);
					//if(!empty($rDisp['descrip'])) $smarty->assign('sDisposition',$disp['description']);
					} //End of IF
			} // End of while loop					

			if(!empty($seg_date)) $smarty->assign('sDate',@formatDate2Local($seg_date,$date_format));
			else $smarty->assign('sDate','?');
			#if(!empty($seg_diagnosis))
				$smarty->assign('sDiagnosis',substr($seg_diagnosis,0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');
			
			#if(!empty($seg_therapy))
				$smarty->assign('sTherapy',substr($seg_therapy,0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
				
					$smarty->assign('sMakePdfIcon','<a href="'.$root_path.'modules/pdfmaker/medocs/report.php'.URL_APPEND.'&enc='.$HTTP_SESSION_VARS['sess_en'].'&mnr='.$row['nr'].'&target='.$target.'" target=_blank><img '.createComIcon($root_path,'pdf_icon.gif','0').'></a>');
		#			if($row['personell_name']) $smarty->assign('sAuthor',$row['personell_name']);
				$smarty->assign('sAuthor',$seg_author);
				$smarty->assign('sDetailsIcon','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=details&type_nr='.$type_nr.'&nr='.$row['nr'].'"><img '.createComIcon($root_path,'info3.gif','0').'></a>');
				
				//add column for department diagnosis ang operation
				$smarty->assign('sDept_nr','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&tabs='.$tabs.'&mode=new&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">'.$LDEnterNewRecord.'</a>');
				
					ob_start();
						$smarty->display('medocs/docslist_row.tpl');
						$sTemp = $sTemp.ob_get_contents();
					ob_end_clean();
			
			//print_r($tmpresult);
			$result = $tmpresult;
			
		} //end while loop 

	}else{
		while($row=$result->FetchRow()){
			
				if($toggle) $smarty->assign('sRowClass','class="wardlistrow2"');
				else $smarty->assign('sRowClass','class="wardlistrow1"');
				$toggle=!$toggle;
				if(!empty($row['date'])) $smarty->assign('sDate',@formatDate2Local($row['date'],$date_format));
				else $smarty->assign('sDate','?');

				if(!empty($row['diagnosis'])) $smarty->assign('sDiagnosis',substr($row['diagnosis'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']).'<br>');
				if(!empty($row['short_notes'])) $smarty->assign('sShortNotes','[ '.$row['short_notes'].' ]');
					
				#			if(!empty($row['therapy'])) $smarty->assign('sTherapy',substr($row['therapy'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
				if(!empty($result_icp['therapy'])) $smarty->assign('sTherapy',substr($result_icp['therapy'],0,$GLOBAL_CONFIG['medocs_text_preview_maxlen']));
					
					
				$smarty->assign('sDetailsIcon','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&mode=details&type_nr='.$type_nr.'&nr='.$row['nr'].'"><img '.createComIcon($root_path,'info3.gif','0').'></a>');
				$smarty->assign('sMakePdfIcon','<a href="'.$root_path.'modules/pdfmaker/medocs/report.php'.URL_APPEND.'&enc='.$HTTP_SESSION_VARS['sess_en'].'&mnr='.$row['nr'].'&target='.$target.'" target=_blank><img '.createComIcon($root_path,'pdf_icon.gif','0').'></a>');
				#			if($row['personell_name']) $smarty->assign('sAuthor',$row['personell_name']);
				if($row['create_id']){
					$smarty->assign('sAuthor',$row['create_id']);
					$smarty->assign('sDept_nr','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&tabs='.$tabs.'&mode=new&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">'.$LDEnterNewRecord.'</a>');
				}

				ob_start();
				$smarty->display('medocs/docslist_row.tpl');
				$sTemp = $sTemp.ob_get_contents();
				ob_end_clean();
			//} //End of IF
		} // End of while loop
	}
		$smarty->assign('sDocsListRows',$sTemp);
	
	}else{
	
		# Show no record prompt

		$smarty->assign('bShowNoRecord',TRUE);

		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'>');
		$smarty->assign('norecordyet',$norecordyet);

	}
}elseif($mode=='details'){

	$row=$result;
	
	# Show the record details

	# Set the include file

	$smarty->assign('sDocsBlockIncludeFile','medocs/form.tpl');
	
	$smarty->assign('sExtraInfo',nl2br($row['aux_notes']));

	if(stristr($row['short_notes'],'got_medical_advice')) $smarty->assign('sYesNo',$LDYes);
		else $smarty->assign('sYesNo',$LDNo);
	
	$smarty->assign('sDiagnosis',nl2br($row['diagnosis']));
#	$smarty->assign('sTherapy',nl2br($row['therapy']));
	$smarty->assign('sTherapy',nl2br($result_icp['therapy']));
	
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
	if($mode=="new" && $is_discharged==0 && ($encounter_type==1 || $encounter_type==2)){
		$smarty->assign('sTailScripts','<script language="javascript">xajax_populateCode("'.$encounter_nr.'","icd","'.$tabs.'");xajax_populateCode("'.$encounter_nr.'","icp","'.$tabs.'");</script>');
	}
	
	//Display ER condition/Results/Disposition
	if($encounter_type==1){
		$rowCond=$objResDisp->_getCondition("E");
		$rowResult=$objResDisp->_getResult("E");
		$rowDisp=$objResDisp->_getDisp("E");
		
		$smarty->assign('sSetCon',TRUE); //Show Condition row
		$smarty->assign('sSetResult',TRUE); //Show Result row and Disposition row
	
	//Note: fix this for direct admission: March 29, 2007 identify class_encounter
	//Display Admission  Result/Disposition  
	}elseif($encounter_type==3 || $encounter_type==4) {
		$rowResult=$objResDisp->_getResult("A");
		$rowDisp=$objResDisp->_getDisp("A");
		
		$smarty->assign('sSetResult',TRUE); //Show Result row and Disposition row
	// Hide Condition/Result/Disposition 
	}else{
		$smarty->assign('sSetResult',FALSE); //Hide Result row and Disposition row
		$smarty->assign('sSetCon',FALSE); //Show Condition row
	}

	if($encounter_type!=2){
		//Display Condition checkbox
		if($encounter_type==1){
			$sTmp ='';
			$c=0;
			while($cond=$rowCond->FetchRow()){
				$tmpCond = $cond['cond_desc'];
				$sTmp .='<input name="con" type="radio" value="'.$cond['cond_code'].'">';
				if(isset($$tmpCond) &&!empty($$tmpCond)) $sTmp.$tmpCond;
				else $sTmp = $sTmp.$cond['cond_desc']."<br />";
				
				if($c<=2){
					$rowConditionA = $sTmp;
					if($c==2){ $sTmp='';}
				}else{ $rowConditionB = $sTmp;}
				$c++;
			}
	
			$smarty->assign('rowConditionA',$rowConditionA);
			$smarty->assign('rowConditionB',$rowConditionB);
		}
	
		//Display Result checkbox 
		$sTmp = '';
		$count=0;	
		while($result=$rowResult->FetchRow()){
			 $tmpResult=$result['result_desc'];
			 	#$sTmp .='<input name="res['.$result['result_code'].']" type="checkbox" value="'.$result['result_code'].'">';		
				$sTmp .='<input name="res" type="radio" value="'.$result['result_code'].'">';		
				if(isset($$tmpResult) &&!empty($$tmpResult))$sTmp.$tmpResult;
				else $sTmp = $sTmp.$result['result_desc']."<br />";
				
				if($count<=2){
					$rowResultA =$sTmp;
					if($count==2){$sTmp='';}
				}else{ $rowResultB =$sTmp; }
			$count++;
		} 
		
		$smarty->assign('rowResultA',$rowResultA);
		$smarty->assign('rowResultB',$rowResultB);
	
		//Display Disposition checkbox 
		$sTmp = '';
		$count=0;
		while($result=$rowDisp->FetchRow()){
			 $tmpResult2=$result['disp_desc']; 
			 $sTmp.='<input name="disp['.$result['disp_code'].']" type="checkbox" value="'.$result['disp_code'].'">';		
			 #$sTmp.='<input name="disp" type="radio" value="'.$result['disp_code'].'">';		
				if(isset($$tmpResult2) &&!empty($$tmpResult2)) $sTmp.$$tmpResult2;
				else $sTmp = $sTmp.$result['disp_desc']."<br />";			
	
				if($count<=2){
					$rowDispA = $sTmp;
					if($count==2) $sTmp = '';
				}else{ $rowDispB = $sTmp; }
			$count++;
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
		}
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

	$smarty->assign('sDateValidateJs_d',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_d" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');
	$smarty->assign('sDateValidateJs_p',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="date_text_p" onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');
	
	$smarty->assign('sYesRadio',"<input type='radio' name='short_notes' value='got_medical_advice'>");
	$smarty->assign('sNoRadio',"<input type='radio' name='short_notes' value=''>");
	
	
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
	$sDeptp = $sDeptp.'<select id="current_dept_nr_p" name="current_dept_nr_p" onChange="jsGetDoctors_p();" >
							<option value="0">-Select a Department-</option>';
	$sDeptp = $sDeptp.'</select>';
	$smarty->assign('sDeptInputP',$sDeptp);
	$sDocp = $sDocp.'<select id="current_doc_nr_p" name="current_doc_nr_p" onChange="jsGetDepartment_p();" >
							<option value="0">-Select a Doctor-</option>';
	$sDocp = $sDocp.'</select>';
	$smarty->assign('sDoctorInputP',$sDocp);
	
	$smarty->assign('sTailScripts2','<script language="javascript">preset_d();preset_p();</script>');
		
	
	ob_start();
	?>
	
			<!--EDITED: SEGWORKS -->
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "date_text_d", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_d", singleClick : true, step : 1
			});
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

<input type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','1'); ?>>hello
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
	$smarty->assign('sNewRecLink','<a href="'.$thisfile.URL_APPEND.'&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target='.$target.'&tabs='.$tabs.'&mode=new&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">'.$LDEnterNewRecord.'</a>');
	
	
	//tabs for Dept. Diagnosis & Clinical Diagnosis  #mark added March 23, 2007
	// default refers to interdepartamental diagnosis
	$smarty->assign('sDeptDiagnosis','<a href="'.$thisfile.URL_APPEND.'&from=such&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target=entry&tabs=0&mode=show&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">Other Diagnosis</a>');
	//final diagnosis & procedure
	$smarty->assign('sFinalDiagnosis','<a href="'.$thisfile.URL_APPEND.'&from=such&pid='.$HTTP_SESSION_VARS['sess_pid'].'&encounter_nr='.$HTTP_SESSION_VARS['sess_en'].'&target=entry&tabs=1&mode=show&type_nr='.$type_nr.'&is_discharged='.$enc_obj->Is_Discharged($encounter_nr).'&encounter_type='.$patient_enc['encounter_type'].'">Principal Diagnosis</a>');
	
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