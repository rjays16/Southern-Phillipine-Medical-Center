<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','stdpass.php');
define('NO_2LEVEL_CHK',1);
#EDITED BY VAS 11-09-2008
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_func_permission.php');
require_once($root_path.'global_conf/areas_allow.php');
$src = $_GET['from'];
#$superuser = array('_a_0_all','System_Admin'); 
#$allowedarea=&$allow_area['admit'];

$append=URL_REDIRECT_APPEND."&userck=$userck";
switch($target)
{
	case 'ipbmreg':
			$title=strtoupper($src)."::Person registration";
			$userck="ck_".$src."_user";
			$allowedarea=array('_a_1_ipbmpatientmanage','_a_2_ipbmpatientregister');
			$fileforward=$root_path."modules/registration_admission/patient_register.php".URL_APPEND."&ptype=".$src."&isipbm=1&from=".$src;
		break;
	
	case "ipbmsearchpatient": 	                                                                        
		$title=strtoupper($src)."::Search patient";
		$userck="ck_".$src."_user";
		$allowedarea=array('_a_1_ipbmpatientmanage','_a_2_ipbmpatientview','_a_2_ipbmpatientupdate','_a_1_ipbmadmission','_a_2_ipbmadmitonly','_a_2_ipbmviewadmission','_a_2_ipbmupdateadmission','_a_2_ipbmcanceladmission','_a_2_ipbmviewipdcoversheet','_a_2_ipbmipdclinicalcharges','_a_1_ipbmconsultation','_a_2_ipbmconsultonly','_a_2_ipbmviewconsultation','_a_2_ipbmupdateconsultation','_a_2_ipbmcancelconsultation','_a_2_ipbmviewopdcoversheet','_a_2_ipbmopdclinicalcharges','_a_1_ipbmadvancesearch','_a_2_ipbmcanceldeath','_a_2_ipbmcanceldischarge','_a_2_ipbmmedcert','_a_2_ipbmmedabs','_a_2_ipbmconcert','_a_1_ipbmclinicalcharges','_a_1_ipbmviewlabradresults');
		$fileforward=$root_path."modules/registration_admission/patient_register_search.php".URL_APPEND."&isipbm=1&ptype=ipbm&from=".$src;
		break;

	case "ipbmsearchadv": 	                                                                        
		$title=strtoupper($src)."::Advance Search patient";
		$userck="ck_".$src."_user";
		$allowedarea=array('_a_1_ipbmadvancesearch');
		$fileforward=$root_path."modules/registration_admission/patient_register_archive.php".URL_APPEND."&ptype=".$src."&isipbm=1&from=".$src;
		break;		
		
	case "ipbmsearchcompre": 	                                                                        
		$title=strtoupper($src)."::Comprehensive Search patient";
		$userck="ck_".$src."_user";
		$allowedarea=array('_a_1_compsearch','_a_1_ipbmadvancesearch');
		$fileforward=$root_path."modules/registration_admission/patient_register_comprehensive_search.php".URL_APPEND."&ptype=".$src."&isipbm=1&from=".$src;
		break;				
	
	case "ipbmconsultation": 	                                                                        
		$title=strtoupper($src)."::Search patient";
		$userck="ck_".$src."_user";
		$allowedarea=array_merge(getChildPermissions($ipbmPermissions,"_a_1_manageipbmpatientencounter"),array("_a_1_ipbmconsultation","_a_2_ipbmviewconsultation","_a_2_ipbmupdateconsultation","_a_2_ipbmcancelconsultation","_a_2_ipbmviewopdcoversheet","_a_1_ipbmclinicalcharges","_a_1_ipbmviewlabradresults","System_Admin","_a_0_all"));
		$fileforward=$root_path."modules/registration_admission/aufnahme_daten_such.php".URL_APPEND."&ptype=opd&from=".$src."&target=opd&isipbm=1";
		break;

	case "ipbmadmission": 	                                                                        
		$title=strtoupper($src)."::Search patient";
		$userck="ck_".$src."_user";
		$allowedarea=array_merge(getChildPermissions($ipbmPermissions,"_a_1_manageipbmpatientencounter"),array("_a_1_ipbmadmission","_a_2_ipbmviewadmission","_a_2_ipbmupdateadmission","_a_2_ipbmcanceladmission","_a_2_ipbmviewipdcoversheet","_a_1_ipbmclinicalcharges","_a_1_ipbmviewlabradresults","System_Admin","_a_0_all"));
		$fileforward=$root_path."modules/registration_admission/aufnahme_daten_such.php".URL_APPEND."&ptype=ipd&from=".$src."&target=ipd&isipbm=1";
		break;	
		
	case "ipbmicdicpm": 	                                                                        
		$title=strtoupper($src)."::Medical Records";
		$userck='medocs_user';
		#$userck="ck_ipbm_user";
		$append=URL_REDIRECT_APPEND.'&from=pass'; 
		$allowedarea=array('_a_1_ipbmmedicalrecords','_a_2_ipbmcanAccessICDICPM');
		$fileforward=$root_path."modules/medocs/medocs_pass.php".$append."&target=medocs_searchpatientrec&ptype=".$src."&isipbm=1&from=".$src;
		break;		   
	
	case "reports":
		$title=strtoupper($src)."::Reports";
		$userck="ck_".$src."_user";
		$allowedarea=array('_a_1_ipbmreports');
		$fileforward=$root_path."modules/repgen/seg_report_generator.php".$append.$userck."&ptype=".$src."&from=".$src;
		break;

	case "reportgen":
		$title = strtoupper($src)."::Hospital Reports";



		$allowedarea=array('_a_1_ipbm_report_launcher','_a_0_all', 'System_Admin','_a_2_PSY_OPD_daily_trans','_a_2_sPSY_Admission_Logbook_For_Docs','_a_2_opd_summary','_a_2_report_discharges','_a_2_causes_confinement','_a_2_report_referral','_a_2_report_icd_encoded','_a_2_death','_a_2_top_10','_a_2_ave_daily_census_admitted','PSY_Research_Query','_a_2_psy_opd_rendered','_a_2_smoking','_a_2_leading_discharges','_a_2_Discharges_7days_Admission','_a_2_discharge_treatment','_a_2_leading_morbidity_oveall','_a_2_discharges_served','_a_2_PSY_Unregistered_Death_Certificate','_a_2_icd_encoded_stat','_a_2_ipd_demog','PSY_leading_mortality','_a_2_summary_patient','_a_2_causes_mortality','_a_2_notifiable','_a_2_PSY_bor');


 
	    #for medical records
	    $dept_nr = '182';
	    $fileforward=$root_path."modules/reports/report_launcher.php".$append."&ptype=".$src."&from=".$src."&dept_nr=".$dept_nr;
	    break;

	case "ipbmsearchdoctor":
		$title = strtoupper($src)."::Search Active and Inactive employee";
		$allowedarea=array('_a_1_searchempdependent');
	    $fileforward=$root_path."modules/personell_admin/personell_search.php?from=".$src."&department=".strtoupper($src);
	    break;
	
	case "pharmacy":
		$title = strtoupper($src)."::Pharmacy";
		$allowedarea=array('_a_1_ipbmpharmacy');
	    $fileforward=$root_path."modules/pharmacy/seg-pharma-order-functions.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmadmission&from=ipbm";
	break;
	case "cashier":
		$title = strtoupper($src)."::Cashier";
		$allowedarea=array('_a_1_ipbmcashier');
	    $fileforward=$root_path."modules/cashier/seg-cashier-functions.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmadmission&from=ipbm";
	break;
	case "billing":
		$title = strtoupper($src)."::Billing";
		$allowedarea=array('_a_1_ipbmbilling');
	    $fileforward=$root_path."modules/billing/bill-main-menu.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmadmission&from=ipbm";
	break;
	case "socialservice":
		$title = strtoupper($src)."::Social Service";
		$allowedarea=array('_a_1_ipbmsocialservice');
	    $fileforward=$root_path."modules/social_service/social_service_main.php?sid=<?=$sid?>&lang=<?=$lang?>&userck=<?=$userck?>&target=ipbmadmission&from=ipbm";
	break;

	case "ipbm_update_vital_sign":

		$userck="ck_prod_db_user";
		if ($ptype == 'ipd')
			$typePermission = "_a_1_ipbmadmission";
		else
			$typePermission = "_a_1_ipbmconsultation";

		$allowedarea = getAllowedPermissions($ipbmPermissions,"_a_2_accessipbm".$ptype."encounter");
		$accessipbmencounter = validarea($HTTP_SESSION_VARS['sess_permission']);
		$pEncVital = $accessipbmencounter ? getAllowedPermissions($ipbmPermissions,"_a_4_ipbmupdatevitalsigns".$enc_stat."encounter") : array();
		
		$allowedarea=array_merge($pEncVital,array($typePermission));
		
		$fileforward=$root_path."index.php".URL_APPEND."&r=admission/vital&encounter_nr=$encounter_nr&pid=$pid&ptype=$ptype&from=ipbm";
		break;	

	case "ipbm_update_outside_med":
		
		$userck="ck_prod_db_user";
		if ($ptype == 'ipd') {
			$typePermission = "_a_1_ipbmadmission";
			$allowedarea = getAllowedPermissions(${'ipbmPermissions'},"_a_2_accessipbmipdencounter");
		}else{
			$typePermission = "_a_1_ipbmconsultation";
			$allowedarea = getAllowedPermissions(${'ipbmPermissions'},"_a_2_accessipbmopdencounter");
		}
		$accessipbmencounter = validarea($HTTP_SESSION_VARS['sess_permission']);
		$pEncOutside = $accessipbmencounter ? getAllowedPermissions($ipbmPermissions,"_a_4_ipbmupdateoutsidemeds".$enc_stat."encounter") : array();
		
		$allowedarea=array_merge($pEncOutside,array($typePermission));
		$fileforward=$root_path."index.php".URL_APPEND."&r=pharmacy/package&encounter_nr=$encounter_nr&ptype=$ptype&from=ipbm";
		break;

	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;}; 
}

