<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

#added by VAN 01-06-2015
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// clean the output buffer
ob_clean();
/**
* CARE2X Integrated Hospital Information System beta 2.0.1 - 2004-07-04
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
#echo "start";

#--------------- EDITED BY VANESSA -----------------------

$lang_tables[]='departments.php';
$lang_tables[]='prompt.php';
$lang_tables[]='help.php';
$lang_tables[]='person.php';
define('LANG_FILE','aufnahme.php');

#commented by VAN 01-25-08
$local_user='aufnahme_user';

require($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/inc_date_format_functions.php');

require_once($root_path.'include/care_api_classes/class_person.php');

require_once($root_path.'include/care_api_classes/class_insurance.php');

require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

require_once($root_path.'include/care_api_classes/class_personell.php');

include_once($root_path.'include/care_api_classes/class_department.php');

include_once $root_path . 'include/inc_ipbm_permissions.php';

$dept_obj=new Department;
$pers_obj=new Personell;
$ward_obj=new Ward;

#added by bryan 02-24-09
require_once($root_path.'include/care_api_classes/class_vitalsign.php');
$vitals_obj = new SegVitalsign();

#added by VAN 05-13-08
require_once($root_path.'include/care_api_classes/class_social_service.php');
$objSS = new SocialService;

#added by VAN 11/19/2013
require_once($root_path . 'include/care_api_classes/emr/class_emr.php');
$emr_obj = new EMR;

require_once($root_path . 'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

$row_hosp = $objInfo->getAllHospitalInfo();
$EMR_address = $row_hosp['EMR_address'];
$EMR_directory = $row_hosp['EMR_directory'];

#Added by Gervie 02/24/2016
require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);

$allow_er_location = $acl->checkPermissionRaw(array('_a_1_erlocation'));
#======================
#---------------ob-van----
#$x=ob_get_contents();
#ob_end_clean();
#var_dump($x);
#exit();
#---------------ob-van----

#-------added 03-07-07------------
global $db, $allow_updateDate, $allow_updateData;


if ($_GET['ptype']){
	$ptype = $_GET['ptype'];
	$orig_ptype = $_GET['ptype'];
}


$allow_updateDate=$allow_updateDate||$isIPBM;
#echo "b4 ptype = ".$ptype;

#if ($ptype=='newborn')
if (($ptype=='newborn') || ($_GET['istobeadmitted']==1))
	$ptype = 'ipd';
#echo "<br> cons ptype = ".$ptype;
#echo "<br>permi ipd = ".$allow_ipd_user||$isIPBM;
#echo "<br>permi opd = ".$allow_opd_user||$isIPBM;
#echo "<br>permi er = ".$allow_er_user||$isIPBM;

#$dept_belong = $dept_obj->getUserDeptInfo($HTTP_SESSION_VARS['sess_user_name']);
	if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
		$seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
	else
		$seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];

	$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name);

	$personell_nr = $dept_belong['personell_nr'];

	if (stristr($dept_belong['job_function_title'],'doctor')===FALSE)
		#echo "not doctor";
		$is_doctor = 0;
	else
		#echo "doctor";
		$is_doctor = 1;

#added by VAN 04-17-08
$seg_direct_admission = $_GET['seg_direct_admission']||($isIPBM&&$ptype=='ipd');

#---------------------------------

$thisfile=basename(__FILE__);
if($origin=='patreg_reg') $breakfile = 'patient_register_show.php'.URL_APPEND.'&pid='.$pid.'&ptype='.$ptype.$IPBMextend;
	#elseif($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path.'main/startframe.php'.URL_APPEND;
	elseif($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $breakfile = $root_path.'modules/registration_admission/aufnahme_daten_such.php'.URL_APPEND;
		elseif(!empty($HTTP_SESSION_VARS['sess_path_referer'])) $breakfile=$root_path.$HTTP_SESSION_VARS['sess_path_referer'].URL_APPEND.'&pid='.$pid.'&ptype='.$ptype;
			else $breakfile = "aufnahme_pass.php".URL_APPEND."&target=entry&ptype=".$ptype.$IPBMextend;

$newdata=1;

/* Default path for fotos. Make sure that this directory exists! */
$default_photo_path=$root_path.'fotos/registration';
$photo_filename='nopic';
$error=0;

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr=0;
if(!isset($mode)) $mode='';
if(!isset($forcesave)) $forcesave=0;
if(!isset($update)) $update=0;

if(!session_is_registered('sess_pid')) session_register('sess_pid');
if(!session_is_registered('sess_full_pid')) session_register('sess_full_pid');
if(!session_is_registered('sess_en')) session_register('sess_en');
if(!session_is_registered('sess_full_en')) session_register('sess_full_en');

$patregtable='care_person';  // The table of the patient registration data

$dbtable='care_encounter'; // The table of admission data

/* Create new person's insurance object */
$pinsure_obj=new PersonInsurance($pid);
/* Get the insurance classes */
if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
	$insurance_classes=&$pinsure_obj->getInsuranceClassInfoObject('class_nr,name,LD_var AS "LD_var"');
}
/* Create new person object */
$person_obj=new Person($pid);
/* Create encounter object */

$encounter_obj=new Encounter($encounter_nr);
/* Get all encounter classes */
if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
	$encounter_classes=$encounter_obj->AllEncounterClassesObject();
}

#-------added 03-08-07------------
#edited by VAN 04-17-08
if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
	$condition_classes=$encounter_obj->AllConditionClassesObject();
	$results_classes=$encounter_obj->AllResultsClassesObject();
	$disposition_classes=$encounter_obj->AllDispositionClassesObject();
}
#---------------

#added by VAN 04-29-08
if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
	$medico_cases = $encounter_obj->getMedicoCases();
}

