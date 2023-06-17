<?php

#created by VAN 03-27-08
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

include_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_med_abstract.php');

define("IPBM_DEPT", 182);

$enc_obj=new Encounter;
$dept_obj=new Department;
$pers_obj=new Personell;
$med_obj=new MedAbstract;

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
	$encounter_nr = $_POST['encounter_nr'];
}

$viewonly = isset($_GET['viewonly']) ? $_GET['viewonly'] : 0;

$errorMsg='';

if (isset($_POST['mode'])){

	
	function stripslashes_deep($value)
	{
	    $value = is_array($value) ?
	                array_map('stripslashes_deep', $value) :
	                stripslashes($value);

	    return $value;
	} 

	if((function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())){
	    // stripslashes_deep($_GET);
	    $_POST = stripslashes_deep($_POST);
	    $HTTP_POST_VARS = stripslashes_deep($HTTP_POST_VARS);
	    // stripslashes_deep($_COOKIE);
	} 

	switch($_POST['mode']) {

		case 'save':
			
			$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_login_username']." \n";
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_login_username'];
			$HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
			
			if ($med_obj->saveMedAbstractInfoFromArray($HTTP_POST_VARS)){
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$med_obj->getErrorMsg().'</font>';			
			}
		break;
		case 'update':
			$HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_login_username']." \n";
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_login_username'];
			$HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
			if ($med_obj->updateMedAbstractInfoFromArray($HTTP_POST_VARS)){
				$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
			}else{
				$errorMsg='<font style="color:#FF0000">'.$med_obj->getErrorMsg().'</font>';			
			}
			#echo "sql = ".$med_obj->sql;
		break;
	}# end of switch statement
}

#transferred by VAN 06-13-08
if($encounter_nr){
#	if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
	if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
		echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
		exit();
	}
	#extract($encInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
	exit();
}

$medAbstractInfo = $med_obj->getMedAbsRecord($encounter_nr);

#added by VAN 06-28-08
  if ($encInfo['current_dept_nr'])	
	$dept_nr = $encInfo['current_dept_nr'];
  else	
	$dept_nr = $encInfo['consulting_dept_nr'];
	
#----------------------

?>
<?php

	$attendingPhysOptions = "";
	$att_physList = $pers_obj->getDoctorByDept(IPBM_DEPT, '1');
	if($att_physList){
		
		while ($r = $att_physList->FetchRow()) {
			if($medAbstractInfo || !empty($medAbstractInfo)){
				$selected = ($medAbstractInfo["dr_nr"] == $r["personell_nr"]) ? "selected" : "";
			}else{
				$selected = ($HTTP_SESSION_VARS["sess_login_personell_nr"] == $r["personell_nr"]) ? "selected" : "";
			}
			
			$att_physName = $r["name_last"].', '.$r["name_first"] . " " . $r["name_middle"];
			$attendingPhysOptions .='<option value="'.$r["personell_nr"].'" '.$selected.'>'. $att_physName .'</option>';
		
		}
			
	}

?>
<html>
<head>
<style type="text/css">
<!--
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #F8F9FA;
}
.style2 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}

.style3 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
}

