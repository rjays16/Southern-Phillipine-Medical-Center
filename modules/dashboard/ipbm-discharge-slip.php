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
require_once($root_path.'include/care_api_classes/class_encounter.php');
require_once($root_path.'include/care_api_classes/class_department.php');
require_once($root_path.'include/care_api_classes/class_ward.php');
require_once($root_path.'include/care_api_classes/class_personell.php');
           
require_once("dashboard.common.php");
global $db; 

if ($xajax) {
    $xajax->printJavascript('../../classes/xajax');

}
define('ADMIT_INPATIENT', 1);
define('NO_2LEVEL_CHK',1);
define(DateValue, '01/01/1970');

require($root_path.'include/inc_front_chain_lang.php');

$enc_obj = new Encounter();
$dept_obj=new Department;

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

    if(isset($_POST['submit'])){

        /* If empty basic algorithm */
        for($i=0;$i<5;$i++){
                    $counter = 0;
                    for($j=0;$j<4;$j++){
                        if($_POST['textf'][$i][$j] != ''){
                            $counter++;
                        }
                       
                        
                    }   
                     if($counter>0){
                            $arrayvar[$i][0] = utf8_encode(trim($_POST['textf'][$i][0])); 
                            $arrayvar[$i][1] = utf8_encode(trim($_POST['textf'][$i][1])); 
                            $arrayvar[$i][2] = utf8_encode(trim($_POST['textf'][$i][2])); 
                            $arrayvar[$i][3] = utf8_encode(trim($_POST['textf'][$i][3])); 
                        }else{
                            $arrayvar[$i][0] = "";
                            $arrayvar[$i][1] = "";
                            $arrayvar[$i][2] = "";
                            $arrayvar[$i][3] = "";
                        }
                }

                $medications = json_encode($arrayvar, JSON_FORCE_OBJECT| JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP|JSON_UNESCAPED_UNICODE);

                /* Home medications concatination */
                 // $medications = $_POST['hm11'] .':'.
                 //                $_POST['hm12'] .':'.
                 //                $_POST['hm13'] .':'.
                 //                $_POST['hm14'] .':'.
                 //                $_POST['hm21'] .':'.
                 //                $_POST['hm22'] .':'.
                 //                $_POST['hm23'] .':'.
                 //                $_POST['hm24'] .':'.
                 //                $_POST['hm31'] .':'.
                 //                $_POST['hm32'] .':'.
                 //                $_POST['hm33'] .':'.
                 //                $_POST['hm34'] .':'.
                 //                $_POST['hm41'] .':'.
                 //                $_POST['hm42'] .':'.
                 //                $_POST['hm43'] .':'.
                 //                $_POST['hm44'] .':'.
                 //                $_POST['hm51'] .':'.
                 //                $_POST['hm52'] .':'.
                 //                $_POST['hm53'] .':'.
                 //                $_POST['hm54'];
                /* Side effefcts concatination */
                $side_effects = $_POST['Check1'] .':'.
                                $_POST['Check2'] .':'.
                                $_POST['Check3'] .':'.
                                $_POST['Check4'] .':'.
                                $_POST['Check5'] .':'.
                                $_POST['Check6'] .':'.
                                $_POST['Check7'] ;

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
                                              $_POST['unit_nr']);

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
    $meds2 = json_decode($result -> fields[3],true);

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
   /* $hm11 = $meds[0];
    $hm12 = $meds[1]; 
    $hm13 = $meds[2]; 
    $hm14 = $meds[3];
    $hm21 = $meds[4]; 
    $hm22 = $meds[5]; 
    $hm23 = $meds[6]; 
    $hm24 = $meds[7];
    $hm31 = $meds[8]; 
    $hm32 = $meds[9]; 
    $hm33 = $meds[10]; 
    $hm34 = $meds[11];
    $hm41 = $meds[12]; 
    $hm42 = $meds[13]; 
    $hm43 = $meds[14]; 
    $hm44 = $meds[15];
    $hm51 = $meds[16]; 
    $hm52 = $meds[17]; 
    $hm53 = $meds[18]; 
    $hm54 = $meds[19];*/

    /**** Added by @Ryan 08/01/18 ****/
    // $meds = explode(':', $medications);
   	//    list( 
   	//    		$list[0],$list[1],$list[2],$list[3],$list[4],$list[5],
   	// 		$list[6],$list[7],$list[8],$list[9],$list[10],$list[11],
   	// 		$list[12],$list[13],$list[14],$list[15],$list[16],
   	// 		$list[17],$list[18],$list[19] 
   	// 		) = $meds;
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
                <hr>
                <form action="ipbm-discharge-slip.php?encounter_nr=<?php echo $encounter_nr; ?>" method="post">
                <div class="row">
                    <div class="col-sm-6">
                        <label class="hfonter">Adlaw sa Check-up :</label>
                        <br>
                        <input <?php echo $disabled ?> type="text" class="form-control" name="cu_day" id="cu_day" value="<?php echo $encounter_date;?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Lugar sa Check-up :</label>
                        <br>
                        <input <?php echo $disabled ?> type="text" readonly class="form-control" name="cu_place" id="cu_place" value="IPBM">
                    </div>
                </div>
                <hr>
                <div class="row">

                    <?php 

                        for($i=0;$i<5;$i++){ 
                            for($j=0;$j<4;$j++) {

                            echo "<div class='col-sm-3'>";
                                if($i==0 && $j==0){
                                    echo "<label class='hfonter'>Tambal sa Balay:</label>";
                                }elseif($i==0 && $j==1){
                                    echo "<label class='hfonter'>Buntag:</label>";
                                }elseif($i==0 && $j==2){
                                    echo "<label class='hfonter'>Udto:</label>";
                                }elseif($i==0 && $j==3){
                                    echo "<label class='hfonter'>Gabii:</label>";
                                }
                                if($j==1){
                                    echo "<input  type='text' maxlength='40' class='form-control' name='textf[$i][$j]' value='".stripslashes(htmlspecialchars($meds2[$i][$j],ENT_QUOTES))."'/>";
                                }else{
                                    echo "<input  type='text' maxlength='65' class='form-control' name='textf[$i][$j]' value='".stripslashes(htmlspecialchars($meds2[$i][$j],ENT_QUOTES))."'/>";
                                }
                            echo "</div>";
                                }
                        }
                    ?>

                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="hfonter">Injection :</label>
                        <br>
                        <input <?php echo $disabled ?> type="text" class="form-control" name="injection" value="<?php echo $injection;?>">
                    </div>
                    <div class="col-sm-6">
                        <label class="hfonter">Schedule :</label>
                        <br>
                        <input <?php echo $disabled ?> type="text" class="form-control" id="date_input" name="date_input" value="<?php echo $date_input == DateValue ? '' : $date_input; ?>" >&nbsp;
                        <font size=1>[Click on the input box]</font>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="hfonter">HOMIS Number:</label>
                        <br>
                        <input <?php echo $disabled ?> type="text" readonly class="form-control" name="unit_nr" value="<?php echo $homis_id;?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="hfonter">Notes:</label>
                        <br>
                        <center>
                            <textarea class="hTextArea" <?php echo $disabled ?> cols="100%" rows="3%" name="notes"><?php echo stripslashes(trim($notes)); ?></textarea>
                        </center>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-11 col-sm-offset-1">
                        <label class="hfonter"><h4>Side Effects :</h4></label>
                        <br>
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="Check1" name="Check1" <?=$Check1?>>
                                <label class="form-check-label" for="Check1">Robot-Robot</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check2" name="Check2" <?=$Check2?>>
                                <label class="form-check-label" for="Check2">Pag laway-laway</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check3" name="Check3" <?=$Check3?>>
                                <label class="form-check-label" for="Check3">Pag layag sa regla</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check4" name="Check4" <?=$Check4?>>
                                <label class="form-check-label" for="Check4">Pag bag-o sa gana sa pakighilawas</label>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="Check5" name="Check5" <?=$Check5?>>
                                <label class="form-check-label" for="Check5">Dili mahimutang</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check6" name="Check6" <?=$Check6?>>
                                <label class="form-check-label" for="Check6">Pag bug-at sa timbang</label>
                                <br>
                                <input type="checkbox" class="form-check-input" id="Check7" name="Check7" <?=$Check7?>>
                                <label class="form-check-label" for="Check7">Sobra sa gana sa pagkaon</label>
                            </div>
                        </div>
                    </div>
                </div>
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
                                echo '&nbsp&nbsp<input class="hPrint" type="button" value="Print" onclick="printDischargeSlip('.$encounter_nr.');"></center><hr>';
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
