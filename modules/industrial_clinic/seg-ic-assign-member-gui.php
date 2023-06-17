<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/agency_mgr.common.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_agency_mgr.php');

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

$thisfile='seg-ic-assign-member-gui.php';

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

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
# Collect javascript code
ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript">

function outputResponse(rep)
{
	window.parent.refreshlist(rep);
}

function assignEmployee()
{
	var rep = confirm("Assign patient to this company?");
	if(rep) {
		var data = [];
		data['pid'] = $('pid').value;
		data['company_id'] = $('company_id').value;
		data['employee_id'] = $('employee_id').value;
		data['position'] = $('position').value;
		data['job_status'] = $('job_status').value;
		xajax_assignAgencyMember(data);
	}
}

function updateEmployeeData()
{
	var rep = confirm("Update employee data?");
	if(rep) {
		var data = [];
		data['pid'] = $('pid').value;
		data['company_id'] = $('company_id').value;
		data['employee_id'] = $('employee_id').value;
		data['position'] = $('position').value;
		data['job_status'] = $('job_status').value;
		xajax_updateEmployeeData(data);
	}
}

//document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
$pid = $_POST['pid'] ? $_POST['pid']:$_GET['pid'];
$company_id = $_POST['company_id'] ? $_POST['company_id']:$_GET['company_id'];
$mode = ($_GET['mode']?$_GET['mode']:$_POST['mode']);
$amgr_obj = new SegAgencyManager();
$data = $amgr_obj->getEmployeeDetails($pid, $company_id);

$smarty->assign('pid', '<input type="text" class="segInput" id="pid" name="pid" style="width:100%" value="'.$pid.'" readonly="readonly"/>');
$smarty->assign('company_id', '<input type="text" class="segInput" id="company_id" name="company_id" style="width:100%" value="'.$company_id.'" readonly="readonly"/>');
$smarty->assign('employee_id', '<input type="text" class="segInput" id="employee_id" name="employee_id" style="width:100%" value="'.$data['employee_id'].'"/>');
$smarty->assign('position', '<input type="text" class="segInput" id="position" name="position" style="width:100%" value="'.$data['position'].'"/>');

$opt_value = array('regular','contractual','job_order','consultant','student','other');
$opt_text = array('Regular', 'Contractual', 'Job Order', 'Consultant','Student', 'Other');
$options = "<option value='0'>-Select a job status-</option>";
for($i=0;$i<count($opt_value);$i++)
{
	if($opt_value[$i]==$data['job_status']) {
		$options.="<option value='".$opt_value[$i]."' selected='selected'>".$opt_text[$i]."</option>";
	} else {
		$options.="<option value='".$opt_value[$i]."'>".$opt_text[$i]."</option>";
	}
}
$smarty->assign('job_status', '<select class="segInput" id="job_status" name="job_status" style="width:100%">'.$options.'</select>');

$smarty->assign('save_btn', '<button class="segButton" '.(strtolower($mode)=='update'? 'onclick="updateEmployeeData(); return false;"':'onclick="assignEmployee(); return false;"').'><img src="../../gui/img/common/default/note_go.png"/>Assign</button>');
$smarty->assign('close_btn','<button class="segButton" onclick="parent.cClick(); return false;"><img src="../../gui/img/common/default/cancel.png"/>Close</button>');

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="agency_form" name="agency_form">');
$smarty->assign('form_end','</form>');
$smarty->assign('submitted', '<input type="hidden" value="TRUE" name="submitted" />');
$smarty->assign('mode', '<input type="hidden" name="mode" id="mode" value= "'.$mode.'">');

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
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/assign_employee_gui.tpl');
$smarty->display('common/mainframe.tpl');

