<?php /* Smarty version 2.6.0, created on 2020-02-12 08:19:06
         compiled from registration_admission/seg-er-location.tpl */ ?>
<div align="center" style="font:bold 12px Tahoma; color:#990000; margin-top: 10px;"><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br />

<?php echo $this->_tpl_vars['sFormStart']; ?>


<table  border="0" cellspacing="2" cellpadding="2" width="95%" align="center">
	<tbody>
		<tr>
            <td class="segPanelHeader" width="*">
                Patient ER Location Details
            </td>
        </tr>
        <tr>
        	<td class="segPanel" align="center" valign="top">
        		<table width="95%" border="0" cellpadding="2" cellspacing="0">
        			<tr>
        				<td valign="center" width="28%" style="text-align: right;"><strong>Location</strong></td>
        				<td style="padding: 5px;"><?php echo $this->_tpl_vars['sERLocation']; ?>
</td>
        			</tr>
        			<tr>
        				<td valign="center" style="text-align: right;"><strong>Section</strong></td>
        				<td style="padding: 5px;"><?php echo $this->_tpl_vars['sERLobby']; ?>
</td>
        			</tr>
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
        	</td>
        </tr>
	</tbody>
</table>

<?php echo $this->_tpl_vars['sFormEnd']; ?>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>