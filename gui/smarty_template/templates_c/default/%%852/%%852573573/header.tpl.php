<?php /* Smarty version 2.6.0, created on 2020-02-05 12:13:59
         compiled from common/header.tpl */ ?>
<?php echo $this->_tpl_vars['HTMLtag']; ?>

<HEAD>
	<TITLE><?php echo $this->_tpl_vars['sWindowTitle']; ?>
 - <?php echo $this->_tpl_vars['Name']; ?>
</TITLE>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/metaheaders.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php echo $this->_tpl_vars['setCharSet']; ?>

	<?php if (count($_from = (array)$this->_tpl_vars['JavaScript'])):
    foreach ($_from as $this->_tpl_vars['currentJS']):
?>
	<?php echo $this->_tpl_vars['currentJS']; ?>

	<?php endforeach; unset($_from); endif; ?>
	<?php echo $this->_tpl_vars['yhScript']; ?>

</HEAD>
<BODY bgcolor="#FFFFFF" <?php echo $this->_tpl_vars['class']; ?>
 <?php echo $this->_tpl_vars['sLinkColors']; ?>
 <?php echo $this->_tpl_vars['sOnLoadJs']; ?>
 <?php echo $this->_tpl_vars['sOnUnloadJs']; ?>
>