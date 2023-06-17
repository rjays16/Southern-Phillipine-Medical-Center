<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/agency_mgr.common.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_dialysis_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

if (!$_GET['from'])
	$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
}

$thisfile='seg-ic-agency-new.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Industrial Clinic :: Agency Manager";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

#save data here
/*if(isset($_POST['submitted'])){
	$data = array(
		'name'=>$_POST['agency_name'],
		'address'=>$_POST['agency_address'],
		'contact_number'=>$_POST['agency_contact'],
		'short_name'=>$_POST['agency_sname'],
		'president'=>$_POST['agency_president'],
		'hr_manager'=>$_POST['agency_hr'],
		'account_no'=>$_POST['agency_account']
	);
	if()
}*/

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
# Collect javascript code
ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">
function validate() {

	if($('agency_name').value=="")
	{
		alert("Please provide the agency name.");
		$('agency_name').focus();
		return false;
	}
	else {
		var data = [];
		data['name'] = $('agency_name').value;
		data['address'] = $('agency_address').value;
		data['contact_no'] = $('agency_contact').value;
		data['short_id'] = $('agency_sname').value;
		data['president'] = $('agency_president').value;
		data['hr_manager'] = $('agency_hr').value;
		data['hosp_acct_no'] = $('agency_account').value;
		xajax_saveAgency(data);
		return false;
	}
}

function outputResponse(rep)
{
	alert(rep)
	window.parent.$('agency-list').list.refresh();
	window.parent.cClick();
}

//document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('agency_name', '<input type="text" class="segInput" id="agency_name" name="agency_name" style="width:100%"/>');
$smarty->assign('agency_address', '<textarea class="segInput" id="agency_address" name="agency_address" style="width:100%;overflow-y:scroll;"></textarea>');
$smarty->assign('agency_contactnum', '<input type="text" class="segInput" id="agency_contact" name="agency_contact" style="width:100%"/>');
$smarty->assign('agency_sname', '<input type="text" class="segInput" id="agency_sname" name="agency_sname" style="width:100%"/>');
$smarty->assign('agency_president', '<input type="text" class="segInput" id="agency_president" name="agency_president" style="width:100%"/>');
$smarty->assign('agency_hr', '<input type="text" class="segInput" id="agency_hr" name="agency_hr" style="width:100%"/>');
$smarty->assign('agency_accountnum', '<input type="text" class="segInput" id="agency_account" name="agency_account" style="width:100%"/>');

$smarty->assign('save_btn', '<button class="segButton" onclick="if (confirm(\'Save this new agency?\')) return validate(); return false;"><img src="../../gui/img/common/default/note_go.png"/>Save</button>');
$smarty->assign('close_btn','<button class="segButton" onclick="parent.cClick(); return false;"><img src="../../gui/img/common/default/cancel.png"/>Close</button>');

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="agency_form" name="agency_form">');
$smarty->assign('form_end','</form>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');

ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/agency_add.tpl');
$smarty->display('common/mainframe.tpl');

