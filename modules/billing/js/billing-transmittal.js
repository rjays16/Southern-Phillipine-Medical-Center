var bClickedHCare = true;
var d_currentPage=0, d_lastPage=0;
var p_currentPage=0, p_lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
var data_delete=[]; //<--for clearing the array...

var memberEditOn = false;

function dCurrentPage() {
	this.value = d_currentPage;
}
dCurrentPage.prototype.setval = function(val) {
	d_currentPage = val;
}

function dLastPage() {
	this.value = d_lastPage;
}
dLastPage.prototype.setval = function(val) {
	d_lastPage = val;
}

function pCurrentPage() {
	this.value = p_currentPage
}
pCurrentPage.prototype.setval = function(val) {
	p_currentPage = val;
}

function pLastPage() {
	this.value = p_lastPage;
}
pLastPage.prototype.setval = function(val) {
	p_lastPage = val;
}

var dcurpage = new dCurrentPage();
var dlastpage = new dLastPage();
var pcurpage = new pCurrentPage();
var plastpage = new pLastPage();

function isValidTime(value) {
	 var hasMeridian = false;
	 var re = /^\d{1,2}[:]\d{2}([:]\d{2})?( [aApP][mM]?)?$/;
	 if (!re.test(value)) { return false; }
	 if (value.toLowerCase().indexOf("p") != -1) { hasMeridian = true; }
	 if (value.toLowerCase().indexOf("a") != -1) { hasMeridian = true; }
	 var values = value.split(":");
	 if ( (parseFloat(values[0]) < 0) || (parseFloat(values[0]) > 23) ) { return false; }
	 if (hasMeridian) {
			if ( (parseFloat(values[0]) < 1) || (parseFloat(values[0]) > 12) ) { return false; }
	 }
	 if ( (parseFloat(values[1]) < 0) || (parseFloat(values[1]) > 59) ) { return false; }
	 if (values.length > 2) {
			if ( (parseFloat(values[2]) < 0) || (parseFloat(values[2]) > 59) ) { return false; }
	 }
	 return true;
}

function checkTimeInput(tvalue) {
	if ((tvalue != '__:__:__') && (tvalue != '')) {
		if (!isValidTime(tvalue)) {
			alert('The time you entered is not valid!');
			$j("#dischrgtme").focus();
			$j("#dischrgtme").select();
			return false;
		}
	}
	return true;
}

function init(e) {
	shortcut.add("Ctrl+F", function(){ searchInsurance(); }, {
			'type':'keypress',
			'propagate':false});
	shortcut.add("ESC", function(){ cClick(); });
//	shortcut.add("Ctrl+S", function(){ js_SaveBilling(); }, {
//			'type':'keypress',
//			'propagate':false});
}//end function init

function searchInsurance() {
	return overlib(OLiframeContent('billing-select-hcare.php', 700, 400, 'fSelHCare', 0, 'auto'),
							WIDTH,700, TEXTPADDING,0, BORDER,0, STICKY, SCROLL, CLOSECLICK, MODAL, DRAGGABLE,
					CLOSETEXT, '<img src=../../images/close.gif border=0 >',
							CAPTIONPADDING, 4, CAPTION,'Select Health Insurance',
							MIDX, 0, MIDY, 0, STATUS,'Select health insurance');
}

function js_showDetailsSection() {
	$('tbl_transmit_details_hdr').style.display = '';
	$('tbl_transmit_details_body').style.display = '';
}

function addClaim(insurance_nr, categ_id, categ_desc, prd, enc_nr, patient, claim, pclaim, newform, smod_opscode, indicator, cat_code_indicator) {
		var details = new Object();

		details.insurance_nr = insurance_nr;
		details.categ_id = categ_id;
		details.categ_desc = categ_desc;
		details.prd = prd;
		details.enc_nr = enc_nr;
		details.patient = patient;
		details.claim = claim;
		details.pclaim = pclaim;
		details.newform = newform;
		details.smod_opscode = smod_opscode; // added by: syboy 06/27/2015
		details.indicator = indicator; // added by: syboy 10/12/2015
		details.cat_code_indicator = cat_code_indicator; // added by: syboy 11/13/2015 : meow

		js_addClaim(details);
}

function editPClaim(id) {
		$("editpclaim_"+id).style.display = "";
		$("rowpclaim_"+id).style.display = "none";
		$("editpclaim_"+id).focus();
}

function isESCPressed(e) {
		var kC  = (window.event) ?    // MSIE or Firefox?
						 event.keyCode : e.keyCode;
		var Esc = (window.event) ?
						27 : e.DOM_VK_ESCAPE // MSIE : Firefox
		return (kC==Esc);
}

function shiftKeyPressed(e) {
		var evtobj = window.event ? event : e;
		return(evtobj.shiftKey);
//    if (evtobj.altKey || evtobj.ctrlKey || evtobj.shiftKey)
//        alert("you pressed one of the 'Alt', 'Ctrl', or 'Shift' keys")
}


function cancelPClaim(id) {
		$("editpclaim_"+id).style.display = "none";
		$("rowpclaim_"+id).style.display = "";
}

function applyPClaim(e, id) {
		var characterCode;

//    bClickedHCare = false;

		if (e) {
				if(e && e.which) { //if which property of event object is supported (NN4)
						characterCode = e.which; //character code is contained in NN4's which property
				}
				else {
						characterCode = e.keyCode; //character code is contained in IE's keyCode property
				}
		}
		else
				characterCode = 13;

		if ( (characterCode == 13) || (isESCPressed(e)) ) {
				var pclaim = $("editpclaim_"+id).value;
				if ( !(isNaN(parseInt(pclaim))) && (parseInt(pclaim)>=0) ) {
						$("rowpclaim_"+id).innerHTML  = '<input name="pclaims[]" id="pclaim_'+id+'" type="hidden" value="'+pclaim+'">'+formatNumber(Number(pclaim),2);
				}

				$("rowpclaim_"+id).style.display = "";
				$("editpclaim_"+id).style.display = "none";
		}
}

