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
 #echo "encounter_nr= ".$encounter_nr."<br>";
#Added by Cherry 08-09-10
if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']){
	$refno = $_POST['refno'];
}
 #echo "refno= ".$refno."<br>";
if (isset($_GET['pid']) && $_GET['pid']){
		$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
		$pid = $_POST['pid'];
}
#echo "pid= ".$pid."<br>";
#echo "had_surgery= ".$_POST['had_surgery']."<br>";
//include_once($root_path.'include/care_api_classes/class_cert_med.php');
//$obj_medCert = new MedCertificate($encounter_nr);


$errorMsg='';

//Added by Cherry 08-12-10
global $HTTP_SESSION_VARS;

global $db;

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


#----------------------------------------------
//$HTTP_POST_VARS['dr_nr'] = $_POST['doctors'];
 /*
if (isset($_POST['mode'])){

	if(!$_POST['had_surgery'])
		$_POST['had_surgery']='0';
	if(!$_POST['has_blood_chem'])
		$_POST['has_blood_chem']='0';
	if(!$_POST['has_xray'])
		$_POST['has_xray']='0';
	if(!$_POST['has_ultrasound'])
		$_POST['has_ultrasound']='0';
	if(!$_POST['has_ct_mri'])
		$_POST['has_ct_mri']='0';
	if(!$_POST['has_biopsy'])
		$_POST['has_biopsy']='0';

	 $data = array('encounter_nr'=>$encounter_nr,
													'subj_comp'=>$_POST['subj_comp'],
													'obj_comp'=>$_POST['obj_comp'],
													'assessment'=>$_POST['assessment'],
													'has_conscious'=>$_POST['has_conscious'],
													'did_vomit'=>$_POST['did_vomit'],
													'gcs'=>$_POST['gcs'],
													'rls'=>$_POST['rls'],
													'had_surgery'=>$_POST['had_surgery'],
													'surgery_date'=>date('Y-m-d H:i:s', strtotime($_POST['surgery_date'])),
													'surgery_proc'=>$_POST['surgery_proc'],
													'has_blood_chem'=>$_POST['has_blood_chem'],
													'has_xray'=>$_POST['has_xray'],
													'has_ultrasound'=>$_POST['has_ultrasound'],
													'has_ct_mri'=>$_POST['has_ct_mri'],
													'has_biopsy'=>$_POST['has_biopsy']

						);
	switch($_POST['mode']) {
				case 'save':
						$save = $radio_obj->saveCTHistory($data);
						if($save){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
				break;
				case 'update':
						$check = $radio_obj->deleteCTHistory($encounter_nr);
						if($check){
							$update = $radio_obj->saveCTHistory($data);
						}

						if ($update){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
				break;
		}# end of switch statement
}  */

