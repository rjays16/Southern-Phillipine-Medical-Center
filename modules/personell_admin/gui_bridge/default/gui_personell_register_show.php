<?php
# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle', "$LDPersonnelManagement :: $LDPersonellData ($full_pnr)");

 # hide return button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('employment_show.php')");

 # href for close button
/* if ($_GET['from'] == 'medocs' && $_GET['department'] != '') {
 	$breakfile = $root_path."main/startframe.php?ntid=false&lang=en";
 }*/

if ($_GET['from'] == 'medocs' && $_GET['department'] == 'Cashier') {
 	$breakfile = $root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND."&userck=$userck";
 }else if ($_GET['department'] == 'Cashier') {
 	// $breakfile = $root_path."main/startframe.php?ntid=false&lang=en";
 	 $breakfile=$root_path."modules/cashier/seg-cashier-functions.php".URL_APPEND."&userck=$userck";
 }


 
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDPersonnelManagement :: $LDPersonellData ($full_pnr)");

# Colllect javascript code

ob_start();

require($root_path.'include/inc_js_barcode_wristband_popwin.php');
require('./include/js_poprecordhistorywindow.inc.php');


$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

global $allow_dependent_only;

# Buffer page output

ob_start();

?>

<table width=100% border=0 cellspacing=0 cellpadding=0>

<!-- Load tabs -->
<?php

//$target='entry';
include('./gui_bridge/default/gui_tabs_personell_reg.php')

?>

<tr>
<td colspan=3>

<p><br>

<?php
#echo "gui_personell_register_show.php : is_discharged = '".$is_discharged."' <br> \n";
#echo "gui_personell_register_show.php : sem = '".$sem."' <br> \n";
if(empty($is_discharged)){
	if(!empty($sem)){
?>
<table border=0>
	<tr>

		<td><img <?php echo createMascot($root_path,'mascot1_r.gif','0','absmiddle') ?>></td>
		<td><font color="#000099" SIZE=3  FACE="verdana,Arial"> <b><?php echo $LDPersonCurrentlyEmployed; ?></b></font></td>
<!--     <td valign="bottom"><img <?php echo createComIcon($root_path,'angle_down_r.gif','0') ?>></td>
 -->  </tr>
</table>
<?php
	}
	elseif ($row['status']=='expired') {
		?>
			&nbsp;&nbsp;<font color="#FF0000" SIZE=3  FACE="verdana,Arial"> <b><?php echo "Person is already dead.";
			?></b></font>
		<?php
	}
	elseif($row['status']=='deleted'){
		?>
		&nbsp;&nbsp;<font color="#000099" SIZE=3  FACE="verdana,Arial"> <b><?php echo "Person is currently unemployed"; ?></b></font>
		<?php
	}
	else{
?>
	&nbsp;&nbsp;<font color="#000099" SIZE=3  FACE="verdana,Arial"> <b><?php echo $LDPersonCurrentlyEmployed; ?></b></font>
<?php
	}
}
?>

<FONT   >

<table border=0>
	<tr>
		<td>&nbsp;
	</td>

	<td>

	<table border=0 cellpadding=0 cellspacing=0 bgcolor="#999999">
	 <tr>
		 <td>

<table border="0" cellspacing=1 cellpadding=0>
<tr bgcolor="white" >
<td valign="top" class="adm_item">&nbsp;<?php echo $LDPersonellNr ?> :
</td>
<td class="adm_input">
<FONT color="#800000">&nbsp;<b><?php echo $full_pnr; ?></b><br>
<?php #
if(file_exists($root_path.'cache/barcodes/en_'.$full_pnr.'.png')) echo '<img src="'.$root_path.'cache/barcodes/en_'.$full_pnr.'.png" border=0 width=180 height=35>';
	else
	{

		echo "<img src='".$root_path."classes/barcode/image.php?code=".$full_pnr."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>";

		echo "<img src='".$root_path."classes/barcode/image.php?code=".$full_pnr."&style=68&type=I25&width=180&height=40&xres=2&font=5' border=0>";
	}
