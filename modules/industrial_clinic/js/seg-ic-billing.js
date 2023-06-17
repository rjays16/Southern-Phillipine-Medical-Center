//added code by angelo m.
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
	list = $("sname");

	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null;
			var data=[];
			var dRows = dBody.getElementsByTagName("tr");
			var dRows = dBody.getElementsByTagName("tr"),
			pid= details["pid"],
			name_last=details["name_last"],
			name_first=details["name_first"],
			name_middle=details["name_middle"],
			full_name=details["full_name"];
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1;
				inputbtn ='<input type="button" id="btn'+pid+'" onclick="this.disbled=true;prepareSelect('+
				'\''+pid+'\', '+
				'\''+full_name+'\' '+
				')" value=">" />';
				src =
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td align="left">'+name_last+'</td>'+
					'<td align="left">'+name_first+'</td>'+
					'<td align="left">'+name_middle+'</td>'+
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

// Added by James 3/13/2014
function selectAllEmployee(checkbox) {
	var obj  = ($('selectall').value = (checkbox.checked) ? "1" : "");

	if(obj == 1)
		$J('.checkall').attr('checked','checked');
	else
		$J('.checkall').attr('checked','');

}