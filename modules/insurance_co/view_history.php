<?php 
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj=new Ward;

$patterns = array ('/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/',
                   '/^\s*{(\w+)}\s*=/');
$replace = array ('\3-\4-\1\2', '$\1 =');

$sql = "SELECT * FROM care_type_room WHERE nr='$nr'";
$result=$db->Execute($sql);
$history=$result->FetchRow();
?>

<head><title>Records History</title></head>

<body onblur="window.close()">
	
	<table>
		
		<tr>
			<td>Room Type: </td>
			<td><?php echo $history['name']; ?></td>
		</tr>
		<tr>
			<td>Room Description: </td>
			<td><?php echo $history['description']; ?></td>
		</tr>
		<tr>
			<td>Room Rate: </td>
			<td><?php echo 'P '. number_format(trim($history['room_rate']),2,".",""); ?></td>
		</tr>
        
	</table>
	<table cellspacing=1 frame="box">
		<tr>
			<td background="../../gui/img/common/default/tableHeaderbg.gif" width="550">
				<font face=arial color="#efefef"><b>ROOM TYPE HISTORY</b></font>
			</td>
		</tr>
		

		<tr bgcolor="#ffffff">
	     <td><font face=arial size=2>
		 <?php echo "<br>";
		 $buffer=nl2br($history['history']);
		 //$str='</td></tr><tr bgcolor="#ffffff" background="'.$root_path.'gui/img/common/default/tableHeaderbg3.gif"><td>';
		 $str='</td></tr><tr bgcolor="#ffffff"><td><font face=arial size=1>';
		 //Slite modified by JEFF 05-09-17
		 $toggle=!$toggle;

		 //$buffer=str_replace('<br>',$str,$buffer);
		 //$buffer=str_replace(',','</td><td>',$buffer);
		 echo $buffer;
		 //echo $history['history'];
		 ?>
		 </td>
	  	</tr>
	</table>

<script type="text/javascript">
	function viewDBHistory(){
		var nr = '<?=$nr?>'; 
		window.open("<?=$root_path?>modules/insurance_co/view_history_pdf.php?&nr="+nr,"viewDBHistory","width=600,height=440,menubar=no,resizable=yes,scrollbars=yes")
	}
		window.resizeTo(640,625);
</script>
<p>
	<img src="../../gui/img/control/default/en/en_viewpdf.gif" onClick="viewDBHistory();" style="cursor:pointer" align="absmiddle" border="0">
</p>

</body>