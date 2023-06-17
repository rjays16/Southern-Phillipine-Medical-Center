<?php

include("roots.php");
include_once($root_path."/classes/fpdf/fpdf.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');

define('NO_2LEVEL_CHK',1);
define('admit_patient',1);

require($root_path.'include/inc_front_chain_lang.php');

include_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

include_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;


if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}

if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
    $encounter_nr = $_POST['encounter_nr'];
}

if (isset($_GET['pid']) && $_GET['pid']){
    $pid = $_GET['pid'];
}

if (isset($_POST['routine']) && $_POST['routine']){
	$HTTP_POST_VARS['routine'] = $_POST['routine'];
}


if (isset($_GET['encoder']) && $_GET['encoder']){
    $personell_title = $pers_obj->getPersonellTitle($_SESSION['sess_login_personell_nr']);
    $encoder = urldecode($_GET['encoder']) . ", " . $personell_title;
}

include_once($root_path.'include/care_api_classes/class_department.php');
include_once($root_path.'include/care_api_classes/class_patient_referral.php');
$obj_patientRef = new PatientReferral($encounter_nr);
$dept_obj=new Department;

$dbtime_format = "Y-m-d H:i:s";
$fulltime_format = "F j, Y g:ia";

$errorMsg='';

function checkInput($input,$field){

    $data = array();
    foreach ($field as $key => $value) {

        if(array_key_exists($value, $input))
            $data[$value] = $input[$value];
        else
            $data[$value] = "";
    }
    return $data;
}


if (isset($_POST['mode'])){

    $HTTP_POST_VARS['create_id']=$encoder;
    $HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
    $input = checkInput($HTTP_POST_VARS,$obj_patientRef->fld_seg_patient_referral);

    switch($_POST['mode']) {
        case 'save':
            
            if ($obj_patientRef->savePatientReferralInfoFromArray($input)){
                
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
            }else{
                
                $errorMsg='<font style="color:#FF0000">'.$obj_patientRef->getErrorMsg().'</font>';            
            }
        break;
        case 'update':
            
            if ($obj_patientRef->updatePatientReferralInfoFromArray($input)){
                
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_patientRef->getErrorMsg().'</font>';            
            }

        break;
    }# end of switch statement
}


    if($encounter_nr){

        if(!($encInfo=$enc_obj->getEncounterInfo($encounter_nr))){
            echo '<em class="warn"> Sorry but the page cannot be displayed!</em>';
            exit();
        }
        
    }else{
        echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
        exit();
    }

    $patientRefInfo = $obj_patientRef->getPatientReferral();
   
    if(!$patientRefInfo){

        $patientRefInfo = $obj_patientRef->fld_seg_patient_referral;

    }

?>
<html>
<head>
<style type="text/css">

body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #F8F9FA;
}
.style2 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: bold;
}

.style3 {
	font-family: Geneva, Arial, Helvetica, sans-serif;
	font-size: 12px;
	font-weight: normal;
}

.row {
  *zoom: 1;
}
.row:before,
.row:after {
  display: table;
  content: "";
  line-height: 0;
}
.row:after {
  clear: both;
}

</style>


<script language="javascript">
<?php

	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>

<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>

<!-- CALENDAR -->
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>


<!-- -->
<script language="javascript">
    
	function printPatientReferral(encounter_nr, pid){
        var encoder = '<?= urlencode($_GET['encoder']); ?>';
		if (window.showModalDialog){  //for IE
			window.showModalDialog("patient_referral.php?encounter_nr="+encounter_nr+"&pid="+pid+"&encoder="+encoder+"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("patient_referral.php?&encounter_nr="+encounter_nr+"&pid="+pid+"&encoder="+encoder,"ConReferral","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}
	}
	
	//-------------------

function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,""); 
}/* end of function trimString */


	//--------------------	
</script>
</head>

