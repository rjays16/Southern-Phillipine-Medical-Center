<?php /* Smarty version 2.6.0, created on 2020-02-17 16:08:40
         compiled from dialysis/machine_occupancy_list.tpl */ ?>

<table cellspacing="0" width="100%" border="0">
<tbody>
	<tr>
		<td class="wardlisttitlerow" width="1%">&nbsp;</td>
		<td class="wardlisttitlerow" width="9%"><?php echo $this->_tpl_vars['LDMachineNo']; ?>
</td>
		<td class="wardlisttitlerow" width="6%" align="middle"><?php echo $this->_tpl_vars['LDGenderInfo']; ?>
</td>
		<!-- <td class="wardlisttitlerow" width="6%"><?php echo $this->_tpl_vars['LDBed']; ?>
</td> -->
		<td class="wardlisttitlerow" width="*"><?php echo $this->_tpl_vars['LDFamilyName']; ?>
, <?php echo $this->_tpl_vars['LDName']; ?>
</td>
	
		<td class="wardlisttitlerow" width="8%"><?php echo $this->_tpl_vars['LDPatNr']; ?>
</td>
		<td class="wardlisttitlerow" width="13%"><?php echo $this->_tpl_vars['BillNr']; ?>
</td>
		<!--<td class="wardlisttitlerow" width="13%"><?php echo $this->_tpl_vars['LDInsuranceType']; ?>
</td>-->
		<td class="wardlisttitlerow" width="20%">
			<table cellspacing="0" width="100%" border="0">
				<tr>
					<center><?php echo $this->_tpl_vars['LDDialyserUsed']; ?>
</center>
				</tr>
				<td>
					<center><?php echo $this->_tpl_vars['LDPrev']; ?>
</center>
				</td>
				<td>
					<center><?php echo $this->_tpl_vars['LDPres']; ?>
</center>
				</td>
				<td>
					<center><?php echo $this->_tpl_vars['LDNew']; ?>
</center>
				</td>
			</table>

		</td>
		<td class="wardlisttitlerow" width="15%" align="center"><?php echo $this->_tpl_vars['LDOptions']; ?>
</td>
	</tr>

	<?php echo $this->_tpl_vars['sOccListRows']; ?>


 </tbody>
</table>