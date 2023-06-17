<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/industrial_clinic/ajax/agency_mgr.common.php');
//require_once $root_path.'include/care_api_classes/dialysis/class_dialysis_request.php';


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

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

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr=0;

//$phpfd = config date format in PHP date() specification

if (!$_GET['from'])
	$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
}

$thisfile='seg-ic-agency-manager.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
global $db;

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

# Collect javascript code
ob_start();
	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript">
function initialize()
{
	ListGen.create($('agency-list'),{
		id: 'agency',
		url: '<?=$root_path?>modules/industrial_clinic/ajax/ajax_list_agency.php',
		params: {'search_key':$('search').value},
		width: 700,
		height: 200,
		columnModel: [
			{
				name: 'agency_name',
				label: 'Agency Name',
				width: 150,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'agency_address',
				label: 'Address',
				width: 150,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'agency_no',
				label: 'Contact No.',
				width: 100,
				sortable: false
			},
			{
				name: 'options',
				label: 'Options',
				width: 200,
				sortable: false
			}
		]
	});
}

function searchAgency()
{
	$('agency-list').list.params={'search_key':$('search').value};
	$('agency-list').list.refresh();
}

function addAgency()
{
	overlib(
	OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-agency-new.php',
			500, 300, 'fGroupTray', 0, 'no'),
			WIDTH,500, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,'Add Agency',
			MIDX,0, MIDY,0,
			STATUS,'Add Agency');
	return false;
}

function openDetailsTray(nr)
{
	overlib(
	OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-agency-details.php?agency_id='+nr,
			800, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,800, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0  onclick="refreshList();">',
			CAPTIONPADDING,2, CAPTION,'Company Details',
			MIDX,0, MIDY,0,
			STATUS,'Company Details');
	return false;
}

function refreshList()
{
	$('agency-list').list.refresh();
	cClick();
}

function deleteAgency(nr)
{
	var rep = confirm("Delete this agency?");
	if(rep) {
		xajax_deleteAgency(nr);
	}
}

function outputResponse(rep)
{
	alert(rep)
	$('agency-list').list.refresh();
}

document.observe('dom:loaded', initialize);
</script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('agency_search', '<input type="text" class="segInput" id="search" name="search" style="width:50%"/>');

$smarty->assign('search_btn', '<button class="segButton" onclick="searchAgency();return false;"><img src="'.$root_path.'gui/img/common/default/zoom.png" style="cursor:pointer"/>Search</button>');
$smarty->assign('add_btn', '<button class="segButton" onclick="addAgency();return false;"><img src="'.$root_path.'gui/img/common/default/add.png" style="cursor:pointer"/>New Agency</button>');

$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="agency_form" name="agency_form">');
$smarty->assign('form_end','</form>');

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
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/agency_mgr.tpl');
$smarty->display('common/mainframe.tpl');

