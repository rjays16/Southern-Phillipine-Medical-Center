<?php
/**
* Created by EJ 11/26/2014
* User Interface for Computerized Discharge Slip
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

define('ADMIT_INPATIENT', 1);
define('NO_2LEVEL_CHK',1);
require($root_path.'include/inc_front_chain_lang.php');
?>

<html>
<head>
    <script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
    <script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type='text/javascript' src="<?=$root_path?>js/jquery/jquery.simplemodal.js"></script>
    <link rel="stylesheet" href="<?=$root_path?>js/jquery/css/jquery-ui.css" />
    <style type="text/css">
    body {margin-left: 0px;margin-top: 0px;margin-right: 0px;margin-bottom: 0px;background-color: #F8F9FA;}
    .style2 {font-family: Geneva, Arial, Helvetica, sans-serif;font-size: 12px;font-weight: bold;}
    .style3 {font-family: Geneva, Arial, Helvetica, sans-serif;font-size: 12px;font-weight: normal; margin: 0 auto;}
    .style4 {font-family: Geneva, Arial, Helvetica, sans-serif;font-size: 14px;font-weight: bold; color: red;}
    .style5 {font-family: Geneva, Arial, Helvetica, sans-serif;font-size: 14px;font-weight: bold; color: red; padding-left: 90px;}
    .space180 {padding-right: 180px;}
    .space40 {padding-right: 40px;}

    /* Added by Robert 05/04/2015 */
    .center-discharge-slip {width: 100%; text-align: center;}
    /* End Add by Robert 05/04/2015 */

    </style>
    <script type="text/javascript">

    jQuery(function($) {
        $( "#date_follow_up_input" ).datepicker({
            dateFormat: "mm-dd-yy",
            changeMonth: true,
            changeYear: true
        });
        $( "#date_input" ).datepicker({
            dateFormat: "mm-dd-yy",
            changeMonth: true,
            changeYear: true
        });

        if(document.getElementById('select_department').value != '' && document.getElementById("select_physician").value == ''){
            getDoctors();
        }
    });

    function printDischargeSlip(encounter_nr){
        if (window.showModalDialog){  
            window.showModalDialog("discharge-slip-pdf.php?encounter_nr="+encounter_nr+"");
        }else{
            window.open("discharge-slip-pdf.php?encounter_nr="+encounter_nr,"modal, width=600,height=1000,menubar=no,resizable=yes,scrollbars=no");
        }
    }

    function getDoctors() {
        var dept_nr = document.getElementById('select_department').value;
        var dept_nr = dept_nr.split(",");

        var element = document.getElementById("select_physician");
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
        
        xajax_setDoctors(dept_nr[0]);
    }

    function getDepartments() {
        var doc_nr = document.getElementById('select_physician').value;

        xajax_setDepartments(doc_nr);
    }

    var js_time = "";
    function js_setTime(jstime){
        js_time = jstime;
    }

    function js_getTime(){
        return js_time;
    }

    function validateTime(S) {
        return /^([01]?[0-9])(:[0-5][0-9])?$/.test(S);
    }

    function checkDate(input_date,element) {

        var date = new Date();
        var month = date.getMonth()+1;
        var day = date.getDate();
        var year = date.getFullYear();
        if(day<10) {
            day='0'+day;
        } 

        if(month<10) {
            month='0'+month
        }

        var date_now = month+"-"+day+"-"+year;
        var check_date = input_date.value;

        validateDate(check_date,date_now,element);
    }

    function validateDate(date,valid_date,element) {
       
        var date_format = /^(\d{1,2})(\/|-)(\d{1,2})\2(\d{4})$/;
        var msg = "Date is not in a valid format.";

        var matchArray = date.match(date_format); 

        if (matchArray == null) {
            if (date == '') {
                document.getElementById('date_follow_up_input').value = '';
            }
            else {
                alert(msg);
                document.getElementById(element).value = valid_date;
            }
        }

        month = matchArray[1]; 
        day = matchArray[3];
        year = matchArray[4];


        if (month < 1 || month > 12) { 
            alert(msg);
            document.getElementById(element).value = valid_date;
        }

        if (day < 1 || day > 31) {
            alert(msg);
            document.getElementById(element).value = valid_date;
        }

        if ((month==4 || month==6 || month==9 || month==11) && day==31) {
            alert(msg);
            document.getElementById(element).value = valid_date;
        }

        if (month == 2) {
            var isleap = (year % 4 == 0 && (year % 100 != 0 || year % 400 == 0));
            if (day>29 || (day==29 && !isleap)) {
                alert(msg);
                document.getElementById(element).value = valid_date;
            }
        }

        if (day.charAt(0) == '0') {
            day = day.charAt(1);
        }

    }

    function compareDate(date) {
        if (date) {
            var follow_up_date = date.value;
        }   
        else {
            var follow_up_date = document.getElementById('date_follow_up_input').value;
        }
       
        var discharge_date = document.getElementById('date_input').value;

        if (follow_up_date<discharge_date) {
            alert("Follow up date is later than Discharge date!");
            document.getElementById('date_follow_up_input').value = discharge_date; 
        };
    }

    function setFormatTime(thisTime,AMPM){
        var stime = thisTime.value;
        var hour, minute;
        var ftime ="";
        var f1 = /^[01]?[0-9]\:[0-5][0-9]$/;
        var f2 = /^[0-9]\:[0-5][0-9]$/;
        var jtime = "";

        if (thisTime.value==''){
            seg_validTime=false;
            return;
        }

        stime = stime.replace(':', '');

        if (stime.length == 3){
            hour = stime.substring(0,1);
            minute = stime.substring(1,3);
        } else if (stime.length == 4){
            hour = stime.substring(0,2);
            minute = stime.substring(2,4);
        }else{
            alert("Invalid time format.");
            thisTime.value = "";
            seg_validTime=false;
            thisTime.focus();
            return;
        }

        jtime = hour + ":" + minute;
        js_setTime(jtime);

        if (hour==0){
            hour = 12;
            document.getElementById(AMPM).value = "AM";
        }else   if((hour > 12)&&(hour < 24)){
            hour -= 12;
            document.getElementById(AMPM).value = "PM";
        }

        ftime =  hour + ":" + minute;

        if(!ftime.match(f1) && !ftime.match(f2)){
            thisTime.value = "";
            alert("Invalid time format.");
            seg_validTime=false;
            thisTime.focus();
        }else{
            thisTime.value = ftime;
            seg_validTime=true;
        }
    }

    function setDoctors(name, personell_nr) {
        var values = document.createElement("option");
        values.setAttribute("value", personell_nr);

        var option = document.createTextNode(name.toUpperCase());
        values.appendChild(option);

        document.getElementById("select_physician").appendChild(values);
    }

    function setDepartments(name, location_nr) {

        var values = document.createElement("option");
        values.setAttribute("value", location_nr);

        var option = document.createTextNode(name.toUpperCase());
        values.appendChild(option);

        document.getElementById("select_department").appendChild(values);
    }

    </script>
