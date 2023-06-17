<?php /* Smarty version 2.6.0, created on 2020-02-05 13:03:21
         compiled from billing/billing_transmittal_form.tpl */ ?>
<style>
.ui-dialog-titlebar-close {
  visibility: hidden;
}
</style>
<div align="center" style="font:bold 12px Tahoma; color:#990000; "><?php echo $this->_tpl_vars['sWarning']; ?>
</div><br>
<?php echo $this->_tpl_vars['sFormStart']; ?>

<div id="loadingBox" style="display:none;" align="center">
	<strong>Generating XML ...</strong><br>
	<img id="xmlLoading" src="../../images/ajax_bar.gif" />
</div>
<div id="xmlParams" style="display:none;" align="center">
    <table>
        <tr>
            <td align="right">Total bills count:</td>
            <td id="billsCount"><?php echo $this->_tpl_vars['billCount']; ?>
</td>
        </tr>
        <tr>
            <td align="right">Member Category:</td>
            <td>
                <select id="memcat">
                    <option value="all">All</option>
                    <option value="none">None</option>
                    <?php echo $this->_tpl_vars['memcats']; ?>

                </select>
            </td>
        </tr>
    </table>
</div>
<div id="mainTablediv" align="center">
		<table width="90%" cellpadding="2" cellspacing="2" id="mainTable" style="border-collapse:collapse; border:1px solid #a6b4c9; color:black">
		<tbody>
			<tr>
				<td width="88%" class="jedPanelHeader">
				TRANSMITTAL
				</td>
			</tr>
			<!-- Basic information -->
			<tr>
				<td rowspan="5" align="left" valign="top" class="segPanel">
					<table width="100%" border="0" cellpadding="2" cellspacing="0" style="font-size:11px">
						<tr class="segPanel">
							<td align="right" valign="middle"><strong>Insurance:</strong></td>
							<td width="38%" valign="middle">
								<?php echo $this->_tpl_vars['sHCareDesc']; ?>
</td>
							<td width="10%" align="left" valign="middle">
								<?php echo $this->_tpl_vars['sSelectHCare']; ?>
</td>
														<td align="right" valign="middle"><strong>Date:</strong></td>
														<td valign="middle" align="left"><?php echo $this->_tpl_vars['sDate'];  echo $this->_tpl_vars['sCalendarIcon']; ?>
</td>
<!--                            <td align="left"><?php echo $this->_tpl_vars['sDate']; ?>
<strong style="font-size:10px">mm/dd/yyyy</strong></td>  -->
						</tr>
						<tr class="segPanel">
							<td  width="67" align="right" valign="top"><strong>Address:</strong></td>
							<td><?php echo $this->_tpl_vars['sHCareAddress']; ?>
</td>
						</tr>
												<tr class="segPanel">
														<td align="right" valign="middle"><strong>Control No.:</strong></td>
														<td valign="middle" align="left"><?php echo $this->_tpl_vars['sTransmitNo']; ?>
</td>
												</tr>
												<tr class="segPanel">
														<td align="right" valign="top"><strong>Remarks:</strong></td>
														<td valign="middle" align="left"><?php echo $this->_tpl_vars['sRemarks']; ?>
</td>
												</tr>
						</table>
					</td>
			</tr>
		</tbody>
	</table>
	<table width="90%">
		<tbody id="tbl_transmit_details_header">
						<tr id="tbl_transmit_details_hdr_row1" <?php echo $this->_tpl_vars['sNoShowButtons']; ?>
>
								<td align="left" colspan="7">&nbsp;</td>
						</tr>
			<tr id="tbl_transmit_details_hdr_row2" <?php echo $this->_tpl_vars['sShowButtons']; ?>
>
				<td align="left" colspan="6"><?php echo $this->_tpl_vars['sBtnAddItem'];  echo $this->_tpl_vars['sBtnDelete'];  echo $this->_tpl_vars['sBtnPrintAll'];  echo $this->_tpl_vars['sAuditTrail']; ?>
</td>
								<td align="right"><?php echo $this->_tpl_vars['sCheckboxSur'];  echo $this->_tpl_vars['sCheckboxMed'];  echo $this->_tpl_vars['sCheckboxCas'];  echo $this->_tpl_vars['sBtnSummary'];  echo $this->_tpl_vars['btnXml'];  echo $this->_tpl_vars['sBtnPrint'];  echo $this->_tpl_vars['sBtnSave']; ?>
