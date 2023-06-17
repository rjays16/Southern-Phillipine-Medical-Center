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
	list = $("transaction");
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			var lastRowNum = null;
			var data=[];
			var dRows = dBody.getElementsByTagName("tr");
			var dRows = dBody.getElementsByTagName("tr"),
			patient_id= details["patient_id"],
			case_no= details["case_no"],
			full_name= details["full_name"];
			refno=details["refno"];
			if (details["FLAG"]=="1") {
				alt = (dRows.length%2)+1;

				inputbtn =
									'<a href="./seg-ic-transaction-form.php?ntid=false&lang=en&from=such&pid='+patient_id+'&refno='+refno+'&origin=patreg_reg&target=adminoverride_show&mode=entry" >'+
									'<img src="../../gui/img/common/default/pdata.gif" />'  +
									'</a>';
				src =
				'<tr'+((dRows.length%2>0)?' class="alt"':'')+'>' +
					'<td align="left">'+case_no+'</td>'+
					'<td align="left" style="color:#660000">'+patient_id+'</td>'+
					'<td align="left">'+full_name+'</td>'+
					'<td class="centerAlign">'+inputbtn+'</td>'+
					'</tr>';
			}
			else {
				src = "<tr><td colspan=\"5\">List is currently empty...</td></tr>";
			}
			dBody.innerHTML += src;
			return true;
		}
	}
	return false;
}