if ($encounter_nr!=NULL){
	#echo "type = ".$encounter_type;
	$patient_enc = $encounter_obj->getPatientEncounter($encounter_nr);
	#echo $encounter_obj->sql;
	if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
		$patient_enc_cond = $encounter_obj->getPatientEncounterCond($encounter_nr);
		$patient_enc_disp = $encounter_obj->getPatientEncounterDisp($encounter_nr);
		$patient_enc_res = $encounter_obj->getPatientEncounterRes($encounter_nr);
		$patient_doc = $dept_obj->getDeptAllInfo($patient_enc['current_dept_nr']);
	}


	if (empty($patient_enc['consulting_dept_nr']))
		$dept_nr = $patient_enc['current_dept_nr'];
	else
		$dept_nr=$patient_enc['consulting_dept_nr'];

	if ($dept_nr)
		$patient_consulting_doc = $dept_obj->getDeptAllInfo($dept_nr);   # burn added : May 17, 2007

	$patient_dept_nr = $patient_enc['current_dept_nr'];

	#-------added by van 04-30-07--------
	#echo "name = ".$name1." - ".$name2." - ".$lname;
	if (((!empty($name1)) || (!empty($name2))) && (!empty($lname)))
		$dr_dept = $pers_obj->get_Dr_Dept_nr($name1, $name2, $lname);
	#echo "sql = ".$pers_obj->sql;
	$ref_dr = $dr_dept['personell_nr'];
	$ref_dept = $dr_dept['nr'];
	#-----------------------------------

	# burn added : May 24, 2007
	if ($patient_enc['encounter_status']=='direct_admission')
		$seg_direct_admission=1;   # set to indicate direct admission

	if (($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4)||($patient_enc['encounter_type']==IPBMIPD_enc)){
		$isadmitted = 1;
		if ($patient_enc['encounter_type']==3){
			$eclass = $LDStationary2;
		}elseif($patient_enc['encounter_type']==4){
			$eclass = $LDAmbulant2;
		}
		elseif($patient_enc['encounter_type']==IPBMIPD_enc){
			$eclass = "IPBM-IPD";
		}
	}else{
		$isadmitted = 0;
	}
	#echo "class = ".$eclass;
}
#--------------------------------
if($pid!='' || $encounter_nr!=''){

			/* Get the patient global configs */
				$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
				$glob_obj->getConfig('patient_%');
				$glob_obj->getConfig('person_foto_path');
				$glob_obj->getConfig('encounter_%');
 if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
		if(!$GLOBAL_CONFIG['patient_service_care_hide']){
			/* Get the care service classes*/
			$care_service=$encounter_obj->AllCareServiceClassesObject();
		}
		if(!$GLOBAL_CONFIG['patient_service_room_hide']){
			/* Get the room service classes */
			$room_service=$encounter_obj->AllRoomServiceClassesObject();
		}
		if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
			/* Get the attending doctor service classes */
			$att_dr_service=$encounter_obj->AllAttDrServiceClassesObject();
		}
 }
				/* Check whether config path exists, else use default path */
				$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;

				if ($pid)
				{
					#------------commented by vanessa 03-14-07--------------
				/*
					# Check whether the person is currently admitted. If yes jump to display admission data
					if(!$update&&$encounter_nr=$encounter_obj->isPIDCurrentlyAdmitted($pid)){
						header('Location:aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry');
						exit;
					}
				*/

			 /* Get the related insurance data */
			 #----comment by VAN
			if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){
			 $p_insurance=&$pinsure_obj->getPersonInsuranceObject($pid);
						 #echo $pinsure_obj->sql;
			 if($p_insurance==false) {
				$insurance_show=true;
			 } else {
				if(!$p_insurance->RecordCount()) {
						$insurance_show=true;
				} elseif ($p_insurance->RecordCount()==1){
						$buffer= $p_insurance->FetchRow();
					extract($buffer);
						$insurance_show=true;
						$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);
				} else { $insurance_show=false;}
			 }
			}

			#echo "<br>doi = ".$HTTP_POST_VARS['DOI'];
			#echo "<br>toi = ".$HTTP_POST_VARS['TOI'];
             
			if (!$HTTP_POST_VARS['is_medico']){
					$HTTP_POST_VARS['is_medico'] = '0';
					$HTTP_POST_VARS['POI'] = "";
					$HTTP_POST_VARS['TOI'] = "";
					$HTTP_POST_VARS['DOI'] = "";
			}else{
				#added by VAN 06-12-08
				if ((!empty($HTTP_POST_VARS['DOI']))&&($HTTP_POST_VARS['DOI']!='0000-00-00'))
					$HTTP_POST_VARS['DOI'] = date("y-m-d",strtotime($HTTP_POST_VARS['DOI']));
				else
					$HTTP_POST_VARS['DOI'] = "";

				$time = $HTTP_POST_VARS['TOI'].":00 ".$HTTP_POST_VARS['selAMPM'];
				#echo "time = ".$time;
				if ((!empty($HTTP_POST_VARS['TOI']))&&($HTTP_POST_VARS['TOI']!='00:00:00'))
					$HTTP_POST_VARS['TOI'] = date("H:i:s",strtotime($time));
				else
					$HTTP_POST_VARS['TOI'] = "";
			}

			if (!$HTTP_POST_VARS['category']){
				$HTTP_POST_VARS['category'] = 4;
			}
			#------------------

			#------------------added by VAN 02-16-09----------------
					if ($encounter_nr)
						$enc_info = $encounter_obj->getEncounterDept($encounter_nr);

						

						#echo "<br>encounter_type = ".$enc_info['encounter_type'];

						#reset the encounter nr if patient from opd is admitted
						if (($update)&&(($enc_info['encounter_type']==2)||($enc_info['encounter_type']==1)||($enc_info['encounter_type']==IPBMOPD_enc))&&(($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))){
						$db->StartTrans();


								$date = date("Y-m-d",strtotime($HTTP_POST_VARS['admission_date']));
								$time = $HTTP_POST_VARS['admission_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
								$time = date("H:i:s",strtotime($time));
								$admissiondt = $date." ".$time;
								$admissiondt = date("Y-m-d H:i:s",strtotime($admissiondt));
								/*$HTTP_POST_VARS['admission_dt'] = $admissiondt;*/
								$encounter_obj->setIsDischarged($encounter_nr,$date,$time);

					            # added by VAS 11/19/2013
					            # integration to EMR starts here	
					            # close case in EMR
					            try {
					                require_once($root_path . 'include/care_api_classes/emr/services/EncounterEmrService.php');
					                $encService = new EncounterEmrService;
					                $encService->closePatientEncounter($pid, $encounter_nr);

									require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
					                $ehr_patientType = "";
					                switch ($ptype) {
					                	case 'ipd':
					                		$ehr_patientType = 'ipe';
					                		break;
					                	case 'opd':
					                		$ehr_patientType = 'ope';
					                		break;
					                	case 'er':
					                		$ehr_patientType = 'ere';
					                		break;
					                	case 'phs':
					                		$ehr_patientType = 'phs';
					                		break;
					                	default:
					                		$ehr_patientType = '';
					                		break;
					                }

									$dischargeData = array(
								    	'encounter_nr'	=> $encounter_nr,
								    	'discharged_dt'	=> $admissiondt,
								    	'status'		=> "discharged"
								    );
									$ehr = Ehr::instance();
									$patient = $ehr->postDischargedPatient($dischargeData);
					            } catch (Exception $exc) {
					            	error_log("Unable to save to EHR. Error: ".$exc, 0);
					                //echo $exc->getTraceAsString();
					            }
            					#===========================

								$update =0;
								$encounter_nr = "";

								if ($enc_info['encounter_type']==1)
										$HTTP_POST_VARS['encounter_class_nr'] = 1;
								elseif ($enc_info['encounter_type']==2)
										$HTTP_POST_VARS['encounter_class_nr'] = 2;

								$HTTP_POST_VARS['parent_encounter_nr'] = $enc_info['encounter_nr'];

								$opdfromipd = 1;

						$db->CompleteTrans();
					}else {
$db->FailTrans();
						}

			#------------------------------

            #added by VAN 10-12-2011
            if ($HTTP_POST_VARS['smoker'])
               $HTTP_POST_VARS['smoker_history'] = $HTTP_POST_VARS['smoker'];
                                    
            if ($HTTP_POST_VARS['drinker'])
               $HTTP_POST_VARS['drinker_history'] = $HTTP_POST_VARS['drinker'];    
            #----------------------

           if ($HTTP_POST_VARS['DEPOvaccine'])
               $HTTP_POST_VARS['DEPOvaccine_history'] = $HTTP_POST_VARS['DEPOvaccine'];
            
			if (($mode=='save') || ($forcesave!=''))
						{
					 #echo "sulod diri:mode =".$mode." - ".!$forcesave;
							/*print_r($HTTP_POST_VARS);
							die('testing');*/
				 if(!$forcesave)
				 {
										//clean and check input data variables
						/**
						*  $error = 1 will cause to show the "save anyway" override button to save the incomplete data
						*  $error = 2 will cause to force the user to enter a data in an input element (no override allowed)
						*/
										$encoder=trim($encoder);
										if($encoder=='') $encoder=$HTTP_SESSION_VARS['sess_user_name'];

					}
					if(!$error)
					{

						if(!$GLOBAL_CONFIG['patient_service_care_hide']){
								if(!empty($sc_care_start)) $sc_care_start=formatDate2Std($sc_care_start,$date_format);
								if(!empty($sc_care_end)) $sc_care_end=formatDate2Std($sc_care_end,$date_format);
								$care_class=compact('sc_care_nr','sc_care_class_nr', 'sc_care_start', 'sc_care_end','encoder');
						}
						if(!$GLOBAL_CONFIG['patient_service_room_hide']){
								if(!empty($sc_room_start)) $sc_room_start=formatDate2Std($sc_room_start,$date_format);
								if(!empty($sc_room_end)) $sc_room_end=formatDate2Std($sc_room_end,$date_format);
								$room_class=compact('sc_room_nr','sc_room_class_nr', 'sc_room_start', 'sc_room_end','encoder');
						}
						if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
								if(!empty($sc_att_dr_start)) $sc_att_dr_start=formatDate2Std($sc_att_dr_start,$date_format);
								if(!empty($sc_att_dr_end)) $sc_att_dr_end=formatDate2Std($sc_att_dr_end,$date_format);
								$att_dr_class=compact('sc_att_dr_nr','sc_att_dr_class_nr','sc_att_dr_start', 'sc_att_dr_end','encoder');
						}
						#echo "<br>update = ".$update;
						#echo "<br>encounter_nr = ".$encounter_nr;
						#echo "<br>bol = ".($update || $encounter_nr);
							if(($update || $encounter_nr))// && !$_GET['istobeadmitted'])
						{
					$groupName  = $encounter_obj->getDepartmentGroupName($HTTP_POST_VARS['current_dept_nr']);
					$room_name  = $encounter_obj->getPatientRoom($HTTP_POST_VARS['current_room_nr'],$HTTP_POST_VARS['current_ward_nr']);

					
					// if($response = $curl_ehr->assignPatientDepartment($assignPatientData)){
					// 	// var_dump($response); die();
					// }else{	
					// 	die("No response");
					// }
							#echo "true";
							$itemno=$itemname;
									$HTTP_POST_VARS['modify_id']=$encoder;

									$current_room = $encounter_obj->getRoom($HTTP_POST_VARS['encounter_nr']);
									$current_room = $current_room->FetchRow();
									$current_room = $current_room['location_nr'];

									$current_location = $encounter_obj->getLocation($HTTP_POST_VARS['encounter_nr']);
									$current_location = $current_location->FetchRow();
									$current_location = $current_location['location_nr'];

									$current_ward_name = $encounter_obj->getCurrentWard($HTTP_POST_VARS['encounter_nr']);
									$current_ward_name = $current_ward_name->FetchRow();
									$current_ward_name = $current_ward_name['name'];

									$ward_name = $encounter_obj->getWardName($HTTP_POST_VARS['current_ward_nr']);
									$ward_name = $ward_name->FetchRow();
									$ward_name = $ward_name['ward_name'];

									$new_room = $HTTP_POST_VARS['current_room_nr'];
									$new_location = $HTTP_POST_VARS['current_bed_nr'];
							
									$defaultLocation = 0;
									$defaultRoom = 0;

									if($new_room == $defaultRoom) {
										$new_room = $current_room;
									}

									if($new_location==NULL) {
										$new_location = $defaultLocation;
									}

									if($dbtype=='mysql'){
										$HTTP_POST_VARS['history']= "CONCAT(history,\"Update: ".date('m-d-Y H:i:s')." = $encoder\n\")";
									}
									// else{
									// 	$HTTP_POST_VARS['history']= "(history || 'Update: ".date('m-d-Y H:i:s')." = $encoder\n')";
									// }
									if($current_ward_name != $ward_name){
										$HTTP_POST_VARS['history']= "CONCAT(history,\"Update ward to ".$ward_name." ".date('m-d-Y H:i:s')." = $encoder\n\")";
									}
									if(($current_ward_name == $ward_name) && ($current_location != $new_location || $current_room != $new_room)){
										$HTTP_POST_VARS['history']= "CONCAT(history,\"Update ward ".date('m-d-Y H:i:s')." = $encoder\n\")";
									}

									if(isset($HTTP_POST_VARS['encounter_nr'])) unset($HTTP_POST_VARS['encounter_nr']);
									if(isset($HTTP_POST_VARS['pid'])) unset($HTTP_POST_VARS['pid']);

								#------------edited by vanessa 03-27-07--------
									if ($informant_name==NULL)
										$HTTP_POST_VARS['informant_name']= " ";
									if ($info_address==NULL)
										$HTTP_POST_VARS['info_address']= " ";
									if ($relation_informant==NULL)
										$HTTP_POST_VARS['relation_informant']= " ";
									if ($current_ward_nr==NULL)
										$HTTP_POST_VARS['current_ward_nr']= " ";
									if ($referrer_diagnosis==NULL)
										$HTTP_POST_VARS['referrer_diagnosis']= " ";
									if ($referrer_recom_therapy==NULL)
										$HTTP_POST_VARS['referrer_recom_therapy']= " ";
									if (($referrer_dr==NULL)||($dr_dept['personell_nr']==NULL)){
										$HTTP_POST_VARS['referrer_dr']= $referrer_dr_name;
										$HTTP_POST_VARS['referrer_dept']= " ";
									}elseif ($dr_dept['personell_nr']!=NULL){
										#echo "dr = ".$dr_dept['personell_nr'];
										#uncommented by VAN
										$HTTP_POST_VARS['referrer_dr'] = $dr_dept['personell_nr'];   # burn commented : May 17, 2007
										$HTTP_POST_VARS['referrer_dept'] = $dr_dept['nr'];   # burn commented : May 17, 2007
									}elseif($referrer_dr!=NULL){
										$HTTP_POST_VARS['referrer_dr']= $referrer_dr;
									}
									if ($referrer_institution==NULL)
										$HTTP_POST_VARS['referrer_institution']= " ";
									if ($referrer_notes==NULL)
										$HTTP_POST_VARS['referrer_notes']= " ";
								#-----------------------------------------------
                                   
										# burn added: March 28, 2007
									#commented by VAN 07-04-08
									$HTTP_POST_VARS['admission_dt']=NULL;
									if ( ($HTTP_POST_VARS['firstUpdate']) &&
											(($HTTP_POST_VARS['encounter_type']==3)||($HTTP_POST_VARS['encounter_type']==4)||($HTTP_POST_VARS['encounter_type']==IPBMIPD_enc))){
										#commented by VAN 07-04-08
										#$HTTP_POST_VARS['admission_dt']=date('Y-m-d H:i:s');
									}

									#added by VAN 01-21-09
									if (isset($HTTP_POST_VARS['admission_date'])){
										$date = date("Y-m-d",strtotime($HTTP_POST_VARS['admission_date']));
										$time = $HTTP_POST_VARS['admission_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
										$time = date("H:i:s",strtotime($time));
										$admissiondt = $date." ".$time;
										$admissiondt = date("Y-m-d H:i:s",strtotime($admissiondt));
										$HTTP_POST_VARS['admission_dt'] = $admissiondt;
										$HTTP_POST_VARS['encounter_date'] = $admissiondt;
									}
									#------------------

									#added by VAN 01-21-09
									if (isset($HTTP_POST_VARS['encounter_dt'])){
										$date = date("Y-m-d",strtotime($HTTP_POST_VARS['encounter_dt']));
										$time = $HTTP_POST_VARS['encounter_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
										$time = date("H:i:s",strtotime($time));
										$enc_date = $date." ".$time;
										$HTTP_POST_VARS['encounter_date'] = date("Y-m-d H:i:s",strtotime($enc_date));
									}
									#----------------------

									#added by VAN 09-01-2011
									#update the encounter date and admission date of admission transaction and discharged date from ER
									if ($HTTP_POST_VARS['isdateupdate']){
										$HTTP_POST_VARS['encounter_date'] = $admissiondt;
										#$er_discharged_date = date("Y-m-d",strtotime($admissiondt));
										#$er_encounter_date = $parent_row['encounter_date'];
										#$parent_encounter_nr = $HTTP_POST_VARS['parent_encounter_nr'];
									}

									#print_r($HTTP_POST_VARS);
									#echo "<br>ed upd= ".$HTTP_POST_VARS['encounter_date'];
									#echo "<br>ed up = ".$HTTP_POST_VARS['admission_dt'];
									#exit();
										# burn added: June 1, 2007
										# first entry, consulting and current dept/doctor are the same,
										# except for direct admission
									if (($HTTP_POST_VARS['encounter_type']==1)||($HTTP_POST_VARS['encounter_type']==2)||($HTTP_POST_VARS['encounter_type']==IPBMOPD_enc)){
										$HTTP_POST_VARS['consulting_dept_nr']=$HTTP_POST_VARS['current_dept_nr'];
										$HTTP_POST_VARS['consulting_dr_nr']=$HTTP_POST_VARS['current_att_dr_nr'];
									}

									#-------added by VAN 09-07-07------------
									if ($_POST["items"]==NULL){
										$HTTP_POST_VARS['insurance_class_nr']=3;
									}
									#-------------------------------

									#added by VAN 04-17-08
									#echo "direct = ".$seg_direct_admission."<br>";
									if ($seg_direct_admission==1 && strtolower(trim($_GET['ptype'])) == "ipd"){
										if($isIPBM) $HTTP_POST_VARS['encounter_status']='ipbm';
										else $HTTP_POST_VARS['encounter_status']='direct_admission';
									}
									elseif(($current_ward_nr)||($current_room_nr)||($current_bed_nr))
										$HTTP_POST_VARS['encounter_status']='disallow_cancel';
									elseif($ptype=='phs')
										$HTTP_POST_VARS['encounter_status'] = 'phs';
									else{
										if($isIPBM) $HTTP_POST_VARS['encounter_status']='ipbm';
										else $HTTP_POST_VARS['encounter_status']='';
									}
									#------------------------------

									#added by VAN 05-21-2010
									$HTTP_POST_VARS['current_ward_nr'] = $HTTP_POST_VARS['prev_ward_nr'];
									$HTTP_POST_VARS['current_room_nr'] = $HTTP_POST_VARS['prev_room_nr'];

									# Added by: Jeff
									# Date: 10-24-2017
									# Purpose: To get the or_id instead of or_desc using user defined function upon update
									$official_rnr = $encounter_obj->getOPDTempeIDnum($HTTP_POST_VARS['official_receipt_nr']);

									if ($official_rnr) {
										$HTTP_POST_VARS['official_receipt_nr'] = $official_rnr;
									}else{
									$HTTP_POST_VARS['official_receipt_nr'] = stripslashes($HTTP_POST_VARS['official_receipt_nr']);
									}
									
									$encounter_obj->setDataArray($HTTP_POST_VARS);

									#echo "medico = ".$HTTP_POST_VARS['is_medico'];
									if (!$HTTP_POST_VARS['is_medico']){
										$HTTP_POST_VARS['is_medico'] = '0';
										#added by VAN 06-12-08
										$HTTP_POST_VARS['POI'] = "";
										$HTTP_POST_VARS['TOI'] = "";
										$HTTP_POST_VARS['DOI'] = "";
									}

									if (!$HTTP_POST_VARS['is_DOA']){
										$HTTP_POST_VARS['is_DOA'] = '0';
										$HTTP_POST_VARS['is_DOA_reason'] = "";
									}else{
										$HTTP_POST_VARS['is_DOA'] = '1';
									}

									if (!$HTTP_POST_VARS['is_confidential']){
										$HTTP_POST_VARS['is_confidential'] = '0';
									}else{
										$HTTP_POST_VARS['is_confidential'] = '1';
									}

									if ($HTTP_POST_VARS['accomodation_type']==2){
										$HTTP_POST_VARS['area'] = "";
									}

									$HTTP_POST_VARS['er_location'] = $HTTP_POST_VARS['er_location'];
									$HTTP_POST_VARS['er_location_lobby'] = $HTTP_POST_VARS['er_location_lobby'];

									// Added by Gervie 04-14-2017
									$impressionData = array(
											'encounter_nr' => $encounter_nr,
											'clinicalInfo' => $HTTP_POST_VARS['er_opd_diagnosis'],
											'Location' => 'AD'
										);

									$encounter_obj->saveToClinicalImpressionTable($impressionData);
									// End


									#echo "date = ".$HTTP_POST_VARS['admission_dt'];

                    if ($encounter_obj->updateEncounterFromInternalArray($encounter_nr)) {

                        # added by VAS 11/19/2013
                        # integration to EMR starts here
                        # for doctor only
                        # Post corresponding Patient's Demographic demographic in EMR
                        $_POST['pid'] = $pid;

                        /**
                         * edited by marc lua 3/6/2014
                         * edited by justin tan 12/09/14
                         */
                        require_once($root_path . 'include/care_api_classes/emr/services/EncounterEmrService.php');
                        // require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');
                        $encService = new EncounterEmrService();
                        // $patientService = new PatientEmrService();

						require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
		                $ehr = Ehr::instance();
                        try {
                        	//update encounter
							$ehr_patientType = "";
							switch ($ptype) {
			                	case 'ipd':
			                		$ehr_patientType = 'ipe';
			                		break;
			                	case 'opd':
			                		$ehr_patientType = 'ope';
			                		break;
			                	case 'er':
			                		$ehr_patientType = 'ere';
			                		break;
			                	case 'phs':
			                		$ehr_patientType = 'phs';
			                		break;
			                	default:
			                		$ehr_patientType = '';
			                		break;
			                }

                        	$assignPatientData = array(
								"groupName"			=>		$groupName,
								"er_areaid"			=>		$HTTP_POST_VARS['current_dept_nr'],
								"personnel_nr"		=>		$HTTP_POST_VARS['current_att_dr_nr'],
								"pid"				=>		$pid,
								"encounter_nr"		=>		$encounter_nr,
								"date"				=>		$HTTP_POST_VARS['encounter_date'].' '.$HTTP_POST_VARS['encounter_time'].":00 ".$HTTP_POST_VARS['selAMPM2'],
								"room_name"			=>		$room_name,
								"patient_type"		=>		$ehr_patientType,
								"er_opd_diagnosis"	=>		$HTTP_POST_VARS['er_opd_diagnosis'],
								"parent_encounter_nr" => 	$HTTP_POST_VARS['parent_encounter_nr']
							);	

			                $utf8_encode_data = array_map("utf8_encode", $assignPatientData);
			                $patient = $ehr->postAssignPatient($utf8_encode_data);

                            $encService->saveEncounter($pid, $encounter_nr, 1);
                            // }
                        } catch (Exception $exc) {
                        	error_log("Unable to update to EHR. Error: ".$exc, 0);
                            //echo $exc->getTraceAsString();
                        }
                        #===============================
						##############  added by bryan on 2/24/09 for vitals  ##############
						$sqlcheck = "SELECT vitalsign_no from seg_encounter_vitalsigns where vitalsign_no=".$_GET['vital_no'];

						$sqlresult = $db->Execute($sqlcheck);

						#edited by VAN 10-13-09
						if ((($_POST['vital_bp_sys'])&&($_POST['vital_bp_dias']))||($_POST['vital_t'])||($_POST['vital_w'])
								 ||($_POST['vital_rr'])||($_POST['vital_pr'])){
							if($db->Affected_Rows() > 0) {
								$dataVital = array(
									'ecounter_nr' => $encounter_nr,
									'date' => date('Y-m-d H:i:s'),
									'pid' => $pid,
									'systole' => $_POST['vital_bp_sys'],
									'diastole' => $_POST['vital_bp_dias'],
									'temp' => $_POST['vital_t'],
									'weight' => $_POST['vital_w'],
									'resp_rate' => $_POST['vital_rr'],
									'pulse_rate' => $_POST['vital_pr'],
									'modify_id' => $_SESSION['sess_temp_userid'],
									'modify_dt' => date('Y-m-d H:i:s')
								);

								$vitals_obj->prepareVitals();

								$PNo = $_GET['vital_no'];

								$dataVital["history"]=$vitals_obj->ConcatHistory("Update: ".date('m-d-Y H:i:s')." [".$_SESSION['sess_temp_userid']."]\n");

								$vitals_obj->setDataArray($dataVital);

								$vitals_obj->where = "vitalsign_no=".$db->qstr($_GET['vital_no']);

								$saveok=$vitals_obj->updateDataFromInternalArray($PNo,FALSE);
							}else{
								$dataVital = array(
									'encounter_nr' => $encounter_nr,
									'date' => date('Y-m-d H:i:s'),
									'pid' => $pid,
									'systole' => $_POST['vital_bp_sys'],
									'diastole' => $_POST['vital_bp_dias'],
									'temp' => $_POST['vital_t'],
									'weight' => $_POST['vital_w'],
									'resp_rate' => $_POST['vital_rr'],
									'pulse_rate' => $_POST['vital_pr'],
									'history' => "Create: ".date('m-d-Y H:i:s')." [".$_SESSION['sess_temp_userid']."]\n",
									'modify_id' => $_SESSION['sess_temp_userid'],
									'modify_dt' => date('Y-m-d H:i:s')
								);

								$vitals_obj->prepareVitals();

								$PNo = $_GET['vitdate'];

								$dataVital['create_id']=$_SESSION['sess_temp_userid'];
								$dataVital['create_dt']= date('Y-m-d H:i:s');
								$vitals_obj->setDataArray($dataVital);
								$saveok=$vitals_obj->insertDataFromInternalArray();
							}
						} #end of if # VAN 10-13-09

						#added by VAN 09-01-2011
						#if yes to be updated
						if ($HTTP_POST_VARS['isdateupdate']){
							#if admission is earlier than the encounter date
							$consult_date = '';
							if (strtotime($HTTP_POST_VARS['admission_dt']) < strtotime($HTTP_POST_VARS['parent_encounter_date'])){

								$date = $HTTP_POST_VARS['admission_dt'];
								$consult_date = date( "Y-m-d H:i:s", strtotime( "$date -1 hour" ));
							}

							#sync the discharged date and encounter date of ER Consultation Entry to admission date of the patient
							$encounter_obj->updateERdischargeDate($HTTP_POST_VARS['parent_encounter_nr'], $HTTP_POST_VARS['admission_dt'], $consult_date);
						}

						#echo "<br>sulod nxt";
						#echo "<br>sql = ".$encounter_obj->sql;
						$encounter_obj->updateConfidential($encounter_nr,$HTTP_POST_VARS['is_confidential']);
										
						if ($encounter_type!=2&&$encounter_type!=IPBMOPD_enc){

						 	$encounter_obj->updateDOA($encounter_nr,$HTTP_POST_VARS['is_DOA'],$HTTP_POST_VARS['is_DOA_reason']);
					 		if (($encounter_type==3)||($encounter_type==4)||($encounter_type==IPBMIPD_enc)) {
								$encounter_obj->updateWardArea($encounter_nr,$HTTP_POST_VARS['area']);
						 	}
							#added by VAN 06-12-08
							$encounter_obj->updateIncident($encounter_nr,$HTTP_POST_VARS['POI'],$HTTP_POST_VARS['TOI'],$HTTP_POST_VARS['DOI']);
							$chksatus = $person_obj->selectDeadDateStatus($encounter_nr);
											// var_dump($_POST['is_DOA']);
											// var_dump($_POST['is_DOAs']);
											// exit();
							#echo "date = ".$encounter_date;
							if ($_POST['is_DOA']==1){
								$person_obj->updateDeathDate($pid,date("Y-m-d",strtotime($HTTP_POST_VARS['encounter_date'])),$encounter_nr,date("H:i:s", strtotime($HTTP_POST_VARS['encounter_date'])));
									if($_POST['is_DOA']!=$_POST['is_DOAs']){
										$person_obj->updateDeathDate1($pid,$encounter_nr);
									}
								// $person_obj->updateDeathDate1($pid,date("Y-m-d"),$encounter_nr,date("H:i:s"));
							}else{

								$person_obj->updateDeathDate($pid,'0000-00-00',$encounter_nr,'00:00:00');
								// Added by Matsuu 12042016
								if($_REQUEST['result_code']==4){
									if ($chksatus['result_code'] !=4)
										$person_obj->updateDeathDate1($pid,$encounter_nr);
								}
								elseif($_REQUEST['result_code']!=4){
									if($chksatus['result_code']==4){	
										 $person_obj->updateDeathDate2($pid,$encounter_nr);
										 }
									elseif($_POST['is_DOA']!=$_POST['is_DOAs']){
										 $person_obj->updateDeathDate2($pid,$encounter_nr);
									}
								}
								
								// Ended by Matsuu 12042016
							}
											// var_dump($result_code);die();

							#added by VAN 04-28-08
							#if ($dept_belong['id']=="ER"){
							if (($allow_er_user||$isIPBM)&&(($ptype=='er')||($ptype=='ipd'))&&($HTTP_POST_VARS['is_medico'])){
								$encounter_obj->setMedico($HTTP_POST_VARS['is_medico'],$encounter_nr);

								$cases = array();
								if(is_object($medico_cases)){
									while($result=$medico_cases->FetchRow()) {
										if ($HTTP_POST_VARS['medico'.$result['code']]){
												if ($HTTP_POST_VARS['medico'.$result['code']]=='OT')
													 $desc =  $HTTP_POST_VARS['description'];
												else
													$desc = "none";
											#$cases[] = array($HTTP_POST_VARS['medico'.$result['code']],'');
											$cases[] = array($HTTP_POST_VARS['medico'.$result['code']],$desc);
										}
									}
								}

								#added by VAN 06-12-08
								if (!$HTTP_POST_VARS['is_medico']){
									$encounter_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
								}else{
									$encounter_obj->deleteMedicoCasesEncounter($encounter_nr,$pid);
									$encounter_obj->addMedicoCasesEncounter($encounter_nr,$pid,$cases);
								}
							}
										#----------------------------

										#edited by VAN 08-20-08
								#in assigning a room
								#if no changes are made, no transaction will be made in care_encounter_location
								#if there are changes, and it is a room transfer and not a correction, another transaction will be inserted in care_encounter_location
								#if there are changes, and it is not a room transfer and it is a correction, previous history will be deleted in care_encounter_location
								#if (($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
							if (($allow_ipd_user||$isIPBM)&&(($ptype=='ipd')||$ptype=='newborn')){
										#edited by VAN 05-21-2010
								$encounter_obj->startTrans();

								if (!$current_bed_nr){
									$current_bed_nr = 0;
									$in_ward = 0;
								}else
									$in_ward = 1;

								$source = 'ADMISSION';

										#in assigning a room
										#if no changes are made, no transaction will be made in care_encounter_location
										#if there are changes, and it is a room transfer and not a correction, another transaction will be inserted in care_encounter_location
										#if there are changes, and it is not a room transfer and it is a correction, previous history will be deleted in care_encounter_location

										#if saved ward, room and bed are not the same with the selected data
								if ($is_same==0){
								 	if ($assign_mode=="correct"){

									 	$room_date = $HTTP_POST_VARS['datefrom'];
										$room_time = $HTTP_POST_VARS['timefrom'].":00 ".$HTTP_POST_VARS['selAMPM3'];

										if ($room_date)
											$room_date = date("Y-m-d",strtotime($room_date));

										if	($room_time)
											$room_time = date("H:i:s",strtotime($room_time));

										$ok = $encounter_obj->deleteRecentRoomLocationIfCorrection($encounter_nr);
										
										if($ok = $encounter_obj->AdmitInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$source,$room_date,$room_time,1,'',1)){
											if($ok) $ok = $encounter_obj->setAdmittedInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,0,$in_ward,0,1);

											$roomInfo = $ward_obj->getRoomRate($current_room_nr, $current_ward_nr);
											$rate = $roomInfo['room_rate'];

											$location = $encounter_obj->getLatestLocNr($encounter_nr);
											$loc_enc_nr = $location['nr'];

											$encounter_obj->isExistInLocationRate($loc_enc_nr,$encounter_nr);
											$rec = $encounter_obj->count;

											if ($rec){
												$encounter_obj->setTransferredLocation($loc_enc_nr,$encounter_nr);
												$encounter_obj->setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
											}else{
												$encounter_obj->setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
											}
															#echo "<br>rate = ".$encounter_obj->sql;
									 	}
												 #die();
								 	}elseif ($assign_mode=="transfer"){
										$room_date = $HTTP_POST_VARS['datefrom'];
										$room_time = $HTTP_POST_VARS['timefrom'].":00 ".$HTTP_POST_VARS['selAMPM3'];

										if ($room_date)
											$date = date("Y-m-d",strtotime($room_date));
										else
											$date=date('Y-m-d');

										if	($room_time)
											$time = date("H:i:s",strtotime($room_time));
										else
											$time=date('H:i:s');

										#it is a room transfer

										if($assign_type=='ward'){
											$dis_type=4; # transfer of ward
										}elseif($assign_type=='room'){
											$dis_type=5; # transfer of room
										}elseif($assign_type=='bed'){
											$dis_type=6; # transfer of bed
										}

										if($ok = $encounter_obj->DischargeFromWard($encounter_nr,$dis_type,$date,$time)){
											if($ok = $encounter_obj->AdmitInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$source,$date,$time,1,'',1)){
												if($ok) $ok = $encounter_obj->setAdmittedInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,0,$in_ward,0,1);

												$roomInfo = $ward_obj->getRoomRate($current_room_nr, $current_ward_nr);
												$rate = $roomInfo['room_rate'];

												$location = $encounter_obj->getLatestLocNr($encounter_nr);
												$loc_enc_nr = $location['nr'];

												$encounter_obj->isExistInLocationRate($loc_enc_nr,$encounter_nr);
												$rec = $encounter_obj->count;

												if ($rec){
													$encounter_obj->setTransferredLocation($loc_enc_nr,$encounter_nr);
													$encounter_obj->setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
												}else{
													$encounter_obj->setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
												}
											}
										}
														#die();
									}elseif ($assign_mode=="delete"){
														#delete the recent location and make the previous location the recent
										if ($ok = $encounter_obj->deleteRecentRoomLocationIfCorrection($encounter_nr)){
															#echo "not same = ".$encounter_obj->sql;
											if ($ok = $encounter_obj->SetPrevRoomLocationToRecent($encounter_nr)){
												$new_ward_nr = $encounter_obj->getRecentWard($encounter_nr);
												$new_room_nr = $encounter_obj->getRecentRoom($encounter_nr);
												$current_ward_nr = $new_ward_nr['location_nr'];
												$current_room_nr = $new_room_nr['location_nr'];
												if($ok) $ok = $encounter_obj->setAdmittedInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,0,0,0,1);
											}
										}
														#doing nothing
									}
								}else{ #if ($is_same==1)
											 #if saved ward, room and bed are the same with the selected data
								 	if ($assign_mode=="delete"){
													#delete the recent location and make the previous location the recent
										if ($ok = $encounter_obj->deleteRecentRoomLocationIfCorrection($encounter_nr)){
															#echo "same = ".$encounter_obj->sql;
											if ($ok = $encounter_obj->SetPrevRoomLocationToRecent($encounter_nr)){
												$new_ward_nr = $encounter_obj->getRecentWard($encounter_nr);
												$new_room_nr = $encounter_obj->getRecentRoom($encounter_nr);
												$current_ward_nr = $new_ward_nr['location_nr'];
												$current_room_nr = $new_room_nr['location_nr'];
												if($ok) $ok = $encounter_obj->setAdmittedInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,0,0,0,1);
											}
										}
									}elseif ($assign_mode=="correct"){
										$room_date = $HTTP_POST_VARS['datefrom'];
										$room_time = $HTTP_POST_VARS['timefrom'].":00 ".$HTTP_POST_VARS['selAMPM3'];

										if ($room_date)
											$room_date = date("Y-m-d",strtotime($room_date));

										if	($room_time)
											$room_time = date("H:i:s",strtotime($room_time));

										$ok = $encounter_obj->deleteRecentRoomLocationIfCorrection($encounter_nr);
													 
									 	if($ok = $encounter_obj->AdmitInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$source,$room_date,$room_time,1,'',1)){
										 	if($ok) $ok = $encounter_obj->setAdmittedInWard($encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,0,$in_ward,0,1);

											$roomInfo = $ward_obj->getRoomRate($current_room_nr, $current_ward_nr);
											$rate = $roomInfo['room_rate'];

											$location = $encounter_obj->getLatestLocNr($encounter_nr);
											$loc_enc_nr = $location['nr'];

											$encounter_obj->isExistInLocationRate($loc_enc_nr,$encounter_nr);
											$rec = $encounter_obj->count;

											if ($rec){
												$encounter_obj->setTransferredLocation($loc_enc_nr,$encounter_nr);
												$encounter_obj->setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
											}else{
												$encounter_obj->setPatientRoomRate($loc_enc_nr,$encounter_nr,$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
											}
															#echo "<br>rate = ".$encounter_obj->sql;
								 		}
									}
								} #if ($is_same==0){

								if (!$ok) $encounter_obj->FailTrans();
								
								$encounter_obj->CompleteTrans();
										#die();
							}
						}
										#---------------------------

						/* Save the service classes */
						if(!$GLOBAL_CONFIG['patient_service_care_hide']){
							$encounter_obj->updateCareServiceClass($care_class);
					 	}
						
						if(!$GLOBAL_CONFIG['patient_service_room_hide']){
							$encounter_obj->updateRoomServiceClass($room_class);
						}
						
						if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
							$encounter_obj->updateAttDrServiceClass($att_dr_class);
						}

						#----------------save a data to seg_encounter_condition, disposition, and result-----------
						#if ($dept_belong['id']=="Admission"){

						if (($allow_ipd_user||$isIPBM)&&(($ptype=='ipd')||($ptype=='er'))){
							#---------condition----------------------
							if (isset($HTTP_POST_VARS['cond_code'])){
								$cond_prev = $encounter_obj->getEncounterConditionInfo($patient_enc_cond['cond_code']);
								$cond_current = $encounter_obj->getEncounterConditionInfo($cond_code);
								#echo "code = ".$cond_code;
								#echo "area 1 = ".$patient_enc_disp['encounter_nr'];
								#echo "<br>area 2 = ".$encounter_nr;
								if (($patient_enc_disp['encounter_nr']==$encounter_nr)&&($cond_current['area_used']==$cond_prev['area_used'])){
									#echo "code = ".$cond_code;
									$condition['cond_code']=$cond_code;
									$condition['modify_id']=$encoder;
									$condition['modify_time']=date('YmdHis');
									$encounter_obj->setDataArray($condition);

									 if(!@$encounter_obj->updateEncounterCondition($encounter_nr,$patient_enc_cond['cond_code'])) echo "<br>$LDDbNoSave";
								}else{
									$condition['encounter_nr'] = $encounter_nr;
									$condition['cond_code']=$cond_code;
									$condition['modify_id']=$encoder;
									$condition['modify_time']=date('YmdHis');
									$condition['create_id']=$encoder;
									$condition['create_time']=date('YmdHis');
									$encounter_obj->setDataArray($condition);

									if(!@$encounter_obj->saveEncounterCondition($condition)) echo "<br>$LDDbNoSave";
								}
							}
							#----------disposition----------------------
							if(isset($HTTP_POST_VARS['disp_code'])){
								$disp_prev = $encounter_obj->getEncounterDispositionInfo($patient_enc_disp['disp_code']);
								$disp_current = $encounter_obj->getEncounterDispositionInfo($disp_code);

								if (($patient_enc_disp['encounter_nr']==$encounter_nr)&&($disp_current['area_used']==$disp_prev['area_used'])){

									$disposition['disp_code']=$disp_code;
									$disposition['modify_id']=$encoder;
									$disposition['modify_time']=date('YmdHis');
									$encounter_obj->setDataArray($disposition);

									if(!@$encounter_obj->updateEncounterDisposition($encounter_nr,$patient_enc_disp['disp_code'])) echo "<br>$LDDbNoSave";

								}else{
									$disposition['encounter_nr'] = $encounter_nr;
									$disposition['disp_code']=$disp_code;
									$disposition['modify_id']=$encoder;
									$disposition['modify_time']=date('YmdHis');
									$disposition['create_id']=$encoder;
									$disposition['create_time']=date('YmdHis');
									$encounter_obj->setDataArray($disposition);

									if(!@$encounter_obj->saveEncounterDisposition($disposition)) echo "<br>$LDDbNoSave";
								}
							}

							#----------results-------------------------

							if(isset($HTTP_POST_VARS['result_code'])){
								$res_prev = $encounter_obj->getEncounterResultInfo($patient_enc_res['result_code']);
								$res_current = $encounter_obj->getEncounterResultInfo($result_code);

								if (($patient_enc_res['encounter_nr']==$encounter_nr)&&($res_current['area_used']==$res_prev['area_used'])){
									$result['result_code']=$result_code;
									$result['modify_id']=$encoder;
									$result['modify_time']=date('YmdHis');
									$encounter_obj->setDataArray($result);

									if(!@$encounter_obj->updateEncounterResults($encounter_nr, $patient_enc_res['result_code'])) echo "<br>$LDDbNoSave";
								}else{
									$result['encounter_nr'] = $encounter_nr;
									$result['result_code']=$result_code;
									$result['modify_id']=$encoder;
									$result['modify_time']=date('YmdHis');
									$result['create_id']=$encoder;
									$result['create_time']=date('YmdHis');
									$encounter_obj->setDataArray($result);

									if(!@$encounter_obj->saveEncounterResults($result)) echo "<br>$LDDbNoSave";
								}
							}
							#---------added by VAN 090107------
											#---------commented by justin 3/18/15------
											#----------INSURANCE--------
											// if (($encounter_type==3)||($encounter_type==4)){
											// 	if ($insurance_class_nr!=3){
											// 		#-----with insurance---
											// 		if ($_POST["items"]!=NULL){
											// 			$bulk_hcare = array();
											// 			$bulk_insurance_nr = array();
											// 			foreach (array_unique($_POST["items"]) as $i=>$v) {
											// 				#------------------hcare_id, insurance_nr, is principal holder-----
											// 				$bulk[] = array($_POST["items"][$i],$_POST["nr"][$i],$_POST["is_principal"][$i]);
											// 				$bulk_hcare[] = array($_POST["items"][$i]);
											// 				$current_array .= $_POST["items"][$i].",";
											// 			}

											// 			$current_array = substr($current_array,0,strlen($current_array)-1);

											// 			$pinsure_obj->clearInsuranceList($encounter_nr);	#clear seg_encounter_insurance table
											// 			$pinsure_obj->addInsurance($encounter_nr,$bulk_hcare,$encoder,date('YmdHis'));
											// 			$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$current_array.") AND pid = ".$pid;
											// 			$ok = $db->Execute($delete_result);
											// 			$pinsure_obj->addInsurance_reg($pid,$bulk,$encoder,date('YmdHis'),$insurance_class_nr);

											// 		}else{
											// 			$pinsure_obj->clearInsuranceList($encounter_nr); #clear seg_encounter_insurance table
											// 			$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$insurance_array_prev.") AND pid = ".$pid;
											// 			$ok = $db->Execute($delete_result);
											// 		}
											// 	}elseif ($insurance_class_nr==3){
											// 		#-----self-pay------
											// 		$pinsure_obj->clearInsuranceList($encounter_nr);		#clear seg_encounter_insurance table
											// 		#$pinsure_obj->clearInsuranceList_reg($pid);         #clear care_person_insurance table
											// 		$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$insurance_array_prev.") AND pid = ".$pid;
											// 		$ok = $db->Execute($delete_result);
											// 	}
											// 										 }
											#--------- end of comment 3/17/15------
											#---------------------------
						} // end if (($allow_ipd_user||$isIPBM)&&(($ptype=='ipd')||($ptype=='er')))
										#-----------------------------------------
											#header("Location: show_opd_clinical_form.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&target=_blank");
											/*$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
											if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
												echo "<script>
														alert('HRN Exists!');
														Location.reload();
													</script>";
											}else{*/
						header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=$encounter_nr&origin=admit&target=entry&newdata=$newdata&update=1&cond_code=$cond_code&disp_code=$disp_code&result_code=$result_code&ptype=$ptype&enc_type=".$patient_enc['encounter_type']."&vital_no=".$_GET['vital_no'].$IPBMextend);
						exit;
											//}
											
					}
				}else{ #if($update || $encounter_nr)

							//print_r($HTTP_POST_VARS);
								#added by VAN 02-29-08
							#echo "pid = ".$pid;
					#change the temporary pid if the baby is admitted to permanent
					if ($pid{0}=='T'){
						$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
						$glob_obj->getConfig('person_%');

						$ref_nr=$GLOBAL_CONFIG['person_id_nr_init'];
						$HTTP_POST_VARS['pid']=$person_obj->getNewPIDNr($ref_nr+$GLOBAL_CONFIG['person_id_nr_init_adder']);

						#update care_person table
						$person_obj->changeTemptoPermanentPID($pid,$HTTP_POST_VARS['pid']);
					}
					#-----------------------------
					#-----added by art 01/22/15 for bug 627
							a:
					$is_locked = $db->GetOne("SELECT IF(IS_USED_LOCK('saving_enc') IS NULL, FALSE , TRUE )");
							
					if (!$is_locked) {
						$db->GetOne("SELECT GET_LOCK('saving_enc',10)");
							#-----for bug 627

					$newdata=1;
							/* Determine the format of the encounter number */
					if($GLOBAL_CONFIG['encounter_nr_fullyear_prepend']) $ref_nr=(int)date('Y').$GLOBAL_CONFIG['encounter_nr_init'];
					else $ref_nr=$GLOBAL_CONFIG['encounter_nr_init'];
							#echo "nr = ".$HTTP_POST_VARS['encounter_class_nr'];

					#edited by VAN 02-06-09
					if ((($enc_info['encounter_type']==2)||($enc_info['encounter_type']==1)||($enc_info['encounter_type']==IPBMOPD_enc))&&(($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))){
						if ($enc_info['encounter_type']==1)
							$HTTP_POST_VARS['encounter_type'] = 3;
						elseif ($enc_info['encounter_type']==2)
							$HTTP_POST_VARS['encounter_type'] = 4;
						elseif ($enc_info['encounter_type']==IPBMOPD_enc)
							$HTTP_POST_VARS['encounter_type'] = IPBMIPD_enc;
					}
							#echo "s = ".$HTTP_POST_VARS['encounter_type'];
							#added by VAN 01-25-10
							if ($HTTP_POST_VARS['encounter_type']==1)
								#ER
								$triage = 'er';
							elseif ($HTTP_POST_VARS['encounter_type']==2)
								#OPD
								$triage = 'opd';
							elseif (($HTTP_POST_VARS['encounter_type']==3)||($HTTP_POST_VARS['encounter_type']==4))
								#ADMISSION
								$triage = 'ipd';
							elseif (($HTTP_POST_VARS['encounter_type']==IPBMIPD_enc)||($HTTP_POST_VARS['encounter_type']==IPBMOPD_enc))
								#ADMISSION
								$triage = 'ipbm';

							$last_enc_nr = $encounter_obj->getLastEncounterNr($triage);

							if ($last_enc_nr)
									$ref_nr = $last_enc_nr;

							#switch($HTTP_POST_VARS['encounter_class_nr'])
							switch($HTTP_POST_VARS['encounter_type'])
							{
								#case '1':
								#case '3':
								/*
								case '1': $HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_inpatient_nr_adder'],1);
											break;
								case '2': $HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_outpatient_nr_adder'],2);
								*/
								#ER
								#case '1': $HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_er_nr_adder'],1);
								case '1': 
											$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
											if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
												if(trim($HTTP_POST_VARS['encounter_nr_temp']) == "" || trim($HTTP_POST_VARS['encounter_nr_temp']) == "0"){
													$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,1);
												}else{
													$HTTP_POST_VARS['encounter_nr']=$HTTP_POST_VARS['encounter_nr_temp'];
												}
											}else{
												$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,1);
											}

											
											break;
								#OPD
								#case '2': $HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_outpatient_nr_adder'],2);
								case '2': 
											$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
											if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
												if(trim($HTTP_POST_VARS['encounter_nr_temp']) == "" || trim($HTTP_POST_VARS['encounter_nr_temp']) == "0"){
													$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,2);
												}else{
													$HTTP_POST_VARS['encounter_nr']=$HTTP_POST_VARS['encounter_nr_temp'];
												}
											}else{
												$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,2);
											}
											
											break;
								#IPD
								case '3':
								case '4':
											$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
											if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
												if(trim($HTTP_POST_VARS['encounter_nr_temp']) == "" || trim($HTTP_POST_VARS['encounter_nr_temp']) == "0"){
													$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,3);
												}else{
													$HTTP_POST_VARS['encounter_nr']=$HTTP_POST_VARS['encounter_nr_temp'];
												}
											}else{
												$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,3);
											}
											#$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_inpatient_nr_adder'],3);
											
											break;
								#IPBM-IPD
								case IPBMIPD_enc_STR:
											// $HTTP_POST_VARS['encounter_status']='ipbm';
											$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
											if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
												if(trim($HTTP_POST_VARS['encounter_nr_temp']) == "" || trim($HTTP_POST_VARS['encounter_nr_temp']) == "0"){
													$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,IPBMIPD_enc);
												}else{
													$HTTP_POST_VARS['encounter_nr']=$HTTP_POST_VARS['encounter_nr_temp'];
												}
											}else{
												$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,IPBMIPD_enc);
											}
											#$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr+$GLOBAL_CONFIG['patient_inpatient_nr_adder'],3);
											
											break;
								#IPBM-OPD
								case IPBMOPD_enc_STR: 
											// $HTTP_POST_VARS['encounter_status']='ipbm';
											$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
											if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
												if(trim($HTTP_POST_VARS['encounter_nr_temp']) == "" || trim($HTTP_POST_VARS['encounter_nr_temp']) == "0"){
													$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,IPBMOPD_enc);
												}else{
													$HTTP_POST_VARS['encounter_nr']=$HTTP_POST_VARS['encounter_nr_temp'];
												}
											}else{
												$HTTP_POST_VARS['encounter_nr']=$encounter_obj->getNewEncounterNr($ref_nr,IPBMOPD_enc);
											}
											
											break;
											
							}

							#echo '<br>ref = '.$ref_nr;
							#echo "<br>sql = ".$encounter_obj->sql;
							#echo "<br>en = ".$HTTP_POST_VARS['encounter_nr'];
							#exit();
							$encounter_nr=$HTTP_POST_VARS['encounter_nr'];

								# burn added: May 24, 2007
							$HTTP_POST_VARS['admission_dt']=NULL;
							if ( (!isset($HTTP_POST_VARS['update'])) &&
									(isset($HTTP_POST_VARS['encounter_status']))
								){
									#edited by VAN 07-04-08
									#$HTTP_POST_VARS['admission_dt']=date('Y-m-d H:i:s');

									$date = date("Y-m-d",strtotime($HTTP_POST_VARS['admission_date']));
									$time = $HTTP_POST_VARS['admission_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
									$time = date("H:i:s",strtotime($time));
									$admissiondt = $date." ".$time;
									$admissiondt = date("Y-m-d H:i:s",strtotime($admissiondt));
									$HTTP_POST_VARS['admission_dt'] = $admissiondt;
							}

								# burn added: May 17, 2007
								# first entry, consulting and current dept/doctor are the same,
								# except for direct admission

							#if (!isset($HTTP_POST_VARS['encounter_status'])){
							if (($HTTP_POST_VARS['encounter_type']==1)||($HTTP_POST_VARS['encounter_type']==2)||($HTTP_POST_VARS['encounter_type']==IPBMOPD_enc)){
								$HTTP_POST_VARS['consulting_dept_nr']=$HTTP_POST_VARS['current_dept_nr'];
								$HTTP_POST_VARS['consulting_dr_nr']=$HTTP_POST_VARS['current_att_dr_nr'];
							}


							#edited by VAN 08-22-08
							if ($seg_direct_admission && strtolower(trim($_GET['ptype'])) == "ipd"){
								$HTTP_POST_VARS['encounter_date']=$admissiondt;
							}else{
								#$HTTP_POST_VARS['encounter_date']=date('Y-m-d H:i:s');
								#added by VAN 01-21-09
								$date = date("Y-m-d",strtotime($HTTP_POST_VARS['encounter_dt']));
								$time = $HTTP_POST_VARS['encounter_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
								$time = date("H:i:s",strtotime($time));
								$enc_date = $date." ".$time;
								$HTTP_POST_VARS['encounter_date'] = date("Y-m-d H:i:s",strtotime($enc_date));
								#---------------
							}

							if ($opdfromipd){
								if (($HTTP_POST_VARS['encounter_type'] == 1)||($HTTP_POST_VARS['encounter_type'] == 3)||($HTTP_POST_VARS['encounter_type'] == IPBMIPD_enc))
										$HTTP_POST_VARS['encounter_class_nr'] = 1;
								elseif (($HTTP_POST_VARS['encounter_type'] == 2)||($HTTP_POST_VARS['encounter_type'] == 4)||($HTTP_POST_VARS['encounter_type'] == IPBMOPD_enc))
										$HTTP_POST_VARS['encounter_class_nr'] = 2;

								$date = date("Y-m-d",strtotime($HTTP_POST_VARS['admission_date']));
								$time = $HTTP_POST_VARS['admission_time'].":00 ".$HTTP_POST_VARS['selAMPM2'];
								$time = date("H:i:s",strtotime($time));
								$admissiondt = $date." ".$time;
								$admissiondt = date("Y-m-d H:i:s",strtotime($admissiondt));
								$HTTP_POST_VARS['admission_dt'] = $admissiondt;

								$HTTP_POST_VARS['encounter_date'] = date('Y-m-d H:i:s',strtotime($HTTP_POST_VARS['admission_dt']));
							}
							$HTTP_POST_VARS['referrer_dr'] = $HTTP_POST_VARS['referrer_dr_name'];
							$HTTP_POST_VARS['modify_id']=$encoder;
							//$HTTP_POST_VARS['modify_time']='NULL';
							$HTTP_POST_VARS['create_id']=$encoder;
							$HTTP_POST_VARS['create_time']=date('YmdHis');
							$HTTP_POST_VARS['history']='Create: '.date('m-d-Y H:i:s').' = '.$encoder."\n";

							#added by VAN 04-17-08
							if ($seg_direct_admission==1 && strtolower(trim($_GET['ptype'])) == "ipd")
								$HTTP_POST_VARS['encounter_status']='direct_admission';
							#elseif(($current_ward_nr)||($current_room_nr)||($current_bed_nr))
							#	$HTTP_POST_VARS['encounter_status']='disallow_cancel';
							elseif($ptype=='phs')
								$HTTP_POST_VARS['encounter_status'] = 'phs';
							else
								$HTTP_POST_VARS['encounter_status']='';

							#print_r($HTTP_POST_VARS);
							if (!$HTTP_POST_VARS['is_DOA']){
								$HTTP_POST_VARS['is_DOA'] = '0';
								$HTTP_POST_VARS['is_DOA_reason'] = '';
							}else{
								$HTTP_POST_VARS['is_DOA'] = '1';

							}

							if ($HTTP_POST_VARS['accomodation_type']==2){
								$HTTP_POST_VARS['area'] = "";
							}
	
							#$db->StartTrans();

							# Added by: Jeff
							# Date: 10-08-2017

							# Purpose: To get the or_id instead of or_desc using user defined function
							$official_rnr = $encounter_obj->getOPDTempeIDnum($HTTP_POST_VARS['official_receipt_nr']);

									if ($official_rnr) {
										$HTTP_POST_VARS['official_receipt_nr'] = $official_rnr;
									}else{
							$HTTP_POST_VARS['official_receipt_nr'] = stripslashes($HTTP_POST_VARS['official_receipt_nr']);
									}
									
							$encounter_obj->setDataArray($HTTP_POST_VARS);        
							                 
							$bSuccess = $encounter_obj->insertDataFromInternalArray();


                   if ($bSuccess) {
                   		
                   		if ($ward_obj->isModWard($HTTP_POST_VARS['current_ward_nr'])) {
                   			
                   			$encounter_obj->updatePatientClassification($encounter_nr,false);
                   		}
                        # added by VAS 11/19/2013
                        # integration to EMR starts here
                        # for doctor only
                        # Post corresponding Patient's Demographic demographic in EMR
                        $_POST['pid'] = $pid;
						
                        /**
                         * edited by marc lua 3/6/2014
                         * edited by justin tan 12/09/14
                         */
                        require_once($root_path . 'include/care_api_classes/emr/services/EncounterEmrService.php');
                        // require_once($root_path . 'include/care_api_classes/emr/services/PatientEmrService.php');
                        $encService = new EncounterEmrService();
                        // $patientService = new PatientEmrService();
                        try {
                        	//save new record
                            // if ($patientService->savePatient($pid)) {
                        	// print_r($pid);die();
                                $encService->saveEncounter($pid, $encounter_nr);
                            // }
                        } catch (Exception $exc) {
                            //echo $exc->getTraceAsString();
                        }
                        #===============================


										#added by VAN 01-25-10
										#echo "<br><br>insert = ".$encounter_obj->sql;
										$bSuccess = $encounter_obj->update_Encounter_Tracker($encounter_nr,$triage);

										if($bSuccess){
					$groupName  = $encounter_obj->getDepartmentGroupName($HTTP_POST_VARS['current_dept_nr']);
					$room_name  = $encounter_obj->getPatientRoom($HTTP_POST_VARS['current_room_nr'],$HTTP_POST_VARS['current_ward_nr']);
					//save
					$ehr_patientType = "";
					switch ($ptype) {
	                	case 'ipd':
	                		$ehr_patientType = 'ipe';
	                		break;
	                	case 'opd':
	                		$ehr_patientType = 'ope';
	                		break;
	                	case 'er':
	                		$ehr_patientType = 'ere';
	                		break;
	                	case 'phs':
	                		$ehr_patientType = 'phs';
	                		break;
	                	default:
	                		$ehr_patientType = '';
	                		break;
	                }
	                
					$assignPatientData = array(
						"groupName"			=>		$groupName,
						"er_areaid"			=>		$HTTP_POST_VARS['current_dept_nr'],
						"personnel_nr"		=>		$HTTP_POST_VARS['current_att_dr_nr'],
						"pid"				=>		$pid,
						"encounter_nr"		=>		$encounter_nr,
						"date"				=>		$HTTP_POST_VARS['encounter_date'].' '.$HTTP_POST_VARS['encounter_time'].":00 ".$HTTP_POST_VARS['selAMPM2'],
						"time"				=>		$time,
						"room_name"			=>		$room_name,
						"patient_type"		=>		$ehr_patientType,
						"er_opd_diagnosis"	=>		$HTTP_POST_VARS['er_opd_diagnosis'],
						"parent_encounter_nr" => 	$HTTP_POST_VARS['parent_encounter_nr']
					);	

					require_once($root_path . 'include/care_api_classes/ehrhisservice/Ehr.php');
		                $ehr = Ehr::instance();
											try {
		                $utf8_encode_data = array_map("utf8_encode", $assignPatientData);
		                $patient = $ehr->postAssignPatient($utf8_encode_data);
											} catch (Exception $e) {
												error_log("Unable to save to EHR. Error: ".$e, 0);
											}


                        }
										#echo "<br><br>".$encounter_obj->sql;
										#exit();
										$profileSuccess = $encounter_obj->setEncounterProfile($encounter_nr,$pid);

										##############  added by bryan on 2/24/09 for vitals  ##############
										#edited by VAN 10-13-09
										 if ((($_POST['vital_bp_sys'])&&($_POST['vital_bp_dias']))||($_POST['vital_t'])||($_POST['vital_w'])
													||($_POST['vital_rr'])||($_POST['vital_pr'])){
															$dataVital = array(
																								'encounter_nr' => $HTTP_POST_VARS['encounter_nr'],
																								'date' => date('Y-m-d H:i:s'),
																								'pid' => $pid,
																								'systole' => $_POST['vital_bp_sys'],
																								'diastole' => $_POST['vital_bp_dias'],
																								'temp' => $_POST['vital_t'],
																								'weight' => $_POST['vital_w'],
																								'resp_rate' => $_POST['vital_rr'],
																								'pulse_rate' => $_POST['vital_pr'],
																								'history' => "Create: ".date('m-d-Y H:i:s')." [".$_SESSION['sess_temp_userid']."]\n",
																								'modify_id' => $_SESSION['sess_temp_userid'],
																								'modify_dt' => date('Y-m-d H:i:s')
																						);

															$vitals_obj->prepareVitals();


															$PNo = $_GET['vitdate'];
															$dataVital['create_id']=$_SESSION['sess_temp_userid'];
															$dataVital['create_dt']= date('Y-m-d H:i:s');
															$vitals_obj->setDataArray($dataVital);
															$saveok=$vitals_obj->insertDataFromInternalArray();
										 }
										 ##############

										#echo "enc = ".$HTTP_POST_VARS['encounter_type'];
										#added by VAN 02-01-09
																				#opd
										if ($HTTP_POST_VARS['is_DOA']==1){
											$person_obj->updateDeathDate($pid,date("Y-m-d"),$encounter_nr,date("H:i:s"));
											$person_obj->updateDeathDate1($pid,$encounter_nr);	
										}else{
											$person_obj->updateDeathDate($pid,'0000-00-00',$encounter_nr,'00:00:00');
										}
											/* Get last insert id */
										/*if($dbtype=='mysql'){
											$encounter_nr=$db->Insert_ID();
										}else{
											$encounter_nr=$encounter_obj->postgre_Insert_ID($dbtable,'encounter_nr',$db->Insert_ID());
										}
										*/

										#echo "sql = ".$encounter_obj->sql;
										#added by VAN 03-10-08
										$date=date('Y-m-d');
										$time=date('H:i:s');

										#added by VAN 04-28-08
										#if ($dept_belong['id']=="ER"){
										if ($HTTP_POST_VARS['is_medico']){
											if (($allow_er_user||$isIPBM)&&(($ptype=='er')||($ptype=='ipd'))){
												$encounter_obj->setMedico($HTTP_POST_VARS['is_medico'],$encounter_nr);

												$cases = array();
												if(is_object($medico_cases)){
													while($result=$medico_cases->FetchRow()) {
														if ($HTTP_POST_VARS['medico'.$result['code']]){
															if ($HTTP_POST_VARS['medico'.$result['code']]=='OT')
																$desc =  $HTTP_POST_VARS['description'];
															else
																$desc = "none";
															#$cases[] = array($HTTP_POST_VARS['medico'.$result['code']],'');
															$cases[] = array($HTTP_POST_VARS['medico'.$result['code']],$desc);
														}
													}
												}

											$encounter_obj->deleteMedicoCasesEncounter($HTTP_POST_VARS['encounter_nr'],$pid);
											$encounter_obj->addMedicoCasesEncounter($HTTP_POST_VARS['encounter_nr'],$pid,$cases);
										}
									}
										#----------------------------

									if (($HTTP_POST_VARS['encounter_type'] == 3)||($HTTP_POST_VARS['encounter_type'] == 4)||($HTTP_POST_VARS['encounter_type'] == IPBMIPD_enc)){
											if (($allow_ipd_user||$isIPBM)&&(($ptype=='ipd')||$ptype=='newborn')){
											#edited by VAN 05-21-2010
											$encounter_obj->startTrans();

											if (!$current_bed_nr){
												$current_bed_nr = 0;
												$in_ward = 0;
											}else
												$in_ward = 1;

											$source = 'ADMISSION';

											$room_date = $HTTP_POST_VARS['datefrom'];
											$room_time = $HTTP_POST_VARS['timefrom'].":00 ".$HTTP_POST_VARS['selAMPM3'];

											if ($room_date)
												$date = date("Y-m-d",strtotime($room_date));
											else
												$date=date('Y-m-d');

											if	($room_time)
												$time = date("H:i:s",strtotime($room_time));
											else
												$time=date('H:i:s');

											if($ok = $encounter_obj->AdmitInWard($HTTP_POST_VARS['encounter_nr'],$current_ward_nr,$current_room_nr,$current_bed_nr,$source,$date,$time,0,'',1)){
												if($ok) $ok = $encounter_obj->setAdmittedInWard($HTTP_POST_VARS['encounter_nr'],$current_ward_nr,$current_room_nr,$current_bed_nr,0,$in_ward,0,1);
												$roomInfo = $ward_obj->getRoomRate($current_room_nr, $current_ward_nr);
													$rate = $roomInfo['room_rate'];
													#echo "<br>sql  = ".$ward_obj->sql;
													#echo "<br>rate = ".$rate;
													$location = $encounter_obj->getLatestLocNr($HTTP_POST_VARS['encounter_nr']);
													#echo "<br>nr = ".$encounter_obj->sql;
													$loc_enc_nr = $location['nr'];

													$encounter_obj->setPatientRoomRate($loc_enc_nr,$HTTP_POST_VARS['encounter_nr'],$current_ward_nr,$current_room_nr,$current_bed_nr,$rate,'');
													#echo "<br>sql = ".$encounter_obj->sql;

												}
												if (!$ok) $encounter_obj->FailTrans();
													$encounter_obj->CompleteTrans();
											}
											#-------------------------

											#added by VAN 12-30-08
											if (($allow_ipd_user||$isIPBM)&&(($ptype=='ipd')||($ptype=='er'))){


											#---------condition----------------------
											if (isset($HTTP_POST_VARS['cond_code'])){
												$condition['encounter_nr'] = $HTTP_POST_VARS['encounter_nr'];
												$condition['cond_code']=$HTTP_POST_VARS['cond_code'];
												$condition['modify_id']=$encoder;
												$condition['modify_time']=date('YmdHis');
												$condition['create_id']=$encoder;
												$condition['create_time']=date('YmdHis');
												$encounter_obj->setDataArray($condition);

												if(!@$encounter_obj->saveEncounterCondition($condition)) echo "<br>$LDDbNoSave";

											}
											#----------disposition----------------------
											if(isset($HTTP_POST_VARS['disp_code'])){
												$disposition['encounter_nr'] = $HTTP_POST_VARS['encounter_nr'];
												$disposition['disp_code']=$HTTP_POST_VARS['disp_code'];
												$disposition['modify_id']=$encoder;
												$disposition['modify_time']=date('YmdHis');
												$disposition['create_id']=$encoder;
												$disposition['create_time']=date('YmdHis');
												$encounter_obj->setDataArray($disposition);

												if(!@$encounter_obj->saveEncounterDisposition($disposition)) echo "<br>$LDDbNoSave";

											#----------results-------------------------
											}

											if(isset($HTTP_POST_VARS['result_code'])){
												$result['encounter_nr'] = $HTTP_POST_VARS['encounter_nr'];
												$result['result_code']=$HTTP_POST_VARS['result_code'];
												$result['modify_id']=$encoder;
												$result['modify_time']=date('YmdHis');
												$result['create_id']=$encoder;
												$result['create_time']=date('YmdHis');


												if($_POST['result_code']==4){
													$person_obj->updateDeathDate1($pid,$encounter_nr);

												}
												
												$encounter_obj->setDataArray($result);


												if(!@$encounter_obj->saveEncounterResults($result)) echo "<br>$LDDbNoSave";
											}



											#---------added by VAN 090107------
											#---------commented by justin 3/18/15------
											#----------INSURANCE--------
												// if ($insurance_class_nr!=3){
												// 	#-----with insurance---
												// 	if ($_POST["items"]!=NULL){
												// 		$bulk_hcare = array();
												// 		$bulk_insurance_nr = array();
												// 		foreach (array_unique($_POST["items"]) as $i=>$v) {
												// 			#------------------hcare_id, insurance_nr, is principal holder-----
												// 			$bulk[] = array($_POST["items"][$i],$_POST["nr"][$i],$_POST["is_principal"][$i]);
												// 			$bulk_hcare[] = array($_POST["items"][$i]);
												// 			$current_array .= $_POST["items"][$i].",";
												// 		}

												// 		$current_array = substr($current_array,0,strlen($current_array)-1);

												// 		$pinsure_obj->clearInsuranceList($HTTP_POST_VARS['encounter_nr']);	#clear seg_encounter_insurance table
												// 		$pinsure_obj->addInsurance($HTTP_POST_VARS['encounter_nr'],$bulk_hcare,$encoder,date('YmdHis'));
												// 		$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$current_array.") AND pid = ".$pid;
												// 		$ok = $db->Execute($delete_result);
												// 		$pinsure_obj->addInsurance_reg($pid,$bulk,$encoder,date('YmdHis'),$insurance_class_nr);

												// 	}else{
												// 		$pinsure_obj->clearInsuranceList($HTTP_POST_VARS['encounter_nr']); #clear seg_encounter_insurance table
												// 		$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$insurance_array_prev.") AND pid = ".$pid;
												// 		$ok = $db->Execute($delete_result);
												// 	}
												// }elseif ($insurance_class_nr==3){
												// 	#-----self-pay------
												// 	$pinsure_obj->clearInsuranceList($HTTP_POST_VARS['encounter_nr']);		#clear seg_encounter_insurance table
												// 	#$pinsure_obj->clearInsuranceList_reg($pid);         #clear care_person_insurance table
												// 	$delete_result="DELETE FROM care_person_insurance WHERE hcare_id IN (".$insurance_array_prev.") AND pid = ".$pid;
												// 	$ok = $db->Execute($delete_result);
												// }
											#--------- end of comment 3/17/15------
											}
											#---------------------------
										}
											#-----------------------------
										#if (!$bSuccess) $db->FailTrans();
										#$db->CompleteTrans();

										# If appointment number available, mark appointment as "done"
										if(isset($appt_nr)&&$appt_nr) $encounter_obj->markAppointmentDone($appt_nr,$HTTP_POST_VARS['encounter_class_nr'],$encounter_nr);
													header("Location: aufnahme_daten_zeigen.php".URL_REDIRECT_APPEND."&encounter_nr=".$HTTP_POST_VARS['encounter_nr']."&origin=admit&target=entry&newdata=$newdata&ptype=$ptype".$IPBMextend);
											 exit;
										}else{
										echo "<script>alert('$LDDbNoSave')</script>";
										header("Location: aufnahme_start.php".URL_APPEND."&pid=".$pid."&origin=patreg_reg&encounter_class_nr=2&ptype=".$ptype.$IPBMextend);
									}
								#-----added by art 01/22/15 for bug 627
								$db->GetOne("SELECT RELEASE_LOCK('saving_enc')"); #added by art 01/22/15
								}else{
									goto a; 
								}#end if lock 
								#-----for bug 627
								
					 }// end of if(update) else()
									}	// end of if($error)
						 } // end of if($mode)
						 
				}elseif($encounter_nr!='') {
				/* Load encounter data */
				$encounter_obj->loadEncounterData();
				#echo $encounter_obj->sql;
				if($encounter_obj->is_loaded) {
							$zeile=&$encounter_obj->encounter;

					//load data
					extract($zeile);

							 // Get insurance firm name
						$insurance_firm_name=$pinsure_obj->getFirmName($insurance_firm_id);

				/* GEt the patient's services classes */

				if(!empty($GLOBAL_CONFIG['patient_financial_class_single_result'])) $encounter_obj->setSingleResult(true);

				if(!$GLOBAL_CONFIG['patient_service_care_hide']){
									if($buff=&$encounter_obj->CareServiceClass()){
							while($care_class=$buff->FetchRow()){
							extract($care_class);
						}
						reset($care_class);
					}
				}
				if(!$GLOBAL_CONFIG['patient_service_room_hide']){
									if($buff=&$encounter_obj->RoomServiceClass()){
							while($room_class=$buff->FetchRow()){
							extract($room_class);
						}
						reset($room_class);
					}
				}
				if(!$GLOBAL_CONFIG['patient_service_att_dr_hide']){
									if($buff=&$encounter_obj->AttDrServiceClass()){
							while($att_dr_class=$buff->FetchRow()){
							extract($att_dr_class);
						}
						reset($att_dr_class);
					}
				}
					}

		}


				#echo "sql = ".$encounter_obj->sql;
