<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
global $HTTP_SESSION_VARS;

/* Define language and local user for this module */
$thisfile=basename(__FILE__);
$lang_tables[]='prompt.php';
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','aufnahme.php');

$local_user = 'aufnahme_user';

require($root_path.'modules/social_service/ajax/social_client_common_ajx.php');
require_once($root_path.'modules/listgen/listgen.php');
$listgen = new ListGen($root_path);

require_once($root_path.'include/care_api_classes/class_social_service.php');
$objSS = new SocialService;

require_once($root_path.'include/inc_front_chain_lang.php');

require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_personell.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);

$date_format=$GLOBAL_CONFIG['date_format'];

$date_format2 = '%m/%d/%Y';

$pid = $_GET['pid'];

$enc_nr = $encounter_nr=='undefined' ? '' : $encounter_nr;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Onload Javascript code
 // $onLoadJs='onLoad="if (window.focus) window.focus();xajax_getProgressNotes('.$pid.','.$enc_nr.');"';
 $onLoadJs='onLoad="if (window.focus) window.focus();"';

 $smarty->assign('sOnLoadJs',$onLoadJs);

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

$session = $_SESSION['sess_login_personell_nr'];
    $strSQL = "select permission,login_id from care_users WHERE personell_nr=".$db->qstr($session);
    $permission = array();
    $ss= array();
    $login_id = "";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){
                  $permission[] = $row['permission'];
                  $login_id = $row['login_id'];
                }
            }
        }
 require_once($root_path . 'include/care_api_classes/class_acl.php');
$objAcl = new Acl($login_id);
$all_prog_notes = $objAcl->checkPermissionRaw('_a_1_manage_progress_notes');
$s_prog_notes = $objAcl->checkPermissionRaw('_a_2_save_progress_notes');
$d_prog_notes = $objAcl->checkPermissionRaw('_a_2_delete_progress_notes');
$p_prog_notes = $objAcl->checkPermissionRaw('_a_2_print_progress_notes');
$v_prog_notes =  $objAcl->checkPermissionRaw('_a_2_view_progress_notes');
$u_prog_notes = $objAcl->checkPermissionRaw('_a_2_update_progress_notes');

// Ended here..
# Buffer extra javascript code
ob_start();

?>

<script language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<link rel="stylesheet" href = "<?= $root_path ?>modules/social_service/css/ui/jquery-ui.css" type="text/css">
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.10.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-ui.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/masking/html-form-input-mask.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>modules/social_service/css/social_service.css" type="text/css" />
<link rel="stylesheet" href="<?= $root_path ?>css/seg/wirecake.css" type="text/css" /> 

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> 
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery1.6.3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script> 

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.table.addrow.js"></script>

<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<!-- YUI Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>

<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">
<!--added by VAN 05-08-08-->
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript"
            src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript"
            src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax_0.5');
# ListGen
$listgen->printJavascript($root_path);

// foreach ($data as $value) {
//     $row.='<tr>'.
//     '<td>'..'</td>'.
//      '<td>'..'</td>'.
//       '<td>'..'</td>'.
//        '<td>'..'</td>'.
//         '<td>'..'</td>'.
//          '<td>'..'</td>'.
//           '<td>'..'</td>'.
//            '<td>'..'</td>'.
//             '<td>'..'</td>'.
//     '<td>'..'</td></tr>';

