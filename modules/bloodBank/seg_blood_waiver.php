<?php 
#raymond

require_once('./roots.php');
// require($root_path.'include/inc_environment_global.php');
require_once($root_path.'modules/bloodBank/ajax/blood-waiver.server.php');
require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');
$xajax->printJavascript($root_path.'classes/xajax');

$components = getBloodComponents();
$source = getBloodSource();

$prevInfo = fetchWaiverInformation($refno,$hrn,$encounter_nr);

if($prevInfo){
  $dummyContent = redrawTable($prevInfo);
}

if($_GET['encounter_nr'])
	$enc_nr = $_GET['encounter_nr'];
else
	$enc_nr = "WALK-IN";

// var_dump($_GET['refno']);exit();
if($_GET['refno']!="T".$_GET['hrn']){
	$refno = $_GET['refno'];
}
else{
	$refno="";
}


$bloodgroup = getBloodType();

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
      <div class="panel-heading">Patient Details</div>
      <div class="panel-body">
      	<table class="table table-hover" style="font-family: calibri;margin-bottom: -5px;">
      		<tr>
      			<td>HRN:&nbsp;&nbsp;<strong><span id="panelhrn"><?= $_GET['hrn']; ?></span></strong></td>
      			<td>Case No.:&nbsp;&nbsp;<strong><span id="panelencounter"><?= $_GET['encounter_nr']; ?></span></strong></td>
      		</tr>
      		<tr>
      			<td>Name:&nbsp;&nbsp;<strong><span id="panelname"><?= $_GET['name']; ?></span></strong></td>
      			<td>Batch No.:&nbsp;&nbsp;<strong><span id="panelbatchnr"><?= $refno  ?></span></strong></td>
      		</tr>
      	</table>
      </div>
    </div>
  

    <input type="hidden" id="panelage" value="<?= $_GET['ages']; ?>"/>
    <input type="hidden" id="count" name="count" value="<?=count($prevInfo) ?>">

    <div class="panel panel-info">
      <div class="panel-body">
      	<table class="table table-striped" id="waiverinfo" style="font-family: calibri;margin-bottom: -5px;border-radius: 3px;">
			<thead>
			<tr>
				<th style="padding:3px;"><input type="text" class="form-control" style="width:97%;height:25px;padding:2px" id="unitno" value=""></th>
				<th style="padding:3px;">
					<select style="width:100%;padding:2px;height: 26px" id="bloodgroup">
						<?php
							while($row = $bloodgroup->FetchRow()){
						?>
							<option value="<?php echo $row['name'] ?>" datavalue="<?php echo $row['name']?>">
								<?php echo $row['name']?>
							</option>
						<?php
							}
						?>
					</select>
				</th>
				<th style="padding:3px;">
					<select style="width:100%;padding:2px;height: 26px" id="donorunit">
						
						<option value="Blood Donor Unit" datavalue="Blood Donor Unit">
							<?php echo "Blood Donor Unit"?>
						</option>
						<option value="Pooled Unit" datavalue="Pooled Unit">
							<?php echo "Pooled Unit"?>
						</option>
					</select>
				</th>
				<th style="padding:3px;" class="hideOnPool">
					<input type="text" style="width:87%;height:25px;padding:2px" class="expiry2" id="expiry">
				</th>
				<th style="padding:3px;">
					<select id="component" style="width:99%;padding:2px;height: 26px">
						<?php foreach ($components as $key => $value) : ?>
							<option value="<?= $components[$key]['id']; ?>"><?= $components[$key]['name']; ?></option>
						<?php endforeach ?>
					</select>
				</th>
				<th style="padding:3px;">
					<select id="source" style="width:99%;padding:2px;height: 26px">
						<?php foreach ($source as $key => $value) : ?>
							<option value="<?= $source[$key]['id']; ?>"><?= $source[$key]['name']; ?></option>
						<?php endforeach ?>
					</select>
				</th>
				<th style="padding:5px;">
					<button type="button" style="font-family:calibri;height: 26px;" class="btn btn-primary" title="Add" id="addNewRow" onclick="addNewRow()"><label style="margin-top: -2px">Add</label></button>
				</th>
			</tr>
			<tr><th colspan="7" style="background:#d9edf7">Waiver Information</th></tr>
			<tr>
				<th>Unit Number</th>
				<th>Blood Group</th>
				<th>Donor Unit</th>
				<th class="hideOnPool">Expiry</th>
				<th>Component</th>
				<th>Source</th>
				<th></th>
			</tr>
			
		</thead>
		
		<tbody id="info_list" style="background: white">
			<?= @$dummyContent; ?> 
		</tbody>
		</table>
      </div>
    </div>

    <div class="panel">
    	<label style="float: left; font-family: calibri;font-size: 13px;color:#337ab7">*Maximum of twenty (20) requests only</label>
      	<button id="saveprint" class="btn btn-success" style="float: right" onclick="saveAndPrintWaiver()"><span class="fa fa-print" style="margin:3px"></span>Save and Print waiver</button>
     </div>
</div>

<script type="text/javascript" src="js/blood-request-new.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/bootstrap/bootstrap.min.js"></script>
</body>
</html>