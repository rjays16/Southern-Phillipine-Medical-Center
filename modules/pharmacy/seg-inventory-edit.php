<?php

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/pharmacy/ajax/order.common.php");

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
global $db;

$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');
# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
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


include_once($root_path."include/care_api_classes/class_pharma_product.php");
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');
 $smarty->assign('sRootPath',$root_path);
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

 # Assign Body Onload javascript code
 $onLoadJS='onload="optTransfer.init(document.forms[0])"';
 $onLoadJS='onload="$(\'generic\').focus()"';
 $smarty->assign('sOnLoadJs',$onLoadJS);



require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);

// $CanViewDATA = $acl->checkPermissionRaw(array('_a_1_pharmainventory'));
$CanCreateDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventorycreate'));
$CanUpdateDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventoryupdate'));
$CanDeleteDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventorydelete'));
$CanRedoDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventoryRedodelete'));

include_once($root_path."include/care_api_classes/class_inventory.php");
$inv_obj=new Inventory;
   

ob_start();
?>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
 <script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript">
var canView = '<?=$CanViewDATA?>';
var canCreate = '<?=$CanCreateDATA?>';
var canUpdate = '<?=$CanUpdateDATA?>';
var canDelete = '<?=$CanDeleteDATA?>';
var CanRedoDATA = '<?=$CanDeleteDATA?>';
</script>
<script type='text/javascript' src="js/new-inventory-transaction.js"></script>
<?php
	 # Load the javascript code
define(_Modify_1, 0);
define(_Modify_2, 2);
define(_Deactivate, 1);
define(_Undo, 3);


