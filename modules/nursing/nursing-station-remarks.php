<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'include/care_api_classes/class_person.php');


/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
$lang_tables=array('date_time.php');
define('LANG_FILE','nursing.php');
$local_user='ck_pflege_user';
//define('NO_2LEVEL_CHK',1);
require_once($root_path.'include/inc_front_chain_lang.php');
/* Create nursing notes object */
require_once($root_path.'include/care_api_classes/class_notes_nursing.php');
require_once($root_path.'include/care_api_classes/class_notes.php');
include_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path . '/frontend/bootstrap.php');


require_once($root_path.'modules/nursing/ajax/nursing-ward-common.php');
$xajax->printJavascript($root_path.'classes/xajax');

$cutoff_time_lunch_from = Config::get('dietary_cutoff_lunch_from');
$cutoff_time_lunch_to= Config::get('dietary_cutoff_lunch_to');
$cutoff_time_dinner_from = Config::get('dietary_cutoff_dinner_from');
$cutoff_time_dinner_to= Config::get('dietary_cutoff_dinner_to');
$get_icu_ward = Config::get('icu_ward');#List of ICU na ang ward_in is not ICU


$_POST['cutoff_time_lunch_from'] = $cutoff_time_lunch_from->value;
$_POST['cutoff_time_lunch_to'] = $cutoff_time_lunch_to->value;
$_POST['cutoff_time_dinner_from'] = $cutoff_time_dinner_from->value;
$_POST['cutoff_time_dinner_to'] = $cutoff_time_dinner_to->value;

$report_obj= new NursingNotes;
$person = new Person;
$notes_obj = new Notes;
$ward_obj= new Ward;
//if ($station=='') { $station='Non-department specific';  }
if($pday=='') $pday=date('d');
if($pmonth=='') $pmonth=date('m');
if($pyear=='') $pyear=date('Y');
$s_date=$pyear.'-'.$pmonth.'-'.$pday;

$thisfile=basename(__FILE__);

define(NO_BMI_AGE, 5);
define(NO_CATEGORY_AGE, 18);

define("ICU_WARD_TEXT", "ICU");
define("IWNHNICU_TEXT", "NICUIWNH");

require_once($root_path.'include/inc_date_format_functions.php');

if(preg_match("/^(?=.*?".ICU_WARD_TEXT.")((?!".IWNHNICU_TEXT."\b).)*$/", strtoupper($_GET["station"]))){
	$isICU = 'ICU';
}else{
	$icu_wards = explode(",",$get_icu_ward->value);
	if(in_array($_GET['station'],$icu_wards)){
		$isICU = 'ICU';
	}else{
		$isICU = ' ';
	}
}

//echo readfile("webdictionary.txt");


