<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');

require($root_path."modules/laboratory/ajax/lab-new.common.php");
$xajax->printJavascript($root_path.'classes/xajax');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
# Default value for the maximum nr of rows per block displayed, define this to the value you wish
# In normal cases this value is derived from the db table "care_config_global" using the "pagin_insurance_list_max_block_rows" element.
define('MAX_BLOCK_ROWS',30);

$lang_tables[]='search.php';
$lang_tables[]='actions.php';
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
#$local_user='ck_lab_user';
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$toggle=0;

$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;
$breakfile="labor.php$append";
$entry_block_bgcolor="#efefef";
$entry_border_bgcolor="#fcfcfc";
$entry_body_bgcolor="#ffffff";

$breakfile=$root_path.'modules/laboratory/'.$breakfile;
$thisfile=basename(__FILE__);
# Data to append to url
$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

//require($root_path.'modules/radiology/ajax/radio-undone-request.common.php');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 include_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
 $srvObj = new SegLab;

 $done = $_GET['done'];
 if ($_GET['done'])
		 $done_caption = "(DONE)";
 else
		 $done_caption = "(UNDONE)";

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$LDLab :: Requests Status List $done_caption");

 # href for help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDLab :: Requests Status List $done_caption");

# Body onload javascript code
#$smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');
#$smarty->assign('sOnLoadJs','onLoad="ShortcutKeys();"');
$smarty->assign('sOnLoadJs','onLoad="preSet(); ShortcutKeys(); DisabledSearch();"');
ob_start();

echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
//echo "<script type=\"text/javascript\" src=\"".$root_path."modules/laboratory/js/lab-undone-request-gui.js\"></script>";
?>

<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
		dojo.require("dojo.widget.TabContainer");
		dojo.require("dojo.widget.LinkPane");
		dojo.require("dojo.widget.ContentPane");
		dojo.require("dojo.widget.LayoutContainer");
		dojo.require("dojo.event.*");
</script>
<style type="text/css">
		body{font-family : sans-serif;}
		dojoTabPaneWrapper{ padding : 10px 10px 10px;}
</style>

<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script language="javascript" >
<!--

function preSet(){
		document.getElementById('searchkey').focus();
}

function eventOnClick(){
//    dojo.event.connect(dojo.widget.byId('demo').tablist, "onSelectChild","handleOnclick");
		dojo.event.connect(dojo.widget.byId('tbContainer').tablist, "onButtonClick","handleOnclick");

}

/*
		This will trim the string i.e. no whitespaces in the
		beginning and end of a string AND only a single
		whitespace appears in between tokens/words
		input: object
		output: object (string) value is trimmed
*/
function trimStringSearchMask(){
		$('searchkey').value = $('searchkey').value.replace(/^\s+|\s+$/g,"");
		$('searchkey').value = $('searchkey').value.replace(/\s+/g," ");
}/* end of function trimString */

function checkEnter(e){
		var characterCode; //literal character code will be stored in this variable

		if(e && e.which){ //if which property of event object is supported (NN4)
				e = e;
				characterCode = e.which; //character code is contained in NN4's which property
		}else{
				//e = event;
				characterCode = e.keyCode; //character code is contained in IE's keyCode property
		}

		if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
				$('skey').value=$('searchkey').value;
				chkSearch();
		}else{
				return true;
		}
}

function ShortcutKeys(){
				shortcut.add('Ctrl+Shift+N', NewRequest,
																{
																		'type':'keydown',
																		'propagate':false,
																}
												);

				 shortcut.add('Ctrl+Shift+L', RequestList,
														{
																'type':'keydown',
																'propagate':false,
														}
												 )

				 shortcut.add('Ctrl+Shift+S', SearchItem,
														{
																'type':'keydown',
																'propagate':false,
														}
												 )

				shortcut.add('Ctrl+Shift+M', BackMainMenu,
														{
																'type':'keydown',
																'propagate':false,
														}
												 )
		}

		function BackMainMenu(){
				urlholder="labor.php<?=URL_APPEND?>";
				window.location.href=urlholder;
		}

		function NewRequest(){
				//urlholder="seg-lab-request-new.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabnew&user_origin=lab";
				window.location.href=urlholder;
		}

		function RequestList(){
				//urlholder="seg-lab-request-order-list.php<?=URL_APPEND?>&user_origin=<?=$user_origin?>";
				urlholder="labor_test_request_pass.php?sid=<?=$sid?>&lang=<?=$lang?>&target=seglabservrequest_new&user_origin=lab";
				window.location.href=urlholder;
		}

		function SearchItem(){
				chkSearch('searchkey',0,0);
		}

