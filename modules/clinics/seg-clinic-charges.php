<?php
error_reporting(E_COMPILE_ERROR | E_CORE_ERROR | E_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';

require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'gui/smarty_template/smarty_care.class.php');
require_once($root_path.'modules/clinics/ajax/clinic-requests.common.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');

require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);

$admin_access_dashboard = $acl->checkPermissionRaw('_a_0_all');
$access_ob_request = $acl->checkPermissionRaw('_a_1_OBGynecreaterequest');
$_a_1_addcharges = $acl->checkPermissionRaw('_a_1_addcharges');
$_a_1_nursingcreaterequest = $acl->checkPermissionRaw('_a_1_nursingcreaterequest');
$_a_2_doctorsdutyplanread = $acl->checkPermissionRaw('_a_2_doctorsdutyplanread');

include_once $root_path . 'include/inc_ipbm_permissions.php'; // added by carriane 10/24/17
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_order.php');
$oc=new SegOrder();
$area_code_row=$oc->getPharmaAreaByuserDefault($_SESSION["sess_login_personell_nr"]);

$personell_obj= new Personell;
$personell_info = $personell_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']);
 $access_personll_meds= array('staff','nurse','nursing attendant');
 $access_adding_newmeds = false;
if(in_array(trim(strtolower($personell_info['job_function_title'])), $access_personll_meds) || $allAccess){
    $access_adding_newmeds = true;
}

 $dr_nr = $_GET['dr_nr'];
$dept_nr = $_GET['dept_nr'];
$is_dr = $_GET['is_dr'];
$area_type = $_GET['area_type'];
$ptype = $_GET['ptype'];
$doc_nr = $_GET['doc_nr'];
$or_no = $_GET['or_no'];
$acc_no = $_GET['enc_accomodation'];
$user_from = (isset($_GET['user_from']) ? $_GET['user_from'] : ''); #Added by Christian 12-03-19
require_once($root_path.'include/care_api_classes/class_request_source.php');
$req_src_obj = new SegRequestSource();
if($ptype=='ipd') {
	if($isIPBM) $request_source = $req_src_obj->getSourceIPBM();
	else $request_source = $req_src_obj->getSourceIPDClinics();
} else if($ptype=='er') {
	$request_source = $req_src_obj->getSourceERClinics();
} else if($ptype=='opd') {
	if($isIPBM) $request_source = $req_src_obj->getSourceIPBM();
	else $request_source = $req_src_obj->getSourceOPDClinics();
} else if($ptype=='phs') {
	$request_source = $req_src_obj->getSourcePHSClinics();
} else if($ptype=='nursing') {
	$request_source = $req_src_obj->getSourceNursingWard();
} else if(($ptype=='ic') || ($ptype=='iclab')) {
	$request_source = $req_src_obj->getSourceIndustrialClinic();
} else if($ptype=='bb') {
	$request_source = $req_src_obj->getSourceBloodBank();
} else if($ptype=='spl') {
	$request_source = $req_src_obj->getSourceSpecialLab();
} else if($ptype=='or') {
	$request_source = $req_src_obj->getSourceOR();
} else if($ptype=='rdu') {
	$request_source = $req_src_obj->getSourceDialysis();
} else if($ptype=='doctor') {
	$request_source = $req_src_obj->getSourceDoctor();
} else if($ptype=='rd') {
	$request_source = $req_src_obj->getSourceDialysis();
} else if($ptype=='ip') {
	$request_source = $req_src_obj->getSourceInpatientPharmacy();
} else if($ptype=='mg') {
	$request_source = $req_src_obj->getSourceMurangGamot();
} else{
	$request_source = $req_src_obj->getSourceLaboratory();
}

if($_GET['ob']=='OB') $request_source = $req_src_obj->getSourceOBGyne();

global $db;

$smarty = new Smarty_Care('common');
$smarty->assign('sToolbarTitle',"Dialysis :: Test Request");
$smarty->assign('sWindowTitle',"Dialysis :: Test Request");