<body>
<table width="467" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2" >
    <form method="POST">
        <input name="encounter_nr" type="hidden" value="<?= $encounter_nr ?>">
	<tr>
		<td colspan="*"><?= $errorMsg ?></td>
	</tr>
	<tr id="space4">
		<td><h2 style="text-align: center;width: 100%;">PATIENT REFERRAL FORM</h2></td>
	</tr>

	<tr id="">
		
		<td class="adm_item" width="40%">
			
			<div>
              
              
                <table width="40%" height="84" border="0" cellpadding="1" style="width:100%; font-size:12px">
                    <tr>
                        <td height="5" valign="middle">
                            
                            <input type="checkbox" name="is_emergency" <?= $patientRefInfo['is_emergency']== 1 ? 'checked' : ''; ?> value="1">
                            EMERGENCY
                        </td>
                        <td  height="5" valign="middle">
                            
                            <input type="checkbox" name="is_urgent" <?= $patientRefInfo['is_urgent']== 1 ? 'checked' : ''; ?> value="1">
                            URGENT
                        </td>
                        <td height="5" valign="middle">
                            
                            <input type="checkbox" name="is_routine" <?= $patientRefInfo['is_routine']== 1 ? 'checked' : ''; ?> value="1">
                            ROUTINE
                        </td>
                    </tr>
                   
                </table>
                <table width="40%" height="84" border="0" cellpadding="1" style="width:100%; font-size:12px">
                   
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;color:red;">DIAGNOSIS: </p>
                        </td>  
                        <td>:</td>
                        <td>
                            <textarea id="diagnosis" name="diagnosis" cols="70" rows="3" wrap="physical" required><?= $patientRefInfo['diagnosis']; ?></textarea>
                        </td>
                       
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;">REFERRAL TO: </p>
                        </td>  
                        <td>:</td>
                        <td>
                            <select id="referral_to" name="referral_to">
                                <option>Select Department</option>
                                <?php
                                    $rs = $dept_obj->getAllOPDMedicalObject(admit_patient);
                                    if($rs){
                                        while ($rowDept = $rs->fetchRow()) {
                                            $deptNr = $rowDept['nr'];
                                            $deptName = $rowDept['name_formal'];
                                            $is_selected = $patientRefInfo['referral_to'] == $deptNr ? 'selected' : '';
                                            echo "<option value='$deptNr' $is_selected>$deptName</option>";
                                        }
                                    }
                                ?>
                                
                            </select>
                        </td>
                    </tr>
                </table>
                <table width="40%" height="84" border="0" cellpadding="1" style="width:100%; font-size:12px">
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;">REASON(S) for REFERRAL: </p>
                        </td>  
                        <td>:</td>
                        <td>
                            <input type="checkbox" name="is_evaluation" <?= $patientRefInfo['is_evaluation']== "1" ? 'checked' : ''; ?> value="1">
                            EVALUATION
                        </td>
                        <td>
                            <input type="checkbox" name="is_comanage" <?= $patientRefInfo['is_comanage']== "1" ? 'checked' : ''; ?> value="1">
                            CO-MANAGE
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            
                        </td>  
                        <td></td>
                        <td>
                            <input type="checkbox" name="is_clearance" <?= $patientRefInfo['is_clearance']== "1" ? 'checked' : ''; ?> value="1">
                            CLEARANCE
                        </td>
                        <td>
                            <input type="checkbox" name="is_transferserv" <?= $patientRefInfo['is_transferserv']== "1" ? 'checked' : ''; ?> value="1">
                            TRANSFER SERVICE
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            
                        </td>  
                        <td></td>
                        <td height="5" valign="middle">
                            <input id="is_others" name="is_others" type="checkbox" <?= $patientRefInfo['others'] != '' ? 'checked' : ''; ?>>
                            OTHERS
                            <input id="others" name="others" type="text" value="<?= $patientRefInfo['others']; ?>">
                        </td> 
                      
                    </tr>
                </table>
                <table width="40%" height="84" border="0" cellpadding="1" style="width:100%; font-size:12px">
                    <td height="5" valign="middle">
                        <p style="width:150px;color:red;">CLINICAL FINDINGS: </p>
                    </td>  
                    <td>:</td>
                    <td>
                        <textarea id="clinical_findings" name="clinical_findings" cols="70" rows="3" wrap="physical" required><?= $patientRefInfo['clinical_findings']; ?></textarea>
                    </td>
                </table>
            </div>
			
		</td>
					
	</tr>
	<tr id="space2">
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
<?php

			if (empty($patientRefInfo['encounter_nr']) || !$patientRefInfo['encounter_nr']){
				
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
			
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printPatientReferral('.$encounter_nr.','.$pid.')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
?>
			&nbsp; &nbsp;
			<input type="button" name="Cancel" value="Cancel" onclick="javascript:window.parent.cClick();">
			<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
		</td>
	</tr>
	</form>
</table>

<script type="text/javascript">

    $(document).ready(function(){

        if($('#is_others').attr('checked')){

            $('#others').show();
        }else{
            $('#others').hide();
            
        }

        $('#is_others').change(function(e){
      
            $('#others').val("");

            if($('#others').is(":hidden")){
                
                $('#others').show();
            }else{
                $('#others').hide();
                
            }
            
        });
    });

</script>
</body>
</html>
