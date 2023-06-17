<?php /* Smarty version 2.6.0, created on 2020-06-13 15:58:46
         compiled from ../../../modules/dashboard/dashlets/PlainHtmlDashlet/templates/Config.tpl */ ?>
<div class="data-form">
	<form id="form-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Content</span>
							<span class="data-form-desc">HTML content to be displayed</span>
						</label>
					</td>
					<td class="data-form-field">
						<textarea type="text" class="input" id="content-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" name="content" style="width:100%" rows="3"><?php echo $this->_tpl_vars['preferences']['content']; ?>
</textarea>
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