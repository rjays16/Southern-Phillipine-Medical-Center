<?php
/**
* @author Jeff Ponteras - May 16,2018
* @param encounter and necessary details
* @return Generation of IPBM Discharge slip
* User Interface for Computerized IPBM Discharge Slip
**/
// Required files...
include("roots.php");
require_once($root_path.'include/inc_environment_global.php');
include_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj = new Encounter();
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj=new Department;
require_once($root_path.'include/care_api_classes/class_personell.php');
$pers_obj=new Personell;

require_once("dashboard.common.php");
global $db;

if ($xajax) {
    $xajax->printJavascript('../../classes/xajax');

}
define('ADMIT_INPATIENT', 1);
define('NO_2LEVEL_CHK',1);
define(DateValue, '01/01/1970');

require($root_path.'include/inc_front_chain_lang.php');
?>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
<link rel="stylesheet" href="<?=$root_path?>js/jquery/css/jquery-ui.css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery.simplemodal.js"></script>
<script type='text/javascript' src="<?=$root_path?>modules/dashboard/js/ipbm-discharge-slip.js"></script>
<!-- End of includes and required files... -->

<!-- Process,functionalities,methods and logic of the page using php... -->
<?php
    function time_name($time){
            $times = date('g:i a',strtotime($time?$time:'0000'));
            return $times;
    }

    if(isset($_POST['submit'])){
        /* If empty basic algorithm */
                /* Home medications concatination */
                for($i=0;$i<14;$i++){
                    $counter = 0;
                    for($j=0;$j<4;$j++){
                        if($_POST['textf'][$i][$j] != '' && $j != 2){
                            $counter++;
                        }      
                    }   
                     if($counter>0){
                            $arrayvar[$i][0] = utf8_encode(trim($_POST['textf'][$i][0])); 
                            $arrayvar[$i][1] = utf8_encode(trim($_POST['textf'][$i][1])); 
                            $arrayvar[$i][2] = date('h:i a',strtotime($_POST['textf'][$i][2])); 
                            $arrayvar[$i][3] = utf8_encode(trim($_POST['textf'][$i][3])); 
                        }else{
                            $arrayvar[$i][0] = "";
                            $arrayvar[$i][1] = "";
                            $arrayvar[$i][2] = "";
                            $arrayvar[$i][3] = "";
                        }
                }
                $medications = json_encode($arrayvar, JSON_FORCE_OBJECT| JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP|JSON_UNESCAPED_UNICODE);
                /* Side effefcts concatination */
                $side_effects = $_POST['Check1'] .':'.
                                $_POST['Check2'] .':'.
                                $_POST['Check3'] .':'.
                                $_POST['Check4'] .':'.
                                $_POST['Check5'] .':'.
                                $_POST['Check6'] .':'.
                                $_POST['Check7'] .':'.
                                $_POST['Check8'] .':'.
                                $_POST['Check9'] .':'.
                                $_POST['Check10'] .':'.
                                $_POST['Check11'];

        /* Date and Time */
        $cu_date = str_replace('-', '/', $_POST['cu_day']);
        $date = str_replace('-', '/', $_POST['date_input']);

        $enc_obj->insertDischargeSlipInfoIpbm($_POST['encounter_nr'], 
                                              $_POST['hrn'],
                                              $cu_date,
                                              $_POST['cu_place'],
                                              $medications, 
                                              $_POST['injection'],
                                              $date,
                                              trim($_POST['notes']),
                                              $side_effects, 
                                              $_POST['dept_nr'], 
                                              $_POST['personnel_nr'], 
                                              $_POST['unit_nr'],
                                              $_POST['chkuptime'],
                                              $_POST['medtime']);
        
        echo "<div style='width: 100%; text-align: center;'><span class='hsuccess'>Patient <i>".$_POST['person_name']."</i> successfully saved!</span></div>";
    }
    /* Patient main info */
    $result = $enc_obj->getDischargeMainInfoIPBM($encounter_nr);
        while($row = $result->FetchRow()) {
                $hrn = $row['hrn'];
                $person_name = $row['person_name'];
                $age = $row['age'];
                $sex = $row['sex'];
                $homis_id = $row['homis_id'];
                $encounter_date = date('m/d/Y', strtotime($row['encounter_date']));
                $civil_status = $row['civil_status'];
        }
    /* Discharge Slip-Information */
    $result = $enc_obj->getDischargeSlipInfoIpbm($_GET['encounter_nr']);
    $getLatestImpression = $enc_obj->getPatientEncInfo($_GET['encounter_nr']);
    $address =  $getLatestImpression['street_name'] . '  ' . $getLatestImpression['brgy_name'] . ',' . $getLatestImpression['mun_name'] . ',  ' . $getLatestImpression['prov_name'];
    $fdiagnosis =  $getLatestImpression['final_diagnosis'];
    $admi = explode(' ', $getLatestImpression['admission_date']);
    $admission1 = $admi[0]. '  ' . date('h:i a',strtotime($admi[1]));

    if($getLatestImpression['discharge_time'] !=''){
        $discharge = $getLatestImpression['discharge_date']. '  ' .date('h:i a',strtotime($getLatestImpression['discharge_time']));
    }else{
        $discharge = $getLatestImpression['discharge_date'].'';
    }
    
    $locations = $getLatestImpression['location'];
    $dr_nr = $getLatestImpression['current_att_dr_nr'];

    $meds2 = json_decode($result -> fields[3],true);  

    if ($dr_nr){
    if ($doc_info = $pers_obj->getPersonellInfo($dr_nr)){

        $middleInitial = "";
        if (trim($doc_info['name_middle'])!=""){
            $thisMI=split(" ",$doc_info['name_middle']);
            foreach($thisMI as $value){
                if (!trim($value)=="")
                    $middleInitial .= $value[0];
            }
            if (trim($middleInitial)!="")
                $middleInitial = " ".$middleInitial.".";
        }
        $physician_name ="Dr. ".$doc_info['name_first']." ".$doc_info['name_2'].$middleInitial." ".$doc_info['name_last'];
    }
}  
    while ($row = $result->FetchRow()) {
        $medtime = $row['medtime'];
        $chkuptime = $row['chkuptime'];
        $notes = $row['notes'];
        $medications = $row['medications'];
        $side_effects = $row['side_effects'];
        $cu_day = $row['checkup_date'];
        $cu_place = $row['checkup_place'];
        $injection = $row['injection'];
        $sched_dt = $row['schedule'];
        $unit_nr = $row['unit_nr'];
        $checkup_dt = date('m/d/Y', strtotime($cu_day));
        $date_input = date('m/d/Y', strtotime($sched_dt));
    
        if ($row) {
            $department_selected = $row['dept_nr'];
            $physician_selected = $row['personnel_nr'];
        }
    }
    
    /* Array of inout fields fetching */

    /**** Added by @Ryan 08/01/18 ****/
    
   	/**** End in this line ****/

    /* Array of checkbox */
    $se = explode(':', $side_effects);
    if ($se[0] == 'on') { $Check1 = "checked"; }
    if ($se[1] == 'on') { $Check2 = "checked"; }
    if ($se[2] == 'on') { $Check3 = "checked"; }
    if ($se[3] == 'on') { $Check4 = "checked"; }
    if ($se[4] == 'on') { $Check5 = "checked"; }
    if ($se[5] == 'on') { $Check6 = "checked"; }
    if ($se[6] == 'on') { $Check7 = "checked"; }
    if ($se[7] == 'on') { $Check8 = "checked"; }
    if ($se[8] == 'on') { $Check9 = "checked"; }
    if ($se[9] == 'on') { $Check10 = "checked"; }
    if ($se[10] == 'on') { $Check11 = "checked"; }

    if (!$department_selected) {
        $dept_nr = $db->GetOne("SELECT location_nr FROM care_personell_assignment cpa LEFT JOIN care_personell cp ON cpa.personell_nr = cp.nr WHERE cp.short_id LIKE '%D%' AND personell_nr = (SELECT personell_nr FROM care_users WHERE login_id = '".$_SESSION['sess_temp_userid']."')");
        $personnell_nr = $db->GetOne("SELECT personell_nr FROM care_users WHERE login_id = '".$_SESSION['sess_temp_userid']."'");
    }
    else {
        $dept_nr = $department_selected;
    }

    echo $confCertInfo['attending_doctor'];

    $is_pending = $enc_obj->checkPendingRequests($_GET['encounter_nr']); 
   
    if ($is_pending) {
        $service_code = $row['service_code'];
        $service_code_lab = $row['service_code_lab'];

        echo  "<div class='center-discharge-slip'><span class='style4'>Patient has pending request you may wait until served or cancel the request: <br><i> ".$is_pending."</i></span></div>";
        $disabled = 'disabled';
    }
    else {
        $disabled = '';
    }    
