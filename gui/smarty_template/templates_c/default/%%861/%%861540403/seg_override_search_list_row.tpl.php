<?php /* Smarty version 2.6.0, created on 2020-12-09 14:02:27
         compiled from system_admin/override/seg_override_search_list_row.tpl */ ?>

<tr  <?php if ($this->_tpl_vars['toggle']): ?> class="wardlistrow2" <?php else: ?> class="wardlistrow1" <?php endif; ?>>
	<td>&nbsp;<?php echo $this->_tpl_vars['sPID']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sPersonnelNr']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sCaseNr']; ?>
 <?php echo $this->_tpl_vars['sOutpatientIcon']; ?>
 <font size=1 color="red"><?php echo $this->_tpl_vars['LDAmbulant']; ?>
</font></td>

	<td>&nbsp;<?php echo $this->_tpl_vars['sSex']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sAge']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sLastName']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sFirstName']; ?>
 <?php echo $this->_tpl_vars['sCrossIcon']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sMiddleName']; ?>
</td>

	<td>&nbsp;<?php echo $this->_tpl_vars['sJobPosition']; ?>
</td>

	<td>&nbsp;<?php echo $this->_tpl_vars['sAdmissionDate']; ?>
</td>
	<td>&nbsp;<?php echo $this->_tpl_vars['sDepartment']; ?>
</td>

	<td align="center">&nbsp;<?php echo $this->_tpl_vars['sOptions']; ?>
 <?php echo $this->_tpl_vars['sHiddenBarcode']; ?>
</td>
</tr>