function js_addClaim(details) {
		var srcRow = '';
		var root_path = $('root_path').value;
		var list = $('transmit_details');
		var deleteIcon = "";
		var canDelete = $('canDeleteTransmittal').value;
		var canAdd = $('addpermission').value;

		if(canDelete == 0){
			deleteIcon = '<a title="No permission to delete" href="#">'+
							'<img class="segSimulatedLink" id="transReasonDelete" src="'+root_path+'images/cashier_delete.gif" border="0" align="absmiddle" '+
									
						'</a>';
		}else{
			deleteIcon = '<a title="Remove" href="#">'+
							'<img class="segSimulatedLink" id="transReasonDelete" src="'+root_path+'images/cashier_delete.gif" border="0" align="absmiddle" '+
									'onclick="reasonDelete(\''+details.enc_nr+'\',\''+details.patient+'\',\''+details.insurance_nr+'\',\''+details.bill_diff+'\')"/>'+
						'</a>';
		}

		if(canAdd == true){
			casenumberlink = '<a title="Edit encounter information!" href="#"><span style="cursor:pointer" id="rowencinfo_'+details.enc_nr+'" onclick="showEncInfoPrompt(\''+details.patient+'\', \''+details.enc_nr+'\', $(\'categid_'+details.enc_nr+'\').value);">'+details.enc_nr+'</span></a>';
		}else{
			casenumberlink = '<span id="rowencinfo_'+details.enc_nr+'">'+details.enc_nr+'</span>';
		}

		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						var dRows = dBody.getElementsByTagName("tr");

						var cases = document.getElementsByName('cases[]');
						if (cases.length == 0) {
								clearTransmitDetails(list);
						}
						alt = (dRows.length%2)+1

					if (details) {
								var n_claim  = details.claim.replace(',', '');
								var n_pclaim = details.pclaim.replace(',', '');
								var days_allowed = $('days_allowed').value;
console.log(days_allowed);
console.log(details.bill_diff);
								if(parseInt(details.bill_diff) > parseInt(days_allowed)){
									$('has_exceeded').value++;
								}

						srcRow = '<tr class="wardlistrow'+alt+'" id="row_'+details.enc_nr+'">'+
												 '<input type="hidden" name="cases[]" id="case_'+details.enc_nr+'" value="'+details.enc_nr+'" />'+
								 '<td width="*" align="right" nowrap="nowrap">'+
										deleteIcon+
										'<input type="hidden" id="categid_'+details.enc_nr+'" value="'+details.categ_id+'" />'+
								 '</td>'+
								 '<td id="policyno_'+details.enc_nr+'" width="9%">'+details.insurance_nr+'</td>'+
								 '<td id="categdesc_'+details.enc_nr+'" width="9%">'+details.categ_desc+'</td>'+
								 '<td align="center" width="30%">'+details.prd+'</td>'+
								 '<td align="center" width="8%">'+casenumberlink+'</td>'+
								 '<td width="20%">'+details.patient+'</td>'+
								 '<td align="right" width="10%">'+formatNumber(Number(n_claim), 2)+'</td>'+
								 '<td width="10%" align="right"><input style="width:95%;display:none;text-align:right" type="text" id="editpclaim_'+details.enc_nr+'" value="'+formatNumber(Number(n_pclaim),2)+'" onFocus="this.select(); bClickedHCare = false;" onblur="cancelPClaim(\''+details.enc_nr+'\'); bClickedHCare = true;" onkeyup="applyPClaim(event,\''+details.enc_nr+'\');"><span style="cursor:pointer" id="rowpclaim_'+details.enc_nr+'" onclick="editPClaim(\''+details.enc_nr+'\');">'+
											'<input name="pclaims[]" id="pclaim_'+details.enc_nr+'" type="hidden" value="'+n_pclaim+'">'+formatNumber(Number(n_pclaim),2)+'</span></td>'+
								 '<td align="center" width="10%">';
							if (66983 == details.smod_opscode ||  66984 == details.smod_opscode || 66987 == details.smod_opscode) {
								if (details.cat_code_indicator == 'true' && details.indicator == 'true') {
									srcRow += '<img class="segSimulatedLink" title="Print" id="btnprint" style="cursor:pointer" src="'+root_path+'/images/cashier_print2.gif" border=0 '+
											'onclick="promptForFormsPrinting(\''+details.enc_nr+'\', Number('+details.newform+'))"/>'+
								 	'&nbsp;<img class="segSimulatedLink" title="Cataract Number: '+details.smod_opscode+'" id="btnAddCataractNo_'+details.enc_nr+'" style="cursor:pointer" src="'+root_path+'/images/claim_ok.gif" border=0 '+ // added by: syboy 06/23/2015
											'onclick="promptForFormsCataract(\''+details.enc_nr+'\', \''+details.smod_opscode+'\')"/> ';
											encounter_arr.push(details.enc_nr);
											smod_opscode_arr.push(details.smod_opscode);
								}else{
									srcRow += '<img class="segSimulatedLink" title="Print" id="btnprint" style="cursor:pointer" src="'+root_path+'/images/cashier_print2.gif" border=0 '+
											'onclick="promptForFormsPrinting(\''+details.enc_nr+'\', Number('+details.newform+'))"/>'+
								 	'&nbsp;<img class="segSimulatedLink" title="Cataract Number: '+details.smod_opscode+'" id="btnAddCataractNo_'+details.enc_nr+'" style="cursor:pointer" src="'+root_path+'/images/edit.gif" border=0 '+ // added by: syboy 06/23/2015
											'onclick="promptForFormsCataract(\''+details.enc_nr+'\', \''+details.smod_opscode+'\')"/> ';
											encounter_arr.push(details.enc_nr);
											smod_opscode_arr.push(details.smod_opscode);
								}
							}else{
								srcRow += '<img class="segSimulatedLink" id="btnprint" style="cursor:pointer" src="'+root_path+'/images/cashier_print2.gif" border=0 '+
											'onclick="promptForFormsPrinting(\''+details.enc_nr+'\', Number('+details.smod_opscode+'))"/>';
											encounter_arr.push(details.enc_nr);
											smod_opscode_arr.push(details.smod_opscode);
							}
							
							srcRow += '</td>'+
								 	  '</tr>';
					}
						else
								srcRow = "<tr><td colspan=\"9\">Transmittal list is currently empty ...</td></tr>";

					$('tbl_transmit_details_body').innerHTML += srcRow;
						return true;
				}
		}
		return false;
}

// added by : syboy 06/27/2015 
function updateCataractNo(data){

	xajax_updateCataractCode(data);

}
// end


var encounter_arr =[];
var smod_opscode_arr =[];

