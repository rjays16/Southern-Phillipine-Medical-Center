<?php /* Smarty version 2.6.0, created on 2020-02-05 12:16:08
         compiled from nursing/ward_occupancy.tpl */ ?>

<?php echo $this->_tpl_vars['sWarningPrompt']; ?>

<table cellspacing="0" cellpadding="0" width="100%">
  <tbody>
    <tr valign="top">
      <td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "nursing/ward_occupancy_list.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
      <td align="right"><?php echo $this->_tpl_vars['sSubMenuBlock']; ?>
</td>
    </tr>
  </tbody>
</table>
<p>
<?php echo $this->_tpl_vars['pbClose']; ?>

<br>
<?php echo $this->_tpl_vars['sOpenWardMngmt']; ?>

<?php echo $this->_tpl_vars['currentWardId']; ?>

<?php echo $this->_tpl_vars['currentWardName']; ?>

</p>