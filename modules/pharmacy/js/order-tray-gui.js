var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;



function display(str) {
	if($('ajax_display')) $('ajax_display').innerHTML = str.replace('\n','<br>');
	$('#DAIcon').val(0);
}

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function parseFloatEx(x) {
	var str = x.toString().replace(/\,|\s/,'')
	return parseFloat(str)
}

function setPagination(pageno, lastpage, pagen, total) {
	currentPage=parseInt(pageno);
	lastPage=parseInt(lastpage);
	firstRec = (parseInt(pageno)*pagen)+1;
	if (currentPage==lastPage)
		lastRec = total;
	else
		lastRec = (parseInt(pageno)+1)*pagen;
	if (parseInt(total))
		$("pageShow").innerHTML = '<span>Showing '+(formatNumber(firstRec))+'-'+(formatNumber(lastRec))+' out of '+(formatNumber(parseInt(total)))+' record(s)</span>'
	else
		$("pageShow").innerHTML = ''
	$("pageFirst").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pagePrev").className = (currentPage>0 && lastPage>0) ? "segSimulatedLink" : "segDisabledLink";
	$("pageNext").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
	$("pageLast").className = (currentPage<lastPage) ? "segSimulatedLink" : "segDisabledLink";
}

function jumpToPage(el, jumpType, set) {
	if (el.className=="segDisabledLink") return false;
	if (lastPage==0) return false;
	switch(jumpType) {
		case FIRST_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',0);
		break;
		case PREV_PAGE:
			if (currentPage==0) return false;
			startAJAXSearch('search',currentPage-1);
		break;
		case NEXT_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',parseInt(currentPage)+1);
		break;
		case LAST_PAGE:
			if (currentPage >= lastPage) return false;
			startAJAXSearch('search',lastPage);
		break;
	}
}
/*Edited By MARK 2016-10-04*/
function prepareAdd(id, mode,DiacOn,isInventory) {

	var details = new Object();
	var areaSelected = $('area').options[$('area').selectedIndex];
	var cash = parseFloatEx($('cash'+id).value),
	charge = parseFloatEx($('charge'+id).value);
	var isBloodBB = $('isBloodBB').value ? $('isBloodBB').value : '0';

	/*if ( isNaN(cash) || (cash <= 0) || isNaN(charge) || (charge <= 0) ) {
		alert("Price not set. Please contact pharma admin for updates...");
		return false
	}*/

	details.id = $('id'+id).innerHTML;
	details.name = $('name'+id).innerHTML;
	details.desc = $('desc'+id).innerHTML;
	details.prod_class = $('prod_class'+id).value;
	details.prcCash = parseFloatEx($('cash'+id).value);
	details.prcCharge= parseFloatEx($('charge'+id).value);
	details.prcCashSC= parseFloatEx($('cashsc'+id).value);
	details.prcChargeSC= parseFloatEx($('chargesc'+id).value);
	details.isSocialized= $('soc'+id).value;
	details.prcDiscounted= parseFloatEx($('d'+id).value);
	details.source = $('source'+id).value;
	details.account_type = $('account_type'+id).value;
	details.stock = parseFloatEx($('stock'+id).innerHTML);
	details.dispensed_qty = 0;
	details.area = areaSelected.value;
	details.area_name = areaSelected.text;
	/*added by MARK January 17, 2017*/
	details.NewCash =  $('NewCash'+id).value;
	details.NewCharge =  $('NewCharge'+id).value;
    details.is_fs = $('is_fs'+id).value;

	details.price_cash_CASH =  parseFloatEx($('price_cash'+id).value);
	details.price_charge_CHARGE =  parseFloatEx($('price_charge'+id).value);

	details.dosage = $j('#dosage'+details.id+'_'+details.area).siblings('input[list=dosage'+details.id+'_'+details.area+']').val();
	details.route = $j('#route'+details.id+'_'+details.area).siblings('input[list=route'+details.id+'_'+details.area+']').val();
	details.frequency =$j('#frequency'+details.id+'_'+details.area).siblings('input[list=frequency'+details.id+'_'+details.area+']').val();
	var qty = $('quantity'+details.id+'_'+details.area).value;

	var isBloodBank = 'BB';
	var isNotBloodBank = 1;
	var isMedicines = 'M';

	if(details.area != isBloodBank || isBloodBB != isNotBloodBank) {
		if (details.prod_class == isMedicines) {
			if (details.dosage.trim() == '') {
				alert('Dosage is required!');
				return false;
			}

			if (details.frequency.trim() == '') {
				alert('Frequency is required!');
				return false;
			}

			if (details.route.trim() == '') {
				alert('Route is required!');
				return false;
			}
		}
	}

	/*end added by MARK January 17, 2017*/
	//alert(details.prcDiscounted);
	if ($('noqty'+id).value != '1' && DiacOn !=0) {
		while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0 || (qty > details.stock) || (qty % 1 !== 0)) {
			if(qty > details.stock)
				qty = prompt("Quantity must not exceed Stock on hand:");
			else
				qty = prompt("Enter valid quantity:");
			if (qty === null) return false;
		}
		}
	// added By MARK 2016-04-10
	else if (DiacOn ==0 ) {
		while (isNaN(parseFloat(qty)) || parseFloat(qty)<=0 || (qty > details.stock) || (qty % 1 !== 0)) {
			if(qty > details.stock)
				qty = prompt("Quantity must not exceed Stock on hand:");
			else
				qty = prompt("Enter valid quantity:");
			if (qty === null) return false;
		}
	}

	details.is_override = DiacOn;
	details.isInventory = (isInventory == null) ? "NO" : "YES";
	details.qty = qty;
	alert($('name'+id).innerHTML + 'HAS BEEN ADDED.');
	var list = window.parent.document.getElementById('order-list'),
	result = window.parent.appendOrder(list,details,0,mode);
	if (result)  {
		//alert('Item added to order list...');
	}
	else
		alert('Failed to add item...');
	if (window.parent && window.parent.refreshDiscount) window.parent.refreshDiscount()
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