$tmpRoot = trim($root_path,"/");
$javascripts = array(
    "<script type='text/javascript' src='$tmpRoot/js/jsprototype/prototype.js'></script>",
    "<link rel='stylesheet' href='$tmpRoot/js/jquery/themes/seg-ui/jquery.ui.all.css' type='text/css' />",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/jquery-1.8.2.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/ui/jquery-ui-1.9.1.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/modules/nursing/js/nursing-remarks.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/listgen/listgen.js'></script>",
    "<link rel='stylesheet' href='$tmpRoot/js/listgen/css/default/default.css' type='text/css'/>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/iframecontentmws.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_draggable.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_filter.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_overtwo.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_scroll.js'></script>",
    "<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_shadow.js'></script>",
	"<script type='text/javascript' src='$tmpRoot/js/overlibmws/overlibmws_modal.js'></script>",
	//"<script type='text/javascript' src='$tmpRoot/ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>" // ADDED by Gerald
	
);
if($mode=='save' && !$Edit){
	# Know where we are
	switch($HTTP_SESSION_VARS['sess_user_origin']){
		case 'lab': $_POST['location_type_nr']=1; # 1 =department
						break;
		default: 	$_POST['location_type_nr']=2; # 2 = ward
						break;
	}
	$_POST['location_id']=$station;

	$occup = true;

$diet_list='';
$list='';
// print_r("<pre>");
// var_dump($_POST); 
// print_r("</pre>");die;
foreach (($_POST["diet"]) as $v) {
							#------------------hcare_id, accreditation_nr-----
						$diet_list = $diet_list.''. $v.',';
							}
							
							$_POST['diet'] = rtrim($diet_list,',');
							$success= $report_obj->saveDietOrder($_POST);
							if($_POST['withincutoff']){
								$success2= $report_obj->saveDietOrderCutOff($_POST);
							}
if($success = $report_obj->saveDailyWardNotes($_POST)){
		// var_dump($report_obj->getLastQuery());
			if($success= $report_obj->saveDietOrder($_POST)){
				if($_POST['withincutoff']){
					$success2= $report_obj->saveDietOrderCutOff($_POST);
				}
			header("Location:$thisfile".URL_REDIRECT_APPEND."&pn=$pn&station=$station&dept_nr=$dept_nr&location_nr=$location_nr&saved=1");
		exit;		
			}

		
	}/*else{echo $report_obj->getLastQuery()."<p>$LDDbNoUpdate";}*/else{
		if($_POST['withincutoff']){
			$success2= $report_obj->saveDietOrderCutOff($_POST);
		}
		$success= $report_obj->saveDietOrder($_POST);
	}
}else{
	if($d_notes=&$report_obj->getDailyWardNotes($pn)){
			include_once($root_path.'include/inc_editor_fx.php');
		if(!$Edit)
			$occup=true;
	}
	# If location name is empty, fetch by location nr
	if(!isset($station)||empty($station)){
		# Know where we are
		switch($HTTP_SESSION_VARS['sess_user_origin']){
			case 'amb': # Create nursing notes object
						include_once($root_path.'include/care_api_classes/class_department.php');
						$obj= new Department;
						$station=$obj->FormalName($dept_nr);
						break;
			default: # Create nursing notes object
						include_once($root_path.'include/care_api_classes/class_ward.php');
						$obj= new Ward;
						$station=$obj->WardName($location_nr);
		}
		echo $obj->getLastQuery();
	}
}
$ward_details = $ward_obj->EncounterLocationsInfo($pn);

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');
#$smarty->assign('impression','<input id="impression" name="impression" size=60 maxlength=60 type="text" value="'.$impression.'"  style="color:#006600; font:bold 14px Arial;"/>');


 $getReligion = $person->encReligion($pn);
  //added by KEMPS 01-03-2020 (BUG 2246)
 $person_data = $person->getPatientInfoByEncounter($pn);
 $person_pid =  $person_data['pid'];
 $person_name =  $person_data['patient_name'];
 $attending_dr_note = $person_data['attending_dr_note'];
 $dept_dr_note  = $person_data['dept_dr_note'];
  //end by KEMPS 01-03-2020 (BUG 2246)
  
 $getNotesInfo = $notes_obj->getDataNotes($pn);

 $getVitalDetails = $notes_obj->getVitalDetails($pn);
 #var_dump($notes_obj->sql)
$getServeMenu = $notes_obj->getLastUpdate($pn);
 
 $getDietName = $notes_obj->getDietName($pn);
 $getListDiet = $notes_obj->getListDiet($pn,$getServeMenu['selected_type']);

// var_dump($getDietName['diet_code']);exit();
$listorder = array();
// $diet_name = explode(',',$getListDiet['diet_name']);
 array_push($listorder,$getDietName['diet_code']);
$diet_name = (!empty($getListDiet['diet_name']) ? explode(',',$getListDiet['diet_name']) : $listorder);

// var_dump($diet_name);exit();

// foreach (($_POST["diet"]) as $v) {
// 							#------------------hcare_id, accreditation_nr-----
// 						$diet_list = $diet_list.''. $v.',';
// 							}
// 							$_POST['diet'] = rtrim($diet_list,',');

	$cutOffTime = true;
	$sqlTime = "SELECT TIME_FORMAT(CURTIME(), '%H:%i') AS CURTIME ";
		$exeTime = $db->GetRow($sqlTime);
		$time = $exeTime['CURTIME'];
		if($time >= "05:01" && $time <= "10:00") {
			$cutOffTime = false;
		}else if($time >= "10:01" && $time <= "15:00") {
			$cutOffTime = false;
		}

