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
$append=URL_REDIRECT_APPEND."&userck=$userck";
switch($target)
{
	case 'er_reg':
			$title="ER::Person registration";
			$userck="ck_opd_user";
		#$allowedarea=array('_a_1_opdpatientmanage','_a_2_opdpatientregister','_a_1_erpatientmanage','_a_2_erpatientregister','_a_1_medocspatientmanage','_a_2_medocspatientregister');
		$allowedarea=array('_a_1_erpatientmanage','_a_2_erpatientregister');
			$fileforward=$root_path."modules/registration_admission/patient_register.php".URL_APPEND."&ptype=er&from=".$src;
		break;
	
	case "er_searchpatient": 	                                                                        
		$title="ER::Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_2_opdpatientview');
		$allowedarea=array('_a_1_erpatientmanage','_a_2_erpatientview');
		$fileforward=$root_path."modules/registration_admission/patient_register_search.php".URL_APPEND."&ptype=er&from=".$src;
		break;
		
	case "er_searchadv": 	                                                                        
		$title="ER::Advance Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_2_opdpatientview');
		$allowedarea=array('_a_1_erpatientmanage','_a_2_erpatientview');
		$fileforward=$root_path."modules/registration_admission/patient_register_archive.php".URL_APPEND."&ptype=er&from=".$src;
		break;		
		
	case "er_searchcompre": 	                                                                        
		$title="ER::Comprehensive Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_2_opdpatientview');
		$allowedarea=array('_a_1_compsearch');
		$fileforward=$root_path."modules/registration_admission/patient_register_comprehensive_search.php".URL_APPEND."&ptype=er&from=".$src;
		break;				
	
	case "er_consultation": 	                                                                        
		$title="ER::Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit');
		// $allowedarea=array('_a_1_erpatientadmit','_a_2_erpatientview');
		// $fileforward=$root_path."modules/registration_admission/aufnahme_daten_such.php".URL_APPEND."&ptype=er&from=".$src;
		
		$allowedarea=array_merge(getChildPermissions($erPermissions,"_a_1_manageerpatientencounter"),array("_a_1_erpatientadmit","System_Admin","_a_0_all"));
		$fileforward=$root_path."modules/registration_admission/aufnahme_daten_such.php".URL_APPEND."&ptype=er&from=".$src;
		break;
		
	case "er_icdicpm": 	                                                                        
		$title="ER::Medical Records";
		$userck='medocs_user';
		#$userck="ck_opd_user";
		$append=URL_REDIRECT_APPEND.'&from=pass'; 
		$allowedarea=array('_a_1_medocswrite','_a_1_medocsmedrecicd');
		#$fileforward=$root_path."modules/medocs/medocs_start.php".$append."&ptype=er&from=".$src;
		$fileforward=$root_path."modules/medocs/medocs_pass.php".$append."&target=medocs_searchpatientrec&ptype=ipd&from=".$src;
		#exit();
		break;		
				
	case "er_medcert":                                                                             
				$title="ER::Medical Certificates";
				$userck='ck_opd_user';
				#$userck="ck_opd_user";
				$append=URL_REDIRECT_APPEND.'&from=pass'; 
				$allowedarea=array('_a_1_erpatientmanage','_a_1_medocswrite','_a_1_medocsmedrecicd','_a_1_admissionwrite');
				#$fileforward=$root_path."modules/medocs/medocs_start.php".$append."&ptype=er&from=".$src;
				$fileforward=$root_path."modules/registration_admission/cert_med_search.php?sid=".$sid."&lang=".$lang."&userck=".$userck."&ptype=er&target=er_med_cert&from=er";
				//$root_path."modules/medocs/medocs_pass.php".$append."&ptype=ipd&from=".$src;
				#exit();
				break;
	
	case "reports":
		$title="ER::Reports";
		$userck="ck_prod_db_user";
		#$allowedarea=array('_a_1_opdreports','_a_1_erreports','_a_1_medocsreports');
		$allowedarea=array('_a_1_erreports');
		#$fileforward="seg-er-reports.php".$append.$userck."&ptype=er&from=".$src;
		$fileforward=$root_path."modules/repgen/seg_report_generator.php".$append.$userck."&ptype=er&from=".$src;
		break;
	# added by: syboy 01/12/2016 : meow
	case "er_searchdoctor":
		$title="ER::Search Active and Inactive employee";
		$userck="ck_prod_db_user";
		$allowedarea=array('_a_1_searchempdependent');
		$fileforward=$root_path."modules/personell_admin/personell_search.php?from=medocs&department=Emergency Room";
		break;

	case "reportgen":
		$title = "ER::Hospital Reports";
		$allowedarea=array('_a_1_er_report_launcher');
	    $dept_nr = '149';
	    $fileforward=$root_path."modules/reports/report_launcher.php".$append."&ptype=medocs&from=".$src."&dept_nr=".$dept_nr;
	    break;

	case "er_update_vital_sign":
	
		$userck="ck_prod_db_user";
		$allowedarea=array_merge(getAllowedPermissions($erPermissions,"_a_4_erupdatevitalsigns".$enc_stat."encounter"),array('_a_1_erpatientadmit'));
		$fileforward=$root_path."index.php".URL_APPEND."&r=admission/vital&encounter_nr=$encounter_nr&pid=$pid&from=er";
		break;	

	case "er_update_outside_med":
		
		$userck="ck_prod_db_user";
		$allowedarea=array_merge(getAllowedPermissions($erPermissions,"_a_4_erupdateoutsidemeds".$enc_stat."encounter"),array('_a_1_erpatientadmit'));
		$fileforward=$root_path."index.php".URL_APPEND."&r=pharmacy/package&encounter_nr=$encounter_nr&req_src=".$ptype."&from=er";
		break;

	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;}; 
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-er-functions.php'.URL_APPEND;
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
