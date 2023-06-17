
<?php
require_once("roots.php");

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);

define('LANG_FILE','aufnahme.php'); 
$local_user='aufnahme_user';

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;
	# Create the helper class for the country table
include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');

#added by VAN 05-23-08
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

require_once($root_path.'include/care_api_classes/class_gui_input_person.php');
$cominfo = new GuiInputPerson();

require_once($root_path.'modules/registration_admission/address.common.php');
if ($xajax) {
		$xajax->printJavascript($root_path.'classes/xajax');
	}

if ($row = $objInfo->getAllHospitalInfo()) {
		$row['hosp_agency'] = strtoupper($row['hosp_agency']);
		$row['hosp_name']   = strtoupper($row['hosp_name']);
}
else {
		$row['hosp_country'] = "Republic of the Philippines";
		$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
		$row['hosp_name']    = "BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY";
		$row['hosp_addr1']   = "Malaybalay, Bukidnon";
		$row['mun_name']     = "Malaybalay";
		$row['prov_name']     = "Bukidnon";
		$row['region_name']     = "Region X";
}

#added by VAN 05-19-08
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;
#------------------

require($root_path.'include/inc_front_chain_lang.php');

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}
#echo "pid = ".$pid;
include_once($root_path.'include/care_api_classes/class_cert_birth.php');
$obj_birthCert = new BirthCertificate($pid);

include_once($root_path.'include/care_api_classes/class_globalconfig.php');
$GLOBAL_CONFIG = array();
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
$glob_obj->getConfig('default_religion%');
/*
if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	#echo "sql = ".$person_obj->sql;
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid PID!</em>';
	exit();
}
*/
$birthYear = intval(substr($date_birth, 0, 4));
$birthMonth = intval(substr($date_birth, 5, 7));
$birthDay = intval(substr($date_birth, 8, 10));
/*
echo "birthYear = '".$birthYear."' <br>\n";
echo "birthMonth = '".$birthMonth."' <br>\n";
echo "birthDay = '".$birthDay."' <br>\n";
*/
$errorMsg='';

if (trim($HTTP_POST_VARS['birth_time'])!=''){
	#convert to 24-Hour time
	$birth_time_array_tmp = explode(':',$HTTP_POST_VARS['birth_time']);
	$hr = intval($birth_time_array_tmp[0]);
	$min = $birth_time_array_tmp[1];
	if($_POST['selAMPM'] == 'P.M.'){
		if(($hr>0)&&($hr<12)){
		$hr = $hr+12;
		}
		$HTTP_POST_VARS['birth_time_tmp2'] = $hr.':'.$min.':00';
		$HTTP_POST_VARS['birth_time'] = $hr.':'.$min.':00';
	}elseif(($_POST['selAMPM'] == 'A.M.')&&($hr==12)){
		$HTTP_POST_VARS['birth_time_tmp2'] = '00:'.$min.':00';
		$HTTP_POST_VARS['birth_time'] = '00:'.$min.':00';
	}
}
/*
echo "HTTP_POST_VARS['birth_time_tmp2'] = '".$HTTP_POST_VARS['birth_time_tmp2']."' <br> \n";

echo "HTTP_POST_VARS['officer_date_sign'] = '".$HTTP_POST_VARS['officer_date_sign']."' <br> \n";
echo "_POST['officer_date_sign'] = '".$_POST['officer_date_sign']."' <br> \n";

echo "HTTP_POST_VARS : <br>"; print_r($HTTP_POST_VARS); echo "<br> \n";
echo "pid = '".$pid."' <br> \n";
*/

#added by VAN 05-19-08

$HTTP_POST_VARS['mother_fname'] = stripslashes($HTTP_POST_VARS['m_name_first']);
$HTTP_POST_VARS['mother_maidenname'] = stripslashes($HTTP_POST_VARS['m_name_middle']);
$HTTP_POST_VARS['mother_mname'] = stripslashes($HTTP_POST_VARS['m_name_middle']);
$HTTP_POST_VARS['mother_lname'] = stripslashes($HTTP_POST_VARS['m_name_last']);

#N/A
if ($HTTP_POST_VARS['f_name_first']=="n/a")
	$HTTP_POST_VARS['father_fname'] = " ";
else
	$HTTP_POST_VARS['father_fname'] = stripslashes($HTTP_POST_VARS['f_name_first']);

if ($HTTP_POST_VARS['f_name_middle']=="n/a")
	$HTTP_POST_VARS['father_mname'] = " ";
else
	$HTTP_POST_VARS['father_mname'] = stripslashes($HTTP_POST_VARS['f_name_middle']);

if ($HTTP_POST_VARS['f_name_last']=="n/a")
	$HTTP_POST_VARS['father_lname'] = " ";
else
	$HTTP_POST_VARS['father_lname'] = stripslashes($HTTP_POST_VARS['f_name_last']);

$bdate = $HTTP_POST_VARS['birth_year']."-".$HTTP_POST_VARS['birth_month']."-".$HTTP_POST_VARS['birth_day'];
$HTTP_POST_VARS['date_birth'] = date("Y-m-d",strtotime($bdate));

#------------------------------

#added by VAN 08-05-08
#echo "<br>resident = ".$is_resident."<br>";
#echo "<br>nonres dr = ".$HTTP_POST_VARS['attendant_name2']."<br>";
#echo "<br>resident dr = ".$HTTP_POST_VARS['attendant_name']."<br>";
if ($is_resident){
	$HTTP_POST_VARS['attendant_name'] = stripslashes($HTTP_POST_VARS['attendant_name']);
	$HTTP_POST_VARS['non_resident_status'] = '';
}else{
	$HTTP_POST_VARS['attendant_name'] = stripslashes($HTTP_POST_VARS['attendant_name2']);
	$HTTP_POST_VARS['non_resident_status'] = $HTTP_POST_VARS['nr_status'];
}
#added by VAN 08-06-08
#echo 	"type = ".$HTTP_POST_VARS['late_married_type'];
#echo "is married = ".$HTTP_POST_VARS['is_married'];
#echo "<br>date = ".$HTTP_POST_VARS['parent_marriage_date'];
#echo "<br>place = ".$HTTP_POST_VARS['parent_marriage_place'];

// added by: syboy 06/27/2015
$HTTP_POST_VARS['m_occupation_other'] = stripslashes($HTTP_POST_VARS['m_occupation_other']);
$HTTP_POST_VARS['f_occupation_other'] = stripslashes($HTTP_POST_VARS['f_occupation_other']);
// end
// var_dump($_POST); die();
if($HTTP_POST_VARS['is_married']==1){
	$HTTP_POST_VARS['parent_marriage_date'] = formatDate2STD($HTTP_POST_VARS['parent_marriage_date'],$date_format,$tmp);
	$HTTP_POST_VARS['parent_marriage_place'] = $HTTP_POST_VARS['parent_marriage_place'];
		$HTTP_POST_VARS['f_fullname'] = " ";
}else{
		 #$HTTP_POST_VARS['f_fullname'] = stripslashes($HTTP_POST_VARS['f_fullname']);
         #$HTTP_POST_VARS['f_fullname'] = addslashes($HTTP_POST_VARS['f_fullname']);
         $HTTP_POST_VARS['f_fullname'] = $HTTP_POST_VARS['f_fullname'];
}

if ($HTTP_POST_VARS['f_fullname'] == '(father\'s name)')
   $HTTP_POST_VARS['f_fullname'] = " ";


if($istribal)
	$HTTP_POST_VARS['is_tribalwed'] = 1;
else
	$HTTP_POST_VARS['is_tribalwed'] = 0;

if ($is_late_reg)
    $HTTP_POST_VARS['is_late_reg'] = 1;
else
    $HTTP_POST_VARS['is_late_reg'] = 0;

if (($HTTP_POST_VARS['late_married_type']==1)&&($HTTP_POST_VARS['is_married']==0)){
	$HTTP_POST_VARS['parent_marriage_date'] = formatDate2STD($HTTP_POST_VARS['late_date_married'],$date_format,$tmp);
	$HTTP_POST_VARS['parent_marriage_place'] = $HTTP_POST_VARS['late_place_married'];
}elseif (($HTTP_POST_VARS['late_married_type']==2)&&($HTTP_POST_VARS['is_married']==0)){
	$HTTP_POST_VARS['parent_marriage_date'] = '0000-00-00';
	$HTTP_POST_VARS['parent_marriage_place'] = "";
}

$mdate = trim($HTTP_POST_VARS['parent_marriage_date']);
if (empty($mdate)){
    $HTTP_POST_VARS['parent_marriage_date'] = '0000-00-00';
    #$HTTP_POST_VARS['parent_marriage_place'] = "";
}

$HTTP_POST_VARS['is_subject_person'] = $is_subject_person;

