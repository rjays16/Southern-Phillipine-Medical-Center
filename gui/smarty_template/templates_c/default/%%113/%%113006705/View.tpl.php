<?php /* Smarty version 2.6.0, created on 2020-02-06 17:45:58
         compiled from ../../../modules/dashboard/dashlets/ClockDashlet/templates/View.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/ClockDashlet/templates/View.tpl', 2, false),)), $this); ?>
<div style="text-align:center; padding:4px"
	<canvas id="clock_<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" class="CoolClock:<?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['clockSkin'])) ? $this->_run_mod_handler('default', true, $_tmp, 'Cold') : smarty_modifier_default($_tmp, 'Cold')); ?>
:<?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['clockRadius'])) ? $this->_run_mod_handler('default', true, $_tmp, 80) : smarty_modifier_default($_tmp, 80)); ?>
::<?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['gmtOffset'])) ? $this->_run_mod_handler('default', true, $_tmp, 8) : smarty_modifier_default($_tmp, 8));  if ($this->_tpl_vars['settings']['showDigital'] == 1): ?>:showDigital<?php endif; ?>"></canvas>
</div>
<script type="text/javascript">
(function($) {
	if ('undefined' == typeof window.CoolClock)
	{
		$.ajax({
			url: '../../js/coolclock/coolclock.js',
			dataType: 'script',
			async: false
		});
		$.ajax({
			url: '../../js/coolclock/moreskins.js',
			dataType: 'script',
			async: false
		});
	}
	CoolClock.findAndCreateClocks();
})(jQuery);
</script>