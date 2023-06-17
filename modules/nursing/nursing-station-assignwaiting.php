<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('aufnahme.php','prompt.php','person.php');
define('LANG_FILE','nursing.php');
//define('NO_2LEVEL_CHK',1);
$local_user='ck_pflege_user';
require_once($root_path.'include/inc_front_chain_lang.php');

if(empty($HTTP_COOKIE_VARS[$local_user.$sid])){
		$edit=0;
	include($root_path."language/".$lang."/lang_".$lang."_".LANG_FILE);
}
/**
* Set default values if not available from url
*/
if (!isset($station)||empty($station)) { $station=$HTTP_SESSION_VARS['sess_nursing_station'];} # Default station must be set here !!
if(!isset($pday)||empty($pday)) $pday=date('d');
if(!isset($pmonth)||empty($pmonth)) $pmonth=date('m');
if(!isset($pyear)||empty($pyear)) $pyear=date('Y');
$s_date=$pyear.'-'.$pmonth.'-'.$pday;
if($s_date==date('Y-m-d')) $is_today=true;
	else $is_today=false;

if(!isset($mode)) $mode='';

$breakfile='javascript:window.close()'; # Set default breakfile

/* Create ward object */
require_once($root_path.'include/care_api_classes/class_ward.php');
$ward_obj= new Ward;

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj= new Encounter;

# Load date formatter
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_globalconfig.php');

#added by VAN 06-08-2010
#require($root_path.'include/inc_checkdate_lang.php');
echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>';
echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>';
#echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>';
echo '<script language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>';

echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jquery/themes/seg-ui/jquery.ui.all.css">';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';

#-------------

#echo "b4 : pn, ward, station, pat_station, transfer = '".$pn."' , '".$ward_nr."' , '".$station."' , '".$pat_station."' , '".$transfer."'";
#added by VAN
if ($_GET['ward_nr']){
	$ward_nr = $_GET['ward_nr'];
	#commented by VAN 05-18-2010
	#$ward_info2=&$ward_obj->getWardInfo($ward_nr);
	#$station = $ward_info2['ward_id'];
}
#echo "ward_nr, station = ".$ward_nr." , ".$station;
#if ($_GET['station'])
#	$station = $_GET['station'];
#echo "s= ".$ward_obj->sql;

if ($_GET['pat_station'])
$pat_station = $_GET['pat_station'];

if ($_GET['transfer'])
	$transfer = $_GET['transfer'];

if ($_GET['modetransfer'])
	$modetransfer = $_GET['modetransfer'];


// if ($_POST['correct']==1)
// 	$modetransfer = 'correct';
#echo "<br>assign mode = ".$modetransfer;
#echo "<br>after : pn, ward, station, pat_station, transfer = '".$pn."' , '".$ward_nr."' , '".$station."' , '".$pat_station."' , '".$transfer."'";

if ($_GET['waiting'])
		$waiting = $_GET['waiting'];
 elseif ($_POST['waiting'])
		$waiting = $_POST['waiting'];
 else
		$waiting = 0;

#if ($waiting)
	#$modetransfer = 'correct';

if(($mode=='')||($mode=='fresh')){
	if($ward_info=&$ward_obj->getWardInfo($ward_nr)){
	#edited by VAN 01-24-08
	#if($ward_info=&$ward_obj->getWardRoomInfo($ward_nr)){
		#$room_obj=&$ward_obj->getRoomInfo($ward_nr,$ward_info['room_nr_start'],$ward_info['room_nr_end']);
		#echo "sql = ".$ward_obj->sql;

		#edited by VAN 05-18-2010
		$room_obj=&$ward_obj->getRoomsData($ward_nr);

		if(is_object($room_obj)) {
			$room_ok=true;
		}else{
			$room_ok=false;
		}
		# GEt the number of beds
		$nr_beds=$ward_obj->countBeds($ward_nr);
		# Get ward patients
		if($is_today) $patients_obj=&$ward_obj->getDayWardOccupants($ward_nr);
			else $patients_obj=&$ward_obj->getDayWardOccupants($ward_nr,$s_date);
		#echo "<br>oc = ".$ward_obj->sql;
		if(is_object($patients_obj)){
			# Prepare patients data into array matrix
			while($buf=$patients_obj->FetchRow()){
				#echo "room = ".$buf['room_nr'];
				#echo "<br>bed = ".$buf['bed_nr']."<br>";
				#print_r($buf);						// edited by: syboy; 05/15/2015
				$patient[$buf['room_nr']][$buf['bed_nr']][]=$buf;
			}
			$patients_ok=true;
			$occup='ja';
		}else{
				$patients_ok=false;
		}

		$ward_ok=true;

		# Load global person photo source path
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$GLOBAL_CONFIG=array();
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('person_foto_path');
		$photo_path = (is_dir($root_path.$GLOBAL_CONFIG['person_foto_path'])) ? $GLOBAL_CONFIG['person_foto_path'] : $default_photo_path;

		#Create encounter object and load encounter info
		$enc_obj=new Encounter($pn);
		$enc_obj->loadEncounterData();
		if($enc_obj->is_loaded) {
			$encounter=&$enc_obj->encounter;
		}

		# Set the foto filename
		$photo_filename=$encounter['photo_filename'];
		/* Prepare the photo filename */
		require_once($root_path.'include/inc_photo_filename_resolve.php');

		# Get billing type
		$billing_type=&$enc_obj->getInsuranceClassInfo($encounter['insurance_class_nr']);

	}else{
			$ward_ok=false;
	}
}
#echo "sss = ".$ward_nr;

if(isset($transfer)&&$transfer){
	$TP_TITLE=$LDTransferPatient.' to '.strtoupper($ward_info['name']);
}else{
	$TP_TITLE= $LDAssignOcc.' '.strtoupper($station);
	$transfer=false;
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
 $smarty->assign('sToolbarTitle', $TP_TITLE);

	# hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('inpatient_assignbed.php','$mode','$occup','$station','$LDStation')");

 # href for close button
 $smarty->assign('breakfile',"javascript:window.close();");

 # OnLoad Javascript code
 $smarty->assign('sOnLoadJs','onLoad="if (window.focus) window.focus();"');

 # Window bar title
 $smarty->assign('sWindowTitle',$TP_TITLE);

 # Hide Copyright footer
 $smarty->assign('bHideCopyright',TRUE);

 # Collect extra javascript code

 ob_start();


?>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
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
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript">var $J = jQuery.noConflict();</script>

<script language="javascript">
<!--

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}/* end of function trimString */