function printAllCF2 () {
		var rpath = $('root_path').value;
		var seg_URL_APPEND = $F('seg_URL_APPEND');
		var hcareid = $F('hcare_id');
		
			urlholder = rpath+'modules/repgen/pdf_philhealth_cf2_new.php'+seg_URL_APPEND+'&encounter_nr='+encounter_arr+'&id='+hcareid;
			nleft = (screen.width - 680)/2;
			ntop = (screen.height - 520)/2;
			
					printwin = window.open(urlholder, "Print PHIC Form", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);

		
}
function printInsuranceForm(enc_nr, form_no, newform) {
		var rpath = $('root_path').value;
		var seg_URL_APPEND = $F('seg_URL_APPEND');
		var hcareid = $F('hcare_id');
		var pclaim  = $('pclaim_'+enc_nr).value;
		if (typeof(newform) == 'undefined')
			newform = 0;
		else
			newform = Number(newform);

		urlholder = '';
		form_no = Number(form_no);

		if (form_no == 1){
			urlholder = rpath+'modules/repgen/pdf_philhealth_cf1.php'+seg_URL_APPEND+'&encounter_nr='+enc_nr+'&id='+hcareid;
		} else if (form_no == 2) {
			if (newform)
				urlholder = rpath+'modules/repgen/pdf_philhealth_cf2.php'+seg_URL_APPEND+'&encounter_nr='+enc_nr+'&id='+hcareid+'&claim='+pclaim;
			else
				urlholder = rpath+'modules/repgen/pdf_philhealth_form2.php'+seg_URL_APPEND+'&encounter_nr='+enc_nr+'&id='+hcareid+'&claim='+pclaim;
		} else if (form_no == 3){
			urlholder = rpath+'modules/repgen/pdf_philhealth_cf2_new.php'+seg_URL_APPEND+'&encounter_nr='+enc_nr+'&id='+hcareid;
        }

		if (urlholder != '') {
			nleft = (screen.width - 680)/2;
			ntop = (screen.height - 520)/2;
	        //alert(urlholder);
			if(enc_nr != ""){
					printwin = window.open(urlholder, "Print PHIC Form", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
			}else{
					alert("No associated claim!");
			}
		}

		return true;
}

function printTransmittalLetter(transmit_no, class_id, detailed) {
		var rpath = $('root_path').value;
		var seg_URL_APPEND = $F('seg_URL_APPEND');
		var trdate = $('transmitdte').value;
		var caseType = "";

		if($('surgicalCase').checked){
			caseType = "Surgical";
		}else if($('medicalCase').checked){
			caseType = "Medical";
		}else{
			caseType = "";
		}

//    detailed = (shiftKeyPressed(e)) ? '1' : '0';
		urlholder = rpath+'modules/repgen/pdf_transmittal_letter.php'+seg_URL_APPEND+'&nr='+transmit_no+'&detailed='+detailed+'&class='+class_id+'&trdte='+(getDateFromFormat(trdate, 'yyyy-MM-dd HH:mm:ss')/1000)+'&caseType='+caseType;

		nleft = (screen.width - 680)/2;
		ntop = (screen.height - 520)/2;
		if(transmit_no != "") {
				printwin = window.open(urlholder, "Transmittal Letter", "toolbar=no, status=no, menubar=no, width=700, height=500, location=center, dependent=yes, resizable=yes, scrollbars=yes, top=" + ntop + ",left=" + nleft);
		}
		else {
				alert("No transmittal letter!");
		}
		return true;
}

function jsInitDetailsSection() {
	$('tbl_transmit_details_hdr').style.display = '';
	jsClearSection('tbl_transmit_details_body');
}

function jsClearSection(elementID) {
	$(elementID).style.display = '';
	//$(elementID).innerHTML = '';
}

function clearTransmitDetails(list) {
	if (!list) list = $('transmit_details')
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0]
		if (dBody) {
			trayItems = 0
			dBody.innerHTML = ""
			return true
		}
	}
	return false
}

function assignHCareID(hcare_id) {
		$('hcare_id').value = hcare_id;
}

function assignMemCategDesc(enc_nr, categ_desc) {
	categ_desc = (categ_desc == "" ? "NONE" : categ_desc);
	$('categdesc_'+enc_nr).innerHTML = categ_desc;
}

function assignInsuranceNr(enc_nr, insurance_nr) {
	$('policyno_'+enc_nr).innerHTML = insurance_nr;
}
// ADDED by JEFF 06-08-17
function clearArrayData(){
	data_delete=[];
	if (data_delete.length ==0) {
		document.transmittal_form.submit();
	}
}
// ADDED by JEFF 06-08-17
function newValidateForm(){
	if (validate()){
		if (data_delete.length >=1) {
			alert("Successfully Deleted Claim/s.");
			xajax_saveDeleteReasonNew(data_delete);
		}else{
			document.transmittal_form.submit();
		}
	}
}
function validate() {
		var days_allowed = $('days_allowed').value;

		if (!$('transmit_no').value) {
				alert("Please enter the transmittal control no.");
				$('transmit_no').focus();
				return false;
		}

		if ($('has_exceeded').value >= 1) {
				alert("Unable to save transmittal. There are some cases that filed beyond "+days_allowed+" days.");
				return false;
		}

		if (document.getElementsByName('cases[]').length==0) {
				alert("Warning: The transmittal list is empty...");
				return false;
		}
		return confirm('Save this transmittal?');
	
}
function reclassRows(list,startIndex) {
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						var dRows = dBody.getElementsByTagName("tr");
						if (dRows) {
								for (i=startIndex;i<dRows.length;i++) {
										dRows[i].className = "wardlistrow"+(i%2+1);
								}
						}
				}
		}
}
function removeClaim(enc_nr) {
		var destTable, destRows;
		var table = $('transmit_details');
		var rmvRow=document.getElementById("row_"+enc_nr);
		if (table && rmvRow) {
				var rndx = rmvRow.rowIndex-1;
				table.deleteRow(rmvRow.rowIndex);
				if (!document.getElementsByName("cases[]") || document.getElementsByName("cases[]").length <= 0)
						js_addClaim(null);
				reclassRows(table,rndx);
				xajax_removeCaseInClaim(enc_nr);
		}
		else
				alert(table+' and '+rmvRow);
//    refreshTotal();
}

var arrayfunc = function (node) {
	return node.value;
}

// added by: syboy 11/17/2015 : meow
function changeIcon(caseNr, indicator1, indicator2) {

	if (indicator1 == 'true' && indicator2 == 'true') {
		$j('#btnAddCataractNo_'+caseNr).attr('src', '../../images/claim_ok.gif');
	}else{
		$j('#btnAddCataractNo_'+caseNr).attr('src', '../../images/edit.gif');
	}
	
}