function jsNoFoundRequest(group_code){
		var dTable,dTBody,rowSrc;

		if (dTable=document.getElementById('Ttab'+group_code)) {
				dTBody=dTable.getElementsByTagName("tbody")[0];
				rowSrc = '<tr><td colspan="14" align="center" bgcolor="#FFFFFF" style="color:#FF0000; font-family:"Arial", Courier, mono; font-style:Bold; font-weight:bold; font-size:12px;">NO MATCHING PENDING REQUEST FOUND</td></tr>';
				dTBody.innerHTML += rowSrc;
		}
}

function clearList(listID) {
		// Search for the source row table element
		var list=$(listID),dRows, dBody;
		if (list) {
				dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						dBody.innerHTML = "";
						return true;    // success
				}
				else return false;    // fail
		}
		else return false;    // fail
}

//added by VAN 08-21-08
function ToBeServed(obj, refno, service_code){
		//alert('served = '+obj.id+" ref = "+refno+" code = "+service_code);
		var is_served;
		is_served = 1;
		var answer = confirm("Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");
		if (answer)
				xajax_savedServedPatient(refno, service_code,is_served);
}

function SentOut(refno,group_id,service_code){
		//alert('refno = '+refno);
		//alert('code = '+code);
		var page = $('pgx').value;
		var mod = $('smode').value;
		var key = $('searchkey').value;

		//alert(page+" , "+mod+" , "+key);
		var reason;
		var answer = confirm("Send the request out to the hospital? It can't be undone. \n Click OK if YES, otherwise CANCEL.");
		if (answer){
				reason = prompt('Reason for sending the request out :');
				if (reason)
					xajax_savedSentOutRequest(refno, group_id,service_code,reason,key,page,mod);
				handleOnclick();
		}
}

//added by VAN 01-09-10
function serveRequest(refno,group_id,service_code){
		var page = $('pgx').value;
		var mod = $('smode').value;
		var key = $('searchkey').value;

		var answer = confirm("This test has no result. Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");
		if (answer){
				xajax_servedRequest(refno, group_id,service_code,key,page,mod,1);
				handleOnclick();
		}
}

function serveRequest_Manual(refno,service_code){
        var page = $('pgx').value;
        var mod = $('smode').value;
        var key = $('searchkey').value;

        //var answer = confirm("This test has no result. Are you sure that the request is already done? It can't be undone. \n Click OK if YES, otherwise CANCEL.");
        /*if (answer){
                xajax_servedRequest(refno, group_id,service_code,key,page,mod,1);
                handleOnclick();
        }*/
        alert('The result of this request is manually generated. It is excluded in the LIS. \n Please ask the result to the Laboratory. It is not yet in the HIS...');
}

function viewLabResult(refno,service_code){
	//window.open("../../modules/laboratory/seg-lab-request-result-summary-pdf.php?refno="+refno+"&service_code="+service_code+"&status=1&showBrowser=1","viewLaboratoryResult","width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes");
	//window.open("seg-lab-result-pdf.php?refno="+refno+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
    // link to view the HL7 message result - pdf format
    //temp, just for testing
    //var refno='2012000021';
    window.open("seg-lab-result-pdf-link.php?refno="+refno+"&showBrowser=1","viewPatientResult","left=150, top=100, width=950,height=700,fullscreen=yes,menubar=no,resizable=yes,scrollbars=yes");
}