?>
<!-- End of process,functionality,methods,logic of the page using php... -->

<!-- User Interface... -->
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$root_path?>css/bootstrap/bootstrap1.css">
</head>
<body>
    <div class="container-fluid">
        <br>
        <div class="panel panel-success">
            <div class="row">
                <div class="col-sm-12">
                    <span style="text-align: center;"><h4>Patient Information</h4></span>
                </div>
            </div>
        </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">Case No : </label> <? echo $encounter_nr; ?>
                    </div>
                    <div class="col-sm-3">
                        <label class="hfonter">HRN :</label> <? echo $hrn; ?>
                    </div>
                    <div class="col-sm-3" background="<?=$root_path?>img/ipbm.png">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">Pasyente :</label> <? echo strtoupper($person_name); ?>
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Edad :</label> <? echo $age; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">Status :</label> <? echo strtoupper($civil_status); ?>
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Sex :</label> <? echo strtoupper($sex); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">HOMIS Number:</label>&nbsp;<?php echo $homis_id;?>
                        <br>
                        
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Ward :</label>&nbsp;<span style="font-size: 14px;"><? echo strtoupper($locations); ?></span>  
                    </div>
                </div>

                <form action="ipbm-discharge-slip-ipd-new.php?encounter_nr=<?php echo $encounter_nr; ?>" method="post">
                <div class="row">
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">Date of Admission :</label><span style="font-size: 14px;"> <? echo $admission1; ?></span>
                        <!-- <br>
                        <input <?php echo $disabled ?> type="text" class="form-control" name="cu_day" id="cu_day" value="<?php echo $addate;?>"> -->
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Date of Discharged :</label><span style="font-size: 14px;"> <? echo $discharge; ?></span>
                    </div>
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">Attending Physician :</label>&nbsp;<span style="font-size: 14px;"><? echo $physician_name; ?></span>
                        
                    </div>
                    
                    <br>
                    <div class="col-sm-5">
                        <label class="hfonter">Address :</label> <span style="font-size: 14px;"><? echo strtoupper($address); ?></span>    
                    </div>
                    <div class="col-sm-5 col-sm-offset-1">
                        <label class="hfonter">Final Diagnosis :</label>
                        
                        <span style="font:sans-seriff;font-size: 14px;"><? echo strtoupper($fdiagnosis); ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Operation/Procedure Done :</label>    
                    </div>

                </div>
                
                <hr>
                <label class="hfonter">HOME MEDICATIONS:</label>
                <br>
                <div class="row">
                <!-- Array of Inputs -->
                <?php 

                for($i=0;$i<14;$i++){ 
                    for($j=0;$j<4;$j++){
                        echo "<div class='col-sm-3'>";
                        if($i==0 && $j==0){
                            echo "<label class='hfonter'>MEDICINE:</label>";
                        }elseif($i==0 && $j==1){
                            echo "<label class='hfonter'>DOSAGE:</label>";
                        }elseif($i==0 && $j==2){
                            echo "<label class='hfonter'>TIME:</label>";
                        }elseif($i==0 && $j==3){
                            echo "<label class='hfonter'>REMARKS:</label>";
                        }
                        if($j==2){
                            if($meds2[$i][$j]!='')
                                $timez = date("H:i",strtotime(time_name($meds2[$i][$j])));
                            else
                                $timez = '';
                                echo "<input type='time' class='form-control' name='textf[$i][$j]' value='$timez'/>";  
                        }else{
                            if($j==1){
                                echo "<input  type='text' maxlength='40' class='form-control' name='textf[$i][$j]' value='".stripslashes(htmlspecialchars($meds2[$i][$j], ENT_QUOTES))."'/>";
                        }else{
                                echo "<input  type='text' maxlength='65' class='form-control' name='textf[$i][$j]' value='".stripslashes(htmlspecialchars($meds2[$i][$j], ENT_QUOTES))."'/>";
                             }
                        }
                        echo "</div>";
                    }
                    if($i==6){
                             echo "<label class='hfonter'>Depot Injection:</label>";
                    }
                    echo "<br>";
                }                 
                ?>
                    
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="hfonter">Special Instructions :</label>
                        <br>
                        <textarea class="hTextArea" maxlength="498" <?php echo $disabled ?> cols="100%" rows="3%" name="notes" id="myTextArea"><?php echo stripslashes(trim($notes)); ?></textarea>
                        &nbsp;&nbsp;&nbsp;&nbsp;<br><font size=1>[Click on the input box]</font>&nbsp;&nbsp;
                        <span id="characters" style="color:#999;">498</span> <span style="color:#999;">left</span>
                    </div>
                   
                </div>
                <hr>              
                <div class="row">
                    <div class="col-sm-3 col-sm-offset-2">
                        <label class="hfonter">FOLLOW-UP CHECKUP:</label>
                        <br>
                        &nbsp;<label class="hfonter">When:</label>
                        <input <?php echo $disabled ?> type="text" class="form-control" id="date_input" name="date_input" value="<?php echo $date_input == DateValue ? '' : $date_input; ?>" >&nbsp;
                        <font size=1>[Click on the input box]</font>
                        <br>
                        &nbsp;<label class="hfonter">Where:</label>
                        <input <?php echo $disabled ?> type="text" class="form-control" name="medtime" value="<?php echo trim($medtime);?>">
                        <br>
                        &nbsp;<label class="hfonter">Time:</label>
                        <input <?php echo $disabled ?> type="time" class="form-control" name="chkuptime" 
                        value="<?php echo date("H:i", strtotime(time_name($chkuptime)));?>">
                        <br>
                        &nbsp;<label class="hfonter">What to Bring:</label><br>
                        <label class="hfonter">* Clinical Summary</label><br>
                        <label class="hfonter">* Yellow Card</label>
                          
                    </div>
                    <div class="col-sm-6 col-sm-offset-1">
                            <div class="form-check">
                                <label class="hfonter">CLASSIFICATION:</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check8" name="Check8" <?=$Check8?>>
                                <label class="form-check-label" for="Check8">PAY</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check9" name="Check9" <?=$Check9?>>
                                <label class="form-check-label" for="Check9">SERVICE</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check10" name="Check10" <?=$Check10?>>
                                <label class="form-check-label" for="Check10">PHIC</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check11" name="Check11" <?=$Check11?>>
                                <label class="form-check-label" for="Check11">NON PHIC</label>
                            </div>
                        </div>
                </div>

                <script>
                    $('textarea').keyup(updateCount);
                    $('textarea').keydown(updateCount);
                    $('textarea').on('notes', updateCount); 
                    
                    var textarea = $('#myTextArea');

                        textarea.addEventListener("keydown", function (e) {
                            if (e.keyCode == 13) { // keyCode 13 corresponds to the Enter key
                                e.preventDefault(); // prevents inserting linebreak
                            }
                        });

                    function updateCount() {
                        var cs = [ 498 - $(this).val().length];
                        $('#characters').text(cs);
                        
                    }
               </script>

                <div class="row">
                    </form>
                    <div class="row">
                        <div class="col-sm-12">
                            <?php 
                            if ($disabled) {
                                echo "<div class='center-discharge-slip'><span class='style4'>Patient has pending request you may wait until served or cancel the request: <br><i> ".$is_pending."</i></span></div>";
                            }
                            else {
                                echo '<hr><center><input class="hSubmit" type="submit" name="submit" value="Save">';
                                echo '&nbsp&nbsp<input class="hPrint" type="button" value="Print" onclick="printDischargeSlipIPD('.$encounter_nr.');"></center><hr>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php 
                            echo '<input type="hidden" name="encounter_nr" value="'.$encounter_nr.'">';
                            echo '<input type="hidden" name="hrn" value="'.$hrn.'">';
                            echo '<input type="hidden" name="person_name" value="'.$person_name.'">';
                            echo '<input type="hidden" name="age" value="'.$age.'">';
                            echo '<input type="hidden" name="sex" value="'.$sex.'">';
                            echo '<input type="hidden" name="civil_status" value="'.$civil_status.'">';
                            echo '<input type="hidden" name="dept_nr" value="'.$dept_nr.'">';
                            echo '<input type="hidden" name="personnel_nr" value="'.$personnell_nr.'">';
                    ?>
                </div>
        </div>
    </div>   
</body>
</html>
<!-- End of User Interface... -->
