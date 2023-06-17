<?php
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/care_api_classes/class_person.php');
require_once($root_path . 'include/care_api_classes/class_encounter.php');
require_once($root_path . 'include/care_api_classes/class_insurance.php');
header('Content-Type: text/html; charset=iso-8859-1');
/***
 *  This routine will return the member relations in an option html element.
 *
 */
function getMemberRelations($reltn)
{
    global $db;
    $select = "'select'";//added by art 1/26/2014
    $html = '<select onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')" class="segInput required-verify required-save" id="patient-relation" name="pPatientIs" >';
    $strselected = ($reltn === '') ? ' selected="selected"' : "";
    $html .= '<option onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')"  value=""' . $strselected . '>--Select relation to member--</option>';

    $strSQL = "SELECT * FROM seg_relationtomember";
    $result = $db->Execute($strSQL);

    if ($result) {
        while ($row = $result->FetchRow()) {
            $strselected = ($reltn === $row['relation_code']) ? ' selected="selected"' : "";
            $html .= '<option onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')" value="' . $row['relation_code'] . '"' . $strselected . '>' . $row['relation_desc'] . '</option>';
        }
    }
    $html .= '</select><br/>';

    return $html;
}

/***
 *  This routine will get the membership types in an option html element.
 *
 */
function getMembershipTypes($memtype, &$disableEmployerFields)
{
    global $db;
    $select = "'input'";
    $html = '<select onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')" class="segInput required-verify required-save" id="membership-type" name="pMembershipType" >';
    $strselected = ($memtype === '') ? ' selected="selected"' : "";
    $html .= '<option  onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')" value=""' . $strselected . '>--Select membership type--</option>';
    $temporary_html = '';
    $strSQL = "SELECT memcategory_code,memcategory_desc,is_employer_info_required,isnbb FROM seg_memcategory ORDER BY memcategory_desc";
    $result = $db->Execute($strSQL);
    if ($result !== false) {
        while ($row = $result->FetchRow()) {
            $isSelected = false;
            if ($memtype == $row['memcategory_code']) {
                $isSelected = true;
                $disableEmployerFields = ($row['is_employer_info_required'] == '0');
            }
            //added by Nick 3/25/2014
            //modified by Robert 04/21/2015
            $sponsored_style = '';
            // if (strpos(htmlentities($row['memcategory_desc']), 'SPONSORED') !== false
            //     || strpos(htmlentities($row['memcategory_desc']), 'KASAMBAHAY') !== false
            //     || strpos(htmlentities($row['memcategory_desc']), 'LIFETIME') !== false
            //     || strpos(htmlentities($row['memcategory_desc']), 'SENIOR') !== false
                
            // ) {
            if($row['isnbb']=='1'){
                $sponsored_style = 'style="color: #FF0000;"';
                $temporary_html .= sprintf(
                    '<option ' . $sponsored_style . ' onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')" value="%s" data-employer-required="%s" %s>%s</option>',
                    htmlentities($row['memcategory_code']),
                    htmlentities($row['is_employer_info_required']),
                    $isSelected ? ' selected="selected"' : '',
                    htmlentities($row['memcategory_desc'])
                );
            } else {
                $html .= sprintf(
                    '<option ' . $sponsored_style . ' onkeydown="if(event.keyCode == 13)jumpNext(this,' . $select . ')" value="%s" data-employer-required="%s" %s>%s</option>',
                    htmlentities($row['memcategory_code']),
                    htmlentities($row['is_employer_info_required']),
                    $isSelected ? ' selected="selected"' : '',
                    htmlentities($row['memcategory_desc'])
                );
            }
            //end Robert
            //end Nick

        }
    }
    $html .= $temporary_html;
    $html .= '</select><br/>';

    return $html;
}