//added by VAN 06-08-2010
var js_time = "";
function js_setTime(jstime){
	js_time = jstime;
}

function js_getTime(){
	return js_time;
}

function validateTime(S) {
		return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
}

var seg_validDate=true;
//var seg_validTime=false;

function seg_setValidDate(bol){
	seg_validDate=bol;
//	alert("seg_setValidDate : seg_validDate ='"+seg_validDate+"'");
}

var seg_validTime=false;
function setFormatTime(thisTime,AMPM){
//	var time = $('time_text_d');
//alert(AMPM);
	var stime = thisTime.value;
	var hour, minute;
	var ftime ="";
	var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
	var f2 = /^[0-9]\:[0-5][0-9]$/;
	var jtime = "";

	trimString(thisTime);

	if (thisTime.value==''){
		seg_validTime=false;
		return;
	}

	stime = stime.replace(':', '');

	if (stime.length == 3){
		hour = stime.substring(0,1);
		minute = stime.substring(1,3);
	} else if (stime.length == 4){
		hour = stime.substring(0,2);
		minute = stime.substring(2,4);
	}else{
		alert("Invalid time format.");
		thisTime.value = "";
		seg_validTime=false;
		thisTime.focus();
		return;
	}

	jtime = hour + ":" + minute;
	js_setTime(jtime);

	if (hour==0){
		 hour = 12;
		 document.getElementById(AMPM).value = "AM";
	}else	if((hour > 12)&&(hour < 24)){
		 hour -= 12;
		 document.getElementById(AMPM).value = "PM";
	}

	ftime =  hour + ":" + minute;

	if(!ftime.match(f1) && !ftime.match(f2)){
		thisTime.value = "";
		alert("Invalid time format.");
		seg_validTime=false;
		thisTime.focus();
	}else{
		thisTime.value = ftime;
		seg_validTime=true;
	}
}// end of function setFormatTime

//--------------------------------

	var urlholder;

function getrem(pn){
	urlholder="nursing-station-remarks.php<?php echo URL_REDIRECT_APPEND; ?>&pn="+pn+"<?php echo "&pday=$pday&pmonth=$pmonth&pyear=$pyear&station=$station"; ?>";
	patientwin=window.open(urlholder,pn,"width=700,height=500,menubar=no,resizable=yes,scrollbars=yes");

	/*return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-remarks.php<?php echo URL_REDIRECT_APPEND ?>&pn='+pn+'&pday=<? echo $pday ?>&pmonth=<? echo $pmonth ?>&pyear=<? echo $pyear ?>&station=<? echo $station ?>',
																	850, 450, 'fGroupTray', 0, 'auto'),
																	WIDTH,850, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose2();">',
																 CAPTIONPADDING,2, CAPTION,'Notes',
																 MIDX,0, MIDY,0,
																 STATUS,'Notes');*/
}

function belegen(pn,rm,bd){
	var datefrom = document.getElementById('datefrom').value;
	var timefrom = document.getElementById('timefrom').value;
	var selAMPM = document.getElementById('selAMPM').value;

<?php
if($encounter['current_ward_nr']!=$ward_nr){
echo '
if(confirm("'.$LDSureAssignRoomBed.'"))
';
	}else
?>
{
<?php
echo '
	urlholder="nursing-station.php?mode=newdata&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"
';
?>
	//alert(urlholder);
	//edited by Cherry 10-26-10
	window.opener.location.replace(urlholder);
	window.close();
	//window.location.href = urlholder;
	//alert('Assignment of bed is successfully saved');
	//pSearchClose();
	//window.parent.cClick();
}
}

// commented by VAN 01-25-08
/*
function transferBed(pn,rm,bd){
<?php
/*echo '
urlholder="nursing-station-transfer-save.php?mode=transferbed&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"
';*/
/*echo '
urlholder="nursing-station-transfer-save.php?mode=transferbed&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&datefrom="+datefrom+"&timefrom="+timefrom+"&selAMPM="+selAMPM+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"
';*/
?>
//window.opener.location.replace(urlholder);
//window.close();
}
*/

function tConvert (time) {
  // Check correct time format and split into components
  time = time.toString ().match (/^([01]\d|2[0-3])(:)([0-5]\d)(:[0-5]\d)?$/) || [time];

  if (time.length > 1) { // If time format is correct
    time = time.slice (1);  // Remove full string match value
    time[5] = +time[0] < 12 ? ' AM' : ' PM'; // Set AM/PM
    time[0] = +time[0] % 12 || 12; // Adjust hours
  }
  return time.join (''); // return adjusted time or original string
}	

