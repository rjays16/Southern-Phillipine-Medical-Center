<style type="text/css">
    .tabFrame {
        /*padding:5px;*/
        min-height:140px;
    }
</style>
<script language="javascript" type="text/javascript">
    function tabClick(obj) {
    if (obj.className=='segActiveTab') return false;
    var dList = obj.parentNode;
    var tab;
    if (dList) {
    var listItems = dList.getElementsByTagName("LI");
    if (obj) {
    for (var i=0;i<listItems.length;i++) {
    if (obj!=listItems[i]) {
    listItems[i].className = "";
    tab = listItems[i].getAttribute('segTab');
    if ($(tab))
        $(tab).style.display = "none";
}
}
tab = obj.getAttribute('segTab');
if ($(tab))	$(tab).style.display = "block";
obj.className = "segActiveTab";
}
}
}

function toggleTBody(list) {
var dTable = $(list);
if (dTable) {
var dBody = dTable.getElementsByTagName("TBODY")[0];
if (dBody) dBody.style.display = (dBody.style.display=="none") ? "" : "none";
}
}

function enableInputChildren(id, enable) {
var el=$(id);
if (el) {
var children = el.getElementsByTagName("INPUT");
if (children) {
for (i=0;i<children.length;i++) {
children[i].disabled = !enable;
}
return true;
}
}
return false;
}
    {{$print}}