.font-red{
	color: red !important;
}
-->
</style>
<?php
echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
/*echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";*/
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
?>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<script language="javascript">
	String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };

	function chkForm(){

		
		if ($F('diagnosis')==''){
			alert(" Please enter Diagnosis field.");
			$('diagnosis').focus();
			return false;
		}
		
		if ($F('remarks')==''){
			alert(" Please enter Remarks field");
			$('remarks').focus();
			return false;
		}
		
		return true;
	}

	
	
	
	function printMedAbst(id,is_enc_ipbm){
		var doc = document.getElementById('dr_nr');
		var doc_name = doc.options[doc.selectedIndex].text;
		if (doc.selectedIndex == 0)
			doc_name='';


		if (id==0) 
			id="";
		if(is_enc_ipbm){
			if (window.showModalDialog){  //for IE
				window.showModalDialog("show_medical_abstract_certificate_ipbm.php?id="+id+"&doc_name="+doc_name,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
			}else{
				window.open("show_medical_abstract_certificate_ipbm.php?id="+id+"&doc_name="+doc_name,"medicalCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
			}
		}
		else{
			alert('for ipbm patient only.');
			return;
		}
	}
	
	
</script>
</head>


<body>
<table width="520" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
	<tr>
		<td colspan="*"><?= $errorMsg ?></td>
	</tr>
	<tr>		
		<td colspan="*" height="23" bgcolor="#FFFFFF" >
			<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
				<tr>
					<td width="18%" ></td>
					<td width="37%" >&nbsp;</td>
					<td width="28%" align="right" ><? echo 'Case No. '?></td>
					<td width="17%" align="left"><? echo $encounter_nr; ?></td>
				</tr>
				<tr>
					<td><span class="style3"><? echo "Name :"?> </span></td>
					<td colspan="2" class="style2">
						<? 
						
							$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
							echo $name;
						
						?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
						<span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td><span class="style3"><? echo "Address :"?></span></td>
					<td colspan="2" class="style2">
                        <?
                        if ($encInfo['brgy_name'] == NULL && $encInfo['mun_name'] && $encInfo['zipcode'] == NULL){
                            echo stripslashes(strtoupper($encInfo['street_name']));
                        }else {
                            echo stripslashes(strtoupper($encInfo['street_name'])).",&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['brgy_name'])).",&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($encInfo['zipcode']));
                        }

                        ?>
					</td>
					<td>&nbsp;</td>
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td colspan="*" align="left" valign="top" bgcolor="#F8F9FA">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="*" width="467" height="23" bgcolor="#FFFFFF">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="15" align="left" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
					<td width="442" background="images/top_05.jpg">&nbsp;</td>
					<td width="10" background="images/top_05.jpg" bgcolor="#FFFFFF">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<form id="med_abstract" name="abstract" method="post" action="" onSubmit="return chkForm()">
	
	<tr id="space5">
		<td>&nbsp;
			<input type="hidden" name="civil_status" value="<?= isset($encInfo['civil_status']) ? $encInfo['civil_status'] : '' ?>">
			<input type="hidden" name="age" value="<?= isset($encInfo['age']) ? $encInfo['age'] : '' ?>">
		</td>
	</tr>

	
	<tr>
		<td valign="top" bgcolor="#F8F9FA" >
			Brief History <div style="padding-left: 161px; position: absolute; margin-top: -15px;">:</div>
		</td>
	</tr>

	<tr>
			<td><div style="padding-left: 180px; margin-top: -14px;">
				<textarea cols="33" rows="5" name="brief_hist" id="brief_hist"><?php echo $medAbstractInfo['brief_hist']; ?></textarea>		</div>	</td>
	</tr>
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA" >
			Mental Status Examination<div style="padding-left: 161px; position: absolute; margin-top: -15px;">:</div>
		</td>
	</tr>

	<tr>
			<td><div style="padding-left: 180px; margin-top: -14px;">
				<textarea cols="33" rows="5" name="mental_status" id="mental_status"><?php echo $medAbstractInfo['mental_status']; ?></textarea>		</div>	</td>
	</tr>
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA" class="font-red">
			Diagnosis <div style="padding-left: 161px; position: absolute; margin-top: -15px;">:</div>
		</td>
	</tr>
	<tr>
			<td><div style="padding-left: 180px; margin-top: -14px;">
				<textarea cols="33" rows="5" name="diagnosis" id="diagnosis" required><?php echo $medAbstractInfo['diagnosis']; ?></textarea>		</div>	</td>
	</tr>
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td valign="top" bgcolor="#F8F9FA" class="font-red">
			Remarks <div style="padding-left: 161px; position: absolute; margin-top: -15px;">:</div>
		</td>
	</tr>
	<tr>
			<td><div style="padding-left: 180px; margin-top: -14px;">
				<textarea cols="33" rows="5" name="remarks" id="remarks" wrap="physical" required><?php echo $medAbstractInfo['remarks']; ?></textarea>		</div>	</td>
	</tr>
	
	<tr id="space5">
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td valign="top" bgcolor="#F8F9FA" >
			Attending Physician <div style="padding-left: 161px; position: absolute; margin-top: -15px;">:</div>
		</td>
	</tr>
	<tr>
			<td><div style="padding-left: 180px; margin-top: -14px;">
				<select name="dr_nr" id="dr_nr">
					<option>Select a Doctor</option>

					<?= $attendingPhysOptions; ?>
					
				</select></div>	</td>
	</tr>

	<tr id="space5">
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
		if ($viewonly != 1) {
			
		
			if (!$medAbstractInfo || empty($medAbstractInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printMedAbst('.$encounter_nr.',1)">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
?>
			&nbsp; &nbsp;
			<input type="button" name="Cancel" value="Cancel"  onclick="javascript:window.parent.cClick()">
			<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
		</td>
<?php
		}else{
			if ($medAbstractInfo || !empty($medAbstractInfo)){
				echo '<input type="button" name="Print" value="Print" onClick="printMedAbst('.$encounter_nr.',1)">'."\n &nbsp; &nbsp;";
			}
		}
?>
	</tr>
	
	</form>
</table>

</body>
</html>
