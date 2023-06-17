<?php /* Smarty version 2.6.0, created on 2020-02-05 13:13:39
         compiled from ../../../modules/dashboard/dashlets/JotPad/templates/noEdit.tpl */ ?>
<div class="data-form">
	<div style="padding:10px">
		<span>There are no configuration options available for this dashlet!</span>
	</div>
	<div class="data-form-controls" align="right">
		<button id="ui-close-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
">Back</button>
	</div>
</div>
<script type="text/javascript">
(function($) {
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