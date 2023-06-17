<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_transactions.php');
require_once($root_path.'modules/industrial_clinic/ajax/transaction.common.php');
require_once $root_path."include/care_api_classes/class_encounter.php";
require_once($root_path.'include/care_api_classes/class_person.php');

#added by VAN 03-01-2011
require_once($root_path.'include/care_api_classes/class_personell.php');
require_once($root_path.'include/care_api_classes/class_hospital_admin.php');

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_ic_transaction_user';
require_once $root_path.'include/inc_front_chain_lang.php';

# Create products object
$GLOBAL_CONFIG=array();

# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

if(!isset($pid)) $pid=0;
if(!isset($encounter_nr)) $encounter_nr='';

//$phpfd = config date format in PHP date() specification

if (!$_GET['from'])
	$breakfile=$root_path."modules/industrial_clinic/seg-industrial_clinic-functions.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/industrial_clinic/".$_GET['from'].".php".URL_APPEND;
}

$thisfile='seg-ic-transaction-form.php';

# Start Smarty templating here
/**
* LOAD Smarty
*/

# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Health Service and Specialty Clinic :: Transaction";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);


#save data here | 

require_once $root_path . 'include/care_api_classes/class_acl.php';
$acl = new Acl($_SESSION['sess_temp_userid']);

$CanViewMedExamChart = $acl->checkPermissionRaw(array('_a_3_CanViewMedExamChart'));
$CanViewMedDriCertificate = $acl->checkPermissionRaw(array('_a_3_CanViewMedDriCertificate'));
$CanViewMedDriLince = $acl->checkPermissionRaw(array('_a_3_CanViewMedDriLince'));
$ShowPerRegist = $acl->checkPermissionRaw(array('_a_3_ShowPerRegist'));
$UpdatePersoRegis = $acl->checkPermissionRaw(array('_a_3_UpdatePersoRegis'));
$CanViewICForm = $acl->checkPermissionRaw(array('_a_3_CanViewICForm')); 
$TransacHistoryList = $acl->checkPermissionRaw(array('_a_3_TransacHistoryList'));
$Examinations = $acl->checkPermissionRaw(array('_a_3_Examinations'));
$CanViewVaccCert = $acl->checkPermissionRaw(array('_a_3_CanViewVaccCert'));
$CanViewLtoMedCert = $acl->checkPermissionRaw(array('_a_3_CanViewLtoMedCert'));
$medocs = $acl->checkPermissionRaw(array('_a_0_all'));
$allow_accessFollowUpForm = $acl ->checkPermissionRaw(array('_a_3_MedExamFollowUpForm'));

/**
 * Added by Gervie 05/09/2016
 * Fetch data of LTO Medical Certificate
 */
$check_enc = $db->GetOne("SELECT encounter_nr FROM seg_industrial_transaction WHERE refno = ".$db->qstr($_GET['refno']));
$lto = $db->GetOne("SELECT * FROM seg_industrial_cert_med_lto WHERE encounter_nr = ".$db->qstr($check_enc));

# Collect javascript code
ob_start();

	 # Load the javascript code
?>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/ajaxcontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>


<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$root_path?>js/seg_utils.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/listgen/listgen.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/listgen/css/default/default.css" type="text/css"/>


<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/lang/calendar-en.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jscalendar/calendar-setup_3.js" ></script>
<script type="text/javascript" language="javascript">

