<?php /* Smarty version 2.6.0, created on 2020-02-05 12:14:31
         compiled from ../../../modules/dashboard/dashlets/DoctorsNotes/templates/View.tpl */ ?>
<?php require_once(SMARTY_DIR . 'core' . DIRECTORY_SEPARATOR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'default', '../../../modules/dashboard/dashlets/DoctorsNotes/templates/View.tpl', 343, false),)), $this); ?>
<div style="width:100%; display:table; padding:0">
	<ul class="dashlet-contents-tabs">
		<li><a href="#DoctorsNotes-subjective-tab" >Subjective</a></li>
		<li><a href="#DoctorsNotes-objective-tab" >Objective</a></li>
		<li><a href="#DoctorsNotes-assessment-tab">Assessment</a></li>
		<li><a href="#DoctorsNotes-plan-tab">Plan</a></li>
	</ul>

	<div class="dashlet-contents-tabs-container">
		<div id="DoctorsNotes-subjective-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-subjective" onsubmit="return false;">
				<table width="100%" cellpadding="0" cellspacing="5">
							<tr>
								<td style="font:normal 18px Arial">Chief Complaint</td>
							</tr>
							<tr>
									<td><textarea rows="8" style="width:100%; font: normal 14px 'Courier New'; overflow:visible;" name="chief_complaint" class="segInput" onblur="DoctorsNotes_SaveNote('DoctorsNotes-subjective')" spellcheck="false" <?php echo $this->_tpl_vars['disable']; ?>
><?php echo $this->_tpl_vars['data']['chief_complaint']; ?>
</textarea></td>
							</tr>
				</table>
			</form>
		</div>
		<div id="DoctorsNotes-objective-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-objective" onsubmit="return false;">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tr>
					<td style="font:normal 18px Arial">Pertinent Physical Examination</td>
				</tr>
				<tr>
						<td><textarea rows="8" style="width:100%; font:normal 14px 'Courier New'; overflow:visible;" name="physical_examination" class="segInput" onblur="DoctorsNotes_SaveNote('DoctorsNotes-objective')" spellcheck="false" <?php echo $this->_tpl_vars['disable']; ?>
><?php echo $this->_tpl_vars['data']['physical_examination']; ?>
</textarea></td>
				</tr>
				</table>
			</form>
		</div>
		<div id="DoctorsNotes-assessment-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-assessment" onsubmit="return false;">
			<table width="100%" cellpadding="0" cellspacing="5">
				<tr style=<?php echo $this->_tpl_vars['sHideDiagnosisList']; ?>
>
					<td style="font:normal 18px Arial">Search Diagnosis</td>
				</tr>
				<tr style=<?php echo $this->_tpl_vars['sHideDiagnosisList']; ?>
>
					<td><input type="text" class="segInput" name="diagnosis" id="DoctorsNotes-diagnosis-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; font: bold 16px 'Times';" / <?php echo $this->_tpl_vars['disable']; ?>
></td>
				</tr>
				<tr style=<?php echo $this->_tpl_vars['sHideDiagnosisList']; ?>
>
					<div id="DoctorsNotes-diagnosis-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:hidden; padding:0; margin-top:10px; <?php echo $this->_tpl_vars['sHideDiagnosisList']; ?>
"></div>
				</tr>
				<!-- edited by Jasper Ian Q. Matunog 11/10/2014 -->
				<tr style=<?php echo $this->_tpl_vars['sHideClinicalImpression']; ?>
>
					<td style="font:normal 18px Arial">
						Clinical Impression
						<!-- Added by Robert 05/26/2015 -->
						<span style="float: right;">
							<button class="button" id="clinical_impression_button" name="clinical_impression_button" onclick="saveClinicalImpressionOnButton()" <?php echo $this->_tpl_vars['disable_clinical_button']; ?>
 <?php echo $this->_tpl_vars['disable']; ?>