// }
?>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.numeric.js?t=<?= time() ?>"></script>
<script type="text/javascript" src="js/social_service_intake.js?t=<?= time() ?>"></script>
<script language="javascript" type="text/javascript"> 
    YAHOO.namespace("example.container");
    YAHOO.util.Event.onDOMReady(init);

    var classificationPreviousValue = '';
    
    var J = jQuery.noConflict();
    
    var timeNow = new Date();
    var hourNow = timeNow.getHours();
    var minuteNow = timeNow.getMinutes();
    
    J().ready(function() {   
        J( "#datetime" ).datetimepicker({
          controlType: 'select',
          oneLine: true,
          hour: hourNow,
          minute: minuteNow,
          changeMonth:true,
          changeYear:true,
          dateFormat: "MM d, yy",
          timeFormat: 'hh:mm tt'
        });
        J('#datetime-trigger').click(function(){
          J("#datetime").focus();
        });

        J( "#tab_form" ).tabs();             
    });

    $J('#pn_audit_trail').click(function () {
        var enc = $J('#encounter_nr').val();
        var pid = $J('#pid').val();
        var pageNotes = 'seg_progress_notes_audit_trail.php?pid='+pid;
        dialogInsurance = $J('<div></div>')
            .html('<iframe style="border: none;" src="' + pageNotes + '" width="100%" height="345px"></iframe>')
            .dialog({
                autoOpen: true,
                closeOnEscape: true,
                modal: true,
                show: 'fade',
                hide: 'fade',
                height: 'auto',
                width: '90%',
                title: 'Audit Trail',
                position: 'top'
            });
        return false;
    });

    jQuery(function($J){

        $J( "#pn_submit" ).button({text: true,icons: {primary: "ui-icon-disk"}});
        $J( "#pn_update" ).button({text: true,icons: {primary: "ui-icon-disk"}});
        $J( "#pn_audit_trail" ).button({text: true,icons: {primary: "ui-icon-folder-open"}});
        $J( "#pn_print" ).button({text: true,icons: {primary: "ui-icon-print"}});
        $J( "#view_prognote" ).button({text: true,icons: {primary: "ui-icon-print"}});

        window.parent.intakeFormData = $J('#progress_notes').serialize();
    
    });

function viewProgressNotes(pid){
    var pageProgressNotes = "social_service_progress_notes_view.php?pid="+pid;
    var dialogDiagnosis = jQuery('<div></div>')
        .html('<iframe style="border: 0px; " src="' + pageProgressNotes + '" width="100%" height="345px"></iframe>')
        .dialog({
          modal : true,
          show: 'fade',
          hide: 'fade',
          title : 'Progress Notes History',
          height: 'auto',
          width : '90%',
          position: 'top',
          closeOnEscape: true,
          autoOpen : true
        });
    }
    

</script>

<input type="hidden" name="root_path" id="root_path" value="<?=$root_path?>">
<input type="hidden" name="sid" id="sid" value="<?=URL_APPEND?>">

<?php
// var_dump($_SESSION);exit();


$datetime = date('F d, Y h:i a');

$datetime_text = '<div class="input text">
                    <div style="display:inline-block">
                        <input type="text" id="datetime" name="datetime" class="segInput" style="width:200px; font:bold 13px Arial" value="'.$datetime.'">
                        <br>
                        <span style="margin-left:2px; font:normal 10px Tahoma; color:#447BC4" class="small">[mm/dd/yyyy hh:mm]</span>
                    </div>
                    <button id="datetime-trigger" style="margin-left: 4px; cursor: pointer;" onclick="return false" title="Select Date and time">
                        <span class="icon calendar"></span>
                    </button>
                  </div>
                  ';

$smarty->assign('datetime', $datetime_text);

global $db;


$sql_cur_acc = "SELECT cw.`nr`,cw.`name`,ce.`pid` FROM care_encounter AS ce INNER JOIN care_ward AS cw  ON ce.`current_ward_nr` = cw.`nr` WHERE ce.`encounter_nr` = ".$db->qstr($encounter_nr);

// $cur_acc = $db->GetOne($sql_cur_acc);

// $sql_ward = "SELECT * FROM `care_ward` WHERE `is_temp_closed`=0 ORDER BY `name`";
$execute_sql_ward = $db->Execute($sql_cur_acc);