</head>

<?php
if(isset($_POST['submit'])){
    $time =  $_POST['time_input']." ".$_POST['selAMPM2'];
    $date_follow_up = str_replace('-', '/', $_POST['date_follow_up_input']);
    $date = str_replace('-', '/', $_POST['date_input']);

    $enc_obj->insertDischargeSlipInfo($_POST['encounter_nr'], 
        $_POST['hrn'], 
        $_POST['diagnosis'], 
        $_POST['home_medications'], 
        $date_follow_up, 
        $_POST['er_nod'], 
        $_POST['department'], 
        $date, 
        $time, 
        $_POST['physician']);

    // Added by Gervie 04-14-2017
    $data = array(
            'encounter_nr' => $_POST['encounter_nr'],
            'clinicalInfo' => $_POST['diagnosis'],
            'location' => 'DD'
        );

    $enc_obj->updateClinicalImpression($data);
    $enc_obj->saveToClinicalImpressionTable($data);
    // End 

    echo "<div style='width: 100%; text-align: center;'><span class='style4'>Patient <i>".$_POST['person_name']."</i> successfully saved!</span></div>";
}

$result = $enc_obj->getDischargeMainInfo($encounter_nr);
while($row = $result->FetchRow()) {
    $hrn = $row['hrn'];
    $person_name = $row['person_name'];
    $age = $row['age'];
    $sex = $row['sex'];
    $civil_status = $row['civil_status'];
    $date_input = date('m-d-Y');
    $time_input = date('g:i');
    if (date('A') == 'AM') {
        $selected_am = "selected";
    }
    else if (date('A') == 'PM') {
        $selected_pm = "selected";
    }
}

$result = $enc_obj->getDischargeSlipInfo($_GET['encounter_nr']);

$getLatestImpression = $enc_obj->getPatientEncInfo($_GET['encounter_nr']);