$countDiet = 0;
foreach ($diet_name as $diet_name) {
	// var_dump($diet_name);exit();
	$getListDiet = $notes_obj->getNameDiet($diet_name);
	if($countDiet>=5){
		$listBR.= "<br>";
	}
	$countDiet++;
	$list = $list.''. $getListDiet['diet_name']."<br>";
	if(!empty($getListDiet)){
		$list_view .= '<tr id=row'.$getListDiet['diet_code'].'>'.
                    	'<td></td><td><input type="hidden" name="diet[]" id="code" value='.$getListDiet['diet_code'].'>'.
                    	'<button type="button" class="'.($cutOffTime?'removebutton':'disablebtn').'" title="Remove this row" style="width:10px; background-color: Transparent; border: none;"><img src="'.$root_path.'/images/close_small.gif" style="margin-left:-5px;"></button><label style="font: 14px Arial; margin-left: 15px;">'.$getListDiet['diet_name'].'</label></td>'.
                      '</tr>';

	}
	
	
}


// var_dump($notes_obj->sql);exit()
if(empty($getListDiet)){
	$getDiet = $getDietName['diet_name'];
}
if(!empty($getListDiet) && !empty($getNotesInfo['enc'])){
	$getDiet = "&nbsp;".rtrim($list,',');
}

 if ($Edit) {
	 $disabled =  " ";
		$_POST['station']=$getNotesInfo['ward_id'];
	}
 else
 	$disabled = !empty($getNotesInfo['notes']) ? "disabled=true" : " ";
 

 $curTme  = strftime("%Y-%m-%d %H:%M:%S");
 $curDate = strftime("%b %d, %Y %I:%M%p", strtotime($curTme));
 //var_dump($getNotesInfo['attending_dr']);die;
 if ($getNotesInfo['notes'] != '') 
	 $impression = $getNotesInfo['notes'];
 else
  $impression = $getNotesInfo['impression'];
// Added by Gerald 10/08/2020
  $services = $getNotesInfo['services'];
  $ivf = $getNotesInfo['ivf'];
  $other = $getNotesInfo['other'];
  $diagnostic = $getNotesInfo['diagnostic'];
  $special = $getNotesInfo['special'];
  $additional = $getNotesInfo['additional'];
  $vs = $getNotesInfo['vs'];
  //End
 $remarks = $getNotesInfo['nRemarks'];
 $ivf = $getNotesInfo['nIVF'];
 $avail_meds = $getNotesInfo['avail_meds'];
 $gadgets = $getNotesInfo['gadgets'];
 $problems = $getNotesInfo['problems'];
 $actions = $getNotesInfo['actions'];

$enc = $getVitalDetails['encounter_nr'];
$gHeight = $getNotesInfo['nHeight'];
$gWeight = $getNotesInfo['nWeight'];
$vHeight = $getVitalDetails['height'];
$vWeight = $getVitalDetails['weight'];

$rs = $notes_obj->checkBMI($getNotesInfo['enc']);

if ($rs) {
    if ($gHeight != $vHeight || $gWeight != $vWeight) {
        $getAuto = $notes_obj->getAutoBMI($enc, $vHeight, $vWeight);
        $gHeight = $vHeight;
        $gWeight = $vWeight;
    }
}

$height = $gHeight ? number_format($gHeight,2) : number_format($vHeight, 2);
$weight = $gWeight ? number_format($gWeight,2) : number_format($vWeight, 2);

$metric = ( $weight / ($height * $height) * 10000 );
 $bmi = round($metric,2);
 $getBMI = $notes_obj->getBMI($bmi);
 #var_dump($notes_obj->sql); die();
 if ($getBMI)
 	$bmi_cat = $getBMI;
 else{
 	$getBMI = $notes_obj->getBMI2($bmi);
 	$bmi_cat = $getBMI;
 }
  #var_dump($getNotesInfo['notes']);
 #var_dump($person->sql);

 //edited by Gerald 08/04/2020
 $new_bmi = $notes_obj->getBMICategory($person_pid, $height, $weight);
// $new_bmi = $notes_obj->getBMICategory($person_pid, $height);

///////// Doctor are being fetch ///////////////
$sqldoc = "SELECT 
				fn_get_personell_name(cp.`nr`) AS doc_name
				FROM
					care_personell AS cp 
					LEFT JOIN care_person AS p 
						ON p.pid = cp.pid 
				WHERE cp.nr = ".$attending_dr_note;
		$attending_dr_list = $db->GetRow($sqldoc);
		$attending_drs = $attending_dr_list['doc_name'];
		$sql_dept = "";

$sqldept = " SELECT * FROM `care_department` WHERE nr= ".$dept_dr_note;
$attending_dept = $db->GetRow($sqldept);
$dept_nrs = $attending_dept['name_formal'];		
// var_dump($attending_drs);die;

