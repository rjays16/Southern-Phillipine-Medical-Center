{{foreach from=$errors item=error}}
    <div class="alert-danger"><strong><i>&cross;</i> Error:</strong> <span>{{$error}}</span></div>
{{/foreach}}
{{$message}}
<div id="hospital-info">
    <p>{{$hospitalInfo.hosp_country}}</p>
    <p>{{$hospitalInfo.hosp_agency}}</p>
    <p>Center for Health Development - Davao Region</p>
    <p>{{$hospitalInfo.hosp_name}}</p>
</div>
<form method="POST">
    <fieldset>
        <legend>General Data:</legend>
        <table width="100%">
            <tr width="90%">
                <td width="25%" class="text">
                    <label for="fname">First Name: </label><br>
                    <input class="name" type="text" name="fname" id="fname" value="{{$firstName}}" readonly>
                </td>
                <td width="25%" class="text">
                    <label for="lname">Last Name: </label><br>
                    <input class="name" type="text" name="lname" id="lname" value="{{$lastName}}" readonly>
                </td>
                <td width="25%" class="text">
                    <label for="mname">Middle Name: </label><br>
                    <input class="name" type="text" name="mname" id="mname" value="{{$middleName}}" readonly>
                </td>
                <td width="25%" class="text">
                    <label for="position">Job Position: </label><br>
                    <input class="name" type="text" name="position" id="position" value="{{$position}}" readonly>
                </td>
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend>I. HAS THE APPLICANT SUFFERED FROM, OR HAS BEEN TOLD HE/SHE HAD ANY OF THE FOLLOWING CONDITIONS:
        </legend>
        <table width="100%">
            <tr width="90%">
                <td class="th" width="20%"></td>
                <!-- <td class="th" width="5%">Yes</td> -->
                <td class="th" width="10%"></td>
                <td class="th" width="20%"></td>
                <!-- <td class="th" width="5%">Yes</td> -->
                <td class="th" width="10%"></td>
                <td class="th" width="20%"></td>
                <!-- <td class="th" width="5%">Yes</td> -->
                <td class="th" width="10%"></td>
            </tr>
            <hr>
            {{$conditions}}
        </table>
        <hr>
        <table>
            <tr>
                <td><label for="personalhist">Personal History: </label><br>
                        <textarea name="content[1]" cols="80" rows="3"
                                  class="textbox">{{$personalHistory}}</textarea>
                </td>
            </tr>
            <tr>
                <td><label for="familyhist">Family History: </label><br><textarea name="content[2]" cols="80"
                                                                                  rows="3"
                                                                                  class="textbox">{{$familyHistory}}</textarea>
                </td>
            </tr>
            <tr>
                <td><label for="immunizationhist">Immunization History: </label><br><textarea name="content[3]"
                                                                                              cols="80" rows="3"
                                                                                              class="textbox">{{$immunizationHistory}}</textarea> 
                </td>
            </tr>
            <tr>
                <td><label for="historyIllness">History of Present Illness: </label><br><textarea name="content[7]"
                                                                                              cols="80" rows="3"
                                                                                              class="textbox">{{$historyPresentIllness}}</textarea>
                </td> 
            </tr>
        </table>
    </fieldset>
    <fieldset>
        <legend>II. PHYSICAL EXAMINATION</legend>
        <table width="100%">
            <tr width="90%">
                <td width="15%">
                    <label for="height">Height</label><br>
                    <input type="text" name="height" id="height" class="textbox"
                           value="{{$height}}">
                </td>
                <td width="15%">
                    <label for="weight">Weight</label><br>
                    <input type="text" name="weight" id="weight" class="textbox"
                           value="{{$weight}}">
                </td>
                <td width="15%">
                    <label for="bp">Blood Pressure</label><br>
                    <input type="text" name="bp" id="bp" class="textbox"
                           value="{{$bloodPressure}}">
                </td>
                <td width="15%">
                    <label for="pr">Pulse Rate</label><br>
                    <input type="text" name="pr" id="pr" class="textbox"
                           value="{{$pulseRate}}">
                </td>
                <td width="15%">
                    <label for="rr">Respiratory Rate</label><br>
                    <input type="text" name="rr" id="rr" class="textbox"
                           value="{{$respiratoryRate}}">
                </td>
                <td width="15%">
                    <label for="bodybuilt">Body Built BMI</label><br>
                    <input type="text" name="bodybuilt" id="bodybuilt" class="textbox"
                           value="{{$bmi}}">
                </td>
            </tr>
        </table>
        <hr>
        <table width="100%">
            <tr width="100%">
                <td width="15%" align="center">Visual Acuity</td>
                <td width="5%" align="center">OD</td>
                <td width="5%" align="center">OS</td>
                <td width="25%" align="center">Ishihara</td>
                <td width="25%" align="center">Hearing</td>
                <td width="25%" align="center">Clarity of Speech</td>
            </tr>
            <tr width="100%">
                <td width="15%" align="center">W/O Correction <br>With Correction</td>
                <td width="5%" align="center"><input type="radio" name="visual" id="odwoc"
                                                     value="1" {{$visual1}}><br><input type="radio" name="visual"
                                                                                          id="odwc"
                                                                                          value="2" {{$visual2}}>
                </td>
                <td width="5%" align="center"><input type="radio" name="visual" id="oswoc"
                                                     value="3" {{$visual3}}><br><input type="radio" name="visual"
                                                                                          id="oswoc"
                                                                                          value="4" {{$visual4}}>
                </td>
                <td width="25%" align="center">Adequate <input type="radio" name="ishihara" id="ishihara_adequate"
                                                               value="1" {{$ishi1}}> <br>Defective <input
                            type="radio" name="ishihara" id="ishihara_defective" value="2" {{$ishi2}}></td>
                <td width="25%" align="center">AD <input type="radio" name="hearing" id="hearing_ad"
                                                         value="1" {{$hear1}}> <br>AS <input type="radio"
                                                                                                name="hearing"
                                                                                                id="hearing_as"
                                                                                                value="2" {{$hear2}}>
                </td>
                <td width="25%" align="center">Adequate <input type="radio" name="speech" id="speech_adequate"
                                                               value="1" {{$speech1}}> <br>Defective <input
                            type="radio" name="speech" id="speech_defective" value="2" {{$speech2}}></td>
            </tr>
        </table>
        <hr>
        <table width="100%">
            <tr width="90%">
                <th rowspan='2' width="20%"></th>
                <th colspan='2' width="">Normal(Y/N)</th>
                <th rowspan='2' width="20%">Remarks</th>
                <th rowspan='2' width="20%"></th>
                <th colspan='2' width="">Normal(Y/N)</th>
                <th rowspan='2' width="20%">Remarks</th>
            </tr>
            <tr>
                <td width="5%" align="center"></td>
                <td width="5%" align="center"></td>
                <td width="5%" align="center"></td>
                <td width="5%" align="center"></td>
            </tr>
        </table>
        <table width="100%">
            {{$physical}}
        </table>
    </fieldset>
    <fieldset>
        <legend>III. DIAGNOSTIC REPORT</legend>
        <table width="100%">
            <tr width="90%">
                <th rowspan='2' width="20%"></th>
                <th colspan='2' width="">Normal(Y/N)</th>
                <th rowspan='2' width="20%">Remarks</th>
                <th rowspan='2' width="20%"></th>
                <th colspan='2' width="">Normal(Y/N)</th>
                <th rowspan='2' width="20%">Remarks</th>
            </tr>
            <tr>

                <td width="5%" align="center"></td>
                <td width="5%" align="center"></td>
                <td width="5%" align="center"></td>
                <td width="5%" align="center"></td>
            </tr>
        </table>
        </table>
        <table width="100%">
            {{$diagnostic}}
        </table>
    </fieldset>
    <fieldset>
        <legend>OTHER ROUTINE MECIDAL EXAMINATION/S:</legend>
        <table width="100%">
            <tr width="90%">
                <td>a.Dental</td>
                <td><textarea name="content[4]" id="dental" rows="3" cols="80"
                              class="textbox">{{$aDental}}</textarea>
                </td>
            </tr>
            <tr width="90%">
                <td>b.Optha</td>
                <td><textarea name="content[5]" id="optha" rows="3" cols="80"
                              class="textbox">{{$bOptha}}</textarea>
                </td>
            </tr>
            <tr width="90%">
                <td>c.ENT</td>
                <td><textarea name="content[6]" id="ent" rows="3" cols="80"
                              class="textbox">{{$cEnt}}</textarea>
                </td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>IV. FINAL DIAGNOSIS</legend>
            <textarea name="final_diagnosis" id="final_diagnosis" rows="5" cols="80"
                      placeholder="Enter Final Diagnosis here..."
                      class="textbox">{{$finalDiagnosis}}</textarea>
    </fieldset>
    {{* added by: syboy 10/26/2015 : meow *}}
    <fieldset>
        <legend>V. Remarks</legend>
            <textarea name="remarks_final" id="remarks_final" rows="5" cols="80"
                      placeholder="Enter Remarks here..."
                      class="textbox">{{$remarks}}</textarea>
    </fieldset>
    {{* ended syboy *}}
    <fieldset>
        <legend>VI. TREATMENT</legend>
            <textarea name="treatment" id="treatment" rows="5" cols="80" placeholder="Enter Treatment here..."
                      class="textbox">{{$treatment}}</textarea>
    </fieldset>

    <fieldset>
        <legend>VII. RECOMMENDATION</legend>
        <table width="100%">
            <tr width="90%">
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_1" value="1" {{$a_checked}}>
                    <label for="recommendation_1">CLASS A</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_2" value="2" {{$bRecommendationChecked}}>
                    <label for="recommendation_2">CLASS B</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_3" value="3" {{$cRecommendationChecked}}>
                    <label for="recommendation_3">CLASS C</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_4" value="4" {{$dRecommendationChecked}}>
                    <label for="recommendation_4">CLASS D</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_5" value="5" {{$eRecommendationChecked}}>
                    <label for="recommendation_5">Pending, for further evaluation</label>
                </td>
            </tr>
        </table>
    </fieldset>

    {{* added by Nick 7-10-2015 *}}
    <fieldset>
        <legend>Other Clinical Findings</legend>
        <div id="other-clinical-findings">
            {{foreach from=$otherClinicalFindings item=finding}}
                <div class="panel">
                    <div class="title-bar"><span>{{$finding.name}}</span></div>
                    <div class="content">

                        {{if isset($finding.left_remark)}}
                            <div class="input-box">
                                <label class="label"
                                       for="exam_left_remark_{{$finding.id}}">{{$finding.left_remark}}</label>
                                <span>:</span>
                                <input id="exam_left_remark_{{$finding.id}}" name="exam[{{$finding.id}}][left_remark]"
                                       type="text" value="{{$personOtherClinicalFindings[$finding.id].left_remark}}"/>
                            </div>
                        {{/if}}

                        {{if isset($finding.right_remark)}}
                            <div class="input-box">
                                <label class="label"
                                       for="exam_right_remark_{{$finding.id}}">{{$finding.right_remark}}</label>
                                <span>:</span>
                                <input id="exam_right_remark_{{$finding.id}}" name="exam[{{$finding.id}}][right_remark]"
                                       type="text" value="{{$personOtherClinicalFindings[$finding.id].right_remark}}"/>
                            </div>
                        {{/if}}

                        {{if $finding.has_result != 0}}
                            <div class="input-box">
                                <label class="label">Result</label>
                                <span>:</span>
                                {{html_radios name="exam[`$finding.id`][result]" options=$otherClinicalFindingsResultOptions separator='&nbsp;' selected=$personOtherClinicalFindings[$finding.id].result}}
                            </div>
                        {{/if}}

                        <div class="input-box">
                            <label class="label" for="exam_remark_{{$finding.id}}">Remarks</label>
                            <span>:</span>
                            <input id="exam_remark_{{$finding.id}}" name="exam[{{$finding.id}}][remark]" type="text"
                                   value="{{$personOtherClinicalFindings[$finding.id].remark}}"/>
                        </div>

                        {{if $finding.code == "eye"}}
                            <h4>w/ Corrected Glasses</h4>
                            <div class="input-box">
                                <label class="label"
                                       for="exam_glass_left_remark_{{$finding.id}}">OD</label>
                                <span>:</span>
                                <input id="exam_glass_left_remark_{{$finding.id}}"
                                       name="exam[{{$finding.id}}][glass_left_remark]" type="text"
                                       value="{{$personOtherClinicalFindings[$finding.id].glass_left_remark}}"/>
                            </div>
                            <div class="input-box">
                                <label class="label"
                                       for="exam_glass_right_remark_{{$finding.id}}">OS</label>
                                <span>:</span>
                                <input id="exam_glass_right_remark_{{$finding.id}}"
                                       name="exam[{{$finding.id}}][glass_right_remark]" type="text"
                                       value="{{$personOtherClinicalFindings[$finding.id].glass_right_remark}}"/>
                            </div>
                        {{/if}}

                        {{if $finding.with_dr_sig != 0}}
                            <div class="input-box">
                                <label class="label">Physician</label>
                                <span>:</span>
                                {{html_options class="combo-box" name="exam[`$finding.id`][physician_nr]" options=$physicianOptions selected=$personOtherClinicalFindings[$finding.id].physician_nr}}
                            </div>
                        {{/if}}

                    </div>
                </div>
            {{/foreach}}
        </div>
    </fieldset>
    {{* end Nick *}}

    <fieldset>
        <legend>Medical Officer</legend>
        <table width="100%">
            <tr width="90%" align="center">
                <!-- <td align="center">{{$medicalOfficer}}</td> -->
                <td align="center">
                {{html_options class="combo-box" name="physician_nr" id="physician_nr" options=$physicianOptions selected=$medicalOfficerNr}}
                </td>
            </tr>
            <tr>
                <input type="hidden" value="{{$pid}}" name="pid">
                <input type="hidden" value="{{$encounter_nr}}" name="encounter_nr">
                <td align="center">
                    {{if not $hasSavedInfo}}
                        <input type="submit" value="SAVE" class="myButton"><input type="hidden" value="save" name="mode" id="mode">
                    {{/if}}
                    {{if $hasSavedInfo}}
                        <input type="submit" value="UPDATE" class="myButton"><input type="hidden" value="update" name="mode" id="mode">
                        <button type="button" class="myButton" onclick="printMedChrt('{{$encounter_nr}}','{{$refno}}');" style="margin-left: 10px;">PRINT</button>
                    {{/if}}
                </td>
            </tr>
        </table>
    </fieldset>

</form>