function transferBed(pn,rm,bd,mode){

	var datefrom = $('datefrom').value;
	var timefrom = $('timefrom').value;
	var selAMPM = $('selAMPM').value;
	var admission_dt = $('admission_dt').value;
	var admission_tm = $('admission_tm').value;
	var curr_date_from = $('curr_date_from').value;

	if(timefrom != ''){
		var hours = Number(timefrom.match(/^(\d+)/)[1]);
		var minutes = Number(timefrom.match(/:(\d+)/)[1]);
	}
	var date_now = "<?=date("m/j/Y")?>";
	var time_now = "<?=date("g:i A")?>";
	var now = new Date(date_now + " "+time_now);
	now = Date.parse(now);

	if(selAMPM == 'PM' && hours < 12) hours = hours + 12;
	else if(selAMPM == 'AM' && hours == 12) hours = hours - 12;

	if(hours < 10) hours = "0"+hours;
	if(minutes < 10) minutes = "0"+minutes;

	timefrom = hours + ":" + minutes;

	ddate_from = Date.parse(datefrom + " "+timefrom);
	aadmission_dt = Date.parse(admission_dt + " " + admission_tm);

	dcurr_date_from = Date.parse(curr_date_from);
	display_adm_tm = tConvert(admission_tm);

	if($('datefrom').value == "" || $('timefrom').value == ""){
		$J("#error-message").dialog({
	    	closeOnEscape: false,
	      	position: ['center',20],
	      	modal: true,
	      	open: function(){
		        var errmsg = "Please set the actual date and time transferred";
		       	$J("#contentholder").text(errmsg)
		    },
	      	buttons: {
	        	Ok: function() {
	        		if(datefrom == ""){
						$J('#datefrom').focus();
					}else{
						$J('#timefrom').focus();
					}
	         		$J(this).dialog( "close" );
	        	}
	      	}
	    });
	}else if (ddate_from < aadmission_dt) {
		alert("Invalid Date. Assignment date should not be earlier than the admission date("+admission_dt + " " + display_adm_tm+").");
	}else if(ddate_from > now){
		alert("Invalid Date. Assignment date should not be later than the current date.");
	}else if(ddate_from < dcurr_date_from){
		alert("Invalid Date. Assignment date should not be earlier than the current accommodation's start date and time.");
	}else{
<?php
global $db;
$sql = "SELECT is_final,is_deleted FROM seg_billing_encounter WHERE encounter_nr = ".$db->qstr($encounter['encounter_nr'])." ORDER BY bill_nr DESC";

if ($result=$db->Execute($sql)) {
	if ($result->RecordCount()) {
		if ($row = $result->FetchRow()) {
			if ($row['is_final']==1 AND $row['is_deleted'] != 1 ) {			
?>
alert("This Patient has been already billed");
						<?php echo 'urlholder="nursing-station-transfer-save.php"'; ?>
<?php echo '
	urlholder="nursing-wardList.php"
'; ?>
window.opener.location.replace(urlholder);
window.close();

<?php
			}
			else
			{
				echo '
urlholder="nursing-station-transfer-save.php?mode="+mode+"&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&datefrom="+datefrom+"&timefrom="+timefrom+"&selAMPM="+selAMPM+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"

';
?>
window.opener.location.replace(urlholder);
window.close();
<?php
			}
		}

	}
				else
			{
				echo '
urlholder="nursing-station-transfer-save.php?mode="+mode+"&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&datefrom="+datefrom+"&timefrom="+timefrom+"&selAMPM="+selAMPM+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"

';
?>
window.opener.location.replace(urlholder);
window.close();
<?php
			}
}
?>
	}
	
//alert("pn,rm,bd,mode,ward,station = "+pn+" , "+rm+" , "+bd+" , "+mode+"'"+<?=$station?>+"'");
//<?php
echo '
urlholder="nursing-station-transfer-save.php?mode="+mode+"&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"

'; 

//echo '
//urlholder="nursing-station-transfer-save.php?mode="+mode+"&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&datefrom="+datefrom+"&timefrom="+timefrom+"&selAMPM="+selAMPM+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"

//';
//?>
//alert(urlholder);
//alert(window.parent.location);
//alert(window.opener.location);
//edited by Cherry 10-25-10
//window.opener.location.replace(urlholder);
//window.close();
//window.location.href = urlholder;
//alert('Assignment of bed is successfully saved');
//pSearchClose();
//window.parent.cClick();

//var ward_nr = <? echo $ward_nr ?>;


/*return overlib(
					OLiframeContent('<?php echo $root_path ?>modules/nursing/nursing-station-transfer-save.php?mode='+mode+'&sid=<?echo $sid?>&lang=<?echo $lang?>&rm='+rm+"&bd="+bd+"&datefrom="+datefrom+"&timefrom="+timefrom+"&selAMPM="+selAMPM+'&pyear=<? echo $pyear?>&pmonth=<? echo $pmonth?>&pday=<? echo $pday?>&pn='+pn+'&station=<? echo $station?>&ward_nr=<? echo $ward_nr ?>',
																	820, 420, 'fGroupTray', 0, 'auto'),
																	WIDTH,820, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=<?=$root_path?>/images/close.gif border=0 onClick="pSearchClose();">',
																 CAPTIONPADDING,2, CAPTION,'Transfer Patient',
																 MIDX,0, MIDY,0,
																 STATUS,'Transfer Patient');   */
/*window.location.href = urlholder;
alert('successfully saved');
pSearchClose();*/

}

function pSearchClose() {
	cClick();  //function in 'overlibmws.js'
	//added by VAN 04-12-08
	//refreshWindow();
}

function refreshWindow(){
	//alert('refresh = '+window.location.href);
	window.location.href=window.location.href;
}

