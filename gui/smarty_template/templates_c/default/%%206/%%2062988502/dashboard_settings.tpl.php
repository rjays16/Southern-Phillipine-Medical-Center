<?php /* Smarty version 2.6.0, created on 2020-02-05 12:43:57
         compiled from ../../../modules/dashboard/templates/ui/dashboard_settings.tpl */ ?>
<div class="data-form">
<!--	<form id="form-<?php echo $this->_tpl_vars['suffix']; ?>
" method="post" action="./">-->
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
				<!-- Added position condition by carriane 07/23/18 -->
				<!-- Hide delete function for Home Page Dashboard -->
				<?php if ($this->_tpl_vars['position'] && $this->_tpl_vars['icon'] != 'home'): ?>
					<tr>
						<td class="data-form-context">
							<label class="data-form-label">
								<span class="data-form-title">Delete dashboard</span>
								<span class="data-form-desc" style="color:#c00000">Delete the current active dashboard?</span>
							</label>
						</td>
						<td class="data-form-field">
							<input type="checkbox" class="input" id="delete-<?php echo $this->_tpl_vars['suffix']; ?>
" name="delete_<?php echo $this->_tpl_vars['suffix']; ?>
" value="delete" />
						</td>
					</tr>
				<?php endif; ?>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Column layout</span>
							<span class="data-form-desc">Select the number of columns for this dashboard</span>
						</label>
					</td>
					<td class="data-form-field">
						<div id="layout-buttonset-<?php echo $this->_tpl_vars['suffix']; ?>
">
							<input type="radio" id="layout-1-<?php echo $this->_tpl_vars['suffix']; ?>
" name="column_layout_<?php echo $this->_tpl_vars['suffix']; ?>
" <?php if ($this->_tpl_vars['settings']['columns'] == 1): ?>checked="checked"<?php endif; ?> value="1" /><label for="layout-1-<?php echo $this->_tpl_vars['suffix']; ?>
">One</label>
							<input type="radio" id="layout-2-<?php echo $this->_tpl_vars['suffix']; ?>
" name="column_layout_<?php echo $this->_tpl_vars['suffix']; ?>
" <?php if ($this->_tpl_vars['settings']['columns'] == 2): ?>checked="checked"<?php endif; ?> value="2" /><label for="layout-2-<?php echo $this->_tpl_vars['suffix']; ?>
">Two</label>
							<input type="radio" id="layout-3-<?php echo $this->_tpl_vars['suffix']; ?>
" name="column_layout_<?php echo $this->_tpl_vars['suffix']; ?>
" <?php if ($this->_tpl_vars['settings']['columns'] == 3): ?>checked="checked"<?php endif; ?> value="3" /><label for="layout-3-<?php echo $this->_tpl_vars['suffix']; ?>
">Three</label>
						</div>
					</td>
				</tr>
				<tr>
					<td class="data-form-context">
						<label class="data-form-label">
							<span class="data-form-title">Column widths</span>
							<span class="data-form-desc">Specify the widths for each dashlet column</span>
						</label>
					</td>
					<td class="data-form-fields">
						<div id="layout-widths-<?php echo $this->_tpl_vars['suffix']; ?>
" class="ui-slider"></div>
						<table id="slider-counter-<?php echo $this->_tpl_vars['suffix']; ?>
" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:2px">
							<tr height="">
								<td width="" align="center" class="slider-1 slider-2 slider-3" style="background-color: #eaeaea">
									<input type="text" id="widths-0-<?php echo $this->_tpl_vars['suffix']; ?>
" style="text-align:center; border:0; color:#f6931f; font-weight:bold; width:50px; background-color:transparent" value=""/>
								</td>
								<td width="" align="center" class="slider-2 slider-3">
									<input type="text" id="widths-1-<?php echo $this->_tpl_vars['suffix']; ?>
" style="text-align:center; border:0; color:#f6931f; font-weight:bold; width:50px; background-color:transparent" value=""/>
								</td>
								<td width="" align="center" class="slider-3" style="background-color: #eaeaea">
									<input type="text" id="widths-2-<?php echo $this->_tpl_vars['suffix']; ?>
" style="text-align:center; border:0; color:#f6931f; font-weight:bold; width:50px; background-color:transparent" value=""/>
								</td>
							</tr>
						</table>

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
<!--	</form>-->
</div>

<script type="text/javascript">
(function($) {
	$("#ui-save-<?php echo $this->_tpl_vars['suffix']; ?>
").button({
		icons: { primary: 'ui-icon-circle-check' }
	}).click(function() {
		if ($("#delete-<?php echo $this->_tpl_vars['suffix']; ?>
:checked").val())
		{
			if (confirm('Do you really wish to delete this dashboard?'))
			{
				Dashboard.dialog.close();
				Dashboard.scrap();
				return false;
			}
			else
			{
				return false;
			}
		}

		var columns = parseInt($("#layout-buttonset-<?php echo $this->_tpl_vars['suffix']; ?>
").find(":checked").first().val());
		var value = $("#layout-widths-<?php echo $this->_tpl_vars['suffix']; ?>
").slider("option", "value") || 100;
		var values = $("#layout-widths-<?php echo $this->_tpl_vars['suffix']; ?>
").slider("option", "values") || [value,100];

		columnWidths = [];
		columnWidths.push( columns==1 ? 100 : values[0] );
		if (columns>1)
			columnWidths.push( values[1]-values[0] );
		if (columns>2)
			columnWidths.push( 100-values[1] );

		var title = $("#title-<?php echo $this->_tpl_vars['suffix']; ?>
").val();
		Dashboard.dialog.close();
		Dashboard.layout({
			title: title,
			columns: columns,
			columnWidths : columnWidths
		});
		return false;
	});

	$("#ui-close-<?php echo $this->_tpl_vars['suffix']; ?>
").button({
		icons: { primary: 'ui-icon-circle-close' }
	}).click(function(){
		Dashboard.dialog.close();
		return false;
	});

	$("#layout-buttonset-<?php echo $this->_tpl_vars['suffix']; ?>
").buttonset().click(function() {
		var columns = parseInt( $("#layout-buttonset-<?php echo $this->_tpl_vars['suffix']; ?>
").find(":checked").first().val() );
		var columnWidths = [0,0,0];
		for (var i=0; i<columns; i++)
			columnWidths[i] = parseInt( 100.0/columns );
		Dashboard.utilities._updateCwSliders('<?php echo $this->_tpl_vars['suffix']; ?>
', columns, columnWidths);
		return true;
	});

	Dashboard.utilities._updateCwSliders( '<?php echo $this->_tpl_vars['suffix']; ?>
', <?php echo $this->_tpl_vars['settings']['columns']; ?>
, <?php echo $this->_tpl_vars['settings']['widths']; ?>
 );
})(jQuery);

</script>