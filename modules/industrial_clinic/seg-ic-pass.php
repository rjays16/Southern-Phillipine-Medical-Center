<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require_once('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
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

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/areas_allow.php');
echo $allowedarea;
$src = $_GET['from'];
$append=URL_REDIRECT_APPEND.'&userck=';
switch($target)
{
	//case "functions":
//		$title="Cashier";
//		$userck="ck_cashier_user";
//		$allowedarea=array("_a_1_cashiermanage","_a_2_cashiercreate");
//		$fileforward=$root_path."modules/cashier/seg-cashier-functions.php".$append.$userck."&from=".$src;
//	break;

	#added by VAN 08-23-2010
	case "ic_reg":
		$title="Industrial Clinic::Register Patient";
		$userck="aufnahme_user";
		$allowedarea=array('_a_1_RegPatient');
		$fileforward=$root_path."modules/registration_admission/patient_register.php".$append.$userck."&ptype=ic&from=".$src;
	break;

	case "ic_searchpatient":
		$title="Industrial Clinic::Search Patient";
		$userck="aufnahme_user";
		$allowedarea=array('_a_1_Search');
		$fileforward=$root_path."modules/registration_admission/patient_register_search.php".$append.$userck."&ptype=ic&from=".$src;
	break;
	#-------------

	case "ic_transaction":
		$title="Industrial Clinic::Transaction";
		$userck="ck_ic_transaction_user";
		$allowedarea=array("_a_1_ictransactionmanage","_a_2__ictransactioncreate");
		$fileforward=$root_path."modules/industrial_clinic/seg-ic-transaction-form.php".$append.$userck."&from=".$src;
	break;

	case "ic_consultation_main":
		$title="Industrial Clinic::Transaction";
		$userck="ck_ic_transaction_user";
		$allowedarea=array('_a_1_phspatientadmit');

//		$fileforward=$root_path."modules/industrial_clinic/seg-ic-transaction-select.php".$append.$userck."&from=".$src;
		$fileforward=$root_path."modules/industrial_clinic/seg-ic-transaction-form.php".$append.$userck."&from=".$src."&pid=2053493";
		break;

	case "ic_transactions_hist":
		$title="Industrial Clinic::Transactions List";
		$userck="ck_ic_transaction_user";
		$allowedarea=array('_a_1_ClinicalTran');

		$fileforward=$root_path."modules/industrial_clinic/seg-ic-transactions-hist.php".$append.$userck."&from=".$src;
		break;

	case "ic_billing":
		$title="Industrial Clinic::Billing";
		$userck="ck_ic_transaction_user";
		$allowedarea=array('_a_1_ClinicBillGen');

		$fileforward=$root_path."modules/industrial_clinic/seg-ic-billing-main.php".$append.$userck."&from=".$src;
		break;

	case "ic_transaction_daily_report":
		$title="Industrial Clinic::Transactions Daily Report";
		$userck="ck_ic_transaction_user";
		$allowedarea=array('_a_1_ictransmanage');

		$fileforward=$root_path."modules/industrial_clinic/seg-ic-transaction-daily-report-form.php".$append.$userck."&from=".$src;
		break;

 	case "ic_consolidated_report_form":
		$title="Industrial Clinic::All Requested Services Print-out";
		$userck="ck_ic_transaction_user";
		$allowedarea=array('_a_1_ClinicBills');

		$fileforward=$root_path."modules/industrial_clinic/seg-ic-agency-print-out-form.php".$append.$userck."&from=".$src;
		break;


	case "ic_manager": 
	    $title="  Industrial Clinic :: Agency Manager ";
	    $userck="ck_ic_transaction_user";
	    $allowedarea=array('_a_1_AgencyManager');
				 
	  	$fileforward=$root_path."modules/industrial_clinic/seg-ic-agency-manager.php".$append.$userck."&from=".$src;
	    break; 

	case "manual": 
	   #$title="  Industrial Clinic :: Agency Manager ";
	    #$userck="ck_ic_transaction_user";
	   # $allowedarea=array('_a_1_AgencyManager');
				 
	  	$fileforward=$root_path."modules/industrial_clinic/pdf/INDUSTRIAL_CLINIC.pdf".$append.$userck."&from=".$src;
	    break; 



	/* added by art 04/26/2014 */
	case "reportgen": 
	    $title="Industrial Clinic::Hospital Reports";
	    $allowedarea=array('_a_1_ReportLauncher');
	    #$fileforward=$root_path."cakeapp/repgen";
	    #for medical records
	    $dept_nr = '138';
	    $fileforward=$root_path."modules/reports/report_launcher.php".$append."&ptype=ic&from=".$src."&dept_nr=".$dept_nr;
	    break; 
	/* end art */
	
	// case "ic_searchdoctor": 
	//     $title="Industrial Clinic::Search Active and Inactive employee";
	//     $allowedarea=array('_a_1_searchempdependent');
	//     $fileforward=$root_path."modules/personell_admin/personell_search.php?from=medocs&department=Health Service and Specialty Clinic";
	//     break; 



	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;};
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];
$breakfile='seg-cashier-functions.php'.URL_APPEND;
$lognote="Cashier $title ok";

// reset all 2nd level lock cookies
require($root_path.'include/inc_2level_reset.php');
setcookie('ck_2level_sid'.$sid,'',0,'/');

require($root_path.'include/inc_passcheck_internchk.php');
if ($pass=='check') include($root_path.'include/inc_passcheck.php');

$errbuf="Cashier";
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
<!-- <img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$title " ?></a><br>
<img <?php echo createComIcon($root_path,'varrow.gif','0') ?>> <a href="<?php echo $root_path; ?>main/ucons.php<?php echo URL_APPEND; ?>"><?php echo "$title " ?>?</a><br>
 -->
<p>
</TABLE>

<?php
require($root_path.'include/inc_load_copyrite.php');
?>

</BODY>
</HTML>