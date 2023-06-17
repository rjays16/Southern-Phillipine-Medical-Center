function prepareAdd(id) {
	var details = new Object();
	details.id = $('id'+id).value;
	details.name = $('name'+id).innerHTML;
	details.accre_nr= $('accre_nr'+id).value;
	details.expiration= $('expiration'+id).value;
	// alert(details.expiration);
	var group_action = $('group_action').value;
	var personell_nr_new = $('personell_nr').value;
    //alert('details = '+details.id+" , "+details.name+" , "+details.nr);
    accreditation_area.push({"group_action":$('group_action').value,"personell_nr":personell_nr_new,"accreditation_area": details.id});
  	window.parent.prepareAddData(accreditation_area);
    // console.log(accreditation_area);
    var newdate = new Date();
    newdate.setDate(newdate.getDate());
    
    var dd = newdate.getDate() - 1;
    var mm = ((newdate.getMonth() + 1) < 10 ? '0' : '') + (newdate.getMonth() + 1);
    var y = newdate.getFullYear();
    var someFormattedDate = y + '-' + mm + '-' + dd;

    if(details.expiration!=''){
    	if (someFormattedDate >= details.expiration){
    		alert('Expiration Date must be not later than the Current Date.');
    	}
    	else{
    		var list = window.parent.document.getElementById('order-list');
    		result = window.parent.appendOrder(list,details);	
    	}
    }else{
    	alert("Date is Empty");
    }
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

function enableButtonAdd(id){
	//alert("enableButtonAdd = "+id);
	document.getElementById('add_insurance'+id).disabled=false;
}

function disableButtonAdd(id){
	//alert("enableButtonAdd = "+id);
	document.getElementById('add_insurance'+id).disabled=true;	
}

function addProductToList(listID, id, firm_id, name, accreditation_nr, date, cnt) {	
	var currentDate = new Date();
	// alert(currentDate);
	var getYear = currentDate.getFullYear();
	var getMonth = currentDate.getMonth() + 1;
	var getDay = currentDate.getDate();
	
	if(getDay<10){
		getDay = '0'+getDay;
	}
	if(getMonth<10){
		getMonth = '0'+getMonth;

	}
	var dateFormat = getYear+'-'+getMonth+'-'+getDay;

	var list=$(listID), dRows, dBody, rowSrc;
	var i;
	var date;

	if (list) {
		dBody=list.getElementsByTagName("tbody")[0];
		dRows=dBody.getElementsByTagName("tr");

		// get the last row id and extract the current row no.
		if (id) {
				alt = (dRows.length%2)+1;
				var check, disable;
				
				if (accreditation_nr)
					disable = "";
				else
					disable = "disabled";
				rowSrc = "<tr>"+
									'<td width="1">'+
										'<span>'+cnt+'</span><br />'+
									'</td>'+
									'<td width="0.5">&nbsp;</td>'+
									'<td width="*">'+
										'<span id="name'+id+'" style="font:bold 11px Arial;color:#660000">'+firm_id+'</span><br />'+
										'<span id="desc'+id+'" style="font:normal 11px Arial; color:#003366">'+name+'</span>'+
									'</td>'+
									'<td><input type="hidden" size="2" id="id'+id+'" name="id'+id+'" value="'+id+'"></td>'+
									'<td width="25%" align="center"><input id="accre_nr'+id+'" align="right" type="text" style="width:90%" value="'+accreditation_nr+'" onKeyUp="if (this.value.length >= 1) enableButtonAdd(\''+id+'\'); else disableButtonAdd(\''+id+'\');" style="text-align:right"/></td>'+
									'<td width="25%" align="center"><input id="expiration'+id+'" align="right" type="date" min="'+dateFormat+'" style="width:90%" value="'+date+'" style="text-align:right"/></td>'+
									'<td width="2">'+
										'<input type="button" name="add_insurance'+id+'" id="add_insurance'+id+'" '+disable+' value=">" style="color:#000066; font-weight:bold; padding:0px 2px; cursor:pointer" '+
											'onclick="prepareAdd(\''+id+'\')" '+
                                        '/>'+
									
									'</td>'+
								'</tr>';
								//'<td width="25%" align="center"><input id="nr'+id+'" align="right" type="text" style="width:90%" value="" style="text-align:right" onblur="this.value = isNaN(parseFloat(this.value))?\'\':parseFloat(this.value)"/></td>'+
		} 
		else {
			rowSrc = '<tr><td colspan="10" style="">No such insurance firm exists...</td></tr>';
		}
		dBody.innerHTML += rowSrc;
	}
}