function getRemarks($encounter_nr, $hcare_id)
{
    global $db;
    $options = '<option value=0>-SELECT-</option>';
    $remarks = $db->GetOne("SELECT remarks FROM seg_encounter_insurance WHERE encounter_nr = " . $db->qstr($encounter_nr) . " AND hcare_id = " . $db->qstr($hcare_id));

    $sql = "SELECT * FROM seg_insurance_remarks_options";
    $rs = $db->Execute($sql);

    if ($rs) {
        if ($rs->RecordCount()) {
            while ($row = $rs->FetchRow()) {
                extract($row);
                $selected = ($remarks == $id) ? 'selected' : '';
                $options .= "<option value='$id' $selected>$title</option>";
            }
        }
    }
    return $options;
}

function getOtherRemarks($encounter_nr, $hcare_id)
{
    global $db;

    $other = $db->GetOne("SELECT other_remarks FROM seg_encounter_insurance WHERE encounter_nr = " . $db->qstr($encounter_nr) . " AND hcare_id = " . $db->qstr($hcare_id));

    return $other;
}

function getWasTempInsurance($encounter_nr, $hcare_id)
{
    global $db;

    $was_temp = $db->GetOne("SELECT was_temp FROM seg_encounter_insurance_memberinfo WHERE encounter_nr = " . $db->qstr($encounter_nr) . " AND hcare_id = " . $db->qstr($hcare_id));

    return $was_temp;
}

// Process page parameters
$id = !empty($_GET["id"]) ? $_GET["id"] : null;
$provider = isset($_GET["provider"]) ? $_GET["provider"] : null;
$pid = isset($_GET["pid"]) ? $_GET["pid"] : null;
$encounter_nr = isset($_GET["encounter_nr"]) ? $_GET["encounter_nr"] : null;
$isPrincipal = (@$_GET["principal"] != 0) ? true : false;

$remarks = $db->GetOne("SELECT remarks FROM seg_encounter_insurance WHERE encounter_nr = " . $db->qstr($encounter_nr) . " AND hcare_id = " . $db->qstr($provider));
$id = ($remarks == 1) ? "TEMP" : $id; 

$lname = '';
$fname = '';
$mname = '';
$suffix = '';
$gender = '';
$bdate = '';
$reltn = '';
$mtype = '';
$empno = '';
$empnm = '';

$person = new Person();
$objInc = new Insurance;
$encObj = new Encounter();
if (!$person->preloadPersonInfo($pid)) {
    // error
}

$encounterType = $encObj->EncounterType($encounter_nr);

// For dialysis patients only
$lastEncounter = $objInc->getDialysisPatientLastEncounter($pid, $encounter_nr);

if ($encounter_nr) {

    $InsuranceEncounter = $objInc->getPatientInfoEnc($encounter_nr);
    echo $objInc->sql;
    if ($InsuranceEncounter) {
        $lname = $InsuranceEncounter['member_lname'];
        $fname = $InsuranceEncounter['member_fname'];
        $mname = $InsuranceEncounter['member_mname'];
        $dependentPin = $InsuranceEncounter['patient_pin'];
        $suffix = $InsuranceEncounter['suffix'];
        $gender = $InsuranceEncounter['sex'];
        $bdate = date("m-d-Y", strtotime($InsuranceEncounter['birth_date']));
        $reltn = $InsuranceEncounter['relation'];
        $empno = $InsuranceEncounter['employer_no'];
        $empnm = $InsuranceEncounter['employer_name'];
        $mtype = $InsuranceEncounter['member_type'];
    } 
    else if(!$InsuranceEncounter && ($encounterType == '5' && $lastEncounter)) {

        $DialysisEncounter = $objInc->getPatientInfoEnc($lastEncounter);

        $lname = $DialysisEncounter['member_lname'];
        $fname = $DialysisEncounter['member_fname'];
        $mname = $DialysisEncounter['member_mname'];
        $suffix = $DialysisEncounter['suffix'];
        $gender = $DialysisEncounter['sex'];
        $bdate = date("m-d-Y", strtotime($DialysisEncounter['birth_date']));
        $reltn = $DialysisEncounter['relation'];
        $empno = $DialysisEncounter['employer_no'];
        $empnm = $DialysisEncounter['employer_name'];
        $mtype = $DialysisEncounter['member_type'];
    }
    else {
        if ($isPrincipal) {
            $lname = $person->Lastname();
            $fname = $person->FirstName();
            $mname = $person->MiddleName();
            $suffix = $person->Suffix();
            $gender = $person->Sex();
            $bdate = date("m-d-Y", strtotime($person->BirthDate()));

            // added by carriane 08/13/18
            if($suffix)
                $fname = str_replace(' '.$suffix, '', $fname);
            // end carriane
        }
        $reltn = @$info["relation"];
        $empno = @$info["employer_no"];
        $empnm = @$info["employer_name"];

    }


}

