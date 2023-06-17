var totalDiscount = 0, totalDiscountedAmount=0, totalNet=0, totalNONSocializedAmount=0;
var HSM = "HOSPITAL SPONSORED MEMBER";
var NBB = "SPONSORED MEMBER";
var privateAccomodation = 2;
var OBGYNE ="OBGUSD";


function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function clearEncounter() {
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('orderaddress').value="";
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('discount').value = '';
	$('discountid').value = '';

	$('refno').value = '';
	$('mode').value="save";

	$('btndiscount').disabled = false;
	$('sw-class').innerHTML = '';
	$('patient_enctype').innerHTML = '';
	$('patient_location').innerHTML = '';
	$('patient_medico_legal').innerHTML = '';

	$('current_att_dr_nr').value = '';
	$('current_dept_nr').value = '';
	$('impression').value = '';
	$('hrn').innerHTML = '';
	$('dob').innerHTML = '';
	$('sex').innerHTML = '';
	$('age').innerHTML = '';
	$('is_walkin').checked = false;

	if (iscash==true)
		$('is_cash').value = 1;
	else
		$('is_cash').value = 0;

	$('btnHistory').style.display = "none";
    $('btn-coverage').style.display = "none";
	$('ptype').value = "";
	$('orig_discountid').value = "";
	$('discount2').value = "";
	$('gender').value = "";
	$('date_birth').value = "";

	$('rid').value = "";

	$('ic_row').style.display = "none";
	$('is_charge2comp').checked = false;
	$('compName').value = "";
	$('compID').value = "";
	//$('source_req').value = "";

    $('iscash1').checked = true; 
    $('iscash0').checked = false; 
    $('is_cash').value = 1;
    $('grant_type').value = "";
    $('type_charge').style.display='none';
    // $('for_manual').disabled = false; # commented by: syboy 12/07/2015
    $('btn-coverage').style.display = "none";
    $('cov_type').update('');
    $('cov_amount').update('');
    $('coverage').setAttribute('value',-1);
    $('phic_ajax').hide();
    
    $('warningcaption').innerHTML = '';
    
    
    if (!iscash) {
        updateCoverage(['']);
    }

	setPriority(0);
}

function pSearchClose() {
    var nr = $('encounter_nr').value;
    //updatePHICCoverage([nr]);
    if (!$("iscash1").checked) {
        updateCoverage([nr]);
    }    
	cClick();  //function in 'overlibmws.js'
}

function emptyTray() {

	//added by BORJ 12-06-2013
	//block combobox change
	var prev_selected = $('grant_type').value;
	if ($('area').value=='clinic')
		{	
			if($('ptype').value==2 && $('phic_nr').length!=1) 
				{
			if($('grant_type').innerHTML == 'personal')
			return;
				if($('grant_type').value == 'phic' || $('grant_type').value == 'mission')
				{
  		    }
		else
			{
				$('grant_type').value = 'phic';
		}
	}
}	
	//end

	var items = document.getElementsByName('items[]');
	var id;

	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		$('rowID'+id).parentNode.removeChild($('rowID'+id));
		$('rowPrcCash'+id).parentNode.removeChild($('rowPrcCash'+id));
		$('rowPrcCharge'+id).parentNode.removeChild($('rowPrcCharge'+id));
		$('rowPrcNet'+id).parentNode.removeChild($('rowPrcNet'+id));
		$('rowQty'+id).parentNode.removeChild($('rowQty'+id));
		$('sservice'+id).parentNode.removeChild($('sservice'+id));
	}

	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshDiscount();
    ctmributtons();
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

function clearOrder(list) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		$('socialServiceNotes').style.display='none';
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function showSocialNotes() {
	var isShow='none';
	var sservice = document.getElementsByName('sservice[]');
	for (var i=0;i<sservice.length;i++) {
		if (sservice[i].value == 0) {
			isShow='';   //there is still a nonsocialized item in the list
		}
	}
	$('socialServiceNotes').style.display=isShow;
}

