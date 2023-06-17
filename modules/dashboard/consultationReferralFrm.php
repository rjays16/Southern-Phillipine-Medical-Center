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

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;

if (isset($_GET['encounter_nr']) && $_GET['encounter_nr']){
	$encounter_nr = $_GET['encounter_nr'];
}

if (isset($_GET['pid']) && $_GET['pid']){
    $pid = $_GET['pid'];
}

if (isset($_POST['routine']) && $_POST['routine']){
	$HTTP_POST_VARS['routine'] = $_POST['routine'];
}

if (isset($_POST['encounter_nr']) && $_POST['encounter_nr']){
    $encounter_nr = $_POST['encounter_nr'];
}

if (isset($_GET['encoder']) && $_GET['encoder']){
    $personell_title = $pers_obj->getPersonellTitle($_SESSION['sess_login_personell_nr']);
    $encoder = urldecode($_GET['encoder']) . ", " . $personell_title;
}



include_once($root_path.'include/care_api_classes/class_consultation_referral.php');
$obj_conRef = new ConsultationReferral($encounter_nr);

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
    $HTTP_POST_VARS['DATE__'] = date($dbtime_format,strtotime($HTTP_POST_VARS['DATE__']));

    switch($_POST['mode']) {
        case 'save':
            
            $HTTP_POST_VARS['history'] = "Create ".date('Y-m-d H:i:s')." ".$encoder." \n";
            $HTTP_POST_VARS['create_id']=$encoder;
            $HTTP_POST_VARS['create_dt']=date('Y-m-d H:i:s');
            $input = checkInput($HTTP_POST_VARS,$obj_conRef->fld_seg_consultation_referral);
            if ($obj_conRef->saveConReferralInfoFromArray($input)){
                
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Saved sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_conRef->getErrorMsg().'</font>';            
            }
        break;
        case 'update':

            $HTTP_POST_VARS['history'] = "Update ".date('Y-m-d H:i:s')." ".$encoder." \n";
            $HTTP_POST_VARS['modify_id']=$encoder;
            $HTTP_POST_VARS['modify_dt']=date('Y-m-d H:i:s');
           
            $input = checkInput($HTTP_POST_VARS,$obj_conRef->fld_seg_consultation_referral);
            if ($obj_conRef->updateConReferralInfoFromArray($input)){
               
                $errorMsg='<font style="color:#FF0000;font-style:italic">'."Updated sucessfully!".'</font>';
            }else{
                $errorMsg='<font style="color:#FF0000">'.$obj_conRef->getErrorMsg().'</font>';            
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

    $conRefInfo = $obj_conRef->getConReferral($encounter_nr);

    if(!$conRefInfo){

        $conRefInfo = $obj_conRef->fld_seg_consultation_referral;
        
        // $conRefInfoField = $obj_conRef->fld_seg_consultation_referral;
        // $conRefInfo = array();
        // foreach ($conRefInfoField as $key => $value) {
        //     $conRefInfo[$value] = "";
        // }

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

	function printConReferral(encounter_nr, pid){

		if (window.showModalDialog){  //for IE
			window.showModalDialog("con_referral.php?encounter_nr="+encounter_nr+"&pid="+pid+"width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
		}else{
			window.open("con_referral.php?&encounter_nr="+encounter_nr+"&pid="+pid,"ConReferral","modal, width=600,height=400,menubar=no,resizable=yes,scrollbars=no");
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

<body onLoad="preset();">
<table width="467" height="236" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#BAD0FC" class="style2">
    <form method="POST">
        <input name="encounter_nr" type="hidden" value="<?= $encounter_nr ?>">
	<tr>
		<td colspan="*"><?= $errorMsg ?></td>
	</tr>
	<tr id="space4">
		<td><h2 style="text-align: center;width: 100%;">CONSULTATION AND REFERRAL SHEET</h2></td>
	</tr>

	<tr id="">
		
		<td class="adm_item" width="40%">
			
			<div>
                <table width="40%" height="84" border="0" cellpadding="1" style="width:100%; font-size:12px">
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;">Date</p>
                        </td>  
                        <td>:</td>
                        <td>
                            <span id="show_date" class="segInput" style="font-weight:bold; color:#000080; padding:0px 2px;width:200px; height:24px"><?php echo (isset($_POST['Submit']) && $_POST['Submit'] ? date($fulltime_format,strtotime($_POST['DATE__'])) : date($fulltime_format)); ?></span><input class="segInput" name="DATE__" id="DATE__" type="hidden" value="<?php echo (isset($_POST['Submit']) && $_POST['Submit'] ? date($dbtime_format,strtotime($_POST['DATE__'])) : date($dbtime_format)); ?>" style="font:bold 12px Arial">
                            <img src="<?= $root_path?>gui/img/common/default/show-calendar.gif" id="date_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:2px;cursor:pointer">

                        </td>
                      
                    </tr>
                   
                    <tr>
                        <td width="2%" height="5" valign="middle">
                        <input name="is_emergency" type="checkbox" <?= $conRefInfo['is_emergency'] ? 'checked' : ''; ?> value="1">
                        </td>
                        <td>:</td>
                        <td><p>EMERGENCY</p></td>   
                    </tr>
                    <tr>
                        <td width="2%" height="5" valign="middle">
                        <input name="is_routine" type="checkbox" <?= $conRefInfo['is_routine'] ? 'checked' : ''; ?> value="1">
                        </td>
                        <td>:</td>
                        <td><p>ROUTINE</p></td>   
                    </tr>
                   
                </table>
                <table width="40%" height="84" border="0" cellpadding="1" style="width:100%; font-size:12px">
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;">TO DR./AGENCY </p>
                        </td>  
                        <td>:</td>
                        <td>
                            <select id="agency_to" name="agency_to">
                                <option>Select Department</option>
                                <?php
                                    $rs = $dept_obj->getAllOPDMedicalObject(admit_patient);
                                    if($rs){
                                        while ($rowDept = $rs->fetchRow()) {
                                            $deptNr = $rowDept['nr'];
                                            $deptName = $rowDept['name_formal'];
                                            $is_selected = $conRefInfo['agency_to'] == $deptNr ? 'selected' : '';
                                            echo "<option value='$deptNr' $is_selected>$deptName</option>";
                                        }
                                        $is_selected = ($conRefInfo['others'] != '' || !empty($conRefInfo['others'])) ? 'selected' : '';
                                        echo "<option value='Others' $is_selected>Other</option>";
                                    }
                                ?>
                                
                            </select>
                            <input id="others" name="others" type="text" value="<?= $conRefInfo['others']; ?>">
                        </td>
                       
                    </tr>
                    <tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;">FROM DR./AGENCY </p>
                        </td>  
                        <td>:</td>
                        <td>
                            <input name="agency_from" cols="70" rows="3" readonly="readonly" wrap="physical" value="<?= $encoder; ?>">
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;color:red;">BRIEF HISTORY AND PERTINENT PHYSICAL FINDINGS </p>
                        </td>  
                        <td>:</td>
                        <td>
                            <textarea name="brief_hist" cols="70" rows="3" wrap="physical" required><?= $conRefInfo['brief_hist']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;color:red;">WORK UP ALREADY DONE</p>
                        </td>  
                        <td>:</td>
                        <td>
                            <textarea name="work_up" cols="70" rows="3" wrap="physical" required><?= $conRefInfo['work_up']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;color:red;">IMPRESSION</p>
                        </td>  
                        <td>:</td>
                        <td>
                            <textarea name="impression" cols="70" rows="3" wrap="physical" required><?= $conRefInfo['impression']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;color:red;">REASON FOR REFERRAL</p>
                        </td>  
                        <td>:</td>
                        <td>
                            <textarea name="reason_referral" cols="70" rows="3" wrap="physical" required><?= $conRefInfo['reason_referral']; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="5" valign="middle">
                            <p style="width:150px;">CONSULTATION NOTE AND SUGGESTION / RECEIVING/DR. / AGENCY REMARKS</p>
                        </td>  
                        <td>:</td>
                        <td>
                            <textarea name="agency_remarks" cols="70" rows="3" wrap="physical"><?= $conRefInfo['agency_remarks']; ?></textarea>
                        </td>
                    </tr>
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

			if (empty($conRefInfo['encounter_nr']) || !$conRefInfo['encounter_nr']){
				
				echo '			<input type="hidden" name="mode" id="mode" value="save">'."\n";
				echo '			<input type="submit" name="Submit" value="Save">'."\n";
			}else{
			
				echo '			<input type="hidden" name="mode" id="mode" value="update">'."\n";
				echo '			<input type="button" name="Print" value="Print" onClick="printConReferral('.$encounter_nr.','.$pid.')">'."\n &nbsp; &nbsp;";
				echo '			<input type="submit" name="Submit" value="Update">'."\n";
			}
			echo '			<input type="hidden" name="encounter_nr" id="encounter_nr" value="'.$encounter_nr.'">'."\n";
?>
			&nbsp; &nbsp;
			<input type="button" name="Cancel" value="Cancel"  onclick="javascript:window.parent.cClick();">
			<input type="hidden" name="pid" id="pid" value="<?=$encInfo['pid']?>">
		</td>
	</tr>
	</form>
</table>

<script type="text/javascript">

    $(document).ready(function(){
        
        if($('#agency_to').val() == "Others"){
            $('#others').show();
        }else{
            $('#others').hide();
        }
    
        $('#agency_to').change(function(e){
           
            if($('#agency_to').val() == "Others"){
                
                $('#others').show();
            }else{
                $('#others').hide();
            }
            
            $('#others').val("");
           
        });
    });

     Calendar.setup ({
        displayArea : "show_date",
        inputField : "DATE__",
        ifFormat : "%Y-%m-%d %H:%M:%S",
        daFormat : "   %B %e, %Y %I:%M%P",
        showsTime : true,
        button : "date_trigger",
        singleClick : true,
        step : 1
    });



</script>
</body>
</html>
