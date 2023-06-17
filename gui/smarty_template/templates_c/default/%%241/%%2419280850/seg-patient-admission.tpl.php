<?php /* Smarty version 2.6.0, created on 2020-08-23 15:05:10
         compiled from registration_admission/seg-patient-admission.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>

    <table border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
        <tbody>
            <tr>
                <td class="segPanelHeader" width="*">
                    Refer/Transfer Details
                </td>
            </tr>
            <tr>
                <td rowspan="3" class="segPanel" align="center" valign="top">
                    <table width="95%" border="0" cellpadding="1" cellspacing="0" style="font-size:11px" >
                        <tr>
                            <td colspan="2"><strong>Transaction type</strong><?php echo $this->_tpl_vars['sRefer']; ?>
</td><td colspan="1" align=right><strong>Date: </strong><?php echo $this->_tpl_vars['sDate'];  echo $this->_tpl_vars['miniCalendar']; ?>
</td>
                        </tr>
                        <tr>
                            
                        </tr>
                    </table>
                    <table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
                        <tr>
                            <td valign="top" width="28%"><strong>&nbsp;&nbsp;Referral Number</strong></td>
                            <td><?php echo $this->_tpl_vars['sRefNo']; ?>
</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>&nbsp;&nbsp;Referring Doctor</strong></td>
                            <td><?php echo $this->_tpl_vars['sDoctor']; ?>
</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>&nbsp;&nbsp;Transfer to</strong></td>
                            <td><?php echo $this->_tpl_vars['sDept']; ?>
</td>
                        </tr>
                        <tr>
                           <td valign="top"><strong>&nbsp;&nbsp;Diagnosis</strong></td>
                            <td><?php echo $this->_tpl_vars['sDiagnosis']; ?>
</td>
                        </tr>
                        <tr>
                            <td valign="top"><strong>&nbsp;&nbsp;Notes</strong></td>
                            <td><?php echo $this->_tpl_vars['sNotes']; ?>
</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

<br>
    <div align="left" style="width:95%">
        <table width="100%">
            <tr>
                <td align="right">
                    <?php echo $this->_tpl_vars['sContinueButton']; ?>

                </td>
            </tr>
        </table>
    </div>
    
<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sIntialRequestList']; ?>

<br/>

<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>
<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sTailScripts']; ?>
     
<hr/>