// added by: syboy 09/16/2015
function showTransDetails(){
	xajax_showTransmittalDetails(18, encs, $('old_trnsmit_no').value);
}
// end

function initDataEditBox() {
		// Define various event handlers for Dialog
		var handleSubmit = function() {
				this.submit();
		};
		var handleCancel = function() {
				this.cancel();
		};

		// Instantiate the Dialog
		YAHOO.encounter.container.dataeditbox = new YAHOO.widget.Dialog("dataeditbox",
        { width : "750px",
                        fixedcenter : true,
                        visible : false,
                        constraintoviewport : true,
             close:true,
             buttons : [ { text:"Save", handler:handleSubmit, isDefault:true },
                                                        {text:"Cancel", handler:handleCancel}]
         });

		YAHOO.encounter.container.dataeditbox.validate = function() {
//				var enc_nr   = $('memcateg_enc').value;
				var user_id  = $('create_id').value;
				var hcareid = $('hcare_id').value;

//				var dischrgdte = $('dischrgdate').value;
//				var dischrgtme = $('dischrgtme').value;
//				var categid    = $('categ_id').value;
//				var categdesc  = $('entrycategory_list').options[$('entrycategory_list').selectedIndex].text

//				var infosrc = $('meminfosrc').value;

				var data  = this.getData();

				xajax_saveEncounterInfo(data, user_id, hcareid);

						return false;
		};
}

function saveICDChanges(enc_nr, user_id) {
		var icdcodes = YAHOO.util.Selector.query("input[name=dcodes]").map(arrayfunc);
		var icddescs = YAHOO.util.Selector.query("input[name=descs]").map(arrayfunc);
		xajax_saveICDDescs(enc_nr, user_id, icdcodes, icddescs);
}

function saveICPChanges(enc_nr, user_id) {
		var refnos = YAHOO.util.Selector.query("input[name=refnos]").map(arrayfunc);
		var entrynos = YAHOO.util.Selector.query("input[name=entrynos]").map(arrayfunc);
		var sources = YAHOO.util.Selector.query("input[name=sources]").map(arrayfunc);
		var pcodes = YAHOO.util.Selector.query("input[name=pcodes]").map(arrayfunc);
		var pdescs = YAHOO.util.Selector.query("input[name=pdescs]").map(arrayfunc);
		var popdates = YAHOO.util.Selector.query("input[name=popdates]").map(arrayfunc);
		xajax_saveICPDetails(enc_nr, user_id, pcodes, refnos, entrynos, sources, pdescs, popdates);
}

// CREATED by JEFF 06-03-17
function reasonDelete(enc_nr,patient,insurance,bill_diff){
	// Trap
	// if ($j('#del_enc_nr').val() == "") {
	 $j('#delete-div').dialog({
            autoOpen: true,
            modal: true,
            show: 'fade',
            fade: 'fade',
            height: 275,
            width: 400,
            resizable: false,
            draggable: false,
            title: "DELETE CLAIM",
            position: "top",
            buttons: {
                "Done": function () {
                if ($j('#select-reason').val()=='0')
                	{
                	alert("Please select a reason or press cancel.");
                	}
                else if($j('#select-reason').val()=='3' && $j('#del_trans_other_reason').val()==''){
                	// if($j('#del_trans_other_reason').val()==''){
                		alert("Please provide reason for Others option");
                	}
                	else {
                	// Code here for saving the choice reason for delete...
                	// Declare vars...
                	var reasonID = $j('#select-reason').val();
                	var reasonHolder = $j('#del_trans_other_reason').val();
                	var delete_enc_nr= $j('#enc_nr').val(); // <-- encounter number...
                	var del_user_id  = $('create_id').value;
                	var reasonLabel  = $j('#select-reason option:selected').text();
                	var transmit_no = $('transmit_no').value; //added by carriane 09/20/17
                	var has_exceeded = $j('#has_exceeded').val();
                	var days_allowed = $j('#days_allowed').val();
                	console.log(has_exceeded);
                	console.log(days_allowed);
                	if(parseInt(bill_diff) > parseInt(days_allowed))
                		$j('#has_exceeded').val(--has_exceeded);
                	// Use function for fetching reason...
                	xajax_getDeleteReasonDesc(reasonID);

                	// Push data to array...
                	data_delete.push({"reason_id": reasonID,
                					  "reason_others": reasonHolder,
                					  "del_enc_nr": enc_nr,
                					  "del_logid": del_user_id,
                					  "del_patient": patient,
                					  "reasonLabel": reasonLabel,
                					  "transmit_no": transmit_no, //added by carriane 09/20/17
                				      "insurance_no": insurance});
                	// console.log(data_delete);
                	// Set values of choice in hidden fields...
                	$j('#reason_id').val(reasonID);
                	$j('#reason_others').val(reasonHolder);
                	$j('#del_enc_nr').val(enc_nr);
                	$j('#del_logid').val(del_user_id);
                	$j('#del_patient').val(patient);
                	$j('#insurance_no').val(insurance);

                	// Clear 1 row of transmimttal...
                	removeClaim(enc_nr);

                	// Close modal...
                    $j(this).dialog("close");

                    // Prompt when removing from list...
                	alert("Claim removed from list.");
                	}
                },
                "Cancel": function () {

                	$j('#reason_id').val("");
	            	$j('#reason_others').val("");
	            	$j('#del_enc_nr').val("");
	            	$j('#del_logid').val("");
	            	$j('#del_patient').val("");
	            	$j('#insurance_no').val("");

                	// Closing modal...
                    $j(this).dialog("close");
                }
            },
            open: function(){
            	$j('#del_trans_other_reason').hide();
            	$j('#reason_others').val("");
            	// Use xajax function...
            	xajax_getTransReasonDelete();
            }
        });
	 // }
	 // else{
	 // 	alert("There is a pending transmittal to be deleted. Press save or refresh to cancel.");
	 // 	}
	 // end Trap
	 
}
// ADDED by JEFF 06-03-17
// MODIFIED by JEFF 10-08-17
function transDeleteReason(){
    var reason = $j('#select-reason').val();

    if(reason == '3'){
        $j('#del_trans_other_reason').show();
        $j('#del_trans_other_reason').val('');
        $j('#reason_id').val('3');
        $j('#del_trans_reason').val(reason);
    }
    else{
        $j('#del_trans_other_reason').hide();
        $j('#reason_id').val(reason);
        $j('#del_trans_reason').val(reason);
        $j('#del_trans_other_reason').val("");
    }
}
// END by JEFF 06-03-17