function appendOrder(list, details) {
	if (list) {
		var dBody = list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var isCash = $("iscash1").checked;

			var totalNetCash;
			var totalNetCashNoPf;
			var net_pf;
			var src, toolTipText;
			var btnicon;
			var paidcnt = 0;
			var lastRowNum = null,
				items = document.getElementsByName('items[]');
			dRows = dBody.getElementsByTagName("tr");
			var nf = new NumberFormat();
			var ptype = $('ptype').value;

			nf.setPlaces(2);
			//alert('details = '+details);
			console.log(details);
			if (details) {
				var id = details.id,
					idGrp = details.idGrp,
					qty = parseFloat(details.qty),
					prcCash = parseFloat(details.prcCash),
					prcCharge = parseFloat(details.prcCharge),
					pf  = parseFloat(details.pf),
					net_pf = parseFloat(details.net_pf),
				net_price = parseFloat(details.net_price)+ net_pf,
				netprice_nopf = parseFloat(details.net_price);

				totalNetCash = net_price * qty;
				totalNetCashNoPf = netprice_nopf * qty;

				//alert('1 totalNetCash = '+totalNetCash);
				alt = (dRows.length % 2) + 1;
				nf.setNumber(qty);
				nf.setPlaces(nf.NO_ROUNDING);
				qty = isNaN(qty) ? '0' : '' + nf.toFormatted();

				nf.setPlaces(2);
				nf.setNumber(prcCash);
				prcCash = isNaN(prcCash) ? 'N/A' : nf.toFormatted();
				nf.setNumber(prcCharge);
				prcCharge = isNaN(prcCharge) ? 'N/A' : nf.toFormatted();

				nf.setNumber(totalNetCash);
				totalNetCash = isNaN(totalNetCash) ? 'N/A' : nf.toFormatted();

				nf.setNumber(pf);
				pf = isNaN(pf) ? 'N/A' : nf.toFormatted();

				nf.setNumber(net_pf);
				net_pf = isNaN(net_pf) ? 'N/A' : nf.toFormatted();

				nf.setNumber(totalNetCashNoPf);
				totalNetCashNoPf = isNaN(totalNetCashNoPf) ? 'N/A' : nf.toFormatted();
				// alert(totalNetCashNoPf);
				if (isCash) {
					prc = prcCash;
				}
				else {
					prc = prcCharge;
				}

				tot = totalNetCash;
				totNoPf = totalNetCashNoPf;
				//alert('js= '+tot);
				//var person_discountid = $("discountid").value;

				toolTipText = "Requesting doctor: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + details.requestDocName + " <br>" +
					"Clinical Impression: <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" + details.clinicInfo;

				//added by VAS 03-21-2012
				// Check coverage limit
				if ($('grant_type').value == "phic") {
					//var coverageLimit = parseFloatEx($('cov_amount').innerHTML);
					var coverageLimit = parseFloatEx($('coverage').value);
					total = parseFloatEx(total-net_pf.replace(",","")) + parseFloatEx(tot.replace(",", "")-net_pf.replace(",",""));
					totalNoPf  = parseFloatEx(totalNoPf) + parseFloatEx(totNoPf.replace(",", ""));
					if (coverageLimit != -1) {
						
						if (coverageLimit < totalNoPf) {
							alert("Coverage limit exceeded for this item...");

							if(details.is_from_tray == 1)
							return true;
						}
					}
				}
				// }

				if (items) {
					for (var i = 0; i < items.length; i++) {
						if (items[i].value == details.id) {
							$('toolTipText' + id).value = toolTipText;
							$('rowPrcCash' + id).value = details.prcCash;
							$('rowPrcCharge' + id).value = details.prcCharge;
							$('rowPrcNet' + id).value = details.net_price;
							$('rowServed' + id).value = details.is_served;
							$('rowDoc' + id).value = details.requestDoc;
							$('rowDocName' + id).value = details.requestDocName;
							$('rowDept' + id).value = details.requestDept;
							$('rowHouse' + id).value = details.is_in_house;
							$('rowInfo' + id).value = details.clinicInfo;
							$('rowQty' + id).value = details.qty;
							document.getElementById('idGrp' + id).innerHTML = id + ' (' + details.dept + ')';
							document.getElementById('name' + id).innerHTML = details.name;
							document.getElementById('prc' + id).innerHTML = prc;
							document.getElementById('tot' + id).innerHTML = tot;
							//alert('update = '+tot);

							inPACS_icon = '<img name="in_PACS' + id + '" id="in_PACS' + id + '" src="../../images/charity.gif" border="0" title="The Procedure is already in PACS!">';
							notinPACS_icon = '<img name="not_inPACS' + id + '" id="not_inPACS' + id + '" src="../../images/notok.gif" border="0" onClick="post_PACSTest(\'' + id + '\');" style="cursor:pointer" title="Click the icon to post the Test to PACS">';

							if (details.inPACS)
								inPACS_icon = inPACS_icon;
							else
								inPACS_icon = notinPACS_icon;

							var name_serv = details.name;
							alert('"' + name_serv.toUpperCase() + '" is already in the list & has been UPDATED!');
							return true;
						}
					}
					if (items.length == 0)
						clearOrder(list);
				}

				delitemImg = '<a href="javascript: nd(); removeItem(\'' + id + '\');">' +
					'	<img src="../../images/btn_delitem.gif" border="0"/></a>';

				if (typeof details.request != 'undefined') {
					if ((parseInt(details.request.allowDelete) == 0) ||(details.pay_type == 'charity' && details.is_served == 1)) { //Updated by Christian 12-31-19
						delitemImg = '<img src="../../images/btn_delitem.gif" border="0" style="opacity:0.3;" title="{message}"/>';
						delitemImg = delitemImg.replace('{message}',details.request.message);
						$('btnEmpty').src = '';
					} else {
						delitemImg = '<a data-warning="{warning}" id="delete{id}" href="javascript: nd(); removeItem(\'{id}\');"><img src="../../images/btn_delitem.gif" border="0"/></a>'
						delitemImg = delitemImg.replace(/\{id\}/g,id).replace('{warning}',details.request.warning);
						$('btnEmpty').src = '../../images/btn_emptylist.gif';
					}
				}

				paiditemImg = '<img src="../../images/btn_paiditem.gif" border="0" onClick="">';
				unpaiditemImg = '<img src="../../images/btn_unpaiditem.gif" border="0" onClick="">';

				charityImg = '<img src="../../images/btn_charity.gif" border="0" onClick="">';
				cmapImg = '<img src="../../images/btn_cmap.gif" border="0" onClick="">';
				lingapImg = '<img src="../../images/btn_lingap.gif" border="0" onClick="">';
				missionImg = '<img src="../../images/btn_mission.gif" border="0" onClick="">';

				// added by VAN 01-15-08
				repeatitemImg = '<img src="../../images/btn_repeat.gif" border="0" onClick="">';

				refno_hasPaid = $F('hasPaid');
				view_mode = 0;
				if ($F('view_from') != '')
					view_mode = 1;
				toolTipTextHandler = ' onMouseOver="return overlib($(\'toolTipText' + id + '\').value, CAPTION,\'Details\',  ' +
					'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', ' +
					'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();"';
				nonSocialized = '';
				if (details.sservice == 0) {
					nonSocialized = '<img src="../../images/btn_nonsocialized.gif" border="0" onClick=""' +
						' onMouseOver="return overlib(\'This is a non-socialized service which means..secret!\', CAPTION,\'Non-socialized Service\',  ' +
						'  TEXTPADDING, 8, CAPTIONPADDING, 4, TEXTFONTCLASS, \'oltxt\', CAPTIONFONTCLASS, \'olcap\', ' +
						'  WIDTH, 250,FGCLASS,\'olfgjustify\',FGCOLOR, \'#bbddff\');" onmouseout="nd();">';
					$('socialServiceNotes').style.display = '';

				}

				if (view_mode == 1)
					btnicon = ((details.hasPaid == 1) ? paiditemImg : unpaiditemImg);
				else {
					if ((($('parent_refno').value != null) || ($('parent_refno').value != "")) && ($('repeat').checked)) {
						btnicon = repeatitemImg;
					} else if ($('is_cash').value == 1) {
						if (($('hasPaid').value == 1) || (details.hasPaid)) {
							if (details.pay_type != "") {
								if (details.pay_type == 'paid')
									btnicon = paiditemImg;
								//Updated by Christian 12-03-19
								else if ((details.pay_type == 'lingap') || (details.pay_type == 'cmap')
									|| (details.pay_type == 'mission') || (details.pay_type == 'charity') || (details.pay_type == 'crcu')){
										var user_fromEl = window.parent.$('user_from');
										var user_from = '';
										if(typeof(user_fromEl) != 'undefined' && user_fromEl != null){
											user_from = user_fromEl.value;
										}
											if(details.pay_type == 'charity'){
												if(user_from != "DOCTOR"){
													delitemImg = '';
												}
												btnicon = delitemImg+'<img src="../../images/btn_' + details.pay_type + '.gif" border="0" onClick="">';
										} //Updated end by Christian 12-03-19
										else
									btnicon = '<img src="../../images/btn_' + details.pay_type + '.gif" border="0" onClick="">';
								}
								else
									btnicon = delitemImg;

								paidcnt = +1;
								disabled = "";
							} else {
								if (paidcnt >= 1)
									btnicon = unpaiditemImg;
								else
									btnicon = delitemImg;
								disabled = "disabled";
							}

						} else {
							btnicon = delitemImg;
							disabled = "disabled";
						}
					} else {
						if ($('grant_type').value != "") {
							if ($('mode').value == 'update') {
								//btnicon = '<img src="../../images/btn_'+$('grant_type').value+'.gif" border="0" onClick="">';
								if ((details.pay_type == 'lingap') || (details.pay_type == 'cmap')
									|| (details.pay_type == 'mission') || (details.pay_type == 'charity'))
									btnicon = '<img src="../../images/btn_' + details.pay_type + '.gif" border="0" onClick="">';
								else
									btnicon = delitemImg;

								disabled = "";
							} else {
								btnicon = delitemImg;
								disabled = "disabled";
							}
						} else {
							btnicon = delitemImg;
							disabled = "";
						}
					}
				}

				if(details.fromdept=='OB'){
					src =
					'<tr class="wardlistrow' + alt + '" id="row' + id + '"> ' +
					'<input type="hidden" name="toolTipText' + id + '" id="toolTipText' + id + '" value="' + toolTipText + '" />' +
					'<input type="hidden" name="sservice[]" id="sservice' + id + '" value="' + details.sservice + '" />' +
					'<input type="hidden" name="pf[]" id="rowPf' + id + '" value="' + details.pf + '" />' +
					'<input type="hidden" name="pcash[]" id="rowPrcCash' + id + '" value="' + details.prcCash + '" />' +
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge' + id + '" value="' + details.prcCharge + '" />' +
					'<input type="hidden" name="items[]" id="rowID' + id + '" value="' + id + '" />' +
					'<input type="hidden" name="is_served[]" id="rowServed' + id + '" value="' + details.is_served + '" />' +
					'<input type="hidden" name="requestDoc[]" id="rowDoc' + id + '" value="' + details.requestDoc + '" />' +
					'<input type="hidden" name="requestDept[]" id="rowDept' + id + '" value="' + details.requestDept + '" />' +
					'<input type="hidden" name="requestDocName[]" id="rowDocName' + id + '" value="' + details.requestDocName + '" />' +
					'<input type="hidden" name="isInHouse[]" id="rowHouse' + id + '" value="' + details.is_in_house + '" />' +
					'<input type="hidden" name="clinicInfo[]" id="rowInfo' + id + '" value="' + details.clinicInfo + '" />' +
					'<input type="hidden" name="pnet[]" id="rowPrcNet' + id + '" value="' + details.net_price + '" />' +
					'<input type="hidden" name="pfnet[]" id="rowPfprNet' + id + '" value="' + details.net_pf + '" />' +
					'<input type="hidden" name="pnetbc[]" id="rowPrcNetbc' + id + '" value="' + details.net_price + '" />' +
					'<input type="hidden"  name="qty[]" id="rowQty' + id + '" itemID="' + id + '" value="' + details.qty + '">' +
					'<input type="hidden"  name="inPACS' + id + '" id="inPACS' + id + '" itemID="' + id + '" value="' + details.in_pacs + '">' +
					'<input type="hidden"  name="pacs_code' + id + '" id="pacs_code' + id + '" itemID="' + id + '" value="' + details.pacs_code + '">' +
					'<input type="hidden"  name="ctMri[]" id="ctMri' + id + '" value="' + details.dept + '">' +
					'<input type="hidden"  name="radId[]" id="radId' + id + '" value="' + id + '">' +

					'<td class="centerAlign">' + btnicon + '</td>' +
					'<td align="centerAlign">' + nonSocialized + '</td>' +
					'<td id="idGrp' + id + '"' + toolTipTextHandler + '>' + id + ' (' + details.dept + ')</td>' +
					'<td id="name' + id + '"' + toolTipTextHandler + '>' + details.name + '</td>' +
					'<td class="rightAlign" id="ppf' + id + '">' +pf + '</td>' +
					'<td class="rightAlign" id="prc' + id + '">' + prc + '</td>' +
					'<td class="rightAlign" id="tot' + id + '">' + tot + '</td>' +
					'</tr>';
				}
				else{
					src =
					'<tr class="wardlistrow' + alt + '" id="row' + id + '"> ' +
					'<input type="hidden" name="toolTipText' + id + '" id="toolTipText' + id + '" value="' + toolTipText + '" />' +
					'<input type="hidden" name="sservice[]" id="sservice' + id + '" value="' + details.sservice + '" />' +
					'<input type="hidden" name="pf[]" id="rowPf' + id + '" value="0" />' +
					'<input type="hidden" name="pcash[]" id="rowPrcCash' + id + '" value="' + details.prcCash + '" />' +
					'<input type="hidden" name="pcharge[]" id="rowPrcCharge' + id + '" value="' + details.prcCharge + '" />' +
					'<input type="hidden" name="items[]" id="rowID' + id + '" value="' + id + '" />' +
					'<input type="hidden" name="is_served[]" id="rowServed' + id + '" value="' + details.is_served + '" />' +
					'<input type="hidden" name="requestDoc[]" id="rowDoc' + id + '" value="' + details.requestDoc + '" />' +
					'<input type="hidden" name="requestDept[]" id="rowDept' + id + '" value="' + details.requestDept + '" />' +
					'<input type="hidden" name="requestDocName[]" id="rowDocName' + id + '" value="' + details.requestDocName + '" />' +
					'<input type="hidden" name="isInHouse[]" id="rowHouse' + id + '" value="' + details.is_in_house + '" />' +
					'<input type="hidden" name="clinicInfo[]" id="rowInfo' + id + '" value="' + details.clinicInfo + '" />' +
					'<input type="hidden" name="pnet[]" id="rowPrcNet' + id + '" value="' + details.net_price + '" />' +
					'<input type="hidden" name="pfnet[]" id="rowPfprNet' + id + '" value="' + details.net_pf + '" />' +
					'<input type="hidden" name="pnetbc[]" id="rowPrcNetbc' + id + '" value="' + details.net_price + '" />' +
					'<input type="hidden"  name="qty[]" id="rowQty' + id + '" itemID="' + id + '" value="' + details.qty + '">' +
					'<input type="hidden"  name="inPACS' + id + '" id="inPACS' + id + '" itemID="' + id + '" value="' + details.in_pacs + '">' +
					'<input type="hidden"  name="pacs_code' + id + '" id="pacs_code' + id + '" itemID="' + id + '" value="' + details.pacs_code + '">' +
					'<input type="hidden"  name="ctMri[]" id="ctMri' + id + '" value="' + details.dept + '">' +
					'<input type="hidden"  name="radId[]" id="radId' + id + '" value="' + id + '">' +

					'<td class="centerAlign">' + btnicon + '</td>' +
					'<td align="centerAlign">' + nonSocialized + '</td>' +
					'<td id="idGrp' + id + '"' + toolTipTextHandler + '>' + id + ' (' + details.dept + ')</td>' +
					'<td id="name' + id + '"' + toolTipTextHandler + '>' + details.name + '</td>' +
					'<td class="rightAlign" id="prc' + id + '">' + prc + '</td>' +
					'<td class="rightAlign" id="tot' + id + '">' + tot + '</td>' +
					'</tr>';

				}
				
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"10\">Request list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;

			ctmributtons();

			document.getElementById('counter').innerHTML = items.length;

			return true;
		}
	}
	return false;
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

//added by borj 2013/06/12
function transactionType()
{
	var IPBMOPD = 14;

if ($('area').value=='clinic')
{
		if ($('ptype').value==2 || $('ptype').value==IPBMOPD)
		{

			if($('phic_nr').innerHTML!="None")
			{

				if($('iscash1').checked==true)
				{
					$("iscash0").checked = false;
					$("iscash1").checked = true;
				}
					else if($('iscash1').checked==false)
				{
					$("iscash0").checked = true;
					$("iscash1").checked = false;
					$('grant_type').value = "phic";
					$('grant_type').show();

				return;
				}
			}
		else
			{
				//alert('Charging is only allowed for current hospital patients..')
				$("iscash0").checked = false;
				$("iscash1").checked = true;
			    $('grant_type').hide();
			}
		}

	}
}
//end


function removeItem(id) {

	// var warning = $('delete' + id).getAttribute('data-warning');
	// if (warning)
	// 	warning = 'Warning! ' + warning;

	// if (confirm("Are you sure you want to delete this item?\n" + warning)) {
		var destTable, destRows;
		var table = $('order-list');
		var rmvRow = document.getElementById("row" + id);
		if (table && rmvRow) {
			$('rowID' + id).parentNode.removeChild($('rowID' + id));
			$('rowPrcCash' + id).parentNode.removeChild($('rowPrcCash' + id));
			$('rowPrcCharge' + id).parentNode.removeChild($('rowPrcCharge' + id));
			$('rowPrcNet' + id).parentNode.removeChild($('rowPrcNet' + id));
			$('rowQty' + id).parentNode.removeChild($('rowQty' + id));
			$('sservice' + id).parentNode.removeChild($('sservice' + id));
			var rndx = rmvRow.rowIndex - 1;
			table.deleteRow(rmvRow.rowIndex);
			reclassRows(table, rndx);
		}

		var items = document.getElementsByName('items[]');
		if (items.length == 0) {
			emptyIntialRequestList();
		}

		document.getElementById('counter').innerHTML = items.length;
		showSocialNotes();
		refreshDiscount();
		ctmributtons();
	// }
}

//created by Francis L.G 02-03-13
function ctmributtons(){

        var ctmri = document.getElementsByName('ctMri[]');
        var radId = document.getElementsByName('radId[]');
        var cm;
        var ct = 0;
        var mri = 0;
        var radServ = new Array();
        var nr = $('pid').value;
        var ref = $('refno').value;
        var submit = 0;

        if (($F('view_from')=='ssview')||($F('view_from')=='override')){
                enableSubmitButton(0);
                submit = 0;
        }else{
                enableSubmitButton(1);
                submit = 1;
        }

        if(ctmri){
            for (var i=0;i<ctmri.length;i++) {
                cm = ctmri[i].value;
                radServ[i] = radId[i].value;
                if(cm=='CT')ct+=1;
                if(cm=='MRI')mri+=1;
            }
        }

        if(ct==0){
            $('btnCTScan').style.display = "none";
        }
        else{
            $('btnCTScan').style.display = "";
        }


        if(mri==0){
            $('btnMRI').style.display = "none";
        }
        else{
            $('btnMRI').style.display = "";
        }

        if(ct>0||mri>0) {
        	xajax_chkCLhis(nr,ref,radServ,submit);
        }


}



//created by Francis L.G 02-03-13
function submitAllow(){

    enableSubmitButton(1);

}

//created by Francis L.G 02-03-13
function submitDisable(){

    enableSubmitButton(0);

}

/* added by Macoy June 24, 2014
to show PMDT, Personal Charging and NSC-M */
//----------------START-------------------//
function showDiscount(discounts){
	if(discounts == "all"){
		$J("#grant_type option").css({
			"display":""
		});
	}else{
		$J("#grant_type option").css({
			"display":"none"
		});
		$J.each(discounts,function(index,value){
			$J("#grant_type option").each(function(){
				if($J(this).val().trim() == value.trim()){
					$J(this).css({
						"display":""
					});
				}
			});
		});
	}
}
//-----------------END-------------------//
function changeTransactionType() {
	var iscash = $("iscash1").checked;
	var prcList, id, total=0;
	var pid = $('pid').value;
	var encounter_nr = $('encounter_nr').value;
	//clearEncounter();

    var mgh = $('is_maygohome').value;
    var bill_nr = $('bill_nr').value;
    var warning = $('warningcaption').innerHTML;
    var from_dept = $('from_dept').value;

    // alert(from_dept);
	if ((pid)&&(!encounter_nr)&&(!iscash)&&(from_dept=='OB')){
		alert('Charging is only allowed for current hospital patients...');
		$("iscash1").checked = true;
		iscash = true;
    }else if ((mgh==1) && (bill_nr!='') &&(!iscash)){
        //mgh or have save billing
        alert('Charging is NOT allowed to this patient. '+warning);
        $("iscash1").checked = true;
        iscash = true;
	}else{
		if (iscash){
			$('sw-class').innerHTML = $F('discountid');
			prcList = document.getElementsByName("pcash[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = false;
		}else{
			$('sw-class').innerHTML = 'None';
			prcList = document.getElementsByName("pcharge[]");
			$('is_walkin').checked = false;
			$('is_walkin').disabled = true;
		}
		//added by borj 2013/06/12
		transactionType();
		//end
		if (iscash==true){
			$('is_cash').value = 1;
			$('type_charge').style.display='none';
			//$('ornumber_title').style.display = '';	//added by cha, 11-23-2010

			/* added by: syboy 11/13/2015 : meow */
			if ($('radio_manual').value != 1) {
				$('for_manual').disabled = true;
			}else{
				$('for_manual').disabled = false;
			}
			/* Ended syboy */

            $('btn-coverage').style.display = "none";

		}else{
			$('is_cash').value = 0;
			$('type_charge').style.display='';
			//$('ornumber_title').style.display = 'none';	//added by cha, 11-23-2010

			$('for_manual').disabled = true;
			$('for_manual').checked = false;

            $('btn-coverage').style.display = "";
			//added by VAN 06-02-2011
			setManualPayment();
		}
		//$('type_charge').style.display='';

		for (var i=0;i<prcList.length;i++) {
			if (iscash)
				id = prcList[i].id.substring(10);
			else
				id = prcList[i].id.substring(12);
			$('prc'+id).innerHTML = formatNumber(prcList[i].value,2);
			$('tot'+id).innerHTML = formatNumber(parseFloat($('rowQty'+id).value)*parseFloat(prcList[i].value),2);
		}

        if ($('encounter_nr').value && !$("iscash1").checked){
            updateCoverage([$('encounter_nr').value])
        }else{
            $('cov_type').update('');
            $('cov_amount').update('');
            $('coverage').setAttribute('value',-1);
            $('phic_ajax').hide();
        }

		refreshDiscount();
	}
}

function refreshDiscount() {

	//added by Macoy June 24, 2014
	//-------------------------------------------//
	if($('pid').value == ""){
		discounts = "all";
	}else if($('ptype').value == ""){
		discounts = ["","pmdt","nscm"]; //modified by EJ 12/24/2014
	}else{
		discounts = "all";
	}
	showDiscount(discounts);
	//-------------------------------------------//
	//var nodes = $("discount");
	totalDiscount = 0;
	total_coverage = 0;
	totalNet = 0;
	totalDiscountedAmount = 0;
	totalNONSocializedAmount = 0;

	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var pf = document.getElementsByName('pf[]');
	var net = document.getElementsByName('pnet[]');
	var pfnet = document.getElementsByName('pfnet[]');
	// dito na mi  3131350
	var sservice = document.getElementsByName('sservice[]');
	var prcCash, prcCharge, prcNet,prcPfNet,prcPf, id, isCash = $("iscash1").checked;
	var qty = document.getElementsByName('qty[]');
	//var person_discountid = $("discountid").value;
	var rad_dept = $('source_req').value;
	var from_ob = $('from_dept').value;
	for (var i=0;i<items.length;i++) {
		id = items[i].value;
		prcCash = parseFloat(cash[i].value);
		//totalCash = prcCash*parseFloat(qty[i].value);
		prcCharge = parseFloat(charge[i].value);
		//totalCharge = prcCharge*parseFloat(qty[i].value);
		prcNet = parseFloat(net[i].value);
		if(rad_dept==OBGYNE || from_ob == 'OB'){
		prcPfNet = parseFloat(pfnet[i].value);
		prcPf= parseFloat(pf[i].value);
		}
		else{
		prcPfNet = 0;
		prcPf = 0;
		}
		// alert(rad_dept);
		// alert(OBGYNE);

		if (isCash)
			totalPrice = prcCash*parseFloat(qty[i].value);
		else
			totalPrice = prcCharge*parseFloat(qty[i].value);


		totalNet = prcNet*parseFloat(qty[i].value);
		if(prcPf>=prcPfNet){
			totalPFNet = prcPfNet - prcPf;
		}
		else{
			totalPFNet = prcPf - prcPfNet;
		}
		
		// alert(prcPfNet+"-"+prcPf);
		totalDiscount = totalPrice - totalNet - totalPFNet;
			
			

		totalDiscountedAmount += totalDiscount;
	}
	//alert('totalDiscountedAmount = '+totalDiscountedAmount);

    document.getElementById('discountTotal').value = totalDiscount;

	refreshTotal();
}

function refreshTotal() {
	var items = document.getElementsByName('items[]');
	var cash = document.getElementsByName('pcash[]');
var pf = document.getElementsByName('pf[]');
	var served = document.getElementsByName('is_served[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var accomodation = $('accomodation').value;
	var admission_accomodation = $('admission_accomodation').value;
	var isCash = $("iscash1").checked;
	var nf = new NumberFormat();
	var NetTotal = 0;
	var total_coverage = 0;

	totalPf = 0.0;
	total = 0.0;
	totalNoPf = 0.0;
	for (var i=0;i<items.length;i++) {
		
		if (isCash){
			total+=parseFloat(cash[i].value)+parseFloat(pf[i].value)*parseFloat(qty[i].value);
			totalNoPf+=parseFloat(cash[i].value)+parseFloat(pf[i].value)*parseFloat(qty[i].value);

			totalPf+=parseFloat(pf[i].value);
}else{
			total+=parseFloat(charge[i].value)+parseFloat(pf[i].value)*parseFloat(qty[i].value);
			totalNoPf+=parseFloat(cash[i].value)*parseFloat(qty[i].value);

	if(served[i].value==1) total_coverage+=parseFloat(charge[i].value)*parseFloat(qty[i].value);
			totalPf+=parseFloat(pf[i].value);

	
}
}


	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");
	var coverage=parseFloatEx($('coverage').value);
		//var dAdjAmount = $("show-discount");
		NetTotal =  total - totalDiscountedAmount;

		subTotal.innerHTML = formatNumber(total.toFixed(2),2);
		discountTotal.innerHTML = "-"+formatNumber(totalDiscountedAmount.toFixed(2),2);
		netTotal.innerHTML = formatNumber(NetTotal.toFixed(2),2);

        document.getElementById('netTotal').value = formatNumber(NetTotal.toFixed(2),2);
    // alert(total_coverage);
    if ($('mode').value=='update') total_coverage=0;
    if ($('coverage').value!=-1 && !$("iscash1").checked) {
        var coverage=parseFloatEx($('coverage').value)
        if($('mem_category').innerHTML == HSM){
			$('cov_amount').update('HSM');
		}else if ($('mem_category').innerHTML == NBB){
			$('cov_amount').update('NBB');
		} else{
			$('cov_amount').update(formatNumber(coverage-total_coverage,2));
		}
    }

    var coverageAmount = document.getElementById('cov_amount').innerHTML;
	var getCoverangeAmount = $('cov').value;
    if(coverageAmount == "0.00" && (accomodation == privateAccomodation || admission_accomodation == privateAccomodation || $('area_type').value=='pw')) {
    	if(getCoverangeAmount == 0 && $('grant_type').value!='phic'){
	    	removeTplChargeType(1);
	    }
    }
}

Array.prototype.max = function() {
var max = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
return max;
}

Array.prototype.min = function() {
var min = this[0];
var len = this.length;
for (var i = 1; i < len; i++) if (this[i] < min) min = this[i];
return min;
}

function preset(iscash){
	//var view_from = window.parent.$('view_from');

	//var source = $('source').value;
	var popup = $('popUp').value;
	var ptype = $('ptype').value;


	if ($('ispayfull').checked)
		 checkIfFull();

	//if (view_from)
		//$('view_from').value =  view_from.value;
	//alert($F('view_from'));

	showDiscount("all"); //added by Macoy June 24, 2014

	$("iscash1").focus();

	if ($('discountid').value=='SC')
		$('issc').checked = true;

	if (($('view_from').value=='ssview') || ($('view_from').value=='override'))
		$('btndiscount').style.display = "";
	else
		$('btndiscount').style.display = "none";

	iscash = $("iscash1").checked;

	if (iscash==true){
		$('is_cash').value = 1;
		$('type_charge').style.display='none';
	}else{
		$('is_cash').value = 0;
		$('type_charge').style.display='';
	}
	//$('type_charge').style.display='';

	if ($F('ordername')){
		$('clear-enc').disabled = false;
		$('btnHistory').style.display = "";

        if (iscash==true){
            $('btn-coverage').style.display = "none";
        }else{
            $('btn-coverage').style.display = "";
        }

        $('btnCTScan').style.display = "none";
        $('btnMRI').style.display = "none";
		//$('btnOther').style.display = "";
	}else{
		$('clear-enc').disabled = true;
		$('btnHistory').style.display = "none";
        $('btn-coverage').style.display = "none";
		//$('btnOther').style.display = "none";


		$('btnCTScan').style.display = "none";
		$('btnMRI').style.display = "none";
	}

	if (($F('view_from')=='ssview')||($F('view_from')=='override')){
		$('btndiscount').style.display='';

		if ($F('view_from')=='override')
			$('override_row').style.display = "";
		else
			$('override_row').style.display = "none";
	}else{
		$('override_row').style.display = "none";
		//enableSubmitButton(1);
	}

	if (($F('hasPaid')==1)||($('repeat').checked)||($F('view_from')=='ssview')||($F('view_from')=='override')){
		$('ordername').readOnly=true;
		$('orderaddress').readOnly=true;
		$('ordername').readOnly=true;

		$('select-enc').setAttribute("onclick","");
		$('select-enc').setAttribute("class","disabled");
		$('select-enc').style.cursor='default';

		$('clear-enc').disabled = true;
		$('clear-enc').style.cursor='default';

		$('btnAdd').setAttribute("onclick","");
		$('btnAdd').setAttribute("class","disabled");
		$('btnAdd').style.cursor='default';

		$('btnEmpty').setAttribute("onclick","");
		$('btnEmpty').setAttribute("class","disabled");
		$('btnEmpty').style.cursor='default';

		$('btndiscount').disabled = true;

		//$('btnCancel').setAttribute("onclick","");
		//$('btnCancel').setAttribute("class","disabled");
		//$('btnCancel').style.cursor='default';

		$('iscash0').disabled=true;
		$('iscash1').disabled=true;

		$('comments').readOnly=true;

		document.getElementsByName('btnRefreshDiscount').disabled = true;
		document.getElementsByName('btnRefreshTotal').disabled = true;
	}

	var refno = document.getElementById('refno').value;
	var area_type = $('area_type').value;

	if ($('mode').value=='save'){
		// er patient and payward patient
		if ((area_type=='pw')||(ptype==1)||($('is_charge2comp').checked)||($('source_req').value=='RDU')){
			$("iscash1").checked = false;
			$("iscash0").checked = true;
			$('type_charge').style.display='';
		}else if (area_type=='ch'){
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}else{
			$("iscash1").checked = true;
			$("iscash0").checked = false;
		}

		setPriority(0);
	}

	if (($F('view_from')=='ssview')||($F('view_from')=='override')){
		enableSubmitButton(0);
	}else{
		enableSubmitButton(1);
	}

	// For Industrial Clinic
	// Edited by James 2/14/2014
	if ($('source_req').value=='IC'){
		 if ($('is_charge2comp').checked)
				$('ic_row').style.display = '';
		 else{
				$('ic_row').style.display = 'none';
				$('iscash0').disabled = true; // for disabling Charge radio
		 }
	}else{
		 $('ic_row').style.display = 'none';
	}

	CheckRepeatInfo();

	//added by VAN 06-02-2011
	setManualPayment();

	if ($J("#pid").val() != "") {
		removeTplChargeType(0); 
	}

}

function CheckRepeatInfo(id){
		var isrepeat = '<?=$_GET["repeat"];?>';
		if (($('repeat').checked)||(isrepeat==1)){
			$('grant_type').value="";
			document.getElementById('repeatinfo').style.display = '';
		}else	{
			document.getElementById('repeat').disabled = true;
			document.getElementById('repeatinfo').style.display = 'none';
		}
	}

function emptyIntialRequestList(){
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
}
/* added rnel */

function setDisableRadRadioButton(hasPaid) {
	if(hasPaid == 1) {
		if($('iscash0').checked) {
			$('iscash0').disabled=true;
			$('grant_type').disabled=true;
		}
		$('iscash1').disabled=true;
		return true;
	}
}

/* end rnel */

function initialRequestList(serv_code, grp_code, name, c_info,r_doc, r_doc_name, n_house, cash, charge,hasPaid, sservice, in_pacs, pacs_code,parent_batch, head, remarks, qty,discounted_price, doc_dept, pay_type,orNumber, groupCode, request, is_served,pf,fromdept,net_pf) {
	var details = new Object();
	var withpaid = 0;
	var isrepeat = '<?=$_GET["repeat"];?>';

	details.requestDoc = r_doc;
	details.requestDocName = r_doc_name;
	details.is_in_house = n_house;
	details.clinicInfo = c_info;
	details.idGrp = grp_code;
	details.id = serv_code;
	details.qty = qty;
	details.name = name;
	details.is_served = is_served;
	details.dept = grp_code;

	details.prcCash = cash;
	details.prcCharge = charge;
	details.hasPaid = hasPaid;
	details.sservice = sservice;
	details.discounted_price = discounted_price;

	details.requestDept = doc_dept;
	details.net_price = discounted_price;
	details.pay_type = pay_type;
	//alert(details.pay_type);
	details.parent_batch = parent_batch;
	details.head = head;
	details.remarks = remarks;

	details.in_pacs = in_pacs;
	details.pacs_code = pacs_code;
	details.request = request;
	details.is_from_tray = 0;
	details.pf = pf;
	details.fromdept = fromdept;
	details.net_pf = net_pf;
	console.log(details);

	details.is_served = is_served; //Added by Christian 12-31-19

	if (($('repeat').checked) || (isrepeat == 1)) {
		details.discounted_price = 0;
		details.net_price = 0;
	}
		setDisableRadRadioButton(hasPaid); //added rnel

	var list = document.getElementById('order-list');

	result = appendOrder(list, details);
}

/*
	This will trim the string i.e. no whitespaces in the
	beginning and end of a string AND only a single
	whitespace appears in between tokens/words
	input: object
	output: object (string) value is trimmed
*/
function trimString(objct){
	objct.value = objct.value.replace(/^\s+|\s+$/g,"");
	objct.value = objct.value.replace(/\s+/g," ");
}/* end of function trimString */


function checkRequestForm(){
	var items = document.getElementsByName('items[]');
	var iscash = $("iscash1").checked;
	var ptype = $('ptype').value;

	if (iscash)
		$('is_cash').value=1;
	else
		$('is_cash').value=0;


	if (items.length==0){
		alert("Please add a request first.");
		$('btnAdd').focus();
		return false;
	}else if($F('ordername') == ''){
		alert("Please indicate the patient's name's.");
		if (iscash)
			$('ordername').focus();
		else
			$('select-enc').focus();
		return false;
	}else if($F('orderdate') == ''){
		alert("Please indicate the date of request.");
		$('orderdate').focus();
		return false;
	}else if (($('priority1').checked)&&(!$('priority0').checked)&&($('comments').value=='')){
		alert("Enter a remarks why the request should be a stat case or urgent.");
		$('comments').focus();
		return false;
	}

	if ($('repeat').checked){
		if ($('remarks').value=='') {
			alert("Enter a remarks why the request should be repeated.");
			$('remarks').focus();
			return false;
		}else if ($('approved_by_head').value=='') {
			alert("Enter a name who approved .");
			$('approved_by_head').focus();
			return false;
		}else if ($('headID').value=='') {
			alert("Enter a user ID who approved .");
			$('headID').focus();
			return false;
		}else if ($('headpasswd').value=='') {
			alert("Enter a password who approved .");
			$('headpasswd').focus();
			return false;
		}
	}

	//added by VAN 06-02-2011
	if ($('for_manual').checked){
		var btntype = valButton("for_manual_type");

		//if (btntype == null){
		if ((!$('for_manual_type1').checked)&&(!$('for_manual_type2').checked)&&
				(!$('for_manual_type3').checked)&&(!$('for_manual_type4').checked)){
			alert("Select the grant type.");
			$('for_manual_type1').focus();
			return false;
		}else if ($('manual_control_no').value=='') {
			alert("Enter the control numberm, OR number or PHIC insurance number.");
			$('manual_control_no').focus();
			return false;
		}else if ($('manual_approved').value=='') {
			alert("Enter a name who approved.");
			$('manual_approved').focus();
			return false;
		}else if ($('manual_reason').value=='') {
			alert("Enter a reason why the request payment should be manually encoded.");
			$('manual_reason').focus();
			return false;
		}
	}
	//-----------------

	$('inputform').submit();
	return true;
}

function warnClear() {
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function checkIfFull(){
		//alert('check = '+$('ispayfull').checked);
	emptyTray();
	if (warnClear()) {
		if ($('ispayfull').checked){
			$('discount2').value = $('discount').value;
			$('orig_discountid').value = $('discountid').value;
			$('discountid').value = '';

			refreshDiscount();
		}else{
			$('discount').value = $('discount2').value;
			$('discountid').value = $('orig_discountid').value;
		}
	}
}

function viewClaimStub(is_cash,refno,source_req){
		//alert("viewPatientRequest refno = "+is_cash+" - "+refno);
		// alert(source_req);
		if(source_req=='OBGUSD'){
			var printWindow = window.open("seg-claimstub2.php?refno="+refno+"&is_cash="+is_cash+"&showBrowser=1","viewClaimStab","left="+((screen.width/2)-310)+", top="+((screen.height/2)-220)+", width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");

		}else{
			var printWindow = window.open("seg-claimstub.php?refno="+refno+"&is_cash="+is_cash+"&showBrowser=1","viewClaimStab","left="+((screen.width/2)-310)+", top="+((screen.height/2)-220)+", width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
		}
		
		if(source_req == 'EHR'){
			printWindow.onunload = function(){
				$J('<div></div>')
					.html('<span>Printed Successfully?</span>')
					.dialog({
						title: '<b style="color:#FF0000">Confirmation</b>',
		        		modal: true,
		            	position: 'top',
		            	buttons: {
		                	Yes: function(){
		                		xajax_updatePrintStatus(refno, 1);
		                		$J(this).dialog('close');
		                	},
		                	No: function(){
		                		xajax_updatePrintStatus(refno, 0);
		                		$J(this).dialog('close');
		                	}
		            	}
					});
			};
		}
}

//added by VAN 10-16-09
function viewHistory(pid,encounter_nr){
	window.open("seg-radio-request-history.php?pid="+pid+"&encounter_nr="+encounter_nr+"&showBrowser=1","viewRequestHistory","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}






//added by VAN 01-11-10
//only digit is allowed
function key_check(e, value){
	if((e.keyCode>=48 && e.keyCode<=57) || (e.keyCode==8) || ((e.keyCode==110)||(e.keyCode==190)) || (e.keyCode>=96 && e.keyCode<=105)){
		return true;
	}else
		return false;
}

function checkIfWalkin(){
	if (warnClear()) {
		emptyTray();
		if ($('is_walkin').checked){
			$('discount2').value = $('discount').value;
			$('orig_discountid').value = $('discountid').value;
			$('discountid').value = '';

			if ($('issc').checked){
				$('discountid').value = 'SC';
				//should be taken from the database temporary only
				$('discount').value = 0.20;
			}else
				$('discount').value = 0;
			refreshDiscount();
		}else{
			$('discount').value = $('discount2').value;
			$('discountid').value = $('orig_discountid').value;
		}
	}
}

function resetValue(){
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	nettotal = 0;
	if ($('show-discount').value!=""){
		$('show-discount').value = formatNumber(nettotal,2);
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			net[i].value = netbc[i].value;
			amount = $('rowPrcNetbc'+id).value;
			$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
		}
		refreshDiscount();
	}
}

function clearValue(){
	$('show-discount').value= "";
	$('is_free').checked = false;

	resetValue();
}

function computeDiscount(discount_amt){
	 var items = document.getElementsByName('items[]');
	 var net = document.getElementsByName('pnet[]');
	 var netbc = document.getElementsByName('pnetbc[]');
	 var val, nettotal, price_per_service, final_net, netamt, pricelist, discount_given, amount;
	 var no_item=0;

	 if (!($('is_free').checked)&&(($('show-discount').value=="")||($('show-discount').value==0.00))){
		$('show-discount').value = formatNumber(nettotal,2);
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			net[i].value = netbc[i].value;
			amount = $('rowPrcNetbc'+id).value;
			$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
		}
		refreshDiscount();
	}else{
		 discount_given = discount_amt.replace(",","");
		 discount_given = parseFloat(discount_given);
		 nettotal = $('show-net-total').innerHTML;
		 nettotal = parseFloat(nettotal);

		 if (items){
			 pricelist = new Array();
			 netamt = 0;
			 for (var i=0;i<items.length;i++) {
					pricelist[i] = parseFloat(netbc[i].value);

					if (netbc[i].value > 0){
						no_item += 1;
						netamt = netamt + parseFloat(netbc[i].value);
					}
			 }

			 netamt = parseFloat(netamt);

			 if (discount_given > netamt){
					 alert('The discount given is MORE than the Net Total (OR payable amount)');
					 for (var i=0;i<items.length;i++) {
							id = items[i].value;
							net[i].value = netbc[i].value;
							amount = $('rowPrcNetbc'+id).value;
							$('tot'+id).innerHTML = formatNumber(Math.round(amount).toFixed(2),2);
					 }
					 $('show-discount').value = '0.00';
					 $('show-discount').focus();
					 refreshDiscount();
			 }else{
					 if (discount_given > 0){
							 price_per_service =  (netamt - discount_given/parseInt(no_item));

							 if (price_per_service > pricelist.min()){
									final_net = (netamt- discount_given) / netamt;
									withdis = 1;
							 }else{
									withdis = 0;
							 }

							 for (var i=0;i<items.length;i++) {
									 id = items[i].value;

									 if (withdis==0){
											price_per_service = Math.round(parseFloat(price_per_service)*100)/100;
											price_per_service = formatNumber(price_per_service.toFixed(2),2);
											net[i].value = price_per_service.replace(",","");
											$('tot'+id).innerHTML = price_per_service
									 }else{
											discountprice = netbc[i].value * final_net;
											discountprice = Math.round(parseFloat(discountprice)*100)/100;
											discountprice = formatNumber(discountprice.toFixed(2),2);
											net[i].value = discountprice.replace(",","");
											$('tot'+id).innerHTML = discountprice;
									 }
							 }
							 refreshDiscount();
					 }
			 }
		 }

		 //refreshDiscount();
	}
}

function formatDiscount(valamount){
		document.getElementById('show-discount').value = formatNumber(valamount,2);
}

function setDiscount(){
	var nettotal=0;
	var items = document.getElementsByName('items[]');
	var net = document.getElementsByName('pnet[]');
	var netbc = document.getElementsByName('pnetbc[]');

	if ($('is_free').checked){
		for (var i=0;i<items.length;i++) {
			id = items[i].value;
			nettotal = nettotal + parseFloat(netbc[i].value);
		}

		$('show-discount').value = nettotal;
		computeDiscount($('show-discount').value);
		formatDiscount($('show-discount').value);
	}else{
		nettotal = 0;
		$('show-discount').value = formatNumber(nettotal,2);
		resetValue();
	}
}

function enableSubmitButton(isenable){
		if (isenable){
			$('btnSubmit').setAttribute("class","");
			$('btnSubmit').style.cursor='pointer';
			$('btnSubmit').setAttribute("onclick","if (confirm(\'Process this request?\')) if (checkRequestForm()) document.inputform.submit()");
		}else{
			$('btnSubmit').setAttribute("class","disabled");
			$('btnSubmit').style.cursor='default';
			$('btnSubmit').setAttribute("onclick","");
		}
}

function setPriority(isUrgent){
		if (isUrgent){
			 $('priority1').checked = true;
			 $('priority0').checked = false;
		}else{
			 $('priority1').checked = false;
			 $('priority0').checked = true;
		}
}

function checkPriority(){
	var area_type = $('area_type').value;

	//if (area_type!='pw'){
		if (warnClear()) {
			emptyTray();
		}
	//}
}

//added by VAN 06-02-2011
// for temporary workaround
function enablePhic(){
	if ($('is_rdu').checked){
		$('for_manual_type4').disabled = false;
	}else{
		$('for_manual_type4').disabled = true;
		$('for_manual_type4').checked = false;
	}
}

function setManualPayment(){

	if ($('for_manual').checked){
		$('manual').style.display = '';
		$('for_manual_payment').value = 1;
	}else{
		$('manual').style.display = 'none';
		$('for_manual_payment').value = 0;
	}

	// commented out by: syboy 12/07/2015 : meow
	/*if ($('iscash0').checked){
		$('for_manual').disabled = true;
	}else
		$('for_manual').disabled = false;*/

	enablePhic();
}

function setLabel(){
	 var or_label = "OR Number";
	 var control_label = "Control Number";
	 var phic_label = "PHIC Number";


	 if ($('for_manual_type1').checked)
			$('label_manual').innerHTML = or_label;
	 else if ($('for_manual_type2').checked)
			$('label_manual').innerHTML = control_label;
	 else if ($('for_manual_type3').checked)
			$('label_manual').innerHTML = control_label;
	 else if ($('for_manual_type4').checked)
			$('label_manual').innerHTML = phic_label;
	 else
			$('label_manual').innerHTML = control_label;
}

function valButton(btn) {
	var cnt = -1;
	var temp = document.getElementsByName(btn);
	if (!$(btn))	{
		return null;
	}

	for (var i=temp.length-1; i > -1; i--) {
		if (temp[i].checked) {
			cnt = i;
			i = -1;
		}
		}

	if (cnt > -1) return temp[cnt].value;
		else return null;
}

//added by VAN 07-04-2011
function showCTScanForm(pid, encounter_nr, refno){
	//alert('showCTScanForm = '+pid+" , "+encounter_nr);

	var radId = document.getElementsByName('radId[]');
	var radServ = new Array();
	for (var i=0;i<radId.length;i++){
		radServ[i] = radId[i].value;
	}


	return overlib(
				OLiframeContent('seg-radio-service-clinical-history-ctscan.php?pid='+pid+'&encounter_nr='+encounter_nr+'&refno='+refno+'&radServ='+radServ,
																	650, 350, 'ctscanform', 1, 'auto'),
																	WIDTH,350, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=../../images/close.gif border=0 onClick="">',
																 CAPTIONPADDING,4, CAPTION,'CT Clinical History',
																 MIDX,0, MIDY,0,
																 STATUS,'CT Scan Request Form');
}

function showMRIForm(pid, encounter_nr, refno){
	 //alert('showMRIForm = '+pid+" , "+encounter_nr);

	 var radId = document.getElementsByName('radId[]');
	 var radServ = new Array();
	 for (var i=0;i<radId.length;i++){
		radServ[i] = radId[i].value;
	 }

	 return overlib(
				OLiframeContent('seg-radio-service-clinical-history-mri.php?pid='+pid+'&encounter_nr='+encounter_nr+'&refno='+refno+'&radServ='+radServ,
																	650, 350, 'mriform', 1, 'auto'),
																	WIDTH,350, TEXTPADDING,0, BORDER,0,
																		STICKY, SCROLL, CLOSECLICK, MODAL,
																		CLOSETEXT, '<img src=../../images/close.gif border=0 onClick="">',
																 CAPTIONPADDING,4, CAPTION,'MRI Clinical History',
																 MIDX,0, MIDY,0,
																 STATUS,'MRI Request Form');
}

//--------------------------
//added by VAS 03-27-2012
function changeChargeType() {    
    if (!$("iscash1").checked) {
        updateCoverage([$('encounter_nr').value]);
    }    
    refreshDiscount();
}

function updateCoverage( param ) {
    if (!param[0]) {
        //$('cov_type').update('Coverage:');
        $('cov_amount').update('');
        $('coverage').setAttribute('value',-1);
        return false;
    }
    
    var ctype = $('grant_type').value;
    var nr = $('refno').value;
    param.push(ctype);
    param.push(nr);
    
    if (ctype=='phic') {  //phic
        $('cov_type').hide();
        $('cov_amount').hide();
        $('phic_ajax').show();
        $('cov_type').update('PHIC Coverage:');
        xajax.call('updateCoverage', {
            parameters : param,
            onError: function(transport) {
                $('phic_ajax').hide();
                $('cov_type').show();
                $('cov_amount').show();
            },
            onSuccess : function(transport) {
                $('phic_ajax').hide();
                $('cov_type').show();
                $('cov_amount').show();
            }
        });
    }
    else {  //other charge type 
        $('cov_type').update('');
        $('cov_amount').update('');
        $('coverage').setAttribute('value',-1);
        $('phic_ajax').hide();
        //$('cov_type').hide();
        //$('cov_amount').hide();
    }
}

function updatePHICCoverage( param ) {
    $('phic_cov').hide();
    $('phic_ajax').show();
    xajax.call('updatePHICCoverage', {
        parameters : param,
        onError: function(transport) {
            $('phic_ajax').hide();
            $('phic_cov').show();
        },
        onSuccess : function(transport) {
            $('phic_ajax').hide();
            $('phic_cov').show();
        }
    });
}

function openCoverages() {
        var enc_nr = $('encounter_nr').value;
        var userck = '<?= $userck; ?>';
        if (enc_nr) {
            var url = '../../modules/insurance_co/seg_coverage_editor.php?userck='+userck+'&encounter_nr='+enc_nr+'&from=CLOSE_WINDOW&force=1';
            overlib(
                OLiframeContent(url, 740, 400, 'fCoverages', 0, 'auto'),
                WIDTH,600, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=../../images/close_red.gif border=0 >',
                CAPTIONPADDING,2,
                CAPTION,'Insurance coverages',
                MIDX,0, MIDY,0,
                STATUS,'Insurance coverages');
        }
        else {
            alert('No patient with confinement case selected...');
        }
        return false
    }

function removeTplChargeType(noCoverage){
	if($('mode').value=='save'){
		var phic_nr = $J("#phic_nr").text();
		var grant_type = $J("#grant_type");
		var hasSaveGrantType = $J("#hasSaveGrantType").val();
		var accomodation = $('accomodation').value;
		var hastpl = 0;
		var hasphic = 0;

		var enc = $('encounter_nr').value;
		var admission_accomodation = $('admission_accomodation').value;
		xajax_updatePHIC(enc);

		$J("#grant_type > option").each(function(){
	    	var thisval = this.value;
	    	if (thisval == 'phic') hasphic = 1;
	    	if (thisval == 'personal' || thisval == '') hastpl = 1;
	    });
	    if (accomodation == privateAccomodation || admission_accomodation == privateAccomodation || $('area_type').value=='pw') {
			if (phic_nr == "None" && noCoverage == 1) {
				if (hastpl==1) {
					$J("#grant_type option[value='']").remove();
				}
				if (hasphic==0) {
					$J("#grant_type").append('<option value="phic">PHIC</option>');
				}

			}else if(phic_nr != "None" && noCoverage == 1){
				if (hastpl==0) {
					$J("#grant_type").append('<option value="">PERSONAL</option>');
				}
				if (hasphic==0) {
					$J("#grant_type").append('<option value="phic">PHIC</option>');
				}
			}else if(phic_nr == "None" && noCoverage == 0){
				if (hastpl==0) {
					$J("#grant_type").append('<option value="">PERSONAL</option>');
				}
				grant_type.val('');
			}
			else{
				if (hasphic==0) {
					$J("#grant_type").append('<option value="phic">PHIC</option>');
				}
				if (hastpl==1) {
					$J("#grant_type option[value='']").remove();
				}
				if(hasSaveGrantType != 1){
					grant_type.val('phic');
				}
			}
	    }else {
			if (hastpl==0) {
				$J("#grant_type").append('<option value="">PERSONAL</option>');
			}
			if(hasSaveGrantType != 1){
				grant_type.val('');
			}
	    }
	    changeTransactionType();
	}
}
function viewOBHistory(pid,encounter_nr){
	window.open("seg-obgyne-request-history.php?pid="+pid+"&encounter_nr="+encounter_nr+"&showBrowser=1","viewRequestHistory","width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

//-------------