<?php
/*
Created: Jayson-OJT, Dialysis module - List of Patients
Content: list of patients per day with respective machines.
Search: by hrn, date.
*/
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/dialysis/ajax/dialysis-transaction.common.php');

define('NO_2LEVEL_CHK',1);
define('LANG_FILE','lab.php');

$local_user='ck_dialysis_user';
require_once $root_path.'include/inc_front_chain_lang.php';

$GLOBAL_CONFIG=array();
#added by art 03/16/2015
require_once($root_path.'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_temp_userid']);
$Edit_permission = $objAcl->checkPermissionRaw('_a_1_dialysisedit');
$Request_permission = $objAcl->checkPermissionRaw('_a_1_dialysisrequest');
#end art
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;

$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd)); //4 digits for year value

// $phpfd=str_replace("yy","%y", strtolower($phpfd));

if (!$_GET['from'])
	$breakfile=$root_path."modules/dialysis/seg-dialysis-menu.php".URL_APPEND;
else {
	if ($_GET['from']=='CLOSE_WINDOW')
		$breakfile = "javascript:window.parent.cClick();";
	else
		$breakfile=$root_path."modules/dialysis/seg-dialysis-menu.php".URL_APPEND;
}

$thisfile='seg-dialysis-machine-list.php';

require_once $root_path."include/care_api_classes/dialysis/class_dialysis.php";
require_once $root_path."include/care_api_classes/class_encounter.php";
$dialysis_obj = new SegDialysis();
$enc_obj = new Encounter($encounter_nr);
global $db;

require_once $root_path.'gui/smarty_template/smarty_care.class.php';
$smarty = new smarty_care('common');

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);
$title = "Dialysis :: List of Patients";

# Title in the title bar
$smarty->assign('sToolbarTitle', $title);

# Window bar title
$smarty->assign('sWindowTitle', $title);

$smarty->assign('pbClose','<a href="'.$breakfile.'"><img '.createLDImgSrc($root_path,'close2.gif','0','absmiddle').'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp');


ob_start();

?>

<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

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
<script type="text/javascript" src="<?=$root_path?>modules/dialysis/js/request-main.js"></script>

<script type="text/javascript">

	function isValidSearch(key) {
		// var ref_source = $('search_by_input').value;
		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();

		return (
			/^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
			/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
			/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
			/^\d+$/.test(s)
		);
	}

	

	function emptier(){
		document.getElementById('search_by_input').value='';
		if($('search_select').checked){
			document.getElementById('search_by_input').style.display = "";
			document.getElementById("search-btn").disabled = true;
		}else{
			document.getElementById('search_by_input').style.display = "none";
			document.getElementById("search-btn").disabled = false;
		}
	}

	
	function DisabledSearch(){
		if($('search_select').checked){
			var in_search = $('search_by').value;
			
			if($('search_by').value == 'by_hrn'){

				var b=isValidSearch(document.getElementById('search_by_input').value);
				document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
				document.getElementById("search-btn").disabled = !b;

			}else if($('search_by').value == 'by_name'){

				var b=isValidSearch(document.getElementById('search_by_input').value);
				document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
				document.getElementById("search-btn").disabled = !b;

			}	

		}
	}

	
	function openMachinesPatientsDate(){ 

		if($('select_date').value == ""){
			var ans = alert("Please select a date first.");
			return false;
		}else{
			
			var in_date = $('select_date').value;
			var in_hrn = $('search_by_input').value;
			var in_search = $('search_by').value;

			var in_selectSearch = '0';

			//identifier: for check box if checked 1 else 0
			if($('search_select').checked){
				 in_selectSearch = '1';
			}
			//end identifier
			window.location.href = "<?php echo $thisfile.URL_REDIRECT_APPEND; ?>&userck=<?php echo $_GET['userck']; ?>
				&from=<?php echo $_GET['from'];?>&checkintern=<?php echo $_GET['checkintern']?>&input_date="+in_date
				+"&input_hrn="+in_hrn+"&search_by="+in_search+"&select_search="+in_selectSearch;
			return true;
		}
	}

	function openRequestTray(encounter_nr,pid){ // to open Laboratory Request Tray, Encounter Nr and PID NEEDED!.
		overlib(
		OLiframeContent('<?=$root_path?>modules/clinics/seg-clinic-charges.php<?php echo URL_REDIRECT_APPEND; ?>&pid='
						+pid+'&encounter_nr='+encounter_nr+"&userck=<?php echo $_GET['userck']; ?>
						&from=<?php echo $_GET['from'];?>&checkintern=<?php echo $_GET['checkintern']?>",
				800, 500, 'fGroupTray', 0, 'auto'),
				WIDTH,800, TEXTPADDING,0, BORDER,0,
				STICKY, SCROLL, CLOSECLICK, MODAL,
				CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
				CAPTIONPADDING,2, CAPTION,'New Test Request',
				MIDX,0, MIDY,0,
				STATUS,'New Test Request');
		return false;
	}

	function updateTransaction(transactionNr)
	{
		var url = '../../index.php?r=dialysis/dialysisTransaction/makeTransaction/transactionNr/'+transactionNr;
		overlib(OLiframeContent(url,900, 500, 'fGroupTray', 0, 'auto'),
			WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
			STICKY, SCROLL, CLOSECLICK, MODAL,
			CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
			CAPTIONPADDING, 2, CAPTION, 'Edit Patient Details',
			MIDX, 0, MIDY, 0,
			STATUS, 'Edit Patient Details'
		);
	}

    function Edit(transaction_nr, machine_nr, encounter_nr, pid, ops_entryno) {
        var current_date = $('select_date').value;
        var $url = "<?=$root_path?>modules/dialysis/machine-list-edit.php";
        $url+= "<?php echo URL_REDIRECT_APPEND; ?>";
        $url+= "&tnr="+transaction_nr;
        $url+= "&machine_nr="+machine_nr;
        $url+= "&encounter_nr="+encounter_nr;
        $url+= "&pid="+pid;
        $url+= "&current_date="+current_date;
        $url+= "&userck=<?php echo $_GET['userck']; ?>";
        $url+= "&from=<?php echo $_GET['from'];?>";
        $url+= "&checkintern=<?php echo $_GET['checkintern']?>";
        $url+= "&entry_no="+ops_entryno;

        overlib(OLiframeContent($url,800, 450, 'fGroupTray', 0, 'auto'),
            WIDTH, 800, TEXTPADDING, 0, BORDER, 0,
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>/images/close_red.gif border=0 >',
            CAPTIONPADDING, 2, CAPTION, 'Edit Patient Details',
            MIDX, 0, MIDY, 0,
            STATUS, 'Edit Patient Details'
        );
    }
	
	function initialize(){
		if($('search_select').checked){
			document.getElementById('search_by_input').style.display = "";
			document.getElementById("search-btn").disabled = false;
		}
	}

	document.observe('dom:loaded', initialize);

