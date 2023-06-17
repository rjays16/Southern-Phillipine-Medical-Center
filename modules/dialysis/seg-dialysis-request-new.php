<?php

use \Segworks\HIS\Helpers\HtmlHelper;

error_reporting(E_COMPILE_ERROR | E_ERROR | E_CORE_ERROR);
require('./roots.php');
require_once($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'modules/dialysis/ajax/dialysis-transaction.common.php');

#added by art 03/16/2015 added all permission
require_once($root_path . 'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_temp_userid']);
$Billing_permission = $objAcl->checkPermissionRaw('_a_1_dialysisbilling');
$Request_permission = $objAcl->checkPermissionRaw('_a_1_dialysisrequest');
// $Discharge_permission = $objAcl->checkPermissionRaw('_a_1_dialysisdischarge');
// $Undischarge_permission = $objAcl->checkPermissionRaw('_a_1_dialysisundischarge');
$Manualpay_permission = $objAcl->checkPermissionRaw('_a_1_dialysismanualpay') ? 1 : 0;
$is_allowedbilling = $Billing_permission != 1 ? 'disabled title="no permission"' : '';
$is_allowedCostcenter = $Request_permission != 1 ? 'disabled title="no permission"' : '';
// $is_allowedDischarge = $Discharge_permission != 1 ? 'disabled title="no permission"' : '';
// $is_allowedUndischarge = $Undischarge_permission != 1 ? 'disabled title="no permission"' : '';
#end art


//require_once $root_path.'include/care_api_classes/dialysis/class_dialysis_request.php';

/**
 * CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
 * GNU General Public License
 * Copyright 2002,2003,2004,2005 Elpidio Latorilla
 * elpidio@care2x.org
 * See the file "copy_notice.txt" for the licence notice
 */
define('NO_2LEVEL_CHK', 1);
define('LANG_FILE', 'lab.php');

require_once $root_path . 'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG = array();

# Create global config object
require_once($root_path . 'include/care_api_classes/class_globalconfig.php');
require_once($root_path . 'include/inc_date_format_functions.php');
$glob_obj = new GlobalConfig($GLOBAL_CONFIG);
if ($glob_obj->getConfig('date_format'))
    $date_format = $GLOBAL_CONFIG['date_format'];
$date_format = $GLOBAL_CONFIG['date_format'];
$phpfd = $date_format;
$phpfd = str_replace("dd", "%d", strtolower($phpfd));
$phpfd = str_replace("mm", "%m", strtolower($phpfd));
$phpfd = str_replace("yyyy", "%Y", strtolower($phpfd));
$phpfd = str_replace("yy", "%y", strtolower($phpfd));

if (!isset($pid))
    $pid = 0;
if (!isset($encounter_nr))
    $encounter_nr = 0;

//$phpfd = config date format in PHP date() specification

if (!$_GET['from'])
    $breakfile = $root_path . "modules/dialysis/seg-dialysis-menu.php" . URL_APPEND;
else {
    if ($_GET['from'] == 'CLOSE_WINDOW')
        $breakfile = "javascript:window.parent.cClick();";
    else
        $breakfile = $root_path . "modules/dialysis/seg-dialysis-menu.php" . URL_APPEND;
}

$thisfile = 'seg-dialysis-request-new.php';

# Start Smarty templating here
/**
 * LOAD Smarty
 */
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme

require_once $root_path . "include/care_api_classes/dialysis/class_dialysis.php";
require_once $root_path . "include/care_api_classes/class_encounter.php";
$dialysis_obj = new SegDialysis();
$enc_obj = new Encounter($encounter_nr);
global $db;

require_once $root_path . 'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp', "javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile', $breakfile);
$title = "Dialysis :: New Request";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);
$smarty->assign('bHideCopyright', true);

include '../../frontend/bootstrap.php';