// $list_dr_name = $attending_drs;
// $list_dept = $dept_nrs;
///////// End ///////////////
		
$smarty->assign('sFormNotes','<form method="POST" name=remform action="nursing-station-remarks.php?station='.$_POST['station']=$getNotesInfo['station_id'].'&pn='.$pn.'">');
$smarty->assign('impression','<textarea name="impression" cols=60 rows=3 '.$disabled.' required>'.$impression.'</textarea>');
// die($isICU);
$smarty->assign('isICU',$isICU); 
$smarty->assign('services','<textarea name="services" cols=60 rows=3 '.$disabled.' required>'.$services.'</textarea>');
$smarty->assign('other','<textarea name="other" cols=60 rows=3 '.$disabled.' required>'.$other.'</textarea>');

$smarty->assign('diagnostic','<textarea name="diagnostic" cols=60 rows=3 '.$disabled.' required>'.$diagnostic.'</textarea>');
$smarty->assign('special','<textarea name="special" cols=60 rows=3 '.$disabled.' required>'.$special.'</textarea>');
$smarty->assign('additional','<textarea name="additional" cols=60 rows=3 '.$disabled.' required>'.$additional.'</textarea>');
$smarty->assign('vs','<textarea name="vs" cols=60 rows=3 '.$disabled.' required>'.$vs.'</textarea>');

//if(strstr($_GET["station"], 'ICU')){

// End



/*$getdiet = "<select name=\"nDiet\" id=\"nDiet\" class=\"segInput\"  >\n".
			"<option value=\"\">--Select Diet--</option>\n";
$result = $db->Execute("SELECT sd.diet_name 
					FROM seg_diet AS sd WHERE sd.status NOT IN ('inactive','void','deleted')");
if ($result) {
	while ($row=$result->FetchRow()) {
		$getdiet.="<option value=\"".$row["diet_name"]."\">".$row['diet_name']."</option>\n";
	}
}
$getdiet .= "</select>";
if ($getDietName && !$Edit) {
$smarty->assign('diet',$getDietName['diet_name']);
}else{
$smarty->assign('diet',$getdiet);
}
*/

# DIET
$sql_diet = "SELECT sd.diet_name,sd.diet_code
					FROM seg_diet AS sd WHERE sd.status NOT IN ('inactive','void','deleted') ORDER BY sd.diet_name";

$rs_diet = $db->Execute($sql_diet);

$diet_option="<option value='0'>-Select Diet-</option>";

if (is_object($rs_diet)){
    while ($row_diet=$rs_diet->FetchRow()) {
        $selected=''; 
     
       $diet_option.='<option '.$selected.' value="'.$row_diet['diet_code'].'">'.ucwords($row_diet['diet_name']).'</option>';
    }
}

$diet_selection = '<select name="nDiet" id="nDiet" class="segInput" style="width:150px;font:bold 12px Arial;">
                        '.$diet_option.'
</select><input type="button" name="addbtn" id="addbtn" onclick="'.($cutOffTime?'AddFn()':'cutOffAdding()').'" style="margin-left: 10px;" value="Add">';


if ($getDiet && !$Edit){
	//die("x");
	$smarty->assign('listBR',$listBR);
	$smarty->assign('diet',$getDiet);
}
else{
	$smarty->assign('diet_list',$list_view);
	$smarty->assign('diet',$diet_selection);

	
	
}


$smarty->assign('dr_nr',$attending_drs);
$smarty->assign('dept_nr',$dept_nrs);

/*var_dump($_GET['diet_name']);*/
//added by KEMPS 01-03-2020 (BUG 2246)
$smarty->assign('sPatientID',$person_pid);
$smarty->assign('patient_name', $person_name);
$smarty->assign('case_number', $pn);
$smarty->assign('bedNumDisplay', $ward_details['bed_nr']);
$smarty->assign('roomNumDisplay', $ward_details['room_nr']);
//end by KEMPS 01-03-2020 (BUG 2246)

  		$age = '';
              if(strpos($getNotesInfo["age"], 'months') !== false) $age = str_replace(' months', 'm', $getNotesInfo["age"]);
              elseif(strpos($getNotesInfo["age"], 'months') !== false) $age = str_replace(' month', 'm', $getNotesInfo["age"]);
              elseif(strpos($getNotesInfo["age"], 'years') !== false) $age = str_replace(' years', 'y', $getNotesInfo["age"]);
              elseif(strpos($getNotesInfo["age"], 'year') !== false) $age = str_replace(' year', 'y', $getNotesInfo["age"]);
              elseif(strpos($getNotesInfo["age"], 'days') !== false) $age = str_replace(' days', 'd', $getNotesInfo["age"]);
              elseif(strpos($getNotesInfo["age"], 'day') !== false) $age = str_replace(' day', 'd', $getNotesInfo["age"]);

              if(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
              elseif(strpos($age, 'days') !== false) $age = str_replace(' days', 'd', $age);
              elseif(strpos($age, 'months') !== false) $age = str_replace(' months', 'm', $age);
              elseif(strpos($age, 'month') !== false) $age = str_replace(' month', 'm', $age);


   			 if((((int)substr($age, 0, -1) < NO_BMI_AGE) && substr($age,-1) == 'y' ) || (substr($age, -1)=='m') || (substr($age, -1)=='d')){
              $bmi_status = FALSE;
              $bmi_status_category = FALSE;
            }elseif (((int)substr($age, 0, -1) < NO_CATEGORY_AGE) && ((int)substr($age, 0, -1) >= NO_BMI_AGE) && substr($age,-1) == 'y') {
              $bmi_status_category = FALSE;
              $bmi_status = TRUE;
            }else{
              $bmi_status = TRUE;
              $bmi_status_category = TRUE;
            }

$brw_diet = 'position: absolute; word-break: break-all; width: 180px';
$brw_remarks = strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE ? 'float: right; margin-top: 5px' : 'float: right;';

$brw_rows = strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE ? 'rows=1' : 'rows=3';

$smarty->assign('pNotes_display', '<input class="segInput" id="pNotes_display" name="pNotes_display" disabled type="text" value ="'.$curDate.'" size="16"  style="font:bold 12px Arial; float;left;" >');
$smarty->assign('NotesDate', '<input class="segInput" id="NotesDate" name="NotesDate" type="hidden" size="16"  style="font:bold 12px Arial; float;left;" >');
$smarty->assign('religion',$getReligion);
$smarty->assign('remarks','<textarea placeholder = "Remarks:" name="remarks" cols=32 '.$brw_rows.'  '.$disabled.'>'.$remarks.'</textarea>');
$smarty->assign('ivf','<textarea name="ivf" cols=60 rows=3 '.$disabled.'>'.$ivf.'</textarea>');
$smarty->assign('avail_meds','<textarea name="avail_meds" cols=60 rows=3 '.$disabled.'>'.$avail_meds.'</textarea>');
$smarty->assign('gadgets','<textarea name="gadgets" cols=60 rows=3 '.$disabled.'>'.$gadgets.'</textarea>');
$smarty->assign('problems','<textarea name="problems" cols=60 rows=3 '.$disabled.'>'.$problems.'</textarea>');
$smarty->assign('actions','<textarea name="actions" cols=60 rows=3 '.$disabled.'>'.$actions.'</textarea>');
$smarty->assign('nBmi','<input  id="nBmi" name="nBmi"  type="hidden"  onkeypress="return isNumberKey(event)" '.$disabled.' value="'.$bmi.'";"/>');
$smarty->assign('height',$height);
$smarty->assign('weight',$weight);
$smarty->assign('bmi',($bmi_status ? $bmi :"" ));
$smarty->assign('bmi_category', $new_bmi) ;
$smarty->assign('brw_diet', $brw_diet);
$smarty->assign('brw_remarks', $brw_remarks);

$smarty->assign('javascripts',$javascripts);
# Title in toolbar
$smarty->assign('sToolbarTitle', $LDNotes.' :: '.$station.' ('.formatDate2Local($s_date,$date_format).')');

# hide back button
$smarty->assign('pbBack',FALSE);

# href for help button
$smarty->assign('pbHelp',"javascript:gethelp('patient_remarks.php','','','$station','$LDNotes')");

# href for close button
#$smarty->assign('breakfile','javascript:window.close()');
$smarty->assign('breakfile','');


# OnLoad Javascript code
if(($mode=='save')&&($occup)||$saved){
	$sTemp = "window.opener.location.reload();";
	
} else $sTemp = '';

$smarty->assign('sOnLoadJs','onLoad="'.$sTemp.' if (window.focus) window.focus();"');

# Window bar title
$smarty->assign('sWindowTitle',$LDNotes.' :: '.$station.' ('.formatDate2Local($s_date,$date_format).')');

# Collect extra javascript code
ob_start();
?>

<script language="javascript">
var n=false;
function checkForm(f)
{
	if(f.notes.value==""||f.personell_name=="") return false;
	 else return true;
}
function setChg()
{
	n=true;
}
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;
    return true;
}
</script>