</td>
			</tr>
		</tbody>
	</table>
	<table id="transmit_details" class="segList" border="0" cellpadding="0" cellspacing="0" width="90%">
		<thead id="tbl_transmit_details_hdr">
			<tr>
								<th width="2%">&nbsp;</th>
								<th width="9%">Policy No.</th>
								<th width="9%">Classification</th>
								<th width="30%">Confinement</th>
								<th width="8%">Case No.</th>
								<th width="20%">Patient</th>
								<th width="10%">Claim</th>
								<th width="10%">Meds/XLO<br>Outside</th>
								<th width="10%">&nbsp;Action</th>
			</tr>
		</thead>
		<tbody id="tbl_transmit_details_body">
<?php echo $this->_tpl_vars['sTransmittalClaims']; ?>

		</tbody>
	</table>
	<br />
	<br />
</div>

<?php echo $this->_tpl_vars['jsCalendarSetup']; ?>

<?php echo $this->_tpl_vars['sHiddenItems']; ?>


<span style="font:bold 15px Arial"><?php echo $this->_tpl_vars['sDebug']; ?>
</span>

<!--ADDED by JEFF 06-06-17-->
<?php echo $this->_tpl_vars['hiddenFieldDelete']; ?>

<?php echo $this->_tpl_vars['sFormEnd']; ?>


<div id="formpromptdbox" style="display:none">
<div class="hd" align="left">Select PHIC Form</div>
<div class="bd">
		<form id="formdbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<tbody>
								<tr>
										<td align="right" width="*">Form:</td>
										<td align="left">
												<select id="forms_list" name="forms_list">
														<option value="">-Select Form-</option>
												</select>
									</td>
								</tr>
						</tbody>
				</table>
				<?php echo $this->_tpl_vars['sFormsHiddenInputs']; ?>

		</form>
</div>
</div>
<!-- ADDED by JEFF 06-03-17 -->
<div id="delete-div" title="Reason of Deletion" style="display:none;">
    <form id="delete-form">
         <fieldset>
            <legend>Reason of deletion:</legend>
            <select id="select-reason" onchange="transDeleteReason();">
            </select>
            <br/><br/>
            <!-- <input type="hidden" name="del_trans_reason" id="del_trans_reason"/> -->
            <textarea name="del_trans_other_reason" id="del_trans_other_reason" rows="5" style="width: 100%; display: none"></textarea>
        </fieldset>
    </form>
</div>
<!-- added by: syboy 06/23/2015 -->
<div id="cataractformpromt" style="display:none">
	<div class="hd" align="left">Application number for cataract</div>
	<div class="bd">
		<form id="cataractformbox" method="post" action="document.location.href">
			<div>
				<table>
						<tr>
							<td></td>
							<td></td>
							<td>Skip Code</td>
						</tr>
					<tbody id="inputs_cataractCodes">
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</tbody>
				</table>
			</div>
			<input type="hidden" class="segInput" id="enc_nr" name="enc_nr"/>			
		</form>
	</div>
</div>
<!-- end -->

<div id="memcategdialogbox"  style="display:none">
<div class="hd" align="left">Membership Category to Print</div>
<div class="bd">
		<form id="mcategdbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<tbody>
								<tr>
										<td align="center" width="45%">
												<select id="category_list" name="category_list">
														<option value="">-Select Category-</option>
												</select>
									</td>
										<td align="center" width="*"><input type="checkbox" id="is_detailed" name="is_detailed" value="">&nbsp;Detailed</td>

								</tr>
								<tr>
									<td align="left" width="*">
										<input type="checkbox" id="surgicalCase" name="surgicalCase" value="surgical" onclick="caseSurgical()" />&nbsp;Surgical
										<input type="checkbox" id="medicalCase" name="medicalCase" value="medical" onclick="caseMedical()" />&nbsp;Medical
										
									</td>
								
								</tr>
							
						</tbody>
					
				</table>
				
		</form>
	
</div>

</div>