?>
</td>
<td rowspan=8 align="center" class="photo_id"><img <?php echo $img_source; ?> hspace=5></td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDHrn ?> :
</td>

<td colspan=2   class="adm_input">
<b><?php echo  $pid;
?> </b>


</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php // echo $LDTitle ?>Title :
</td>
<td class="adm_input">&nbsp;<?php echo $title ?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php // echo $LDLastName ?>Family Name :
</td>
<td bgcolor="#ffffee"><FONT color="#800000">&nbsp;<b><?php echo $name_last; ?>
		<?php if($row['status']=='expired' || $personell_status['frombilling']=='1'){?> 
	<img <?php echo createComIcon($root_path,'blackcross_sm.gif','0','absmiddle') ?>>
	<?php } ?>
</b>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php // echo $LDFirstName ?>Given Name :
</td>
<td bgcolor="#ffffee"><FONT color="#800000">&nbsp;<b><?php echo $name_first; ?>
</b>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php // echo $LDBday ?>Date of Birth :
</td>
<td bgcolor="#ffffee"><FONT color="#800000">&nbsp;<b><?php echo formatDate2Local($date_birth,$date_format);?></b>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php // echo $LDSex ?>Sex :
</td>
<td bgcolor="#ffffee"><FONT color="#800000">&nbsp;<b><?php if($sex=='m') echo "Male"; elseif($sex=='f') echo "Female"; ?></b>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php // echo $LDAddress ?>Address :
</td>
<td class="adm_input" colspan=2>
<?php

/* Note: The address is displayed in german format. */
echo $personell_obj->formattedAddress_DE();
/*echo $addr_str.' '.$addr_str_nr.'<br>';
echo $addr_zip.' '.$addr_citytown_name.'<br>';
*/
/*
if ($addr_province) echo $addr_province.'<br>';
if ($addr_region) echo $addr_region.'<br>';
if ($addr_country) echo $addr_country.'<br>';
*/
?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDShortID ?> :
</td>
<td colspan=2   class="adm_input">
<?php echo  $short_id; ?>
</td>
</tr>
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDJobFunction ?> :
</td>
<td colspan=2   class="adm_input">
<?php echo  $job_function_title;
?>
</td>
</tr>

<?php

	if (substr($short_id,0,1)=='N')
		$display_mode_nurse = "";
	else
		$display_mode_nurse = "none";


	if (substr($short_id,0,1)=='D')
		$display_mode = "";
	else
		$display_mode = "none";

	if ($is_reliever)
		$display_mode_reliever = "none";
	else
		$display_mode_reliever = "";
?>

<tr bgcolor="white" style="display:<?=$display_mode?>">
<td class="adm_item">&nbsp;<?php echo "Role" ?> :
</td>
<td colspan=2 class="adm_input">
<?php
      if ($result = $personell_obj->fnGetDoctorRoleName($doctor_role)) {
          $row = $result->fetchrow();
          echo $row['name'];
      }
?>
</td>
</tr>

<tr bgcolor="white" style="display:<?=$display_mode?>">
<td class="adm_item">&nbsp;<?php echo "Doctor Level" ?> :
</td>
<td colspan=2 class="adm_input">
<?php
      if ($result = $personell_obj->getDoctorLevelDesc($doctor_level)) {
          $row = $result->FetchRow();
          echo $row['desc'];
      }
      
?>
</td>
</tr>

<!-- added by VAN 06-14-08-->
<tr bgcolor="white">
<td class="adm_item">&nbsp;Position :
</td>
<td colspan=2   class="adm_input">
<?php echo  $job_position;
?>
</td>

<?php
			$is_reliever_label = 'NO';
			if ($is_reliever)
				$is_reliever_label = 'YES';
