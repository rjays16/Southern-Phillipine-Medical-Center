<?php
# Created by EJ - 11/13/214 ---- to allow user at billing department to view audit trails.
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/billing_new/ajax/icd_icp.common.php");
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

require_once($root_path.'include/care_api_classes/billing/class_transmittal.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

# Collect javascript code
ob_start()

?>



<link href="../billing_new/css/dataTables/bootstrap.min.css" rel="stylesheet">   
<script src="../billing_new/js/dataTables/jquery.min.js"></script>
<link rel="stylesheet" href="../billing_new/css/dataTables/jquery.dataTables.min.css"></style>
<script type="text/javascript" src="../billing_new/js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../billing_new/js/dataTables/bootstrap.min.js"></script>

<table border="1" id="myTable" class="table table-striped">
	<thead style="background-color: #d0dff5;">
		<th><center>Action</center></th>
		<th><center>Encounter Number</center></th>
		<th><center>Patient's Name</center></th>
		<th><center>Encoder</center></th>
		<th><center>Date/Time</center></th>
		<th><center>Reason of Deletion</center></th> <!-- added by carriane 09/20/17 -->
	</thead>
 <tbody>  
	<?php
	$result = $enc_obj->getAuditTrailTrans($_GET['transmit_no']);
	/*var_dump($enc_obj->sql); die();*/
	$content = '';
	while ($row = $result->FetchRow()) {

	$getName = $enc_obj->getAuditTrailName($row['encounter_nr']); // added carriane 09/21/17 

		echo "<tr>";
		// if ($row['action_type']=='Add'|| $row['action_type']=='Delete') {
		// 	echo "<td align = 'center'>".mb_strtoupper($row['action_type'])." ".$row['field_change']." ".$row['encounter_nr']."</td>";

		// Modified by JEFF 05-27-17 and modified again on 06-23-17
		if ($row['action_type']=='Add') {
			echo "<td align = 'center'> ADDED </td>";
		}
		else if ($row['action_type']=='Delete') {
			echo "<td align = 'center'> DELETED </td>";
		}
		else if ($row['action_type']=='Update') {
			echo "<td align = 'center'> UPDATED </td>";
		}
		else{
			// echo "<td align = 'center'>".mb_strtoupper($row['action_type'])." ".$row['field_change']." From ".$row['old_val']." to ".$row['new_val']."  </td>";
			echo "<td align = 'center'> VIEWED </td>";
		}
		echo "<td align = 'center'>".$row['encounter_nr']."</td>";
		$c_dateTime = $row['trans_date'];

		echo "<td align = 'center'>".$getName['patient_name']."</td>"; // updated by carriane 09/21/17

		// echo "<td align = 'center'>".$row['description']."</td>";
		echo "<td align = 'center'>".$row['login_id']."</td>";
		// echo "<td align = 'center'>".$row['trans_date']."</td>";
		echo "<td align = 'center'>".date("Y-m-d h:i A",strtotime($c_dateTime))."</td>";
		
		//added by carriane 09/20/17
		if($row['action_type']=='Delete')
			echo "<td align='center'>".$row['reason_delete']."</td>";
		else
			echo "<td align='center'></td>";
		//end carriane
		
		echo "</tr>";
	}
	?>
	</tbody>
</table>
<script type="text/javascript">
    $(document).ready(function(){
    $('#myTable').dataTable(
    	{
    		"ordering": false
    	});
})
</script>
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