</script>

<?php

	if(isset($_GET['input_date']))
	{
		$input_date = $_GET['input_date'];
	}
	else
	{
		$input_date = date("m/d/Y");
	}


	if(isset($_GET['input_hrn']))
	{
		 $hrn_no = $_GET['input_hrn'];
	}
	else
	{
		$hrn_no = '';
	}


	if(isset($_GET['search_by']))
	{
		 $search = $_GET['search_by'];
		 if($search == 'by_hrn')
		 {
		 	$by_hrn = ' selected="selected" ';
		 }
		 else if($search == 'by_name')
		 {
		 	$by_name = ' selected="selected" ';
		 }
	}

	

	if($_GET['select_search'] == 1){
		$box_check = ' checked="checked" ';
	}else{
		$box_check="";
	}



$smarty->assign('search_select', '<input class="segInput" type="checkbox" name="search_select" id="search_select" onclick="emptier();" '.$box_check.' > ');
$smarty->assign('search_by', '<select class="segInput" name="search_by" id="search_by" >
								<option value="by_hrn" '.$by_hrn.'>HRN</option>
								<option value="by_name" '.$by_name.'>Patient Name</option>
							  </select>'); 

$smarty->assign('search_box', '<input type="text" class="segInput" name="search_by_input" id="search_by_input" value="'.$hrn_no.'" style="width: 100px; display: none" onkeyup="DisabledSearch()">'); 

$smarty->assign('date', '<input class="segInput" name="select_date" id="select_date" type="text" size="12" value="'.$input_date.'"/>
												<img src="'.$root_path.'gui/img/common/default/calendar_add.png" id="tg_select_date" align="absmiddle" style="cursor:pointer;"  />');
$smarty->assign('date_js', '<script type="text/javascript">
														Calendar.setup ({
																inputField : "select_date", ifFormat : "'.$phpfd.'", showsTime : false, button : "tg_select_date", singleClick : true, step : 1
														});
												</script>');

$smarty->assign('view_btn', '<button class="segButton" id="search-btn" name="search-btn" onclick="openMachinesPatientsDate();" style="cursor:pointer"><img src="'.$root_path.'gui/img/common/default/statbel2.gif"/>View List of Patients</button>');


$smarty->assign('LDMachineNo','Machine No.');	
$smarty->assign('LDGenderInfo','Gender');
$smarty->assign('LDFamilyName','Last Name');
$smarty->assign('LDName','Given Name');
$smarty->assign('LDPatNr','HRN');
$smarty->assign('BillNr','Case Number');
$smarty->assign('LDDialyserUsed','Dialyser Used');
$smarty->assign('LDPrev','Prev');
$smarty->assign('LDPres','Pres');
// $smarty->assign('LDNew','New');
$smarty->assign('LDOptions','Options');


$list_of_patients = $dialysis_obj->getMachines($input_date, $hrn_no, $search_by, '');

#for row color.
$count = 0;

#array for list of machine..
$ar_machine_nr = array();
if (empty($list_of_patients)) {
	$smarty->assign('sNodata',TRUE);
}
foreach($list_of_patients as $row)
{

	#just for row colors
	$count = $count +1;
	 if($count%2 == 1)
	 {
		$smarty->assign('bToggleRowClass',TRUE);
		$class_label = "wardlistrow1";
		$smarty->assign('class_label',$class_label);
	}
	else
	{
		$smarty->assign('bToggleRowClass',FALSE);
		$class_label = "wardlistrow2";
		$smarty->assign('class_label',$class_label);
	}
	#end row colors

	
	$sBuffer = '<a href="javascript:popPic('.$row["pid"].')">';


	$patient_exists = 'false';
	#identifier if there is a patient in the current machine.
	if(isset($row["pid"]))
	{
		$patient_exists = 'true';
	}
	
	#start queue
	#to queue list of patient with the same machine number without  displaying the same machine number
	$ar_machine_nr[$count] = $row['machine_nr']; 
	if($count != 0 )
	{
		if($ar_machine_nr[$count] != $ar_machine_nr[$count - 1])
		{
			$smarty->assign('sMachineNumber', $row['machine_nr']);
		}
		else
		{
			$smarty->assign('sMachineNumber', '');
		}
	}
	else
	{
		$smarty->assign('sMachineNumber', $row['machine_nr']);
	}
	#end queue


	#for gender icon
	if($row['sex'] == 'f' && $patient_exists == 'true')
	{
		$smarty->assign('sGenderInfo',$sBuffer.'<img '.createComIcon($root_path,'spf.gif','0','',TRUE).'></a>');
	}
	else if($row['sex'] == 'm' && $patient_exists == 'true')
	{
		$smarty->assign('sGenderInfo',$sBuffer.'<img '.createComIcon($root_path,'spm.gif','0','',TRUE).'></a>');
	}
	else
	{
		$smarty->assign('sGenderInfo','<img '.createComIcon($root_path,'spm.gif','0','',TRUE).' style="opacity: 0">');
		#default of jud row spacing, opacity: 0
	}
	#enc gender icon

	
	$smarty->assign('sFamilyName', $row['name_last']);	
	

	#start - comma if pid exists show comma
	if($patient_exists == 'true')
	{
		$smarty->assign('cComma',', ' );
	}
	else
	{
		$smarty->assign('cComma','' );
	}
	#end comma
					
	$smarty->assign('sName', $row['name_first']." ".$row['name_middle']);
	$smarty->assign('sPatNr', $row['pid']);
	$smarty->assign('sEnc', $row['bill_nr']);

	if(isset($row['dialyzer_count'])){
		$smarty->assign('sPrev', ($row['dialyzer_count']==0 ? 0 : $row['dialyzer_count']));
		$smarty->assign('sPres', ($row['dialyzer_count']==0 ? 1 : $row['dialyzer_count']+1));	
	}else{
		$smarty->assign('sPrev','');					
		$smarty->assign('sPres','');
	}	



	$row_date = substr($row['service_date'], 0, 10);

	#added by art 03/16/2015
	if ($row['is_discharged'] == 1) {
		$disablereq = 'disabled title="discharged"';
		$disableedit = 'disabled title="discharged"';
	}else{
		$disablereq = '';
		$disableedit = '';
		if ($Request_permission == 0) {
			$disablereq = 'disabled title="No permission"';
		}
		if ($Edit_permission == 0) {
			$disableedit = 'disabled title="No permission"';
		}
	}
	#end art
	
	if($patient_exists == 'true')
	{
		$smarty->assign('sRequestTray',
		'<button id="reqbtn" class="segButton" '.$disablereq.' onclick="openRequestTray(\''.$row["encounter_nr"].'\',\''.$row['pid'].'\');return false;"  style="height: 30"><img src="../../gui/img/common/default/cart_add.png"/>Request</button>
		<button id="editbtn" class="segButton" '.$disableedit.' title="Edit" onclick="updateTransaction(\''.$row["transaction_nr"].'\')" style="height: 30"><img src="../../gui/img/common/default/pencil.png"/  ></button>');
	}
	else
	{
		$smarty->assign('sRequestTray', '');
	}

	ob_start();
	$smarty->display('dialysis/machine_occupancy_list_row.tpl'); //looping per row.
	$sListRows = $sListRows.ob_get_contents();
	ob_end_clean();
	$smarty->assign('sOccListRows',$sListRows);

}

 $smarty->assign('sMainBlockIncludeFile','dialysis/machine_occupancy.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

 ?>