<style type="text/css" name="s2">
	td.vn { font-family:verdana,arial; color:#000088; font-size:10px}
</style>

<?php

$sTemp = ob_get_contents();

ob_end_clean();

$smarty->append('JavaScript',$sTemp);

ob_start();

if($occup){
	$tbg= 'background="'.$root_path.'gui/img/common/'.$theme_com_icon.'/tableHeaderbg3.gif"';
?>
 <table border=0 cellpadding=4 cellspacing=1 width=100%>

<?php
	$toggle=0;
	// var_dump($d_notes);die();
	if($d_notes){
		while($row=$d_notes->FetchRow()){
			if($toggle) $bgc='#efefef';
				else $bgc='#f0f0f0';
			if($toggle) $sRowClass='wardlistrow2';
				else $sRowClass='wardlistrow1';
			$toggle=!$toggle;
		/*if(!empty($row['short_notes'])) $bgc='yellow';*/

?>


		<tr  class="<?php echo $sRowClass ?>"  valign="top">
			<td><?php  $smarty->assign('datetime',date('F d, Y',strtotime($row['date']))." ".date('h:i A',strtotime($row['time']))); ?></td> <!-- update by carriane 09/14/17 -->

		</td>
		<!-- 	<td><?php if($row['personell_name']) echo $row['personell_name']; ?></td> -->
			<td><?php  $smarty->assign('lastmod',$row['personell_name']); ?></td>
		</tr>

<?php
	}
}else{
?>
		<tr  class="<?php echo $sRowClass ?>"  valign="top">
			<td><?php  $smarty->assign('datetime',date('F d, Y',strtotime($getNotesInfo['date']))." ".date('h:i A',strtotime($getNotesInfo['time']))); ?></td> <!-- update by carriane 09/14/17 -->

		</td>
			<td><?php  $smarty->assign('lastmod',$getNotesInfo['modify_id']); ?></td>
		</tr>
<?
}
?>
</table>
<?php
}
?>


 <ul>

<!-- <form method="post" name=remform action="nursing-station-remarks.php" onSubmit="return checkForm(this)"> -->
<!-- <textarea name="notes" cols=60 rows=5 wrap="physical" onKeyup="setChg()"></textarea> -->
<!-- <input type="text" name="personell_name" size=60 maxlength=60 value="<?php echo $HTTP_SESSION_VARS['sess_user_name']; ?>" readonly> -->

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="station" value="<?php echo $station ?>">
<input type="hidden" name="location_nr" value="<?php echo $location_nr; ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="pn" id="pn" value="<?php echo $pn ?>">
<input type="hidden" name="loginid" id="loginid" value="<?php echo $_SESSION['sess_temp_userid'] ?>">
<input type="hidden" name="withincutoff" id="withincutoff" value="">
<input type="hidden" name="encounterno_bmi" id="encounterno_bmi" value="<?php echo $getVitalDetails['encounter_nr']; ?>">
<input type="hidden" name="height" id="height" value="<?php echo $height; ?>"/>
<input type="hidden" name="weight" id="weight" value="<?php echo $weight; ?>"/>

<input type="hidden" name="dept_nr" value="<?php echo $dept_nr ?>">
     <p></p>
<?php



if(empty($getNotesInfo['notes'])){
	?>
	<input type="submit" name="Submit" value="SAVE" onclick="return validationForm(this)">
	<?php
}
if(!empty($getNotesInfo['notes']) && !$Edit){
?>
	<input type="submit" name="Edit" value="Edit">
	
	<?php
} 
if ($Edit) {
	?>
	<input type="submit" name="Submit" value="SAVE"  onclick="return validationForm(this)">
	<!-- <input type="button" name="Cancel" value="Cancel" > -->
	<?php
}
?>

 

</form>

<p>
<!--<a href="javascript:window.close()"><img <?php echo createLDImgSrc($root_path,'close2.gif','0') ?>></a>-->
</ul>

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the page output to the mainframe center block

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
$smarty->assign('sMainBlockIncludeFile','nursing/nursing-notes.tpl');
 $smarty->display('common/mainframe.tpl');

 ?>