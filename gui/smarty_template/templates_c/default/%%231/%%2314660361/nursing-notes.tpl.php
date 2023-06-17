<?php /* Smarty version 2.6.0, created on 2020-12-01 18:09:40
         compiled from nursing/nursing-notes.tpl */ ?>
<!-- <?php echo $this->_tpl_vars['sFormStart']; ?>
 -->
<?php echo $this->_tpl_vars['sFormNotes']; ?>

<head>
    <?php if (count($_from = (array)$this->_tpl_vars['javascripts'])):
    foreach ($_from as $this->_tpl_vars['script']):
?>
    <?php echo $this->_tpl_vars['script']; ?>

    <?php endforeach; unset($_from); endif; ?>
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
</head>

<div style="width:630px;">
		<table border="0" cellspacing="2" cellpadding="2" width="100%" align="center" style="font:12px Arial;">
			<tbody valign="middle">



				<!-- <tr>
					<td class="segPanelHeader" colspan="2">Notes</td>
				</tr> -->
				<tr>
					<td class="segPanelHeader" colspan="2">Patient's Details</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  id="table_notes" style="font:14px Arial">
							<tr>
								<td colspan="2">
								<table  width="100%" class="transaction_details_table" cellpadding="0" cellspacing="0" style="font:normal 14px Arial; padding:4px" >
									<tr>
										<td nowrap="nowrap"><strong>Name : </strong></td><td nowrap="nowrap"><?php echo $this->_tpl_vars['patient_name']; ?>
</td>
										<td><strong>Date : </strong></td><td><?php echo $this->_tpl_vars['pNotes_display']; ?>
</td>
										<?php echo $this->_tpl_vars['NotesDate']; ?>

									</tr>
									<tr>
										<td nowrap="nowrap"><strong>HRN : </strong></td><td><?php echo $this->_tpl_vars['sPatientID']; ?>
</td>
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>Case No. : </strong></td><td><?php echo $this->_tpl_vars['case_number']; ?>
</td>
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>Bed No. : </strong></td><td><?php echo $this->_tpl_vars['bedNumDisplay']; ?>

									</td>
									</tr>
									<tr>
										<td nowrap="nowrap"><strong>Room No. : </strong></td><td><?php echo $this->_tpl_vars['roomNumDisplay']; ?>
</td>
									</tr>
								</table>
								</td>
							</tr>
					</td>		
				</tr>

				<tr>
					<td class="segPanelHeader" colspan="2">Patient's Note</td>
				</tr>
				<tr>
					<td class="segPanel">
						<table border="0" cellpadding="2" cellspacing="2" width="100%" align="center"  id="table_notes" style="font:14px Arial">
							<tr>			
							
<!-- 								<td width="135px"><label  style="font:14px Arial;">Impression/Diagnosis:</label></td>
								<td><?php echo $this->_tpl_vars['impression']; ?>
</td> -->
								<td width="135px"><label  style="font:14px Arial;">Full Diagnosis:</label></td>
								<td><?php echo $this->_tpl_vars['impression']; ?>
</td> 
							</tr>
							<tr>
								<td><label style="font:14px Arial;">&nbsp</label></td>
							</tr>

							<tr>
								<td><label style="color: red; font-size: 16px">*</label><label style="font:14px Arial;">Diet:</label></td>
								<td><span style="<?php echo $this->_tpl_vars['brw_diet']; ?>
"><?php echo $this->_tpl_vars['diet']; ?>
</span><span style="<?php echo $this->_tpl_vars['brw_remarks']; ?>
">&nbsp;&nbsp;&nbsp;<?php echo $this->_tpl_vars['remarks']; ?>
</span></td>
								<!-- <td><?php echo $this->_tpl_vars['remarks']; ?>
</td> -->
							</tr>
							
							<tbody id="tb_notes">
								<?php echo $this->_tpl_vars['diet_list']; ?>

							</tbody>
							<tr>
								<td><?php echo $this->_tpl_vars['listBR']; ?>
</td>
								<td><?php echo $this->_tpl_vars['listBR']; ?>
</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">&nbsp</label></td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">IVF/Level/Due Time:</label></td>
								<td><?php echo $this->_tpl_vars['ivf']; ?>
</td>
							</tr>
			
							<tr>
								<td><label style="font:14px Arial;">Religion:</label></td>
								<td><?php echo $this->_tpl_vars['religion']; ?>
</td>
							</tr>
							<tr>
								<td><label style="color: red; font-size: 16px">*</label><label style="font:14px Arial;">Height:</label></td>
								<td><?php echo $this->_tpl_vars['height']; ?>
