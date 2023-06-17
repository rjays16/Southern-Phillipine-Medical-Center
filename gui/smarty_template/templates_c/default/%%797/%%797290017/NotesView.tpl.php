<?php /* Smarty version 2.6.0, created on 2020-02-05 12:17:30
         compiled from ../../../../modules/dashboard/dashlets/PatientHistory/templates/NotesView.tpl */ ?>
<div align="center">
	<table width="100%" cellpadding="2" cellspacing="0" style="font:bold 12px Arial">
			<tr>
					<td><label style="font:bold 18px Arial">Subjective</label></td>
			</tr>
			<tr>
					<td class="segPanel">
							<table width="100%" cellpadding="2" cellspacing="0" style="font:bold 12px Arial">
									<tr>
											<td><label style="font:normal 16px Arial; color:#0000a0">Chief Complaint</label></td>
									</tr>
									<tr>
											<td><textarea rows="3" style="width:100%; font: normal 14px 'Courier New'; overflow:visible; margin-bottom:5px;" id="chief_complaint" class="clear"  readonly="readonly" spellcheck="false"><?php echo $this->_tpl_vars['chief_complaint']; ?>
</textarea></td>
									</tr>
							</table>
					</td>
			</tr>
			<tr>
					<td><label style="font:normal 18px Arial">Objective</label></td>
			</tr>
			<tr>
					<td class="segPanel">
							<table width="100%" cellpadding="2" cellspacing="0" style="font:bold 12px Arial">
									<tr>
											<td><label style="font:normal 16px Arial; color:#0000a0">Pertinent Physical Examination</label></td>
									</tr>
									<tr>
											<td><textarea rows="3" style="width:100%; font: normal 14px 'Courier New'; overflow:visible; margin-bottom:5px;" id="physical_examination" class="clear"  readonly="readonly" spellcheck="false"><?php echo $this->_tpl_vars['physical_examination']; ?>
</textarea></td>
									</tr>
							</table>
					</td>
			</tr>
			<tr>
					<td><label style="font:normal 18px Arial">Assessment</label></td>
			</tr>
			<tr>
					<td class="segPanel">
							<table width="100%" cellpadding="2" cellspacing="0" style="font:bold 12px Arial">
									<tr>
											<td><label style="font:normal 16px Arial; color:#0000a0">Diagnosis</label></td>
									</tr>
									<tr>
											<td><textarea rows="3" style="width:100%; font: normal 14px 'Courier New'; overflow:visible; margin-bottom:5px;" id="diagnosis" class="clear"  readonly="readonly"   spellcheck="false"><?php echo $this->_tpl_vars['diagnosis']; ?>
</textarea></td>
									</tr>
							</table>
					</td>
			</tr>
			<tr>
					<td><label style="font:normal 18px Arial">Plan</label></td>
			</tr>
			<tr>
					<td class="segPanel">
							<table width="100%" cellpadding="2" cellspacing="0" style="font:bold 12px Arial">
									<tr>
											<td><label style="font:normal 16px Arial; color:#0000a0">Progress Notes/Clinical Summary</label></td>
									</tr>
									<tr>
											<td><textarea rows="3" style="width:100%; font: normal 14px 'Courier New'; overflow:visible; margin-bottom:5px;" id="clinical_summary" class="clear"  readonly="readonly"  spellcheck="false"><?php echo $this->_tpl_vars['clinical_summary']; ?>
</textarea></td>
									</tr>
							</table>
					</td>
			</tr>
	</table>
</div>