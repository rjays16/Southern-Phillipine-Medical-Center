<?php
/**
* Created by Carriane 01/27/19
* User Interface for Death Certificate
**/
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once("dashboard.common.php");

global $db; 

if ($xajax) {
    $xajax->printJavascript('../../classes/xajax');
}

require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj = new Encounter();

require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

$pid = $_GET['pid'];
$encounter_nr = $_GET['encounter_nr'];

require_once($root_path.'include/care_api_classes/class_cert_death.php'); //added rnel / rebranched carriane 01-19-18
$death_cert_obj = new DeathCertificate($pid);

// require($root_path.'include/inc_front_chain_lang.php');
?>

<html>
<head>
    <script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
    <script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type='text/javascript' src="<?=$root_path?>js/jquery/jquery.simplemodal.js"></script>
  	<script type="text/javascript" src="css/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="<?=$root_path?>js/jquery/css/jquery-ui.css" />
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
    <style type="text/css">
	    body {margin-left: 0px;margin-top: 10px;margin-right: 0px;margin-bottom: 0px;background-color: #F8F9FA;}
	    .titleFont {font-size: 18px;font-weight: bold;}
	    .cusbutton {width: 100px;font-weight: bold;}
	    .required:after {content:" *"; color: #e32;}
    </style>
</head>

<script type="text/javascript">
	$( document ).ready(function() {
	    var isinfant = document.getElementById("isinfant").value;

	    if(isinfant == 1){
	    	document.getElementById("adultTbl").style = "display:none;";
	    }else{
	    	document.getElementById("infantTbl").style = "display:none;";
	    }
	});

	function refresh(){
		location.reload();
	}

	function printDeathCert(id){
		var isinfant = document.getElementById('isinfant').value;
		var root_path = "<?=$root_path?>"
		var encounter_nr = "<?=$encounter_nr?>";

		window.open(root_path+"modules/registration_admission/certificates/cert_death_adult.php?id="+id+"&isinfant="+isinfant+"&encounter_nr="+encounter_nr,"deathCertificate","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
	}

	function saveDeathCause(id){
		var deathData = [];
		var data = new Array();
		var pid = "<?=$pid?>";
		var encounter_nr = "<?=$encounter_nr?>";
		var error  = 0;
		var isinfant = "<?=$isinfant?>";

		if(isinfant == 1) {
			main_disease = document.getElementById('main_disease').value;
			other_disease = document.getElementById('other_disease').value;
			main_maternal = document.getElementById('main_maternal').value;
			other_maternal = document.getElementById('other_maternal').value;
			other_relevant = document.getElementById('other_relevant').value;

			if(main_disease == ''){
				message = "Please indicate Main Disease/Condition of Infant";
				error = 1;
			}else if(main_maternal == ''){
				message = "Please indicate Main Maternal Disease/Condition Affecting Infant";
				error = 1;
			}else{
				data = {
					mainDisease : main_disease,
					otherDisease : other_disease,
					mainMaternal : main_maternal,
					otherMaternal : other_maternal,
					otherRelevant : other_relevant
				}
			}

		} else {
			immediate = document.getElementById('immediate').value;
			immediate_int = document.getElementById('immediate_int').value;
			antecedent = document.getElementById('antecedent').value;
			antecedent_int = document.getElementById('antecedent_int').value;
			underlying = document.getElementById('underlying').value;
			underlying_int = document.getElementById('underlying_int').value;
			other = document.getElementById('other').value;

			if(immediate == ''){
				message = "Please indicate Immediate Cause of Death";
				error = 1;
			}else if(antecedent == ''){
				message = "Please indicate Antecedent Cause of Death";
				error = 1;
			}else if(underlying == ''){
				message = "Please indicate Underlying Cause of Death";
				error = 1;
			}else{
				data = {
					immediate : immediate,
					immediate_int : immediate_int,
					antecedent : antecedent,
					antecedent_int : antecedent_int,
					underlying : underlying,
					underlying_int : underlying_int,
					other : other
				}
			}
		}

		if(error == 1){
			alert(message);
		}else{
			xajax_savedeathcause(data, pid, encounter_nr,isinfant);
		}
	}
	
</script>

<?
	$disabled = '';
	$isinfant = $_GET['isinfant'];
	$hasSaved = 0;
	$disabledPrint = 'disabled';
	$data = array('pid' => $pid, 'encounter_nr' => $encounter_nr);

	$cause = $death_cert_obj->getDeathCauseRecord($data);

	# for disabling input fields if has already saved death cert from medical records
	$checkifhassaved = $death_cert_obj->getDeathCertRecord($pid);

	if($checkifhassaved){
		$disabled = "disabled";
	}

	# for disabling print buitton if has no saved death cause from doctor's dashboard
	if($cause){
		$hasSaved = 1;
		$disabledPrint = "";
	}

	if($cause) {
		extract($cause);
	}

	$death_cause = json_decode($cause['death_cause'], true);

	#adult
	$immediateLabel = 'Immediate Cause:';
	$antecedentLabel = 'Antecedent Cause:';
	$underlyingLabel = 'Underlying Cause:';
	$otherLabel = 'Other Significant Condition Contributing to Death:';

	#infant
	$mainDisease = 'Main Disease/Condition of Infant:';
	$otherDisease = 'Other Disease/Condition of Infant:';
	$mainMaternal = 'Main Maternal Disease/Condition Affecting Infant:';
	$otherMaternal = 'Other Maternal Disease/Condition Affecting Infant:';
	$otherRelevant = 'Other Relevant Circumstances:';

?>

<body>
	<div>
		<?php
			echo '<input type="hidden" name="isinfant" id="isinfant" value="'.$isinfant.'">';
			echo '<input type="hidden" name="hasSaved" id="hasSaved" value="'.$hasSaved.'">';
		?>
	</div>
	<!-- rebranched carriane 01-19-18 from arnel's branch -->
	<div id="death_certificate" align="center">
	    <div align="center" style="overflow:hidden;">
	    	
	    	<div class="container" style="width: 95%">
	    		<table class="table table-condensed" id="adultTbl">
	    			<thead>
	    				<tr>
	    					<th colspan="2" class="titleFont">Cause of Death (Age 8 Days and Above)</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
	    					<td></td>
	    					<td></td>
	    					<td align="center">Interval Between Onset and Death</td>
	    				</tr>
	    				<tr>
	    					<td class="required"><? echo $immediateLabel; ?></td>
	    					<td>
	    						<input id="immediate" name="immediate" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['immediate']) ?>" style="width:400px;" required <? echo $disabled?> >
	    					</td>
	    					<td>
	    						<input id="immediate_int" name="immediate_int" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['immediate_int']) ?>" style="width:400px;" <? echo $disabled?> >
	    					</td>
	    				</tr>
	    				<tr>
                			<td class="required"><? echo $antecedentLabel; ?></td>
                			<td>
								<input id="antecedent" name="antecedent" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['antecedent']) ?>" style="width:400px;" required <? echo $disabled?> >
							</td>
                			<td>
								<input id="antecedent_int" name="antecedent_int" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['antecedent_int']) ?>" style="width:400px;" <? echo $disabled?> >
							</td>
                		</tr>
                		<tr>
                			<td class="required"><? echo $underlyingLabel; ?></td>
                			<td>
								<input id="underlying" name="underlying" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['underlying']) ?>" style="width:400px;" required <? echo $disabled?> >
							</td>
                			<td>
								<input id="underlying_int" name="underlying_int" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['underlying_int']) ?>" style="width:400px;" <? echo $disabled?> >
							</td>
                		</tr>
                		<tr>
                			<td><? echo $otherLabel; ?></td>
                			<td>
								<input id="other" name="other" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['other']) ?>" style="width:400px;" <? echo $disabled?> >
							</td>
                		</tr>
	    			</tbody>
	    		</table>
	    	</div>

	    	<div class="container" style="width: 95%">
	    		<table class="table table-condensed" id="infantTbl">
	    			<thead>
	    				<tr>
	    					<th colspan="2" class="titleFont">Cause of Death (Age 0 To 7 Days)</th>
	    				</tr>
	    			</thead>
	    			<tbody>
	    				<tr>
                			<td class="required"><? echo $mainDisease; ?></td>
                			<td>
								<input id="main_disease" name="main_disease" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['mainDisease']) ?>" style="width:600px;" required <? echo $disabled?> >
							</td>
                		</tr>
                		<tr>
                			<td><? echo $otherDisease; ?></td>
                			<td>
								<input id="other_disease" name="other_disease" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['otherDisease']) ?>" style="width:600px;" <? echo $disabled?> >
							</td>
                		</tr>
                		<tr>
                			<td class="required"><? echo $mainMaternal; ?></td>
                			<td>
								<input id="main_maternal" name="main_maternal" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['mainMaternal']) ?>" style="width:600px;" required <? echo $disabled?> >
							</td>
                		</tr>
                		<tr>
                			<td><? echo $otherMaternal; ?></td>
                			<td>
								<input id="other_maternal" name="other_maternal" type="text-alignt" value="<? echo $death_cert_obj->cleanInput($death_cause['otherMaternal']) ?>" style="width:600px;" <? echo $disabled?> >
                			</td>
                		</tr>
                		<tr>
                			<td><? echo $otherRelevant; ?></td>
                			<td>
								<input id="other_relevant" name="other_relevant" type="text" value="<? echo $death_cert_obj->cleanInput($death_cause['otherRelevant']) ?>" style="width:600px;" <? echo $disabled?> >
                			</td>
                		</tr>
	    			</tbody>
	    		</table>
	    	</div>
		   
		    <button type="button" onclick="printDeathCert(<? echo $pid; ?>)" <? echo $disabledPrint?> class="btn btn-info btn-lg cusbutton">
		    	Print
		    </button>
		    <?
		    	if($hasSaved){
		    ?>
		    		<button type="button" onclick="saveDeathCause(<? echo $pid; ?>)" class="btn btn-info btn-lg cusbutton">
				    	Update
				    </button>
		    <?
		    	}else{
		    ?>
		    		<button type="button" onclick="saveDeathCause(<? echo $pid; ?>)" class="btn btn-info btn-lg cusbutton">
				    	Submit
				    </button>
		    <?
		    	}
		    ?>
	    </div>
	</div>

	<!-- end carriane -->
</body>
</html>