#save data here
if (isset($_POST["submitted"])) {
    try {
        //added by Kenneth Kempis 04/13/2018
        $is_released = 0;
        if(isset($_POST['printIndicator2']))   
        $is_released = $_POST['printIndicator2'];
        //end Kenneth Kempis 04/13/2018
        //use relational transaction
        $db->StartTrans();
        $enc_type = $db->GetOne("SELECT type_nr FROM care_type_encounter WHERE name LIKE 'Dialysis%' AND status <> 'deleted'");
        // get last encounter
        $last_enc = $enc_obj->getLastEncounterNr("dialysis");
        // get new encounter
        $new_encounter = $enc_obj->getNewEncounterNr($last_enc, $enc_type);

        /*        if ($_POST['labRequest'] == 1) {
                    $_POST['encounter_nr'] = $new_encounter;
                    //auto insert pre and post lab requests
                    $dialysis_obj->addLabRequest('PRE-HD', $_POST);
                    $dialysis_obj->addLabRequest('POST-HD', $_POST);
                }
        */
        // get dept no of dialysis
        $dept_nr = $db->GetOne("SELECT nr FROM care_department WHERE id LIKE 'Dialysis%' AND type='1' AND status <> 'deleted'");
        $bill_typeph = "PH";
        $bill_typenph = "NPH";
        $bill_typehdf = "HDF";

        $dialysis_data = array(
            'encounter_nr' => $new_encounter,
            'pid' => $_POST["pid"],
            'request_date' => $_POST["requestdate"],
            'requesting_doctor' => $_POST["request_doctor"],
            'attending_nurse' => $_POST["attending_nurse"],
            'remarks' => $_POST[""],
            
            'diagnosis' => $_POST["remarksreqdiagnosis"],
            'procedure' => $_POST["procedure"],
            'modify_id' => $_SESSION["sess_temp_userid"],
            'is_released' => $is_released,
        );

        $prebill_data_ph = array(
            'encounter_nr' => $new_encounter,
            'bill_type' => $bill_typeph,
            'amount' => !empty($_POST['PHamount']) ? $_POST['PHamount'] : 0,
            'hdf_amount' => !empty($_POST['HDFAmount']) ? $_POST['HDFAmount'] : 0,
            'subsidy_amount' => !empty($_POST['PH_subsidize_amount']) ? $_POST['PH_subsidize_amount'] : 0,
            'subsidy_class' => !empty($_POST['PH_subsidize_class']) ? $_POST['PH_subsidize_class'] : 0
        );

        $prebill_data_nph = array(
            'encounter_nr' => $new_encounter,
            'bill_type' => $bill_typenph,
            'amount' => !empty($_POST['NPHamount']) ? $_POST['NPHamount'] : 0,
            'hdf_amount' => !empty($_POST['HDFAmountNPH']) ? $_POST['HDFAmountNPH'] : 0,
            'subsidy_amount' => !empty($_POST['NPH_subsidize_amount']) ? $_POST['NPH_subsidize_amount'] : 0,
            'subsidy_class' => !empty($_POST['NPH_subsidize_class']) ? $_POST['NPH_subsidize_class'] : 0
        );

        if ($_POST["request_doctor"])
            $doctorDepartment = $db->GetOne("SELECT location_nr FROM care_personell_assignment WHERE personell_nr = ?", $_POST["request_doctor"]);

        $encounter_data = array(
            'encounter_nr' => $new_encounter,
            'encounter_type' => $enc_type,
            'er_opd_diagnosis' => $_POST["reqdiagnosis"],//added by Nick 05-13-2014 - reflect diagnosis in billing
            'current_att_dr_nr' => $_POST["request_doctor"],
            // 'consulting_dept_nr'=>$_POST["request_doctor"],
            'encounter_date' => $_POST["requestdate"],
            'pid' => $_POST["pid"],
            'admission_dt' => date('Y-m-d H:i:s', strtotime($_POST['requestdate'])),
            'current_dept_nr' => $doctorDepartment,
            'encounter_class_nr' => $enc_type,
            'encounter_status' => '',
            'current_ward_nr' => 0,
            'current_room_nr' => 0,
            'create_id' => $_SESSION["sess_temp_userid"],
            'create_time' => date('Y-m-d H:i:s'),
            'modify_id' => $_SESSION["sess_temp_userid"],
            'modify_date' => date('Y-m-d H:i:s'),
            'history' => 'Create: ' . date('Y-m-d H:i:s') . ' = ' . $_SESSION["sess_temp_userid"]
        );
        //save encounter and add new dialysis request
        $saveok = $dialysis_obj->saveTransaction($encounter_data, $dialysis_data, $new_encounter);
        //insert new prebill
        $saveyes = $dialysis_obj->insertNewRequest($prebill_data_ph, $prebill_data_nph);
        // var_dump($dialysis_data);
        if ($saveok && $saveyes) {
            $smarty->assign('sysInfoMessage', 'Dialysis transaction successfully submitted.');
        } else {
            $db->FailTrans();
            $smarty->assign('sysErrorMessage', '<strong>Error:</strong> Cannot save dialysis transactions.<br/> SQL_ERROR:' . $dialysis_obj->getErrorMsg());
        }
        $db->CompleteTrans();
        //if successful redirect to clear post data
        //header('Location:' . $root_path. 'modules/dialysis/seg-dialysis-request-new.php');
        if ($_POST['labRequest'] == 1) {
            $_POST['encounter_nr'] = $new_encounter;
            //auto insert pre and post lab requests
            $dialysis_obj->addLabRequest('PRE-HD', $_POST);
            $dialysis_obj->addLabRequest('POST-HD', $_POST);
        }


        #added by KENTOOT 09-15-2014
        //get patient encounter where encounter type is in Dialysis
        $dialysis_encounter = $enc_obj->getDialysisEncounter($pid);
        $count = 0;
        while ($row = $dialysis_encounter->FetchRow()) {
            $old_nr = $row['encounter_nr'];
            $is_final = $row['is_final'];

            //get dialysis unused prebill
            #$count = 0;
            $unused_prebill = $dialysis_obj->getUnusedDialysisPrebill($old_nr, $is_final);
            while ($row2 = $unused_prebill->FetchRow()) {
                #$count++;
                if (is_null($row2['transaction_date']) && $row2['is_discharged'] == '1') {
                    #$smarty->assign('alertPrebill', $count.' Unused paid sessions are transfered into new transaction.');
                    $update_prebill = $dialysis_obj->updateDialysisPrebill($new_encounter, $old_nr, $is_final);
                    $count += $db->Affected_Rows();
                }
            }
        }
        $smarty->assign('alertPrebill', $count . ' Unused paid sessions are transfered into new transaction.');
    } catch (Exception $e) {
        $db->FailTrans();
    }
}

