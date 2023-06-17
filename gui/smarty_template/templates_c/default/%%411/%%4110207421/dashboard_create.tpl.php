<?php /* Smarty version 2.6.0, created on 2020-02-05 13:03:32
         compiled from ../../../modules/dashboard/templates/ui/dashboard_create.tpl */ ?>
<div class="data-form">
	<form id="form-<?php echo $this->_tpl_vars['suffix']; ?>
" method="post" action="./">
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tbody class="data-form-group">
				<tr>
					<td>
						<label style="color:red;">Maximum of 15 characters only</label>
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Dashboard name</span>
							<span class="data-form-desc">Title for the current dashboard</span>
						</label>
					</td>
					<td class="data-form-field">
						<input maxlength="15" type="text" class="input" id="title-<?php echo $this->_tpl_vars['suffix']; ?>
" name="title_<?php echo $this->_tpl_vars['suffix']; ?>
" value="<?php echo $this->_tpl_vars['settings']['title']; ?>
" style="width:200px" />
					</td>
				</tr>
			</tbody>
		</table>
		<div class="data-form-controls" align="right">
			<button id="ui-save-<?php echo $this->_tpl_vars['suffix']; ?>
">Save settings</button>
			<button id="ui-close-<?php echo $this->_tpl_vars['suffix']; ?>
">Close</button>
		</div>
	</form>
</div>

<script type="text/javascript">
(function($) {
	$("#ui-save-<?php echo $this->_tpl_vars['suffix']; ?>
").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function(){
		Dashboard.addDashboard({
			title: $('#title-<?php echo $this->_tpl_vars['suffix']; ?>
').val()
		});
		Dashboard.dialog.close();
		return false;
	});

	$("#ui-close-<?php echo $this->_tpl_vars['suffix']; ?>
").button({
		icons: { primary: 'ui-icon-circle-close' }
	}).click(function(){
		Dashboard.dialog.close();
		return false;
	});
})(jQuery);

</script>