if (empty($id)) {
    $id = @$info['insurance_nr'];
}


if (!empty($encounter_nr)) {
    $encounter = new Encounter;
    $lastEncounter = $objInc->getDialysisPatientLastEncounter($pid, $encounter_nr);
    
    $mtype = $encounter->getMemberType($encounter_nr);

    if(!$mtype) {
        $mtype = $encounter->getMemberType($lastEncounter);
    }
} else {
    $mtype = @$info['member_type'];
}

// Send back the contact form HTML
$disableEmployerFields = false;
?>
<div class="modal-top"></div>
<div class="modal-content">
    <h1 class="modal-title">Membership Details</h1>

    <div class="modal-loading"></div>
    <div class="modal-message"></div>
    <form id="member-form" action="#">
        <label for="is_temp">Is Temp?</label>
        <input type="checkbox" id="is_temp"/><br/>
        <label for="member-id">Insurance No</label>
        <input type="text" id="member-id" maxlength="12" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               class="segInput required-verify required-save" name="pPIN" value="<?= htmlentities($id) ?>" 
               onkeypress="return numericOnly(event)"/>


        <?php if (!$isPrincipal) { ?>
            <label for="dependent-pin">Dependent PIN:</label>
            <input type="text" id="dependent-pin" maxlength="12" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               class="segInput required-get required-verify required-save" name="pDependentPin"
               value="<?= htmlentities($dependentPin) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <?php } ?>



        <label for="member-lastname">Last Name:</label>
        <input type="text" id="member-lastname" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               class="segInput required-get required-verify required-save" name="pMemberLastName"
               value="<?= htmlentities($lname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-firstname">First Name</label>
        <input type="text" id="member-firstname" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               class="segInput required-get required-verify required-save" name="pMemberFirstName"
               value="<?= htmlentities($fname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-middlename">Middle Name</label>
        <input type="text" id="member-middlename" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               class="segInput" name="pMemberMiddleName"
               value="<?= htmlentities($mname) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-suffix">Suffix</label>
        <input type="text" id="member-suffix" onkeydown="if(event.keyCode == 13)jumpNext(this,'input')" class="segInput"
               name="pMemberSuffix"
               value="<?= htmlentities($suffix) ?>" <?= $isPrincipal ? 'readonly="readonly"' : '' ?>/>
        <label for="member-gender">Gender</label>
        <select class="segInput required-verify required-save" id="member-gender" name="pGender">
            <option value="m" <?= ($gender=='m') ? "selected" : "" ?>>Male</option>
            <option value="f" <?= ($gender=='f') ? "selected" : "" ?>>Female</option>
        </select>
        <label for="member-birthdate">Birthdate</label>
        <input type="text" id="member-birthdate" onkeydown="if(event.keyCode == 13)jumpNext(this,'select')" onkeyup="maskBday()"
               class="segInput required-get required-verify required-save" name="pMemberBirthDate" value="<?= $bdate ?>" placeholder="mm-dd-yyyy"
               style="width:100px" pattern="/^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/"/><br/>
        <?php if (!$isPrincipal) { ?>
            <label for="patient-relation">Patient's Relation</label>
            <?= getMemberRelations($reltn); ?>
        <?php } else { ?>
            <input type="hidden" id="patient-relation" name="pPatientIs" value="M"/>
        <?php } ?>

        <label for="membership-type">Membership Type</label>
        <?= getMembershipTypes($mtype, $disableEmployerFields); ?>
        <label for="member-pempno">Employer ID</label>
        <input type="text" id="member-pempno" class="segInput required-verify required-save" name="pPEN"
               onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               value="<?= htmlentities($empno) ?>" <?= $disableEmployerFields ? 'disabled="disabled"' : '' ?> />
        <label for="member-pempnm">Employer Name</label>
        <input type="text" id="member-pempnm" class="segInput required-verify required-save" name="pEmployerName"
               onkeydown="if(event.keyCode == 13)jumpNext(this,'input')"
               value="<?= htmlentities($empnm) ?>" <?= $disableEmployerFields ? 'disabled="disabled"' : '' ?> />

        <label for="member-remarks">Remarks</label>
        <select class="segInput required-verify required-save" id="member-remarks" name="remarks">
            <?= getRemarks($encounter_nr, $provider) ?>
        </select>

        <input type="hidden" id="provider" name="provider" value="<?= $provider ?>"/>
        <input type="hidden" id="pid" name="pid" value="<?= $pid ?>"/>
        <input type="hidden" id="encounter_nr" name="encounter_nr" value="<?= $encounter_nr ?>"/>
        <label for="other_remarks"></label>
        <input class="segInput" type="text" id="other_remarks" name="other_remarks" value="<?= getOtherRemarks($encounter_nr, $provider) ?>" style="display: none">

        <input type="hidden" id="was_temp" name="was_temp" value="<?= getWasTempInsurance($encounter_nr, $provider) ?>" />

</div>

<div class="modal-bottom" style="text-align:center">
    <!--     <button id="member-get" type="button" class="segButton">
            <img src="../../gui/img/common/default/page_go.png" />Get PIN
        </button>
        <button id="member-verify" type="button" class="segButton">
            <img src="../../gui/img/common/default/report_magnify.png" />Verify
        </button> -->
    <button id="member-save" type="button" class="segButton">
        <img src="../../gui/img/common/default/add.png"/>Add Insurance
    </button>
    <!--    <button id="member-cancel" type="button" class="segButton">
            <img src="../../gui/img/common/default/cancel.png" />Check SPC
        </button>-->
    <button id="member-cancel" type="button" class="segButton">
        <img src="../../gui/img/common/default/cancel.png"/>Close
    </button>
</div>

<script type="text/javascript">
    var TEMP_INSURANCE_NR = 11111;
    var INS_TEMP_ID = 1;
    var INS_SUBREQ_ID = 2;
    var INS_4P_ID = 3;
    var INS_SPONSORED_ID = 4;
    var SENIOR = 5;
    var OTHERS = 6;
    var TEMP_PAYWARD = 7;

    jQuery(function ($) {
        adjustDetails();

        $("#member-birthdate").datepicker({
            dateFormat: "mm-dd-yy",
            changeMonth: true,
            changeYear: true
        });
        $('#membership-type').change(function () {
            var $this = $(this);
            var selected = $this.find(':selected');
            $('#member-pempno,#member-pempnm').attr(
                'disabled',
                (selected.data('employer-required') != '0') ?
                    null : 'disabled'
            );
        });
        $('#member-remarks').change(adjustDetails);
        $('#is_temp').change(isTemp);

        if($('#member-id').val() == 'TEMP'){
            $('#is_temp').prop("checked", true);
        }
        else{
            $("#is_temp").prop("checked", false);
        }

        function isTemp(){
            var memberID = $('#member-id');
            var remarks = $('#member-remarks');
            var other_remarks = $('#other_remarks');
            var was_temp = $('#was_temp');

            if($('#is_temp').is(":checked")){
                memberID.val('TEMP');
                memberID.attr('class', 'segInput');
                memberID.prop("readonly", true);
                remarks.val('0');
                other_remarks.val('');
                other_remarks.attr("style", "display:none");
                was_temp.val('1');
            }
            else{
                memberID.val('<?= ($id == "TEMP") ? "" : $id?>');
                memberID.attr('class', 'segInput');
                memberID.prop("readonly", false);
                remarks.val('');
                other_remarks.val('<?= getOtherRemarks($encounter_nr, $provider) ?>');
                other_remarks.attr("style", "display:none");
                was_temp.val('<?= getWasTempInsurance($encounter_nr, $provider) ?>');
            }
        }

        function adjustDetails() {
            var remarks = $('#member-remarks').find(":selected").val();
            var memberID = $('#member-id');
            var other_remarks = $('#other_remarks');
            var temp_chk = $('#is_temp');
            if (remarks == 0) {
                memberID.attr('class', 'segInput required-verify required-save');
                memberID.prop("readonly", false);
                other_remarks.val('');
                other_remarks.attr("style", "display:none");
            } else if (remarks == INS_TEMP_ID) {
                memberID.val('');
                memberID.attr('class', 'segInput');
                memberID.prop("readonly", true);
                other_remarks.val('');
                other_remarks.attr("style", "display:none");
            } else if (remarks == INS_SUBREQ_ID) {
                memberID.val('');
                $('#membership-type').val('HSM');
                memberID.prop("readonly", true);
                memberID.attr('class', 'segInput');
                temp_chk.prop('checked', false);
                other_remarks.val('');
                other_remarks.attr("style", "display:none");
                $('#member-pempno,#member-pempnm').attr('disabled','disabled');
            } else if (remarks == INS_4P_ID || remarks == INS_SPONSORED_ID || remarks == SENIOR) {
                memberID.val('');
                $('#membership-type').val('I');
                memberID.prop("readonly", true);
                memberID.attr('class', 'segInput');
                temp_chk.prop('checked', false);
                other_remarks.val('');
                other_remarks.attr("style", "display:none");
                $('#member-pempno,#member-pempnm').attr('disabled','disabled');
            } else if (remarks == OTHERS){
                memberID.val('');
                memberID.prop("readonly", true)
                memberID.attr('class', 'segInput');
                temp_chk.prop('checked', false);
                other_remarks.attr("style", "");
                $('#member-pempno,#member-pempnm').attr('disabled','disabled');
            }
             else if (remarks == TEMP_PAYWARD){
                memberID.val('');
                memberID.prop("readonly", true);
                memberID.attr('class', 'segInput');
                temp_chk.prop('checked', false);
                other_remarks.val('');
                other_remarks.attr("style", "display:none");
                $('#member-pempno,#member-pempnm').attr('disabled','disabled');
            }else {
                memberID.prop("readonly", true);
                temp_chk.prop('checked', false);
            }
        }

    });
    //added by art 01/26/2014
    function jumpNext(input, type) {
        $(input).next(type).focus();
    }
    function getfocus() {
        document.getElementById('member-save').focus();
    }
    //end art

    //Added by Gervie 07/20/2016
    function maskBday() {
        var mask = "__-__-____";
        var bday = document.getElementById("member-birthdate");
        var text = "";
        var num = [];
        var output = "";
        var lastPos = 1;

        text = bday.value;

        //get numbers
        for(var i = 0; i < text.length; i++) {
            if (!isNaN(text.charAt(i)) && text.charAt(i) != '') {
                num.push(text.charAt(i));
            }
        }

        //write over mask
        for(var j = 0; j < mask.length; j++) {
            if(mask.charAt(j) == "_") {
                if(num.length == 0) {
                    output = output + mask.charAt(j);
                }
                else {
                    output = output + num.shift();
                    lastPos = j + 1;
                }
            }
            else {
                output = output + mask.charAt(j);
            }
        }

        document.getElementById("member-birthdate").value = output;
        document.getElementById("member-birthdate").setSelectionRange(lastPos, lastPos);

        if(lastPos == 10){
            if(validateDate(output) == false){
                document.getElementById("member-birthdate").value = "__-__-____";
                alert("Invalid Date");
            };
        }
    }

    function validateDate(date) {
        var bits = date.split('-');
        var y = bits[2], d  = bits[1], m = bits[0];
        // Assume not leap year by default (note zero index for Jan)
        var daysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];

        var cur_year = new Date().getFullYear();

        if(y < 1900 || y > cur_year) {
            return false;
        }

        // If evenly divisible by 4 and not evenly divisible by 100,
        // or is evenly divisible by 400, then a leap year
        if ( (!(y % 4) && y % 100) || !(y % 400)) {
            daysInMonth[1] = 29;
        }
        return d <= daysInMonth[--m];
    }

    //added by Mary 05/24/2016
    function numericOnly(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
    }

</script>