// added by: syboy 07/11/2015
$date_advance = date('m-d-Y', strtotime("+7 days"));
$date_follow_up_input = $date_advance;
// end
while ($row = $result->FetchRow()) {
    // added by: syboy
    $diagnosis = $row['diagnosis'];
    $medications = $row['medications'];  

    if ($row['follow_up_date'] == '1970-01-01' || $row['follow_up_date'] == '0000-00-00') {
         $date_follow_up_input = '';
        
    } elseif ($row['follow_up_date'] == '') {

        $date_follow_up_input = $date_advance;
        
    } else {

        $date_follow_up_input = date('m-d-Y', strtotime($row['follow_up_date']));
        // $date_follow_up_input = $date_advance;

    }
    // end
    
    $er_nod = $row['er_nod'];
    $date_input = date('m-d-Y', strtotime($row['discharge_date']));

    $time = date('h:i A', strtotime($row['discharge_time']));
    $time = explode(' ', $time);
    $time_input = $time[0];
    if ($time[1] == 'AM') {
        $selected_am = "selected";
    }
    else if ($time[1] == 'PM') {
        $selected_pm = "selected";
    }

    if ($row) {
        $department_selected = $row['dept_nr'];
        $physician_selected = $row['personnel_nr'];
    }

}

if (!$department_selected) {
    
    $dept_nr = $db->GetOne("SELECT location_nr FROM care_personell_assignment cpa LEFT JOIN care_personell cp ON cpa.personell_nr = cp.nr WHERE cp.short_id LIKE '%D%' AND personell_nr = (SELECT personell_nr FROM care_users WHERE login_id = '".$_SESSION['sess_temp_userid']."')");
    $personnell_nr = $db->GetOne("SELECT personell_nr FROM care_users WHERE login_id = '".$_SESSION['sess_temp_userid']."'");
}
else {
    $dept_nr = $department_selected;
}

// $chk_pending = $enc_obj->checkPending($_GET['encounter_nr']);   
// print_r($enc_obj->sql);
// while($row = $chk_pending->FetchRow()) {
//      $service_code = $row['service_code'];
//      $pending = $row['pending'];
// }
// echo $service_code;
 echo $confCertInfo['attending_doctor'];

$is_pending = $enc_obj->checkPendingRequests($_GET['encounter_nr']); 
// $service_code = $is_pending['service_code'];
// echo "test".$is_pending['service_code'];

if ($is_pending) {
    $service_code = $row['service_code'];
    $service_code_lab = $row['service_code_lab'];

    #$note = "<span class='style4'>Patient has pending request you may wait until served or cancel the request <i>: ".$is_pending."</i></span>";
    echo  "<div class='center-discharge-slip'><span class='style4'>Patient has pending request you may wait until served or cancel the request: <br><i> ".$is_pending."</i></span></div>";
    $disabled = 'disabled';
}
else {
    $disabled = '';
}

$diagnosis = ($diagnosis != '') ? $diagnosis : $getLatestImpression['er_opd_diagnosis'];

?>

