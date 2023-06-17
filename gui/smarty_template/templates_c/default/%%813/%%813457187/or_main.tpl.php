<?php /* Smarty version 2.6.0, created on 2020-05-06 14:02:09
         compiled from or/or_main.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_radios', 'or/or_main.tpl', 29, false),array('function', 'html_options', 'or/or_main.tpl', 40, false),)), $this); ?>

<style>
#pre_op_diagnosis, #proposed_surgery, #operation_started, #operation_ended, #operation_date {
  width: 100%;
}
.flexigrid div.fbutton .close {
  padding: 0px 0px 1px 0px;                              
  background: url('../../../images/close_select_anesthesia.png') no-repeat;
}
</style>


<?php if (count($_from = (array)$this->_tpl_vars['javascript_array'])):
    foreach ($_from as $this->_tpl_vars['foo']):
?>
    <?php echo $this->_tpl_vars['foo']; ?>

<?php endforeach; unset($_from); endif; ?>
<?php echo $this->_tpl_vars['or_main_css']; ?>

<?php echo $this->_tpl_vars['form_start']; ?>

  <table align="left" cellspacing="2" cellpadding="2" width="100%">
  
      <tr class="segPanelHeader">
        <td>Request Details</td>
      </tr>
      <tr>
        <td class="segPanel">
           <table width="100%">
             <tr>
               <td width="20%">Request Type:</td>
               <td><?php echo smarty_function_html_radios(array('name' => 'request_type','options' => $this->_tpl_vars['request_type'],'selected' => $this->_tpl_vars['selected_request']), $this);?>
</td>
               <td width="16%">Date of Operation:</td>
               <td><?php echo $this->_tpl_vars['operation_date']; ?>
</td>
               <td><?php echo $this->_tpl_vars['operation_date_cal_icon']; ?>
</td>
             </tr>
             
             <tr>
               <td>Patient Name:</td>
               <td><?php echo $this->_tpl_vars['patient_name']; ?>
&nbsp;<?php echo $this->_tpl_vars['patient_select']; ?>
</td>
                <td>Operation Started:</td>
               <td><?php echo $this->_tpl_vars['operation_started']; ?>
</td>
               <td><select name="os_meridian"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['os_meridian_opts'],'selected' => $this->_tpl_vars['os_meridian_selected']), $this);?>
</select></td>
             </tr>
             
             <tr>
               <td>Request Date:</td>
               <td><?php echo $this->_tpl_vars['request_date']; ?>
&nbsp;<?php echo $this->_tpl_vars['request_date_cal_icon']; ?>
 (mm/dd/yyyy)</td>
               <td>Operation Finished:</td>
               <td><?php echo $this->_tpl_vars['operation_ended']; ?>
</td>
               <td><select name="oe_meridian"><?php echo smarty_function_html_options(array('options' => $this->_tpl_vars['oe_meridian_opts'],'selected' => $this->_tpl_vars['oe_meridian_selected']), $this);?>
</select></td>
             </tr>
            
            <tr>
               <td>Consent Signed:</td>
               <td><?php echo smarty_function_html_radios(array('name' => 'consent_signed','options' => $this->_tpl_vars['consent_signed'],'selected' => $this->_tpl_vars['consent_signed_selected']), $this);?>
</td>
               <td>Priority:</td>
               <td colspan="2"><?php echo smarty_function_html_radios(array('name' => 'priority','options' => $this->_tpl_vars['priority'],'selected' => $this->_tpl_vars['priority_selected']), $this);?>
</td>
             </tr>
             
             <tr>
               <td>Pre-operative Diagnosis:</td>
               <td colspan="4"><?php echo $this->_tpl_vars['pre_op_diagnosis']; ?>
</td>
             </tr>
             
             <tr>
               <td>Proposed Surgery:</td>
               <td colspan="4"><?php echo $this->_tpl_vars['proposed_surgery']; ?>
</td>
             </tr>
             
             
             
             <tr>
               <td>Case:</td>
               <td>Service:<?php echo smarty_function_html_radios(array('name' => 'case','options' => $this->_tpl_vars['service'],'selected' => $this->_tpl_vars['op_case_selected']), $this);?>
</td>
               <td colspan="3">Pay:<?php echo smarty_function_html_radios(array('name' => 'case','options' => $this->_tpl_vars['pay'],'selected' => $this->_tpl_vars['op_case_selected']), $this);?>
</td>
               
             </tr>
             
             <tr>                   
               <td>Case Classification</td>
               <td colspan="4"><?php echo smarty_function_html_radios(array('name' => 'case_classification','options' => $this->_tpl_vars['case_classification'],'selected' => $this->_tpl_vars['case_classification_selected']), $this);?>
</td>
             </tr>
           </table>
        </td>
      </tr>
  </table>
 <br style="clear:both"/>
 <!--
  <table width="100%" cellspacing="2" cellpadding="2" >
    <tr class="segPanelHeader">
        <td>Surgical Memorandum</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        <table class="segList" width="100%">
          <thead>
            <tr>
              <th>Family Name</th>
              <th>First Name</th>
              <th>Middle Initial</th>
              <th>Age</th>
              <th>Department</th>
              <th>Hospital Number</th>
              <th>Date of Operation</th>
            </tr>
          </thead>
         
        </table>
        <?php echo $this->_tpl_vars['add_surgical_memo']; ?>

      </td>
    </tr>
  </table>
  -->
  <table cellspacing="2" width="50%" cellpadding="2" style="float:left">
    <tr class="segPanelHeader">
      <td>Surgeon(s)</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        <?php echo $this->_tpl_vars['surgeons']; ?>

        <table class="segList" width="100%" id="surgeon-list">
          <thead>
            <tr>
              <th colspan="3">Name of Surgeon(s)</th>
            </tr> 
          </thead>
          <tbody>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['add_surgeon']; ?>

      </td>
    </tr>
  </table>
  
  <table cellspacing="2" width="50%" cellpadding="2" style="float:left">
    <tr class="segPanelHeader">
      <td>Assistant Surgeon(s)</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        <table class="segList" width="100%" id="asst-surgeon-list">
          <thead>
            <tr>
              <th colspan="3">Name of Assistant Surgeon(s)</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['add_asst_surgeon']; ?>

      </td>
    </tr>
  </table>
  
  <br style="clear:both" />
  <table width="35%" cellpadding="2" cellspacing="2" style="float:left">
    <tr class="segPanelHeader">
      <td>Anesthesiologist(s)</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        <table class="segList" width="100%" id="anesthesiologist-list">
          <thead>
            <tr>
              <th colspan="3">Name of Anesthesiologist(s)</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['add_anesthesiologist']; ?>

      </td>
    </tr>
  </table>
  <table width="30%" cellpadding="2" cellspacing="2" style="float:left">
    <tr class="segPanelHeader">
      <td>Scrub Nurse(s)</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        <table class="segList" width="100%" id="nurse-scrub-list">
          <thead>
            <tr>
              <th colspan="3">Name of Scrub Nurse(s)</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['add_scrub_nurse']; ?>

      </td>
    </tr>
  </table>
  <table width="35%" cellpadding="2" cellspacing="2" style="float:left">
    <tr class="segPanelHeader">
      <td>Circulating Nurse(s)</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        <table class="segList" width="100%" id="nurse-rotating-list">
          <thead>
            <tr>
              <th colspan="3">Name of Curculating Nurse(s)</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['add_circulating_nurse']; ?>

      </td>
    </tr>
  </table>
  <br style="clear:both" />
  <table width="100%" cellspacing="2" cellpadding="2" >
    <tr class="segPanelHeader">
        <td>Anesthesia Procedures</td>
    </tr>
    <tr>
      <td class="segPanel" align="center">
        
        <table class="segList" width="100%" id="anesthesia_procedures">
          <thead>
            <tr>
              <th colspan="2">Anesthesia</th>
              <th>Anesthetics</th>
              <th>Time Begun</th>
              <th>Time Ended</th>
            </tr>
          </thead>
          <tbody>
            <!-- add anesthesia procedures here -->
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['add_anesthesia_procedure']; ?>

        
      </td>
    </tr>
  </table>
  


  <br style="clear:both" />
  <table width="100%" cellspacing="2" cellpadding="2">
    <tr class="segPanelHeader">
      <td>Operation Performed</td>
    </tr>
    
    <tr>
      <td class="segPanel"><?php echo $this->_tpl_vars['operation_performed']; ?>
</td>
    </tr>
  </table>
  <table width="100%" cellspacing="2" cellpadding="2">
    <tr class="segPanelHeader">
      <td>Operation Diagnosis</td>
    </tr>
    
    <tr>
      <td class="segPanel"><?php echo $this->_tpl_vars['operation_diagnosis']; ?>
</td>
    </tr>
  </table>
  
  <table width="100%" cellspacing="2" cellpadding="2">
    <tr class="segPanelHeader">
      <td>Supplies Used</td>
      
      <td width="15%"><?php echo $this->_tpl_vars['is_senior']; ?>
</td>
    </tr>
    
    <tr>
      <td class="segPanel" align="center" colspan="2">
        <table class="segList" width="100%" id="supplies-list">
          <thead>
            <tr>
              <th width="1%" nowrap="nowrap">&nbsp;</th>
              <th width="10%" nowrap="nowrap" align="left">Item No.</th>
              <th width="*" nowrap="nowrap" align="left">Item Description</th>
              <th width="4%" nowrap="nowrap" align="center">Consigned</th>
              <th width="10%" align="center" nowrap="nowrap">Quantity</th>
              <th width="10%" align="right" nowrap="nowrap">Price(Orig)</th>
              <th width="10%" align="right" nowrap="nowrap">Price(Adj)</th>
              <th width="10%" align="right" nowrap="nowrap">Acc. Total</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="8">Order list is currently empty...</td></tr>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['supplies_add_button']; ?>

      </td>
    </tr>
    
  </table>
  <table width="100%" style="font-size: 12px; margin-top: 5px" border="0" cellspacing="1">
            <tbody>
                <tr>
                    <td width="*" align="right" style="background-color:#ffffff; padding:4px" height=""><strong>Sub-Total</strong></th>
                    <td id="show-sub-total" align="right" width="17% "style="background-color:#e0e0e0; color:#000000; font-family:Arial; font-size:15px; font-weight:bold"></td>
                </tr>
                <tr>
                    <td align="right" style="background-color:#ffffff; padding:4px"><strong>Discount</strong></th>
                    <td id="show-discount-total" align="right" style="background-color:#cfcfcf; color:#006600; font-family:Arial; font-size:15px; font-weight:bold"></td>
                </tr>
                <tr>
                    <td align="right" style="background-color:#ffffff; padding:4px"><strong>Net Total</strong></th>
                    <td id="show-net-total" align="right" style="background-color:#bcbcbc; color:#000066; font-family:Arial; font-size:15px; font-weight:bold"></td>
                </tr>
            </tbody>
        </table>
  
  <table width="100%" cellspacing="2" cellpadding="2">
    <tr class="segPanelHeader">
      <td>ICPM</td>
    </tr>
    
    <tr>
      <td class="segPanel" align="center">
        <table class="segList" width="100%" id="order-list">
          <thead>
            <tr>
              <th colspan="2">Code</th>
              <th colspan="2">Description</th>
              <th>RVM</th>
              <th>Multiplier</th>
              <th>Charge</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
        <?php echo $this->_tpl_vars['sBtnAddItem']; ?>

        <?php echo $this->_tpl_vars['sBtnEmptyList']; ?>

      </td>
    </tr>
  </table>
  <input type="hidden" name="pid" id="pid" value="" />
  <input type="hidden" name="patient_address" id="patient_address" value="" />
  <input type="hidden" name="encounter_nr" id="encounter_nr" value="" />
  <input type="hidden" name="discountid" id="discountid" value="">
  <input type="hidden" name="discount" id="discount" value="">
  <input type="submit" value="Submit Form" />
  <input type="hidden" name="is_submitted" />
  <input type="hidden" name="op_request_nr" value="" />
  <input type="hidden" name="mode" value="save" />
  <input type="hidden" name="pharma_area" id="pharma_area" value="OR" />
  
  <?php echo $this->_tpl_vars['department']; ?>

  <?php echo $this->_tpl_vars['operating_room']; ?>

  <?php echo $this->_tpl_vars['op_nr']; ?>

<?php echo $this->_tpl_vars['form_end']; ?>


<div class="jqmWindow" id="omick" style="width:500px;top:17%;border:1px #000 solid;background:#FFF;position:fixed;display:none">
<table class="flexme1" align="left" style="font-family: Arial; font-size: 11px;">
   
</table>

</div>


 <script type="text/javascript">

 var J = jQuery.noConflict();

J('#omick').jqm(); 
function clickme() {
J('#omick').jqmShow();
J('.flexme1').flexigrid({
            url: 'http://192.168.2.237/seghis/modules/anesthesia/ajax_anesthesia.php?table=anesthesia_procedures',
            dataType: 'json',
            colModel : [
                {display: 'Anesthesia', name : 'id', width : 180, sortable : true, align: 'left'},
                {display: 'Select', name:'select', width: 140, sortable: false, align: 'left'}
                ],
            buttons : [
                {name: 'Close', bclass: 'close', onpress : hide}
                ],

            searchitems : [
                {display: 'Anesthesia', name : 'id', isdefault: true}
                ],
            sortname: "id",
            sortorder: "asc",
            usepager: true,
            title: 'Please select an anesthesia',
            useRp: true,
            rp: 5,
            width: 500,
            resizable: false
            });
}
function hide() {
J('#omick').jqmHide(); 
}

function check_or_main(){
  try {
    var form_elements = [{element: document.forms[0].request_type, name: 'Request type', type: 'radio'},
                         {element: document.forms[0].patient_name, name: 'Patient name', type: 'input_text'},
                         {element: document.forms[0].request_date, name: 'Request date', type: 'input_text'},
                         {element: document.forms[0].pre_op_diagnosis, name: 'Pre-operative Diagnosis', type: 'input_text'},
                         {element: document.forms[0].proposed_surgery, name: 'Proposed Surgery', type: 'input_text'},
                         {element: document.forms[0].consent_signed, name: 'Consent signed', type: 'radio'},
                         {element: document.forms[0].case_classification, name: 'Case classification', type: 'radio'},
                         {element: document.getElementsByName('ops_code[]'), name: 'ICPM', type: 'multi_hidden'},
                         {element: document.getElementsByName('surgeon[]'), name: 'Surgeon', type: 'multi_hidden'},
                         {element: document.getElementsByName('anesthesiologist[]'), name: 'Anesthesiologist', type: 'multi_hidden'},
                         {element: document.getElementsByName('anesthesia_procedure[]'), name: 'Anesthesia Procedure', type: 'multi_hidden'},
                         {element: document.forms[0].operation_date, name: 'Date of Operation', type: 'input_text'},
                         {element: document.forms[0].operation_started, name: 'Time of Operation: Start', type: 'input_text'},
                         {element: document.forms[0].operation_ended, name: 'Time of Operation: Finish', type: 'input_text'},
                         {element: document.forms[0].operation_performed, name: 'Operation Performed', type: 'textarea'},
                         {element: document.forms[0].operation_diagnosis, name: 'Operation Diagnosis', type: 'textarea'}];
    
    var errors = new Array();
    for (var i=0; i<form_elements.length; i++) {
    
      if (form_elements[i].type == 'input_text' || form_elements[i].type == 'textarea') {
        var temp_var = form_elements[i].element.value;
        
        if (temp_var == '') {
          errors.push(form_elements[i].name + ' is empty.');
          form_elements[i].element.style.background = "#E75A5A";
          form_elements[i].element.style.color = "#FFFFFF";
          form_elements[i].element.style.border = "1px #FFFFFF solid";
        } 
      }
    }
    if (errors.length != 0) {
      for (var j=0; j<errors.length; j++) {alert(errors[j]);}
      return false;
    }
  } catch (e) {
    alert(e.description);
    return false;
  }
}

function remove_anesthesia_procedure(table, id) {
  var table1 = $(table).getElementsByTagName('tbody').item(0);
  table1.removeChild($('anesthesia_procedure'+id));
  document.forms[0].removeChild($('anesthesia_procedure_hidden'+id));
}

function populate_anesthesia_fields(table, nr, id, anesthetics, time_begun, time_ended, tb_meridian, te_meridian) {
  var details = new Object();
  details.nr = nr;
  details.anesthetics = anesthetics;
  details.time_begun = time_begun;
  details.time_ended = time_ended;
  details.tb_meridian = tb_meridian;
  details.te_
  add_or_main_anesthesia(table, nr, id, details);
}

function add_or_main_anesthesia(table, id, name, details) {

if ($('anesthesia_procedure_hidden'+id)) {
  alert('Existing');
}
else {
var table1 = $(table).getElementsByTagName('tbody').item(0);
var row = document.createElement("tr");

var array_elements = [{type: 'img', src: '../../../images/btn_delitem.gif'},
                      {type: 'td_text', name: name},
                      {type: 'input', name: 'anesthetics[]', text_value: details.anesthetics},
                      {type: 'input', name: 'time_begun[]', is_time: true, meridian: 'tb_meridian[]'},
                      {type: 'input', name: 'time_ended[]', is_time: true, meridian: 'te_meridian[]'}];
for (var i=0; i<array_elements.length; i++) {
  var cell = document.createElement("td");
  if (array_elements[i].type == 'td_text')
    cell.appendChild(document.createTextNode(array_elements[i].name));
  if(array_elements[i].type == 'input')  {
    element = document.createElement(array_elements[i].type) 
    cell.appendChild(element);
    element.name = array_elements[i].name;
    element.type = "text";
    if (array_elements[i].text_value) {
      element.value = array_elements[i].text_value;
    }
  }
  if (array_elements[i].type == 'img') {
    img = document.createElement("img");
    cell.appendChild(img);
    img.src = array_elements[i].src;
    img.style.cursor = "pointer";
    img.addEventListener("click", function() {remove_anesthesia_procedure(table, id)}, false);
  }
                                               
  if (array_elements[i].is_time) {
    element = document.createElement("select");
    element.name = array_elements[i].meridian;
    cell.appendChild(element);
    var options = ['AM', 'PM'];
    for (var j=0; j<options.length; j++) {
      var option = document.createElement("option");
      option.text = options[j];
      option.value = options[j];
      element.options[j] = option;
    }
    
  }
  cell.align = "center";
  row.appendChild(cell);
}
row.id = 'anesthesia_procedure'+id;
$(table).getElementsByTagName('tbody').item(0).appendChild(row);

var hidden_array = document.createElement('input');
hidden_array.name = "anesthesia_procedure[]";
hidden_array.type = "hidden";
hidden_array.value = id;
hidden_array.id = "anesthesia_procedure_hidden"+id;
document.forms[0].appendChild(hidden_array);
}
}
  document.body.onLoad = refreshTotalSupplies();  
</script>