// updated by carriane 07/03/2020 
//added by VAN 05-26-2010
function assignBed(pn,rm,bd,mode){
	var popUp = $('popUp').value;

	var datefrom = $('datefrom').value;
	var timefrom = $('timefrom').value;
	var selAMPM = $('selAMPM').value;
	var admission_dt = $('admission_dt').value;
	var admission_tm = $('admission_tm').value;
	var curr_date_from = $('curr_date_from').value;

	if(timefrom != ''){
		var hours = Number(timefrom.match(/^(\d+)/)[1]);
		var minutes = Number(timefrom.match(/:(\d+)/)[1]);
	}
	var date_now = "<?=date("m/j/Y")?>";
	var time_now = "<?=date("g:i A")?>";
	var now = new Date(date_now + " "+time_now);
	now = Date.parse(now);

	if(selAMPM == 'PM' && hours < 12) hours = hours + 12;
	else if(selAMPM == 'AM' && hours == 12) hours = hours - 12;

	if(hours < 10) hours = "0"+hours;
	if(minutes < 10) minutes = "0"+minutes;

	timefrom = hours + ":" + minutes;

	ddate_from = Date.parse(datefrom + " "+timefrom);
	aadmission_dt = Date.parse(admission_dt + " " + admission_tm);

	dcurr_date_from = Date.parse(curr_date_from);
	display_adm_tm = tConvert(admission_tm);

	console.log("datefrom",datefrom);
	console.log("timefrom",timefrom);
	console.log("ddate_from",ddate_from);
	console.log("aadmission_dt",aadmission_dt);
	console.log("now",now);
	console.log("dcurr_date_from",dcurr_date_from);
	//alert(timefrom2+" > "+admission_tm);
	//if ((datefrom < admission_dt)||((datefrom == admission_dt)&&(timefrom2 > admission_tm))){

	if($('datefrom').value == "" || $('timefrom').value == ""){
		$J("#error-message").dialog({
	    	closeOnEscape: false,
	      	position: ['center',20],
	      	modal: true,
	      	open: function(){
		        var errmsg = "Please set the actual date and time transferred";
		       	$J("#contentholder").text(errmsg)
		    },
	      	buttons: {
	        	Ok: function() {
	        		if(datefrom == ""){
						$J('#datefrom').focus();
					}else{
						$J('#timefrom').focus();
					}
	         		$J(this).dialog( "close" );
	        	}
	      	}
	    });
	}else if (ddate_from < aadmission_dt) {
		alert("Assignment date should not be earlier than the admission date ("+admission_dt + " " + display_adm_tm+").");
	}else if(ddate_from > now){
		alert("Assignment date should not be later than the current date.");
	}else if(ddate_from < dcurr_date_from){
		alert("Assignment date should not be earlier than the current accommodation's start date and time ("+curr_date_from+")");
	}else{

		<?php
		/*echo '
		urlholder="nursing-station-transfer-save.php?mode="+mode+"&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"

		'; */

		echo '
		urlholder="nursing-station-transfer-save.php?mode="+mode+"&sid='.$sid.'&lang='.$lang.'&rm="+rm+"&bd="+bd+"&datefrom="+datefrom+"&timefrom="+timefrom+"&selAMPM="+selAMPM+"&pyear='.$pyear.'&pmonth='.$pmonth.'&pday='.$pday.'&pn="+pn+"&station='.$station.'&ward_nr='.$ward_nr.'"

		';

		?>
        
		if (popUp==1){
			window.parent.parent.location.href = urlholder;
			pSearchClose();
			window.parent.cClick();
		}else{
			window.opener.location.replace(urlholder);
			window.close();
		}
		//window.location.href = urlholder;
		//alert('Successfully Saved ');
		//pSearchClose();
		//window.parent.cClick();
	}

}
//---------------

// added by VAN 02-06-08
function Transfer(pn,pw){

	var modetransfer = document.getElementById('modetransfer').value;
	//var waiting = '<?=$waiting?>';
	var waiting = document.getElementById('waiting').value;
    var popUp = $('popUp').value;

	//var assigndate = document.getElementById('datefrom').value;
	//var assigntime = document.getElementById('timefrom').value;
	//var assignmeridian = document.getElementById('selAMPM').value;

	//assignfintime = assigntime+":00 "+assignmeridian;

	if(confirm("<?php echo $LDSureTransferPatient ?>")){
		//urlholder="nursing-station-transfer-select.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr=<?php echo $ward_nr ?>&station=<?php echo $station ?>&modetransfer="+modetransfer+"&waiting="+waiting+"&assigndate="+assigndate+"&assignfintime="+assignfintime;
		urlholder="nursing-station-transfer-select.php<?php echo URL_REDIRECT_APPEND ?>&pn="+pn+"&pat_station="+pw+"&ward_nr=<?php echo $ward_nr ?>&station=<?php echo $station ?>&modetransfer="+modetransfer+"&waiting="+waiting+"&popUp="+popUp;
		//transwin<?php echo $sid ?>=window.open(urlholder,"transwin<?php echo $sid ?>","width=650,height=600,menubar=no,resizable=yes,scrollbars=yes");
		//window.location.href=urlholder;
		//window.close();
		window.location.href = urlholder;
		//pSearchClose();
	}
}

