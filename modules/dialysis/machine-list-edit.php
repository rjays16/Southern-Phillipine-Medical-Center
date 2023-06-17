<?
/*
  Created: Jayson-OJT - 2/13/2014
  Edit Patient Details User Interface
 */

error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . "include/care_api_classes/dialysis/class_dialysis.php");
require_once($root_path . "include/care_api_classes/dialysis/DialysisTransaction.php");
require_once($root_path . 'modules/dialysis/ajax/dialysis-transaction.common.php');

define('NO_2LEVEL_CHK', 1);
$local_user = 'ck_dialysis_user';

require_once($root_path . 'include/inc_front_chain_lang.php');
require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
require_once($root_path . 'include/inc_date_format_functions.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/inc_date_format_functions.php');

global $db;
$dialysis_obj = new SegDialysis();
$tnr = $_REQUEST['tnr'];
#Added by Aleeya

$encounter_nr = $_REQUEST['encounter_nr'];
$pid = $_REQUEST['pid'];
$from = $_REQUEST['from'];

// var_dump($from);exit;
$smarty = new Smarty_Care('common');
$input_date = $_REQUEST['service_dte'];
$input_year = substr($input_date, 0, 4);
$input_month = substr($input_date, 5, 2);
$input_day = substr($input_date, 8, 2);

$complete_date = $input_month . "/" . $input_day . "/" . $input_year;

if ($from == "billing") {
    $title = "Dialysis :: New Patient Details";
} else {
    $title = "Dialysis :: Edit Patient Details";
}

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);
$smarty->assign('bHideTitleBar', true);

# Window bar title
$smarty->assign('sWindowTitle', $title);

$phpfd = $date_format;
$phpfd = str_replace("dd", "%d", strtolower($phpfd));
$phpfd = str_replace("mm", "%m", strtolower($phpfd));
$phpfd = str_replace("yyyy", "%Y", strtolower($phpfd));
$pid = $_GET['pid'];

$classDialysisTransaction = new DialysisTransaction($tnr, $pid);
$personInfo = $classDialysisTransaction->getPersonInfo();

if($personInfo) {
    $smarty->assign('detailView', array(
        'Transaction No:' => $tnr,
        'HRN:' => $pid,
        'Name:' => $personInfo['name_last'] . ', ' . $personInfo['name_first'],
        'Date of Birth:' => $personInfo['date_birth'],
        'Sex:' => $personInfo['sex'] == 'm' ? 'Male' : 'Female',
        'Current Machine No:' => $classDialysisTransaction->machineNr,
    ));
}
if ($classDialysisTransaction->isPhicTrxn()) {
    $smarty->assign('is_phic', 'checked');
}
else{
    $smarty->assign('is_phic', '');
}

/**
 * if new transaction
 * else edit transaction
 */

$smarty->assign('hasReusableDialyzer',$classDialysisTransaction->hasReusableDialyzer()?1:0);

if($_GET['action'] == 'newmachine') {
    $smarty->assign('date_accom',  date("m/d/Y"));
    $smarty->assign('isUpdate', 0);
    $smarty->assign('serialNo', uniqid(''));
    $smarty->assign('timeHours', date('h'));
    $smarty->assign('timeMins', date('i'));
    $smarty->assign('meridiem', date('A'));
    
    /**
     * check dialyzer use
     */
    if($classDialysisTransaction->hasReusableDialyzer()) {
        $smarty->assign('currentSerialNo', $classDialysisTransaction->dialyzerSerialNr);
        $smarty->assign('dialyzerTypeHidden', 'style=display:none');
        $smarty->assign('currentDialyzerType', $classDialysisTransaction->dialyzerType);
        $smarty->assign('dialyzerType', $classDialysisTransaction->dialyzerType);
        $smarty->assign('noOfReuse', $classDialysisTransaction->dialyzerReuse);
        $smarty->assign('dialyzerTypeDisabled', 'disabled');
    } else {
        $smarty->assign('noOfReuse', 0);
        //$smarty->assign('disabled', 'disabled');
        $smarty->assign('checked', 'checked');
    }
} else {
    $smarty->assign('isUpdate', 1);
    $smarty->assign('currentSerialNo', $classDialysisTransaction->dialyzerSerialNr);
    $tnDate = $classDialysisTransaction->transactionDate;
    $smarty->assign('date_accom', $tnDate->format('m/d/Y'));
    $smarty->assign('timeHours', $tnDate->format('h'));
    $smarty->assign('timeMins', $tnDate->format('i'));
    $smarty->assign('meridiem', $tnDate->format('A'));
    $smarty->assign('dialyzerType', $classDialysisTransaction->dialyzerType);
    $smarty->assign('noOfReuse', $classDialysisTransaction->dialyzerReuse);
    $smarty->assign('machineNr', $classDialysisTransaction->machineNr);
    $smarty->assign('disabled', 'disabled');
    $smarty->assign('dialyzerTypeHidden', 'style=display:none');
}