<div id="dataeditbox" style="display:none">
<div class="hd" align="left">Update Encounter Information</div>
<div class="bd">
		<form id="dtaeditbox" method="post" action="document.location.href">
				<table width="100%" class="segPanel">
						<thead id="tbl_transmit_details_hdr">
								<tr>
										<th width="10%" align="left">Patient:</th>
										<th width="*" align="left" colspan="3"><input class="segInput" id="patientname" name="patientname" type="text" size="50" value="" disabled="disabled" readOnly></th>
								</tr>
						</thead>
						<tbody>
								<tr>
										<td align="left" width="10%" valign="middle">Member:</td>
										<td align="left" width="*" valign="middle" colspan="3">
												<input class="segInput" type="text" id="membernmlast" name="membernmlast" size="20" disabled="disabled"/><span>, </span>
												<input class="segInput" type="text" id="membernmfirst" name="membernmfirst" size="20" disabled="disabled"/>
												<input class="segInput" type="text" id="membernmmid" name="membernmmid" size="10" disabled="disabled"/>
												<span style="vertical-align:bottom; cursor:pointer"><img id="btn_editmem" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
/images/cashier_edit_3.gif" border="0" align="absmiddle" onclick="allowMbrEdit();"/></span>
										</td>
								</tr>
								<tr>
										<td align="left" width="10%">&nbsp;&nbsp;Address:</td>
										<td align="left" width="*" colspan="3"><input class="segInput" type="text" id="street_addr" name="street_addr" size="50" disabled="disabled"/></td>
								</tr>
								<tr>
										<td align="left" width="10%">&nbsp;&nbsp;&nbsp;</td>
										<td align="left" width="*" colspan="3">
											 <div id="barangay_autocomplete">
														<input type="text" size="25" value="" id="barangay" name="barangay" onblur="trimString(this);" disabled="disabled"/>
														<div id="barangay_container" style="width:30em"></div>
											 </div>
										</td>
								</tr>
								<tr>
										<td align="left" width="10%">&nbsp;&nbsp;&nbsp;</td>
										<td align="left" width="*" colspan="3">
											 <div id="municipality_autocomplete">
														<input type="text" size="25" value="" id="municipality" name="municipality" onblur="trimString(this);" disabled="disabled"/>
														<div id="municipality_container" style="width:45em"></div>
											 </div>
										</td>
								</tr>
								<tr>
										<td align="left"><label for="date">Discharge Date: </label></td>
										<td align="left" colspan="3"><?php echo $this->_tpl_vars['sDischargeDate']; ?>
&nbsp;&nbsp;<label for="date">Time (24-hour format): </label><input type="text" id="dischrgtme" name="dischrgtme" size="6" onblur="checkTimeInput(this.value);" /></td>
								</tr>
								<tr>
<!--										<td align="left" width="10%" colspan="2">Final Diagnosis:</td>-->
										<td width="10%" nowrap="nowrap" align="left">
											 <div id="icdAutoComplete">
														<input type="text" size="25" value="" id="icdCode" name="icdCode" onblur="trimString(this);" />
														<div id="icdContainer" style="width:35em"></div>
											 </div>
										</td>
										<td width="*" nowrap="nowrap" align="left">
											 <div style="width:auto;" id="icdDescAutoComplete">
														<input type="text" size="25" value="" id="icdDesc" name="icdDesc" onblur="trimString(this);" />
														<div id="icdDescContainer" style="width:45em"></div>
											 </div>
										</td>
										<td style="vertical-align:middle;" width="13%">
											<div style="vertical-align:middle;"><input type="checkbox" id="is_primary" name="is_primary" value=""><span style="vertical-align:top;">Primary?</span></div>
										</td>
										<td valign="top" align="left" width="8%"><img id="btn_adddiag" style="cursor:pointer" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
