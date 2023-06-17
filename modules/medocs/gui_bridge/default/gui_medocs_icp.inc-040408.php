<!-- ICPM ENTRY BLOCK -->
<div id="icpSearchTab" style="border:0px solid black; padding:2px; background-color:#FFFFFF; width:100%; position:relative; display:block" align="center">	
	<table border="0" cellpadding="0" style="width:100%">
		<tr>
			<td width="35%" valign="top">		
				<div style="width:100%;height:98px;overflow:hidden;border:1px solid black;">
				<div style="width:100%;height:100px;overflow:scroll;border:1px solid black">
		
					<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:10px">
						<thead>
							<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader">
								<th width="40%" align="center"><strong>ICP Code</strong>&nbsp;</th>
								<th width="50%" nowrap align="right">							
									<label id="icpTypeName">Principal Procedure</label>
									<input id="icpType" name="icpType" type="checkbox" onclick="setType(1)" value="" checked />
									<!--<input id="icpCode" name="icpCode" type="text" value="" onfocus="setKeyCode(1,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" width="5" maxlength="11">-->
									<!--<input id="icpCode" name="icpCode" type="text" value="" onfocus="setKeyCode(1,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" onkeyup="ajax_showOptions(this,'getCountriesByLetters',event);" width="5" maxlength="11">-->
									
									<input type="text" size="20" value="" id="icpCode" name="icpCode" onkeyup="if (this.value!=''){xajax_populateICD_ICP('icp', this.value);}else{hideDiv('icp');}" onfocus="if (this.value>=1){showDiv('icp');} setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" />
									<div class="suggestionsBox" align="left" id="suggestions_icp" style="display: none; position:absolute; margin-top:-1px; margin-left:360px">
										<!--<img src="<?=$root_path?>images/upArrow.png" style="position: relative; top: -12px; left: 30px;" alt="upArrow" />-->
										
										<div class="suggestionList" id="autoSuggestionsList_icp" align="left">
											&nbsp;
										</div>
									</div>
									
									<input id="hicpCode" type="hidden" value="">
								</th>
								<th width="225">
									<input id="btnAddIcpCode" type="button" value="Add" onFocus="hideDiv('icp');" onclick="if (checkDeptDocProcedureERMode(<?=$encounter_type?>)){ prepareAddIcpCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }" style="width:100%">
								</th>
							</tr>
						</thead>
					</table>
					<table id="icpCodeTable" width="100%" border="0" cellpadding="0" cellspacing="1">
						<thead></thead>
						<tbody>
						 	 <!-- 
						 	    <tr> </tr>
							  -->
						</tbody>
						
					</table>
				</div>
				</div>
			</td>
		</tr>	
	</table>
</div>
<!-- END: ICPM BLOCK -->