?>
<tr bgcolor="white" style="display:<?=$display_mode_nurse?>">
<td class="adm_item">&nbsp;Reliever (All Ward)?  :
</td>
<td colspan=2   class="adm_input">
<?php echo  $is_reliever_label;
?>
</td>
</tr>

</tr>

<tr>
	<td class="adm_item">
		&nbsp;RIS ID:
	</td>
	<td colspan="2" class="adm_input">
		<?php echo $ris_id; ?>
	</td>
</tr>

<?php
		#echo "s = ".$ward_nr;
		require_once($root_path.'include/care_api_classes/class_ward.php');
		$ward_obj=new Ward;
		$ward_name = $ward_obj->WardName($ward_nr);

		if (empty($ward_name))
			$ward_name = 'Reliever (All Wards)';
?>
<!--<tr bgcolor="white" style="display:<?=$display_mode_nurse?>">
<td class="adm_item">&nbsp;Ward Area  :
</td>
<td colspan=2   class="adm_input">
<?php echo  $ward_name;
?>
</td>
</tr>-->

<?php
		$result = $personell_obj->get_Nurse_Ward_Area($personell_nr);
		$count =  $personell_obj->count;
		#echo "ss = ".$personell_obj->sql;
		if ($count==0){
				$src = '<tr><td colspan=4>Ward list is currently empty...</td></tr>';
		}else{
				$rows=array();
				while ($row=$result->FetchRow()) {
						$rows[] = $row;
				}
				foreach ($rows as $i=>$row) {
						if ($row) {
							 $count++;
							 $alt = ($count%2)+1;

							 $src .= '
															<tr class="wardlistrow'.$alt.'" id="row'.$row['ward_nr'].'">
																 <input type="hidden" name="wardlist[]" id="rowWard'.$row['ward_nr'].'" value="'.$row['ward_nr'].'" />
																 <td class="centerAlign"><img src="../../images/claim_ok.gif" border="0"/></td>
																 <td>&nbsp;</td>
																 <td width="*" id="ward_name'.$row['ward_nr'].'">'.$row['name'].'</td>
															</tr>
														';
						 }
				}
								#echo $src;

			}
?>

<tr bgcolor="white" style="display:<?=$display_mode_reliever?>">
	<td class="adm_item">
		 List of Ward:
	</td>
	<td colspan=2 class="adm_item">
			<table id="ward-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					 <tr id="ward-list-header">
						 <th width="5%" nowrap align="left">Cnt : <span id="counter"><?=$scounter?></span></th>
						 <th width="1%">&nbsp;</th>
						 <th width="*" nowrap align="center">&nbsp;&nbsp;Ward</th>
					 </tr>
			 </thead>
			<tbody>
				<?=$src?>
			</tbody>
		</table>
 </td>
</tr>

<?php
	require_once($root_path.'include/care_api_classes/class_inventory.php');
	$inv_obj=new Inventory;
	$result = $inv_obj->getInventoryAreaByPersonnel($personell_nr);
	$count =  $inv_obj->count;

	if ($count==0){
		$invsrc = '<tr><td colspan=3>Inventory area list is currently empty...</td></tr>';
	}else{
		$rows = array();
		while ($row=$result->FetchRow()) {
			$count++;
			$alt = ($count%2)+1;

			$invsrc .= '
				<tr class="wardlistrow'.$alt.'" id="row'.$row['area_code'].'">
					<input type="hidden" name="invarealist[]" id="rowInv'.$row['area_code'].'" value="'.$row['area_code'].'" />
					<td class="centerAlign"><img src="../../images/claim_ok.gif" border="0"/></td>
					<td></td>
					<td width="*" id="inv_area_name'.$row['area_code'].'">'.$row['area_name'].'</td>
				</tr>
			';
		}
	}