//-----------Added by Cherry 08-12-10------------
if (isset($_POST['mode'])){

	//$trans = $obj_medCert->getInfoTrans($encounter_nr);
	$trans_date = $trans['trxn_date'];

	/*echo "<pre>";
	print_r($_POST);
	echo "</pre>";
	die("end"); */



	switch($_POST['mode']) {
				case 'save':

				$nr = $obj_medCert->getExamNr();

				$nr++;

				$exam_list = array();
				$num_list = $_POST['num_list'];
				for($n = 0; $n < sizeof($exam_type); $n++){
					$temp = $n + 1;
					$exam_type_list = 'exam_type_list'.$temp;
					//print_r($$exam_type_list);
					$tfile = $$exam_type_list;
					$exam_list[$n] = $tfile[0];
				}

				$exam_details = array();

				//print_r($dr_nr);

				$k = 0;
				for($j = 0; $j < sizeof($exam_type); $j++){

					$exam_details[$j][$k]	= "'".$nr."'";

					if($exam_type[$j]!='')
						$exam_details[$j][$k+1]	= "'".$exam_type[$j]."'";
					else
						$exam_details[$j][$k+1]	= "''";

					if($exam_list[$j]!='')
						$exam_details[$j][$k+2] = "'".$exam_list[$j]."'";
					else
						$exam_details[$j][$k+2] = "''";

					if($remarks[$j]!='')
						$exam_details[$j][$k+3] = "'".$remarks[$j]."'";
					else
						$exam_details[$j][$k+3] = "''";

					if($j <= 4){
						if($dr_nr=='' || $dr_nr==0)
							$exam_details[$j][$k+4] = "'0'";
						else
							$exam_details[$j][$k+4] = "'".$dr_nr[$j]."'";
					/*}else if($j==5){
						$exam_details[$j][$k+4] = "'0'";  */
					}
				}

				#echo implode(",", $exam_details[0]);

				for($a = 0; $a <sizeof($exam_type); $a++){
					if($a <= 4)
						$temp_string .= "(".implode(",", $exam_details[$a])."),";
					else if($a >4 && $a < 8)
						$temp_string .= "(".implode(",", $exam_details[$a]).",'0'"."),";
					else if($a==8)
						$temp_string .= "(".implode(",", $exam_details[$a]).",'0'".")";
				}

				#echo "pid= ".$pid."<br>";
				#echo "encounter_nr= ".$encounter_nr."<br>";
				#echo "refno= ".$refno."<br>";

				//array
				$data = array('exam_nr'=>$nr,
											'pid'=>$pid,
											'encounter_nr'=>$encounter_nr,
											'refno'=>$refno,
											'diagnosis'=>$_POST['diagnosis'],
											'recommendation'=>$_POST['recommendation'],
											'physician_nr'=>$_POST['physician_nr'],
											'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
											'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
											'create_dt'=>date('Y-m-d H:i:s'),
											'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
											'modify_dt'=>date('Y-m-d H:i:s'),
											'remarks_other' =>$_POST['remarks_other']
				);

				$data_vital = array('exam_nr'=>$nr,
														'systole'=>$_POST['systole'],
														'diastole'=>$_POST['diastole'],
														'cardiac_rate'=>$_POST['cr_rate'],
														'resp_rate'=>$_POST['rr_rate'],
														'temperature'=>$_POST['temperature'],
														'weight'=>$_POST['weight'],
														'height'=>$_POST['height'],
														'bmi'=>$_POST['bmi']
				);

				$save = $obj_medCert->saveMedChartFromArray($data);
				$save_vital = $obj_medCert->saveVitalsignsFromArray($data_vital);
				$save_details = $obj_medCert->saveMedChartDetails($temp_string);
				if($save && $save_vital && $save_details){
					$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
				}else{
					$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
				}

				/*$save_details = $obj_medCert->saveMedChartDetails($temp_string);
				if($save_details){
					echo "you can sleep";
				}    */
				#echo "<br> save= ".$save_details;
				/*echo "<pre>";
				print_r($exam_details);
				echo "</pre>";
				die("end");    */




					/*	if($obj_medCert->saveCertMedInfoFromArray($data)){
						//if($save2){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}    */
				break;
				case 'update':

						$nr = $obj_medCert->getExamNr();

						$exam_list = array();
				$num_list = $_POST['num_list'];
				for($n = 0; $n < sizeof($exam_type); $n++){
					$temp = $n + 1;
					$exam_type_list = 'exam_type_list'.$temp;
					//print_r($$exam_type_list);
					$tfile = $$exam_type_list;
					$exam_list[$n] = $tfile[0];
				}

				$exam_details = array();

				//print_r($dr_nr);

				$k = 0;
				for($j = 0; $j < sizeof($exam_type); $j++){

					$exam_details[$j][$k]	= "'".$nr."'";

					if($exam_type[$j]!='')
						$exam_details[$j][$k+1]	= "'".$exam_type[$j]."'";
					else
						$exam_details[$j][$k+1]	= "''";

					if($exam_list[$j]!='')
						$exam_details[$j][$k+2] = "'".$exam_list[$j]."'";
					else
						$exam_details[$j][$k+2] = "''";

					if($remarks[$j]!='')
						$exam_details[$j][$k+3] = "'".$remarks[$j]."'";
					else
						$exam_details[$j][$k+3] = "''";

					if($j <= 4){
						if($dr_nr=='' || $dr_nr==0)
							$exam_details[$j][$k+4] = "'0'";
						else
							$exam_details[$j][$k+4] = "'".$dr_nr[$j]."'";
					/*}else if($j==5){
						$exam_details[$j][$k+4] = "'0'";  */
					}
				}

				#echo implode(",", $exam_details[0]);

				for($a = 0; $a <sizeof($exam_type); $a++){
					if($a <= 4)
						$temp_string .= "(".implode(",", $exam_details[$a])."),";
					else if($a >4 && $a < 8)
						$temp_string .= "(".implode(",", $exam_details[$a]).",'0'"."),";
					else if($a==8)
						$temp_string .= "(".implode(",", $exam_details[$a]).",'0'".")";
				}

						#echo "nr= ".$nr."<br>";
						#echo "encounter_nr= ".$encounter_nr."<br>";
						#echo "pid= ".$pid."<br>";
						#echo "refno = ".$refno."<br>";

				$data = array('exam_nr'=>$nr,
											'pid'=>$pid,
											'encounter_nr'=>$encounter_nr,
											'refno'=>$refno,
											'diagnosis'=>$_POST['diagnosis'],
											'recommendation'=>$_POST['recommendation'],
											'physician_nr'=>$_POST['physician_nr'],
											'history'=>'',
											'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
											'modify_dt'=>date('Y-m-d H:i:s'),
											'remarks_other' =>$_POST['remarks_other']
				);

				$data_vital = array('exam_nr'=>$nr,
														'systole'=>$_POST['systole'],
														'diastole'=>$_POST['diastole'],
														'cardiac_rate'=>$_POST['cr_rate'],
														'resp_rate'=>$_POST['rr_rate'],
														'temperature'=>$_POST['temperature'],
														'weight'=>$_POST['weight'],
														'height'=>$_POST['height'],
														'bmi'=>$_POST['bmi']
				);

				$updated = $obj_medCert->updateMedChartFromArray($data);
				$updated_vital = $obj_medCert->updateVitalsignsFromArray($data_vital);
				$updated_details = $obj_medCert->updateMedChartDetails($nr, $temp_string);
				if($updated && $updated_vital && $updated_details){
					$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
				}else{
					$errorMsg='<font style="color:#FF0000">'."Failed to update data".'</font>';
				}

						/*if ($obj_medCert->updateCertMedInfoFromArray($data)){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}      */
				break;
		}# end of switch statement
}

//-------------------End Cherry------------------


if($encounter_nr){
#    if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
				echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
				exit();
		}
		#extract($encInfo);
}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
		exit();
}

#echo "refno= ".$refno."<br>";
$medchartInfo = $obj_medCert->getMedChartInfo($refno);
$det = $obj_medCert->getMedChartDetails($refno);
$chart_det = array();
$chart_remarks = array();
$chart_dr = array();
$count = 0;
$var = 1;

if($det){
		while($rowdet = $det->FetchRow()){
		/*if($rowdet['exam_type_list']=='1')
			$chart_det[$count][$var] = "checked";
			$chart_det[$count][$var+1] = "";
			$chart_det[$count][$var+2] = "";
									*/
		$chart_remarks[$count] = $rowdet['remarks'];
		$chart_dr[$count] = $rowdet['dr_nr'];
		$count++;
	}
}

//$trylang = array('checked', "", "");

#echo "encInfo['encounter_type'] = '".$encInfo['encounter_type']."' <br> \n";
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
	background-color: #CCCCFF;
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
			/*	if ($F('purpose')==''){
						alert(" Please enter the purpose of requesting the certificate.");
						$('purpose').focus();
						return false;
				}

				return true;*/
			if(document.cert_med.with_medical.checked==true){
				if(document.cert_med.medical_findings.value=='' || document.cert_med.dr_nr_med.value==0)
					alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
					$('with_medical').focus();
					return false;
			}

			else if(document.cert_med.with_dental.checked==true){
				if(document.cert_med.dental_findings.value=='' || document.cert_med.dr_nr_dental.value==0)
					alert('Please input dental findings in the provided area and select the dentist in-charge of the examination');
				$('with_dental').focus();
				return false;
			}

			else if(document.cert_med.with_medical.checked==false && document.cert_med.with_dental.checked==false){
				if(document.cert_med.medical_findings.value=='' || document.cert_med.dr_nr_med.value==0 || document.cert_med.dental_findings.value=='' || document.cert_med.dr_nr_dental.value==0)
					alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
				$('with_medical').focus();
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

		//End Cherry

		function closing(){
			 alert('hello');
			//close = <td align="RIGHT"><a href="javascript:return '+fnRef+'cClick();" '+closeevent+'="return '+fnRef+'cClick();" style="color: '+o3_closecolor+'; font-family: '+o3_closefont+'; font-size: '+o3_closesize+o3_closesizeunit+'; text-decoration: '+o3_closedecoration+'; font-weight: '+o3_closeweight+'; font-style:'+o3_closestyle+';">'+close+'</a></td>';
			cClick();
		}

		function checkType(thisType){

				type = thisType;
		}

		function printMedChart(refno){


						window.open("seg-ic-exam-chart-pdf.php?refno="+refno,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}

		//Added by Cherry 08-09-10
		function printCTHistory(encounter_nr,pid){
			//alert('try lang');
				window.open("seg-radio-ct-history-pdf.php?encounter_nr="+encounter_nr+"&pid="+pid,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}
		//Added by Cherry August 16, 2010
		function showMedical(){
			if(document.cert_med.with_medical.checked==true){
				document.getElementById('medical').style.display='';
				document.getElementById('medical_findings').style.display = '';
				document.getElementById('dr_nr_med').style.display = '';
				document.getElementById('physician').style.display = '';
				document.getElementById('show_medical').style.display ='';
			}else{
				document.getElementById('medical').style.display='none';
				document.getElementById('medical_findings').style.display = 'none';
				document.getElementById('dr_nr_med').style.display = 'none';
				document.getElementById('physician').style.display = 'none';
				document.getElementById('show_medical').style.display = 'none';
			}

		}

		function showDental(){
			if(document.cert_med.with_dental.checked==true){
				document.getElementById('dental').style.display='';
				document.getElementById('dental_findings').style.display = '';
				document.getElementById('dr_nr_dental').style.display = '';
				document.getElementById('dentist').style.display = '';
				document.getElementById('show_dental').style.display ='';
			}else{
				document.getElementById('dental').style.display='none';
				document.getElementById('dental_findings').style.display='none';
				document.getElementById('dentist').style.display='none';
				document.getElementById('dr_nr_dental').style.display='none';
				document.getElementById('show_dental').style.display ='none';
			}
		}

		function digitsOnly(obj){
obj.value=obj.value.replace(/[^\d]/g,'');
//obj.value=obj.value.replace(/^\d+$/,'');
}


		//End Cherry

		function preset(){
			//alert('HOY!');
				var d = document.med_chart;
				//alert('scsdc');
				//var tmp1 = <?=$nr?>;
				//alert(tmp1);
				//d.exam_type_list11.checked==true;

			/*if(d.with_medical.checked==true){
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
															*/

				/*var encounter_nr = window.parent.$('encounter_nr').value;

				//alert(encounter_nr);

				if(d.had_surgery.checked==true){
					 document.getElementById('proc_date').style.display = '';
					 document.getElementById('proc').style.display = '';
				}else{
					document.getElementById('proc_date').style.display = 'none';
					document.getElementById('proc').style.display = 'none';
				}

				if(d.laboratory.checked==true){
					document.getElementById('show_lab_date').style.display = '';
					document.getElementById('show_lab_result').style.display = '';
					document.getElementById('biopsy').style.display = '';
					document.getElementById('ct_mri').style.display = '';
					document.getElementById('ultrasound').style.display = '';
					document.getElementById('xray').style.display = '';
					document.getElementById('blood_chem').style.display = '';

						if(d.has_biopsy.checked==true){
							document.getElementById('biopsy1').style.display = '';
							document.getElementById('biopsy2').style.display = '';
							document.getElementById('biopsy3').style.display = '';
						}else{
							document.getElementById('biopsy1').style.display = 'none';
							document.getElementById('biopsy2').style.display = 'none';
							document.getElementById('biopsy3').style.display = 'none';
						}

						if(d.has_ct_mri.checked==true){
							document.getElementById('ct_mri1').style.display = '';
							document.getElementById('ct_mri2').style.display = '';
							document.getElementById('ct_mri3').style.display = '';
						}else{
							document.getElementById('ct_mri1').style.display = 'none';
							document.getElementById('ct_mri2').style.display = 'none';
							document.getElementById('ct_mri3').style.display = 'none';
						}

						if(d.has_ultrasound.checked==true){
							document.getElementById('ultrasound1').style.display = '';
							document.getElementById('ultrasound2').style.display = '';
							document.getElementById('ultrasound3').style.display = '';
						}else{
							document.getElementById('ultrasound1').style.display = 'none';
							document.getElementById('ultrasound2').style.display = 'none';
							document.getElementById('ultrasound3').style.display = 'none';
						}

						if(d.has_xray.checked==true){
							document.getElementById('xray1').style.display = '';
							document.getElementById('xray2').style.display = '';
							document.getElementById('xray3').style.display = '';
						}else{
							document.getElementById('xray1').style.display = 'none';
							document.getElementById('xray2').style.display = 'none';
							document.getElementById('xray3').style.display = 'none';
						}

						if(d.has_blood_chem.checked==true){
							document.getElementById('blood_chem1').style.display = '';
							document.getElementById('blood_chem2').style.display = '';
							document.getElementById('blood_chem3').style.display = '';
						}else{
							document.getElementById('blood_chem1').style.display = 'none';
							document.getElementById('blood_chem2').style.display = 'none';
							document.getElementById('blood_chem3').style.display = 'none';
						}

				}else{
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
*/
		}

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

<body onLoad="preset();">
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
		<form id="med_chart" name="med_chart" method="post" action="" onSubmit="return chkForm()">

		<tr id="space5">
				<td>&nbsp;</td>
		</tr>

		<tr>
			<td valign="top" align="center" bgcolor="#CCCCFF">
				<font color="#000099">
						Medical Examination Chart
				</font>
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<?
				//global $db;
				$cnt_exam;
				$cnt_list;
				$cnt = 0;
				$physician = array('Dentist', 'Doctor');

				$exam = $obj_medCert->getTypeExam();
				$num_exam = $exam->RecordCount();
				$list = $obj_medCert->getChartList();
				$num_list = $list->RecordCount();

				if(is_object($list)){
					while($row = $list->FetchRow()){
						$rowList[$row['list_id']][$cnt] = $row['list_name'];
						$rowList[$row['list_id']][$cnt+1] = $row['list_datatype'];
					}
				}

				if(is_object($exam)){
					while($row2 = $exam->FetchRow()){
						$rowExam[$row2['id']][$cnt] = $row2['name'];
						$rowExam[$row2['id']][$cnt+1] = $row2['with_dr_sig'];
					}
				}

				//print_r($rowExam);
				for($cnt_exam = 1; $cnt_exam <= $num_exam; $cnt_exam++){
					$string = '<tr>
										<td>
										<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
												<tr>
													<td width="30%" align="left"><strong>'.$rowExam[$cnt_exam][$cnt].'</strong>
													<input type="hidden" name="exam_type[]" id="exam_type'.$cnt_exam.'" value="'.$cnt_exam.'">
													</td>
													<td width="*" align="left">';
					for($cnt_list = 1; $cnt_list <= $num_list; $cnt_list++){
									if($cnt_list < $num_list && counter!=1){
										//$char = echo $trylang[$cnt_list];
										$string .= '<input type="'.$rowList[$cnt_list][$cnt+1].'" name="exam_type_list'.$cnt_exam.'[]" id="exam_type_list'.$cnt_exam.$cnt_list.'" value="'.$cnt_list.'"><strong>'.$rowList[$cnt_list][$cnt].'</strong> <br>';
										//$string .= '<input type="radio" name="exam_type_list[]" id="exam_type_list'.$cnt_list.'" value="'.$cnt_list.'"><strong>'.$rowList[$cnt_list][$cnt].'</strong> <br>';
									 #echo "exam_type_list".$cnt_exam.$cnt_list."<br>";
									}
									if($cnt_list == $num_list){
										$string .='	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Remarks</strong>
																<textarea id="remarks'.$cnt_list.'" cols="40" rows="3" name="remarks[]"></textarea>
															</td>
														</tr>
											';
									}

					}

				 $listDoctors = array_unique($listDoctors);
													foreach($listDoctors as $key=>$value){

														if ($medcertInfo['dr_nr']==$key){
														//if($chart_dr[0]==$key){
															$options .=				"<option value='".$key."' selected=\"selected\">".$value."</option> \n";
														}else{
															$options .=				"<option value='".$key."'>".$value."</option> \n";
														}
													}

					if($rowExam[$cnt_exam][$cnt+1]=='1'){
						if($cnt_exam=='1'){
							$string .= '<tr>
													<td width="30%" align="left"><strong>'.$physician[0].'</strong></td>
													<td>
															<select name="dr_nr[]" id="dr_nr">
																<option value="'.$cnt.'">-Select a doctor-</option>
																			'.$options.'
															</select>
													</td>
													</tr>
												';
						}else{
							$string .= '<tr>
													<td width="30%" align="left"><strong>'.$physician[1].'</strong></td>
													<td>
															<select name="dr_nr[]" id="dr_nr">
																<option value="'.$cnt.'">-Select a doctor-</option>
																			'.$options.'
															</select>
													</td>
													</tr>
												';
						}

					}

						/*	$string .='			</table>
												</td>
											</tr>
											';  */
						$systole = $medchartInfo['systole'];
						$diastole = $medchartInfo['diastole'];
						$cr = $medchartInfo['cardiac_rate'];
						$rr = $medchartInfo['resp_rate'];
						$temp_rate = $medchartInfo['temperature'];
						$wt = $medchartInfo['weight'];
						$ht = $medchartInfo['height'];
						$body_mass = $medchartInfo['bmi'];
						if($cnt_exam == '4'){

							$string .= '<tr>
														<td colspan="2" align="center">
															<table width="80%" border="0" bgcolor="#F8F9FA" class="style4" valign="middle">
																<tr>
																	<td align="center" colspan="3"><strong>Vital Signs</strong></td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>BP:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="systole" id="systole" value="'.$systole.'" size="5">/
																		<input type="text" name="diastole" id="diastole" value="'.$diastole.'" size="5">mmHG
																	</td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>CR:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="cr_rate" id="cr_rate" value="'.$cr.'" size="10">
																	</td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>RR:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="rr_rate" id="rr_rate" value="'.$rr.'" size="10">br/m
																	</td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>T:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="temperature" id="temperature" value="'.$temp_rate.'">C
																	</td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>Wt:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="weight" id="weight" value="'.$wt.'">kg
																	</td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>Ht:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="height" id="height" value="'.$ht.'">ft
																	</td>
																</tr>
																<tr>
																	<td width="20%">&nbsp;</td>
																	<td align="left" width="10%"><strong>BMI:</strong></td>
																	<td align="left" width="50%">
																		<input type="text" name="bmi" id="bmi" value="'.$body_mass.'">
																	</td>
																</tr>
															</table>
														</td>
												 </tr>';
						}
							$string .='			</table>
												</td>
											</tr>
											<tr>
												<td>&nbsp;</td>
											</tr>';



					echo $string;
				}


			?>
		</tr>
		<!--
		<tr>
			<td>
			<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
					<tr>
						<td width="30%" align="left"><strong>CBC</strong></td>
						<td width="*" align="left">
							<input type="radio" name="exam_type_list" id="exam_type_list" value="1">Examination not done <br>
							<input type="radio" name="exam_type_list" id="exam_type_list" value="2">No abnormality found <br>
							<input type="radio" name="exam_type_list" id="exam_type_list" value="3">Abnormality Noted <br>
							&nbsp;&nbsp;&nbsp;&nbsp;Remarks
							<textarea id="remarks" cols="40" rows="3" name="remarks"></textarea>
						</td>
					</tr>
					<tr>
					<td width="30%" align="left">Doctor</td>
					<td>
							<select name="dr_nr" id="dr_nr">
									<option value='0'>-Select a doctor-</option>
									<?php

										#edited by VAN 04-28-08
										$listDoctors = array_unique($listDoctors);

										if (empty($medCertInfo['dr_nr']))
											$medCertInfo['dr_nr'] = 0;

										foreach($listDoctors as $key=>$value){

											#echo "key = ".$key;
											#echo "<br>val = ".$value;
											if ($medcertInfo['dr_nr_med']==$key){
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
		<tr>
			<td>
					<table width="100%" border="0" bgcolor="#F8F9FA" class="style4">
						<tr>
						<td align="center" colspan="2"><strong>Vital Signs</strong></td>

					</tr>
					</table>
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>
			 -->
		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<?
			$other_exams = array();
			$tmp = 0;

				$query = "SELECT srv.name, 'lab' AS dept FROM seg_lab_servdetails AS sls
									INNER JOIN seg_lab_services AS srv ON srv.service_code = sls.service_code
									WHERE sls.refno = '$refno'
									AND sls.status NOT IN('deleted')
									AND sls.service_code NOT IN('CBC', 'URINE', 'FECAL')
									UNION ALL
									select rad.name, 'radio' AS dept FROM care_test_request_radio AS ct
									INNER JOIN seg_radio_services AS rad ON rad.service_code = ct.service_code
									WHERE ct.refno = '$refno'
									AND ct.status NOT IN('deleted')
									AND ct.service_code NOT IN('XRAY-C');
									";
				$result = $db->Execute($query);
				$num = $result->RecordCount();
				if(is_object($result)){
					while($row = $result->FetchRow()){

						if($row['dept']=='lab')
							$other_exams[$tmp] = $row['name']." (LB)";
						else
							$other_exams[$tmp] = $row['name']." (RD)";
						$tmp++;
					}
				}
				//print_r($other_exams);

			?>
			<td>
					<table width="100%" border="0" bgcolor="#F8F9FA" class="style4">
						<tr>
							<td align="center" colspan="2"><strong>Other Laboratory Exams</strong></td>
						</tr>
						<tr>
								<td align="center" colspan="2">
										<select size="10">
											<?
												for($i = 0; $i<$num; $i++){
													echo '<option>'.$other_exams[$i].'</option>';
												}
											?>
										</select>
								</td>
						</tr>
						<tr>
							<td align="left" width="30%"><strong>Remarks:</strong></td>
							<td>
								<textarea id="remarks_other" cols="40" rows="3" name="remarks_other"><?php echo $medchartInfo['remarks_other']; ?></textarea>
							</td>
						</tr>
					</table>
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
					<tr>
						<td width="30%"><strong>Diagnosis:</strong></td>
						<td>
							<textarea id="diagnosis" cols="43" rows="5" name="diagnosis"><?php echo $medchartInfo['diagnosis']; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td width="30%"><strong>Recommendations:</strong></td>
						<td>
							<textarea id="recommendation" cols="43" rows="5" name="recommendation"><?=$medchartInfo['recommendation']?></textarea>
						</td>
					</tr>

					<tr>
						<td>&nbsp;</td>
					</tr>

					<tr>
					<td width="30%" align="left"><strong>Physician In-charge:</strong></td>
					<td>
							<select name="physician_nr" id="physician_nr">
									<option value='0'>-Select a doctor-</option>
									<?php

										#edited by VAN 04-28-08
										$listDoctors = array_unique($listDoctors);

										if (empty($medchartInfo['physician_nr']))
											$medchartInfo['physician_nr'] = 0;

										foreach($listDoctors as $key=>$value){

											#echo "key = ".$key;
											#echo "<br>val = ".$value;
											if ($medchartInfo['physician_nr']==$key){
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
					<td align="left" width="30%"><strong>Encoded by:</strong></td>
					<td>
						<input type="text" size="30" id="doc_name" name="doc_name" value="<?=$HTTP_SESSION_VARS["sess_user_name"]; ?>" readonly="readonly">
					</td>
				 </tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td align="center" colspan="2" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
						if (!$medchartInfo || empty($medchartInfo)){
								echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
								echo '            <input type="submit" name="Submit" value="Save">'."\n";
						}else{
								echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
								echo '            <input type="button" name="Print" value="Print" onClick="printMedChart('.$refno.')">'."\n &nbsp; &nbsp;";
								echo '            <input type="submit" name="Submit" value="Update">'."\n";
						}
						echo '            <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
						echo '						<input type="hidden" name="refno" id="refno" value="'.$refno.'">'."\n";
?>
						&nbsp; &nbsp;
						<!--<input type="button" name="Cancel" value="Cancel"  onclick="closeWindow();">-->
						<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
						<input type="hidden" name="refno" id="refno" value="<?=$refno?>">
						<input type="hidden" name="num_list" id="num_list" value="<?=$num_list?>">
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
