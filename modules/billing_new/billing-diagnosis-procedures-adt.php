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

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

# Collect javascript code
ob_start()

?>



<link href="css/dataTables/bootstrap.min.css" rel="stylesheet">   
<script src="js/dataTables/jquery.min.js"></script>
<link rel="stylesheet" href="css/dataTables/jquery.dataTables.min.css"></style>
<script type="text/javascript" src="js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/dataTables/bootstrap.min.js"></script>

<table border="1" id="myTable" class="table table-striped">
<thead>
	<th>Action</th>
	<th>Code</th>
	<th>Description</th>
	<th>Encoder</th>
	<th>Date</th>
</thead>
 <tbody>  
	<?php
	$result = $enc_obj->getDiagProcAdt($_GET['encounter_nr']);
	while ($row = $result->FetchRow()) {
		echo "<tr>";
		echo "<td align = 'center'>".$row['action']."</td>";
		echo "<td align = 'center'>".$row['code']."</td>";
		echo "<td align = 'center'>".$row['description']."</td>";
		echo "<td align = 'center'>".$row['encoder']."</td>";
		echo "<td align = 'center'>".$row['date_modified']."</td>";
		echo "</tr>";
	}
	?>
	</tbody>
</table>
<script type="text/javascript">
    $(document).ready(function(){
    $('#myTable').dataTable();
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