// -->
</script>
<style type="text/css" name="s2">
td.vn { font-family:verdana,arial; color:#000088; font-size:10}
</style>

<?php

$sTemp = ob_get_contents();

ob_end_clean();

#added by VAN 06-08-2010
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
#---------

$smarty->append('JavaScript',$sTemp);

$smarty->assign('sClassItem','class="reg_item"');
$smarty->assign('sClassInput','class="reg_input"');

$smarty->assign('LDCaseNr',$LDAdmitNr);

$smarty->assign('sEncNrPID',$pn);

$encInfo = $enc_obj->getEncounterData($pn);

$smarty->assign('img_source',"<img $img_source>");

$smarty->assign('LDTitle',$LDTitle);
$smarty->assign('title',$encounter['title']);
$smarty->assign('LDLastName',$LDLastName);
$smarty->assign('name_last',$encounter['name_last']);
$smarty->assign('LDFirstName',$LDFirstName);
$smarty->assign('name_first',$encounter['name_first']);

# If person is dead show a black cross and assign death date

if($encounter['death_date'] && $encounter['death_date'] != DBF_NODATE){
	$smarty->assign('sCrossImg','<img '.createComIcon($root_path,'blackcross_sm.gif','0','',TRUE).'>');
	$smarty->assign('sDeathDate',@formatDate2Local($encounter['death_date'],$date_format));
}

# Set a row span counter, initialize with 5
$iRowSpan = 5;

if(trim($encounter['blood_group'])){
	$smarty->assign('LDBloodGroup',$LDBloodGroup);
	$buf=trim('LD'.$encounter['blood_group']);
	$smarty->assign('blood_group',$$buf);
	$iRowSpan++;
}

$smarty->assign('sRowSpan',"rowspan=\"$iRowSpan\"");

$smarty->assign('LDBday',$LDBday);
$smarty->assign('sBdayDate',@formatDate2Local($encounter['date_birth'],$date_format));

$smarty->assign('LDSex',$LDSex);
if($encounter['sex']=='m') $smarty->assign('sSexType',$LDMale);
	elseif($encounter['sex']=='f') $smarty->assign('sSexType',$LDFemale);

$smarty->assign('LDBillType',$LDBillType);
if (isset($$billing_type['LD_var'])&&!empty($$billing_type['LD_var'])) $smarty->assign('billing_type',$$billing_type['LD_var']);
		else $smarty->assign('billing_type',$billing_type['name']);

$smarty->assign('LDAdmissionDate',$LDAdmissionDateNursing);
$smarty->assign('admission_dt',date("F d, Y h:i A", strtotime($encounter['admission_dt'])));
$smarty->assign('LDDiagnosis',$LDDiagnosis);
$smarty->assign('referrer_diagnosis',$encounter['referrer_diagnosis']);
$smarty->assign('LDTherapy',$LDTherapy);
$smarty->assign('referrer_recom_therapy',$encounter['referrer_recom_therapy']);
$smarty->assign('LDSpecials',$LDSpecials);
$smarty->assign('referrer_notes',$encounter['referrer_notes']);
# Buffer page output

ob_start();

$smarty->display('nursing/basic_data_admit.tpl');

	#added by VAN 06-08-10
	$date_accom = date("Y-m-d");
	#$date_accom = "";

	#$smarty->assign('sLDDateFrom','<input type="text" name="datefrom" value="'.$date_accom.'" size=10 maxlength=10 id="datefrom" onChange="if (IsValidDate(this,\''.$date_format.'\')){ seg_setValidDate(true); }else{ seg_setValidDate(false); }" onKeyUp="setDate(this,\''.$date_format.'\',\''.$lang.'\')" />');

	// if($waiting || $modetransfer == 'trasferward')
	// 	$hidedatetime = 0;
	// else
	// 	$hidedatetime = 1;

	# Updated by carriane 06/29/2020 - remove default value for datefrom = $date_accom; change to input date
	// $smarty->assign('sLDDateFrom','<input type="text" name="datefrom" size=10 maxlength=10 id="datefrom" style="font-size:20px;font-family: Verdana"/>');
	$smarty->assign('sLDDateFrom','<input type="date" name="datefrom" size=10 maxlength=10 id="datefrom" style="font-size:20px;font-family: Verdana;" max="'.$date_accom.'" onkeydown="return false"/>');

	#$TP_href_date="javascript:show_calendar('document.getElementById(\'datefrom\')','".$date_format."')";
	#echo "sss = ".$TP_href_date;
	$dfbuffer="LD_".strtr($date_format,".-/","phs");
	$TP_date_format=$$dfbuffer;

	$jsCalScript = "<script type=\"text/javascript\">
							Calendar.setup ({
							inputField : \"datefrom\", ifFormat : \"$phpfd\", showsTime : false, button : \"datefrom_trigger\", singleClick : true, dateStatusFunc : function (date) { 
								                var now= new Date();
										        if(date.getFullYear()>now.getFullYear())
										        {
										            return true;
										        }

										        if(date.getFullYear()==now.getFullYear())
										        {
										            if(date.getMonth()>now.getMonth())
										            {
										                return true;
										            }

										            if(date.getMonth()==now.getMonth())
											        {
											            if(date.getDate()>now.getDate())
											            {
											                return true;
											            }
											        }
										        }

										        }, step : 1
							});
						</script>
						";
	// $smarty->assign('jsCalendarSetup', $jsCalScript);
	// $smarty->assign('sDateMiniCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="datefrom_trigger" align="absmiddle" style="cursor:pointer"> <font size=3>['.$TP_date_format.']</font>');
	$smarty->assign('sDateMiniCalendar','<font size=3>[dd/mm/yyyy]</font>');

	$time_accom = date("g:i");
	#$time_accom = "";
	$meridian = date("A");

	if ($meridian=='PM'){
		$selected1 = "";
		$selected2 = "selected";
	}elseif ($meridian=='AM'){
		$selected1 = "selected";
		$selected2 = "";
	}

	# Updated by carriane 06/29/2020 - remove default value for timefrom = $time_accom
	$admtime = '<input type="text" id="timefrom" name="timefrom" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM\')" style="font-size:20px"/>
						<select id="selAMPM" name="selAMPM" style="font-size:20px">
							<option value="AM" '.$selected1.'>A.M.</option>
							<option value="PM" '.$selected2.'>P.M.</option>
						</select>&nbsp;<font size=3>[hh:mm]</font>';

	// $smarty->assign('hidedatetime', $hidedatetime);
	$smarty->assign('sLDTimeFrom',$admtime);
#-----------

	$recentWard = $enc_obj->getRecentWard($pn);

	$curr_accom_dttime = $recentWard['date_from']." ".$recentWard['time_from'];
?>
	<input type="hidden" name="admission_tm" id="admission_tm" value="<?=date("H:i:s",strtotime($encInfo['admission_dt']))?>">
	<input type="hidden" name="admission_dt" id="admission_dt" value="<?=date("m/d/Y",strtotime($encInfo['admission_dt']))?>">
	<input type="hidden" name="curr_date_from" id="curr_date_from" value="<?=date("m/d/Y H:i:s",strtotime($curr_accom_dttime))?>">
<?php
	//if($hidedatetime){
?>
		<!-- <input type="hidden" name="timefrom" id="timefrom" value="<?=$time_accom?>">
		<input type="hidden" name="datefrom" id="datefrom" value="<?=$date_accom?>">
		<input type="hidden" name="selAMPM" id="selAMPM" value="<?=$meridian?>"> -->
<?php
	//} //Close if statement -- if($hidedatetime)
?>
<table width=100% border=0 cellpadding="0" cellspacing=0>
	<tr valign=top >
		<td bgcolor=<?php echo $cfg['body_bgcolor']; ?> valign=top colspan=2>


<!--  Show stop sign and warn if the initial ward assignment is different from this ward -->
<?php
if($encounter['current_ward_nr']!=$ward_nr){
?>
			<table border=0>
				<tr>
					<td><img <?php 	echo createLDImgSrc($root_path,'stop.png','0'); ?>></td>
					<td><?php  echo str_replace('~ward_id~',$pat_station,$LDChkWardConflict); ?></td>
				</tr>
			</table>

<?php
}else{
?>
			<table border=0>
				<tr>
					<td><img <?php 	echo createComIcon($root_path,'angle_down_l.gif','0','',TRUE); ?>></td>
					<td><FONT SIZE=3><?php 	echo $LDSelectRoomBed; ?></font></td>
					<td><img <?php 	echo createMascot($root_path,'mascot1_l.gif','0'); ?>></td>
				</tr>
			</table>

<?php
}