?>
<tr bgcolor="white">
	<td class="adm_item">
		List of Inventory Area:
	</td>
	<td colspan=2 class="adm_item">
		<table id="inv-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="inv-list-header">
					<th width="5%" nowrap align="left">Cnt : <span id="inv-counter"><?=$inv_obj->count?></span></th>
					<th width="1%"></th>
					<th width="*" nowrap align="center">Inventory Area</th>
				</tr>
			</thead>
			<tbody>
				<?=$invsrc?>
			</tbody>
		</table>
	</td>
</tr>

<!-- added by syboy 05/15/2015-->
<?php
	$catJobLevel = $personell_obj->getCatJob($personell_nr);
	$count =  $personell_obj->count;
	if ($count == 0) {
		$catJ = "No Category Level";
	} else {
		$catJL = $catJobLevel->FetchRow();
	 	$catJ = $catJL['spjsD'];
	 	$catT = $catJL['spcd'];
	}
?>
<!-- <tr bgcolor="white">
	<td class="adm_item">&nbsp;<?php // echo $LDcatlev ?>Contract Type :</td>
	<td colspan=2   class="adm_input">
		<?php //echo $catJ; ?>
	</td>
</tr> -->
<!-- end -->
<!--- added by: syboy 01/17/2016 : meow -->
<tr>
	<td class="adm_item">&nbsp;Category Type :</td>
	<td colspan=2 class="adm_input">
		<?php echo $catT; ?>
	</td>
</tr>
<!-- end -->

<!-- added by syboy 05/14/2015-->
<tr bgcolor="white">
<td class="adm_item">&nbsp;ID Number :
</td>
<td colspan=2   class="adm_input">
<?php echo  $id_nr;
?>
</td>
</tr>
<!-- end -->

<!-- added by: syboy 01/17/2016 : meow-->
<tr bgcolor="white">
<td class="adm_item">&nbsp;Biometric Number :
</td>
<td colspan=2   class="adm_input">
<?php echo  $bio_nr;
?>
</td>
</tr>
<!-- end -->

<!-- added by VAN 11-27-09-->
<tr bgcolor="white">
<td class="adm_item">&nbsp;Other Title :
</td>
<td colspan=2   class="adm_input">
<?php echo  $other_title;
?>
</td>
</tr>

<!--  style="display:<?=$display_mode?>" -->
<?php// if (($personnel_type=='D')||($personnel_type=='N')){ ?>
<tr bgcolor="white">
<td class="adm_item">&nbsp;License No. :
</td>
<td colspan=2   class="adm_input">
<?php echo  $license_nr;
?>
</td>
</tr>
<? //} ?>

<?php if ($personnel_type=='D'){ ?>
<tr bgcolor="white">
<td class="adm_item">&nbsp;PTR No. :
</td>
<td colspan=2   class="adm_input">
<?php echo  $ptr_nr;
?>
</td>
</tr>
<tr bgcolor="white">
<td class="adm_item">&nbsp;S2 No. :
</td>
<td colspan=2   class="adm_input">
<?php echo  $s2_nr;
?>
</td>
</tr>
<? } ?>

