<?php /* Smarty version 2.6.0, created on 2020-03-11 10:49:15
         compiled from special_lab/splab-eeg-result.tpl */ ?>
<div style="padding: 10px;">
    <center><span style="color: #880000;"><strong><?php echo $this->_tpl_vars['sMessage']; ?>
</strong></span></center>
    <h3 align="center">EEG OFFICIAL RESULT FORM</h3>
    <hr/>

    <?php echo $this->_tpl_vars['sFormStart']; ?>

    <table cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td><strong>HRN:</strong></td>
            <td><?php echo $this->_tpl_vars['sHRN']; ?>
</td>
        </tr>
        <tr>
            <td width="65px;"><strong>Name:</strong></td>
            <td width="200px"><?php echo $this->_tpl_vars['sName']; ?>
</td>
            <td width="50px"><strong>Date:</strong></td>
            <td>
                <input type="text" name="perform_date" id="perform_date" style="font: bold 12px Arial;"
                       maxlength="10" size="10" value="<?php echo $this->_tpl_vars['sDate']; ?>
"/>
                <img height="22" border="0" align="absmiddle" width="26" style="cursor: pointer;"
                     id="eeg_trigger" src="<?php echo $this->_tpl_vars['sImgCalendar']; ?>
"/>
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
            <td><?php echo $this->_tpl_vars['sAge']; ?>
</td>
            <td><strong>Sex:</strong></td>
            <td><?php echo $this->_tpl_vars['sSex']; ?>
</td>
        </tr>

    </table>
    <br/><br/>
    <table cellpadding="3">
        <tr>
            <td align="right" style="font-weight: bold">Service Name:</td>
            <td><?php echo $this->_tpl_vars['sService']; ?>
</td>
        </tr>
        <tr>
            <td><strong>Requesting Physician:</strong></td>
            <td colspan="3"><?php echo $this->_tpl_vars['sRequestDoc']; ?>
</td>
        </tr>
    </table>
    <table cellspacing="10">
        <tr>
            <td align="right" style="font-weight: bold">Clinical Data:</td>
            <td colspan="4"><?php echo $this->_tpl_vars['sClinical']; ?>
</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Medications:</td>
            <td><?php echo $this->_tpl_vars['sMedication']; ?>
</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Technical Summary:</td>
            <td><?php echo $this->_tpl_vars['sSummary']; ?>
</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Interpretation:</td>
            <td><?php echo $this->_tpl_vars['sInterpret']; ?>
</td>
        </tr>
    </table>
    <table cellpadding="5">
        <tr>
            <td align="right" style="font-weight: bold">Consulting Doctor:</td>
            <td><?php echo $this->_tpl_vars['sConsultDoc']; ?>
</td>
        </tr>
        <tr>
            <td align="right" style="font-weight: bold">Doctor Title:</td>
            <td><?php echo $this->_tpl_vars['sDoctorTitle']; ?>
</td>
        </tr>
    </table>
    <hr/>
    <center><?php echo $this->_tpl_vars['sButtons']; ?>
</center>
    <?php echo $this->_tpl_vars['sFormEnd']; ?>

</div>