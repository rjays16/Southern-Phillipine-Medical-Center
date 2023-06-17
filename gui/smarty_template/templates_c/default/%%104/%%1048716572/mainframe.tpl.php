<?php /* Smarty version 2.6.0, created on 2020-02-05 12:13:56
         compiled from common/mainframe.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'config_load', 'common/mainframe.tpl', 4, false),)), $this); ?>

<?php echo smarty_function_config_load(array('file' => "test.conf",'section' => 'setup'), $this);?>


<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/header.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<div style="width:auto; padding:2px">
<table id="main" width="100%" border="0" cellspacing="0" height="100%">
	<tbody class="main">
	<?php if (! $this->_tpl_vars['newArea']): ?>
			<?php if (! $this->_tpl_vars['bHideTitleBar']): ?>
					<tr>
						<td  valign="top" align="middle" height="35">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/header_topblock.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</td>
					</tr>
			<?php endif; ?>
		<?php endif; ?>
		<tr>
			<td bgcolor=<?php echo $this->_tpl_vars['body_bgcolor']; ?>
 valign="top">
				<div align="center">
				<?php if ($this->_tpl_vars['sysInfoMessage'] != ""): ?>
					<dl id="system-message">
						<dt>Information</dt>
						<dd>
							<?php echo $this->_tpl_vars['sysInfoMessage']; ?>

						</dd>
					</dl>
				<?php elseif ($this->_tpl_vars['sysErrorMessage'] != ""): ?>
					<dl id="error-message">
						<dt>System error</dt>
						<dd>
							<?php echo $this->_tpl_vars['sysErrorMessage']; ?>

						</dd>
					</dl>
				<?php endif; ?>

								<?php if ($this->_tpl_vars['sMainBlockIncludeFile'] != ""): ?>
					<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => $this->_tpl_vars['sMainBlockIncludeFile'], 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['sMainFrameBlockData'] != ""): ?>
					<?php echo $this->_tpl_vars['sMainFrameBlockData']; ?>

				<?php endif; ?>
				
				</div>
			</td>
		</tr>
		<?php if (! $this->_tpl_vars['newArea']): ?>
			<?php if (! $this->_tpl_vars['bHideCopyright']): ?>
					<tr valign=top label="copyright">
						<td bbgcolor=<?php echo $this->_tpl_vars['bot_bgcolor']; ?>
 bgcolor="ffffff">
							<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/copyright.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
						</td>
					</tr>
			<?php endif; ?>
		<?php endif; ?>
	</tbody>
</table>
</div>
<script type="text/javascript">
	window.addEventListener('storage', function (event) {
		if (event.key == 'seghis-login') {
			if(event.newValue == 0){
				var l = window.location,
	            	baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];

	            if(window.parent)
	            	window.parent.location = baseUrl;
	            else
	            	window.location = baseUrl;
			}
		}
	});
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "common/footer.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>