<tr bgcolor="white" style="display:<?=$display_mode?>">
<!--
<td class="adm_item">&nbsp;PHIC No. :
</td>
<td colspan=2   class="adm_input">
<?php echo  $phic_nr;
?>
</td>
-->
<td class="adm_item">Accreditation No. :
						</td>
						<td colspan=3 class="adm_input" width="*">

								<!--<input name="phic_nr" id="phic_nr" type="text" size="30" value="<?php echo $phic_nr; ?>">-->
								<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
									 <thead>
												 <tr id="order-list-header">
														<th width="4%" nowrap></th>
														<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
														<th width="20%" nowrap align="right">&nbsp;&nbsp;Accreditation No.</th>
														<th width="20%" nowrap align="right">&nbsp;&nbsp;Expiration Date</th>
														<th width="5%"></th>
												 </tr>
										<thead>
										<tbody>
												<tr>
														<!--<td colspan="4">Accreditation list is currently empty...</td>-->
														<?php
																$result = $personell_obj->get_Doctor_Accreditation($personell_nr);
																$count =  $personell_obj->count;
																#echo "c = ".$count;
																if ($count==0){
																		 echo '<td colspan=4>Accreditation list is currently empty...</td>';
																}else{

																				$rows=array();
																				while ($row=$result->FetchRow()) {
																						$rows[] = $row;
																				}
																				foreach ($rows as $i=>$row) {
																						if ($row) {
																								$count++;
																								$alt = ($count%2)+1;

																								$src .= '
																												<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
																														<td class="centerAlign">&nbsp;</td>
																														<td width="*" id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
																														<td width="20%" align="right" id="inspin'.$row['hcare_id'].'">'.$row['accreditation_nr'].'</td>
																	<td width="20%" align="right" id="inspin'.$row['hcare_id'].'">'.@formatDate2Local($row['expiration'], $date_format).'</td>
																														<td></td>
																												</tr>
																										';
																						}
																				}
																				echo $src;
																 }
														?>
												</tr>
										</tbody>
								</table>
						</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;TIN :
</td>
<td colspan=2   class="adm_input">
<?php echo  $tin;
?>
</td>
</tr>

<tr bgcolor="white" style="display:<?=$display_mode?>">
<td class="adm_item">&nbsp;Is resident doctor? :
</td>
<td colspan=2   class="adm_input">
<?php
		if ($is_resident_dr)
				$label = 'YES';
		else
				$label = 'NO';
		echo  $label;
?>
</td>
</tr>

<tr bgcolor="white" style="display:<?=$display_mode?>">
<td class="adm_item">&nbsp;Level :
</td>
<td colspan=2 class="adm_input">
<?php
		$result = $personell_obj->getRoleTypeLevels();
		$ntier_nr = 1;
		$stiers = '';
		$count = 0;
		if ($result) {
				while($row=$result->FetchRow()) {
						$checked=($row['tier_nr'] == $tier_nr) ? 'selected="selected"' : "";
						$stiers .= "<option value=\"".$row['tier_nr']."\" $checked>".$row['tier_desc']."</option>\n";

						if ($checked || ($count == 0)) $ntier_nr = $row['tier_nr'];
						if ($checked) $index = $count;
						$count++;
				}
		}
		else
				$stiers = "<option value=\"0\" $checked>- Select Level -</option>\n";
		$stiers = '<select class="segInput" name="tier_nr" id="tier_nr" disabled>'."\n".$stiers."</select>\n";
		echo $stiers;

		#SIGNATURE
		$signature = "{$root_path}gui/img/control/default/en/en_x-blank.gif";
	    $imageFiles = scandir("{$root_path}fotos/registration");

		foreach ($imageFiles as $key => $fileName) {
			if(strpos($fileName, $personell_nr) > -1){
				$signature = "{$root_path}fotos/registration/{$fileName}";
			}
			#END
		}
?>
</td>
</tr>

<!-- -->
<tr bgcolor="white">
<td class="adm_item">&nbsp;Department/Unit :
</td>
<td colspan=2 class="adm_input">
<?php echo  $deptOfDoc['name_formal'];
?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDDateJoin ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php  if($date_join != DBF_NODATE)   echo formatDate2Local($date_join,$date_format); ?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDDateExit ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php  if($date_exit && $date_exit != DBF_NODATE) echo formatDate2Local($date_exit,$date_format); ?></td>
</tr>


<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDRemarks ?> :
</td>
<td colspan=2 class="adm_input">
<?php
      if ($result = $personell_obj->getRemarks($pid)) {
          $row = $result->FetchRow();
          echo $row['remarks'];
      }
      