?>

<!--<script type="text/javascript" src="--><?//= $root_path ?><!--js/jsprototype/prototype.js"></script>-->
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>

<script type="text/javascript" src="<?= $root_path ?>js/jquery/select2-3.5.3/select2.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/select2-3.5.3/select2.css" type="text/css"/>

<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>

<!--<script type="text/javascript" src="--><?//= $root_path ?><!--js/scriptaculous/scriptaculous.js?load=effects"></script>-->
<script type="text/javascript" src="<?= $root_path ?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
<script type="text/javascript" src="<?= $root_path ?>js/jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery-validation/additional-methods.min.js"></script>
<script type="text/javascript" src="<?= $root_path ?>modules/dialysis/js/dialyzer-validation.js"></script>
<script type="text/javascript">

/**
 * Apply jquery ui autocomplete to list of machine no
 */
function applyMachineNrAutocomplete() {

    $J( "#machine_nr" ).select2({
        asDropDownList : false,
        width : 300,
        id : function(data){ return data.machine_nr; },
        ajax : {
            url : '../../modules/dialysis/ajax/ajax_machine_list.php',
            dataType : 'json',
            delay : 250,
            data : function (term, page) {
                return {
                    term : term
                };
            },
            results: function (data, page) {
                return {
                    results: data
                };
            }
        },
        initSelection: function(element, callback) {            
            callback({
                machine_nr:"<?=$classDialysisTransaction->machineNr?>"
            });
        },
        escapeMarkup: function (markup) { return markup; },
        minimumInputLength: 1,
        formatResult: function(data, container, query){
            return '<span>' + data.machine_nr + '</span>';
        },
        formatSelection : function(data, container) { return data.machine_nr; }
    });
}
/**
 * If the dialyzer is new, display dropdown and default reuse.
 * If not display the no of reuse and dialyzer type.
 */
function applyCheckNewDialyzerBehavior() {
    $J('#new_dialyser_id').click(function () {
        var $this = $J(this);
        if ($J('#current_serial_nr').val() != '') {
            $J('#dialyzer_type').hide();
            $J('#plainDialyzerType').show();
            $J('#reuse').show();
            $J('#defaultReuse').hide();
        }
        if ($this.is(':checked')) {
            $J('#dialyzer_type').show();
            $J('#plainDialyzerType').hide();
            $J('#reuse').val(0);
        }else{
            if(!$J('#has-dialyzer').val())
                $J('#dialyzer_type').hide();
            
            $J('#plainDialyzerType').show();
        }
    });
}

var $J = jQuery.noConflict();
$J('document').ready(function () {
    applyMachineNrAutocomplete();
    applyCheckNewDialyzerBehavior();
});

/**
 * Save dialyzer transaction. Generates unique serial no if the dialyzer is new.
 *  Reuses the current serial no if the last transaction is valid.
 */
