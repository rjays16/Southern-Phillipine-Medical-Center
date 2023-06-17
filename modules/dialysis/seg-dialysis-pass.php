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

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'global_conf/areas_allow.php');

$src = $_GET['from'];
$append=URL_REDIRECT_APPEND.'&userck=';

switch($target)
{
	case "newrequest":
		$title="Dialysis::New Request";
		$userck="ck_dialysis_user";
		$allowedarea=array("_a_1_dialysiscreaterequest");
		$fileforward=$root_path."modules/dialysis/seg-dialysis-request-new.php".$append.$userck."&from=".$target;
	break;


	case "listpatients":
		$title="Dialysis::List of Patients";
		$userck="ck_dialysis_user";
		$allowedarea=array("_a_1_dialysiscreaterequest");
		$fileforward=$root_path."modules/dialysis/seg-dialysis-machine-list.php".$append.$userck."&from=".$target;

	break;	

	//Created - Jayson - OJT 2/10/2014
	//reason: dialysis must have list of patient
	//content: list of patients  based on date with their machine number, with edit and request (unified) button..
    
	case "dialysis_transmittal":
		$title="Billing - Transmittal";
		$userck="ck_dialysis_user";
		$allowedarea=array('_a_1_dialysistransmittal',"_a_1_billtransmittal");
		$fileforward=$root_path."modules/billing/billing-transmittal.php".$append.$userck."&from=".$target;
	break;

	//Created - Jayson - OJT 2/10/2014
	//reason: dialysis menu, must be connected to billing transmittal.
	
	case "reports":
		$title="Dialysis::Reports";
		$userck="ck_dialysis_user";
		$allowedarea=array("_a_1_dialysisreports");
		$fileforward=$root_path."modules/reports/report_launcher.php". URL_APPEND."&dept_nr=212";
	break;

	case "manageservice":
		$title="Dialysis::Services Manager";
		$userck="ck_dialysis_user";
		$allowedarea=array("_a_1_dialysisservicemanager");
		$fileforward=$root_path."modules/dialysis/seg-dialysis-service-manager.php".$append.$userck."&from=".$target;
	break;

	case "managetest":
		$title="Dialysis::Test Default Manager";
		$userck="ck_dialysis_user";
		$allowedarea=array("_a_1_dialysislistestmananger");
		$fileforward=$root_path."modules/dialysis/seg-dialysis-test-manager.php".$append.$userck."&from=".$target;
	break;

	# added by: syboy 01/12/2016 : meow
	// case "diasearchdoctor":
	// 	$title="Dialysis::Search Active and Inactive employee";
	// 	$userck="ck_dialysis_user";
	// 	$allowedarea=array("_a_1_searchempdependent");
	// 	$fileforward=$root_path."modules/personell_admin/personell_search.php?from=medocs&department=Dialysis";
	// break;
		
	default: 	{header("Location:".$root_path."language/".$lang."/lang_".$lang."_invalid-access-warning.php"); exit;};
}
$thisfile=basename(__FILE__)."?".$_SERVER['QUERY_STRING'];

if($target == 'transmittal'){
	$breakfile = "bill-main-menu.php".URL_APPEND;

}else{
$breakfile='seg-dialysis-menu.php'.URL_APPEND;
}


$lognote="Dialysis $title ok";

$userck='aufnahme_user';



setcookie($userck.$sid,'');
//Created: Jayson - OJT - 2/10/2014
//added to access transmittal, prevent Unauthorized Page Access

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