</script>
{{$sFormStart}}
<div style="width:750px; margin-top:10px">
    <table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="">
        <tbody>
            <tr height="5"><td class="segPanelHeader" colspan="4">Patient Information</td></tr>
            <tr>
                <td width="53%" valign="top" class="segPanel">
                    <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>PID</label></td>
                            <td align="left" valign="middle">{{$sPatientID}}</td>
                            <td rowspan="5" class="photo_id" align="center" id="photo_row" style="background-color:transparent">
                                <img width="180px" height="150px" src="{{$img_source}}" name="headpic" id="headpic" border="0">
                                <input type="hidden" id="photo_src" name="photo_src" value=""/>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Fullname</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">
                                {{$sPatientEncNr}}
                                {{$sPatientName}}
                                {{$sSelectEnc}}
                                {{$sClearEnc}}
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Address</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">{{$sAddress}}</td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Age</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">
                                {{$sPatientAge}}
                                <label>Birthday</label>
                                {{$sPatientBirthday}}
                            </td>
                        </tr>
                        <tr valign="top">
                            <td align="right" nowrap="nowrap"><label>Gender</label></td>
                            <td align="left" nowrap="nowrap" valign="middle">
                                {{$sPatientGender}}
                                <label>Civil Status</label>
                                {{$sPatientStatus}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="53%" valign="top" class="segPanel">
                    <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                        <tr valign="top">
                            <td nowrap="nowrap" align="right"><label>Admitting Diagnosis</label></td>
                            <td valign="middle">{{$sPatientDiagnosis}}</td>
                            <td nowrap="nowrap" align="right">
                                <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                                    <tr valign="top">
                                        <td nowrap="nowrap" align="left">
                                            <label>Admission Date</label>&nbsp;&nbsp;
                                            {{$sPatientAdmissionDate}}
                                        </td>
                                    </tr>
                                    <tr valign="top">
                                        <td nowrap="nowrap" align="left">
                                            <label>Discharged Date</label>
                                            {{$sPatientDischargeDate}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr valign="top">
                            <td nowrap="nowrap" align="right"><label>Location/Clinic</label></td>
                            <td align="left" valign="middle">{{$sPatientLocation}}</td>
                            <td nowrap="nowrap" align="right">
                                <table border="0" cellspacing="1" cellpadding="2" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                                    <tr valign="top">
                                        <td nowrap="nowrap" align="left">
                                            <label>Patient Type</label>&nbsp;&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$sPatientType}}
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <br/>
</div>
<div style="width:800px;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top">
                <ul id="request-tabs" class="segTab" style="padding-left:10px;">
                    <li id="tab_request" {{if $bTabRequest}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="new_transact">
                        <h2 class="segTabText">New Transaction</h2>
                    </li>
                    <li id="tab_history" {{if $bTabHistory}}class="segActiveTab"{{/if}} onclick="tabClick(this)" segTab="history">
                        <h2 class="segTabText">Billing</h2>
                    </li>
                </ul>
                <div class="" style="width:100%;height:300px; border-top:2px solid #4e8ccf; margin-left:10px" align="center">
                    <div id="new_transact" style="padding-top: 10px; padding-bottom: 10px;{{if !$bTabRequest}}display:none{{/if}}">
                        <table border="0" cellspacing="0" cellpadding="0" width="110%" align="center" style="">
                            <tbody>
                                <tr height="5">
                                    <td class="segPanelHeader" colspan="2">Transaction Details</td>
                                </tr>
                                <tr>
                                    <td width="53%" valign="top"  class="segPanel">
                                        <table border="0" cellspacing="1" cellpadding="5" width="100%" style="margin-top: 5%;font-family:Arial, Helvetica, sans-serif">
                                            <!--
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Reference</label></td>
                                                <td align="left" valign="middle">{{$requestReferenceNo}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>No. of Visits</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestVisitNo}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Attending Nurse</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestNurses}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Request Type</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestDialysisType}}</td>
                                            </tr>

                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Remarks</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestRemarks}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Procedure</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestProcedure}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Status</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestStatus}}</td>
                                            </tr>
                                            -->
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap" valign="bottom"><label>Requesting Doctor</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestDoctors}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap" valign="bottom"><label>Diagnosis</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestDiagnosis}}</td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap" valign="middle"><label>Encoded by</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$requestEncoder}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="*" valign="top"  class="segPanel">
                                        <table border="0" cellspacing="2" cellpadding="0" width="100%" style="font-family:Arial, Helvetica, sans-serif">
                                            <tr valign="top">

                                                <td width="1%" nowrap="nowrap" align="right"><label>Transaction Date</label></td>
                                                <td width="5%" align="left" valign="middle">{{$requestDate}}{{$sCalendarIcon}}{{$jsCalendarSetup}}</td>

                                            </tr>
                                            <!-- <tr>
                                                <td colspan="2"><strong>VITAL SIGNS</strong></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Blood Pressure</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$bp_systole}}&nbsp;/&nbsp;{{$bp_diastole}}&nbsp;<span style="font: 11px Arial;">mm Hg</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Temperature</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$temperature}}&nbsp;<span style="font: 11px Arial;">�C</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Weight</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$weight}}&nbsp;<span style="font: 11px Arial;">kg</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Resp. Rate</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$resp_rate}}&nbsp;<span style="font: 11px Arial;">br/m</span></td>
                                            </tr>
                                            <tr valign="top">
                                                <td align="right" nowrap="nowrap"><label>Pulse Rate</label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">{{$pulse_rate}}&nbsp;<span style="font: 11px Arial;">b/m</span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" nowrap="nowrap"><label></label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">&nbsp;<span style="font: 11px Arial;"></span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" nowrap="nowrap"><label></label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">&nbsp;<span style="font: 11px Arial;"></span></td>
                                            </tr>
                                            <tr>
                                                <td align="right" nowrap="nowrap"><label></label></td>
                                                <td align="left" nowrap="nowrap" valign="middle">&nbsp;<span style="font: 11px Arial;"></span></td>
                                            </tr> -->
                                            <tr valign="top">

                                                <td align="right" nowrap="nowrap" valign="middle"><label>Pre-Billing Details</label></td>
                                                
                                                <!-- added by KENTOOT 09-19-2014 -->
                                                <td align="left" nowrap="nowrap" colspan="4" style="font:bold 11px Arial; color: red;">
                                                <label>
                                                    <strong>{{$alertPrebill}}</strong>
                                                </label>
                                                </td>

                                            </tr>
                                            <tr valign="top">
                                                <td></td>
                                                <td  nowrap="nowrap" valign="middle">
                                                    <table width="90%">
                                                        <tr>
                                                            <td align="center" >
                                                                <center><label>Amount</label></center>
                                                            </td>
                                                            <td>
                                                                <center><label>Quantity</label></center>
                                                            </td>
                                                            <td>
                                                                <center><label>HDF</label></center>
                                                            </td>
                                                            </tr>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                        <tr valign="top">

                            <td align="middle" nowrap="nowrap" valign="middle"><strong>{{$requestBillTypePH}}</strong></td>

                            <td  nowrap="nowrap" valign="middle">
                                <table width="100%">
                                    <tr>
                                        <td align="middle">
                                            {{$requestAmountPH}}
                                        </td>
                                        <td align="middle">
                                            {{$requestQuantityPH}}
                                        </td>
                                                                                                                        <td align="middle">
                                            {{$requestAmountHDF}}
                                        </td>
                                        <td align="middle">
                                            {{$requestSubsidizePH}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="middle" nowrap="nowrap" valign="middle"><strong>{{$requestBillTypeNPH}}</strong></td>
                            <td  nowrap="nowrap" valign="middle">
                                <table width="100%">
                                    <tr>
                                        <td align="middle" >
                                            {{$requestAmountNPH}}
                                        </td>
                                        <td align="middle">
                                            {{$requestQuantityNPH}}
                                        </td>
                                                                                                                        <td align="middle">
                                            {{$requestAmountHDFNPH}}
                                        </td>
                                        <td align="middle">
                                            {{$requestSubsidizeNPH}}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                           <!-- <td align="middle" nowrap="nowrap" valign="middle"><strong>HDF Request</strong></td> -->
                           <td  nowrap="nowrap" valign="middle">
                                <table width="100%">
                                    <tr>
                                        <td align="middle" >
                                            <!-- {{$requestAmountHDF}} -->
                                        </td>
                                        <td align="middle">
                                            <!-- {{$requestQuantityHDF}} -->
                                        </td>
                                        <td align="middle">
                                            <!-- {{$requestSubsidizeHDF}} -->
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td align="middle" nowrap="nowrap" valign="middle"><strong>Include Lab Request</strong></td>
                            <td align="left" nowrap="nowrap" colspan="4">
                                <input type='hidden' value='0' name="labRequest">
                                <input type="checkbox" name="labRequest" style="margin-left:15px" value='1'>
                            </td>
                        </tr>
                        <tr>
                            <td align="middle" nowrap="nowrap" valign="middle"><label style="color:black"><strong>Already Released</strong></label></td>
                            <td align="left" nowrap="nowrap" colspan="4">
                                {{$printIndicator2}}
                            </td>
                        </tr>
                        <tr valign="bottom">
                            <td align="right" nowrap="nowrap" colspan="4">
                                {{$submitBtn}}<!--{{$cancelBtn}}-->
                            </td>
                        </tr>
                        </table>
                        </td>
                        </tr>
                        </tbody>
                        </table>
                    </div>

                    <div id="history" style="padding:2px;padding-top:3px;{{if !$bTabHistory}}display:none{{/if}}">
                        <table border="0" cellspacing="1" cellpadding="0" width="100%" align="center" style="font-family:Arial, Helvetica, sans-serif">
                            <tbody>
                                <tr height="5">
                                    <td class="segPanelHeader" colspan="4">Billing Details</td>
                                </tr>
                                <tr>
                                    <td class="segPanel">
                                        <table style="font-family:Arial, Helvetica, sans-serif" width="100%" align="center">
                                            <tr>
                                                <td align="left">Encounter No:<br>{{$patientEncounter}} &nbsp;
                                                    {{$toLaboratory}}{{$viewBilling}} {{$toDischarge}}
                                                    <label id="printedIndicator" style="display:none;" for="printIndicator"><strong><label style="color:black">Already Released</label>{{$printIndicator}}</strong></label></td>
                                                <td align="right"><!--{{$requestBtn}}--><br>
                                                   {{$buttonScheduleHistory}}{{$toRefresh}}{{$historyBtn}}{{$continuousReportBtn}}{{$prntsoa}}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="billing_list" style="margin-top:5px;"></div>
                    </div>
            </td>
    </table>
</div>
<div id="frame-dialog" style="display: none;">
    <iframe id="dialog-frame" src="" style="height: 100%; width: 100%; border: none;">

    </iframe>
</div>
                                <table id="subsidizeModal" style="display:none;">
                                    <tr>
                                        <td align="left" colspan="2">
                                            <h1 id="subsidizeText"></h1> 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            Subsidy Amount: 
                                        </td>
                                        <td align="left">
                                            {{$subsidy_amount_input}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="left">
                                            Classification: 
                                        </td>
                                        <td align="left">
                                            {{$subsidy_classification_options}}
                                        </td>
                                    </tr>
                                </table>
{{$submitted}}
<!--{{$dialysis_type}} -->
{{$encounter_nr}}
{{$encounter_type}}
{{$pid}}
{{$sHiddenInputs}}
{{$jsCalendarSetup}}
{{$sFormEnd}}
{{$sTailScripts}}
{{$DialogModals}}