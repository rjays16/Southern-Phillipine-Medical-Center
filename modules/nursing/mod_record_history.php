<?php 
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

$patterns = array ('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/',
                   '/^\s*{(\w+)}\s*=/');
$replace = array ('\3-\4-\1\2', '$\1 =');

$pName = $db->GetOne("SELECT fn_get_person_name_first_mi_last(".$db->qstr($pid).")");
$sql = "SELECT history FROM seg_ipbm_patient_classification WHERE encounter_nr =".$db->qstr($encounter_nr);
$history=$db->GetOne($sql);

?>

<head><title>Classification History</title></head>

<body">
	
	<table>
		
		<tr>
			<td><b>Patient Name:</b> </td>
			<td><b><?php echo ucfirst($pName) ?></b></td>
		</tr>
		
        
	</table>
	<table cellspacing=1 frame="box">
		<tr>
			<td background="../../gui/img/common/default/tableHeaderbg.gif" width="550">
				<font face=arial color="#efefef"><b>DB RECORD'S HISTORY</b></font>
			</td>
		</tr>
		

		<tr bgcolor="#ffffff">
	     <td><font face=arial size=2>
		 <?php echo "<br>";
		 $buffer=nl2br($history);

		 $str='</td></tr><tr bgcolor="#ffffff"><td><font face=arial size=1>';
	
		 $toggle=!$toggle;

		
		 echo $buffer;

		 ?>
		 </td>
	  	</tr>
	</table>

<script type="text/javascript">
	
		window.resizeTo(640,625);
</script>

</body>