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
				inputbtn ='<input type="button" id="btn'+c_id+'" onclick="this.disbled=true;prepareSelect('+
				'\''+c_id+'\', '+
				'\''+c_name+'\', '+
				'\''+c_eid+'\', '+
				'\''+c_pos+'\', '+
				'\''+c_jobStatus+'\' '+
				')" value=">" />';
				src =
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td align="left" style="color:#660000">'+c_name+'</td>'+
					'<td align="left">'+c_addr+'</td>'+
					'<td class="centerAlign">'+inputbtn+'</td>'+
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