/* added by art 05/11/2014 */

var currentPage=0, lastPage=0;
var FIRST_PAGE=1, PREV_PAGE=2, NEXT_PAGE=3, LAST_PAGE=4, SET_PAGE=0;
var data=[];

function formatNumber(num,dec) {
	var nf = new NumberFormat(num);
	if (isNaN(dec)) dec = nf.NO_ROUNDING;
	nf.setPlaces(dec);
	return nf.toFormatted();
}

function display(str) {
	document.write(str);
}
function prepareSelect(id) {
	var pid = $('id'+id).value;
	var name = $('fullname'+id).value;
	var addr = $('address'+id).value;

	if (var_pid)
		window.parent.$(var_pid).value = /*(noprefix==1 ? '' : 'IC')+*/pid;
	if (var_name) {
		window.parent.$(var_name).value = name;
		window.parent.$(var_name).readOnly = true;
	}
	if (var_addr) {
		window.parent.$(var_addr).value = addr;
		window.parent.$(var_addr).readOnly = true;
	}
	if (var_clear)
		window.parent.$(var_clear).disabled=false;

	if (window.parent.pSearchClose) window.parent.pSearchClose();
	else if (window.parent.cClick) window.parent.cClick();
}
function addItem(details) {
	list = $("company");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null;
			var data=[];
			var dRows = dBody.getElementsByTagName("tr");
			var dRows = dBody.getElementsByTagName("tr"),
			c_id= details["company_id"],
			c_name=details["company_name"],
			c_addr=details["company_address"],
			c_eid=details["employee_id"],
			c_pos=details["position"],
			c_jobStatus=details["job_status"];
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1;
				inputbtn ='<input type="button" id="btn'+c_id+'" onclick="prepareSelect(\''+c_id+'\')" value="Select" />';
				src =
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td align="left" style="color:#660000">'+c_id+'</td>'+
					'<td align="left" style="color:#660000">'+c_name+'</td>'+
					'<td align="left">'+c_addr+'</td>'+
					'<td class="centerAlign">'+inputbtn+'</td>'+
					'<input type="hidden" id="id'+c_id+'" value="'+c_id+'">'+
					'<input type="hidden" id="fullname'+c_id+'" value="'+c_name+'">'+
					'<input type="hidden" id="address'+c_id+'" value="'+c_addr+'">'+
				'</tr>';
			}
			else{
				src = "<tr><td colspan=\"5\">List is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}