cm</td>
							</tr>
								<tr>
								<td><label style="color: red; font-size: 16px">*</label><label style="font:14px Arial;">Weight:</label></td>
								<td><?php echo $this->_tpl_vars['weight']; ?>
kg</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">BMI:</label></td>
								<td><?php echo $this->_tpl_vars['bmi_category']; ?>
</td>
							</tr>
								<tr>
								<td>&nbsp;</td>
							</tr>
								<?php if ($this->_tpl_vars['isICU'] != 'ICU'): ?>
							<tr>
								<td><label style="font:14px Arial;">Available Meds:</label></td>
								<td><?php echo $this->_tpl_vars['avail_meds']; ?>
</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">Other Gadgets Incl. Blood (Bag#, S#, Type):</label></td>
								<td><?php echo $this->_tpl_vars['gadgets']; ?>
</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">Problems/Meds/Msg/Others:</label></td>
								<td><?php echo $this->_tpl_vars['problems']; ?>
</td>
							</tr>
							<tr>
								<td><label style="font:14px Arial;">Actions:</label></td>
								<td><?php echo $this->_tpl_vars['actions']; ?>
</td>
							</tr>

							<tr>
							<!-- Start added  -->
							<?php endif; ?>
							<?php if ($this->_tpl_vars['isICU'] == 'ICU'): ?>
							<tr>
								<td width="135px"><label  style="font:14px Arial;">Service:</label></td>
								<td><?php echo $this->_tpl_vars['services']; ?>
</td><br>
							</tr>
						
							<tr>
								<td><label style="font:14px Arial;">&nbsp</label></td>
							</tr>
							<!-- <tr>
								<td><label style="font:14px Arial;">Attending Doctor:</label></td>
								<td><span style="<?php echo $this->_tpl_vars['nr']; ?>
"><?php echo $this->_tpl_vars['dept_nr']; ?>
</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="<?php echo $this->_tpl_vars['doc_name']; ?>
"><?php echo $this->_tpl_vars['dr_nr']; ?>
</span></td>
							</tr> -->

							<tr>
								<td width="135px"><label  style="font:14px Arial;">Other Gadgets Incl. Blood (Bag#,S#,Types):</label></td>
								<td><?php echo $this->_tpl_vars['other']; ?>
</td> 
							</tr>

							<tr>
								<td width="135px"><label  style="font:14px Arial;">Diagnostic Procedures:</label></td>
								<td><?php echo $this->_tpl_vars['diagnostic']; ?>
</td> 
							</tr>

							<tr>
								<td width="135px"><label  style="font:14px Arial;">Special Endorsement:</label></td>
								<td><?php echo $this->_tpl_vars['special']; ?>
</td> 
							</tr>
<!-- 
							<tr>
								<td width="135px"><label  style="font:14px Arial;">Additional Endorsement:</label></td>
								<td><?php echo $this->_tpl_vars['additional']; ?>
</td> 
							</tr> -->

							<tr>
								<td width="135px"><label  style="font:14px Arial;">VS/ I&O:</label></td>
								<td><?php echo $this->_tpl_vars['vs']; ?>
</td> 
							</tr>
							<?php endif; ?>
							<!-- End added  -->
							<?php if ($this->_tpl_vars['lastmod']): ?>
								<td><label style="font:14px Arial;">Last modified by:</label></td>
									<td><span><?php echo $this->_tpl_vars['lastmod']; ?>
</span></td>
									<tr>
									<td><label style="font:14px Arial;">Date/time:</label></td>
									<td><span><?php echo $this->_tpl_vars['datetime']; ?>
</span></td></tr>
							<?php endif; ?>
							</tr>
							<tr>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<!--<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td align="center"><?php echo $this->_tpl_vars['cancelBtn']; ?>
 </td>
				</tr>   -->
			</tbody>
		</table>
</div>
<?php echo $this->_tpl_vars['submitted']; ?>

<?php echo $this->_tpl_vars['encounter_nr']; ?>

<?php echo $this->_tpl_vars['pid']; ?>

<?php echo $this->_tpl_vars['ward']; ?>

<?php echo $this->_tpl_vars['ward_nr']; ?>

<?php echo $this->_tpl_vars['nBmi']; ?>

<?php echo $this->_tpl_vars['sHiddenInputs']; ?>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<!-- <?php echo $this->_tpl_vars['sFormEnd']; ?>
 -->
<?php echo $this->_tpl_vars['sTailScripts']; ?>