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
								<th width="35%" align="center" valign="middle">
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<strong>ICD code</strong></th>
								<th width="55%" nowrap align="right">							
									<label id="icdTypeName">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Principal Diagnosis</label>
									&nbsp;&nbsp;
									<input id="icdType" name="icdType" type="checkbox" onclick="setType(0)" value="" checked />
									<!--<input id="icdCode" name="icdCode" height="10" type="text" value="" onfocus="setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" width="5" maxlength="11">-->
									
									<!--dojoType="dijit.form.ComboBox" -->
									<!--
									<select name="icdCode" id="icdCode"
                						dojoType="dojoType="dijit.form.ComboBox"
						               autocomplete="false"
						               value="" 
											onfocus="setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);"
					                  onChange="setVal1" 
											onKeyup="populateICD_ICP(this.id);">
						   	            <option id="A00.0" value="A00.0">A00.0</option>
												<option id="A00.1" value="A00.0">A00.1</option>
												<option id="D00.0" value="D00.0">D00.0</option>
												<option id="D00.1" value="D00.0">D00.1</option>
							        </select>
									-->
									<select dojoType="dijit.form.FilteringSelect" name="icdCode"  id="icdCode"
 									   autocomplete="false" value="">
										
									   <option value="A00.0" id="A00.0">A00.0</option>
								      <option value="A00.1" id="A00.1">A00.1</option>
								      <!--
										<option value="A00.9" id="A00.9">A00.9</option>
								      <option value="D09.7" id="D09.7">D09.7</option>
								      <option value="D09.9" id="D09.9">D09.9</option>
								      <option value="C96.7" id="C96.7">C96.7</option>
								      <option value="C96.9" id="C96.9">C96.9</option>
										<option value="E11.3" id="E11.3">E11.3</option>
								      <option value="E11.5" id="E11.5">C96.7</option>
								      <option value="E14.0" id="E14.0">E14.0</option>
										-->
									</select>
									<!--<select id="opt1" name="opt1"></select>-->
									<!--
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<select name="icdCode" id="icdCode"
                						dojoType="ComboBox"
						               value="" 
											onfocus="setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);"
					                  onKeyup="populateICD_ICP(this.id);" style="margin-top:-10px; margin-left:-5px ">
						   	            <option id="A00.0" value="A00.0">A00.0</option>
												<option id="A00.1" value="A00.0">A00.1</option>
							        </select>
									 --> 
									<input id="hicdCode" type="hidden" value="">
								</th>
								<th width="225">
									<input id="btnAddIcdCode" height="10" type="button" value="Add" onclick="if (checkDeptDocDiagnosisERMode(<?=$encounter_type?>) && checkICDSpecific()){ prepareAddIcdCode('<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>') }" style="width:100%">
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