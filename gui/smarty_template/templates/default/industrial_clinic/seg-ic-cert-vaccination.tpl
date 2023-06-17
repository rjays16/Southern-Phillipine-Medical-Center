{{$sFormStart}}
<table cellspacing="0" cellpadding="0" align="center" width="100%">
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
    <td colspan="*" style="padding-left: 20px;" background="images/top_05.jpg">{{$sMsg}}</td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
    <td colspan="*">
        <table align="center" width="65%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="td1"
                ">Name:</td>
                <td class="td2">{{$sName}}</td>
                <td colspan="*"></td>
            </tr>
            <tr>
                <td class="td1">HRN:</td>
                <td class="td2">{{$sHrn}}</td>
                <td class="td1">Case Number:</td>
                <td class="td2">{{$sCase}}</td>
            </tr>
            <tr>
                <td class="td1">Age:</td>
                <td class="td2">{{$sAge}}</td>
                <td class="td1">Sex:</td>
                <td class="td2">{{$sSex}}</td>
            </tr>
            <tr>
                <td class="td1">Civil Status:</td>
                <td class="td2">{{$sStatus}}</td>
                <td class="td1">Nationality:</td>
                <td class="td2">{{$sNationality}}</td>
            </tr>
            <tr>
                <td class="td1">Address:</td>
                <td colspan="3" class="td2">{{$sAddress}}</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
    <td bgcolor="#696969" align="center">
        <span class="txt1" style="color: #FFFFFF;"><strong>Vaccination Certificate</strong></span>
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>

<tr>
    <td align="center">
        <table class="txt2" width="80%">
            <tr>
                <td class="txt1" align="center">Tetanus Toxoid</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="center">
        <table class="txt2" width="80%">
            <tr>
                <td align="right">{{$tDose1}}</td>
                <td class="td1">
                    First Dose:
                </td>
                <td class="td2">
                    <input type="text" name="first_tetanus" id="first_tetanus" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="{{$val1}}"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="tetanus_trigger1" src="{{$sImgCalendar}}"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "first_tetanus", ifFormat: "%m/%d/%Y",
                            showsTime: false,
                            button: "tetanus_trigger1",
                            singleClick: true,
                            step: 1
                        });
                    </script>
                </td>
                <td>
                    <select name="tetanus_deltoid1">
                        {{$tOption1}}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">{{$tDose2}}</td>
                <td class="td1">
                    Second Dose:
                </td>
                <td class="td2">
                    <input type="text" name="second_tetanus" id="second_tetanus" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="{{$val2}}"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="tetanus_trigger2" src="{{$sImgCalendar}}"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "second_tetanus", ifFormat: "%m/%d/%Y",
                            showsTime: false,
                            button: "tetanus_trigger2",
                            singleClick: true,
                            step: 1
                        });
                    </script>
                </td>
                <td>
                    <select name="tetanus_deltoid2">
                        {{$tOption2}}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">{{$tDose3}}</td>
                <td class="td1">
                    Third Dose:
                </td>
                <td class="td2">
                    <input type="text" name="third_tetanus" id="third_tetanus" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="{{$val3}}"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="tetanus_trigger3" src="{{$sImgCalendar}}"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "third_tetanus", ifFormat: "%m/%d/%Y",
                            showsTime: false,
                            button: "tetanus_trigger3",
                            singleClick: true,
                            step: 1
                        });
                    </script>
                </td>
                <td>
                    <select name="tetanus_deltoid3">
                        {{$tOption3}}
                    </select>
                </td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <td align="center">
        <table class="txt2" width="80%">
            <tr>
                <td class="txt1" align="center">Hepatitis B Vaccine</td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="center">
        <table class="txt2" width="80%">
            <tr>
                <td align="right">{{$hDose1}}</td>
                <td class="td1">
                    First Dose:
                </td>
                <td class="td2">
                    <input type="text" name="first_hepatitis" id="first_hepatitis" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="{{$val4}}"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="hepatitis_trigger1"
                         src="{{$sImgCalendar}}"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "first_hepatitis", ifFormat: "%m/%d/%Y",
                            showsTime: false,
                            button: "hepatitis_trigger1",
                            singleClick: true,
                            step: 1
                        });
                    </script>
                </td>
                <td>
                    <select name="hepatitis_deltoid1">
                        {{$hOption1}}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">{{$hDose2}}</td>
                <td class="td1">
                    Second Dose:
                </td>
                <td class="td2">
                    <input type="text" name="second_hepatitis" id="second_hepatitis" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="{{$val5}}"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="hepatitis_trigger2"
                         src="{{$sImgCalendar}}"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "second_hepatitis", ifFormat: "%m/%d/%Y",
                            showsTime: false,
                            button: "hepatitis_trigger2",
                            singleClick: true,
                            step: 1
                        });
                    </script>
                </td>
                <td>
                    <select name="hepatitis_deltoid2">
                        {{$hOption2}}
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">{{$hDose3}}</td>
                <td class="td1">
                    Third Dose:
                </td>
                <td class="td2">
                    <input type="text" name="third_hepatitis" id="third_hepatitis" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="{{$val6}}"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="hepatitis_trigger3"
                         src="{{$sImgCalendar}}"/>
                    <script type="text/javascript">
                        Calendar.setup({
                            inputField: "third_hepatitis", ifFormat: "%m/%d/%Y",
                            showsTime: false,
                            button: "hepatitis_trigger3",
                            singleClick: true,
                            step: 1
                        });
                    </script>
                </td>
                <td>
                    <select name="hepatitis_deltoid3">
                        {{$hOption3}}
                    </select>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td align="center">
        <table class="txt2" width="80%">
             <tr>
                <td class="td1">
                    Requested by:
                </td>
                <td class="td2">
                    <select name="vacc_cert2" id="nurse-incharge">
                        {{$vacc_cert2}}
                    </select>
                </td>
                <td class="td1">
                    Noted by:
                </td>
                <td class="td2">
                    <select name="vacc_cert" id="in-charge">
                        {{$vacc_cert}}
                    </select>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td>&nbsp;</td>
</tr>

<tr>
    <td align="center" background="images/top_05.jpg" bgcolor="#EDF2FE">
        {{$sButtons}}
        {{$sEncRef}}
        {{$sMode}}
    </td>
</tr>
</table>
{{$sFormEnd}}