function showSaveStatus(bSuccess, errMsg) {
	if (Number(bSuccess) == 1) {
		alert('Successfully saved the updates!');
		YAHOO.encounter.container.dataeditbox.hide();
				}
	else
		alert('ERROR in saving the updates!\n'+errMsg);
}

   /*
	*
	* Author : syboy 06/23/2015
	*
	*/
function initCataractFormsPrompt(){
		var handleSubmit = function() {
				this.submit();
		};
		var handleCancel = function() {
				this.cancel();
		};

		YAHOO.encounter.container.cataractformpromt = new YAHOO.widget.Dialog("cataractformpromt",
																				 { width : "450px",
																					fixedcenter : true,
																					visible : false,
																					constraintoviewport : true,
																					buttons : [ { text:"Update", handler:handleSubmit, isDefault:true },
																											{ text:"Cancel", handler:handleCancel } ]
																				 });

		YAHOO.encounter.container.cataractformpromt.validate = function() {
				var data  = this.getData();
				updateCataractNo(data);
				return true;
		};

}

	// end

function initFormsPrompt() {
		var handleSubmit = function() {
				this.submit();
		};
		var handleCancel = function() {
				this.cancel();
		};

		// Instantiate the Dialog
		YAHOO.encounter.container.formpromptdbox = new YAHOO.widget.Dialog("formpromptdbox",
			 { width : "390px",
				fixedcenter : true,
				visible : false,
				constraintoviewport : true,
				buttons : [ { text:"Print", handler:handleSubmit, isDefault:true },
										{ text:"Cancel", handler:handleCancel } ]
			 });

		YAHOO.encounter.container.formpromptdbox.validate = function() {
				var data  = this.getData();
				var frm   = data.forms_list;
				var enc_nr = data.encounter_nr;
				var newform = data.newform;
				printInsuranceForm(enc_nr, frm, newform);
				return true;
		};

//    YAHOO.util.Event.addListener("btnPrint", "click", showCategoryPrompt);
}

function initCategoryPrompt(){
		// Define various event handlers for Dialog
		var handleSubmit = function() {
				this.submit();
		};
		var handleCancel = function() {
				this.cancel();
		};

		// Instantiate the Dialog
		YAHOO.encounter.container.memcategdialogbox = new YAHOO.widget.Dialog("memcategdialogbox",
																																						 { width : "390px",
																																							fixedcenter : true,
																																							visible : false,
																																							constraintoviewport : true,
																																							buttons : [ { text:"Print", handler:handleSubmit, isDefault:true },
																																													{ text:"Cancel", handler:handleCancel } ]
																																						 });

		YAHOO.encounter.container.memcategdialogbox.validate = function() {
				var data  = this.getData();

//        if(data.category_list == '0') {
//            alert("Please select membership category!");
//            return false;
//        }
//        else {
				var transmit_no = $('old_trnsmit_no').value;
				var detailed = ($('is_detailed').checked) ? '1' : '0';
				printTransmittalLetter(transmit_no, data.category_list, detailed);
				return true;
//        }
		};

		YAHOO.util.Event.addListener("btnPrintTransmittal", "click", showCategoryPrompt);
}

/*
 * Author : syboy 06/23/2015
 */ 
function promptForFormsCataract(enc_nr, opsCode){

		xajax_loadDeffCode(enc_nr,opsCode);
		xajax_loadInputsCatCode(enc_nr);

		$('cataractformpromt').style.display = "";
		$('enc_nr').value = enc_nr;

		YAHOO.encounter.container.cataractformpromt.render();
		YAHOO.encounter.container.cataractformpromt.show();

}
// end

function promptForFormsPrinting(enc_nr, newform) {
		$('encounter_nr').value = enc_nr;
		$('newform').value = newform;

		$('formpromptdbox').style.display = "";
		$('forms_list').style.visibility = "";

		YAHOO.encounter.container.formpromptdbox.render();
		YAHOO.encounter.container.formpromptdbox.show();

		xajax_setFormsForSelection();
}

//added by Francis 09-02-13
function caseSurgical() {
	var check = $('surgicalCase').checked;
	if(check){
		$('medicalCase').checked = false;
	}
}

//added by Francis 09-02-13
function caseMedical() {
	var check = $('medicalCase').checked;
	if(check){
		$('surgicalCase').checked = false;
	}
}

function showCategoryPrompt() {
		$('memcategdialogbox').style.display = "";
		$('category_list').style.visibility = "";

		YAHOO.encounter.container.memcategdialogbox.render();
		YAHOO.encounter.container.memcategdialogbox.show();

		xajax_setMemCategoryOptionsForPrint();
}

function showEncInfoPrompt(pname, enc_nr, categ_id) {
		$('dataeditbox').style.display = "";
		$('entrycategory_list').style.visibility = "";
		$('memcateg_enc').value = enc_nr;
		$('patientname').value = pname;
		$('categ_id').value  = categ_id;

		YAHOO.encounter.container.dataeditbox.render();
		YAHOO.encounter.container.dataeditbox.show();

		categ_id = (typeof(categ_id) == 'undefined') ? '0' : categ_id;

		xajax_getPatientEncounterInfo(enc_nr);
		xajax_getPolicyHolderInfo(enc_nr, $('hcare_id').value, $('policyno_'+enc_nr).innerHTML);
		xajax_getDischrgDateTime(enc_nr);
		xajax_setMemCategoryOptions(Number(categ_id));
		dfunc(0);
}

function assignMemberInfo(data) {
		$('membernmlast').value = data.lastname;
		$('membernmfirst').value = data.firstname;
		$('membernmmid').value = data.midname;

		$('street_addr').value = data.street_name;
		$('barangay').value = data.barangay;
		$('municipality').value = data.municity;

		$('barangay_nr').value = data.brgy_nr;
		$('municipality_nr').value = data.mun_nr;

		$('meminfosrc').value = data.infosource;
}

function setMemCategoryOptions(categ_id) {
		xajax_setMemCategoryOptions(categ_id);
}

function js_ClearOptions(tagId){
	var optionsList, el=$(tagId);
	if(el){
		optionsList = el.getElementsByTagName('OPTION');
		for(var i=optionsList.length-1; i >=0 ; i--){
			optionsList[i].parentNode.removeChild(optionsList[i]);
		}
	}
}//end of function js_ClearOptions

