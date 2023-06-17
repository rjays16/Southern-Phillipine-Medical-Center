<?php
/*MARK 2016-10-30*/
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
<div style="width:100%;">
<div style="width:100%;background: #8f0d00;">
<img src="http://10.1.80.47/SPMC_QA_API/header.png" style="width:800px;"/>
</div>
<table border="1" id="myTable"  class="table table-striped" style="background: #ededed;font-size: 12px;">
<thead>
	<th> </th>
	<th>Issuance Date</th>
	<th>HRN</th>
	<th>Case Number</th>
	<th>First Name</th>
	<th>Last Name</th>
	<th>Barcode</th>
	<th>Item Code</th>
	<th>Item Name</th>
	<th>Amount Issued</th>
	<th>Unit</th>
	<th>UID</th>
</thead>
 <tbody id="list-data">
 <?php 
 		
 		$API_KEy = $_GET['SEGAPIKEY'].'&hnumber='.$_GET['hnumber'];
		$dataItem = $invService->GetItemListFromDai($API_KEy,'item_transactView');
		$dataItems = count($dataItem['transinfo']['hospital_number']);
		// var_dump($dataItem); die();
		if ($dataItems == 1) {
							echo $rowSrc = '<tr>
								       	  <td align="center">1</td>
										  <td>'.date('F j,Y', strtotime($dataItem['transinfo']['issuance_date'])).'</td>
										  <td>'.$dataItem['transinfo']['hospital_number'].'</td>
										  <td align="center">'.$dataItem['transinfo']['case_number'].'</td>
										  <td align="center">'.$dataItem['transinfo']['first_name'].'</td>
										  <td align="center">'.$dataItem['transinfo']['last_name'].'</td>
										  <td align="center">'.$dataItem['transinfo']['barcode'].'</td>
										  <td align="center">'.$dataItem['transinfo']['item_code'].'</td>
										  <td align="center">'.$dataItem['transinfo']['item_name'].'</td>
										  <td align="center">'.$dataItem['transinfo']['amount_issued'].'</td>
										  <td align="center">'.$dataItem['transinfo']['unit'].'</td>
										  <td align="center">'.$dataItem['transinfo']['uid'].'</td>
								  		</tr>';
		}elseif ($dataItem==404) {
			exit("<h2><font>No matching item transaction in DAI..</font><h2>");
		}elseif($dataItem ==0){
			exit("<h2><font color='red'>Inventory system is down, Please contact administrator.</font><h2>");
		}
		else{
 			foreach ($dataItem['transinfo'] as $key => $value) {
                   $count++; 		
						echo $rowSrc = '<tr>
								       	  <td align="center">'.$count.'</td>
										  <td>'.date('F j,Y', strtotime($value['issuance_date'])).'</td>
										  <td>'.$value['hospital_number'].'</td>
										  <td align="center">'.$value['case_number'].'</td>
										  <td align="center">'.$value['first_name'].'</td>
										  <td align="center">'.$value['last_name'].'</td>
										  <td align="center">'.$value['barcode'].'</td>
										  <td align="center">'.$value['item_code'].'</td>
										  <td align="center">'.$value['item_name'].'</td>
										  <td align="center">'.$value['amount_issued'].'</td>
										  <td align="center">'.$value['unit'].'</td>
										  <td align="center">'.$value['uid'].'</td>
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