$ward_option = '<option value="">-Select Ward-</option>';
if(is_object($execute_sql_ward)){
  while($row_ward = $execute_sql_ward->FetchRow()){
      $selected = "selected";
      $ward_option.='<option '.$selected.' value="'.$row_ward['nr'].'">'.ucwords($row_ward['name']).'</option>';
      $pid = $row_ward['pid'];
      $ward_nr = $row_ward['nr'];
  }
}





$ward_selection = '<select name="ward_nr" id="ward_nr" class="segInput" style="width:300px; font:bold 13px Arial;" disabled>
                        '.$ward_option.'
                    </select>';

$smarty->assign('ward', $ward_selection);

$sql_diagnosis = "SELECT `er_opd_diagnosis` FROM `care_encounter` WHERE `encounter_nr` = ".$db->qstr($encounter_nr);
$diagnosis = $db->GetOne($sql_diagnosis);


// $smarty->assign('diagnosis', '<input class="segInput" id="diagnosis" name="diagnosis" type="text" value="'.$diagnosis.'" style="width:300px; font:bold 13px Arial;" readonly/>');
$smarty->assign('diagnosis', '<textarea class="segInput" id="diagnosis" name="diagnosis" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:13px; font-weight:bold; width:410px;" disabled>'.$diagnosis.'</textarea>');