function js_AddOptions(tagId, text, value, bselected){
		var elTarget = $(tagId);
		bselected = (typeof(bselected) == 'undefined') ? false : (bselected != '0');
		if(elTarget){
				var opt = new Option(text, value);
				//var opt = new Option(value, value);
				opt.selected = bselected;
				opt.id = value;
				elTarget.appendChild(opt);

			if (bselected) {
				$('categ_id').value = value;
				var enc_nr = $('memcateg_enc').value;
				$('categid_'+enc_nr).value = value;
				$('categ_desc').value = text;
			}
		}
		var optionsList = elTarget.getElementsByTagName('OPTION');
}//end of function js_AddOption

function jsCategoryOptionChange(obj, value, sdesc){
	if (obj.id== 'entrycategory_list') {
		$('categ_id').value   = value;
		var enc_nr = $('memcateg_enc').value;
		$('categid_'+enc_nr).value = value;
		$('categ_desc').value = sdesc;
	}
}

function gotoBreakFile(breakfile) {
		window.location = breakfile;
}

function clearList(listID) {
	// Search for the source row table element
	var list=$(listID),dRows, dBody;
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

function addslashes(str) {
	str=str.replace("'","\\'");
	return str;
}

function trimString(objct){
//    alert("inside frunction trimString: objct = '"+objct+"'");
	objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g,"");
}

function addDiagnosisToList(listID, diagnosis_nr, code, description, doctor, altdesc, isprimary) {
	var list=$(listID), dRows, dBody, rowSrc;
	var i;
//	alert("before diagnosis_nr, code, description, doctor =  "+diagnosis_nr+" , "+code+" , "+description+" , "+doctor);
	if (typeof(altdesc) == 'undefined') altdesc = '';
	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		//alert(dBody.id);
		dRows=dBody.getElementsByTagName("tr");

		var rows = document.getElementsByName('rows[]');
		if (rows.length == 0) {
			clearList(list);
		}

		if (diagnosis_nr) {
//			alert("after diagnosis_nr, code, description, doctor =  "+diagnosis_nr+" , "+code+" , "+description+" , "+doctor);
			alt = (dRows.length%2)+1;
			create_id = $('create_id').value;
			stmp = '<img style="cursor:pointer" title="Remove!" src="../../images/cashier_delete.gif" border="0" onclick="xajax_rmvCode('+diagnosis_nr+', \''+create_id+'\');"/>';
//			stmp = '&nbsp;';

			if (altdesc != '') description = altdesc;
			rowSrc = '<tr class="wardlistrow'+alt+'" id="row'+addslashes(diagnosis_nr)+'">'+
									'<input type="hidden" name="rows[]" id="index_'+addslashes(diagnosis_nr)+'" value="'+addslashes(diagnosis_nr)+'" />'+
									'<input type="hidden" name="dcodes" id="code_'+addslashes(diagnosis_nr)+'" value="'+code+'" />'+
							'<td align="center">'+code+'</td>'+
							'<td><input style="width:95%;display:none;" type="text" name="descs" id="descalt_'+addslashes(diagnosis_nr)+'" value="'+description+'" onFocus="this.select();" onblur="cancelAltDesc(\''+addslashes(diagnosis_nr)+'\');" onkeyup="applyAltDesc(event,\''+addslashes(diagnosis_nr)+'\', \''+code+'\');">'+
								'<span id="descmain_'+addslashes(diagnosis_nr)+'"><a style="cursor:pointer" onclick="editAltDesc('+addslashes(diagnosis_nr)+')">'+description+'</a></span></td>'+
							'<td>'+((doctor == '') ? '&nbsp;' : doctor)+'</td><td>'+((isprimary == '1') ? 'Primary' : 'Secondary')+'</td><td>'+ stmp + '</td>'+
				 '</tr>';
//			alert(rowSrc);
		}
		else {
			rowSrc = '<tr><td colspan="7">No diagnosis history available ...</td></tr>';
		}

		dBody.innerHTML += rowSrc;
		//alert(dBody.innerHTML);
	}
}

function removeAddedICD(id) {
		var destTable, destRows;
		var table = $('diagnosisList');
		var rmvRow=document.getElementById("row"+id);
		if (table && rmvRow) {
				var rndx = rmvRow.rowIndex-1;
				table.deleteRow(rmvRow.rowIndex);
				if (!document.getElementsByName("rows[]") || document.getElementsByName("rows[]").length <= 0)
						addDiagnosisToList(table, null);
				reclassRows(table,rndx);
		}
		else
				alert(table+' and '+rmvRow);
}

function editAltDesc(id) {
	$("descalt_"+id).style.display = "";
	$("descmain_"+id).style.display = "none";
	$("descalt_"+id).focus();
}

function cancelAltDesc(id) {
	$("descalt_"+id).style.display = "none";
	$("descmain_"+id).style.display = "";
}

function isESCPressed(e) {
	var kC  = (window.event) ?    // MSIE or Firefox?
			 event.keyCode : e.keyCode;
	var Esc = (window.event) ?
			27 : e.DOM_VK_ESCAPE // MSIE : Firefox
	return (kC==Esc);
}

function applyAltDesc(e, id, code) {
	var characterCode;
		var enc_nr = $('memcateg_enc').value;
	var user_id  = $('create_id').value;

	if (e) {
		if(e && e.which) { //if which property of event object is supported (NN4)
			characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
			characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}
	}
	else
		characterCode = 13;

	if ( (characterCode == 13) || (isESCPressed(e)) ) {
		var altdesc = $("descalt_"+id).value;
		if (altdesc != '') {
			$("descmain_"+id).innerHTML = '<a style="cursor:pointer" onclick="editAltDesc('+id+')">'+altdesc+'</a>';

			// At this point, save the encoded alternate description for the ICD code in table ...
//			xajax_saveAltDesc(enc_nr, code, altdesc, user_id);
		}
		$("descalt_"+id).style.display = "none";
		$("descmain_"+id).style.display = "";
	}
}

//var currentPage=0, lastPage=0;
//var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var AJAXTimerID=0;
//var lastSearch="";

var dfunc = function populateICDList(page) {
	var encounter;
	encounter_nr = $('memcateg_enc').value;

	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	$("d-ajax-loading").style.display = "";
	$("diagnosisList-body").style.display = "none";
	AJAXTimerID = setTimeout("xajax_populateDiagnosisList('"+encounter_nr+"',"+page+")",100);
}

function endpopulateICDList() {
	$("d-ajax-loading").style.display = "none";
	$("diagnosisList-body").style.display = "";

	pfunc(0);
}