?>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDContractStart ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;
	<?php
				if($contract_start != DBF_NODATE)
					# echo formatDate2Local($contract_start,$date_format,1,1);   # burn commented : May 4, 2007
					echo formatDate2Local($contract_start,$date_format);   # burn added : May 4, 2007

	?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDContractEnd ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;
	<?php
				if($contract_end && $contract_end != DBF_NODATE)
						# echo formatDate2Local($contract_end,$date_format,1,1);   # burn commented : May 4, 2007
						echo formatDate2Local($contract_end,$date_format);   # burn added : May 4, 2007
	?>
</td>
</tr>

<!-- Commented out : syboy 01/17/2016 : meow -->
<!-- <tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDPayClass ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php echo $pay_class; ?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDPaySubClass ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php echo $pay_class_sub; ?>
</td>
</tr>


<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDLocalPremiumID ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php echo $local_premium_id; ?>
</td>
</tr> -->
<!--
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDAccountNr ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php echo $tax_account_nr; ?>
</td>
</tr>
-->
<!-- <tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDInternalRevenueCode ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php echo $ir_code; ?>
</td>
</tr>
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDNrWorkDay ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php if($nr_workday) echo $nr_workday; ?>
</td>
</tr>
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDNrWeekHour ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php if($nr_weekhour>0) echo $nr_weekhour; ?>
</td>
</tr>
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDNrVacationDay ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php if($nr_vacation_day) echo $nr_vacation_day; ?>
</td>
</tr>
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDMultipleEmployer ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php if($multiple_employer) echo $multiple_employer; ?>
</td>
</tr> -->
<!-- Ended syboy -->
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDNrDependent ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;
<?php // if($nr_dependent) echo $nr_dependent; 
	# added by: syboy 02/04/2016 : meow
	require_once($root_path.'include/care_api_classes/class_personell.php');
 	$objPersonell = new Personell();
 	$dependent = $objPersonell->getEmployeeDependents($pid);
 	echo $dependent;
?>
</td>
</tr>

<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDRecordedBy ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php if ($create_id) echo $create_id ;  ?>
</td>
</tr>

<!-- added by: syboy 05/14/2015 -->
<tr bgcolor="white">
<td class="adm_item">&nbsp;<?php echo $LDModifiedBy ?> :
</td>
<td colspan=2 class="adm_input">&nbsp;<?php if ($modify_id) echo $modify_id ;  ?>
</td>
</tr>

<!-- end -->
<!--signature-->
<tr bgcolor="white">
	<td class="adm_item">&nbsp;<?php echo $LDSignaBy ?> :</td>
	<td class="adm_item"  colspan="2" >
		<?php
		//append random query string into the url to bypass the image's cache - nick
		$signaturePath = Personell::getPersonnelSignature($personell_nr,$root_path)."?r=".strtotime(date('Y-m-d'));
		?>
		<img id="signature-image" src="<?= $signaturePath ?>" height="50" width="200"/>
	</td>
</tr>
<!--end-->

</table>

	 </td>
	 </tr>
 </table>

	</td>
		<td valign="top">
		<?php include('./gui_bridge/default/gui_options_personell_register_show.php'); ?>
	</td>
	</tr>
</table>
<p>
&nbsp;
<!-- added by: syboy 12/18/205 : meow -->
<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''): ?>
	<img class="disabled" <?php echo createLDImgSrc($root_path,'update_data.gif','0','top') ?>>
<?php else: ?>
	<a href="<?php echo $updatefile.URL_APPEND.'&personell_nr='.$personell_nr.'&update=1'; ?>"><img <?php echo createLDImgSrc($root_path,'update_data.gif','0','top') ?>></a>
<?php endif ?>
<!-- Ended syboy -->
</td>
</tr>
</table>
<p>
&nbsp;
<!-- added by: syboy 12/18/205 : meow -->
<?php if ($_GET['from'] == 'medocs' && $_GET['department'] != ''): ?>
	<a href="<?php echo "../../modules/personell_admin/personell_search.php?from=medocs&department=".$_GET['department'] ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
<?php else: ?>
	<a href="<?php echo $breakfile;?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
<?php endif ?>
<!-- Ended syboy -->
<p>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>

