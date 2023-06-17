<?php 
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require_once($root_path."modules/nursing/ajax/nursing-ward-common.php");
require($root_path.'include/care_api_classes/class_person.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/billing/class_billing_new.php');
include_once($root_path . 'include/care_api_classes/class_encounter.php');
include_once($root_path . 'include/care_api_classes/class_accommodation.php');
require_once($root_path . 'include/care_api_classes/class_acl.php');

$local_user='ck_pflege_user';
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');

$smarty = new smarty_care('nursing');

 # Hide the title bar
 $smarty->assign('bHideTitleBar',TRUE);

$encounter_nr = $_GET['pn'];
$pid = $_GET['hrn'];
$name_last = $_GET['name_last'];
$name_first = $_GET['name_first'];

$objWard = new Ward();
$wards = $objWard->getAllActiveWards();

$a = 0;
while($row = $wards->FetchRow()){
	$wardList[$a]['nr'] = $row['nr'];
	$wardList[$a]['name'] = $row['name'];
	$a++;
}

/*$numbers = array(1,4,5,2,3,7,5,1,2);

for($a=0; $a < count($numbers); $a++){
	$c = $a;
	for ($b=$a-1; $b > -2; $b--) {
		if($numbers[$c] < $numbers[$b] && $numbers[$b] != NULL){
			$temp = $numbers[$c];
			$numbers[$c] = $numbers[$b];
			$numbers[$b] = $temp;
			$c--;
		}else if($numbers[$c] > $numbers[$b]){
			break;
		}
	}
	
	
}*/

$objAcl = new Acl($_SESSION['sess_temp_userid']);
$canAdd = $objAcl->checkPermissionRaw('_a_2_nursingaddpatientaccommodation');
$canDelete = $objAcl->checkPermissionRaw('_a_2_nursingdeletepatientaccommodation');
$parentAccommodation = $objAcl->checkPermissionRaw('_a_1_nursingpatientaccommodation');
$parentAccommodationOnly = ($parentAccommodation && (!$canAdd && !$canDelete));

$objEnc = new Encounter();
$savedBilling = $objEnc->hasSavedBilling($encounter_nr);
$encounter_data = $objEnc->getEncounterData($encounter_nr);

if($savedBilling['is_final']){
	$smarty->assign('hasSavedBilling', "This patient has a saved bill and already advised to go home...");
	$isfinal = 1;
	$bill_dte = $savedBilling['bill_dte'];
	$bill_frmdt = $savedBilling['bill_frmdte'];
}else{
	$curTme = strftime("%Y-%m-%d %H:%M:%S");
	$curDate = strftime("%b %d, %Y %I:%M %p", strtotime($curTme));
	$bill_dte = $curDate;
	$bill_frmdt = $encounter_data['encounter_date'];
}

$death_date = $db->GetOne("SELECT STR_TO_DATE(CONCAT(DATE_FORMAT(death_date, '%Y-%m-%d'),' ', DATE_FORMAT(death_time, '%H:%i:%s')),'%Y-%m-%d %H:%i:%s') FROM care_person WHERE pid=".$db->qstr($pid));

$admission_dt = $encounter_data['admission_dt'];

$objBilling = new Billing();

$objBilling->setBillArgs($encounter_nr, $bill_dte, $bill_frmdt, $death_date);
$objBilling->greater_accom_effec = 1;
$accommodations = $objBilling->getAccomodationList()->GetRows();

$details = $objBilling->getExtractedAccommodationList($accommodations);
// echo "<pre>" . print_r($details,true) . "</pre>";die;
$a = 0;
$inc = 0;
$str_date_from = strtotime($details[0]['date_from']);
$str_date_to = strtotime($details[0]['date_to']);
$accom_dates = array();
$isdead = 0;

if($death_date != '0000-00-00 00:00:00'){
	$tempdte_to = $death_date;
	$isdead = 1;
}
else $tempdte_to = strftime("%m/%d/%Y", strtotime($bill_dte));

$missing_dates = 0;

