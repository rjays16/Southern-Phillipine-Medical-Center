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
require_once($root_path.'global_conf/areas_allow.php');
$src = $_GET['from'];
$append=URL_REDIRECT_APPEND."&userck=$userck";
switch($target)
{
  case 'phsreg':
	    $title="PHS::Person registration";
    	$userck="ck_opd_user";
		#$allowedarea=array('_a_1_opdpatientmanage','_a_2_opdpatientregister','_a_1_erpatientmanage','_a_2_erpatientregister','_a_1_medocspatientmanage','_a_2_medocspatientregister');
		$allowedarea=array('_a_1_phspatientmanage','_a_2_phspatientregister');
	    $fileforward=$root_path."modules/registration_admission/patient_register.php".URL_APPEND."&ptype=phs&from=".$src;
		break;
  
  case "phssearchpatient": 	                                                                        
		$title="PHS::Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_2_opdpatientview');
		$allowedarea=array('_a_1_phspatientmanage','_a_2_phspatientview');
		$fileforward=$root_path."modules/registration_admission/patient_register_search.php".URL_APPEND."&ptype=phs&from=".$src;
		break;
		
  case "phssearchadv": 	                                                                        
		$title="PHS::Advance Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_2_opdpatientview');
		$allowedarea=array('_a_1_phspatientmanage','_a_2_phspatientview');
		$fileforward=$root_path."modules/registration_admission/patient_register_archive.php".URL_APPEND."&ptype=phs&from=".$src;
		break;		
		
  case "phssearchcompre": 	                                                                        
		$title="PHS::Comprehensive Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit','_a_2_opdpatientview');
		$allowedarea=array('_a_1_phspatientmanage','_a_2_phspatientview');
		$fileforward=$root_path."modules/registration_admission/patient_register_comprehensive_search.php".URL_APPEND."&ptype=phs&from=".$src;
		break;				
  
  case "phsconsultation": 	                                                                        
		$title="PHS::Search patient";
		$userck="ck_opd_user";
		#$allowedarea=array('_a_1_admissionwrite','_a_1_medocswrite','_a_1_opdpatientadmit','_a_1_erpatientadmit');
		$allowedarea=array('_a_1_phspatientadmit');
		$fileforward=$root_path."modules/registration_admission/aufnahme_daten_such.php".URL_APPEND."&ptype=phs&from=".$src;
		break;
		
  case "phsicdicpm": 	                                                                        
		$title="PHS::Medical Records";
		$userck='medocs_user';
		#$userck="ck_opd_user";
		$append=URL_REDIRECT_APPEND.'&from=pass'; 
		$allowedarea=array('_a_1_medocswrite','_a_1_medocsmedrecicd');
		#$fileforward=$root_path."modules/medocs/medocs_start.php".$append."&ptype=opd&from=".$src;
		$fileforward=$root_path."modules/medocs/medocs_pass.php".$append."&ptype=ipd&from=".$src;
		#exit();
		break;		
  
  case "opdmedcert":                                                                             
        $title="PHS::Medical Certificates";
        $userck='ck_opd_user';
        #$userck="ck_opd_user";
        $append=URL_REDIRECT_APPEND.'&from=pass'; 
        $allowedarea=array('_a_1_phspatientmanage','_a_1_medocswrite','_a_1_medocsmedrecicd','_a_1_admissionwrite');
        #$fileforward=$root_path."modules/medocs/medocs_start.php".$append."&ptype=opd&from=".$src;
        $fileforward=$root_path."modules/registration_admission/cert_med_search.php?sid=".$sid."&lang=".$lang."&userck=".$userck."&ptype=phs&target=phsmedcert&from=er";
        //$root_path."modules/medocs/medocs_pass.php".$append."&ptype=ipd&from=".$src;
        #exit();
        break;        
	
  case "reports":
		$title="PHS::Reports";
		$userck="ck_prod_db_user";
		#$allowedarea=array('_a_1_opdreports','_a_1_erreports','_a_1_medocsreports');
		$allowedarea=array('_a_1_phsreports');
		#$fileforward="seg-opd-reports.php".$append.$userck."&from=".$src;
		$fileforward=$root_path."modules/repgen/seg_report_generator.php".$append.$userck."&ptype=phs&from=".$src;
		break;
	// added by: syboy 07/11/2015
	case "reportgen": 
                    $title="Personnel Health Services::Hospital Reports";
                    $allowedarea=array('_a_1_phs_report_launcher');
                    $dept_nr = '133';
                    $fileforward=$root_path."modules/reports/report_launcher.php".$append."&ptype=phs&from=".$src."&dept_nr=".$dept_nr;
                    break;

    case "phssearchdoctor": 
        $title="Personnel Health Services::Search Active and Inactive employee";
        $allowedarea=array('_a_1_searchempdependent');
        $fileforward=$root_path."modules/personell_admin/personell_search.php?from=medocs&department=Personnel Health Station";
        break;

	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;}; 
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-phs-functions.php'.URL_APPEND;
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