$breakfile = 'javascript:window.parent.cClick();';
$smarty->assign('breakfile', $breakfile);
$smarty->assign('ptype', '<input type="hidden" id="ptype" name="ptype" value="'.$ptype.'">');
$smarty->assign('user_from', '<input type="hidden" id="user_from" name="user_from" value="'.$user_from.'">'); #Added by Christian 12-03-19
$smarty->assign('request_source', '<input type="hidden" id="request_source" name="request_source" value="'.$request_source.'">');
header('Content-type: text/html; charset=utf-8');

ob_start();
$smarty->assign('defaultArea', $area_code_row['area_code']);
?>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=$root_path?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<!-- <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" /> -->
<!-- <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script> -->
<!-- <script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script> -->


<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<script type="text/javascript" src="js/clinic-request-tray.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.12.4.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.12.1.js"></script>

<link rel="stylesheet" href="<?= $root_path ?>js/bootstrap/4.0.0/css/bootstrap.min.css" type="text/css" />
<script type='text/javascript' src="<?=$root_path?>js/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>css/fontawesome-free-5.7.2-web/css/all.css" type="text/css" />
<link rel="stylesheet" href="<?= $root_path ?>frontend/themes/poc/css/custom.css" type="text/css" />

<link rel="stylesheet" href="<?= $root_path ?>/js/bootstrap/table/bootstrap-table.min.css" rel="stylesheet">
<script src="<?= $root_path ?>/js/bootstrap/table/bootstrap-table.min.js"></script>
<script src="<?= $root_path ?>/js/bootstrap/table/locale/bootstrap-table-en-US.min.js"></script>
<script src="<?= $root_path ?>/js/bootstrap/table/extensions/export/bootstrap-table-export.min.js"></script>
<style>
  .select,
  #locale {
    width: 100%;
  }
  .like {
    margin-right: 10px;
  }
</style>

<script type="text/javascript" src="<?=$root_path?>js/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();
var current_active_tab;
/*var oldcClick = cClick;
cClick = function() {
	if (OLloaded && OLgateOK) {
		if (over && OLshowingsticky) {
			refreshPage();
		}
	}
	oldcClick();
}*/

//Added by Jarel 11/10/2013 for autotagging
var ptype = '<?=$ptype?>';
var doc_nr = '<?=$doc_nr?>';
var or_no = '<?=$or_no?>';

function viewRequestPrintout()
{
	var enc_nr = $('encounter_nr').value;
	window.open('seg-clinic-request-printout2.php?encounter_nr='+enc_nr,null,'menubar=no,directories=no,height=600,width=800,resizable=yes');
}

//Added by Mary ~ June 09,2016
//Function for deletion request audit trail
function viewDeletionRequestTrail(argument) {
	var enc_nr = $('encounter_nr').value;
	// alert("hgdhaghagd");
	// window.open('seg-deletion-request-audit-trail.php?encounter_nr='+enc_nr,null,'menubar=no,directories=no,height=600,width=800,resizable=yes');

	return overlib(
		OLiframeContent('<?=$root_path?>modules/clinics/seg-deletion-request-audit-trail.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Deletion Request Audit Trail',
		MIDX,0, MIDY,0,
		STATUS,'Deletion Request Audit Trail');
}

function viewChargeRequestPrintout()
{
	var enc_nr = $('encounter_nr').value;
	window.open('seg-clinic-charge-request-printout.php?encounter_nr='+enc_nr,null,'menubar=no,directories=no,height=600,width=800,resizable=yes');
}

// updated by carriane 10/24/17; added url extension if ipbm
function openSpLabRequest()
{
	var IPBMextend = "<?=$IPBMextend?>";

	if(ptype=='doctor') autoTagging();	
	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/special_lab/seg-splab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&user_origin=splab&ischecklist=1&ptype=<?=$ptype?>&enc_accomodation=<?=$enc_accomodation?>'+IPBMextend,
			800, 450, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Special Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'Special Laboratory Request');
}