<body>
    <table cellpadding="0" align="center">
        <tr>        
            <td>
                <table class="style3">
                    <tr>
                        <td class="style3">Case No :</td>
                        <td class="style2"><? echo $encounter_nr; ?></td>
                        <td class="space180"></td>
                        <td class="style3">HRN :</td>
                        <td class="style2"><? echo $hrn; ?></td>
                    </tr>
                    <tr>
                        <td class="style3">Name :</td>
                        <td class="style2"><? echo strtoupper($person_name); ?></td>
                        <td class="space180"></td>
                        <td class="style3">Age :</td>
                        <td class="style2"><? echo $age; ?></span></td>
                        <td class="style3">Sex :</td>
                        <td class="style2"><? echo strtoupper($sex); ?></span></td>
                    </tr>
                    <tr>
                        <td class="style3">Status :</td>
                        <td class="style2"><? echo strtoupper($civil_status); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <form action="discharge-slip.php?encounter_nr=<?php echo $encounter_nr; ?>" method="post">
            <tr>        
                <td>
                    <table class="style3">
                        <tr>
                            <td class="style3">Diagnosis :</td>
                        </tr>
                        <tr>
                            <td>
                                <textarea <?php echo $disabled ?> cols="65" rows="5" name="diagnosis"><?php echo $diagnosis; ?></textarea></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>        
                    <td>
                        <table class="style3">
                            <tr>
                                <td class="style3">Home Medications:</td>
                            </tr>
                            <tr>
                                <td><textarea <?php echo $disabled ?> cols="65" rows="10" name="home_medications"><?php echo $medications; ?></textarea></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>        
                    <td>
                        <table class="style3">
                            <tr>
                                <td class="style3">Follow up at OPD on :</td>
                                <td class="style3"><input <?php echo $disabled ?> type="text" id="date_follow_up_input" name="date_follow_up_input" value="<?php echo $date_follow_up_input; ?>" onchange="checkDate(this,'date_follow_up_input'); compareDate(this);">&nbsp;<font size=1>[Click on the input box]</font></td>
                            </tr>
                            <tr>
                                <td class="style3">Date:</td>
                                <td class="style3"><input <?php echo $disabled ?> type="text" id="date_input" name="date_input" value="<?=$date_input?>" onChange="checkDate(this,'date_input'); compareDate();">&nbsp;<font size=1>[Click on the input box]</font></td>
                            </tr>
                            <tr>
                                <td class="style3">Time:</td>
                                <?php echo '<td>
                                <input '.$disabled.' type="text" id="time_input" name="time_input" value="'.$time_input.'" size="4" maxlength="5" onChange="setFormatTime(this,\'selAMPM2\')">&nbsp;
                                <select '.$disabled.' id="selAMPM2" name="selAMPM2">
                                <option value="AM" '.$selected_am.'>AM</option>
                                <option value="PM" '.$selected_pm.'>PM</option>
                                </select>&nbsp;
                                <font size=1>[12 hour format]</font>
                            </td>'; ?>
                        </tr>
                        <tr>
                            <td class="style3">ER NOD :</td>
                            <td class="style2"><input <?php echo $disabled ?> type="text" name="er_nod" value="<?php echo $er_nod;?>"></td>
                        </tr>
                        <tr>
                            <td class="style3">Department :</td>
                            <td class="style3">
                                <select <?php echo $disabled ?> class="style3" id="select_department" name="department" onChange="getDoctors()">
                                    <?php 
                                    $result = $dept_obj->getAllOPDMedicalObject(ADMIT_INPATIENT);
                                    while($row=$result->FetchRow()){
                                        if ($row['nr'] == $department_selected) {
                                            $selected = 'selected';
                                        }
                                        else if ($row['nr'] == $dept_nr) {
                                            $selected = 'selected';
                                        }
                                        else {
                                            $selected = '';
                                        }
                                        echo "<option id='".$row['nr']."' value='".$row['nr']."' $selected>".$row['name_formal']."</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="style3">Attending Physician :</td>
                            <td class="style3">
                                <select <?php echo $disabled ?> class="style3" id="select_physician" name="physician" onChange="getDepartments()">
                                    <?php 
                                    $result = $dept_obj->getDoctorsByDepartment($dept_nr);
                                    if($result){
                                        while($row=$result->FetchRow()){
                                            if ($row['personell_nr'] == $physician_selected) {
                                                $selected = 'selected';
                                            }
                                            else if ($row['personell_nr'] == $personnell_nr) {
                                                $selected = 'selected';
                                            }
                                            else {
                                                $selected = '';
                                            }
                                            echo "<option value='".$row['personell_nr']."' $selected>".strtoupper($row['name'])."</option>";
                                        }    
                                    }
                                    
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>        
                <td>
                    <table class="style3" align="center">
                        <tr>    
                            <?php 
                            if ($disabled) {
                                echo "<div class='center-discharge-slip'><span class='style4'>Patient has pending request you may wait until served or cancel the request: <br><i> ".$is_pending."</i></span></div>";
                            }
                            else {
                                echo '<td><input type="submit" name="submit" value="Save"></td>';
                                echo '<td><input type="button" value="Print" onclick="printDischargeSlip('.$encounter_nr.');"></td>';
                            }

                            ?>
                        </tr>
                        <tr>
                            <?php 
                            echo '<input type="hidden" name="encounter_nr" value="'.$encounter_nr.'">';
                            echo '<input type="hidden" name="hrn" value="'.$hrn.'">';
                            echo '<input type="hidden" name="person_name" value="'.$person_name.'">';
                            echo '<input type="hidden" name="age" value="'.$age.'">';
                            echo '<input type="hidden" name="sex" value="'.$sex.'">';
                            echo '<input type="hidden" name="civil_status" value="'.$civil_status.'">';
                            ?>
                        </tr>
                    </table>
                </td>
            </tr>
        </tr>
    </form>
</table>
</body>
</html>
