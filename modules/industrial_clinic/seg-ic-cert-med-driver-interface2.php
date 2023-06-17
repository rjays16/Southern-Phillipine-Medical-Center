<?php

#created by Cherry 09-14-09
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');


#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'/include/care_api_classes/class_drg.php');
$objDRG= new DRG;

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;

require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_department.php');

#Added by Cherry 08-05-10
require_once($root_path.'include/care_api_classes/class_radiology.php');
$radio_obj = new SegRadio;

require_once($root_path.'include/care_api_classes/class_vitalsign.php');
$vital_sign = new SegVitalsign();

require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
$obj_medCert = new SegICCertMed;

$dept_obj=new Department;
$pers_obj=new Personell;

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
		$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
		$encounter_nr = $_POST['encounter_nr'];
}
// echo "encounter_nr= ".$encounter_nr."<br>";
#Added by Cherry 08-09-10
if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']){
	$refno = $_POST['refno'];
}
// echo "refno= ".$refno."<br>";
#echo "had_surgery= ".$_POST['had_surgery']."<br>";
//include_once($root_path.'include/care_api_classes/class_cert_med.php');
//$obj_medCert = new MedCertificate($encounter_nr);


$errorMsg='';

//Added by Cherry 08-12-10
global $HTTP_SESSION_VARS;

$seg_user_nr = $HTTP_SESSION_VARS['sess_temp_personell_nr'];

 if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

 $dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

$personell = $pers_obj->get_Personell_info($seg_user_nr);

$person_details = $pers_obj->getPersonellInfo($seg_user_name);
# echo "s = ".$dept_obj->sql;

if (stristr($personell['job_function_title'],'doctor')===FALSE)
		$is_doctor = 0;
else
		$is_doctor = 1;

		$area = $_GET['area'];
	$area_type = $_GET['area_type'];

	$refno = $_GET['refno'];
//End Cherry

//-----------Added by Cherry 08-12-10------------
if (isset($_POST['mode'])){

	//$trans = $obj_medCert->getInfoTrans($encounter_nr);
	$trans_date = $trans['trxn_date'];

	switch($_POST['mode']) {
				case 'save':
				//if(!$_POST['abnormality'])
				//	$_POST['abnormality'] = '';

				//array
				$data = array('refno'=>$refno,
									'height'=>$_POST['height'],
									'height_unit'=>$_POST['height_unit'],
									'weight'=>$_POST['weight'],
									'weight_unit'=>$_POST['weight_unit'],
									'systole'=>$_POST['systole'],
									'diastole'=>$_POST['diastole'],
									'general_physique'=>$_POST['general_physique'],
									'abnormality'=>$_POST['abnormality'],
									'contagious_diseases'=>$_POST['contagious_diseases'],
									'left_eye_snellen'=>$_POST['left_eye_snellen'],
									'right_eye_snellen'=>$_POST['right_eye_snellen'],
									'left_eye_acuity'=>$_POST['left_eye_acuity'],
									'right_eye_acuity'=>$_POST['right_eye_acuity'],
									'left_ear'=>$_POST['left_ear'],
									'right_ear'=>$_POST['right_ear'],
									'left_lower_extremities'=>$_POST['left_lower_extremities'],
									'right_lower_extremities'=>$_POST['right_lower_extremities'],
									'remarks'=>$_POST['remarks'],
									'comment_drive'=>$_POST['comment_drive'],
									'conditions'=>$_POST['conditions'],
									'educ_attain_nr'=>$_POST['educ_attain_nr'],
									'work_status_nr'=>$_POST['work_status_nr'],
									'employ_nr'=>$_POST['employ_nr'],
									'vehicle_nr'=>$_POST['vehicle_nr'],
									'create_dt'=>date('Y-m-d H:i:s'),
									'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'left_eye_other'=>$_POST['left_eye_other'],
									'right_eye_other'=>$_POST['right_eye_other'],
									'left_upper_extremities'=>$_POST['left_upper_extremities'],
									'right_upper_extremities'=>$_POST['right_upper_extremities'],
									'exam_physician'=>$_POST['exam_physician'],
									'with_disease'=>$_POST['with_disease'],
									'control_number'=>$_POST['control_number']

						);


						if($obj_medCert->saveCertMedDriverInfoFromArray($data)){
							$cert_nr = $db->Insert_ID();
						//if($save2){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
				break;
				case 'update':
				//$info = $obj_medCert->getAllInfoCertMed($refno);
				//$cert_nr = $medcertDriverInfo['cert_nr'];
				$cert = $obj_medCert->getCertNrDriver($refno);
				$cert_nr = $cert['cert_nr'];
				//echo "cert_nr= ".$cert_nr."<br>";
				//array
				$data = array('refno'=>$refno,
									'cert_nr'=>$cert_nr,
									'height'=>$_POST['height'],
									'height_unit'=>$_POST['height_unit'],
									'weight'=>$_POST['weight'],
									'weight_unit'=>$_POST['weight_unit'],
									'systole'=>$_POST['systole'],
									'diastole'=>$_POST['diastole'],
									'general_physique'=>$_POST['general_physique'],
									'abnormality'=>$_POST['abnormality'],
									'contagious_diseases'=>$_POST['contagious_diseases'],
									'left_eye_snellen'=>$_POST['left_eye_snellen'],
									'right_eye_snellen'=>$_POST['right_eye_snellen'],
									'left_eye_acuity'=>$_POST['left_eye_acuity'],
									'right_eye_acuity'=>$_POST['right_eye_acuity'],
									'left_ear'=>$_POST['left_ear'],
									'right_ear'=>$_POST['right_ear'],
									'left_lower_extremities'=>$_POST['left_lower_extremities'],
									'right_lower_extremities'=>$_POST['right_lower_extremities'],
									'remarks'=>$_POST['remarks'],
									'comment_drive'=>$_POST['comment_drive'],
									'conditions'=>$_POST['conditions'],
									'educ_attain_nr'=>$_POST['educ_attain_nr'],
									'work_status_nr'=>$_POST['work_status_nr'],
									'employ_nr'=>$_POST['employ_nr'],
									'vehicle_nr'=>$_POST['vehicle_nr'],
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'left_eye_other'=>$_POST['left_eye_other'],
									'right_eye_other'=>$_POST['right_eye_other'],
									'left_upper_extremities'=>$_POST['left_upper_extremities'],
									'right_upper_extremities'=>$_POST['right_upper_extremities'],
									'exam_physician'=>$_POST['exam_physician'],
									'with_disease'=>$_POST['with_disease'],
									'control_number'=>$_POST['control_number']
						);

						if ($obj_medCert->updateCertMedDriverInfoFromArray($data)){
							//echo $obj_medCert->sql;
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
				break;
		}# end of switch statement
}

//-------------------End Cherry------------------


if($encounter_nr){
	//echo "encounter_nr= ".$encounter_nr."<br>";
	 #if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
				echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
				exit();
		}
		#extract($encInfo);
}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
		exit();
}



