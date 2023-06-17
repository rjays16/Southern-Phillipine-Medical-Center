<?php /* Smarty version 2.6.0, created on 2020-12-09 14:02:30
         compiled from system_admin/override/seg_override_main.tpl */ ?>
<!--<div align="center" style="width:95%"> -->
<div align="left" style="width:100%">
	<table width="100%" border="0" cellspacing="5" cellpadding="0">
		<tr>
			<td width="50%" valign="top">
				 <table border="0" cellpadding="2" cellspacing="2" width="100%">
					<tbody>
						<tr>
							<td class="segPanelHeader" colspan="3">Patient Information</td>
						</tr>
						<tr>
							<td class="segPanel" id="hpid" width="1%" nowrap="nowrap"><strong>Health Record Number</strong></td>
							<td class="jedPanel3" id="spid" width="50%"><?php echo $this->_tpl_vars['sPid']; ?>
</td>
							<td rowspan="8" class="photo_id"><?php echo $this->_tpl_vars['img_source']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Personnel No.</strong></td>
							<td class="jedPanel3" id="scase_no"><?php echo $this->_tpl_vars['sPersonnelNo']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Job Function</strong></td>
							<td class="jedPanel3" id="scase_no"><?php echo $this->_tpl_vars['sJobFunction']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel" id="hcase_no"><strong>Case Number</strong></td>
							<td class="jedPanel3" id="scase_no"><?php echo $this->_tpl_vars['sEncNrPID']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Title</strong></td>
							<td class="jedPanel3"><?php echo $this->_tpl_vars['title']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Family Name</strong></td>
							<td class="jedPanel3"><?php echo $this->_tpl_vars['name_last']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Given Name</strong></td>
							<td class="jedPanel3"><?php echo $this->_tpl_vars['name_first']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Gender</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sSexType']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Date of Birth</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sBdayDate']; ?>
 &nbsp; <?php echo $this->_tpl_vars['sCrossImg']; ?>
 &nbsp; <font color="black"><?php echo $this->_tpl_vars['sDeathDate']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Place of Birth</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sBirthPlace']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Age</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sAge']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Civil Status</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sCivilStat']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Religion</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sReligion']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Occupation</strong></td>
							<td class="jedPanel3"  colspan="2"><?php echo $this->_tpl_vars['sOccupation']; ?>
</font></td>
						</tr>
						<tr>
							<td class="segPanel"><strong>Address</strong></td>
							<td class="jedPanel3" colspan="2"><?php echo $this->_tpl_vars['sAddress']; ?>
</font></td>
						</tr>
						<tr id="senior_row" style="display:none">
							<td class="segPanel" id="tdsenior1"><strong><?php echo $this->_tpl_vars['sSeniorLabel']; ?>
</strong></td>
							<td class="jedPanel3" id="tdsenior2"><?php echo $this->_tpl_vars['sSeniorID']; ?>
</td>
						</tr>
						<tr>
							<td class="segPanelHeader" colspan="3">Admitting Diagnosis</td>
						</tr>
						<tr>
							<td id="admitting_diagnosis" class="jedPanel3" colspan="3"></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td width="50%" valign="top">
			<!--Added by borj System Admin (Overriding of Test Request) 2014-26-06-->
				 <table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<div id= "ifOnlybilled" class ="dashlet"  >
				 			<button class="jedInput" id="updateprofile" style="margin-left:8px" onclick="setValue()">Show Billing</button>
				 			<button class="jedInput" id="updateprofile" style="margin-left:8px" onclick="showBillWithAllDiscounts()">Show Billing Discount</button>
				 			<button class="jedInput" id="updateprofile" style="margin-left:8px" onclick="applyBillDiscount()">Apply Billing Discount</button>
				 			<button class="jedInput" id="updateprofile" style="margin-left:8px;" onclick="deleteDiscount()">Delete Billing Discount</button>
				 		</div>
				 	</tr>
					<tr>
			<!--end-->
						<td valign="top">
							<div id="requests" class="dashlet" align="left" style="width:100%">
								<table class="dashletHeader" width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td width="99%" nowrap="nowrap"><h1>List of current requests</h1></td>
										</tr>
								</table>
							</div>
							<?php echo $this->_tpl_vars['sRequestList']; ?>

						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<?php echo $this->_tpl_vars['sTailScripts']; ?>

<?php echo $this->_tpl_vars['sTailScripts2']; ?>

<!--</form>-->