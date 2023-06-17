<?php /* Smarty version 2.6.0, created on 2020-02-05 14:30:58
         compiled from nursing/clinical_chart.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_options', 'nursing/clinical_chart.tpl', 66, false),)), $this); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title></title>
<?php if (count($_from = (array)$this->_tpl_vars['css_and_js'])):
    foreach ($_from as $this->_tpl_vars['css_js']):
?>
    <?php echo $this->_tpl_vars['css_js']; ?>

<?php endforeach; unset($_from); endif; ?>
</head>
<body>
  <div id="header_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span><?php echo $this->_tpl_vars['close_popup']; ?>
<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Date</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['record_date'];  echo $this->_tpl_vars['rd_icon']; ?>
</td>
        </tr>
        <tr>
          <td>Hospital Days</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['hospital_days']; ?>
</td>
        </tr>
        <tr>
          <td>Day P.O. or P.P.</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['day_po_pp']; ?>
</td>
        </tr>
      </table>
      <?php echo $this->_tpl_vars['add_header']; ?>

    </div>
    <?php echo $this->_tpl_vars['resize']; ?>

  </div>
  
  <div id="footer1_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span><?php echo $this->_tpl_vars['close_popup']; ?>
<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Respiration</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['respiration']; ?>
</td>
        </tr>
        <tr>
          <td>Blood Pressure</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['blood_pressure1']; ?>
/<?php echo $this->_tpl_vars['blood_pressure2']; ?>
</td>
        </tr>
      </table>
      <?php echo $this->_tpl_vars['add_first_footer']; ?>

    </div>
    <?php echo $this->_tpl_vars['resize']; ?>

  </div>
  
  <div id="footer2_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span><?php echo $this->_tpl_vars['close_popup']; ?>
<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Weight</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['weight']; ?>
</td>
		  <td><select name="weight_unit"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['weight_unit']), $this);?>
</select></td>
        </tr>
       
      </table>
      <?php echo $this->_tpl_vars['add_second_footer']; ?>

    </div>
    <?php echo $this->_tpl_vars['resize']; ?>

  </div>
  
   <div id="footer3_popup" class="clinical_chart_popup" align="left">
    <div class="jqDrag header"><span style="float:left">Graphical Chart</span><?php echo $this->_tpl_vars['close_popup']; ?>
<br style="clear:both" /></div>
                    
    <div class="body">
       <table border="1">
        <tr>
          <td>Intake Oral</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['intake_oral']; ?>
</td>
        </tr>
        <tr>
          <td>Parenteral</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['parenteral']; ?>
</td>
        </tr>
        <tr>
          <td>Output Urine</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['output_urine']; ?>
</td>
        </tr>
		<tr>
          <td>Drainage</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['drainage']; ?>
</td>
        </tr>
		<tr>
          <td>Emesis</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['emesis']; ?>
</td>
        </tr>
		<tr>
          <td>Stools</td>
          <td>:</td>
          <td><?php echo $this->_tpl_vars['stool']; ?>
</td>
        </tr>
      </table>
      <?php echo $this->_tpl_vars['add_third_footer']; ?>

    </div>
    <?php echo $this->_tpl_vars['resize']; ?>

  </div>
  

  <table width="50%">
    <tr>
      <td class="segPanelHeader" colspan="2">Patient's Information</td>
    </tr>
    <tr>
      <td class="segPanel">Surname</td>
      <td class="segPanel"><?php echo $this->_tpl_vars['person_last_name']; ?>
</td> 
    </tr>
    <tr>
      <td class="segPanel">Given Name</td>
      <td class="segPanel"><?php echo $this->_tpl_vars['person_first_name']; ?>
</td>
    </tr>
    <tr>
      <td class="segPanel">Age</td>
      <td class="segPanel"><?php echo $this->_tpl_vars['person_age']; ?>
</td>
    </tr>
    <tr>
      <td class="segPanel">Sex</td>
      <td class="segPanel"><?php echo $this->_tpl_vars['person_gender']; ?>
</td>
    </tr>
    <tr>
      <td class="segPanel">Hospital Number</td>
      <td class="segPanel"><?php echo $this->_tpl_vars['hospital_number']; ?>
</td>
    </tr>
    <tr>
      <td class="segPanel">Ward/Room</td>
      <td class="segPanel"><?php echo $this->_tpl_vars['ward']; ?>
</td> 
    </tr>
    
    <tr>
      <td class="segPanelHeader" colspan="2">GRAPHIC CHART (Centigrade)</td>
    </tr>
    
    <tr>
      <td class="segPanel" colspan="2">
        <div><?php echo $this->_tpl_vars['clinical_chart']; ?>
</div>
        <map name="clinical_grid" id="my_grid">
          <?php echo $this->_tpl_vars['header_area']; ?>

          <?php echo $this->_tpl_vars['image_area']; ?>

          <?php echo $this->_tpl_vars['footer_first']; ?>

          <?php echo $this->_tpl_vars['footer_second']; ?>
 
        </map>
      </td>
    </tr>
    
  </table>
  <div style="background:#0066FF; width:21; height:9;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="ss"></div>
  <div style="background:#0066FF; width:131; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="dd"></div>
  <div style="background:#0066FF; width:21; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="ee"></div>
  <div style="background:#0066FF; width:65; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="ff"></div>
  <div style="background:#0066FF; width:32; height:19;position:absolute;top:821;left:175;opacity:0.7;cursor:pointer;display:none" id="gg"></div>
  <?php echo $this->_tpl_vars['pointer']; ?>

  <?php echo $this->_tpl_vars['mode']; ?>

  <?php echo $this->_tpl_vars['x_axis']; ?>

  <?php echo $this->_tpl_vars['y_axis']; ?>

  <?php echo $this->_tpl_vars['temperature']; ?>

  <?php echo $this->_tpl_vars['pulse']; ?>

</body>
</html>