$trans = $obj_medCert->getInfoTrans($encounter_nr);
$medcertDriverInfo = $obj_medCert->getAllInfoCertMedDriver($refno);
$vehicle = $obj_medCert->getVehicleDriven();
$employment = $obj_medCert->getTypeEmployment();
$work_status = $obj_medCert->getWorkStatus();
$education = $obj_medCert->getEducationalAttainment();
$height_unit = $obj_medCert->getUnitsHeight();
$weight_unit = $obj_medCert->getUnitsWeight();
#$medico_cases = $enc_obj->getMedicoCases();
if($medcertDriverInfo=='' || empty($medcertDriverInfo) || !$medcertDriverInfo){
	$medcertDriverInfo = 1;
}
$vs = $vital_sign->get_latest_vital_signs($encInfo['pid'], $encounter_nr);
//print_r($vs);
//echo "ss= ".$encInfo['consulting_dr_nr'];
//print_r($encInfo);
//echo "<br>"."scsc= ".$medcertDriverInfo."<br>";
//print_r($medcertDriverInfo);
#echo "encInfo['encounter_type'] = '".$encInfo['encounter_type']."' <br> \n";

#---------setup options for Educational Attainment---------#
$listEducAttain=array();
	if(is_object($education)){
		while($educInfo = $education->FetchRow()){
				$listEducAttain[$educInfo['educ_attain_nr']] = $educInfo['educ_attain_name'];
		}
	}
#-------------setup options for Work Status---------------#
$listWorkStatus=array();
	if(is_object($work_status)){
		while($workInfo = $work_status->FetchRow()){
			$listWorkStatus[$workInfo['work_status_nr']] = $workInfo['work_status_name'];
		}
	}
#------------setup options for Kind of Employment---------#
$listEmployment=array();
	if(is_object($employment)){
		while($employInfo = $employment->FetchRow()){
			$listEmployment[$employInfo['type_employment_nr']] = $employInfo['type_employment_name'];
		}
	}
#---------setup options for Motor Vehicle Driven----------#
$listVehicle=array();
	if(is_object($vehicle)){
		while($vehicleInfo = $vehicle->FetchRow()){
			$listVehicle[$vehicleInfo['vehicle_drive_nr']] = $vehicleInfo['vehicle_drive_name'];
		}
	}
#--------------setup options for height unit--------------#
$listHeightUnit=array();
	if(is_object($height_unit)){
		while($heightUnit = $height_unit->FetchRow()){
			$listHeightUnit[$heightUnit['unit_id']] = $heightUnit['unit_name'];
		}
	}
#-------------setup options for weight unit---------------#
$listWeightUnit=array();
	if(is_object($weight_unit)){
		while($weightUnit = $weight_unit->FetchRow()){
			$listWeightUnit[$weightUnit['unit_id']] = $weightUnit['unit_name'];
		}
	}
#----------------------------end -------------------------#

$listDoctors=array();

#echo "encInfo['current_dept_nr'] = '".$encInfo['current_dept_nr']."' <br> \n";
#added by VAN 06-28-08
	if ($encInfo['current_dept_nr'])
		$dept_nr = $encInfo['current_dept_nr'];
	else
		$dept_nr = $encInfo['consulting_dept_nr'];


			 $doctors = $pers_obj->getDoctors(1);

	if (is_object($doctors)){
		while($drInfo=$doctors->FetchRow()){

				$middleInitial = "";
				if (trim($drInfo['name_middle'])!=""){
						$thisMI=split(" ",$drInfo['name_middle']);
						foreach($thisMI as $value){
								if (!trim($value)=="")
										$middleInitial .= $value[0];
						}
						if (trim($middleInitial)!="")
								$middleInitial .= ". ";
				}

				#$name_doctor = trim($drInfo["name_first"])." ".trim($drInfo["name_2"])." ".$middleInitial.trim($drInfo["name_last"]);
				#$name_doctor = "Dr. ".$name_doctor;
				$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
				$name_doctor = ucwords(strtolower($name_doctor)).", MD";

				#echo "<br> dr = ".$name_doctor;
				#$listDoctors['doctor_name']=$name_doctor;

				$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
				#$listDoctors['doctor_nr']=$drInfo["personell_nr"];

				#print_r($listDoctors);
		}
 }

#----------------------

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

.style4 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
	border-width: 2px;
	padding: 1px;
	border-style: ridge;
	border-color: #CCCCFF;
	/*background-color: #CCCCFF;*/
	background-color: #CCCCCC;
	-moz-border-radius: 0px 0px 0px 0px;
}

table.sample {
	border-width: 2px;
	border-spacing: 2px;
	border-style: solid;
	border-color: blue;
	border-collapse: collapse;
	background-color: rgb(255, 250, 250);
}
table.sample th {
	border-width: 2px;
	padding: 1px;
	border-style: inset;
	border-color: gray;
	background-color: white;
	-moz-border-radius: 0px 0px 0px 0px;
}
table.sample td {
	border-width: 2px;
	padding: 1px;
	border-style: inset;
	border-color: gray;
	background-color: white;
	-moz-border-radius: 0px 0px 0px 0px;
}

-->
</style>
<?php
echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

#added by VAN 06-13-08
#require($root_path.'include/inc_checkdate_lang.php');
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
/*echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";*/
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
echo '<script type="text/javascript" src="'.$root_path.'js/jquery/jquery-1.8.2.js"></script>'."\n";  
echo '<script type="text/javascript" src="js/med-cert-drivers.js"></script>';
?>