#$allowedarea = array_merge($allowedarea, $superuser);
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-ipbm-functions.php'.URL_APPEND;
$lognote="$title ok";

// reset all 2nd level lock cookies
$userck='aufnahme_user';
setcookie($userck.$sid,'');
require($root_path.'include/inc_2level_reset.php'); 
setcookie(ck_2level_sid.$sid,'');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') include($root_path.'include/inc_passcheck.php');

$errbuf="$title";
$minimal=1;
require($root_path.'include/inc_passcheck_head.php');

?>

<BODY  <?php if (!$nofocus) echo 'onLoad="document.passwindow.userid.focus()"'; echo  ' bgcolor='.$cfg['body_bgcolor']; 
 if (!$cfg['dhtml']){ echo ' link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; } 
?>>

<p>
<P>
<img src="../../gui/img/common/default/lampboard.gif" border=0 align="middle">
<FONT  COLOR="<?php echo $cfg[top_txtcolor] ?>"  SIZE=5  FACE="verdana"> <b><?php echo "$title" ?></b></font>
<p>
<table width=100% border=0 cellpadding="0" cellspacing="0"> 

<?php require($root_path.'include/inc_passcheck_mask.php') ?>  

<p>
<!-- <img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDIntro2 $LDPharmacy $title " ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$LDWhat2Do $LDPharmacy $title " ?>?</a><br>
 -->
<p>
</TABLE>

<?php
require($root_path.'include/inc_load_copyrite.php');
?>

</BODY>
</HTML>
