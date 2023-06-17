<?php /* Smarty version 2.6.0, created on 2020-02-17 16:08:40
         compiled from dialysis/machine_occupancy_list_row.tpl */ ?>

 <?php if ($this->_tpl_vars['bToggleRowClass']): ?>
	<tr class="<?php echo $this->_tpl_vars['class_label']; ?>
">
 <?php else: ?>
	<tr class="<?php echo $this->_tpl_vars['class_label']; ?>
">
 <?php endif; ?>
		<td></td>
		<td ><?php echo $this->_tpl_vars['sMachineNumber']; ?>
</td>
		<td align="center"><?php echo $this->_tpl_vars['sGenderInfo']; ?>
</td>
	
		<td ><?php echo $this->_tpl_vars['sTitle']; ?>
 <?php echo $this->_tpl_vars['sFamilyName'];  echo $this->_tpl_vars['cComma']; ?>
 <?php echo $this->_tpl_vars['sName']; ?>
</td>
		<td style="font-size:x-small ">&nbsp;<?php echo $this->_tpl_vars['sPatNr']; ?>
</td>
		<td style="font-size:x-small ">&nbsp;<?php echo $this->_tpl_vars['sEnc']; ?>
</td>
		<td style="font-size:x-small" >
			<table cellspacing="0" width="100%" border="0">
				
				<td align="center"><?php echo $this->_tpl_vars['sPrev']; ?>
</td>
				<td align="center"><?php echo $this->_tpl_vars['sPres']; ?>
</td>
				<!-- <td align="center" width="30%"><?php echo $this->_tpl_vars['sNew']; ?>
</td> -->
			</table>
		</td>
	
		<td align="center">&nbsp;<?php echo $this->_tpl_vars['sAdmitDataIcon']; ?>
 <?php echo $this->_tpl_vars['sChartFolderIcon']; ?>
 <?php echo $this->_tpl_vars['sNotesIcon']; ?>
 <?php echo $this->_tpl_vars['sTransferIcon']; ?>
 <?php echo $this->_tpl_vars['sDischargeIcon'];  echo $this->_tpl_vars['sRequestTray']; ?>
</td>
		</tr>
				 
				
		
		
		<tr>
		<td colspan="8" class="thinrow_vspacer"><?php echo $this->_tpl_vars['sOnePixel']; ?>
</td>
		</tr>