<?php /* Smarty version 2.6.0, created on 2020-09-09 09:34:32
         compiled from industrial_clinic/seg-ic-cert-vaccination.tpl */ ?>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<table cellspacing="0" cellpadding="0" align="center" width="100%">
<tr>
    <td>&nbsp;</td>
</tr>
<tr>
    <td colspan="*" style="padding-left: 20px;" background="images/top_05.jpg"><?php echo $this->_tpl_vars['sMsg']; ?>
</td>
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
                <td class="td2"><?php echo $this->_tpl_vars['sName']; ?>
</td>
                <td colspan="*"></td>
            </tr>
            <tr>
                <td class="td1">HRN:</td>
                <td class="td2"><?php echo $this->_tpl_vars['sHrn']; ?>
</td>
                <td class="td1">Case Number:</td>
                <td class="td2"><?php echo $this->_tpl_vars['sCase']; ?>
</td>
            </tr>
            <tr>
                <td class="td1">Age:</td>
                <td class="td2"><?php echo $this->_tpl_vars['sAge']; ?>
</td>
                <td class="td1">Sex:</td>
                <td class="td2"><?php echo $this->_tpl_vars['sSex']; ?>
</td>
            </tr>
            <tr>
                <td class="td1">Civil Status:</td>
                <td class="td2"><?php echo $this->_tpl_vars['sStatus']; ?>
</td>
                <td class="td1">Nationality:</td>
                <td class="td2"><?php echo $this->_tpl_vars['sNationality']; ?>
</td>
            </tr>
            <tr>
                <td class="td1">Address:</td>
                <td colspan="3" class="td2"><?php echo $this->_tpl_vars['sAddress']; ?>
</td>
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
                <td align="right"><?php echo $this->_tpl_vars['tDose1']; ?>
</td>
                <td class="td1">
                    First Dose:
                </td>
                <td class="td2">
                    <input type="text" name="first_tetanus" id="first_tetanus" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="<?php echo $this->_tpl_vars['val1']; ?>
"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="tetanus_trigger1" src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
                        <?php echo $this->_tpl_vars['tOption1']; ?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_tpl_vars['tDose2']; ?>
</td>
                <td class="td1">
                    Second Dose:
                </td>
                <td class="td2">
                    <input type="text" name="second_tetanus" id="second_tetanus" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="<?php echo $this->_tpl_vars['val2']; ?>
"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="tetanus_trigger2" src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
                        <?php echo $this->_tpl_vars['tOption2']; ?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_tpl_vars['tDose3']; ?>
</td>
                <td class="td1">
                    Third Dose:
                </td>
                <td class="td2">
                    <input type="text" name="third_tetanus" id="third_tetanus" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="<?php echo $this->_tpl_vars['val3']; ?>
"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="tetanus_trigger3" src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
                        <?php echo $this->_tpl_vars['tOption3']; ?>

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
                <td align="right"><?php echo $this->_tpl_vars['hDose1']; ?>
</td>
                <td class="td1">
                    First Dose:
                </td>
                <td class="td2">
                    <input type="text" name="first_hepatitis" id="first_hepatitis" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="<?php echo $this->_tpl_vars['val4']; ?>
"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="hepatitis_trigger1"
                         src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
                        <?php echo $this->_tpl_vars['hOption1']; ?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_tpl_vars['hDose2']; ?>
</td>
                <td class="td1">
                    Second Dose:
                </td>
                <td class="td2">
                    <input type="text" name="second_hepatitis" id="second_hepatitis" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="<?php echo $this->_tpl_vars['val5']; ?>
"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="hepatitis_trigger2"
                         src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
                        <?php echo $this->_tpl_vars['hOption2']; ?>

                    </select>
                </td>
            </tr>
            <tr>
                <td align="right"><?php echo $this->_tpl_vars['hDose3']; ?>
</td>
                <td class="td1">
                    Third Dose:
                </td>
                <td class="td2">
                    <input type="text" name="third_hepatitis" id="third_hepatitis" style="font: bold 12px Arial;"
                           maxlength="10" size="10" value="<?php echo $this->_tpl_vars['val6']; ?>
"/>
                    <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                         id="hepatitis_trigger3"
                         src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
                        <?php echo $this->_tpl_vars['hOption3']; ?>

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
                        <?php echo $this->_tpl_vars['vacc_cert2']; ?>

                    </select>
                </td>
                <td class="td1">
                    Noted by:
                </td>
                <td class="td2">
                    <select name="vacc_cert" id="in-charge">
                        <?php echo $this->_tpl_vars['vacc_cert']; ?>

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
        <?php echo $this->_tpl_vars['sButtons']; ?>

        <?php echo $this->_tpl_vars['sEncRef']; ?>

        <?php echo $this->_tpl_vars['sMode']; ?>

    </td>
</tr>
</table>
<?php echo $this->_tpl_vars['sFormEnd']; ?>