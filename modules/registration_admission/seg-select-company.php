<?php
/* added by art 05/11/2014 */

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/company_search.common.php");
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title="Transaction::Company Search";
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

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

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="init()"');

 # Collect javascript code
 ob_start();

?>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function init() {
	shortcut.add('ESC', closeMe,
		{
			'type':'keydown',
			'propagate':false,
		}
	);

	company.reload();
	setTimeout("$('search').focus()",100);
}

function closeMe() {
	window.parent.cClick();
}
<?php
	$varArray = array(
		'var_pid'=>'',
		'var_rid'=>'',
		'var_name'=>'',
		'var_addr'=>'',
		'var_clear'=>'',
		'noprefix'=>'0'
	);

	foreach ($varArray as $i=>$v) {
		$value = $_REQUEST[$i];
		if (!$value) $value = $v;
		if (!is_numeric($value)) $value = "'$value'";
		echo "var $i=$value;\n";
	}
?>

function startSearch() {
	var kword = $('search').value;
	pid=window.parent.$('pid').value;

	company.currentPage = 0;

	company.fetcherParams = [kword,pid];
	company.reload();
}

function updateControls() {
	var s = $('search').value;
	$('search-btn').disabled = (s.length < 1);
}


// -->
</script>
<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/js/agency_select.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$listgen->printJavascript($root_path);
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
	<FORM name="top_form" action="" method="post" onsubmit="return false">
	<table width="99%" cellspacing="1" cellpadding="1" style="margin:5px">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<table width="95%" border="0" cellpadding="2" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:2px; margin-left:10px">
						<tr>
							<td width="15%" nowrap="nowrap">
								Search keyword
							</td>
							<td valign="middle" width="*">
								<input id="search" class="jedInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="updateControls(); if (event.keyCode == 13) startSearch()" onclick="updateControls()"/>
								<input class="jedInput" id="search-btn" type="button" align="absmiddle" disabled="disabled" value="Search" onclick="startSearch()"/>
								<input class="jedInput" id="refresh-btn" type="button" align="absmiddle" value="Refresh!" onclick="startSearch()"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
<?php
	$listgen->setListSettings('MAX_ROWS','10');
	$company = &$listgen->createList('company',
		array('ID','Name', 'Location', ''),
		array(0,0,1,NULL),
		'populateCompany');
	$company->addMethod = 'addItem';
	$keyword="";
	$company->fetcherParams = array($keyword,$_GET["pid"]);
	$company->columnWidths = array("10%","40%","40%","%");
	print $company->getHTML();
?>
				</td>
			</tr>
		</tbody>
	</table>


	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">

	</FORM>
<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);

	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
