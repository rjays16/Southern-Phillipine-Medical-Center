<!-- ICD10 ENTRY BLOCK -->
<div id="icdSearchTab" style="border:0px solid black; padding:2px; background-color:#FFFFFF; width:100%; position:relative; display:block" align="center">	
	<table border="0" cellpadding="0" style="width:100%">
		<tr>
		  <td width="35%" valign="top">		
				<div style="width:100%;height:139px;overflow:hidden;border:1px solid black;">
				<div style="width:100%;height:140px;overflow:scroll;border:1px solid black">
				
					<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:10px">
						<thead>
							<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader">
								<th width="40%" align="center">
									<strong>ICD code</strong>&nbsp;</th>
								<th width="50%" nowrap align="right">							
									<label id="icdTypeName">Principal Diagnosis</label>
									<input id="icdType" name="icdType" type="checkbox" onclick="setType(0)" value="" checked />
									<!--<input id="icdCode" name="icdCode" height="10" type="text" value="" onfocus="setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" width="5" maxlength="11">-->
									<!--<div id="icdCodeautocomplete">-->
									<!--	
										<input id="icdCode" name="icdCode" height="10" type="text" value="" onfocus="setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" width="5" maxlength="11">
										<div id="icdCodecontainer" style="position:absolute"></div>
									-->
									
									<!--<input type="text" size="20" value="" id="icdCode" name="icdCode" onkeyup="lookup(this.value);autoComplete(this,document.getElementById('autoSuggestionsList'),'value','true');" onfocus="if (this.value>=1){showDiv();} setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);fill();" />-->
									<!--<input type="text" size="20" value="" id="icdCode" name="icdCode" onkeyup="if (this.value!=0){xajax_populateICD_ICP('icd', this.value);autoComplete(this,document.getElementById('autoSuggestionsList'),'value','true');}else{hideDiv();}" onfocus="if (this.value>=1){showDiv();} setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);fill();" />-->
									<input type="text" size="20" value="" id="icdCode" name="icdCode" onkeyup="if (this.value!=''){xajax_populateICD_ICP('icd', this.value);}else{hideDiv('icd');}" onfocus="if (this.value>=1){showDiv('icd');} setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" />
									<div class="suggestionsBox" align="left" id="suggestions_icd" style="display: none; position:absolute; margin-top:-1px; margin-left:354px">
										<!--<img src="<?=$root_path?>images/upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />-->
										<div class="suggestionList" id="autoSuggestionsList_icd" align="left">
											&nbsp;
										</div>
										<!--
										<select id="autoSuggestionsList" onBlur="hideDiv();" style="width:148px;">
										</select>
										-->
									</div>
									
									<input id="hicdCode" type="hidden" value="">
								</th>
								<th width="225">
									<input id="btnAddIcdCode" height="10" type="button" value="Add" onFocus="hideDiv('icd');" onclick="if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific()){ prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }" style="width:100%">
								</th>
							</tr>
						</thead>
					</table>
					<table id="icdCodeTable" name="icdCodeTable" width="100%" border="0" cellpadding="0" cellspacing="1">
						<thead></thead>
						<tbody>
						 	 <!-- 
						 	   <tr></tr>
							  -->
						</tbody>
					</table>
				</div>
				</div>
			</td>
		</tr>	
	</table>
</div>
<!-- END: ICD10 BLOCK -->