><img src="../../gui/img/common/default/save.png"/>
								Save
							</button>
						</span>
						<!-- End add by Robert -->
					</td>
				</tr>
				<tr style=<?php echo $this->_tpl_vars['sHideClinicalImpression']; ?>
>
						<td><textarea rows="8" style="width:100%; font:normal 14px 'Courier New'; overflow:visible;" id="clinical_impression" name="clinical_impression" class="segInput" onblur="" spellcheck="false" <?php echo $this->_tpl_vars['disable']; ?>
 <?php echo $this->_tpl_vars['disable_clinical']; ?>
><?php echo $this->_tpl_vars['sClinicalImpression']; ?>
</textarea></td>
				</tr>
				<!-- Added by Kenneth 02/05/2018 -->
				<tr style=<?php echo $this->_tpl_vars['sHideClinicalImpression']; ?>
>
					<td style="font:normal 18px Arial">
						Final Diagnosis
						<span style="float: right;">
							<button class="button" id="audit_diagnosis_button" name="audit_diagnosis_button" onclick="AuditTrailDiagnosis()" ><img src="../../gui/img/common/default/document.gif"/>
								Audit Trail
							</button>
							<button class="button" id="edit_diagnosis_button" name="edit_diagnosis_button"  <?php echo $this->_tpl_vars['disabled_edit_btn']; ?>
 <?php echo $this->_tpl_vars['hide_edit_button']; ?>
 onclick="editFinalDiagnosis()" ><img src="../../gui/img/common/default/save.png"/>
								Edit
							</button>
							<button class="button" id="final_diagnosis_button" name="final_diagnosis_button" onclick="saveFinalDiagnosisOnButton()"   <?php echo $this->_tpl_vars['hide_save_button']; ?>
><img src="../../gui/img/common/default/save.png"/>
								Save
							</button>
							
						</span>
					</td>
				</tr>
				<tr style=<?php echo $this->_tpl_vars['sHideClinicalImpression']; ?>
>
						<td><textarea rows="8" style="width:100%; font:normal 14px 'Courier New'; overflow:visible;" id="final_diagnosis" name="final_diagnosis" class="segInput" onblur="" spellcheck="false" <?php echo $this->_tpl_vars['disable']; ?>
><?php echo $this->_tpl_vars['sFinalDiagnosis']; ?>
</textarea></td>
				</tr>
				<tr style=<?php echo $this->_tpl_vars['sHideClinicalImpression']; ?>
>
					<td style="font:normal 18px Arial">
						Other Diagnosis
						<span style="float: right;">
							<button class="button" id="edit_other_diagnosis_button" name="edit_other_diagnosis_button"  <?php echo $this->_tpl_vars['disabled_edit_btn']; ?>
 <?php echo $this->_tpl_vars['hide_edit_button']; ?>
 onclick="editOtherDiagnosis()" ><img src="../../gui/img/common/default/save.png"/>
								Edit
							</button>
							<button class="button" id="other_diagnosis_button" name="other_diagnosis_button" onclick="saveOtherDiagnosisOnButton()" <?php echo $this->_tpl_vars['hide_save_button']; ?>
><img src="../../gui/img/common/default/save.png"/>
								Save
							</button>
						</span>
					</td>
				</tr>
				<tr style=<?php echo $this->_tpl_vars['sHideClinicalImpression']; ?>
>
						<td><textarea rows="8" style="width:100%; font:normal 14px 'Courier New'; overflow:visible;" id="other_diagnosis" name="other_diagnosis" class="segInput" onblur="" spellcheck="false" <?php echo $this->_tpl_vars['disable']; ?>
