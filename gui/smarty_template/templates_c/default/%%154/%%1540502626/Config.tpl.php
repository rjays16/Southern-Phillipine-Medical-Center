<?php /* Smarty version 2.6.0, created on 2020-02-06 21:01:26
         compiled from ../../../modules/dashboard/dashlets/ClockDashlet/templates/Config.tpl */ ?>
<div class="data-form">
	<form id="form-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Skin</span>
							<span class="data-form-desc">Sets the clock's display theme</span>
						</label>
					</td>
					<td class="data-form-field">
						<select class="input" id="skin-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="skin">
							<?php if (count($_from = (array)$this->_tpl_vars['skins'])):
    foreach ($_from as $this->_tpl_vars['skin']):
?>
							<option value="<?php echo $this->_tpl_vars['skin']; ?>
" <?php if ($this->_tpl_vars['settings']['clockSkin'] == $this->_tpl_vars['skin']): ?>selected="selected"<?php endif; ?>><?php echo $this->_tpl_vars['skin']; ?>
</option>
							<?php endforeach; unset($_from); endif; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Radius</span>
							<span class="data-form-desc">Sets the pixel-size of the clock's face</span>
						</label>
					</td>
					<td class="data-form-field">
						<input class="input" type="text" size="8" id="radius-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="radius" value="<?php echo $this->_tpl_vars['settings']['clockRadius']; ?>
" />
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Digitial clock?</span>
							<span class="data-form-desc">Show/hide the digital clock</span>
						</label>
					</td>
					<td class="data-form-field">
						<input class="input" type="checkbox" size="15" id="digital-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="digital" value="1" <?php if ($this->_tpl_vars['settings']['showDigital'] == 1): ?>checked="checked"<?php endif; ?> />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
">Save settings</button>
			<button id="ui-close-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
">Back</button>
		</div>
	</form>
</div>
<script type="text/javascript">
(function($) {
	$("#ui-save-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function() {
		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "save", {
			data: $('#form-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').serializeArray()
		});
		return false;
	});

	$("#ui-close-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
").button({
		icons: { primary: 'ui-icon-arrowreturnthick-1-w' }
	}).click(function() {
		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "setMode", {mode:'view'});
		return false;
	});
})(jQuery);
</script>