function openExaminationsTray()
{

	var Examinations = '<?=$Examinations?>';
    var medocs = '<?=$allpermission?>';
	if (medocs){

	overlib(
	OLiframeContent('<?=$root_path?>modules/clinics/seg-clinic-charges.php?ptype=ic&pid='+$('pid').value+'&encounter_nr='+$('caseNo').value,
			850, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,850, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,'New Test Request',
			MIDX,0, MIDY,0,
			STATUS,'New Test Request');
	return false;

	} else if (Examinations == 1){

	overlib(
	OLiframeContent('<?=$root_path?>modules/clinics/seg-clinic-charges.php?ptype=ic&pid='+$('pid').value+'&encounter_nr='+$('caseNo').value,
			850, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,850, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,'New Test Request',
			MIDX,0, MIDY,0,
			STATUS,'New Test Request');
	return false;

	} else alert('No Permission in Examinations');

}

function openOutdieMedsModal() {
	return overlib(
		OLiframeContent('<?=$root_path?>index.php?r=pharmacy/package&encounter_nr='+$('caseNo').value+'&req_src=IC',
			820, 450, 'fGroupTray', 0, 'auto'),
		WIDTH,410, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="<?=$root_path?>/images/close.gif" border=0 onclick="requestByDate();">',
		CAPTIONPADDING,2, CAPTION,'Outside Medicines',
		MIDX,0, MIDY,0,
		STATUS,'Outside Medicines');

}

/* Added by Cherry 09-06-10 */
function openMedDentalCert()
{
	var CanViewMedDriCertificate = '<?=$CanViewMedDriCertificate?>';
    var medocs = '<?=$allpermission?>';
	if (medocs){
		overlib(
		OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-medcert-new.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
				850, 500, 'fGroupTray', 0, 'auto'),
				WIDTH,850, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
				CAPTIONPADDING,2, CAPTION,'Medical / Dental Certificate',
				MIDX,0, MIDY,0,
				STATUS,'Medical / Dental Certificate');
		return false;

		} else if (CanViewMedDriCertificate == 1){

	overlib(
	OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-medcert-new.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
			850, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,850, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,'Medical / Dental Certificate',
			MIDX,0, MIDY,0,
			STATUS,'Medical / Dental Certificate');
	return false;

		}else
			alert('No Permission in Medical/Dental Certificate');
}

function openDriverCert()
{

	var CanViewMedDriLince = '<?=$CanViewMedDriLince?>';
    var medocs = '<?=$allpermission?>';
	if (medocs){

	overlib(
	OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-cert-med-driver-interface2.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
			850, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,850, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,"Medical Certificate for Driver's License",
			MIDX,0, MIDY,0,
			STATUS,"Medical Certificate for Driver's License");
	return false;

	} else if (CanViewMedDriLince == 1){

	overlib(
	OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-cert-med-driver-interface2.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
			850, 500, 'fGroupTray', 0, 'auto'),
			WIDTH,850, TEXTPADDING,0, BORDER,0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING,2, CAPTION,"Medical Certificate for Driver's License",
			MIDX,0, MIDY,0,
			STATUS,"Medical Certificate for Driver's License");
	return false;

	}else
			alert('No Permission in Med. Cert Drivers License');

}

//commented out by Nick 7-9-2015, transferred to seg-ic-transaction-form.js openMedExamChart()
//function openMedExamChart(){
//
//	var CanViewMedExamChart = '<?//=$CanViewMedExamChart?>//';
//    var medocs = '<?//=$allpermission?>//';
//
//	if (medocs){
//	overlib(
//	OLiframeContent('<?//=$root_path?>//modules/industrial_clinic/seg-ic-cert-med-exam-interface1.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
//			1350, 500, 'fGroupTray', 0, 'auto'),
//			WIDTH,1350, TEXTPADDING,0, BORDER,0,
//			STICKY, SCROLL, CLOSECLICK, MODAL,
//			CLOSETEXT, '<img src=<?//=$root_path?>///images/close_red.gif border=0 >',
//			CAPTIONPADDING,2, CAPTION,"Medical Examination Chart",
//			MIDX,0, MIDY,0,
//			STATUS,"Medical Examination Chart");
//	return false;
//	} else if (CanViewMedExamChart == 1){
//	overlib(
//	OLiframeContent('<?//=$root_path?>//modules/industrial_clinic/seg-ic-cert-med-exam-interface1.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
//			1350, 500, 'fGroupTray', 0, 'auto'),
//			WIDTH,1350, TEXTPADDING,0, BORDER,0,
//			STICKY, SCROLL, CLOSECLICK, MODAL,
//			CLOSETEXT, '<img src=<?//=$root_path?>///images/close_red.gif border=0 >',
//			CAPTIONPADDING,2, CAPTION,"Medical Examination Chart",
//			MIDX,0, MIDY,0,
//			STATUS,"Medical Examination Chart");
//	return false;
//	} else
//		alert('No Permission in Medical Exam Chart');
//
//}
/* End Cherry */