function openICLabRequest() 
{
	if(ptype=='doctor') autoTagging();	
	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/ic_lab/seg-iclab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&area_type=<?=$area_type?>&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&user_origin=iclab&ischecklist=1&ptype=<?=$ptype?>',
			800, 370, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'IC Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'IC Laboratory Request');
}

// updated by carriane 10/24/17; added url extension if ipbm
function openLabRequest() 
{
	if(ptype=='doctor') autoTagging();	

	var IPBMextend = "<?=$IPBMextend?>";

	var from_dialysis = <?php if($_GET['userck'] == 'ck_dialysis_user'){ echo $db->qstr('yes'); }else{ echo $db->qstr('no'); } ?>;

	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&area_type=<?=$area_type?>&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&user_origin=lab&ischecklist=1&ptype=<?=$ptype?>&enc_accomodation=<?=$enc_accomodation?>&from_dialysis=' + from_dialysis+IPBMextend,
			800, 400, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Laboratory Request',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Request');
}

function openLabResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Laboratory Results',
		MIDX,0, MIDY,0,
		STATUS,'Laboratory Results');
}

function viewCbgResult() {    
    const inputOptions = new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          'isoformat-cbg-reading': 'Tabular',
          'chart-cbg-reading': 'Chart'
        })
      }, 100)
    })
    
    async function f() {
        const {value: rformat} = await Swal.fire({
            title: 'Select Format',
            input: 'radio',
            inputOptions: inputOptions,
            inputValidator: (value) => {
                if (!value) {
                  return 'Please select the format!'
                }
            }
        })        
        if (rformat) {
            var rawUrlData = { reportid: rformat, 
                               repformat: 'pdf',
                               param:{enc_no:<?=$encounter_nr?>} };
            var urlParams = $J.param(rawUrlData);
            window.open('<?=$root_path?>/modules/reports/show_report.php?'+urlParams, '_blank');
        }
    }
    
    f();    
}
// updated by carriane 10/24/17; added url extension if ipbm
function openBloodRequest() {
	var IPBMextend = "<?=$IPBMextend?>";
	if(ptype=='doctor') autoTagging();	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/bloodBank/seg-blood-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&ptype=<?=$ptype?>&user_origin=blood&ischecklist=1&enc_accomodation=<?=$enc_accomodation?>'+IPBMextend,
			800, 450, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Blood Bank Request',
		MIDX,0, MIDY,0,
		STATUS,'Blood Bank Request');
}

function openBloodResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/laboratory/seg-lab-request-result-patient-list.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,2, CAPTION,'Blood Bank Results',
		MIDX,0, MIDY,0,
		STATUS,'Blood Bank Results');
}

// updated by carriane 10/24/17; added url extension if ipbm
function openRadioRequest() {
	var IPBMextend = "<?=$IPBMextend?>";
	if(ptype=='doctor') autoTagging();	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/seg-radio-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&ischecklist=1&is_dr=<?=$is_dr?>&ptype=<?=$ptype?>&enc_accomodation=<?=$enc_accomodation?>'+IPBMextend,
			800, 450, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Radiology Request',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Request');
}

function openRadioResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/radiology_patient_request.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>',
			800, 370, 'fGroupTray', 1, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,4, CAPTION,'Radiology Results',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Results');
}
function openOBGYNERequest() {
	if(ptype=='doctor') autoTagging();	
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/seg-radio-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&area=clinic&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&dr_nr=<?=$dr_nr?>&dept_nr=<?=$dept_nr?>&ischecklist=1&is_dr=<?=$is_dr?>&ptype=<?=$ptype?>&ob=OB',
			800, 450, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'OB-GYN Ultrasound Request',
		MIDX,0, MIDY,0,
		STATUS,'OB-GYN Ultrasound Request');
}
function openOBGYNEResults() {
	return overlib(
		OLiframeContent('<?=$root_path?>modules/radiology/radiology_patient_request.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp=1&is_doctor=1&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&ob=OB',
			800, 370, 'fGroupTray', 1, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0>',
		CAPTIONPADDING,4, CAPTION,'OB GYNE Results',
		MIDX,0, MIDY,0,
		STATUS,'OB GYNE Results');
}


// updated by carriane 10/24/17; added url extension if ipbm
function openPharmaRequest(area) {
	var isIPBM = "<?=$isIPBM?>";
	var fromIPBM = '';
	if(isIPBM == 1){
		fromIPBM = "&fromIPBM=1";
	}
	if(ptype=='doctor') autoTagging();	

	return overlib(
	OLiframeContent('<?=$root_path?>modules/pharmacy/seg-pharma-order.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&area='+area+'&pid=<?=$pid?>&encounterset=<?=$encounter_nr?>&is_dr=<?=$is_dr?>&billing=1&request_source=<?=$request_source?>&enc_accomodation=<?=$enc_accomodation?>'+fromIPBM,
		1200, 600, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
	WIDTH,1100, TEXTPADDING,0, BORDER,0,MODALSCROLL,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
	CAPTIONPADDING,2, CAPTION,'Pharmacy Request',
	MIDX,0, MIDY,0,
	STATUS,'Pharmacy Request');
}

function openPackageModal() {
	// if(ptype=='doctor') autoTagging();	

	return overlib(
	OLiframeContent('<?=$root_path?>index.php?r=or_/package&encounter_nr='+
		'<?=$encounter_nr?>&req_src=<?=$request_source?>',
		900, 370, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
	WIDTH,410, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
	CAPTIONPADDING,2, CAPTION,'Package List',
	MIDX,0, MIDY,0,
	STATUS,'Package List');

}

function openPocOrder() {
    $J.ajax({
        url: '../../index.php?r=poc/order/orderCare&encounter_nr=<?=$encounter_nr?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            openPocModal(data);
        },
        error: function(jqXHR, exception) {         
            if (jqXHR.status === 0) {
                alert('Not connected.\n Verify Network.');
            } else if (jqXHR.status === 404) {
                alert('Requested page not found. [404]');
            } else if (jqXHR.status === 500) {
                alert('Internal Server Error [500].');
            } else if (jqXHR.status === 480) {
                Swal.fire(
                        'Error!',
                        jqXHR.responseText,
                        'error'
                     );                
            } else if (exception === 'parsererror') {
                alert('Requested JSON parse failed.');
            } else if (exception === 'timeout') {
                alert('Time out error.');
            } else if (exception === 'abort') {
                alert('Ajax request aborted.');
            } else {
                alert('Uncaught Error.\n' + jqXHR.responseText);
            }            
        }
    });
}
   
function openPocModal(data) {
    jQuery(function($) {    
        $("#divModalContent").html(data);
    });
    $J("#divModalDialog").modal('show');
}

function closePocModal() {
    $J("#divModalDialog").modal('hide'); 
    refreshPocTable(true);
}

function currencyFormat(num) {
    let cnum = new Intl.NumberFormat('en', {
                    style: 'currency',
                    currency: 'USD',
                    signDisplay: 'exceptZero',
                    currencySign: 'accounting',
                  }).format(num);        
    return cnum.substring(1);
}

function refreshPocTable(brefresh) {
    jQuery(function ($) {
        $.ajax({
            url: '../../index.php?r=poc/order/getPocOrders&encounter_nr=<?=$encounter_nr?>',
            type: 'GET',
            dataType: 'json',
            success: function (jqdata) {
                var $table = $J('#poc_orders')
                $(function () {
                    if (brefresh) {
                        $table.bootstrapTable('destroy').bootstrapTable({data: jqdata})
                    } else {
                        $table.bootstrapTable({data: jqdata})
                    }
                })

                // Update total charge and total cash in POC tab.
                var tcash = parseFloat("0.00");
                var tchrg = parseFloat("0.00");
                jqdata.forEach(function (element) {
                    if (element.order_type == "START") {
                        if (element.is_cash == "0") {
                            tchrg += (element.total == null) ? 0.0 : parseFloat(element.total);
                        } else {
                            tcash += (element.total == null) ? 0.0 : parseFloat(element.total);
                        }
                    }
                });

                $J('#poc-total-charge').html(currencyFormat(tchrg));
                $J('#poc-total-cash').html(currencyFormat(tcash));
            },
            error: function (jqXHR, exception) {
                if (jqXHR.status === 0) {
                    alert('Not connected.\n Verify Network.');
                } else if (jqXHR.status === 404) {
                    alert('Requested page not found. [404]');
                } else if (jqXHR.status === 500) {
                    alert('Internal Server Error [500].');
                } else if (exception === 'parsererror') {
                    alert('Requested JSON parse failed.');
                } else if (exception === 'timeout') {
                    alert('Time out error.');
                } else if (exception === 'abort') {
                    alert('Ajax request aborted.');
                } else {
                    alert('Uncaught Error.\n' + jqXHR.responseText);
                }
            }
        });    
    });
}

function openOutdieMedsModal() {
	return overlib(
	OLiframeContent('<?=$root_path?>index.php?r=pharmacy/package&encounter_nr='+
	WIDTH,410, TEXTPADDING,0, BORDER,0,
		'<?=$encounter_nr?>&req_src=<?=$request_source?>&pid=<?=$pid?>',
		1200, 450, 'fGroupTray', 0, 'auto'),
	WIDTH,1200, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
	CAPTIONPADDING,2, CAPTION,'Outside Medicines',
	MIDX,0, MIDY,0,
	STATUS,'Outside Medicines');
}

// updated by carriane 10/24/17; added url extension if ipbm
function openMiscellaneousRequest() {
	var isIPBM = "<?=$isIPBM?>";
	var fromIPBM = '';
	if(isIPBM == 1){
		fromIPBM = "&fromIPBM=1";
	}
	if(ptype=='doctor' && or_no!='') autoTagging();	
	return overlib(
	OLiframeContent('<?=$root_path?>modules/dialysis/seg-misc-request-new.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&pid=<?=$pid?>&encounter_nr=<?=$encounter_nr?>&mode=new&area=<?=$ptype?>'+fromIPBM,
		800, 400, 'fGroupTray', 0, 'auto'), //edited by Macoy, June 9, 2014 (Size Change of UI)
	WIDTH,800, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
	CAPTIONPADDING,2, CAPTION,'Miscellaneous Request',
	MIDX,0, MIDY,0,
	STATUS,'Miscellaneous Request');
}

function initialize() {
	initializeTab(0);
	var enc_nr = $('encounter_nr').value;
	var pid = $('pid').value;
	xajax_computeTotalPayment(pid, enc_nr);
}

function refreshPage() {
    var pharma_area = "<?php echo $_SESSION['pharma_area']; ?>";/*added by mark 2016-10-21*/
	if (pharma_area)/*added by mark 2016-10-21*/
	initializeTab(4);/*added by mark 2016-10-21*/
	else 
           //window.location.reload();
          initializeTab(current_active_tab);	
}

function requestByDate()
{
//	var seltabs = $('#tabs').tabs();
//	var selected = seltabs.tabs('option', 'selected')
        var selected = $J( "#tabs" ).tabs( "option", "active" );
	initializeTab(selected);
}

function initializeTab(id)
{
	current_active_tab=id;
	// alert(id)
	//alert($('is_bill_final').value);
	//$('is_bill_final').value=1;
	if($('is_bill_final').value==1 && $('is_bill_deleted').value != 1) { //added by art 02/05/2014
	//if($('is_bill_final').value==1 ) {

		document.getElementById('viewRequestPrintoutBtn').disabled = true;
		document.getElementById('openLabRequestBtn').disabled = true;
            document.getElementById('openPocOrderBtn').disabled = true;
            if (!!document.getElementById('openICLabRequestBtn')) {
		document.getElementById('openICLabRequestBtn').disabled = true;
            }
		document.getElementById('openBloodRequestBtn').disabled = true;
		document.getElementById('openSpLabRequestBtn').disabled = true;
		document.getElementById('openRadioRequestBtn').disabled = true;
		document.getElementById('openPharmaRequestBtnIP').disabled = true;
		document.getElementById('openPackageBtn').disabled = true;
		//document.getElementById('openPharmaRequestBtnMG').disabled = true;
		document.getElementById('openOBGyneRequestBtn').disabled = true;
		document.getElementById('openMiscellaneousRequestBtn').disabled = true;
            if (!!document.getElementById('openOutsidePharmaRequestBtnIP')) {
		document.getElementById('openOutsidePharmaRequestBtnIP').disabled = true;
            }

 $("#tabs").tabs({
                beforeActivate: function( event, ui ) {
                    event.preventDefault();
                }                
            });

	}

	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	var enc_nr = $('encounter_nr').value;
	var pid = $('pid').value;
	var src = $('request_source').value;
	var billed = $('is_bill_final').value == 1 && $('is_bill_deleted').value == 0 ? 1 : 0;

	var seldate = $('seldate').value;

	var isIPBM = "<?=$isIPBM?>";


// alert(seldate);
	if($('is_ic').value==1) {
			switch(id)
			{
				/*case 0:
					AJAXTimerId = setTimeout("xajax_populateLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 1:
					AJAXTimerId = setTimeout("xajax_populateICLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 2:
					AJAXTimerId = setTimeout("xajax_populateBloodRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 3:
					AJAXTimerId = setTimeout("xajax_populateSpLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 4:
					AJAXTimerId = setTimeout("xajax_populateRadioRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 5:
					AJAXTimerId = setTimeout("xajax_populateIpRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;*/ /*commented by art 06/17/2014*/
				/*case 6:
					AJAXTimerId = setTimeout("xajax_populateMgRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;*/
				/*case 6:
					AJAXTimerId = setTimeout("xajax_populateMiscRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;*/ /*commented by art 06/17/2014*/

				case 0:
				        AJAXTimerId = setTimeout("xajax_populateLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"', '"+isIPBM+"')",100);
					break;
				case 1:
					AJAXTimerId = setTimeout("xajax_populateBloodRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
					break;
				case 2:
					AJAXTimerId = setTimeout("xajax_populateSpLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"', '"+isIPBM+"')",50);
					break;
				case 3:
					AJAXTimerId = setTimeout("xajax_populateRadioRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 4:
					AJAXTimerId = setTimeout("xajax_populateIpRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
				case 5:
					AJAXTimerId = setTimeout("xajax_populateMiscRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
					break;
                                        
                                case 6:
                                    refreshPocTable(false);
                                    break; 
                             	case 7:
				AJAXTimerId = setTimeout("xajax_populateOBGRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;                                    
			}
	} else {
		switch(id)
		{
			// alert(id);
			case 0:
				AJAXTimerId = setTimeout("xajax_populateLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"', '"+isIPBM+"')",100);
				break;
			case 1:
				AJAXTimerId = setTimeout("xajax_populateBloodRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",100);
				break;
			case 2:
				AJAXTimerId = setTimeout("xajax_populateSpLabRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"', '"+isIPBM+"')",50);
				break;
			case 3:
				AJAXTimerId = setTimeout("xajax_populateRadioRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
			case 4:
				AJAXTimerId = setTimeout("xajax_populateIpRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
			/*case 5:
				AJAXTimerId = setTimeout("xajax_populateMgRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;*/
			case 5:
				AJAXTimerId = setTimeout("xajax_populateMiscRequests('"+enc_nr+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
                                
                        case 6:
                                refreshPocTable(false);
                                break;
case 7:
				AJAXTimerId = setTimeout("xajax_populateOBGRequests('"+enc_nr+"','"+pid+"', '"+src+"', '"+billed+"', '"+seldate+"')",50);
				break;
		}
	}

	xajax_computeTotalPayment(pid, enc_nr);
}

/*
* Creted by Jarel
* Created on 11/10/2013
* Use to call ajax function for auto tagging of patient, only if request is from Doctor's Dashboard 
*/
function autoTagging(){
	xajax_autoTagging($J('#encounter_nr').val(),doc_nr,or_no);
}

//commented by justin
// //added by janken 11/13/2014 for disabling the add package button if the patient is not from OPD
// function disableAddPackage(){
// 	$('openPackageBtn').disabled = true;
// }

//Added: Jayson-OJT 2/27/2014
//Added: <?php if($_GET['userck'] == 'ck_dialysis_user'){ echo 6; }else{ echo 0; }?>
//for dialysis module. Defeault to Miscellaneous tab.
    jQuery(function($) {
//        $J(function() {
                $("#tabs").tabs({
                    active: <?php if($_GET['userck'] == 'ck_dialysis_user'){
								if($_GET['from'] == 'newrequest'){ echo 0;}
								if($_GET['from'] == 'listpatients'){echo 6; }
							}
							else{ echo 0; }?>,
                    
//                        select: function(event, ui) {
//                                var selected = ui.index;
//                                //alert(ui.panel.empty());
//                                initializeTab(selected);
//                        }
                        
            beforeActivate: function( event, ui ) {                        
                var selected = ui.newTab.index();
				//alert(ui.panel.empty());
				// alert(selected);
				initializeTab(selected);
			}
		});
	});

var AJAXTimerID=0;
document.observe('dom:loaded', function(){
	initializeTab(<?php if($_GET['userck'] == 'ck_dialysis_user'){ echo 6; }else{ echo 0; }?>);
	//initialize();
});

jQuery('#divModalDialog').modal({'show':false});
</script>
<?php
if(!$admin_access_dashboard  && !$access_ob_request && !$_a_1_addcharges && !$_a_1_nursingcreaterequest && !$_a_2_doctorsdutyplanread){
	$smarty->assign('btnDisableOB', 'disabled');
}
$xajax->printJavascript($root_path.'classes/xajax_0.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

// $smarty->assign('form_start', '<form name="main_or_form" method="POST" action="'.$_SERVER['PHP_SELF'].'">');
// $smarty->assign('form_end', '</form>');

$pid = isset($_POST['pid']) ? $_POST['pid'] : $_GET['pid'];
$seg_person = new Person($pid);
$person_info = $seg_person->getAllInfoArray();
$middle_initial = (strnatcasecmp($person_info['name_middle'][0], $person_info['name_middle'][1]) == 0) ? ucwords(substr($person_info['name_middle'], 0, 2)) : strtoupper($person_info['name_middle'][0]);
$person_name = ucwords($person_info['name_last']) . ', ' . ucwords($person_info['name_first']) . ' ' . $middle_initial;

$person_address = implode(", ",array_filter(array($person_info['street_name'], $person_info["brgy_name"], $person_info["mun_name"])));
if ($person_info["zipcode"])
	$person_address.=" ".$person_info["zipcode"];
if ($person_info["prov_name"])
	$person_address.=" ".$person_info["prov_name"];

$smarty->assign('sPatientID','<input id="pid" name="pid" class="clear" type="text" value="'.$pid.'" readonly="readonly" style="color:#006600; font:bold 16px Arial;"/>');
$smarty->assign('patient_name', $person_name);

// updated by carriane 10/24/17; added IPBM encounter types
$encounter_types = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)', "5"=>'DIALYSIS', "6"=>'Health Service and Specialty Clinic', IPBMOPD_enc => 'IPBM - OPD', IPBMIPD_enc => 'IPBM - IPD');
$encounter_nr = isset($_POST['encounter_nr']) ? $_POST['encounter_nr'] : $_GET['encounter_nr'];
$seg_encounter = new Encounter();
$encounter_details = $seg_encounter->getEncounterInfo($encounter_nr);
$encounter_type = $encounter_types[$encounter_details['encounter_type']];
$smarty->assign('encounter_type', $encounter_type);
$smarty->assign('encounter_nr_val', $encounter_nr);
$smarty->assign('encounter_nr', '<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'" />');

// added by carriane 10/24/17
if($encounter_details['encounter_type'] == IPBMOPD_enc || $encounter_details['encounter_type'] == IPBMIPD_enc){
	$smarty->assign('packageDisable', 'disabled');
}
// end carriane


if(!$access_adding_newmeds){
    $btn_disabled  = 'disabled';
}

$smarty->assign('newMedsDisable',$btn_disabled);
if($encounter_details['encounter_type']==2)	
	$smarty->assign('area', 'amb');
else 
	$smarty->assign('area', 'or');

$is_bill_final = 0;
$sql_billed = "SELECT is_final , is_deleted FROM seg_billing_encounter WHERE encounter_nr ='".$encounter_nr."' ORDER BY bill_frmdte ASC ";
$result_bill = $db->Execute($sql_billed);
if($row_bill = $result_bill->FetchRow())    {
	# commented out by: syboy 08/28/2015
	$is_bill_final = $row_bill['is_final'];
	$is_bill_deleted = $row_bill['is_deleted'];
	# added by: syboy 08/28/2015
	// if ($row_bill['is_final'] == 1 && $row_bill['is_deleted'] == 1) {
	// 	$is_bill_final = 0;
	// 	$is_bill_deleted = 0;
	// }else if ($row_bill['is_final'] == 0 && $row_bill['is_deleted'] == 0) {
	// 	$is_bill_final = 0;
	// 	$is_bill_deleted = 0;
	// }else if ($row_bill['is_final'] == 1 && $row_bill['is_deleted'] != 1) {
	// 	$is_bill_final = 1;
	// 	$is_bill_deleted = 0;
	// }else{
	// 	$is_bill_final = 1;
	// 	$is_bill_deleted = 0;
	// }
	# end
}

if($_GET['transfertobed'])
	$smarty->assign('disableRes', 'disabled');

$smarty->assign('is_bill_final', '<input type="hidden" name="is_bill_final" id="is_bill_final" value="'.$is_bill_final.'"/>');
$smarty->assign('is_bill_deleted', '<input type="hidden" name="is_bill_final" id="is_bill_deleted" value="'.$is_bill_deleted.'"/>');
$smarty->assign('ipbmextend', '<input type="hidden" name="ipbmextend" id="ipbmextend" value="'.$IPBMextend.'"/>'); // added by carriane 10/24/17

$service_type_code = array (49,50,51,52,53,54,"");
$service_type_name = array ("Physical Medicine & Rehab", "Dental", "Orthopedics", "ENT-HNS", "Pediatrics", "Special Lab", "Other");
$service_type_options = "<option value='0'> -Select service type- </option";
for($i=0;$i<count($service_type_code);$i++)
{
	$service_type_options.="<option value='".$service_type_code[$i]."'>".$service_type_name[$i]."</option>";
}
$smarty->assign('miscServiceTypes', $service_type_options);

$isIc = FALSE;
if(strtolower($ptype)=="ic") {
 $isIc = TRUE;
}
/*commented by art 05/18/2014
$smarty->assign('isIC', $isIc);
*/
$smarty->assign('isIc_hidden', '<input type="hidden" id="is_ic" value="'.$isIc.'"/>');

$smarty->assign('dateToday', date('F d, Y'));
$smarty->assign('dateTodayValue', date('Y-m-d'));

$smarty->assign('bHideTitleBar',TRUE);
$smarty->assign('bHideCopyright',TRUE);
$smarty->assign('sMainBlockIncludeFile','clinics/request_tray.tpl'); //Assign the or_main template to the frameset
$smarty->display('common/mainframe.tpl'); //Display the contents of the frame

?>
