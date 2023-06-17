<?php /* Smarty version 2.6.0, created on 2020-02-05 15:41:02
         compiled from special_lab/splab-ecg-result.tpl */ ?>
<div style="padding: 10px;">
    <center><span style="color: #880000;"><strong><?php echo $this->_tpl_vars['sMessage']; ?>
</strong></span></center>
    <h3 align="center">ECG OFFICIAL RESULT FORM</h3>
    <hr/>

    <?php echo $this->_tpl_vars['sFormStart']; ?>

    <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td width="65px;"><strong>Name:</strong></td>
            <td width="200px"><?php echo $this->_tpl_vars['sName']; ?>
</td>
            <td width="50px"><strong>Date:</strong></td>
            <td>
                <input type="text" name="ecg_date" id="ecg_date" style="font: bold 12px Arial;"
                       maxlength="10" size="10" value="<?php echo $this->_tpl_vars['sDate']; ?>
"/>
                <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                     id="ecg_trigger" src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
            <td><?php echo $this->_tpl_vars['sAge']; ?>
</td>
            <td><strong>Sex:</strong></td>
            <td><?php echo $this->_tpl_vars['sSex']; ?>
</td>
        </tr>
        <tr>
            <td><strong>Address:</strong></td>
            <td colspan="3"><?php echo $this->_tpl_vars['sAddress']; ?>
</td>
        </tr>
        <tr>
            <td><strong>Clinic:</strong></td>
            <td colspan="3"><?php echo $this->_tpl_vars['sClinic']; ?>
</td>
        </tr>

    </table>
    <br/><br/>
    <table>
        <tr>
            <td align="right" style="font-weight: bold">Rhythm:</td>
            <td><?php echo $this->_tpl_vars['sRhythm']; ?>
</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Axis:</td>
            <td><?php echo $this->_tpl_vars['sAxis']; ?>
</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Atrial Rate:</td>
            <td><?php echo $this->_tpl_vars['sAtrial']; ?>
 BPM</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Ventricular Rate:</td>
            <td><?php echo $this->_tpl_vars['sVentri']; ?>
 BPM</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">PR Interval:</td>
            <td><?php echo $this->_tpl_vars['sInterval']; ?>
 SEC</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">QRS:</td>
            <td><?php echo $this->_tpl_vars['sQrs']; ?>
 SEC</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">QT:</td>
            <td><?php echo $this->_tpl_vars['sQt']; ?>
 SEC</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Position:</td>
            <td><?php echo $this->_tpl_vars['sPosition']; ?>
</td>
        </tr>
    </table>
    <br/><br/>
    <div class="container">
        <fieldset>
            <legend>Impression:</legend>
            <?php echo $this->_tpl_vars['sEcgAbbre']; ?>

            <?php echo $this->_tpl_vars['sImpression']; ?>

            <?php echo $this->_tpl_vars['sHiddenEcgAbbre']; ?>

        </fieldset>
    </div>
    <br/><br/>
    <table>
        <tr>
            <td align="right" style="font-weight: bold">Prepared By:</td>
            <td><?php echo $this->_tpl_vars['sPreparedBy']; ?>
</td>
        </tr>
    </table>
    <hr/>
    <center><?php echo $this->_tpl_vars['sButtons']; ?>
</center>
    <?php echo $this->_tpl_vars['sFormEnd']; ?>

</div>