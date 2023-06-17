<script src="js/ComboBox.js"></script>
<script>
	var AJAXTimerID=0;
	
	function makeCombobox(keyword){
		icdCode=new ComboBox("icdCode",$('icd'));
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		
		if (keyword==0)
			keyword = '';
		
		//AJAXTimerID = setTimeout("xajax_populateICD_ICP('icd')",50);
		AJAXTimerID = setTimeout("xajax_populateICD_ICP('icd','"+keyword+"')",50);
	}			
	
	function refreshCombo(keyword){
		//alert('keyword = '+keyword);
		//ajxClearSelection(len);
		if (AJAXTimerID) clearTimeout(AJAXTimerID);
		AJAXTimerID = setTimeout("xajax_populateICD_ICP('icd','"+keyword+"')",50);
	}
	
	
	function ajxClearSelection(){
		// Search for the source row table element
		var list=$('tableId'),dRows, dBody;
		if (list) {
			dBody=list.getElementsByTagName("tbody")[0];
			if (dBody) {
				dBody.innerHTML = "";
				return true;	// success
			}
			else return false;	// fail
		}
		else return false;	// fail
	}
	
		
	function appendToSelection(code, desc, i, keyword){
		//var list=$(listID);
		//alert(list);
		//makeCombobox(keyword);
		icdCode.add(
			new ComboBoxItem(code,desc,i)
		);		
		//document.getElementById('index').value = i;
	}
	
</script>

<div id="icdSearchTab" style="border:0px solid black; padding:2px; background-color:#FFFFFF; width:100%; position:relative; display:block" align="center">	
	<table border="0" cellpadding="0" style="width:100%">
		<tr>
		  <td width="35%" valign="top">		
				<div style="width:100%;height:139px;overflow:hidden;border:1px solid black;">
				<div style="width:100%;height:140px;overflow:scroll;border:1px solid black">
				
					<table width="100%" border="0" cellpadding="0" cellspacing="1" id="srcRowsTable" style="font-size:10px">
						<thead>
							<tr class="reg_list_titlebar" style="font-weight:bold " id="srcRowsHeader">
								<th width="40%" align="center"><strong>ICD code</strong>&nbsp;</th>
								<th width="20%" nowrap align="left">							
									<label id="icdTypeName">Principal Diagnosis</label>
									<input id="icdType" name="icdType" type="checkbox" onclick="setType(0)" value="" checked />
									<!--<input id="icdCode" name="icdCode" height="10" type="text" value="" onfocus="setKeyCode(0,'<?= $HTTP_SESSION_VARS['sess_en'] ?>','<?=$encounter_type?>','<?=$encounter_type_a?>','<?= $HTTP_SESSION_VARS['sess_user_name'] ?>')" onblur="trimString(this);" width="5" maxlength="11"> -->
									<!--<span id="icd"> <script>makeCombobox();</script></span>-->
									<input id="hicdCode" type="hidden" value="">
									
									<input id="index" type="hidden" value="">
									<!-- added by VAN 03-28-08-->
									<input id="sess_en" type="hidden" value="<?= $HTTP_SESSION_VARS['sess_en'] ?>">
									<input id="encounter_type" type="hidden" value="<?=$encounter_type?>">
									<input id="encounter_type_a" type="hidden" value="<?=$encounter_type_a?>">
									<input id="sess_user_name" type="hidden" value="<?= $HTTP_SESSION_VARS['sess_user_name'] ?>">
                        </th>	
								<th width="30%" id="icd"><script>makeCombobox(0);</script></th>
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