//edited by VAN 01-09-10, add the with_result parameter for lab test, this field is taken from seg_lab_services, it is different 'with_res' of raiss
function jsRequest(sub_dept_nr,No,refno, name, req_date, urgency, or_no, service, code, repeat, 
                   pid,age,sex,location,enctype, with_res, group_id, with_result,isrepeat,in_lis, lis_order_no){
                       
		var dTable,dTBody,dRows,rowSrc,sid,lang,radio_findings_link;
		var patType;
		var i, mode, editlink, ornum, priority, reqstatus,ornum,sex_img,repeat_status;

		if (dTable=document.getElementById('Ttab'+sub_dept_nr)) {

				dTBody=dTable.getElementsByTagName("tbody")[0];
				//dRows=dBody.getElementsByTagName("tr");

				//alert(refno);

				if (refno) {
					if (urgency=='Urgent')
							priority = '<font color="#FF0000">'+urgency+'</font>';
					else
							priority = urgency;
					if (status==1)
							reqstatus = '<font color="#000066">Done</font>';
					else if (status==0)
							reqstatus = '<font color="#FF0000">Pending</font>';

					if (or_no=="")
							ornum = '<font color="#FF0000">CHARGE</font>';
					else
							ornum = '<font color="#000066">'+or_no+'</font>';

					//editlink ='onclick="return overlib(OLiframeContent(\'seg-lab-request-result.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&paid=0&service_code='+code+'&ref='+refno+'\', 400, 200, \'flab-list\', 1, \'auto\'), ' +
//										'WIDTH, 400, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow();>\', '+
//										'CAPTIONPADDING, 4, CAPTION, \'Laboratory Request\', MIDX, 0, MIDY, 0, STATUS, \'Laboratory Request\');">';
					//alert(in_lis);
					if (sex=='m')
						sex_img = '<img src="../../gui/img/common/default/spm.gif" align="absmiddle" border="0"/>';
					else if (sex=='f')
						sex_img = '<img src="../../gui/img/common/default/spf.gif" align="absmiddle" border="0"/>';

					var rowid = refno+""+code;
					var done = $('done').value;
					//alert('r, g = '+with_result+" - "+group_id);

					if (done==1){
						serve ='onclick="return overlib(OLiframeContent(\'lab_results.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&status=edit&refno='+refno+'&pid='+pid+'&service_code='+code+'&group_id='+group_id+'&done=0\', 850, 450, \'flab-list\', 0, \'auto\'), ' +
								'WIDTH, 840, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
								'CAPTIONPADDING, 2, DRAGGABLE, CAPTION, \'Process Request\', MIDX, 0, MIDY, 0, STATUS, \'Process Request\');">';
						 donerow='';

						 //edited by VAN 01-09-10
						 /*if ((with_result==1)||(group_id!=0)){
							 if (in_lis==0){
								 labresult ='onclick="return overlib(OLiframeContent(\'lab_results.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&status=add&refno='+refno+'&pid='+pid+'&service_code='+code+'&group_id='+group_id+'&done=1\', 850, 450, \'flab-list\', 0, \'auto\'), ' +
										'WIDTH, 840, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
										'CAPTIONPADDING, 2, DRAGGABLE, CAPTION, \'Laboratory Results\', MIDX, 0, MIDY, 0, STATUS, \'Laboratory Results\');"';
							 }else{
								 //labresult = 'onClick="viewLabResult(\''+refno+'\',\''+code+'\')"';
								 labresult = 'onClick="viewLabResult(\''+refno+'\')"';
							 }
						 }else{
									labresult ='onclick="alert(\'No result available. Pls ask the attending doctor for the result.\')"';
						 }
						 labresult_row = '<td align="center"><a href="javascript:void(0);" '+labresult+'><img src="../../images/cashier_edit.gif" border="0"></a></td>';*/
                         if (with_res==0){
                           icon_result = '<img src="../../images/cashier_view_red.gif" border="0" title="No Result fetch from the LIS yet . \nOr the result is manually generated. \nPlease ask the Laboratory for the result.">';
                        }else{
                           icon_result = '<a href="javascript:void(0);" onClick=viewResult(\''+refno+'\',\''+pid+'\',\''+lis_order_no+'\');><img src="../../images/cashier_view.gif" border="0" title="Click for the Result"></a>';
                        }
                        
                        labresult_row = '<td align="center">'+icon_result+'</td>';
					}
					else{

						 serve ='onclick="return overlib(OLiframeContent(\'seg-lab-processform.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&pid='+pid+'&service_code='+code+'&refno='+refno+'\', 830, 430, \'flab-list\', 1, \'auto\'), ' +
								'WIDTH, 840, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow(key,pagekey);>\', '+
								'CAPTIONPADDING, 4, CAPTION, \'Process Request\', MIDX, 0, MIDY, 0, STATUS, \'Process Request\');">';
						 donerow = '<td align="center"><img src="../../images/cashier_check.png" border="0" onClick="SentOut('+refno+','+group_id+',\''+code+'\');"></td>';
						 searchkey = "searchkey";

						 //edited by VAN 01-09-10
						 if (in_lis==1){
								//labresult ='onclick="return overlib(OLiframeContent(\'seg-lab-request-result.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&paid=0&service_code='+code+'&ref='+refno+'\', 400, 200, \'flab-list\', 1, \'auto\'), ' +
//										'WIDTH, 400, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=refreshWindow();>\', '+
//										'CAPTIONPADDING, 4, CAPTION, \'Laboratory Request\', MIDX, 0, MIDY, 0, STATUS, \'Laboratory Request\');">';
								labresult ='onclick="alert(\'Please wait that LIS will process the result. This test will be done in LIS automation and not in SegHIS. Thank you.\')"';
						 }else{
							 if ((with_result==1)||(group_id!=0)){
							 labresult ='onclick="return overlib(OLiframeContent(\'lab_results.php?sid=<?php echo "$sid&lang=$lang"?>&clear_ck_sid=<?php echo "$clear_ck_sid"?>&popUp='+1+'&mode=update&update=1&status=add&refno='+refno+'&pid='+pid+'&service_code='+code+'&group_id='+group_id+'&done=0\', 850, 450, \'flab-list\', 0, \'auto\'), ' +
										'WIDTH, 840, TEXTPADDING, 0, BORDER, 0, STICKY, SCROLL, CLOSECLICK, MODAL, CLOSETEXT, \'<img src=../../images/close.gif border=0 onClick=chkSearch(searchkey,0,0);>\', '+
									'CAPTIONPADDING, 2, DRAGGABLE, CAPTION, \'Laboratory Results\', MIDX, 0, MIDY, 0, STATUS, \'Laboratory Results\');"';
							 }else{
									//labresult ='onclick="serveRequest('+refno+','+group_id+',\''+code+'\')"';
                                    labresult ='onclick="serveRequest_Manual('+refno+',\''+code+'\')"';
							 }
						 }
						 labresult_row = '<td align="center"><a href="javascript:void(0);" '+labresult+'><img src="../../images/cashier_edit.gif" border="0"></a></td>';

					}

					if (isrepeat==1)
						repeat_status = '<font color="#000066">YES</font>';
					else
						repeat_status = '<font color="#FF0000">NO</font>';
                                

					/*if(with_res==1 && done!=1) {
                        rowSrc = '<tr class="wardlistrow" id="row'+refno+'">'+
											'<td align="center" style="font-size:11px;color:#ca26a5">'+refno+'</td>'+
											'<td align="left" style="font-size:11px;color:#ca26a5">'+name+'</td>'+
											'<td align="left" style="font-size:11px;color:#ca26a5">'+pid+'</td>'+
											'<td align="right" style="font-size:11px;color:#ca26a5">'+age+'</td>'+
											'<td align="left">'+sex_img+'</td>'+
											'<td align="left" style="font-size:11px;color:#ca26a5">'+enctype+'</td>'+
											'<td align="left" style="font-size:11px;color:#ca26a5">'+location+'</td>'+
											'<td align="left" style="font-size:11px;color:#ca26a5">'+service+'</td>'+
											'<td align="center" style="font-size:11px;color:#ca26a5">'+req_date+'</td>'+
											'<td align="center" style="font-size:11px;color:#007">'+priority+'</td>'+
											'<td align="center" style="font-size:11px">'+ornum+'</td>'+
											'<td align="center" style="font-size:11px">'+repeat_status+'</td>'+
											''+labresult_row+''+
								 '</tr>';
					}
					else{
                        rowSrc = '<tr class="wardlistrow" id="row'+refno+'">'+
												'<td align="center" style="font-size:11px">'+refno+'</td>'+
												'<td align="left" style="font-size:11px;">'+name+'</td>'+
												'<td align="left" style="font-size:11px">'+pid+'</td>'+
												'<td align="right" style="font-size:11px">'+age+'</td>'+
												'<td align="left">'+sex_img+'</td>'+
												'<td align="left" style="font-size:11px">'+enctype+'</td>'+
												'<td align="left" style="font-size:11px">'+location+'</td>'+
												'<td align="left" style="font-size:11px">'+service+'</td>'+
												'<td align="center" style="font-size:11px">'+req_date+'</td>'+
												'<td align="center" style="font-size:11px;color:#007">'+priority+'</td>'+
												'<td align="center" style="font-size:11px">'+ornum+'</td>'+
												'<td align="center" style="font-size:11px">'+repeat_status+'</td>'+
												''+labresult_row+''+
									 '</tr>';  
					}*/
                    rowSrc = '<tr class="wardlistrow" id="row'+refno+'">'+
                                '<td align="center" style="font-size:11px">'+refno+'</td>'+
                                '<td align="left" style="font-size:11px;">'+name+'</td>'+
                                '<td align="left" style="font-size:11px">'+pid+'</td>'+
                                '<td align="right" style="font-size:11px">'+age+'</td>'+
                                '<td align="left">'+sex_img+'</td>'+
                                '<td align="left" style="font-size:11px">'+enctype+'</td>'+
                                '<td align="left" style="font-size:11px">'+location+'</td>'+
                                '<td align="left" style="font-size:11px">'+service+'</td>'+
                                '<td align="center" style="font-size:11px">'+req_date+'</td>'+
                                '<td align="center" style="font-size:11px;color:#007">'+priority+'</td>'+
                                '<td align="center" style="font-size:11px">'+ornum+'</td>'+
                                '<td align="center" style="font-size:11px">'+repeat_status+'</td>'+
                                ''+labresult_row+''+
                                '</tr>';
			}else{
					rowSrc = '<tr><td colspan="14" style="">No such record exists...</td></tr>';
			}
			dTBody.innerHTML += rowSrc;
		}
}