if ($_GET['area_code']) {
	$result = $inv_obj->getAreasPharmaBy($_GET['area_code']) or die("ERROR SQL: ".$inv_obj->sql);
	while ($row = $result->FetchRow()) {
		if ($_GET['area_code'] && ($_GET['isAction']==_Modify_1 || $_GET['isAction']==_Modify_2)){
			
			$smarty->assign('sAreaCode','<input readonly value="'.$row['area_code'].'" class="segInput" type="text" name="item_code" id="area_code" size="30"  />  <font color="red">*</font>');
			$smarty->assign('sAreaName','<input value="'.$row['area_name'].'"   class="segInput" type="text" name="barcode" id="area_name" size="30"  /> <font color="red">*</font>');
			$smarty->assign('sIsSocialized','<input class="segInput" type="checkbox" name="is_socialized" id="allow_socialized" '.($row['allow_socialized'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sLockFlag','<input class="segInput" type="checkbox" value="1" name="price_cash" id="lockflag" '.($row['lockflag'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sShowArea','<input class="segInput" type="checkbox" value="1"  name="price_charge" id="show_area" '.($row['show_area'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sIntventoryAreaCode','<input readonly value="'.$row['inv_area_code'].'"   class="segInput" type="text" name="barcode" id="inv_area_code" />  <font color="red">*</font>');
			$smarty->assign('sIntventoryAPIkey','<input  value="'.$row['inv_api_key'].'"  class="segInput" type="text" name="barcode" id="inv_api_key"   />  <font color="red">*</font>');
			$smarty->assign('sCreadtedDT','<input  value="'.($row['create_dt'] ? date("F-d-Y", strtotime($row['create_dt'])) : '').'"   class="segInput" type="text" />');
			$smarty->assign('sCreadtedBy','<input value="'.$row['create_id'].'"     class="segInput" type="text" />');
			$smarty->assign('sBtnModify','<button class="segButton" id="MODIFY"><img src="'.$root_path.'gui/img/common/default/disk.png" />Update</button>');

		}else if($_GET['area_code'] && $_GET['isAction'] ==_Deactivate){
			$smarty->assign('sAreaCode','<input readonly value="'.$row['area_code'].'" class="segInput" type="text" name="item_code" id="area_code" size="30"  />');
			$smarty->assign('sAreaName','<input readonly value="'.$row['area_name'].'"   class="segInput" type="text" name="barcode" id="area_name" size="30"  />');
			$smarty->assign('sIsSocialized','<input  class="segInput" type="checkbox" name="is_socialized" id="allow_socialized" '.($row['allow_socialized'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sLockFlag','<input  class="segInput" type="checkbox" value="1" name="price_cash" id="lockflag" '.($row['lockflag'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sShowArea','<input  class="segInput" type="checkbox" value="1"  name="price_charge" id="show_area" '.($row['show_area'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sIntventoryAreaCode','<input readonly value="'.$row['inv_area_code'].'"   class="segInput" type="text" name="barcode" id="inv_area_code" />');
			$smarty->assign('sIntventoryAPIkey','<input readonly value="'.$row['inv_api_key'].'"  class="segInput" type="text" name="barcode" id="inv_api_key"   />');
			$smarty->assign('sCreadtedDT','<input readonly value="'.($row['create_dt'] ? date("F-d-Y", strtotime($row['create_dt'])) : '').'"   class="segInput" type="text"/>');
			$smarty->assign('sCreadtedBy','<input readonly value="'.$row['create_id'].'"     class="segInput" type="text" id="create_id"  />');
			$smarty->assign('sBtnModify','<button class="segButton"  id="DELETE"><img src="'.$root_path.'gui/img/common/default/delete.gif" />Confirm Deactivate</button>');
		}else if($_GET['area_code'] && $_GET['isAction'] ==_Undo){
			$smarty->assign('sAreaCode','<input readonly value="'.$row['area_code'].'" class="segInput" type="text" name="item_code" id="area_code" size="30"  />');
			$smarty->assign('sAreaName','<input readonly value="'.$row['area_name'].'"   class="segInput" type="text" name="barcode" id="area_name" size="30"  />');
			$smarty->assign('sIsSocialized','<input  class="segInput" type="checkbox" name="is_socialized" id="allow_socialized" '.($row['allow_socialized'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sLockFlag','<input  class="segInput" type="checkbox" value="1" name="price_cash" id="lockflag" '.($row['lockflag'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sShowArea','<input  class="segInput" type="checkbox" value="1"  name="price_charge" id="show_area" '.($row['show_area'] ? 'checked="checked"' : '').'/>');
			$smarty->assign('sIntventoryAreaCode','<input readonly value="'.$row['inv_area_code'].'"   class="segInput" type="text" name="barcode" id="inv_area_code" />');
			$smarty->assign('sIntventoryAPIkey','<input readonly value="'.$row['inv_api_key'].'"  class="segInput" type="text" name="barcode" id="inv_api_key"   />');
			$smarty->assign('sCreadtedDT','<input readonly value="'.($row['create_dt'] ? date("F-d-Y", strtotime($row['create_dt'])) : '').'"   class="segInput" type="text"/>');
			$smarty->assign('sCreadtedBy','<input readonly value="'.$row['create_id'].'"     class="segInput" type="text" id="create_id"  />');
			$smarty->assign('sBtnModify','<button class="segButton"  id="UNDO"><img src="'.$root_path.'/img/icons/icon-16-clear.png" />Undo Delete</button>');
		}

	}
}else {
		    $smarty->assign('sAreaCode','<input class="segInput" type="text" name="item_code" id="area_code" size="30"  /> <font color="red">*</font>');
			$smarty->assign('sAreaName','<input   class="segInput" type="text" name="barcode" id="area_name" size="30"  /> <font color="red">*</font>');
			$smarty->assign('sIsSocialized','<input class="segInput" value="1"  type="checkbox" name="is_socialized" id="allow_socialized" />');
			$smarty->assign('sLockFlag','<input class="segInput" value="1"  type="checkbox" name="price_cash" id="lockflag" />');
			$smarty->assign('sShowArea','<input class="segInput" value="1"  type="checkbox" name="price_charge" id="show_area"/>');
			$smarty->assign('sIntventoryAreaCode','<input    class="segInput" type="text" name="barcode" id="inv_area_code" size="30"  /> <font color="red">*</font>');
			$smarty->assign('sIntventoryAPIkey','<input    class="segInput" type="text" name="barcode" id="inv_api_key" size="30"  /> <font color="red">*</font>');
			$smarty->assign('sCreadtedDT','<input readonly  value="'.date("F-d-Y").'"  class="segInput" type="text"  />');
			$smarty->assign('sCreadtedBy','<input readonly  value="'.$HTTP_SESSION_VARS['sess_user_name'].'"  class="segInput" type="text"  />');
			$smarty->assign('sBtnModify','<button class="segButton"  id="ADD""><img src="'.$root_path.'gui/img/common/default/disk.png" />Save</button>');
}
 $smarty->assign('shiddenActions','<input type="hidden" value="'.$_GET['isDeleted'].'" id="hiddenActions"/>');

# Assign the form template to mainframe
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->assign('sMainBlockIncludeFile','pharmacy/manage-inventory.tpl');
$smarty->display('common/mainframe.tpl');

?>