<?php /* Smarty version 2.6.0, created on 2020-02-05 13:42:19
         compiled from ../../../modules/dashboard/dashlets/JotPad/templates/pad.tpl */ ?>
<textarea spellcheck="false" id="pad_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width: 100%; font: normal 12px 'Tahoma'; border: 0; overflow:visible"><?php echo $this->_tpl_vars['dashlet']['content']; ?>
</textarea>
<script type="text/javascript">
(function($) {
	if ('undefined' == typeof $.fn.TextAreaExpander)
	{
		$.ajax({
			url: '../../js/jquery/plugins/jquery.textarea-expander.js',
			dataType: 'script',
			async: false
		});
	}

	$("#pad_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
")
		.TextAreaExpander()

		.blur(function() {
			Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "save", {
				data: $('#pad_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').val()
			});
		});

})(jQuery);
</script>