if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':
			$tmp=array();
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
#			if (!empty(trim($HTTP_POST_VARS['attendant_date_sign']))){
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['f_com_tax_date'])!=""){
				$HTTP_POST_VARS['f_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['f_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['m_com_tax_date'])!=""){
				$HTTP_POST_VARS['m_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['m_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['officer_date_sign'])!=""){
				$HTTP_POST_VARS['officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['officer_date_sign'],$date_format,$tmp);
			}
            if (trim($HTTP_POST_VARS['receiver_date_sign'])!=""){
                $HTTP_POST_VARS['receiver_date_sign'] = formatDate2STD($HTTP_POST_VARS['receiver_date_sign'],$date_format,$tmp);
            }
			$HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
			$HTTP_POST_VARS['create_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');

			$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['m_residence_basic'];
			$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['m_residence_brgy'];

			#if (trim($HTTP_POST_VARS['parent_marriage_date'])!=""){
				#$HTTP_POST_VARS['parent_marriage_date'] = formatDate2STD($HTTP_POST_VARS['parent_marriage_date'],$date_format,$tmp);
			#}

			if (trim($HTTP_POST_VARS['affiant_com_tax_date'])!=""){
				$HTTP_POST_VARS['affiant_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['affiant_com_tax_date'],$date_format,$tmp);
			}

            if (trim($HTTP_POST_VARS['affiant_com_tax_date2'])!=""){
                $HTTP_POST_VARS['affiant_com_tax_date2'] = formatDate2STD($HTTP_POST_VARS['affiant_com_tax_date2'],$date_format,$tmp);
            }

			if (trim($HTTP_POST_VARS['late_officer_date_sign'])!=""){
				$HTTP_POST_VARS['late_officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['late_officer_date_sign'],$date_format,$tmp);
			}
			# added by: syboy 11/12/2015 : meow
			$f_residence_basic = $HTTP_POST_VARS['f_residence_basic'];
			$m_residence_basic = $HTTP_POST_VARS['m_residence_basic'];
			$informant_address = $HTTP_POST_VARS['informant_address'];
			unset($HTTP_POST_VARS['f_residence_basic']);
			unset($HTTP_POST_VARS['m_residence_basic']);
			unset($HTTP_POST_VARS['informant_address']);
			# ended
			if ($obj_birthCert->saveBirthCertificateInfoFromArray($HTTP_POST_VARS, $f_residence_basic, $m_residence_basic, $informant_address)){
				#$errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
				#added by VAN 05-19-08
				#update person table, first name,middle name, last name, birthdate, sex,
				# mother's name, father's name
				$HTTP_POST_VARS['history'] = $obj_birthCert->ConcatHistory("Update : Birth Reg. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");

				if (empty($HTTP_POST_VARS['name_middle']))
					$HTTP_POST_VARS['name_middle'] = " ";
				// uncomment by carriane to update other details 02/20/18
				// Comment by Carl .. cause of editing admission street details
				$person_obj->updatePersonInfo($pid, $HTTP_POST_VARS);

				// SAVE PATIENT INFO
				require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
				$ehr = Ehr::instance();
				$data = array(
					'pid'	=> $HTTP_POST_VARS['pid']
				);
				$patient = $ehr->getAddPersonData($data);
				$ehrResponse = $ehr->getResponseData();
				$EHRstatus = $patient->status;
				if(!$EHRstatus){

				}
				#edited by VAN 02-15-08
				echo " \n <script type=\"text/javascript\">alert(\"Saved sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break;
		case 'update':
		//Added by Mats
		$encount_nr = $_GET['encounter_nr'];
		$sql1 = "SELECT is_final,ce.encounter_nr,pid FROM seg_billing_encounter AS sbe 
				 INNER JOIN care_encounter AS ce ON ce.encounter_nr = sbe.encounter_nr 
				 WHERE ce.encounter_nr =".$db->qstr($encount_nr)." ORDER BY sbe.`bill_dte` DESC";
		$result = $db->GetRow($sql1);
		if($result){
			$ins_nr = $result['encounter_nr'];
			$is_final = $result['is_final'];
			$is_pid = $result['pid'];
			
		}
		$sql2 = "SELECT sei.encounter_nr as nrs ,seim.encounter_nr as nrs_info FROM seg_encounter_insurance AS sei 
 				 INNER JOIN seg_encounter_insurance_memberinfo AS seim ON sei.encounter_nr = seim.encounter_nr where sei.encounter_nr=".$db->qstr($encount_nr)."AND seim.encounter_nr=".$db->qstr($encount_nr);
		$result_nr = $db->GetRow($sql2);
		if($result_nr){
			$nrs = $result_nr['nrs'];
			$nrs_info=$result_nr['nrs_info'];
		}
		//Add ended here by Mats

			$tmp=array();
			if (trim($HTTP_POST_VARS['attendant_date_sign'])!=""){
#			if (!empty(trim($HTTP_POST_VARS['attendant_date_sign']))){
				$HTTP_POST_VARS['attendant_date_sign'] = formatDate2STD($HTTP_POST_VARS['attendant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['informant_date_sign'])!=""){
				$HTTP_POST_VARS['informant_date_sign'] = formatDate2STD($HTTP_POST_VARS['informant_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['encoder_date_sign'])!=""){
				$HTTP_POST_VARS['encoder_date_sign'] = formatDate2STD($HTTP_POST_VARS['encoder_date_sign'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['f_com_tax_date'])!=""){
				$HTTP_POST_VARS['f_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['f_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['m_com_tax_date'])!=""){
				$HTTP_POST_VARS['m_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['m_com_tax_date'],$date_format,$tmp);
			}
			if (trim($HTTP_POST_VARS['officer_date_sign'])!=""){
				$HTTP_POST_VARS['officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['officer_date_sign'],$date_format,$tmp);
			}
            if (trim($HTTP_POST_VARS['receiver_date_sign'])!=""){
                $HTTP_POST_VARS['receiver_date_sign'] = formatDate2STD($HTTP_POST_VARS['receiver_date_sign'],$date_format,$tmp);
            }
			$HTTP_POST_VARS['history'] = $obj_birthCert->ConcatHistory("Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
			$HTTP_POST_VARS['modify_id']=$HTTP_SESSION_VARS['sess_user_name'];
			$HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');

			$HTTP_POST_VARS['street_name'] = $HTTP_POST_VARS['m_residence_basic'];
			$HTTP_POST_VARS['brgy_nr'] = $HTTP_POST_VARS['m_residence_brgy'];
			#if (trim($HTTP_POST_VARS['parent_marriage_date'])!=""){
				#$HTTP_POST_VARS['parent_marriage_date'] = formatDate2STD($HTTP_POST_VARS['parent_marriage_date'],$date_format,$tmp);
			#}

			if (trim($HTTP_POST_VARS['affiant_com_tax_date'])!=""){
				$HTTP_POST_VARS['affiant_com_tax_date'] = formatDate2STD($HTTP_POST_VARS['affiant_com_tax_date'],$date_format,$tmp);
			}

            if (trim($HTTP_POST_VARS['affiant_com_tax_date2'])!=""){
                $HTTP_POST_VARS['affiant_com_tax_date2'] = formatDate2STD($HTTP_POST_VARS['affiant_com_tax_date2'],$date_format,$tmp);
            }

			if (trim($HTTP_POST_VARS['late_officer_date_sign'])!=""){
				$HTTP_POST_VARS['late_officer_date_sign'] = formatDate2STD($HTTP_POST_VARS['late_officer_date_sign'],$date_format,$tmp);
			}


			#echo "<br>brgy = ".$HTTP_POST_VARS['brgy_nr'];

			if ($obj_birthCert->updateBirthCertificateInfoFromArray($HTTP_POST_VARS)){
				#added by VAN 05-19-08
				#update person table, first name,middle name, last name, birthdate, sex,
				# mother's name, father's name
				$HTTP_POST_VARS['history'] = $obj_birthCert->ConcatHistory("Update : Birth Reg. ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']."\n");
				#echo "history = ".$HTTP_POST_VARS['history'];
				if (empty($HTTP_POST_VARS['name_middle']))
					$HTTP_POST_VARS['name_middle'] = " ";

				if(!Person::setAdmissionDate($HTTP_POST_VARS['pid'],$HTTP_POST_VARS['date_birth']." ".$HTTP_POST_VARS['birth_time'])){
					echo " \n <script type=\"text/javascript\">alert(\"ERROR: Unable to update patient's admissiond date!\")</script>";
				}

				// uncomment by carriane to update other details 02/20/18
				// Comment by Carl .. cause of editing admission street details
				$person_obj->updatePersonInfo($pid, $HTTP_POST_VARS);

				// UPDATE PATIENT INFO
				require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
				$ehr = Ehr::instance();
				$data = array(
					'pid'	=> $HTTP_POST_VARS['pid']
				);
				$patient = $ehr->getAddPersonData($data);
				$ehrResponse = $ehr->getResponseData();
				$EHRstatus = $patient->status;
				if(!$EHRstatus){

				}

				try {
                	//save new record
                    require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');
                    $patientService = new PatientEmrService();
                    #add new argument to detect if to update patient demographic or not
                    $patientService->savePatient($pid, 1);
                } catch (Exception $exc) {
                    //echo $exc->getTraceAsString();
                }
                //added by Mmats 08262016
                
                 $names_last = $HTTP_POST_VARS['name_last'];
                 $names_first = $HTTP_POST_VARS['name_first'];
				 $names_middle = $HTTP_POST_VARS['name_middle'];
				 $sexs = $HTTP_POST_VARS['sex'];
				 $birth_days = $HTTP_POST_VARS['date_birth'];
				
				 
				  $birth_days1 = strtotime($birth_days);
				  $birth_days2 = date('m/d/Y',$birth_days1);
		
				
			
				 if($sexs == "f"){
							$sexs = "Female";
						}
						else
						{
							$sexs = "Male";
						}
				if(($_POST['name_first'] != $_POST['name_first_data']) || ($_POST['name_middle'] != $_POST['name_middle_data']) 
																	   || ($_POST['name_last'] != $_POST['name_last_data']) 
																	   || ($birth_days2 != $_POST['late_bdate']) 
																	   || ($_POST['sex'] != $_POST['sex_data'])
																	  ){
							if($is_final!='1' && $encount_nr==$nrs && $ins_nr!='' ){
									echo '<input id="alertB" type="hidden" value="1">'; 
										}
							elseif($is_final!='1' && $encount_nr==$nrs && $nrs_info !='' ){
									echo '<input id="alertB" type="hidden" value="1">'; 
									// echo " \n <script type=\"text/javascript\">alert(\"Update Success:\\nGiven Name : $names_last \\nFirst Name: $names_first\\nSex: $sexs\\nBirthday: $birth_days \")</script>";
										}
						else{
									echo "<script>alert('Update Success')</script>";
							}
							//ended

				 }else{
				 	echo "<script>alert('Update Success')</script>";
				 }

				
			}else{
				#$errorMsg='<font style="color:#FF0000"> error '.$obj_birthCert->getErrorMsg().'</font>'; 
                                #echo "sql = ".$obj_birthCert->sql;
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break;
	}# end of switch statement
}
#echo "sql = ".$obj_birthCert->sql;
if ($pid){
	if (!($basicInfo=$person_obj->BasicDataArray($pid))){
		echo '<em class="warn"> sorry byt the page cannot be displayed!</em>';
		exit();
	}
#	echo "basicInfo : <br> \n"; print_r($basicInfo); echo "<br>\n";
	#echo "sql = ".$person_obj->sql;
	extract($basicInfo);
}else{
	echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid HRN!</em>';
	exit();
}


$birthCertInfo = $obj_birthCert->getBirthCertRecord($pid);
#echo "cert_birth_interface.php : obj_birthCert->sql = '".$obj_birthCert->sql."' <br> \n";
if ($birthCertInfo){
	extract($birthCertInfo);
	#$marriage_type = substr($parent_marriage_info, 0, 1);
	#$parent_marriage_info_tmp = substr($parent_marriage_info, 4);
/*
	echo "parent_marriage_info  = '".$parent_marriage_info."' <br> \n";
	echo "marriage_type  = '".$marriage_type."' <br> \n";
	echo "parent_marriage_info_tmp = '".$parent_marriage_info_tmp."' <br> \n";
*/
	$attendant_type = substr(trim($birthCertInfo['attendant_type']),0,1);
	$attendant_type_others = substr(trim($birthCertInfo['attendant_type']),4);
#	$attendant_type = substr(trim($attendant_type),0,1);
#	$attendant_type_others = substr(trim($attendant_type),4);
/*
	echo "attendant_type = '".$attendant_type."' <br> \n";
	echo "attendant_type_others = '".$attendant_type_others."' <br> \n";
	echo "f_com_tax_nr = '".$f_com_tax_nr."' <br> \n";
	echo "m_com_tax_nr = '".$m_com_tax_nr."' <br> \n";
	echo "birthCertInfo : <br>"; print_r($birthCertInfo); echo "<br> \n";
*/
}

	//if ($marriage_type=="2"){
	if ($is_married=="0"){
		# Not Married
		$presetJavascript = "
				blocking('married_parents', false);
				blocking('tribal', false);
				blocking('married_parents_not', true);";
	}else{
		# Married
		$presetJavascript = "
				blocking('married_parents', true);
				blocking('tribal', true);
				blocking('married_parents_not', false);";
	}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Certificate of Live Birth</title>
<style type="text/css">
<!--
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #FFFFFF;
}
.style4 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #FFFFFF;
	font-weight: bold;
}
.style6 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; }
.style8 {color: #7C3501}
.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #333300; }
.style11 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #7C3501; }
.style12 {color: #000066}
.style13 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #000066; }
.style15 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #FF6600; }
.style18 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #F9FFFB; }
.required {font-family: Arial, Helvetica, sans-serif; font-size: 12px; } /*added by KENTOOT 06/25/2014*/
a {
	font-size: 10px;
	color: #006600;
	font-family: Arial, Helvetica, sans-serif;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
-->
</style>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
/*
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
#edited by VAN 02-16-08
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jquery/ui/jquery-ui.js"></script>'."\n";

?>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
<script>var noC = jQuery.noConflict();</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

<!--<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>-->

<script language="javascript">
    var $J = jQuery.noConflict();

    jQuery(function($){
         $J("#parent_marriage_date").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#f_com_tax_date").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#m_com_tax_date").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#officer_date_sign").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#attendant_date_sign").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#informant_date_sign").mask("99/99/9999");
    });

   /* jQuery(function($){
         $J("#late_bdate4").mask("99/99/9999");
    });*/

    jQuery(function($){
         $J("#late_date_married").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#affiant_com_tax_date").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#late_officer_date_sign").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#affiant_com_tax_date2").mask("99/99/9999");
    });

    jQuery(function($){
         $J("#encoder_date_sign").mask("99/99/9999");
    });


/**/
	$J(document).ready(function(){
    	if($J('#alertB').val() ==1){
    		alertbox();
    	}
    });

    function insertEmptyAudit(){
    	var is_pid = '<?=$pid?>';
    	var encoder='<?=$_SESSION[sess_login_username]?>';                           
		xajax_addauditPHIC(is_pid,"",encoder);        
    }

    function alertbox(){
    	var is_pid = '<?=$pid?>';
    	var encoder='<?=$_SESSION[sess_login_username]?>';
    	var name_first_data_value = "<?php echo $names_first;?>";
    	var name_last_data_value  ="<?php echo $names_last;?>";
    	var gender = "<?php echo $sexs;?>";
    	var DOB_data ="<?php echo $birth_days;?>";
    	var src = "<div style='background:red;'><center><h1 style='color:#ffffff;'>Patient has already submitted PHIC requirement in Billing</h1></center><h1 style='color:#ffffff;'>Last Name: "+name_last_data_value+" </h1>"+
												"<h1 style='color:#ffffff;'>First Name: "+name_first_data_value+" </h1>"+
												"<h1 style='color:#ffffff;'>Gender: "+gender+"</h1>"+
												"<h1 style='color:#ffffff;'>Date of Birth: "+DOB_data+" </h1>"+
												"</div>";
								$J(src).dialog({
				                        autoOpen: true,
				                        modal:true,
				                        height: "auto",
				                        width: "70%",
				                        resizable: false,
				                        show: "fade",
				                        hide: "explode",
				                        title: "Updated Data.",
				                        position: "top", 
				                        buttons: {
				                            "OK": function() 
				                            {
				                            	// xajax_addauditPHIC(is_pid,"",encoder);
				                            	$J( this ).dialog( "close" );
				                              
				                            }
				                           
				                        }
				                    });
    }
/**/



		/*
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words
				input: object
				output: object (string) value is trimmed
		*/	
	// added by: syboy 03/18/2016 : meow
	$J(function(){
		var is_view = $J('#viewbirthcert').val();
		if (is_view) {
			$J('#print_1').attr('disabled', true);
			$J('#update_1').attr('disabled', true);
			$J('#cancel_1').attr('disabled', true);
			$J('#registry_nr').attr('disabled', true);
			$J('#name_first').attr('disabled', true);
			$J('#name_middle').attr('disabled', true);
			$J('#name_last').attr('disabled', true);
			$J('#birth_day').attr('disabled', true);
			$J('#birth_month').attr('disabled', true);
			$J('#birth_year').attr('disabled', true);
			$J('#birth_place_basic').attr('disabled', true);
			$J('input[name="sex"]').attr('disabled', true);
			$J('input[name="birth_type_tmp"]').attr('disabled', true);
			$J('#birth_order').attr('disabled', true);
			$J('#birth_weight').attr('disabled', true);
			$J('#m_name_first').attr('disabled', true);
			$J('#m_name_middle').attr('disabled', true);
			$J('#m_name_last').attr('disabled', true);
			$J('#m_citizenship').attr('disabled', true);
			$J('#m_religion').attr('disabled', true);
			$J('#m_total_alive').attr('disabled', true);
			$J('#m_still_living').attr('disabled', true);
			$J('#m_now_dead').attr('disabled', true);
			$J('#m_fetal_death').attr('disabled', true);
			$J('#m_ethnic').attr('disabled', true);
			$J('#m_occupation').attr('disabled', true);
			$J('#m_age').attr('disabled', true);
			$J('#m_residence_basic').attr('disabled', true);
			$J('#m_residence_brgy').attr('disabled', true);
			$J('#m_residence_mun').attr('disabled', true);
			$J('#m_residence_prov').attr('disabled', true);
			$J('#m_residence_country').attr('disabled', true);
			$J('#f_name_first').attr('disabled', true);
			$J('#f_name_middle').attr('disabled', true);
			$J('#f_name_last').attr('disabled', true);
			$J('#f_citizenship').attr('disabled', true);
			$J('#f_religion').attr('disabled', true);
			$J('#f_ethnic').attr('disabled', true);
			$J('#f_occupation').attr('disabled', true);
			$J('#f_age').attr('disabled', true);
			$J('#f_residence_basic').attr('disabled', true);
			$J('#f_residence_mun').attr('disabled', true);
			$J('#f_residence_prov').attr('disabled', true);
			$J('#f_residence_country').attr('disabled', true);
			$J('#f_residence_brgy').attr('disabled', true);
			$J('input[name="is_married"]').attr('disabled', true);
			$J('#f_fullname').attr('disabled', true);
			$J('#officer_date_sign').attr('disabled', true);
			$J('#f_com_tax_nr').attr('disabled', true);
			$J('#f_com_tax_date').attr('disabled', true);
			$J('#late_officer_place_sign').attr('disabled', true);
			$J('#officer_title').attr('disabled', true);
			$J('#officer_address').attr('disabled', true);
			$J('#officer_name').attr('disabled', true);
			$J('input[name="istribal"]').attr('disabled', true);
			$J('#parent_marriage_date').attr('disabled', true);
			$J('#parent_marriage_place').attr('disabled', true);
			$J('input[name="attendant_type_tmp"]').attr('disabled', true);
			$J('#attendant_type_others').attr('disabled', true);
			$J('#birth_time').attr('disabled', true);
			$J('#selAMPM').attr('disabled', true);
			$J('#attendant_address').attr('disabled', true);
			$J('#is_resident').attr('disabled', true);
			$J('#attendant_name').attr('disabled', true);
			$J('#attendant_title').attr('disabled', true);
			$J('#attendant_date_sign').attr('disabled', true);
			$J('#informant_name').attr('disabled', true);
			$J('#informant_address').attr('disabled', true);
			$J('#informant_relation').attr('disabled', true);
			$J('#informant_date_sign').attr('disabled', true);
			$J('#is_late_reg').attr('disabled', true);
			$J('#encoder_name').attr('disabled', true);
			$J('#encoder_title').attr('disabled', true);
			$J('#encoder_date_sign').attr('disabled', true);
			$J('#receiver_name').attr('disabled', true);
			$J('#receiver_title').attr('disabled', true);
			$J('#receiver_date_sign').attr('disabled', true);
			$J('input[name="muslim_print"]').attr('disabled', true);
			$J('#print_2').attr('disabled', true);
			$J('#update_2').attr('disabled', true);
			$J('#cancel_2').attr('disabled', true);
		}
	});
	

	function fatherDateChecker(elem,date){
		if($('f_name_first').value.toLowerCase() != "n/a"){
			IsValidDate(elem,date);
		}
	}

	// added by: syboy 06/21/2015
	function displayOthers(){
		if ($('m_occupation').value == 650) {
			// alert('show');
			$('m_occupation_other').style.display = "";

		}else{
			// alert('hide');
			$('m_occupation_other').style.display = "none";

		}

		if ($('f_occupation').value == 650) {
			// alert('show');
			$('f_occupation_other').style.display = "";

		}else{
			// alert('hide');
			$('f_occupation_other').style.display = "none";

		}
	}
	// end

    function disableAdd(){

/*    	//added by KENTOOT 07/09/2014
    	if($('birth_type_tmp').checked){
			objBirthOrder = 1;
			$('birth_order').disabled = true;
		}else{
			ojBirthOrder = 0;
			$('birth_order').disabled = false;
		}//end KENTOOT commented by art 08/01/2014*/

        if($('m_residence_country').value != 'PH'){
            $('m_residence_prov').disabled = true;
            $('m_residence_mun').disabled = true;
            $('m_residence_brgy').disabled = true;
            //$('m_residence_basic').disabled = true;
        }else{
            // if ($('#viewbirthcert').value) {
            // 	$('m_residence_brgy').disabled = true;
            // 	$('m_residence_mun').disabled = true;
            // 	$('m_residence_prov').disabled = true;
            // }else{
            	$('m_residence_brgy').disabled = false;
            	$('m_residence_mun').disabled = false;
            	$('m_residence_prov').disabled = false;
            // }
            //$('m_residence_basic').disabled = false;
        }
        if($('f_residence_country').value != 'PH'){
            $('f_residence_prov').disabled = true;
            $('f_residence_mun').disabled = true;
            $('f_residence_brgy').disabled = true;
            //$('f_residence_basic').disabled = true;
        }else{
            /*$('f_residence_prov').disabled = false;
            $('f_residence_mun').disabled = false;
            $('f_residence_brgy').disabled = false;*/
            // if ($('#viewbirthcert').value) {
            // 	$('f_residence_prov').disabled = true;
            // 	$('f_residence_mun').disabled = true;
            // 	 $('f_residence_brgy').disabled = true;
            // }else{
            	$('f_residence_prov').disabled = false;
            	$('f_residence_mun').disabled = false;
            	$('f_residence_brgy').disabled = false;
            // }
            //$('f_residence_basic').disabled = false;
        }

        if (($('f_name_first').value=="n/a") || ($('f_name_first').value=="N/A")){
            $('f_residence_brgy').value = 0;
            $('f_residence_brgy').disabled = false;
            $('f_residence_mun').value = 0;
            $('f_residence_mun').disabled = false;
            $('f_residence_prov').value = 0;
            $('f_residence_prov').disabled = false;
            $('f_residence_country').value = 0;
            $('f_residence_country').disabled = false;
        }else{
            if ($('#viewbirthcert').value) {
            	$('f_residence_country').disabled = true;
            	$('f_residence_prov').disabled = true;
            	$('f_residence_mun').disabled = true;
            	 $('f_residence_brgy').disabled = true;
            }else{
            	$('f_residence_country').disabled = false;
            	$('f_residence_prov').disabled = false;
            	$('f_residence_mun').disabled = false;
            	$('f_residence_brgy').disabled = false;
            }
        }

    }


	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g," ");
		objct.value = objct.value.replace(/\s+/g," ");
	}/* end of function trimString */

	function trimString2(value){
		value = value.replace(/^\s+|\s+$/g,"");
		value = value.replace(/\s+/g," ");
	}/* end of function trimString */

	function blocking(objectName, flag){
//		alert("objectName = '"+objectName+"' \n flag = '"+flag+"'");
		if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
		} else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
		} else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
		}
	}/* end of function blocking*/



	var seg_validTime=false;

	function setFormatTime(thisTime){
	//	var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;

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

		if (hour==0){
			 hour = 12;
			 $('selAMPM').value = "A.M.";
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 $('selAMPM').value = "P.M.";
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

	var countingNumber = true;
	var wholeNumber = false;

	//added by VAN 05-16-08
	function convertword(objValue){
		var word;
		objValue = parseInt(objValue);
		switch(objValue){
			case 1 :  word = "first";
						 break;
			case 2 :  word = "second";
						 break;
			case 3 :  word = "third";
						 break;
			case 4 :  word = "fourth";
						 break;
			case 5 :  word = "fifth";
						 break;
			case 6 :  word = "sixth";
						 break;
			case 7 :  word = "seventh";
						 break;
			case 8 :  word = "eighth";
						 break;
			case 9 :  word = "ninth";
						 break;
			case 10 : word = "tenth";
						 break;
			case 11 :  word = "eleventh";
						 break;
			case 12 :  word = "twelfth";
						 break;
			case 13 :  word = "thirteenth";
						 break;
			case 14 :  word = "fourteenth";
						 break;
			case 15 :  word = "fifteenth";
						 break;
			case 16 :  word = "sixteenth";
						 break;
			case 17 :  word = "seventeenth";
						 break;
			case 18 :  word = "eighteenth";
						 break;
			case 19 :  word = "nineteenth";
						 break;
			case 20 : word = "twentieth";
						 break;
		}
		//alert(objValue);
		//alert(word);
		return word;
	}

	//check if the object value exists in the array
	Array.prototype.in_array = function ( obj ) {
	var len = this.length;
	for ( var x = 0 ; x <= len ; x++ ) {
		if ( this[x] == obj ) return true;
	}
	return false;
	}

	function updateBirthRank(){
		$('birth_rank').value =$('birth_rank_others').value;
	}

	function checkWord(objValue){
		var order=new Array("first", "second", "third", "fourth", "fifth", "sixth", "seventh", "eighth", "ninth","tenth",
												"eleventh","twelfth","thirteenth","fourteenth","fifteenth","sixteenth","seventeenth","eighteenth","nineteenth","twentieth");
		//alert(objValue);
		//alert(order.in_array(objValue));
		return order.in_array(objValue);
	}

	function convertToWords(obj){
		//alert(objvalue);
		var objValue = obj.value;
		var objName = obj.name;
		var word;

		objValue = objValue.toLowerCase();

		if (objValue=="")
			obj.value = '';
		else{
		if (objValue>20){
			alert('Are you sure of the order you have entered?');
			obj.value="";
			obj.focus();
			//$('birth_rank_others').focus();
			return false;
		}else{
			if (isNaN(objValue)){
				valid = checkWord(objValue);
				if (valid){
					obj.value = objValue;
				}else{
					alert('Incorrect input. Please check the spelling.');
					obj.value = "";
				}
			}else{
				word = convertword(objValue);
				obj.value = word;
			}
		}
		}
	}

function EnableBirthRank(){
	var objBirthRank = document.livebirth_certificate.birth_type_tmp;
	var objBirthRankOthers = document.livebirth_certificate.birth_rank_tmp;

	if(objBirthRank[0].checked){
		objBirthRankOthers[0].disabled = true;
		objBirthRankOthers[1].disabled = true;
		objBirthRankOthers[2].disabled = true;
        objBirthRankOthers[3].disabled = true;
		$('birth_rank_others').readOnly = true;
	}else if(objBirthRank[1].checked){
		objBirthRankOthers[0].disabled = false;
		objBirthRankOthers[1].disabled = false;
		objBirthRankOthers[2].disabled = true;
        objBirthRankOthers[3].disabled = true;
		$('birth_rank_others').readOnly = true;
    }else if(objBirthRank[2].checked){
        objBirthRankOthers[0].disabled = false;
        objBirthRankOthers[1].disabled = false;
        objBirthRankOthers[2].disabled = false;
        objBirthRankOthers[3].disabled = true;
        $('birth_rank_others').readOnly = true;
    }else{
		objBirthRankOthers[0].disabled = false;
		objBirthRankOthers[1].disabled = false;
		objBirthRankOthers[2].disabled = false;
        objBirthRankOthers[3].disabled = false;
		$('birth_rank_others').readOnly = false;
	}
}

	//added by VAN 08-05-08

	function update_LateDr_name1(){
		$('late_doctor2').value = $('attendant_name2').value;
	}

	function update_LateDr_name2(){
		$('attendant_name2').value = $('late_doctor2').value;
	}

	function late_dr_status(){
		var drname = '<?=$attendant_name?>';

		if(($('is_late_reg').checked==true)&&($('is_resident').checked==true)){
			$('late_resident').style.display="";
			$('late_non_resident').style.display="none";

			if (isNaN(drname)){
				$('late_doctor').value=0;
			}else{
				$('late_doctor').value=drname;
			}

		}else if(($('is_late_reg').checked==true)&&($('is_resident').checked==false)){
			$('late_resident').style.display="none";
			$('late_non_resident').style.display="";

			if (isNaN(drname)){
				$('late_doctor2').value=drname;
			}else{
				$('late_doctor2').value="";
			}
		}
		//alert('res = '+$('late_resident').style.display);
		//alert('nonres = '+$('late_non_resident').style.display);
	}

	function clearMarriedInfo(){
		//if ($('istribal').checked==true){
			$('parent_marriage_place').value='';
			$('parent_marriage_date').value='';
		//}
	}

	function check_Doctor_status(){
		var drname = '<?=$attendant_name?>';
		//alert(drname);
		if ($('is_resident').checked==true){
			$('resident').style.display="";
			$('non_resident').style.display="none";

			$('nonres_status').style.display="none";

			if (isNaN(drname)){
				$('attendant_name').value=0;
			}else{
				$('attendant_name').value=drname;
			}

			late_dr_status();

		}else{
			$('resident').style.display="none";
			$('non_resident').style.display="";
			$('nonres_status').style.display="";
			//alert(isNaN(drname));
			if (isNaN(drname)){
				$('attendant_name2').value=drname;
			}else{
				$('attendant_name2').value="";
			}
			late_dr_status();
		}
	}
	//--------------

	function preSet(){

		// added by carriane 04/02/18
		if ($('informant_address').value==""){
			UpdateInformantAddress();
		}
		// end carriane

        //added by VAN 08-05-08
		check_Doctor_status();
		//----------------------

		if ($('attendant_title').value=="")
			$('attendant_title').value = 'Physician';

		//$('attendant_address').innerHTML = 'BUKIDNON PROVINCIAL HOSPITAL - MALAYBALAY';
				//$('attendant_address').innerHTML = '<?php echo $row['hosp_name']."-".$row['mun_name'];?>';

		//$('m_citizenship').value = 'FILIPINO';
		//$('m_religion').value = 62;
		//$('m_occupation').value = 5;

		if (document.livebirth_certificate.is_married[1].checked){
			if (($('officer_place_sign').value=="")||($('officer_place_sign').value==" ") || ($('officer_place_sign').value==null))
				$('officer_place_sign').value = "<?=$row['mun_name'];?>";
		}

		if ($('is_late_reg').checked) {
			LateApplicant();

            document.getElementById('late_reg').style.display = '';

			if (($('late_bhosp').value=="")||($('late_bhosp').value==" ") || ($('late_bhosp').value==null))
				$('late_bhosp').value = "<?php echo $row['hosp_name']."-".$row['mun_name'];?>";

			if (($('late_hospital').value=="")||($('late_hospital').value==" ") || ($('late_hospital').value==null))
				$('late_hospital').value = "<?php echo $row['hosp_name']."-".$row['mun_name'];?>";

			if (($('late_officer_place_sign').value=="")||($('late_officer_place_sign').value==" ") || ($('late_officer_place_sign').value==null))
				$('late_officer_place_sign').value = "<?=$row['mun_name'];?>";

			if (($('late_reason').value=="")||($('late_reason').value==" ") || ($('late_reason').value==null))
				$('late_reason').innerHTML = "Overlooked";

			if (($('late_purpose').value=="")||($('late_purpose').value==" ") || ($('late_purpose').value==null))
				$('late_purpose').value = "Registration";

			if (document.livebirth_certificate.late_married_type[0].checked){
				$('late_date_married').value = $('parent_marriage_date').value;
				$('late_date_married').readOnly = "";
				$('late_place_married').value = $('parent_marriage_place').value;
				$('late_place_married').readOnly = "";
				document.getElementById('late_date_married_trigger').style.cursor = "pointer";

				$('late_name_father').value = "";
				$('late_name_father').readOnly = "readonly";
			}else{
				$('late_date_married').value = "";
				$('late_date_married').readOnly = "readonly";
				$('late_place_married').value = "";
				$('late_place_married').readOnly = "readonly";
				document.getElementById('late_date_married_trigger').style.cursor = "default";

				if ($('late_name_father2').value)
					$('late_name_father').value = $('late_name_father2').value;
				else
					$('late_name_father').value = "";

				$('late_name_father').readOnly = "";
			}

			if (document.livebirth_certificate.late_informantType[0].checked){
				//if ($('late_name_father2').value)
					//$('late_husband').value = $('late_name_father2').value;
				//else
					//$('late_husband').value = "";

				$('late_husband').readOnly = "";

				$('late_relationship').value = "";
				//$('late_relationship').readOnly = "readonly";
				$('late_relationship').disabled = true;
			}else{
				$('late_husband').value = "";
				$('late_husband').readOnly = "readonly";

				if ($('late_relationship2').value)
					$('late_relationship').value = $('late_relationship2').value;
				else
					$('late_relationship').value = "";

				//$('late_relationship').readOnly = "";
				$('late_relationship').disabled = false;
			}

			/*
			if ($('late_ismarriedto').checked){
				if ($('late_name_father2').value)
					$('late_husband').value = $('late_name_father2').value;
				else
					$('late_husband').value = "";

				$('late_husband').readOnly = "";
			}else{
				$('late_husband').value = "";
				$('late_husband').readOnly = "readonly";
			}

			if ($('late_forguardian').checked){
				//$('late_relationship').value = "";
				if ($('late_relationship2').value)
					$('late_relationship').value = $('late_relationship2').value;
				else
					$('late_relationship').value = "";

				$('late_relationship').readOnly = "";
			}else{
				$('late_relationship').value = "";
				$('late_relationship').readOnly = "readonly";
			}
			*/

		}else{
			document.getElementById('late_reg').style.display = 'none';
		}

        EnableBirthRank();
	}

	function EnableForGuardian(obj){
		if (obj.checked==true){
			//$('late_relationship').value = "";
			if ($('late_relationship2').value)
				$('late_relationship').value = $('late_relationship2').value;
			else
				$('late_relationship').value = "";
			//$('late_relationship').readOnly = "";
			$('late_relationship').disabled = false;

			//added by VAN 08-05-08
			$('late_husband').value = "";
			$('late_husband').readOnly = "readonly";
		}else{
			$('late_relationship').value = "";
			//$('late_relationship').readOnly = "readonly";
			$('late_relationship').disabled = true;

			//added by VAN 08-05-08
			/*
			if ($('late_name_father2').value)
				$('late_husband').value = $('late_name_father2').value;
			else
				$('late_husband').value = "";
			*/
			$('late_husband').readOnly = "";
		}
	}

	function EnableMarriedTo(obj){
	//alert($('late_name_father2').value);
		if (obj.checked==true){
			/*
			if ($('late_name_father2').value)
				$('late_husband').value = $('late_name_father2').value;
			else
				$('late_husband').value = "";
				*/
			$('late_husband').readOnly = "";

			//added by VAN 08-05-08
			$('late_relationship').value = "";
			//$('late_relationship').readOnly = "readonly";
			$('late_relationship').disabled = true;
		}else{
			$('late_husband').value = "";
			$('late_husband').readOnly = "readonly";

			//added by VAN 08-05-08
			if ($('late_relationship2').value)
				$('late_relationship').value = $('late_relationship2').value;
			else
				$('late_relationship').value = "";
			//$('late_relationship').readOnly = "";
			$('late_relationship').disabled = false;
		}
	}

	function UpdateLateMarriedDate(){
		$('late_date_married').value=$('parent_marriage_date').value
	}

	function UpdateMarriedDate(){
		$('parent_marriage_date').value=$('late_date_married').value
	}

    function LateApplicant(){
        //var typeValue = Form.getInputs('myform','radio','is_subject_person').find(function(radio) { return radio.checked; }).value;
        //alert(typeValue);

        is_subject_person = document.livebirth_certificate.is_subject_person;
        if (is_subject_person[0].checked){
           $('late_birth_place').value = $('late_birth_place3').value;
           $('late_bdate3').value = $('late_bdate').value;
           $('late_bdate3').readOnly = "";
           $('late_baby_name').value = "";
           $('late_bhosp').value = "";
           $('late_bdate2').value = "";
           $('late_bdate2').readOnly = "readonly";
           $('late_bhosp').readOnly = "readonly";
           $('late_birth_place').readOnly = "";
           $('late_baby_name').readOnly = "readonly";

           jQuery(function($){
             $J("#late_bdate3").mask("99/99/9999");
           });

           jQuery(function($){
             $J("#late_bdate2").unmask("99/99/9999");
           });
        }else{
           $('late_birth_place').value = "";
           $('late_bdate3').value = "";
           $('late_bdate3').readOnly = "readonly";
           $('late_baby_name').value = $('late_baby_name3').value;
           $('late_bhosp').value = $('late_birth_place3').value;
           $('late_bdate2').value = $('late_bdate').value;
           $('late_bdate2').readOnly = "";
           $('late_bhosp').readOnly = "";
           $('late_birth_place').readOnly = "readonly";
           $('late_baby_name').readOnly = "";

           jQuery(function($){
             $J("#late_bdate3").unmask("99/99/9999");
           });

           jQuery(function($){
             $J("#late_bdate2").mask("99/99/9999");
           });
        }

    }

	function LateMarriedType(objValue){
		if (objValue==1){
			$('late_date_married').value = $('parent_marriage_date').value;
			$('late_date_married').readOnly = "";
			$('late_place_married').value = $('parent_marriage_place').value;
			$('late_place_married').readOnly = "";
			document.getElementById('late_date_married_trigger').style.cursor = "pointer";

			$('late_name_father').value = "";
			$('late_name_father').readOnly = "readonly";
		}else{
			$('late_date_married').value = "";
			$('late_date_married').readOnly = "readonly";
			$('late_place_married').value = "";
			$('late_place_married').readOnly = "readonly";
			document.getElementById('late_date_married_trigger').style.cursor = "default";

			if ($('late_name_father2').value)
				$('late_name_father').value = $('late_name_father2').value;
			else
				$('late_name_father').value = "";

			$('late_name_father').readOnly = "";
		}
	}

	//added by VAN
	//number only and decimal point is allowed
	function keyPressHandler(e){
		var unicode=e.charCode? e.charCode : e.keyCode
		//if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
		if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
			return false //disable key press
	}

	/*
	todo comment by Rnel
	 */

	//added by VAN 08-01-08
	function updateChildSName(){
		var mother_Lname = $('m_name_last').value;
		var father_Lname = $('f_name_last').value;
		var father_Fname = $('f_name_first').value;

		if ((father_Fname==null) || (father_Fname=="") || (father_Fname==" ") || (father_Fname=="n/a") || (father_Fname=="N/A")){
			father_Lname = "";

		// alert('mother_Lname = '+mother_Lname);
		if ((mother_Lname)&&(father_Lname==""))
			//alert("1");
			$('name_last').value = mother_Lname;
			$('name_middle').value = "";
		}else{
			//alert("2");
			$('name_last').value = father_Lname;
			$('name_middle').value = mother_Lname;
		}

	}
	//-------------------------

	function fatherField(obj) {
		var fobj = obj.value; 
		if(fobj == 'n/a' || fobj == 'N/A') {

			$('f_name_first').value = "n/a";
			$('f_name_middle').value = "n/a";
			$('f_name_last').value = "n/a";

			$('f_citizenship').value = "n/a";
			$('f_religion').value = 105;
			$('f_occupation').value = 359;
			$('f_age').value = "n/a";

			$('f_residence_basic').value = "n/a";
            $('f_residence_brgy').value = 0;
            $('f_residence_brgy').disabled = true;
            $('f_residence_mun').value = 0;
            $('f_residence_mun').disabled = true;
            $('f_residence_prov').value = 0;
            $('f_residence_prov').disabled = true;
            $('f_residence_country').value = 0;
            $('f_residence_country').disabled = true;

            $('f_religion').value == 1;	
            $('f_ethnic').value == 1;
            $('f_occupation').value == 1;
		}
	}

	function checkFather(obj){

		var objValue = obj.value;
		//alert(obj.id);
		//if ((objValue==null) || (objValue=="") || (objValue==" ") || (objValue=="n/a") || (objValue=="N/A")){
		if ((objValue=="n/a") || (objValue=="N/A")){
			/*
			if ($('f_name_first').value)
				$('f_name_first').value = objValue.toLowerCase();
			else
				$('f_name_first').value = "n/a";
			*/
			$('f_name_first').value = "n/a";
			$('f_name_middle').value = "n/a";
			$('f_name_last').value = "n/a";

			$('f_citizenship').value = "n/a";
			$('f_religion').value = 105;
			$('f_occupation').value = 359;
			$('f_age').value = "n/a";

			$('name_middle').value = "";
			$('name_last').value = $('name_middle2').value;

            $('f_residence_basic').value = "n/a";
            $('f_residence_brgy').value = 0;
            $('f_residence_brgy').disabled = true;
            $('f_residence_mun').value = 0;
            $('f_residence_mun').disabled = true;
            $('f_residence_prov').value = 0;
            $('f_residence_prov').disabled = true;
            $('f_residence_country').value = 0;
            $('f_residence_country').disabled = true;

            // added by: syboy 07/09/2015
            $('f_religion').value == 1;	
            $('f_ethnic').value == 1;
            $('f_occupation').value == 1;
            // end
			return false;
		}else{
			if (obj.id=='f_name_first'){
				//$('f_name_middle').value = "";
				// added by: syboy 07/09/2015
				/*$('f_citizenship').value = "";
				$('f_age').value = "";
				$('f_residence_basic').value = "";*/
				// end
				if (($('f_name_middle').value)&&($('f_name_middle').value!='n/a'))
					$('f_name_middle').value = $('f_name_middle').value;
				else
					$('f_name_middle').value = "";

				if (($('f_name_last').value)&&($('f_name_last').value!='n/a'))
					$('f_name_last').value = $('f_name_last').value;
				else
					$('f_name_last').value = "";

			}else if (obj.id=='f_name_middle'){
				//$('f_name_first').value = "";
				// added by: syboy 07/09/2015
				/*$('f_citizenship').value = "";
				$('f_age').value = "";
				$('f_residence_basic').value = "";*/
				// end
				if (($('f_name_first').value)&&($('f_name_first').value!='n/a'))
					$('f_name_first').value = $('f_name_first').value;
				else
					$('f_name_first').value = "";

				if (($('f_name_last').value)&&($('f_name_last').value!='n/a'))
					$('f_name_last').value = $('f_name_last').value;
				else
					$('f_name_last').value = "";
			}else if (obj.id=='f_name_last'){
				//$('f_name_middle').value = "";
				// added by: syboy 07/09/2015
				/*$('f_citizenship').value = "";
				$('f_age').value = "";
				$('f_residence_basic').value = "";*/
				// end
				if (($('f_name_first').value)&&($('f_name_first').value!='n/a'))
					$('f_name_first').value = $('f_name_first').value;
				else
					$('f_name_first').value = "";

				if (($('f_name_middle').value)&&($('f_name_middle').value!='n/a'))
					$('f_name_middle').value = $('f_name_middle').value;
				else
					$('f_name_middle').value = "";
			}

            $('f_residence_brgy').disabled = false;
            $('f_residence_mun').disabled = false;
            $('f_residence_prov').disabled = false;
            $('f_residence_country').disabled = false;

			/*
			$('f_citizenship').value = "FILIPINO";
			$('f_religion').value = 62;
			$('f_occupation').value = 1;
			$('f_age').value = "";
			*/
			return true;
		}
	}

	function UpdateBabyinLate(){
		$('late_baby_name').value = $('name_first').value+" "+$('name_middle').value+" "+$('name_last').value;
	}

	function UpdateFatherinLate(){
		if (document.livebirth_certificate.late_married_type[1].checked){
			//$('late_name_father').value = $('f_name_first').value+" "+$('f_name_middle').value+" "+$('f_name_last').value;
			var is_exists, f_name_first, f_name_first_new, fname1, fname2;

				f_name_first = $F('f_name_first');
				is_exists = f_name_first.search(/,/i);
				if (is_exists>0){
					f_name_first_new = f_name_first.split(",");
					fname1 = f_name_first_new[0];
					fname2 = f_name_first_new[1].replace(" ","");
				}else{
					fname1 = f_name_first;
					fname2 = "";
				}

				if (fname2)
					fname2 = ", "+fname2;

				$('late_name_father').value = fname1+" "+$('f_name_middle').value+" "+$('f_name_last').value+""+fname2;
		}else{
			$('late_name_father').value="";
		}

		//edited by VAN 08-05-08
		//if (document.livebirth_certificate.late_ismarriedto.checked){

		if (document.livebirth_certificate.late_informantType[0].checked){
			//$('late_husband').value = $('f_name_first').value+" "+$('f_name_middle').value+" "+$('f_name_last').value;
			//edited by VAN 08-28-08
				//$('late_husband').value = fname1+" "+$('f_name_middle').value+" "+$('f_name_last').value+""+fname2;
		}else{
			$('late_husband').value="";
		}
	}

	function UpdateLateMarriedPlace(){
		if ($('late_married_type').checked==true){
			$('late_place_married').value = $('parent_marriage_place').value;
		}
	}

	function UpdateMarriedPlace(){
		if ($('is_married').checked==true){
			$('parent_marriage_place').value = $('late_place_married').value;
		}
	}

	function showDrLate(){
		$('late_doctor').value = $('attendant_name').value;
	}

	function showAttendantDr(){
		$('attendant_name').value = $('late_doctor').value;
	}

	function showLateReg(obj){

		if (obj.checked==true){
			document.getElementById('late_reg').style.display = '';

			if (($('late_bhosp').value=="")||($('late_bhosp').value==" ") || ($('late_bhosp').value==null))
				$('late_bhosp').value = "<?php echo $row['hosp_name']."-".$row['mun_name'];?>";

			if (($('late_hospital').value=="")||($('late_hospital').value==" ") || ($('late_hospital').value==null))
				$('late_hospital').value = "<?php echo $row['hosp_name']."-".$row['mun_name'];?>";

			if (($('late_officer_place_sign').value=="")||($('late_officer_place_sign').value==" ") || ($('late_officer_place_sign').value==null))
				$('late_officer_place_sign').value = "<?=$row['mun_name'];?>";

			if (($('late_reason').value=="")||($('late_reason').value==" ") || ($('late_reason').value==null))
				$('late_reason').innerHTML = "Overlooked";

			if (($('late_purpose').value=="")||($('late_purpose').value==" ") || ($('late_purpose').value==null))
				$('late_purpose').value = "Registration";

			if (document.livebirth_certificate.late_married_type[0].checked){

				$('late_date_married').value = "";
				$('late_date_married').readOnly = "";
				$('late_place_married').value = "";
				$('late_place_married').readOnly = "";
				document.getElementById('late_date_married_trigger').style.cursor = "pointer";

				$('late_name_father').value = "";
				$('late_name_father').readOnly = "readonly";
			}else{
				$('late_date_married').value = "";
				$('late_date_married').readOnly = "readonly";
				$('late_place_married').value = "";
				$('late_place_married').readOnly = "readonly";
				document.getElementById('late_date_married_trigger').style.cursor = "default";

				if ($('late_name_father2').value)
					$('late_name_father').value = $('late_name_father2').value;
				else
					$('late_name_father').value = "";

				$('late_name_father').readOnly = "";

			}
			late_dr_status();
			//edited by VAN 08-05-08
			if (document.livebirth_certificate.late_informantType[0].checked){
				/*
				if ($('late_name_father2').value)
					$('late_husband').value.value = $('late_name_father2').value;
				else
					$('late_husband').value.value = "";
					*/
				$('late_husband').readOnly = "";
				$('late_relationship').value = "";
				//$('late_relationship').readOnly = "readonly";
				$('late_relationship').disabled = true;

			}else{
				$('late_husband').value = "";
				$('late_husband').readOnly = "readonly";

				$('late_relationship').value = "";
				//$('late_relationship').readOnly = "";
				$('late_relationship').disabled = false;
			}
			/*
			if ($('late_ismarriedto').checked){
				if ($('late_name_father2').value)
					$('late_husband').value.value = $('late_name_father2').value;
				else
					$('late_husband').value.value = "";

				$('late_husband').readOnly = "";
			}else{
				$('late_husband').value = "";
				$('late_husband').readOnly = "readonly";
			}

			if ($('late_forguardian').checked){
				$('late_relationship').value = "";
				$('late_relationship').readOnly = "";
			}else{
				$('late_relationship').value = "";
				$('late_relationship').readOnly = "readonly";
			}
			*/
		}else	{
			//document.getElementById('late_reg').style.display = 'none';
            $('late_reg').style.display = 'none';
		}
	}

    function updateBdate(){
        var bd = $('birth_day').value;
        var bm = $('birth_month').value;
        var by = $('birth_year').value;

        var dte;

        dte = bd+"/"+bm+"/"+by;
        if ($('is_late_reg').checked) {
            is_subject_person = document.livebirth_certificate.is_subject_person;

            if (is_subject_person[0].checked){
                $('late_bdate3').value = dte;
                $('late_bdate2').value = "";
            }else{
                $('late_bdate3').value = "";
                $('late_bdate2').value = dte;
            }
            $('late_bdate').value = dte;
            //$('late_bdate4').value = dte;
       }
    }

	function UpdateBirthDate(dte){
		//var dte = $('late_bdate').value;

        var arr = dte.split("/");
		//alert($('birth_day').value+" - "+arr[0]);

		$('birth_month').value = arr[0];
		$('birth_day').value = arr[1];
        $('birth_year').value = arr[2];

        if ($('is_late_reg').checked) {
            is_subject_person = document.livebirth_certificate.is_subject_person;

            if (is_subject_person[0].checked){
                $('late_bdate3').value = dte;
                $('late_bdate2').value = "";
            }else{
                $('late_bdate3').value = "";
                $('late_bdate2').value = dte;
            }
            $('late_bdate').value = dte;
            //$('late_bdate4').value = dte;
        }
	}
	//-----------------------

	function chkInteger(obj,noZero){
//	function chkInteger(obj){
		var objValue = obj.value;
		var objName = obj.name;

		if (objValue=="")
			return false;
//alert(objName);
		if (isNaN(parseInt(objValue, 10)) || (parseInt(objValue, 10) < 0) ||((noZero) && (parseInt(objValue, 10)==0))){
			switch (objName){
				case 'birth_order':
						msg=" Invalid birth order! \n A valid number is expected.";
					break;
				case 'birth_rank_tmp':
				case 'birth_rank_others':
						msg=" Invalid birth rank! \n A valid number is expected.";
						$('birth_rank').value='';
					break;
				case 'm_total_alive':
						msg=" Invalid total number of children born alive! \n A valid number is expected.";
					break;
				case 'm_still_living':
						msg=" Invalid number of children still living includin this birth! \n A number is expected.";
					break;
				case 'm_now_dead':
						msg=" Invalid number of children born alive but are now dead! \n A valid number is expected.";
					break;
				case 'm_age':
						msg=" Invalid age of the mother! \n A valid number is expected.";
					break;
				case 'f_age':
						msg=" Invalid age of the father! \n A valid number is expected.";
					break;
				case 'birth_weight':
						msg=" Invalid weight at birth! \n A valid number is expected.";
					break;
			}
			alert(msg);
			obj.value="";
			obj.focus();
			return false;
		}

		obj.value = parseInt(objValue, 10);
		return true;
	}// end of function chkInteger

	function chkDecimal(obj){
		var objValue = obj.value;
		var objName = obj.name;
		var ms='';

		if (objValue=="")
			return false;
		if (!parseFloat(objValue)){
			switch (objName){
				case 'm_age':
						msg=" Invalid age of the mother! \n A valid number is expected.";
					break;
				case 'f_age':
						msg=" Invalid age of the father! \n A valid number is expected.";
					break;
				case 'birth_weight':
						msg=" Invalid wieght at birth! \n A valid number is expected.";
					break;
			}
			alert(msg);
			obj.value="";
			obj.focus();
			return false;
		}
		obj.value = parseFloat(objValue);
		return true;
	}// end of function chkDecimal

	function typeOfBirth(obj){
		var objValue = obj.value;
		var objBirthRank = document.livebirth_certificate.birth_rank_tmp;
		var objBirthRankOthers = document.livebirth_certificate.birth_rank_others;

		//alert("typeOfBirth : objValue = '"+objValue+"'; objBirthRank ='"+objBirthRank+"'; objBirthRank.length ='"+objBirthRank.length+"'");

/*		//added by KENTOOT 07/09/2014
		//commented by art 08/01/2014
		var objBirthOrder;
		if($('birth_type_tmp').checked){
			objBirthOrder = 1;
			$('birth_order').value = '';
			$('birth_order').disabled = true;
		}else{
			objBirthOrder = 0;
			$('birth_order').disabled = false;
		}//end KENTOOT
*/
		if (objValue==1){
			objBirthRank.value = '';
			objBirthRankOthers.value = '';
			for(var i=0; i<objBirthRank.length;i++ ){
				objBirthRank[i].disabled = true;
				objBirthRank[i].checked = false;
			}
			objBirthRankOthers.disabled = true;
            $('birth_type_others').disabled = true;
            $('birth_type_others').value = "";

		//added by VAN 05-16-08
		}else if (objValue==2){
			for(var i=0; i<objBirthRank.length-1;i++ ){
				objBirthRank[i].disabled = false;
			}
			objBirthRank[2].disabled = true;
			objBirthRank[0].checked = true;
			objBirthRankOthers.disabled = true;
            $('birth_type_others').disabled = true;
            $('birth_type_others').value = "";
		//-------------
        }else if (objValue==3){
            for(var i=0; i<objBirthRank.length-1;i++ ){
                objBirthRank[i].disabled = false;
            }
            objBirthRank[3].disabled = true;
            objBirthRank[0].checked = true;
            objBirthRankOthers.disabled = true;
            $('birth_type_others').disabled = true;
            $('birth_type_others').value = "";
        //-------------
        }else{
			for(var i=0; i<objBirthRank.length;i++ ){
				objBirthRank[i].disabled = false;
			}
			objBirthRank[0].checked = true;

//			objBirthRankOthers.disabled = false;
		}
		$('birth_type').value=objValue;
		$('birth_rank').value='';

        $('birth_type_others').disabled = false;

		//alert("typeOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");
	}/* end of function typeOfBirth */

	function rankOfBirth(obj){

		var objValue = obj.value;
		var objBirthRankOthers = $('birth_rank_others');

		//alert("rankOfBirth: objValue = '"+objValue+"'; objBirthRankOthers.value ='"+objBirthRankOthers.value+"'");

		if ((objValue!="first")&&(objValue!="second")&&(objValue!="third")){
			objBirthRankOthers.disabled = false;
			$('birth_rank').value='';
			objBirthRankOthers.readOnly = false;
		}else{
			objBirthRankOthers.disabled = true;
			objBirthRankOthers.value = '';
		}
		$('birth_rank').value=objValue;
		$('birth_rank_others').value='';
		//alert("rankOfBirth : $F('birth_rank') ='"+$F('birth_rank')+"'");


	}/* end of function rankOfBirth */

	function parentName(obj){
		var parentType = obj.name.substring(0,1).toLowerCase();
//		alert("obj.value = '"+obj.name+"'; parentType = '"+ parentType+"'");
		var is_exists, f_name_first, f_name_first_new, fname1, fname2;

		if (parentType=="m"){
			 // mother
//			$('m_fullname').value = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
			if (($F('m_name_first')=="")&&($F('m_name_middle')=="")&&($F('m_name_last')=="")){
				$('m_fullname').innerHTML = "(mother's name)";
				//added by Christian 11-18-19
				$('m_fullname_signature').innerHTML = "(mother's name)";
				$('m_fullname_sworn').innerHTML = "(mother's name)";
			}else{
				$('m_fullname').innerHTML = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
			    //added by Christian 10-31-19 
			    $('m_fullname_signature').innerHTML = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
			    $('m_fullname_sworn').innerHTML = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
			}
			
			if($F('f_name_first')=="n/a" || ($F('f_name_first')=="N/A") || ($F('f_name_first')==""))
				$('m_fullname_sworn').innerHTML = '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
		}else{
			 //father
//			$('f_fullname').value = $F('f_name_first')+" "+$F('f_name_middle')+" "+$F('f_name_last');
			if (($F('f_name_first')=="")&&($F('f_name_middle')=="")&&($F('f_name_last')=="")){
				//$('f_fullname').innerHTML = "(father's name)";
				$('f_fullname').value = "(father's name)";
				//added by Christian 11-18-19
				$('f_fullname_signature').innerHTML = "(father's name)";
				$('f_fullname_sworn').innerHTML = "(father's name)";
			}
			else{
				//edited by VAN 08-28-08
				//$('f_fullname').innerHTML = $F('f_name_first')+" "+$F('f_name_middle')+" "+$F('f_name_last');
				f_name_first = $F('f_name_first');
				is_exists = f_name_first.search(/,/i);
				if (is_exists>0){
					f_name_first_new = f_name_first.split(",");
					fname1 = f_name_first_new[0];
					fname2 = f_name_first_new[1].replace(" ","");
				}else{
					fname1 = f_name_first;
					fname2 = "";
				}

				if (fname2)
					fname2 = ", "+fname2;

				//$('f_fullname').innerHTML = fname1+" "+$F('f_name_middle')+" "+$F('f_name_last')+""+fname2;
				//$('f_fullname').innerHTML = fname1+""+fname2+" "+$F('f_name_middle')+" "+$F('f_name_last');
				//$('f_fullname').value = fname1+""+fname2+" "+$F('f_name_middle')+" "+$F('f_name_last');
				
				//added by Christian 11-04-19
				if($F('f_name_first')=="n/a" || ($F('f_name_first')=="N/A") || ($F('f_name_first')=="")){
					var f_name_none = fname1.toLowerCase();
					$('f_fullname').value = f_name_none;
					$('f_fullname_signature').innerHTML = '';
					$('f_fullname_sworn').innerHTML = '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
					$('late_officer_place_sign').value = "";
					$('m_fullname_sworn').innerHTML = '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
				}else{
					$('f_fullname').value = fname1+""+fname2+" "+$F('f_name_middle')+" "+$F('f_name_last');
					$('f_fullname_signature').innerHTML = fname1+""+fname2+" "+$F('f_name_middle')+" "+$F('f_name_last');
					$('f_fullname_sworn').innerHTML = fname1+""+fname2+" "+$F('f_name_middle')+" "+$F('f_name_last');
					if($F('late_officer_place_sign')=="")
						$('late_officer_place_sign').value = "DAVAO CITY";
					$('m_fullname_sworn').innerHTML = $F('m_name_first')+" "+$F('m_name_middle')+" "+$F('m_name_last');
				}
				//added by Christian 11-04-19 end
			}
		}
	}/* end of function parentName */

	function typeOfMarriageInfo(objValue){

		//alert("typeOfMarriageInfo : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
		if (objValue=="1"){   //Married
			$('f_com_tax_nr').value='';
			$('f_com_tax_date').value='';
			$('f_com_tax_place').value='';
			$('m_com_tax_nr').value='';
			$('m_com_tax_date').value='';
			$('m_com_tax_place').value='';
			$('officer_title').value='';
			$('officer_name').value='';
			$('officer_address').value='';

		}else{   //Not Married
			//$('parent_marriage_info_tmp').value='';
			//$('parent_marriage_info_tmp').value='';
			$('parent_marriage_date').value='';
			$('parent_marriage_place').value='';
		}
		//alert("typeOfMarriageInfo : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
	}/* end of function typeOfMarriageInfo */

	function typeOfMarriage(obj){
		var objValue = obj.value;

		//alert("typeOfMarriage : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
		if (objValue=="1"){   //Married
			blocking("married_parents", true); // display
			blocking("tribal", true); // display
			blocking("married_parents_not", false); // not display
			$('officer_place_sign').value = "<?=$row['mun_name'];?>";
			//$('parent_marriage_info').value='1';

			//added by VAN 05-24-08
			if ($('is_late_reg').checked==true){
				//$('late_ismarriedto').checked = true;
				//edited by VAN 08-05-08
				document.livebirth_certificate.late_informantType[0].checked = true;
				//$('late_husband').value = $('f_name_first').value+" "+$('f_name_middle').value+" "+$('f_name_last').value;
				$('late_husband').readOnly = false;
			}
		}else{   //Not Married
			blocking("married_parents", false); // not display
			blocking("tribal", false); // display
			blocking("married_parents_not", true); // display
			//$('parent_marriage_info').value='2';

			//added by VAN 05-24-08
			if ($('is_late_reg').checked==true){
				//$('late_ismarriedto').checked = false;
				//edited by VAN 08-05-08
				document.livebirth_certificate.late_informantType[0].checked = false;

				$('late_husband').value="";
				$('late_husband').readOnly = true;
			}
		}
		//alert("typeOfMarriage : $F('parent_marriage_info') = '"+$F('parent_marriage_info')+"'");
	}/* end of function typeOfMarriage */

	function typeOfAttendant(obj){
		var objValue = obj.value;
		var objAttendantTypeOthers = $('attendant_type_others');

		//alert("attendant_type : $F('attendant_type') ='"+$F('attendant_type')+"'");
		//alert("typeOfAttendant:  typeOfAttendant : objValue = '"+objValue+"'; objAttendantTypeOthers.value ='"+objAttendantTypeOthers.value+"'");

		if (objValue=="5"){
			objAttendantTypeOthers.disabled = false;
		}else{
			objAttendantTypeOthers.value = "";
			objAttendantTypeOthers.disabled = true;
		}
		switch(objValue){
			case "1":
				objValue = objValue + " - Physician";
				break;
			case "2":
				objValue = objValue + " - Nurse";
				break;
			case "3":
				objValue = objValue + " - Midwife";
				break;
			case "4":
				objValue = objValue + " - Hilot";
				break;
			case "5":
				objValue = objValue + " - "+objAttendantTypeOthers.value;
				break;
		}
		$('attendant_type').value=objValue;

		//alert("attendant_type : $F('attendant_type') ='"+$F('attendant_type')+"'");
	}/* end of function typeOfAttendant */

	//added by VAN 05-26-08
	function printMuslimCert(id){
		if (id==0)
			id="";
	 var wsign;
	 var answer = confirm("With signature? If yes, just click OK otherwise CANCEL.");

		if (answer)
			wsign = 1;
		else
			wsign = 0;
		//alert(wsign);
		///if (window.showModalDialog){  //for IE
			//window.showModalDialog("cert_birth_muslim_pdf.php?id="+id+"&wsign="+wsign,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		//}else{
			window.open("cert_birth_muslim_pdf.php?id="+id+"&wsign="+wsign,"muslimCertificate","modal, width=900,height=700,menubar=no,resizable=yes,scrollbars=no");
		//}
	}
	//--------------------

	function printBirthCert(id){
		if (id==0)
			id="";

		//if (window.showModalDialog){  //for IE
		//	window.showModalDialog("cert_birth_pdf.php?id="+id,"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		//}else{
			window.open("cert_birth_pdf_jasper.php?pid="+id,"birthCertificate","modal, width=900,height=700,menubar=no,resizable=yes,scrollbars=no");
		//}

	}/* end of function printBirthCert */

	//added by KENTOOT 06/25/2014
	function formValidation(){

	    var noError;
	    var fields = jQuery(".required").serializeArray();
	   		jQuery.each(fields, function(i, field){
	         	var id = field.name;
	       	 if(field.value == ""){         	 
	           	 alert('All fields with * are required');
	           	 $(id).focus();
	           	 $(id).style.border = '2px solid #FF77FF';
	             noError = false;
	             return false;

	       	}else{
	            	noError = true;
	        }
	    });
	   
	    return noError;
	}//end formVaildation

	// added by: syboy 08/25/2051
	function validationOccupationM(){
		var occupationM;
		if ($('m_occupation').value == 1 || $('m_occupation').value == 359 || $('m_occupation').value == 7) {
    		if (confirm('Occupation of mother is Not Indicated/Applicable/None. \n Do you want to proceed anyway?')) {
    			occupationM = true;
    			return true;
    		} else {
    			alert('Please enter the occupation of the mother.');
    			$('m_occupation').style.border = '2px solid #FF77FF';
	        	$('m_occupation').focus();
	        	occupationM = false;
	        	return false;
    		}
        }else{
        	occupationM = true;
    		return true;
        }
        return occupationM;
	}
	function validationOccupationF(){
		var occupationF;
		if ($('f_occupation').value == 1 || $('f_occupation').value == 359 || $('f_occupation').value == 7) {
    		if (confirm('Occupation of father is Not Indicated/Applicable/None. \n Do you want to proceed anyway?')) {
    			occupationF = true;
    			return true;
    		} else {
    			alert('Please enter the occupation of the fathers.');
    			$('f_occupation').style.border = '2px solid #FF77FF';
	        	$('f_occupation').focus();
	        	occupationF = false;
	        	return false;
    		}
        }else{
        	occupationF = true;
    		return true;
        }
        return occupationF;
	}
	function validationIsMarried(){
		var IsMarried;									
		if ($F('is_married') == null && $('f_fullname').value != "n/a n/a n/a" || $F('is_married') == null && $('f_fullname').value != "n/a") { //Updated by Christian 11-18-2019
			if ($('f_fullname').value=="" || $('f_fullname').value=="(father's name)") {
	        		alert('Pleas enter the fathers fullname.');
	        		$('f_name_first').focus();
	        		IsMarried = false;
	        		return false;
	    	}else{
	    		if ($('f_fullname').value == "n/a n/a n/a" || $('f_fullname').value == "n/a") { //Updated by Christian 11-18-2019
	    			IsMarried = true;
	    			return true;
	    		}else{
	    			if ($('f_com_tax_nr').value == "" || $('f_com_tax_nr').value == null) {
	        			alert('Please enter Community Tax Certificate Number.');
	        			$('f_com_tax_nr').focus();
	        			$('f_com_tax_nr').style.border = '2px solid #FF77FF';
	        			$('late_officer_place_sign').style.border = '2px solid #FF77FF';
						$('f_com_tax_date').style.border = '2px solid #FF77FF';
						IsMarried = false;
	        			return false;
	        		}else if ($('late_officer_place_sign').value == "") {
	        			alert('Please fill out this field.');
	        			$('late_officer_place_sign').focus();
	        			$('late_officer_place_sign').style.border = '2px solid #FF77FF';
	        			IsMarried = false;
	        			return false;
	        		}else if ($('f_com_tax_date').value == "") {
	        			alert('Please fill out this field.');
	        			$('f_com_tax_date').focus();
	        			$('f_com_tax_date').style.border = '2px solid #FF77FF';
	        			IsMarried = false;
	        			return false;
	        		}else{
	        			IsMarried = true;
	    				return true;
	        		}
	    		}
	    		
	    	}
		}else{
			IsMarried = true;
	    	return true;
		}
		
    	return IsMarried; 
	}
	// end

	//updated by KENTOOT 07/20/2014
	function chckForm(d){
		var msg='';
		//alert("d = '"+d+"'");
		var fixed;
		msg= " $F('birth_rank') ='"+$F('birth_rank')+"'; \n $F('birth_rank_others') ='"+$F('birth_rank_others')+"'";
		// alert($('f_ethnic').value);

		// added by: syboy 06/21/2015
		if ($('m_religion').value == 16) {
        	if ($('m_ethnic').value == 1) {
        		alert('Please enter the Ethnic Origin of the mother.');
        		$('m_ethnic').focus();
        		return false;
        	}
        }
        if($('f_religion').value == 16){
        	if ($('f_ethnic').value == 1) {
        		alert('Please enter the Ethnic Origin of the father.');
        		$('f_ethnic').focus();
        		return false;
        	} 
        }
		// end
		/*
		msg = " $F('parent_marriage_info') ='"+$F('parent_marriage_info')+
				"'; \n $F('parent_marriage_info').substring(0,1) ='"+$F('parent_marriage_info').substring(0,1)+
				"'; \n $F('parent_marriage_info_tmp') ='"+$F('parent_marriage_info_tmp')+"'";
		//alert(msg);
		msg = "$F('attendant_type') ='"+$F('attendant_type')+
				"'; \n $F('attendant_type').substring(0,1) ='"+$F('attendant_type').substring(0,1)+
				"'; \n $F('attendant_type_others') ='"+$F('attendant_type_others')+"'";
		//alert(msg);
		*/
		//var p_marriage_info=$F('parent_marriage_info');
		//var p_marriage_info=$F('is_married');
		var a_type = $F('attendant_type');

		//if (	($F('birth_type')!="1") && ($F('birth_rank')!="1") &&
		//		($F('birth_rank')!="2") && ($F('birth_rank_others')=="") ){
		//alert(document.death_certificate.birth_rank_tmp[2].checked);

		//modified by KENTOOT

        if(!(document.livebirth_certificate.birth_type_tmp[0].checked)&&(document.livebirth_certificate.birth_rank_tmp[3].checked)&&($F('birth_rank_others')=="")){
			alert("Please specify the type of birth.");
			$('birth_rank_others').focus();
			return false;
		//}else	if ((p_marriage_info.substring(0,1)=="1") && ($F('parent_marriage_info_tmp')=="")){
		}else if (($F('is_married')==1) && ($('istribal').checked==false) && (($F('parent_marriage_date')=="")||($F('parent_marriage_place')==""))){
			alert("Please enter the date and place of marriage.");
			$('parent_marriage_date').style.border = '2px solid #FF77FF';
			$('parent_marriage_place').style.border = '2px solid #FF77FF';
			// $('parent_marriage_info_tmp').focus();
			$('parent_marriage_date').focus();
			return false;
		}else if ((document.livebirth_certificate.birth_type_tmp[0].checked)&&($F('birth_order')=="")){
			alert("Please enter the birth order.");
			$('birth_order').focus();
			return false;
		}else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
			alert("Please enter the type of attendant.");
			$('attendant_type_others').focus();
			return false;
		}else if (d.birth_time.value==""){
			alert("Please enter the time of birth.");
			$('birth_time').focus();
			return false;
		}else if ($('attendant_name').value==0) {
			alert("Please select a doctor.");
			$('attendant_name').style.border = '2px solid #FF77FF';
			$('attendant_name').focus();
			return false;
		}else if($('attendant_name').value==0) {
			alert("Please select a doctor.");
			$('attendant_name').style.border = '2px solid #FF77FF';
			$('attendant_name').focus();
			return false;
		}else if (informant_relation.value==""){
			alert("Please specify the informant relation to the child.");
			$('informant_relation').style.border = '2px solid #FF77FF';
			$('informant_relation').focus();
			return false;
		}else if($F('is_married')==1){
			if ($('f_residence_basic').value == null) {
				alert('Pleas enter the fathers residence.');
	    		$('f_residence_basic').focus();
	    		$('f_residence_basic').style.border = '2px solid #FF77FF';
	    		return false;
			}
		}
        
		// added by: syboy 08/17/2015; for accepting apostrophe
		// var withapostf = $F('f_residence_basic');
		// var withapostm = $F('m_residence_basic');
		// if (withapostm) {
		// 	document.getElementById(withapostf).style.display = "block";
		// 	return true;
		// }else{
		// 	return false;
		// }

/*		else if (registry_nr.value=="") {
			alert("Please specify the field Registry No.");
			$('registry_nr').focus();
			return false;
		}commented by art 08/01/2014*/
		//end KENTOOT

		// else if (attendant_name.value=="") {
		// 	alert("Please specify the field DATE AND PLACE OF MARRIAGE .");
		// 	$('attendant_name').focus();
		// 	return false;

		// }else if (name_first.value=="") {
		// 	alert("Please specify the field name first.");
		// 	$('name_first').focus();
		// 	return false;

		// }else if (name_middle.value=="") {
		// 	alert("Please specify the field middle name.");
		// 	$('name_middle').focus();
		// 	return false;

		// }else if (name_last.value=="") {
		// 	alert("Please specify the field last name.");
		// 	$('name_last').focus();
		// 	return false;

		// }else if (name_last.value=="") {
		// 	alert("Please specify the field last name.");
		// 	$('name_last').focus();
		// 	return false;

		// }else if (birth_place_basic.value=="") {
		// 	alert("Please specify the field birth of place.");
		// 	$('birth_place_basic').focus();
		// 	return false;

		// }else if (birth_order.value=="") {
		// 	alert("Please specify the field birth order.");
		// 	$('birth_order').focus();
		// 	return false;

		// }else if (birth_weight.value=="") {
		// 	alert("Please specify the field birth weight.");
		// 	$('name_last').focus();
		// 	return false;

		// // }else if (birth_rank_others.value=="") {
		// // 	alert("empty field multiple birth.");
		// // 	$('birth_rank_others').focus();
		// // 	return false;

		// }else if (m_name_first.value=="") {
		// 	alert("Please specify the field mother name first.");
		// 	$('m_name_first').focus();
		// 	return false;

		// }else if (m_name_middle.value=="") {
		// 	alert("Please specify the field mother name middle.");
		// 	$('m_name_middle').focus();
		// 	return false;

		// }else if (m_name_last.value=="") {
		// 	alert("Please specify the field mother name last.");
		// 	$('m_name_last').focus();
		// 	return false;

		// }else if (m_citizenship.value=="") {
		// 	alert("Please specify the field mother citizenship.");
		// 	$('m_citizenship').focus();
		// 	return false;

		// }else if (m_total_alive.value=="") {
		// 	alert("Please specify the field Total number of children born alive.");
		// 	$('m_total_alive').focus();
		// 	return false;

		// }else if (m_still_living.value=="") {
		// 	alert("Please specify the field No. of children still living including this birth .");
		// 	$('m_still_living').focus();
		// 	return false;

		// }else if (m_now_dead.value=="") {
		// 	alert("Please specify the field No. of children born alive but now are dead.");
		// 	$('m_now_dead').focus();
		// 	return false;

		// }else if (m_fetal_death.value=="") {
		// 	alert("Please specify the field Total No. of Fetal Deaths / Abortion.");
		// 	$('m_fetal_death').focus();
		// 	return false;

		// }else if (m_age.value=="") {
		// 	alert("Please specify the field Age at the time of this birth .");
		// 	$('m_age').focus();
		// 	return false;

		// }else if (m_residence_basic.value=="") {
		// 	alert("Please specify the field Street/Barangay .");
		// 	$('m_residence_basic').focus();
		// 	return false;

		// // }else if (f_name_first.value=="") {
		// // 	alert("Please specify the field father name first.");
		// // 	$('f_name_first').focus();
		// // 	return false;

		// // }else if (f_name_middle.value=="") {
		// // 	alert("Please specify the field father name middle.");
		// // 	$('f_name_middle').focus();
		// // 	return false;

		// // }else if (f_name_last.value=="") {
		// // 	alert("Please specify the field father name last.");
		// // 	$('f_name_last').focus();
		// // 	return false;

		// // }else if (f_citizenship.value=="") {
		// // 	alert("Please specify the field mother name last.");
		// // 	$('birth_weight').focus();
		// // 	return false;

		// // }else if (f_age.value=="") {
		// // 	alert("Please specify the field father age.");
		// // 	$('f_age').focus();
		// // 	return false;

		// // }else if (f_residence_basic.value=="") {
		// // 	alert("Please specify the field Street/Barangay .");
		// // 	$('f_residence_basic').focus();
		// // 	return false;

		// }else if (parent_marriage_date.value=="") {
		// 	alert("Please specify the field DATE AND PLACE OF MARRIAGE .");
		// 	$('parent_marriage_date').focus();
		// 	return false;

		// }else if (attendant_address.value=="") {
		// 	alert("Please specify the field birth weight.");
		// 	$('attendant_address').focus();
		// 	return false;

		// }else if (attendant_title.value=="") {
		// 	alert("Please specify the field attendant title.");
		// 	$('attendant_title').focus();
		// 	return false;

		// }else if (birth_time.value=="") {
		// 	alert("Please specify the field birth time.");
		// 	$('birth_time').focus();
		// 	return false;

		// }else if (attendant_date_sign.value=="") {
		// 	alert("Please specify the field date sign.");
		// 	$('attendant_date_sign').focus();
		// 	return false;

		// // }else if (informant_name.value=="") {
		// // 	alert("Please specify the field informant name.");
		// // 	$('informant_name').focus();
		// // 	return false;

		// // }else if (informant_address.value=="") {
		// // 	alert("Please specify the field informant address.");
		// // 	$('informant_address').focus();
		// // 	return false;

		// // }else if (informant_date_sign.value=="") {
		// // 	alert("Please specify the field informant date.");
		// // 	$('informant_date_sign').focus();
		// // 	return false;

		// }else if (encoder_title.value=="") {
		// 	alert("Please specify the field Title or Position.");
		// 	$('encoder_title').focus();
		// 	return false;

		// }else if (encoder_date_sign.value=="") {
		// 	alert("Please specify the field Prepared Date .");
		// 	$('encoder_date_sign').focus();
		// 	return false;

		// }else if (receiver_name.value=="") {
		// 	alert("Please specify the field Name in Print.");
		// 	$('receiver_name').focus();
		// 	return false;

		// }else if (receiver_title.value=="") {
		// 	alert("Please specify the field Title or Position  .");
		// 	$('receiver_title').focus();
		// 	return false;

		// }else if (receiver_date_sign.value=="") {
		// 	alert("Please specify the field Receiver Date.");
		// 	$('receiver_date_sign').focus();
		// 	return false;

		// }else if (registry_nr.value=="") {
		// 	alert("Please specify the field Registry No.");
		// 	$('registry_nr').focus();
		// 	return false;



		// //}else	if ((p_marriage_info.substring(0,1)=="1") && ($F('parent_marriage_info_tmp')=="")){
		// }else	if (($F('is_married')==1) && ($('istribal').checked==false) && (($F('parent_marriage_date')=="")||($F('parent_marriage_place')==""))){
		// 	alert("Please enter the date and place of marriage.");
		// 	//$('parent_marriage_info_tmp').focus();
		// 	$('parent_marriage_date').focus();
		// 	return false;
		// }else if ((a_type.substring(0,1)=="5") && ($F('attendant_type_others')=="")){
		// 	alert("Please enter the type of attendant.");
		// 	$('attendant_type_others').focus();
		// 	return false;
		// }else if (d.birth_time.value==""){
		// 	alert("Please enter the time of birth.");
		// 	$('birth_time').focus();
		// 	return false;
		// }
		//formValidation()
		//msg = "2 : $F('parent_marriage_info') ='"+$F('parent_marriage_info')+"'";
		if ($('f_fullname').value == "n/a n/a n/a" || $('f_fullname').value == "n/a"  || $J('#is_married').val() != 1) { //Updated by Christian 11-18-2019
			if (validationOccupationM() && formValidation()) {
				fixed = true;
				return true;
			}else{
				fixed = false;
				return false;
			}
		}else if($F('is_married') == null && $('f_fullname').value != "n/a n/a n/a" || $('f_fullname').value != "n/a"){ //Updated by Christian 11-18-2019
			if (validationOccupationM() && validationOccupationF() && validationIsMarried() && formValidation()) {
				fixed = true;
				return true;
			}else{
				fixed = false;
				return false;
			}
		}else{
			if (validationOccupationM() && validationOccupationF() && formValidation()) {
				fixed = true;
				return true;
			}else{
				fixed = false;
				return false;
			}
		}

		msg = "2 : $F('attendant_type') ='"+$F('attendant_type')+"'";
		//alert(msg);
		return fixed;
	}/* end of function chckForm */


	//added by VAN 09-16-08
	function UpdateFatherSurname(){
		//if (document.livebirth_certificate.is_married[0].checked){
		if ((document.getElementById('f_name_last').value!='n/a')&&(document.getElementById('f_name_last').value!='N/A')){
			document.getElementById('f_name_last').value = document.getElementById('name_last').value;
		}else{
			document.getElementById('m_name_last').value = document.getElementById('name_last').value;
		}

	}

	function UpdateMotherSurname(){
		//if (document.livebirth_certificate.is_married[0].checked){
		//if ((document.getElementById('f_name_last').value!='n/a')||(document.getElementById('f_name_last').value!='N/A')){
		if ((document.getElementById('f_name_last').value!='n/a')&&(document.getElementById('f_name_last').value!='N/A')){
			document.getElementById('m_name_last').value = document.getElementById('name_middle').value;
		}

	}

	function stristr( haystack, needle, bool ) {
		// http://kevin.vanzonneveld.net
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// *     example 1: stristr('Kevin van Zonneveld', 'Van');
		// *     returns 1: 'van Zonneveld'
		// *     example 2: stristr('Kevin van Zonneveld', 'VAN', true);
		// *     returns 2: 'Kevin '

		var pos = 0;

		pos = haystack.toLowerCase().indexOf( needle.toLowerCase() );
		if( pos == -1 ){
				return false;
		} else{
				if( bool ){
						return haystack.substr( 0, pos );
				} else{
						return haystack.slice( pos );
				}
		}
}

	function ucwords( str ) {
		// http://kevin.vanzonneveld.net
		// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
		// +   improved by: _argos
		// *     example 1: ucwords('kevin van zonneveld');
		// *     returns 1: 'Kevin Van Zonneveld'
		// *     example 2: ucwords('HELLO WORLD');
		// *     returns 2: 'HELLO WORLD'
		return str.replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase ( ); } );
	}

	function capitalizeAfterDash (string) {
	    return string.replace( /-([a-z])/ig, function( all, letter ) {
	        return '-'+letter.toUpperCase();
	    });
	}

	function capitalizeAfterBracket(string){
		if(string.indexOf("(") !== -1){
			return string.replace(/\(([^]+)\)/ig, function( all, letter ) {
		        var temp = letter.substring(0,1).toUpperCase();
		        return '('+temp+letter.substring(1)+')';
		    });
		}else{
			return string.replace(/\[([^]+)\]/ig, function( all, letter ) {
		        var temp = letter.substring(0,1).toUpperCase();
		        return '['+temp+letter.substring(1)+']';
		    });
		}
	}

	function convertAddress(reNum,reRomnum,address,compAddress){
		var matches = address.toUpperCase().match(reRomnum);
		var count = 0;
		var comma = '';

	    if(compAddress != '')
	        comma = ', ';

		
		if(matches){
			var split_data = address.toUpperCase().split(" ");

			if(split_data.indexOf(matches[0])){
				temp = ucwords(capitalizeAfterBracket(address));
				myString = temp.substring(0, matches.index) + matches[0] + temp.substring((matches.index + matches[0].length), temp.length);
				compAddress = compAddress+comma+myString;
				count = 1;
			}
		}else{
			if(reNum.test(address) == true){
				compAddress = compAddress+comma+ucwords(capitalizeAfterDash(capitalizeAfterBracket(address)));
			}else compAddress = compAddress+comma+ucwords(capitalizeAfterBracket(address));
		}

		return compAddress;
	}

	function UpdateInformantAddress(){
		var informant_address;
		var barObj = $('m_residence_brgy');
		var munObj = $('m_residence_mun');
		var provObj = $('m_residence_prov');
		var hasNumber = /\d/;
		var hasRomanNumeral = /\b(?:X?L?(?:X{0,3}(?:IX|IV|V|V?I{1,3})|IX|X{1,3})|XL|L)\b/;
		var brgy = barObj.options[barObj.selectedIndex].text.toLowerCase();
		var municity = munObj.options[munObj.selectedIndex].text.toLowerCase();
		var province = provObj.options[provObj.selectedIndex].text.toLowerCase();

		informant_address  = document.getElementById('m_residence_basic').value;	

		// console.log(matches);
		if(informant_address.replace(/\s/g, '').length != 0){
			if(barObj.options[barObj.selectedIndex].value == 0)
				brgy ='';	

			if(brgy != ''){
				tempAddress = convertAddress(hasNumber,hasRomanNumeral,brgy,informant_address);
				informant_address = tempAddress;
			}
		}else{
			tempAddress = convertAddress(hasNumber,hasRomanNumeral,brgy,informant_address);
			informant_address = tempAddress;
		}
		
		if (informant_address!=''){
			tempAddress = convertAddress(hasNumber,hasRomanNumeral,municity,informant_address);
			informant_address = tempAddress;
		}else{
			tempAddress = convertAddress(hasNumber,hasRomanNumeral,municity,informant_address);
			informant_address = tempAddress;
		}

		if (stristr(munObj.options[munObj.selectedIndex].text, 'City',true)){

		}else{
			if (informant_address!=''){
				if(hasNumber.test(province) == true)
					informant_address = informant_address+", "+ucwords(capitalizeAfterDash(province));
				else informant_address = informant_address+", "+ucwords(province);
			}else{
				if(hasNumber.test(province) == true)
					informant_address = informant_address+" "+ucwords(capitalizeAfterDash(province));
				else informant_address = informant_address+" "+ucwords(province);
			}
		}
		

		document.getElementById('informant_address').value=informant_address;
	}
	//-----------------------

    //added by VAN 05-16-2013
    function UpdatePosition(personell_name, document){
         encoder_title = '<?=$encoder_title?>';
         var title;

         $J.ajax({
              url: "ajax/ajax_getPersonnelPosition.php?personell_name="+personell_name+"&document="+document,
              type: "GET",
              success: function(data){
                  var obj = JSON.parse(data);
                  //alert(obj.personell_name);
                   if (obj.signatory_position=='')
                        title = encoder_title;
                   else
                        title = obj.signatory_position;

                   $J('#encoder_title').val(title);
              }
         });
    }


    /**
     * display baby's name for viewing
     * author arnel 06-24-16
     *
     */
    
    // function displayInfantname() {
    // 	var midname = document.getElementById('name_middle').value;
    // 	var firstname = document.getElementById('name_first').value;
    // 	var lastname = document.getElementById('name_last').value;
    // 	var infantFullname = firstname+' '+ucwords(midname)+' '+lastname;
    // 	// console.log(infantFullname);
    // 	document.getElementById('bname').innerHTML = infantFullname;
    // }

    /**
     * display father's name for viewing
     * author arnel 06-24-16
     * test test
     */
    /*function diplayFatherName() {
    	var midname = document.getElementById('f_name_middle').value;
    	var firstname = document.getElementById('f_name_first').value;
    	var lastname = document.getElementById('f_name_last').value;
    	var fatherFullname = ucwords(firstname)+' '+ucwords(midname)+' '+ucwords(lastname);
    	// console.log(fatherFullname);
    	document.getElementById('f_fullname2').innerHTML = fatherFullname;
    	document.getElementById('f_fullname3').innerHTML = fatherFullname;
    }*/


</script>
</head>

<body onload="preSet();disableAdd();">
<?php echo $errorMsg; ?>
<form id="livebirth_certificate" name="livebirth_certificate" method="post" action="" onsubmit="return chckForm(this);">
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td align="left" bgcolor="#F9FFFB">

				<span class="style2">Republic of the Philippines</span><BR />
				<span class="style6">OFFICE OF THE CIVIL REGISTRAR GENERAL</span><BR />
				<span class="style15">CERTIFICATE OF LIVE BIRTH</span>    </td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF">
			<table width="100%" height="44" border="0" cellpadding="0" cellspacing="2">
				<tr>
					<td width="72%" height="19" bgcolor="#F9FFFB">
							<span class="style2">Province : &nbsp;&nbsp;</span>
								<span class="style6">
								<input type="hidden" name="province" id="province" value="<?=$row['prov_name'];?>">
					 <b><?=$row['prov_name'];?></b></span> </td>
					<td width="28%" rowspan="2" align="right" bgcolor="#F9FFFB">
						<span class="style15">Registry no.</span><span class="style2"> &nbsp;&nbsp;</span>
						<input type="text" name="registry_nr" id="registry_nr" value="<?= $registry_nr ?>" onblur="trimString(this)">
					</td>
				</tr>
				<tr>
					<td height="19" bgcolor="#F9FFFB">
							<span class="style2">City/Municipality : &nbsp;&nbsp;</span>

							<span class="style6">
							<input type="hidden" name="muncity" id="muncity" value="<?=$row['mun_name'];?>">
					 <b><?=$row['mun_name'];?></b></span> </td>
				</tr>
			</table>
		<br />
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="8%" class="style15">Go to</td>
				<td width="16%"><a href="#mother">Mother Profile</a></td>
				<td width="51%"><a href="#father">Father Profile</a></td>
				<td width="25%">&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
				 <div align="center">
	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td  bgcolor="#A0C2DA">
				<center>
	<?php
			if (!$birthCertInfo || empty($birthCertInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save" onClick="insertEmptyAudit();">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" id="print_1" value="Print" onClick="printBirthCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
				#added by VAN 05-26-08
				echo '			<input type="button" name="muslim_print" id="muslim_print" value="For Muslim Only" onClick="printMuslimCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";

				echo '			<input type="submit" name="Submit" id="update_1" value="Update" onClick="insertEmptyAudit();">'."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
	&nbsp; &nbsp;
	<input type="button" name="Cancel" value="Cancel"  onclick="window.close()" id="cancel_1" />
			 </center>
		 </td>
		</tr>
	</table>
</div>

		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td width="3%" bgcolor="#98D228" ><img src="images/brdr_03.gif" width="31" height="26" /></td>
					<td width="94%" bgcolor="#98D228"><span class="style5">CHILD</span></td>
					<td width="3%" bgcolor="#98D228">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" bgcolor="#98D228">
						<table width="100%" border="0" cellspacing="1" cellpadding="1">
							<tr>
								<td height="468" align="center" bgcolor="#F9FFFB"> <br />
										<table width="90%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="20%" align="center" valign="middle" bgcolor="#CFEC9B" class="style9">Name</td>
															<td width="30%" align="center" valign="top" bgcolor="#F9FFFB"><span class="style2"><b>Given Name</b></span><br />
																	<!--
											 <span class="style2"><b>
																	<?= ucwords(strtolower($name_first)) ?>
																	</b> </span>
											 -->
																	<input type="text" name="name_first" id="name_first" class="required" size="30" value="<?= ucwords(strtolower(stripslashes($name_first))) ?>" onblur="UpdateBabyinLate();" required />
																	<input type="hidden" name="name_first_data" id="name_first_data" size="30" value="<?= ucwords(strtolower($name_first)) ?>"
			
															</td>

															<td width="30%" align="center" valign="top" bgcolor="#F9FFFB"> 
																<span class="style2">Middle Name</span>
																<br />
																	<!--
											 <span class="style2"> <b>
																	<?= ucwords(strtolower($name_middle)) ?>
																	</b> </span>
											 -->
																	<input 
																		type="text" 
																		name="name_middle" 
																		id="name_middle" 
																		size="30" 
																		value="<?= ucwords(strtolower(stripslashes($name_middle))) ?>" 
																		onblur="UpdateBabyinLate(); displayInfantname()" 
																	/>
																	<input type="hidden" name="name_middle_data" id="name_middle_data" size="30" value="<?= ucwords(strtolower($name_middle)) ?>" />


											 						<input 
											 							type="hidden" 
											 							name="name_middle2" 
											 							id="name_middle2" 
											 							value="<?= ucwords(strtolower(stripslashes($name_middle))) ?>" 
										 							/>

															</td>

															<td 
																width="30%" 
																align="center" 
																valign="top" 
																bgcolor="#F9FFFB"
															> 
																<span class="style2">Last Name</span>

																<br />
																 <!--
											 <span class="style2"> <b>
																	<?= ucwords(strtolower($name_last)) ?>
																	</b> </span>
											 -->
																	<input 
																		type="text" 
																		name="name_last" 
																		id="name_last" 
																		size="30" 
																		class="required" 
																		value="<?= ucwords(strtolower(stripslashes($name_last))) ?>" 
																		onblur="UpdateBabyinLate();" 
																		required 
																	/>
																	<input type="hidden" name="name_last_data" id="name_last_data" size="30" value="<?= ucwords(strtolower($name_last)) ?>" />


											 						<input 
											 							type="hidden" 
											 							name="name_last2" 
											 							id="name_last2" 
											 							value="<?= ucwords(strtolower(stripslashes($name_last))) ?>" 
										 							/>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>

										<table width="90%" border="0" cellpadding="0" cellspacing="1">
											<tr>
												<td width="25%" height="74" valign="top" bgcolor="#F9FFFB">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td valign="top" bgcolor="#F9FFFB">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td height="74" valign="top" bgcolor="#98D228">
																			<table width="100%" border="0" cellpadding="4" cellspacing="1">
																				<tr>
																					<td colspan="2" bgcolor="#CFEC9B" class="style9" style="color:#FF0000;">*SEX</td>
																				</tr>
																				<tr>
																					<td width="50%" align="center" bgcolor="#F9FFFB" class="style2">
																						<center>
																							<input type="hidden" name="sex" id="sex" value="<?= $sex ?>" />
																							<!-- //added by Mats 07192016 -->
																							 <input type="hidden" name="sex_data" id="sex_data" value="<?= $sex ?>" />
																							 <!-- ended -->
																							<br />
																							<!--<input name="radiobutton" type="radio" value="radiobutton" disabled="disabled" <?php if ($sex=='m') echo "checked"; ?> />-->
															 <input name="sex" id="sex" type="radio" value="m" <?php if ($sex=='m') echo "checked"; ?> />
																							Male
																						</center>
																					</td>
																					<td width="50%" align="center" bgcolor="#F9FFFB" class="style2">
																						<center>
																							<br />
																							<!--<input name="radiobutton" type="radio" value="radiobutton" disabled="disabled" <?php if ($sex=='f') echo "checked"; ?> />-->
															 <input name="sex" id="sex" type="radio" value="f" <?php if ($sex=='f') echo "checked"; ?> />
																							Female
																						</center>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
												<td width="75%" valign="top" bgcolor="#F9FFFB">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td align="right" bgcolor="#F9FFFB">
																<table width="95%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td height="57" valign="top" bgcolor="#98D228">
																			<table width="100%" border="0" cellpadding="4" cellspacing="1">
																				<tr>
																					<td colspan="3" bgcolor="#CFEC9B" class="style9" style="color:#FF0000">*DATE OF BIRTH</td>
																				</tr>
																				<tr>
																					<td width="25%" align="center" bgcolor="#F9FFFB" class="style2">
																						<center>
																							Day <br />
																							<!--
															 <b>
																								<?= $birthDay ?>
																							</b>
																							<input type="hidden" name="birth_day" id="birth_day" value="<?= $birthDay ?>" />
																							 -->
															<?php
																#echo "date = ".$date_birth;
																#if ($date_birth!="0000-00-00")
																$birthDay = date("d",strtotime($date_birth));

																$dategen->genDay('updateBdate()','birth_day', $birthDay);
															?>
															</center>
																					</td>
																					<td width="37%" bgcolor="#F9FFFB" class="style2">
																						<center>
																							Month <br />
															 <!--
																							<b>
																								<?php
	$arrayMonth = array ("","January","February","March","April","May","June","July","August","September","October","November","December");
	echo $arrayMonth[$birthMonth];
							?>
																							</b>
																							<input type="hidden" name="birth_month" id="birth_month" value="<?= $birthMonth ?>" />
																						-->
															<?php
																	#if ($date_birth!="0000-00-00")
																$birthMonth = date("m",strtotime($date_birth));

																	$dategen->genMonth('updateBdate()','birth_month','long', $birthMonth);
															?>
															</center>
																					</td>
																					<td width="38%" bgcolor="#F9FFFB" class="style2">
																						<center>
																							Year <br />
															 <!--
																							<b>
																								<?= $birthYear ?>
																							</b>
																							<input type="hidden" name="birth_year" id="birth_year" value="<?= $birthYear ?>" />
																								-->
																<?php
																	#echo "year - ".$birthYear;
																	#if ($date_birth!="0000-00-00")
																	$birthYear = date("Y",strtotime($date_birth));

																	$dategen->genYear('updateBdate()','birth_year','150',$birthYear);
																?>
															</center>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>

										<table width="90%" border="0" cellspacing="1" cellpadding="0">
											<tr>
												<td bgcolor="#98D228">
													<table width="100%" border="0" cellpadding="4" cellspacing="1">
														<tr>
															<td colspan="3" bgcolor="#CFEC9B" class="style6" style="color:#FF0000">*PLACE OF BIRTH</td>
														</tr>
														<tr align="center" valign="top">
															<td width="40%" height="71" bgcolor="#F9FFFB"> <span class="style2">Name of Hospital/Clinic/Institution/ <br />
																House No.,Street,Barangay </span><br />
																<span class="style6"><!--<b>DAVAO MEDICAL CENTER</b>-->
												<?php
														if (empty($birth_place_basic))
															#$birth_place_basic = $row['hosp_name']."-".$row['mun_name'];
															$birth_place_basic = $row['hosp_name'];

														if (empty($birth_place_mun))
															$birth_place_mun = $row['mun_name'];

														if (empty($birth_place_prov))
															$birth_place_prov = $row['prov_name'];
											?>
																	<input type="text" name="birth_place_basic" id="birth_place_basic" size="50" class="required" value="<?=$birth_place_basic?>" />
																</span></td>
															<td width="30%" bgcolor="#F9FFFB"> <span class="style2">City/Municipality </span><br />
																	<br />
																	<span class="style6"><b><?=strtoupper($row['mun_name']);?></b>
																	<!--<input type="hidden" name="birth_place_mun" id="birth_place_mun" value="Davao City" />-->
											 <input type="hidden" name="birth_place_mun" id="birth_place_mun" value="<?=$birth_place_mun?>" />
																</span></td>
															<td width="30%" bgcolor="#F9FFFB"> <span class="style2">Province </span><br />
																	<br />
																	<span class="style6"><b><?=$row['prov_name'];?></b>
																	<input type="hidden" name="birth_place_prov" id="birth_place_prov" value="<?=$birth_place_prov?>" />
																</span></td>
														</tr>
													</table>
												</td>
											</tr>
										</table>

										<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td height="124" valign="top" bgcolor="#98D228">
													<table width="100%" border="0" cellspacing="0" cellpadding="0">
														<tr>
															<td height="124" valign="top" bgcolor="#F9FFFB">
																<table width="100%" border="0" cellpadding="0" cellspacing="1" bgcolor="#F9FFFB">
																	<tr>
																		<td width="50%" height="122" align="left" valign="top" bgcolor="#F9FFFB">
																			<table width="90%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td bgcolor="#98D228">
																						<table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																								<td bgcolor="#CFEC9B" class="style6" style="color:#FF0000">*TYPE OF BIRTH</td>
																							</tr>
																							<tr>
																								<td valign="top" bgcolor="#F9FFFB" class="style2">
																									<table width="100%" border="0" cellspacing="0" cellpadding="0">
																										<tr>
																											<td width="31%" bgcolor="#F9FFFB" class="style2">
																												<input type="hidden" name="birth_type" id="birth_type" value="<?php if ($birth_type) echo $birth_type; else echo "1" ?>" />
																												<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="1" onclick="typeOfBirth(this)" <?php if (($birth_type=='1')||(!$birth_type)) echo "checked"; ?> />
																												Single</td>
																											<td width="90%" rowspan="3" valign="top">&nbsp;</td>
																										</tr>
																										<tr>
																											<td bgcolor="#F9FFFB" class="style2">
																												<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="2" onclick="typeOfBirth(this)" <?php if ($birth_type=='2') echo "checked"; ?> />
																												Twin</td>
																										</tr>
																										<tr>
																											<td height="23" bgcolor="#F9FFFB" class="style2">
																												<input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="3" onclick="typeOfBirth(this)" <?php if ($birth_type=='3') echo "checked"; ?> />
																												Triplet</td>
																										</tr>
                                                                                                        <tr>
                                                                                                            <td height="43" bgcolor="#F9FFFB" class="style2">
                                                                                                                <input name="birth_type_tmp" id="birth_type_tmp" type="radio" value="4" onclick="typeOfBirth(this)"  <?php if (($birth_type=='4')) echo "checked"; ?> />
                                                                                                                Others, Specify
                                                                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                                                                <input type="text" name="birth_type_others" id="birth_type_others" onblur="trimString(this);" <?php if (($birth_type=='4')) echo 'value="'.$birth_type_others.'"'; else echo "disabled"; ?> />
                                                                                                            </td>
                                                                                                        </tr>
																									</table>

														                                            <input type="hidden" name="birth_rank" id="birth_rank" value="<?= $birth_rank ?>" />
																									<table width="100%" border="0" cellspacing="0" cellpadding="4">
																										<tr>
																											<td colspan="2" bgcolor="#CFEC9B"><span class="style6" style="color:#FF0000">*IF MULTIPLE BIRTH, CHILD WAS</span></td>
																										</tr>
																										<tr>
																											<td width="54%" bgcolor="#F9FFFB" class="style2">
																												<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this);" value="first" <?php if ($birth_rank=='first') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
																												First</td>
																											<td width="46%" valign="top">&nbsp;</td>
																										</tr>
																										<tr>
																											<td bgcolor="#F9FFFB" class="style2">
																												<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="second" <?php if ($birth_rank=='second') echo "checked"; if (($birth_type==1)||(!$birth_type)) echo "disabled"; ?> />
																												Second</td>
																											<td width="46%" valign="top">&nbsp;</td>
																										</tr>
                                                                                                        <tr>
                                                                                                            <td bgcolor="#F9FFFB" class="style2">
                                                                                                                <input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="third" <?php if ($birth_rank=='third') echo "checked"; if ((($birth_type==1)&&($birth_type==2))||(!$birth_type)) echo "disabled"; ?> />
                                                                                                                Third</td>
                                                                                                            <td width="46%" valign="top">&nbsp;</td>
                                                                                                        </tr>
																										<tr>
																											<td height="43" bgcolor="#F9FFFB" class="style2">
																												<input name="birth_rank_tmp" id="birth_rank_tmp" type="radio" onclick="rankOfBirth(this)" value="" <?php if (($birth_rank!='first')&&($birth_rank!='second')&&($birth_rank!='third')) echo "checked"; if ((($birth_type==1)&&($birth_type==2)&&($birth_type==3))||(!$birth_type)) echo "disabled"; ?> />
																												Others, Specify
																												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																			                                    <input type="text" name="birth_rank_others" id="birth_rank_others" onblur="trimString(this); convertToWords(this); updateBirthRank();" <?php if (($birth_rank!='first')&&($birth_rank!='second')&&($birth_rank!='third')) echo 'value="'.$birth_rank.'"'; else echo "disabled"; ?> />
																											</td>
																											<td width="46%" valign="top">&nbsp;</td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																			</table>
																		</td>
																		<td width="50%" align="right" valign="top" bgcolor="#F9FFFB">
																			<table width="90%" border="0" cellspacing="0" cellpadding="0">
																				<tr>
																					<td height="122" valign="top" bgcolor="#98D228">
																						<table width="100%" border="0" cellpadding="4" cellspacing="1">
																							<tr>
																<td width="100%" bgcolor="#CFEC9B" class="style6" style="color:#FF0000;">*BIRTH ORDER</td>
																							</tr>
																							<tr>
																								<td width="100%" height="104" valign="top" bgcolor="#F9FFFB" class="style2">
																									<table width="100%" border="0" cellspacing="0" cellpadding="0">
																										<tr>
																											<td bgcolor="#F9FFFB" class="style2">&nbsp;&nbsp; (live births and fetal deaths including this delivery) </td>
																										</tr>
																										<tr>
																											<td bgcolor="#F9FFFB" class="style2">

																												<!-- added by KENTOOT 07/09/2014 -->
																												<?php if($objBirthOrder==1){ ?>
																												<input type="text" name="birth_order" id="birth_order" value="<?= $birth_order ?>" onblur="trimString(this); chkInteger(this,countingNumber);" required />
																												<?php }else if($objBirthOrder==0){ ?>
																												<input type="text" name="birth_order" id="birth_order" class="required" value="<?= $birth_order ?>" onblur="trimString(this); convertToWords(this);" required />
																												<?php } ?>
																												&nbsp; (first, second, third, etc.) </td>
																												<!-- end KENTOOT -->

																										</tr>
																									</table>
																									<table width="100%" border="0" cellspacing="0" cellpadding="0">
																										<tr>
																											<td width="100%" bgcolor="#CFEC9B" class="style6" style="color:#FF0000;">&nbsp;*WEIGHT AT BIRTH </td>
																										</tr>
																										<tr>
																											<td bgcolor="#F9FFFB" class="style2">
																												<input name="birth_weight" id="birth_weight" class="required" type="text" size="10" onblur="trimString(this); chkDecimal(this);" onkeypress="return keyPressHandler(event);" maxlength="4" value="<?= $birth_weight ?>" required />
																												&nbsp; grams </td>
																										</tr>
																									</table>
																								</td>
																							</tr>
																						</table>
																					</td>
																				</tr>
																			</table>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									<br />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td height="37">&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FEC194">
				<tr>
					<td width="2%"><img src="images/brdr_pnk_03.gif" width="31" height="26" /></td>
					<td width="65%" bgcolor="#FEC194" class="style5"><A NAME="mother">.</a>MOTHER</td>
					<td width="33%" align="right" bgcolor="#FEC194"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
				</tr>
				<tr>
					<td colspan="3" bgcolor="#FEC194">
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
							<tr>
								<td bgcolor="#F9FFFB">
				<br />
									<table width="90%" border="0" align="center" cellspacing="1">
										<tr>
											<td bgcolor="#FEC194">
												<table width="100%" border="0" cellpadding="4" cellspacing="0" bordercolor="#FFECDD" class="style2">
													<tr>
														<td width="17%" align="center" valign="middle" bordercolor="#FFECDD" bgcolor="#FFECDD" class="style6 style8">MAIDEN NAME</td>
														<td width="35%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0">
															<center>
																<span class="style8">First Name</span><br />
																<?php
																		#added by VAN 05-19-08
																		if (empty($m_name_first)){
																			$m_name_first = strtoupper(stripslashes($mother_fname));
																		}
																		if (empty($m_name_middle)){
																			$m_name_middle = strtoupper(stripslashes($mother_mname));
																		}
																		if (empty($m_name_last)){
																			if ($mother_mname)
																				$m_name_last = strtoupper(stripslashes($mother_lname));
																			else
																				$m_name_last = strtoupper(stripslashes($mother_lname));
																		}

																?>
																<input 
																	name="m_name_first" 
																	type="text" 
																	class="style2" 
																	id="m_name_first" 
																	onblur="trimString(this); parentName(this);" 
																	value="<?= $m_name_first ?>" 
																	size="50" 
																	required 
																/>
															</center>
														</td>
														<td width="22%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0">
															<center>
																<span class="style8">Middle Name</span><br />
																<input 
																	name="m_name_middle" 
																	type="text" 
																	class="style2" 
																	id="m_name_middle" 
																	onblur="trimString(this); parentName(this);" 
																	value="<?= $m_name_middle ?>" 
																	size="30" 
																	required 
																/>
															</center>
														</td>
														<td width="26%" align="center" bordercolor="#FFECDD" bgcolor="#FFF7F0">
															<center>
																<span class="style8">Last Name</span><br />
																<input 
																	name="m_name_last" 
																	type="text" 
																	class="style2" 
																	id="m_name_last" 
																	onblur="trimString(this); parentName(this);" 
																	value="<?= $m_name_last ?>" 
																	size="30" 
																	required 
																/>
															</center>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
					<br />
									<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td bgcolor="#FEC194">
												<table width="100%" border="0" cellpadding="0" cellspacing="1">
													<tr>
														<td width="50%" bgcolor="#F9FFFB" class="style8 style2">
															<table width="100%" border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td width="23%" align="center" bgcolor="#FFECDD" class="style9"><span class="style8" style="color:#FF0000;">*CITIZENSHIP &nbsp;</span></td>
																	<td width="77%" bgcolor="#FFF7F0">
													<?php
														if (empty($m_citizenship))
															$m_citizenship = "FILIPINO";
												?>
												<input type="text" name="m_citizenship" id="m_citizenship" class="required" value="<?= $m_citizenship ?>" onblur="trimString(this);" />
																	</td>
																</tr>
															</table>
														</td>
														<td width="50%" bgcolor="#F9FFFB" class="style8 style2" >
															<table width="100%" border="0" cellspacing="0" cellpadding="0">
																<tr>
																	<td width="18%" align="center" bgcolor="#FFECDD" class="style9"><span class="style6" style="color:#FF0000;">*RELIGION &nbsp;&nbsp;</span></td>
																	<td width="82%" bgcolor="#FFF7F0"><b>
																		<select name="m_religion" id="m_religion">
																			<?php
		# Retrieve record set of all religions
	$religion_obj = $person_obj->getReligion();
	if (empty($m_religion)|| !$m_religion)
		#$m_religion=1;   # default religion, 'Not Indicated'
		#$m_religion=62;   # default religion, 'Not Indicated'

	$m_religion = (int)$GLOBAL_CONFIG['default_religion'];

	while ($result=$religion_obj->FetchRow()){
		$selected='';
		if ($m_religion==$result['religion_nr'])
			$selected='selected';
		echo '					<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
	}
?>
																		</select>
																	</b></td>
																</tr>

															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table><br />
									<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td width="51%" align="left" valign="top">
												<table width="98%" border="0" cellspacing="0" cellpadding="0">
													<tr>
														<td bgcolor="#FEC194">
															<table width="100%" border="0" cellspacing="1" cellpadding="0">
																<tr>
																	<td bgcolor="#F9FFFB">
																		<table width="100%" border="0" cellpadding="4" cellspacing="1" class="style2">
																			<tr>
																				<td width="70%" bgcolor="#FFECDD" class="style11" style="color:#FF0000;">*Total number of children born alive</td>
																				<td width="30%" bgcolor="#FFF7F0">
																					<input name="m_total_alive" id="m_total_alive" class="required" type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" onkeypress="return keyPressHandler(event);" value="<?= $m_total_alive ?>" required />
																				</td>
																			</tr>
																			<tr>
																				<td bgcolor="#FFECDD" class="style11" style="color:#FF0000;">*No. of children still living including this birth &nbsp;</td>
																				<td bgcolor="#FFF7F0">
																					<input name="m_still_living" id="m_still_living" class="required"  type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" onkeypress="return keyPressHandler(event);" value="<?= $m_still_living ?>" required />
																				</td>
																			</tr>
																			<tr>
																				<td bgcolor="#FFECDD" class="style11" style="color:#FF0000;">*No. of children born alive but now are dead &nbsp;</td>
																				<td bgcolor="#FFF7F0">
																					<input name="m_now_dead" id="m_now_dead" class="required"  type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" onkeypress="return keyPressHandler(event);" value="<?= $m_now_dead ?>" required />
																				</td>
																			</tr>
													<tr>
																				<td bgcolor="#FFECDD" class="style11" style="color:#FF0000;">*Total No. of Fetal Deaths / Abortion &nbsp;</td>
																				<td bgcolor="#FFF7F0">
																					<input name="m_fetal_death" id="m_fetal_death" class="required"  type="text" size="5" onblur="trimString(this); chkInteger(this,wholeNumber);" onkeypress="return keyPressHandler(event);" value="<?= $m_fetal_death ?>" required />
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
											<td width="49%" align="right" valign="top">
												<table width="90%" border="0" cellpadding="1" cellspacing="0" class="style2">
													<tr>
														<td bgcolor="#FEC194">
															<table width="100%" border="0" cellspacing="0" cellpadding="4">
																<tr>
																	<td width="48%" bgcolor="#FFECDD" class="style9"> <span class="style6" style="color:#FF0000">*ETHNIC ORIGIN&nbsp;&nbsp;</span></td>
																	<td width="52%" bgcolor="#FFF7F0">
																		<select name="m_ethnic" id="m_ethnic">
																			<?php
														# Retrieve record set of all ethnic origins
														$ethnic_obj = $person_obj->getEthnic_orig();
														if (empty($m_ethnic)|| !$m_ethnic)
															$m_ethnic=1;   # default occupation, 'Not Indicated'
														while ($result=$ethnic_obj->FetchRow()){
															$selected='';
															if ($m_ethnic==$result['nr'])
																$selected='selected';
															echo '					<option value="'.$result['nr'].'" '.$selected.'>'.$result['name']."</option> \n";
														}
													?>
																		</select>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
									<tr>
														<td bgcolor="#FEC194">
															<table width="100%" border="0" cellspacing="0" cellpadding="4">
																<tr>
																	<td width="48%" bgcolor="#FFECDD" class="style9"> <span class="style6" style="color:#FF0000">*OCCUPATION &nbsp;&nbsp;</span></td>
																	<td width="52%" bgcolor="#FFF7F0">
																		<select name="m_occupation" id="m_occupation" onchange="displayOthers();">
																			<?php
		# Retrieve record set of all occupations
	$occupation_obj = $person_obj->getOccupation();
	$m_occupation_others = $person_obj->getMOccupationother($pid); # get others occupation for mother ; added by: syboy 06/27/2015
	if (empty($m_occupation)|| !$m_occupation)
		#$m_occupation=1;   # default occupation, 'Not Indicated'
		$m_occupation=5;   # default occupation, 'Not Indicated'
	while ($result=$occupation_obj->FetchRow()){
		$selected='';
		if ($m_occupation==$result['occupation_nr'])
			$selected='selected';
		echo '<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
	}

	if ($m_occupation_others == '') {
		echo '<span><input type="text" id="m_occupation_other" name="m_occupation_other" style="display:none;" onblur="trimString(this);"></span>';
	} else {
		echo '<span><input type="text" id="m_occupation_other" name="m_occupation_other" value="'.$m_occupation_others.'" onblur="trimString(this);"></span>';
	}
?>
																		</select>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
													<tr>
														<td bgcolor="#FEC194">
															<table width="100%" border="0" cellspacing="0" cellpadding="4">
																<tr>
																	<td width="48%" bgcolor="#FFECDD" class="style9"><span class="style6" style="color:#FF0000;">*Age at the time of this birth</span> &nbsp;</td>
																	<td width="52%" bgcolor="#FFF7F0">

																	
																		<!-- <input name="m_age" id="m_age" class="required"  type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" onkeypress="return keyPressHandler(event);" value="<?= $m_age ?>" required /> -->
																		<!-- // modified by Mary~06-30-2016 -->
																		<input name="m_age" id="m_age" class="required"  type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" onkeypress="" value="<?= $m_age ?>" required />
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
										 </td>
										</tr>
									</table><br />
									<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
										<tr>
											<td bgcolor="#FEC194">
												<table width="100%" border="0" cellpadding="4" cellspacing="1">
													<tr>
														<td colspan="4" bgcolor="#FFECDD" class="style6" style="color:#FF0000">*RESIDENCE</td>
													</tr>
													<tr align="center">
										<?php
											if(!$m_residence_basic || empty($m_residence_basic))
												$m_residence_basic = $street_name;

											if (empty($m_residence_brgy)|| !$m_residence_brgy){
												$m_residence_brgy = $brgy_nr;
												$municity = $address_country->getMunicityByBrgy($m_residence_brgy);
												$m_residence_mun = $municity['mun_nr'];

												$prov = $address_country->getProvinceByBrgy($m_residence_mun);
												$m_residence_prov = $prov['prov_nr'];
											}

									?>
									<td width="40%" bgcolor="#FFF7F0" class="style2">Street/Barangay<br />

											<input type="text" name="m_residence_basic" id="m_residence_basic" class="required"  onblur="trimString(this);UpdateInformantAddress();" value="<?= stripslashes($m_residence_basic) ?>" size="60" required />
											<select id="m_residence_brgy" name="m_residence_brgy" onchange="UpdateInformantAddress(); CheckDropdown(this.value, this.id);">
												<option value="0">-Select a Barangay-</option>
												<?php
													# Retrieve record set of all occupations
													if(empty($m_residence_mun))
														$m_residence_mun=$row['mun_nr'];
													$brgy_obj = $address_country->getBarangayByMuncipality($m_residence_mun);
													while ($result=$brgy_obj ->FetchRow()){
														$selected='';
														if ($m_residence_brgy==$result['brgy_nr'])
															$selected='selected';

															echo '<option value="'.$result['brgy_nr'].'" '.$selected.'>'.$result['brgy_name']."</option> \n";
														}
												?>
											</select>

									 </td>
														<td width="30%" bgcolor="#FFF7F0" class="style2">City/Municipality <br />
																<!--<input type="text" name="m_residence_mun" id="m_residence_mun" onblur="trimString(this)" value="<?= $m_residence_mun ?>" size="30" />-->

											<select id="m_residence_mun" name="m_residence_mun" onchange="UpdateInformantAddress(); CheckDropdown(this.value, this.id);" style="width:300px">
												<!--<option value="0">-Select a City/Municipality-</option>-->
												<?php
													# Retrieve record set of all occupations
													$mun_obj = $address_country->getMunicipal();
													if (empty($m_residence_mun)|| !$m_residence_mun)
														$m_residence_mun=$row['mun_nr'];
													while ($result=$mun_obj ->FetchRow()){
														$selected='';
														if ($m_residence_mun==$result['mun_nr'])
															$selected='selected';

															echo '<option value="'.$result['mun_nr'].'" '.$selected.'>'.$result['mun_name']."</option> \n";
														}
												?>
										</select>
														</td>
														<td width="30%" bgcolor="#FFF7F0" class="style2"> Province <br />
										 <select id="m_residence_prov" name="m_residence_prov" onchange="UpdateInformantAddress(); CheckDropdown(this.value, this.id);">
												<?php
													# Retrieve record set of all occupations
													$prov_obj = $address_country->getProvince();
													if (empty($m_residence_prov)|| !$m_residence_prov)
														$m_residence_prov=$row['prov_nr'];
													while ($result=$prov_obj ->FetchRow()){
														$selected='';
														if ($m_residence_prov==$result['prov_nr'])
															$selected='selected';

															echo '<option value="'.$result['prov_nr'].'" '.$selected.'>'.$result['prov_name']."</option> \n";
														}
												?>
										</select>
														</td>
                                                        <td width="30%" bgcolor="#FFF7F0" class="style2"> Country <br />
                                         <select id="m_residence_country" name="m_residence_country" onchange="disableAdd();">
                                                <?php
                                                    # Retrieve record set of all occupations
                                                    $country_obj = $address_country->getCountry();
                                                    if (empty($m_residence_country)|| !$m_residence_country)
                                                        $m_residence_country='PH';   # default country of citizenship, 'Philippines'
                                                    while ($result=$country_obj ->FetchRow()){
                                                        $selected='';
                                                        if ($m_residence_country==$result['country_code'])
                                                            $selected='selected';

                                                            echo '<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";
                                                        }
                                                ?>
                                        </select>
                                                        </td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
					<br />
								</td>
							</tr>
						</table>
					</td>
			 </tr>
				<tr>
					<td bgcolor="#F9FFFB">&nbsp;</td>
					<td bgcolor="#F9FFFB">&nbsp;</td>
					<td bgcolor="#F9FFFB">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
			<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#A0C2DA" bgcolor="#A0C2DA">
				<tr>
					<td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
					<td bgcolor="#A0C2DA" class="style5"><A NAME="father">.</a>FATHER</td>
					<td align="right" bgcolor="#A0C2DA" class="style5"><a href="#goto">Go to Top</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" bgcolor="#FFFFFF">
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="1">
							<tr>
								<td bgcolor="#F9FFFB"> <br />
										<table width="90%" border="0" align="center" cellpadding="1" cellspacing="0">
											<tr>
												<td bgcolor="#A0C2DA">
													<table width="100%" border="0" cellpadding="2" cellspacing="0" bordercolor="#FFECDD" class="style2">
														<tr>
															<td width="17%" align="center" valign="middle" bordercolor="#FFECDD" bgcolor="#D9E7F0" class="style6" style="color:#FF0000">*NAME</td>
															<?php
																if (empty($f_name_first)){
																	$f_name_first = strtoupper(stripslashes($father_fname));
																}
																if (empty($f_name_middle)){
																	$f_name_middle = strtoupper(stripslashes($father_mname));
																}
																if (empty($f_name_last)){
																	$f_name_last = strtoupper(stripslashes($father_lname));
																}
															?>
										<td width="35%" align="center" bordercolor="#EAF1F7" bgcolor="#EAF1F7">
																<center>
																	First Name <br />
																	<input 
																		type="text" 
																		size="50" 
																		name="f_name_first" 
																		id="f_name_first" 
																		onblur="trimString(this);  /*if (ack){*/ parentName(this); UpdateFatherinLate();
																			fatherField(this);
																		 /*}*/ " 
																		value="<?= $f_name_first ?>" 
																		required 
																	/>
																</center>
															</td>
															<td width="22%" align="center" bordercolor="#FFECDD" bgcolor="#EAF1F7">
																<center>
																	Middle Name<br />
																	<input 
																		type="text" 
																		size="30" 
																		name="f_name_middle" 
																		id="f_name_middle" 
																		onblur="trimString(this);  /*if (ack){*/ parentName(this); UpdateFatherinLate();
																			/*fatherField(this);*/
																		 /*}*/ " 
																		value="<?= $f_name_middle ?>" 
																		required 
																	/>
																</center>
															</td>
															<td width="26%" align="center" bordercolor="#FFECDD" bgcolor="#EAF1F7">
																<center>
																	Last Name<br />
																	<input 
																		type="text" 
																		size="30" 
																		name="f_name_last" 
																		id="f_name_last" 
																		onblur="trimString(this);  /*if (ack){*/ parentName(this); UpdateFatherinLate();
																			/*fatherField(this);*/
																		 /*}*/ " 
																		value="<?= $f_name_last ?>" 
																		required 
																	/>
																</center>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									<br />
										<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
											<tr>
												<td bgcolor="#A0C2DA">
													<table width="100%" border="0" cellpadding="2" cellspacing="1">
														<tr>
															<td width="50%" bgcolor="#F9FFFB" class="style8 style2">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td width="23%" align="center" bgcolor="#D9E7F0" class="style5"><span class="style6" style="color:#FF0000;">*CITIZENSHIP</span> &nbsp;</td>
																		<td width="77%" bgcolor="#EAF1F7">
													<?php
														if (empty($f_citizenship))
															$f_citizenship = "FILIPINO";
													?>
																			<input type="text" name="f_citizenship" id="f_citizenship" class="required" value="<?= $f_citizenship ?>" onblur="trimString(this);" required />
																		</td>
																	</tr>
																</table>
															</td>
															<td width="50%" bgcolor="#F9FFFB" class="style8 style2">
																<table width="100%" border="0" cellspacing="0" cellpadding="0">
																	<tr>
																		<td width="18%" align="center" bgcolor="#D9E7F0" class="style5"><span class="style6" style="color:#FF0000">*RELIGION </span>&nbsp;&nbsp;</td>
																		<td width="82%" bgcolor="#EAF1F7">
																			<select name="f_religion" id="f_religion" class="required">
																				<?php
		# Retrieve record set of all religions
	$religion_obj = $person_obj->getReligion();
	if (empty($f_religion)|| !$f_religion)
		#$f_religion=1;   # default religion, 'Not Indicated'
		#$f_religion=62;   # default religion, 'Not Indicated'
		$f_religion = (int)$GLOBAL_CONFIG['default_religion'];
	
	while ($result=$religion_obj->FetchRow()){
		$selected='';
		if ($f_religion==$result['religion_nr'])
			$selected='selected';
		echo '					<option value="'.$result['religion_nr'].'" '.$selected.'>'.$result['religion_name']."</option> \n";
	}
?>
																			</select>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									<br />

										<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F9FFFB">
											<tr>
												<td height="32" bgcolor="#A0C2DA">
													<table width="100%" border="0" cellspacing="1" cellpadding="2">
														<tr>
															<td bgcolor="#FFFFFF">
																<table width="100%" border="0" cellpadding="2" cellspacing="0">
																	<tr>
																		<td width="23%" bgcolor="#D9E7F0">
																			<span class="style6" style="color:#FF0000">*ETHNIC ORIGIN</span> &nbsp;&nbsp;
																		</td>
																		<td width="24%" bgcolor="#EAF1F7">
																			<select name="f_ethnic" id="f_ethnic">
																			<?php
														# Retrieve record set of all ethnic origins
														$ethnic_obj = $person_obj->getEthnic_orig();
														if (empty($f_ethnic)|| !$f_ethnic)
															$f_ethnic=1;   # default occupation, 'Not Indicated'
														while ($result=$ethnic_obj->FetchRow()){
															$selected='';
															if ($f_ethnic==$result['nr'])
																$selected='selected';
															echo '					<option value="'.$result['nr'].'" '.$selected.'>'.$result['name']."</option> \n";
														}
													?>
																		</select>
																		</td>
																		<td width="26%" bgcolor="#D9E7F0" class="style6" style="color:#FF0000">
																			<center>
																				*Age at the time of this birth
																			</center>
																		</td>
																		<td width="27%" bgcolor="#EAF1F7">
																			<!-- <input name="f_age" id="f_age" class="required" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" onkeypress="return keyPressHandler(event);" value="<?= $f_age ?>" required /> -->

																			<!-- // modified by Mary~06-30-2016 -->

																			<input name="f_age" id="f_age" class="required" type="text" size="5" onblur="trimString(this); chkInteger(this,countingNumber);" onkeypress="" value="<?= $f_age ?>" required />
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
												</td>
											</tr>
							 <tr>
												<td height="32" bgcolor="#A0C2DA">
													<table width="100%" border="0" cellspacing="1" cellpadding="2">
														<tr>
															<td bgcolor="#FFFFFF">
																<table width="100%" border="0" cellpadding="2" cellspacing="0">
																	<tr>
																		<td width="23%" bgcolor="#D9E7F0">
																			<span class="style6" style="color:#FF0000">*OCCUPATION</span> &nbsp;&nbsp;
																		</td>
																		<td width="24%" bgcolor="#EAF1F7">
																			<select name="f_occupation" id="f_occupation" onblur="displayOthers();">
																				<?php
															# Retrieve record set of all occupations
															$occupation_obj = $person_obj->getOccupation();
															$f_occupation_others = $person_obj->getFOccupationother($pid); # get others occupation for mother ; added by: syboy 06/27/2015
															if (empty($f_occupation)|| !$f_occupation)
																$f_occupation=1;   # default occupation, 'Not Indicated'
															while ($result=$occupation_obj->FetchRow()){
																$selected='';
																if ($f_occupation==$result['occupation_nr'])
																$selected='selected';
																echo '					<option value="'.$result['occupation_nr'].'" '.$selected.'>'.$result['occupation_name']."</option> \n";
															}

															if ($f_occupation_others == '') {
																echo '<span><input type="text" id="f_occupation_other" name="f_occupation_other" style="display:none;" onblur="trimString(this);"></span>';
															} else {
																echo '<span><input type="text" id="f_occupation_other" name="f_occupation_other" value="'.$f_occupation_other.'" onblur="trimString(this);"></span>';
															}
													?>
																			</select>
																		</td>
																		<td bgcolor="#EAF1F7" colspan="2">&nbsp;

																	 </td>
																	</tr>
																</table>
															</td>
														</tr>
                                                    </table>
												</td>
											</tr>
                                            <!-- added by VAS 06-27-2012-->
                                            <tr>
                                                 <td>
                                                    &nbsp;
                                                </td>
                                            </tr>
                                            <tr>

                                                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                 <tr bgcolor="#FFFFFF">
                                                    <td bgcolor="#A0C2DA">
                                                        <table width="100%" border="0" cellpadding="4" cellspacing="1">
                                                             <br />
                                                            <tr>
                                                                <td colspan="4" bgcolor="#D9E7F0">
                                                                    <span class="style6" style="color:#FF0000">*RESIDENCE</span>
                                                                </td>
                                                            </tr>
                                                            <tr align="center">
                                                                <?php
                                                                    if (empty($f_residence_brgy)|| !$f_residence_brgy){
                                                                        $f_residence_brgy = $brgy_nr;
                                                                        $municity = $address_country->getMunicityByBrgy($f_residence_brgy);
                                                                        $f_residence_mun = $municity['mun_nr'];

                                                                        $prov = $address_country->getProvinceByBrgy($f_residence_mun);
                                                                        $f_residence_prov = $prov['prov_nr'];
                                                                    }

                                                                ?>
                                                                <td width="40%" bgcolor="#EAF1F7" class="style6">Street/Barangay<br />

                                                                    <input type="text" name="f_residence_basic" id="f_residence_basic" onblur="trimString(this);" value="<?= $f_residence_basic ?>" size="60" required />
                                                                    <select id="f_residence_brgy" name="f_residence_brgy" onchange="CheckDropdown(this.value, this.id);">
                                                                        <option value="0">-Select a Barangay-</option>
                                                                        <?php
                                                                            # Retrieve record set of all occupations
                                                                            if(empty($f_residence_mun))
																				$f_residence_mun=$row['mun_nr'];
                                                                            $brgy_obj = $address_country->getBarangayByMuncipality($f_residence_mun);
                                                                            while ($result=$brgy_obj ->FetchRow()){
                                                                                $selected='';
                                                                                if ($f_residence_brgy==$result['brgy_nr'])
                                                                                    $selected='selected';

                                                                                    echo '<option value="'.$result['brgy_nr'].'" '.$selected.'>'.$result['brgy_name']."</option> \n";
                                                                                }
                                                                        ?>
                                                                    </select>

                                                                </td>
                                                                <td width="30%" bgcolor="#EAF1F7" class="style2">City/Municipality <br />
                                                                    <select id="f_residence_mun" name="f_residence_mun" style="width:300px" onchange="CheckDropdown(this.value, this.id);">
                                                                            <?php
                                                                                # Retrieve record set of all occupations
                                                                                $mun_obj = $address_country->getMunicipal();
                                                                                if (empty($f_residence_mun)|| !$f_residence_mun)
                                                                                    $f_residence_mun=$row['mun_nr'];   # default country of citizenship, 'Philippines'
                                                                                while ($result=$mun_obj ->FetchRow()){
                                                                                    $selected='';
                                                                                    if ($f_residence_mun==$result['mun_nr'])
                                                                                        $selected='selected';

                                                                                        echo '<option value="'.$result['mun_nr'].'" '.$selected.'>'.$result['mun_name']."</option> \n";
                                                                                    }
                                                                            ?>
                                                                    </select>
                                                                </td>
                                                                <td width="30%" bgcolor="#EAF1F7" class="style2"> Province <br />
                                                                    <select id="f_residence_prov" name="f_residence_prov" onchange="CheckDropdown(this.value, this.id);">
                                                                        <?php
                                                                            # Retrieve record set of all occupations
                                                                            $prov_obj = $address_country->getProvince();
                                                                            if (empty($f_residence_prov)|| !$f_residence_prov)
                                                                                $f_residence_prov=$row['prov_nr'];;   # default country of citizenship, 'Philippines'
                                                                            while ($result=$prov_obj ->FetchRow()){
                                                                                $selected='';
                                                                                if ($f_residence_prov==$result['prov_nr'])
                                                                                    $selected='selected';

                                                                                    echo '<option value="'.$result['prov_nr'].'" '.$selected.'>'.$result['prov_name']."</option> \n";
                                                                                }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td width="30%" bgcolor="#EAF1F7" class="style2"> Country <br />
                                                                     <select id="f_residence_country" name="f_residence_country" onchange="disableAdd();">
                                                                            <?php
                                                                                # Retrieve record set of all occupations
                                                                                $country_obj = $address_country->getCountry();
                                                                                if (empty($f_residence_country)|| !$f_residence_country)
                                                                                    $f_residence_country='PH';   # default country of citizenship, 'Philippines'
                                                                                while ($result=$country_obj ->FetchRow()){
                                                                                    $selected='';
                                                                                    if ($f_residence_country==$result['country_code'])
                                                                                        $selected='selected';

                                                                                        echo '<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";
                                                                                    }
                                                                            ?>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                 </tr>
                                            </table>

                                </tr>
                                <!-- -->

								 </table>
					</td>
				 </tr>
					 </table>
				 <br />         </td>
			 </tr>
		 </table>
	 </td>
				</tr>
				<tr>
					<td bgcolor="#F9FFFB">&nbsp;</td>
					<td width="65%" bgcolor="#F9FFFB">&nbsp;</td>
					<td width="33%" bgcolor="#F9FFFB">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>


	<tr>
		<td align="center" class="style4">&nbsp;</td>
	</tr>
</table>
<br />
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
	<tr>
		<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
			<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
				<tr>
					<td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="32" /></td>
					<td width="97%" class="style5">DATE AND PLACE OF MARRIAGE OF PARENTS (If not married , accomplish Affidavit of Acknowledgment/Admission of Paternity) </td>
				</tr>
			</table>
			</td>
		</tr>
	<tr>
	 <td height="40" colspan="2" bgcolor="#F9FFFB" class="style2">
		<!--<input type="hidden" name="parent_marriage_info" id="parent_marriage_info" value="<?php if ($parent_marriage_info) echo $parent_marriage_info; else echo "1"; ?>">-->
		&nbsp;&nbsp;&nbsp;&nbsp;
		<!--<input type="radio" name="marriage_type_tmp" id="marriage_type_tmp" onclick="typeOfMarriage(this);" value="1" <?php if (($marriage_type=='1')||(!$marriage_type)) echo "checked"; ?>>-->
		<input type="radio" name="is_married" id="is_married" onclick="typeOfMarriage(this); displayInfantname();" value="1" <?php if (($is_married=='1')||(!$is_married)) echo "checked"; ?>>
		Married &nbsp;&nbsp;&nbsp;&nbsp;
		<!--<input type="radio" name="marriage_type_tmp" id="marriage_type_tmp" onclick="typeOfMarriage(this);" value="2" <?php if ($is_married=='2') echo "checked"; ?>>-->
		<input type="radio" name="is_married" id="is_married" onclick="typeOfMarriage(this); displayInfantname();" value="0" <?php if ($is_married=='0') echo "checked"; ?>>
		Not Married&nbsp;&nbsp;    </td>
	</tr>

	<tr id="tribal"  height="40" colspan="2" bgcolor="#F9FFFB" class="style2">
	<?php
							if ($is_tribalwed)
								$checked = "checked";
							else
								$checked = "";
					?>
		<td><input type="checkbox" name="istribal" id="istribal" value="1" onchange="clearMarriedInfo()" <?=$checked?> /> Tribal Wedding</td>
	</tr>

	<tr>
	 <td height="42" colspan="2" bgcolor="#F9FFFB" class="style2" id="married_parents" style="color:#FF0000;">
		 &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
		 <b>*Date and Place of Marriage</b>&nbsp;&nbsp;
		<!--<input type="text" size="100" name="parent_marriage_info_tmp" id="parent_marriage_info_tmp" onblur="trimString(this); $('parent_marriage_info').value= '1 - '+this.value;" value="<?= $parent_marriage_info_tmp ?>">-->
		<?php
								#$parent_marriage_info_tmp

											$phpfd=$date_format;
											$phpfd=str_replace("dd", "%d", strtolower($phpfd));
											$phpfd=str_replace("mm", "%m", strtolower($phpfd));
											$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

											if (($parent_marriage_date!='0000-00-00')  && ($parent_marriage_date!=""))
												$parent_marriage_date = @formatDate2Local($parent_marriage_date,$date_format);
											else
												$parent_marriage_date='';

											 $sDateJS= '<input name="parent_marriage_date" type="text" size="8" maxlength=10 value="'.$parent_marriage_date.'"'.
                                                            'id = "parent_marriage_date"
                                                            onBlur="IsValidDate(this,\''.$date_format.'\'); UpdateLateMarriedDate();">
                                                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="parent_marriage_date_trigger" style="cursor:pointer" >
                                                            <font size=3>[';
											ob_start();
										?>
											<script type="text/javascript">
												Calendar.setup ({
													inputField : "parent_marriage_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "parent_marriage_date_trigger", onClose: function(cal) { cal.hide();$('late_date_married').value=$('parent_marriage_date').value}, singleClick : true, step : 1
												});
											</script>
										<?php
											$calendarSetup = ob_get_contents();
											ob_end_clean();

											$sDateJS .= $calendarSetup;
											$dfbuffer="LD_".strtr($date_format,".-/","phs");
											$sDateJS = $sDateJS.$$dfbuffer.']';
										?>
													 &nbsp;&nbsp; <?= $sDateJS ?>

		at &nbsp;<input type="text" name="parent_marriage_place" id="parent_marriage_place" size="60" value="<?=$parent_marriage_place?>" onblur="UpdateLateMarriedPlace();">
		</td>
	</tr>
	<tr>
		<td colspan="2" bgcolor="#F9FFFB" class="style2">    </td>
	</tr>
	<tr>
		<td id="married_parents_not" colspan="2" bgcolor="#F9FFFB">
			<table bordercolor="#FF0000" bgcolor="#FFFFFF">
				<tr>
					<td width="35%">					</td>
					<td width="30%">					</td>
					<td width="35%">					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<b>AFFIDAVIT OF ACKNOWLEDGMENT/ADMISSION OF PATERNITY</b>
                        <br>
                        <span><font size="-1">(For births before 3 August 1988) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (For births on or after August 1988)</font></span>
                        </td>
				</tr>
				<tr>
					<td colspan="3" align="center">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="justify">
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						I/We,
						<!-- <span id="f_fullname" style="color:#FF0000;font-style:italic;"> -->
						<?php
							$name_father=$f_name_first." ".$f_name_middle." ".$f_name_last;
							#edited by VAN 08-28-08
							if (stristr($f_name_first,",")){
								$f_name_first_new = explode(",",$f_name_first);
								
								for ($i=0;$i<sizeof($f_name_first_new);$i++){
									$name_new .= "'".trim($f_name_first_new[$i])."',";
								}
								
								$fname1 = $f_name_first_new[0];
								$fname2 = $f_name_first_new[1];
							}else{
								$fname1 = $f_name_first;
								$fname2 = "";
							}


							if ((stristr($fname2,'JR')) || (stristr($fname2,'SR')))
								$comma = ", ";
							else
								$comma = " ";

							if ($fname2)
								$fname2 = $comma.$fname2;

							$name_father=$fname1." ".$f_name_middle." ".$f_name_last.$fname2;
							$name_father=$fname1.$fname2." ".$f_name_middle." ".$f_name_last;

							if (trim($name_father)!=""){
								// echo "						$name_father";
								if (empty($f_fullname))
									$f_fullname = $name_father;
							}else{
								// echo "						(father's name)";
								$f_fullname = "(father's name)";
							}
						?>
						<!-- </span> -->
						<input 
							type="text" 
							name="f_fullname" 
							id="f_fullname" 
							value="<?=$f_fullname?>" 
							size="<?=strlen($f_fullname)+20?>"
						/>
						<!-- <span id="f_fullname2" style="color:#FF0000;font-style:italic;">
						</span> -->
						and
						<span id="m_fullname" style="color:#FF0000;font-style:italic;">
						<?php
							$name_mother=$m_name_first." ".$m_name_middle." ".$m_name_last;
							if (trim($name_mother)!=""){
								echo($name_mother);
							}else{
								echo "						(mother's name)";
							}
						?>
						</span>
						of legal age, am/are the natural mother and/or father of
                        <span id="bname" style="color:#FF0000;font-style:italic;">
                        <?php
                            $baby_name = ucwords(strtolower($name_first))." ".ucwords(strtolower($name_middle))." ".ucwords(strtolower($name_last));
                            echo "                        $baby_name";
                        ?>
                        </span>, who was born on
                        <!-- <span id="bname" style="color:#FF0000;font-style:italic;"> -->
                        <?php
                            $birthdate = date("F d, Y", strtotime($date_birth));
                            echo "                        $birthdate";
                        ?>
                        </span>
                        at
                        <span id="bname" style="color:#FF0000;font-style:italic;">
                        <?php
                            echo "                        $birth_place_basic";
                        ?>
                        </span>
                        .					</td>
						<!--<input type="text" name="father_name_new" id="father_name_new" size="100" value="<?=$name_father?>" />-->
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
                <tr>
                    <td colspan="3" align="justify">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        I am / We are executing this affidavit to attest to the truthfulness of the foregoing statements
                        and for purposes at acknowledging my/our child.
                    </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
				<tr>
					<td align="center">					
					<!-- Christian 11-15-19 -->
						<span id="f_fullname_signature"  style="color:#000000;font-style:normal;">
						<?php
                            $f_fullname_signature=$f_fullname;

                            if($f_fullname=='n/a'){
                            	$f_fullname_signature='';
                            }

                            echo "                        $f_fullname_signature";
                        ?>
						</span><br>
					<!--/ Christian end -->
					_____________________________________________</td>
					<td>&nbsp;</td>
					<td align="center">	
					<!-- Updated by Christian 10-31-19 -->
						<span id="m_fullname_signature" style="color:#000000;font-style:normal;"><?=$name_mother;?></span><br>_____________________________________________
					</td>
				</tr>
				<tr>
					<td align="center">(Signature Over Printed Name of Father)</td>
					<td>&nbsp;</td>
					<td align="center">(Signature Over Printed Name of Mother)</td>
				</tr>
				<!--
                <tr>
					<td>
						Community Tax No. &nbsp;&nbsp;
						<input name="f_com_tax_nr" id="f_com_tax_nr" onblur="trimString(this)" value="<?= $f_com_tax_nr ?>" >
					</td>
					<td>&nbsp;</td>
					<td>
						Community Tax No. &nbsp;&nbsp;
						<input name="m_com_tax_nr" id="m_com_tax_nr" onblur="trimString(this)" value="<?= $m_com_tax_nr ?>" >
					</td>
				</tr>
				<tr>
					<td>
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!=""))
		$f_com_tax_date = @formatDate2Local($f_com_tax_date,$date_format);
	else
		$f_com_tax_date='';

	$sDateJS= '<input name="f_com_tax_date" type="text" size="8" maxlength=10 value="'.$f_com_tax_date.'"'.
				'id = "f_com_tax_date"
				onBlur="IsValidDate(this,\''.$date_format.'\'); ">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="f_com_tax_date_trigger" style="cursor:pointer" >
				<font size=3>[';
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "f_com_tax_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "f_com_tax_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
						Date Issued &nbsp;&nbsp; <?= $sDateJS ?>
					</td>
					<td>&nbsp;</td>
					<td>
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($m_com_tax_date!='0000-00-00') && ($m_com_tax_date!=""))
		$m_com_tax_date = @formatDate2Local($m_com_tax_date,$date_format);
	else
		$m_com_tax_date='';

	$sDateJS= '<input name="m_com_tax_date" type="text" size="8" maxlength=10 value="'.$m_com_tax_date.'"'.
				'id = "m_com_tax_date"
				onBlur="IsValidDate(this,\''.$date_format.'\'); ">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="m_com_tax_date_trigger" style="cursor:pointer" >
				<font size=3>[';
			ob_start();
	?>
			<script type="text/javascript">

			Calendar.setup ({
					inputField : "m_com_tax_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "m_com_tax_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
						Date Issued &nbsp;&nbsp; <?= $sDateJS ?>
					</td>
				</tr> -->

				<!-- Uncomment by mary~06-30-2016 -->
				<!-- <tr>
					<td>
						Place Issued &nbsp;&nbsp;
						<input name="f_com_tax_place" id="f_com_tax_place" onblur="trimString(this)" value="<?= $f_com_tax_place ?>" >
					</td>
					<td>&nbsp;</td>
					<td>
						Place Issued &nbsp;&nbsp;
						<input name="m_com_tax_place" id="m_com_tax_place" onblur="trimString(this)" value="<?= $m_com_tax_place ?>" >
					</td>
				</tr> -->
               
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($officer_date_sign!='0000-00-00') && ($officer_date_sign!=""))
		$officer_date_sign = @formatDate2Local($officer_date_sign,$date_format);
	else
		$officer_date_sign='';

	$sDateJS= '<input name="officer_date_sign" type="text" size="8" maxlength=10 value="'.$officer_date_sign.'"'.
				'id = "officer_date_sign"
				onBlur="fatherDateChecker(this,\''.$date_format.'\');">
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="officer_date_sign_trigger" style="cursor:pointer" >
				<font size=3>[';
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "officer_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "officer_date_sign_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
					<td colspan="3" align="justify">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<b> SUBSCRIBED AND SWORN </b> to before me this
					<?= $sDateJS ?>
					&nbsp; by
					<!--  <span id="f_fullname2" style="color:#FF0000;font-style:italic;"> commented by Christian 11-01-19 id replaced --> 
                    <span id="f_fullname_sworn" style="color:#FF0000;font-style:italic;">
                    <?php
                    		  #Commented by Christian 11-15-19 repeat code.
       //                     if (stristr($f_name_first,",")){
       //                          $f_name_first_new = explode(",",$f_name_first);
       //                          /*
       //                          for ($i=0;$i<sizeof($f_name_first_new);$i++){
       //                              $name_new .= "'".trim($f_name_first_new[$i])."',";
       //                          }
       //                          */
       //                          $fname1 = $f_name_first_new[0];
       //                          $fname2 = $f_name_first_new[1];
       //                      }else{
       //                          $fname1 = $f_name_first;
       //                          $fname2 = "";
       //                      } 
                            
							// $name_father=$fname1.$fname2." ".$f_name_middle." ".$f_name_last;

       //                      if (trim($name_father)!=""){
       //                          #echo "                        $name_father";
       //                          if (empty($f_fullname))
       //                              $f_fullname = $name_father;
       //                      }else{
       //                          #echo "                        (father's name)";
       //                          $f_fullname = "(father's name)";
       //                      }

       //                      echo "                        $f_fullname";
       //                      
       						#added by Christian 11-15-19			
                            $f_fullname_sworn=$f_fullname;

                            if($f_fullname=='n/a'){
                            	$f_fullname_sworn='&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
                            }

                            echo "                        $f_fullname_sworn";
                            #added by Christian 11-15-19 end             
                     ?>
                        </span>
                        and
                        <!-- past code below -->
                        <!-- <span id="m_fullname2" style="color:#FF0000;font-style:italic;"> commented by Christian id replaced -->
                        <span id="m_fullname_sworn" style="color:#FF0000;font-style:italic;">
                        <?php
                       		// Updated by Christian 11-26-19
                            $name_mother=$m_name_first." ".$m_name_middle." ".$m_name_last;
                            // var_dump($name_mother);
                            if ($f_fullname == "n/a" || $f_fullname == "n/a n/a n/a"){
                            	$name_mother_sworn = '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
                            }
                            else if (trim($name_mother)!=""){
                            	$name_mother_sworn = $name_mother;
                            }else{
                                $name_mother_sworn = "(mother's name)";
                            }
                            echo "                        $name_mother_sworn";
                        ?>
                        </span>
                    , who exhibited to me (his/her) Community Tax Cert. No.
                    <input name="f_com_tax_nr" id="f_com_tax_nr" onblur="trimString(this)" value="<?= $f_com_tax_nr ?>" >
                    Issued on
                    <?php
        $phpfd=$date_format;
        $phpfd=str_replace("dd", "%d", strtolower($phpfd));
        $phpfd=str_replace("mm", "%m", strtolower($phpfd));
        $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
        //$phpfd=str_replace("yy","%Y", strtolower($phpfd));

    if (($f_com_tax_date!='0000-00-00') && ($f_com_tax_date!=""))
        $f_com_tax_date = @formatDate2Local($f_com_tax_date,$date_format);
    else
        $f_com_tax_date='';

    $sDateJS= '<input name="f_com_tax_date" type="text" size="15" maxlength=10 value="'.$f_com_tax_date.'"'.
                'onFocus="this.select();"
                id = "f_com_tax_date"
                onBlur="fatherDateChecker(this,\''.$date_format.'\'); "
                >
                <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="f_com_tax_date_trigger" style="cursor:pointer" >
                <font size=3>[';
            ob_start();
    ?>
            <script type="text/javascript">	
            Calendar.setup ({
                    inputField : "f_com_tax_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "f_com_tax_date_trigger", singleClick : true, step : 1
            });
            </script>
<?php
            $calendarSetup = ob_get_contents();
            ob_end_clean();

        $sDateJS .= $calendarSetup;
        /**/
        $dfbuffer="LD_".strtr($date_format,".-/","phs");
        $sDateJS = $sDateJS.$$dfbuffer.']';
?>
                    &nbsp;&nbsp; <?= $sDateJS ?>
                    at
					<!-- <input name="f_com_tax_place" id="f_com_tax_place" onblur="trimString(this)" value="<?= $f_com_tax_place ?>" > -->
                    <?php
        				#Updated By Christian 11-21-19
                        if (empty($late_officer_place_sign)){
                        	if($f_fullname == "n/a" || $f_fullname == "n/a n/a n/a" || $f_fullname == "(father's name)") 
                            	$late_officer_place_sign = "";
                            else
                            	$late_officer_place_sign = "DAVAO CITY";
                        }
                    ?>
                    <input name="late_officer_place_sign" id="late_officer_place_sign" size="60" onblur="trimString(this)" value="<?= $late_officer_place_sign ?>" >
                    , Philippines.
                </td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td align="center">_____________________________________________</td>
					<td>&nbsp;</td>
					<td align="center">
						<input name="officer_title" id="officer_title" onblur="trimString(this)" size="30" value="<?= $officer_title ?>" >
					</td>
				</tr>
				<tr>
					<td align="center">(Signature of the Administering Officer)</td>
					<td>&nbsp;</td>
					<td align="center">(Position/Title/Designation)</td>
				</tr>
				<tr>
					<td align="center" valign="bottom">
						<input name="officer_name" id="officer_name" onblur="trimString(this)" size="50" value="<?= $officer_name ?>" >
					</td>
					<td>&nbsp;</td>
					<td align="center">
						<textarea name="officer_address" id="officer_address" cols="50" rows="2"><?= $officer_address ?></textarea>
					</td>
				</tr>
				<tr>
					<td align="center">(Name in Print)</td>
					<td>&nbsp;</td>
					<td align="center">(Address)</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2" bgcolor="#F9FFFB" class="style2">

			<table width="100%" border="0" cellpadding="4" cellspacing="0">
				<!--
			<tr>
					<td colspan="2" bgcolor="#A0C2DA" class="style5"> ATTENDANT</td>
				</tr>
			-->
			<tr>
					<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
					<tr>
								<td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
								<td width="97%" class="style5">ATTENDANT </td>
							</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td width="35%" height="145" bgcolor="#F9FFFB" >
						<table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
							<tr>
								<td bgcolor="#EAF1F7" class="style2">
						<input type="hidden" name="attendant_type" id="attendant_type" value="<?php if ($attendant_type) echo $attendant_type.' - '.$attendant_type_others; else echo "1 - Physician"; ?>">
									<input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="1" <?php if (($attendant_type=='1')||(!$attendant_type)) echo "checked"; ?>>
									1. Physician                </td>
							</tr>
							<tr>
								<td height="56" bgcolor="#EAF1F7" class="style2">
									<input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="4" <?php if ($attendant_type=='4') echo "checked"; ?>>
									4. Hilot (Traditional Midwife)</td>
							</tr>
						</table>
						<center>
						</center>
						</td>
					<td bgcolor="#F9FFFB" >
						<table width="90%" border="0" align="center" cellpadding="0" cellspacing="1">
							<tr>
								<td bgcolor="#EAF1F7" class="style2">
									<input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="2" <?php if ($attendant_type=='2') echo "checked"; ?>>
									2. Nurse                </td>
								<td bgcolor="#EAF1F7" class="style2">
									<input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="3" <?php if ($attendant_type=='3') echo "checked"; ?>>
									3. Midwife</td>
							</tr>
							<tr>
								<td height="61" colspan="2" bgcolor="#EAF1F7" class="style2">
									<input type="radio" name="attendant_type_tmp" id="attendant_type_tmp" onclick="typeOfAttendant(this)" value="5" <?php if ($attendant_type==5) echo "checked"; ?>>
									5. Others (Specify) &nbsp;&nbsp;
									<input type="text" size="50" name="attendant_type_others" id="attendant_type_others" onblur="trimString(this); $('attendant_type').value= '5 - '+this.value;" value="<?php if ($attendant_type==5) echo $attendant_type_others; ?>" <?php if (($attendant_type!=5)||(!$attendant_type)) echo "disabled"; ?>>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		<br />
		</td>
	</tr>
	<tr>
		<td colspan="2" bgcolor="#F9FFFB">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="2%" bgcolor="#A0C2DA"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
		<td width="98%" bgcolor="#A0C2DA">
			<?php
	if (($birth_time!="") && ($birth_time)){
	#if ($birth_time!=""){
		#$birth_time_tmp = convert24HourTo12HourLocal($birth_time);
#		echo "birth_time_tmp ='".$birth_time_tmp."' &nbsp;&nbsp;";
		#$birth_time_array = explode(' ',$birth_time_tmp);
		#$birth_time = $birth_time_array[0];
		#$birth_time_period = $birth_time_array[1];
        $birth_time2 = date('h:i', strtotime($birth_time));
        $birth_time_period = date('A', strtotime($birth_time));
	}else{
		$birth_time = '';
	}
?>
			<span class="style5">CERTIFICATION OF BIRTH </span></td>
	</tr>
	<tr>
		<td colspan="2" bgcolor="#A0C2DA">
			<table width="100%" border="0" cellspacing="1" cellpadding="0">
				<tr>
					<td bgcolor="#F9FFFB">
						<table width="100%" border="0" cellspacing="10" cellpadding="0">
							<tr>
								<td bgcolor="#FFFFFF"><span class="style2">I hereby certify that I attended the birth of the child who was born alive at &nbsp;&nbsp;
										<input type="text" size="3" name="birth_time" id="birth_time" class="required" style="border-style:double; border-color:#FF0000;" value="<?= $birth_time2 ?>" onchange="setFormatTime(this);" required/>
&nbsp;o'clock &nbsp;
<select id='selAMPM' name="selAMPM">
	<option value="A.M." <?php if ($birth_time_period=='AM') echo "selected"; ?>>A.M.</option>
	<option value="P.M." <?php if ($birth_time_period=='PM') echo "selected"; ?>>P.M.</option>
</select>
on the date stated above. </span></td>
							</tr>
							<tr>
								<td>
									<table width="90%" border="0" align="center" cellspacing="2">
										<tr>
											<td width="50%" colspan="2" bgcolor="#EAF1F7" class="style2">Signature</td>
											<td width="50%" rowspan="2" bgcolor="#EAF1F7" class="style2" style="color:#FF0000;"><b>*Address</b><br />
													<?php
																if (empty($attendant_address)){
																	$attendant_address = $row['hosp_name'].", ".$row['brgy_name'].", ".$row['mun_name'];
																}
													?>
													<textarea name="attendant_address" id="attendant_address" cols="50" rows="2"><?= $attendant_address ?></textarea>
											</td>
										</tr>
							<!-- added by VAN 08-05-08 -->

							<tr>
									<td bgcolor="#EAF1F7" class="style2"> Current resident doctor? </td>
											 <?php
										if (is_numeric($attendant_name)){
											$is_resident = 1;
										}else{
											$is_resident = 0;
											#$attendant_name = attendant_name
										}

										if (empty($attendant_name))
											$is_resident = 1;
								?>
								<td bgcolor="#EAF1F7" class="style2"><input type="checkbox" name="is_resident" id="is_resident" value="1" onchange="check_Doctor_status();"  <?= ($is_resident)?'checked="checked" ':''?>/></td>
							</tr>
							<tr id="nonres_status" style="display:none">
											<td bgcolor="#EAF1F7" class="style2" colspan="2">
								 <input type="radio" name="nr_status" id="nr_status" value="graduated" <?= ($non_resident_status=='graduated')?'checked="checked" ':''?> />&nbsp;Graduated&nbsp;&nbsp;
								 <input type="radio" name="nr_status" id="nr_status" value="resigned" <?= ($non_resident_status=='resigned')?'checked="checked" ':''?> />&nbsp;Resigned&nbsp;&nbsp;
								 <input type="radio" name="nr_status" id="nr_status" value="on-leave" <?= ($non_resident_status=='on-leave')?'checked="checked" ':''?> />&nbsp;On-Leave&nbsp;&nbsp;
							 </td>

							</tr>
							<!-- -->
										<tr>
											<td bgcolor="#EAF1F7" class="style6" style="color:#FF0000">*Name in Print </td>
											<td bgcolor="#EAF1F7" class="style2">
												<!--<input type="text" size="50" name="attendant_name" id="attendant_name" onblur="trimString(this)" value="<?= $attendant_name ?>" />-->
								<span id="resident" style="display:none ">
								<select id="attendant_name" name="attendant_name" onchange="showDrLate()">
							<option value="0">-Select a Doctor-</option>
							<?php
										# Retrieve record set of all occupations
										#$doctor_obj = $pers_obj->getDoctors(1);
										$doctor_obj = $pers_obj->getDoctors2(1," AND d.nr IN (139,140,155) ");
										/*
										if (empty($late_doctor)|| !$late_doctor)
											$late_doctor="0";   # default country of citizenship, 'Philippines'
										*/
										while ($result=$doctor_obj->FetchRow()){
											$selected='';
											if ($attendant_name==$result['personell_nr'])
												$selected='selected';

											$middleInitial = "";
											if (trim($result['name_middle'])!=""){
												$thisMI=split(" ",$result['name_middle']);
												foreach($thisMI as $value){
													if (!trim($value)=="")
													$middleInitial .= $value[0];
												}
												if (trim($middleInitial)!="")
												$middleInitial .= ". ";
											}
											#$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
											#$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));
											$doctor_name = trim($result["name_last"]).", ".trim($result["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
											$doctor_name = ucwords(strtolower($doctor_name)).", MD";

											echo '<option value="'.$result['personell_nr'].'" '.$selected.'>'.$doctor_name."</option> \n";
										}
							?>
						</select></span>
									<span id="non_resident" style="display:none">
										<input type="text" name="attendant_name2" id="attendant_name2" size="35" onkeyup="update_LateDr_name1();" onblur="update_LateDr_name1();" value="<?=$attendant_name?>">
													</span>
							 </td>
										</tr>
										<tr>
											<td bgcolor="#EAF1F7" class="style2" style="color:#FF0000;"> <b>*Title or Position</b> </td>
											<td bgcolor="#EAF1F7" class="style2">
												<input type="text" size="35" name="attendant_title" id="attendant_title" class="required" onblur="trimString(this)" value="<?= $attendant_title ?>" />
											</td>
											<td bgcolor="#EAF1F7"> <span class="style2" style="color:#FF0000;">
												<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

	if (($attendant_date_sign!='0000-00-00') && ($attendant_date_sign!=""))
		$attendant_date_sign = @formatDate2Local($attendant_date_sign,$date_format);
	else
		$attendant_date_sign='';

	$sDateJS= '<input name="attendant_date_sign" type="text" size="8" maxlength=10 value="'.$attendant_date_sign.'"'.
				'id = "attendant_date_sign" class="required"
				onBlur="IsValidDate(this,\''.$date_format.'\');" required>
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="attendant_date_trigger" style="cursor:pointer" >
				<font size=3>[';
			ob_start();
	?>
					<script type="text/javascript">
			Calendar.setup ({
					inputField : "attendant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "attendant_date_trigger", singleClick : true, step : 1
			});
						</script>
												<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
												<b>*Date</b> &nbsp;&nbsp;
												<?= $sDateJS ?>
											</span></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		</tr>
</table>
<br />
</td>
	</tr>
	<tr>
		<td colspan="2" class="style2">
			<table width="100%" border="0" cellpadding="4" cellspacing="0">
				<!--
			<tr>
					<td bgcolor="#A0C2DA"><span class="style5"> INFORMANT</span></td>
				</tr>
			-->
			<tr>
					<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
					<tr>
								<td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
								<td width="97%" class="style5"><span class="style6" style="color:#FF0000">*INFORMANT</span> </td>
							</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td bgcolor="#F9FFFB">
						<table width="90%" border="0" align="center" cellspacing="2">
							<tr>
								<td width="50%" height="45" bgcolor="#EAF1F7" class="style2">Signature</td>
								<td width="50%" rowspan="2" bgcolor="#EAF1F7" class="style2">
									Address <br>
								 <textarea name="informant_address" id="informant_address" class="required" cols="50" rows="2" required><?= $informant_address ?></textarea>
								</td>
							</tr>
							<tr>
								<td height="66" bgcolor="#EAF1F7" class="style2">
									Name in Print&nbsp;&nbsp;
								 <input type="text" size="50" name="informant_name" id="informant_name" class="required" onblur="trimString(this)" value="<?= $informant_name ?>" required>
								</td>
							</tr>
							<tr>
								<td height="54" bgcolor="#EAF1F7" class="style2">
									Relationship to the child&nbsp;&nbsp;
										 <!--<input type="text" size="40" name="informant_relation" id="informant_relation" onblur="trimString(this)" value="<?= $informant_relation ?>">-->
									<select name="informant_relation" id="informant_relation">
																				<?php
															# Retrieve record set of all occupations
															$relationship_obj = $person_obj->getRelationship();
															if (empty($informant_relation)|| !$informant_relation)
																$informant_relation='mother';   # default relationship, 'mother'
															while ($result=$relationship_obj->FetchRow()){
																$selected='';
																if ($informant_relation==$result['child_relation'])
																$selected='selected';
																echo '					<option value="'.$result['child_relation'].'" '.$selected.'>'.$result['child_relation']."</option> \n";
															}
													?>
											</select>
					 </td>
								<td bgcolor="#EAF1F7" class="style2">
<?php
		$phpfd=$date_format;
		$phpfd=str_replace("dd", "%d", strtolower($phpfd));
		$phpfd=str_replace("mm", "%m", strtolower($phpfd));
		$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
		//$phpfd=str_replace("yy","%Y", strtolower($phpfd));
	#echo "sign = ".$informant_date_sign;
	if (($informant_date_sign!='0000-00-00')  && ($informant_date_sign!=""))
		$informant_date_sign = @formatDate2Local($informant_date_sign,$date_format);
	else
		$informant_date_sign='';

	$sDateJS= '<input name="informant_date_sign" type="text" size="8" maxlength=10 value="'.$informant_date_sign.'"'.
				'id = "informant_date_sign" class="required"
				onBlur="IsValidDate(this,\''.$date_format.'\'); " required>
				<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="informant_date_trigger" style="cursor:pointer" >
				<font size=3>[';
			ob_start();
	?>
			<script type="text/javascript">
			Calendar.setup ({
					inputField : "informant_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "informant_date_trigger", singleClick : true, step : 1
			});
			</script>
<?php
			$calendarSetup = ob_get_contents();
			ob_end_clean();

		$sDateJS .= $calendarSetup;
		/**/
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$sDateJS = $sDateJS.$$dfbuffer.']';
#echo "$ dfbuffer ='".$dfbuffer."' &nbsp;&nbsp;";
#echo "$ $ dfbuffer ='".$$dfbuffer."' &nbsp;&nbsp;";
?>
						 Date &nbsp;&nbsp; <?= $sDateJS ?>
<!--
								 <input type="text" name="informant_date_sign" id="informant_date_sign" onblur="trimString(this)" value="<?= $informant_date_sign ?>">
-->
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<!--added by VAN 05-23-08-->
	<tr>
		<td colspan="2" class="style2">
			<table width="100%" border="0" cellpadding="4" cellspacing="0">

					<tr>
						<td height="26" colspan="2" bgcolor="#F9FFFB" class="style6">
						 <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#A0C2DA">
								<tr>
										<td width="2%"><img src="images/brdr_blgry_03.gif" width="31" height="26" /></td>
										<td width="97%" class="style5"><span class="style5"> LATE / DELAYED REGISTRATION</span> </td>
									 </tr>
							</table>
						</td>
						</tr>
				<tr bgcolor="#F9FFFB">
					<?php
							if ($is_late_reg)
								$checked = "checked";
							else
								$checked = "";
					?>
					<td><input type="checkbox" name="is_late_reg" id="is_late_reg" value="1" onclick="showLateReg(this);" <?= $checked; ?>/>&nbsp;<span><strong>IS LATE / DELAYED REGISTRATION? </strong></span></td>
				</tr>
			</table>
		</td>
	</tr>

    <tr id="late_reg" style="display:none">
		<td width="100%" bgcolor="#F9FFFB" colspan="2">
			<table id="lateTable" width="100%" border="0" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="3"  bgcolor="#F9FFFB">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<b>AFFIDAVIT FOR DELAYED REGISTRATION OF BIRTH</b>
						<br>
						<span><font size="-1">(To be accomplished by the hospital/clinic administrator, father, mother, or guardian of the person
                                               himself if 18 years old or over.)</font></span>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I, &nbsp;<input type="text" name="late_affiant_name" id="late_affiant_name" size="40" value="<?=$late_affiant_name?>" />&nbsp;, of legal age, single/married/divorced/widow/widower,
						with residence and postal address at
						 <br>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" name="late_affiant_address" id="late_affiant_address" size="50" value="<?=$late_affiant_address?>" />&nbsp;,
						after having been duly sworn in accordance with law, do hereby depose and say: <br>
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>
						<?php
								$late_baby_name = ucwords(strtolower($name_first))." ".ucwords(strtolower($name_middle))." ".ucwords(strtolower($name_last));
						        #$late_birth_place = $birth_place_basic.", ".$row['brgy_name'].", ".$row['mun_name'];
                                $late_birth_place = $birth_place_basic;
                        ?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. That I am the applicant for the delayed registration of :
                        <br>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="is_subject_person" id="is_subject_person" value="1" onclick="LateApplicant();" <?php if ($is_subject_person) echo "checked"; ?>>
                        &nbsp;my birth in&nbsp;
                            <input type="text" name="late_birth_place" id="late_birth_place" size="40" value="<?=$late_birth_place?>" readonly="1" />&nbsp;
                            on&nbsp;
                            <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

                                            $late_bdate = $date_birth;
                                            if (($late_bdate!='0000-00-00')  && ($late_bdate!=""))
                                                $late_bdate = @formatDate2Local($late_bdate,$date_format);
                                            else
                                                $late_bdate='';

                                            $sDateJS= '<input name="late_bdate3" type="text" size="8" maxlength=10 value="'.$late_bdate.'"'.
                                                            'id = "late_bdate3"
                                                            onBlur="IsValidDate(this,\''.$date_format.'\'); UpdateBirthDate(this.value);"
                                                            >
                                                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="late_bdate3_trigger" style="cursor:pointer" >
                                                            <font size=3>[';
                                            ob_start();
                                        ?>
                                            <script type="text/javascript">
                                                Calendar.setup ({
                                                    inputField : "late_bdate3",
                                                    ifFormat : "<?php echo $phpfd?>",
                                                    showsTime : false,
                                                    button : "late_bdate3_trigger",
                                                    onClose: function(cal) {
                                                                        cal.hide();
                                                                        UpdateBirthDate($('late_bdate3').value);
                                                             },
                                                    singleClick : true,
                                                    step : 1
                                                });
                                            </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $sDateJS .= $calendarSetup;
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $sDateJS = $sDateJS.$$dfbuffer.']';
                                        ?>
                                                     &nbsp;&nbsp; <?= $sDateJS ?>&nbsp;
                        <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="hidden" name="late_bdate" id="late_bdate" value="<?=$late_bdate?>" />
                        <input type="radio" name="is_subject_person" id="is_subject_person" value="0" onclick="LateApplicant();" <?php if (!$is_subject_person) echo "checked"; ?>>
                        &nbsp;the birth of&nbsp;<input type="text" name="late_baby_name" id="late_baby_name" size="40" value="<?=$late_baby_name?>" readonly="1" />&nbsp;
                        &nbsp;who was born in <input type="text" name="late_bhosp" id="late_bhosp" size="40" value="<?=$late_birth_place?>" readonly="1" />
                        &nbsp;on
                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

                                            $sDateJS= '<input name="late_bdate2" type="text" size="8" maxlength=10 value="'.$late_bdate.'"'.
                                                            'id = "late_bdate2"
                                                            onBlur="IsValidDate(this,\''.$date_format.'\'); UpdateBirthDate(this.value);"
                                                            >
                                                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="late_bdate2_trigger" style="cursor:pointer" >
                                                            <font size=3>[';
                                            ob_start();
                                        ?>
                                            <script type="text/javascript">
                                                Calendar.setup ({
                                                    inputField : "late_bdate2",
                                                    ifFormat : "<?php echo $phpfd?>",
                                                    showsTime : false,
                                                    button : "late_bdate2_trigger",
                                                    onClose: function(cal) {
                                                                        cal.hide();
                                                                        UpdateBirthDate($('late_bdate2').value);
                                                             },
                                                    singleClick : true,
                                                    step : 1
                                                });
                                            </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $sDateJS .= $calendarSetup;
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $sDateJS = $sDateJS.$$dfbuffer.']';
                                        ?>
                                                     &nbsp;&nbsp; <?= $sDateJS ?>&nbsp;
                        <br>
					    <input type="hidden" name="late_birth_place3" id="late_birth_place3" value="<?=$late_birth_place?>" />
                        <input type="hidden" name="late_bdate" id="late_bdate" value="<?=$late_bdate?>" />
                        <input type="hidden" name="late_baby_name3" id="late_baby_name3" value="<?=$late_baby_name?>" />
                    </td>
				</tr>
                <tr>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. That I/he/she was attended at birth by &nbsp;
						<?php
								if ($attendant_name)
									$late_doctor = $attendant_name;
						?>
						<span id="late_resident" style="display:none">
						<select id="late_doctor" name="late_doctor" onchange="showAttendantDr();">
							<option value="0">-Select a Doctor-</option>
							<?php
										# Retrieve record set of all occupations
										$doctor_obj = $pers_obj->getDoctors2(1," AND d.nr IN (139,140,155) ");
										while ($result=$doctor_obj->FetchRow()){
											$selected='';
											if ($late_doctor==$result['personell_nr'])
												$selected='selected';

											$middleInitial = "";
											if (trim($result['name_middle'])!=""){
												$thisMI=split(" ",$result['name_middle']);
												foreach($thisMI as $value){
													if (!trim($value)=="")
													$middleInitial .= $value[0];
												}
												if (trim($middleInitial)!="")
												$middleInitial .= ". ";
											}
											$doctor_name = $result["name_first"]." ".$result["name_2"]." ".$middleInitial.$result["name_last"];
											$doctor_name = "Dr. ".ucwords(strtolower($doctor_name));

											echo '<option value="'.$result['personell_nr'].'" '.$selected.'>'.$doctor_name."</option> \n";
										}
							?>
						</select>
						</span>
						<span id="late_non_resident" style="display:none">
							<input size="40" type="text" name="late_doctor2" id="late_doctor2" onkeyup="update_LateDr_name2();" onblur="update_LateDr_name2();" value="<?=$late_doctor?>" />
						</span>
						&nbsp; who resides at <input type="text" name="late_hospital" id="late_hospital" size="60" value="<?=$late_hospital?>" />&nbsp;.
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. That I/he/she is a citizen of &nbsp;
							<select id="late_baby_citizenship" name="late_baby_citizenship">
								<option value="0">-Select a Country of Citizenship-</option>
								 <?php
										# Retrieve record set of all occupations
										$country_obj = $address_country->getAllAddress();
										if (empty($late_baby_citizenship)|| !$late_baby_citizenship)
											$late_baby_citizenship="PH";   # default country of citizenship, 'Philippines'
										while ($result=$country_obj->FetchRow()){
											$selected='';
											if ($late_baby_citizenship==$result['country_code'])
												$selected='selected';

											echo '					<option value="'.$result['country_code'].'" '.$selected.'>'.$result['country_name']."</option> \n";
										}
								?>
							</select>
							&nbsp;.<br>
                     </td>
				</tr>
				<tr>
					<td>

						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. That my/his/her parents were &nbsp;
						<input type="radio" name="late_married_type" id="late_married_type" value="1" onclick="LateMarriedType(this.value);" <?php if ($parent_marriage_date) echo "checked"; ?>> &nbsp;married on
						<?php
								#$parent_marriage_info_tmp

											$phpfd=$date_format;
											$phpfd=str_replace("dd", "%d", strtolower($phpfd));
											$phpfd=str_replace("mm", "%m", strtolower($phpfd));
											$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

											if (($late_date_married!='0000-00-00')  && ($late_date_married!=""))
												$late_date_married = @formatDate2Local($late_date_married,$date_format);
											else
												$late_date_married='';

											$sDateJS= '<input name="late_date_married" type="text" size="8" maxlength=10 value="'.$late_date_married.'"'.
															'id = "late_date_married"
															onBlur="UpdateMarriedDate();"
															>
															<img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="late_date_married_trigger" style="cursor:pointer" >
															<font size=3>[';
											ob_start();
										?>
											<script type="text/javascript">
												Calendar.setup ({
													inputField : "late_date_married", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "late_date_married_trigger", onClose: function(cal) { cal.hide();$('parent_marriage_date').value=$('late_date_married').value},singleClick : true, step : 1
												});
											</script>
										<?php
											$calendarSetup = ob_get_contents();
											ob_end_clean();

											$sDateJS .= $calendarSetup;
											$dfbuffer="LD_".strtr($date_format,".-/","phs");
											$sDateJS = $sDateJS.$$dfbuffer.']';
										?>
													 &nbsp;&nbsp; <?= $sDateJS ?>
                    </td>
				</tr>
				<tr>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						at &nbsp;<input type="text" name="late_place_married" id="late_place_married" size="60" value="<?=$late_place_married?>" onblur="UpdateMarriedPlace();" />						&nbsp;.	<br>
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<!--<input type="radio" name="late_married_type" id="late_married_type" onclick="LateMarriedType(this.value);" value="2" <?php if (($is_married=='0')||(!$is_married)) echo "checked"; ?>>-->
						<input type="radio" name="late_married_type" id="late_married_type" onclick="LateMarriedType(this.value);" value="2" <?php if (!$parent_marriage_date) echo "checked"; ?>> &nbsp; not married but I/he/she was acknowledged by my/his/her father whose
					</td>
				</tr>
				<tr>
					<td>
						<?php
								if (stristr($f_name_first,",")){
									$f_name_first_new = explode(",",$f_name_first);
									$fname1 = $f_name_first_new[0];
									$fname2 = $f_name_first_new[1];
								}else{
									$fname1 = $f_name_first;
									$fname2 = "";
								}

								if ($fname2)
									$fname2 = ", ".$fname2;

								#$late_name_father = ucwords(strtolower($f_name_first))." ".ucwords(strtolower($f_name_middle))." ".ucwords(strtolower($f_name_last));
								$late_name_father = ucwords(strtolower(stripslashes($fname1)))." ".ucwords(strtolower(stripslashes($f_name_middle)))." ".ucwords(strtolower(stripslashes($f_name_last)))."".strtoupper(stripslashes($fname2));
								#echo "fname = ".$late_name_father;
						?>
						<input type="hidden" name="late_name_father2" id="late_name_father2" value="<?=$late_name_father?>" />
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						name is <input type="text" id="late_name_father" name="late_name_father" value="<?=$late_name_father?>" size="50" readonly="1">
                    </td>
				</tr>
				<tr>
					<td>
					    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5. That the reason for the delay in registering my/his/her birth was <br>
					    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea id="late_reason" name="late_reason" rows="3" cols="80"><?=$late_reason?></textarea>&nbsp;.
                    </td>
				</tr>
				<tr>
					<td>
					    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;6.
					    <input type="radio" id="late_informantType" name="late_informantType" value="1" onclick="EnableMarriedTo(this);" <?php if (($is_married=='1')||(!$is_married)) echo "checked"; ?> /> &nbsp; (For the applicant only)
						     That I am married to &nbsp;&nbsp;&nbsp;<input type="text" name="late_husband" id="late_husband" size="50" value="<?=$late_husband?>" />&nbsp;.
                    </td>
				</tr>
				<tr>
					<td>
						<?php
								if ($late_relationship)
									$checked = "checked";
								else
									$checked = "";
						?>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" id="late_informantType" name="late_informantType" value="1" onclick="EnableForGuardian(this);" <?=$checked ?> /> &nbsp; (If the applicant is other than the document owner)
						 That I am the &nbsp;
						<select name="late_relationship" id="late_relationship">
																				<?php
															# Retrieve record set of all occupations
															$relationship_obj = $person_obj->getRelationship();
															if (empty($late_relationship)|| !$late_relationship)
																$late_relationship='mother';   # default relationship, 'mother'
															while ($result=$relationship_obj->FetchRow()){
																$selected='';
																if ($late_relationship==$result['child_relation'])
																$selected='selected';
																echo '					<option value="'.$result['child_relation'].'" '.$selected.'>'.$result['child_relation']."</option> \n";
															}
													?>
											</select>
						 of the said person&nbsp;.
						<input type="hidden" name="late_relationship2" id="late_relationship2" value="<?=$late_relationship?>" />
						<input type="hidden" id="late_purpose" name="late_purpose" size="60" value="<?=$late_purpose?>" />
                    </td>
				</tr>
                <tr>
                    <td colspan="3" align="justify">
                        7. That I am executing this affidavit to attest to the truthfulness of the foregoing statements for all
                           legal intents and purposes.
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="justify">
                        In truth whereof, I have affixed my signature below this
                        <?php
                                            $phpfd=$date_format;
                                            $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                            $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                            $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

                                            if (($affiant_com_tax_date2!='0000-00-00')  && ($affiant_com_tax_date2!=""))
                                                $affiant_com_tax_date2 = @formatDate2Local($affiant_com_tax_date2,$date_format);
                                            else
                                                $affiant_com_tax_date2='';

                                            $sDateJS= '<input name="affiant_com_tax_date2" type="text" size="15" maxlength=10 value="'.$affiant_com_tax_date2.'"'.
                                                            'onFocus="this.select();"
                                                            id = "affiant_com_tax_date2"
                                                            onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                            >
                                                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="affiant_com_tax_date2_trigger" style="cursor:pointer" >
                                                            <font size=3>[';
                                            ob_start();
                                        ?>
                                            <script type="text/javascript">
                                                Calendar.setup ({
                                                    inputField : "affiant_com_tax_date2", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "affiant_com_tax_date2_trigger", singleClick : true, step : 1
                                                });
                                            </script>
                                        <?php
                                            $calendarSetup = ob_get_contents();
                                            ob_end_clean();

                                            $sDateJS .= $calendarSetup;
                                            $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                            $sDateJS = $sDateJS.$$dfbuffer.']';
                                        ?>
                                        &nbsp;&nbsp; <?= $sDateJS ?>
                        at
                        &nbsp;&nbsp;&nbsp;<input type="text" id="affiant_com_tax_place2" name="affiant_com_tax_place2" size="30" value="<?=$affiant_com_tax_place2?>">
                        , Philippines.
                    </td>
                </tr>
				<tr>
					<td>
						<table width="100%" border="0" cellpadding="4" cellspacing="0">
							<tr>
									<td>_____________________________________________</td>
                            </tr>
							<tr>
                                <td>(Signature Over Printed Name of Affiant)</td>

							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>

				            <tr>
					            <td>&nbsp;</td>
				            </tr>
				            <tr>
					            <?php
							        $phpfd=$date_format;
							        $phpfd=str_replace("dd", "%d", strtolower($phpfd));
							        $phpfd=str_replace("mm", "%m", strtolower($phpfd));
							        $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

							        if (($late_officer_date_sign!='0000-00-00') && ($late_officer_date_sign!=""))
								        $late_officer_date_sign = @formatDate2Local($late_officer_date_sign,$date_format);
							        else
								        $late_officer_date_sign='';

							        $sDateJS= '<input name="late_officer_date_sign" type="text" size="8" maxlength=10 value="'.$late_officer_date_sign.'"'.
											        'id = "late_officer_date_sign"
											        onBlur="IsValidDate(this,\''.$date_format.'\'); ">
											        <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="late_officer_date_sign_trigger" style="cursor:pointer" >
											        <font size=3>[';
							        ob_start();
						        ?>
						        <script type="text/javascript">
							        Calendar.setup ({
								        inputField : "late_officer_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "late_officer_date_sign_trigger", singleClick : true, step : 1
							        });
						        </script>
						        <?php
							        $calendarSetup = ob_get_contents();
							        ob_end_clean();

							        $sDateJS .= $calendarSetup;

							        $dfbuffer="LD_".strtr($date_format,".-/","phs");
							        $sDateJS = $sDateJS.$$dfbuffer.']';
						        ?>

					            <td colspan="2" align="justify">
						            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						            <b> SUBSCRIBED AND SWORN </b> to before me this
						            <?= $sDateJS ?>
						            <br>
							            at
							            <?php
                                                if (empty($officer_place_sign))
                                                    $officer_place_sign = $row['mun_name'];
                                        ?>
                                    <input name="officer_place_sign" id="officer_place_sign" size="30" onblur="trimString(this)" value="<?= $officer_place_sign ?>" >
                                    , Philippines, affiant who exhibited to me his Community Tax Cert.
                                     &nbsp;&nbsp;&nbsp;<input type="text" id="affiant_com_tax_nr" name="affiant_com_tax_nr" size="30" value="<?=$affiant_com_tax_nr?>">
                                    issued <br> on
                                    <?php
                                                        $phpfd=$date_format;
                                                        $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                                        $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                                        $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

                                                        if (($affiant_com_tax_date!='0000-00-00')  && ($affiant_com_tax_date!=""))
                                                            $affiant_com_tax_date = @formatDate2Local($affiant_com_tax_date,$date_format);
                                                        else
                                                            $affiant_com_tax_date='';

                                                        $sDateJS= '<input name="affiant_com_tax_date" type="text" size="8" maxlength=10 value="'.$affiant_com_tax_date.'"'.
                                                                        'onFocus="this.select();"
                                                                        id = "affiant_com_tax_date"
                                                                        onBlur="IsValidDate(this,\''.$date_format.'\'); "
                                                                        onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">
                                                                        <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="affiant_com_tax_date_trigger" style="cursor:pointer" >
                                                                        <font size=3>[';
                                                        ob_start();
                                                    ?>
                                                        <script type="text/javascript">
                                                            Calendar.setup ({
                                                                inputField : "affiant_com_tax_date", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "affiant_com_tax_date_trigger", singleClick : true, step : 1
                                                            });
                                                        </script>
                                                    <?php
                                                        $calendarSetup = ob_get_contents();
                                                        ob_end_clean();

                                                        $sDateJS .= $calendarSetup;
                                                        $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                                        $sDateJS = $sDateJS.$$dfbuffer.']';
                                                    ?>
                                                    &nbsp;&nbsp; <?= $sDateJS ?>
                                        at
                                        &nbsp;&nbsp;&nbsp;<input type="text" id="affiant_com_tax_place" name="affiant_com_tax_place" size="30" value="<?=$affiant_com_tax_place?>">
                                        .
                                    </td>
				                </tr>
				            <tr>
					            <td>&nbsp;</td>
				            </tr>
				            <tr>
					            <td>
						            <table width="100%" border="0" cellpadding="4" cellspacing="0">
							            <tr>
								            <td align="center">_____________________________________________</td>
								            <td>&nbsp;</td>
								            <td align="center">
									            <input name="late_officer_title" id="late_officer_title" onblur="trimString(this)" size="30" value="<?= $late_officer_title ?>" >								</td>
							            </tr>
							            <tr>
								            <td align="center">(Signature of Administering Officer)</td>
								            <td>&nbsp;</td>
								            <td align="center">(Position/Title/Designation)</td>
							            </tr>
							            <tr>
								            <td align="center" valign="bottom">
									            <input name="late_officer_name" id="late_officer_name" onblur="trimString(this)" size="50" value="<?= $late_officer_name ?>" >								</td>
								            <td>&nbsp;</td>
								            <td align="center">
									            <textarea name="late_officer_address" id="late_officer_address" cols="50" rows="2"><?= $late_officer_address ?></textarea>								</td>
							            </tr>
							            <tr>
								            <td align="center">(Name in Print)</td>
								            <td>&nbsp;</td>
								            <td align="center">(Address)</td>
							            </tr>
						            </table>
                                </td>
				            </tr>
				            <tr>
					            <td>&nbsp;</td>
                            </tr>
			        </table>
		        </td>
	        </tr>
  </table>
 </table>
<div align="center">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
        <td width="100%" class="style2" bgcolor="#F9FFFB">

        <br />

        <table width="100%" border="0" cellspacing="0" cellpadding="0">
             <tr>
                 <td width="32%" bgcolor="#FF6600" colspan="2">
                    <span class="style18">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PREPARED BY</span>
                 </td>
             </tr>
        </table>
        <table width="100%" border="0" cellspacing="2">
            <tr>
                <td colspan="2" bgcolor="#FFF3EC" class="style2">Signature</td>
            </tr>
            <tr>
                <td bgcolor="#FFF3EC" class="style2" width="15%">
                        Name in Print &nbsp;&nbsp;
                </td>
                    <?php
                        $signatory = $pers_obj->get_Signatory('birthcert', true);
                        if (empty($encoder_name)){
                            #$encoder_info = $pers_obj->get_Signatory('birthcert');
                            #$signatory = $pers_obj->get_Signatory('birthcert', true);
                            #$encoder_name = $encoder_info['name'];
                            #$encoder_title = $encoder_info['signatory_position'];
                            //$encoder_title = '';
                            $default_title = $signatory->FetchRow();
                            $encoder_title = $default_title['signatory_position'];
                        }else{

                            /*$personell = $pers_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);
                            if (empty($encoder_title)){
                                if ($personell['job_position'])
                                    $encoder_title = strtoupper($personell['job_position']);
                                else
                                    $encoder_title = strtoupper($personell['job_function_title']);
                            }*/
                            $sig_array = array();
                            while ($row = $signatory->FetchRow()) {
                              $sig_array[] = $row['name'];
                            }

                            if (!(in_array( $encoder_name, (array)$sig_array)))
                                $add_option = '<option value="'.$encoder_name.'" selected>'.$encoder_name."</option> \n";

                        }
                        $signatory = $pers_obj->get_Signatory('birthcert', true);
                	?>
                <td bgcolor="#FFF3EC" width="85%">
                    <!--<input name="encoder_name" type="text" class="style2" id="encoder_name" onblur="trimString(this)" value="<?= $encoder_name ?>" size="50" />-->
                    <select id="encoder_name" name="encoder_name" onchange="UpdatePosition(this.value,'birthcert');">
                        <!--<option value="0">-Select a Signatory-</option>-->
                        <?php
                            # Retrieve record set of all signatories
                            echo $add_option;
                            while ($result = $signatory->FetchRow()) {
                                $selected='';
                                if ($encoder_name==$result['name'])
                                    $selected='selected';

                                echo '<option value="'.$result['name'].'" '.$selected.'>'.$result['name']."</option> \n";
                            }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td bgcolor="#FFF3EC" class="style2">
                        Title or Position&nbsp;&nbsp;
                </td>
                <td bgcolor="#FFF3EC">
                        <input name="encoder_title" type="text" class="style2" id="encoder_title" onblur="trimString(this)" value="<?= $encoder_title ?>" size="40" />
                </td>
            </tr>
            <tr>
                 <td bgcolor="#FFF3EC" class="style2" style="color:#FF0000;">
                        <b>*Date</b>&nbsp;&nbsp;
                 </td>
                <td bgcolor="#FFF3EC" class="style2" style="color:#FF0000;">
                            <?php
                                    $phpfd=$date_format;
                                    $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                    $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                    $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                    //$phpfd=str_replace("yy","%Y", strtolower($phpfd));

                                if (($encoder_date_sign!='0000-00-00')  && ($encoder_date_sign!=""))
                                    $encoder_date_sign = @formatDate2Local($encoder_date_sign,$date_format);
                                else
                                    $encoder_date_sign='';

                                $sDateJS= '<input name="encoder_date_sign" type="text" size="8" maxlength=10 value="'.$encoder_date_sign.'"'.
                                            'id = "encoder_date_sign" class="required"
                                            onBlur="IsValidDate(this,\''.$date_format.'\');" required>
                                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="encoder_date_trigger" style="cursor:pointer" >
                                            <font size=3>[';
                                        ob_start();
                                ?>
                                        <script type="text/javascript">
                                        Calendar.setup ({
                                                inputField : "encoder_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "encoder_date_trigger", singleClick : true, step : 1
                                        });
                                        </script>

                            <?php
                                        $calendarSetup = ob_get_contents();
                                        ob_end_clean();

                                    $sDateJS .= $calendarSetup;
                                    /**/
                                    $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                    $sDateJS = $sDateJS.$$dfbuffer.']';
                            ?>
                                                     <?= $sDateJS ?>
                    </td>
                </tr>
            <tr>
                <td colspan="2" bgcolor="#FFF3EC">&nbsp;</td>
            </tr>
        </table>
        <!-- added by VAN 09-03-2012-->
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td width="32%" bgcolor="#FF6600" colspan="2">
                    <span class="style18">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;RECEIVED BY</span>
                </td>
            </tr>
         </table>
         <table width="100%" border="0" cellspacing="2">
            <tr>
                <td colspan="2" bgcolor="#FFF3EC" class="style2">Signature</td>
            </tr>
            <tr>
                <td bgcolor="#FFF3EC" class="style2" width="15%">
                        Name in Print&nbsp;&nbsp;
                </td>
                    <?php
                        if (empty($receiver_name)){
                            $receiver_info = $pers_obj->get_Signatory_Outside('birthcert','receiver');
                            $receiver_name = $receiver_info['signatory_name'];
                            $receiver_title = $receiver_info['signatory_position'];
                        }
                ?>
                <td bgcolor="#FFF3EC" width="85%">
                    <input name="receiver_name" type="text" class="style2" id="receiver_name" onblur="trimString(this)" value="<?= $receiver_name ?>" size="50" />
                </td>
            </tr>
            <tr>
                <td bgcolor="#FFF3EC" class="style2">
                       Title or Position&nbsp;&nbsp;
                </td>
                <td bgcolor="#FFF3EC">
                        <input name="receiver_title" type="text" class="style2" id="receiver_title" onblur="trimString(this)" value="<?= $receiver_title ?>" size="40" />
                </td>
            </tr>
            <tr>
                 <td bgcolor="#FFF3EC" class="style2">
                        Date&nbsp;&nbsp;
                 </td>
                <td bgcolor="#FFF3EC" class="style2">
                            <?php
                                    $phpfd=$date_format;
                                    $phpfd=str_replace("dd", "%d", strtolower($phpfd));
                                    $phpfd=str_replace("mm", "%m", strtolower($phpfd));
                                    $phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
                                    //$phpfd=str_replace("yy","%Y", strtolower($phpfd));

                                if (($receiver_date_sign!='0000-00-00')  && ($receiver_date_sign!=""))
                                    $receiver_date_sign = @formatDate2Local($receiver_date_sign,$date_format);
                                else
                                    $receiver_date_sign='';

                                $sDateJS= '<input name="receiver_date_sign" type="text" size="8" maxlength=10 value="'.$receiver_date_sign.'"'.
                                            'id = "receiver_date_sign"
                                            onBlur="IsValidDate(this,\''.$date_format.'\'); ">
                                            <img '.createComIcon($root_path,'show-calendar.gif','0','absmiddle').' id="receiver_date_trigger" style="cursor:pointer" >
                                            <font size=3>[';
                                        ob_start();
                                ?>
                                        <script type="text/javascript">
                                        Calendar.setup ({
                                                inputField : "receiver_date_sign", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "receiver_date_trigger", singleClick : true, step : 1
                                        });
                                        </script>

                            <?php
                                        $calendarSetup = ob_get_contents();
                                        ob_end_clean();

                                    $sDateJS .= $calendarSetup;
                                    /**/
                                    $dfbuffer="LD_".strtr($date_format,".-/","phs");
                                    $sDateJS = $sDateJS.$$dfbuffer.']';
                            ?>
                                                     <?= $sDateJS ?>
                    </td>
                </tr>
            <tr>
                <td colspan="2" bgcolor="#FFF3EC">&nbsp;</td>
            </tr>
        </table>
        <!-- -->
     </td>
     <td width="51%" bgcolor="#F9FFFB" class="style2">&nbsp;</td>
    </tr>
        <tr>
			<td  bgcolor="#A0C2DA">
				<center>
	<?php
			if (!$birthCertInfo || empty($birthCertInfo)){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save" onClick="insertEmptyAudit();">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" id="print_2" value="Print" onClick="printBirthCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";
				#added by VAN 05-26-08
				echo '			<input type="button" name="muslim_print" id="muslim_print" value="For Muslim Only" onClick="printMuslimCert(\''.$pid.'\')">'."\n &nbsp; &nbsp;";

				echo '			<input type="submit" id="update_2" name="Submit" value="Update" onClick="insertEmptyAudit();"> '."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
	&nbsp; &nbsp;
	<input type="button" name="Cancel" value="Cancel"  onclick="window.close()" id="cancel_2" />
	<!-- added by: syboy 03/18/2016 : meow -->
	<input type="hidden" name="viewbirthcert" id="viewbirthcert" value="<?=$_GET['viewCert']?>"> 
			 </center>
		 </td>
		</tr>
	</table>
</div>
</form>
<script language="javascript">
<?php
	echo $presetJavascript;
?>
</script>
<!-- ADDED BY Carl -->
<script>
function CheckDropdown(Check, Id){
			if(Id == "m_residence_prov"){
				GetMunicipality(Check, Id);
				setTimeout(function() { 
					var municipalID = noC("#m_residence_mun").val();
					GetBarangay(municipalID, 'm_residence_mun');
				 }, 500);
			}
			else if(Id == "m_residence_mun"){
				GetBarangay(Check, Id);
				setTimeout(function() { 
					var provID = noC("#m_residence_mun").val();
					GetProvince(provID, Id);
				 }, 500);
			}
			else if(Id == "m_residence_brgy"){
				GetReverseMunicipality(Check, Id);
				setTimeout(function() { 
					var provID = noC("#m_residence_mun").val();
					GetProvince(provID, Id);
				 }, 500);
			}


			else if(Id == "f_residence_prov"){
				GetMunicipality(Check, Id);
				setTimeout(function() { 
					var municipalID = noC("#f_residence_mun").val();
					GetBarangay(municipalID, 'f_residence_mun');
				 }, 500);
			}
			else if(Id == "f_residence_mun"){
				GetBarangay(Check, Id);
				setTimeout(function() { 
					var provID = noC("#f_residence_mun").val();
					GetProvince(provID, Id);
				 }, 500);
			}
			else if(Id == "f_residence_brgy"){
				GetReverseMunicipality(Check, Id);
				setTimeout(function() { 
					var provID = noC("#f_residence_mun").val();
					GetProvince(provID, Id);
				 }, 500);
			}
		}

		function GetProvince(province, element) {
			if(element == "m_residence_brgy"){
				element = "m_residence_prov";
			}
			if(element == "f_residence_brgy"){
				element = "f_residence_prov";
			}
			if(element == "m_residence_mun"){
				element = "m_residence_prov";
			}
			if(element == "f_residence_mun"){
				element = "f_residence_prov";
			}

			noC.ajax({
				type: "POST",
				url: "ajax/ajax_getResidence.php",
				data:'province='+province,
				success: function(data){
					noC("#"+element).html(data);
				}
			});
		}

		function GetMunicipality(municipality, element) {
			if(element == "m_residence_prov"){
				element = "m_residence_mun";
			}
			if(element == "f_residence_prov"){
				element = "f_residence_mun";
			}

			noC.ajax({
				type: "POST",
				url: "ajax/ajax_getResidence.php",
				data:'municipality='+municipality,
				success: function(data){
					noC("#"+element).html(data);
				}
			});
		}
		function GetBarangay(barangay, element) {
			if(element == "m_residence_mun"){
				element = "m_residence_brgy";
			}
			if(element == "f_residence_mun"){
				element = "f_residence_brgy";
			}

			noC.ajax({
				type: "POST",
				url: "ajax/ajax_getResidence.php",
				data:'barangay='+barangay,
				success: function(data){
					noC("#"+element).html(data);
				}
			});
		}
		function GetReverseMunicipality(revmunicipality, element) {
			if(element == "m_residence_brgy"){
				element = "m_residence_mun";
			}
			if(element == "f_residence_brgy"){
				element = "f_residence_mun";
			}

			noC.ajax({
				type: "POST",
				url: "ajax/ajax_getResidence.php",
				data:'revmunicipality='+revmunicipality,
				success: function(data){
					noC("#"+element).html(data);
				}
			});
		}
</script>
<!-- END Carl -->
</body>
</html>