function saveMachine() {
    var tnr = $J('#tnr').val();
    var pid = $J('#pid').val();
    var machine = $J('#machine_nr').val();
    var isNew = $J('#new_dialyser_id').attr('checked') == 'checked';
    var isPhic = $J('#is_phic').attr('checked') == 'checked';
    var selectedEncounter = ("<?= $_GET['encounter_nr'] ?>") ? "<?= $_GET['encounter_nr'] ?>" : parent.getSelectedEncounter();

    var dialyzerTr = new Object();
    if(isNew) {
        dialyzerTr['serialNo'] = $J('#dialyzer_serial_nr').val();
    } else {
        dialyzerTr['serialNo'] = $J('#current_serial_nr').val();
    }
    dialyzerTr['hours'] = $J('#timefromHours').val();
    dialyzerTr['mins'] = $J('#timefromMins').val();
    dialyzerTr['date'] = $J('#datefrom').val();
    dialyzerTr['meridian'] = $J('#selAMPM').val();
    dialyzerTr['reusex'] = $J('#reuse').val();
    dialyzerTr['serviceCode'] = $J('#dialyzer_type').val();
    dialyzerTr['name'] = $J('#dialyzer_type option:selected').html();

    if ($J('#update').val() > 0) {
        dialyzerTr['entry_no'] = "<?= $_GET['entry_no'] ?>";
        xajax_savePatientDetails(tnr, pid, machine, dialyzerTr, isNew, 1,selectedEncounter, isPhic);
    } else {
        xajax_savePatientDetails(tnr, pid, machine, dialyzerTr, isNew, 0,selectedEncounter, isPhic);
    }
}

    function closeWindow() {
        <?php
        if (isset($_GET['action']) && $_GET['action'] == 'newmachine') {
            echo 'window.parent.refreshHistory();';
        } else {
            echo 'window.parent.location.href = window.parent.location.href;';
        }
        ?>
        window.parent.cClick();
    }

    function refreshWindow(){
        window.location.href = window.location.href;
    }

    function openRequestTray(encounter_nr,pid){ // to open Laboratory Request Tray, Encounter Nr and PID NEEDED!.
        overlib(
        OLiframeContent('<?= $root_path ?>modules/clinics/seg-clinic-charges.php<?php echo URL_REDIRECT_APPEND; ?>&pid='+pid+'&encounter_nr='+encounter_nr+"&userck=<?php echo $_GET['userck']; ?>&from=<?php echo $_GET['from']; ?>&checkintern=<?php echo $_GET['checkintern'] ?>",
        800, 450, 'fGroupTray', 0, 'auto'),
        WIDTH,800, TEXTPADDING,0, BORDER,0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 onclick="refreshWindow()">', //added refreshWindow() to update the current/(parent) Edit Patient Details overLib
        CAPTIONPADDING,2, CAPTION,'New Test Request',
        MIDX,0, MIDY,0,
        STATUS,'New Test Request');
        return false;
    }

</script>
<?
$xajax->printJavascript($root_path . 'classes/xajax_0.5');
$smarty->assign('QuickMenu', FALSE);
$smarty->assign('formAction', $root_path);
$smarty->assign('bHideCopyright', TRUE);
$jsCalScript = "<script type=\"text/javascript\">
						Calendar.setup ({
							inputField : \"datefrom\", ifFormat : \"$phpfd\", showsTime : false, button : \"datefrom_trigger\", singleClick : true, step : 1
						});
					</script>
					";
$smarty->assign('dialyzerList', $dialysis_obj->getDialyzerList());
$smarty->assign('jsCalendarSetup', $jsCalScript);
$smarty->assign('sDateMiniCalendar', '<img ' . createComIcon($root_path, 'show-calendar.gif', '0') . ' id="datefrom_trigger" align="absmiddle" style="cursor:pointer"> <font size=1>[' . strtolower($date_format) . ']</font>');

$smarty->assign('tnr', $_GET['tnr']);
$smarty->assign('pid', $_GET['pid']);
$smarty->assign('saveButtonImg', createLDImgSrc($root_path, 'savedisc.gif', '0'));
$smarty->assign('closeButtonImg', createLDImgSrc($root_path, 'close2.gif', '0'));
$smarty->assign('sMainBlockIncludeFile', 'dialysis/machine_transfer_patient.tpl');
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame