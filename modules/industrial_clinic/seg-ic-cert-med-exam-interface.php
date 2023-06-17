<?php
require_once("roots.php");

require_once($root_path.'include/inc_environment_global.php');
require_once($root_path.'include/inc_date_format_functions.php');

#added by VAN 02-18-08
define('NO_2LEVEL_CHK',1);

define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';

include_once($root_path.'include/care_api_classes/class_person.php');
$person_obj=new Person;
	# Create the helper class for the country table
include_once($root_path.'include/care_api_classes/class_address.php');
$address_country = new Address('country');

#added by VAN 05-23-08
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

#Added by Cherry 08-24-10
include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

#Added by Cherry 08-25-10
include_once($root_path.'include/care_api_classes/class_department.php');
$dep_obj = new Department;

#Added by Cherry 08-25-10
include_once($root_path.'include/care_api_classes/industrial_clinic/class_ic_med_cert.php');
$ic_obj = new SegICCertMed;

require_once($root_path.'include/care_api_classes/class_hospital_admin.php');
$objInfo = new Hospital_Admin();

		if ($row = $objInfo->getAllHospitalInfo()) {
			$row['hosp_agency'] = strtoupper($row['hosp_agency']);
			$row['hosp_name']   = strtoupper($row['hosp_name']);
		}
		else {
			$row['hosp_country'] = "Republic of the Philippines";
			$row['hosp_agency']  = "DEPARTMENT OF HEALTH";
			$row['hosp_name']    = "SOUTHERN PHILIPPINES MEDICAL CENTER";
			$row['hosp_addr1']   = "JICA Bldg., JP Laurel Avenue, Davao City";
		}

#added by VAN 05-19-08
require_once($root_path.'include/care_api_classes/class_dateGenerator.php');
$dategen = new DateGenerator;
#------------------

require($root_path.'include/inc_front_chain_lang.php');

if (isset($_GET['pid']) && $_GET['pid']){
	$pid = $_GET['pid'];
}
if (isset($_POST['pid']) && $_POST['pid']){
	$pid = $_POST['pid'];
}

#Added by Cherry 08-09-10
if (isset($_GET['refno']) && $_GET['refno']){
	$refno = $_GET['refno'];
}
if (isset($_POST['refno']) && $_POST['refno']){
	$refno = $_POST['refno'];
}

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}
if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
	$encounter_nr = $_POST['encounter_nr'];
}

global $db;

#Edited by Cherry Nov. 30, 2010
if($encounter_nr){
#    if(!($encInfo = $enc_obj->getEncounterInfo($_GET['encounter_nr']))){
		if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
				echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
				exit();
		}
		#extract($encInfo);
}else{
		echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
		exit();
}

$errorMsg='';

#------------------------------

if (isset($_POST['mode'])){
	switch($_POST['mode']) {
		case 'save':
						 /*
			$pid = $encInfo['pid'];

			$check_mss = $socserv->getSocServPatient($pid);

			$is_exists = $socserv->count;


			if (!($is_exists)){
				$mss_no = $socserv->getLastMSSnr(date("Y-m-d"),"'".$GLOBAL_CONFIG['mss_nr_init']."'");

				$data_patient = array('pid'=>$pid,
														'mss_no'=>$mss_no,
														'status'=>'',
														'history'=>"Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
														'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
														'modify_time'=>date('Y-m-d H:i:s'),
														'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
														'create_time'=>date('Y-m-d H:i:s')
											);

				$ok = $socserv->saveSocialPatientArray($data_patient);
			}else{
				$mss_no = $check_mss['mss_no'];
				$ok = 1;
			}

			$data = array('mss_no'=>$mss_no,
										'encounter_nr'=>$encounter_nr,
										'informant_name'=>$_POST['informant_name'],
										'info_address'=>$_POST['info_address'],
										'educational_attain'=>$_POST['educational_attain'],
										'occupation'=>$_POST['occupation'],
										'employer'=>$_POST['employer'],
										'employer_address'=>$_POST['employer_address'],
										'source_income'=>$_POST['source_income'],
										'monthly_income'=>$_POST['monthly_income'],
										'per_capita_income'=>$_POST['per_capita_income'],
										'food_expense'=>$_POST['food_expense'],
										'ligth_expense'=>$_POST['ligth_expense'],
										'water_expense'=>$_POST['water_expense'],
										'transport_expense'=>$_POST['transport_expense'],
										'other_expense'=>$_POST['other_expense'],
										'status'=>'',
										'history'=> "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
										'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
										'modify_time'=>date('Y-m-d H:i:s'),
										'create_id'=>$HTTP_SESSION_VARS['sess_user_name'],
										'create_time'=>date('Y-m-d H:i:s'),
										'education_expense'=>$_POST['education_expense'],
										'househelp_expense'=>$_POST['househelp_expense'],
										'fuel_expense'=>$_POST['fuel_expense'],
										'clothing_expense'=>$_POST['clothing_expense'],
										'med_expenditure'=>$_POST['med_expenditure'],
										'insurance_mortgage'=>$_POST['insurance_mortgage'],
										'total_monthly_expense'=>$_POST['total_monthly_expense'],
										'address'=>$_POST['address'],
										'house_type'=>$_POST['house_type'],
										'final_diagnosis'=>$_POST['final_diagnosis'],
										'duration_problem'=>$_POST['duration_problem'],
										'duration_treatment'=>$_POST['duration_treatment'],
										'treatment_plan'=>$_POST['treatment_plan'],
										'accessibility_problem'=>$_POST['accessibility_problem'],
										'info_agency'=>$_POST['info_agency'],
										'info_contact_no'=>$_POST['info_contact_no'],
										'remarks'=>$_POST['remarks'],
										'social_worker'=>$_POST['social_worker']
									);

			if ($ok && $socserv->saveSocServPatientArray($data)){
				echo " \n <script type=\"text/javascript\">alert(\"Saved sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000">'.$obj_birthCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
				#echo '<em class="warn"><font style="color:#FF0000"><b>'.$socserv->sql.'<b></font></em>';
			}
		break;
		case 'update':

			$pid = $encInfo['pid'];
			$check_mss = $socserv->getSocServPatient($pid);
			$mss_no = $check_mss['mss_no'];

			$data = array('mss_no'=>$mss_no,
										'encounter_nr'=>$encounter_nr,
										'informant_name'=>$_POST['informant_name'],
										'info_address'=>$_POST['info_address'],
										'educational_attain'=>$_POST['educational_attain'],
										'occupation'=>$_POST['occupation'],
										'employer'=>$_POST['employer'],
										'employer_address'=>$_POST['employer_address'],
										'source_income'=>$_POST['source_income'],
										'monthly_income'=>$_POST['monthly_income'],
										'per_capita_income'=>$_POST['per_capita_income'],
										'food_expense'=>$_POST['food_expense'],
										'ligth_expense'=>$_POST['ligth_expense'],
										'water_expense'=>$_POST['water_expense'],
										'transport_expense'=>$_POST['transport_expense'],
										'other_expense'=>$_POST['other_expense'],
										'status'=>'',
										'history'=> "Update ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n",
										'modify_id'=>$HTTP_SESSION_VARS['sess_user_name'],
										'modify_time'=>date('Y-m-d H:i:s'),
										'education_expense'=>$_POST['education_expense'],
										'househelp_expense'=>$_POST['househelp_expense'],
										'fuel_expense'=>$_POST['fuel_expense'],
										'clothing_expense'=>$_POST['clothing_expense'],
										'med_expenditure'=>$_POST['med_expenditure'],
										'insurance_mortgage'=>$_POST['insurance_mortgage'],
										'total_monthly_expense'=>$_POST['total_monthly_expense'],
										'address'=>$_POST['address'],
										'house_type'=>$_POST['house_type'],
										'final_diagnosis'=>$_POST['final_diagnosis'],
										'duration_problem'=>$_POST['duration_problem'],
										'duration_treatment'=>$_POST['duration_treatment'],
										'treatment_plan'=>$_POST['treatment_plan'],
										'accessibility_problem'=>$_POST['accessibility_problem'],
										'info_agency'=>$_POST['info_agency'],
										'info_contact_no'=>$_POST['info_contact_no'],
										'remarks'=>$_POST['remarks'],
										'social_worker'=>$_POST['social_worker'],
										'other_source_income'=>$_POST['other_source_income']
									);


			if ($socserv->updateSocServPatientArray($mss_no, $encounter_nr, $data)){
				echo " \n <script type=\"text/javascript\">alert(\"Updated sucessfully!\")</script>";
			}else{
				#$errorMsg='<font style="color:#FF0000"> error '.$obj_birthCert->getErrorMsg().'</font>';
				echo '<em class="warn"><font style="color:#FF0000"><b>'.$db->ErrorMsg().'<b></font></em>';
			}
		break; */
	}# end of switch statement
}

#edited by Cherry 11-30-2010
/*$socInfo = $socserv->getSocialServPatientEncounter($encounter_nr);

if(!$socInfo || empty($socInfo) || $socInfo==''){
	$socInfo = 1;
}
$education = $ic_obj->getEducationalAttainment();
$occupation = $socserv->getOccupation();
$relationship = $socserv->getRelation();   */

#---------setup options for Educational Attainment---------#
$listEducAttain=array();
	if(is_object($education)){
		while($educInfo = $education->FetchRow()){
				$listEducAttain[$educInfo['educ_attain_nr']] = $educInfo['educ_attain_name'];
		}
	}
#---------------end------------------#

#---------setup options for Occupation---------#
$listOccupation=array();
	if(is_object($occupation)){
		while($occuInfo = $occupation->FetchRow()){
				$listOccupation[$occuInfo['occupation_nr']] = $occuInfo['occupation_name'];
		}
	}
#---------------end------------------#

#---------setup options for Occupation---------#
$listRelation=array();
	if(is_object($relationship)){
		while($relInfo = $relationship->FetchRow()){
				$listRelation[$relInfo['id']] = $relInfo['child_relation'];
		}
	}
#---------------end------------------#

#--------setup options for medical officer------#
$listDoctors=array();
	 $doctors = $pers_obj->getDoctors(1);

	if (is_object($doctors)){
		while($drInfo=$doctors->FetchRow()){

				$middleInitial = "";
				if (trim($drInfo['name_middle'])!=""){
						$thisMI=split(" ",$drInfo['name_middle']);
						foreach($thisMI as $value){
								if (!trim($value)=="")
										$middleInitial .= $value[0];
						}
						if (trim($middleInitial)!="")
								$middleInitial .= ". ";
				}

				#$name_doctor = trim($drInfo["name_first"])." ".trim($drInfo["name_2"])." ".$middleInitial.trim($drInfo["name_last"]);
				#$name_doctor = "Dr. ".$name_doctor;
				$name_doctor = trim($drInfo["name_last"]).", ".trim($drInfo["name_first"])." ".$middleInitial; #substr(trim($drInfo["name_middle"]),0,1).$dot;
				$name_doctor = ucwords(strtolower($name_doctor)).", MD";

				#echo "<br> dr = ".$name_doctor;
				#$listDoctors['doctor_name']=$name_doctor;

				$listDoctors[$drInfo["personell_nr"]]=$name_doctor;
				#$listDoctors['doctor_nr']=$drInfo["personell_nr"];

				#print_r($listDoctors);
		}
 }
