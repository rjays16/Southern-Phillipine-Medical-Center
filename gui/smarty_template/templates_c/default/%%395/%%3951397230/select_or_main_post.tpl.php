<?php /* Smarty version 2.6.0, created on 2020-09-23 11:17:09
         compiled from or/select_or_main_post.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'or/select_or_main_post.tpl', 21, false),)), $this); ?>
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

<div id="select_or">
  <br/>
 
       
<div id="approve_or">

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