function addProductToList(listID, details) {
	// ,id, name, desc, cash, charge, cashsc, chargesc, d, soc
	var list=$(listID), dRows, dBody, rowSrc;
	var _MEDICINE_ITEM = "M";
	var i;
	if (list) {
		var ifClassified =  $('ifClassified').value;
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.

		if (typeof(details)=="object") {
			var id = details.id,
				name = details.name,
				desc = details.desc,
				prod_class = details.prod_class,				
				cash = details.cash,
				charge = details.charge,
				cashsc = details.cashsc,
				chargesc = details.chargesc,
				d = details.d,
				soc = (ifClassified !="") ? details.soc : 0,
				noqty = details.noqty,
				source = details.source,
				account_type = details.account_type,
                is_fs = details.isFs;
				stock = details.stock;
				isInventory = details.isInventory;
				iTemCode = details.iTemCode;
				barcode = details.barcode;
				areaName = details.areaName;
				area = details.area;
				areaISsoc = details.areaISsoc;
				/*added by MARK January 17, 2017*/
				NewCash = details.NewCash;
				NewCharge = details.NewCharge;
				MYfinalCash = (soc==1 && areaISsoc==1) ? cash : NewCash;

				price_cash = details.price_cash;
				price_charge = details.price_charge;

				/*Invest*/
				if(prod_class == _MEDICINE_ITEM){
					dosagePrevious = details.dosagePrevious;
					frequencyPrevious = details.frequencyPrevious;
					routePrevious = details.routePrevious;
				}else{
					dosagePrevious = "N/A";
					frequencyPrevious = "N/A";
					routePrevious = "N/A";
				}
				
				
				/*added by MARK January 17, 2017*/

			var cashHTML, chargeHTML;
			var cashSeniorHTML, chargeSeniorHTML;
			var DAIcon = $j("#DAIcon").val();/*added By MARK 2016-04-10*/
			/* add field is_override,remarks MARK 2016-04-10*/
			PIvButton = '';
			styleColor = ""
			if(isInventory ==1){ 	//disable button if stock is below zero
				if (DAIcon ==0)
					{
				/*edited replace InventoryAdd to  prepareAdd 2016-10-30*/
					PIvButton = '<button title="Override this item."  style="cursor:pointer;"  onclick="prepareAdd(\''+id+'\', \''+details.mode+'\',\''+'1'+'\',\''+'isInventory'+'\',\''+'isInventory'+'\');"><img width="16" height="16" border="0" align="absmiddle" src="../../gui/img/common/default/exclamation.png"><font style="font-size:12px;font-weight: bold;">&nbsp;Add</font></button>';
				    styleColor ='background:red;';
					}
				else {
					if (stock <=0 && iTemCode =="" && barcode=="" && isInventory ==1)
						PIvButton ='<img title="is in inventory" border="0" onclick="alert(\''+'Item has no item code and barcode. Please contact administrator. '+'\')" src="../../images/his_addbtn.gif" id="add-item" class="segSimulatedLink">';
					else if(stock=== undefined && isInventory ==1)
						 PIvButton ='<img title="ITEM NOT IN'+areaName+'" border="0" style="cursor:no-drop;" src="../../images/his_addbtn.gif" id="add-item" class="segSimulatedLink">';
			 		else if(stock <= 0 && isInventory ==1)
						 PIvButton ='<img title="ITEM OUT OF STOCK" border="0" style="cursor:no-drop;" src="../../images/his_addbtn.gif" id="add-item" class="segSimulatedLink">';
			 		else if(stock=== 'n/a' && isInventory ==1)
						 PIvButton ='<img title="ITEM NOT IN '+areaName+'" border="0" style="cursor:no-drop;" src="../../images/his_addbtn.gif" id="add-item" class="segSimulatedLink">';
			 	
			 		else
			 			  PIvButton ='<img title="is in inventory" border="0" onclick="prepareAdd(\''+id+'\', \''+details.mode+'\',\''+'0'+'\',\''+'isInventory'+'\')" src="../../images/his_addbtn.gif" id="add-item" class="segSimulatedLink">';
			    			
			    	styleColor ='background:#449244;';		
			 	 }

			}else{
			
			 	  PIvButton ='<img title="'+(isInventory == 1 ? 'is in inventory ' : 'Item not in inventory')+'" border="0" onclick="prepareAdd(\''+id+'\', \''+details.mode+'\',\''+'0'+'\')" src="../../images/his_addbtn.gif" id="add-item" class="segSimulatedLink">';
			 	
			 
			}
			if (d>=0)

			rowSrc = "<tr>"+
									'<td>'+
										'<span id="name'+id+'" style="font:bold 12px Arial;color:#000066;width:6px">'+name+'</span><br />'+
										'<div style=""><div id="desc'+id+'" style="font:normal 11px Arial; color:#000000;width:6px">'+desc+'</div></div>'+
									'</td>'+									
									'<td align="center">'+
										'<input id="soc'+id+'" type="hidden" value="'+soc+'"/>'+
										'<span id="id'+id+'" style="font:bold 11px Arial;color:#660000">'+id+'</span>'+'<br>'+
										'<span id="barcode'+id+'" style="font:bold 11px Arial;color:#000000">'+barcode+'</span></td>'+
									'<td align="right" '+(cash<=0 ? '' : '')+'>'+
										'<input id="noqty'+id+'" type="hidden" value="'+(noqty ? '1' : '0')+'"/>'+
										'<input id="NewCash'+id+'" type="hidden" value="'+formatNumber(MYfinalCash,2)+'"/>'+
										'<input id="NewCharge'+id+'" type="hidden" value="'+formatNumber(NewCharge,2)+'"/>'+
										'<input id="d'+id+'" type="hidden" value="'+d+'"/>'+
										'<input id="prod_class'+id+'" type="hidden" value="'+prod_class+'"/>'+
										'<input id="cash'+id+'" type="hidden" value="'+cash+'"/>'+
										'<input id="source'+id+'" type="hidden" value="'+source+'"/>'+
                                        '<input id="is_fs'+id+'" type="hidden" value="'+is_fs+'"/>'+
										'<input id="account_type'+id+'" type="hidden" value="'+account_type+'"/>'+
										'<input id="price_cash'+id+'" type="hidden" value="'+price_cash+'"/>'+
										'<input id="price_charge'+id+'" type="hidden" value="'+price_charge+'"/>'+
											
										 ((soc==1 && areaISsoc==1 && ifClassified !="") ? formatNumber(cash,2) :formatNumber(NewCash,2)) +
										'</td>'+
									'<td align="right">'+
										'<input id="charge'+id+'" type="hidden" value="'+charge+'"/>'+(charge>0 ? formatNumber(charge,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="cashsc'+id+'" type="hidden" value="'+cashsc+'"/>'+(cashsc>0 ? formatNumber(cashsc,2) : '-')+'</td>'+
									'<td align="right">'+
										'<input id="chargesc'+id+'" type="hidden" value="'+chargesc+'"/>'+(chargesc>0 ? formatNumber(chargesc,2) : '-')+
									'</td>'+
/*									'<td align="center">'+
										'<input class="jedInput" id="qty'+id+'" type="text" style="text-align:right;width:30px" value="" '+(noqty ? 'disabled="disabled"' : '')+' style="text-align:right" onblur="this.value = isNaN(parseFloatEx(this.value))?\'\':parseFloatEx(this.value)"/>'+
									'</td>'+ */
									'<td align="center">'+
										'<span id="stock'+id+'" style="font:bold 11px Arial;color:#660000">'+(stock === undefined ? 'n/a' : stock)+'</span></td>'+
									'</td>'+
									'<td align="center">'+
										'<input onchange="checkQty(this);" onkeypress="return event.charCode >= 48 && event.charCode <= 57" min="1" id="quantity'+id+'_'+area+'" name="quantity[]" class="segInput" style="width:50px;" type="number" value="1">'+
									'</td>'+
									'<td align="center">'+
										'<span style="font:bold 11px Arial;color:#660000">'+
											'<input oninput="chkInputLimit(this,500);" list="dosage'+id+'_'+area+'" value="'+dosagePrevious+'" class="dlist segInput" style="width:80px;" maxlength="500">'+
											'<datalist id="dosage'+id+'_'+area+'"></datalist>'+
										'</span>'+
									'</td>'+
									'<td align="center">'+
										'<span style="font:bold 11px Arial;color:#660000">'+
											'<input oninput="chkInputLimit(this,50);" list="frequency'+id+'_'+area+'" value="'+frequencyPrevious+'" class="dlist segInput" style="width:100px;" maxlength="50">'+
											'<datalist id="frequency'+id+'_'+area+'"></datalist>'+
										'</span>'+
									'</td>'+
									'<td align="center">'+
										'<span style="font:bold 11px Arial;color:#660000">'+
											'<input oninput="chkInputLimit(this,500);" list="route'+id+'_'+area+'" value="'+routePrevious+'" class="dlist segInput" style="width:100px;" maxlength="500">'+
											'<datalist id="route'+id+'_'+area+'"></datalist>'+
										'</span>'+
									'</td>'+
									'<td style='+styleColor+' >'+PIvButton+
									'</td>'+
								'</tr>';
		}
		else {
			rowSrc = '<tr><td colspan="12" style="">No such product exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}/*function's Added MARK 2016-10-04*/
 function  InventoryAdd(id,model,Override,name,isInventory){
 				
                var dialogOverrideItems = $j('#OverrideItem')
                    .dialog({
                        autoOpen: true,
                        modal: true,
                        height: "auto",
                        width: "50%",
                        show: 'fade',
                        hide: 'explode',
                        resizable: false,
                        draggable: true,
                        title: 'Override Item: ('+id+')'+name,
                        position: "center",
						buttons: {
							        Yes: function() {
							        	prepareAdd(id,model,'1','isInventory');
							        		
							         		$j(this).dialog( "close" );
							        },
							        No: function(){
							        		$j(this).dialog( "close" );
							        }
							      }
                    });
    		}


    function ErrorConnection(){
    	    var ErrorConnection = $j('#error-message')
                    .dialog({
                    	dialogClass: 'transparent-dialog',
                        autoOpen: true,
                        modal: true,
                        height: "auto",
                        width: "50%",
                        show: 'bounce',
                        hide: 'explode',
                        resizable: false,
                        draggable: true,
                        title: 'Error Connection ',
                        position: "center",
                         open: function(event, ui) {
            				$j(".transparent-dialog").css({background: "transparent",border:"none"});	
			            }
					
                    });
                      $j(".ui-dialog-titlebar").hide();
                       
                             $j('#closeDD').click(function() {
                             	$j("#hasClicked").val(1);
						     $j('#error-message').dialog("close");  
						}); 

		    }
		function ErrorConnectionDAI2(){
				var offline = $j("#DAIcon").val();
				var offlineLabel = $j("#INV_address").val();

				if (offline == 0){
					// if ($j("#hasClicked").val() == 0) {
					// 	// ErrorConnection();
					// }
					
					$('ajax_display').innerHTML ="<em><font color='red'><strong>&nbsp;<span id='warningcaption'>"
				            		+"INVENTORY SYSTEM("+offlineLabel+")IS DOWN. BUT STILL, YOU CAN PROCEED TO HIS TRANSACTION.</span></strong></font></em>";
				}else if(offline==1){
				     $('ajax_display').innerHTML ="<em><font color='Green'><strong>&nbsp;<span id='warningcaption'>"
				            		+"INVENTORY SYSTEM("+offlineLabel+")IS CONNECTED....</span></strong></font></em>";
				
				}

		}
  /*END all functions Added MARK 2016-10-04*/

  		function updateDAIStatus(offline){
  			$j("#DAIcon").val(offline);
  			ErrorConnectionDAI2();
  		}

  		function disableDRF(idarea){

  			var DRF = ['dosage','frequency','route'];
  			DRF.each(function(v){
  				$j('#'+v+idarea).siblings('input[list='+v+idarea+']').attr("disabled","disabled");
  			});
  		}


  		function checkQty(quantity){
  			var str = quantity.value;
			var reg = new RegExp('^[0-9]+$');
			var res = reg.test(str); 
  			if(!res || str==0) quantity.value = 1;
  		}

  		function chkInputLimit(elem,maxlength){
			if (elem.value.length == maxlength) {
				alert('You have reached the maximum number of Characters!');
			}
		}