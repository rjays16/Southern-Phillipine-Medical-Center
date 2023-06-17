<?php /* Smarty version 2.6.0, created on 2020-03-18 09:46:21
         compiled from or/select_or_main_request.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'or/select_or_main_request.tpl', 44, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>

<?php if (count($_from = (array)$this->_tpl_vars['css_and_js'])):
    foreach ($_from as $this->_tpl_vars['script']):
?>
    <?php echo $this->_tpl_vars['script']; ?>

<?php endforeach; unset($_from); endif; ?>

</head>

<body>

<div id="cancel_or_main_request" align="left">
  <div id="header"><span style="float:left">Cancel OR Main Request</span><?php echo $this->_tpl_vars['close_cancel']; ?>
<br style="clear:both" /></div>
  
  <div id="body">
    This request cannot be cancelled unless a reason for cancellation is provided.
    Take note that once this request is cancelled, other operations such as 
    viewing/editing of the request details will be permanently removed. 
    <?php echo $this->_tpl_vars['form_open']; ?>

    <?php echo $this->_tpl_vars['cancellation_reason_label'];  echo $this->_tpl_vars['required_mark']; ?>

    <?php echo $this->_tpl_vars['error_msg']; ?>

    <?php echo $this->_tpl_vars['cancellation_reason']; ?>

    
    <?php echo $this->_tpl_vars['submit_cancel']; ?>

    <?php echo $this->_tpl_vars['cancel_cancel']; ?>

    <?php echo $this->_tpl_vars['submitted']; ?>

    <?php echo $this->_tpl_vars['refno']; ?>

    <?php echo $this->_tpl_vars['mode']; ?>

    <?php echo $this->_tpl_vars['form_close']; ?>

    <br style="clear:both" />
    
  </div>
</div>

<div id="select_or">
  <br/>
 
       
<div id="charge_request">

<div id="search_bar" align="left">
  <?php echo $this->_tpl_vars['search_field'];  echo smarty_function_html_options(array('name' => 'qtype','options' => $this->_tpl_vars['departments'],'selected' => $this->_tpl_vars['selected_department']), $this); echo $this->_tpl_vars['search_button']; ?>

</div>
<div id="navigation">
    
    <div class="group"><select name="number_of_pages"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['number_of_pages']), $this);?>
</select></div>
    <div id="button_separator"></div>
    <div class="group">
      <div id="first" class="button"><span></span></div>
      <div id="prev" class="button"><span></span></div>
    </div>
    <div id="button_separator"></div>
    <div class="group"><span id="control">Page <?php echo $this->_tpl_vars['page_number']; ?>
 of <span></span></span></div>
    <div id="button_separator"></div> 
    <div class="group">
      <div id="next" class="button"><span></span></div>
      <div id="last" class="button"><span></span></div>
    </div>
    <div id="button_separator"></div>
    <div class="group">
      <div id="reloader" class="pre_load button loading"><span></span></div>
    </div>
    <div id="button_separator"></div>
    <div class="group"><span id="page_stat">Processing, please wait...</span></div>
</div>
<table id="or_request_table" align="left"></table>
</div>

<div align="left">
<br/>
<?php echo $this->_tpl_vars['return']; ?>

</div>
</div>

  
</body>

</html>