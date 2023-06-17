<?php /* Smarty version 2.6.0, created on 2020-10-27 15:15:06
         compiled from registration_admission/admit_search_list_row.tpl */ ?>

<tr  <?php if ($this->_tpl_vars['toggle']): ?> class="wardlistrow2" <?php else: ?> class="wardlistrow1" <?php endif; ?>>
	<td>&nbsp;<?php echo $this->_tpl_vars['sCaseNr']; ?>
 <?php echo $this->_tpl_vars['sOutpatientIcon']; ?>
 <font size=1 color="red"><?php echo $this->_tpl_vars['LDAmbulant']; ?>
</font></td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sEncDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sCurrentDept']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sSex']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sLastName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sFirstName']; ?>
 <?php echo $this->_tpl_vars['sCrossIcon']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMiddleName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sBday']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sBrgy']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMuni']; ?>
</td>
<!--	
	<td>&nbsp;<?php echo $this->_tpl_vars['sZipCode']; ?>
</td>
-->
	<?php if ($this->_tpl_vars['ptype'] == 'ipd'): ?>
		<td align="center"><?php echo $this->_tpl_vars['sCurrent_ward_name']; ?>
</td>
	<?php endif; ?>	
	<?php if ($this->_tpl_vars['ptype'] == 'ipd' || $this->_tpl_vars['ptype'] == 'opd' || $this->_tpl_vars['ptype'] == 'er'): ?>
		<td align="center"><?php echo $this->_tpl_vars['sDischarge_date']; ?>
</td>
	<?php endif; ?>	
	<td align="center">&nbsp;<?php echo $this->_tpl_vars['sOptions']; ?>
 <?php echo $this->_tpl_vars['sHiddenBarcode']; ?>
</td>
	<?php if ($this->_tpl_vars['sServeOption']): ?>
		<td align="center"><?php echo $this->_tpl_vars['sServeOption']; ?>
</td>        
	<?php endif; ?>	
</tr>