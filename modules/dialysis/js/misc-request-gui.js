function empty_misc() {
	var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
	table1.innerHTML = '<tr id="empty_misc_row"><td colspan="7">Miscellaneous charges empty...</td></tr>';
	J("input[@name='misc_item[]']").remove();
	J("input[@name='misc_prc[]']").remove();
	J("input[@name='misc_account_type[]']").remove();
	J("input[@name='misc_qty[]']").remove();
	update_total_misc();
}

function warnClear() {
    var items = document.getElementsByName('misc_item[]');
    if (items.length == 0) return true;
    else return confirm('Performing this action will clear the order tray. Do you wish to continue?');
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function parseFloatEx(x) {

	 if (x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
	 }
	 else {
		 return x;
	 }
}

function retrieve_misc(table, details) {
    //edited by: ian villanueva
    // alert(details.lock);
    if (!table) table = $('misc_list');
    if (table) {
        var dBody = $(table).select("tbody")[0];
        if (dBody) {
            var table1 = $(table).getElementsByTagName('tbody').item(0);
            if ($('empty_misc_row')) {
                table1.removeChild($('empty_misc_row'));
            }
            var dRows = dBody.getElementsByTagName("tr");
            if (details) {
                var discount = getDiscount(details.lock);
                var flag_ico = ' ';

                var status;
                var discount2 = parseFloat(details.quantity * details.price * discount);
                var nonSocialDiscount = $('nonSocialDiscount').value;

                // added by gelie 10-18-2015
                if(details.lock == 1 && $('is_senior').value.toUpperCase() == 'SC'){        //for none-socialized misc services
                        discount2 = parseFloat(details.quantity * details.price * 0.20);    //discount 20% for seniors
                }
                // end gelie
                else if(details.lock == 1 && ($('discountid').value.toUpperCase() == 'PHSDEP' || $('discountid').value.toUpperCase() == 'PHS')){
                	discount2 = parseFloat(details.quantity * details.price * $('nonSocialDiscount').value);
                }

                var total = parseFloat((details.quantity * details.price - discount2) - details.creditgrant);

                var flag = '';
                if (total == 0) {
                    flag = 'charity';
                }
                alt = (dRows.length % 2 > 0) ? ' class="alt"' : ''
                if (details.status != "") {
                    if (details.status.toLowerCase() == "cmap")
                        status = '<td class="centerAlign"><img src="../../images/flag_cmap.gif" title="Item charged to CMAP"/></td>';
                    else if (details.status.toLowerCase() == "lingap")
                        status = '<td class="centerAlign"><img src="../../images/flag_lingap.gif" title="Item charged to LINGAP"/></td>';
                    else if (details.status.toLowerCase() == "paid")
                        status = '<td class="centerAlign"><img src="../../images/flag_paid.gif" title="Item paid"/></td>';
                    else if (details.status.toLowerCase() == "charity")
                        status = '<td class="centerAlign"><img src="../../images/btn_charity.gif" title="Item paid"></td>';
                    else if (details.status.toLowerCase() == "crcu")
                        status = '<td class="centerAlign"><img src="../../images/btn_crcu.gif" title="Item paid"></td>';
                    else
                        status = '<td class="centerAlign"><img src="../../images/bullet.gif"/></td>';
                } else {
                    status = '<td class="centerAlign"><img src="../../images/bullet.gif"/></td>';
                }

                var is_disabled = "";
                var inputqty = "";
                if (details.disable == "1" || $('view_from').value == "ssview") {
                    is_disabled = status;
                    inputqty = '<input type="text" class="segInput" name="misc_qty[]" id="misc_qty' + details.code + '" value="' + details.quantity + '" readonly style="width:57px;text-align:right" onblur="reCalcTotal(\'' + details.id + '\', \'' + details.lock + '\');"/>';
                } else {
                    is_disabled = '<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="remove_misc_charge(\'' + details.code + '\')"/></td>';
                    inputqty = '<input type="text" class="segInput" name="misc_qty[]" id="misc_qty' + details.code + '" value="' + details.quantity + '" style="width:57px;text-align:right" onblur="reCalcTotal(\'' + details.code + '\');"/>';
                }

                rowSrc = '<tr class="' + alt + '" id="misc_row' + details.code + '">' +
                    is_disabled +
                    '<td>' +
                    '<span style="color:#660000">' + flag_ico + ' ' + details.code + '</span>' +
                    '<input type="hidden" name="misc_item[]" id="misc_item' + details.code + '" value="' + details.code + '"/>' +
                    //added by: ian villanueva
                    '<input type="hidden" name="misc_dsc[]" id="misc_item' + details.code + '" value="' + details.lock + '"/>' +
                    '<input type="hidden" name="misc_account_type[]" id="misc_account_type' + details.code + '" value="' + details.account_type + '"/>' +
                    '<input type="hidden" name="misc_prc[]" id="misc_prc' + details.code + '" value="' + details.price + '"/>' +
                    '<input type="hidden" name="misc_request_flag[]" id="misc_request_flag' + details.id + '" value="' + flag + '"/>' +
                    '<input type="hidden" name="misc_adj_prc[]" id="misc_adj_prc' + details.code + '" value="' + total + '"/>' +
                    '<input type="hidden" name="misc_clinicInfo[]" id="misc_clinicInfo'+details.code+'" value="'+details.clinicInfo+'"/>'+
                    '<input type="hidden" name="misc_createId[]" id="misc_createId' + details.code + '" value="' + details.create_id + '"/>' +
                    '<input type="hidden" name="misc_createDt[]" id="misc_createDt' + details.code + '" value="' + details.create_dt + '"/>' +
                    '<input type="hidden" name="misc_discnt[]" id="misc_discnt'+details.code+'" value="'+nonSocialDiscount+'"/>'+
                    '</td>' +
                    //'<td><span style="color:#660000">'+details.type_name+'</span></td>'+
                    '<td><span style="color:#660000">' + details.name + '</span></td>' +
                    '<td class="centerAlign">' +
                    inputqty +
                    '</td>' +
                    '<td class="rightAlign" id="row_misc_prc' + details.code + '">' + formatNumber(details.price, 2) + '</td>' +
                    '<td class="rightAlign" id="row_misc_total' + details.code + '">' + formatNumber(total, 2) + '</td>' +
                    //status+
                    '<input type="hidden" id="misc_item_disabled' + details.code + '" name="misc_item_disabled[]" value="' + details.disable + '"/>' +
                    '</tr>';
            } else {
                rowSrc = '<tr id="empty_misc_row"><td colspan="9">Miscellaneous charges is empty...</td></tr>';
            }
            $('credittotals').value = details.credittotals;
            dBody.insert(rowSrc);
            update_total_misc();
        }
    }
}

function update_total_misc() {
	// to do: discounts
	//edited by: ian villanueva
    var discount = getDiscount(0);

	var misc = document.getElementsByName('misc_item[]');
	var quantity = document.getElementsByName('misc_qty[]');
	var original_misc_price = document.getElementsByName('misc_prc[]');
	var dsc = document.getElementsByName('misc_dsc[]');
	var accType = document.getElementsByName('misc_account_type[]');//added by Nick 3/24/2014
	var dscnt = document.getElementsByName('misc_discnt[]');//added by Gervie 01/11/2016
    var lock = document.getElementsByName('misc_lock[]');
	
	var sub_total = 0;
	var discount_total = 0;
	var net_total = 0;

	for (var i=0; i<misc.length; i++) {
		sub_total += parseFloat(quantity[i].value * original_misc_price[i].value );
		//added by: ian villanueva
		//updated by Nick 3/24/2014
		if (parseFloat(dsc[i].value) != 1){       //if item is socialized
			// if(accType[i].value == '50' && $('view_from').value != 'ssview'){
			// 	discount_total += 0;
			// }
   //          else{
				discount_total += parseFloat((quantity[i].value * original_misc_price[i].value) * discount );
			// }
		}
        // added by gelie 10-17-2015    
        else if(parseFloat(dsc[i].value) == 1 && $('is_senior').value.toUpperCase() == 'SC'){                    //for none-socialized misc services
                    discount_total += parseFloat((quantity[i].value * original_misc_price[i].value) * 0.20);    //discount 20% for seniors
        }
        // end gelie
        else if(parseFloat(dsc[i].value) == 1 && ($('discountid').value.toUpperCase() == 'PHSDEP' || $('discountid').value.toUpperCase() == 'PHS')){
			discount_total += parseFloat((quantity[i].value * original_misc_price[i].value) * dscnt[i].value);
		}
        else{
			discount_total += 0 ;
		}
	}
	//comment out by: ian villanueva
    // discount_total = parseFloat(sub_total * discount);
    // alert(discount_total);
    var credittotals = $('credittotals').value;
    if(credittotals == ''){
        credittotals = 0;
    }

    discount_total = parseFloat(discount_total) + parseFloat(credittotals);
    net_total = parseFloat(sub_total - discount_total);

	J('#misc_subtotal').html(formatNumber(sub_total, 2));
	J('#misc_discount_total').html('-'+formatNumber(discount_total, 2));
	J('#misc_net_total').html(formatNumber(net_total, 2));

}

function append_empty_misc() {
	var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
	var row = document.createElement("tr");
	var cell = document.createElement("td");
	row.id = "empty_misc_row";
	cell.appendChild(document.createTextNode('Miscellaneous charges is empty...'));

	cell.colSpan = "8";
	row.appendChild(cell);
	$('misc_list').getElementsByTagName('tbody').item(0).appendChild(row);
}

function update_misc_total(id) {

	 J('#total_misc_td'+id).html((J('#quantity'+id).val() * J('#original_misc_price'+id).val()).toFixed(2));
	 update_total_misc();
}

function getDiscount(lock) {
    var discount = 0;
    
    if (($('discount').value != '' && $('transaction_type').value == 1 && lock != 1) || $('ptype').value == 6) {
        if ($('ptype').value == 6) {
            if($('discountid').value == 'SC') {
                discount = 0.2;
            } else {
                discount = 0;
            }
        } else {
            discount = $('discount').value;
        }
    } else {
        discount = 0;
    }

    return discount;
}

function addServiceToList(details) {

		if ($('misc_row'+details.id)) {
		alert('This item is already in the list.');
		return false;
	}
	else
	{
		var table = $('misc_list');
		if(table){
			var dBody = table.select("tbody")[0];
			if ($('empty_misc_row')) {
				table.getElementsByTagName('tbody').item(0).removeChild($('empty_misc_row'));
			}
			if(dBody){
				var dRows = dBody.getElementsByTagName("tr");
				if(details){
					var discount = getDiscount(details.lock);
					var discount2;

                    //added by Nick, 3/13/2014
                    if(details.lock == 1)
                    	flag_ico = '<img border="0" onclick="" src="../../images/btn_nonsocialized.gif">';
                    else
                    	flag_ico = '';



                    // if(details.misc_type == 50 && $('view_from').value != 'ssview'){
                    // 	discount2 = 0;
                    // }
                    if(details.lock == 1 && $('is_senior').value.toUpperCase() == 'SC') {  //for none-socialized misc services
                        discount2 = parseFloat(details.qty*details.price * 0.20);          //discount 20% for seniors
                    }
                    //end gelie
                    else if(details.lock == 1 && ($('discountid').value.toUpperCase() == 'PHSDEP' || $('discountid').value.toUpperCase() == 'PHS')){
                    	discount2 = parseFloat(details.qty*details.price * details.discount);
                    }
                    else{
                        discount2 = parseFloat(details.qty*details.price*discount);

                    }

                    var total = parseFloat(details.qty*details.price-discount2);
                    var flag='';
                    if(total==0){
                    	flag = 'charity';
                    }
					alt = (dRows.length%2>0) ? ' class="alt"':''
					rowSrc = '<tr class="'+alt+'" id="misc_row'+details.id+'">'+
							'<td style="height:30px" class="centerAlign"><img class="segSimulatedLink" src="../../images/close_small.gif" border="0" onclick="remove_misc_charge(\''+details.id+'\')"/></td>'+
							'<td>'+
								'<span style="color:#660000">'+flag_ico+' '+details.id+'</span>'+
								'<input type="hidden" name="misc_item[]" id="misc_item'+details.id+'" value="'+details.id+'"/>'+
								'<input type="hidden" name="misc_account_type[]" id="misc_account_type'+details.id+'" value="'+details.misc_type+'"/>'+
								//added by: ian villanueva
								'<input type="hidden" name="misc_dsc[]" id="misc_item'+details.code+'" value="'+details.lock+'"/>'+
								'<input type="hidden" name="misc_prc[]" id="misc_prc'+details.id+'" value="'+details.price+'"/>'+
                                '<input type="hidden" name="misc_adj_prc[]" id="misc_adj_prc'+details.id+'" value="'+total+'"/>'+
                                '<input type="hidden" name="misc_request_flag[]" id="misc_request_flag'+details.id+'" value="'+flag+'"/>'+
                                '<input type="hidden" name="misc_clinicInfo[]" id="misc_clinicInfo'+details.id+'" value="'+details.clinicInfo+'"/>'+
                                '<input type="hidden" name="misc_createId[]" id="misc_createId'+details.id+'" value="'+$('misc_create_id').value+'"/>'+
                                '<input type="hidden" name="misc_createDt[]" id="misc_createDt'+details.id+'" value="'+$('misc_create_dt').value+'"/>'+
								'<input type="hidden" name="misc_discnt[]" id="misc_discnt'+details.id+'" value="'+details.discount+'"/>'+
							'</td>'+
							'<td><span style="color:#660000">'+details.name+'</span></td>'+
							'<td class="centerAlign">'+
								'<input type="text" class="segInput" name="misc_qty[]" id="misc_qty'+details.id+'" value="'+details.qty+'" style="width:57px;text-align:right" onblur="reCalcTotal(\''+details.id+'\', \''+details.lock+'\');"/>'+
							'</td>'+
							'<td class="rightAlign" id="row_misc_prc'+details.id+'">'+formatNumber(details.price,2)+'</td>'+
							'<td class="rightAlign" id="row_misc_total'+details.id+'">'+formatNumber(total,2)+'</td>'+
							'<input type="hidden" id="misc_item_disabled'+details.id+'" name="misc_item_disabled[]" value="'+details.disable+'"/>'+
						'</tr>';
				}else
				{
					rowSrc = '<tr id="empty_misc_row"><td colspan="9">Miscellaneous charges is empty...</td></tr>';
				}
				dBody.insert(rowSrc);
				update_total_misc();
			}
		}
	}
}

//edited by: ian villanueva
function reCalcTotal(id, lock)
{
    var discount = getDiscount(lock);

	var qty = $('misc_qty'+id).value;
	if(qty==0){
    	alert("Invalid Input");
    	$('misc_qty'+id).value = 1;
    	qty = 1;
    }
	var price = $('row_misc_prc'+id).innerHTML;
	var new_total = parseFloatEx(qty) * parseFloatEx(price);
    var discount_total = parseFloat(new_total * discount);
    // alert (discount);
    // $('dsc'+id).value = discount_total;
	$('row_misc_total'+id).innerHTML = formatNumber(new_total-discount_total,2);
    $('misc_adj_prc'+id).value = parseFloatEx(new_total-discount_total);
     // $('dsc'+id).value = parseFloatEx(new_total * discount);
	update_total_misc();
}

function remove_misc_charge(id) {
	var table1 = $('misc_list').getElementsByTagName('tbody').item(0);
	table1.removeChild($('misc_row'+id));

	if (!document.getElementsByName('misc_item[]') || document.getElementsByName('misc_item[]').length <= 0) {
		append_empty_misc();
	}
	update_total_misc();
}

function addSlashes(str) {
	var ret = str.replace('"','\\"');
	return ret.replace("'","\\'");
}