//added by VAN 02-11-2013
function viewResult(refno, pid, lis_order_no){
     //window.open("seg-lab-result_pdf-view.php?refno="+refno+"&pid="+pid+"&lis="+lis_order_no+"&showBrowser=1","viewPatientResult","left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
    permission = 1; // temporary open to all
    if (permission != 1) {
        alert("You have no permission to access this feature.");
        return;
    }

     var urlholder = "seg-lab-report-hl7.php?pid=" + pid + "&lis_order_no=" + lis_order_no + "&showBrowser=1";
     window.open(urlholder, "viewPatientResult", "left=150, top=100, width=620,height=440,menubar=no,resizable=yes,scrollbars=yes");
}

function jsSortHandler(items,oitem,dir,sub_dept_nr){
		var tab;
		var key, pgx, thisfile, rpath, sub_dept_nr, mode;
		var done = $F('done');
		var isERIP = $('isERIP').value;

		 try{
				tab = dojo.widget.byId('tbContainer').selectedChild;
		}catch(e){
				//alert("e.message = "+e.message);
				tab = 'tab0';   // use in initial loading
		}
		 mode = document.getElementById('smode').value;
		 rpath = document.getElementById('rpath').value;
		//setPgx(0);   // resets to the first page every time a tab is clicked
		 pgx = document.getElementById('pgx').value;
		key = document.getElementById('searchkey').value;
		 thisfile = document.getElementById('thisfile').value;
		 oitem = 'name_last';
		odir = 'ASC';
		sub_dept_nr = tab.substr(3);
		//clear List muna
		xajax_PopulateUndoneRequests('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir,done,isERIP);
}//end of function jsSortHandler

function chkSearch(searchID, page, mod){
				//alert("chkSearch");
				$('skey').value=$F('searchkey');
				handleOnclick();
}

function handleOnclick(){
//     var tab = dojo.widget.byId('tbContainer').selectedChild;
		var tab;
		var key, pgx, thisfile, rpath, sub_dept_nr, mode;
		var done = $F('done');
		var isERIP = $('isERIP').value;

		 try{
				tab = dojo.widget.byId('tbContainer').selectedChild;
		}catch(e){
				//alert("e.message = "+e.message);
				tab = 'tab0';   // use in initial loading
		}
		mode = document.getElementById('smode').value;
		rpath = document.getElementById('rpath').value;
		setPgx(0);   // resets to the first page every time a tab is clicked
		pgx = document.getElementById('pgx').value;
		key = document.getElementById('searchkey').value;
		thisfile = document.getElementById('thisfile').value;
		oitem = 'name_last';
		odir = 'ASC';
		sub_dept_nr = tab.substr(3);
		clearList('T'+tab);
		xajax_PopulateUndoneRequests('T'+tab, key, sub_dept_nr, pgx, thisfile, rpath, mode,oitem,odir,done,isERIP);
 }

function setTotalCount(val){
		$('totalcount').value=val;
}

function setPgx(val){
		$('pgx').value=val;
}

function setOItem(val){
		$('oitem').value=val;
}

function setODir(val){
		$('odir').value=val;
}

//added by VAN 01-29-10
function isValidSearch(key) {

		if (typeof(key)=='undefined') return false;
		var s=key.toUpperCase();
		return (
						/^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
						/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
						/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
						/^\d+$/.test(s)
		);
}

function DisabledSearch(){
		var b=isValidSearch(document.getElementById('searchkey').value);
		document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
		document.getElementById("search-btn").disabled = !b;
}


//------------------------------------------
// -->
</script>

<!--  Dojo script function for undone request -->
<script language="javascript">
 dojo.addOnLoad(eventOnClick);
</script>

<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$tmp1 = ob_get_contents();
ob_end_clean();
$smarty->assign('yhScript', $tmp1);

#get client IP address and check if unit in ER LAB = seg_lab_er_ip
$isERIP = $srvObj->isIPinERLab($_SERVER['REMOTE_ADDR']);

# Collect extra javascript code
ob_start();

?>
<!--commented by VAN 06-28-08 -->
<!--<ul>-->
		<?php if ($isERIP){	?>
					<span><em><font color='RED'><strong>&nbsp;<?=$LDERLabCaption;?></strong></font></em></span>
		<?php } ?>
		<table width=100% border=0 cellpadding="0" cellspacing="0">
				<tr bgcolor="#ffffff" >
						<td align="center">
								<p><br>
								<ul>
										<table width="600" border=0 cellpadding=0>
												<tr>
														<td align="center">
																<table border=0 cellspacing=5 cellpadding=5 width="105%">
																		<tr bgcolor="#ffffff">
																				<td align="center">
																				<span style="font-family:Arial, Helvetica, sans-serif; font-size:13px">
																								Enter the search key (HRN, Case No., Batch No., Family Name or Request Date).<br> Enter dates in <font color="#0000FF"><b>MM/DD/YYYY</b></font> format.
																Enter asterisk (<b>*</b>) to show all data.
																						</span>
																						<p>
																				<form name="searchform" onSubmit="return false;">
																						<!--<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask();" onKeyUp="if (this.value.length >= 3){chkSearch();}" value="">-->
																						<!--<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask();" onKeyPress="checkEnter(event)" onChange="trimStringSearchMask();" onKeyUp="if (this.value.length >= 3){chkSearch('search',0,0);}" value="">&nbsp; -->
																						<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onBlur="DisabledSearch();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('searchkey').value))) chkSearch('searchkey',0,0); " value="">&nbsp;
																						<!---<input type="image" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="chkSearch();">--->
																						<input type="image" name="search-btn" id="search-btn" src="<?= $root_path ?>images/his_searchbtn.gif" onClick="chkSearch('searchkey',0,0);" align="absmiddle" /><br /><b>
																						<span><a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></b>
																				</form>
																				</td>
																		</tr>
																</table>
														</td>
												</tr>
										</table>
										<p>
										<span id='textResult'></span>

												<!--  Test for dojo tab event  -->
										<div id="tbContainer" dojoType="TabContainer" style="width:auto; height:30.5em; ">
												<div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none; overflow:auto;" >
														<table id="Ttab0" cellpadding="0" cellspacing="0" class="segList">
																<!-- List of ALL Pending Requests  -->
														</table>
												</div>
<?php
												#Department object
												$radio_sub_dept=$srvObj->getLabServiceGroups('name','AND group_code NOT IN (\'B\',\'SPL\',\'IC\')');
												#echo $srvObj->sql;

												if ($srvObj->count){
														$dept_counter=2;
														while ($rowSubDept = $radio_sub_dept->FetchRow()){
																if (trim($rowSubDept['name'])!=''){
																		$text_name = trim($rowSubDept['name']);
																}elseif (trim($rowSubDept['group_code'])!=''){
																		$text_name = trim($rowSubDept['group_code']);
																}else{
																		#$text_name = trim($rowSubDept['name_formal']);
																		$text_name = trim($rowSubDept['name']);
																}
?>                                              <!--
												<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['group_code']?>" label="<?=$text_name?>" style="display:none; overflow:auto" >
														<table id="Ttab<?=$rowSubDept['group_code']?>" cellpadding="0" cellspacing="0" class="segList">
														</table>
												</div>  -->
<?php
																$dept_counter++;
														} # end of while loop
												}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
										</div>
								</ul>
								<p>
						</td>
				</tr>
		</table>
<!--
		<input type="hidden" name="skey" id="skey" value="<?= $HTTP_SESSION_VARS['sess_searchkey']? $HTTP_SESSION_VARS['sess_searchkey']:'*'?>">
-->
		<!--<input type="hidden" name="skey" id="skey" value="*"> -->
		<input type="hidden" name="skey" id="skey" value="">
		<input type="hidden" name="smode" id="smode" value="<?= $mode? $mode:'searchkey' ?>">
		<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
		<input type="hidden" name="done" id="done" value="<?php echo $done; ?>">
		<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
		<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
		<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
		<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'name_last' ?>">
		<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
		<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">
		<input type="hidden" name="sid" id="sid" value="<?php echo $sid; ?>">
		<input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
		<input type="hidden" name="noresize" id="noresize" value="<?php echo $noresize; ?>">
		<input type="hidden" name="target"  id="target" value="<?php echo $target; ?>">
		<input type="hidden" name="user_origin" id="user_origin" value="<?php echo $user_origin; ?>">
		<input type="hidden" name="mode" id="mode" value="searchkey">
		<input type="hidden" name="isERIP" id="isERIP" value="<?=$isERIP?>">
	<!--
		<table>
				<tr align="center" style="width:auto">
						<td>
								<?php
										$requestFileForward = $root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=lab_test&user_origin=lab";
										echo '<a href="'.$requestFileForward.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Service Request"></a>';
								?>
						</td>
				<tr>
		</table>
		-->
</ul>
<p>
<script>
    var l = window.location,
        baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1] +'/';
    if(window.parent.location['href'] === baseUrl){
        // Do nothing if the active window location is the index..
    }else{
        localStorage.notifToken = "<?= $_SESSION['token'] ?>";
        localStorage.notifSocketHost = "<?= $notification_socket ?>";
        localStorage.username = "<?= $_SESSION['sess_login_userid'] ?>";
        $j('<iframe />');  // Create an iframe element
        $j('<iframe />', {
            id: 'notifcontIf',
            src: '../../socket.html',
        }).appendTo('body');
        $j("iframe#notifcontIf").css("border-style","none");
        $j("iframe#notifcontIf").css("height", "0px");
    }
</script>
<script language="javascript">
		document.getElementById('skey').value = 'null';
		handleOnclick();
</script>
<?php
$sTemp = ob_get_contents();
ob_end_clean();

# Assign to page template object
$smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
// require($root_path.'js/floatscroll.js');
?>
