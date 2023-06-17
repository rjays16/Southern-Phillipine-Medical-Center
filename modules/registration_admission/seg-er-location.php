<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/laboratory/ajax/lab-request-new.common.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

define('LANG_FILE','lab.php');
$local_user='ck_lab_user';

define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
$smarty->assign('sToolbarTitle',"$title");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"$title");

if($_GET['encounter_nr']) {
	$enc_nr = $_GET['encounter_nr'];
}
else{
	$enc_nr = '';
}

$enc_info = $enc_obj->getEncounterInfo($enc_nr);

$list_location = $enc_obj->getERLocation();
$list_lobby = $enc_obj->getERLobby();

$data_location = $_POST['er_location'];
$data_lobby = $_POST['er_location_lobby'];

$data = "er_location=$data_location, er_location_lobby=$data_lobby";

if($enc_obj->_setCurrentAssignment($enc_nr, $data)){
	$smarty->assign("sWarning", "Patient's ER Location was successfully updated!");
	echo "<script type='text/javascript'>setTimeout(function(){ window.location = '$root_path/modules/registration_admission/seg-er-location.php?encounter_nr=$enc_nr';}, 2000);</script>";
}

ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" language="javascript">
</script>

<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');
?>

<?php
$location = '<select class="segInput" name="er_location" id="er_location">';
$location .= '<option value="0">-Select Location Area-</option>';
while ($result = $list_location->FetchRow()) {
	$location .= '<option value="'. $result['location_id'] .'" ';
	if($enc_info['er_location'] == $result['location_id'])
		$location .= 'selected';
	$location .= '>'. $result['area_location'] .'</option>';
}
$location .= '</select>';

$lobby = '<select class="segInput" name="er_location_lobby" id="er_location_lobby">';
$lobby .= '<option value="0">-Select Lobby Area-</option>';
while ($result = $list_lobby->FetchRow()) {
	$lobby .= '<option value="'. $result['lobby_id'] .'" ';
	if($enc_info['er_location_lobby'] == $result['lobby_id'])
		$lobby .= 'selected';
	$lobby .= '>'. $result['lobby_name'] .'</option>';
}
$lobby .= '</select>';

$smarty->assign("sFormStart", "<form method='post'>");
$smarty->assign("sERLocation", $location);
$smarty->assign("sERLobby", $lobby);
$smarty->assign("sContinueButton", "<input type=image name=submit src='$root_path/images/btn_save.gif' value='Save'>");
$smarty->assign("sFormEnd", "</form>");

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs',$sTemp);


# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','registration_admission/seg-er-location.tpl');
$smarty->display('common/mainframe.tpl');
?>