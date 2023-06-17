<?php 
#raymond

require_once('./roots.php');
// require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/bloodBank/ajax/blood-request-new.common.php');
$xajax->printJavascript($root_path.'classes/xajax');

if($_GET['encounter_nr'])
	$enc_nr = $_GET['encounter_nr'];
else
	$enc_nr = "WALK-IN";

if($_GET['refno']!="T".$_GET['hrn']){
	$refno = $_GET['refno'];
}
else{
	$refno="";
}

$currentDate = Date("Y/m/d H:i:s");
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
      	<table class="table table-striped" style="font-family: calibri;margin-bottom: -5px;border-radius: 3px;">
			<thead>
			<tr id="trans-form">

				<th style="padding:3px;">
                    <input type="hidden" id="return_reason" name="return_reason">
                    <label for="particulars">Particulars:</label>
					<select style="width:100%;padding:2px;height:25px;" id="particulars" onchange="setTransDate()" class="trans-input">
                        <option value=""></option>
						<?php
                            $blood_received_dates = array("Release of Result","Issuance of Blood","Returned Blood","Re-Issue Blood","Consumed Blood");
							foreach ($blood_received_dates as $key => $value){
						?>
							<option value="<?php echo $key ?>">
								<?php echo $value; ?>
							</option>
						<?php
							}
						?>
					</select>
				</th>

				<th style="padding:3px;width:19%;" class="hideOnPool">
                    <label for="r_date">Date & Time:</label>
					<input type="text" style="width:100%;height:25px;padding:2px" id="r_date" class="trans-input">
				</th>

                <th style="padding:3px;padding-left: 8px;">
                    <label for="serialno">Serial Number:</label>
                    <input type="text" class="form-control trans-input" autofocus onchange="setTransDate()" style="width:100%;height:25px;padding:2px" id="serialno" value=""></th>
				<th style="padding:3px;">
					<button type="button" style="font-family:calibri;height: 25px;margin-bottom: 9px;width:100%;" class="btn btn-primary trans-input" title="Add" id="save" onclick="updatePDate()"><label style="margin-top: -2px">Add</label></button>
				</th>
			</tr>

      </div>
    </div>

</div>

<script>
    let currentDate = '<?= $currentDate ?>';
    let dateObj = new Date(currentDate);
    $J('#r_date').datetimepicker({
        beforeShow: function (input, inst) {
            setTimeout(function () {
                inst.dpDiv.css({
                    top: $J("#r_date").offset().top + 25,
                    left: $J("#r_date").offset().left
                });
            }, 0);
        },
        dateFormat: 'M d, yy',
        timeFormat: 'hh:mm tt',
        onSelect: function (selectedDate) {
            let clickNow = false;
            let selDate = formatDate(new Date(selectedDate), "yyyy-mm-dd hh:mn") + ':00';

            $J(document).on('click',"button.ui-datepicker-current", function() {
                clickNow = true;
                $J("#r_date").datepicker( "setDate", dateObj);
            });
            if(clickNow === false)
                $J('#r_date').val(selDate);
        },
        

    });
    $J("#r_date").datepicker( "setDate", dateObj);
    $J('#ui-datepicker-div').hide();

    function setTransDate(){
        let particulars = $J("#particulars").val();
        let refno = '<?= $refno; ?>';
        let serial_no = $J("#serialno").val();

        if (particulars && refno && serial_no){

            $J("#trans-form").css({'pointer-events':'none'});
            xajax_setParticularDate(particulars,refno,serial_no);
        }
    }

    function updatePDate(){

        let particular = $J("#particulars").val();
        let refno = '<?= $refno; ?>';
        let serial_no = $J("#serialno").val();
        let r_date = $J("#r_date").val();
        let return_of_blood = "2";//Return of blood

        if (particular && refno && r_date && serial_no){
            let new_r_date = new Date(r_date);
            let d = formatDate(new_r_date,"yyyy-mm-dd hh:mn");
            if (particular === return_of_blood){
                let return_reason = $J("#return_reason").val();
                xajax_saveTransfusion(refno,serial_no,d,particular,return_reason);
            }else{
                xajax_saveTransfusion(refno,serial_no,d,particular);
            }
        }
    }

    function checkTrans(){
        let serial_no = $J("#serialno").val();
        if (!serial_no) return false;
        return true;
    }

    function formatDate(date,format){
        let formatted = format.replace('yyyy', date.getFullYear())
                              .replace('mm', ("0" + (date.getMonth() + 1)).slice(-2))
                              .replace('dd', ("0" + date.getDate()).slice(-2))
                              .replace('hh', ("0" + date.getHours()).slice(-2))
                              .replace('mn', ("0" + date.getMinutes()).slice(-2));
        return formatted+":00";
    }

    function addReturnReason(){
        while (true){
            let reason = prompt("Please put the reason.");
            if (reason === ""){
                alert("Reason for Return is required");
            }else{
                if(reason !== null){
                    $J("#return_reason").val(reason);
                }else{
                    $J("#particulars").val("");
                }
                break;
            }
        }
    }

</script>

<script type="text/javascript" src="<?=$root_path?>js/bootstrap/bootstrap.min.js"></script>
</body>
</html>