><?php echo $this->_tpl_vars['sOtherDiagnosis']; ?>
</textarea></td>
				</tr>
				<!-- Ended by Kenneth 02/05/2018 -->
			</table>
			</form>
		</div>
		<div id="DoctorsNotes-plan-tab" class="dashlet-contents-tabs-content">
			<form id="DoctorsNotes-plan" onsubmit="return false;">
				<table width="100%" cellpadding="0" cellspacing="5">
					<tr>
						<td style="font:normal 18px Arial">Progress Notes/Clinical Summary</td>
					</tr>
					<tr>
						<td><textarea rows="8" style="width:100%; font: normal 14px 'Courier New'; overflow:visible;" name="clinical_summary" class="segInput" onblur="DoctorsNotes_SaveNote('DoctorsNotes-plan')" spellcheck="false" <?php echo $this->_tpl_vars['disable']; ?>
><?php echo $this->_tpl_vars['data']['clinical_summary']; ?>
</textarea></td>
					</tr>
				</table>
			</form>
		</div>
		<div id="edit-dialog" class="edit-dialog" style="display: none;">
			<form id="edit_final_diagnosis">
			<table>
				<tr>
				<td><textarea rows="8" style="width:220%; font: normal 14px 'Courier New'; overflow:visible;" name="edit_summary_diagnosis"   id="edit_summary_diagnosis" class="segInput" spellcheck="false "  <?php echo $this->_tpl_vars['disable_edit']; ?>
><?php echo $this->_tpl_vars['sEditFinalDiagnosis']; ?>
</textarea></td>
				</tr>
			</table>
			</form>
    	</div>
    	<div id="edit-other-dialog"   class="edit-other-dialog"style="display: none;">
			<form id="edit-other-dialog">
			<table>
				<tr>
				<td><textarea rows="8" style="width:220%; font: normal 14px 'Courier New'; overflow:visible;" name="edit_summary_other_diagnosis"   id="edit_summary_other_diagnosis" class="segInput" spellcheck="false "  <?php echo $this->_tpl_vars['disable_edit']; ?>
><?php echo $this->_tpl_vars['sEditOtherDiagnosis']; ?>
</textarea></td>
				</tr>
			</table>
			</form>
    	</div>
    	<div id="audit-dialog" style="display: none;">
    		<div id="audit-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
" style="width:100%; overflow:hidden; padding:0; margin-top:10px; <?php echo $this->_tpl_vars['sHideDiagnosisList']; ?>
"></div>
    	</div>
	</div>
</div>

<script type="text/javascript">
function DoctorsNotes_SaveNote(form_id) {
	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveDrNote", {
		data:$J('#'+form_id).serializeArray()
	});
}

function DoctorsNotes_DeleteDiagnosis(code) {
	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "deleteDrDiagnosis", {
		data:code
	});
}

function DoctorsNotes_SaveIcdCode(code) {
	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveDrDiagnosis", {
		data:code
	});
}

function DoctorsNotes_refreshIcdList() {
	$('DoctorsNotes-diagnosis-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').list.refresh();
	$('DoctorsNotes-diagnosis-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').value="";
	$('DoctorsNotes-diagnosis-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').focus();
}



// function saveClinicalImpression() {
// 	 // alert($J('#clinical_impression').val());
// 	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveClinicalImpression", {
// 		data:$J('#clinical_impression').val()
// 	});
// }

// Added by Robert 04/28/2015
function saveClinicalImpressionOnButton() {
	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveClinicalImpressionOnButton", {
		data:$J('#clinical_impression').val()
	});
	alert('Clinical impression successfully saved');
}
// End add by Robert

// Added by Kenneth 02/05/2018
function saveFinalDiagnosisOnButton() {
	
	// alert($J('#final_diagnosis').val());
	/*if($J('#final_diagnosis').val()!="") {*/
		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveFinalDiagnosisOnButton", {
			data:$J('#final_diagnosis').val()
		});
		alert('Final Diagnosis successfully saved');
	/*}else{
		alert('Final Diagnosis must not be empty');
	}*/
}
function saveOtherDiagnosisOnButton() {
	
	// alert($J('#other_diagnosis').val());
	//if($J('#other_diagnosis').val()!="") {
		Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveOtherDiagnosisOnButton", {
			data:$J('#other_diagnosis').val()
		});
		alert('Other Diagnosis successfully saved');
	/*}else{
		alert('Other Diagnosis must not be empty');
	}*/
}
// Ended by Kenneth 02/05/2018

