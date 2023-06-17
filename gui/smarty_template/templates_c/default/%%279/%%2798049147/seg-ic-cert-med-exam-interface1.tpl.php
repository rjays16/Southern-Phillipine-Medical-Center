<?php /* Smarty version 2.6.0, created on 2020-02-05 13:14:15
         compiled from industrial_clinic/seg-ic-cert-med-exam-interface1.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'html_radios', 'industrial_clinic/seg-ic-cert-med-exam-interface1.tpl', 303, false),array('function', 'html_options', 'industrial_clinic/seg-ic-cert-med-exam-interface1.tpl', 338, false),)), $this); ?>
<?php if (count($_from = (array)$this->_tpl_vars['errors'])):
    foreach ($_from as $this->_tpl_vars['error']):
?>
    <div class="alert-danger"><strong><i>&cross;</i> Error:</strong> <span><?php echo $this->_tpl_vars['error']; ?>
</span></div>
<?php endforeach; unset($_from); endif; ?>
<?php echo $this->_tpl_vars['message']; ?>

<div id="hospital-info">
    <p><?php echo $this->_tpl_vars['hospitalInfo']['hosp_country']; ?>
</p>
    <p><?php echo $this->_tpl_vars['hospitalInfo']['hosp_agency']; ?>
</p>
    <p>Center for Health Development - Davao Region</p>
    <p><?php echo $this->_tpl_vars['hospitalInfo']['hosp_name']; ?>
</p>
</div>
<form method="POST">
    <fieldset>
        <legend>General Data:</legend>
        <table width="100%">
            <tr width="90%">
                <td width="25%" class="text">
                    <label for="fname">First Name: </label><br>
                    <input class="name" type="text" name="fname" id="fname" value="<?php echo $this->_tpl_vars['firstName']; ?>
" readonly>
                </td>
                <td width="25%" class="text">
                    <label for="lname">Last Name: </label><br>
                    <input class="name" type="text" name="lname" id="lname" value="<?php echo $this->_tpl_vars['lastName']; ?>
" readonly>
                </td>
                <td width="25%" class="text">
                    <label for="mname">Middle Name: </label><br>
                    <input class="name" type="text" name="mname" id="mname" value="<?php echo $this->_tpl_vars['middleName']; ?>
" readonly>
                </td>
                <td width="25%" class="text">
                    <label for="position">Job Position: </label><br>
                    <input class="name" type="text" name="position" id="position" value="<?php echo $this->_tpl_vars['position']; ?>
" readonly>
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
            <?php echo $this->_tpl_vars['conditions']; ?>

        </table>
        <hr>
        <table>
            <tr>
                <td><label for="personalhist">Personal History: </label><br>
                        <textarea name="content[1]" cols="80" rows="3"
                                  class="textbox"><?php echo $this->_tpl_vars['personalHistory']; ?>
</textarea>
                </td>
            </tr>
            <tr>
                <td><label for="familyhist">Family History: </label><br><textarea name="content[2]" cols="80"
                                                                                  rows="3"
                                                                                  class="textbox"><?php echo $this->_tpl_vars['familyHistory']; ?>
</textarea>
                </td>
            </tr>
            <tr>
                <td><label for="immunizationhist">Immunization History: </label><br><textarea name="content[3]"
                                                                                              cols="80" rows="3"
                                                                                              class="textbox"><?php echo $this->_tpl_vars['immunizationHistory']; ?>
</textarea> 
                </td>
            </tr>
            <tr>
                <td><label for="historyIllness">History of Present Illness: </label><br><textarea name="content[7]"
                                                                                              cols="80" rows="3"
                                                                                              class="textbox"><?php echo $this->_tpl_vars['historyPresentIllness']; ?>
</textarea>
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
                           value="<?php echo $this->_tpl_vars['height']; ?>
">
                </td>
                <td width="15%">
                    <label for="weight">Weight</label><br>
                    <input type="text" name="weight" id="weight" class="textbox"
                           value="<?php echo $this->_tpl_vars['weight']; ?>
">
                </td>
                <td width="15%">
                    <label for="bp">Blood Pressure</label><br>
                    <input type="text" name="bp" id="bp" class="textbox"
                           value="<?php echo $this->_tpl_vars['bloodPressure']; ?>
">
                </td>
                <td width="15%">
                    <label for="pr">Pulse Rate</label><br>
                    <input type="text" name="pr" id="pr" class="textbox"
                           value="<?php echo $this->_tpl_vars['pulseRate']; ?>
">
                </td>
                <td width="15%">
                    <label for="rr">Respiratory Rate</label><br>
                    <input type="text" name="rr" id="rr" class="textbox"
                           value="<?php echo $this->_tpl_vars['respiratoryRate']; ?>
">
                </td>
                <td width="15%">
                    <label for="bodybuilt">Body Built BMI</label><br>
                    <input type="text" name="bodybuilt" id="bodybuilt" class="textbox"
                           value="<?php echo $this->_tpl_vars['bmi']; ?>
">
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
                                                     value="1" <?php echo $this->_tpl_vars['visual1']; ?>
><br><input type="radio" name="visual"
                                                                                          id="odwc"
                                                                                          value="2" <?php echo $this->_tpl_vars['visual2']; ?>
>
                </td>
                <td width="5%" align="center"><input type="radio" name="visual" id="oswoc"
                                                     value="3" <?php echo $this->_tpl_vars['visual3']; ?>
><br><input type="radio" name="visual"
                                                                                          id="oswoc"
                                                                                          value="4" <?php echo $this->_tpl_vars['visual4']; ?>
>
                </td>
                <td width="25%" align="center">Adequate <input type="radio" name="ishihara" id="ishihara_adequate"
                                                               value="1" <?php echo $this->_tpl_vars['ishi1']; ?>
> <br>Defective <input
                            type="radio" name="ishihara" id="ishihara_defective" value="2" <?php echo $this->_tpl_vars['ishi2']; ?>
></td>
                <td width="25%" align="center">AD <input type="radio" name="hearing" id="hearing_ad"
                                                         value="1" <?php echo $this->_tpl_vars['hear1']; ?>
> <br>AS <input type="radio"
                                                                                                name="hearing"
                                                                                                id="hearing_as"
                                                                                                value="2" <?php echo $this->_tpl_vars['hear2']; ?>
>
                </td>
                <td width="25%" align="center">Adequate <input type="radio" name="speech" id="speech_adequate"
                                                               value="1" <?php echo $this->_tpl_vars['speech1']; ?>
> <br>Defective <input
                            type="radio" name="speech" id="speech_defective" value="2" <?php echo $this->_tpl_vars['speech2']; ?>
></td>
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
            <?php echo $this->_tpl_vars['physical']; ?>

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
            <?php echo $this->_tpl_vars['diagnostic']; ?>

        </table>
    </fieldset>
    <fieldset>
        <legend>OTHER ROUTINE MECIDAL EXAMINATION/S:</legend>
        <table width="100%">
            <tr width="90%">
                <td>a.Dental</td>
                <td><textarea name="content[4]" id="dental" rows="3" cols="80"
                              class="textbox"><?php echo $this->_tpl_vars['aDental']; ?>
</textarea>
                </td>
            </tr>
            <tr width="90%">
                <td>b.Optha</td>
                <td><textarea name="content[5]" id="optha" rows="3" cols="80"
                              class="textbox"><?php echo $this->_tpl_vars['bOptha']; ?>
</textarea>
                </td>
            </tr>
            <tr width="90%">
                <td>c.ENT</td>
                <td><textarea name="content[6]" id="ent" rows="3" cols="80"
                              class="textbox"><?php echo $this->_tpl_vars['cEnt']; ?>
</textarea>
                </td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>IV. FINAL DIAGNOSIS</legend>
            <textarea name="final_diagnosis" id="final_diagnosis" rows="5" cols="80"
                      placeholder="Enter Final Diagnosis here..."
                      class="textbox"><?php echo $this->_tpl_vars['finalDiagnosis']; ?>
</textarea>
    </fieldset>
        <fieldset>
        <legend>V. Remarks</legend>
            <textarea name="remarks_final" id="remarks_final" rows="5" cols="80"
                      placeholder="Enter Remarks here..."
                      class="textbox"><?php echo $this->_tpl_vars['remarks']; ?>
</textarea>
    </fieldset>
        <fieldset>
        <legend>VI. TREATMENT</legend>
            <textarea name="treatment" id="treatment" rows="5" cols="80" placeholder="Enter Treatment here..."
                      class="textbox"><?php echo $this->_tpl_vars['treatment']; ?>
</textarea>
    </fieldset>

    <fieldset>
        <legend>VII. RECOMMENDATION</legend>
        <table width="100%">
            <tr width="90%">
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_1" value="1" <?php echo $this->_tpl_vars['a_checked']; ?>
>
                    <label for="recommendation_1">CLASS A</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_2" value="2" <?php echo $this->_tpl_vars['bRecommendationChecked']; ?>
>
                    <label for="recommendation_2">CLASS B</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_3" value="3" <?php echo $this->_tpl_vars['cRecommendationChecked']; ?>
>
                    <label for="recommendation_3">CLASS C</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_4" value="4" <?php echo $this->_tpl_vars['dRecommendationChecked']; ?>
>
                    <label for="recommendation_4">CLASS D</label>
                </td>
                <td width="20%">
                    <input type="radio" name="recommendation" id="recommendation_5" value="5" <?php echo $this->_tpl_vars['eRecommendationChecked']; ?>
>
                    <label for="recommendation_5">Pending, for further evaluation</label>
                </td>
            </tr>
        </table>
    </fieldset>

        <fieldset>
        <legend>Other Clinical Findings</legend>
        <div id="other-clinical-findings">
            <?php if (count($_from = (array)$this->_tpl_vars['otherClinicalFindings'])):
    foreach ($_from as $this->_tpl_vars['finding']):
?>
                <div class="panel">
                    <div class="title-bar"><span><?php echo $this->_tpl_vars['finding']['name']; ?>
</span></div>
                    <div class="content">

                        <?php if (isset ( $this->_tpl_vars['finding']['left_remark'] )): ?>
                            <div class="input-box">
                                <label class="label"
                                       for="exam_left_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
"><?php echo $this->_tpl_vars['finding']['left_remark']; ?>
</label>
                                <span>:</span>
                                <input id="exam_left_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
" name="exam[<?php echo $this->_tpl_vars['finding']['id']; ?>
][left_remark]"
                                       type="text" value="<?php echo $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['left_remark']; ?>
"/>
                            </div>
                        <?php endif; ?>

                        <?php if (isset ( $this->_tpl_vars['finding']['right_remark'] )): ?>
                            <div class="input-box">
                                <label class="label"
                                       for="exam_right_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
"><?php echo $this->_tpl_vars['finding']['right_remark']; ?>
</label>
                                <span>:</span>
                                <input id="exam_right_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
" name="exam[<?php echo $this->_tpl_vars['finding']['id']; ?>
][right_remark]"
                                       type="text" value="<?php echo $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['right_remark']; ?>
"/>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['finding']['has_result'] != 0): ?>
                            <div class="input-box">
                                <label class="label">Result</label>
                                <span>:</span>
                                <?php echo smarty_function_html_radios(array('name' => "exam[".($this->_tpl_vars['finding']['id'])."][result]",'options' => $this->_tpl_vars['otherClinicalFindingsResultOptions'],'separator' => '&nbsp;','selected' => $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['result']), $this);?>

                            </div>
                        <?php endif; ?>

                        <div class="input-box">
                            <label class="label" for="exam_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
">Remarks</label>
                            <span>:</span>
                            <input id="exam_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
" name="exam[<?php echo $this->_tpl_vars['finding']['id']; ?>
][remark]" type="text"
                                   value="<?php echo $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['remark']; ?>
"/>
                        </div>

                        <?php if ($this->_tpl_vars['finding']['code'] == 'eye'): ?>
                            <h4>w/ Corrected Glasses</h4>
                            <div class="input-box">
                                <label class="label"
                                       for="exam_glass_left_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
">OD</label>
                                <span>:</span>
                                <input id="exam_glass_left_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
"
                                       name="exam[<?php echo $this->_tpl_vars['finding']['id']; ?>
][glass_left_remark]" type="text"
                                       value="<?php echo $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['glass_left_remark']; ?>
"/>
                            </div>
                            <div class="input-box">
                                <label class="label"
                                       for="exam_glass_right_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
">OS</label>
                                <span>:</span>
                                <input id="exam_glass_right_remark_<?php echo $this->_tpl_vars['finding']['id']; ?>
"
                                       name="exam[<?php echo $this->_tpl_vars['finding']['id']; ?>
][glass_right_remark]" type="text"
                                       value="<?php echo $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['glass_right_remark']; ?>
"/>
                            </div>
                        <?php endif; ?>

                        <?php if ($this->_tpl_vars['finding']['with_dr_sig'] != 0): ?>
                            <div class="input-box">
                                <label class="label">Physician</label>
                                <span>:</span>
                                <?php echo smarty_function_html_options(array('class' => "combo-box",'name' => "exam[".($this->_tpl_vars['finding']['id'])."][physician_nr]",'options' => $this->_tpl_vars['physicianOptions'],'selected' => $this->_tpl_vars['personOtherClinicalFindings'][$this->_tpl_vars['finding']['id']]['physician_nr']), $this);?>

                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; unset($_from); endif; ?>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Medical Officer</legend>
        <table width="100%">
            <tr width="90%" align="center">
                <!-- <td align="center"><?php echo $this->_tpl_vars['medicalOfficer']; ?>
</td> -->
                <td align="center">
                <?php echo smarty_function_html_options(array('class' => "combo-box",'name' => 'physician_nr','id' => 'physician_nr','options' => $this->_tpl_vars['physicianOptions'],'selected' => $this->_tpl_vars['medicalOfficerNr']), $this);?>

                </td>
            </tr>
            <tr>
                <input type="hidden" value="<?php echo $this->_tpl_vars['pid']; ?>
" name="pid">
                <input type="hidden" value="<?php echo $this->_tpl_vars['encounter_nr']; ?>
" name="encounter_nr">
                <td align="center">
                    <?php if (! $this->_tpl_vars['hasSavedInfo']): ?>
                        <input type="submit" value="SAVE" class="myButton"><input type="hidden" value="save" name="mode" id="mode">
                    <?php endif; ?>
                    <?php if ($this->_tpl_vars['hasSavedInfo']): ?>
                        <input type="submit" value="UPDATE" class="myButton"><input type="hidden" value="update" name="mode" id="mode">
                        <button type="button" class="myButton" onclick="printMedChrt('<?php echo $this->_tpl_vars['encounter_nr']; ?>
','<?php echo $this->_tpl_vars['refno']; ?>
');" style="margin-left: 10px;">PRINT</button>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </fieldset>

</form>