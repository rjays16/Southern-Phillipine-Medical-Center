<?php
# Created by JEFF @ October 15, 2017 for Blood Bank Preint History Audit Trail.
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
require($root_path.'modules/social_service/ajax/social_client_common_ajx.php');
$xajax->printJavascript($root_path.'classes/xajax_0.5');


$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);

# Title in the title bar
$smarty->assign('sToolbarTitle',"$title");

# href for the back button
// $smarty->assign('pbBack',$returnfile);
// 3003818 | 3000506
// $pid ="3003818";
// $enc_nr="2017000012";
# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");
// $onLoadJs='onLoad="if (window.focus) window.focus();xajax_getProgressNotes('.$pid.','.$enc_nr.');"';
// $smarty->assign('sOnLoadJs',$onLoadJs);
# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"$title");

$session = $_SESSION['sess_login_personell_nr'];
    $strSQL = "select permission,login_id from care_users WHERE personell_nr=".$db->qstr($session);
    $permission = array();
    $ss= array();
    $login_id = "";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){
                  $permission[] = $row['permission'];
                  $login_id = $row['login_id'];
                }
            }
        }
 require_once($root_path . 'include/care_api_classes/class_acl.php');
$objAcl = new Acl($login_id);
$all_prog_notes = $objAcl->checkPermissionRaw('_a_1_manage_progress_notes');
$s_prog_notes = $objAcl->checkPermissionRaw('_a_2_save_progress_notes');
$d_prog_notes = $objAcl->checkPermissionRaw('_a_2_delete_progress_notes');
$p_prog_notes = $objAcl->checkPermissionRaw('_a_2_print_progress_notes');
$v_prog_notes =  $objAcl->checkPermissionRaw('_a_2_view_progress_notes');
$u_prog_notes = $objAcl->checkPermissionRaw('_a_2_update_progress_notes');
# Collect javascript code
ob_start()
?>

<link href="../billing_new/css/dataTables/bootstrap.min.css" rel="stylesheet">   
<script src="../billing_new/js/dataTables/jquery.min.js"></script>
<link rel="stylesheet" href="../billing_new/css/dataTables/jquery.dataTables.min.css"></style>
<script type="text/javascript" src="../billing_new/js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="../billing_new/js/dataTables/bootstrap.min.js"></script>
<script type="text/javascript" src="js/social_service_intake.js?t=<?= time() ?>">
// xajax_getProgressNotes($pid.','$enc_nr);
</script>
<table border="1" id="myTable" class="table table-striped">
	<thead style="background-color:#6c7ae0; color:white;">
		<th style="text-align: center;">Date/Time </th>
		<th style="text-align: center;">Ward</th>
		<th style="text-align: center;">Diagnosis</th>
		<th style="text-align: center;">Referral</th>
		<th style="text-align: center;">Informant</th>
		<th style="text-align: center;">Relationship</th>
		<th style="text-align: center;">Purpose</th>
		<th style="text-align: center;">Action</th>
		<th style="text-align: center;">Recommendation</th>
		<th style="text-align: center; font-size: 10px;">Social Worker</th>
		<th style="text-align: center;">UPDATE</th>
		<th style="text-align: center;">DELETE</th>
	</thead>
 	<!-- <tbody id="social_form_data">   -->
 	<tbody>  