// Added by Matsuu 11012018
function saveEditFinalDiagnosis(data){

	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveEditFinalDiagnosis", {
			data:data
		});
		alert('Final Diagnosis successfully saved');
}
function saveEditOtherDiagnosis(data){

	Dashboard.dashlets.sendAction("<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", "saveEditOtherDiagnosis", {
			data:data
		});
		alert('Other Diagnosis successfully saved');
}

function DoctorsNotes_refreshAudit() {
	$('audit-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').list.refresh();
}

// Ended here by Matsuu 11012018

//added rnel for populating selected ICD10 in clinical impression textarea input field
function populateSelectedDiagnosis(data) {
	var i, dataImpression, splittedData;

	dataImpression = $('clinical_impression').value;

	splittedData = dataImpression.split('\n');

	for(i in splittedData) {
		if(splittedData[i] == data) {
			alert(data + ' is already in the list');
			return false;
		}
	}
	$('clinical_impression').value += data + "\n";

}
// Added by Matsuu 11022018
function editFinalDiagnosis (){
	
$J("#edit-dialog").dialog({
		autoOpen: true,
		modal: true,
		show: 'fade',
		hide: 'fade',
		height: 280,
		width: '28%',
		title: 'Edit Final Diagnosis',
		draggable: false,
		resizable: false,
		position: 'fixed',
		buttons: {
			"SAVE" : function (){
				var final_diagnosis=$J('#edit_summary_diagnosis').val();
				if(final_diagnosis.trim().length < 1){
					alert("Please Fill in Final Diagnosis");
					return false;
				}
					saveEditFinalDiagnosis(final_diagnosis);
					$J('#final_diagnosis').val(final_diagnosis);
				    $J(this).dialog("close");
			},
			"Close": function() {
				$J(this).dialog("close");
			}
		}
	});
}

function editOtherDiagnosis (){
	
$J("#edit-other-dialog").dialog({
		autoOpen: true,
		modal: true,
		show: 'fade',
		hide: 'fade',
		height: 280,
		width: '28%',
		title: 'Edit Other Diagnosis',
		draggable: false,
		resizable: false,
		position: 'fixed',
		buttons: {
			"SAVE" : function (){
				var other_diagnosis=$J('#edit_summary_other_diagnosis').val();
				if(other_diagnosis.trim().length < 1){
					alert("Please Fill in Final Diagnosis");
					return false;
				}
					saveEditOtherDiagnosis(other_diagnosis);
				    $J(this).dialog("close");
				    $J('#other_diagnosis').val(other_diagnosis);
			},
			"Close": function() {
				$J(this).dialog("close");
			}
		}
	});
}

function AuditTrailDiagnosis (){
	$J("#audit-dialog").dialog({
		autoOpen: true,
		modal: true,
		show: 'fade',
		hide: 'fade',
		height: 400,
		width: '60%',
		title: 'Audit Trail',
		draggable: true,
		resizable: false,
		position: 'fixed',
		buttons: {
			"Close": function() {
				$J(this).dialog("close");
			}
		}
	});
}
// Added by M

