<?php /* Smarty version 2.6.0, created on 2020-02-17 16:08:40
         compiled from dialysis/machine_occupancy.tpl */ ?>

<?php echo $this->_tpl_vars['sWarningPrompt']; ?>

<?php echo $this->_tpl_vars['sFormStart']; ?>

<table width="35%" border="0" style="font-size: 12px; margin-top:5px" cellspacing="2" cellpadding="2">
		<tbody>
			<tr>
				<td align="center" class="segPanelHeader" colspan="2"><strong>Search Option</strong></td>
			</tr>
			<tr>
				<td class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="2" style="font:12px Arial;color:#000000">
						<tr id="date" style="display:">
								<td align="right" width="20%"><?php echo $this->_tpl_vars['search_select']; ?>
HRN/Patient:</td>
								<td align="left" width="80%"><?php echo $this->_tpl_vars['search_by']; ?>
&nbsp;<?php echo $this->_tpl_vars['search_box']; ?>
</td>
								<!-- <td align="left"><?php echo $this->_tpl_vars['date_js']; ?>
</td> -->
						</tr>
						<tr id="date" style="display:">
								<td align="right" width="40%">Date:</td>
								<td align="left"><?php echo $this->_tpl_vars['date'];  echo $this->_tpl_vars['date_js']; ?>
</td>
						</tr>
						<tr>
							<td colspan="2" align="center"><?php echo $this->_tpl_vars['view_btn']; ?>
</td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo $this->_tpl_vars['sFormEnd']; ?>

<BR>


<table cellspacing="0" cellpadding="0" width="100%">
  <tbody><?php if ($this->_tpl_vars['sNodata']): ?><tr valign="top" ><td align="center" style="background-color:#93b6dc;color:red;"><b>NO RECORDS FOUND</b></td></tr><?php else: ?>
    <tr valign="top">
      <td>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "dialysis/machine_occupancy_list.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
      <td align="right"><?php echo $this->_tpl_vars['sSubMenuBlock']; ?>
</td>
    </tr>
    <?php endif; ?>
  </tbody>
</table>

<?php echo $this->_tpl_vars['sInputDate']; ?>

<?php echo $this->_tpl_vars['sInputHRN']; ?>


<p>
<?php echo $this->_tpl_vars['pbClose']; ?>


<br>
</p>