<?php 
		global $db;
		$sql = "SELECT sspn.`notes_id` AS id, sspn.`progress_date` AS prog_date,IF(sspn.`ward`!='',sspn.`ward`,'No Ward') AS ward,IF(cw.`name`!='',cw.`name`,'No Data') AS NAME,IF(sspn.`diagnosis`!='',sspn.`diagnosis`,'No Diagnosis') AS diagnosis,IF(sspn.`referral`!='',sspn.`referral`,'No Data') AS referral,IF(sspn.`informant`!='',sspn.`informant`,'No Data') AS informant, IF(sspn.`relationship`!='',sspn.`relationship`,'No Data') AS relationship,IF(sspn.`purpose`!='',sspn.`purpose`,'No Data') AS purpose,IF(sspn.`action_taken`!='',sspn.`action_taken`,'No Data') AS action_taken,IF(sspn.`recommendation`!='',sspn.`recommendation`,'No Data') AS recommendation,`fn_get_personell_firstname_last`(sspn.`create_id`) AS medsocwork,sspn.`create_id` AS medsocname FROM seg_social_progress_notes AS sspn LEFT JOIN care_ward AS cw ON sspn.`ward` = cw.`nr` WHERE /* sspn.`encounter_nr` = $enc   AND */ sspn.`pid` = ".$db->qstr($_GET['pid'])." AND is_deleted='0' ORDER BY sspn.`create_time` DESC"; 
		/*var_dump($sql);die();*/

		$result = $result = $db->Execute($sql);
		if($all_prog_notes && !($d_prog_notes || $v_prog_notes)){
			$disabled_delete = false;
		}else{
			if($v_prog_notes && !($d_prog_notes)){
				$disabled_delete = true;
			}
			else{
			  $disabled_delete = false;
			}
			if(!$u_prog_notes){
				$disabled_update = true; 
			}
		}

				while($row = $result->FetchRow()){
					if(!empty($row['medsocwork'])) {
						$soc_name = $row['medsocwork'];
					}else{
						$soc_name = $row['medsocname'];
					}	
					echo "<tr>";
		            echo "<td align = 'center'>" . $row['prog_date'] . "</td>";
		            // echo "<td align = 'center'>" . $row['ward']. "</td>";  
		            echo "<td align = 'center'>" . $row['NAME']. "</td>";  
		            echo "<td align = 'center'>" . $row['diagnosis']. "</td>";  
		            echo "<td align = 'center'>" . $row['referral']. "</td>";  
		            echo "<td align = 'center'>" . $row['informant']. "</td>";  
		            echo "<td align = 'center'>" . $row['relationship']. "</td>";  
		            echo "<td align = 'center'>" . $row['purpose']. "</td>";  
		            echo "<td align = 'center'>" . $row['action_taken']. "</td>";  
		            echo "<td align = 'center'>" . $row['recommendation']. "</td>";  
		            echo "<td align = 'center'>" . 	$soc_name. "</td>";
		            if($disabled_update){
		            	echo "<td align = 'center'><img src='../../gui/img/common/default/update.gif' width=20 border=0 height=20 style='cursor: pointer;' title='No Access Permission'></td>";
		            }else{
		            	echo "<td align = 'center'><img src='../../gui/img/common/default/update.gif' width=20 border=0 height=20 onclick=xajax_LoadProgressNote(".$row['id'].")  style='cursor: pointer;' title='Update Note'></td>";
		            }
		            if(!$disabled_delete){
		            	echo "<td align = 'center'><img src='../../gui/img/common/default/delete.gif' width=20 border=0 height=20 onclick=del_Note(".$row['id'].")  style='cursor: pointer;' title='Delete Note'></td>";
			          	echo "</tr>";
			          }
			          else{
			          	echo "<td align = 'center'><img src='../../gui/img/common/default/lock_delete.png' width=20 border=0 height=20  style='cursor: pointer;' title='No Access Permission Note'></td>";
			          	echo "</tr>";
			          }
					}
				  
				
		      	//  echo "<input class='segInput' id='pn_print' name='pn_print' type='button' onclick='printprogressnotes();' value='Print' ".$disabled_print."/>"; | . $row['id']. 
				// 
		?>
		</tbody>
	</table>
	<script type="text/javascript">
	    $(document).ready(function(){
	    $('#myTable').dataTable();
	})

	    // added by jeff @ 11-03-17 for function delete of progress notes
	    function del_Note(id){

			if (confirm('Are you sure you want to delete this progress note?')) {
			    xajax_deleteProgNotes(id);
				    setTimeout(function(){
				    	window.location.reload();
				    	 }, 1000);
			} else {
			    window.location.reload();
			}
	    }

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