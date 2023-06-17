<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/industrial_clinic/ajax/transaction.common.php");

//LISTGEN YEHEY
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

//$db->debug=1;

$thisfile=basename(__FILE__);

$title="Transaction::Transaction Search";
$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND."&userck=$userck";

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

	transaction.reload();
	setTimeout("$('search').focus()",100);
}

function closeMe() {
	window.parent.cClick();
}


function startSearch() {
	var kword = $('search').value;
//	pid=window.parent.$('pid').value;

	transaction.currentPage = 0;

	transaction.fetcherParams = [kword];
	transaction.reload();
}

function updateControls() {
	var s = $('search').value;
	$('search-btn').disabled = (s.length < 1);
}


function prepareSelect(case_no) {
		//alert(c_id+c_name+c_eid+c_pos+c_jobStatus);
//	window.parent.$('agency_organization_id').value=c_id;
//	window.parent.$('agency_organization').value=c_name;
//	window.parent.$('position').value=c_pos;
//	window.parent.$('id_no').value=c_eid;
//	for(var i=1;i<=5;i++){
//		var v_tmp="statusR"+i;
//		if(window.parent.$(v_tmp).value==c_jobStatus){
//				window.parent.$(v_tmp).checked=true;
//				window.parent.setStatus(window.parent.$(v_tmp).value,window.parent.$(v_tmp));

//				break;
//		}

//	}

//	if (window.parent.pSearchClose) window.parent.pSearchClose();
//	else if (window.parent.cClick) window.parent.cClick();
}

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>modules/industrial_clinic/js/transaction-search.js?t=<?=time()?>"></script>
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
	$transaction = &$listgen->createList('transaction',
		array('Case No','Patient ID','Name',''),
		array(0,1,NULL),
		'populateTransaction');
	$transaction->addMethod = 'addItem';
	$keyword="";
	//var kword = $('search').value;

//	company.currentPage = 0;
//	company.fetcherParams = [kword,pid];
//	echo $_GET["pid"];
	$transaction->fetcherParams = array($keyword);
	$transaction->columnWidths = array("20%","20%","50%","*");
	print $transaction->getHTML();
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