#echo "here = ".$encounter_class_nr." - ".$encounter_type;
		 #if (($encounter_class_nr==2)||($encounter_type==2)){
	 if ((($encounter_class_nr==2)||($encounter_type==2)||($encounter_type==IPBMOPD_enc)) || ($ptype=='opd') || ($ptype=='phs')){
		# Load all  doctors in OPD
		$cond = "";
		if ($ptype=='phs')
			$cond = " AND personell_nr IN (SELECT dr_nr FROM seg_phs_dr)";

		$doctor_dept=$pers_obj->getDoctors(0, $cond);
		$all_meds=&$dept_obj->getAllOPDMedicalObject(0, $cond);
	 }else{
		# Load all  doctors in IPD
		$doctor_dept=$pers_obj->getDoctors(1);
		$all_meds=&$dept_obj->getAllOPDMedicalObject(1);
	 }

	 #----IPD-------------

		#if(!$encounter_nr||$encounter_class_nr==1){	#-----comment 03-07-07------
	 if(!$encounter_nr||$update==1){
		# Load all  wards info
		$items='nr,name';
		$ward_info=&$ward_obj->getAllWardsItemsObject($items,$isIPBM);
	}

	#----OPD-----------------
	$person_obj->setPID($pid);
	if($data=&$person_obj->BasicDataArray($pid)){
	#echo $person_obj->sql;
		extract($data);
	}
    $bornhere = $fromtemp;

#print_r($data);
	# Prepare the photo filename
	include_once($root_path.'include/inc_photo_filename_resolve.php');
	/* Get the citytown name */
	$addr_citytown_name=$person_obj->CityTownName($addr_citytown_nr);

}
# Prepare text and resolve the numbers
include_once($root_path.'include/inc_patient_encounter_type.php');

# Prepare the title
#if($encounter_nr) $headframe_title = "$headframe_title $headframe_append ";
if($encounter_nr){
	if ($patient_enc['encounter_type']==1)
		$headframe_title = "Consultation :: ER";
	elseif ($patient_enc['encounter_type']==2)
		$headframe_title = "Consultation :: OPD";
	elseif (($patient_enc['encounter_type']==2)&&($patient_enc['encounter_status']=='phs'))
		$headframe_title = "Consultation :: PHS";
	elseif (($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4))
		$headframe_title = "Admission";
	elseif ($patient_enc['encounter_type']==IPBMIPD_enc)
		$headframe_title = "IPBM-IPD";
	elseif ($patient_enc['encounter_type']==IPBMOPD_enc)
		$headframe_title = "IPBM-OPD";

	$headframe_title = "$headframe_title $headframe_append ";
}else{
	if ($ptype=='er')
		$headframe_title = "Consultation :: ER";
	elseif ($ptype=='opd')
		if($isIPBM) $headframe_title = "IPBM-OPD";
		else $headframe_title = "Consultation :: OPD";
	elseif ($ptype=='phs')
		$headframe_title = "Consultation :: PHS";
	elseif ($ptype=='ipd')
		if($isIPBM) $headframe_title = "IPBM-IPD";
		else $headframe_title = "Admission";
}

#echo "enc = ".$patient_enc['encounter_type'];
# Prepare onLoad JS code
#if(!$encounter_nr && !$pid) $sOnLoadJs ='onLoad="if(document.searchform.searchkey.focus) document.searchform.searchkey.focus();"';


# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

#added by VAN 01-06-2015
# clear the entire cache
$smarty->clear_all_cache();

# Title in the toolbar
 $smarty->assign('sToolbarTitle',$headframe_title);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('admission_how2new.php')");

 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('title',$headframe_title);

 # Onload Javascript code
 $sOnLoadJs='onLoad="preset();"';
 $smarty->assign('sOnLoadJs',$sOnLoadJs);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('person_admit.php')");

 # Hide the return button
 $smarty->assign('pbBack',FALSE);

 # Start collectiong extra Javascript code
 ob_start();

require_once("doctor-dept.common.php");

if ($xajax) {
		$xajax->printJavascript('../../classes/xajax');

}

