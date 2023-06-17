<?php
# Load the address object
require_once($root_path.'include/care_api_classes/class_address.php');

if ($brgy_nr) {
	$address_brgy = new Address('barangay');
	#$brgy_list = $address_brgy->getAllAddress();
	$brgy_info=$address_brgy->getAddressInfo($brgy_nr,TRUE);
	if ($brgy_info){
		$brgy_row=$brgy_info->FetchRow();
	}
}
else {
	$address_brgy = new Address('municity');
	$brgy_info=$address_brgy->getAddressInfo($mun_nr,TRUE);
	if ($brgy_info){
		$brgy_row=$brgy_info->FetchRow();
		$brgy_row['brgy_name'] = 'Not Provided';
	}
}

/*
** Added by James
** Get seg_audit_trail values
*/
$row = $this->checkAuditTrail($this->pid);
$login = $row['login'];
$dateChanged = $row['date_changed'];
$oldValue = explode("+", $row['old_value']);
$newValue = explode("+", $row['new_value']);

$compareValue = 0; // new_value index will be stored here

while($count<(count($newValue)-1)) {
	if($newValue[$count] == $brgy_row['brgy_name']){
		$compareValue = $count;
		break;
	}
	$count++;
}

if($newValue[$compareValue] == $brgy_row['brgy_name']){

	// Sets the overLib display
	$overLibVal = "<strong>Field Name:</strong> Barangay".
				  "<br><strong>Old Value:</strong> ".$oldValue[$compareValue].
				  "<br><strong>Modified by:</strong> ".$login.
				  "<br><strong>Date Changed:</strong> ".$dateChanged.
				  "<br><br><strong>Item Remaks:</strong> Recenty updated";

	// Sets the <td> parameter for overLib
	$overLib = "onmouseover=\"return overlib('$overLibVal', 
				CAPTION,'Details', TEXTPADDING, 8, 
				CAPTIONPADDING, 4, TEXTFONTCLASS, 'oltxt', 
				CAPTIONFONTCLASS, 'olcap', WIDTH, 250,FGCLASS,'olfgjustify',FGCOLOR, '#bbddff');\"
				onmouseout=\"nd();\"";

	// Set the notification display for recently updated items
	$notifier ="<img src=\"../../gui/img/common/default/arrow-blu.gif\"/>";
}else{
	$overLib;
	$notifier;
}
// End James

ob_start();

?>

	<tr>
		<td class="reg_item"><?php echo $segHouseNoStreet ?>: </td>
		<td class="reg_input" colspan="2">
	 		<?= strtoupper($street_name) ?>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segBrgyName; ?>: 
		</td>
		<td class="reg_input" <?= $overLib ?> colspan="2">
			<?= $notifier ?>
			<?= strtoupper($brgy_row['brgy_name']) ?>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segMuniCityName ?>: </td>
		<td class="reg_input">
			<?= strtoupper($brgy_row['mun_name']) ?>
		</td>
		<td>
			<span class="reg_item"><?php echo $LDZipCode ?>:</span>
			<span class="reg_input"><?= $brgy_row['zipcode'] ?></span>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segProvinceName ?>: </td>
		<td class="reg_input" colspan="2">
			<?= strtoupper($brgy_row['prov_name']) ?>
		</td>
	</tr> 
	<tr>
		<td class="reg_item"><?php echo $segRegionName ?>: </td>
		<td class="reg_input" colspan="2">
			<?= strtoupper($brgy_row['region_name']) ?>
		</td>
	</tr> 
<?php

$segAddressNew = ob_get_contents();
ob_end_clean();

?>
