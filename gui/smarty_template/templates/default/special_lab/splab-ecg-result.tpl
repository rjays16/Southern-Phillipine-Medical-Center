<div style="padding: 10px;">
    <center><span style="color: #880000;"><strong>{{$sMessage}}</strong></span></center>
    <h3 align="center">ECG OFFICIAL RESULT FORM</h3>
    <hr/>

    {{$sFormStart}}
    <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="65px;"><strong>Name:</strong></td>
            <td width="200px">{{$sName}}</td>
            <td width="50px"><strong>Date:</strong></td>
            <td>
                <input type="text" name="ecg_date" id="ecg_date" style="font: bold 12px Arial;"
                       maxlength="10" size="10" value="{{$sDate}}"/>
                <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                     id="ecg_trigger" src="{{$sImgCalendar}}"/>
                <script type="text/javascript">
                    Calendar.setup({
                        inputField: "ecg_date", ifFormat: "%Y-%m-%d",
                        showsTime: false,
                        button: "ecg_trigger",
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
        <tr>
            <td><strong>Address:</strong></td>
            <td colspan="3">{{$sAddress}}</td>
        </tr>
        <tr>
            <td><strong>Clinic:</strong></td>
            <td colspan="3">{{$sClinic}}</td>
        </tr>

    </table>
    <br/><br/>
    <table>
        <tr>
            <td align="right" style="font-weight: bold">Rhythm:</td>
            <td>{{$sRhythm}}</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Axis:</td>
            <td>{{$sAxis}}</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Atrial Rate:</td>
            <td>{{$sAtrial}} BPM</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Ventricular Rate:</td>
            <td>{{$sVentri}} BPM</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">PR Interval:</td>
            <td>{{$sInterval}} SEC</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">QRS:</td>
            <td>{{$sQrs}} SEC</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">QT:</td>
            <td>{{$sQt}} SEC</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Position:</td>
            <td>{{$sPosition}}</td>
        </tr>
    </table>
    <br/><br/>
    <div class="container">
        <fieldset>
            <legend>Impression:</legend>
            {{$sEcgAbbre}}
            {{$sImpression}}
            {{$sHiddenEcgAbbre}}
        </fieldset>
    </div>
    <br/><br/>
    <table>
        <tr>
            <td align="right" style="font-weight: bold">Prepared By:</td>
            <td>{{$sPreparedBy}}</td>
        </tr>
    </table>
    <hr/>
    <center>{{$sButtons}}</center>
    {{$sFormEnd}}
</div>