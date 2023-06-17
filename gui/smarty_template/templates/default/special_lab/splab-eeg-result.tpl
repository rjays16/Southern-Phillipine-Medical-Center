<div style="padding: 10px;">
    <center><span style="color: #880000;"><strong>{{$sMessage}}</strong></span></center>
    <h3 align="center">EEG OFFICIAL RESULT FORM</h3>
    <hr/>

    {{$sFormStart}}
    <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td><strong>HRN:</strong></td>
            <td>{{$sHRN}}</td>
        </tr>
        <tr>
            <td width="65px;"><strong>Name:</strong></td>
            <td width="200px">{{$sName}}</td>
            <td width="50px"><strong>Date:</strong></td>
            <td>
                <input type="text" name="perform_date" id="perform_date" style="font: bold 12px Arial;"
                       maxlength="10" size="10" value="{{$sDate}}"/>
                <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                     id="eeg_trigger" src="{{$sImgCalendar}}"/>
                <script type="text/javascript">
                    Calendar.setup({
                        inputField: "perform_date", ifFormat: "%Y-%m-%d",
                        showsTime: false,
                        button: "eeg_trigger",
                        singleClick: true,
                        step: 1
                    });
                </script>
            </td>
        </tr>
        <tr>
            <td><strong>Age:</strong></td>
            <td>{{$sAge}}</td>
            <td><strong>Sex:</strong></td>
            <td>{{$sSex}}</td>
        </tr>

    </table>
    <br/><br/>
    <table cellpadding="3">
        <tr>
            <td align="right" style="font-weight: bold">Service Name:</td>
            <td>{{$sService}}</td>
        </tr>
        <tr>
            <td><strong>Requesting Physician:</strong></td>
            <td colspan="3">{{$sRequestDoc}}</td>
        </tr>
    </table>
    <table cellspacing="10">
        <tr>
            <td align="right" style="font-weight: bold">Clinical Data:</td>
            <td colspan="4">{{$sClinical}}</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Medications:</td>
            <td>{{$sMedication}}</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Technical Summary:</td>
            <td>{{$sSummary}}</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Interpretation:</td>
            <td>{{$sInterpret}}</td>
        </tr>
    </table>
    <table cellpadding="5">
        <tr>
            <td align="right" style="font-weight: bold">Consulting Doctor:</td>
            <td>{{$sConsultDoc}}</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Doctor Title:</td>
            <td>{{$sDoctorTitle}}</td>
        </tr>
    </table>
    <hr/>
    <center>{{$sButtons}}</center>
    {{$sFormEnd}}
</div>