$smarty->assign('referral', '<div class="segInput" style="width:300px; font:bold 13px Arial";>
                              <input type="radio" name="referral" id="external" value="external"/>External 
                            </div>');
$smarty->assign('internal', '<div class="segInput" style="width:300px; font:bold 13px Arial";>
                              <input type="radio" name="referral" id="internal" value="internal"/>Internal
                            </div>');

$smarty->assign('informant', '<input class="segInput" id="informant" name="informant" type="text" style="width:300px; font:bold 13px Arial;"/>');

$smarty->assign('reltopatient', '<input class="segInput" id="reltopatient" name="reltopatient" type="text" style="width:300px; font:bold 13px Arial;"/>');

$smarty->assign('purpose','<textarea class="segInput" id="purpose" name="purpose" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:13px; font-weight:bold; width:410px;"></textarea>');

$smarty->assign('action_taken','<textarea class="segInput" id="action_taken" name="action_taken" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:13px; font-weight:bold; width:410px;"></textarea>');

$smarty->assign('recommendation','<textarea class="segInput" id="recommendation" name="recommendation" cols="85" rows="3" wrap="physical" style="overflow-y:scroll; overflow-x:hidden; float:left; margin-left:5px; font-size:13px; font-weight:bold; width:410px;"></textarea>');
$smarty->assign('permission_all',$all_prog_notes);
$smarty->assign('permission_view',$v_prog_notes);
if($all_prog_notes && !($s_prog_notes || $p_prog_notes || $v_prog_notes || $d_prog_notes)){
  $disabled_save = '';
  $disabled_print='';
}
else{
  if(!$s_prog_notes){
    $disabled_save = disabled;
  }
  if(!$p_prog_notes){
    $disabled_print = disabled;
  }
  if(!($v_prog_notes ||  $d_prog_notes)){
    $disabled_view = disabled;
  }
}
// var_dump($root_path);exit()
$smarty->assign('pn_audit_trail', '<button id="pn_audit_trail" name="pn_audit_trail" class="segInput" type="submit" style="width: 150px; height: 30px;">Audit Trail</button>');

$smarty->assign('pn_update', '<button class="segInput" style="width: 150px; height: 30px; display:none;" id="pn_update" name="pn_update" onclick="UpdateProgressNotes();" >Update</button>');

$smarty->assign('pn_submit', '<input class="segInput" style="width: 150px; height: 30px;" id="pn_submit" name="pn_submit" onclick="saveprogressnotes();" type="button" value="Submit" '.$disabled_save.' />');

$smarty->assign('pn_print', '<input class="segInput" style="width: 150px; height: 30px;" id="pn_print" name="pn_print" type="button" onclick="dialogDate();" value="Print" '.$disabled_print.'/>');

$smarty->assign('progNotesbtn', '<input class="segInput" style="width: 150px; height: 30px;" id="view_prognote" name="view_prognote" type="button" onclick="viewProgressNotes(\''.$pid.'\');" value="View Progress Notes" '.$disabled_view.' />');

$datefrom_text = '<div class="input text">
                      <input type="text" style="margin-left: -100px" maxlength="10" size="10" id="datefrom" name="datefrom" class="segInput">
                      <button id="datefrom-trigger" style="margin-left: 4px; cursor: pointer;" onclick="return false" title="Select Start Date">
                      <span class="icon calendar"></span>
                              Select
                      </button>
                      <br>
                      <span style="margin-left:-100px; font:normal 10px Tahoma; color:#447BC4" class="small">[mm/dd/yyyy]</span>
                    
                  </div>
                  ';

$dateto_text = '<div class="input text">
                      <input type="text" style="margin-left: -100px" maxlength="10" size="10" id="dateto" name="dateto" class="segInput">
                      <button id="dateto-trigger" style="margin-left: 4px; cursor: pointer;" onclick="return false" title="Select End Date">
                      <span class="icon calendar"></span>
                              Select
                      </button>
                      <br>
                      <span style="margin-left:-100px; font:normal 10px Tahoma; color:#447BC4" class="small">[mm/dd/yyyy]</span>
                        
                 </div>
                 ';

$jsCalScript  = '<script type="text/javascript">
                    now = new Date();
                    Calendar.setup ({
                            inputField: "datefrom",
                            dateFormat: "'.$date_format2.'",
                            trigger: "datefrom-trigger",
                            showTime: false,
                            fdow: 0,
                            /*max : Calendar.dateToInt(now),*/
                            onSelect: function() { this.hide() }
                    });

                    Calendar.setup (
                    {
                            inputField: "dateto",
                            dateFormat: "'.$date_format2.'",
                            trigger: "dateto-trigger",
                            showTime: false,
                            fdow: 0,
                            /*max : Calendar.dateToInt(now),*/
                            onSelect: function() { this.hide() }
                    }
                    );
                </script>
                ';

$smarty->assign('jsCalendarSetup', $jsCalScript.'<input type="hidden" id="date_format" name="date_format" value="'.$date_format.'">');

$smarty->assign('datefrom_fld', $datefrom_text);
$smarty->assign('dateto_fld', $dateto_text);


$smarty->assign('med_social_worker', $HTTP_SESSION_VARS['sess_user_name']);
$hiddenInputs = '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$enc_nr.'">
                 <input type="hidden" name="pid" id="pid" value="'.$pid.'">
                <input type="hidden" name="medsocwork" id="medsocwork" value="'.$HTTP_SESSION_VARS['sess_user_name'].'">
                <input type="hidden" name="ward_nr" id="ward_nr" value="'.$ward_nr.'">
                <input type="hidden" name="save_prog_notes" id="save_prog_notes" value="'.$s_prog_notes.'">
                <input type="hidden" name="del_prog_notes" id="del_prog_notes" value="'.$d_prog_notes.'">
                <input type="hidden" name="print_prog_notes" id="print_prog_notes" value="'.$p_prog_notes.'">
                <input type="hidden" name="all_prog_notes" id="all_prog_notes" value="'.$all_prog_notes.'">
                 <input type="hidden" name="view_prog_notes" id="view_prog_notes" value="'.$v_prog_notes.'">
                 <input type="hidden" name="note_id" id="note_id" value="">
                
                ';

$xTemp = ob_get_contents();
ob_end_clean();
$smarty->assign('sHiddenInputs', $hiddenInputs);
$smarty->assign('sTailScripts', $xTemp);

$smarty->assign('sMainBlockIncludeFile','social_service/social_service_progress_notes.tpl');

$smarty->display('common/mainframe.tpl');

?>