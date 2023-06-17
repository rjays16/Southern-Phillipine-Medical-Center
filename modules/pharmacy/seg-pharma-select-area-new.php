<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/order.common.php");
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','prompt.php');
$local_user='ck_prod_order_user';
require_once($root_path.'include/inc_front_chain_lang.php');
global $db;
if(empty($pday)) $pday=date('j');
if(empty($pmonth)) $pmonth=date('n');
if(empty($pyear)) $pyear=date('Y');
$abtarr=array();
$abtname=array();
$datum=date('d.m.Y');
# Load the medical department list
require_once($root_path.'include/care_api_classes/class_product.php');
$prod_obj=new Product;
#Added By Mark 06-05-2016
$getAreaFromBB = "";
$headerTitle = "All areas (Requires access privelege)";

$dept=$prod_obj->getAllPharmaAreas();


# ENDAdded By Mark 06-05-2016

$title=$LDSelectDept;

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 # Buffer department rows output
 ob_start();
#edited By Mark 06-05-2016
echo '
<tr class="wardlistrow1">
	<td>&nbsp;<strong>'.$headerTitle.'</strong></td>
	<td width="1">
		
	</td>
</tr>
';

require_once($root_path.'include/care_api_classes/class_inventory.php');
$inv_obj = new Inventory;
$inv_area = $inv_obj->getInventoryAreaByPersonnel($_SESSION['sess_login_personell_nr']);
$select_area = array();
$PICU_PHARMACY = "";
if($inv_area){
	while ($rows = $inv_area->FetchRow()){
		$select_area[]= $rows['area_code'];
		if ($rows['area_code']=="PP") {
			// can't validated PP PICU PHARMACY by in_array
			$PICU_PHARMACY ="PP";
		}
	}
}

$inv_area2 = $inv_obj->getInventoryAreaAll();
$toggler=1;
while($row=$inv_area2->FetchRow()){
	$bold='';
	$boldx='';
		if ($toggler==0)
			{ echo '<tr class="wardlistrow1">'; $toggler=1;}
				else { echo '<tr class="wardlistrow2">'; $toggler=0;}
	echo '<td>&nbsp;'.$bold;
	echo ($getAreaFromBB !="") ? "<h3>".$row['area_name']."</h3>" : $row['area_name'];
	echo $boldx.'&nbsp;</td>';
	echo '<td width="1">';
	if (empty($PICU_PHARMACY) && $row["area_code"]=="PP") {
		echo '<a class="disabled">
					<img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" ></a>';
	}else{
			if (in_array($row["area_code"], $select_area,TRUE) || $row['is_deleted'] == 1) {
				echo '<a href="#" onclick="getAreas(\''.addslashes($row["area_code"]).'\',\''.addslashes($row["area_name"]).'\')">
					<img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" ></a>';
			}else{
				echo '<a class="disabled">
					<img '.createLDImgSrc($root_path,'ok_small.gif','0','absmiddle').' alt="'.$LDShowActualPlan.'" ></a>';
			}
	}
	echo '</td></tr>';
	echo "\n";
	}

$sTemp = ob_get_contents();
ob_end_clean();

 $smarty->assign('newArea',$_GET['new']==1 ? true : false);
 $smarty->assign('sDeptRows',$sTemp);
 $smarty->assign('sMainBlockIncludeFile','order/select_area.tpl');
 $smarty->display('common/mainframe.tpl');
 $xajax->printJavascript($root_path.'classes/xajax_0.5');
 $smarty->append('JavaScript',$sTemp);
?>
<!-- Added by cha, 11-22-2010-->
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/event.simulate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" language="javascript">
function getAreas(areas_code,area_name) {
		xajax_saveAreasbyUserDefault(areas_code);
		window.parent.getAreas(areas_code,area_name);
}
</script>
