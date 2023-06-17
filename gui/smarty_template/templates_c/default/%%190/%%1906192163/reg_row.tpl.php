<?php /* Smarty version 2.6.0, created on 2020-02-05 12:16:32
         compiled from registration_admission/reg_row.tpl */ ?>
<?php if ($this->_tpl_vars['sItem'] == 'Date of Birth'): ?>
	<span <?php echo $this->_tpl_vars['sOverLib']; ?>
><?php echo $this->_tpl_vars['sNotifier']; ?>
 <?php echo $this->_tpl_vars['sInput']; ?>
</span>
<?php else: ?>
	<tr <?php echo $this->_tpl_vars['segClassName']; ?>
>
	  <td class="reg_item" <?php echo $this->_tpl_vars['sColSpan1']; ?>
><?php echo $this->_tpl_vars['sItem']; ?>
</td>
	  <td class="reg_input" <?php echo $this->_tpl_vars['sOverLib']; ?>
 <?php echo $this->_tpl_vars['sColSpan2']; ?>
><?php echo $this->_tpl_vars['sNotifier']; ?>
 <?php echo $this->_tpl_vars['sInput']; ?>
</td>
	</tr>
<?php endif; ?>