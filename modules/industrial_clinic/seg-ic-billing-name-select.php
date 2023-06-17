<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/industrial_clinic/ajax/agency_mgr.common.php");


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

$title="Agency:: Search";
$breakfile=$root_path."modules/industrial_clinic/seg-ic-agency-details.php".URL_APPEND."&userck=$userck";

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
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function init() {
	load_company_employees();
	shortcut.add('ESC', closeMe,
		{
			'type':'keydown',
			'propagate':false,
		}
	);

	lname.reload();
	setTimeout("$('search').focus()",100);
}

function closeMe() {
	window.parent.cClick();
}


function startSearch() {

	//var kword = $('search').value;
//	var company_id=window.parent.$('company_id').value;
//	sname.currentPage = 0;
//	sname.fetcherParams = [kword,company_id];
//	sname.reload();
//	load_company_employees();
//	lname.showRefresh();
	var d = document.getElementById('uname');
	var olddiv = document.getElementById('sname');
	d.removeChild(olddiv);
	var newdiv = document.createElement('div');
	var divIdName = 'sname';
	newdiv.setAttribute('id',divIdName);
	newdiv.innerHTML ="<div id='sname' style='margin-top:10px'></div>";
	d.appendChild(newdiv);
	load_company_employees();

}

function updateControls() {
	var s = $('search').value;
	$('search-btn').disabled = (s.length < 1);
}


function prepareSelect(pid,full_name) {

		window.parent.$('txtsearchName').value=full_name;
		window.parent.$('txtPid').value=pid;
	if (window.parent.pSearchClose) window.parent.pSearchClose();
	else if (window.parent.cClick) window.parent.cClick();
}


function load_company_employees()
{

//	alert($('search').value+' '+window.parent.$('agency_id').value);
	ListGen.create($('sname'),{
		id: 'sname',
		url: '<?=$root_path?>modules/industrial_clinic/seg-ic-billing-employee-list.php',
		params: {'agency_id':window.parent.$('agency_id').value, 'search_person':$('search').value ,'optionList':"select" },
		width: 550,
		height: 290,
		columnModel: [
			{
				name: 'patient_id',
				label: 'Patient ID',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					fontWeight: 'bold'
				}
			},
			{
				name: 'patient_name',
				label: 'Employee Name',
				width: 230,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#660000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_bdate',
				label: 'Birthdate',
				width: 110,
				sortable: false,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11'
				}
			},
			{
				name: 'patient_sex',
				label: 'Sex',
				width: 70,
				sortable: true,
				sorting: ListGen.SORTING.asc,
				styles: {
					color: '#000000',
					font: 'Tahoma',
					fontSize: '11',
					textAlign: 'center'
				}
			},
			{
				name: 'option',
				label: 'Option',
				width: 70,
				sortable: false
			}

		]
	});
}



// -->
</script>
<script type="text/javascript" src="<?=$root_path?>modules/industrial_clinic/js/seg-ic-billing.js?t=<?=time()?>"></script>
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
				<div id="uname" style="margin-top:10px">
					<div id="sname" style="margin-top:10px"></div>
				</div>
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
