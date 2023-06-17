var jQueryDialogSearch;//added by Nick 11-21-2015


var ViewMode = false;

var totalDiscount = 0;
var HSM = "HOSPITAL SPONSORED MEMBER";
var NBB = "SPONSORED MEMBER";
var privateAccomodation = 2;
var coverage_limit_exceeded=0;
var areaHI = 'H';
var areaOR = 'O1';
var areaORIWNH = 'O2';
var areaHeart = 'MHC';
var areaORP = 'OR';
var areaIP4 = 'IP4';

var PHIC = "PHIC";
var routeList;
var frequencyList;
var dosageList;
function isCash() {
	return $('iscash1').checked;
}

function parseFloatEx(x) {
	console.log("parseFloatEx");
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function warnClear() {
	console.log("warnClear");
	var items = document.getElementsByName('items[]');
	if (items.length == 0) return true;
	else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function formatNumber(num,dec) {
	console.log("formatNumber");
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function resetRefNo(newRefNo,error) {
	console.log("resetRefNo");
	$("refno").style.color = error ? "#ff0000" : "";
	$("refno").value=newRefNo;
}

function clearEncounter() {
	console.log("clearEncounter");
	var iscash = $("iscash1").checked;
	$('ordername').value="";
	$('ordername').readOnly=!iscash;
	$('orderaddress').value="";
	$('orderaddress').readOnly=!iscash;
	$('is_tpl').disabled = !iscash;
	$('pid').value="";
	$('encounter_nr').value="";
	$('clear-enc').disabled = true;
	$('clear-enc').disabled = true;
	$('sw-class').innerHTML = 'None';
	$('encounter_type_show').innerHTML = 'WALK-IN';
	$('encounter_type').value = '';
	$('ordername').focus();

    $('warningcaption').innerHTML = '';
	//updatePHICCoverage(['']);
	if (!iscash) {
		updateCoverage(['']);
	}
	//clearCharityDiscounts();
}

function updateCoverage( param ) {
	console.log("updateCoverage");
	if (!param[0]) {
		$('cov_type').update('');
		$('cov_amount').update('');
		$('coverage').setAttribute('value',-1);
		return false;
	}

	var ctype = $('charge_type').value;
	param.push(ctype);

	if (ctype=='PERSONAL') {
		$('cov_type').update('');
		$('cov_amount').update('');
		$('coverage').setAttribute('value',-1);
	}
	else {
		$('cov_type').hide();
		$('cov_amount').hide();
		$('phic_ajax').show();

		$('cov_type').update(ctype + ' Coverage:');
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
}

function updatePHICCoverage( param ) {
	console.log("updatePHICCoverage");
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

function pSearchClose() {
	console.log("pSearchClose");
	var nr = $('encounter_nr').value;
	updatePHICCoverage([nr]);
	updateCoverage([nr]);
	cClick();
}

function autoSuggestWalkin(element) {
	console.log("autoSuggestWalkin");
	if ($("iscash1").checked && !element.readOnly) {
		$('orderaddress').readOnly = false;
		if (!$F('orderaddress')) $('orderaddress').value = "NOT PROVIDED";
		var script = "ajax/walkin.php";
		var options = {
			delay: 5,
			timeout: 2000,
			script: function (input) { return ( script + '?s='+input); },
			callback: function (obj) {
				//$('xml_info').update('you have selected: '+obj.id + ' ' + obj.value + ' (' + obj.info + ')');
				$('ordername').readOnly = true;
				$('orderaddress').readOnly = true;
				$('ordername').value = obj.value;
				$('orderaddress').value = obj.info;
				$('pid').value = 'W'+obj.id;
				$('clear-enc').disabled = false;
			}
		};
		var xml=new AutoComplete(element.id,options);
		return true;
	}
	else {
		$('orderaddress').readOnly = true;
		return false;
	}
}

function emptyTray() {
	console.log("emptyTray");
	clearOrder($('order-list'));
	appendOrder($('order-list'),null);
	refreshTotal();
}

function reclassRows(list,startIndex) {
	console.log("reclassRows");
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

/*
function clearCharityDiscounts() {
	var cNodes = document.getElementsByName("charity[]");
	if (cNodes) {
		for (var i=cNodes.length-1;i>=0;i--) {
			cNodes[i].parentNode.removeChild(cNodes[i]);
		}
	}
}

function addCharityDiscount(discountid, discount) {
	var dsc = document.createElement("INPUT");
	dsc.setAttribute("type","text");
	dsc.setAttribute("id","ch"+discountid);
	dsc.setAttribute("name","charity[]");
	dsc.setAttribute("discount",discount);
	dsc.setAttribute("value",discountid);
	$("orderForm").appendChild(dsc);
}
*/

function clearOrder(list) {
	console.log("clearOrder");
	if (!list) list = $('order-list')
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


function appendOrder(list, details, disabled, mode = 'new') {
    var accomodation = $("accomodation").value;
    var admission_accomodation = $('admission_accomodation').value;
    var phic_nr = $('phic_nr').innerHTML;
    var charge_type = $('charge_type').value;
    var charge_type_disp = document.getElementById('charge_type').style.display;
	var is_paid = 'paid';

	if (!list) list = $('order-list');
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var discount = parseFloatEx($("discount").value);
			var isCash = $("iscash1").checked;
			var isCharge = $("iscash0").checked;
			var isSC = $("issc").checked;  // Senior Citizen checking
			var totalCash, totalCharge;
			var src,priceOrig;
			var medicine_item = "M";
			var chargeType = 'charge';
			var cashType = 'cash';
			var transactionType = isCash ? cashType : chargeType;
			var lastRowNum = null,
					items = document.getElementsByName('items[]')
					misc_item = document.getElementsByName('misc_item[]');
					dRows = dBody.getElementsByTagName("tr");
			if (details) {

				var id = details.id+'_'+details.area,
					code = details.id,
					area = details.area,
					qty = parseFloatEx(details.qty),
					prcCash = parseFloatEx(details.prcCash),
					prcCharge = parseFloatEx(details.prcCharge),
					prcCashSC = parseFloatEx(details.prcCashSC),
					prcChargeSC = parseFloatEx(details.prcChargeSC),
					/*added by MARK January 17, 2017*/
					NewprcCash = parseFloatEx(details.NewCash),
					NewprcCharge = parseFloatEx(details.NewCharge),

					NewprcCashType = parseFloatEx(details.price_cash_CASH),
					NewprcChargeType = parseFloatEx(details.price_charge_CHARGE),
					/*END added by MARK January 17, 2017*/
					totalCash, totalCharge,
					source = details.source,
					account_type = details.account_type,is_fs = details.is_fs;

                

				netPrice = isCash ? prcCash : prcCharge;
				orig = netPrice;
				priceOrig = netPrice;
				if (isSC)	{
					seniorPrice = parseFloatEx(isCash ? details.prcCashSC : prcChargeSC);
					if (seniorPrice > 0)
						netPrice = seniorPrice
				}
				var NewcashORcharge;
				if (isCharge) {
						NewcashORcharge = details.price_charge_CHARGE;
				}else if(isCash){
						NewcashORcharge = details.price_cash_CASH ;
				}
				// Check if item is socialized and discount is of effect
				if (parseInt(details.isSocialized)==1 && isCash) {
					//if (discount==1.0)
					//	netPrice=0;
					//else {
						if (parseFloatEx(details.prcDiscounted) < netPrice) {
							netPrice = parseFloatEx(details.prcDiscounted)
							// if (netPrice == 0) netPrice = orig;
						}
					//}
				}
				if (details.forcePrice) netPrice = details.forcePrice;
				tot = netPrice*qty;

				var coverageLimit = parseFloatEx($('coverage').value);
				var netTotal = parseFloatEx($('show-sub-total').innerHTML.replace(',',''));
				// Check coverage limit
				// alert($('modeval').value);
				
				if($('modeval').value!='edit'){
					if (coverageLimit >= 0) {
						if ((coverageLimit < tot || coverageLimit < (netTotal + tot))  && charge_type=='PHIC' && charge_type_disp != 'none') {
							// if(coverage_limit_exceeded==0) {
							alert("Coverage limit exceeded for this item...");
							// 	coverage_limit_exceeded++;
							// }
							// if(!((accomodation == privateAccomodation || admission_accomodation == privateAccomodation) && ((details.area == areaHI || details.area == areaOR) && phic_nr!='None'))){
							return true;
							// }
						}
					}
				}

				orig = isNaN(orig) ? '<span style="margin-right:5px">-</span>' : formatNumber(orig,2);
				if (items || misc_item) {
					if ($('rowID'+id)) {
						var itemRow = $('row'+id), itemQty = $('rowQty'+id);
						if(isNaN(details.stock)) {
						itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty);
						}else{
							var stock = parseFloatEx(details.stock)
							if((parseFloatEx(itemQty.value) + parseFloatEx(details.qty))>stock) itemQty.value = stock;
							else itemQty.value = parseFloatEx(itemQty.value) + parseFloatEx(details.qty);
						}
						itemQty.setAttribute('prevValue',itemQty.value);
						qty = parseFloatEx(itemQty.value);
						tot = netPrice*qty;
						$('rowPrcCashSC'+id).value = prcCashSC;
						$('rowPrcChargeSC'+id).value = prcChargeSC;
						$('rowPrcCash'+id).value = details.prcCash;
						$('rowPrcCharge'+id).value = details.prcCharge;
                        $('rowFS'+id).value = details.is_fs;
						$('rowPrc'+id).setAttribute("prevValue",orig);
						//$('qty'+id).innerHTML 				= isNaN(qty) ? '<span style="margin-right:5px">-</span>' : 'x'+formatNumber(qty,null)
						$('rowPrc'+id).value = isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2);
						$('tot'+id).innerHTML = isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2);
						refreshTotal();
						return true;
					}
					if (items.length == 0 && misc_item.length==0) clearOrder(list);
				}

				alt = (dRows.length%2) ? 'class="alt"' : '';
				qty = isNaN(qty) ? '<span style="margin-right:5px">-</span>;' : 'x'+formatNumber(qty,null);
				prc = isNaN(netPrice) ? '<span style="margin-right:5px">-</span>' : formatNumber(netPrice,2);
				tot = isNaN(tot) ? '<span style="margin-right:5px">-</span>' : formatNumber(tot,2);

				var disabledAttrib = disabled ? 'disabled="disabled"' : "";

				var itemNameFlag = ((source=='M') ? '1' : '0');

				src =
					'<tr '+alt+' id="row'+id+'" style="height:26px">' +
						'<input type="hidden" name="soc[]" id="rowSoc'+id+'" value="'+details.isSocialized+'" />'+
						'<input type="hidden" name="pdisc[]" id="rowPrcDiscounted'+id+'" value="'+details.prcDiscounted+'" />'+
						'<input type="hidden" name="pcashsc[]" id="rowPrcCashSC'+id+'" value="'+prcCashSC+'" />'+
						'<input type="hidden" name="pchargesc[]" id="rowPrcChargeSC'+id+'" value="'+prcChargeSC+'" />'+
						'<input type="hidden" name="pcash[]" id="rowPrcCash'+id+'" value="'+details.price_cash_CASH+'" />'+
						'<input type="hidden" name="pcharge[]" id="rowPrcCharge'+id+'" value="'+details.NewCharge+'" />'+
						'<input type="hidden" name="misc_account_type[]" id="misc_account_type'+id+'" value="'+account_type+'" />'+
						'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+code+'" />'+
						'<input type="hidden" name="itemArea[]" id="area'+id+'" value="'+area+'" />'+
                        '<input type="hidden" name="is_fs[]" id="rowFS'+id+'" value="'+details.is_fs+'" />'+
						//'<input type="hidden" name="misc_item[]" id="rowID'+id+'" value="'+id+'" />'+
						'<input type="hidden" name="isNewCash" id="isNewCash'+id+'" value="'+transactionType+'" /> '+
						'<input type="hidden" name="flag[]" id="flag'+id+'" value="'+itemNameFlag+'" /> '+
						'<input type="hidden" class="areaCode" name="areaCode[]" id="areaCode'+id+'" value="'+details.area+'" /> '+
						'<input type="hidden" name="is_override[]" class="is_override'+details.isInventory+'" value="'+details.is_override+'" />';

				if (disabled){
					/*START added BY MARK 2016-11-03*/
					if (details.inv_uid !=""  && details.is_down_inv == 1 && details.is_in_inventory == 1)
						src+='<td><img  title="Overriden data in Inventory" src="../../images/claim_notok.gif" align="absmiddle"/></td>';
					else if (details.inv_uid !="" && details.inv_uid !="FAILED" &&  details.is_down_inv  == 0 &&  details.is_in_inventory == 1)
						src+='<td><img onclick="viewTransDai(\''+details.inv_api_key+'\',\''+details.pid+'\')" class="ViewDAItransact" style="cursor:pointer;" title="Transacted in DAI" src="../../images/claim_ok.gif" align="absmiddle"/></td>';
					else if(details.inv_uid =="FAILED" && details.served_new =='S' && details.is_down_inv == 0 && details.is_in_inventory == 1)
						src+='<td><img  title="FAILED" src="../../images/claim_notok.gif" align="absmiddle"/></td>';
	
					else
						src+='<td></td>';
					/*END added BY MARK 2016-11-03*/
				}
				else
					src+='<td class="centerAlign"><img class="segSimulatedLink" src="../../images/cashier_delete_small.gif" border="0" onclick="removeItem(\''+id+'\')"/></td>'


				src+=
					'<td class="centerAlign">'+code+'</td>'+
					'<td><span style="color:#660000">'+details.name+'</span></td>'+
					'<td>'+details.area_name+'</span></td>'+
					'<td class="centerAlign"><input type="checkbox" name="consigned[]" value="'+id+'" '+(parseInt(details.isConsigned)==1 ? 'checked="checked"' : '')+' '+(disabled ? 'disabled="disabled"' : '')+'></td>'+
					'<td class="centerAlign" nowrap="nowrap" id="qty'+id+'">'+
						//'<input type="text" class="segInput" name="qty[]" id="rowQty'+id+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:right"'+(disabled ? ' disabled="disabled"' : '')+' onfocus="this.value=this.getAttribute(\'prevValue\')" onchange="adjustQty(this)"/>'+
						'<input type="text" class="segInput" name="qty[]" id="rowQty'+id+'" max="'+details.stock+'" itemID="'+id+'" value="'+details.qty+'" prevValue="'+details.qty+'" style="width:80%;text-align:center"'+((disabled || details.request_flag == is_paid) ? ' disabled="disabled"' : '')+' onchange="editQuantity(\''+id+'\')"/>'+
					'</td>'+
					'<td class="centerAlign"><input '+(details.prod_class == medicine_item ? "" : "readonly")+' oninput="chkInputLimit(this,500);" maxlength="500" class="dosageInput" name="dosage[]" list="rowdosage'+id+'" style="width:145px;" '+(disabled ? 'disabled="disabled"' : '')+' value="'+(details.prod_class == medicine_item ? details.dosage : "N/A")+'">'+
					'<datalist id="rowdosage'+id+'">';
					if(details.dosageOptions)
						details.dosageOptions.each(function(v,k){
							src +='<option value="'+v+'">'+v+'</option>';
					});
					src +='</datalist></td>'+
					'<td class="centerAlign"><input '+(details.prod_class == medicine_item ? "" : "readonly")+' oninput="chkInputLimit(this,50);" maxlength="50"class="frequencyInput" name="frequency[]" list="rowfrequency'+id+'" style="width:145px;" '+(disabled ? 'disabled="disabled"' : '')+' value="'+(details.prod_class == medicine_item ? details.frequency : "N/A")+'" maxlength="50">'+
					'<datalist id="rowfrequency'+id+'">';
					if(details.frequencyOptions)
						details.frequencyOptions.each(function(v,k){
							src +='<option value="'+v+'">'+v+'</option>';
					});
					src +='</datalist></td>'+
					'<td class="centerAlign"><input '+(details.prod_class == medicine_item ? "" : "readonly")+' oninput="chkInputLimit(this,500);" maxlength="500" class="routeInput" name="route[]" list="rowroute'+id+'" style="width:145px;" '+(disabled ? 'disabled="disabled"' : '')+' value="'+(details.prod_class == medicine_item ? details.route : "N/A")+'" maxlength="500">'+
					'<datalist id="rowroute'+id+'">';
					if(details.routeOptions)
						details.routeOptions.each(function(v,k){
							src +='<option value="'+v+'">'+v+'</option>';
					});
					src +='</datalist></td>'

				//if item is newly added do not display dispensed quantity
			
				if(disabled)
					src += '<td class="centerAlign" nowrap="nowrap">'+details.dispensed_qty+'</td>';
				else if(mode !='new')
				src += '<td class="centerAlign" nowrap="nowrap">'+details.dispensed_qty+'</td>';
				else if(mode =='edit')
				src += '<td class="centerAlign" nowrap="nowrap">'+details.dispensed_qty+'</td>';
					
				src+= '<td class="rightAlign" id="prc'+id+'">'+formatNumber(NewcashORcharge,2)+'</td>'+
					'<td class="rightAlign">'

				if	(disabled || (parseFloatEx(details.prcDiscounted)>0 && (!isSC || (isSC && parseFloatEx(seniorPrice)>0))))
					src+= '<input type="text" class="segClearInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" readonly="readonly"/>'
				else
					src+= '<input type="text" class="segInput" name="prc[]" id="rowPrc'+id+'" value="'+prc+'" style="width:95%;text-align:right" itemID="'+id+'" prevValue="'+netPrice+'" onfocus="this.value=this.getAttribute(\'prevValue\')" onblur="adjustPrice(this)"/>'

				src+=	'</td>'+
					'<td class="rightAlign" id="tot'+id+'">'+tot+'</td>'+
				'</tr>';
				trayItems++;

			}
			else {
				src = "<tr style=\"height:26px\"><td colspan=\"12\">Order list is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;

			refreshTotal();

			if(details)
				xajax_initDosageRouteFreq(id,details.prod_class,details.dosage,details.route,details.frequency);
			return true;
		}
	}
	return false;
}

function chkInputLimit(elem,maxlength){
	if (elem.value.length == maxlength) {
		alert('You have reached the maximum number of Characters!');
	}
}

function removeItem(id) {
	var destTable, destRows;
	var table = $('order-list');
	var rmvRow=document.getElementById("row"+id);
	if (table && rmvRow) {
		var rndx = rmvRow.rowIndex-1;
		table.deleteRow(rmvRow.rowIndex);
		if ((!document.getElementsByName("items[]") || document.getElementsByName("items[]").length <= 0) && (!document.getElementsByName("misc_item[]") || document.getElementsByName("misc_item[]").length <= 0))
			appendOrder(table, null);
		reclassRows(table,rndx);
	}
	refreshTotal();
}

function seniorCitizen() {
	console.log("seniorCitizen");
	var iscash = $("iscash1").checked
	var issc = $("issc").checked
	var discount = parseFloatEx($("discount").value)
	var pdisc = document.getElementsByName('pdisc[]')
	var soc = document.getElementsByName('soc[]')
	var items = document.getElementsByName('items[]')
	var cash = document.getElementsByName('pcash[]')
	var charge = document.getElementsByName('pcharge[]')
	var cashsc = document.getElementsByName('pcashsc[]')
	var chargesc = document.getElementsByName('pchargesc[]')
	var prc = document.getElementsByName('prc[]')
	var isCash = $("iscash1").checked
	var newPrice, discountPrice, seniorPrice, cashPrice, chargePrice,
			cashSc, chargeSc


	for (var i=0;i<items.length;i++) {
		priceCash = parseFloatEx(cash[i].value)
		priceCharge = parseFloatEx(charge[i].value)
		newPrice = iscash ?  priceCash : priceCharge
		discountPrice = newPrice
		if (parseInt(soc[i].value)==1 && iscash) {
			//if (discount==1.0)	newPrice = 0
			//else {
				discountPrice = parseFloatEx(pdisc[i].value)
				if (discountPrice > 0) newPrice = discountPrice
			//}
		}

		seniorPrice = 1.0
		if (issc) {
			cashSc = parseFloatEx(cashsc[i].value)
			chargeSc = parseFloatEx(chargesc[i].value)
			seniorPrice = Math.min(newPrice, iscash ? cashSc : chargeSc)
			if (seniorPrice > 0) newPrice = seniorPrice
		}

		// disabled flag
		disabledFlag = false
		//alert('issc:'+issc+'\ndsc:'+discountPrice+'\nsprc:'+seniorPrice)
		if (disabledFlag || (discountPrice >0 && (!issc || (issc && seniorPrice>0)))) {
			prc[i].className = "segClearInput"
			prc[i].value = formatNumber(newPrice,2)
			prc[i].readOnly = true
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "")
			prc[i].setAttribute("onblur", "")
		}
		else {
			prc[i].className = "segInput"
			prc[i].readOnly = false
			prc[i].value = formatNumber(newPrice,2)
			prc[i].setAttribute("prevValue", newPrice)
			prc[i].setAttribute("onfocus", "this.value=this.getAttribute(\'prevValue\')")
			prc[i].setAttribute("onblur", "adjustPrice(this)")
		}
	}
}

function changeChargeType() {
	console.log("changeChargeType");
	var enc = $('encounter_nr').value;
	var charge_type = $('charge_type').value;
	if(!$("iscash1").checked) xajax_updateCoverage(enc,charge_type,1); 
	// updateCoverage([$('encounter_nr').value]);
	// refreshTotal();
}

function changeTransactionType(search=0) {
	console.log("changeTransactionType-"+search);
	var isCash = $("iscash1").checked;
	var phic = $('phic_nr').innerHTML;
	var ptype = $('encounter_type_show').innerHTML;
	var encounter_nr = $('encounter_nr').value;
	//clearEncounter();
	
	var mgh = $('is_maygohome').value;
    var bill_nr = $('bill_nr').value;
    var warning = $('warningcaption').innerHTML;
 	var accomodation = $("accomodation").value;

    if ((mgh==1) && (bill_nr!='') &&(!isCash) && (search == 0)){
        //mgh or have save billing
        alert('Charging is NOT allowed to this patient. '+warning);
        $("iscash1").checked = true;
        isCash = true;
        return false;
    }

	if (!isCash) {
		if(ptype == "WALK-IN"){
			isCash = false;
		}else{
			isCash = true;
		}
		if (!($('ordername').value) && (search == 0)) {
			alert("Charging is only allowed for current hospital patients...");
			$("iscash1").checked = true;
			isCash = true;
			return false;
		}

		if(isCash == false){
			xajax_newChargeType();
		}

		$("issc").checked = false;
		$('issc').disabled = true;
		seniorCitizen();
	}else{
		isCash = true;
		$('issc').disabled = false;
	}

    if ((mgh==1) && (bill_nr!='') &&(!isCash)){
        alert('Charging is NOT allowed to this patient. '+warning);
        $("iscash1").checked = true;
        isCash = true;
        return false;
    }
	$('charge_type').style.display = $("iscash1").checked ? 'none' : '';

	var enc = $('encounter_nr').value;
	var charge_type = $('charge_type').value;
	if(!$("iscash1").checked) xajax_updateCoverage(enc,charge_type,search); 
	else {
		$('cov_type').hide();
		$('cov_amount').hide();
		$('phic_ajax').hide();
	}
}

function adjustPrice(obj) {
	console.log("adjustPrice");
	var id = obj.getAttribute("itemID");
	if (isNaN(obj.value)) {
		obj.value = formatNumber(obj.getAttribute("prevValue"),2);
		return false;
	}
	if (parseFloatEx(obj.value) != parseFloatEx(obj.getAttribute("prevValue"))) {
		$('tot'+id).innerHTML = formatNumber(obj.value*parseFloatEx($('rowQty'+id).value),2);
		refreshTotal();
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	obj.value = formatNumber(obj.value,2);
	return true;
}

//Added by Jarel 04/11/2013
function editQuantity(id) {
	console.log("editQuantity");
	var qtyInput = $('rowQty'+id);
	var newqty = qtyInput.value;
	var prevValue = parseFloat(qtyInput.getAttribute("prevValue"));
	var max = parseFloat(qtyInput.getAttribute("max"));
	var backToPrev = true;
	// ===
	var coverage=parseFloatEx($('coverage').value);
	var items = document.getElementsByName('items[]');
	var area = document.getElementsByName('itemArea[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var prc = document.getElementsByName('prc[]');
	var isCash = $("iscash1").checked;
	var total = 0.0, orig = 0.0;
	var id2

	for (var i=0;i<items.length;i++) {
		id2 = items[i].value + "_" + area[i].value;
		orig+=parseFloatEx(isCash ? cash[i].value : charge[i].value)*parseFloatEx(qty[i].value);
		val = parseFloatEx(prc[i].value)*parseFloatEx(qty[i].value)
		total+=val;
	}
	// ===

   if (!isNaN(newqty) && newqty!=null){
   	   var charge_type = "";
   	   if (!isCash) 
   	   		charge_type = $('charge_type').value;	
   
	   if(coverage - total < 0 && charge_type == PHIC){
	   	   alert('Coverage limit exceeded for this item...');
		   backToPrev = true;

	   }else {
		newqty = parseFloat(newqty);
		   if(newqty > 0 && newqty%1 === 0) {
			    if (!isNaN(max)) {
				   if (newqty <= max) {
	    			backToPrev = false;
				}
   }else{
	    		backToPrev = false;
   }
		}
   }

   }

	if(backToPrev){
		qtyInput.innerHTML = prevValue;
		qtyInput.value = prevValue;
   }else{
		qtyInput.setAttribute("value",newqty);
		qtyInput.setAttribute("prevValue",newqty);
		$('tot'+id).innerHTML = formatNumber(parseFloatEx($('rowPrc'+id).value)*parseFloatEx($('rowQty'+id).value),2);
   }

   refreshTotal();
}

function adjustQty(obj) {
	console.log("adjustQty");
	var id = obj.getAttribute("itemID");
	var charge_type = $J("#charge_type").val();
	var coverageLimit = parseFloatEx($J('#coverage').val());
	var netTotal = parseFloatEx($J('#show-sub-total').text().replace(',',''));
	var tot = parseFloatEx($J("#rowPrc"+id).val().replace(',',''))  * parseFloatEx(obj.value);
	var prevValue =  parseFloatEx(obj.getAttribute("prevValue"));
    var accomodation = $("accomodation").value;
    var admission_accomodation = $('admission_accomodation').value;
    var phic_nr = $('phic_nr').innerHTML;
    var area = $('area').value;

	if (isNaN(obj.value)) {
		obj.value = prevValue;
		return false;
	}
	if (charge_type == 'PHIC') {
		if (coverageLimit < netTotal) {
			// if(coverage_limit_exceeded==0) {
				alert("Coverage limit exceeded for this item...");
			// 	coverage_limit_exceeded++;
			// }
			$J("#rowQty"+id).val(prevValue);
			refreshTotal();
			// if(!((accomodation == privateAccomodation || admission_accomodation == privateAccomodation) && ((area == areaHI || area == areaOR) && phic_nr!='None'))){
				return false;
			// }
		}else{
			if (parseFloatEx(obj.value) != prevValue) {
				$('tot'+id).innerHTML = formatNumber(parseFloatEx($('rowPrc'+id).value)*parseFloatEx($('rowQty'+id).value),2);
				refreshTotal();
			}
			obj.setAttribute("prevValue",parseFloatEx(obj.value));
			return true;
		}
	}else{
		if (parseFloatEx(obj.value) != prevValue) {
		$('tot'+id).innerHTML = formatNumber(parseFloatEx($('rowPrc'+id).value)*parseFloatEx($('rowQty'+id).value),2);
		refreshTotal();
		}
	}
	obj.setAttribute("prevValue",parseFloatEx(obj.value));
	return true;
}

// function refreshDiscount() {
// 	console.log("refreshDiscount");
// 	var nodes;
// 	var nr = $('encounter_nr').value;
// 	if (nr)
// 		nodes = document.getElementsByName("charity[]");
// 	else
// 		nodes=document.getElementsByName("discount[]");
// 	totalDiscount = 0;
// 	if (nodes) {
// 		for (var i=0;i<nodes.length;i++) {
// 			if (nodes[i].value) totalDiscount += parseFloatEx(nodes[i].getAttribute('discount'));
// 		}
// 	}
// 	var dItem = $("show-discount");
// 	if (dItem) {
// 		dItem.value = parseFloatEx(totalDiscount * 100).toFixed(2);
// 	}
// }

function checkReqSource(){
	console.log("checkReqSource");
	// For Industrial Clinic
	if ($('source_req').value=='IC'){
		 if ($('is_charge2comp').value == 0)
			$('iscash0').disabled = true; // for disabling Charge radio
	}
}

function refreshTotal() {
	// refreshDiscount
	var nodes;
	var nr = $('encounter_nr').value;
	if (nr)
		nodes = document.getElementsByName("charity[]");
	else
		nodes=document.getElementsByName("discount[]");
	totalDiscount = 0;
	if (nodes) {
		for (var i=0;i<nodes.length;i++) {
			if (nodes[i].value) totalDiscount += parseFloatEx(nodes[i].getAttribute('discount'));
		}
	}
	var dItem = $("show-discount");
	if (dItem) {
		dItem.value = parseFloatEx(totalDiscount * 100).toFixed(2);
	}
	//refreshDiscount
	console.log("refreshTotal");
	var items = document.getElementsByName('items[]');
	var area = document.getElementsByName('itemArea[]');
	var cash = document.getElementsByName('pcash[]');
	var charge = document.getElementsByName('pcharge[]');
	var qty = document.getElementsByName('qty[]');
	var prc = document.getElementsByName('prc[]');
	var isCash = $("iscash1").checked;
	var total = 0.0, orig = 0.0;
	var id
	for (var i=0;i<items.length;i++) {
		id = items[i].value + "_" + area[i].value;
		orig+=parseFloatEx(isCash ? cash[i].value : charge[i].value)*parseFloatEx(qty[i].value);
		val = parseFloatEx(prc[i].value)*parseFloatEx(qty[i].value)
		total+=val;
		$('tot'+id).innerHTML = formatNumber(val,2)
	}

	var subTotal = $("show-sub-total");
	var discountTotal = $("show-discount-total");
	var netTotal = $("show-net-total");

	subTotal.innerHTML = formatNumber(orig.toFixed(2),2);
	disc = total-orig;
	// alert(disc);
	if (disc <= 0) {
		discountTotal.style.color = "#006600";
		discountTotal.innerHTML = "("+formatNumber(Math.abs(disc),2)+")";
	}
	else {
		discountTotal.style.color = "red";
		discountTotal.innerHTML = formatNumber(Math.abs(disc),2);
	}
	netTotal.innerHTML = formatNumber(total.toFixed(2),2);
	if($('modeval').value=='edit') total=0;
	if ($('coverage').value!=-1 && !$("iscash1").checked) {
		var coverage=parseFloatEx($('coverage').value);
		if($('mem_category').innerHTML == HSM){
			$('cov_amount').update('HSM');
		}else if ($('mem_category').innerHTML == NBB){
			$('cov_amount').update('NBB');
		} else{
			$('cov_amount').update(formatNumber(coverage-total,2));
		}
	}
}


/**
 *
 * added by rnel
   generic function that uses both pharmacy module and examination frame
   load default pay type base on patient info 
 *
 */


function loadTypeCharge(type_nr, phic, enc_nr) {
	console.log("loadTypeCharge");
	var hasPHIC = $('hasPHIC').value;
	var exists = false;
	var hasfinal_bill = $('hasfinal_bill').value;
	var accomodation = $("accomodation").value;

	if(type_nr == '3' || type_nr == '4' ) {
		if(hasfinal_bill){
			$('iscash1').checked = true;
			$('charge_type').style.display = 'none';
		}else{
			$('iscash0').checked = true;
			$('charge_type').style.display = '';
			
		}
		if(accomodation == privateAccomodation) {
			if(phic != 'None' || hasPHIC == 1) {
				$J('#charge_type option').each(function(){
				    if (this.value == 'PHIC') {
				        exists = true;
				    }
				});

					// if(exists == false){
					// 	$J("#charge_type").append('<option value="PHIC">PHIC</option>');
					// }

				updateCoverage([enc_nr]);
					refreshTotal();
			}else{
				$J("#charge_type option[value='PHIC']").remove();
				updateCoverage([enc_nr]);
					refreshTotal();
			}
		}else {
			// $J("#charge_type").append('<option value="PERSONAL">TPL</option>');
			updateCoverage([enc_nr]);
			refreshTotal();
		}
	} else {
		if(hasPHIC != 1)
		$('charge_type').style.display = 'none';
	}

}

/* end rnel */

	//added by julius search hrd id order 01-06-2017
function recieved_orderloc(data)
{
	console.log("parseFloatEx");
	jQuery("#current_loc").html(data);
}

/**
 * added by Nick 11-20-2015 called from
 * the Search GUI(index.php?r=person/search)
 * @param response
 */
function loadPerson(response) {
	console.log("loadPerson");
	xajax_checkifhasphic(response.encounter_nr); // added by carriane 12/13/17
	//added by julius  01-06-2017
	xajax_getpharmalocation(response.encounter_nr);
	jQuery("#hrn_id").html(response.pid);
	//end by julius s 01-06-2017

	// added by carriane 10/24/17
	var IPBMOPD_enc = 14;
	var IPBMIPD_enc = 13;
	// end carriane
	
	// console.log(response);
	setTimeout(function() {
		loadTypeCharge(response.encounter_type, response.phic_nr, response.encounter_nr) // added by rnel load default pay type via pharmacy module
	
	},1000);
	xajax_getExpiryDate(response.encounter_nr); //Added by Matsuu
	for (var property in response) {
		if (response.hasOwnProperty(property)) {
			var element = jQuery('#' + property);
			if (element instanceof jQuery) {
				var tag = element.prop('tagName');
				var type = element.prop('type');
				if (tag == 'INPUT' && (type == 'hidden' || type == 'text')) {
					element.val(response[property] || '');
				} else if (tag == 'INPUT' && (type == 'radio' || type == 'checkbox')) {
					element.prop('checked', response[property]);
				} else if (tag == 'SPAN' || tag == 'TEXTAREA') {
					element.html(response[property] || '');
				} else {
					console.log('Unknown tag');
				}
			} else {
				console.log(property);
			}
			// else {
			// 	// alert('wew');
			// 	var element = jQuery('#' + property);
			// 	if (element instanceof jQuery) {
			// 		var tag = element.prop('tagName');
			// 		var type = element.prop('type');
			// 		if (tag == 'INPUT' && (type == 'hidden' || type == 'text')) {
			// 			element.val(response[property] || '');
			// 		} else if (tag == 'INPUT' && (type == 'radio' || type == 'checkbox')) {
			// 			element.prop('checked', response[property]);
			// 		} else if (tag == 'SPAN' || tag == 'TEXTAREA') {
			// 			element.html(response[property] || '');
			// 		} else {
			// 			console.log('Unknown tag');
			// 		}
			// 	} else {
			// 		console.log(property);
			// 	}
			// }
	
			if(response.hasfinal_bill == 1) {
				// console.log('test');
				changeChargeType();
				changeTransactionType(1);
			}
		}
			
	}
	if(response.hasfinal_bill == 1){
		$("iscash1").checked = true;
	}
	jQueryDialogSearch.dialog('close');
}
/*added By MARK 2016-11-03*/
 function viewTransDai(apiKey,pid){
 	console.log("viewTransDai");
                var viewTransDaiForm = "../../modules/pharmacy/seg-check-transaction-dai.php?SEGAPIKEY="+apiKey+"&hnumber="+pid;
                var dialogAUditNote = $j('<div id="dialogTrans"></div>')
                    .html('<iframe id="viewTransDaiData" style="border: 0px; " src="' + viewTransDaiForm + '" width="100%" height="345px"></iframe>')
                    .dialog({
                        autoOpen: true,
                        closeOnEscape: false,
                        modal: true,
                        height: "auto",
                        width: "80%",
                        show: 'fade',
                        hide: 'fade',
                        resizable: true,
                        draggable: true,
                        title: 'View Tansact Items ',
                        position: "top",
                        buttons: {	
                        			NewTab: function(){
                        				openInNewTabDAItrans(viewTransDaiForm);
                        			},
							        Close: function() {
							         		// $j(this).dialog( "close" );
							        
				                         	$j(this).dialog( "close" );
				                         
							
							        }
							      },
					 	dialogClass: 'my-dialog'
                    });
                    $j('.my-dialog .ui-button-text:contains(NewTab)').text('Open new tab');
    		}
  
  function openInNewTabDAItrans(url) {
	  var win = window.open(url, '_blank');
	  win.focus();
  }	

  function disableDRF(id){
  	var DRF = ['dosage','route','frequency'];
  	DRF.each(function(v){
  		$j('input[list=row'+v+id).val("N/A");
  		$j('input[list=row'+v+id).attr('readonly','readonly');

  	});
  }

/*END added By MARK 2016-11-03*/



//added by VAN 03-13-2013
//fixed for bug id 110
//Commented By Jarel Mandated by Dr. Vega for HSM changes
/*function validatePHIC(){

    if (!$("iscash1").checked) {
        if($J('#charge_type').val()=="PHIC") {
            var phic_nr = $J('#phic_nr').html();
            phic_nr = phic_nr.replace(/-/g,'');

            //if phic is temporarary or not the right format of phic number which is PHIC nr has a 16 digit format
            //if ((phic_nr.toLowerCase().match('temp')=='temp') || (phic_nr.length!=12)){
            if (phic_nr.toLowerCase().match('temp')=='temp'){
                return false;
            }else
                return true;
        }else{
            return true;
        }
    }else{
        return true;
    }
}*/

function removeTplChargeType(isSet=0){
	console.log("removeTplChargeType");
	var charge_type = $J('#charge_type');
    var phic_nr = $J('#phic_nr');
    var area = $J("#area");
    var exclude_area = $J("#exclude_area").val();
    var enc = $('encounter_nr').value;
    var accomodation = $("accomodation").value;
    var admission_accomodation = $('admission_accomodation').value;
	var hasphic = 0;
    var hastpl = 0;
    var coverageAmount = document.getElementById('cov_amount').innerHTML;
	var getCoverangeAmount = $('phic_coverage').value;
	var message_status='';
	var ptype = $('encounter_type_show').innerHTML;


				if (phic_nr) phic_nr = phic_nr.text();
				else phic_nr = 'None';

				if (area) area = area.val();
			    else area = "";

			    $J("#charge_type > option").each(function(){
			    	var thisval = this.value;
			    	if (thisval == 'PHIC') hasphic = 1;
			    	if (thisval == 'PERSONAL') hastpl = 1;
			    });
			    // alert(exclude_area);
			    if (accomodation == privateAccomodation || admission_accomodation == privateAccomodation) {
			    	message_status+="(Payward Patient)";
			    	if(exclude_area==1 && $('mem_category').innerHTML != 'N/A'){
			    		message_status+="(HI/OR/OR-IWNH Area w/ PHIC)";
			    		if(isSet == 0){
			    			message_status+="(Default)";
			    			if (hasphic==0) {
								$J("#charge_type").append('<option value="PHIC">PHIC</option>');
							}
							if (hastpl==0) {
								$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
							}
							if(getCoverangeAmount <= 0) charge_type.val('PERSONAL');
							else charge_type.val('PHIC');
			    		}else{
			    			message_status+="(Manual)";
			    // 			if (hastpl==1) {
							// 	$J("#charge_type option[value='PERSONAL']").remove();
							// }
							if (hastpl==0) {
								$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
							}
							if (hasphic==0) {
								$J("#charge_type").append('<option value="PHIC">PHIC</option>');
							}
			    		}
			    	}else{
			    		message_status+="(Non-HI/OR/OR-IWNH)";
			    		if(isSet == 0){
			    			message_status+="(Default)";
				    		if (phic_nr=='None' && $('mem_category').innerHTML == 'N/A'){
				    			message_status+="(Non-PHIC)";
				    			if (hasphic==1) {
									$J("#charge_type option[value='PHIC']").remove();
								}
								if (hastpl==0) {
									$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
								}
								charge_type.val('PERSONAL');
				    		}else{
				    			message_status+="(PHIC)";
				    			if(getCoverangeAmount <= 0){
				    				message_status+="(Zero Coverage)";
				    				if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==1) {
										$J("#charge_type option[value='PERSONAL']").remove();
									}
									charge_type.val('PHIC');
				    			}else{
				    				message_status+="(Has Coverage)";
				    				if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==1) {
										$J("#charge_type option[value='PERSONAL']").remove();
									}
									charge_type.val('PHIC');
				    			}
				    		}
				    	}else{
				    		message_status+="(Manual)";
				    		if (phic_nr=='None' && $('mem_category').innerHTML == 'N/A'){
				    			message_status+="(Non-PHIC)";
				    			if (hasphic==1) {
									$J("#charge_type option[value='PHIC']").remove();
								}
								if (hastpl==0) {
									$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
								}
				    		}else{
				    			message_status+="(PHIC)";
				    			if(getCoverangeAmount <= 0){
				    				message_status+="(Zero Coverage)";
				    	// 			if (hasphic==1) {
									// 	$J("#charge_type option[value='PHIC']").remove();
									// }
									if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==1) {
										$J("#charge_type option[value='PERSONAL']").remove();
									}
				    			}else{
				    				message_status+="(Has Coverage)";
				    				if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==1) {
										$J("#charge_type option[value='PERSONAL']").remove();
									}
				    			}
				    		}
				    	}
			    	}
				}else {
					if($('pid').value==''){
						message_status+="(No patient)";
						if (hastpl==0) {
							//$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
						}
						//charge_type.val('PERSONAL');
					}else{
						message_status+="(Service Ward Patient)";
			    		if(isSet == 0){
			    			message_status+="(Default)";
				    		if (phic_nr=='None' && $('mem_category').innerHTML == 'N/A'){
				    			message_status+="(Non-PHIC)";
				    			if (hasphic==1) {
									$J("#charge_type option[value='PHIC']").remove();
								}

								if(ptype != 'WALK-IN'){
									if (hastpl==0) {
										$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
									}
									charge_type.val('PERSONAL');
								}
				    		}else{
				    			message_status+="(PHIC)";
				    			if(getCoverangeAmount <= 0){
				    				message_status+="(Zero Coverage)";
									// if (hasphic==1) {
									// 	$J("#charge_type option[value='PHIC']").remove();
									// }
									//As per master 11-20-2019 start
									if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									//As per master 11-20-2019 end
									if (hastpl==0) {
										$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
									}
									// charge_type.val('PERSONAL');
									//As per master 11-20-2019 start
									charge_type.val('PHIC');
									//As per master 11-20-2019 end
				    			}else{
				    				message_status+="(Has Coverage)";
				    				if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==0) {
										$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
									}
									charge_type.val('PHIC');
				    			}
				    		}
				    	}else{
				    		message_status+="(Manual)";
				    		if (phic_nr=='None' && $('mem_category').innerHTML == 'N/A'){
				    			message_status+="(Non-PHIC)";
				    			if (hasphic==1) {
									$J("#charge_type option[value='PHIC']").remove();
								}
								if(ptype != 'WALK-IN'){
									if (hastpl==0) {
										$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
									}
								}
				    		}else{
				    			message_status+="(PHIC)";
				    			if(getCoverangeAmount <= 0){
				    				message_status+="(Zero Coverage)";
				    	// 			if (hasphic==1) {
									// 	$J("#charge_type option[value='PHIC']").remove();
									// }
									if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==0) {
										$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
									}
				    			}else{
				    				message_status+="(Has Coverage)";
				    				if (hasphic==0) {
										$J("#charge_type").append('<option value="PHIC">PHIC</option>');
									}
									if (hastpl==0) {
										$J("#charge_type").append('<option value="PERSONAL">TPL</option>');
									}
				    			}
				    		}
				    	}
					}
					
				}
				// alert($('charge_type').value);
				if($('charge_type').value=='PHIC'){
					$('cov_type').update('PHIC Coverage:');
					$('cov_type').show();
					$('cov_amount').show();
				}else{
					$('cov_type').update('');
					$('cov_amount').update('');
					$('coverage').setAttribute('value',-1);
				}
				console.log(message_status);
				refreshTotal();
}

function checkExcludedArea(){
	console.log("checkExcludedArea");
	var area = $J("#area").val();
	xajax_getExcludedAreas(area);
	changeTransactionType();
}