/images/his_addbtn.gif" border=0 onclick="if (checkICDSpecific() && (document.getElementById('icdCode').value!='')) { addICDCode(); }" ></td>
								</tr>
								<tr>
									<td colspan="4">
										<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
										<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
											<thead>
												<tr class="nav">
												<th colspan="9">
													<div id="d-pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE, dcurpage, dlastpage, dfunc)">
														<img title="First" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/start.gif" border="0" align="absmiddle"/>
														<span title="First">First</span>
													</div>
													<div id="d-pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE, dcurpage, dlastpage, dfunc)">
														<img title="Previous" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/previous.gif" border="0" align="absmiddle"/>
														<span title="Previous">Previous</span>
													</div>
													<div id="d-pageShow" style="float:left; margin-left:10px">
														<span></span>
													</div>
													<div id="d-pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE, dcurpage, dlastpage, dfunc)">
														<span title="Last">Last</span>
														<img title="Last" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/end.gif" border="0" align="absmiddle"/>
													</div>
													<div id="d-pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE, dcurpage, dlastpage, dfunc)">
														<span title="Next">Next</span>
														<img title="Next" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/next.gif" border="0" align="absmiddle"/>
													</div>
													<input id="d-search" name="d-search" type="hidden" />
												</th>
											</tr>
											</thead>
										</table>
										</div>
										<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:100px; width:100%; background-color:#e5e5e5">
											<table id="diagnosisList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
												<thead>
													<tr>
														<th width="10%" align="left">Code</th>
														<th width="40%" align="left">Diagnosis</th>
														<th width="*" align="left">Clinician</th>
														<th width="8%" align="center">Type</th>
														<th width="2%">&nbsp;</th>
													</tr>
												</thead>
												<tbody id="diagnosisList-body">
												</tbody>
											</table>
											<img id="d-ajax-loading" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
										</div>
										</td>
								</tr>
								<tr>
										<td align="left" width="10%" colspan="4">Services Performed:</td>
								</tr>
								<tr>
									<td colspan="4">
										<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; overflow-x:hidden; width:100%; background-color:#e5e5e5">
										<table class="segList" width="100%" border="0" cellpadding="0" cellspacing="0">
											<thead>
												<tr class="nav">
												<th colspan="10">
													<div id="p-pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE, pcurpage, plastpage)">
														<img title="First" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/start.gif" border="0" align="absmiddle"/>
														<span title="First">First</span>
													</div>
													<div id="p-pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
														<img title="Previous" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/previous.gif" border="0" align="absmiddle"/>
														<span title="Previous">Previous</span>
													</div>
													<div id="p-pageShow" style="float:left; margin-left:10px">
														<span></span>
													</div>
													<div id="p-pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
														<span title="Last">Last</span>
														<img title="Last" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/end.gif" border="0" align="absmiddle"/>
													</div>
													<div id="p-pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
														<span title="Next">Next</span>
														<img title="Next" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/next.gif" border="0" align="absmiddle"/>
													</div>
													<input id="p-search" name="p-search" type="hidden" />
												</th>
											</tr>
											</thead>
										</table>
										</div>
										<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll;overflow-x:hidden; height:100px; width:100%; background-color:#e5e5e5">
											<table id="proceduresList" class="segList" width="100%" border="0" cellpadding="0" cellspacing="0" style="overflow:auto">
												<thead>
													<tr>
														<th width="20%" align="left">Code</th>
														<th width="60%" align="left">Procedure</th>
														<th width="*" align="left">Operation Date</th>
													</tr>
												</thead>
												<tbody id="proceduresList-body">
												</tbody>
											</table>
											<img id="p-ajax-loading" src="<?php echo $this->_tpl_vars['sRootPath']; ?>
images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
										</div>
										</td>
								</tr>
								<tr>
									<td colspan="4">
										<div>
											<table>
												<tr>
												<td width="3%" align="left">Classification:</td>
												<td align="left" width="35%">
															<select id="entrycategory_list" name="entrycategory_list" onchange="jsCategoryOptionChange(this, this.options[this.selectedIndex].value, this.options[this.selectedIndex].text)">
																	<option value="">-Select Classification-</option>
															</select>
												</td>
												<td width="20%" align="right"><b>Policy No.:</b></td>
												<td align="left" width="*">
															<input type="text" size="25" value="" id="insurance_nr" name="insurance_nr" onblur="trimString(this);" />
												</td></tr>
											</table>
										</div>
									</td>
								</tr>
						</tbody>
				</table>
				<?php echo $this->_tpl_vars['sDataEditHiddenInputs']; ?>

		</form>
</div>
</div>
<?php echo $this->_tpl_vars['sTailScripts']; ?>