/*added by art 04/15/2014*/
function openClinical(){

	var CanViewICForm = '<?=$CanViewICForm?>';
    var medocs = '<?=$allpermission?>';

	if (medocs){
		window.open('reports/show_ic_clinical_form.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,"viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	} else if (CanViewICForm == 1){
	window.open('reports/show_ic_clinical_form.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,"viewClinicalForm","width=800,height=700,menubar=no,resizable=yes,scrollbars=yes");
	} else 
		alert('No Permission in IC Clinical Form');
}

/*added by gervie 07/08/2015*/
function openVaccination(){
    var medocs = '<?=$CanViewVaccCert?>';

    if(medocs){
        overlib(
            OLiframeContent('<?=$root_path?>modules/industrial_clinic/seg-ic-vacc-cert.php?pid='+$('pid').value+'&encounter_nr='+$('caseNo').value+'&refno='+$('refno').value,
                850, 500, 'fGroupTray', 0, 'auto'),
            WIDTH,850, TEXTPADDING,0, BORDER,0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
            CAPTIONPADDING,2, CAPTION,"Vaccination Certificate",
            MIDX,0, MIDY,0,
            STATUS,"Vaccination Certificate");
        return false;
    }
    else{
        alert('No Permission in IC Vaccination Form');
    }
}

/*added by Gervie 05/05/2016*/
function openLtoCert(){
	var hasMedCert = '<?= $lto; ?>';
	var CanViewLtoMedCert = '<?= $CanViewLtoMedCert ?>';

	if(CanViewLtoMedCert) {
		if(hasMedCert != '') {
			return overlib(
		        OLiframeContent('<?=$root_path?>?r=industrialClinic/certificate/view&id='+hasMedCert+'&pid='+$('pid').value+'&encounter_nr='+$('caseNo').value,
		            850, 500, 'fGroupTray', 0, 'auto'),
		        WIDTH,850, TEXTPADDING,0, BORDER,0,
		        STICKY, SCROLL, CLOSECLICK, MODAL,
		        CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
		        CAPTIONPADDING,2, CAPTION,"LTO Medical Certificate",
		        MIDX,0, MIDY,0,
		        STATUS,"LTO Medical Certificate");
		}
		else {
			return overlib(
		        OLiframeContent('<?=$root_path?>?r=industrialClinic/certificate/create&pid='+$('pid').value+'&encounter_nr='+$('caseNo').value,
		            850, 500, 'fGroupTray', 0, 'auto'),
		        WIDTH,850, TEXTPADDING,0, BORDER,0,
		        STICKY, SCROLL, CLOSECLICK, MODAL,
		        CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
		        CAPTIONPADDING,2, CAPTION,"LTO Medical Certificate",
		        MIDX,0, MIDY,0,
		        STATUS,"LTO Medical Certificate");
		}
	}
	else {
		alert('No Permission in LTO Medical Certificate');
	}
}

function modeHistory(mode){

	var TransacHistoryList = '<?=$TransacHistoryList?>';
    var medocs = '<?=$allpermission?>';

    if (medocs){
	return overlib(
				OLiframeContent('../../modules/registration_admission/seg-mode-history.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&ptype=<?=$ptype?>&pid='+$('pid').value+'&encounterset='+$('caseNo').value+'&is_dr=<?=$is_doctor?>&mode='+mode,
								800, 420, 'fGroupTray', 0, 'auto'),
										WIDTH,800, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
									 CAPTIONPADDING,2, CAPTION,'Requests History',
									 MIDX,0, MIDY,0,
									 STATUS,'Requests History');
	} else if (TransacHistoryList == 1){

	return overlib(
				OLiframeContent('../../modules/registration_admission/seg-mode-history.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&from=CLOSE_WINDOW&ptype=<?=$ptype?>&pid='+$('pid').value+'&encounterset='+$('caseNo').value+'&is_dr=<?=$is_doctor?>&mode='+mode,
								800, 420, 'fGroupTray', 0, 'auto'),
										WIDTH,800, TEXTPADDING,0, BORDER,0,
								STICKY, SCROLL, CLOSECLICK, MODAL,
								CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="">',
									 CAPTIONPADDING,2, CAPTION,'Requests History',
									 MIDX,0, MIDY,0,
									 STATUS,'Requests History');
	} else 
		alert('No Permission in Transaction History List');


}
/*end art*/

</script>
<script type="text/javascript" src="<?= $root_path ?>modules/industrial_clinic/js/seg-ic-transaction-form.js" ></script>
<script type="text/javascript" src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript">
var $J = jQuery.noConflict();
	$J(function() {
   		// console.log( "ready!" );
   		var a = $J('#purpose_exam option:selected').val();
   		if (a == 'OT') {
   			$J('#purpose_exam_other').show();
   		}else{
   			$J('#purpose_exam_other').hide();
   		}		
	});

	function purpose(){
		var a = $J('#purpose_exam option:selected').val();
   		if (a == 'OT') {
   			$J('#purpose_exam_other').show();
   			$J('#purpose_exam_other').focus();
   		}else{
   			$J('#purpose_exam_other').hide();
   		} 
	}


	function showReg(){

	var ShowPerRegist = '<?=$ShowPerRegist?>';
    var medocs = '<?=$allpermission?>';

	    if (medocs){

	   		window.location.href = '../../modules/registration_admission/patient_register_show.php?pid='+$J('#pid').val()+'&from=&newdata=1&target=&ptype=ic';
		}else if (ShowPerRegist == 1){
   		window.location.href = '../../modules/registration_admission/patient_register_show.php?pid='+$J('#pid').val()+'&from=&newdata=1&target=&ptype=ic';
		}else
			alert('No Permission in Show Person Registration');	
	}

	function showRegUpdate(){

	var UpdatePersoRegis = '<?=$UpdatePersoRegis?>';
    var medocs = '<?=$allpermission?>';

	    if (medocs){
   		window.location.href = '../../modules/registration_admission/patient_register.php?ntid=false&lang=en&pid='+$J('#pid').val()+'&update=1&ptype=ic';
		} else if (UpdatePersoRegis == 1){
   		window.location.href = '../../modules/registration_admission/patient_register.php?ntid=false&lang=en&pid='+$J('#pid').val()+'&update=1&ptype=ic';
		} else 
			alert('No Permission in Update Person Registration');	
	}


</script>
<div class="calendar" style="position: absolute; display: none; left: 10px; top: 500px;">
</div>

<?php
$tr_obj = new SegICTransaction();
$objResponse = new xajaxResponse();

$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
global $allowedarea;

//$strDate="08/28/2010 12:09 PM";
//$strDate=date("Y-m-d H:i:s a",strtotime($strDate));
//echo $strDate;



if(isset($_GET['refno'])){

	if($_GET["process"]=="add"){
		$strMsg="Transaction successfully saved!";		
	}
	elseif(($_GET["process"]=="update")){
		$strMsg="Transaction successfully updated!";
	}
	elseif(($_GET["process"]=="view")){
		$isDischarged = $tr_obj->isDischarged($_GET['refno']);
		$strMsg=($isDischarged == 0 ? '' : 'Transaction DISCHARGED');
	}
		

	if($_GET["process"]=="add" or $_GET["process"]=="update" or $_GET["process"]=="view"){
		if ($strMsg != '') {
			$outputResponse='<dl id="system-message">
						<dt>Information</dt>
						<dd>'.$strMsg.'
						</dd>
					</dl>';
		}
			
	}
	else
		$outputResponse="";
		$refno=$_GET['refno'];
			#echo $refno;
			 $data=$tr_obj->getTransactionData($_GET['refno']);
			 $pid=$data['pid'];
			 $encounter_nr=$data['encounter_nr'];
			 $pid=$data['pid'];
			 $default_date=date('m/d/Y',strtotime($data['trxn_date']));
			 $default_time=date('g:i',strtotime($data['trxn_date']));
			 $ampm=strtoupper(date('a',strtotime($data['trxn_date'])));
			 $purpose_exam_id=$data['purpose_exam'];
			 $purpose_exam_other = ($purpose_exam_id == 'OT' ? $tr_obj->getExamPurposeOthers($_GET['refno']) : '');
			 $remarkString=$data['remarks'];
			 $agency_charged=$data['agency_charged'];

			 #edited by art 02/20/2014
			/*if charge to agency*/
			 if($agency_charged==1){ 
			 	$agency_charged="checked='checked'";
			 	$position=$data['position'];
			 	$company_id=$data['company_id'];
			 	$company_name=$data['name'];
			 	$employee_id=$data['employee_id'];
			 	$job_status=$data['job_status'];
			 	$jStatusVal=$job_status;
				 if($job_status=="regular")
					$jStatus['regular']="checked='checked'";
				 elseif($job_status=="job_order")
					$jStatus['job_order']="checked='checked'";
				 elseif($job_status=="contractual")
					$jStatus['contractual']="checked='checked'";
				 elseif($job_status=="consultant")
					$jStatus['consultant']="checked='checked'";
				 elseif($job_status=="other")
					$jStatus['other']="checked='checked'";
				 elseif($job_status=="student")
					$jStatus['student']="checked='checked'";
			 } #end art
			#_a_3_CanUpdate

		$allowedarea = array('_a_3_CanUpdate');
		if (validarea($_SESSION['sess_permission'],1)) {
			 $submitButton='<img src="'.$root_path.'gui/img/control/default/en/en_update_data.gif" id="saveButton" name="saveButton" title="Update" onclick="doSave(2);" class="segSimulatedLink" />';
		}else {
			 $submitButton='<img src="'.$root_path.'gui/img/control/default/en/en_update_data.gif" id="saveButton" name="saveButton" title="No Permission"  class="disabled" />';
		}
}
else{
	#for new registration of transaction
	$pid=$_GET['pid'];

	$default_date=date("m/d/Y");
	$default_time=date("g:i");
	$ampm= strtoupper(date("a"));

	$allowedarea = array('_a_3_CanUpdate');
		if (validarea($_SESSION['sess_permission'],1)) {
	$submitButton='<img src="'.$root_path.'gui/img/control/default/en/en_savedisc.gif" id="saveButton" name="saveButton" title="Save" onclick="doSave(1);" class="segSimulatedLink"/>';
		}else {
			$submitButton='<img src="'.$root_path.'gui/img/control/default/en/en_savedisc.gif" id="saveButton" name="saveButton" title="No Permission" class="disabled"/>';
		}
}

$result=$tr_obj->getPersonData($pid);
$hrn='<input type="text" style="color: rgb(0, 102, 0); font: bold 16px Arial;" readonly="readonly" value="'.$pid.'" class="clear" name="hrn" id="hrn">';
if($result['photo_filename']!='') {
	$photo_src = $root_path.'fotos/registration/'.$result['photo_filename'];
}
else
	$photo_src = '../../gui/img/control/default/en/en_x-blank.gif';
$smarty->assign('img_source',$photo_src);
$full_name=$result["full_name"];
//$address=$result["address"];
if($result['citizenship'] != 'PH') {
    $address = $result['street_name'];
}else {
    $address = $result['address'];
}

$full_name='<input type="text" value="'.$full_name.'" readonly="readonly" style="font: bold 12px Arial; color: rgb(0, 0, 255);" size="30" name="name" id="name" class="segInput">';
$address='<textarea readonly="readonly" style="width: 100%; font: bold 12px Arial; border: 1px solid rgb(195, 195, 195); overflow-y: scroll; float: left;" name="address" id="address" class="segInput">
'.$address.'
</textarea>';
// var_dump($result);
 // added by Kenneth 04-06-2016
$age="";
if($result['date_birth']=="0000-00-00"){
    $age=$result['age'];
}
else{
    $age = floor((time() - strtotime($result['date_birth']))/31556926); #added by art 02/20/2014;
}
// end Kenneth

 // if($result['date_birth']="0000-00-00"){
 // 	$age="No date of birth";
 // }

//added by Nick 05-19-2014
//commented - not compatible for php 5.2
//$d1 = date_create($result['date_birth']);
//$d2 = date_create(date('Y-m-d'));
//$age = date_diff($d1,$d2);
//$age = $age->y;
if($age >= 60){
    $tr_obj->setSeniorCitizenDiscount($pid);//added by Nick 06-23-2014
}
//end Nick

if($result['sex']=="m"){
	$gender='male';
}else	$gender='female';
$birthday=$result['date_birth'];
$civil_status=$result['civil_status'];


$age='<input type="text" value="'.$age.'" readonly="readonly" style="font: bold 12px Arial;" size="15" name="age" id="age" class="segInput">';
$gender='<input type="text" value="'.$gender.'" readonly="readonly" style="font: bold 12px Arial;" size="15" name="gender" id="gender" class="segInput">';
$birthday='<input type="text" value="'.$birthday.'" readonly="readonly" style="font: bold 12px Arial;" size="20" name="birthday" id="birthday" class="segInput">';
$civil_status='<input type="text" value="'.$civil_status.'" readonly="readonly" style="font: bold 12px Arial;" size="20" name="civil_status" id="civil_status" class="segInput">';



$smarty->assign('hrn',$hrn);
$smarty->assign('full_name', $full_name);
$smarty->assign('address', $address);
$smarty->assign('age', $age);
$smarty->assign('gender', $gender);
$smarty->assign('birthday', $birthday);
$smarty->assign('civil_status',$civil_status);


$transaction_date='
<input type="text"
 id="transaction_date" style="font: bold 12px Arial;"
 value="'.$default_date.'"
 maxlength="10"
 size="10"
 name="transaction_date"/>
<img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;" id="transaction_date_trigger"
src="'.$root_path.'gui/img/common/default/show-calendar.gif">'.
'<script type="text/javascript">
							Calendar.setup ({
								inputField : "transaction_date", ifFormat : "%m/%d/%Y",
								 showsTime : false,
								 button : "transaction_date_trigger",
								 singleClick : true,
								 step : 1
							});
</script>';

$transaction_time='
						<span style="color: rgb(128, 0, 0);">
						<input type="text" '. ' onchange="'."setFormatTime(this,'selAMPM1')".'" '.
'value="'.$default_time.'" maxlength="5" size="4" name="transaction_time" id="transaction_time" style="font: bold 12px Arial;">
						<select name="selAMPM1" id="selAMPM1"  style="font: bold 12px Arial; visibility: visible;">
							<option value="AM" ';


if($ampm=="AM")
	$transaction_time=$transaction_time.'selected="selected" ';
$transaction_time=$transaction_time.' >AM</option> ';

$transaction_time=$transaction_time.'<option value="PM"';
if($ampm=="PM")
	$transaction_time=$transaction_time.'selected="selected" ';
$transaction_time=$transaction_time.' >PM</option>
						</select>&nbsp;<font size="1">[hh:mm]</font></span>';

#added by VAN 03-01-2011
#to check if the patient is a personnel of the hospital
/*
$pers_obj = new Personell();
$objInfo = new Hospital_Admin();

$personnel_info = $pers_obj->is_personnel($pid);

if (!empty($personnel_info[nr]))
	$is_personnel = true;
else
	$is_personnel = false;

if ($row = $objInfo->getAllHospitalInfo()) {
	$hosp_id = $row['hosp_id'];
}

if ($is_personnel){
	$agency_charged = "checked='checked'";

	$hosp_info = $tr_obj->getCompanyInfo($hosp_id);
	$company_name = $hosp_info['name'];
	$company_id = $hosp_info['company_id'];

	$job_position = trim($personnel_info['job_position']);
	if (!empty($job_position))
		$position = $personnel_info['job_position'];
	else
		$position = $personnel_info['job_function_title'];
	$employee_id = $personnel_info['nr'];
}
*/#commented by art 02/19/2014
//$encounter_nr= $_POST['caseNo']?$_POST['caseNo']:$encounter_nr;
$caseNo=
			"<input type='hidden' id='refno' name='refno' class='segInput' value='$refno'/>".
			"<input type='hidden' id='pid' name='pid' class='segInput' value='$pid'/>".
			"<input type='text' id='caseNo' name='caseNo' style='font: bold 12px Arial;' class='segInput' value='".$encounter_nr."' readonly='true' style='font: bold 12px Arial;'/>";
$chargeToAgency=
			'<input type="checkbox" name="ischargeToAgency" '.$agency_charged.' id="ischargeToAgency" class="segInput" onclick="CheckIsAgency();" />';
$agency_organization=
			"<input type='text' style='font: bold 12px Arial;' id='agency_organization' name='agency_organization' size='40' class='segInput' readonly='true' value='$company_name' style='font: bold 12px Arial;'/>".
			"<input type='hidden' id='agency_organization_id' name='agency_organization_id' size='40' class='segInput' value='$company_id'/>";
//$agency_organization=$agency_organization.
//	'<img border="0" id="com_search" name="com_search"  align="absmiddle" onclick="CompanySearch('.$pid.')" alt="Company Search" src="../../images/his_searchbtn.gif" class="segSimulatedLink">';

$agency_organization=$agency_organization.
'<button disabled="false" id="com_search" name="com_search" onclick="CompanySearch('.$pid.');return false;"    class="segButton"><img style="cursor: pointer;"
src="../../gui/img/common/default/zoom.png">Search</button>
'."<script type='text/javascript' language='javascript'>
		CheckIsAgency();
	</script>";

$position="<input type='text' style='font: bold 12px Arial;' id='position' name='position' size='40' class='segInput' disabled='true'
value='$position' />";
$id_no="
	<input type='hidden' style='font: bold 12px Arial;' id='id_no_status' name='id_no_status' size='40' class='segInput' readonly='true' value=''/>
	<input type='text' style='font: bold 12px Arial;' id='id_no' name='id_no' size='40' class='segInput' disabled='true' value='$employee_id'/> ";
$status="<input type='hidden' name='status' id='status'  value='".$jStatusVal."'  />

			<input name='statusR1' id='statusR1' type='radio' onclick='CheckIsJobStatus(this.id)' disabled='disabled' value='regular' ".$jStatus['regular']."  />
				Regular&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input name='statusR2' id='statusR2' onclick='CheckIsJobStatus(this.id)' type='radio' disabled='disabled'  value='job_order' ".$jStatus['job_order']."  />
				Job Order&nbsp;&nbsp;
			<input name='statusR3' id='statusR3' onclick='CheckIsJobStatus(this.id)' type='radio' disabled='disabled' value='student' ".$jStatus['student']."   />
				Student
				<br>
			<input name='statusR4' id='statusR4' onclick='CheckIsJobStatus(this.id)' type='radio' disabled='disabled' value='contractual' ".$jStatus['contractual']."  />
				Contractual
			<input name='statusR5' id='statusR5' onclick='CheckIsJobStatus(this.id)' type='radio' disabled='disabled' value='consultant' ".$jStatus['consultant']." />
				Consultant
			<input name='statusR6' id='statusR6' onclick='CheckIsJobStatus(this.id)' type='radio' disabled='disabled' value='other' ".$jStatus['other']."  />
				Other
			";
# added by gervie 06/11/2015
$histSQL = "SELECT `smoker_history`, `drinker_history` FROM `care_encounter` WHERE `encounter_nr` = '$encounter_nr'";
$histResult = $db->Execute($histSQL);

if($histResult)
{
    //$smokeValue = $row['smoker_history'];
    //$drinkValue = $row['drinker_history'];
    while($row = $histResult->fetchRow())
    {
        if($row['smoker_history'] == 'yes')
        {
            $hSmoke['yes'] = "checked='checked'";
        }
        elseif($row['smoker_history'] == 'no')
        {
            $hSmoke['no'] = "checked='checked'";
        }
        elseif($row['smoker_history'] == 'na')
        {
            $hSmoke['na'] = "checked='checked'";
        }
        
        if($row['drinker_history'] == 'yes')
        {
            $hDrink['yes'] = "checked='checked'";
        }
        elseif($row['drinker_history'] == 'no')
        {
            $hDrink['no'] = "checked='checked'";
        }
        elseif($row['drinker_history'] == 'na')
        {
            $hDrink['na'] = "checked='checked'";
        }
    }
}

$hSmoking="<input type='radio' name='smoker_history' id='smokingR1' value='yes' " . $hSmoke['yes'] . " /> YES
           <input type='radio' name='smoker_history' id='smokingR2' value='no' " . $hSmoke['no'] . " /> NO
           <input type='radio' name='smoker_history' id='smokingR3' value='na' " . $hSmoke['na'] . " /> N/A";
$hDrinker="<input type='radio' name='drinker_history' id='drinkingR1' value='yes' " . $hDrink['yes'] . " /> YES
           <input type='radio' name='drinker_history' id='drinkingR2' value='no' " . $hDrink['no'] . " /> NO
           <input type='radio' name='drinker_history' id='drinkingR3' value='na' " . $hDrink['na'] . " /> N/A";
# end gervie
$purpose_exam=
		'<select name="purpose_exam" class="segInput" id="purpose_exam" title="Purpose of Exam"  style="font: bold 12px Arial; visibility: visible;" onchange="purpose();">'.
		'<option value="">-Select-</option> ';
//$others = "<input type='text' style='font: bold 12px Arial; margin-left:5px;' id='purpose_exam_other' name='purpose_exam_other' size='20' class='segInput'  value='".$purpose_exam_other."' />";

# commented out by: syboy 10/19/2015 : meow
// if($purpose_exam_other == "Labs & PE"){
//     $other_1 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "Labs, ECG, UTZ & PE")
// {
//     $other_2 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "Ultrasound Only")
// {
//     $other_3 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "X-Ray Only")
// {
//     $other_4 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "Mammogram")
// {
//     $other_5 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "ECG Only")
// {
//     $other_6 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "Vaccination")
// {
//     $other_7 = 'selected="selected"';
// }
// elseif($purpose_exam_other == "2nd Copy - Med Cert./Lab")
// {
//     $other_8 = 'selected="selected"';
// }
// $others = '&nbsp;&nbsp;&nbsp;<select name="purpose_exam_other" class="segInput" id="purpose_exam_other" style="font: bold 12px Arial; visibility: visible;">'.
//           '<option value="">-Select-</option>'.
//           '<option value="Labs & PE" ' . $other_1 . '>Labs & PE</option>'.
//           '<option value="Labs, ECG, UTZ & PE" ' . $other_2 . '>Labs, ECG, UTZ & PE</option>'.
//           '<option value="Ultrasound Only" ' . $other_3 . '>Ultrasound Only</option>'.
//           '<option value="X-Ray Only" ' . $other_4 . '>X-Ray Only</option>'.
//           '<option value="Mammogram" ' . $other_5 . '>Mammogram</option>'.
//           '<option value="ECG Only" ' . $other_6 . '>ECG Only</option>'.
//           '<option value="Vaccination" ' . $other_7 . '>Vaccination</option>'.
//           '<option value="2nd Copy - Med Cert./Lab" ' . $other_8 . '>2nd Copy - Med Cert./Lab</option>'.
//           '</select>';
# ended

# added by: syboy 10/19/2015 : meow
$others .= '&nbsp;&nbsp;&nbsp;<select name="purpose_exam_other" class="segInput" id="purpose_exam_other" style="font: bold 12px Arial; visibility: visible;">';
$sql = "SELECT * FROM seg_industrial_purpose_other WHERE status = 0 ORDER BY NAME";
$rs = $db->Execute($sql);
if ($rs) {
	$others .= '<option value="">-Select-</option>';
	while ($row = $rs->FetchRow()) {
		if ($row['name'] == $purpose_exam_other) {
			$others .= '<option value="'.$row["name"].'" selected="selected">'.$row["name"].'</option>';
		}else {
			$others .= '<option value="'.$row["name"].'" >'.$row["name"].'</option>';
		}
	}
}
$others .= '</select>';
#ended 

$strSQL="select `id`,`name` from seg_industrial_purpose ORDER BY `name` ASC";
$items="";
$result=$db->Execute($strSQL);

if($result){
		 while($row=$result->FetchRow()){
				if($row["id"]==$purpose_exam_id){
					$items=$items.'<option value="'.$row["id"].'" selected="selected">'.$row["name"].'</option>';
				}
				else{
					$items=$items.'<option value="'.$row["id"].'">'.$row["name"].'</option>';
				}

		 }
}
$medChart = ($CanViewMedExamChart) ? 1 : 0;
$allPermission = ($medocs) ? 1 : 0;
$purpose_exam=$purpose_exam.$items.
		'</select>';
$remarks='<textarea style="font: bold 12px Arial;" name="textarea" cols="50" id="remarks" name="remarks" class="segInput" value="'.$remarkString.'" style="width: 100%; overflow-y: scroll;">'.$remarkString.'</textarea>';
$cancelButton='<img border="0" align="absmiddle" src="'.$root_path.'images/btn_cancelorder.gif" id="cancelButton" name="cancelButton" title="Cancel" onclick="doCancel(\''.$breakfile.'\')" class="segSimulatedLink" >';
$medExam = '<a onmousedown="nd()" onclick="openMedExamChart('. $medChart . ',' . $allPermission .');" href="javascript:void(0)"> <img border="0" src="../../gui/img/common/default/chart.gif" /> Medical Exam Chart</a>';
$smarty->assign('transaction_date', $transaction_date);
$smarty->assign('transaction_time', $transaction_time);
$smarty->assign('caseNo', $caseNo);
$smarty->assign('chargeToAgency', $chargeToAgency);
$smarty->assign('agency_organization', $agency_organization);
$smarty->assign('position', $position);
$smarty->assign('id_no', $id_no);
$smarty->assign('status', $status);
# added by gervie 06/11/2015
$smarty->assign('hSmoking', $hSmoking);
$smarty->assign('hDrinker', $hDrinker);
$smarty->assign('purpose_exam', $purpose_exam);
# end gervie
$smarty->assign('others', $others);
$smarty->assign('remarks', $remarks);

$smarty->assign('submitButton', $submitButton);
$smarty->assign('cancelButton', $cancelButton);
$smarty->assign('outputResponse', $outputResponse);
$smarty->assign('isDischarged', $isDischarged);
$smarty->assign('medExamLink', $medExam);
$smarty->assign('allow_accessFollowUpForm',$allow_accessFollowUpForm);


$smarty->assign('form_start','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND.'" method="POST" id="transaction_form" name="transaction_form">');
$smarty->assign('form_end','</form>');

ob_start();
$sTemp='';

?>
<input type="hidden" name="submitted" value="1" />
<input type="hidden" name="sid" value="<?php echo $sid?>">
<input type="hidden" name="lang" value="<?php echo $lang?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">
<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
<input type="hidden" name="lockflag" value= "<?php echo  $lockflag?>" >


<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='close2.gif';

$smarty->assign('sHiddenInputs',$sTemp);
if (!$viewonly) {
	$smarty->assign('sContinueButton','<input type="image" class="segSimulatedLink" src="'.$root_path.'images/btn_submitorder.gif" align="absmiddle" alt="Submit">');
	$smarty->assign('sBreakButton','<img class="segSimulatedLink" src="'.$root_path.'images/btn_cancelorder.gif" alt="'.$LDBack2Menu.'" align="absmiddle" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;">');
}

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','industrial_clinic/transaction-form.tpl');
$smarty->display('common/mainframe.tpl');

