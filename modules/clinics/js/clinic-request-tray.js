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

function createTableHeader(divId, tableId, refno, type, user_id, ref_source)
{

	var div = $(divId);
	
	if(tableId.indexOf('misc') !== -1){
		var create_user = "Created by: " + user_id;
	}
	else{
		create_user = '';
	}

	var tableSrc =
			'<div align="right" style="width:100%;margin-bottom:3px">'+
				'<table width="100%" cellpadding="0" cellspacing="0">'+
					'<tr>'+
						'<td align="right" id="btn-'+refno+'"></td>'+
					'</tr>'+
				'</table>'+
			'</div>'+
			'<table width="100%" class="segList" id="'+tableId+'" cellpadding="0" cellspacing="0">'+
				'<thead>'+
					'<tr class="calnav">'+
						'<th colspan="2" style="text-align: left;">Reference no. '+refno+' ('+type+')</th>'+
						'<th colspan="7" style="text-align: right;">'+create_user+'</th>'+
					'</tr>'+
				'</thead>'+
				'<thead>'+
					'<tr>'+
						'<th align="center">Date</th>'+
						'<th align="center">Status</th>'+
						'<th align="center">Item Description</th>'+
						((divId == 'splab_requests') ? '<th align="center">Served</th>' : '' )+
						'<th align="center">Quantity</th>'+
						'<th align="center">Unit Price</th>'+
						'<th align="center">Total</th>'+
						'<th width="18"></th>'+
					'</tr>'+
				'</thead>'+
				'<tbody style="max-height:100px; overflow-y:auto; overflow-x:hidden">'+
				'</tbody>'+
			'</table><br/>';
	div.insert(tableSrc);
}

function printRequestlist(divId, tableId, details, ref_source)
{
	var div = $(divId);
	var table = $(tableId);
	var dBody = table.select("tbody")[0];
	/*var table1 = $(table).getElementsByTagName('tbody').item(0);
	if ($('empty_misc_row')) {
		table1.removeChild($('empty_misc_row'));
	}*/
	var dRows = dBody.getElementsByTagName("tr")

	var checkImg = '<img src="../../images/cashier_ok.gif" border="0"/>';
	var closeImg = '<img src="../../images/close_small.gif" border="0"/>';
	var serveCol = details.is_served == '1' ? checkImg : closeImg;
	if(details){
		alt = (dRows.length%2>0) ? ' class="alt"':''
		rowSrc =
			'<tr class="'+alt+'" id="ip_row'+details.refno+details.item_code+'">'+
				'<td class="centerAlign" width="14%">'+(details.is_in_inventory == 1 ? details.DAIstatus :"")+' '+ details.order_date+'</td>'+
				'<td class="centerAlign" width="10%">'+details.status+'</td>'+
				'<td class="leftAlign" width="*">'+details.item_name+'</td>'+
				((divId == 'splab_requests') ? '<td class="centerAlign" width="8%">'+serveCol+'</td>' : '' )+
				'<td class="centerAlign" width="8%">'+details.item_qty+'</td>'+
				'<td class="rightAlign" width="8%">'+formatNumber(details.item_prc,2)+'</td>'+
				'<td class="rightAlign" width="8%">'+formatNumber(details.total_prc,2)+'</td>'+
				'<td></td>'+
			'</tr>';
		dBody.insert(rowSrc);
	}
}

function openEditRequest(id, refno,pid)
{
	switch(id)
	{
		case 'lab_requests': editLabRequest(refno); break;
		case 'splab_requests': editSpLabRequest(refno); break;
		case 'blood_requests': editBloodRequest(refno); break;
		case 'radio_requests': editRadioRequest(refno); break;
		case 'ip_requests': editPharmaRequest(refno); break;
		case 'mg_requests': editPharmaRequest(refno); break;
		case 'misc_requests': editMiscRequest(refno); break;
		case 'obgyne_requests': editOBGRequest(refno); break;
	}
}
function openServeRequest(id, refno){
	switch(id){
		case 'ip_requests': servePharmaRequest(refno); break;
	}
}

