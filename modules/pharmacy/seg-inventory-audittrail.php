<?php
/*Author MARK 2016-10-20*/
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


include_once($root_path."include/care_api_classes/class_inventory.php");
$inv_obj=new Inventory;
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
<CENTER>
<div style="width: 750px;">
<table border="1" id="myTable" class="table table-striped" style="background: #ededed;font-size: 12px;">
<thead>
	<th class="no-sort" align="center">Action Taken/By</th>
	<th class="no-sort" align="center">Date</th>
</thead>
 <tbody id="list-data">
 <?php 
 		$areacode ="";
 		$areacode = $_GET['areCode'];
		$result = $inv_obj->getAreasPharmaBy($areacode) or die("ERROR SQL: ".$inv_obj->sql);
		while ($row = $result->FetchRow()) {
				$slice_data = explode("\n", $row['history']);
				foreach ($slice_data as $key) {
					$slice_data2 = explode("|", $key);
						echo $rowSrc = '<tr>
										 <td align="center" ><font style="font-size:15px;">'.$slice_data2[0].'</font></td>
										 <td align="center" ><font style="font-size:15px;">'.(($slice_data2[1] =="") ? "" :date('F j,Y -h:i:s A', strtotime($slice_data2[1]))).'</font></td>
									</tr>';
								
					}
					
				}
			
		
	
 ?>
</tbody>
</table>
</div>
</CENTER>
<script type="text/javascript">
	$(document).ready(function(){
    	 $('#myTable').DataTable({
    	 	language: {
        	searchPlaceholder: "  ALL     * "
    	},
    	 "columnDefs": [ {
                  "targets": 'no-sort',
                  "orderable": false,
            } ],
    	"sPaginationType": "full_numbers",
    	 });
 	});
</script>