#--------------end---------------#

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Driver's License</title>
<style type="text/css">
<!--
.style2 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
body {
	background-color: #FFFFFF;
}
.style3 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 10px;
	color: #FFFFFF;
	font-weight: normal;
}
.style4 {
	font-family: Arial, Helvetica, sans-serif;
	font-weight: bold;
}
.style5 {	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	color: #FFFFFF;
	font-weight: bold;
}
.style6 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 16px; color: #307D7E}
.style8 {color: #7C3501}
/*.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #333300; } */
.style9 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #FFE87C; }
.style11 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #7C3501; }
.style12 {color: #000066}
.style13 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #000066; }
.style15 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #FF6600; }
.style18 {font-family: Arial, Helvetica, sans-serif; font-weight: bold; font-size: 12px; color: #F9FFFB; }
a {
	font-size: 10px;
	color: #006600;
	font-family: Arial, Helvetica, sans-serif;
}
a:link {
	text-decoration: none;
}
a:visited {
	text-decoration: none;
}
a:hover {
	text-decoration: none;
}
a:active {
	text-decoration: none;
}
-->
</style>

<script language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php');
?>
</script>

<?php
	echo '<script type="text/javascript" language="javascript" src="'.$root_path.'js/jsprototype/prototype.js"></script>'."\n";

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">'."\n";
/*
	echo '<script type="text/javascript" src="'.$root_path.'js/fat/fat.js"></script>'."\r\n";
*/
#edited by VAN 02-16-08
	echo '<script language="javascript" src="'.$root_path.'js/setdatetime.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/checkdate.js"></script>'."\n";
	echo '<script language="javascript" src="'.$root_path.'js/dtpick_care2x.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>'."\n";
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>'."\n";

?>

<script language="javascript">

		/*
				This will trim the string i.e. no whitespaces in the
				beginning and end of a string AND only a single
				whitespace appears in between tokens/words
				input: object
				output: object (string) value is trimmed
		*/
	function trimString(objct){
		objct.value = objct.value.replace(/^\s+|\s+$/g,"");
		objct.value = objct.value.replace(/\s+/g," ");
	}/* end of function trimString */

	function trimString2(value){
		value = value.replace(/^\s+|\s+$/g,"");
		value = value.replace(/\s+/g," ");
	}/* end of function trimString */

	function blocking(objectName, flag){
//		alert("objectName = '"+objectName+"' \n flag = '"+flag+"'");
		if (document.layers) {
			document.layers[objectName].display = (flag) ? '' : 'none';
		} else if (document.all) {
			document.all[objectName].style.display = (flag) ? '' : 'none';
		} else if (document.getElementById) {
			document.getElementById(objectName).style.display = (flag) ? '' : 'none';
		}
	}/* end of function blocking*/

	var seg_validTime=false;

	function setFormatTime(thisTime){
	//	var time = $('time_text_d');
		var stime = thisTime.value;
		var hour, minute;
		var ftime ="";
		var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
		var f2 = /^[0-9]\:[0-5][0-9]$/;

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

		if (hour==0){
			 hour = 12;
			 $('selAMPM').value = "A.M.";
		}else	if((hour > 12)&&(hour < 24)){
			 hour -= 12;
			 $('selAMPM').value = "P.M.";
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

	//check if the object value exists in the array
	Array.prototype.in_array = function ( obj ) {
	var len = this.length;
	for ( var x = 0 ; x <= len ; x++ ) {
		if ( this[x] == obj ) return true;
	}
	return false;
}

function key_check(e, value){
			if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
			return true;
			}else
			return false;
		}

	//Added by Cherry 08-26-10
	function formatValue(num,dec){
	var nf = new NumberFormat(num.value);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	num.value = nf.toFormatted();
}

	function computeCapita(){
	var percapita;
	var nodep = document.getElementById('nr_dependents').value;
	var mincome = document.getElementById('monthly_income').value;
	if (((mincome)&&(mincome!=0)) && ((nodep)&&(nodep)!=0)){
		percapita = parseInt(mincome) / parseInt(nodep);
		document.getElementById('per_capita_income').value = percapita.toFixed(2);
		//document.getElementById('m_cincome').value = percapita;
	}else{
		if ((mincome)&&(mincome!=0)){
			document.getElementById('per_capita_income').value = mincome.toFixed(2);
			//document.getElementById('m_cincome').value = mincome;
		}else{
			document.getElementById('per_capita_income').value = "";
			//document.getElementById('m_cincome').value = 0;
		}
	}
}

	function computeTotal(){
	//	alert('cscsdcds');
	var total=0, food=0, light = 0, fuel = 0, education=0, househelp=0;
	var water=0, transport=0, other=0, clothing=0, med=0, insurance=0;
	//alert($F('hauz_lot2'));

	if ($F('fuel_expense'))
		fuel = parseInt($F('fuel_expense'));

	if ($F('education_expense'))
		education = parseInt($F('education_expense'));

	if ($F('househelp_expense'))
		househelp = parseInt($F('househelp_expense'));

	if ($F('clothing_expense'))
		clothing = parseInt($F('clothing_expense'));

	if ($F('med_expenditure'))
		med = parseInt($F('med_expenditure'));

	if ($F('insurance_mortgage'))
		insurance = parseInt($F('insurance_mortgage'));

	if ($F('food_expense'))
		food = parseInt($F('food_expense'));

	if (($F('ligth_expense')))
		light = parseInt($F('ligth_expense'));

	if (($F('water_expense')))
		water = parseInt($F('water_expense'));

	if ($F('transport_expense'))
		transport = parseInt($F('transport_expense'));

	if ($F('other_expense'))
		other = parseInt($F('other_expense'));

	total = food + light + water + transport + other + insurance + fuel + education + househelp + clothing + med;

	document.getElementById('total_monthly_expense').value = total.toFixed(2);
}
	//--------------

	function preSet(){

	}
	//number only and decimal point is allowed
	function keyPressHandler(e){
		var unicode=e.charCode? e.charCode : e.keyCode
		//if (unicode>31 && (unicode<46 || unicode == 47 ||unicode>57)) //if not a number
		if (unicode>31 && (unicode<48 || unicode>57)) //if not a number
			return false //disable key press
	}
	//-------------------------

	function printMSWDForm(pid, encounter_nr){
			window.open("seg-socserv-mswd_form3.php?pid="+pid+"&encounter_nr="+encounter_nr,"MSWD Form 3","modal, width=900,height=700,menubar=no,resizable=yes,scrollbars=no");
		//}

	}/* end of function printBirthCert */

	function chckForm(a){
		var d = document.social_service;

		/*if((d.monthly_income.value=='0.00') || (d.food_expense.value=='0.00') || (d.ligth_expense.value=='0.00') || (d.water_expense.value=='0.00') || (d.transport_expense.value=='0.00') || (d.house_type.value=='0.00')){
			alert('Please fill-in all the fields');
			$('ligth_expense').focus();
			return false;
		}else{
			return true;
		}                  */

		alert('Please fill-in all the fields')
		return false;

	}/* end of function chckForm */


	function stristr( haystack, needle, bool ) {
		// http://kevin.vanzonneveld.net
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		// *     example 1: stristr('Kevin van Zonneveld', 'Van');
		// *     returns 1: 'van Zonneveld'
		// *     example 2: stristr('Kevin van Zonneveld', 'VAN', true);
		// *     returns 2: 'Kevin '

		var pos = 0;

		pos = haystack.toLowerCase().indexOf( needle.toLowerCase() );
		if( pos == -1 ){
				return false;
		} else{
				if( bool ){
						return haystack.substr( 0, pos );
				} else{
						return haystack.slice( pos );
				}
		}
}

	function ucwords( str ) {
		// http://kevin.vanzonneveld.net
		// +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
		// +   improved by: _argos
		// *     example 1: ucwords('kevin van zonneveld');
		// *     returns 1: 'Kevin Van Zonneveld'
		// *     example 2: ucwords('HELLO WORLD');
		// *     returns 2: 'HELLO WORLD'

		return str.replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase ( ); } );
	}
	//-----------------------
</script>
</head>

<body onload="preSet();">
<?php echo $errorMsg; ?>
<form id="med_cert" name="med_cert" method="post" action="" onsubmit="return chckForm(this);">
<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
	<tr>
		<td align="center" bgcolor="#F9FFFB">

				<span class="style2"><?=$row['hosp_country']?></span><BR />
				<span class="style2"><?=$row['hosp_agency']?></span><BR />
				<span class="style2"><? echo "Center for Health Development - Davao Region"?></span><BR/>
				<span class="style15"><?=$row['hosp_name']?></span><BR/><BR/>

		</td>
	</tr>

	<tr>
		<td align="center" bgcolor="#F9FFFB" class="style6">PATIENT'S HEALTH ASSESSMENT FORM</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td align="center" bordercolor="#FFCCCC" bordercolordark="#000000">
				 <div align="center">

</div>

		<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<!--<td width="3%" bgcolor="#98D228" ><img src="images/brdr_03.gif" width="31" height="26" /></td>
					<td width="94%" bgcolor="#98D228"><span class="style5">CHILD</span></td>
					<td width="3%" bgcolor="#98D228">&nbsp;</td>-->
					<td width="25%" bgcolor="#3EA99F" class="style9" align="center">Last Name<br>
					<?
						$last_name = $encInfo['name_last'];
						$first_name = $encInfo['name_first'];
						$middle_name = $encInfo['name_middle'];
						$position = $encInfo['occupation'];

					?>
					<input type="text" name="last_name" id="last_name" size="20" value="<?=$last_name?>" readonly="readonly">
					</td>
					<?//print_r($encInfo);
					?>
					<td width="25%" bgcolor="#3EA99F" class="style9" align="center">First Name<br>
					<?
						/*if($encInfo['encounter_type']=='1' || $encInfo['encounter_type']=='2'){
							$admission_dt = date("F j, Y", strtotime($encInfo['encounter_date']));
						}else if($encInfo['encounter_type']=='3' || $encInfo['encounter_type']=='4'){
							$admission_dt = date("F j, Y", strtotime($encInfo['admission_dt']));
						}       */
					?>
					<input type="text" name="first_name" id="first_name" size="20" value="<?=$first_name?>" readonly="readonly">
					</td>
					<!--
					<td width="20%" bgcolor="#A0C2DA" class="style9" align="center">Ward&nbsp;&nbsp;&nbsp;
					<input type="text" name="ward" id="ward" size="20"><br>ER&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="er" id="er" size="20"><br>OPD&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="text" name="opd" id="opd" size="20">
					</td>-->
					<td width="25%" bgcolor="#3EA99F" class="style9" align="center">Middle Name<br>
					<input type="text" name="middle_name" id="middle_name" size="20" value="<? echo $middle_name ?>" readonly="readonly">
					</td>
					<td width="25%" bgcolor="#3EA99F" class="style9" align="center">Position<br>
					<input type="text" name="position" id="position" size="20" value="<? echo $position ?>" readonly="readonly">
					</td>
				</tr>

				<tr bgcolor="#3EA99F">
					<td colspan="4"> &nbsp;</td>
				</tr>

					<!--<tr>
					<td colspan="4" bgcolor="#3EA99F"></td>
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<td width="10%" bgcolor="#3EA99F" class="style9" align="center">Age<br>
								<input type="text" name="age" id="age" size="10" value="<? echo $age ?>" readonly="readonly">
							</td>
							<td width="10%" bgcolor="#3EA99F" class="style9" align="center">Sex<br>
								<input type="text" name="sex" id="sex" size="10" value="<? echo $sex ?>" readonly="readonly">
							</td>
							<td width="10%" bgcolor="#3EA99F" class="style9" align="center">Civil Status<br>
								<input type="text" name="civil_status" id="civil_status" size="10" value="<? echo $civil_status ?>" readonly="readonly">
							</td>
							<td width="*" bgcolor="#3EA99F">
								<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
									<tr>
										<td width="100%" bgcolor="#3EA99F" class="style9" align="left">&nbsp;&nbsp;Residential Address:
											&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="text" id="address" name="address" size="40" value="<? echo $address ?>">
										</td>
									</tr>
									<tr>
										<td width="100%" bgcolor="#3EA99F" class="style9" align="left">&nbsp;&nbsp;OPD no:
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<input type="text" id="contact" name="contact" size="20" value="<? echo $contact ?>">
										</td>
									</tr>
								</table>
							</td>
						</table>
				</tr>


				<tr bgcolor="#3EA99F">
					<td colspan="4"> &nbsp;</td>
				</tr>                       -->

				<tr bgcolor="#307D7E">
					<td align="left" class="style9" colspan="6">&nbsp;&nbsp;
					I. HAS THE APPLICANT SUFFERED FROM, OR HAS BEEN TOLD HE HAD ANY OF THE FOLLOWING CONDITIONS:</td>
				</tr>

				<tr>
					<td colspan="6" bgcolor="#307D7E">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td height="200" align="center" bgcolor="#F9FFFB">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#307D7E">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td colspan="9" bgcolor="	#92C7C7"></td>
																<table width="100%" border="0" cellspacing="1" cellpadding="1">
																<tr>
																	<td width="23%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																	<td width="23%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																	<td width="24%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																</tr>


																	<?
																	$jump1 = 10;
																	$jump2 = 20;

																	$cond_list = $ic_obj->getDriverCondList();

																	if(is_object($cond_list)){
																		while($row = $cond_list->FetchRow()){
																			$rowCondList[$row['id']] = $row['id'].". ".$row['cond_name'];
																		}
																	}
																	$num_cond = $cond_list->RecordCount();
																	$num_cond = $num_cond / 3;
																	#echo "cond_list = ".$cond_list;
																	for($cnt = 1; $cnt<=$num_cond; $cnt++){
																		#echo $rowCondList[$cnt];
																		$string = '
																							<tr>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowCondList[$cnt].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="cond'.$cnt.'[]" id="cond'.$cnt.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="cond'.$cnt.'[]" id="cond'.$cnt.'" value="0">
																							</td>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowCondList[$cnt+$jump1].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="cond'.$tmp = $cnt + $jump1.'[]" id="cond'.$cnt1 = $cnt + $jump1.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="cond'.$tmp = $cnt + $jump1.'[]" id="cond'.$cnt1 = $cnt + $jump1.'" value="0">
																							</td>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowCondList[$cnt+$jump2].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="cond'.$tmp = $cnt + $jump2.'[]" id="cond'.$cnt2 = $cnt +$jump2.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="cond'.$tmp = $cnt + $jump2.'[]" id="cond'.$cnt2 = $cnt +$jump2.'" value="0">
																							</td>
																							</tr>';

																		echo $string;
																	}

																	?>
																</table>
														</tr>

														<tr>
															<td valign="" bgcolor="#92C7C7" class="style9" align="left">Personal/Social History:<br>
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<textarea id="personal_history" name="personal_history" cols="80" rows="3"></textarea>
															</td>
														</tr>

														<tr>
															<td valign="" bgcolor="#92C7C7" class="style9" align="left">Family History:<br>
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<textarea id="family_history" name="family_history" cols="80" rows="3"></textarea>
															</td>
														</tr>

														<tr>
															<td valign="" bgcolor="#92C7C7" class="style9" align="left">Immunization History:<br>
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<textarea id="immunization_history" name="immunization_history" cols="80" rows="3"></textarea>
															</td>
														</tr>

													</table>
												</td>
											</tr>
										</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr bgcolor="#3EA99F">
					<td align="center" class="style9" colspan="6" height="30">&nbsp;&nbsp;
					MEDICAL EXAMINATION CHART</td>
				</tr>


				<tr bgcolor="#307D7E">
					<td align="left" class="style9" colspan="6">&nbsp;&nbsp;
					II. PHYSICAL EXAMINATION</td>
				</tr>

				<tr>
					<td colspan="6" bgcolor="#307D7E">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td height="150" align="center" bgcolor="#F9FFFB">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#307D7E">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="5%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Height<br/>
																<input type="text" name="height" id="height" value="" align="middle" size="5">
															</td>
															<td width="5%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Weight<br/>
																<input type="text" name="weight" id="weight" value="" align="middle" size="5">
															</td>
															<td width="14%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Blood Pressure<br/>
																<input type="text" name="blood_pressure" id="blood_pressure" value="" align="middle" size="10">
															</td>
															<td width="10%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Pulse Rate<br/>
																<input type="text" name="pulse_rate" id="pulse_rate" value="" align="middle" size="10">
															</td>
															<td width="14%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Respiratory Rate<br/>
																<input type="text" name="respiratory_rate" id="respiratory_rate" value="" align="middle" size="10">
															</td>
															<td width="*" valign="top" bgcolor="#92C7C7" class="style9" align="left">&nbsp;Body Built
																 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<input type="radio" name="body_built" id="body_built" value="0" align="middle">poorly developed &nbsp;&nbsp;&nbsp;
																<input type="radio" name="body_built" id="body_built" value="1" align="middle">well developed<br>
																 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<input type="radio" name="body_built" id="body_built" value="2" align="middle">fairly developed&nbsp;&nbsp;&nbsp;
																&nbsp;&nbsp;&nbsp;
																<input type="radio" name="body_built" id="body_built" value="3" align="middle">obese
															</td>
														</tr>

														<tr>
												<td height="36" colspan="6" valign="top" bgcolor="#307D7E">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td width="15%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Visual Acuity<br/></td>
															<td width="5%" valign="top" bgcolor="#92C7C7" class="style9" align="center">OD</td>
															<td width="5%" valign="top" bgcolor="#92C7C7" class="style9" align="center">OS</td>
															<td colspan="2" valign="top" bgcolor="#92C7C7" class="style9" align="center">Color Vision/Ishihara</td>
															<!--<td colspan="3" valign="top" bgcolor="#98AFC7" class="style9" align="center">Hearing</td>-->
															<td colspan="3">
																<table width="100%" border="0" cellpadding="0" cellspacing="1">
																	<tr>
																		<td colspan="3" valign="top" bgcolor="#92C7C7" class="style9" align="center">Hearing</td>
																	</tr>
																	<tr>
																		<td width="20%" valign="top" bgcolor="#92C7C7" class="style9" align="center">&nbsp;</td>
																		<td width="40%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Conversation</td>
																		<td width="40%" valign="top" bgcolor="#92C7C7" class="style9" align="center">Audiometry</td>
																	</tr>
																</table>
															</td>
															<td width="*" valign="top" bgcolor="#92C7C7" class="style9" align="center">Clarity of Speech<br>
															(Radio Officer only)
															</td>
														</tr>
														<tr>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																W/O Correction <br>
																With Correction
															</td>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																<input type="radio" name="visual_acuity" id="visual_acuity" value="0" align="middle"><br>
																<input type="radio" name="visual_acuity" id="visual_acuity" value="2" align="middle">
															</td>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																<input type="radio" name="visual_acuity" id="visual_acuity" value="1" align="middle"><br>
																<input type="radio" name="visual_acuity" id="visual_acuity" value="3" align="middle">
															</td>
															<td width="15%" valign="top" bgcolor="#92C7C7" class="style9" align="center">
																Adequate<br>
																Defective
															</td>
															<td width="5%" valign="top" bgcolor="#92C7C7" class="style9" align="center">
																<input type="radio" name="color_vision" id="color_vision" value="0" align="middle"><br>
																<input type="radio" name="color_vision" id="color_vision" value="1" align="middle">
															</td>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																AD<br>
																AS
															</td>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																<input type="radio" name="hearing" id="hearing" value="0" align="middle"><br>
																<input type="radio" name="hearing" id="hearing" value="2" align="middle">
															</td>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																<input type="radio" name="hearing" id="hearing" value="1" align="middle"><br>
																<input type="radio" name="hearing" id="hearing" value="3" align="middle">
															</td>
															<td valign="top" bgcolor="#92C7C7" class="style9" align="center">
																<input type="radio" name="speech" id="speech" value="0" align="middle">&nbsp;&nbsp;Adequate<br>
																<input type="radio" name="speech" id="speech" value="1" align="middle">&nbsp;&nbsp;Defective
															</td>
														</tr>

														<tr>
															<td colspan="9" bgcolor="#92C7C7"></td>
																<table width="100%" border="0" cellspacing="1" cellpadding="1">
																<tr>
																	<td valign="middle" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td colspan="2" align="center" valign="top" bgcolor="#92C7C7" class="style9">Normal</td>
																	<td valign="middle" bgcolor="#92C7C7" class="style9" align="center">Remarks</td>
																	<td valign="middle" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td colspan="2" align="center" valign="top" bgcolor="#92C7C7" class="style9">Normal</td>
																	<td valign="middle" bgcolor="#92C7C7" class="style9" align="center">Remarks</td>
																</tr>
																<tr>
																	<td width="23%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																	<td width="17%" align="center" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="23%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																	<td width="17%" align="center" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>

																</tr>


																	<?
																	$jump1 = 10;
																	$jump2 = 20;

																	$phys_list = $ic_obj->getDriverPhysicalExamList();

																	if(is_object($phys_list)){
																		while($row = $phys_list->FetchRow()){
																			$rowPhysList[$row['id']] = $row['id'].". ".$row['label_name'];
																		}
																	}
																	#$num_cond = $phys_list->RecordCount();
																	$num_phys = 10;
																	#echo "cond_list = ".$cond_list;
																	$string = "";
																	for($cnt = 1; $cnt<=$num_phys; $cnt++){
																		#echo $rowCondList[$cnt];
																		if($cnt<9){
																		$string = '
																							<tr>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowPhysList[$cnt].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="phys'.$cnt.'[]" id="phys'.$cnt.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="phys'.$cnt.'[]" id="phys'.$cnt.'" value="0">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2">
																								<textarea class="style2" id="phys_remarks'.$cnt.'" name="phys_remarks[]" cols="23" rows="2"></textarea>
																							</td>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowPhysList[$cnt+$jump1].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="phys'.$tmp = $cnt + $jump1.'[]" id="phys'.$cnt1 = $cnt + $jump1.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="phys'.$tmp = $cnt + $jump1.'[]" id="phys'.$cnt1 = $cnt + $jump1.'" value="0">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2">
																								<textarea class="style2" id="phys_remarks'.$cnt1 = $cnt + $jump1.'" name="phys_remarks[]" cols="25" rows="2"></textarea>
																							</td>
																							</tr>';
																		}else{
																			$string = '<tr>
																									<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowPhysList[$cnt].'</td>
																									<td align="center" bgcolor="#F9FFFB" class="style2" >
																										<input type="radio" name="phys'.$cnt.'[]" id="phys'.$cnt.'" value="1">
																									</td>
																									<td align="center" bgcolor="#F9FFFB" class="style2" >
																										<input type="radio" name="phys'.$cnt.'[]" id="phys'.$cnt.'" value="0">
																									</td>
																									<td align="center" bgcolor="#F9FFFB" class="style2">
																										<textarea class="style2" id="phys_remarks'.$cnt.'" name="phys_remarks[]" cols="23" rows="2"></textarea>
																									</td>

																									';
																									if($cnt == 9){
																										$string .=' <td colspan="4" align="center" bgcolor="#F9FFFB" class="style2" >
																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																		<tr>
																																			<td align="left" bgcolor="#F9FFFB" class="style2">OTHERS:<br>
																																				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																																				&nbsp;&nbsp;&nbsp;&nbsp;
																																				<textarea id="phys_others" name="phys_others" cols="30" rows="3"></textarea>
																																			</td>

																																		</tr>
																																	</table>
																																</td>
																															 </tr>';
																									}elseif($cnt == 10){
																										$string .=' <td colspan="4" align="center" bgcolor="#F9FFFB" class="style2" >
																																	<table width="100%" border="0" cellspacing="0" cellpadding="0">
																																		<tr>
																																			<td>&nbsp;</td>
																																		</tr>
																																	</table>
																																</td>
																															 </tr>';
																									}
																		}
																		echo $string;
																	}


																	?>
																</table>
														</tr>

													</table>
												</td>
											</tr>
										</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr bgcolor="#307D7E">
					<td align="left" class="style9" colspan="6">&nbsp;&nbsp;
					III. DIAGNOSTIC REPORT:</td>
				</tr>

				<tr>
					<td colspan="6" bgcolor="#307D7E">
						<table width="100%" border="0" cellspacing="1" cellpadding="0">
							<tr>
								<td height="200" align="center" bgcolor="#F9FFFB">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td height="36" colspan="3" valign="top" bgcolor="#307D7E">
													<table width="100%" border="0" cellpadding="0" cellspacing="1">
														<tr>
															<td colspan="9" bgcolor="#92C7C7"></td>
																<table width="100%" border="0" cellspacing="1" cellpadding="1">
																<!--<tr>
																	<td width="23%" valign="top" bgcolor="#98AFC7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#98AFC7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#98AFC7" class="style9">No</td>
																	<td width="23%" valign="top" bgcolor="#98AFC7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#98AFC7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#98AFC7" class="style9">No</td>
																	<td width="24%" valign="top" bgcolor="#98AFC7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#98AFC7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#98AFC7" class="style9">No</td>
																</tr>  -->
																	<td colspan="9" bgcolor="#92C7C7"></td>
																<table width="100%" border="0" cellspacing="1" cellpadding="1">
																<tr>
																	<td valign="middle" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td colspan="2" align="center" valign="top" bgcolor="#92C7C7" class="style9">Normal</td>
																	<td valign="middle" bgcolor="#92C7C7" class="style9" align="center">Remarks</td>
																	<td valign="middle" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td colspan="2" align="center" valign="top" bgcolor="#92C7C7" class="style9">Normal</td>
																	<td valign="middle" bgcolor="#92C7C7" class="style9" align="center">Remarks</td>
																</tr>
																<tr>
																	<td width="23%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																	<td width="17%" align="center" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="23%" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">Yes</td>
																	<td width="5%" align="center" valign="top" bgcolor="#92C7C7" class="style9">No</td>
																	<td width="17%" align="center" valign="top" bgcolor="#92C7C7" class="style9">&nbsp;</td>

																</tr>

																	<?
																	$jump1 = 5;
																	$add_tmp = 1;
																	$diag_list = $ic_obj->getDriverDiagnosisList();

																	if(is_object($diag_list)){
																		while($row = $diag_list->FetchRow()){
																			$rowDiagList[$row['id']] = $row['id'].". ".$row['label_name'];
																		}
																	}
																	#$num_cond = $cond_list->RecordCount();
																	$num_diag = 5;
																	#echo "cond_list = ".$cond_list;
																	for($cnt = 1; $cnt<=$num_diag; $cnt++){
																		#echo $cnt;
																		if($cnt<3){
																		$string = '<tr>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="0">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2">
																								<textarea class="style2" id="diag_remarks'.$cnt.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																							</td>
																							<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt+$jump1].'</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="diag'.$tmp = $cnt + $jump1.'[]" id="diag'.$cnt1 = $cnt + $jump1.'" value="1">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2" >
																								<input type="radio" name="diag'.$tmp = $cnt + $jump1.'[]" id="diag'.$cnt1 = $cnt + $jump1.'" value="0">
																							</td>
																							<td align="center" bgcolor="#F9FFFB" class="style2">
																								<textarea class="style2" id="diag_remarks'.$cnt1 = $cnt + $jump1.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																							</td>
																							</tr>';


																		echo $string;
																		}else if($cnt == 3){
																			$string = '<tr>
																									<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt].'</td>
																									<td align="center" bgcolor="#F9FFFB" class="style2" >
																										<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="1">
																									</td>
																									<td align="center" bgcolor="#F9FFFB" class="style2" >
																										<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="0">
																									</td>
																									<td align="center" bgcolor="#F9FFFB" class="style2">
																										<textarea class="style2" id="diag_remarks'.$cnt.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																									</td>
																									<td colspan="4" bgcolor="#98AFC7">
																										<table width="100%" border="0" cellspacing="1" cellpadding="1">
																											<tr>
																												<td width="46%" align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt+$jump1].'</td>
																												<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" >
																													<input type="radio" name="diag'.$tmp = $cnt + $jump1.'[]" id="diag'.$cnt1 = $cnt + $jump1.'" value="1">
																												</td>
																												<td width="10%" align="center" bgcolor="#F9FFFB" class="style2" >
																													<input type="radio" name="diag'.$tmp = $cnt + $jump1.'[]" id="diag'.$cnt1 = $cnt + $jump1.'" value="0">
																												</td>
																												<td width="34%" align="center" bgcolor="#F9FFFB" class="style2">
																													<textarea class="style2" id="diag_remarks'.$cnt1 = $cnt + $jump1.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																												</td>
																											</tr>

																											<tr>
																												<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt+$jump1+$add_tmp].'</td>
																												<td align="center" bgcolor="#F9FFFB" class="style2" >
																													<input type="radio" name="diag'.$tmp = $cnt + $jump1 + $add_tmp.'[]" id="diag'.$cnt1 = $cnt + $jump1 + $add_tmp.'" value="1">
																												</td>
																												<td align="center" bgcolor="#F9FFFB" class="style2" >
																													<input type="radio" name="diag'.$tmp = $cnt + $jump1 + $add_tmp.'[]" id="diag'.$cnt1 = $cnt + $jump1 + $add_tmp.'" value="0">
																												</td>
																												<td align="center" bgcolor="#F9FFFB" class="style2">
																													<textarea class="style2" id="diag_remarks'.$cnt1 = $cnt + $jump1 + $add_tmp.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																												</td>
																											</tr>
																										</table>
																									</td>
																								</tr>';
																			echo $string;
																		}else if($cnt==4){
																			$string = '<tr>
																										<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt].'</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="1">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="0">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2">
																											<textarea class="style2" id="diag_remarks'.$cnt.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																										</td>
																										<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt+$jump1+$add_tmp].'<br>
																										Film No:&nbsp; <input type="text" name="film_no" id="film_no" size="5">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$tmp = $cnt + $jump1 + $add_tmp.'[]" id="diag'.$cnt1 = $cnt + $jump1 + $add_tmp.'" value="1">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$tmp = $cnt + $jump1 + $add_tmp.'[]" id="diag'.$cnt1 = $cnt + $jump1 + $add_tmp.'" value="0">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2">
																											<textarea class="style2" id="diag_remarks'.$cnt1 = $cnt + $jump1 + $add_tmp.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																										</td>
																									</tr>';
																			echo $string;
																		}else if($cnt==5){

																			 $string =
																			'<tr>
																										<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt].'</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="1">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$cnt.'[]" id="diag'.$cnt.'" value="0">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2">
																											<textarea class="style2" id="diag_remarks'.$cnt.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																										</td>
																										<td align="left" bgcolor="#F9FFFB" class="style2" >'.$rowDiagList[$cnt+$jump1+$add_tmp].'

																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$tmp = $cnt + $jump1 + $add_tmp.'[]" id="diag'.$cnt1 = $cnt + $jump1 + $add_tmp.'" value="1">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2" >
																											<input type="radio" name="diag'.$tmp = $cnt + $jump1 + $add_tmp.'[]" id="diag'.$cnt1 = $cnt + $jump1 + $add_tmp.'" value="0">
																										</td>
																										<td align="center" bgcolor="#F9FFFB" class="style2">
																											<textarea class="style2" id="diag_remarks'.$cnt1 = $cnt + $jump1 + $add_tmp.'" name="diag_remarks[]" cols="25" rows="2"></textarea>
																										</td>
																									</tr>';
																			echo $string;
																		}
																	}

																	?>
																</table>
														</tr>

														<tr>
															<td valign="" bgcolor="#307D7E" class="style9" align="left">Diagnosis:<br>
																&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																<textarea id="diagnosis" name="diagnosis" cols="80" rows="3"></textarea>
															</td>
														</tr>

														<tr bgcolor="#307D7E">
															<td align="left" class="style9" colspan="6">&nbsp;&nbsp;
																Recommendation at the time of examination:</td>
														</tr>
														<tr>
															<td colspan="6" bgcolor="#307D7E">
																<table width="100%" border="0" cellspacing="0" cellpadding="1">
																	<tr>
																		<td width="25%" align="center" bgcolor="#307D7E" class="style9" >
																			<input type="radio" name="recommendation" id="recommendation" value="0">
																			&nbsp;&nbsp;Class A
																		</td>
																		<td width="25%" align="center" bgcolor="#307D7E" class="style9" >
																			<input type="radio" name="recommendation" id="recommendation" value="1">
																			&nbsp;&nbsp;Class B
																		</td>
																		<td width="25%" align="center" bgcolor="#307D7E" class="style9" >
																			<input type="radio" name="recommendation" id="recommendation" value="2">
																			&nbsp;&nbsp;Class C

																		</td>
																		<td width="25%" align="center" bgcolor="#307D7E" class="style9" >
																			<input type="radio" name="recommendation" id="recommendation" value="3">
																			&nbsp;&nbsp;Class D
																		</td>
																	</tr>

																</table>
															</td>
														</tr>
														<tr bgcolor="#307D7E">
															<td align="left" height="40" class="style9" colspan="6">&nbsp;&nbsp;
																Medical Officer:&nbsp;&nbsp;&nbsp;&nbsp;
																<select name="physician_nr" id="physician_nr">
																	<option value='0'>-Select a doctor-</option>
																	<?php

																		#edited by VAN 04-28-08
																		$listDoctors = array_unique($listDoctors);

																		if (empty($medchartInfo['physician_nr']))
																			$medchartInfo['physician_nr'] = 0;

																		foreach($listDoctors as $key=>$value){

																			#echo "key = ".$key;
																			#echo "<br>val = ".$value;
																			if ($medchartInfo['physician_nr']==$key){
																				echo "				<option value='".$key."' selected=\"selected\">".$value."</option> \n";
																			}else{
																				echo "				<option value='".$key."'>".$value."</option> \n";
																			}
																		}
																	?>
																</select>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!--- hindi kasali --->

			</table>
		</td>
	</tr>
</table>



</table>
<div align="center">
	<table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td  bgcolor="#82CAFF">
				<center>
	<?php
			if (!$socInfo || empty($socInfo) || $socInfo=='' || $socInfo=='1'){
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printMSWDForm(\''.$pid.'\',\''.$encounter_nr.'\')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="pid" id="pid" value="'.$pid.'">'."\n";
?>
	&nbsp; &nbsp;
	<!--<input type="button" name="Cancel" value="Cancel"  onclick="window.close()" />-->
			 </center>
		 </td>
		</tr>
	</table>
</div>
</form>
<script language="javascript">
<?php
	//echo $presetJavascript;
?>
</script>

</body>
</html>
