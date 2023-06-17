<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/order.common.php");
$xajax->printJavascript($root_path.'classes/xajax_0.5');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','prompt.php');
$local_user='ck_prod_order_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";

if(empty($pday)) $pday=date('j');
if(empty($pmonth)) $pmonth=date('n');
if(empty($pyear)) $pyear=date('Y');
$abtarr=array();
$abtname=array();
$datum=date('d.m.Y');

# Load the medical department list
require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
$dept=$prod_obj->getAllPharmaAreas();

$title=$LDSelectDept;
# Set forward file
/*
switch($target){
	case 'catalog': $fileforward=$root_path."modules/products/products-bestellkatalog-edit.php".URL_APPEND."&cat=$cat";
							break;
	default : $fileforward=$root_path."modules/products/products-bestellung.php".URL_APPEND."&cat=$cat";
}
*/
// var_dump($_SESSION['sess_pharma_area']);exit();
if ($_GET["set"]=='1') $target="menu";
$fileforward = $root_path.'modules/pharmacy/apotheke-pass.php'. URL_APPEND."&userck=$userck".'&target='.$target;

/*added by MARK April 19, 2017*/
if ($_GET['myarea']!="") {
	header("Location:$fileforward&area=".$_GET['myarea']);
	exit;
}

if ($_SESSION['sess_pharma_area'] && $_GET['set']!='1') {
	header("Location:$fileforward&area=".$_SESSION['sess_pharma_area']);
	exit;
}
/*end by MARK April 19, 2017*/

require_once($root_path.'include/care_api_classes/class_inventory.php');
$inv_obj = new Inventory;
$inv_area = $inv_obj->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);
if(!empty($inv_area)){
	while ($row = $inv_area->FetchRow()){
		header("Location:$fileforward&area=".$row['area_code']);
		exit;
	} 
}


# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

# Title in toolbar
 $smarty->assign('sToolbarTitle','Pharmacy::Select area');

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('dept_select.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle','Pharmacy::Select area');

$smarty->assign('sMascotImg','<img '.createMascot($root_path,'mascot1_r.gif','0','bottom').' align="absmiddle">');
$smarty->assign('LDPlsSelectDept',$LDPlsSelectDept);

 # Buffer department rows output
 ob_start();

echo "<h3>Please contact IHOMP for inventory area assignment.</h3>";
//removed for inventory
// echo '
// <tr class="wardlistrow1">
// 	<td>&nbsp;<strong>All areas (Requires access privelege)</strong></td>
// 	<td width="1">
// 		<a href="'.$fileforward.'&area=all">
// 			<img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" >
// 		</a>
// 	</td>
// </tr>';
// $toggler=1;
// while($row=$dept->FetchRow()){

// 	$bold='';
// 	$boldx='';
// 	#if($hilitedept==$v['nr']) 	{ echo '<tr bgcolor="yellow">'; $bold="<font color=\"red\" size=2><b>";$boldx="</b></font>"; }
// 	#else
// 		if ($toggler==0)
// 			{ echo '<tr class="wardlistrow1">'; $toggler=1;}
// 				else { echo '<tr class="wardlistrow2">'; $toggler=0;}
// 	echo '<td>&nbsp;'.$bold;
// 	echo $row['area_name'];
// 	echo $boldx.'&nbsp;</td>';
// 	echo '<td width="1"><a href="'.$fileforward.'&area='.strtolower($row['area_code']).'">
// 	<img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" ></a> </td></tr>';
// 	echo "\n";

// 	}

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the dept rows  to the frame template

 $smarty->assign('sDeptRows',$sTemp);

$smarty->assign('sBackLink','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0').' alt="'.$LDCloseAlt.'">');

 $smarty->assign('sMainBlockIncludeFile','order/select_area.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
<!-- Added by cha, 11-22-2010-->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" language="javascript">
function init() {
	//select all areas
	shortcut.add('shift+a', keyAllAreas,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select blood bank
	shortcut.add('shift+b', keyBB,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select ASU
	shortcut.add('shift+c', keyASU,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select ER
	shortcut.add('shift+e', keyER,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select IP
	shortcut.add('shift+i', keyIP,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select MG
	shortcut.add('shift+g', keyMG,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select MHC
	shortcut.add('shift+h', keyMHC,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select OR
	shortcut.add('shift+o', keyOR,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
	//select Wards
	shortcut.add('shift+w', keyWards,
			{
				'type':'keydown',
				'propagate':false,
			}
		);
}
function getAreas(area_code) {
	xajax_saveAreasbyUserDefault(area_code);

	// window.location = "<?= $fileforward.'&area='.strtolower($row['area_code'])?>";
}
function getlocation() {
	window.location = "<?= $fileforward.'&area='.strtolower($row['area_code'])?>";
}

function keyAllAreas() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=all";
}

function keyBB() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=bb";
}

function keyASU() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=asu";
}

function keyER() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=er";
}

function keyIP() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=ip";
}

function keyMG() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=mg";
}

function keyMHC() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=mhc";
}

function keyOR() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=or";
}

function keyWards() {
	window.location = "<?=$root_path?>modules/pharmacy/apotheke-pass.php?&userck=<?=$userck?>&target=<?=$target?>&area=wd";
}

document.observe('dom:loaded', init);
</script>