<script language="javascript">
<?php
		require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script language="javascript">
		String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };

		function chkForm(){
					//alert('sdcdscadsc ');
		var d = document.cert_med;
			/*	if ($F('purpose')==''){
						alert(" Please enter the purpose of requesting the certificate.");
						$('purpose').focus();
						return false;
				}

				return true;*/
	/*		if(document.cert_med.with_medical.checked==true){
				if(document.cert_med.medical_findings.value=='' || document.cert_med.dr_nr_med.value==0)
					alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
					$('with_medical').focus();
					return false;
			}
*/

			if(d.left_eye_snellen.value=='' || d.right_eye_snellen.value=='' || d.left_eye_acuity.value=='' || d.right_eye_acuity.value=='' || d.left_ear.checked==true || d.right_ear.checked==true || d.left_lower_extremities.checked==true || d.right_lower_extremities.checked==true || d.comment_drive.checked==true || d.left_upper_extremities.checked==true || d.right_upper_extremities.checked==true){
				/*alert('Please fill in all fields');*/

				$('educ_attain_nr').focus();
				return false;
			}

		}

		//Added by Cherry 08-05-10
		function viewResult(batch_nr,pid,dept_nr,rep){
			if(rep==1)
				window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
			else
				window.open('<?=$root_path?>modules/radiology/certificates/seg-radio-unified-report-pdf.php?batch_nr='+batch_nr+'&pid='+pid+'&dept_nr='+dept_nr,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');
			//window.open("cert_reinstatement_pdf.php?id="+id,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
			//window.open('<?=$root_path?>modules/laboratory/seg-lab-result-pdf.php?pid='+pid,null,'width=620,height=440,menubar=no,resizable=yes,scrollbars=yes');

		}

		//Added by Cherry 08-19-10
/*		commented by art 04/22/2014
		function showConditions(){
			if(document.cert_med.comment_drive[1].checked==true){
				document.getElementById('get_conditions').style.display = '';
			}else{
				document.getElementById('get_conditions').style.display = 'none';
				document.cert_med.conditions[0].checked=false;
				document.cert_med.conditions[1].checked=false;
				document.cert_med.conditions[2].checked=false;
				document.cert_med.conditions[3].checked=false;
				document.cert_med.conditions[4].checked=false;

			}
		}

		function showTextAbnormal(){
			if(document.cert_med.general_physique[1].checked==true){
				document.getElementById('abnormality').style.display = '';
			}else{
				document.getElementById('abnormality').style.display = 'none';
			}
		}

		function showTextDisease(){
			if(document.cert_med.with_disease[1].checked==true){
				document.getElementById('contagious_diseases').style.display = '';
			}else{
				document.getElementById('contagious_diseases').style.display = 'none';
			}
		}
*/
		function key_check(e, value){
			if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
			return true;
			}else
			return false;
		}

		function key_check2(e, value){
			if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || (e.keyCode>=96 && e.keyCode<=105)){
			return true;
			}else
			return false;
		}

		//end Cherry
