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


require_once($root_path . 'include/care_api_classes/inventory/NewInventoryServices.php');
$invService = new InventoryServiceNew();
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

<center>
<div style="width:850px;">

<img src="http://10.1.80.47/SPMC_QA_API/header.png" style="width:850px;"/>
<table border="1" id="myTable"  class="table table-striped" style="background: #ededed;font-size: 12px;">
<thead>
	<th> </th>
	<th>Barcode</th>
	<th>Item code</th>
	<th>Item Name</th>
	<th>Quantity</th>
	<th>Unit</th>
</thead>
 <tbody id="list-data">
 <?php 
 		$API_KEy ="";
 		$API_KEy = $_GET['SEGAPIKEY'];
		$dataItem = $invService->GetItemListFromDai($API_KEy,'item_list');
		$dataItems = count($dataItem['iteminfo']['barcode']);
		 // var_dump($dataItems); die();
			if ($dataItems == 1) {

				  $count++; 		
						echo $rowSrc = '<tr>
								       	  <td align="center">'.$count.'</td>
										  <td>'.$dataItem['iteminfo']['barcode'].'</td>
										  <td>'.$dataItem['iteminfo']['item_code'].'</td>
										  <td align="center">'.$dataItem['iteminfo']['item_name'].'</td>
										  <td align="center">'.$dataItem['iteminfo']['quantity'].'</td>
										  <td align="center">'.$dataItem['iteminfo']['unit'].'</td>
								  		</tr>';
			

			}elseif ($dataItem==404) {
			exit("<h2><font>No matching item in DAI in this area..</font><h2>");
			}elseif($dataItem ==0){
				exit("<h2><font color='red'>Inventory system is down, Please contact administrator.</font><h2>");
			
			}else{
 			foreach ($dataItem['iteminfo'] as $key => $value) {
                   $count++; 		
						echo $rowSrc = '<tr>
								       	  <td align="center">'.$count.'</td>
										  <td>'.$value['barcode'].'</td>
										  <td>'.$value['item_code'].'</td>
										  <td align="center">'.$value['item_name'].'</td>
										  <td align="center">'.$value['quantity'].'</td>
										  <td align="center">'.$value['unit'].'</td>
								  		</tr>';
					}
				}
 ?>
</tbody>
</table>
</div>
</center>
<script type="text/javascript">
	$(document).ready(function(){
    	 $('#myTable').DataTable({
    	 	language: {
        	searchPlaceholder: "  ALL     * "
    	},
    	"sPaginationType": "full_numbers",
    	 });
 	});
</script>