var pfunc = function populateICPMList(page) {
	var encounter;
	encounter_nr = $('memcateg_enc').value;

	if (AJAXTimerID) clearTimeout(AJAXTimerID);
	$("p-ajax-loading").style.display = "";
	$("proceduresList-body").style.display = "none";
	AJAXTimerID = setTimeout("xajax_getCurrentOpsInEncounter('"+encounter_nr+"',"+page+")",100);
}

function endpopulateICPMList() {
	$("p-ajax-loading").style.display = "none";
	$("proceduresList-body").style.display = "";
}

function setPaginationICD(pageno, lastpage, pagen, total) {
//	----  	currentpage=parseInt(pageno);
	d_currentPage = parseInt(pageno);
	lastPage=parseInt(d_lastPage);
	firstRec = (parseInt(pageno)*pagen)+1;

	if (d_currentPage == lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;

	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

	$("pageFirst").className = (d_currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (d_currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (d_currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (d_currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function setPaginationICP(pageno, lastpage, pagen, total) {
//	----  	currentpage=parseInt(pageno);
	p_currentPage = parseInt(pageno);
	lastPage=parseInt(p_lastPage);
	firstRec = (parseInt(pageno)*pagen)+1;

	if (p_currentPage == lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;

	if (parseInt(total)==0)
		$("pageShow").innerHTML = '<span>Showing '+(lastRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';
	else
		$("pageShow").innerHTML = '<span>Showing '+(firstRec)+'-'+(lastRec)+' out of '+(parseInt(total))+' record(s).</span>';

	$("pageFirst").className = (p_currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (p_currentPage>0 && lastPage>0 && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (p_currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (p_currentPage<lastPage && total>10) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, cpg, lpg, fillfnc) {
	if (el.className=="segDisabledLink") return false;
	if (lpg.value == 0) return false;
	//alert(jumpType);
	//alert(d_currentPage+", "+d_lastPage);
	switch(jumpType) {
		case FIRST_PAGE:
			if (cpg.value == 0) return false;
			fillfnc(0);
		break;
		case PREV_PAGE:
			if (cpg.value == 0) return false;
			fillfnc(parseInt(cpg.value)-1);
		break;
		case NEXT_PAGE:
			if (cpg.value >= lpg.value) return false;
			fillfnc(parseInt(cpg.value)+1);
		break;
		case LAST_PAGE:
			if (cpg.value >= lpg.value) return false;
			fillfnc(parseInt(lpg.value));
		break;
	}
}

//function addCurrentOpsToList(listID, id, op_date, group_id, description, descriptionFull, rvu, multiplier, provider, refno, entryno, ncount) {
function addCurrentOpsToList(listID, id, op_date, description, descriptionFull, refno, entryno, provider) {
	var list=$(listID), dRows, dBody, rowSrc;
//	var i;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {
//						alert("addCurrentOpsToList : id = '"+id+"' "+listID);

			rowSrc = '<tr>'+
					 '    <input name="refnos" id="refno'+refno+'_'+id+'_'+entryno+'" type="hidden" value="'+refno+'">'+
					 '    <input name="entrynos" id="entryno'+refno+'_'+id+'_'+entryno+'" type="hidden" value="'+entryno+'">'+
					 '    <input name="sources" id="provider'+refno+'_'+id+'_'+entryno+'" type="hidden" value="'+provider+'">'+
					 '<td>'+
					 '    <span style="font:bold 12px Arial;color:#660000">'+id+'</span>'+
					 '    <input name="pcodes" id="code'+refno+'_'+id+'_'+entryno+'" type="hidden" value="'+id+'">'+
					 '</td>'+
					 '<td>'+
								'<input style="width:95%;display:none;" type="text" name="pdescs" id="procdesc_'+refno+'_'+id+'_'+entryno+'" value="'+descriptionFull+'" onFocus="this.select();" onblur="cancelProcDesc(\''+id+'\','+entryno+', \''+refno+'\');" onkeyup="saveProcDesc(event,\''+id+'\', \''+refno+'\', '+entryno+');">'+
								'<span id="pdescmain_'+refno+'_'+id+'_'+entryno+'"><a style="cursor:pointer" onclick="editProcDesc(\''+id+'\','+entryno+', \''+refno+'\')">'+descriptionFull+'</a></span>'+
//						'<span id="description'+id+'_'+entryno+'" style="font:bold 12px Arial">'+descriptionFull+'</span><br />'+
					 '</td>'+
					 '<td align="center">'+
					 '    <input style="width:95%;display:none;text-align:left" type="text" name="popdates" id="editopdte_'+refno+'_'+id+'_'+entryno+'" value="'+op_date+'" onFocus="this.select();" onblur="cancelEditOpDate(\''+id+'\','+entryno+', \''+refno+'\');" onkeyup="saveOpDate(event,\''+id+'\', \''+refno+'\', '+entryno+');">'+
					 '    <a id="showopdate_'+refno+'_'+id+'_'+entryno+'" style="cursor:pointer" onclick="editOpDate(\''+id+'\','+entryno+',\''+refno+'\')">'+op_date+'</a>'+
					 '</td></tr>';

//			if (provider == 'OA')
//				rowSrc += '<td align="center"><img src="../../images/btn_delitem.gif" style="border-right:hidden; cursor:pointer" onclick="delMiscOps(\''+id+'\')" ></td></tr>';
//			else
//				rowSrc += '<td align="center">&nbsp;</td></tr>';
		}
		else {
			rowSrc = '<tr><td colspan="3" style="">No procedure encoded yet ...</td></tr>';
		}

		dBody.innerHTML += rowSrc;
	}
}

function editProcDesc(id, entryno, refno) {
	$("procdesc_"+refno+'_'+id+'_'+entryno).style.display = "";
	$("pdescmain_"+refno+'_'+id+'_'+entryno).style.display = "none";
	$("procdesc_"+refno+'_'+id+'_'+entryno).focus();
}

function cancelProcDesc(id, entryno, refno) {
	$("procdesc_"+refno+'_'+id+'_'+entryno).style.display = "none";
	$("pdescmain_"+refno+'_'+id+'_'+entryno).style.display = "";
}

function saveProcDesc(e, id, refno, entryno) {
	var characterCode;
	var enc_nr   = $('memcateg_enc').value;
	var user_id  = $('create_id').value;

	if (e) {
		if(e && e.which) { //if which property of event object is supported (NN4)
			characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
			characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}
	}
	else
		characterCode = 13;

	if ( (characterCode == 13) || (isESCPressed(e)) ) {
		var altdesc = $("procdesc_"+refno+'_'+id+'_'+entryno).value;
		if (altdesc != '') {
			$("pdescmain_"+refno+'_'+id+'_'+entryno).innerHTML = '<a style="cursor:pointer" onclick="editProcDesc(\''+id+'\','+entryno+', \''+refno+'\')">'+altdesc+'</a>';

			// At this point, save the encoded alternate description for the ICD code in table ...
//			xajax_saveProcDesc(enc_nr, id, refno, entryno, altdesc, user_id);
		}
		$("procdesc_"+refno+'_'+id+'_'+entryno).style.display = "none";
		$("pdescmain_"+refno+'_'+id+'_'+entryno).style.display = "";
	}
}

function editOpDate(id, entryno, refno) {
	var $j = jQuery.noConflict();

	jQuery(function($){
		 $j("#editopdte_"+refno+'_'+id+'_'+entryno).mask("99-99-9999");
	});

	$("editopdte_"+refno+'_'+id+'_'+entryno).style.display = "";
	$("showopdate_"+refno+'_'+id+'_'+entryno).style.display = "none";
	$("editopdte_"+refno+'_'+id+'_'+entryno).focus();
}

function cancelEditOpDate(id, entryno, refno) {
	$("editopdte_"+refno+'_'+id+'_'+entryno).style.display = "none";
	$("showopdate_"+refno+'_'+id+'_'+entryno).style.display = "";
}

function saveOpDate(e, id, refno, entryno) {
	var characterCode;
	var enc_nr   = $('memcateg_enc').value;
	var user_id  = $('create_id').value;

	if (e) {
		if(e && e.which) { //if which property of event object is supported (NN4)
			characterCode = e.which; //character code is contained in NN4's which property
		}
		else {
			characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}
	}
	else
		characterCode = 13;

	if ( (characterCode == 13) || (isESCPressed(e)) ) {
		var altopdate = $("editopdte_"+refno+'_'+id+'_'+entryno).value;
		if (altopdate != '') {
			$("showopdate_"+refno+'_'+id+'_'+entryno).innerHTML = altopdate;

			// At this point, save the encoded alternate description for the ICD code in table ...
//			xajax_saveProcDesc(enc_nr, id, refno, entryno, altdesc, user_id);
		}
		$("editopdte_"+refno+'_'+id+'_'+entryno).style.display = "none";
		$("showopdate_"+refno+'_'+id+'_'+entryno).style.display = "";
	}
}

function showDischargeDateTime(dischrgdte, dischrgtme) {
	$('dischrgdate').value = dischrgdte;
	$('dischrgtme').value = dischrgtme;
}

function showInsuranceNr(insurance_nr) {
	$('insurance_nr').value = insurance_nr;
	$('oldinsurance_nr').value = insurance_nr;
}

function addICDCode() {
		var enc_nr   = $('memcateg_enc').value;
		var icd_code = $('icdCode').value;
		var enc_type = $('enc_type').value;
		var dischrgdtetm = $('dischrgdtetm').value;
		var dr_nr = $('doc_nr').value;
		var isprimary = $('is_primary').checked ? 1 : 0;

//		alert('Doc nr = '+dr_nr);
		xajax_addCode(enc_nr, enc_type, dischrgdtetm, dr_nr, icd_code, isprimary);
}

function setEncounterParams(enc_type, dschrg, drnr, gender) {
	$('enc_type').value = enc_type;
	$('dischrgdtetm').value = dschrg;
	$('doc_nr').value = drnr;
	$('gender').value = gender;
}

function allowMbrEdit() {
	if (!memberEditOn) {
		$('membernmlast').disabled = false;
		$('membernmfirst').disabled = false;
		$('membernmmid').disabled = false;

		$('street_addr').disabled = false;
		$('barangay').disabled = false;
		$('municipality').disabled = false;
		memberEditOn = true;
	}
	else {
		$('membernmlast').disabled = true;
		$('membernmfirst').disabled = true;
		$('membernmmid').disabled = true;

		$('street_addr').disabled = true;
		$('barangay').disabled = true;
		$('municipality').disabled = true;
		memberEditOn = false;
	}
}

function setMuniCity(mun_nr, mun_name) {
	$('municipality_nr').value = mun_nr;
	$('municipality').value = mun_name;
}

//added by nick, 3/28/2014
function saveXml(){
    showXmlLoading();
//    xajax_downloadClaimsXmlArchive($('transmit_no').value,$('memcat').value,$j('#memcat option:selected').html());
	xajax_downloadXmlFile($('transmit_no').value,$('memcat').value,$j('#memcat option:selected').html());
}

function saveXmlFile(xmlString,isValid){
	if(isValid == 0){
		if(confirm("The XML generated is not valid, download anyway?")){
			download(xmlString);
		}
	}else{
		download(xmlString);
	}
}

function download(xmlString){
    //xml = xmlString.replace(/-enye-/gi,"ñ");
    //xml = xml.replace(/-cenye-/gi,"Ñ");
    //console.log(xml);
	var blob = new Blob([xmlString], {type: "text/plain;charset=utf-8"});
	if($('transmit_no').value.trim() == ""){
		filename = "transmittal";
	}else{
		filename = $('transmit_no').value.trim();
        filename+= "(Membership Category:" + $j('#memcat option:selected').html() + ")"
	}
	saveAs(blob,filename + ".xml");
}

function showXmlLoading(){
	$j('#loadingBox').dialog({
		autoOpen:true,
		modal:true,
		title:"Loading",
		width:$("xmlLoading").width + 40,
		height:100,
		position:"top",
		closeOnEscape: false,
   		open: function(event, ui){
   			$j(".ui-dialog-titlebar-close", ui.dialog || ui).hide(); 
   		}
	});
	$('btnSaveXml').style.display = 'none';
}

function hideXmlLoading(){
	$j('#loadingBox').dialog("close");
	$('btnSaveXml').style.display = '';
}
//end nick

function xmlParamDialog(){
    $j("#xmlParams").dialog({
        modal:true,
        title:"Additional Parameters",
        width:450,
        position: 'top',
        buttons:{
            Generate:function(){
                saveXml();
                $j(this).dialog('close');
            },
            Cancel:function(){
                $j(this).dialog('close');
            }
        }
    });
}

//added by Nick 06-28-2014
function downloadClaimsXmlArchive(source,archive){
    hideXmlLoading();
    window.location.href="billing-claim-archive.php?filename="+archive;
}