/* commented by art 04/22/2014
		function showSurgicalDetails(){
			if(document.clinical_history.had_surgery.checked==true){
				document.getElementById('proc_date').style.display = '';
				document.getElementById('proc').style.display='';
			}else{
				document.getElementById('proc_date').style.display = 'none';
				document.getElementById('proc').style.display = 'none';
			}
		}

		function showLabDetails(){
				if(document.clinical_history.laboratory.checked==true){
				document.getElementById('show_lab_date').style.display = '';
				document.getElementById('show_lab_result').style.display = '';
				document.getElementById('biopsy').style.display = '';
				document.getElementById('ct_mri').style.display='';
				document.getElementById('ultrasound').style.display='';
				document.getElementById('xray').style.display='';
				document.getElementById('blood_chem').style.display='';
			}else{

				document.clinical_history.has_biopsy.checked =false;
				document.clinical_history.has_ct_mri.checked=false;
				document.clinical_history.has_ultrasound.checked=false;
				document.clinical_history.has_xray.checked=false;
				document.clinical_history.has_blood_chem.checked=false;

				document.getElementById('show_lab_date').style.display = 'none';
				document.getElementById('show_lab_result').style.display = 'none';
				document.getElementById('biopsy').style.display = 'none';
				document.getElementById('biopsy1').style.display = 'none';
				document.getElementById('biopsy2').style.display = 'none';
				document.getElementById('biopsy3').style.display = 'none';
				document.getElementById('ct_mri').style.display = 'none';
				document.getElementById('ct_mri1').style.display = 'none';
				document.getElementById('ct_mri2').style.display = 'none';
				document.getElementById('ct_mri3').style.display = 'none';
				document.getElementById('ultrasound').style.display = 'none';
				document.getElementById('ultrasound1').style.display = 'none';
				document.getElementById('ultrasound2').style.display = 'none';
				document.getElementById('ultrasound3').style.display = 'none';
				document.getElementById('xray').style.display = 'none';
				document.getElementById('xray1').style.display = 'none';
				document.getElementById('xray2').style.display = 'none';
				document.getElementById('xray3').style.display = 'none';
				document.getElementById('blood_chem').style.display = 'none';
				document.getElementById('blood_chem1').style.display = 'none';
				document.getElementById('blood_chem2').style.display = 'none';
				document.getElementById('blood_chem3').style.display = 'none';

			}
		}


		//End Cherry

		function closing(){
			 alert('hello');
			//close = <td align="RIGHT"><a href="javascript:return '+fnRef+'cClick();" '+closeevent+'="return '+fnRef+'cClick();" style="color: '+o3_closecolor+'; font-family: '+o3_closefont+'; font-size: '+o3_closesize+o3_closesizeunit+'; text-decoration: '+o3_closedecoration+'; font-weight: '+o3_closeweight+'; font-style:'+o3_closestyle+';">'+close+'</a></td>';
			cClick();
		}

		function checkType(thisType){

				type = thisType;
		}
*/
		function printMedCert(encounter_nr,refno){


						// window.open("seg-ic-cert-med-driver-pdf.php?encounter_nr="+encounter_nr,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
						window.open("reports/medcert_drivers.php?encounter_nr="+encounter_nr+"&refno="+refno,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}

/* commented by art 04/22/2014
		function digitsOnly(obj){
obj.value=obj.value.replace(/[^\d]/g,'');
//obj.value=obj.value.replace(/^\d+$/,'');
}
*/
		//End Cherry

		/*function preset(){
			//alert('HOY!');
			var d = document.cert_med;

			document.getElementById('abnormality').style.display='none';
			document.getElementById('contagious_diseases').style.display='none';
			//document.getElementById('conditions').style.display='none';

			if(d.comment_drive[1].checked){
				document.getElementById('get_conditions').style.display='';
			}else{
				document.getElementById('get_conditions').style.display='none';
			}

			if(d.with_disease[1].checked){
				document.getElementById('contagious_diseases').style.display='';
			}else{
				document.getElementById('contagious_diseases').style.display='none';
			}

			if(d.general_physique[1].checked){
				document.getElementById('abnormality').style.display='';
			}else{
				document.getElementById('abnormality').style.display='none';
			}

			
			if(d.with_medical.checked==true){
				document.getElementById('medical').style.display='';
				document.getElementById('medical_findings').style.display = '';
				document.getElementById('dr_nr_med').style.display = '';
				document.getElementById('physician').style.display = '';
				document.getElementById('show_medical').style.display = '';
			}else{
				document.getElementById('medical').style.display='none';
				document.getElementById('medical_findings').style.display = 'none';
				document.getElementById('dr_nr_med').style.display = 'none';
				document.getElementById('physician').style.display = 'none';
				document.getElementById('show_medical').style.display = 'none';
			}

			if(d.with_dental.checked==true){
				document.getElementById('dental').style.display='';
				document.getElementById('dental_findings').style.display='';
				document.getElementById('dentist').style.display='';
				document.getElementById('dr_nr_dental').style.display='';
				document.getElementById('show_dental').style.display='';
			}else{
				document.getElementById('dental').style.display='none';
				document.getElementById('dental_findings').style.display='none';
				document.getElementById('dentist').style.display='none';
				document.getElementById('dr_nr_dental').style.display='none';
				document.getElementById('show_dental').style.display='none';
			}


				/*var encounter_nr = window.parent.$('encounter_nr').value;

				//alert(encounter_nr);

				if(d.had_surgery.checked==true){
					 document.getElementById('proc_date').style.display = '';
					 document.getElementById('proc').style.display = '';
				}else{
					document.getElementById('proc_date').style.display = 'none';
					document.getElementById('proc').style.display = 'none';
				}

*/
	/*	}*/

function trimString(objct){

		objct.value = objct.value.replace(/\s+/g,"");
}/* end of function trimString */

var js_time = "";
function js_setTime(jstime){
		js_time = jstime;
}

function js_getTime(){
		return js_time;
}

function validateTime(S) {
		return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}

var seg_validDate=true;
//var seg_validTime=false;

function seg_setValidDate(bol){
		seg_validDate=bol;
//    alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
}

function closeWindow(){
	window.parent.pSearchClose();
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//    var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;
		var jtime = "";

		trimString(thisTime);

		if (thisTime.value==''){
				seg_validTime=false;
				return;
		}

		stime = stime.replace(':', '');

		if (stime.length == 3){
				hour = stime.substring(0,1);
				minute = stime.substring(1,3);
		} else if (stime.length == 4){
				hour = stime.substring(0,2);
				minute = stime.substring(2,4);
		}else{
				alert("Invalid time format.");
				thisTime.value = "";
				seg_validTime=false;
				thisTime.focus();
				return;
		}

		jtime = hour + ":" + minute;
		js_setTime(jtime);

		if (hour==0){
				 hour = 12;
				 document.getElementById(AMPM).value = "A.M.";
		}else    if((hour > 12)&&(hour < 24)){
				 hour -= 12;
				 document.getElementById(AMPM).value = "P.M.";
		}

		ftime =  hour + ":" + minute;

		if(!ftime.match(f1) && !ftime.match(f2)){
				thisTime.value = "";
				alert("Invalid time format.");
				seg_validTime=false;
				thisTime.focus();
		}else{
				thisTime.value = ftime;
				seg_validTime=true;
		}
}// end of function setFormatTime

		//-------------------
								//<body onLoad="preset();"
</script>
</head>

<body >
<table width="520" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
		<tr>
				<td colspan="*"><?= $errorMsg ?></td>
		</tr>
		<tr>
				<td class="segPanel" colspan="*" height="23" bgcolor="#FFFFFF" >
						<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
								<tr>
										<td width="18%" ></td>
										<td width="37%" >&nbsp;</td>
										<!--<td width="28%" align="right" ><? echo 'Case No. '?></td>
										<td width="17%" align="left"><? echo $encounter_nr; ?></td>  -->
										<td width="28%" align="right"></td>
										<td width="17%" align="left"></td>
								</tr>
								<tr>
										<td><span class="style3"><? echo "Name :"?> </span></td>
										<td colspan="2" class="style2">
												<?
														#edited by VAN 02-28-08
														$name = stripslashes(strtoupper($encInfo['name_first'])).' '.stripslashes(strtoupper($encInfo['name_middle'])).' '.stripslashes(strtoupper($encInfo['name_last']));
														echo $name;

												?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<!--<span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?>  -->

										</td>
										<td>&nbsp;</td>
								</tr>
								<!--<tr>
										<td><span class="style3"><? echo "Age :  "?></span><? echo $encInfo['age'].' old';?></td>
										<td><span class="style2"><? echo "Sex :  "?></span><? echo $encInfo['sex']?></td>
										<!--<td><span class="style3"><? echo "Address :"?></span></td>
										<td colspan="2" class="style2">
												<? echo stripslashes(strtoupper($encInfo['street_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['brgy_name']))."&nbsp;&nbsp; ".stripslashes(strtoupper($encInfo['mun_name'])). ", ".stripslashes(strtoupper($encInfo['prov_name'])). "&nbsp;&nbsp;".stripslashes(strtoupper($encInfo['zipcode']));?>
										</td>
										<td>&nbsp;</td>
								</tr> -->
							<tr>
										<td><span class="style3"><? echo "Age :"?> </span></td>
										<td colspan="2" class="style2">
												<?
														echo $encInfo['age'].' old';

												?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<span class="style3"><? echo "Sex :  "?></span><?
													if($encInfo['sex']=='f')
														echo "Female";
													else
														echo "Male";

													?>

										</td>
										<td>&nbsp;</td>
								</tr>

							 <tr>
										<td><span class="style3"><? echo "Hospital # :"?> </span></td>
										<td colspan="2" class="style2">
												<?
													echo $encInfo['pid'];
												?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;
												<span class="style3"><? echo "Case # :  "?></span><? echo $encInfo['encounter_nr'];?>

										</td>
										<td>&nbsp;</td>
								</tr>

								<!--added by Cherry 08-18-10-->
								<tr>
										<td><span class="style3"><? echo "Marital Status :"?> </span></td>
										<td colspan="2" class="style2">
												<?
														echo ucfirst($encInfo['civil_status']);

												?>  <? #echo stripslashes(strtoupper($encInfo['name_last']));?>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<span class="style3"><? echo "Nationality:  "?></span><?
													echo ucfirst($encInfo['citizenship']);

													?>

										</td>
										<td>&nbsp;</td>
								</tr>

								<tr>
									<td class="style3">Address:</td>
									<td class="style2" colspan="2">
										<?php

											if (trim($encInfo['street_name'])){
													if (trim($encInfo["brgy_name"]) ==NULL)
														$street_name = trim($encInfo['street_name'])." ";
													else
														$street_name = trim($encInfo['street_name']).", ";
											}else{
													$street_name = "";
											}

											if ((!(trim($encInfo["brgy_name"]))) || (trim($encInfo["brgy_name"])=="NOT PROVIDED"))
												$brgy_name = "";
											else
												$brgy_name  = trim($encInfo["brgy_name"]).", ";

											if ((!(trim($encInfo["mun_name"]))) || (trim($encInfo["mun_name"])=="NOT PROVIDED"))
												$mun_name = "";
											else{
												if ($brgy_name)
													$mun_name = trim($encInfo["mun_name"]);
												else
													$mun_name = trim($encInfo["mun_name"]);
											}

											if ((!(trim($encInfo["prov_name"]))) || (trim($encInfo["prov_name"])=="NOT PROVIDED"))
												$prov_name = "";
											else
												$prov_name = trim($encInfo["prov_name"]);

											if(stristr(trim($encInfo["mun_name"]), 'city') === FALSE){
												if ((!empty($encInfo["mun_name"]))&&(!empty($encInfo["prov_name"]))){
													if ($prov_name!="NOT PROVIDED")
														$prov_name = ", ".trim($prov_name);
													else
														$prov_name = trim($prov_name);
												}else{
													#$province = trim($prov_name);
													$prov_name = "";
												}
											}else
												$prov_name = "";

											$address = trim($street_name).trim($brgy_name).trim($mun_name).trim($prov_name);

											echo rtrim($address, ',');
											//print_r($education);
										?>
									</td>
								</tr>
								<!--end Cherry -->
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
		<form id="cert_med" name="cert_med" method="post" action="" onSubmit="return chkForm()">
		<?php

						if ($result_diagnosis = $objDRG->getDiagnosisCodes($encounter_nr,$encInfo['encounter_type'])){
								$rowsDiagnosis = $result_diagnosis->RecordCount();
								while($temp=$result_diagnosis->FetchRow()){

								}
						}

						if ($result_therapy = $objDRG->getProcedureCodes($encounter_nr,$encInfo['encounter_type'])){
								$rowsTherapy = $result_therapy->RecordCount();
								while($temp=$result_therapy->FetchRow()){


								}
						}

						$patientEncInfo = $enc_obj->getEncounterInfo($encounter_nr);
						$consulting_dr = $pers_obj->getPersonellInfo($patientEncInfo['consulting_dr_nr']);

						$consulting_dr_middleInitial = "";
						if (trim($consulting_dr['name_middle'])!=""){
								$thisMI=split(" ",$consulting_dr['name_middle']);
								foreach($thisMI as $value){
										if (!trim($value)=="")
												$consulting_dr_middleInitial .= $value[0];
								}
								if (trim($consulting_dr_middleInitial)!="")
										$consulting_dr_middleInitial = " ".$consulting_dr_middleInitial.".";
						}

						$attending_dr = $pers_obj->getPersonellInfo($patientEncInfo['current_att_dr_nr']);

						$attending_dr_middleInitial = "";
						if (trim($attending_dr['name_middle'])!=""){
								$thisMI=split(" ",$attending_dr['name_middle']);

								foreach($thisMI as $value){
										if (!trim($value)=="")
												$attending_dr_middleInitial .= $value[0];
								}
										if (trim($attending_dr_middleInitial)!=""){
												$attending_dr_middleInitial = " ".$attending_dr_middleInitial.".";
										}
						}

						$consulting_dr_name = "Dr. ".$consulting_dr['name_first']." ".$consulting_dr['name_2']." ".$consulting_dr_middleInitial." ".$consulting_dr['name_last'];
						$attending_dr_name = "Dr. ".$attending_dr['name_first']." ".$attending_dr['name_2']." ".$attending_dr_middleInitial." ".$attending_dr['name_last'];
		?>

		<tr id="space5">
				<td>&nbsp;</td>
		</tr>

		<tr>
			<td valign="top" align="center" bgcolor="#CCCCCC">
				<font color="#000099">
						Medical Certificate
				</font>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>
<!-- 
		<tr>
			<td>
				<table width="100%" border="1" bgcolor="#F8F9FA"class="style4">
					<tr>
						<td class="style2" width="35%">Educational Attainment:</td>
						<td align="left" width="65%">
							<select name="educ_attain_nr" id="educ_attain_nr">
							<?php
								if (empty($socInfo['educ_attain_nr']))
									$medcertDriverInfo['educ_attain_nr'] = 0;
												//print_r($education);
								foreach($listEducAttain as $key=>$value){
									if ($medcertDriverInfo['educ_attain_nr']==$key){
										echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
									}else{
										echo "				<option value='".$key."'>".$value."</option> \n";
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="style2" width="35%">Work Status:</td>
						<td align="left" width="65%">
							<select name="work_status_nr" id="work_status_nr">
								<?php
								if (empty($medcertDriverInfo['work_status_nr']))
									$medcertDriverInfo['work_status_nr'] = 0;
												//print_r($education);
								foreach($listWorkStatus as $key=>$value){
									if ($medcertDriverInfo['work_status_nr']==$key){
										echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
									}else{
										echo "				<option value='".$key."'>".$value."</option> \n";
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="style2" width="35%">Kind of Employment:</td>
						<td align="left" width="65%">
							<select name="employ_nr" id="employ_nr">
								<?php
										if (empty($medcertDriverInfo['employ_nr']))
										$medcertDriverInfo['employ_nr'] = 0;
													//print_r($education);
									foreach($listEmployment as $key=>$value){
										if ($medcertDriverInfo['employ_nr']==$key){
											echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
										}else{
											echo "				<option value='".$key."'>".$value."</option> \n";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="style2" width="35%">Motor Vehicle Driven:</td>
						<td align="left" width="65%">
							<select name="vehicle_nr" id="vehicle_nr">
								<?php
										if (empty($medcertDriverInfo['vehicle_nr']))
										$medcertDriverInfo['vehicle_nr'] = 0;
													//print_r($education);
									foreach($listVehicle as $key=>$value){
										if ($medcertDriverInfo['vehicle_nr']==$key){
											echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
										}else{
											echo "				<option value='".$key."'>".$value."</option> \n";
										}
									}
								?>
							</select>
						</td>
					</tr>
				</table>
			</td>
		</tr>
 -->
		<tr>
			<td>
				&nbsp;
			</td>
		</tr>

		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
					<tr>
						<td class="style2" align="center" >Physical Examination</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
					<tr>
						<td class="style2" align="left" width="50%" >Height:
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="height" id="height" size="12" onkeydown="return key_check(event, this.value)" value="<?=$medcertDriverInfo['height']?>"> CM
							<!-- <select name="height_unit" id="height_unit">
								<?php
										if (empty($medcertDriverInfo['height_unit']))
										$medcertDriverInfo['height_unit'] = 0;
													//print_r($education);
									foreach($listHeightUnit as $key=>$value){
										if ($medcertDriverInfo['height_unit']==$key){
											echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
										}else{
											echo "				<option value='".$key."'>".$value."</option> \n";
										}
									}
								?>
							</select> -->
						</td>

						<td class="style2" align="left" width="50%">Weight:
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="text" name="weight" id="weight" size="12" onkeydown="return key_check(event, this.value)" value="<?=$medcertDriverInfo['weight']?>">KG
						<!-- <select name="weight_unit" id="weight_unit">
								<?php
										if (empty($medcertDriverInfo['weight_unit']))
										$medcertDriverInfo['weight_unit'] = 0;
													//print_r($education);
									foreach($listWeightUnit as $key=>$value){
										if ($medcertDriverInfo['weight_unit']==$key){
											echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
										}else{
											echo "				<option value='".$key."'>".$value."</option> \n";
										}
									}
								?>
							</select> -->
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
					<tr>
						<td class="style2" align="left" width="30%">Blood Pressure:</td>
						<td>
							<input type="text" name="systole" id="systole" size="10" maxlength="3" onkeydown="return key_check2(event, this.value)" value="<?=$medcertDriverInfo['systole']?>">
							&nbsp;/&nbsp;
							<input type="text" name="diastole" id="diastole" size="10" maxlength="3" onkeydown="return key_check2(event, this.value)" value="<?=$medcertDriverInfo['diastole']?>">
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2" align="left" width="30%">General Physique:</td>
						<td class="style2">
							<?php
								if($medcertDriverInfo['general_physique']=="Normal" || !$medcertDriverInfo){
									$general_physique1 = "checked";
									$general_physique2 = "";
								}else if($medcertDriverInfo['general_physique']=="Abnormal"){
									$general_physique1 = "";
									$general_physique2 = "checked";
								}
							?>
							<input type="radio" name="general_physique" id="general_physique" value="Normal" <? echo $general_physique1;?>>Normal<br>
							<input type="radio" name="general_physique" id="general_physique" value="Abnormal" <? echo $general_physique2; ?>>With Disability
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="abnormality" id="abnormality" size="30" value="<?=$medcertDriverInfo['abnormality']?>">
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2" align="left" width="30%">Contagious Diseases:</td>
						<td class="style2">
							<?php
								if($medcertDriverInfo['with_disease']=='0'){
									$with_disease = "checked";
									$with_disease1 = "";
								}else if($medcertDriverInfo['with_disease']=='1'){
									$with_disease = "";
									$with_disease1 = "checked";
								}
							?>
							<input type="radio" name="with_disease" id="with_disease" value="0" <? echo $with_disease;?>>None<br>
							<input type="radio" name="with_disease" id="with_disease" value="1" <? echo $with_disease1; ?>>With disease
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" name="contagious_diseases" id="contagious_diseases" size="30" value="<?=$medcertDriverInfo['contagious_diseases']?>">
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
					<tr>
						<td class="style2">Visual Acuity:</td>
					</tr>

					<tr>
						<td class="style2" align="left" width="50%">Left Eye: Snellen
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="text" name="left_eye_snellen" id="left_eye_snellen" size="10" value="<?=$medcertDriverInfo['left_eye_snellen']?>">
						</td>
						<td class="style2" align="left" width="50%">Right Eye: Snellen
						&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="text" name="right_eye_snellen" id="right_eye_snellen" size="10" value="<?=$medcertDriverInfo['right_eye_snellen']?>">
						</td>
					</tr>

					<!-- <tr>
						<td class="style2" align="left" width="50%">Visual Acuity Chart:
						&nbsp;
						<input type="text" name="left_eye_acuity" id="left_eye_acuity" size="10" value="<?=$medcertDriverInfo['left_eye_acuity']?>">
						</td>
						<td class="style2" align="left" width="50%">Visual Acuity Chart:
						&nbsp;
						<input type="text" name="right_eye_acuity" id="right_eye_acuity" size="10" value="<?=$medcertDriverInfo['right_eye_acuity']?>">
						</td>
					</tr> -->

					<tr>
					<?php
						if($medcertDriverInfo['left_eye_other']=="1" || !$medcertDriverInfo){
							$left_eye_other1 = "checked";
							$left_eye_other2 = "";
						}else if($medcertDriverInfo['left_eye_other']=="2"){
							$left_eye_other1 = "";
							$left_eye_other2 = "checked";
						}

						if($medcertDriverInfo['right_eye_other']=="1" || !$medcertDriverInfo){
							$right_eye_other1 = "checked";
							$right_eye_other2 = "";
						}else if($medcertDriverInfo['right_eye_other']=="2"){
							$right_eye_other1 = "";
							$right_eye_other2 = "checked";
						}
					?>
						<td class="style2" align="left" width="50%">
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_eye_other" id="left_eye_other" value="1" <? echo $left_eye_other1; ?>>With corrective lens or contact
						</td>
						<td class="style2" align="left" width="50%">
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_eye_other" id="right_eye_other" value="1" <? echo $right_eye_other1; ?>>With corrective lens or contact
						</td>
					</tr>

					<tr>
						<td class="style2" align="left" width="50%">
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_eye_other" id="left_eye_other" value="2" <? echo $left_eye_other2; ?>>Color Blind
						</td>
						<td class="style2" align="left" width="50%">
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_eye_other" id="right_eye_other" value="2" <? echo $right_eye_other2; ?>>Color Blind
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2" align="left" width="50%">Hearing:</td>
					</tr>

					<tr>
						<td class="style2">
							<?php
								//if($medcertDriverInfo['left_ear']=="1" || !$medcertDriverInfo){
								if($medcertDriverInfo['left_ear']=="1"){
									$left_ear1 = "checked";
									$left_ear2 = "";
									$left_ear3 = "";
									$left_ear4 = "";
								}else if($medcertDriverInfo['left_ear']=="2"){
									$left_ear1 = "";
									$left_ear2 = "checked";
									$left_ear3 = "";
									$left_ear4 = "";
								}else if($medcertDriverInfo['left_ear']=="3"){
									$left_ear1 = "";
									$left_ear2 = "";
									$left_ear3 = "checked";
									$left_ear4 = "";
								}else if($medcertDriverInfo['left_ear']=="4"){
									$left_ear1 = "";
									$left_ear2 = "";
									$left_ear3 = "";
									$left_ear4 = "checked";
								}
							?>
							Left Ear:<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_ear" id="left_ear" value="1" <? echo $left_ear1; ?>>Normal<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_ear" id="left_ear" value="2" <? echo $left_ear2; ?>>Reduced<br>
							&nbsp;&nbsp;&nbsp;
							<!-- <input type="radio" name="left_ear" id="left_ear" value="3" <? echo $left_ear3; ?>>Zero<br>
							&nbsp;&nbsp;&nbsp; -->
							<input type="radio" name="left_ear" id="left_ear" value="4" <? echo $left_ear4; ?>>With Hearing Aid
						</td>
						<td class="style2">
							<?php
								//if($medcertDriverInfo['right_ear']=="1" || !$medcertDriverInfo){
								if($medcertDriverInfo['right_ear']=="1"){
									$right_ear1 = "checked";
									$right_ear2 = "";
									$right_ear3 = "";
									$right_ear4 = "";
								}else if($medcertDriverInfo['right_ear']=="2"){
									$right_ear1 = "";
									$right_ear2 = "checked";
									$right_ear3 = "";
									$right_ear4 = "";
								}else if($medcertDriverInfo['right_ear']=="3"){
									$right_ear1 = "";
									$right_ear2 = "";
									$right_ear3 = "checked";
									$right_ear4 = "";
								}else if($medcertDriverInfo['right_ear']=="4"){
									$right_ear1 = "";
									$right_ear2 = "";
									$right_ear3 = "";
									$right_ear4 = "checked";
								}
							?>
							Right Ear: <br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_ear" id="right_ear" value="1" <? echo $right_ear1; ?>>Normal<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_ear" id="right_ear" value="2" <? echo $right_ear2; ?>>Reduced<br>
							&nbsp;&nbsp;&nbsp;
							<!-- <input type="radio" name="right_ear" id="right_ear" value="3" <? echo $right_ear3; ?>>Zero<br>
							&nbsp;&nbsp;&nbsp; -->
							<input type="radio" name="right_ear" id="right_ear" value="4" <? echo $right_ear4; ?>>With Hearing Aid
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2">Upper Extremities:</td>
					</tr>

					<tr>
						<td class="style2">
							<?php
								//if($medcertDriverInfo['left_upper_extremities']=="1" || !$medcertDriverInfo){
								if($medcertDriverInfo['left_upper_extremities']=="1"){
									$left_upper_extremities1 = "checked";
									$left_upper_extremities2 = "";
									$left_upper_extremities3 = "";
								}else if($medcertDriverInfo['left_upper_extremities']=="2"){
									$left_upper_extremities1 = "";
									$left_upper_extremities2 = "checked";
									$left_upper_extremities3 = "";
								}else if($medcertDriverInfo['left_upper_extremities']=="3"){
									$left_upper_extremities1 = "";
									$left_upper_extremities2 = "";
									$left_upper_extremities3 = "checked";
								}

							?>
							Left:<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_upper_extremities" id="left_upper_extremities" value="1" <? echo $left_upper_extremities1; ?>>Normal<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_upper_extremities" id="left_upper_extremities" value="2" <? echo $left_upper_extremities2; ?>>With Disability<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_upper_extremities" id="left_upper_extremities" value="3" <? echo $left_upper_extremities3; ?>>With Special Equipment<br>
						</td>
						<td class="style2">
							<?php
								if($medcertDriverInfo['right_upper_extremities']=="1"){
									$right_upper_extremities1 = "checked";
									$right_upper_extremities2 = "";
									$right_upper_extremities3 = "";
								}else if($medcertDriverInfo['right_upper_extremities']=="2"){
									$right_upper_extremities1 = "";
									$right_upper_extremities2 = "checked";
									$right_upper_extremities3 = "";
								}else if($medcertDriverInfo['right_upper_extremities']=="3"){
									$right_upper_extremities1 = "";
									$right_upper_extremities2 = "";
									$right_upper_extremities3 = "checked";
								}
								/*else{
									$right_upper_extremities1 = "checked";
									$right_upper_extremities2 = "";
									$right_upper_extremities3 = "";
								}*/

							?>
							Right:<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_upper_extremities" id="right_upper_extremities" value="1" <? echo $right_upper_extremities1; ?>>Normal<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_upper_extremities" id="right_upper_extremities" value="2" <? echo $right_upper_extremities2; ?>>With Disability<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_upper_extremities" id="right_upper_extremities" value="3" <? echo $right_upper_extremities3; ?>>With Special Equipment<br>
						</td>
					</tr>

					<tr>
						<td class="style2">Lower Extremities:</td>
					</tr>

					<tr>
						<td class="style2">
							<?php
								if($medcertDriverInfo['left_lower_extremities']=="1"){
									$left_lower_extremities1 = "checked";
									$left_lower_extremities2 = "";
									$left_lower_extremities3 = "";
								}else if($medcertDriverInfo['left_lower_extremities']=="2"){
									$left_lower_extremities1 = "";
									$left_lower_extremities2 = "checked";
									$left_lower_extremities3 = "";
								}else if($medcertDriverInfo['left_lower_extremities']=="3"){
									$left_lower_extremities1 = "";
									$left_lower_extremities2 = "";
									$left_lower_extremities3 = "checked";
								}
								/*else{
									$left_lower_extremities1 = "checked";
									$left_lower_extremities2 = "";
									$left_lower_extremities3 = "";
								}*/
							?>

							Left:<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_lower_extremities" id="left_lower_extremities" value="1" <? echo $left_lower_extremities1; ?>>Normal<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_lower_extremities" id="left_lower_extremities" value="2" <? echo $left_lower_extremities2; ?>>With Disability<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="left_lower_extremities" id="left_lower_extremities" value="3" <? echo $left_lower_extremities3; ?>>With Special Equipment<br>
						</td>
						<td class="style2">
							<?php
								if($medcertDriverInfo['right_lower_extremities']=="1"){
									$right_lower_extremities1 = "checked";
									$right_lower_extremities2 = "";
									$right_lower_extremities3 = "";
								}else if($medcertDriverInfo['right_lower_extremities']=="2"){
									$right_lower_extremities1 = "";
									$right_lower_extremities2 = "checked";
									$right_lower_extremities3 = "";
								}else if($medcertDriverInfo['right_lower_extremities']=="3"){
									$right_lower_extremities1 = "";
									$right_lower_extremities2 = "";
									$right_lower_extremities3 = "checked";
								}
								/*else{
									$right_lower_extremities1 = "checked";
									$right_lower_extremities2 = "";
									$right_lower_extremities3 = "";
								}*/
							?>

							Right:<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_lower_extremities" id="right_lower_extremities" value="1" <?php echo $right_lower_extremities1; ?>>Normal<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_lower_extremities" id="right_lower_extremities" value="2" <? echo $right_lower_extremities2;?>>With Disability<br>
							&nbsp;&nbsp;&nbsp;
							<input type="radio" name="right_lower_extremities" id="right_lower_extremities" value="3" <? echo $right_lower_extremities3;?>>With Special Equipment<br>
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
						<tr>
						<td class="style2">COMMENTS:<br>
						<?php
							if ($medcertDriverInfo['comment_drive']=="1"){
								$checked1 = "checked";
								$checked2 = "";
								$checked3 = "";
							}else if($medcertDriverInfo['comment_drive']=="2"){
								$checked1 = "";
								$checked2 = "checked";
								$checked3 = "";
							}else if($medcertDriverInfo['comment_drive']=="3"){
								$checked1 = "";
								$checked2 = "";
								$checked3 = "checked";
							}
							/*else{
								$checked1 = "checked";
								$checked2 = "";
								$checked3 = "";
							}*/
						?>
						&nbsp;&nbsp;&nbsp;
						<input type="radio" name="comment_drive" id="comment_drive" value="1"  <?php echo $checked1; ?>>Fit to Drive<br>
						<!-- &nbsp;&nbsp;&nbsp;
						<input type="hidden" name="comment_drive" id="comment_drive" value="2" onclick="showConditions();" <?php echo $checked2; ?>>Without Conditions<br> -->
						&nbsp;&nbsp;&nbsp;
						<input type="radio" name="comment_drive" id="comment_drive" value="3"  <?php echo $checked3; ?>>With Conditions<br>
						</td>
					</tr>

					<tr>
						<td class="style2" id="get_conditions">
						<?php
							if($medcertDriverInfo['conditions']=="1"){
								$checka = "checked";
								$checkb = "";
								$checkc = "";
								$checkd = "";
								$checke = "";
							}else if($medcertDriverInfo['conditions']=="2"){
								$checka = "";
								$checkb = "checked";
								$checkc = "";
								$checkd = "";
								$checke = "";
							}else if($medcertDriverInfo['conditions']=="3"){
								$checka = "";
								$checkb = "";
								$checkc = "checked";
								$checkd = "";
								$checke = "";
							}else if($medcertDriverInfo['conditions']=="4"){
								$checka = "";
								$checkb = "";
								$checkc = "";
								$checkd = "checked";
								$checke = "";
							}else if($medcertDriverInfo['conditions']=="5"){
								$checka = "";
								$checkb = "";
								$checkc = "";
								$checkd = "";
								$checke = "checked";
							}

						?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="conditions" id="conditions" value="1" <? echo $checka; ?>>A. Wear Corrective Lenses<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="conditions" id="conditions" value="2" <? echo $checkb; ?>>B. Drive Only With Special Equipment For Upper Limbs<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="conditions" id="conditions" value="3" <? echo $checkc; ?>>C. Drive Only With Special Equipment For Lower Limbs<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="conditions" id="conditions" value="4" <? echo $checkd; ?>>D. Daylight Driving Only<br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="conditions" id="conditions" value="5" <? echo $checke ?>>E. Must be Accompanied by a Person With Normal Hearing<br>
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Remarks:</td>
					</tr>

					<tr>
						<td align="center">
						<textarea cols=43 rows="3" id="remarks" name="remarks" ><?php echo $medcertDriverInfo['remarks']; ?></textarea>
						</td>
					</tr>

					<tr>
						<td class="style2">Examining Physician:
						&nbsp;&nbsp;&nbsp;&nbsp;
						<select name="exam_physician" id="exam_physician">
									<option value='0'>-Select a doctor-</option>
									<?php

										#edited by VAN 04-28-08
										$listDoctors = array_unique($listDoctors);

										if (empty($medcertDriverInfo['exam_physician']))
											$medcertDriverInfo['exam_physician'] = 0;

										foreach($listDoctors as $key=>$value){

											#echo "key = ".$key;
											#echo "<br>val = ".$value;
											if ($medcertDriverInfo['exam_physician']==$key){
												echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
											}else{
												echo "				<option value='".$key."'>".$value."</option> \n";
											}
										}
									?>
												</select>
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2">Control Number: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;
						<input type="text" size="38" id="control_number" name="control_number" value="<?=$medcertDriverInfo['control_number'];?>">
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
						<td class="style2">Encoded by: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<input type="text" size="38" id="doc_name" name="doc_name" value="<?=$HTTP_SESSION_VARS["sess_user_name"]; ?>" readonly="readonly">
						</td>
					</tr>

				</table>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
						if (!$medcertDriverInfo || empty($medcertDriverInfo) || $medcertDriverInfo=='' || $medcertDriverInfo=='1'){
								echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
								echo '            <input type="submit" name="Submit" value="Save">'."\n";
						}else{
								echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
								echo '            <input type="button" name="Print" value="Print" onClick="printMedCert('.$encounter_nr.','.$refno.')">'."\n &nbsp; &nbsp;";
								echo '            <input type="submit" name="Submit" value="Update">'."\n";
						}
						echo '            <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
						echo '						<input type="hidden" name="refno" id="refno" value="'.$refno.'">'."\n";
?>
						&nbsp; &nbsp;
						<!--<input type="button" name="Cancel" value="Cancel"  onclick="javascript.window.cClick();" -->
						<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
						<input type="hidden" name="refno" id="refno" value="<?=$refno?>">
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		</form>
</table>

</body>
</html>