function openDeleteRequest(id, refno, warning)
{
	 switch(id)
	{
		case 'lab_requests': deleteLabRequest(refno,warning); break;
		case 'splab_requests': deleteSpLabRequest(refno,warning); break;
		case 'blood_requests': deleteBloodRequest(refno,warning); break;
		case 'radio_requests': deleteRadioRequest(refno,warning); break;
		case 'ip_requests': deletePharmaRequest(refno); break;
		case 'mg_requests': deletePharmaRequest(refno); break;
		case 'misc_requests': deleteMiscRequest(refno); break;
		case 'obgyne_requests': deleteOBGRequest(refno); break;	
	}
}

function editLabRequest(refno)
{
	var ipbmextend = $('ipbmextend').value; // added by carriane 10/24/17
	var pid=$('pid').value;

	// updated by carriane 10/24/17; added IPBMextend
	return overlib(OLiframeContent('../../modules/laboratory/seg-lab-request-new.php?popUp='+1+'&ref='+refno+'&pid='+pid+'&encounter_nr='+$('encounter_nr').value+'&user_origin=lab&ischecklist=1'+ipbmextend,
		800, 440, 'flab-list', 1, 'auto'),
	WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
	'<img src=../../images/close.gif border=0 onclick="refreshPage();">',
	CAPTIONPADDING, 4, CAPTION, 'Laboratory Request', MIDX, 0, MIDY, 0, STATUS, 'Laboratory Request');
}

function deleteLabRequest(refno, warning) {
	if (warning) {
		warning = 'Warning! ' + warning;
	}
	var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. " + (refno) + "?\n" + warning);
	if (answer){
			xajax_deleteRequest(refno);
	}
}

function editSpLabRequest(refno)
{
	var ipbmextend = $('ipbmextend').value; // added by carriane 03/16/18
	var pid=$('pid').value;

	return overlib(OLiframeContent('../../modules/special_lab/seg-splab-request-new.php?popUp=1&ref='+refno+'&pid='+pid+'&encounter_nr='+$('encounter_nr').value+'&user_origin=splab&ischecklist=1'+ipbmextend,
		800, 440, 'flab-list', 1, 'auto'),
	WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
	'<img src=../../images/close.gif border=0 onclick="refreshPage();">',
	CAPTIONPADDING, 4, CAPTION, 'Special Laboratory Request', MIDX, 0, MIDY, 0, STATUS, 'Special Laboratory Request');
}

function deleteSpLabRequest(refno, warning) {
	if (warning) {
		warning = 'Warning! ' + warning;
	}
	var answer = confirm("Are you sure you want to delete the laboratory request with a reference no. " + (refno) + "?\n" + warning);
	if (answer){
			xajax_deleteRequest(refno);
	}
}

function editBloodRequest(refno)
{
	var ipbmextend = $('ipbmextend').value; // added by carriane 03/16/18
	var pid=$('pid').value;
	return overlib(OLiframeContent('../../modules/bloodBank/seg-blood-request-new.php?popUp='+1+'&repeat=0&ref='+refno+'&pid='+pid+'&encounter_nr='+$('encounter_nr').value+'&user_origin=blood&ischecklist=1'+ipbmextend,
		800, 440, 'flab-list', 1, 'auto'),
	WIDTH, 750, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT,
	'<img src=../../images/close.gif border=0 onclick="refreshPage();">',
	CAPTIONPADDING, 4, CAPTION, 'Blood Request', MIDX, 0, MIDY, 0, STATUS, 'Blood Request');
}

function deleteBloodRequest(refno,warning) {
	if (warning) {
		warning = 'Warning! ' + warning;
	}
	var answer = confirm("Are you sure you want to delete the blood request with a reference no. " + (refno) + "?\n"+warning);
		if (answer){
			xajax_deleteRequest(refno);
		}
}