# If  pid exists, output the form checker javascript
if(isset($pid) && $pid){
echo '<script type="text/javascript" src="'.$root_path.'js/shortcuts.js"></script>';
?>

<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
<!--
.olbg {
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	background-color:#0000ff;
	border:1px solid #4d4d4d;
}
.olcg {
	background-color:#aa00aa;
	background-image:url("<?= $root_path ?>images/bar_05.gif");
	text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
	background-color:#ffffcc;
	text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
	font-family:Arial; font-size:13px;
	font-weight:bold;
	color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>

<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script> 
<script type="text/javascript" src="js/reg-insurance-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="js/vitals.js?t=<?=time()?>"></script>

<!-------------------------------------------->

<script  language="javascript">
var initiator=0;
var $J = jQuery.noConflict();
<!--

/*
	This will trim the string i.e. no whitespaces in the
	beginning and end of a string AND only a single
	whitespace appears in between tokens/words
	input: object
	output: object (string) value is trimmed
*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}/* end of function trimString */

//------------added by van 03-16-07----------

function valButton(btn) {
	var cnt = -1;
	var temp = document.getElementsByName(btn);
	if (!$(btn))	{
		return null;
	}

	for (var i=temp.length-1; i > -1; i--) {
		if (temp[i].checked) {
			cnt = i;
			i = -1;
		}
		}

	if (cnt > -1) return temp[cnt].value;
		else return null;
}

//added by VAN 08-20-08

function chckRoomModeStatus(){
	var d = document.aufnahmeform;
	var prev_ward_nr = $('prev_ward_nr').value;
	var prev_room_nr = $('prev_room_nr').value;
	var prev_bed_nr = $('prev_bed_nr').value;

	var current_ward_nr = $('current_ward_nr').value;
	//var current_room_nr = $('current_room_nr').value;
	var current_room_nr =d.current_room_nr.options[d.current_room_nr.selectedIndex].value;
	var current_bed_nr = $('cur_bed_nr').value;

	if ((prev_ward_nr==current_ward_nr)&&(prev_room_nr==current_room_nr)&&(prev_bed_nr==current_bed_nr)){
		//alert('the same');
		$('is_same').value=1;
		//$('is_correction').value=0;
	}else{
		$('is_same').value=0;
	}

	$('assign_type').value='';
	if (prev_ward_nr!=current_ward_nr)
		$('assign_type').value='ward';
	else if (prev_room_nr!=current_room_nr)
		$('assign_type').value='room';
	else if  (prev_bed_nr!=current_bed_nr)
		$('assign_type').value='bed';
	/*
	else{
		//alert('the different');
		$('is_same').value=0;
		answer = confirm("In assigning a room, is it a room transfer and not a correction? \n Click OK to TRANSFER, otherwise CANCEL to CORRECT");
		//alert(answer);
		if (answer){
			res = confirm("Are you sure of your answer? \n Click OK to YES, otherwise CANCEL to NO");
			if (res)
				$('is_correction').value=0;
			else
				$('is_correction').value=1;
		}else{
			$('is_correction').value=1;
		}
	}
	*/
}

function showDateTime(mode){
	if (mode!='correct')
		$('datefrom_row').style.display = 'none';
	else
		$('datefrom_row').style.display = '';

}

//added by bryan on Feb. 2, 2008
function openVital() {
				overlib(
				OLiframeContent('<?=$root_path?>modules/registration_admission/seg-vitalsigns.php?<?=$var_qry?>&var_include_enc=0',
								700, 200, 'fSelEnc', 0, 'auto'),
				WIDTH,700, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
								CAPTION,'Vital Signs',
				MIDX,0, MIDY,0,
				STATUS,'Vital Signs');
				return false;
}

function getMode(mode){
	$('assign_mode').value = mode;
	showDateTime(mode);

	//added by VAN 05-26-2010
	var count_accom = $('count_accom').value;
	var objMode = document.aufnahmeform.assign_room_mode;

	if (count_accom==0){
		 //disable transfer and delete if no accomodation yet
		 objMode[1].disabled = true;
		 objMode[2].disabled = true;
	}else if (count_accom==3){
		 // enable transfer and disable delete if there is only 1 accomodation
		 objMode[1].disabled = false;
		 objMode[2].disabled = true;
	}else if (count_accom>3){
		 // enable both
		 objMode[1].disabled = false;
		 objMode[2].disabled = false;
	}

	if (mode=='delete'){
			$('accomodation_assignment').style.display = 'none';
			$('room_assignment').style.display = 'none';
			$('area_assignment').style.display = 'none';
			$('datefrom_row').style.display = 'none';
			$('bed_assignment').style.display = 'none';
	}else{
			$('accomodation_assignment').style.display = '';
			$('room_assignment').style.display = '';
			$('area_assignment').style.display = '';
			$('datefrom_row').style.display = '';
			$('bed_assignment').style.display = '';
	}
}

function chkform(d) {
	var encr =<?php if ($encounter_class_nr) {echo $encounter_class_nr; } else {echo '0';} ?>;
	var update = <?php echo $update; ?>;
	var dept_belong = "<?php echo $dept_belong['id']; ?>";
	var allow_er_user = '<?php echo $allow_er_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var allow_medocs_user = '<?php echo $allow_medocs_user; ?>';
	var allow_ipd_user = '<?php echo $allow_ipd_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var direct_admission = <?php if ($seg_direct_admission) echo $seg_direct_admission; else echo '0'; ?>;
	//added by VAN 01-21-09
	var encounter_nr = '<?=$encounter_nr?>';
	var isIPBM = '<?=$isIPBM?>';
	var IPBMIPD_enc = '<?=IPBMIPD_enc?>';
	var IPBMOPD_enc = '<?=IPBMOPD_enc?>';
	var encounter_type = '<?=$encounter_type?>';
	var ptype = '<?php echo $ptype; ?>';
	//if(dept_belong == "Admission"){

	var current_date = '<?= date("m/d/Y H:i:s")?>';
	

	if(encounter_type == IPBMIPD_enc || encounter_type == IPBMOPD_enc){
		if($J('input[name=smoker]:checked').length != 1){
			alert('History of smoking option is required');
			$('smoker_no').focus();
			return false;
		}

		if($J('input[name=drinker]:checked').length != 1){
			alert('Alcohol drinker option is required');
			$('drinker_no').focus();
			return false;
		}

		if($J('input[name=DEPOvaccine]:checked').length != 1 && (encounter_type == IPBMOPD_enc)){
			alert('DEPOT Medicine option is required');
			$('drinker_no').focus();
			return false;
		}
	}

	//added by Nick 3-28-2015
	if(ptype == 'opd' || ptype == 'phs'){

//		if($J('input[name=smoker]:checked').length != 1){
//			alert('History of smoking option is required');
//			$('smoker_no').focus();
//			return false;
//		}
//		if($J('input[name=drinker]:checked').length != 1){
//			alert('Alcohol drinker option is required');
//			$('drinker_no').focus();
//			return false;
//		}

		//added by aiza 06-07-2016
		var encounter_dt = $('encounter_dt').value;
		var encounter_tm = $('encounter_time').value;
		var encounter_meridian = $('selAMPM2').value;

		var encounter_time_text_hr = encounter_tm.substr(0, 2);
		var encounter_time_text_min = encounter_tm.substr(3);

			if (encounter_meridian == 'PM') {
				if (encounter_time_text_hr != '12')
					encounter_time_text_hr = parseInt(encounter_time_text_hr) + 12;

			} else if (encounter_meridian == 'AM') {
				if (encounter_time_text_hr == '12')
					encounter_time_text_hr = '00';
			}

		encounter_tm = encounter_time_text_hr + ":" + encounter_time_text_min + ":00";
		
		var en_dt;
		en_dt = encounter_dt + " " + encounter_tm;
		var encounter_date = new Date(en_dt);
		var current_date2 = new Date(current_date);


		if (encounter_date > current_date2){
			alert("Consultation date/time must be earlier or the same with the current date/time.");
			$('encounter_dt').focus();
			return false;
		}


		if($('official_receipt_nr').value==''){
			alert('OR Number is required');
			$('official_receipt_nr').focus();
			return false;
		}
		if($('current_dept_nr').value==0){
			alert('Consulting Clinic is required');
			$('current_dept_nr').focus();
			return false;
		}
		return true;
	}else if ((allow_ipd_user == 1) && (ptype == 'ipd')) {

		var btncond = valButton("cond_code");
		var btnres = valButton("result_code");
		var btndisp = valButton("disp_code");

		var is_admit_obj = document.getElementsByName('is_admit');

		//	chckRoomModeStatus();

		if ($('is_admit')) {
			if ((update == 1) && !d.is_admit.checked) {
				alert("<?php echo $LDPlsCheckAdmit; ?>");
				d.is_admit.focus();
				return false;
			}
		}
		//added by VAN 09-01-2011
		//validation of admission date and time
		var parent_encounter_date = $('parent_encounter_date').value;
		var current_encounter_date = $('current_encounter_date').value;
		var admission_date_text = $('admission_date').value;
		var admission_time_text = $('admission_time').value;
		var admission_meridian_text = $('selAMPM2').value;
		var istobeadmitted = '<?=$_GET['istobeadmitted']?>';
		var orig_ptype = '<?=$orig_ptype?>';

		//added by Nick 3-28-2015
		if ($J('#is_medico').is(':checked')) {
			if($J('#srcMedicoTable input[type=checkbox]:checked').length==0){
				alert('Please select medico legal cases');
				$('description').focus();
				return false;
			}

			if($J('#medicoOT').is(':checked') && $J('#srcMedicoTable #description').val().trim() == ''){
				alert("You need to write on the description");
				$('description').focus();
				return false
			}

			if($('DOI').value == '') {
				alert('Please enter Date of Incident');
				$('DOI').focus();
				return false;
			}

			if($('TOI').value == '') {
				alert('Please enter Time of Incident');
				$('TOI').focus();
				return false;
			}
		}

		if (((encounter_type == 3) || (encounter_type == 4) || (encounter_type == IPBMIPD_enc))
			|| (($(is_admit).checked) && (istobeadmitted == 1))) {

			admission_time_text_hr = admission_time_text.substr(0, 2);
			admission_time_text_min = admission_time_text.substr(3);

			if (admission_meridian_text == 'PM') {
				if (admission_time_text_hr != '12')
					admission_time_text_hr = parseInt(admission_time_text_hr) + 12;

			} else if (admission_meridian_text == 'AM') {
				if (admission_time_text_hr == '12')
					admission_time_text_hr = '00';
			}

			admission_time_text = admission_time_text_hr + ":" + admission_time_text_min + ":00";
			//alert('admission_time_text = '+admission_time_text);
		}

		var adm_dt, consult_dt;
		adm_dt = admission_date_text + " " + admission_time_text;
		if(ptype=='ipd' && orig_ptype != 'opd'){
			if(direct_admission == 1) {
				if (d.cond_code.value == 0){
					alert("Please select patient\'s condition from Other Institution.");
					document.getElementById("cond_code").focus();
					return false;
				} else if (d.result_code.value == 0){
					alert("Please select patient\'s result from Other Institution.");
					document.getElementById("result_code").focus();
					return false;
				}
			} else {
				if (d.cond_code.value == 0){
					alert("Please select patient\'s condition from ER.");
					document.getElementById("cond_code").focus();
					return false;
				} else if (d.result_code.value == 0){
					alert("Please select patient\'s result from ER.");
					document.getElementById("result_code").focus();
					return false;
				}
			}
		}

		if ((((encounter_type == 3) || (encounter_type == 4) || (encounter_type == 13)) && (!direct_admission))
			|| (istobeadmitted == 1)) {
			if (parent_encounter_date > adm_dt) {
				//alert("Admission date must not be earlier than the parent encounter date ("+parent_encounter_date+")");
				//d.admission_date.focus();
				//return false;
				var msg = "Admission date must not be earlier than the parent encounter date (" + parent_encounter_date + "). \n Do you really want to edit the date?";

				if(d.current_room_nr.value == 0){
					alert("Please select room.");
					d.current_room_nr.focus();
					return false;
				}else{
					if (confirm(msg)) {
						$('isdateupdate').value = 1;
					} else {
						$('isdateupdate').value = 0;
						d.admission_date.focus();
						return false;
					}
				}
			} else {
				$('isdateupdate').value = 1;

				if(istobeadmitted==0){
					if (d.cond_code.value == 0){
						alert("Please select patient\'s condition from ER.");
						document.getElementById("cond_code").focus();
						return false;
					} else if (d.result_code.value == 0){
						alert("Please select patient\'s result from ER.");
						document.getElementById("result_code").focus();
						return false;
					}
				}

				if ((d.consulting_dr_nr.value == 0) && (!direct_admission)) {
					alert("Please select an Admitting Physician.");
					d.consulting_dr_nr.focus();
					return false;
				} else if (d.consulting_dept_nr.value == 0) {
					alert("Please select Admitting department.");
					d.consulting_dept_nr.focus();
					return false;
				} else if (d.current_dept_nr.value == 0) {
					alert("Please select the Attending Department.");
					d.current_dept_nr.focus();
					return false;
				} else if (d.current_ward_nr.value == 0) {
					alert("Please select Accomodation.");
					d.current_ward_nr.focus();
					return false;
				} else if (d.current_room_nr.value == 0) {
					alert("Please select room.");
					d.current_room_nr.focus();
					return false;
				} else {
					return true;
				}
			}
		}

		//added by Aiza 06-06-2016
		var from_date_text = $('datefrom').value;
		var from_time_text = $('timefrom').value;
		var from_meridian_text = $('selAMPM3').value;

		var from_time_text_hr = from_time_text.substr(0, 2);
		var from_time_text_min = from_time_text.substr(3);

		if (from_meridian_text == 'PM') {
			if (from_time_text_hr != '12')
				from_time_text_hr = parseInt(from_time_text_hr) + 12;
		} else if (from_meridian_text == 'AM') {
			if (from_time_text_hr == '12')
				from_time_text_hr = '00';
		}

		from_time_text = from_time_text_hr + ":" + from_time_text_min + ":00";
			
		

		var from_dt;
		from_dt = from_date_text + " " + from_time_text;
		var admit_date1 = new Date(adm_dt);
		var consult_date1 = new Date(consult_dt);
		var current_date1 = new Date(current_date);


		//trapping future admission date/time
		if (admit_date1 > current_date1 && consult_date1 > current_date1){
			alert("Admission date/time must be earlier or the same with the current date/time.");
			d.admission_date.focus();
			return false;
		}

		// else if (from_dt, consult_time > current_time){
		// 	alert("From date/time must be earlier or the same with the current date/time.");
		// 	d.datefrom.focus();
		// 	return false;
		// }


		//edited by VAN 03-08-2013
		else if ((d.consulting_dr_nr.value == 0) && (!direct_admission)) {
			//if(d.consulting_dr_nr.value==0){
			alert("Please select an Admitting Physician.");
			d.consulting_dr_nr.focus();
			return false;
		} 

		else if (d.current_ward_nr.value == 0) {
			alert("Please select Accomodation.");
			d.current_ward_nr.focus();
			return false;
		} else if (d.current_room_nr.value == 0) {
			alert("Please select room.");
			d.current_room_nr.focus();
			return false;
		} else if (d.consulting_dept_nr.value == 0) {
			alert("Please select Admitting department.");
			d.consulting_dept_nr.focus();
			return false;

		} else if (d.current_dept_nr.value == 0) {
			alert("Please select the Attending Department.");
			d.current_dept_nr.focus();
			return false;

		}else if (d.consulting_dr_nr.value==0){
			alert("Please select an Admitting Physician.");
			d.consulting_dr_nr.focus();
			return false;
		}
		 else if ((d.er_opd_diagnosis.value == "") && (!direct_admission)) {
			alert("Please enter Admitting Diagnosis.");
			d.er_opd_diagnosis.focus();
			return false;

			//added by VAN 02-06-08
			//}else if((d.current_ward_nr.value==0)&&(!direct_admission)){
			//}else if((d.current_ward_nr.value==0)||(d.current_ward_nr.value==0 &&($('is_wellbaby').value=='1'))){	//modified by CHA, 05082010
		} else if (d.current_ward_nr.value == 0 && ($('is_wellbaby').value == '1')) {	//modified by CHA, 05182010
			// edited by VAN 05-25-2010
			// }else if ((d.current_ward_nr.value==0)&&((parent_enc_nr=="")||(parent_enc_nr==0))){
			alert("Please select a Ward for the patient.");
			d.current_ward_nr.focus();
			return false;

			//}else if ((d.current_ward_nr.value!=0)&&((d.current_room_nr.value==0)||((d.current_bed_nr.value==0)||(d.current_bed_nr.value==null)||(d.current_bed_nr.value=="")))){
		} else if (d.current_room_nr.value == 0 && ($('is_wellbaby').value == '1')) {
			//}else if ((d.current_room_nr.value==0)&&((parent_enc_nr=="")||(parent_enc_nr==0))){
			alert("Please select a Room for the patient.");
			d.current_room_nr.focus();
			/*	}else if ((d.current_room_nr.value!=0)&&((d.current_bed_nr.value==0)||(d.current_bed_nr.value==null)||(d.current_bed_nr.value==""))){
			 alert("Please select a Bed for the patient.");
			 d.current_bed_nr.focus();
			 }
			 return false;
			 */
		} else if (d.encoder.value == "") {
			alert("<?php echo $LDPlsEnterFullName; ?>");
			d.encoder.focus();
			return false;
		} else {
			//alert('here');
			// ONLY consulting department is required for consultation
			if ((encr == 2) && (d.official_receipt_nr.value == "")) {
				//alert("Please enter official receipt number.");
				alert("Please pay first the consultation fee at the cashier \n or go to the social service for classification.");
				d.official_receipt_nr.focus();
				return false;
			} else if (d.current_dept_nr.value == 0) {
				alert("<?php echo $LDPlsSelectDept; ?>");
				d.current_dept_nr.focus();
				return false;
				//}else if((dept_belong == "ER")&&(d.is_DOA.checked)&&(d.is_DOA_reason.value=="")){
			} else if (((allow_er_user == 1) && (ptype == 'er')) && (d.is_DOA.checked) && (d.is_DOA_reason.value == "")) {
				//alert('here');
				alert("Please input the reason of immediate death.");
				d.is_DOA_reason.focus();
				return false;
			} else {
				return true;
			}
		}
	}
	else if(ptype == 'er'){
		//added by aiza 06-06-2016
		//trapping future consultation date/time
		var consultation_dt = $('encounter_dt').value;
		var consultation_tm = $('encounter_time').value;
		var consultation_meridian = $('selAMPM2').value;
		
		var consultation_time_text_hr = consultation_tm.substr(0, 2);
		var consultation_time_text_min = consultation_tm.substr(3);

			if (consultation_meridian == 'PM') {
				if (consultation_time_text_hr != '12')
					consultation_time_text_hr = parseInt(consultation_time_text_hr) + 12;

			} else if (consultation_meridian == 'AM') {
				if (consultation_time_text_hr == '12')
					consultation_time_text_hr = '00';
			}

		consultation_tm = consultation_time_text_hr + ":" + consultation_time_text_min + ":00";
		
		var consult_dt;
		consult_dt = consultation_dt + " " + consultation_tm;
		var consult_date3 = new Date(consult_dt);
		var current_date3 = new Date(current_date);


		if (consult_date3 > current_date3){
			alert("Consultation date/time must be earlier or the same with the current date/time.");
			$('encounter_dt').focus();
			return false;
		}

		if($('current_dept_nr').value == '0'){
			alert('Consulting Department is required.');
			$('current_dept_nr').focus();
			return false;
		}

		if($('is_medico').checked) {
			if($('DOI').value == '') {
				alert('Please enter Date of Incident');
				$('DOI').focus();
				return false;
			} else if($('TOI').value == '') {
				alert('Please enter Time of Incident');
				$('TOI').focus();
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	//added by aiza 06-07-16
	else if(ptype == 'phs'){
		//trapping future consultation date/time
		var consultation_dt = $('encounter_dt').value;
		var consultation_tm = $('encounter_time').value;
		var consultation_meridian = $('selAMPM2').value;
		
		var consultation_time_text_hr = consultation_tm.substr(0, 2);
		var consultation_time_text_min = consultation_tm.substr(3);

			if (consultation_meridian == 'PM') {
				if (consultation_time_text_hr != '12')
					consultation_time_text_hr = parseInt(consultation_time_text_hr) + 12;

			} else if (consultation_meridian == 'AM') {
				if (consultation_time_text_hr == '12')
					consultation_time_text_hr = '00';
			}

		consultation_tm = consultation_time_text_hr + ":" + consultation_time_text_min + ":00";
		
		var consult_dt;
		consult_dt = consultation_dt + " " + consultation_tm;
		var consult_date4 = new Date(consult_dt);
		var current_date4 = new Date(current_date);

		if (consult_date4 > current_date4){
			alert("Consultation date/time must be earlier or the same with the current date/time.");
			$('encounter_dt').focus();
			return false;
		}
		return true;

	}
	else{
		return true;
	}
}

		//added by aiza
	
		 function chkAdmissionDate(){
				
			var current_date = '<?= date("m/d/Y H:i:s")?>';
			var current_time = '<?= date("h:i")?>';

			var admission_date_text = $('admission_date').value;
			var admission_time_text = $('admission_time').value;
			var admission_meridian_text = $('selAMPM2').value;

			var admission_time_text_hr = admission_time_text.substr(0, 2);
			var admission_time_text_min = admission_time_text.substr(3);

			if (admission_meridian_text == 'PM') {
				if (admission_time_text_hr != '12')
					admission_time_text_hr = parseInt(admission_time_text_hr) + 12;

			} else if (admission_meridian_text == 'AM') {
				if (admission_time_text_hr == '12')
					admission_time_text_hr = '00';
			}

			admission_time_text = admission_time_text_hr + ":" + admission_time_text_min + ":00";
			//alert('admission_time_text = '+admission_time_text);
			

			var adm_dt, consult_dt;
			adm_dt = admission_date_text + " " + admission_time_text;
			var adm_date1 = new Date(adm_dt);
			var adm_date2 = new Date(current_date);

			if(isValidDate2($('admission_date').value)) {
		 		if (adm_date1 > adm_date2){
					alert('Admission Date/Time must be earlier or the same with the current date/time.');
			 		$('admission_date').value = '<?= date("m/d/Y")?>';
			 		$('admission_time').value = current_time;

				}

		 	} else {
		 		alert('Invalid date entered.');
		 		$('admission_date').value = '<?= date("m/d/Y")?>';
		 		$('admission_time').value = current_time;
		 	}

		 	console.log();
		 	document.getElementById('selAMPM2').style.visibility = 'visible';
		 }

		 function chkConsultationDate(){
		 	var current_date = '<?= date("m/d/Y H:i:s")?>';
			var current_time = '<?= date("h:i")?>';
				
			var consultation_dt = $('encounter_dt').value;
			var consultation_tm = $('encounter_time').value;
			var consultation_meridian = $('selAMPM2').value;
			
			var consultation_time_text_hr = consultation_tm.substr(0, 2);
			var consultation_time_text_min = consultation_tm.substr(3);

				if (consultation_meridian == 'PM') {
					if (consultation_time_text_hr != '12')
						consultation_time_text_hr = parseInt(consultation_time_text_hr) + 12;

				} else if (consultation_meridian == 'AM') {
					if (consultation_time_text_hr == '12')
						consultation_time_text_hr = '00';
				}

			consultation_tm = consultation_time_text_hr + ":" + consultation_time_text_min + ":00";
			
			var consult_dt;
			consult_dt = consultation_dt + " " + consultation_tm;
			var consult_date5 = new Date(consult_dt);
			var current_date5 = new Date(current_date);

			if(isValidDate2($('encounter_dt').value)) {
		 		if (consult_date5 > current_date5){
					alert("Consultation date/time must be earlier or the same with the current date/time.");
			 		$('encounter_dt').value = '<?= date("m/d/Y")?>';
			 		$('encounter_time').value = current_time;

				}

		 	} else {
		 		alert('Invalid date entered.');
		 		$('encounter_dt').value = '<?= date("m/d/Y")?>';
		 		$('encounter_time').value = current_time;
		 	}

		 	console.log();
		 	document.getElementById('selAMPM2').style.visibility = 'visible';
		 }
		function isValidDate2(s) {
			var bits = s.split('/');
		  	var y = bits[2], d  = bits[1], m = bits[0];
		  	// Assume not leap year by default (note zero index for Jan)
		  	var daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];

		  	// If evenly divisible by 4 and not evenly divisible by 100,
		  	// or is evenly divisible by 400, then a leap year
		  	if ( (!(y % 4) && y % 100) || !(y % 400)) {
		    	daysInMonth[1] = 29;
		  	}
		  	return d <= daysInMonth[--m]
		}


function resolveLoc(){
	d=document.aufnahmeform;
		var sex = '<?=$sex?>';
		var age = '<?=$age?>';

	if(d.encounter_class_nr[1].checked==true){  // for OPD
		d.current_ward_nr.selectedIndex=0;
		document.aufnahmeform.current_ward_nr.disabled = true;
		document.getElementById("doctor_label").innerHTML = "Consultant Doctor";
		xajax_setDoctors(sex, age,0,0);       // get all OPD doctors
		xajax_setALLDepartment(0);	  // get all OPD Department

	}else {  // for IPD, ER-Admission and OPD-Admission

		if (d.encounter_class_nr[0].checked==true){
			d.current_ward_nr.selectedIndex=0;
			document.aufnahmeform.current_ward_nr.disabled = true;
		}else{
			document.aufnahmeform.current_ward_nr.disabled = false;
		}
		d.current_dept_nr.selectedIndex=0;
		document.getElementById("doctor_label").innerHTML = "Attending Doctor";
		xajax_setDoctors(sex, age,1,0);		// get all IPD doctors
		xajax_setALLDepartment(1);	// get all OPD Department

	}
}

//added by VAN 06-12-08

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
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
}

// Added by Gervie 11/15/2015
function compare_date(p_type){

	if(p_type == 'er') {
		var consultation_dt = $('encounter_dt').value;
		var consultation_tm = $('encounter_time').value;
	}
	else{
		var consultation_dt = $('admission_date').value;
		var consultation_tm = $('admission_time').value;
	}
	var doi = $('DOI').value;
	var toi = $('TOI').value;
	var meridian = $('selAMPM').value;
	var meridian2 = $('selAMPM2').value;

	var regExp = /(\d{1,2})\:(\d{1,2})\:(\d{1,2})/;

	if((p_type == 'er') || (p_type == 'ipd')) {
		var date1 = Date.parse(consultation_dt);
		var date2 = Date.parse(doi);
		var date_diffs = (date1 - date2);

		var dis_time;
		var dis_time2;

		var toi_hour = toi.substring(0,2);
		var toi_minute = toi.substring(3,5);

		var c_hour = consultation_tm.substring(0,2);
		var c_minute = consultation_tm.substring(3,5);

		if (meridian == 'PM'){
			if (parseFloat(toi_hour) < 12)
				toi_hour = parseFloat(toi_hour) + 12;

			dis_time = toi_hour+":"+toi_minute+":00";
		}else{
			if (parseFloat(toi_hour) == 12)
				toi_hour = '00';

			dis_time = toi_hour+":"+toi_minute+":00";
		}

		if (meridian2 == 'PM'){
			if (parseFloat(c_hour) < 12)
				c_hour = parseFloat(c_hour) + 12;

			dis_time2 = c_hour+":"+c_minute+":00";
		}else{
			if (parseFloat(c_hour) == 12)
				c_hour = '00';

			dis_time2 = c_hour+":"+c_minute+":00";
		}

		if(isNaN(date_diffs)){
			alert("Please enter Date of Incident.");
			$('TOI').value = '';
			$('DOI').focus();
		}
		else {
			if (date_diffs < 0) {
				alert('Date of Incident must be earlier than or same with the Consultation Date.');
				$('DOI').value = '';
				$('TOI').value = '';
				$('DOI').focus();
			}
			else if (date_diffs == 0) {
				if (toi != '') {
					if (parseFloat(dis_time.replace(regExp, "$1$2$3")) > parseFloat(dis_time2.replace(regExp, "$1$2$3"))) {
						alert("Time of Incident must be earlier than the Consultation Time");
						$('TOI').value = '';
						$('TOI').focus();
					}
				}
			}
			else {
				seg_setValidDate(true);
			}
		}
	}
	else{
		seg_setValidDate(true);
	}
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//	var time = $('time_text_d');
//alert(AMPM);
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
		hour = '0' + stime.substring(0,1);
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
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 hour = "0" + hour;
		 document.getElementById(AMPM).value = "PM";
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

//---------------------------------

function ReferralHandler1(){
	var d = document.aufnahmeform;
	d.referrer_dr.value = " ";
}


function GetDr_nr(){
	var d = document.aufnahmeform;
	var dr_split;
	var i;
	var dr_name = d.referrer_dr_name.value;
	dr_name = dr_name.toLowerCase();

	if (dr_name.match("dr.")!=null){
		dr_name_split = dr_name.substr(4);
	}else{
		dr_name_split = dr_name;
	}

	dr_split = dr_name_split.split(" ");

	if (dr_split.length==2){
		d.name1.value = dr_split[0];
		d.lname.value = dr_split[1];
	}else if(dr_split.length==3){
		d.name1.value = dr_split[0];
		d.name2.value = dr_split[1];
		d.lname.value = dr_split[2];
	}
}

//added by VAN 05-26-08
function showDOAReason(){
	if (document.getElementById('is_DOA').checked==true){
		document.getElementById('is_DOA_reason').style.display = '';
		document.getElementById('reason_label').style.display = '';
		document.getElementById('is_DOA_reason').value = document.getElementById('is_DOA_reason2').value;

	}else{
		document.getElementById('is_DOA_reason').style.display = 'none';
		document.getElementById('reason_label').style.display = 'none';
		$('is_DOA_reason').value = "";
	}
}
//-------------------

//added by VAN 04-28-08
function checkMedico(){
	if (document.getElementById('is_medico')){
		if (document.getElementById('is_medico').checked==true){
			//show medico legal info
			document.getElementById('ERMedico').style.display = '';
			//added by VAN 06-12-08
			document.getElementById('ERMedicoPOI').style.display = '';
			document.getElementById('ERMedicoTOI').style.display = '';
			document.getElementById('ERMedicoDOI').style.display = '';

			if (document.getElementById('medicoOT').checked==true){
				enableTextBox('medicoOT');
			}

		}else{
			//hide medico legal info
			document.getElementById('ERMedico').style.display = 'none';
			//added by VAN 06-12-08
			document.getElementById('ERMedicoPOI').style.display = 'none';
			document.getElementById('ERMedicoTOI').style.display = 'none';
			document.getElementById('ERMedicoDOI').style.display = 'none';
		}
	}	
}

function preset(){
	$('saveButton').disabled = false;
	var d = document.aufnahmeform;
	var encounter_class_nr;
	var update = <?php echo $update; ?>;
	var dept_belong = "<?php echo $dept_belong['id']; ?>";
	var allow_er_user = '<?php echo $allow_er_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var allow_phs_user = '<?php echo $allow_phs_user; ?>';
	var allow_medocs_user = '<?php echo $allow_medocs_user; ?>';
	var allow_ipd_user = '<?php echo $allow_ipd_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var allow_opd_user = '<?php echo $allow_opd_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var ptype = '<?php echo $ptype; ?>';
	var dept_nr = "<?php echo $current_dept_nr?$current_dept_nr:0; ?>";
	var er_dept_nr = "<?php echo $consulting_dept_nr?$consulting_dept_nr:0; ?>";  // burn added : May 16, 2007
	var er_dr_nr = "<?php echo $consulting_dr_nr; ?>";  // burn added : May 16, 2007
	var encounter_class_nr = "<?php echo $encounter_class_nr; ?>";
	var encounter_type = "<?php echo $encounter_type; ?>";
	var doc = "<?php echo $current_att_dr_nr; ?>";
	var dr_name = "<?php echo $patient_enc['consulting_dr']; ?>";
	var dr_dept = "<?php echo $patient_doc['name_formal']; ?>";
	var cond_code = "<?php echo $patient_enc_cond['cond_code']; ?>";
	var disp_code = "<?php echo $patient_enc_disp['disp_code']; ?>";
	var res_code = "<?php echo $patient_enc_res['result_code']; ?>";
	var ward = "<?php echo $current_ward_nr; ?>";
	var room = "<?php echo $current_room_nr; ?>";
	var sex = '<?=$sex?>';
	var age = '<?=$age?>';
	var phs_dr_login = document.getElementById('phs_dr').value;
	var personell_nr = "<?php echo $current_room_nr; ?>";
	var items = document.getElementsByName('items[]');
	var OPD_consulting_dr = "<?php echo $consulting_dr_nr; ?>";
	var OPD_consulting_dept = "<?php echo $consulting_dept_nr; ?>";
	var IPD_admitting_dr = "<?php echo $consulting_dr_nr; ?>";
	var IPD_admitting_dept = "<?php echo $consulting_dept_nr; ?>";
	var IPD_attending_dr = "<?php echo $current_att_dr_nr; ?>";
	var IPD_attending_dept = "<?php echo $current_dept_nr; ?>";
	var IPBMdept_nr = "<?php echo IPBMdept_nr; ?>";
	if (ptype=='opd'){
		if ($('official_receipt_nr').value=="")
			$('official_receipt_nr').focus();
		else
			$('current_dept_nr').focus();
	}
	
	if (((encounter_type==3)||(encounter_type==4)||(encounter_type==13))&&(items.length > 0)) {
			document.getElementById('iconIns').style.display = '';
	}
	if (ptype=='er') enableTextBox('medicoOT');
	if ((allow_ipd_user)&&(ptype=='ipd')) {
		getMode($('assign_mode').value);
		checkMedico();
	}
	if ((allow_er_user==1)&&(ptype=='er')){
		checkMedico();
		showDOAReason();
	}
<?php
	if (($seg_direct_admission)||($encounter_type==3)||($encounter_type==4)||($encounter_type==IPBMIPD_enc)) {
?>
		//commented by VAN 01-24-09
		document.getElementById('rowDateAdmit').style.display = "";
		document.getElementById('rowTimeAdmit').style.display = "";
		document.getElementById('rowDateConsult').style.display = "none";
		document.getElementById('rowTimeConsult').style.display = "none";
		<?php 
			if($isIPBM&&!$encounter_nr){
		?>
			xajax_setAllDepartmentEROPD(1,IPBMdept_nr);
			xajax_setDoctorsEROPD(sex,age,1,IPBMdept_nr,0);
			xajax_setAllDepartmentIPD(1,IPBMdept_nr);
			xajax_setDoctorsIPD(sex,age,1,IPBMdept_nr,0);
		<?php 
			}else{
		?>
			xajax_setAllDepartmentEROPD(1,IPD_admitting_dept);
			xajax_setDoctorsEROPD(sex,age,1,IPD_admitting_dept,IPD_admitting_dr);
			xajax_setAllDepartmentIPD(1,IPD_attending_dept);
			xajax_setDoctorsIPD(sex,age,1,IPD_attending_dept,IPD_attending_dr);
		<?php 
			}
		?>
		// xajax_setALLDepartment(1,ptype);
		// // alert(IPD_attending_dept+"---"+IPD_attending_dr);
		// // xajax_setDoctors(sex,age,1,IPD_attending_dept,IPD_attending_dr,ptype);
		// alert(IPD_attending_dept);
		// ajxSetDepartment(IPD_attending_dept);
		// ajxSetDoctor(IPD_attending_dr);
		// xajax_setDepartments(IPD_attending_dr,IPD_attending_dept,sex,age);
		xajax_setRooms(ward,room);
		xajax_setBeds(ward,room);
<?php
	}else{
?>
		document.getElementById('rowDateAdmit').style.display = "none";
		document.getElementById('rowTimeAdmit').style.display = "none";
		document.getElementById('rowDateConsult').style.display = "";
		document.getElementById('rowTimeConsult').style.display = "";
		<?php 
			if($isIPBM&&!$encounter_nr){
		?>
			xajax_setAllDepartmentIPD(0,IPBMdept_nr);
			xajax_setDoctorsIPD(sex,age,0,IPBMdept_nr,0);
		<?php 
			}elseif($ptype=='er'||($ptype=='ipd'&&($encounter_type==1))){
		?>
			xajax_setAllDepartmentEROPD(1,OPD_consulting_dept);
			xajax_setDoctorsEROPD(sex,age,1,OPD_consulting_dept,OPD_consulting_dr);
			xajax_setAllDepartmentIPD(1,IPD_attending_dept);
			xajax_setDoctorsIPD(sex,age,1,IPD_attending_dept,IPD_attending_dr);
		<?php 
			}else{
		?>
			xajax_setAllDepartmentEROPD(0,OPD_consulting_dept);
			xajax_setDoctorsEROPD(sex,age,0,OPD_consulting_dept,OPD_consulting_dr);
			xajax_setAllDepartmentIPD(0,IPD_attending_dept);
			xajax_setDoctorsIPD(sex,age,0,IPD_attending_dept,IPD_attending_dr);
		<?php 
			}
		?>
		if ((encounter_type==1) || (encounter_type==2) || (encounter_type==14)){
			jsShowConditionResultDisposition(false);   // burn added : May 16, 2007
		}
		if ((encounter_class_nr == 2)||((encounter_class_nr == 2)&&(encounter_type == 2 || encounter_type == 14))){
			if (ptype=='phs'){
				personell_nr = phs_dr_login;
			}else{
				personell_nr = 0;
			}
			if (((allow_er_user==1)&&(ptype=='er')) || ((allow_opd_user==1)&&(ptype=='opd')) || ((allow_phs_user==1)&&(ptype=='phs'))){
			}else{
			}
		}else{

		}
		if ((update == 1)&&((allow_ipd_user)&&(ptype=='ipd'))){
			if (((allow_ipd_user)&&(ptype=='ipd')) && (encounter_type!=4)){
				if(cond_code) d.cond_code[cond_code-1].checked = true;
				if(disp_code) d.disp_code[disp_code-1].checked = true;
					if(res_code) d.result_code[res_code-1].checked = true;
					d.consulting_dr.value = dr_name;
			}
		}
<?php
	}
?>
			
	if ((update == 1)&&((allow_ipd_user)&&(ptype=='ipd')) && ((encounter_type==3)||(encounter_type==4)||(encounter_type==13))){

			//alert("update");
			if (d.insurance_class_nr[2].checked == true){
				//alert("2");
				//d.insurance_nr.readOnly = true;
				//d.insurance_nr.value=" ";
				//d.insurance_firm_name.value=" ";
				document.getElementById('iconIns').style.display = 'none';
			}else if((d.insurance_class_nr[0].checked == true)||(d.insurance_class_nr[1].checked == true)){
				//d.insurance_nr.readOnly = false;
				//alert("0 or 1");
				document.getElementById('iconIns').style.display = '';
			}else{
				//alert("else");
				d.insurance_class_nr[2].checked = true;
				//d.insurance_nr.readOnly = true;
				//d.insurance_nr.value=" ";
				//d.insurance_firm_name.value=" ";
				document.getElementById('iconIns').style.display = 'none';
			}
	}
	//added by VAN 05-21-2010
	if ((update == 1)&&((allow_ipd_user)&&(ptype=='ipd'))){
		//var mode = $('assign_mode').value;
		mode = 'correct';
		showDateTime(mode);
		$('is_same').value=1;
	}
}

//added by VAN 08-29-09
function enableTextBox(objID){
		//alert('case = '+obj.id);
		
		if (objID=='medicoOT'){
				if (document.getElementById(objID).checked)
						document.getElementById('description').style.display="";
					
				else
						document.getElementById('description').style.display="none";
		}
}
//--------------

//---- AJAX -----------------------
	/*
		burn added : May 16, 2007
	*/
function ajxClearOptionEROPDDrDept(DrDept) {
	var optionsList;
	var el;

	if (DrDept==0)
		el=document.aufnahmeform.consulting_dr_nr;
	else
		el=document.aufnahmeform.consulting_dept_nr;

	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptionEROPDDrDept */

function ajxClearOptionIPDDrDept(DrDept) {
	var optionsList;
	var el;

	if (DrDept==0)
		el=document.aufnahmeform.current_att_dr_nr;
	else
		el=document.aufnahmeform.current_dept_nr;

	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptionEROPDDrDept */

function ajxSetEROPDDrDept(DrDept,DrDept_nr) {
	if (DrDept==0)
		document.aufnahmeform.consulting_dr_nr.value = DrDept_nr;
	else
		document.aufnahmeform.consulting_dept_nr.value = DrDept_nr;
}/* end of function ajxSetEROPDDrDept */

function ajxSetIPDDrDept(DrDept,DrDept_nr) {
	if (DrDept==0)
		document.aufnahmeform.current_att_dr_nr.value = DrDept_nr;
	else
		document.aufnahmeform.current_dept_nr.value = DrDept_nr;
}/* end of function ajxSetEROPDDrDept */

	/*
		burn added : May 16, 2007
	*/
function ajxAddOptionEROPDDrDept(DrDept, text, value) {
	var grpEl;

	if (DrDept==0)
		grpEl=document.aufnahmeform.consulting_dr_nr;
	else
		grpEl=document.aufnahmeform.consulting_dept_nr;

	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
}/* end of function ajxAddOptionEROPDDrDept */

function ajxAddOptionIPDDrDept(DrDept, text, value) {
	var grpEl;
	
	if (DrDept==0)
		grpEl=document.aufnahmeform.current_att_dr_nr;
	else
		grpEl=document.aufnahmeform.current_dept_nr;

	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
}/* end of function ajxAddOptionEROPDDrDept */

	/*
		burn added : May 24, 2007
	*/
function jsGetEROPDDepartment(){
		var d = document.aufnahmeform;
		// document.getElementById('consulting_dept_nr').value = "-Select a Department-"; // commented by carriane 10/31/17
		//alert("s");
		var aPersonell_nr=d.consulting_dr_nr.value;
		var sex = '<?=$sex?>';
		var age = '<?=$age?>';
		//added by VAN 06-17-09
		var dept_nr = document.getElementById('consulting_dept_nr').value;
		//alert(aPersonell_nr+" - "+dept_nr);

		if (aPersonell_nr != 0) {
			xajax_setDepartmentEROPD(aPersonell_nr, sex, age,dept_nr);
		} else{
			d.consulting_dept_nr.value = 0;
		}
}
	/*
		burn added : May 24, 2007
	*/
function jsGetEROPDDoctors(){
		var d = document.aufnahmeform;
		var aDepartment_nr=d.consulting_dept_nr.value;

		var aDoctor=d.consulting_dr_nr;
		var aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;

		var sex = '<?=$sex?>';
		var age = '<?=$age?>';

		if (aDepartment_nr != 0) {
			xajax_setDoctorsEROPD(sex, age, 0,aDepartment_nr,aDoctor_nr);	//get the list of ER-OPD doctors under "aDepartment_nr" department
		} else{
			xajax_setDoctorsEROPD(sex, age,0,aDoctor_nr);	//get the list of ER-OPD doctors
		}
}

function ajxClearOptions(status) {
var optionsList;
var el;

	if (status==0){
		el=document.aufnahmeform.current_att_dr_nr;
	}else{
		el=document.aufnahmeform.current_dept_nr;
	}

	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptions */

function ajxAddOption(status, text, value) {
var grpEl;

	if (status==0){
		grpEl=document.aufnahmeform.current_att_dr_nr;
	}else{
		grpEl=document.aufnahmeform.current_dept_nr;
	}

	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');

}/* end of function ajxAddOption */


function ajxSetDepartment(dept_nr) {
		//document.aufnahmeform.current_dept_nr.value = dept_nr;
		document.getElementById('current_dept_nr').value = dept_nr;
	//alert('dept_nr = '+dept_nr+" - "+document.aufnahmeform.current_dept_nr.value);
}

function ajxSetDoctor(personell_nr) {
	//document.aufnahmeform.current_att_dr_nr.value = personell_nr;
		document.getElementById('current_att_dr_nr').value = personell_nr;
	//alert('personell_nr = '+personell_nr+" - "+document.aufnahmeform.current_att_dr_nr.value);
}

function ajxSetEROPDDepartment(dept_nr) {
		//document.aufnahmeform.consulting_dept_nr.value = dept_nr;
	document.getElementById('consulting_dept_nr').value = dept_nr;
	//document.getElementById('current_dept_nr').value = dept_nr;
}

function ajxSetEROPDConsultDoctor(personell_nr) {
		//document.aufnahmeform.consulting_dept_nr.value = dept_nr;
		document.getElementById('consulting_dr_nr').value = personell_nr;
}

function ajxSetEROPDDoctor() {
	xajax_setDoctorsEROPD(null);
}

//----added by VAN 02-01-08
function jsSetRoom(){
	var d = document.aufnahmeform;
	var aWardNr = d.current_ward_nr;
	//var aRoomNr = d.current_room_nr;

	//alert("ward nr = "+aWardNr.value);
	//alert("ward nr = "+aRoomNr.value);

	document.getElementById('tbBedRoom').innerHTML = "";
	//xajax_setRooms(aWardNr.value, aRoomNr.value);
	xajax_setRooms(aWardNr.value);

}

function ajxSetRoom(room_nr) {
	var OB_MAIN_WARD = "15"; //#current_ward_nr value
	var OB_DEFAULT_ROOM = "1000"; // #current_ward_nr:<option> value
	var d = document.aufnahmeform;
	var aWardNr = d.current_ward_nr;
	// alert($('prev_ward_nr').value+"---"+$('prev_room_nr').value);
	if(initiator==0 && $('prev_ward_nr').value!='0' && $('prev_ward_nr').value!='0'){
		$('current_ward_nr').value=$('prev_ward_nr').value;
		$('current_room_nr').value=$('prev_room_nr').value;
		jsSetBed();
		initiator++;
	}else{
		if(aWardNr.value == OB_MAIN_WARD){
			document.aufnahmeform.current_room_nr.value = OB_DEFAULT_ROOM;
			jsSetBed();
		}else{
			document.aufnahmeform.current_room_nr.value = 0;
			jsSetBed();
		}
	}
	
	//xajax_setBeds(aWardNr.value, room_nr);
	
}/* end of function ajxSetRoom */

function jsSetBed(){
	var d = document.aufnahmeform;
	var aWardNr = d.current_ward_nr;
	var aRoomNr = d.current_room_nr;
	var ward = "<?php echo $current_ward_nr; ?>";
	var wardnr;

	if (aWardNr.value==null)
		wardnr = ward;
	else
		wardnr = aWardNr.value;
	//alert("ward nr, room = "+wardnr+" , "+aRoomNr.value);
	//xajax_setBeds(aWardNr.value, aRoomNr.value);
	xajax_setBeds(wardnr, aRoomNr.value);
}

//added by VAN 08-20-08
function getCurrentBed(current_value){
	$('cur_bed_nr').value = current_value;
	chckRoomModeStatus();
}
//---------------------
//edited by art 11/29/14 added new parameter for locked beds
function ajxGetBedRoom(nr_bed, patient, patient2,locked_beds){
	var d = document.aufnahmeform;
	var aWardNr = d.current_ward_nr;
	var aRoomNr = d.current_room_nr;
	var dBody = document.getElementById('tbBedRoom');
	var gender, sex, occupy;
	var room = "<?php echo $current_room_nr; ?>";
	//var bed = document.getElementById('bed_nr').value;
	//alert("bed = "+document.getElementById('bed_nr').value);
	//alert('bed = '+bed);

	if (dBody) {
		var dRows, newRowSrc, i;
		dRows=dBody.getElementsByTagName("tr");

		// clear the table
		dBody.innerHTML = "";
		//alert("bed = "+document.getElementById('bed_nr').value);
		$('prev_bed_nr').value = document.getElementById('bed_nr').value;
		$('cur_bed_nr').value = document.getElementById('bed_nr').value;

		if (nr_bed!=0) {
			for (i=1; i <= nr_bed; i++){
				//alert(i+" < = "+document.getElementById('bed_nr').value)
				if ((document.getElementById('bed_nr').value==i)&&(aRoomNr.value==room))
					occupy = '<img src="../../gui/img/common/default/check-r.gif" border="0">';
				else
					occupy = "";

				if (patient[aRoomNr.value][i]=='m'){
					//sex = 'Male Patient';
					sex = '<img src="../../gui/img/common/default/spm.gif" border="0"> Occupied';
					if ((document.getElementById('bed_nr').value==i)&&(aRoomNr.value==room))
						vacant = '<input type="radio" id="current_bed_nr" checked name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
					else
						vacant = '<img src="../../gui/img/common/default/padlock.gif" border="0">';

				}else if (patient[aRoomNr.value][i]=='f'){
					//sex = 'Female Patient';
					sex = '<img src="../../gui/img/common/default/spm.gif" border="0"> Occupied';
					if ((document.getElementById('bed_nr').value==i)&&(aRoomNr.value==room))
						vacant = '<input type="radio" id="current_bed_nr" checked name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
					else
						vacant = '<img src="../../gui/img/common/default/padlock.gif" border="0">';
				//added by art 11/29/14 for locked beds
				}else if(locked_beds[aRoomNr.value][i] == 1){
					sex = '<img src="../../gui/img/common/default/delete2.gif" border="0"> Locked   ';
					vacant = '<img src="../../gui/img/common/default/padlock.gif" border="0">';

				}else{
					sex = '<img src="../../gui/img/common/default/patdata.gif" border="0"> Vacant   ';
					vacant = '<input type="radio" id="current_bed_nr" name="current_bed_nr" onClick="getCurrentBed(this.value);" value="'+i+'"">';
				}

				newRowSrc = '<tr>' +
									/*'<td align="left">'+String.fromCharCode(64+i)+'</td>'+ */
									'<td align="left">'+i+'</td>'+
									'<td align="left">'+sex+'</td>'+
									'<td align="center">'+vacant+'</td>'+
									'<td align="center">'+occupy+'</td>'+
								'</tr>';
				dBody.innerHTML += newRowSrc;
				//alert(dBody.innerHTML);
			}
		}else{
			dBody.innerHTML = "";
		}
	}
	//alert('nr_bed = '+nr_bed);
}

function ajxClearOptionsRoom() {
var optionsList;
var el;

	el=document.aufnahmeform.current_room_nr;

	if (el) {
		optionsList = el.getElementsByTagName('OPTION');
		for (var i=optionsList.length-1;i>=0;i--) {
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}/* end of function ajxClearOptionsRoom */

function ajxAddOptionRoom(text, value) {
var grpEl;

	grpEl=document.aufnahmeform.current_room_nr;

	if (grpEl) {
		var opt = new Option( text, value );
		opt.id = value;
		grpEl.appendChild(opt);
	}
	var optionsList = grpEl.getElementsByTagName('OPTION');

}/* end of function ajxAddOptionRoom */

function jsSetRooms2(){
	d = document.aufnahmeform;
	alert(d.current_ward_nr.value);
	xajax_setRooms2();
}
//------------------------------

function jsGetDepartment(){
		var d = document.aufnahmeform;
		// document.getElementById('current_dept_nr').value = "-Select a Department-"; // commented by carriane 10/31/17
		var aDoctor=d.current_att_dr_nr;
		var aPersonell_nr;
		var optionsList;
		var sex = '<?=$sex?>';
		var age = '<?=$age?>';

		var isIPBM = "<?=$isIPBM?>";

		//added by VAN 06-17-09
		var dept_nr = document.getElementById('current_dept_nr').value;

		d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
		aPersonell_nr = aDoctor.value;
		
		if (aPersonell_nr != 0) {
			xajax_setDepartments(aPersonell_nr,dept_nr,sex,age);
			optionsList = aDoctor.getElementsByTagName('OPTION');
		} else{
			if(isIPBM == 0)
				d.current_dept_nr.value = 0;
		}

		if (d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text != "-Select a Doctor-"){
			d.consulting_dr.value = d.current_att_dr_nr.options[d.current_att_dr_nr.selectedIndex].text;
		}else{
			d.consulting_dr.value = " ";
		}
}

function jsGetDoctors(){
	var d = document.aufnahmeform;
	var aDepartment=d.current_dept_nr.value;
	var aDoctor=d.current_att_dr_nr.value;

	var aDepartment_nr;
	var optionsList;
	var encounter_class_nr = <?php echo $encounter_class_nr; ?>;
	var update = <?php echo $update; ?>;
	var encounter_type = "<?php echo $encounter_type; ?>";
	var dept_belong = "<?php echo $dept_belong['id']; ?>";

	var allow_er_user = '<?php echo $allow_er_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var allow_phs_user = '<?php echo $allow_phs_user; ?>';
	var allow_medocs_user = '<?php echo $allow_medocs_user; ?>';
	var allow_ipd_user = '<?php echo $allow_ipd_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';
	var allow_opd_user = '<?php echo $allow_opd_user||$isIPBM||(($isIPBM&&$ptype=='ipd')?1:0); ?>';

	var current_dept_nr = "<?php echo $current_dept_nr; ?>";
	var current_doc = "<?php echo $current_att_dr_nr; ?>";

	var ptype = '<?php echo $ptype; ?>';
	var sex = '<?=$sex?>';
	var age = '<?=$age?>';

	var aDepartment_nr = aDepartment;
	var aDoctor_nr = aDoctor;
	// var aDepartment_nr = aDepartment.options[aDepartment.selectedIndex].value;
	// var aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;
	var phs_dr_login = document.getElementById('phs_dr').value;
	var personell_nr;

	if (phs_dr_login)
		personell_nr = phs_dr_login;

	d.consulting_dr.value = " ";

	if (update != 1){
		//alert(aDoctor_nr);
		if (encounter_class_nr == 1){
			if (aDepartment_nr != 0){
				xajax_setDoctors(sex, age,1,aDepartment_nr,aDoctor_nr);
			}else{
				xajax_setDoctors(sex, age,1,0,aDoctor_nr);			// get all IPD doctors
			}
		}else{
			if (aDepartment_nr != 0){
				xajax_setDoctors(sex, age,0,aDepartment_nr,aDoctor_nr,ptype,1);
			}else{
				xajax_setDoctors(sex, age,0,aDepartment_nr,aDoctor_nr,ptype,1);			// get all OPD doctors
			}
		}
	}else{

		//if ((encounter_type==2)&&(encounter_class_nr==2)&&(dept_belong!="Admission")){
		if ((encounter_type==2)&&(encounter_class_nr==2)&&((allow_ipd_user==1)&&(ptype=='ipd'))){
			if (aDepartment_nr != 0){
				xajax_setDoctors(sex, age,0,aDepartment_nr,aDoctor_nr);
			}else{
				xajax_setDoctors(sex, age,0,0,aDoctor_nr);			// get all IPD doctors
			}
		}else{
			 if (aDepartment_nr != 0){
				if (aDoctor_nr==0)
					aDoctor_nr = current_doc;

				xajax_setDoctors(sex, age,1,aDepartment_nr,aDoctor_nr);
			}else{
				xajax_setDoctors(sex,age,1,0,aDoctor_nr);			// get all IPD doctors
			}
		}
	}
}

//added by VAN------

var trayItems = 0;

function openOrderTray() {
	window.open("seg-reg-insurance-tray.php<?=URL_APPEND?>&clear_ck_sid=<?=$clear_ck_sid?>","patient_select","width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
}


//-----------EDITED BY VAN
function DisableInsurance(){
	var d = document.aufnahmeform;
	var rowSrc;
	var list = document.getElementById('order-list');
	var dBody=list.getElementsByTagName("tbody")[0];

	if(d.insurance_class_nr[2].checked==true){
		document.getElementById('iconIns').style.display = 'none';
		rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>'
		dBody.innerHTML = rowSrc;

	}else{
		document.getElementById('iconIns').style.display = '';
		rowSrc = " ";
		//-----added by VAN---------------------
		<?php
			$result = $encounter_obj->getPersonInsuranceItems($encounter_nr);
			$rows=array();
			while ($row=$result->FetchRow()) {
				$rows[] = $row;
			}
			foreach ($rows as $i=>$row) {
				if ($row) {

					$count++;
					$alt = ($count%2)+1;

					$sql2 = "SELECT ci.* FROM care_person_insurance AS ci
								WHERE ci.pid =".$pid."
								AND ci.hcare_id = '".$row['hcare_id']."'";
					$res=$db->Execute($sql2);

					$row2=$res->RecordCount();

					if ($row2!=0){
						while($rsObj=$res->FetchRow()) {
							$ins_nr = $rsObj["insurance_nr"];
							$is_principal = $rsObj["is_principal"];
							if ($is_principal){
								$principal = "YES";
							}else{
								$principal = "NO";
							}
						}
					}
			?>
					rowSrc +='<tr class="wardlistrow<?= $alt; ?>" id="row<?= $row['hcare_id'];?>">' +
									'<input type="hidden" name="items[]" id="rowID<?=$row['hcare_id'];?>" value="<?=$row['hcare_id'];?>" />'+
									'<input type="hidden" name="nr[]" id="rowNr<?=$row['hcare_id'];?>" value="<?=$ins_nr;?>" />'+
									'<input type="hidden" name="is_principal[]" id="rowis_principal<?=$row['hcare_id'];?>" value="<?=$is_principal;?>" />'+
									'<td class="centerAlign"><a href="javascript:removeItem(\'<?= $row['hcare_id'];?>\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
									'<td width="*" id="name<?= $row['hcare_id'];?>"><?= $row['firm_id'];?></td>'+
									'<td width="25%" align="right" id="inspin<?= $row['hcare_id'];?>"><?= $ins_nr; ?></td>'+
									'<td width="18%" class="centerAlign" id="insprincipal<?= $row['hcare_id'];?>"><?= $principal; ?></td>'+
									'<td></td>'+
							'</tr>';

		<?php }
			}
		?>
		if (rowSrc==" "){
			rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>'
		}
		dBody.innerHTML = rowSrc;

		//----------------------------------------
	}

	//d.insurance_firm_id.value = " ";
}

	/*
	*	burn added: April 20, 2007
	*	NOTE : Assuming that Admit option is the FIRST option in 'disp_code' radio button
	*/
	function checkAdmit(obj){
		if (obj.checked){
			if (document.aufnahmeform.disp_code)
				document.aufnahmeform.disp_code[0].checked=true;
			jsShowConditionResultDisposition(true);

			//added by VAN 07-04-08
			//document.getElementById('adm_date').style.display = "";
			//document.getElementById('adm_time').style.display = "";
			//document.getElementById('admission_date').value="";
			//document.getElementById('admission_time').value="";
			//added by VAN 01-24-09
			document.getElementById('rowDateConsult').style.display = "";
			document.getElementById('rowTimeConsult').style.display = "";
			document.getElementById('rowDateAdmit').style.display = "";
			document.getElementById('rowTimeAdmit').style.display = "";
			document.getElementById('admission_date').value="<?= date("m/d/Y")?>";
			document.getElementById('admission_time').value="<?= date("h:i");?>";
			document.getElementById('selAMPM2').value="<?= date("A");?>";
			document.getElementById('rowDate').innerHTML = '<?=$LDAdmitDate?>';
			document.getElementById('rowTime').innerHTML = '<?=$LDAdmitTime?>';
			document.getElementById('datefrom').value="<?= date("m/d/Y")?>"; // added by carriane 04/25/19
			document.getElementById('timefrom').value="<?= date("h:i");?>"; // added by carriane 04/25/19
			document.getElementById('selAMPM3').value="<?= date("A");?>"; // added by carriane 04/25/19
		} else {
			if (document.aufnahmeform.disp_code)
				document.aufnahmeform.disp_code[0].checked=false;
			jsShowConditionResultDisposition(false);

			//added by VAN 07-04-08
			//document.getElementById('adm_date').style.display = "none";
			//document.getElementById('adm_time').style.display = "none";
			//document.getElementById('admission_date').value="";
			//document.getElementById('admission_time').value="";
			//added by VAN 01-24-09
			document.getElementById('rowDateConsult').style.display = "";
			document.getElementById('rowTimeConsult').style.display = "";
			document.getElementById('rowDateAdmit').style.display = "none";
			document.getElementById('rowTimeAdmit').style.display = "none";
			document.getElementById('rowDate').innerHTML = '<?=$LDConsultDate?>';
			document.getElementById('rowTime').innerHTML = '<?=$LDConsultTime?>';
			document.getElementById('admission_date').value="";
			document.getElementById('admission_time').value="";
		}
	}/* end of function checkAdmit */

	function getElementsByClass(searchClass,node,tag) {
		var classElements = new Array();
		if ( node == null )
			node = document;
		if ( tag == null )
			tag = '*';
		var els = node.getElementsByTagName(tag);
		var elsLen = els.length;
		var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
		for (i = 0, j = 0; i < elsLen; i++) {
			if ( pattern.test(els[i].className) ) {
				classElements[j] = els[i];
				j++;
			}
		}
		return classElements;
	}/* end of function getElementsByClass */

		function jsShowConditionResultDisposition(showDetails){
			var personDetails = getElementsByClass("ERDetails");
			for (var i=0; i< personDetails.length; i++){
				personDetails[i].style.display = (showDetails) ? '' : 'none';
			}

		}/* end of function jsShowConditionResultDisposition */

		//OPD
	shortcut("F2",
		function(){
			//disable the save Button if request is not yet save.
			//$('saveButton').disabled = true;
			$('bsave').innerHTML = '<img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif"  title="<?=$LDSaveData?>" border=0 align="absmiddle">';
			var ans = confirm('Process this transaction?');
			if (ans){
				checkPreviousTrxn(1);
					$('bsave').innerHTML = '<a href="javascript:void(0);"><img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
			}else
				$('bsave').innerHTML = '<a href="javascript:void(0);"><img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
		}
	);


    function submitForm(){
    	
        if (chkform(document.aufnahmeform)){

			var str = $J("#admission_class").text();
			if (str == 'OUTPATIENT') {
				var datastring = $J("#aufnahmeform").serialize();
				var pid = $('pid').value;
				// $J.ajax({
    //     	    	type: "POST",
    //     	    	url: "save_opd_registration.php",
    //     	    	data: datastring,
    //     	    	//dataType: "json",
    //     	    	success: function(data) {
    //     	    	     window.location.assign("save_opd_registration.php");
    //     	    	},
    //     	    	error: function(jqXHR, textStatus, errorThrown) { alert("Error: Status: "+textStatus+" Message: "+errorThrown); }
    //     		});
				
				xajax_SaveAuditOpd(datastring, pid);
				//document.aufnahmeform.submit();
        	}else{
        		document.aufnahmeform.submit();
        	}
         
        }else{
            //$('saveButton').disabled = false;
            $('bsave').innerHTML = '<a href="javascript:void(0);"><img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
    	}
    }

    function SecondSubmitForm(){
    	document.aufnahmeform.submit();
    }

	// Edited by Gervie 11/15/2015
	function gotoCheck(){
		 //disable the save Button if request is not yet save.
			//$('saveButton').disabled = true;
			// $('bsave').innerHTML = '<img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif"  title="<?=$LDSaveData?>" border=0 align="absmiddle">';
			var ans = confirm('Process this transaction?');
			
			if (ans){
				//chkform(aufnahmeform);
				//document.aufnahmeform.onsubmit = chkform(aufnahmeform);
				   $J("#er_location_lobby").prop('disabled', false);
                checkPreviousTrxn(1);

					$('bsave').innerHTML = '<a href="javascript:void(0);"><img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
			} else{
					$('bsave').innerHTML = '<a href="javascript:void(0);"><img id="saveButton" name="saveButton" src="../../gui/img/control/default/en/en_savedisc.gif" border=0  title="<?=$LDSaveData?>"  align="absmiddle" onclick="gotoCheck();"></a>';
	}}

	//added by VAN 06-30-2010
	function checkPreviousTrxn(issubmit){
		 var update = <?php echo $update; ?>;
		 var pid = $('pid').value;
		 var current_dept_nr = $('current_dept_nr').value;
		 var patient_type = '<?=$ptype?>';

		 var encounter_date = $('encounter_dt');
		 if (encounter_date)
			encounter_date = encounter_date.value;

         var encounter_nr = $('encounter_nr').value;   

		 var admission_date = $('admission_date');
		 if (admission_date)
			encounter_date = admission_date.value;
		 //alert(update);
		 //alert('pid,current_dept_nr,ptype,encounter_date = '+pid+','+current_dept_nr+','+patient_type+','+encounter_date);
		 //edited by VAN 02-03-2012
         //if (update==0)
		 xajax_checkPreviousTrxn(pid, current_dept_nr, patient_type, encounter_date, update, encounter_nr, issubmit);
	}

	function jsGetEROPDDoctors_2()
	{
		var d = document.aufnahmeform;
		var aDepartment_nr=d.current_dept_nr.value;

		var aDoctor=d.current_dept_nr;
		var aDoctor_nr = aDoctor.options[aDoctor.selectedIndex].value;

		var sex = '<?=$sex?>';
		var age = '<?=$age?>';

		if (aDepartment_nr != 0) {
			xajax_setDoctorsEROPD(sex, age, 0,aDepartment_nr,aDoctor_nr);	//get the list of ER-OPD doctors under "aDepartment_nr" department
		} else{
			xajax_setDoctorsEROPD(sex, age,0,aDoctor_nr);	//get the list of ER-OPD doctors
		}
	}

	//added by CHA, May 21, 2010
	function open_search_tray()
	{
		var params = "var_encounter_nr=parent_encounter_nr&var_ward_nr=parent_ward_nr&var_room_nr=parent_room_nr&var_dept_nr=parent_dept_nr&var_include_enc=1";
		overlib(
					OLiframeContent('<?=$root_path?>modules/registration_admission/seg-select-enc.php?'+params, 700, 400,
													'fSelEnc', 0, 'auto'),
					WIDTH, 700, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL,CLOSETEXT,
					'<img src="<?=$root_path?>images/close_red.gif" border=0 >',
					CAPTIONPADDING,2, CAPTION, 'Select registered parent',
					MIDX,0, MIDY,0, STATUS, 'Select registered parent');
					return false;
	}
	//end CHA

    //added by VAS 08/20/2012
    function jsUpdateAdmissionDate(dept){
        //alert('jsUpdateAdmissionDate'+dept);
        var bornhere = '<?=$bornhere?>';
        // NICU dept = 191
        if (bornhere=='1'){
        if (dept=='191'){
            //will automatically set the birth date and time as the admission date 
            //when the baby is admitted
            $('admission_date').value = $('date_birth').value;
            $('admission_time').value = $('birth_time').value;
            $('selAMPM2').value = $('birth_time_meridian').value;
        }else{
            $('admission_date').value = '<?=date('m/d/Y')?>';
            $('admission_time').value = '<?=date('h:i')?>';
            $('selAMPM2').value = '<?=date('A')?>';
        }
    }
    }

    /*
    * Created by Jarel
    * Created on 07/17/2013
    * Set OR from the selection
    */
    function assignOR(val){
    	if(val=='WCPU'){
    		$('is_confidential').checked = true;
    	}else{
    		$('is_confidential').checked = false;
    	}

    	if(val=='NOT SERVED'){
    		$J('#official_receipt_nr').val('');
    		$J( "#OrDialog" ).dialog({
		        autoOpen: true,
		        height: 130,
		        width: 250,
		        modal:true,
		        show: "blind",
		        hide: "explode",
		        title: "ENTER OR",
		        position: "top",
		        buttons: {
		                ADD: function() {                     
		                	validateOR();
		                },
		                CANCEL: function(){
		                    $J(this).dialog( "close" );
		                }
		        }
		    });
    	}else{
    		$('official_receipt_nr').value = val;
    	}
    }

    /*
    * Created by Jarel
    * Created on 11/19/2013
    * Call ajax function to validate OR
    */
    function validateOR(){
    	xajax_validateOR($J("#or_dialog").val(),$J("#pid").val());
		$J('#or_dialog').val('');
		$J('#or_temp').val(0);
		$J('#OrDialog').dialog( "close" );
    }
    
  /*START added by Mark 2016-20-10*/
/*FUNCTION USES: Location area of OB-ER must default to LOBBY area*/
$J(document).on('ready',function(){
	 if ($J("#er_location").prop('selectedIndex', 1)){
		    	$J("#er_location_lobby").prop('selectedIndex', 0);
		    	$J("#er_location_lobby").attr('disabled', false);
		    }
		$J("#er_location").change(function(){
		    if (this.selectedIndex ===3){
		    	$J("#er_location_lobby").prop('selectedIndex', 3);
		    	$J("#er_location_lobby").attr('disabled', true);
		    }else
		    	$J("#er_location_lobby").attr('disabled', false);
		});
});
	/*END added By MARK*/
    

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

-->
</script>
<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/jsprototype/prototype.js"></script>

<!--<body onLoad="preset();">-->

<?php

} // End of if(isset(pid))

#added by VAN 06-12-08
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';
/*echo '<script type="text/javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';*/

#-------------------

require('./include/js_popsearchwindow.inc.php');

$sTemp = ob_get_contents();
ob_end_clean();

#added by VAN 06-12-08
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

$smarty->append('JavaScript',$sTemp);

# Load tabs
$target='entry';

$parent_admit = TRUE;

include('./gui_bridge/default/gui_tabs_patadmit.php');

# If the origin is admission link, show the search prompt
if(!isset($pid) || !$pid){

	# Set color values for the search mask
	$searchmask_bgcolor="#f3f3f3";
	$searchprompt=$LDEntryPrompt;
	$entry_block_bgcolor='#fff3f3';
	$entry_body_bgcolor='#ffffff';

	$smarty->assign('entry_border_bgcolor','#6666ee');

	$smarty->assign('sSearchPromptImg','<img '.createComIcon($root_path,'angle_down_l.gif','0','',TRUE).'>');

	$smarty->assign('LDPlsSelectPatientFirst',$LDPlsSelectPatientFirst);
	$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_l.gif','0','absmiddle').'>');

	# Start buffering the searchmask

	ob_start();

	$search_script='patient_register_search.php';
	$user_origin='admit';
	include($root_path.'include/inc_patient_searchmask.php');

	$sTemp = ob_get_contents();

	ob_end_clean();

	$smarty->assign('sSearchMask',$sTemp);
	$smarty->assign('sWarnIcon','<img '.createComIcon($root_path,'warn.gif','0','absmiddle',TRUE).'>');
	$smarty->assign('LDRedirectToRegistry',$LDRedirectToRegistry);

}else{

	$smarty->assign('bSetAsForm',TRUE);

	if($error){
		$smarty->assign('error',TRUE);
		$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');

		 if ($errornum>1) $smarty->assign('LDError',$LDErrorS);
			else 	$smarty->assign('LDError',$LDError);
	}

	$smarty->assign('LDRegistryNr',$LDRegistryNr);
	$smarty->assign('pid',$pid);

	$permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);
	$manual_hrn = false;
	/*foreach($permission_val as $val){
		echo "value: ".$val."<br/>";
	}*/


	$smarty->assign('LDCaseNr',$LDCaseNr);
	if(isset($encounter_nr)&&$encounter_nr && (!$_GET['istobeadmitted'])){
		$smarty->assign('encounter_nr',$encounter_nr);
	}else{
		#if ($dept_belong['id'] == 'OPD-Triage')
		#if (($allow_opd_user||$isIPBM)&&($ptype=='opd'))
		if(in_array("_a_1_ipdpatientmanualhrn", $permission_val)){
			$encounter_nr_design = "<input id='encounter_nr_temp' name='encounter_nr_temp' type='text' value='' />";
			$smarty->assign('encounter_nr',$encounter_nr_design);
		}else{
			if ((($allow_opd_user||$isIPBM)&&($ptype=='opd')) || (($allow_phs_user)&&($ptype=='phs'))){
				#$smarty->assign('encounter_nr','<font color="red">No transaction yet</font>');
				$smarty->assign('encounter_nr','<font color="red">No transaction yet</font>');
			}else{
				$smarty->assign('encounter_nr','<font color="red">'.$LDNotYetAdmitted.'</font>');
			}
		}
		
	}

		# Create the encounter barcode image
	if(file_exists($root_path.'cache/barcodes/en_'.$encounter_nr.'.png')) {
		$smarty->assign('sEncBarcode','<img src="'.$root_path.'cache/barcodes/en_'.$encounter_nr.'.png" border=0 width=180 height=35>');
	}else{
		$smarty->assign('sHiddenBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$encounter_nr."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>");
		$smarty->assign('sEncBarcode',"<img src='".$root_path."classes/barcode/image.php?code=".$encounter_nr."&style=68&type=I25&width=180&height=40&xres=2&font=5' border=0>");
	}

	$smarty->assign('img_source',"<img $img_source>");

	#added by VAN 07-04-08
	#echo "wait = ".$dept_belong['id'];

	#if ($dept_belong['id']=="Admission"){
	if (($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
		#echo "se = ".$seg_direct_admission;
			$smarty->assign('sAdmissionBol',false);
		$smarty->assign('LDAdmitDate2',$LDAdmitDate);
		$smarty->assign('LDAdmitTime2',$LDAdmitTime);

		if (($patient_enc['encounter_type']==1) || ($patient_enc['encounter_type']==2) || ($patient_enc['encounter_type']==IPBMOPD_enc))
			$segAdmitDateTime = $patient_enc['encounter_date'];
		else
			$segAdmitDateTime = $patient_enc['admission_dt'];

		if ($segAdmitDateTime)
			$adm_date = date("Y-m-d",strtotime($segAdmitDateTime));
		else
			#$segAdmitDateTime = date("Y-m-d");
			$adm_date = "";

		#edited by VAN 01-21-09
		if (($adm_date=='0000-00-00')||(empty($adm_date))){
			#$admission_date = "";
			$admission_date = date("m/d/Y");
			$adm_time = date("H:i:s");
			$meridian = date("A");
		}else{
			$admission_date = date("m/d/Y",strtotime($adm_date));
			$adm_time = date("H:i:s",strtotime($segAdmitDateTime));
			$meridian = date("A",strtotime($segAdmitDateTime));
		}

		$readOnly_date = "";
		$disabled_date = "";
		if (!$allow_updateDate){
			$readOnly_date = 'readonly="readonly"';
			$disabled_date = 'disabled="disabled"';
		}

		$smarty->assign('sAdmitDate2','<input type="text" '.$readOnly_date.' name="admission_date" value="'.$admission_date.'" size=10 maxlength=10 id="admission_date" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true);chkAdmissionDate();}else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');

		$TP_href_date="javascript:show_calendar('aufnahmeform.date','".$date_format."')";
		$dfbuffer="LD_".strtr($date_format,".-/","phs");
		$TP_date_format=$$dfbuffer;

		if (!$allow_updateDate){
			$smarty->assign('sDateMiniCalendar2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . '> <font size=1>['.$TP_date_format.']</font>');
		}else{
			$jsCalScript = "<script type=\"text/javascript\">
							Calendar.setup ({
								inputField : \"admission_date\", ifFormat : \"$phpfd\", showsTime : false, button : \"admission_date_trigger\", singleClick : true, step : 1
							});
						</script>
						";
			$smarty->assign('jsCalendarSetup2', $jsCalScript);

			$smarty->assign('sDateMiniCalendar2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="admission_date_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
		}

		if ($meridian=='PM'){
			$selected1 = "";
			$selected2 = "selected";
		}else{
			$selected1 = "selected";
			$selected2 = "";
		}

		if ($adm_time=='00:00:00'){
			$admission_time = date("h:i");
			$meridian = date("A");

			if ($meridian=='AM'){
				$selected1 = 'selected';
				$selected2 = '';
			}else{
				$selected1 = '';
				$selected2 = 'selected';
			}
			#echo "meridia - ".$meridian;
		}else{
			$admission_time = date("h:i",strtotime($adm_time));
		}

			#----------------
		$admtime = '<input type="text" id="admission_time" name="admission_time" size="4" maxlength="5" value="'.$admission_time.'" onChange="setFormatTime(this,\'selAMPM2\');chkAdmissionDate();" />
						<select id="selAMPM2" name="selAMPM2" onchange="chkAdmissionDate()">
							<option value="AM" '.$selected1.'>A.M.</option>
							<option value="PM" '.$selected2.'>P.M.</option>
						</select>&nbsp;<font size=1>[hh:mm]</font>';

		$smarty->assign('sAdmitTime2',$admtime);

		#$smarty->assign('LDConsultDate',$LDConsultDate);
		#$smarty->assign('LDConsultTime',$LDConsultTime);
		#$smarty->assign('sConsultDate',$admtime);
		#$smarty->assign('sConsultTime',$admtime);

	}else{
				 #added by VAN 10-28-08
			 # $smarty->assign('sAdmitDate',$LDAdmitDate);
				 $smarty->assign('sAdmissionBol',true);
				#edited by VAN 01-21-09

		$segConsultDateTime = $patient_enc['encounter_date'];

		if ($segConsultDateTime)
			$enc_date = date("Y-m-d",strtotime($segConsultDateTime));
		else
			$enc_date = "";

		#edited by VAN 01-21-09
		if (($enc_date=='0000-00-00')||(empty($enc_date))){
			#$admission_date = "";
			$consult_date = date("m/d/Y");
			$enc_time = date("H:i:s");
			$meridian = date("A");
		}else{
			$consult_date = date("m/d/Y",strtotime($enc_date));
			$enc_time = date("H:i:s",strtotime($segConsultDateTime));
			$meridian = date("A",strtotime($segConsultDateTime));
		}

		$readOnly_date = "";
		$disabled_date = "";
		if (!$allow_updateDate){
			$readOnly_date = 'readonly="readonly"';
			$disabled_date = 'disabled="disabled"';
		}

				#$smarty->assign('sAdmitDate2','<input type="text" name="encounter_dt" value="'.$consult_date.'" size=10 maxlength=10 id="encounter_dt" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');
		$smarty->assign('sConsultDate','<input type="text" '.$readOnly_date.' name="encounter_dt" value="'.$consult_date.'" size=10 maxlength=10 id="encounter_dt" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true);chkConsultationDate(); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');

				$TP_href_date="javascript:show_calendar('aufnahmeform.date','".$date_format."')";
				$dfbuffer="LD_".strtr($date_format,".-/","phs");
				$TP_date_format=$$dfbuffer;

		if (!$allow_updateDate){
			$smarty->assign('sDateMiniCalendar2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . '> <font size=1>['.$TP_date_format.']</font>');
		}else{
			$jsCalScript = "<script type=\"text/javascript\">
														Calendar.setup ({
																inputField : \"encounter_dt\", ifFormat : \"$phpfd\", showsTime : false, button : \"encounter_dt_trigger\", singleClick : true, step : 1
														});
												</script>
												";
					$smarty->assign('jsCalendarSetup2', $jsCalScript);

			$smarty->assign('sDateMiniCalendar2','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="encounter_dt_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
		}

		if ($meridian=='PM'){
			$selected1 = "";
			$selected2 = "selected";
		}else{
			$selected1 = "selected";
			$selected2 = "";
		}


		if ($enc_time=='00:00:00'){
			$consult_time = date("h:i");
			$meridian = date("A");

			if ($meridian=='AM'){
				$selected1 = 'selected';
				$selected2 = '';
			}else{
				$selected1 = '';
				$selected2 = 'selected';
			}
			#echo "meridia - ".$meridian;
		}else{
			$consult_time = date("h:i",strtotime($enc_time));
		}
			#----------------
		$enctime = '<input type="text" id="encounter_time" name="encounter_time" size="4" maxlength="5" value="'.$consult_time.'" onChange="setFormatTime(this,\'selAMPM2\');chkConsultationDate();" />
						<select id="selAMPM2" name="selAMPM2" onchange="chkConsultationDate()">
							<option value="AM" '.$selected1.'>A.M.</option>
							<option value="PM" '.$selected2.'>P.M.</option>
						</select>&nbsp;<font size=1>[hh:mm]</font>';
		#echo "enc = ".$enctime;
		#$smarty->assign('sAdmitTime',$enctime);
		$smarty->assign('sConsultTime',$enctime);
	}

	#----------------

	#added by VAN 08-20-08

	$recent_loc = $encounter_obj->getRecentWard($encounter_nr);
#	var_dump($encounter_obj->sql);

	if (($recent_loc['date_from']=='0000-00-00') || (empty($recent_loc['date_from']))){
		$date_accom = date("m/d/Y",strtotime($admission_date));
	}else{
		$date_accom = date("m/d/Y",strtotime($recent_loc['date_from']));
	}

	$smarty->assign('sLDDateFrom','<input type="text" name="datefrom" value="'.$date_accom.'" size=10 maxlength=10 id="datefrom" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');
	#echo "date = ".date("m/d/Y",strtotime($recent_loc['date_from']));
	$TP_href_date="javascript:show_calendar('aufnahmeform.date','".$date_format."')";
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$TP_date_format=$$dfbuffer;

	$jsCalScript = "<script type=\"text/javascript\">
							Calendar.setup ({
								inputField : \"datefrom\", ifFormat : \"$phpfd\", showsTime : false, button : \"datefrom_trigger\", singleClick : true, step : 1
							});
						</script>
						";
	$smarty->assign('jsCalendarSetup3', $jsCalScript);
	$smarty->assign('sDateMiniCalendar3','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="datefrom_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');

	if (($recent_loc['time_from']=='00:00:00') || (empty($recent_loc['time_from']))){
		$time_accom = date("h:i",strtotime($admission_time));
		$meridian2 = date("A");
	}else{
		$time_accom = date("h:i",strtotime($recent_loc['time_from']));
		$meridian2 = date("A",strtotime($recent_loc['time_from']));
	}

	if ($meridian2=='PM'){
		$selected1 = "";
		$selected2 = "selected";
	}else{
		$selected1 = "selected";
		$selected2 = "";
	}


	$admtime2 = '<input type="text" id="timefrom" name="timefrom" size="4" maxlength="5" value="'.$time_accom.'" onChange="setFormatTime(this,\'selAMPM3\')" />
						<select id="selAMPM3" name="selAMPM3">
							<option value="AM" '.$selected1.'>A.M.</option>
							<option value="PM" '.$selected2.'>P.M.</option>
						</select>&nbsp;<font size=1>[hh:mm]</font>';

		$smarty->assign('sLDTimeFrom',$admtime2);
	#------------------

		# burn added: May 15, 2007
		#edited by VAN
	if (($patient_enc['encounter_type']=='3')||($patient_enc['encounter_type']=='4')||($patient_enc['encounter_type']==IPBMIPD_enc_STR)||($seg_direct_admission)){
	#if (($patient_enc['encounter_type']=='3')||($patient_enc['encounter_type']=='4')){
		#edited by VAN 07-04-08
		#$smarty->assign('LDAdmitDate',$LDAdmitDate);
		#$smarty->assign('LDAdmitTime',$LDAdmitTime);

		$smarty->assign('LDAdmitDate',$LDAdmitDate);
		$smarty->assign('LDAdmitTime',$LDAdmitTime);

	}else{
	#echo "here".$LDAdmitDate." - ".$LDAdmitTime;
		$smarty->assign('LDAdmitDate',$LDConsultDate);
		$smarty->assign('LDAdmitTime',$LDConsultTime);
	}

	$smarty->assign('LDConsultDate',$LDConsultDate);
	$smarty->assign('LDConsultTime',$LDConsultTime);

			 # burn added: March 29, 2007
	 if(isset($encounter_nr)&&$encounter_nr){
		#if (($patient_enc['encounter_type']==1) || ($patient_enc['encounter_type']==2) || ($seg_direct_admission)){
		if (($patient_enc['encounter_type']==1) || ($patient_enc['encounter_type']==2) || ($patient_enc['encounter_type']==IPBMOPD_enc)){
			#echo "true";
			$smarty->assign('sAdmission',false);
			# ER/OPD
			$segAdmitDateTime = $patient_enc['encounter_date'];
			#commented by VAN 01-21-09
			#$smarty->assign('sAdmitDate',@formatDate2Local($segAdmitDateTime,$date_format));
			#$smarty->assign('sAdmitTime',@formatDate2Local($segAdmitDateTime,$date_format,1,1));
			if ((!$seg_direct_admission)&&(($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))){
				$smarty->assign('sConsultDate',@formatDate2Local($segAdmitDateTime,$date_format));
				$smarty->assign('sConsultTime',@formatDate2Local($segAdmitDateTime,$date_format,1,1));
				#$smarty->assign('jsCalendarSetup2','');
				#$smarty->assign('sDateMiniCalendar2','');
			}

			$consult_date = @formatDate2Local($segAdmitDateTime,$date_format);
			#$consult_time = @formatDate2Local($segAdmitDateTime,$date_format,1,1);
			$consult_time = date("h:i",strtotime($segAdmitDateTime));
			$consult_meridian = date("A",strtotime($segAdmitDateTime));
			#----------------
		}else{
			#echo "false";
			$smarty->assign('sAdmission',true);
			# Inpatient
			$segAdmitDateTime = $patient_enc['admission_dt'];
			#commented by VAN 01-21-09
			#$smarty->assign('sAdmitDate',@formatDate2Local($segAdmitDateTime,$date_format));
			#$smarty->assign('sAdmitTime',$admtime);

			$consult_date = @formatDate2Local($patient_enc['encounter_date'],$date_format);
			#$consult_time = @formatDate2Local($segAdmitDateTime,$date_format,1,1);
			$consult_time = date("h:i",strtotime($patient_enc['encounter_date']));
			$consult_meridian = date("A",strtotime($patient_enc['encounter_date']));
		}

		#$smarty->assign('sAdmitDate',@formatDate2Local($segAdmitDateTime,$date_format));
		#$smarty->assign('sAdmitTime',@formatDate2Local($segAdmitDateTime,$date_format,1,1));
	}#added by VAN 08-01-08
	else{
			$smarty->assign('sAdmission',true);
			# Inpatient
			$segAdmitDateTime = $patient_enc['admission_dt'];
			#commented by VAN 01-21-09
			#$smarty->assign('sAdmitDate',@formatDate2Local($segAdmitDateTime,$date_format));
			#$smarty->assign('sAdmitTime',$admtime);

			$consult_date = @formatDate2Local($patient_enc['encounter_date'],$date_format);
			#$consult_time = @formatDate2Local($segAdmitDateTime,$date_format,1,1);
			$consult_time = date("h:i",strtotime($patient_enc['encounter_date']));
			$consult_meridian = date("A",strtotime($patient_enc['encounter_date']));
	}

	// added by carriane 08/13/18
	if($suffix)
		$name_first = str_replace(' '.$suffix, ', '.$suffix, $name_first);
	// end carriane

	$smarty->assign('LDTitle',$LDTitle);
	$smarty->assign('title',$title);
	$smarty->assign('LDLastName',$LDLastName);
	$smarty->assign('name_last',$name_last);
	$smarty->assign('LDFirstName',$LDFirstName);
	$smarty->assign('name_first',$name_first);

	# Set a row span counter, initialize with 6
	$iRowSpan = 6;

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

		$smarty->assign('LDBirthplace',"$segBirthplace");
		$smarty->assign('sBirthplace',ucwords(strtolower($place_birth)));

		$smarty->assign('segAge','Age');
		$smarty->assign('age',$age);

		$smarty->assign('LDSex',$LDSex);
		if($sex=='m') $smarty->assign('sSexType',$LDMale);
			elseif($sex=='f') $smarty->assign('sSexType',$LDFemale);

		# Retrieves record set of occupation
		if ($occupation_obj = $person_obj->getOccupation("occupation_nr=$occupation")){
			if ($occupation_row = $occupation_obj->FetchRow())
				$occupation = $occupation_row['occupation_name'];
		}

		$smarty->assign('sOccupation',"$LDOccupation");
		$smarty->assign('sOccupations',ucwords(strtolower($occupation)));

		# Retrieves record set for religion
		if ($religion_obj=$person_obj->getReligion("religion_nr=$religion")){
			if ($religion_row = $religion_obj->FetchRow())
				$religion = $religion_row['religion_name'];
		}

		$smarty->assign('sReligion',$LDReligion);
		$smarty->assign('sReligions',ucwords($church = strtolower($religion)));

		$smarty->assign('LDBloodGroup',$LDBloodGroup);
		if($blood_group){
				$buf='LD'.$blood_group;
			$smarty->assign('blood_group',$$buf);
		}
				## populates the vital sign details of encounter with the oldest ##
				$rowvitaldataResult = $vitals_obj->getOldestVitalDetailsbyPid($pid,$encounter_nr);
				$rowvitaldata = $rowvitaldataResult->FetchRow();
				$unittemp = $vitals_obj->getUnitName($rowvitaldata['temp_unit']);
				$unitbp = $vitals_obj->getUnitName($rowvitaldata['bp_unit']);
				$unitweight = $vitals_obj->getUnitName($rowvitaldata['weight_unit']);
				$unitrr = $vitals_obj->getUnitName($rowvitaldata['rr_unit']);
				$unitpr = $vitals_obj->getUnitName($rowvitaldata['pr_unit']);
				# onblur="convertNumberValue(this, this.value);"
		// $vitalHTML = '<table width="95%" border="0" cellpadding="1" cellspacing="1" style="font:bold 12px Arial; color:#2d2d2d; margin:0%">
		// 										<tr>
		// 												<td width="50%">
		// 														<table>
		// 																<tr>
		// 																		<td width="45%">
		// 																				<span style="font: 11px Arial;">Blood Pressure (BP)</span>
		// 																		</td>
		// 																		<td width="55%">
		// 																				<input class="segInput" id="vital_bp_sys" name="vital_bp_sys" type="text" tabindex=1 size="1" maxlength=3 style="padding-left:4px;font:bold 11px Arial;" value="'.$rowvitaldata['systole'].'" onkeydown="return key_check(event, this.value)"/><span> / <span><input class="segInput" id="vital_bp_dias" name="vital_bp_dias" type="text" tabindex=2 size="1" maxlength=3 style="padding-left:4px;font:bold 11px Arial;" value="'.$rowvitaldata['diastole'].'" onkeydown="return key_check(event, this.value)" /><span style="font: 11px Arial;"> '.$unitbp['unit_name'].' </span><span style="font: 11px Arial;">mm Hg</span>
		// 																				<input class="segInput" id="vital_no" name="vital_no" type="hidden" value="'.$rowvitaldata['vital_no'].'" />
		// 																		</td>
		// 																</tr>
		// 																<tr>
		// 																		<td width="45%">
		// 																				<span style="font: 11px Arial;">Temperature (T)</span>
		// 																		</td>
		// 																		<td width="55%">
		// 																				<input class="segInput" id="vital_t" name="vital_t" type="text" tabindex=4 size="8" maxlength=5 style="padding-left:4px;font:bold 11px Arial" value="'.$rowvitaldata['temp'].'" onkeydown="return key_check(event, this.value)"/><span style="font: 11px Arial;"> '.$unittemp['unit_name'].' </span><span style="font: 11px Arial;">�C</span>
		// 																		</td>
		// 																</tr>
		// 																<tr>
		// 																		<td width="45%">
		// 																				<span style="font: 11px Arial;">Weight (W)</span>
		// 																		</td>
		// 																		<td width="55%">
		// 																				<input class="segInput" id="vital_w" name="vital_w" tabindex=6 type="text" size="8" maxlength=5 style="padding-left:4px;font:bold 11px Arial" value="'.$rowvitaldata['weight'].'" onkeydown="return key_check(event, this.value)" onblur="convertNumberValue(this, this.value);"/><span style="font: 11px Arial;"> '.$unitweight['unit_name'].' </span><span style="font: 11px Arial;">kg</span>
		// 																		</td>
		// 																</tr>
		// 														</table>
		// 												</td>
		// 												<td width="5%"></td>
		// 												<td width="45%" align="right" valign="top">
		// 														<table>
		// 																<tr>
		// 																		<td width="45%">
		// 																				<span style="font: 11px Arial;">Resp. Rate (RR)</span>
		// 																		</td>
		// 																		<td width="55%">
		// 																				<input class="segInput" id="vital_rr" name="vital_rr" tabindex=3 type="text" size="8" maxlength=3 style="padding-left:4px;font:bold 11px Arial" value="'.$rowvitaldata['resp_rate'].'" onkeydown="return key_check(event, this.value)" /><span style="font: 11px Arial;"> '.$unitrr['unit_name'].' </span><span style="font: 11px Arial;">br/m</span>
		// 																		</td>
		// 																</tr>
		// 																<tr>
		// 																		<td width="45%">
		// 																				<span style="font: 11px Arial;">Pulse Rate (PR)</span>
		// 																		</td>
		// 																		<td width="55%">
		// 																				<input class="segInput" id="vital_pr" name="vital_pr" tabindex=5 type="text" size="8" maxlength=3 style="padding-left:4px;font:bold 11px Arial" value="'.$rowvitaldata['pulse_rate'].'" onkeydown="return key_check(event, this.value)"/><span style="font: 11px Arial;"> '.$unitpr['unit_name'].' </span> <span style="font: 11px Arial;">b/m</span>
		// 																		</td>
		// 																</tr>
		// 														</table>
		// 												</td>
		// 										</tr>
		// 								</table>';

		// 		$smarty->assign('LDVitalSigns','<span>Vital Signs</span>');
		// 		$smarty->assign('vital_signs','<img src="'.$root_path.'images/btn_add.gif" align="absmiddle" style="cursor:pointer" onclick="openVital();">');
		// 		$smarty->assign('vital_signs',$vitalHTML);

		$smarty->assign('LDAddress',$LDAddress);

		#added by VAN 04-28-08
		if(!empty($street_name))
			$street_name = $street_name.', ';

		if(!empty($brgy_name))
			$brgy_name = $brgy_name.', ';

		#$segAddress=$street_name.', '.$brgy_name.', '.$mun_name.' '.$zipcode.' '.$prov_name;   # burn added: March 12, 2007
        if($brgy_name == 'NOT PROVIDED' || $brgy_name == NULL && $mun_name == 'NOT PROVIDED' && $zipcode == '' && $prov_name == 'NOT PROVIDED') {
            $segAddress = rtrim($street_name, ', ');
        }else {
            $segAddress=$street_name.' '.$brgy_name.' '.$mun_name.' '.$zipcode.' '.$prov_name;
        }
		$smarty->assign('segAddress',$segAddress);   # burn added: March 12, 2007

		$smarty->assign('LDAdmitClass',$LDAdmitClass);
		#commented by VAN 01-25-08

		if ($HTTP_SESSION_VARS['sess_user_name'] == "Administrator"){
			if(is_object($encounter_classes)){

				$sTemp = '';
				while($result=$encounter_classes->FetchRow()) {
					$LD=$result['LD_var'];

					if($encounter_nr ){ # If admitted, freeze encounter class
						if ($encounter_class_nr==$result['class_nr']){
							if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD;
								else $sTemp = $sTemp.$result['name'];
							$sTemp = $sTemp.'<input name="encounter_class_nr" type="hidden"  value="'.$encounter_class_nr.'">';
							break;
						}
					}else{

						$sTemp = $sTemp.'<input name="encounter_class_nr" onClick="resolveLoc()" type="radio"  value="'.$result['class_nr'].'" ';
						if($encounter_class_nr==$result['class_nr']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';

						if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD;
							else $sTemp = $sTemp.$result['name'];
					} #end of IF-else
				} #end of While loop
			} # end of if(is_object($encounter_classes))
			#echo "temp = ".$sTemp;
			$smarty->assign('sAdmitClassInput',$sTemp);
		}else{

			if (($patient_enc['encounter_type']!=3)&&($patient_enc['encounter_type']!=4)&&($patient_enc['encounter_type']!=IPBMIPD_enc)){
				#if($dept_belong['id'] == "OPD-Triage"){
				if(($allow_opd_user||$isIPBM)&&($ptype=='opd')){
					if($isIPBM){
						$admission_class = 'IPBM-OPD';
						$encounter_type = IPBMOPD_enc;
					}
					else{
						$admission_class = $LDAmbulant;
						$encounter_type = 2;
					}
					
				#}elseif($dept_belong['id'] == "ER"){
				}elseif(($allow_er_user||$isIPBM)&&($ptype=='er')){
					$admission_class = $LDStationary;
					$encounter_type = 1;
				}elseif(($allow_phs_user)&&($ptype=='phs')){
					$admission_class = "PHS-".$LDAmbulant;
					$encounter_type = 2;
				#}elseif($dept_belong['id'] == "Admission"){
				}elseif(($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
					$sAdmit ="<input type=\"checkbox\" name=\"is_admit\" id=\"is_admit\" onChange=\"checkAdmit(this);\" value=\"1\">";
					if ($encounter_class_nr == 2){
						$admission_class = $LDAmbulant;
						$encounter_class_nr = 2;
						$encounter_type = 4;
					}else{
						if($isIPBM){
							$admission_class = "IPBM-IPD";
							$encounter_class_nr = 1;
							$encounter_type = IPBMIPD_enc;
						}
						else{
							$admission_class = $LDStationary;
							$encounter_class_nr = 1;
							$encounter_type = 3;
						}
					}
				}
			}elseif(($patient_enc['encounter_type']==3)||($patient_enc['encounter_type']==4)||($patient_enc['encounter_type']==IPBMIPD_enc)){
				if ($patient_enc['encounter_type']==3)
					$admission_class = $LDStationary2;
				elseif($patient_enc['encounter_type']==4)
					$admission_class = $LDAmbulant2;
				elseif($patient_enc['encounter_type']==IPBMIPD_enc)
					$admission_class = "IPBM-IPD";
			}

			$sTemp = '';
			#if($dept_belong['id'] == "Admission"){
			if(($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
				if ($patient_enc['encounter_type']==1){
					$sTemp = $sTemp.'<label id="admission_class"><font color="red"><b>'.strtoupper($admission_class).'</b></font></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sAdmit."&nbsp;&nbsp;Pls. check if you want to Admit the patient.";
				}elseif($patient_enc['encounter_type']==2){
					$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sAdmit."&nbsp;&nbsp;Pls. check if you want to Admit the patient.";
				}elseif($patient_enc['encounter_type']==IPBMIPD_enc){
					$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$sAdmit."&nbsp;&nbsp;Pls. check if you want to Admit the patient.";
				}else{
					$sTemp = $sTemp.'<label id="admission_class"><font color="green"><b>'.strtoupper($admission_class).'</b></font></label>';
				}
			}else{
				if($patient_enc['encounter_type']!=NULL){
					if ($patient_enc['encounter_type']==1){
						$sTemp = $sTemp.'<label id="admission_class"><font color="red"><b>'.strtoupper($admission_class).'</b></font></label>';
					}elseif($patient_enc['encounter_type']==2){
						$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>';
					}
					elseif($patient_enc['encounter_type']==IPBMOPD_enc){
						$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>';
					}
				}else{
					if ($encounter_class_nr==1){
						$sTemp = $sTemp.'<label id="admission_class"><font color="red"><b>'.strtoupper($admission_class).'</b></font></label>';
					}elseif($encounter_class_nr==2){
						$sTemp = $sTemp.'<label id="admission_class"><font color="blue"><b>'.strtoupper($admission_class).'</b></font></label>';
					}
				}
			}

			$sTemp2 = $sTemp2.'<input name="encounter_class_nr" id="encounter_class_nr" type="hidden" size="5" value="'.$encounter_class_nr.'">';
			$sTemp3 = $sTemp3.'<input name="encounter_type" id="encounter_type" type="hidden" size="5" value="'.$encounter_type.'">';

			if ($seg_direct_admission){
				if ($update){
					if($isIPBM) $sTemp = '<label id="admission_class"><font color="green"><b>IPBM-IPD</b></font></label>';
					else $sTemp = '<label id="admission_class"><font color="green"><b>'.strtoupper($LDInpatientDirectAdmission).'</b></font></label>';
				}
				else{
					if($isIPBM) $sTemp = '<label id="admission_class"><font color="red"><b>IPBM-IPD</b></font></label>';
					else $sTemp = '<label id="admission_class"><font color="red"><b>'.strtoupper($LDDirectAdmission).'</b></font></label>';
				}
			}

			$smarty->assign('sAdmitClassInput',$sTemp);
			$smarty->assign('sAdmitClassInput2',$sTemp2);
			$smarty->assign('sAdmitClassInput3',$sTemp3);
		#commented by VAN 01-25-08
		}	#end of if ($HTTP_SESSION_VARS['sess_user_name'] == "Administrator")

			#-------------------add 03-07-07--------------------
			#if(($dept_belong['id']=="Admission")||($dept_belong['id']=="ER")||($dept_belong['id']!="OPD-Triage")){
			if((($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))||(($allow_er_user||$isIPBM)&&($ptype=='er'))||(((!$allow_opd_user||$isIPBM)&&($ptype!='opd'))&&((!$allow_phs_user)&&($ptype!='phs')))){
				$smarty->assign('LDInformant',$LDInformant);
				$smarty->assign('informant_name','<input name="informant_name" id="informant_name" type="text" size="60" value="'.ucwords(strtolower(trim($informant_name))).'">');

				$smarty->assign('LDInfoAdd',$LDInfoAdd);
				$smarty->assign('info_address','<input name="info_address" id="info_address" type="text" size="60" value="'.ucwords(strtolower(trim($info_address))).'">');

				$smarty->assign('LDInfoRelation',$LDInfoRelation);
				$smarty->assign('relation_informant','<input name="relation_informant" id="relation_informant" type="text" size="60" value="'.ucwords(strtolower(trim($relation_informant))).'">');
			}
			#------------add 02-27-07-------------------

			#-------added by VAN 06-20-08
			$smarty->assign('LDConfidential','Confidential');
			if ($is_confidential)
				$checked = 'checked';
			else
				$checked = '';


			$smarty->assign('sConfidential','<input type="checkbox" name="is_confidential" id="is_confidential" '.$checked.' value="1">');
			#-------------------------------

			#added by VAN 04-28-08
			#if ($dept_belong['id']=="ER"){
			if (($allow_er_user||$isIPBM)&&($ptype=='er')){
				#---added by VAN 06-13-08
				$smarty->assign('LDTriageCategory','Triage Category');
				$list_category = $encounter_obj->getTriageCategory();

				if(is_object($list_category)){
					$sTemp = '';
					if (empty($category))
						$category = 3;
					while($result=$list_category->FetchRow()) {
						$sTemp = $sTemp.'<input name="category" id="category" type="radio"  value="'.$result['category_id'].'" ';
							if($category==$result['category_id']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp."  ".$result['roman_id']." - ".$result['category']."<br>";

					}
				}
				$smarty->assign('sCategory',$sTemp);
			}

				#---------------------------
			//modified by Francis 05-06-2013
			if (($allow_er_user||$isIPBM)&&(($ptype=='er')||($ptype=='ipd'))){

				$smarty->assign('LDMedico',$LDMedico);

				if ($is_medico)
					$checked = 'checked';
				else
					$checked = '';


				$smarty->assign('Medico','<input type="checkbox" name="is_medico" id="is_medico" onChange="checkMedico();" '.$checked.' value="1">');

				#$enc_medico = $encounter_obj->getEncounterMedicoCases($encounter_nr,$pid);
				#echo "sql = ".$encounter_obj->sql;

				$smarty->assign('LDMedicoCases',"<font color=red>Medico Legal Cases:</font> ");
				if(is_object($medico_cases)){
					$sTemp = '';
					$count=0;

					while($result=$medico_cases->FetchRow()) {
							#echo "<br>here = ".$result2['medico_cases']."==".$result['code'];
							$sTemp = $sTemp.'<input name="medico'.$result['code'].'" id="medico'.$result['code'].'" type="checkbox" onclick="enableTextBox(this.id);" value="'.$result['code'].'" ';

							$medico=$encounter_obj->getEncounterByMedicoCases($encounter_nr,$pid,$result['code']);
														if (($medico['description'])&& ($medico['description']!='none'))
																$description = $medico['description'];
							#if($result2['medico_cases']==$result['code']) $sTemp = $sTemp.'checked';
							if($medico['medico_cases']==$result['code']) $sTemp = $sTemp.'checked';

							$sTemp = $sTemp.'>';
							$sTemp = $sTemp.$result['medico_cases']."<br>";
							if($count<=5){
								$rowMedicoA =$sTemp;
								if($count==5){$sTemp='';}
							}else{ $rowMedicoB =$sTemp; }
							$count++;
					}

				}
				$smarty->assign('sdescription','<textarea style="display:none" id="description" name="description" cols="42" rows="2">'.trim(stripslashes($description)).'</textarea>');
				#$smarty->assign('sdescription','<textarea style="display:none" id="description" name="description" cols="42" rows="2">'.trim($description).'</textarea>');
				#$smarty->assign('sResults',$sTemp);
				$smarty->assign('rowMedicoA',$rowMedicoA);
				$smarty->assign('rowMedicoB',$rowMedicoB);

				#added by VAN 06-12-08
				$smarty->assign('LDPOI',"<font>Place of Incident (POI)</font>");
				$smarty->assign('sPOI','<input name="POI" id="POI" type="text" size="73" value="'.ucwords(strtolower(trim($POI))).'">');
				$smarty->assign('LDTOI',"<font>Time of Incident (TOI)</font>");
				#$smarty->assign('sFormatTime','onChange="setFormatTime(this,\'selAMPM\')"');
				$meridian = date("A",strtotime($TOI));
				#echo "meridian = ".$meridian;
				if ($meridian=='PM'){
					$selected1 = "";
					$selected2 = "selected";
				}else{
					$selected1 = "selected";
					$selected2 = "";
				}

				if ($TOI=='00:00:00'){
					$TOI_val = "";
				}else{
					$TOI_val = date("h:i",strtotime($TOI));
				}

				if (!$TOI){
					$TOI_val = "";
				}

				$TOI_var = '<input type="text" id="TOI" name="TOI" size="4" maxlength="5" value="'.$TOI_val.'" onChange="setFormatTime(this,\'selAMPM\'); compare_date(\''.$ptype.'\');" />
								<select id="selAMPM" name="selAMPM" onChange="compare_date(\''.$ptype.'\');">
									<option value="AM" '.$selected1.'>A.M.</option>
									<option value="PM" '.$selected2.'>P.M.</option>
								</select>&nbsp;<font size=1>[hh:mm]</font>';

				$smarty->assign('sTOI',$TOI_var);
				$smarty->assign('LDDOI',"<font>Date of Incident (DOI)</font>");

				if ($DOI=='0000-00-00'){
					$DOI_val = "";
				}else{
					$DOI_val = date("m/d/Y",strtotime($DOI));
				}

				if (!$DOI){
					$DOI_val = "";
				}
				#$smarty->assign('sDOI','<input type="text" name="DOI" value="'.$DOI_val.'" size=10 maxlength=10 {{$sDateValidateJs}} />');
				#$smarty->assign('sDateValidateJs',  'value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="DOI" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')"');

				$smarty->assign('sDOI','<input type="text" name="DOI" value="'.$DOI_val.'" size=10 maxlength=10 value="'.@formatDate2Local(date('Y-m-d'),$date_format).'" id="DOI" onChange="if (IsValidDate(this,\''.$date_format.'\')){ compare_date(\''.$ptype.'\'); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');

				$TP_href_date="javascript:show_calendar('aufnahmeform.date','".$date_format."')";
				$dfbuffer="LD_".strtr($date_format,".-/","phs");
				#echo "buffer = ".$$dfbuffer;
				$TP_date_format=$$dfbuffer;

				$jsCalScript = "<script type=\"text/javascript\">
									Calendar.setup ({
										inputField : \"DOI\", ifFormat : \"$phpfd\", showsTime : false, button : \"DOI_trigger\", singleClick : true, step : 1
									});
								</script>
							";
				$smarty->assign('jsCalendarSetup', $jsCalScript);
				#$sDateJS .= $calendarSetup;
				$smarty->assign('sDateMiniCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="DOI_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>['.$TP_date_format.']</font>');
				#$smarty->assign('sDOI','');
				#------------------------
			}

			#for Dead On Arrival
			#if ($dept_belong['id']=="ER"){
			if (($allow_er_user||$isIPBM)&&($ptype=='er')){
				$smarty->assign('LDDOA',"Is Dead on Arrival?");

				if ($is_DOA)
					$checked = 'checked';
				else
					$checked = '';

				$smarty->assign('sDOA','<input type="checkbox" name="is_DOA" id="is_DOA" onChange="showDOAReason();" '.$checked.' value="1">');
			    $smarty->assign('sDOAs','<input type="checkbox" name="is_DOAs" style="display:none" id="is_DOAs" onChange="showDOAReason();" '.$checked.' value="1">');
				#$smarty->assign('sDOAreason','<input type="text" name="is_DOA_reason" id="is_DOA_reason" value="'.$is_DOA_reason.'" size="60" style="display:none">');
				$smarty->assign('sDOAreason','<span id="reason_label">Reason : </span><textarea name="is_DOA_reason" id="is_DOA_reason" cols="40" rows="3" style="display:none">'.$is_DOA_reason.'</textarea><input type="hidden" name="is_DOA_reason2" id="is_DOA_reason2" value="'.$is_DOA_reason.'" size="60">');

			}

            #added by VAN 10-12-2011
//removed by Nick 4-11-2015
//            $smarty->assign('LDSmokers',($ptype=='opd') ? '<span style="color: #f00;">History of Smoking</span>' : 'History of Smoking');
//            if ($smoker_history=='yes'){
//                $smoker_checked_yes = 'checked';
//                $smoker_checked_no = '';
//                $smoker_checked_na = '';
//            }elseif ($smoker_history=='no'){
//                $smoker_checked_yes = '';
//                $smoker_checked_no = 'checked';
//                $smoker_checked_na = '';
//            }elseif ($smoker_history=='na'){
//                $smoker_checked_yes = '';
//                $smoker_checked_no = '';
//                $smoker_checked_na = 'checked';
//            }else{
//                $smoker_checked_yes = '';
//                $smoker_checked_no = '';
//                $smoker_checked_na = '';
//            }
//
//
//            $smarty->assign('sSmokersInput','<input type="radio" name="smoker" id="smoker_yes" '.$smoker_checked_yes.' value="yes"> YES
//                                             <input type="radio" name="smoker" id="smoker_no" '.$smoker_checked_no.' value="no"> NO
//                                             <input type="radio" name="smoker" id="smoker_na" '.$smoker_checked_na.' value="na"> N/A');
//
//            $smarty->assign('LDDrinker',($ptype=='opd') ? '<span style="color: #f00;">Alcohol Drinker</span>' : 'Alcohol Drinker');
//            if ($drinker_history=='yes'){
//                $drinker_checked_yes = 'checked';
//                $drinker_checked_no = '';
//                $drinker_checked_na = '';
//            }elseif ($drinker_history=='no'){
//                $drinker_checked_yes = '';
//                $drinker_checked_no = 'checked';
//                $drinker_checked_na = '';
//            }elseif ($drinker_history=='na'){
//                $drinker_checked_yes = '';
//                $drinker_checked_no = '';
//                $drinker_checked_na = 'checked';
//            }else{
//                $drinker_checked_yes = '';
//                $drinker_checked_no = '';
//                $drinker_checked_na = '';
//            }
//
//
//            $smarty->assign('sDrinkerInput','<input type="radio" name="drinker" id="drinker_yes" '.$drinker_checked_yes.' value="yes"> YES
//                                             <input type="radio" name="drinker" id="drinker_no" '.$drinker_checked_no.' value="no"> NO
//                                             <input type="radio" name="drinker" id="drinker_na" '.$drinker_checked_na.'  value="na"> N/A');
	// added by carriane 11/10/17
	if($encounter_type==IPBMOPD_enc || $encounter_type == IPBMIPD_enc)
		$smarty->assign('required',"color='red'");
	// end carriane

	if($isIPBM && $ptype == 'opd'){
		$smarty->assign('LDVaccine','DEPOT Medicine');
		$smarty->assign('vaccineValue',$vaccineValue);
		$smarty->assign('vaccineRadioList',array(
			'yes' => 'YES',
			'no' => 'NO'
		));
	}

	//added by Nick 4-11-2015
	$smarty->assign('LDSmokers','History of Smoking');
	$smarty->assign('LDDrinker','Alcohol Drinker');
	$smarty->assign('smokerValue',$smoker_history);
	$smarty->assign('drinkerValue',$drinker_history);
	$smarty->assign('smokerRadioList',array(
		'yes' => 'YES',
		'no' => 'NO',
		'na' => 'N/A'
	));
	$smarty->assign('drinkerRadioList',array(
		'yes' => 'YES',
		'no' => 'NO',
		'na' => 'N/A'
	));
	//end Nick


			# If no encounter nr or inpatient, show ward/station info, 1 = inpatient

			#if(!$encounter_nr||$update==1 && $dept_belong['id']=="Admission"){
			if(!$encounter_nr||$update==1 && (($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))){
			#if(!$encounter_nr||$encounter_class_nr==1){

				if ($errorward||$update==1)
#					$smarty->assign('LDWard',"<font color=red>$LDWard</font>");
					$smarty->assign('LDWard',"<font color=red>Accomodation</font>");
				#if ($errorward||$encounter_class_nr==1) $smarty->assign('LDWard',"<font color=red>$LDWard</font>");

			#if ($dept_belong['id']=="Admission"){
			if (($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
#					$smarty->assign('LDWard',$LDWard);
					$smarty->assign('LDWard','Accomodation');

					#added by VAN 12-17-08
					$smarty->assign('LDArea','Area');
					$smarty->assign('sLDArea','<input type="text" size=50 id="area" name="area" value="'.$area.'">');
					#--------------------

				$sTemp = '';
				#echo "inward = ".$in_ward;
				#commented by VAN
				/*
				if($in_ward){

					while($station=$ward_info->FetchRow()){
						if(isset($current_ward_nr)&&($current_ward_nr==$station['nr'])){
							$sTemp = $sTemp.$station['name'];
							$sTemp = $sTemp.'<input name="current_ward_nr" type="hidden"  value="'.$current_ward_nr.'">';
							break;
						}
					}
				}else{
				*/
					# edited by VAN 02-01-08
					/*
					$sTemp = $sTemp.'<select name="current_ward_nr" id="current_ward_nr">
								<option value=""></option>';
					*/
					#added by VAN 02-01-08
					$patientloc = $encounter_obj->getPatientLocation($encounter_nr, $current_ward_nr);

					if ($patientloc['location_nr'])
						$bednr = $patientloc['location_nr'];
					else
						$bednr = 0;

					$sTemp = $sTemp.'<input type="hidden" name="bed_nr" id="bed_nr" value="'.$bednr.'"><select name="current_ward_nr" id="current_ward_nr" onChange="jsSetRoom();">
								<option value="0">-Select a Ward-</option>';

					if(!empty($ward_info)&&$ward_info->RecordCount()){
						while($station=$ward_info->FetchRow()){
							$sTemp = $sTemp.'
								<option value="'.$station['nr'].'" ';
							if(isset($current_ward_nr)&&($current_ward_nr==$station['nr']))
							{
								$sTemp = $sTemp.'selected';	
							} 
								
							$sTemp = $sTemp.'>'.$station['name'].'</option>';
						}
					}
					$sTemp = $sTemp.'</select>
							<font size=1><img '.createComIcon($root_path,'redpfeil_l.gif','0','',TRUE).'> '.$LDForInpatient.'</font>';
				#}
				$smarty->assign('sWardInput',$sTemp);
				#-----------added by VAN 01-31-08-----------

				$smarty->assign('LDRoom','Room');
				$smarty->assign('LDBed','Bed');

				$sBed .=
					'<table id="tbBedRoom" border="0" cellpadding="0" cellspacing="2" width="40%">
					 </table>
					';

				$sRoom = $sRoom.'<select name="current_room_nr" id="current_room_nr" onChange="jsSetBed();">
								<option value="0">-Select a Room-</option>';
				$sRoom = $sRoom.'</select>';

				$smarty->assign('sLDRoom',$sRoom);

				#echo "enc , ward = ".$encounter_nr." - ".$current_ward_nr;

				$smarty->assign('sLDBed',$sBed);


				$sql_accom = "SELECT * FROM care_encounter_location WHERE encounter_nr='".$encounter_nr."'";
				#echo $sql_accom;
				$rs_accom = $db->Execute($sql_accom);
				$count_accom = $rs_accom->RecordCount();

				$disable_del = 'disabled';
				$disable_trans = 'disabled';
				if ($count_accom){
					$disable_del = '';
					$disable_trans = '';
				}else
					$count_accom = 0;


				//added by VAN 08-20-08
				$sroomMode = '<input type="radio" name="assign_room_mode" id="assign_room_mode" checked="checked" onClick="getMode(this.value);" value="correct"> Correct
											<input type="radio" name="assign_room_mode" id="assign_room_mode" onClick="getMode(this.value);" '.$disable_trans.' value="transfer"> Transfer
											<input type="radio" name="assign_room_mode" id="assign_room_mode" onClick="getMode(this.value);" '.$disable_del.' value="delete"> Delete
				';

				$smarty->assign('sLDRoomMode',$sroomMode);
				#-------------------

				#-------------------------------------------

				} # end for WARD field

			} //  End of if no encounter nr

            #added by VAN 06-06-2013
            #add permission
            global $allow_opdornumber;
            # Official receipt number, available/required ONLY when generating OPD encounter
			if ($encounter_type == 2||$encounter_type == IPBMOPD_enc) {
				$smarty->assign('segORNumber',"OR Number");
				#$smarty->assign('sORNumber','<input name="official_receipt_nr" id="official_receipt_nr" type="text" onBlur="trimString(this);" size="30" value="'.ucwords(strtolower(trim($official_receipt_nr))).'">');

				$name = trim($name_first)." ".trim($name_last);
				$patientOR = $encounter_obj->getPatientOPDORNoforADay($pid, $name);
				
				if($patientOR['or_no']){
					$patient_OR = $patientOR['or_no'];
				}else{
					$patientORSocial = $encounter_obj->getPatientOPDORNoforADaySocial($pid, $name);
					$patient_OR = $patientORSocial['or_no'];
				}

				$patSS = $objSS->getPatientSocialClass($pid);
				#echo $objSS->sql;
				#echo "sql = ".$patSS['discountdesc'];
				#echo "count = ".$encounter_obj->count;
				#echo "or = ".$patientOR['or_no'];
				#added by VAN 04-26-08
				#echo "<br>".$insurance_nr;
                
                #added by VAN 06-19-2013
                #for free consultation from social service
                $social = $encounter_obj->getPatientOPDFreeCFforADay($pid);
                
				/*if($insurance_nr){
					$ornum = "PIHP = ".$insurance_nr;
				}else*/

				$show = "display:none";
				if ($official_receipt_nr){
					$ornum = $official_receipt_nr;
				}elseif ($patient_OR){
					$ornum = trim($patient_OR);
                }elseif ($social['pid']){
                    $ornum = "CHARITY FROM SOCIAL SERVICE";    
				}elseif(($patSS['parentid']=='D')||$patSS['discountid']=='D'){
					#$ornum = "CLASS D (".$patSS['discountdesc'].")";
					#$ornum = strtoupper(trim(substr($patSS['discountdesc'],0,strpos($patSS['discountdesc'], "/"))));
					/*
					$len = strlen($patSS['discountdesc']);
					if ($len<4)
						$ornum = "CLASS ".strtoupper($patSS['discountdesc']);
					else
						$ornum = strtoupper($patSS['discountdesc']);
					*/
					if($patSS['discountdesc'] == 'Senior Citizen')
						$ornum = "CLASS D (SC)";
					else if ($patSS['parentid'])
						$ornum = "CLASS ".strtoupper($patSS['parentid']);
					else
						$ornum = "CLASS ".strtoupper($patSS['discountid']);
				}else{
					if ($senior_ID){
						$ornum = "SENIOR CITIZEN";
					}elseif($personnelID){
						$ornum = "DMC PERSONNEL";
					}elseif ($ptype=='phs')
						$ornum = 'PERSONNEL DEPENDENT';
					else
						$show = "display:''";	
				}

				if ($ptype=='phs')
					$ornum = $ornum." (PHS)";
                
				# Added by: JEFF
				# Date: August 24 2017
				# Purpose: for using function to fetch description instead of number.
				$ornum_fetch = $encounter_obj->getOPDTempDesc($ornum);

				if ($ornum_fetch) {
						$ornum = $ornum_fetch;
					}
					else{
						$ornum = $ornum;
					}
 				
                // added by carriane 11/10/17
				$append = '';

				if($isIPBM)
					$append = " OR soot.`status` IS NULL ORDER BY soot.`or_desc`";
				else
					$append = " AND soot.is_ipbm <> '1' ";
				// end carriane

				#Added by Jarel 07/17/2013
				#updated by carriane 11/10/17
				$or_temp = $encounter_obj->getOPDTempOR($append,$isIPBM);

                $or_option="<option value=''> </option>";
                if (is_object($or_temp)){
                    while ($result=$or_temp->FetchRow()) {
                    	$selected = '';
                    	if($ornum == $result['or_desc'])
                    	{
                    		$selected='selected'; 
                    		$show = "display:''";
                    	}
                    	elseif ($ornum != $result['or_desc']) {
                    		$show = "display:''";
                    	}
                    	$or_option.='<option '.$selected.' value="'.$result['or_desc'].'">'.ucwords($result['or_desc']).'</option>';
                    }
                }
                $or_selection = '<select name="or_temp" id="or_temp" class="segInput" onchange="assignOR(this.value)" style="width:200px;font:bold 12px Arial;'.$show.'">
                    '.$or_option.'
                </select>';
                $smarty->assign('sORTEMP', $or_selection);
                if(!$allow_opdornumber){
                	$readonly_or = 'readonly="readonly"';
                }
                if($isIPBM)	$readonly_or = '';
				#$smarty->assign('sORNumber','<input name="official_receipt_nr" id="official_receipt_nr" type="text" onBlur="trimString(this);" size="50" value="'.$ornum.'" >');
				$smarty->assign('sORNumber','<input name="official_receipt_nr" class="segInput" id="official_receipt_nr" type="text" size="50" value="'.$ornum.'" '.$readonly_or.'/>');

				$smarty->assign('sOrDialog','<div class="segPanel" id="OrDialog" style="display:none" align="left">
			                                    <div align="center" style="overflow:hidden">
			                                        <table class="data-grid rounded-borders-bottom" id="parameter_list">
			                                            <tbody>
			                                                <tr height="100%" id="params">   
			                                                    <td  width="20%" nowrap="nowrap"><input name="or_dialog" id="or_dialog" type="text" size="37" value="" onkeyup="if (event.keyCode==13) validateOR(); return false;"></td>
			                                                </tr>
			                                            </tbody>
			                                        </table>
			                                    </div>
			                                </div>');

			}elseif ($encounter_type == 4){
				$smarty->assign('segORNumber',"OR Number");
				$smarty->assign('sORNumber','<input name="official_receipt_nr" id="official_receipt_nr" type="text" size="50" value="'.trim($official_receipt_nr).'" '.$readonly_or.'/>');
			}

			#Doctor's name

			if (($encounter_type == 3)||($encounter_type == 4)||($encounter_type == IPBMIPD_enc)){
				$sTemp = '';
				$sTemp = $sTemp.'<label id="doctor_label">'.$LDDoctor3.'</label>';
				$smarty->assign('LDDoctor',$sTemp);
			}else{
				$sTemp = '';
				$sTemp = $sTemp.'<label id="doctor_label">'.$LDDoctor1.'</label>';
				$smarty->assign('LDDoctor',$sTemp);
			}

			$sTemp ='';
			$sTemp = $sTemp.'<select name="current_att_dr_nr" class="segInput" id="current_att_dr_nr" onChange="jsGetDepartment();">
								<option value="0">-Select a Doctor-</option>';
			$sTemp = $sTemp.'</select>';

			$smarty->assign('doctor_name',$sTemp);
			$smarty->assign('doctor_name2','<input name="consulting_dr" id="consulting_dr" type="hidden" size="60" >');
			#-------------------------------------------

			# If no encounter nr or outpatient, show clinic/department info, 2 = outpatient

					 # burn added: May 16, 2007
				#if ($dept_belong['dept_nr']==149){   # burn added : May 16, 2007
				if (($allow_er_user||$isIPBM)&&($ptype=='er')){   # burn added : May 16, 2007
						# the user is from ER-Triage
					$smarty->assign('LDDepartment',"Consulting $LDDepartment");

					# Added by Gervie 02/21/2016
					if($allow_er_location){
						$list_location = $encounter_obj->getERLocation();
						$list_lobby = $encounter_obj->getERLobby();

						if(is_object($list_location)){
							$sTempLoc = '';
							if(empty($er_location))
								$er_location = 1;

							$sTempLoc = $sTempLoc . '<select class="segInput" name="er_location" id="er_location">';
							$sTempLoc = $sTempLoc . '<option value="0">-Select Location Area-</option>';
							while ($result = $list_location->FetchRow()) {
								$sTempLoc = $sTempLoc . '<option value="'. $result['location_id'] .'" ';
								if($er_location == $result['location_id'])
									$sTempLoc = $sTempLoc . 'selected';
								$sTempLoc = $sTempLoc .'>'. $result['area_location'] .'</option>';
							}
							$sTempLoc = $sTempLoc . '</select>';
							
							$sTempLoc = $sTempLoc . '&nbsp;&nbsp;<select class="segInput" name="er_location_lobby" id="er_location_lobby">';
							$sTempLoc = $sTempLoc . '<option value="0">-Select Lobby Area-</option>';
							while ($result2 = $list_lobby->FetchRow()) {
								$sTempLoc = $sTempLoc . '<option value="'. $result2['lobby_id'] .'" ';
								if($er_location_lobby == $result2['lobby_id'])
									$sTempLoc = $sTempLoc . 'selected';
								$sTempLoc = $sTempLoc .'>'. $result2['lobby_name'] .'</option>';
							}
							$sTempLoc = $sTempLoc . '</select>';
								
						}

						// var_dump($sTempLoc); die();
						$smarty->assign('segERAreaLocation', "Area:");
						$smarty->assign('er_area_location', $sTempLoc);
					}
				}
				#if ($dept_belong['dept_nr']==150){   # burn added : May 16, 2007
				#if (($allow_opd_user||$isIPBM)&&($ptype=='opd')){   # burn added : May 16, 2007
				if ((($allow_opd_user||$isIPBM)&&($ptype=='opd'))||(($allow_phs_user)&&($ptype=='phs'))){   # burn added : May 16, 2007
						# the user is from OPD-Triage
					$smarty->assign('LDDepartment',"Consulting $LDClinic");
				}

				$smarty->assign('segERDetailsHideable',"");
				#if ($dept_belong['dept_nr']==148){   # burn added : May 16, 2007
				if (($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){   # burn added : May 16, 2007
				#echo "here";
						# the user is from Admitting section
					$smarty->assign('LDDepartment',"Attending $LDDepartment");
					$smarty->assign('segERDetailsHideable'," class='ERDetails'");

					$sTemp_ERDr_option='';
					#commented by VAN 07-05-08
					#if ($seg_direct_admission){
						$sTemp_ERDr_option = ' onChange="jsGetEROPDDepartment();" ';
					#}
					$sTemp_ERDr ='';
					$sTemp_ERDr = $sTemp_ERDr.'<select name="consulting_dr_nr" id="consulting_dr_nr"'.$sTemp_ERDr_option.'>';
					$sTemp_ERDr = $sTemp_ERDr."</select> \n";

					$smarty->assign('sERDrInput',$sTemp_ERDr);
					$sTemp_ERDr ='';

					$smarty->assign('segEROPDDr',"<font color='red'>Admitting Physician</font>");
					if ($patient_enc['encounter_class_nr']==1){
						$smarty->assign('segEROPDDepartment',"<font color='red'>Admitting $LDDepartment</font>");
					}elseif ($patient_enc['encounter_class_nr']==2){
						$smarty->assign('segEROPDDepartment',"<font color='red'>Admitting $LDClinic</font>");
					}

					if ($seg_direct_admission){
						$smarty->assign('segEROPDDr',"<font color='red'>Admitting Physician</font>");
						$smarty->assign('segEROPDDepartment',"<font color='red'>Admitting $LDDepartment</font>");
						$sTemp = '';
						if($isIPBM){
							$sTemp = $sTemp.'
							<select disabled>
							<option class="segInput" value="'.IPBMdept_nr.'">IPBM</option>
							</select>
							<input name="consulting_dept_nr" type="hidden" id="consulting_dept_nr" value="'.IPBMdept_nr.'">';
						}
						else{
							$sTemp = $sTemp.'
							<select name="consulting_dept_nr" id="consulting_dept_nr" onChange="jsGetEROPDDoctors();">
							</select>';
						}
						$smarty->assign('sERDeptInput',$sTemp);
					}else{
						#$smarty->assign('sERDeptInput','<input name="consulting_dept_nr" id="consulting_dept_nr" type="text" value="'.$patient_enc['consulting_dept_nr'].'">'.$patient_consulting_doc['name_formal']);
						#edited by VAN 02-27-08
						#$sTemp = '<input name="consulting_dept_nr" id="consulting_dept_nr" type="hidden" value="'.(empty($patient_enc['consulting_dept_nr']))?$patient_enc['current_dept_nr']:$patient_enc['consulting_dept_nr'].'">';
						#$sTemp.= $patient_consulting_doc['name_formal'];
						#echo "temp = ".$sTemp;
						#$sTemp = '';
						#$smarty->assign('sERDeptInput',$sTemp);
						$sTemp = '';
						if($isIPBM){
							$sTemp = $sTemp.'
							<select disabled>
							<option class="segInput" value="'.IPBMdept_nr.'">IPBM</option>
							</select>
							<input name="consulting_dept_nr" type="hidden" id="consulting_dept_nr" value="'.IPBMdept_nr.'">';
						}else{
							$sTemp = $sTemp.'
							<select name="consulting_dept_nr" id="consulting_dept_nr" onChange="jsGetEROPDDoctors();">
							</select>';
						}
						$smarty->assign('sERDeptInput',$sTemp);
                        $smarty->assign('segEROPDDr',"<font color='red'>Admitting Physician</font>");
					}
				}# end of if-stmt "if ($dept_belong['dept_nr']==148)"

				$sTemp = '';

				if($in_dept){
					while($deptrow=$all_meds->FetchRow()){
						if(isset($current_dept_nr)&&($current_dept_nr==$deptrow['nr'])){
							$sTemp = $sTemp.$deptrow['name_formal'];
							$sTemp = $sTemp.'<input name="current_dept_nr" class="segInput" type="hidden"  value="'.$current_dept_nr.'">';
							break;
						}
					}
				}else{

					#$sTemp = $sTemp.'<select name="current_dept_nr" id="current_dept_nr" onChange="checkPreviousTrxn(); jsGetDoctors();">
					#		<option value="0">-Select a Department-</option>';
					#$sTemp = $sTemp.'</select>';
					if($isIPBM){
						$sTemp = $sTemp.'<select class="segInput" disabled>
								<option value="'.IPBMdept_nr.'">IPBM</option>
								';
						$sTemp = $sTemp.'</select><input type="hidden" name="current_dept_nr" id="current_dept_nr" value="'.IPBMdept_nr.'">';
					}else{
						$sTemp = $sTemp.'<select name="current_dept_nr" class="segInput" id="current_dept_nr" onChange="jsGetEROPDDoctors_2(); checkPreviousTrxn(); jsUpdateAdmissionDate(this.value);">
								<option value="0">-Select a Department-</option>';
						$sTemp = $sTemp.'</select>';
					}
				}
				$smarty->assign('sDeptInput',$sTemp);
			#} // End of if no encounter nr #---------comment 03-06-07----------

		#--------added condition 03-14-07 by vanessa ---------
		#if ($dept_belong['id']!="OPD-Triage"){
		if (($ptype!='opd')||($ptype!='phs')){
			#$readonly=" readonly ";   # burn added : May 16, 2007
			$readonly=" ";   # edited by VAN 02-10-09

			#if ($dept_belong['dept_nr']==149){   # burn added : May 16, 2007
			#edited by VAN 04-17-08
			#if (($dept_belong['dept_nr']==149)||($seg_direct_admission)){   # burn added : May 16, 2007
			if ((($allow_er_user||$isIPBM)&&($ptype=='er'))||($seg_direct_admission)){
				# the user is from ER-Triage
				$readonly="";
				}

			#if ($dept_belong['dept_nr']==148){   # burn added : May 16, 2007
			#commented by VAN 02-22-09
						#edited bt VAN 08-22-09
						$admit_label = "";
			if (($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
					$admit_label = "Admitting ";
						#}
					# the user is from Admitting section
				#$smarty->assign('segERDiagnosis',"<font color='red'>Admitting ".$LDDiagnosis."</font>");
								$smarty->assign('segERDiagnosis',"<font color='red'>".$admit_label.$LDDiagnosis."</font>");
					# burn added : June 1, 2007
				if ($seg_direct_admission) # not required if direct admission
					#$smarty->assign('segERDiagnosis',"Admitting ".$LDDiagnosis);
										$smarty->assign('segERDiagnosis',$admit_label.$LDDiagnosis);
			}
				#$smarty->assign('er_opd_diagnosis','<textarea name="er_opd_diagnosis" id="er_opd_diagnosis" cols="55" rows="3" wrap="VIRTUAL">'.trim(stripslashes($er_opd_diagnosis)).'</textarea>');   # burn added: May 16, 2007
				// if ($is_doctor) {
				// 	$smarty->assign('er_opd_diagnosis','<textarea name="er_opd_diagnosis" id="er_opd_diagnosis" cols="55" rows="3" wrap="VIRTUAL">'.trim($er_opd_diagnosis).'</textarea>');
				// }else{
				// 	$smarty->assign('er_opd_diagnosis','<textarea name="er_opd_diagnosis" id="er_opd_diagnosis" cols="55" rows="3" wrap="VIRTUAL" readonly>'.trim($er_opd_diagnosis).'</textarea>');
				// }
				$smarty->assign('er_opd_diagnosis','<textarea name="er_opd_diagnosis" id="er_opd_diagnosis" cols="55" rows="3" wrap="VIRTUAL">'.trim($er_opd_diagnosis).'</textarea>');   # burn added: May 16, 2007
			if ((($allow_opd_user||$isIPBM)&&($ptype=='opd'))||(($allow_er_user||$isIPBM)&&($ptype=='er'))){
				$smarty->assign('segComplaint',TRUE);
				$smarty->assign('segChiefComplaint',"Chief Complaint");
				$smarty->assign('chief_complaint','<textarea name="chief_complaint" id="chief_complaint" class="segInput" cols="55" rows="3" wrap="VIRTUAL">'.trim($chief_complaint).'</textarea>');   # burn added: May 16, 2007
			}
			#}# end of if-stmt "if ($dept_belong['dept_nr']==148)"

				# burn added: May 16, 2007
			 if ((($encounter_class_nr==1)&&(!$seg_direct_admission))||($seg_direct_admission)){
#			if (($encounter_class_nr==1)||($seg_direct_admission)){
				$smarty->assign('segShowIfFromER',"true");
			}else{
				$smarty->assign('segShowIfFromER',"");
			}

			if ($seg_direct_admission){
				$smarty->assign('segShowIfDirect',"true");
			}else{
				$smarty->assign('segShowIfDirect',"");
			}
			#echo " drie = ".$seg_direct_admission;
			#if ($encounter_class_nr!=2){
			if (($encounter_class_nr!=2&&$encounter_class_nr!=IPBMOPD_enc)||($seg_direct_admission)){
				$smarty->assign('LDDiagnosis',"Referred ".$LDDiagnosis);
				$smarty->assign('referrer_diagnosis','<textarea name="referrer_diagnosis" id="referrer_diagnosis" cols="55" rows="3" wrap="VIRTUAL"'.$readonly.'>'.ucwords(strtolower(trim($referrer_diagnosis))).'</textarea>');   # burn added: April 20, 2007
				$smarty->assign('LDRecIns',$LDRecIns);
				$smarty->assign('referrer_institution','<input name="referrer_institution" id="referrer_institution" type="text" size="73" value="'.ucwords(strtolower(trim($referrer_institution))).'"'.$readonly.'>');
				$smarty->assign('LDSpecials',$LDSpecials);
				$smarty->assign('referrer_notes','<textarea name="referrer_notes" id="referrer_notes" cols="55" rows="2" wrap="VIRTUAL"'.$readonly.'>'.ucwords(strtolower(trim($referrer_notes))).'</textarea>');   # burn added: May 16, 2007
				$smarty->assign('LDRecBy',$LDRecBy);
			}

			#if ((($patient_enc['encounter_type']==1)||($patient_enc['encounter_type']==2))&&($update==1)&&($dept_belong['id']=="Admission")){
			if ((($patient_enc['encounter_type']==1)||($patient_enc['encounter_type']==2)||($patient_enc['encounter_type']==IPBMOPD_enc))&&($update==1)&&(($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))){
				$smarty->assign('referrer_dr_name','<input name="referrer_dr_name" id="referrer_dr_name" onKeyUp="ReferralHandler1()" type="text" size="73" value="'.ucwords(strtolower(trim($referrer_dr))).'"'.$readonly.'>');
				$smarty->assign('referrer_dr','<input name="referrer_dr" id="referrer_dr" type="hidden" value="'.$patient_enc['referrer_dr'].'">');

			}else{

				$dr_name = $pers_obj->get_Person_name($patient_enc['referrer_dr']);
				$dr_fname = ucwords(strtolower(trim($dr_name['name_first'])));
				$dr_fname2 = ucwords(strtolower(trim($dr_name['name_2'])));
				$dr_lname = ucwords(strtolower(trim($dr_name['name_last'])));
				$ref_dr_name= "Dr. ".$dr_fname." ".$dr_fname2."".$dr_lname;

				if (is_numeric($referrer_dr)){

					$smarty->assign('referrer_dr_name','<input name="referrer_dr_name" id="referrer_dr_name" type="text" onKeyUp="ReferralHandler1();"  onBlur="GetDr_nr();" size="73" value="'.trim($ref_dr_name).'"'.$readonly.'>');
					$smarty->assign('referrer_dr','<input name="referrer_dr" id="referrer_dr" type="hidden" value="'.$patient_enc['referrer_dr'].'">');
					$smarty->assign('referrer_dept','<input name="referrer_dept" id="referrer_dept" type="hidden" size="5" value="'.$patient_enc['referrer_dept'].'">');
				}else{

					$smarty->assign('referrer_dr_name','<input name="referrer_dr_name" id="referrer_dr_name" type="text" onKeyUp="ReferralHandler1();" onBlur="GetDr_nr();" size="73" value="'.ucwords(strtolower(trim($referrer_dr))).'"'.$readonly.'>');
					$smarty->assign('referrer_dr','<input name="referrer_dr" id="referrer_dr" type="hidden" value="'.ucwords(strtolower(trim($referrer_dr))).'">');
					$smarty->assign('referrer_dept','<input name="referrer_dept" id="referrer_dept" type="hidden" size="5" value="">');
				}
				$smarty->assign('name1','<input name="name1" id="name1" type="hidden" size="10" value="">');
				$smarty->assign('name2','<input name="name2" id="name2" type="hidden" size="10"  value="">');
				$smarty->assign('lname','<input name="lname" id="lname" type="hidden" size="10"  value="">');
			}

			if (empty($insurance_class_nr))
					$insurance_class_nr = 3;

			#---------commented by justin 3/18/15------
			#if($dept_belong['id']=="Admission"){
// 			if ((($allow_ipd_user||$isIPBM)&&($ptype=='ipd'))||(($allow_er_user||$isIPBM)&&($ptype=='er'))){
// 				if ($errorinsclass) $smarty->assign('LDBillType',"<font color=red>$LDBillType</font>");
// 					else  $smarty->assign('LDBillType',$LDBillType);

// 				#added by VAN 11-26-09
// 				if ((($encounter_type==3)||($encounter_type==4))&&($patient_Insinfo['insurance_nr'])){
// 						$insurance_class_nr = 2;
// 				}

// 				$sTemp = '';
// 				if(is_object($insurance_classes)){
// 					while($result=$insurance_classes->FetchRow()) {
// 						$sTemp = $sTemp.'<input name="insurance_class_nr" id="insurance_class_nr" type="radio" onChange="DisableInsurance();"  value="'.$result['class_nr'].'" ';
// 						if($insurance_class_nr==$result['class_nr']) $sTemp = $sTemp.'checked';
// 						$sTemp = $sTemp.'>';

// 						$LD=$result['LD_var'];
// 						#if(isset($$LD)&&!empty($$LD)) $sTemp = $sTemp.$$LD;
// 						if(isset($$LD)&&!empty($$LD)) $sTemp.=$$LD;
// 							#else $sTemp = $sTemp.$result['name'];
// 							else $sTemp .= $result['name'];
// 					}
// 				}
// 				$smarty->assign('sBillTypeInput',$sTemp);

// 				#-----added by VAN 08-30-07-----

// 				$smarty->assign('sBtnAddItem','<a href="javascript:void(0);"
// 										 onclick="return overlib(
// 													 OLiframeContent(\'seg-reg-insurance-tray.php?pid='.$pid.'&encounter_nr='.$encounter_nr.'&frombilling=0\', 600, 450, \'fOrderTray\', 1, \'auto\'),
// 													 WIDTH,600, TEXTPADDING,0, BORDER,0,
// 												 STICKY, SCROLL, CLOSECLICK, MODAL,
// 												 CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
// 																		 CAPTIONPADDING,4,
// CAPTION,\'Add insurance details\',
// 														 MIDX,0, MIDY,0,
// STATUS,\'Add insurance details\');"
// 									 onmouseout="nd();">
// 							<img name="btninsurance" id="btninsurance" src="'.$root_path.'images/btn_additems.gif" border="0"></a>');

// 				if ($error_ins_nr) $smarty->assign('LDInsuranceNr',"<font color=red>$LDInsuranceList</font>");
// 					else  $smarty->assign('LDInsuranceNr',$LDInsuranceList);

// 				#$smarty->assign('sOrderItems',"
// 				#<tr>
// 				#	<td colspan=\"10\">Insurance list is currently empty...</td>
// 				#</tr>");
// 				$smarty->assign('sOrderItems',"
// 				<tr>
// 					<td colspan=\"10\">Insurance list is currently empty...</td>
// 				</tr>");

// 				# Note: make a class function for this part later
// 				$result = $encounter_obj->getPersonInsuranceItems($encounter_nr);
// 				#echo "sql = ".$encounter_obj->sql;
// 				$rows=array();
// 				$src = '';
// 				while ($row=$result->FetchRow()) {
// 					$rows[] = $row;
// 				}
// 				#echo "rows = ";
// 				#print_r($rows);
// 				#echo "pid = ".$pid;

// 				foreach ($rows as $i=>$row) {
// 					if ($row) {
// 						$count++;
// 						$alt = ($count%2)+1;

// 						$bulk_array_prev[] = array($row['hcare_id'],$row['insurance_nr'],$row["is_principal"]);
// 						$insurance_array_prev .= $row['hcare_id'].",";

// 						$sql2 = "SELECT ci.* FROM care_person_insurance AS ci
// 									WHERE ci.pid =".$pid."
// 									AND ci.hcare_id = '".$row['hcare_id']."'";
// 						#echo "sql = ".$sql2;
// 						$res=$db->Execute($sql2);

// 						$row2=$res->RecordCount();

// 						if ($row2!=0){
// 							while($rsObj=$res->FetchRow()) {
// 									$ins_nr = $rsObj["insurance_nr"];
// 									$is_principal = $rsObj["is_principal"];
// 									if ($is_principal){
// 										$principal = "YES";
// 									}else{
// 										$principal = "NO";
// 									}
// 							}
// 						}

// 						$src .= '
// 									<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
// 										<input type="hidden" name="items[]" id="rowID'.$row['hcare_id'].'" value="'.$row['hcare_id'].'" />
// 										<input type="hidden" name="nr[]" id="rowNr'.$row['hcare_id'].'" value="'.$ins_nr.'" />
// 										<input type="hidden" name="is_principal[]" id="rowis_principal'.$row['hcare_id'].'" value="'.$is_principal.'" />
// 										<td class="centerAlign"><a href="javascript:removeItem(\''.$row['hcare_id'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
// 										<td id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
// 										<td width="25%" align="right" id="inspin'.$row['hcare_id'].'">'.$ins_nr.'</td>
// 										<td width="18%" class="centerAlign" id="insprincipal'.$row['hcare_id'].'">'.$principal.'</td>
// 										<td></td>
// 									</tr>
// 						';
// 					}
// 				}
// 				$insurance_array_prev = substr($insurance_array_prev,0,strlen($insurance_array_prev)-1);
// 				#echo "<br>insurance class = ".$insurance_class_nr;
// 				#if ($src) $smarty->assign('sOrderItems',$src);
// 				if (($src) && ($insurance_class_nr!=3))
// 					$smarty->assign('sOrderItems',$src);

// 				#------------------------------

// 			} # end if Admission
			#--------- end of comment 3/18/15------
		} # end if not OPD-Triage

			if (!$GLOBAL_CONFIG['patient_service_care_hide']&& is_object($care_service)){
				$smarty->assign('LDCareServiceClass',$LDCareServiceClass);
				$sTemp = '';

				$sTemp = $sTemp.'<select name="sc_care_class_nr" >';

				while($buffer=$care_service->FetchRow()){
					$sTemp = $sTemp.'
						<option value="'.$buffer['class_nr'].'" ';
					if($sc_care_class_nr==$buffer['class_nr']) $sTemp = $sTemp.'selected';
					$sTemp = $sTemp.'>';
					if(empty($$buffer['LD_var'])) $sTemp = $sTemp.$buffer['name'];
						else $sTemp = $sTemp.$$buffer['LD_var'];
					$sTemp = $sTemp.'</option>';
				}
				$sTemp = $sTemp.'</select>';

				$smarty->assign('sCareServiceInput',$sTemp);

				$smarty->assign('LDFrom',$LDFrom);
				$sTemp = '';
				 if(!empty($sc_care_start)) $sTemp = @formatDate2Local($sc_care_start,$date_format);

				$smarty->assign('sCSFromInput','<input type="text" name="sc_care_start"  value="'.$sTemp.'" size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('LDTo',$LDTo);
				$sTemp = '';
				 if(!empty($sc_care_end)) $sTemp = @formatDate2Local($sc_care_end,$date_format);
				$smarty->assign('sCSToInput','<input type="text" name="sc_care_end"  value="'.$sTemp.'"  size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('sCSHidden','<input type="hidden" name="sc_care_nr" value="'.$sc_care_nr.'">');

			}

			if (!$GLOBAL_CONFIG['patient_service_room_hide']&& is_object($room_service)){
				$smarty->assign('LDRoomServiceClass',$LDRoomServiceClass);
				$sTemp = '';

				$sTemp = $sTemp.'<select name="sc_room_class_nr" >';

				while($buffer=$room_service->FetchRow()){
					$sTemp = $sTemp.'
						<option value="'.$buffer['class_nr'].'" ';
					if($sc_room_class_nr==$buffer['class_nr']) $sTemp = $sTemp.'selected';
					$sTemp = $sTemp.'>';
					if(empty($$buffer['LD_var'])) $sTemp = $sTemp.$buffer['name'];
						else $sTemp = $sTemp.$$buffer['LD_var'];
					$sTemp = $sTemp.'</option>';
				}
				$sTemp = $sTemp.'</select>';

				$smarty->assign('sCareRoomInput',$sTemp);

				//$smarty->assign('LDFrom',$LDFrom);
				$sTemp = '';
				 if(!empty($sc_room_start)) $sTemp = @formatDate2Local($sc_room_start,$date_format);

				$smarty->assign('sRSFromInput','<input type="text" name="sc_room_start"  value="'.$sTemp.'" size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				//$smarty->assign('LDTo',$LDTo);
				$sTemp = '';
				 if(!empty($sc_room_end)) $sTemp = @formatDate2Local($sc_room_end,$date_format);
				$smarty->assign('sRSToInput','<input type="text" name="sc_room_end"  value="'.$sTemp.'"  size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('sRSHidden','<input type="hidden" name="sc_room_nr" value="'.$sc_room_nr.'">');

			}

			if (!$GLOBAL_CONFIG['patient_service_att_dr_hide']&& is_object($att_dr_service)){
				$smarty->assign('LDAttDrServiceClass',$LDAttDrServiceClass);
				$sTemp = '';

				$sTemp = $sTemp.'<select name="sc_att_dr_class_nr" >';

				while($buffer=$att_dr_service->FetchRow()){
					$sTemp = $sTemp.'
						<option value="'.$buffer['class_nr'].'" ';
					if($sc_att_dr_class_nr==$buffer['class_nr']) $sTemp = $sTemp.'selected';
					$sTemp = $sTemp.'>';
					if(empty($$buffer['LD_var'])) $sTemp = $sTemp.$buffer['name'];
						else $sTemp = $sTemp.$$buffer['LD_var'];
					$sTemp = $sTemp.'</option>';
				}
				$sTemp = $sTemp.'</select>';

				$smarty->assign('sCareDrInput',$sTemp);

				//$smarty->assign('LDFrom',$LDFrom);
				$sTemp = '';
				 if(!empty($sc_att_dr_start)) $sTemp = @formatDate2Local($sc_att_dr_start,$date_format);

				$smarty->assign('sDSFromInput','<input type="text" name="sc_att_dr_start"  value="'.$sTemp.'" size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				//$smarty->assign('LDTo',$LDTo);
				$sTemp = '';
				 if(!empty($sc_att_dr_end)) $sTemp = @formatDate2Local($sc_att_dr_end,$date_format);
				$smarty->assign('sDSToInput','<input type="text" name="sc_att_dr_end"  value="'.$sTemp.'"  size=9 maxlength=10   onBlur="IsValidDate(this,\''.$date_format.'\')" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')">');
				$smarty->assign('sDSHidden','<input type="hidden" name="sc_att_dr_nr" value="'.$sc_att_dr_nr.'">');

			}
			$LDAdmitBy = 'Encoded By';
			$smarty->assign('LDAdmitBy',$LDAdmitBy);
			#if (empty($encoder)) $encoder = $HTTP_COOKIE_VARS[$local_user.$sid];
            #edited by VAN 02-22-2013
            if (empty($encoder)) $encoder = $_SESSION['sess_user_name'];
			$smarty->assign('encoder','<input  name="encoder" class="segInput" id="encoder" type="text" value="'.$encoder.'" size="28" readonly>');

			$smarty->assign('LDDeptBelong',$LDDepartment);
			$smarty->assign('sDeptBelong','<input  name="sDeptBelong" class="segInput" type="text" value="'.$dept_belong['name_formal'].'" size="28" readonly>');

			#----------------added 03-08-07-------------------
			#if($dept_belong['id']=="Admission"){
			if (($allow_ipd_user||$isIPBM)&&($ptype=='ipd')){
				#condition
				if ($seg_direct_admission)
					$LDCondition ='Condition at Other Institution';

				$smarty->assign('LDCondition',$LDCondition);

				if(is_object($condition_classes)){
					$sTemp = '';
					while($result=$condition_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="cond_code" id="cond_code" type="radio"  value="'.$result['cond_code'].'" ';
							#if($cond_code==$result['cond_code']) $sTemp = $sTemp.'checked';
							if($patient_enc_cond['cond_code']==$result['cond_code']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['cond_desc']."<br>";

					}
				}
				$smarty->assign('sCondition',$sTemp);

				#results
				if ($seg_direct_admission)
					$smarty->assign('LDResults',$LDResults." from Other Institution");
				else
					$smarty->assign('LDResults',$LDResults." from ER");

				if(is_object($results_classes)){
					$sTemp = '';
					$count=0;
					while($result=$results_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="result_code" id="result_code" type="radio" value="'.$result['result_code'].'" ';
							#if($result_code==$result['result_code']) $sTemp = $sTemp.'checked';
							if($patient_enc_res['result_code']==$result['result_code']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['result_desc']."<br>";

						#added by VAN 02-27-08
						if($count<=1){
							$rowResultA =$sTemp;
						if($count==1){$sTemp='';}
						}else{ $rowResultB =$sTemp; }
						$count++;

					}
				}
				#$smarty->assign('sResults',$sTemp);
				$smarty->assign('rowResultA',$rowResultA);
				$smarty->assign('rowResultB',$rowResultB);

				#disposition
				if ($seg_direct_admission){
					$smarty->assign('LDDisposition',$LDDisposition." from Other Institution");
					if (!$patient_enc_disp['disp_code'])
						$patient_enc_disp['disp_code'] = 1;
				}else
					$smarty->assign('LDDisposition',$LDDisposition." from ER");

				if(is_object($disposition_classes)){
					$sTemp = '';
					$count=0;
					while($result=$disposition_classes->FetchRow()) {
						$sTemp = $sTemp.'<input name="disp_code" id="disp_code" type="radio" value="'.$result['disp_code'].'" ';
							#if($disp_code==$result['disp_code']) $sTemp = $sTemp.'checked';
							if($patient_enc_disp['disp_code']==$result['disp_code']) $sTemp = $sTemp.'checked';
						$sTemp = $sTemp.'>';
						$sTemp = $sTemp.$result['disp_desc']."<br>";

						#added by VAN 02-27-08
						if($count<=2){
							$rowDispositionA =$sTemp;
						if($count==2){$sTemp='';}
						}else{ $rowDispositionB =$sTemp; }
						$count++;
					}
				}

				#edited by VAN 02-27-08
				$smarty->assign('rowDispositionA',$rowDispositionA);
				$smarty->assign('rowDispositionB',$rowDispositionB);
			}
			#------------------------------------------------

			if (empty($assign_mode))
				$assign_mode = 'correct';

			#added by VAN 01-21-09
			if ($patient_enc['encounter_date']){
				$segConsultDateTime = $patient_enc['encounter_date'];
				$consult_date = date("m/d/Y",strtotime($segConsultDateTime));
				$consult_time = date("h:i",strtotime($segConsultDateTime));
				$consult_meridian = date("A",strtotime($segConsultDateTime));
			}else{
				$consult_date = $admission_date;
				$consult_time = $admission_time;
				$consult_meridian = $meridian;
			}
			#------------------------

			#added by VAN 09-01-2011

			#with existing admission, get the encounter date of the ER encounter. : parent_encounter_nr
			#get the encounter_date of the parent encounter (ER or OPD encounter)
			$parent_row = $encounter_obj->getParentEncInfo($encounter_nr);

			if ($encounter_obj->count){
				$parent_enc_nr = $parent_row['parent_encounter_nr'];
				$parent_encounter_date = date("Y-m-d H:i:s",strtotime($parent_row['parent_encounter_date']));
				$current_encounter_date = date("Y-m-d H:i:s",strtotime($parent_row['encounter_date']));
				$admission_encounter_date = date("Y-m-d H:i:s",strtotime($parent_row['admission_dt']));
				$parent_encounter_nr = $parent_row['parent_encounter_nr'];
			}else{
				$parent_encounter_date = date("Y-m-d H:i:s",strtotime($patient_enc['encounter_date']));
				$current_encounter_date = date("Y-m-d H:i:s",strtotime($patient_enc['encounter_date']));
			}
			#---------

			if (($personell_nr)&&($is_doctor)&&($allow_phs_user))
				$phs_dr = $personell_nr;
			else
				$phs_dr = 0;

            if (($date_birth!='0000-00-00')&&($date_birth!=''))
                $date_birth = date('m/d/Y', strtotime($date_birth));
                
            if (($birth_time!='0000-00-00')&&($birth_time!='')){
                $time_birth = date('h:i', strtotime($birth_time));
                
                $time_birth_meridian = date('A', strtotime($birth_time));                        
            }    

            $permission_val = explode(" ",$HTTP_SESSION_VARS['sess_permission']);

			
				$sTemp = '<input type="hidden" name="pid" id="pid" value="'.$pid.'">
					<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">
					<input type="hidden" name="appt_nr" value="'.$appt_nr.'">
					<input type="hidden" name="sid" value="'.$sid.'">
					<input type="hidden" name="lang" value="'.$lang.'">
					<input type="hidden" name="mode" value="save">
					<input type="hidden" name="insurance_array_prev" id="insurance_array_prev" value="'.$insurance_array_prev.'" size="100">
					<input type="hidden" name="insurance_show" value="'.$insurance_show.'">
					<input type="hidden" name="consult_date" id="consult_date" value="'.$consult_date.'">
					<input type="hidden" name="consult_time" id="consult_time" value="'.$consult_time.'">
					<input type="hidden" name="consult_meridian" id="consult_meridian" value="'.$consult_meridian.'">

					<input type="hidden" name="prev_ward_nr" id="prev_ward_nr" value="'.($current_ward_nr?$current_ward_nr:'0').'">
					<input type="hidden" name="prev_room_nr" id="prev_room_nr" value="'.($current_room_nr?$current_room_nr:'0').'">
					<input type="hidden" name="prev_bed_nr" id="prev_bed_nr" value="'.$prev_bed_nr.'">
					<input type="hidden" name="cur_bed_nr" id="cur_bed_nr" value="'.$cur_bed_nr.'">
					<input type="hidden" name="is_same" id="is_same" value="0">
					<input type="hidden" name="accomodation_type" id="accomodation_type" value="" >
					<input type="hidden" name="assign_mode" id="assign_mode" value="'.$assign_mode.'">
					<input type="hidden" name="phs_dr" id="phs_dr" value="'.$phs_dr.'">
					<input type="hidden" name="assign_type" id="assign_type" value="'.$assign_type.'">
					<input type="hidden" name="count_accom" id="count_accom" value="'.$count_accom.'">

	                <input type="hidden" name="date_birth" id="date_birth" value="'.$date_birth.'">
	                <input type="hidden" name="birth_time" id="birth_time" value="'.$time_birth.'">
	                <input type="hidden" name="birth_time_meridian" id="birth_time_meridian" value="'.$time_birth_meridian.'">

					<input type="hidden" name="parent_encounter_date" id="parent_encounter_date" value="'.date("m/d/Y H:i:s",strtotime($parent_encounter_date)).'">
					<input type="hidden" name="current_encounter_date" id="current_encounter_date" value="'.date("m/d/Y H:i:s",strtotime($current_encounter_date)).'">
					<input type="hidden" name="isdateupdate" id="isdateupdate" value="">
					<input type="hidden" name="parent_encounter_nr" id="parent_encounter_nr" value="'.$parent_encounter_nr.'">

					';

			/*$sTemp = '<input type="hidden" name="pid" id="pid" value="'.$pid.'">
				<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">
				<input type="hidden" name="appt_nr" value="'.$appt_nr.'">
				<input type="hidden" name="sid" value="'.$sid.'">
				<input type="hidden" name="lang" value="'.$lang.'">
				<input type="hidden" name="mode" value="save">
				<input type="hidden" name="insurance_array_prev" id="insurance_array_prev" value="'.$insurance_array_prev.'" size="100">
				<input type="hidden" name="insurance_show" value="'.$insurance_show.'">
				<input type="hidden" name="consult_date" id="consult_date" value="'.$consult_date.'">
				<input type="hidden" name="consult_time" id="consult_time" value="'.$consult_time.'">
				<input type="hidden" name="consult_meridian" id="consult_meridian" value="'.$consult_meridian.'">

				<input type="hidden" name="prev_ward_nr" id="prev_ward_nr" value="'.$current_ward_nr.'">
				<input type="hidden" name="prev_room_nr" id="prev_room_nr" value="'.$current_room_nr.'">
				<input type="hidden" name="prev_bed_nr" id="prev_bed_nr" value="'.$prev_bed_nr.'">
				<input type="hidden" name="cur_bed_nr" id="cur_bed_nr" value="'.$cur_bed_nr.'">
				<input type="hidden" name="is_same" id="is_same" value="0">
				<input type="hidden" name="accomodation_type" id="accomodation_type" value="" >
				<input type="hidden" name="assign_mode" id="assign_mode" value="'.$assign_mode.'">
				<input type="hidden" name="phs_dr" id="phs_dr" value="'.$phs_dr.'">
				<input type="hidden" name="assign_type" id="assign_type" value="'.$assign_type.'">
				<input type="hidden" name="count_accom" id="count_accom" value="'.$count_accom.'">

                <input type="hidden" name="date_birth" id="date_birth" value="'.$date_birth.'">
                <input type="hidden" name="birth_time" id="birth_time" value="'.$time_birth.'">
                <input type="hidden" name="birth_time_meridian" id="birth_time_meridian" value="'.$time_birth_meridian.'">

				<input type="hidden" name="parent_encounter_date" id="parent_encounter_date" value="'.date("m/d/Y H:i:s",strtotime($parent_encounter_date)).'">
				<input type="hidden" name="current_encounter_date" id="current_encounter_date" value="'.date("m/d/Y H:i:s",strtotime($current_encounter_date)).'">
				<input type="hidden" name="isdateupdate" id="isdateupdate" value="">
				<input type="hidden" name="parent_encounter_nr" id="parent_encounter_nr" value="'.$parent_encounter_nr.'">

				';*/



			if($update) $sTemp = $sTemp."\n<input type='hidden' name=update value=1>";

				# burn added : May 24, 2007
			if ($seg_direct_admission){
				# if direct admission, set encounter_status AS direct admission
				$sTemp = $sTemp."\n				<input name='encounter_status' id='encounter_status' type='hidden' value='direct_admission'>";
			}

				# burn added: March 29, 2007
			if (($patient_enc['encounter_type']==1) ||($patient_enc['encounter_type']==2) ||($patient_enc['encounter_type']==IPBMOPD_enc)){
				$sTemp = $sTemp."\n				<input type='hidden' name='firstUpdate' value=1>";
			}else{
				$sTemp = $sTemp."\n				<input type='hidden' name='firstUpdate' value=0>";
			}

			$smarty->assign('sHiddenInputs',$sTemp);

			#$smarty->assign('pbSave','<input  type="image" id="saveButton" name="saveButton" '.createLDImgSrc($root_path,'savedisc.gif','0').' title="'.$LDSaveData.'" align="absmiddle" onclick="if (confirm(\'Process this transaction?\')){ if (chkform(\'aufnahmeform\')){ document.inputform.submit(); }else {$(\'saveButton\').disabled = false;}}">');
					#$smarty->assign('pbSave','<input  type="image" id="saveButton" name="saveButton" '.createLDImgSrc($root_path,'savedisc.gif','0').' title="'.$LDSaveData.'" align="absmiddle" onclick="gotoCheck();">');
					$smarty->assign('pbSave','<span id="bsave"><a href="javascript:void(0);"><img id="saveButton" name="saveButton" '.createLDImgSrc($root_path,'savedisc.gif','0').'  title="'.$LDSaveData.'"  align="absmiddle" onclick="gotoCheck();"></a></span>');

			$smarty->assign('pbRegData','<a href="patient_register_show.php'.URL_APPEND.'&pid='.$pid.'&ptype='.$ptype.$IPBMextend.'"><img '.createLDImgSrc($root_path,'reg_data.gif','0').'  title="'.$LDRegistration.'"  align="absmiddle"></a>');

#			$smarty->assign('pbCancel','<a href="aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');   # burn commented: March 12, 2007

				# burn added: March 12, 2007
			if($origin=='patreg_reg') {
				$smarty->assign('pbCancel','<a href="'.$breakfile.$IPBMextend.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');
			}else{
				$smarty->assign('pbCancel','<a href="aufnahme_daten_zeigen.php'.URL_REDIRECT_APPEND.'&encounter_nr='.$encounter_nr.'&origin=admit&sem=isadmitted&target=entry&ptype='.$ptype.$IPBMextend.'"><img '.createLDImgSrc($root_path,'cancel.gif','0').'  title="'.$LDCancel.'"  align="absmiddle"></a>');
			}
			//<!-- Note: uncomment the ff: line if you want to have a reset button  -->
			/*<!--
			$smarty->assign('pbRefresh','<a href="javascript:document.aufnahmeform.reset()"><img '.createLDImgSrc($root_path,'reset.gif','0').' alt="'.$LDResetData.'"  align="absmiddle"></a>');
			-->
			*/

			if($error==1)
				$smarty->assign('sErrorHidInputs','<input type="hidden" name="forcesave" value="1">
				<input  type="submit" value="'.$LDForceSave.'">');

	if (!($newdata)) {

		$sTemp = '
		<form name="admission" action='.$thisfile.' method=post>
		<input type="hidden" name=sid value='.$sid.'>
		<input type="hidden" name=patnum value="">
		<input type="hidden" name="lang" value="'.$lang.'">
		<input type=submit value="'.$LDNewForm.'">
		</form>';

		$smarty->assign('sNewDataForm',$sTemp);
	}

	#----added by CHA, May 21,2010
	$smarty->assign('sMotherCaseNr', '<input type="text" class="segInput" value="" maxlength="12" size="20" name="parent_encounter_nr" id="parent_encounter_nr"/>');
	$smarty->assign('sMotherWardNr', '<input type="hidden" id="parent_ward_nr" name="parent_ward_nr"/>');
	$smarty->assign('sMotherRoomNr', '<input type="hidden" id="parent_room_nr" name="parent_room_nr"/>');
	$smarty->assign('sMotherDeptNr', '<input type="hidden" id="parent_dept_nr" name="parent_dept_nr"/>');
	$smarty->assign('sMotherSelect', '<input type="button" onclick="open_search_tray();" class="SegButton" value="Search Parent" name="search"/>');
	$smarty->assign('LDMotherNr', 'Mother\'s Case Nr:');
	#----end CHA

}  // end of if !isset($pid...

# Prepare shortcut links to other functions
$smarty->assign('isIPBM',$isIPBM);
$smarty->assign('HOMIS_ID',$homis_id);
$smarty->assign('sSearchLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_daten_such.php'.URL_APPEND.'">'.$LDPatientSearch.'</a>');
$smarty->assign('sArchiveLink','<img '.createComIcon($root_path,'varrow.gif','0').'> <a href="aufnahme_list.php'.URL_APPEND.'&newdata=1&from=entry">'.$LDArchive.'</a>');

$smarty->assign('sMainBlockIncludeFile','registration_admission/admit_input.tpl');

$smarty->display('common/mainframe.tpl');
?>
</body>