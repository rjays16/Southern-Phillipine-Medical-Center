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
//include xajax common file . .
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);
$breakfile=$root_path."modules/pharmacy/seg-pharma-order-functions.php".URL_APPEND."&userck=$userck";

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
# Title in the title bar
$smarty->assign('sToolbarTitle',"Pharmacy:Inventory Area Manager");
# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");
# href for the close button
$smarty->assign('breakfile',$breakfile);
$smarty->assign('sWindowTitle',"Pharmacy:Inventory Area Manager");


include_once($root_path."include/care_api_classes/class_inventory.php");
$inv_obj=new Inventory;
// var_dump($inv_obj->savePharmaAreas()); die();
# Collect javascript code
ob_start();

require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);

// $CanViewDATA = $acl->checkPermissionRaw(array('_a_1_pharmainventory'));
$CanCreateDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventorycreate'));
$CanUpdateDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventoryupdate'));
$CanDeleteDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventorydelete'));
$CanRedoDATA = $acl->checkPermissionRaw(array('_a_2_pharmainventoryRedodelete'));


?>

<link href="../billing_new/css/dataTables/bootstrap.min.css" rel="stylesheet">   
<script src="../billing_new/js/dataTables/jquery.min.js"></script>
<link rel="stylesheet" href="../billing_new/css/dataTables/jquery.dataTables.min.css"></style>
<script type="text/javascript" src="../billing_new/js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../billing_new/js/dataTables/bootstrap.min.js"></script>
<script type="text/javascript" src="../billing_new/js/dataTables/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
 <script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
 <script type="text/javascript">var $j = jQuery.noConflict();</script>
<script type="text/javascript">
var canView = '<?php $CanViewDATA;?>';
var canCreate = '<?=$CanCreateDATA?>';
var canUpdate = '<?=$CanUpdateDATA?>';
var canDelete = '<?=$CanDeleteDATA?>';

</script>
<style type="text/css">
	.hover_data:hover{ background-color: #969595;color: #ffffff; font-size: 15px;}
</style>
<script type='text/javascript' src="./js/new-inventory-manager.js"></script>
 
<button  onclick="dialoGAreas('','');" id="addArea" class="segButton"><img width="16" height="16"  src="../../gui/img/common/default/package_add.png">New Area</button>
<div id="dataTableFile" style="display: none;"></div>
<input class="global_filter"  id="global_filter" value="<?php echo $_GET['thisData']; ?>" type="hidden" />
<div id="inventoryDAI" style="display: none;" ></div>
<table border="0" id="myTable"  style="background: #ededed;width: 100%;" class="row-border" cellspacing="0">
<thead>
	<th  title="Sort Area Code by Ascending or Descending">Area Code</th>
	<th  title="Sort Area name by Ascending or Descending">Area Name</th>
	<th  title="Sort Inventory Area Code by Ascending or Descending">Inventory Area Code</th>
	<th  title="Sort Socialized by Ascending or Descending">Socialized</th>
	<th  align="center"  title="Action" class="no-sort" >Option</th>
</thead>
 <tbody id="list-data">
 <?php 
 		$pharma_arear = $inv_obj->getAreasPharma() or die("ERROR SQL: ".$inv_obj->sql);
			foreach ($pharma_arear as $key ) {
				if ($key['is_deleted']== 1) {
					if($CanRedoDATA){
						/*allow audittrail*/
						$button ='<img id="btnDelete" title="Undo" onclick="dialoGAreas(\''.$key['area_code'].'\',3)" border="0" align="absmiddle" src="../../img/icons/table_refresh.png" class="segSimulatedLink">
								  <img id="das" title="Audit Trail" onclick="AuditTrail(\''.$key['area_code'].'\')" border="0" align="absmiddle" src="../../images/edit2.gif" class="segSimulatedLink">';
					}else{
						$button ='<img id="btnView" title="Undo Areas" onclick="dialoGAreas(\''.$key['area_code'].'\',0)" border="0" align="absmiddle"  src="../../images/cashier_edit.gif" class="segSimulatedLink">
						  	  		<img id="btnDeleteAlert" title="Undo"onclick="alert(\''.addslashes("You don't have permission to undo this data.").'\')" border="0" align="absmiddle" src="../../img/icons/table_refresh.png" class="segSimulatedLink">';
					}
					# code...
				}else{
					$button ='<img id="btnView" title="View and Modify" onclick="dialoGAreas(\''.$key['area_code'].'\',0)" border="0" align="absmiddle"  src="../../images/cashier_edit.gif" class="segSimulatedLink">
						      <img id="btnDelete" title="Deactivate Area." onclick="dialoGAreas(\''.$key['area_code'].'\',1)" border="0" align="absmiddle" src="../../images/cashier_delete.gif" class="segSimulatedLink">
						      <img id="das" title="View API item\'s From DAI" onclick="inventoryDAI(\''.$key['inv_api_key'].'\')" border="0" align="absmiddle" src="../../gui/img/common/default/book_edit.png" class="segSimulatedLink">
						      <img id="das" title="Audit Trail" onclick="AuditTrail(\''.$key['area_code'].'\')" border="0" align="absmiddle" src="../../images/edit.gif" class="segSimulatedLink">';
			

				}
				
					echo $rowSrc = '<tr class="hover_data" '.(($key['is_deleted']== 1) ? 'style="background:#f26e21;color:#000000;"title="Deactivated Inventory Area"' : '').'>
						       	  <td '.(($key['is_deleted']== 1) ? 'style="background:#ff5722;color:#000000;"' : '').' align="center">'.$key['area_code'].'</td>
								  <td>'.$key['area_name'].'</td>
								  <td>'.$key['inv_area_code'].'</td>
								  <td align="center">'.(($key['allow_socialized']==1) ? 'YES' : 'NO').'</td>
								  <td align="center">
								  	<div id="actionBTN">
								  	'.$button.'
							  	 </div>
								  </td>
						  		</tr>';
					}
 ?>
</tbody>
</table>

<?php

 $sTemp = ob_get_contents();
ob_end_clean();
$xajax->printJavascript($root_path.'classes/xajax_0.5');
$listgen->printJavascript($root_path);

# Assign the form template to mainframe
$smarty->assign('sMainFrameBlockData',$sTemp);
$smarty->display('common/mainframe.tpl');
?>
