<?php /* Smarty version 2.6.0, created on 2020-02-05 12:21:04
         compiled from registration_admission/reg_comp_search_list_row.tpl */ ?>

<tr  <?php if ($this->_tpl_vars['toggle']): ?> class="wardlistrow2" <?php else: ?> class="wardlistrow1" <?php endif; ?>>
    <td>&nbsp;<?php echo $this->_tpl_vars['sCaseNr']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sRegistryNr']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sSex']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sLastName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sFirstName']; ?>
 <?php echo $this->_tpl_vars['sCrossIcon']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sBday']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sAdmissionDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sLocation']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sDischargeDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sOptions']; ?>
 <?php echo $this->_tpl_vars['sHiddenBarcode']; ?>
</td>
    <td>&nbsp;<?php echo $this->_tpl_vars['sOptions2']; ?>
</td>
</tr>