//initialize list gen
ListGen.create("DoctorsNotes-diagnosis-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'DoctorsNotes-diagnosis-listgen-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "100%",
	height: "auto",
	url: "dashlets/DoctorsNotes/Listgen.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	maxRows: <?php echo ((is_array($_tmp=@$this->_tpl_vars['settings']['pageSize'])) ? $this->_run_mod_handler('default', true, $_tmp, '5') : smarty_modifier_default($_tmp, '5')); ?>
,
	rowHeight: 32,
	layout: [
		//['<h1>My Patients</h1>'],
		['#first', '#prev', '#pagestat', '#next', '#last', '#refresh'],
		['#thead'],
		['#tbody']
	],
	columnModel:[
		{
			name: "",
			label: '',
			width: 30,
			sortable: false,
			visible: true,
			styles: {
				textAlign: "center",
				whiteSpace: "nowrap"
			},
			render: function(data, index)
			{
				var row = data[index];
					return '<img class="link" src="../../images/cashier_delete_small.gif" onclick="DoctorsNotes_DeleteDiagnosis(\''+row["code"]+'\');return false;"/>';
			}
		},
		{
			name: "code",
			label: "ICD10",
			width: 100,
			styles: {
				color: "#000080",
				textAlign: "center"
			},
			sorting: ListGen.SORTING.desc,
			sortable: true,
			visible: true
		},
		{
			name: "description",
			label: "Description",
			width: 300,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		}
	]
});
//end for list gen

ListGen.create("audit-list-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
", {
	id:'DoctorsNotes-audit-listgen-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
',
	width: "350px",
	height: "auto",
	url: "dashlets/DoctorsNotes/AuditTrail.php",
	showFooter: true,
	iconsOnly: true,
	effects: true,
	dataSet: [],
	autoLoad: true,
	rowHeight: 32,
	columnModel:[
		{
			name: "",
			label: "",
			width: 50,
			styles: {
				color: "#000080",
				textAlign: "center"
			}
		},
		{
			name: "date_changed",
			label: "Date/Time",
			width: 100,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "encounter_nr",
			label: "Encounter",
			width: 100,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "doctor_name",
			label: "Physician's Name",
			width: 200,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000"
			}
		},
		{
			name: "tod",
			label: "Type of Diagnosis",
			width: 200,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12px",
				color: "#c00000",
			}
		},
		{
			name: "diagnosis",
			label: "Diagnosis",
			width: 200,
			sorting: ListGen.SORTING.none,
			sortable: true,
			visible: true,
			styles: {
				fontSize: "12",
				color: "#c00000",
				wordWrap:"break-word"
			}
		},
	]
});

//for tabs
	//When page loads...
	$J(".dashlet-contents-tabs-content").hide(); //Hide all content
	$J("ul.dashlet-contents-tabs li:first").addClass("active").show(); //Activate first tab
	$J(".dashlet-contents-tabs-content:first").show(); //Show first tab content

	//On Click Event
	$J("ul.dashlet-contents-tabs li").click(function() {

		$J("ul.dashlet-contents-tabs li").removeClass("active"); //Remove any "active" class
		$J(this).addClass("active"); //Add "active" class to selected tab
		$J(".dashlet-contents-tabs-content").hide(); //Hide all tab content
		if($J('.edit-dialog').size() >  1){
			$J('.edit-dialog').eq(1).remove();
		}
		if($J('.edit-other-dialog').size()>1){
				$J('.edit-other-dialog').eq(1).remove();
		}	
		var activeTab = $J(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$J(activeTab).show(); //Fade in the active ID content
		return false;
	});
//end for tabs

//for autocomplete
$J('#DoctorsNotes-diagnosis-<?php echo $this->_tpl_vars['dashlet']['id']; ?>
').autocomplete({
		minLength: 1,
		source: '../../modules/dashboard/dashlets/DoctorsNotes/icd10List.php',
		select: function(event, ui) {
			// NOTE: put onSelect logic here
			populateSelectedDiagnosis(ui.item.description) // added rnel
			DoctorsNotes_SaveIcdCode(ui.item.icd_code)
			return false;
		}
	})
	.data( "autocomplete" )._renderItem = function( ul, item ) {

		return $J( "<li></li>" )
			.data( "item.autocomplete", item )
			.append(
				"<a>" +
					'<span style="font-weight:bold;color:#000066">' + item.description+ '</span>' +
					"<br/>" +
					'<span style="font:normal 10px Arial;color:#404040">' + item.icd_code+'</span>' +
				"</a>" )
			.appendTo( ul );
	};
	//end for autocomplete


</script>