function editRadioRequest(refno) {
	var ipbmextend = $('ipbmextend').value; // added by carriane 03/16/18
    var pid=$('pid').value;
	return overlib(

		OLiframeContent('../../modules/radiology/seg-radio-request-new.php?popUp=1&ref='+refno+'&user_origin=radio&ischecklist=1&pid='+pid+ipbmextend+'&encounter_nr='+$('encounter_nr').value, //Updated by Christian 12-31-19

			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Radiology Request',
		MIDX,0, MIDY,0,
		STATUS,'Radiology Request');
}
function editOBGRequest(refno) {
    var pid=$('pid').value;
	return overlib(
		OLiframeContent('../../modules/radiology/seg-radio-request-new.php?popUp=1&ref='+refno+'&ischecklist=1&ob=OB&pid='+pid,
			800, 440, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'OB-GYN Ultrasound Request',
		MIDX,0, MIDY,0,
		STATUS,'OB-GYN Ultrasound Request');
}

function deleteRadioRequest(refno,warning){
	if (warning) {
		warning = 'Warning! ' + warning;
		var answer = confirm("You are about to delete service request #"+refno+". Are you sure?\n"+warning);
	}
	var answer = confirm("You are about to delete service request #"+refno+". Are you sure?\n");
	if (answer){
		xajax_deleteRadioServiceRequest(refno);
	}
}
function deleteOBGRequest(refno){
	var answer = confirm("You are about to delete service request #"+refno+". Are you sure?");
	if (answer){
		xajax_deleteRadioServiceRequest(refno);
	}
}

function editPharmaRequest(refno) {
	 return overlib(
		OLiframeContent('../../modules/pharmacy/seg-pharma-order.php?target=edit&from=CLOSE_WINDOW&ref='+refno,
			1100, 600, 'fGroupTray', 0, 'auto'),
		WIDTH,1100, TEXTPADDING,0, BORDER,0,MODALSCROLL,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Pharmacy Request',
		MIDX,0, MIDY,0,
		STATUS,'Pharmacy Request');
}

function servePharmaRequest(refno) {
	 return overlib(
		OLiframeContent('../../modules/pharmacy/seg-pharma-order.php?target=serve&from=CLOSE_WINDOW&ref='+refno,
			800, 400, 'fGroupTray', 0, 'auto'),
		WIDTH,750, TEXTPADDING,0, BORDER,0,MODALSCROLL,
		STICKY, SCROLL, CLOSECLICK, MODAL,
		CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
		CAPTIONPADDING,2, CAPTION,'Serve order',
		MIDX,0, MIDY,0,
		STATUS,'Serve order');
}

function deletePharmaRequest(refno)
{
	var answer = confirm("Delete this pharmacy order?");
	if(answer){
		xajax_deleteOrder(refno);
	}
}

function editMiscRequest(refno)
{
	return overlib(
	OLiframeContent('../../modules/dialysis/seg-misc-request-new.php?from=CLOSE_WINDOW'+'&pid='+$('pid').value+'&encounter_nr='+$('encounter_nr').value+'&mode=edit&area='+$('ptype').value+'&refno='+refno,
		700, 360, 'fGroupTray', 0, 'auto'),
	WIDTH,700, TEXTPADDING,0, BORDER,0,
	STICKY, SCROLL, CLOSECLICK, MODAL,
	CLOSETEXT, '<img src="../../images/close.gif" border=0 onclick="refreshPage();">',
	CAPTIONPADDING,2, CAPTION,'Miscellaneous Request',
	MIDX,0, MIDY,0,
	STATUS,'Miscellaneous Request');
}

function deleteMiscRequest(refno)
{
	var answer = confirm("Delete this miscellaneous request?");
	if(answer){
		xajax_deleteMiscRequest(refno);
	}
}
/*added By MARK 2016-11-03*/
 function viewTransDai(apiKey,pid){
                var viewTransDaiForm = "../../modules/pharmacy/seg-check-transaction-dai.php?SEGAPIKEY="+apiKey+"&hnumber="+pid;
                var dialogAUditNote = $J('<div id="dialogTrans"></div>')
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
							        
				                         	$J(this).dialog( "close" );
				                         
							
							        }
							      },
					 	dialogClass: 'my-dialog'
                    });
                    $J('.my-dialog .ui-button-text:contains(NewTab)').text('Open new tab');
    		}
  function openInNewTabDAItrans(url) {
  var win = window.open(url, '_blank');
  win.focus();
}
