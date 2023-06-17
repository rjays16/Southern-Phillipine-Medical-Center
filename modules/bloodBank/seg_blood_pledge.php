<?php 
#raymond

require_once('./roots.php');
// require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/bloodBank/ajax/blood-waiver.server.php');
require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');
require_once($root_path.'include/care_api_classes/class_blood_bank.php');

$xajax->printJavascript($root_path.'classes/xajax');

$components = getBloodComponents();

$bloodtype = ($_GET['blood_type'] != '-Not Indicated-' ? $_GET['blood_type'] : "Not Indicated");

$bloodObj = new SegBloodBank();
$pledge_details = $bloodObj->getPledgeDetails($_GET['refno'], $_GET['encounter_nr'], $_GET['hrn']);

$blood_type_person = ($pledge_details['blood_type'] ? $pledge_details['blood_type'] : $bloodtype);
$total_quantity= ($pledge_details['no_of_units'] ? $pledge_details['no_of_units'] : $_GET['tq']);
$watcher_name = ($pledge_details['watcher_name'] ? $pledge_details['watcher_name'] : '');
$components_exp = explode(",", $pledge_details['components']);

switch ($pledge_details['donated_to']) {
	case 'spmc_program':
		$spmc_program = 'checked';
		break;
	case 'dvo_blood_center':
		$dvo_blood_center = 'checked';
		break;
	case 'brgy_donation':
		$brgy_donation = 'checked';
		break;	
}

if($pledge_details)
	$disabled_save = "disabled";
else
	$disabled_print = "disabled";

?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="<?=$root_path?>css/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/bloodbankwaiver.css">
	<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
	<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css"/>
	<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>
	<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/jscal2.css" />
	<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/border-radius.css" />
	<link rel="stylesheet" type="text/css" href="<?=$root_path?>js/jscal2/css/steel/steel.css" />
	<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/jscal2.js"></script>
	<script type="text/javascript" src="<?=$root_path?>js/jscal2/js/lang/en.js"></script>
	<script type="text/javascript" src="<?= $root_path ?>js/datefuncs.js"></script>
	<script type="text/javascript" src="<?= $root_path ?>js/gen_routines.js"></script>
    <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type="text/javascript"
            src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-timepicker-addon.js"></script>
    <script type="text/javascript"
            src="<?= $root_path ?>js/jquery/jquery.datetimepicker/jquery-ui-sliderAccess.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
    <script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
      var $J = jQuery.noConflict();
    </script>
</head>
<body>

<div class="container-fluid col-md-4" style="margin: 3px;">
	<div class="panel panel-primary">
      <div class="panel-heading">Patient's Details</div>
      <div class="panel-body">
      	<table class="table table-hover" style="font-family: calibri;margin-bottom: -5px;">
      		<tr>
      			<td>HRN:&nbsp;&nbsp;<strong><span id="panelhrn"><?php echo $_GET['hrn'] ?></span></strong></td>
      		</tr>
      		<tr>
      			<td>
      				Last Name:&nbsp;&nbsp;<strong><span id="panelnamelast"></span></strong>
	      			<div style="margin-top: -20px; padding-left: 350px">First Name:&nbsp;&nbsp;<strong><span id="panelnamefirst"></span></strong></div>
	      			<div style="margin-top: -20px; text-align: right; padding-right: 200px">M.I:&nbsp;&nbsp;<strong><span id="panelnamemiddle"></span></strong></div>
      			</td>
      		</tr>
      		<tr>
      			<td>Address:&nbsp;&nbsp;<strong><span id="panelnameaddress"></span></strong></td>
      		</tr>
      	</table>
      </div>
    </div>

    <div class="panel panel-info">
      <div class="panel-body">
      	<b>To be donated to: </b><br>
      		<div style="margin-left: 160px; margin-top: 10px">
		      	<input type="radio" name="to_be_donated" value="spmc_program" style="margin-top: -3px;" <?=$spmc_program?>> SPMC Blood Donation Program
				<input type="radio" name="to_be_donated" value="dvo_blood_center" style="margin-top: -3px;margin-left: 30px" <?=$dvo_blood_center?>> Davao Blood Center
				<input type="radio" name="to_be_donated" value="brgy_donation" style="margin-top: -3px;margin-left: 30px" <?=$brgy_donation?>> Mass Blood Donation in our Brgy 
      		</div>
      		<div style="margin-top: 20px">
      			<b style="padding-right: 75px">Blood Type:</b><input type="text" name="blood_type" id="blood_type" value="<?=$blood_type_person?>" style="width:20%;padding:2px;height: 26px" readonly>
      		</div>
      		<div style="margin-top: 10px">
      			<b style="padding-right: 70px">No. of Units: </b>
      			<input type="text" name="no_of_units" id="no_of_units" style="width:5%;padding:2px;height: 26px" value="<?=$total_quantity?>" readonly>
      		</div>
      		<div style="margin-top: 10px">
      			<b style="padding-right: 35px">Components: </b>
      			<!-- <select style="width:20%;padding:2px;height: 26px" id="components"> -->
					<?php foreach ($components as $key => $value) : ?>
						<?php $selected = (in_array($components[$key]['name'], $components_exp) ? "checked" : ''); ?>
							<input type="checkbox" name="components" value="<?= $components[$key]['id']; ?>" style="margin-top: -3px;margin-left: 30px" <?=$selected?>> <?= $components[$key]['name']; ?>
					<?php endforeach ?>
				<!-- </select> -->
      		</div>
      		<div style="margin-top: 15px">
      			<b style="padding-right: 30px">Name of Watcher: </b>
      			<input type="text" name="watcher_name" id="watcher_name" style="width:30%;padding:2px;height: 26px" value="<?=$watcher_name?>">
      		</div>
      </div>
    </div>

    <div class="panel">
    	<button id="save" class="btn btn-success" style="float: right" onclick="savePledgeCommitment()" <?=$disabled_save?>><span class="fa fa-print" style="margin:3px"></span>Save</button>
    	<button id="print" class="btn btn-success" style="float: right;margin-right: 10px" onclick="printPledgeCommitment()" <?=$disabled_print?>><span class="fa fa-print" style="margin:3px;p"></span>Generate</button>
     </div>
</div>
<input type="hidden" name="ref_no" id="ref_no" value="<?=$_GET['refno']?>">
<input type="hidden" name="encounter_nr" id="encounter_nr" value="<?=$_GET['encounter_nr']?>">
<input type="hidden" name="pid" id="pid" value="<?=$_GET['hrn']?>">
<input type="hidden" name="blood_type" id="blood_type" value="<?=$bloodtype?>">

<script type="text/javascript" src="js/blood-request-new.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/bootstrap/bootstrap.min.js"></script>
</body>
</html>