#print_r($ward_info);

if($ward_ok){

	if($pyear.$pmonth.$pday<date('Ymd')){
		$smarty->assign('sWarningPrompt','
		<img '.createComIcon($root_path,'warn.gif','0','absmiddle',TRUE).'> <font color="#ff0000"><b>'.$LDAttention.'</font> '.$LDOldList.'</b>');

		# Prevent adding new patients to the list  if list is old
		$edit=FALSE;
	}

	# Start here, create the occupancy list
	# Assign the column  names
	#added by Mats 07262016
	$smarty->assign('LDDescription',$LDDescription);
	#ended
	$smarty->assign('LDRoom',$LDRoom);
	$smarty->assign('LDBed',$LDPatListElements[1]);
	$smarty->assign('LDFamilyName',$LDLastName);
	$smarty->assign('LDName',$LDName);
	$smarty->assign('LDBirthDate',$LDBirthDate);
	$smarty->assign('LDPatNr',$LDPatListElements[4]);
	#$smarty->assign('$LDBillType',$LDBillType);
	$smarty->assign('LDBillType',$LDBillType);
	$smarty->assign('LDOptions',$LDPatListElements[6]);

	# Initialize help flags
	$toggle=1;
	$room_info=array();
	# Set occupied bed counter
	$occ_beds=0;
	$lock_beds=0;
	$males=0;
	$females=0;
	$cflag=$ward_info['room_nr_start'];

	# Initialize list rows container string
	$sListRows='';

	# Loop trough the ward rooms
#echo "<br>start = ".$ward_info['room_nr_start'];
#echo "<br>end = ".$ward_info['room_nr_end'];
	#edited by VAN 05-18-2010
	/*for ($i=$ward_info['room_nr_start'];$i<=$ward_info['room_nr_end'];$i++){
		if($room_ok){
			$room_info=$room_obj->FetchRow();
			#echo "<br>sql = ".$i." - ".$room_obj->sql;
		}else{
			$room_info['nr_of_beds']=1;
			$edit=false;
		}
	 */
 $wardRoom = $ward_obj->getRoomsData($ward_nr);

 if (is_object($wardRoom)){
	while ($perRoom = $wardRoom->FetchRow()){
		$i = $perRoom['room_nr'];
		#added by Mats 07262016
		$roomDesc = $perRoom['info'];
		#ended
		if($room_ok){
			$room_info=$room_obj->FetchRow();
		}else{
			$room_info['nr_of_beds']=1;
			$edit=false;
		}
		# added by: syboy 10/14/2015
		$split = explode('/', $room_info['closed_beds']);
		# ended
		// Scan the patients object if the patient is assigned to the bed & room
		# Loop through room beds
#print_r($room_info);
		for($j=1;$j<=$room_info['nr_of_beds'];$j++){
#echo "name = ".$patient[$i][$j]['name_last']." , ".$patient[$i][$j]['name_first'];

			# Reset elements

			$smarty->assign('sMiniColorBars','');
			$smarty->assign('sRoom','');
			$smarty->assign('sBed','');
			$smarty->assign('sBedIcon','');
			$smarty->assign('cComma','');
			$smarty->assign('sFamilyName','');
			$smarty->assign('sName','');
			$smarty->assign('sTitle','');
			$smarty->assign('sBirthDate','');
			$smarty->assign('sPatNr','');
			$smarty->assign('sAdmitDataIcon','');
			$smarty->assign('sChartFolderIcon','');
			$smarty->assign('sNotesIcon','');
			$smarty->assign('sTransferIcon','');
			$smarty->assign('sDischargeIcon','');

			$sAstart='';
			$sAend='';
			$sFamNameBuffer='';
			$sNameBuffer='';

			// var_dump($patient[$i][$j]);
			if($patients_ok){
				if(isset($patient[$i][$j])){

						// start foreach ..
						//  added by: syboy; 05/15/2015
						$bed = array();
						$notes = '';
						$gender = '';
						$countPatients = array();
						$patientCount = 0;
						$insurance = '';

						foreach ($patient[$i][$j] as $patients) {
							
							$bed['name_last'] .= '<tr style="height: 20px;"><td>'.$patients['name_last'].', '.$patients['name_first'].'</td></tr>';
							$bed['encounter_nr'] .= $patients['encounter_nr'];
							$bed['date_birth'] .= '<tr style="height: 20px;"><td>'.date('m/d/Y', strtotime($patients['date_birth'])).'</td></tr>';

							if ($patients['ward_notes'] == null) {
								$notes .= '<tr style="height: 20px;"><td><a href="javascript:void(0);" onClick="getrem(\''.$patients['encounter_nr'].'\')" title="'.$LDNoticeRW.'"><img '.createComIcon($root_path,'bubble2.gif','0','',TRUE).' alt="'.$LDNoticeRW.'"></a></td></tr>';
							} else {
								$notes .= '<tr style="height: 20px;"><td><a href="javascript:void(0);" onClick="getrem(\''.$patients['encounter_nr'].'\')" title="'.$LDNoticeRW.'"><img '.createComIcon($root_path,'bubble3.gif','0','',TRUE).' alt="'.$LDNoticeRW.'"></a></td></tr>';
							}

							if (strtolower($patients['sex'] == 'f')) {
								$gender .= '<tr style="height: 20px;"><td><a href="javascript:popPic(\''.$patients['pid'].'\')"><img '.createComIcon($root_path,'spf.gif','0','',TRUE).'></a></td></tr>';
							} elseif (strtolower($patients['sex'] == 'm')) {
								$gender .= '<tr style="height: 20px;"><td><a href="javascript:popPic(\''.$patients['pid'].'\')"><img '.createComIcon($root_path,'spm.gif','0','',TRUE).'></a></td></tr>';
							} else {
								$gender .= '<tr style="height: 20px;"><td><a href="javascript:popPic(\''.$patients['pid'].'\')"><img '.createComIcon($root_path,'bn.gif','0','',TRUE).'></a></td></tr>';
							}

							if(isset($$patients['insurance_LDvar']) && !empty($$patients['insurance_LDvar'])) {
								$bed['insurance_name'] .= '<tr style="height: 20px;"><td>';

								if($patients['insurance_class_nr'] != 2)
									$bed['insurance_name'] .= '<font color="#ff0000">';

								$bed['insurance_name'] .= $$patients['insurance_LDvar'].'</td></tr>';
							}
							else
								$bed['insurance_name'] .= '<tr style="height: 20px;"><td>'.$patients['insurance_name'].'</td></tr>';

							$countPatients[$i][$j] = $patientCount++;
						}

						//end 
						// var_dump($bed);
						// $bed=&$patient[$i][$j];
						$is_patient=true;
					# Increase occupied bed nr
					$occ_beds++;
				}else{
					$is_patient=false;
					$bed=NULL;
				}
			}
			# If same patient, highlight bacground
			if($transfer&&$bed['encounter_nr']==$pn){
				$smarty->assign('bHighlightRow',TRUE);
			}else{
				$smarty->assign('bHighlightRow',FALSE);
				# set room nr change flag , toggle row color
				if($cflag!=$i){
					$toggle=!$toggle;
					$cflag=$i;
				}
				# set row color/class
				if ($toggle){
					$smarty->assign('bToggleRowClass',TRUE);
				}else{
					$smarty->assign('bToggleRowClass',FALSE);
				}
			}


			# Check if bed is locked
			# added by: syboy 10/14/2015
			foreach ($split as $key) {
				if (trim($key) == trim($j)) {
					$bed_locked=true;
					$lock_beds++;
					$occ_bed++;
					break;
				}else{
					$bed_locked=false;
				}
			}
			# commented out by : syboy 10/14/2015
			// if(stristr($room_info['closed_beds'],$j.'/')){
			// 	$bed_locked=true;
			// 	$lock_beds++;
			// 	# Consider locked bed as occupied so increase occupied bed counter
			// 	$occ_bed++;
			// }else{
			// 	$bed_locked=false;
			// }
			# ended

			# If bed nr  is 1, show the room number
			if($j==1){
				#commented by VAN 01-24-08
				#$smarty->assign('sRoom',strtoupper($ward_info['roomprefix']).$i);
				# edited by VAN 01-24-08
				$smarty->assign('sRoom',strtoupper($ward_info['roomprefix']).$room_info['room_nr']);
				#added by Mats 07262016
				$smarty->assign('sDescription',$roomDesc);

			} else{
				$smarty->assign('sRoom','');
				#added by Mats 07262016
				$smarty->assign('sDescription','');
			}

			#$smarty->assign('sBed',strtoupper(chr($j+96)));
			$smarty->assign('sBed',$j);

			# If patient, show images by sex
			if($is_patient){
				if($countPatients[$i][$j] < 3)
					$gender .= '<tr style="height: 20px;"></tr>';

				$smarty->assign('sBedIcon', $gender);

				// $sBuffer = '<a href="javascript:popPic(\''.$bed['pid'].'\')">';
				// switch(strtolower($bed['sex'])){
				// 	case 'f':
				// 		$smarty->assign('sBedIcon',$sBuffer.'<img '.createComIcon($root_path,'spf.gif','0','',TRUE).'></a>');
				// 		$females++;
				// 		break;
				// 	case 'm':
				// 		$smarty->assign('sBedIcon',$sBuffer.'<img '.createComIcon($root_path,'spm.gif','0','',TRUE).'></a>');
				// 		$males++;
				// 		break;
				// 	default:
				// 		$smarty->assign('sBedIcon',$sBuffer.'<img '.createComIcon($root_path,'bn.gif','0','',TRUE).'></a>');
				// }

			}elseif($bed_locked){
				$smarty->assign('sBedIcon','<img '.createComIcon($root_path,'delete2.gif','0','',TRUE).'>');
			}

			if($is_patient&&($bed['encounter_nr']!="")){


				// added by: syboy; 05/152015 
				if ($waiting){
							$as_img='assign_here.gif';
							$js_fx='assignBed';
							$modetransfer = isset($_GET['modetransfer']) && isset($_GET['modetransfer']) ? $_GET['modetransfer'] : 'assignBedwaiting';
					}else{
						if($transfer){
							$as_img='transfer_sm.gif';
							$js_fx='transferBed';
						}else{
							$as_img='assign_here.gif';
							$js_fx='assignBed';
							$modetransfer = 'assignBedwaiting';
						}
						if (empty($modetransfer))
							$modetransfer = 'transferbed';
					}

					// var_dump($countPatients[$i][$j]);
					if($countPatients[$i][$j] < 3) {
						$sTransBuffer ='<tr style="height: 20px;"><td><a href="javascript:'.$js_fx.'(\''.$pn.'\',\''.$i.'\',\''.$j.'\',\''.$modetransfer.'\')"><img '.createLDImgSrc($root_path,$as_img,'0','middle').' alt="'.$LDClk2Occupy.'"></a></td></tr>';
					}
					else {
						$sTransBuffer = '';
					}
				// end

				$smarty->assign('sTitle',ucfirst($bed['title']));

				if(isset($sln)&&$sln) $smarty->assign('sFamilyName',eregi_replace($sln,'<span style="background:yellow">'.ucfirst($sln).'</span>',ucfirst($bed['name_last'])));
					else $smarty->assign('sFamilyName',ucfirst($bed['name_last']));

				if($bed['name_last']) $smarty->assign('cComma','');
					else $smarty->assign('cComma','');

				if(isset($sfn)&&$sfn) $smarty->assign('sName',eregi_replace($sfn,'<span style="background:yellow">'.ucfirst($sln).'</span>',ucfirst($bed['name_first'])));
					else $smarty->assign('sName',ucfirst($bed['name_first']).$sTransBuffer); // edited by: syboy; 05/15/2015

			}else{
				if(!$bed_locked){
					#added by VAN 06-07-2010
					if ($waiting){
						/*if (($modetransfer=='transferward')|($modetransfer=='transferbed')){
							$as_img='assign_here.gif';
							$js_fx='assignBed';
							$modetransfer = 'assignWardwaiting';
						}else{*/
							$as_img='assign_here.gif';
							$js_fx='assignBed';
							$modetransfer = 'assignBedwaiting';
						#}
					}else{
						if($transfer){
							$as_img='transfer_sm.gif';
							$js_fx='transferBed';
						}else{
							$as_img='assign_here.gif';
							#$js_fx='belegen';
							#edited by VAN 05-26-2010
							$js_fx='assignBed';
							$modetransfer = 'assignBedwaiting';
						}
						#edited by VAN 01-25-08
						if (empty($modetransfer))
							$modetransfer = 'transferbed';
					}

					#$sTransBuffer ='<a href="javascript:'.$js_fx.'(\''.$pn.'\',\''.$i.'\',\''.$j.'\')"><img '.createLDImgSrc($root_path,$as_img,'0','middle').' alt="'.$LDClk2Occupy.'"></a>';
					$sTransBuffer ='<a href="javascript:'.$js_fx.'(\''.$pn.'\',\''.$i.'\',\''.$j.'\',\''.$modetransfer.'\')"><img '.createLDImgSrc($root_path,$as_img,'0','middle').' alt="'.$LDClk2Occupy.'"></a>';

					$smarty->assign('sFamilyName',$sTransBuffer);
				}else{
					$smarty->assign('sFamilyName',$LDLocked);
				}
				$smarty->assign('sName','');
				$smarty->assign('cComma','');
			}


			if($bed['date_birth']){

				// if(isset($sg)&&$sg) $smarty->assign('sBirthDate',eregi_replace($sg,"<font color=#ff0000><b>".ucfirst($sg)."</b></font>",formatDate2Local($bed['date_birth'],$date_format)));
				// 	else $smarty->assign('sBirthDate',formatDate2Local($bed['date_birth'],$date_format));
				if($countPatients[$i][$j] < 3)
					$bed['date_birth'] .= '<tr style="height: 20px;"></tr>';

				$smarty->assign('sBirthDate', $bed['date_birth']);
			}

			//if ($bed['encounter_nr']) $smarty->assign('sPatNr',$bed['encounter_nr']);

			// $sBuffer = '';
			// if($bed['insurance_class_nr']!=2) $sBuffer = $sBuffer.'<font color="#ff0000">';

			// if(isset($$bed['insurance_LDvar'])&&!empty($$bed['insurance_LDvar']))  $sBuffer = $sBuffer.$$bed['insurance_LDvar'];
			// 	else  $sBuffer = $sBuffer.$bed['insurance_name'];

			if($countPatients[$i][$j] < 3)
				$bed['insurance_name'] .= '<tr style="height: 20px;"></tr>';

			$smarty->assign('sInsuranceType',$bed['insurance_name']);

			if(($is_patient)&&!empty($bed['encounter_nr'])){

				// $sBuffer = '<a href="javascript:getrem(\''.$bed['encounter_nr'].'\')"><img ';
				// #$sBuffer = '<a href="javascript:void(0);" onClick="getrem(\''.$bed['encounter_nr'].'\')"><img ';
				// if($bed['ward_notes']) $sBuffer = $sBuffer.createComIcon($root_path,'bubble3.gif','0','',TRUE);
				// 	else $sBuffer = $sBuffer.createComIcon($root_path,'bubble2.gif','0','',TRUE);
				// $sBuffer = $sBuffer.' alt="'.$LDNoticeRW.'"></a>';
				if($countPatients[$i][$j] < 3)
					$notes .= '<tr style="height: 20px;"></tr>';

				$smarty->assign('sNotesIcon',$notes);
			}

			# Create the rows using ward_transferbed_list_row.tpl template
			ob_start();
				$smarty->display('nursing/ward_transferbed_list_row.tpl');
				$sListRows = $sListRows.ob_get_contents();
			ob_end_clean();

		} // end of bed loop

		# Append the new row to the previous row in string

		$smarty->assign('sOccListRows',$sListRows);
	} // end of ward loop
 }

	# Display the empty bed transfer list
	$smarty->display('nursing/ward_transferbed_list.tpl');

}else{
	echo '
			<ul><img '.createMascot($root_path,'mascot1_r.gif','0','absmiddle').'><font face="Verdana, Arial" size=3>
			<font class="prompt"><b>'.str_replace("~station~",strtoupper($station),$LDNoInit).'</b></font><br>
			<a href="nursing-station-new.php'.URL_APPEND.'&station='.$station.'&edit='.$edit.'">'.$LDIfInit.' <img '.createComIcon($root_path,'bul_arrowgrnlrg.gif','0','',TRUE).'></a><p></font>
			</ul>';
}

?>

<p>
<!--
<a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>></a>
-->
<table width="40%">
	<tr>
		<!--<td><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>></a></td>-->
		<td><a href="<?php echo $breakfile ?>"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>></a></td>
		<td><a href="javascript:Transfer('<?php echo $pn;?>')" title="<?php echo $LDTransferPatient; ?>"><img <?php echo createLDImgSrc($root_path,'transfer.gif','0') ?>></a></td>
	</tr>
</table>
<p>

		</td>
	</tr>
	<div id="error-message" title="" style="display: none">
	  <p style="color:red;font-weight: bold;font-size: 15px; font-style: Tahoma" id="contentholder">
	  </p>
	</div>
</table>

<input type="hidden" name="modetransfer" id="modetransfer" value="<?=$modetransfer?>" />
<input type="hidden" name="waiting" id="waiting" value="<?=$waiting?>">
<input type="hidden" name="popUp" id="popUp" value="<?=$_GET['popUp']?>">

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the page output to the mainframe center block

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

 ?>