if(count($details) != 0){
	foreach ($details as $key => $value) {
		if($details[$key]['name'] != NULL){

			$final_acc_details[$key]['ward_name'] = $details[$key]['name']." Rm # ".$details[$key]['room'];
			$final_acc_details[$key]['room'] = $details[$key]['room'];
			$final_acc_details[$key]['today'] = 0;

			if($details[$key]['date_to'] == '0000-00-00'){
				$details[$key]['date_to'] = strftime("%m/%d/%Y");
			}else
				$details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($details[$key]['date_to']));
			
			if($details[$key]['source'] == 'AD'){
	            if($details[$key]['status'] !='discharged'){
	            	$final_acc_details[$key]['today'] = 1;

	                $details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
	            }else{
	            	if(strtotime($details[$key]['date_to']) > strtotime($tempdte_to)){
		        		$details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
		        	}
	            }
	        }else{
	        	if(strtotime($details[$key]['date_to']) > strtotime($tempdte_to)){
	        		$details[$key]['date_to'] = strftime("%m/%d/%Y", strtotime($tempdte_to));
	        	}
	        }

			$details[$key]['date_from'] = strftime("%m/%d/%Y", strtotime($details[$key]['date_from']));

			if(strtotime($details[$key]['date_from']) == strtotime($details[$key-1]['date_to']))
				$str_date_from = strtotime($details[$key]['date_from']."+1 day");
			else
				$str_date_from = strtotime($details[$key]['date_from']);

			$str_date_to = strtotime($details[$key]['date_to']);

			for ($i=$str_date_from; $i<=$str_date_to; $i+=86400) {  
			    $dates[$inc] = date("m/d/Y", $i);  
			    $inc++;
			}
			// gather all dates with accommodation
			$start_from_date = new DateTime(date("m/d/Y", strtotime($details[$key]['date_from'])));
			$interval_on_date = new DateInterval('P1D');
			$end_to_date = new DateTime(date("m/d/Y", strtotime($details[$key]['date_to'])));

			if($details[$key]['date_from'] == $details[$key-1]['date_to'] && ($details[$key]['date_from'] != $details[$key]['date_to'])){
				$start_from_date = new DateTime(date("m/d/Y", strtotime($details[$key]['date_from']."+1 day")));
			}

			if($details[$key]['date_from'] != $details[$key-1]['date_to'] && $details[$key-1]['date_to'] != NULL)
				$missing_dates = 1;
			
			$end_to_date->setTime(0,0,1);

			$get_range_dates = new DatePeriod($start_from_date, $interval_on_date, $end_to_date);
			
			$date_from_arr[$key] = $details[$key]['date_from'];
			$date_to_arr[$key] = $details[$key]['date_to'];

			if(($details[$key]['date_from'] == $details[$key]['date_to'])){
				foreach ($get_range_dates as $key1 => $value1) {
					$isExistTwice = array_keys($accom_dates, $value1->format('m/d/Y'));

					if(count($isExistTwice) > 1)
						$overlaps = 1;
					else{
						$accom_dates[$a] = $value1->format('m/d/Y');
					    $a++;
					}
				}
			}else{
				foreach ($get_range_dates as $key1 => $value1) {
					if(!in_array($value1->format('m/d/Y'), $accom_dates)){
					    $accom_dates[$a] = $value1->format('m/d/Y');
					    $a++;
					}else
						$overlaps = 1;
				}
			}
			// end of gathering

			$date_from = date_create($details[$key]['date_from']);
			$date_to = date_create($details[$key]['date_to']);
			$diff = date_diff($date_from,$date_to);
			$days_stay = $diff->format("%a");

			$hrs_in_words = '';
			if($details[$key]['is_per_hour']){
				if($isdead){
					$tempdte_from = $details[$key]['date_from']." ".$details[$key]['time_from'];
					$df = strtotime($tempdte_from);
					$dt = strtotime($tempdte_to);
					$diff = $dt - $df;
					$hours = floor($diff / ( 60 * 60 ));
					$days_stay = floor($hours/24);
					$hrs_stay = $hours - ($days_stay * 24);
				}else{
					$days_stay = floor($details[$key]['hrs_stay']/24);
					$hrs_stay = $details[$key]['hrs_stay'] - ($days_stay * 24);
				}

				if($hrs_stay){
					if($hrs_stay > 1) $hrs_word = ' hrs';
					else $hrs_word = ' hr';

					$hrs_in_words = ", ".$hrs_stay." ".$hrs_word;
				}
			}

			if($days_stay <= 0 && !$details[$key]['is_per_hour']) $details[$key]['days_stay'] = 1;
			else $details[$key]['days_stay'] = $days_stay;

			if($details[$key]['days_stay'] > 1) $days_word = ' days';
			else $days_word = ' day';

			$final_acc_details[$key]['nofdays'] =$details[$key]['days_stay']." ".$days_word.$hrs_in_words." (".$details[$key]['date_from']." to ".$details[$key]['date_to'].")";

			$final_acc_details[$key]['create_id'] = $details[$key]['create_id'];
			$final_acc_details[$key]['create_dt'] = strftime("%b %d, %Y %I:%M %p", strtotime($details[$key]['create_dt']));

			$final_acc_details[$key]['ward_id'] = $details[$key]['ward_id']."_".$details[$key]['room']."_".$details[$key]['date_from']."_".$details[$key]['date_to'];
		}
	}
} else $missing_dates = 1;