# Collect javascript code
ob_start();
# Load the javascript code
?>
    <link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css"/>
    <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
    <link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>
    <script language="javascript" src="<?= $root_path ?>js/setdatetime.js"></script>
    <script language="javascript" src="<?= $root_path ?>js/checkdate.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>

    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/iframecontentmws.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/ajaxcontentmws.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_modal.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_filter.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_shadow.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_scroll.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_draggable.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/overlibmws/overlibmws_overtwo.js"></script>


    <script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type="text/javascript">
        var $j = jQuery.noConflict();
    </script>

    <script type="text/javascript" src="<?= $root_path ?>js/jsprototype/prototype.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/scriptaculous/scriptaculous.js?load=effects"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/seg_utils.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/NumberFormat154.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/listgen/listgen.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jquery-validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>js/jquery-validation/additional-methods.min.js"></script>
    <script type="text/javascript" src="<?= $root_path ?>modules/dialysis/js/request-main-validation.js?ver=1.0"></script>
    <script>

        function markAsReleased(enc_number)
        {

                var enc = $j('#dp_encounter').val();
                var selected_encounter = enc.substr(0, 10);
                $j('#prntIndctr').change(function() {
                    if($j(this).is(":checked")) {
                        xajax_ajxsetIsPrinted(selected_encounter,1);
                        $j(this).attr("checked", true);
                    }
                    else
                        xajax_ajxsetIsPrinted(selected_encounter,0);    
                });
          
        }

        function getIsPrinted(val)
        {
            $('printedIndicator').style.display = "";
            if(val == 1)
                $j("#prntIndctr").attr('checked',"");
            else
                $j("#prntIndctr").removeAttr('checked');
        }

        function hideIsPrinted()
        {
            $('printedIndicator').style.display = "none";
        }

        function printScheduleHistory() {
            var encounterNr = $j('#dp_encounter option:selected').val();
            if (encounterNr == 0) {
                alert('Please select a case #');
                return false;
            }
            var url = '../../index.php?r=dialysis/dialysis/printTransactionHistory/caseNr/'+encounterNr;
            window.open(url, "Billing", "toolbar=no, status=no, menubar=no, width=800, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top="+screen.height/2-250+",left="+screen.width/2-400);
        }

        function isInt(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            return !(charCode > 31 && (charCode < 48 || charCode > 57));
        }

        function resetControls() {
            $j('#pid').val('');
            $j('#name').val('');
            $j('#address').val('');
            $j('#age').val('');
            $j('#birthdate').val('');
            $j('#gender').val('');
            $j('#civil_status').val('');
            openPatientSelect();
        }

        function openRequestTray(encounter_nr, pid) {
            overlib(
                OLiframeContent('<?= $root_path ?>modules/dialysis/seg-dialysis-request-window.php?pid=' + pid + '&encounter_nr=' + encounter_nr,
                    800, 500, 'fGroupTray', 0, 'auto'),
                WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
                CAPTIONPADDING, 2, CAPTION, 'New Test Request',
                MIDX, 0, MIDY, 0,
                STATUS, 'New Test Request');
            return false;
            //urlholder="<?php echo $root_path ?>"+"modules/dialysis/seg-dialysis-request-window.php?pid="+pid+"&encounter_nr="+encounter_nr;
            //window.open(urlholder,"dienstinfo","width=800,height=600,menubar=no,resizable=yes,scrollbars=yes");
        }

        function openBillingTray() {
            var selected_encounter = $j('#dp_encounter').val();
            if (selected_encounter == 0 || selected_encounter == '') {
                alert('Select an Encounter Number');
                return;
            }
            var pid = $('pid').value;
            xajax_getPreviousDiag(pid,selected_encounter);
            var url = '../../modules/billing_new/billing-main-new.php<?= URL_REDIRECT_APPEND ?>&popUp=1&area=DIALYSIS&pid=' + pid + '&enc_nr=' + selected_encounter;
            w = screen.width * 0.7;
            h = screen.height * 0.7;
            x = (screen.width - w) / 2;
            y = (screen.height - h) / 2;
            printwin = window.open(url, "Billing", "toolbar=no, status=no, menubar=no, width=" + w + ", height=" + h + ", location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + y + ",left=" + x);
            var pollTimer = window.setInterval(function () {
                if (printwin.closed !== false) { // !== is required for compatibility with Opera
                    window.clearInterval(pollTimer);
                    xajax_getDischargeFlag(selected_encounter);
                    refreshHistory();
                }
            }, 200);
        }


        //added By Keith
        //March 4, 2014

        function requestByDate() {
            var seltabs = $J('#tabs').tabs();
            var selected = seltabs.tabs('option', 'selected')
            initializeTab(selected);
        }

        //added By Keith
        //March 4, 2014

        // function openLabRequest()
        // {
        //  var selected_encounter = $('selected_encounter').value ;
        //  var pid = $('pid').value;
        //  // var area = $('area_type').value;
        //  // alert(area);
        //  // alert(pid);
        //  return overlib(
        //      OLiframeContent('<?= $root_path ?>modules/laboratory/seg-lab-request-new.php?sid=<?php echo "$sid&lang=$lang" ?>&clear_ck_sid=<?php echo "$clear_ck_sid" ?>&popUp=1&area=dialysis&area_type=<?= $area_type ?>&pid='+pid+'&encounter_nr='+selected_encounter+'&dr_nr=<?= $dr_nr ?>&dept_nr=<?= $dept_nr ?>&user_origin=lab&ischecklist=1&ptype=<?= $ptype ?>',
        //          800, 370, 'fGroupTray', 0, 'auto'),
        //      WIDTH,410, TEXTPADDING,0, BORDER,0,
        //      STICKY, SCROLL, CLOSECLICK, MODAL,
        //      CLOSETEXT, '<img src="<?= $root_path ?>/images/close.gif" border=0 onclick="requestByDate();">',
        //      CAPTIONPADDING,2, CAPTION,'Laboratory Request',
        //      MIDX,0, MIDY,0,
        //      STATUS,'Laboratory Request');
        // }

        function check() {
            var selected_encounter = $('selected_encounter').value;

            if (!selected_encounter || selected_encounter == null || selected_encounter == 0) {
                // document.getElementById('req').disabled=false;
                alert('Select an Encounter Number');
            }
            else {
                openRequest();
            }

        }

        // function discharge()
        // {
        //     var selected_encounter = $('selected_encounter').value ;

        //     if( !selected_encounter || selected_encounter==null || selected_encounter==0 ) {
        //         alert('Select an Encounter Number');
        //     } else{
        //         xajax_disableDialysisEncounter(selected_encounter);
        //         alert('Dialysis Encounter has been disabled');
        //     }
        // }

        // function undischarge()
        // {
        //     var selected_encounter = $('selected_encounter').value ;

        //     if( !selected_encounter || selected_encounter==null || selected_encounter==0 )
        //     {
        //         alert('Select an Encounter Number');
        //     }
        //     else{
        //         xajax_enableDialysisEncounter(selected_encounter);
        //         alert('Dialysis Encounter has been enabled');
        //     }
        // }


        function disabledButtons() {
            $j("#req").prop("disabled", true);
            //$j("#printBills").prop("disabled",true);
            $j("#viewBillButton").prop("disabled", true);
            // $j("#dischargeButton").hide();
            // $j("#undischargeButton").show();
            $j("#prntsoabtn").hide();
            /*        document.getElementById("req").disabled = true;
             document.getElementById("printBills").disabled = true;
             document.getElementById("viewBillButton").disabled = true;
             // document.getElementById("dischargeButton").disabled = true;
             document.getElementById("dischargeButton").style.display = "none";
             document.getElementById("undischargeButton").style.display = "";*/

        }

        function enabledButtons() {
            document.getElementById("req").disabled = false;
            //document.getElementById("printBills").disabled = false;
            document.getElementById("viewBillButton").disabled = false;
            // document.getElementById("dischargeButton").style.display = "";
            // document.getElementById("undischargeButton").style.display = "none";
            $j("#prntsoabtn").show();

        }


        // function Disabled(){
        //      var b=document.getElementById('req').value);
        //      var selected_encounter = $('selected_encounter').value ;

        //      alert(selected_encounter);
        //      // document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
        //      if (selected_encounter != NULL){document.getElementById("req").disabled = false;}
        // }

        function openRequest() { // to open Laboratory Request Tray, Encounter Nr and PID NEEDED!.

            var selected_encounter = $('selected_encounter').value;
            var pid = $('pid').value;

            overlib(
                OLiframeContent('<?= $root_path ?>modules/clinics/seg-clinic-charges.php<?php echo URL_REDIRECT_APPEND; ?>&pid=' + pid + '&encounter_nr=' + selected_encounter + "&userck=<?php echo $_GET['userck']; ?>&from=newrequest&checkintern=<?php echo $_GET['checkintern'] ?>&user_origin=<?php echo $_GET['userck']; ?>",
                    900, 550, 'fGroupTray', 0, 'auto'),
                WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
                CAPTIONPADDING, 2, CAPTION, 'New Test Request',
                MIDX, 0, MIDY, 0,
                STATUS, 'New Test Request');
            return false;
        }
        //Added by Aleeya
        //Open Machine Assignment Pop-up
        function openAssignMachineTray(transaction_nr, encounter_nr, pid) {
            // alert(transaction_nr + ' ' +encounter_nr+ ' '+ pid);
            //'machine-list-edit.php<?php echo URL_REDIRECT_APPEND; ?>&tnr='+transaction_nr+'&from=billing&encounter_nr='+encounter_nr+'&pid='+pid+"&userck=<?php echo $_GET['userck']; ?>&action=newmachine"
            var url = '../../index.php?r=dialysis/dialysisTransaction/makeTransaction/transactionNr/' + transaction_nr;
            $j('#dialog-frame').prop('src', url);
            $j('#frame-dialog').dialog({
                modal: true,
                width: 900,
                height: 500,
                position: 'top',
                title: 'Transaction',
                beforeClose: refreshHistory
            });
//        overlib(
//        OLiframeContent(url,
//        900, 500, 'fGroupTray', 0, 'auto'),
//        WIDTH,800, TEXTPADDING,0, BORDER,0,
//        STICKY, SCROLL, CLOSECLICK, MODAL,
//        CLOSETEXT, '<img src=<?//= $root_path ?>///images/close_red.gif border=0 >',
//        CAPTIONPADDING,2, CAPTION,'New Patient Details',
//        MIDX,0, MIDY,0,
//        STATUS,'New Patient Details');
//        return false;
        }

        function addTrxn(encounter_nr) {
            overlib(
                OLiframeContent('add-trxn.php<?php echo URL_REDIRECT_APPEND; ?>&from=billing&encounter_nr=' + encounter_nr + "&userck=<?php echo $_GET['userck']; ?>",
                    800, 450, 'fGroupTray', 0, 'auto'),
                WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
                CAPTIONPADDING, 2, CAPTION, 'Add new transaction',
                MIDX, 0, MIDY, 0,
                STATUS, 'Add new transaction');
            return false;
        }

        function appendTbl_add_new_trxn(str) {
            var enc = $j('#dp_encounter').val();
            var Manualpay_permission = '<?=$Manualpay_permission?>';
            if (enc != 0) {
                $j('#dialysis_test-dataTable > tr:last').after(str);
                $j('#dialysis_test-dataTable-empty tr:last').after(str);
                $j('#dialysis_test-dataRow-empty').remove();
            }
            // if($j('#undischargeButton').is(':visible')) {
            //     $j("#addbtn").prop("disabled",true);
            // }
            if (Manualpay_permission == 0) {
                $j("#addbtn").prop("disabled", true);
                $j('#addbtn').prop('title', 'No permission');
            }
        }

        function initialize() {
            ListGen.create($('billing_list'), {
                id: 'dialysis_test',
                url: '<?= $root_path ?>modules/dialysis/ajax/ajax_dialysis_billing_list.php',
                params: {'pid': $('pid').value, 'selected_encounter': $('selected_encounter').value},
                width: 850,
                height: 'auto',
                autoLoad: true,
                columnModel: [
                    // {
                    //  name: 'check',
                    //  label: 'flag',
                    //  width: 35,
                    //  sortable: false
                    // },
                    {
                        name: 'trn_no',
                        label: 'Transaction No.',
                        width: 100,
                        sorting: ListGen.SORTING.asc,
                        sortable: true
                    },
                    {
                        name: 'bill_type',
                        label: 'Bill Type',
                        width: 60,
                        sortable: false
                    },
                    {
                        name: 'status',
                        label: 'Status',
                        width: 65,
                        sortable: false
                    },
                    {
                        name: 'orNo',
                        label: 'OR No.',
                        width: 75,
                        sorting: ListGen.SORTING.asc,
                        sortable: true
                    },
                    {
                        name: 'details',
                        label: 'Payment Details',
                        width: 225,
                        sortable: false
                    },
                    // {
                    //     name: 'lingap',
                    //     label: 'LINGAP',
                    //     width: 75,
                    //     sorting: ListGen.SORTING.asc,
                    //     sortable: true
                    // },
                    // {
                    //     name: 'cmap',
                    //     label: 'CMAP',
                    //     width: 75,
                    //     sorting: ListGen.SORTING.asc,
                    //     sortable: true
                    // },
                    // {
                    //  name: 'additional',
                    //  label: 'Additional',
                    //  width: 80,
                    //  sorting: ListGen.SORTING.asc,
                    //  sortable: true
                    // },
                    {
                        name: 'totalbill',
                        label: 'Total Billed',
                        width: 75,
                        sorting: ListGen.SORTING.asc,
                        sortable: true
                    },
                    {
                        name: 'dateVisited',
                        label: 'Date Visited',
                        width: 100,
                        sorting: ListGen.SORTING.asc,
                        sortable: true
                    },
                    {
                        name: 'options',
                        label: 'Options',
                        width: 250,
                        sortable: false
                    }
                ]
            });
            //openPatientSelect();
            xajax_getDoctors("",$('pid').value,"<?=$_GET['target']?>");
            xajax_getNurses();
            xajax_setVisitNo($('pid').value);
            xajax_showEncounterByPid($('pid').value);
            getPrevTrn();
        }

        function setVisitNo(nr) {
            xajax_getPreviousRequest($('pid').value);
            xajax_setVisitNo($('pid').value);
            xajax_showEncounterByPid($('pid').value);
            xajax_getDoctors("",$('pid').value,"<?=$_GET['target']?>");
            var encounter_nr = $('selected_encounter').value;
            var list_a = $('billing_list').list.params = {
                'pid': $('pid').value,
                'selected_encounter': $('selected_encounter').value
            };
            var list_b = $('billing_list').list.refresh();
            $j.when(list_a, list_b).done(function () {
                xajax_appendTbl_add_new_trxn_ajx(encounter_nr);
            });
        }

        function openPatientSelect() {
            if ($('select-enc').hasClassName('disabled')) return false;
            <?php
            $var_arr = array(
                "var_pid" => "pid",
                "var_encounter_nr" => "encounter_nr",
                "var_name" => "name",
                "var_addr" => 'address',
                "var_clear" => "clear-enc",
                "var_include_walkin" => "0",
                "var_reg_walkin" => "0",
                "var_age" => "age",
                "var_gender" => "gender",
                "var_adm_diagnosis" => "diagnosis",
                "var_enctype" => "patient_type",
                "var_enctype_show" => "1",
                "var_type" => "encounter_type",
                "var_date_admitted" => "admission_date",
                "var_location" => "location",
                "var_dob" => "birthdate",
                "var_civil_status" => "civil_status",
                //"var_photo_filename"=>"photo_row"
                "var_photo_filename" => "headpic"
            );

            $vas = array();
            foreach ($var_arr as $i => $v) {
                $vars[] = "$i=$v";
            }
            $var_qry = implode("&", $vars);
            ?>
            overlib(
                OLiframeContent('seg-dialysis-search-person.php?<?= $var_qry ?>&var_include_enc=0&from_dialysis=1',
                    700, 400, 'fSelEnc', 0, 'no'),
                WIDTH, 700, TEXTPADDING, 0, BORDER, 0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
                CAPTIONPADDING, 2,
                CAPTION, 'Select registered person',
                MIDX, 0, MIDY, 0,
                STATUS, 'Select registered person');

            return false;
        }

        function changeStatus(id, refno, enc_nr) {
            if ($(id).value == "1") {
                var answer = confirm("Performing this action will disable any requests for Reference #" + refno + ". Continue?")
                if (answer) {
                    xajax_changeTransactionStatus(refno, $(id).value, "", enc_nr);
                }
            } else {
                var reason = prompt("Please log the reason to UNDONE request.");
                if (reason) {
                    xajax_changeTransactionStatus(refno, $(id).value, reason, enc_nr);
                }
            }
        }

        function refreshHistory(isNewlyAdded = true) {
            //isNewlyAdded ->true if refreshHistory is called from add new transaction
            //isNewlyAdded ->false if refreshHistory is called from refresh button
            var unpaid = 0;
            var pid = $('pid').value;
            var encounter_nr = $('selected_encounter').value;
            var list_a = $('billing_list').list.params = {
                'pid': $('pid').value,
                'selected_encounter': $('selected_encounter').value
            };
            var list_b = $('billing_list').list.refresh();
            $j.when(list_a, list_b).done(function () {
                //workaround to reload add button
                if(!document.getElementById('dialysis_test-dataTable-empty') &&
                    !document.getElementById('dialysis_test-dataRow-empty') && !isNewlyAdded){
                    alert('List reloaded. Click OK to close ');
                }
            xajax_appendTbl_add_new_trxn_ajx(encounter_nr);
            });

        }
     
        function deleteRequest(enc_nr, pid, refno) {
            var answer = confirm("Performing this action will disable any requests for Reference #" + refno + ". Continue?")
            if (answer) {
                xajax_deleteDialysisRequest(enc_nr, pid, refno);
            }
        }

        function selectEncounter() {
            var enc = $('dp_encounter').value;
            var selected_encounter = enc.substr(0, 10);
            xajax_getDischargeFlag(selected_encounter);
            xajax_ajxgetIsPrinted(selected_encounter);
            document.getElementById('selected_encounter').value = selected_encounter;
            var list_a = $('billing_list').list.params = {
                'pid': $('pid').value,
                'selected_encounter': selected_encounter
            };
            var list_b = $('billing_list').list.refresh();
            $j.when(list_a, list_b).done(function () {
                xajax_appendTbl_add_new_trxn_ajx(enc);
            });
        }


        function printDlg(enc) {
            window.open("reports/soa.php?enc=" + enc, "TEST", "width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
        }

        function printsoa() {
            var enc = $j('#dp_encounter').val();
            if (enc == 0) {
                alert('no encounter Number selected!');
                $j('#dp_encounter').focus();
            } else {
                window.open("reports/soa.php?enc=" + enc, "TEST", "width=720,height=500,menubar=no,resizable=no,scrollbars=yes");
            }

        }
        function openHistoryReport() {
            if ($('pid').value == "") {
                alert("Please select a patient first.");
                $('name').focus();
                return false;
            } else {
                window.open('seg-dialysis-history-report.php?pid=' + $('pid').value, 'history_report', "width=800,height=600,menubar=no,resizable=yes,scrollbars=no");
            }
        }

        function getSelectedEncounter() {
            return $('dp_encounter').value;
        }

        document.observe('dom:loaded', initialize);
    </script>

<?php
$xajax->printJavascript($root_path . 'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript', $sTemp);

$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
$curDate = date($dbtime_format);
$curDate_show = date($fulltime_format);

if (!isset($_POST["pid"])) {
    $smarty->assign('sOnLoadJs', "onLoad=openPatientSelect();return false");
} else {
    $smarty->assign('print', 'printDlg(' . $new_encounter . ')');
}

$smarty->assign('sSelectEnc', '<button id="select-enc" class="button" onclick="openPatientSelect();return false"><img ' . createComIcon($root_path, 'user.png') . ' />Select</button>');
$smarty->assign('sClearEnc', '<input class="segButton" id="clear-enc" type="button" value="Reset" disabled="disabled" onclick="if (confirm(\'Search for another patient?\')) resetControls()"/>');
//keith
//added onclick in submitBtn by matsuu 02242017
$smarty->assign('submitBtn', '<button class="segButton" onClick="getPrevTrn();"' . $is_allowedbilling . '><img src="' . $root_path . 'gui/img/common/default/printer.png"/>Submit and Print Pre-Bills</button>');
$smarty->assign('cancelBtn', '<button class="segButton" onclick="return false;"><img src="' . $root_path . 'gui/img/common/default/cancel.png"/>Cancel</button>');
#$smarty->assign('historyBtn', '<button class="segButton" id="printBills" name="printBills" onclick="openHistoryReport();return false;"><img src="' . $root_path . 'gui/img/common/default/report.png"/>Print Bills</button>');
//$smarty->assign('requestBtn','<button class="segButton" onclick="openRequestTray();return false;"><img src="'.$root_path.'gui/img/common/default/report.png"/>Request</button>');
$smarty->assign('detailsBtn', '<button class="segButton" onclick="return false;"><img src="' . $root_path . 'gui/img/common/default/book_open.png"/>Details</button>');

$smarty->assign('patientEncounter', '<select class="segInput" id="dp_encounter" name="dp_encounter" onChange="selectEncounter()" style="font:bold 12px Arial"></select>');

$smarty->assign('sPatientEncNr', '<input type="hidden" id="encounter_nr" name="encounter_nr" type="text" value="' . $_POST["encounter_nr"] . '"/>');
$smarty->assign('sPatientID', '<input id="pid" name="pid" class="clear" type="text" value="' . $_POST["pid"] . '" readonly="readonly" style="color:#006600; font:bold 16px Arial;"/>');
$smarty->assign('sPatientName', '<input class="segInput" id="name" name="name" type="text" size="30" style="font:bold 12px Arial; color:#0000ff" readonly="readonly" value="' . $_POST["name"] . '"/>');
$smarty->assign('sPatientAge', '<input class="segInput" id="age" name="age" type="text" size="15" style="font:bold 12px Arial;" readonly="readonly" value="' . $_POST["age"] . '"/>');
$smarty->assign('sPatientBirthday', '<input class="segInput" id="birthdate" name="birthdate" type="text" size="20" style="font:bold 12px Arial;" readonly="readonly" value="' . $_POST["birthdate"] . '"/>');
$smarty->assign('sPatientGender', '<input class="segInput" id="gender" name="gender" type="text" size="15" style="font:bold 12px Arial;" readonly="readonly" value="' . $_POST["gender"] . '"/>');
$smarty->assign('sPatientStatus', '<input class="segInput" id="civil_status" name="civil_status" type="text" size="17" style="font:bold 12px Arial;" readonly="readonly" value="' . $_POST["civil_status"] . '"/>');
$smarty->assign('sAddress', '<textarea class="segInput" id="address" name="address" style="width:70%;font:bold 12px Arial;border:1px solid #c3c3c3; overflow-y:scroll; float:left;" readonly="readonly">' . $_POST["address"] . '</textarea>');

$smarty->assign('sPatientDiagnosis', '<textarea class="segInput" id="diagnosis" name="diagnosis" style="font:bold 12px Arial; width:95%; border:1px solid #c3c3c3; overflow-y:scroll; float:left;" readonly="readonly">' . $_POST["diagnosis"] . '</textarea>');
$smarty->assign('sPatientLocation', '<input class="segInput" id="location" name="location" type="text" size="45" style="font:bold 12px Arial" readonly="readonly" value="' . $_POST["location"] . '"/>');
$smarty->assign('sPatientAdmissionDate', '<input class="segInput" id="admission_date" name="admission_date" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="' . $_POST["admission_date"] . '"/>');
$smarty->assign('sPatientDischargeDate', '<input class="segInput" id="discharge_date" name="discharge_date" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="' . $_POST["discharge_date"] . '"/>');
$smarty->assign('sPatientType', '<input class="segInput" id="patient_type" name="patient_type" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="' . $_POST["patient_type"] . '"/>');

/* $new_refno = $dialysis_obj->getNewRefno();
  $refno = $_POST["reference_no"] ? $_POST["reference_no"] : $new_refno;
  $smarty->assign('requestReferenceNo', '<input class="segInput" id="reference_no" name="reference_no" type="text" size="30" style="font:bold 12px Arial" readonly="readonly" value="'.$refno.'"/>'); */

/* $new_visit_no = 0;
  $visit_no = $_POST["visit_no"] ? $_POST["visit_no"] : $new_visit_no;
  $smarty->assign('requestVisitNo', '<input class="clear" id="visit_no" name="visit_no" type="text" style="font:bold 12px Arial" readonly="readonly" value="'.$visit_no.'"/>');
  $smarty->assign('visit_number', '<input class="clear" id="visit_number" name="visit_number" type="text" style="font:bold 12px Arial" readonly="readonly" value="'.$visit_no.'"/>');
 */

$smarty->assign('requestDoctors', '<select class="segInput" id="request_doctor" name="request_doctor" style="font:bold 12px Arial"></select>');
$smarty->assign('requestNurses', '<select class="segInput" id="attending_nurse" name="attending_nurse" style="font:bold 12px Arial"></select>');
/* $smarty->assign('requestDialysisType',
  '<input type="radio" id="dtypeb" name="dialysis_type" checked="checked" value="before"/><label>Before Dialysis</label>&nbsp;&nbsp;
  <input type="radio" id="dtypea" name="dialysis_type" value="after"/><label>After Dialysis</label>
  '); */
//$smarty->assign('dialysis_type', '<input type="hidden" id="dialysis_type" name="dialysis_type" value="";/>');
$smarty->assign('requestRemarks', '<textarea class="segInput" id="remarks" name="remarks" style="width:100%"></textarea>');
$encoder = $_POST["request_encoder"] ? $_POST["request_encoder"] : $_SESSION["sess_user_name"];
$smarty->assign('requestEncoder', '<input class="clear" id="request_encoder" name="request_encoder" type="text" size="30" style="font:bold 12px Arial" readonly value="' . $encoder . '"/>');


$previousDiagnosis = SegDialysis::getPreviousDiagnosis($_POST["pid"]);//added by Nick 8-1-2015

$smarty->assign('requestDiagnosis', '<input type="text" class="segInput" id="reqdiagnosis" name="reqdiagnosis" style="width:100%" value="' . $previousDiagnosis . '">');


$smarty->assign('requestProcedure', '<input type="text"  class="segInput" id="procedure" name="procedure" style="width:100%">');
$smarty->assign('requestStatus', '<select class="segInput" id="request_status" name="request_status">
                <option value="undone">Undone</option>
                <option value="done">Done</option>
                </select>
                ');

$smarty->assign('requestBillTypePH', 'PhilHealth');

$smarty->assign('requestBillTypeNPH', 'Non-PhilHealth');
// Added by Matsuu 01072017
// edited by Matsuu 02242017
$previousTransaction = new SegDialysis;
$PrevTrans = $previousTransaction->getPHICTransaction($_POST["pid"],$_POST["limitcopy"]);
$phquantityd = $PrevTrans['ph_type'];
$phamountd = $PrevTrans['ph_amount'];
$nphquantityd = $PrevTrans['nph_type'];
$nphamountd = $PrevTrans['nph_amount'];
$hdfamountd = $PrevTrans['ph_hdf'];
$hdfamountdnph = $PrevTrans['nph_hdf'];
$subsidyValue = $PrevTrans['sub_amount'];
$subsidyClass = $PrevTrans['sub_class'];
$subsidyNValue = $PrevTrans['n_sub_amount'];
$subsidyNClass = $PrevTrans['n_sub_class'];

//Ended by Matsuu 01072017
$smarty->assign('requestQuantityPH', '<input type="text"  class="segInput" id="PHquantity" value="'.$phquantityd.'" name="PHquantity" style="width:80%">');
// TRY Check Amount
$smarty->assign('requestAmountPH', '<input type="text"  class="segInput" id="PHamount" value="'.$phamountd.'" name="PHamount" style="width:80%">');
$smarty->assign('printIndicator','<input type="checkbox" onClick="markAsReleased('.$cur_enc_number.')" value="4" class="segInput" id="prntIndctr" name="printIndicator">');
$smarty->assign('printIndicator2','<input type="checkbox" style="margin-left:15px" value="1" class="segInput" id="prntIndctr2" name="printIndicator2" >');
$smarty->assign('requestAmountHDF','<input type="text"  class="segInput" id="HDFAmount"  value="'.$hdfamountd.'" name="HDFAmount" style="width:80%">');
$smarty->assign('requestAmountHDFNPH','<input type="text"  class="segInput" id="HDFAmountNPH"  value="'.$hdfamountdnph.'" name="HDFAmountNPH" style="width:80%">');
#added by raymond
// $smarty->assign('requestQuantityHDF','<input type="text"  class="segInput" id="HDFQty"  value="'.$hdfquantityd.'" name="HDFquantity" style="width:80%">');
// $smarty->assign('requestAmountHDF','<input type="text"  class="segInput" id="HDFAmount"  value="'.$hdfamountd.'" name="HDFamount" style="width:80%">');

$smarty->assign('requestQuantityNPH', '<input type="text"  class="segInput" id="NPHquantity"  value="'.$nphquantityd.'" name="NPHquantity" style="width:80%">');
$smarty->assign('requestAmountNPH', '<input type="text"  class="segInput" id="NPHamount"  value="'.$nphamountd.'" name="NPHamount" style="width:80%">');

$option_subsidy="";
$subsidy_sql = "SELECT class_code,class_desc FROM seg_subsidy_classification WHERE is_enabled = 1";
$ergebnis=$db->GetAll($subsidy_sql);
if ($ergebnis) {
    foreach ($ergebnis as $row3){
        $option_subsidy .= '<option value="'.$row3['class_code'].'" '.$selectSubClass.'>'.$row3['class_desc'].'</option>';
    }
}

$smarty->assign('subsidy_classification_options', '<select class="segInput" id="subsidyClass" name="subsidyClass">'.
                $option_subsidy
                .'</select>
                ');
$smarty->assign('subsidy_amount_input', '<input class="segInput" type="text" name="subsidyValue" placeholder="Amount" id="subsidyValue">');

$smarty->assign('requestSubsidizePH', '<img ' . createComIcon($root_path, 'add.png', '0') . '  onclick="showSubsidize(\'ph\');" id="PHsubsidize" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer;display:none;">
    <input type="hidden" name="PH_subsidize_amount" id="PH_subsidize_amount" value="'.$subsidyValue.'" />
    <input type="hidden" name="PH_subsidize_class" id="PH_subsidize_class" value="'.$subsidyClass.'" />');
$smarty->assign('requestSubsidizeNPH', '<img ' . createComIcon($root_path, 'add.png', '0') .  ' onclick="showSubsidize(\'nph\');" id="NPHsubsidize" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer;display:none;">
    <input type="hidden" name="NPH_subsidize_amount" id="NPH_subsidize_amount" value="'.$subsidyNValue.'" />
    <input type="hidden" name="NPH_subsidize_class" id="NPH_subsidize_class" value="'.$subsidyNClass.'" />');
// $smarty->assign('requestSubsidizeHDF', '<img ' . createComIcon($root_path, 'add.png', '0') . ' onclick="showSubsidize(\'hdf\');"id="HDFsubsidize" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">
//     <input type="hidden" name="HDF_subsidize_amount" id="HDF_subsidize_amount" />
//     <input type="hidden" name="HDF_subsidize_class" id="HDF_subsidize_class" />');

$smarty->assign('requestDate', '<span id="show_requestdate" class="jedInput" style="font-weight:bold; color:#0000c0; padding:0px 2px;width:200px; height:24px">' . ($submitted ? date($fulltime_format, strtotime($_POST['requestdate'])) : $curDate_show) . '</span>
<input class="jedInput" name="requestdate" id="requestdate" type="hidden" value="' . ($submitted ? date($dbtime_format, strtotime($_POST['requestdate'])) : $curDate) . '" style="font:bold 12px Arial">');

$smarty->assign('sCalendarIcon', '<img ' . createComIcon($root_path, 'date_add.png', '0') . ' id="requestdate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">');
$jsCalScript = "<script type=\"text/javascript\">
Calendar.setup({
        displayArea : \"show_requestdate\",
            inputField : \"requestdate\",
            ifFormat : \"%Y-%m-%d %H:%M\",
            daFormat : \" %B %e, %Y %I:%M%P\",
            showsTime : true,
            button : \"requestdate_trigger\",
            singleClick : true,
            step : 1
});
</script>";
$smarty->assign('jsCalendarSetup', $jsCalScript);

$active_tab = 'request';
$smarty->assign('bTab' . ucfirst($active_tab), TRUE);
$smarty->assign('submitted', '<input value="TRUE" name="submitted" style="opacity: 0"/>');
$smarty->assign('encounter_type', '<input type="hidden" name="encounter_type" id="encounter_type" />');

//include_once($root_path.'include/inc_photo_filename_resolve.php');
$photo_src = $_POST["photo_src"] ? $_POST["photo_src"] : '../../gui/img/control/default/en/en_x-blank.gif';
$smarty->assign('img_source', $photo_src);

//$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.'&target=edit&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="validate();">');
$smarty->assign('sFormStart', '<form ENCTYPE="multipart/form-data" action="' . $thisfile . URL_APPEND . "&clear_ck_sid=" . $clear_ck_sid . '&target=edit&from=' . $_GET['from'] . '" method="POST" id="orderForm" name="inputform">');
$smarty->assign('sFormEnd', '</form>');

ob_start();
$sTemp = '';
?>

    <input type="hidden" name="submitted" value="1"/>
     <input type="hidden" name="limitcopy" id="limitcopy" value="<?php echo $limitcopy?>"/>
   
    <input type="hidden" name="sid" value="<?php echo $sid ?>">
    <input type="hidden" name="lang" value="<?php echo $lang ?>">
    <input type="hidden" name="cat" value="<?php echo $cat ?>">
    <input type="hidden" name="userck" value="<?php echo $userck ?>">
    <input type="hidden" name="encoder"
           value="<?php echo str_replace(" ", "+", $HTTP_COOKIES_VARS[$local_user . $sid]) ?>">
    <input type="hidden" name="dstamp" value="<?php echo str_replace("_", ".", date(Y_m_d)) ?>">
    <input type="hidden" name="tstamp" value="<?php echo str_replace("_", ".", date(H_i)) ?>">
    <input type="hidden" name="lockflag" value="<?php echo $lockflag ?>">
    <input type="hidden" name="selected_encounter" id="selected_encounter"/>
    

<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg = 'close2.gif';
#$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs', $sTemp);
if (!$viewonly) {
    $smarty->assign('sContinueButton', '<input type="image" class="segSimulatedLink" src="' . $root_path . 'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
    $smarty->assign('sBreakButton', '<img class="segSimulatedLink" src="' . $root_path . 'images/btn_cancelorder.gif" alt="' . $LDBack2Menu . '" align="absmiddle" onclick="window.location=\'' . $breakfile . '\'" onsubmit="return false;">');
}

#Added by Aleeya
$smarty->assign('viewBilling', '<button class="segButton" id="viewBillButton" ' . $is_allowedbilling . ' name="viewBillButton" onclick="openBillingTray();return false;"><img src="../../gui/img/common/default/calculator.png"/>View Bill</button>');
// $smarty->assign('toDischarge', '<button class="segButton" id="dischargeButton" '.$is_allowedDischarge.' name="dischargeButton" onclick="discharge();return false;" style="display: none"><img src="' . $root_path . 'gui/img/common/default/door_out.png"/>Discharge</button>
//                             <button class="segButton" id="undischargeButton" '.$is_allowedUndischarge.' name="undischargeButton" onclick="undischarge();return false;" style="display: none"><img src="' . $root_path . 'gui/img/common/default/door_out.png"/>Un-discharge</button>');
#modified by raymond : added parameter on refreshHistory
$smarty->assign('toRefresh', '<button class="segButton" id="historyButton" name="historyButton" onclick="refreshHistory(false);return false;"><img src="' . $root_path . 'gui/img/common/default/table_refresh.png"/>Refresh</button>');

$smarty->assign('buttonScheduleHistory', HtmlHelper::htmlButton('<img src="../../gui/img/common/default/printer.png"/> Schedule History', array(
    'id' => 'buttonScheduleHistory',
    'class' => 'segButton',
    'onclick' => 'printScheduleHistory()'
)));

#Added by Keith
$smarty->assign('prntsoa', '<button class="segButton" id="prntsoabtn" ' . $is_allowedbilling . ' name="prntsoabtn" onclick="printsoa();return false;"><img src="' . $root_path . 'gui/img/common/default/printer.png"/>Print Prebill</button>');
#March 4, 2014
// $smarty->assign('toLaboratory','<button class="segButton" onclick="openLabRequest();return false;" ><img src="../../gui/img/common/default/calculator.png"/>Request Laboratory</button>');
// <button class="segButton" onclick="openRequestTray(\''.$row["encounter_nr"].'\',\''.$row['pid'].'\');return false;"  style="height: 30"><img src="../../gui/img/common/default/cart_add.png"/>Request</button>

$smarty->assign('toLaboratory', '<button class="segButton" id="req" ' . $is_allowedCostcenter . ' onClick="check();return false;"  style="height: 30"><img src="../../gui/img/common/default/cart_add.png"/>Request</button>');

# '.(($encounter_nr==NULL)?'disabled="disabled"': '').'
# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile', 'dialysis/request_main.tpl');
$smarty->display('common/mainframe.tpl');

