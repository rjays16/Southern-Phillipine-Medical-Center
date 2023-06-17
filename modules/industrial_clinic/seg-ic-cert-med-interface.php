<?php
#edited by art 03/07/2014
#created by Cherry 09-14-09
include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
global $db;
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
define('MedCertTbl', 'seg_industrial_cert_med');

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
		$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
		$encounter_nr = $_POST['encounter_nr'];
}
# echo "encounter_nr= ".$encounter_nr."<br>";
#Added by Cherry 08-09-10
if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']){
	$refno = $_POST['refno'];
}
# echo "refno= ".$refno."<br>";
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
#echo "set = ".(isset($_POST['submit']));
if (isset($_POST['mode'])){
 //echo "<script language=javascript>alert('Pag sure dira!')</script>";
	//$trans = $obj_medCert->getInfoTrans($encounter_nr);
	$trans_date = $trans['trxn_date'];
#echo "<br>ode = ".$_POST['mode']."<br>";
	#commented by art 07/24/2014
	#$db->debug = true;
	/*switch($_POST['mode']) {
				case 'save':
				#echo "mode= ".$_POST['mode'];
	 //echo "<script language=javascript>alert('Pag sure dira!')</script>";
				//array
				$data = array('refno'=>$refno,
									'remarks'=>$_POST['remarks'],
									'dr_nr_med'=>$_POST['dr_nr_med'],
									'dr_nr_dental'=>$_POST['dr_nr_dental'],
									'medcert_date'=>date('Y-m-d H:i:s'),
									'transaction_date'=>date('Y-m-d H:i:s', strtotime($trans_date)),
									'with_medical'=>$_POST['with_medical'],
									'with_dental'=>$_POST['with_dental'],
									'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'create_dt'=>date('Y-m-d H:i:s'),
									'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'clinic_num'=>$encounter_nr,
									'medical_findings'=>$_POST['medical_findings'],
									'dental_findings'=>$_POST['dental_findings'],
									#added by art 03/06/2014
									'with_optha'=>$_POST['with_optha'],
									'with_ent'=>$_POST['with_ent'],
									'optha_findings'=>$_POST['optha_findings'],
									'ent_findings'=>$_POST['ent_findings'],
									'dr_nr_optha'=>$_POST['dr_nr_optha'],
									'dr_nr_ent'=>$_POST['dr_nr_ent'],
									#end art
						);
					
					$rs = $db->Replace('seg_industrial_cert_med',$data,array('refno'),$autoQuote = true);
						#if($obj_medCert->saveCertMedInfoFromArray($data)){
						if($rs){

							$cert_nr = $db->Insert_ID();
						//if($save2){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
							echo $obj_medCert->sql;
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
				break;
				case 'update':
				//$info = $obj_medCert->getAllInfoCertMed($refno);
				
				#$cert_nr = $medcertInfo['cert_nr'];
				$cert_nr = $obj_medCert->getCertNr($refno,'seg_industrial_cert_med');

				//array
				$data = array('refno'=>$refno,
									'cert_nr'=>$cert_nr['cert_nr'],
									'remarks'=>$_POST['remarks'],
									'dr_nr_med'=>$_POST['dr_nr_med'],
									'dr_nr_dental'=>$_POST['dr_nr_dental'],
									'medcert_date'=>date('Y-m-d H:i:s'),
									'transaction_date'=>date('Y-m-d H:i:s', strtotime($trans_date)),
									'with_medical'=>$_POST['with_medical'],
									'with_dental'=>$_POST['with_dental'],
									'history'=>"Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'clinic_num'=>$encounter_nr,
									'medical_findings'=>htmlentities($_POST['medical_findings']),
									'dental_findings'=>htmlentities($_POST['dental_findings']),
									#added by art 03/06/2014
									'with_optha'=>$_POST['with_optha'],
									'with_ent'=>$_POST['with_ent'],
									'optha_findings'=>htmlentities($_POST['optha_findings']),
									'ent_findings'=>htmlentities($_POST['ent_findings']),
									'dr_nr_optha'=>$_POST['dr_nr_optha'],
									'dr_nr_ent'=>$_POST['dr_nr_ent'],
									#end art

						);

					$rs = $db->Replace('seg_industrial_cert_med',$data,array('refno','cert_nr'),$autoQuote = true);

						#if ($obj_medCert->updateCertMedInfoFromArray($data)){
							if ($rs){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
				break;
		}# end of switch statement*/
		#added by art 07/24/2014
		switch($_POST['mode']) {
				case 'save':

						$data = array(
									'remarks'=>$_POST['remarks'],
									'dr_nr_med'=>$_POST['dr_nr_med'],
									'dr_nr_dental'=>$_POST['dr_nr_dental'],
									'medcert_date'=>date('Y-m-d H:i:s'),
									'transaction_date'=>date('Y-m-d H:i:s', strtotime($trans_date)),
									'with_medical'=>$_POST['with_medical'],
									'with_dental'=>$_POST['with_dental'],
									'history'=>"Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'create_dt'=>date('Y-m-d H:i:s'),
									'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'clinic_num'=>$encounter_nr,
									'medical_findings'=>$_POST['medical_findings'],
									'dental_findings'=>$_POST['dental_findings'],
									'with_optha'=>$_POST['with_optha'],
									'with_ent'=>$_POST['with_ent'],
									'optha_findings'=>$_POST['optha_findings'],
									'ent_findings'=>$_POST['ent_findings'],
									'dr_nr_optha'=>$_POST['dr_nr_optha'],
									'dr_nr_ent'=>$_POST['dr_nr_ent'],
									'refno'=>$refno

						);
						$rs = $obj_medCert->saveMedcert($data,'save');
						if($rs){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'."Failed to save data".'</font>';
						}
				break;
				case 'update':
				$cert_nr = $obj_medCert->getCertNr($refno,'seg_industrial_cert_med');
					$data = array(
									'remarks'=>$_POST['remarks'],
									'dr_nr_med'=>$_POST['dr_nr_med'],
									'dr_nr_dental'=>$_POST['dr_nr_dental'],
									'medcert_date'=>date('Y-m-d H:i:s'),
									'transaction_date'=>date('Y-m-d H:i:s', strtotime($trans_date)),
									'with_medical'=>$_POST['with_medical'],
									'with_dental'=>$_POST['with_dental'],
									'history'=>"Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
									'modify_dt'=>date('Y-m-d H:i:s'),
									'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
									'clinic_num'=>$encounter_nr,
									'medical_findings'=>$_POST['medical_findings'],
									'dental_findings'=>$_POST['dental_findings'],
									'with_optha'=>$_POST['with_optha'],
									'with_ent'=>$_POST['with_ent'],
									'optha_findings'=>$_POST['optha_findings'],
									'ent_findings'=>$_POST['ent_findings'],
									'dr_nr_optha'=>$_POST['dr_nr_optha'],
									'dr_nr_ent'=>$_POST['dr_nr_ent'],
									'refno'=>$refno

						);
						$rs = $obj_medCert->saveMedcert($data,'update');
						if ($rs){
								$errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
						}else{
								$errorMsg='<font style="color:#FF0000">'.$obj_medCert->getErrorMsg().'</font>';
						}
				break;
		}# end of switch statement
		#end art
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

//$confCertInfo = $obj_medCert->getConfCertRecord($encounter_nr);
//$reinstateCertInfo = $obj_medCert->getReinstatementCertRecord($encounter_nr);
$cthistoryInfo = $radio_obj->getCTHistory($encounter_nr);   //Added by Cherry 08-05-10
$trans = $obj_medCert->getInfoTrans($encounter_nr);
$medcertInfo = $obj_medCert->getAllInfoCertMed($refno);
#$medico_cases = $enc_obj->getMedicoCases();
$vs = $vital_sign->get_latest_vital_signs($encInfo['pid'], $encounter_nr);
//print_r($vs);
//echo "ss= ".$encInfo['consulting_dr_nr'];
//print_r($encInfo);

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
#added by art 03/14/2014
echo '<script type="text/javascript" src="'.$root_path.'js/jquery/jquery-1.8.2.js"></script>'."\n";
echo '<script type="text/javascript">
		var $j = jQuery.noConflict();
	</script>'."\n";
echo '<script type="text/javascript" src="js/med-cert.js"></script>';
#end art
?>

<script language="javascript">
<?php
		require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<script language="javascript">
		String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };
/* commented by art 03/14/2014
		function chkForm(){
					//alert('Mode= '+document.cert_med.mode.value);
				// if ($F('purpose')==''){
				// 		alert(" Please enter the purpose of requesting the certificate.");
				// 		$('purpose').focus();
				// 		return false;
				// }

				// return true;
			if(document.cert_med.with_medical.checked==true){
				if(document.cert_med.medical_findings.value=='' || document.cert_med.dr_nr_med.value==0){
					alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
					$('with_medical').focus();
					return false;
				}
			}

			else if(document.cert_med.with_dental.checked==true){
				if(document.cert_med.dental_findings.value=='' || document.cert_med.dr_nr_dental.value==0){
					alert('Please input dental findings in the provided area and select the dentist in-charge of the examination');
					$('with_dental').focus();
					return false;
				}
			}

			else if(document.cert_med.with_optha.checked==true){
				if(document.cert_med.optha_findings.value=='' || document.cert_med.dr_nr_optha.value==0){
					alert('Please input Opthalmology findings in the provided area and select the doctor in-charge of the examination');
					$('with_dental').focus();
					return false;
				}
			}

			else if(document.cert_med.with_ent.checked==true){
				if(document.cert_med.ent_findings.value=='' || document.cert_med.dr_nr_ent.value==0){
					alert('Please input ENT findings in the provided area and select the doctor in-charge of the examination');
					$('with_dental').focus();
					return false;
				}
			}

			else if(document.cert_med.with_medical.checked==false && document.cert_med.with_dental.checked==false && document.cert_med.with_optha.checked==false && document.cert_med.with_ent.checked==false){
				if(document.cert_med.medical_findings.value=='' || document.cert_med.dr_nr_med.value==0 || document.cert_med.dental_findings.value=='' || document.cert_med.dr_nr_dental.value==0|| document.cert_med.dr_nr_optha.value==0|| document.cert_med.dr_nr_ent.value==0){
					alert('Please input medical findings in the provided area and select the physician in-charge of the examination');
					$('with_medical').focus();
					return false;
				}
			}

			return true;

		}

		function showSurgicalDetails(){
			if(document.clinical_history.had_surgery.checked==true){
				document.getElementById('proc_date').style.display = '';
				document.getElementById('proc').style.display='';
			}else{
				document.getElementById('proc_date').style.display = 'none';
				document.getElementById('proc').style.display = 'none';
			}
		}

		//End Cherry
*/
		function closing(){
			 alert('hello');
			//close = <td align="RIGHT"><a href="javascript:return '+fnRef+'cClick();" '+closeevent+'="return '+fnRef+'cClick();" style="color: '+o3_closecolor+'; font-family: '+o3_closefont+'; font-size: '+o3_closesize+o3_closesizeunit+'; text-decoration: '+o3_closedecoration+'; font-weight: '+o3_closeweight+'; font-style:'+o3_closestyle+';">'+close+'</a></td>';
			cClick();
		}

		function checkType(thisType){

				type = thisType;
		}

		function printMedCert(encounter_nr){


						window.open("seg-ic-cert-med-pdf.php?encounter_nr="+encounter_nr,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}

		//Added by Cherry 08-09-10
		function printCTHistory(encounter_nr,pid){
			//alert('try lang');
				window.open("seg-radio-ct-history-pdf.php?encounter_nr="+encounter_nr+"&pid="+pid,"medicalCertificate","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
		}
/* commented by art 03/14/2014
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

		function showOptha(){
			if(document.cert_med.with_optha.checked==true){
				document.getElementById('optha').style.display='';
				document.getElementById('optha_findings').style.display = '';
				document.getElementById('dr_nr_optha').style.display = '';
				document.getElementById('opthalmologist').style.display = '';
				document.getElementById('show_optha').style.display ='';
			}else{
				document.getElementById('optha').style.display='none';
				document.getElementById('optha_findings').style.display='none';
				document.getElementById('dr_nr_optha').style.display='none';
				document.getElementById('opthalmologist').style.display='none';
				document.getElementById('show_optha').style.display ='none';
			}
		}

		function showEnt(){
			if(document.cert_med.with_ent.checked==true){
				document.getElementById('ent').style.display='';
				document.getElementById('ent_findings').style.display = '';
				document.getElementById('dr_nr_ent').style.display = '';
				document.getElementById('entologist').style.display = '';
				document.getElementById('show_ent').style.display ='';
			}else{
				document.getElementById('ent').style.display='none';
				document.getElementById('ent_findings').style.display='none';
				document.getElementById('dr_nr_ent').style.display='none';
				document.getElementById('entologist').style.display='none';
				document.getElementById('show_ent').style.display ='none';
			}
		}		
*/
		function digitsOnly(obj){
obj.value=obj.value.replace(/[^\d]/g,'');
//obj.value=obj.value.replace(/^\d+$/,'');
}

/* commented by art 03/14/2014
		//End Cherry

		function preset(){
			//alert('HOY!');
				var d = document.cert_med;

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

			if(d.with_optha.checked==true){
				document.getElementById('optha').style.display='';
				document.getElementById('optha_findings').style.display = '';
				document.getElementById('dr_nr_optha').style.display = '';
				document.getElementById('opthalmologist').style.display = '';
				document.getElementById('show_optha').style.display ='';
			}else{
				document.getElementById('optha').style.display='none';
				document.getElementById('optha_findings').style.display='none';
				document.getElementById('dr_nr_optha').style.display='none';
				document.getElementById('opthalmologist').style.display='none';
				document.getElementById('show_optha').style.display ='none';
			}

			if(d.with_ent.checked==true){
				document.getElementById('ent').style.display='';
				document.getElementById('ent_findings').style.display = '';
				document.getElementById('dr_nr_ent').style.display = '';
				document.getElementById('entologist').style.display = '';
				document.getElementById('show_ent').style.display ='';
			}else{
				document.getElementById('ent').style.display='none';
				document.getElementById('ent_findings').style.display='none';
				document.getElementById('dr_nr_ent').style.display='none';
				document.getElementById('entologist').style.display='none';
				document.getElementById('show_ent').style.display ='none';
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
		// }

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
														echo floor((time() - strtotime($encInfo['date_birth']))/31556926).' old';

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
			<td valign="top" align="center" bgcolor="#CCCCFF">
				<font color="#000099">
						Medical / Dental Certificate
				</font>
			</td>
		</tr>

<!-- start dental -->
		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>
				<?php
					if($medcertInfo){
						if($medcertInfo['with_dental']=='1'){
							$checked_dental_true = "checked";
						}else{
							$checked_dental_true = "";
						}
					}
				?>
				<input type="hidden" value="0" name="with_dental">
				<input type="checkbox" id="with_dental" name="with_dental" onclick="showDental();" value="1" <?php echo $checked_dental_true; ?>> Dental
			</td>
		</tr>

		<tr id="show_dental">
				<td colspan="*" height="23" bgcolor="#FFFFFF" >
					<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
						<tr>
								<td align="left" id="dental" name="dental">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<strong>Dental Findings:</strong>
								</td>
								<td>
									<textarea cols="43" rows="5" name="dental_findings" id="dental_findings" wrap="physical"><?php echo $medcertInfo['dental_findings']; ?></textarea>
								</td>
							</tr>

							<tr>
								<td id="dentist">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<strong>Dentist</strong>
								</td>
								<td>
									<select name="dr_nr_dental" id="dr_nr_dental">
									<option value='0'>-Select a doctor-</option>
									<?php

										#edited by VAN 04-28-08
										$listDoctors = array_unique($listDoctors);

										if (empty($medCertInfo['dr_nr_dental']))
											$medCertInfo['dr_nr_dental'] = 0;

										foreach($listDoctors as $key=>$value){

											#echo "key = ".$key;
											#echo "<br>val = ".$value;
											if ($medcertInfo['dr_nr_dental']==$key){
												echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
											}else{
												echo "				<option value='".$key."'>".$value."</option> \n";
											}
										}
									?>
												</select>
									<input type="hidden" name="dr_nr2" id="dr_nr2" value="<?=$medCertInfo['dr_nr_dental']?>">
								</td>
							</tr>

					 </table>
				</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>
<!-- end dental -->
<!-- start optha -->

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			<?php 
				if ($medcertInfo) {
					if($medcertInfo['with_optha']=='1'){
						$checked_optha_true = "checked";
					}else{
						$checked_optha_true = "";
					}
				}
			?>
			<input type="hidden" value="0" name="with_optha">
			<input type="checkbox" id="with_optha" name="with_optha" onclick="showOptha();" value="1" <?php echo $checked_optha_true; ?>>	Opthalmology
		</td>
	</tr>
	<tr id="show_optha">
		<td class="jedPanel" align="right">
			<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
				<tr>
					<td align="left" id="optha" name="optha">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<strong>Opthalmology Findings:</strong>
					</td>
					<td>
						<textarea cols="43" rows="5" name="optha_findings" id="optha_findings" wrap="physical"><?php echo $medcertInfo['optha_findings']; ?></textarea>
					</td>
				</tr>

				<tr>
					<td id="opthalmologist">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<strong>Doctor</strong>
					</td>
					<td>
						<select name="dr_nr_optha" id="dr_nr_optha">
						<option value='0'>-Select a doctor-</option>
						<?php

							#edited by VAN 04-28-08
							$listDoctors = array_unique($listDoctors);

							if (empty($medCertInfo['dr_nr_optha']))
								$medCertInfo['dr_nr_optha'] = 0;

							foreach($listDoctors as $key=>$value){

								#echo "key = ".$key;
								#echo "<br>val = ".$value;
								if ($medcertInfo['dr_nr_optha']==$key){
									echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
								}else{
									echo "				<option value='".$key."'>".$value."</option> \n";
								}
							}
						?>
						</select>
						<input type="hidden" name="dr_nr3" id="dr_nr3" value="<?=$medCertInfo['dr_nr_optha']?>">
					</td>
				</tr>
			</table>
		</td>
		
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

<!-- end optha -->

<!-- start ent -->

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td valign="top" bgcolor="#F8F9FA">
			<?php 
				if ($medcertInfo) {
					if($medcertInfo['with_ent']=='1'){
						$checked_ent_true = "checked";
					}else{
						$checked_ent_true = "";
					}
				}
			?>
			<input type="hidden" value="0" name="with_ent">
			<input type="checkbox" id="with_ent" name="with_ent" onclick="showEnt();" value="1" <?php echo $checked_ent_true; ?>>	ENT
		</td>
	</tr>
	<tr id="show_ent">
		<td class="jedPanel" align="right">
			<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
				<tr>
					<td align="left" id="ent" name="ent">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<strong>ENT Findings:</strong>
					</td>
					<td>
						<textarea cols="43" rows="5" name="ent_findings" id="ent_findings" wrap="physical"><?php echo $medcertInfo['ent_findings']; ?></textarea>
					</td>
				</tr>

				<tr>
					<td id="entlogist">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<strong>Doctor</strong>
					</td>
					<td>
						<select name="dr_nr_ent" id="dr_nr_ent">
						<option value='0'>-Select a doctor-</option>
						<?php

							#edited by VAN 04-28-08
							$listDoctors = array_unique($listDoctors);

							if (empty($medCertInfo['dr_nr_ent']))
								$medCertInfo['dr_nr_ent'] = 0;

							foreach($listDoctors as $key=>$value){

								#echo "key = ".$key;
								#echo "<br>val = ".$value;
								if ($medcertInfo['dr_nr_ent']==$key){
									echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
								}else{
									echo "				<option value='".$key."'>".$value."</option> \n";
								}
							}
						?>
						</select>
						<input type="hidden" name="dr_nr4" id="dr_nr4" value="<?=$medCertInfo['dr_nr_ent']?>">
					</td>
				</tr>
			</table>
		</td>
		
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

<!-- end ent -->

<!-- start medical -->
		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td valign="top" bgcolor="#F8F9FA">
				<?php
					if($medcertInfo){
						if($medcertInfo['with_medical']=='1'){
							$checked_medical_true = "checked";
						}else{
							$checked_medical_true = "";
						}
					}
				?>
				<input type="hidden" value="0" name="with_medical">
				<input type="checkbox" id="with_medical" name="with_medical" onclick="showMedical();" value="1" <?php echo $checked_medical_true; ?>>	Medical
				</td>
		</tr>
		<tr id="show_medical">
						<td class="jedPanel" align="right">
						<table width="100%" border="0" bgcolor="#F8F9FA"class="style4">
							<tr>
								<td align="left" id="medical" name="medical">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<strong>Medical Findings:</strong>
								</td>
								<td>
									<textarea cols="43" rows="5" name="medical_findings" id="medical_findings" wrap="physical"><?php echo $medcertInfo['medical_findings']; ?></textarea>
								</td>
							</tr>

							<tr>
								<td id="physician">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<strong>Physician</strong>
								</td>
								<td>
									<select name="dr_nr_med" id="dr_nr_med">
									<option value='0'>-Select a doctor-</option>
									<?php

										#edited by VAN 04-28-08
										$listDoctors = array_unique($listDoctors);

										if (empty($medCertInfo['dr_nr_med']))
											$medCertInfo['dr_nr_med'] = 0;

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
									<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$medcertInfo['dr_nr_med']?>">
								</td>
							</tr>

						</table>
						</td>

		<tr>
			<td>&nbsp;</td>
		</tr>
<!-- end medical -->
		<tr>
			<td>
				<table width="100%" border="0" bgcolor="#F8F9FA"class="style3">
					<tr>
						<td><strong>Note:</strong></td>
						<td>
							<textarea id="remarks" cols="43" rows="5" name="remarks"><?php echo $medcertInfo['remarks']; ?></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<!--<tr>
			<td>
				<strong>Clinic Number:</strong>&nbsp;&nbsp;&nbsp;
				<input type="text" id="clinic_num" name="clinic_num" size="30" onkeypress="digitsOnly(this);">
			</td>
		</tr>  -->

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
			<td>Encoded by: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="text" size="30" id="doc_name" name="doc_name" value="<?=$HTTP_SESSION_VARS["sess_user_name"]; ?>" readonly="readonly">
			</td>
		</tr>

		<tr>
			<td>&nbsp;</td>
		</tr>

		<tr>
				<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php
						if (!$medcertInfo || empty($medcertInfo)){
								echo '            <input type="hidden" name="mode" id="mode" value="save">'."\n";
								echo '            <input type="submit" name="Submit" id="submit" value="Save">'."\n";
						}else{
								echo '            <input type="hidden" name="mode" id="mode" value="update">'."\n";
								echo '            <input type="button" name="Print" value="Print" onClick="printMedCert('.$encounter_nr.')">'."\n &nbsp; &nbsp;";
								echo '            <input type="submit" name="Submit" value="Update">'."\n";
						}
						echo '            <input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
						echo '						<input type="hidden" name="refno" id="refno" value="'.$refno.'">'."\n";
?>
						&nbsp; &nbsp;
						<!--<input type="button" name="Cancel" value="Cancel"  onclick="closeWindow();"> -->
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