if(strtotime($details[count($details)-1]['date_from']) == (strtotime(date('m/d/Y'))))
	$enableTodayDateTo = 1;

$a = 0;
// exit();

// echo "<pre>" . print_r($details,true) . "</pre>";exit();
if(!$missing_dates){
	$start_date = new DateTime(date("m/d/Y", strtotime($admission_dt)));
	$interval = new DateInterval('P1D');
	$end_date = new DateTime(date("m/d/Y", strtotime($tempdte_to)));

	$end_date->setTime(0,0,1);

	$period = new DatePeriod($start_date, $interval, $end_date);

	$a = 0;

	foreach ($period as $key => $value) {
	    $dates2[$a] = $value->format('m/d/Y');
	    $a++;
	}

	$missing_dates = array_diff($dates2, $dates);
}

if($missing_dates)
	$lack_of_date = 1;

$message = '';

$hasPermissionAdd = 0;
$hasPermissionDelete = 0;

$smarty->assign('hrn', $pid);
$smarty->assign('encounter_nr', $encounter_nr);
$smarty->assign('fullname', $name_last.", ".$name_first);
$smarty->assign('admission_dt', date("F d, Y; h:i A", strtotime($admission_dt)));
$smarty->assign('wardlist', $wardList);
$smarty->assign('accommodations', $final_acc_details);

if(!$isfinal){
	if($overlaps){
		$disabled = "disabled";
		$message .= "Accommodation has an overlapping of dates\n";
	}

	if($parentAccommodationOnly || $canAdd)
		$hasPermissionAdd = 1;

	if($parentAccommodationOnly || $canDelete)
		$hasPermissionDelete = 1;

	if($lack_of_date)
		$message .= "Accommodation has lacking of dates";
}else{
	$overlaps = 0;
	$lack_of_date = 0;
}

$dbAdmissionDate = $db->GetOne("SELECT ce.`encounter_date` FROM care_encounter ce WHERE ce.`encounter_nr` =".$db->qstr($encounter_nr));
$dbDate = $db->GetOne("SELECT NOW() - INTERVAL 1 DAY");

$smarty->assign('message', $message);
$smarty->assign('disabled', $disabled);
$smarty->assign('isfinal', $isfinal);
$smarty->assign('hasPermissionAdd', $hasPermissionAdd);
$smarty->assign('hasPermissionDelete', $hasPermissionDelete);

?>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>css/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="css/nursing.css">
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css"/>
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>

<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
<?php
ob_start();

$xajax->printJavascript($root_path.'classes/xajax');

?>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js"></script>
<script type="text/javascript" src="<?= $root_path ?>js/gen_routines.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type="text/javascript"
        src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript"
        src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/bootstrap/bootstrap.min.js"></script>
<script type="text/javascript">
	var $J = jQuery.noConflict();
</script>
<script type="text/javascript" src="js/nursing-accommodation.js"></script>

<?php
// $sTemp = ob_get_contents();

// ob_end_clean();

// $smarty->append('JavaScript',$sTemp);

?>

<input type="hidden" name="bill_date" id="bill_date" value="<?=$bill_dte?>">
<input type="hidden" name="enc_nr" id="enc_nr" value="<?=$encounter_nr?>">
<input type="hidden" name="admission_dt" id="admission_dt" value="<?=$admission_dt?>">
<input type="hidden" name="overlaps" id="overlaps" value="<?=$overlaps?>">
<input type="hidden" name="lack_of_date" id="lack_of_date" value="<?=$lack_of_date?>">
<input type="hidden" name="enableTodayDateTo" id="enableTodayDateTo" value="<?=$enableTodayDateTo?>">
<input type="hidden" name="server_date" id="server_date" value="<?= $dbDate; ?>">
<input type="hidden" name="server_admission_date" id="server_admission_date" value="<?= $dbAdmissionDate; ?>">

<?php 
	 $smarty->display('nursing/nursing-accommodation.tpl');
?>