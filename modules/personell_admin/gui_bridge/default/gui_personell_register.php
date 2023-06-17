<?php
# Prepare title

$sTitle="$LDPersonnelManagement :: $LDNewEmployee ";
if($full_nr) $sTitle=$sTitle.$full_pnr;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
 $smarty->assign('sToolbarTitle',$sTitle);

 # hide return button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('employee_how2new.php','$personell_nr','$pid')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',$sTitle);

# Colllect javascript code

ob_start();
?>

<!---------added by VAN----------->
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.9.js"></script>
<script>var $j = jQuery.noConflict();</script>
<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<!-- added by: syboy 09/24/2015 -->
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.9.js"></script>
<script>
// syboy here ..
	var $J = jQuery.noConflict();
	$J(function(){
		// alert($J);
		$J('#category').on("change", function(){
			// alert('wew');
			$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=subCategory",
				data: {
					category: $J('#category').val()
				},
				success: function(result){
		        	$J("#subcat").html(result);
		    	}
		    	// $J('#category').trigger("change");
			});
		});
		$J('#category').trigger("change");

		$J('#ris_id').change(function(){
			var risId = this.value;
			var thisObj = this;
			$J.ajax({
				url: "ajax/ajax-personnel.php",
				data: {
					risId: risId,
					request: "checkDuplicateRisId"
				},
				success: function(result){
					if(result){
		        		thisObj.setCustomValidity("Duplicate RIS ID");
		        		$J('#risWarning').show();
	        		}
		        	else{
		        		thisObj.setCustomValidity("");
		        		$J('#risWarning').hide();
		        	}
		    	}
			});
		});
	});
</script>
<!-- ended -->

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">

	/* added by Nick 4-4-2015 */
	#upload-file-container{
		display: inline-block;
	}

	#loading{
		float:right;
		position: relative;
		left: -48px;
		display: none;
	}

	#btn-browse-file,#btn-delete-file{
		position: relative;
		top: -18px;
		font-weight: bold;
		background-color: #606060;
		border: none;
		border-radius: 1px 1px 1px 1px;
	}

	#btn-browse-file:hover,#btn-delete-file:hover,
	#btn-browse-file:focus,#btn-delete-file:focus{
		color: #FFF;
	}

	#btn-browse-file{
		left: 160px;
	}

	#btn-delete-file{
		left: 155px;
	}

	#signature{
		border-radius: 2px 2px 2px 2px;
		border: solid 1px #AAA;
	}

	div[id^=req]{
		font-size: 8pt;
		margin-top: 2px;
		margin-top: 2px;
	}
	/* end Nick */

<!--
.olbg {
		background-image:url("<?= $root_path ?>images/bar_05.gif");
		background-color:#0000ff;
		border:1px solid #4d4d4d;
}
.olcg {
		background-color:#aa00aa;
		background-image:url("<?= $root_path ?>images/bar_05.gif");
		text-align:center;
}
.olcgif {background-color:#333399; text-align:center;}
.olfg {
		background-color:#ffffcc;
		text-align:center;
}
.olfgif {background-color:#bbddff; text-align:center;}
.olcap {
		font-family:Arial; font-size:13px;
		font-weight:bold;
		color:#708088;
}
a.olclo {font-family:Verdana; font-size:11px; font-weight:bold; color:#ddddff;}
a.olclo:hover {color:#ffffff;}
.oltxt {font-family:Arial; font-size:12px; color:#000000;}

a {color:#338855;font-weight:bold;}
a:hover {color:#FF00FF;}
.text12 {font-family:Verdana,Arial,sans-serif; font-size:12px;}
.text14 {font-family:Verdana,Arial,sans-serif; font-size:14px;}
.text16 {font-family:Verdana,Arial,sans-serif; font-size:16px;}
.text18 {font-family:Verdana,Arial,sans-serif; font-size:18px;}

.myHeader {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:22px;}
.mySubHead {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;}
.mySpacer {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:4px;}
.myText {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:13px;color:#000000;}
.snip {font-family:Verdana,Arial,Helvetica;font-size:10px;}
.purple14 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:14px;color:purple;
 font-weight:bold;}
.purple18 {font-family:Verdana,Arial,Helvetica,sans-serif;font-size:18px;color:purple;
 font-weight:bold;font-style:italic;}
.yellow {color:#ffff00;}
.red {color:#cc0000;}
.blue {color:#0000cc;}
-->
</style>


<script language="javascript" src="<?=$root_path?>js/yui/yahoo/yahoo-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/event/event-min.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<script language="javascript" src="<?=$root_path?>js/yui/dom/dom.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<script  language="javascript">
// Global array for trail added by Matsuu 06182017
var inv_area = [];
var inv_area_delete = [];
var ward_area = [];
var ward_area_delete = [];
var acceditationArrayObjct = [];
// var accreditation_area=[];
var accreditation_area_delete =[];
// ended by Matsu


	//added by Nick 4-4-2015
	$j(function(){

		$j('#upload-file').on('change',function(){
			var reader = new FileReader();
			reader.onload = function(e){
				$j('#loading').show();
				$j('#signature').attr('src',e.target.result);
				$j.ajax({
					url : '<?=$root_path?>modules/personell_admin/ajax/ajax-personnel-signature-upload.php?a=savePersonnelSignature',
					data : new FormData($('aufnahmeform')),
					dataType : 'json',
					cache: false,
					contentType: false,
					processData: false,
					type: 'POST',
					success: function(data){
						$j("div[id^=req-]").css({color:'#000'});
						$j("#req-"+data.element).css({color:'#F00'});
						if(data.element=='alert'){
							alert(data.message);
						}
						$j('#loading').hide();
					},
					completed:function(){
						$j('#loading').hide();
					}
				});
			};
			try{reader.readAsDataURL(this.files[0]);}
			catch(e){}
		});

		$j("#btn-browse-file").on('click',function(){
			$j('#upload-file').click();
		});

		$j("#btn-delete-file").on('click',function(){
			if($j('#signature').attr('src')){
				if(!confirm('Are you sure you want to delete this image?'))
					return false;
			}else{
				return false;
			}
			$j('#loading').show();
			$j.ajax({
				url : '<?=$root_path?>modules/personell_admin/ajax/ajax-personnel-signature-upload.php?a=deletePersonnelSignature',
				data : {
					personell_nr : $j('input[name=personell_nr]').val()
				},
				dataType : 'json',
				type : 'POST',
				success : function(data){
					if(data){
						alert("Image was deleted");
						$j('#signature').attr('src','');
						$j('#upload-file').val('');
					}else{
						alert("Error: Image was not deleted");
					}
					$j('#loading').hide();
				},
				completed : function(){
					$j('#loading').hide();
				}
			});
		});

	});
	// Added by Matsuu 06182017
	function saveINVAreaAudit(data){

			$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=inserTINvArea",
				data: {
					arae_value: data
				},
				async:false,
				success: function(result){
		        	// alert(result);
		    	}
		    	
			});
}
function delINVAreaAudit(data){
			$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=deleteINvArea",
				data: {
					arae_value: data
				},
				async:false,
				success: function(result){
		        	// alert(result);
		    	}
		    	
			});
}
function saveWardAreaAudit(data){
			$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=InsertWardArea",
				data: {
					ward_nr: data
				},
				async:false,
				success: function(result){
		        	// alert(result);
		    	}
			});
}

function deleteWardAreaAudit(data){
			$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=DeleteWardArea",
				data: {
					ward_nr: data
				},
				async:false,
				success: function(result){
		        	// alert(result);
		    	}
		    	
			});
}
function saveAccreditationAudit(data){
			$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=InsertAccreditationArea",
				data: {
					accreditation_area: data
				},
				async:false,
				success: function(result){
		        	// alert(result);
		    	}

		    	
			});
}

function deleteAccreditationAudit(data){
	$J.ajax({
				url: "../../modules/personell_admin/ajax/ajax-personnel.php?request=DeleteAccreditationArea",
				data: {
					accreditation_area: data
				},
				async:false,
				success: function(result){
		        	// alert(result);
		    	}
		    	
			});
			
}

// Ended by Matsuu 06182017

function prepareAddData(details) {
	// var details = new Object();
    console.log(details);
    acceditationArrayObjct = details;
    
}






function chkForm(d){

	if(d.job_function_title.value==""){
		alert("<?php echo $LDPlsEnterJobFunction ?>");
		d.job_function_title.focus();
		return false;
	}else if(d.date_join.value==""){
		alert("<?php echo "$LDDateJoin\\n$LDPlsEnterDate" ?>");
		d.date_join.focus();
		return false;
	}else if(d.contract_start.value==""){
		alert("<?php echo "$LDContractStart\\n$LDPlsEnterDate" ?>");
		d.contract_start.focus();
		return false;
	}else if(d.encoder.value==""){
		alert("<?php echo $LDPlsEnterFullName ?>");
		d.encoder.focus();
		return false;
	}else if(d.ris_id.value=="0" || d.ris_id.value=="00"){
		alert("<?php echo $LDEnterValidRisID ?>");
		d.ris_id.focus();
		return false;
	}else{
		// Added by Matsuu for Trail 06182017
		if (inv_area.length !=0) {
			saveINVAreaAudit(inv_area);
		}
		if(inv_area_delete.length!=0){
			delINVAreaAudit(inv_area_delete);
		}
		if(ward_area.length!=0){
			saveWardAreaAudit(ward_area);
		}
		if(ward_area_delete.length!=0){
			deleteWardAreaAudit(ward_area_delete);
		}
		if(acceditationArrayObjct.length!=0){
			saveAccreditationAudit(acceditationArrayObjct);
		}
		if(accreditation_area_delete.length!=0){
			deleteAccreditationAudit(accreditation_area_delete);
		}
		// ended by Matsuu
		return true;
	}
}


function preset(){
	//searchform
	//$('counter').innerHTML = 0;
	//document.forms["aufnahmeform"].short_id2.value = '<?php echo $pid; ?>';
	//commented by VAN 06-26-08
	document.getElementById('short_id2').value = '<?php echo $pid; ?>';
	//document.getElementById("short_id2").value = '<?php echo $pid; ?>';
	//document.forms["aufnahmeform"].short_id.value = document.forms["aufnahmeform"].short_id2.value;
	//commented by VAN 06-26-08
	document.getElementById('short_id').value = document.getElementById('short_id2').value;
	//commented by VAN 06-26-08
	//if (document.forms["aufnahmeform"].job_function_title.value!=""){
	if (document.getElementById('job_function_title').value!=""){
		getID_var();
	}

	showNurseInfo();
	// $j('#signature').on('change',setSignatureImage);

	$j('#signature').on('change',function(){
		alert(this.value);
	});

}

function fnAssignRole(){
   document.getElementById('doctor_role').value = document.getElementById('select_doctor_role').value;
}

//Added by Jarel 04/03/2013
function assignLevel (){
   document.getElementById('doctor_level').value = document.getElementById('select_doctor_level').value;
}

function getID_var(){
	var short_id, ind, jobtype;
	var shortid_array = new Array();

    //alert(document.getElementById('select_doctor_role').value);
 	//shortid_array = document.forms["aufnahmeform"].short_id2.value.split("");
	shortid_array = document.getElementById('short_id2').value.split("");
	//jobtype = document.forms["aufnahmeform"].job_function_title.value.toLowerCase();
	jobtype = document.getElementById('job_function_title').value.toLowerCase();

	if (isNaN(shortid_array[0]))
			ind = 1;
		else
			ind = 0;

	//short_id = document.forms["aufnahmeform"].short_id2.value.substr(ind);
	short_id = document.getElementById('short_id2').value.substr(ind);

	if ((jobtype.match("doctor")!=null)||(jobtype.match("surgeon")!=null)||(jobtype.match("anesthesiologist")!=null)){
		//document.forms["aufnahmeform"].short_id2.value = "D".concat(short_id);
		document.getElementById('short_id2').value = "D".concat(short_id);
		//document.forms["aufnahmeform"].short_id.value = document.forms["aufnahmeform"].short_id2.value;
		document.getElementById('short_id').value = document.getElementById('short_id2').value;

		/*document.getElementById('license_row').style.display = "";*/ /*commented out : syboy 11/18/2015 : meow*/
		document.getElementById('plicense_row').style.display = "";	//added by cha, august 17, 2010
        document.getElementById('plicense_row2').style.display = "";
		document.getElementById('phic_row').style.display = "";
		document.getElementById('tin_row').style.display = "";
		document.getElementById('resident_row').style.display = "";
		document.getElementById('tier_row').style.display = "";

		document.getElementById('area_nurse_row').style.display = "none";
		document.getElementById('area_nurse_row2').style.display = "none";
		document.getElementById('area_nurse_reliever_row').style.display = "none";
        //added by jasper 02/06/13
        document.getElementById('doc_role').style.display = "";
        document.getElementById('doctor_role').value = document.getElementById('select_doctor_role').value;
        //Added by Jarel 04/03/2013
        document.getElementById('doctor_level').value = document.getElementById('select_doctor_level').value;
        document.getElementById('doc_level').style.display = "";

	}else if (jobtype.match("nurse")!=null||(jobtype.match("nursing attendant")!=null)){ //edited by Nick, added <nursing attendant> condition 11/15/2013 4:58 PM
		//document.forms["aufnahmeform"].short_id2.value = "N".concat(short_id);
		//document.forms["aufnahmeform"].short_id.value = document.forms["aufnahmeform"].short_id2.value;
		document.getElementById('short_id2').value = "N".concat(short_id);
		document.getElementById('short_id').value = document.getElementById('short_id2').value;

		/*document.getElementById('license_row').style.display = "";*/ /*commented out : syboy 11/18/2015 : meow*/
		document.getElementById('plicense_row').style.display = "";	//added by cha, august 17, 2010
        document.getElementById('plicense_row2').style.display = "";
		document.getElementById('phic_row').style.display = "none";
		document.getElementById('tin_row').style.display = "";
		document.getElementById('resident_row').style.display = "none";
		document.getElementById('tier_row').style.display = "none";

		document.getElementById('area_nurse_row').style.display = "none";
		document.getElementById('area_nurse_row2').style.display = "none";
		document.getElementById('area_nurse_reliever_row').style.display = "";
        //added by jasper 02/06/13
        document.getElementById('doc_role').style.display = "none";
        document.getElementById('doctor_role').value = "";
        //added by Jarel 04/03/2013
        document.getElementById('doc_level').style.display = "none";
        document.getElementById('doctor_level').value = "";
        
	}else {
		//document.forms["aufnahmeform"].short_id2.value = "G".concat(short_id);
		//document.forms["aufnahmeform"].short_id.value = document.forms["aufnahmeform"].short_id2.value;
		document.getElementById('short_id2').value = "G".concat(short_id);
		document.getElementById('short_id').value = document.getElementById('short_id2').value;

		/*document.getElementById('license_row').style.display = "none";*/ /*commented out : syboy 11/18/2015 : meow*/
		document.getElementById('plicense_row').style.display = "none";	//added by cha, august 17, 2010
        document.getElementById('plicense_row2').style.display = "none";
		document.getElementById('phic_row').style.display = "none";
		document.getElementById('tin_row').style.display = "";
		document.getElementById('resident_row').style.display = "none";
		document.getElementById('tier_row').style.display = "none";

		document.getElementById('area_nurse_row').style.display = "none";
		document.getElementById('area_nurse_row2').style.display = "none";
		document.getElementById('area_nurse_reliever_row').style.display = "none";
        //added by jasper 02/06/13
        document.getElementById('doc_role').style.display = "none";
        document.getElementById('doctor_role').value = "";
        //added by Jarel 04/03/2013
        document.getElementById('doc_level').style.display = "none";
        document.getElementById('doctor_level').value = "";
	}
}

//added by VAN 05-22-09
var trayItems = 0;
function appendOrder(list,details) {

		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				var initial = details.expiration.split("-").reverse().join("-");
				var dateArrangement = [initial[3], initial[4], initial[2], initial[0], initial[1], initial[5],initial[6], initial[7], initial[8], initial[9]].join('').replace(/\-/g,'/');
				if (dBody) {
						var src;
						var lastRowNum = null,
										items = document.getElementsByName('items[]');
										dRows = dBody.getElementsByTagName("tr");

						if (details) {
								var id = details.id;
								if (items) {

										for (var i=0;i<items.length;i++) {
												if (items[i].value == details.id) {
														var itemRow = $('row'+items[i].value);
														document.getElementById('rowNr'+id).value = details.accre_nr;
														document.getElementById('rowDate'+id).value = details.expiration;
														document.getElementById('name'+id).innnerHTML = details.name;
														document.getElementById('inspin'+id).innerHTML = details.accre_nr;
														document.getElementById('expiration'+id).innerHTML = dateArrangement;

														var name_serv = details.name;	
														alert('"'+name_serv.toUpperCase()+'" is already in the list & has been UPDATED!');

														return true;
												}
										}
										if (items.length == 0)
												 clearOrder(list);
								}

								alt = (dRows.length%2)+1;
								//alert(details.accre_nr);
								src =
										'<tr class="wardlistrow'+alt+'" id="row'+id+'">' +
												'<input type="hidden" name="items[]" id="rowID'+id+'" value="'+id+'" />'+
												'<input type="hidden" name="accre_nr[]" id="rowNr'+id+'" value="'+details.accre_nr+'" />'+
												'<input type="hidden" name="expiration[]" id="rowDate'+id+'" value="'+details.expiration+'" />'+
												'<td class="centerAlign"><a href="javascript:removeItem(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
												'<td width="*" id="name'+id+'">'+details.name+'</td>'+
												'<td width="25%" align="right" id="inspin'+id+'">'+details.accre_nr+'</td>'+
												'<td width="20%" align="right" id="expiration'+id+'">'+dateArrangement+'</td>'+
												'<td></td>'+
										'</tr>';
																//'<td width="1">'+id+'</td>'+
								alert("Successfully Added");
								trayItems++;
						}
						else {
								src = "<tr><td colspan=\"4\">Accreditation list is currently empty...</td></tr>";
						}

						dBody.innerHTML += src;

						return true;
				}
		}
		return false;
}

function reclassRows(list,startIndex) {
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

function clearOrder(list) {
		if (list) {
				var dBody=list.getElementsByTagName("tbody")[0];
				if (dBody) {
						trayItems = 0;
						dBody.innerHTML = "";
						return true;
				}
		}
		return false;
}

function removeItem(id) {
		var destTable, destRows;
		var table = $('order-list');
		var rmvRow=document.getElementById("row"+id);
		var group_action = $('group_action').value;
		var personell_nr_new = '<?=$_GET["personell_nr"]?>';
		if (table && rmvRow) {
				$('rowID'+id).parentNode.removeChild($('rowID'+id));
				$('rowNr'+id).parentNode.removeChild($('rowNr'+id));
				var rndx = rmvRow.rowIndex;
				table.deleteRow(rmvRow.rowIndex);
				reclassRows(table,rndx);
		}
		var items = document.getElementsByName('items[]');
		if (items.length == 0){
				emptyIntialRequestList();
		}
	accreditation_area_delete.push({"group_action":$('group_action').value,"personell_nr":personell_nr_new,"accreditation_area": id});
	console.log(accreditation_area_delete);

}

function emptyIntialRequestList(){
		clearOrder($('order-list'));
		appendOrder($('order-list'),null);
}

function emptyTray() {
		clearOrder($('order-list'));
		appendOrder($('order-list'),null);
		refreshDiscount();
}

//--------------------

//added by VAN 05-04-2010
function addWard(){
	var d = document.aufnahmeform;
	var aWard=d.ward_nr;

	var ward_nr = $('ward_nr').value;
	var ward_name = aWard.options[aWard.selectedIndex].text;
	// added by Matsuu 06182017
	var group_action = $('group_action').value;
	var personell_nr_new = '<?=$_GET["personell_nr"]?>';
	var short_id2 = $('short_id2').value.charAt(0);
	// ended by Matsuu
	var details = new Object();
	
	if (ward_nr==0){
		alert("Please select a ward first.");
		$('ward_nr').focus();
		return false;
	}else{
		details.ward_nr = ward_nr;
		details.ward_name = ward_name;
// added by Matsuu 06182017
	if(short_id2=='N'){
	ward_area.push({"group_action":$('group_action').value,"personell_nr":personell_nr_new,"ward_nr": details.ward_nr});
	}
	// ended by Matsuu
	// console.log(ward_area);
		var list = window.document.getElementById('ward-list');
				result = window.appendWard(list,details);
	}
}

function appendWard(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var lastRowNum = null,
					wardlist = document.getElementsByName('wardlist[]');
					dRows = dBody.getElementsByTagName("tr");

			if (details) {
				var id = details.ward_nr;

				if (wardlist) {
					for (var i=0;i<wardlist.length;i++) {
						if (wardlist[i].value == id) {
							var itemRow = $('row'+wardlist[i].value);//,

							var ward_name = details.ward_name;
							alert('"'+ward_name.toUpperCase()+'" is already in the list!');
							// added by MAtsuu 06182017
							for( i=ward_area.length-1; i>=0; i--) {
							    if( ward_area[i].ward_nr == details.ward_nr) ward_area.splice(i,1);
							}
							// ended by Matsuu 06182017


							return true;
						}
					}
					if (wardlist.length == 0)
						clearWard(list);
				}

				alt = (dRows.length%2)+1;

				src =
					'<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
					'<input type="hidden" name="wardlist[]" id="rowWard'+id+'" value="'+id+'" />'+
					'<td class="centerAlign" ><a href="javascript:removeWard(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td>&nbsp;</td>'+
					'<td width="*" id="ward_name'+id+'">'+details.ward_name+'</td>'+
				'</tr>';
				trayItems++;
			}
			else {
				src = "<tr><td colspan=\"3\">Ward's list is currently empty...</td></tr>";
			}
			//alert(src);
			dBody.innerHTML += src;

			document.getElementById('counter').innerHTML = wardlist.length;

			return true;
		}
	}
	return false;
}

function clearWard(list) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];
		if (dBody) {
			trayItems = 0;
			dBody.innerHTML = "";
			return true;
		}
	}
	return false;
}

function removeWard(id) {
	var destTable, destRows;
	var table = $('ward-list');
	var rmvRow=document.getElementById("row"+id);
	var personell_nr_new = '<?=$_GET["personell_nr"]?>';
	var group_action = $('group_action').value;
	if (table && rmvRow) {
		$('rowWard'+id).parentNode.removeChild($('rowWard'+id));

		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}

	var wardlist = document.getElementsByName('wardlist[]');
	if (wardlist.length == 0){
		emptyIntialRequestList();
	}
	// Added by Matsuu 06182017
	ward_area_delete.push({"group_action":$('group_action').value,"personell_nr":personell_nr_new,"ward_nr": id});
	// console.log(ward_area_delete);
	// Ended by Matsu
	document.getElementById('counter').innerHTML = wardlist.length;
}

function emptyIntialRequestList(){
	clearWard($('ward-list'));
	appendWard($('ward-list'),null);
}

//added by justin 09-30-2015
function addInventory(){
	var d = document.aufnahmeform;
	var aInv = d.inv_nr;

	var area_code = $('inv_nr').value;
	var area_name = aInv.options[aInv.selectedIndex].text;
	var group_action = $('group_action').value;

	var details = new Object();
	var personell_nr_new = '<?=$_GET["personell_nr"]?>';


	if (area_code==0){
		alert("Please select a inventory area first.");
		$('inv_nr').focus();
		return false;
	}else{
		details.area_code = area_code;
		details.area_name = area_name;
		// added by Matsuu 06182017
	inv_area.push({"group_action":$('group_action').value,"personell_nr":personell_nr_new,"area_code":area_code});
	// ended by Matsuu
		var list = window.document.getElementById('inv-list');
			result = window.appendInventory(list,details);
	}
}

function appendInventory(list,details) {
	if (list) {
		var dBody=list.getElementsByTagName("tbody")[0];

		if (dBody) {
			var lastRowNum = null,
				invlist = document.getElementsByName('invarealist[]');
				dRows = dBody.getElementsByTagName("tr");

			if (details) {
				var id = details.area_code,
					area_name = details.area_name;

				if (invlist) {
					for (var i=0;i<invlist.length;i++) {
						if (invlist[i].value == id) {
							var itemRow = $('row'+invlist[i].value);//,

							alert('"'+area_name.toUpperCase()+'" is already in the list!');
							// Added by Matsuu 06082017
							for( i=inv_area.length-1; i>=0; i--) {
							    if( inv_area[i].area_code == details.area_code) inv_area.splice(i,1);
							}
							// ended by Matsuu 06082017

							return true;
						}
					}
					if (invlist.length == 0)
						clearWard(list);
				}

				alt = (dRows.length%2)+1;

				src =
					'<tr class="invlistrow'+alt+'" id="row'+id+'">'+
					'<input type="hidden" name="invarealist[]" id="rowInv'+id+'" value="'+id+'" />'+
					'<td class="centerAlign"><a href="javascript:removeInvArea(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td></td>'+
					'<td width="*" id="inv_area_name'+id+'">'+area_name+'</td>'+
				'</tr>';
			}
			else {
				src = "<tr><td colspan=\"3\">Inventory area list is currently empty...</td></tr>";
			}
			//alert(src);
			dBody.innerHTML += src;

			document.getElementById('inv-counter').innerHTML = invlist.length;

			return true;
		}
	}
	return false;
}

function removeInvArea(id) {
	var destTable, destRows;
	var table = $('inv-list');
	var personell_nr_new = '<?=$_GET["personell_nr"]?>';
	var rmvRow = document.getElementById("row"+id);
	var group_action = $('group_action').value;
	if (table && rmvRow) {
		$('rowInv'+id).parentNode.removeChild($('rowInv'+id));
		var rndx = rmvRow.rowIndex;
		table.deleteRow(rmvRow.rowIndex);
		reclassRows(table,rndx);
	}
	// Added by Matsuu 06182017
	inv_area_delete.push({"group_action":group_action,"personell_nr":personell_nr_new,"area_code": id})
	// ended by Matsuu
	// console.log(inv_area_delete);
	var invlist = document.getElementsByName('invarealist[]');
	if (invlist.length == 0){
		emptyIntialInvRequestList();
	}
	document.getElementById('inv-counter').innerHTML = invlist.length;
}

function emptyIntialInvRequestList(){
	clearWard($('inv-list'));
	appendInventory($('inv-list'),null);
}


function reclassRows(list,startIndex) {
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

function showNurseInfo(){
	if ($('is_reliever').checked){
		document.getElementById('area_nurse_row').style.display = "none";
		document.getElementById('area_nurse_row2').style.display = "none";
	}else{
		document.getElementById('area_nurse_row').style.display = "";
		document.getElementById('area_nurse_row2').style.display = "";
	}
}
// ------------ end of VAN 05-04-2010-----------

//---------------add 02-21-07-----------------------------

<?php require($root_path.'include/inc_checkdate_lang.php'); ?>

-->
</script>

<script language="javascript" src="<?php echo $root_path; ?>js/setdatetime.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/checkdate.js"></script>
<script language="javascript" src="<?php echo $root_path; ?>js/dtpick_care2x.js"></script>

<?php

	echo '<link rel="stylesheet" type="text/css" media="all" href="' .$root_path.'js/jscalendar/calendar-win2k-cold-1.css">';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/lang/calendar-en.js"></script>';
	echo '<script type="text/javascript" src="'.$root_path.'js/jscalendar/calendar-setup_3.js"></script>';

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

</HEAD>


<BODY bgcolor="<?php echo $cfg['body_bgcolor'];?>" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0
<?php
if(!$personell_nr && !$pid)
{
?>
onLoad="if(document.searchform.searchkey.focus) document.searchform.searchkey.focus(); "
<?php
}

?>
<?php if (!$cfg['dhtml']){ echo ' link='.$cfg['body_txtcolor'].' alink='.$cfg['body_alink'].' vlink='.$cfg['body_txtcolor']; } ?> onLoad="preset(); ">


<table width=100% border=0 cellspacing="0" cellpadding=0>

<!-- Load tabs -->
<?php

$target='personell_reg';
include('./gui_bridge/default/gui_tabs_personell_reg.php')

?>

<tr>
<td colspan=3>

<ul>

<?php

# If the origin is admission link, show the search prompt
if(!$pid&&!$personell_nr){
	ob_start();
?>
		<table border=0>
			<tr>
				<td valign="bottom"><img <?php echo createComIcon($root_path,'angle_down_l.gif','0') ?>></td>
				<td class="prompt"><?php echo $LDPlsFindPersonFirst ?></td>
				<td><img <?php echo createMascot($root_path,'mascot1_l.gif','0','absmiddle') ?>></td>
			</tr>
		</table>
<?php
		$sPretext=ob_get_contents();
	ob_end_clean();

	$user_origin='admit';

	require_once($root_path.'include/care_api_classes/class_gui_search_person.php');
	$psearch = & new GuiSearchPerson;

	$psearch->setSearchFile('personell_register_search.php');

	$psearch->setTargetFile('person_register_show.php');

	$psearch->setCancelFile($root_path.'main/spediens.php');

	# Set to TRUE if you want to auto display a single result
	//$psearch->auto_show_byalphanumeric =TRUE;
	# Set to TRUE if you want to auto display a single result based on a numeric keyword
	# usually in the case of barcode scanner data
	$psearch->auto_show_bynumeric = TRUE;

	$psearch->setPrompt($LDEnterPersonSearchKey);

	$psearch->pretext=$sPretext;

	$psearch->display();

}else{

?>

<form method="post" action="<?php echo $thisfile; ?>" name="aufnahmeform" id="aufnahmeform" onSubmit="return chkForm(this)" enctype="multipart/form-data">

<table border="0" cellspacing=1 cellpadding=0 width=65%>

<?php

if($error)
{

?>
<tr>
<td colspan=4><center>
<font class="warnprompt">
<img <?php echo createMascot($root_path,'mascot1_r.gif','0','bottom') ?> align="absmiddle">
	<?php echo $LDDataNoSaved; ?>
</center>
</td>
</tr>
<?php
}
 ?>


<tr>
<td  class="adm_item" width="30%"><?php echo $LDPersonellNr ?>:
</td>
<td class="adm_input">
<?php echo $full_pnr;  ?>
</td>
<td rowspan=8 align="center" class="photo_id"><img <?php echo $img_source ?>>
</td>
</tr>

<tr>
<td  class="adm_item">&nbsp;<?php //echo $LDDateJoin ?>
</td>
<td class="adm_input"><FONT color="#800000">&nbsp;
<?php
		// if($date_join!='0000-00-00') echo @formatDate2Local($date_join,$date_format);
?>
</td>
</tr>

<tr>
<td class="adm_item">&nbsp;<?php// echo $LDDateExit ?>
</td>
<td class="adm_input"><FONT color="#800000">&nbsp;<?php //if($date_exit!='0000-00-00') echo @formatDate2Local($date_exit,$date_format);  ?>
</td>
</tr>

<tr>
<td class="adm_item"><?php echo $LDTitle ?>:
</td>
<td class="adm_input"><?php echo $title ?>
</td>

</tr>
<tr>
<td class="adm_item"><?php echo $LDLastName ?>:
</td>
<td bgcolor="#ffffee">&nbsp;<FONT color="#800000"><b><?php echo $name_last; ?></b>
</td>
</tr>

<tr>
<td class="adm_item"><?php echo $LDFirstName ?>:
</td>
<td bgcolor="#ffffee">&nbsp;<FONT color="#800000"><b><?php echo $name_first; ?></b>
</td>
</tr>

<?php if($GLOBAL_CONFIG['patient_name_2_show'])
{
?>
<tr>
<td class="adm_item"><?php echo $LDName2 ?>:
</td>
<td bgcolor="#ffffee" colspan=2>&nbsp;<FONT color="#800000"><b><?php echo $name_2; ?></b>
</td>
</tr>
<?php
}

if($GLOBAL_CONFIG['patient_name_3_show'])
{
?>
<tr>
<td class="adm_item"><?php echo $LDName3 ?>:
</td>
<td bgcolor="#ffffee" colspan=2>&nbsp;<FONT color="#800000"><b><?php echo $name_3; ?></b>
</td>
</tr>
<?php
}

if($GLOBAL_CONFIG['patient_name_middle_show'])
{
?>
<tr>
<td class="adm_item"><?php echo $LDNameMid ?>:
</td>
<td bgcolor="#ffffee" colspan=2>&nbsp;<FONT color="#800000"><b><?php echo $name_middle; ?></b>
</td>
</tr>
<?php
}
?>

<tr>
<td class="adm_item"><?php echo $LDBday ?>:
</td>
<td bgcolor="#ffffee">&nbsp;<FONT color="#800000"><b><?php echo @formatDate2Local($date_birth,$date_format);?></b>
</td>

</tr>

<tr>
<td class="adm_item"><?php echo $LDSex ?>
</td>
<td bgcolor="#ffffee">&nbsp;<FONT color="#800000"><?php if($sex=='m') echo $LDMale; elseif($sex=='f') echo $LDFemale; ?>
</td>
</tr>

<tr>
<td class="adm_item"><?php echo $LDShortID; ?>:
</td>
<td colspan=2 class="adm_input">

<input name="short_id2" id="short_id2" type="text" size="30"  maxlength="10" value="<?php echo $short_id; ?>" disabled >
<input type="hidden" name="short_id" id="short_id">
</td>
</tr>

<!--
<tr>
<td class="adm_item"><?php //echo $LDJobNr; ?>:
</td>
<td colspan=2 class="adm_input"><input name="job_type_nr" type="text" size="30" maxlength="3" value="<?php if($job_type_nr) echo $job_type_nr; ?>" >
</td>
</tr>
-->
<?
	$phpfd=$date_format;

	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
	//$phpfd=str_replace("yy","%Y", strtolower($phpfd));

?>
<!--
<tr>
<td class="adm_item"><?php echo $LDJobFunction; ?>:
</td>
<td colspan=2 class="adm_input"><input name="job_function_title" type="text" size="30"  maxlength="60" value="<?php echo $job_function_title; ?>" >
</td>
</tr>
-->

<tr bgcolor="white">
<td class="adm_item"><?php echo $LDJobFunction ?>:
</td>
<td colspan=2   class="adm_input">

<select name="job_function_title" id="job_function_title" onChange="getID_var();">
<?php
	 # added burn: Sept 27, 2006
	 require_once($root_path.'include/care_api_classes/class_personell.php');
	 $personell_obj2=new Personell();
	 $jobsFunction = $personell_obj2->getRoleNameOfPerson();
	 if((trim($job_function_title=="")|| !$jobsFunction)){
			# no job function title yet, or
		# no role name entry from the database 'care_role_person' table
		echo "<option value=\"\" selected></option>";
	 }
	 while ($rowJob = $jobsFunction->FetchRow()){
			echo "<option value=\"".$rowJob['name']."\" ";
			if(trim($job_function_title)==trim($rowJob['name'])){
				 echo 'selected';
			}
			echo ">".trim($rowJob['name'])."</option>";
	 } # end while loop
?>
</select>
</td>
</tr>

<!-- added by: syboy; 5/15/2015 -->
<tr bgcolor="white">
<td class="adm_item"><?php // echo $LDcatlev ?>Contract Type :
</td>
<td colspan=2   class="adm_input">

<select name="category" id="category">
	<option>-Select category level-</option>
<?php
	 require_once($root_path.'include/care_api_classes/class_personell.php');

	 $categoryL = new Personell();
	 $catLevel = $categoryL->getCategoryLevel();
	 if((trim($category=="")|| !$catLevel)){
		//echo "<option value=\"\" selected></option>";
	 }

	 # start while loop
	 while ($rowJob = $catLevel->FetchRow()){
			echo "<option value=\"".$rowJob['id']."\" ";
			if(trim($category)==trim($rowJob['id'])){
				 echo 'selected';
			}
			echo ">".trim($rowJob['description'])."</option>";
	 } # end while loop
?>
</select>
	<!-- <span id="subcat"></span> -->
	<!-- <?= $jobcatdes; // var_dump($jobcatdes); die(); ?> -->
</td>
</tr>

<!-- end -->
<!--- added by: syboy 01/17/2016 : meow -->
<tr>
	<td class="adm_item">Category Type :</td>
	<td colspan=2 class="adm_input">
		<span id="subcat"></span>
	</td>
</tr>
<!-- end -->

<tr bgcolor="white" id="doc_role">
<td class="adm_item">Role:
<input type="hidden" name="doctor_role" id="doctor_role" value="<?php echo $doctor_role; ?>">
</td>
<td colspan=2   class="adm_input">
<select name="select_doctor_role" id="select_doctor_role" onChange="fnAssignRole();">
<?php
     # added by jasper 02/06/13
     require_once($root_path.'include/care_api_classes/class_personell.php');
     $personell_obj3=new Personell();
     $doc_role = $personell_obj3->fnGetDoctorRole();
     if((trim($doctor_role=="")|| !$doc_role)){
            # no job function title yet, or
        # no role name entry from the database 'care_role_person' table
        echo "<option value=\"\" selected></option>";
     }
     while ($rowJob = $doc_role->FetchRow()){
            echo "<option value=\"".$rowJob['id']."\" ";
            if(trim($doctor_role)==$rowJob['id']){
                 echo 'selected';
            }
            echo ">".trim($rowJob['name'])."</option>";
     } # end while loop

     # added by jasper 02/06/13
?>
</select>
</td>
</tr>


<tr bgcolor="white" id="doc_level">
<td class="adm_item">Doctor Level:
<input type="hidden" name="doctor_level" id="doctor_level" value="<?php echo $doctor_level; ?>">
</td>
<?php #Added by JArel 04/03/2013
require_once($root_path.'include/care_api_classes/class_personell.php');
$personell_obj3=new Personell();
$doc_level = $personell_obj3->getDoctorLevel();
if (is_object($doc_level)){
    while ($row_doc=$doc_level->FetchRow()) {
        $selected='';     
        if ($doctor_level==$row_doc['id'])
            $selected='selected';
                
        $doc_option.='<option '.$selected.' value="'.$row_doc['id'].'">'.ucwords($row_doc['desc']).'</option>';
    }
}
?>
<td colspan=2   class="adm_input">
<select name="select_doctor_level" id="select_doctor_level" onChange="assignLevel();">
<?=$doc_option?>
</select>
</td>
</tr>


<?php
		require_once($root_path.'include/care_api_classes/class_ward.php');
		$ward_obj=new Ward;
		$rsWard = $ward_obj->getAllActiveWards();
		$options="";

		$options="<option value='0'>-No Ward-</option>";

		while ($rowWard = $rsWard->FetchRow()){
			#if ($ward_nr==$rowWard['nr'])
			#	$selected = "selected";
			#else
			#	$selected = "";

			$options.='<option value="'.$rowWard['nr'].'" '.$selected.'>'.$rowWard['name'].'</option>';
		} # end while loop
?>
<tr style="display:none" id="area_nurse_reliever_row">
<td class="adm_item">Reliever (All Ward)? :
</td>
<td colspan=2 class="adm_input">
<input type="checkbox" id="is_reliever" name="is_reliever" value="1" onchange="showNurseInfo();"  <?= (($is_reliever)?'checked=checked':'')?> />
</td>
</tr>

<tr style="display:none" id="area_nurse_row">
<td class="adm_item">Ward Area :
</td>
<td colspan=2 class="adm_input">
<select id="ward_nr" name="ward_nr">
<?=$options?>
</select> &nbsp;&nbsp; <!--<button id="btn_ward" name="btn_ward" onclick="addWard();" style="cursor:pointer">ADD WARD</button>-->
<img id="btn_ward" src='../../images/btn_add.gif' border=0 onclick="addWard();" align="absmiddle" style="cursor:pointer">
</td>
</tr>
<!--
'<tr class="wardlistrow'+alt+'" id="row'+id+'">'+
					'<input type="hidden" name="wardlist[]" id="rowWard'+id+'" value="'+id+'" />'+
					'<td class="centerAlign" ><a href="javascript:removeWard(\''+id+'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>'+
					'<td>&nbsp;</td>'+
					'<td width="*" id="ward_name'+id+'">'+details.ward_name+'</td>'+
-->
<tr style="display:none" id="area_nurse_row2">
	<td class="adm_item">
		 List of Ward:
	</td>
	<?php
			$result = $personell_obj->get_Nurse_Ward_Area($personell_nr);
			#echo $personell_obj->sql;
			$count =  $personell_obj->count;
			#echo "s = ".$count;
	?>
	<td colspan=2 class="adm_item">
			<table id="ward-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
				<thead>
					 <tr id="ward-list-header">
						 <th width="5%" nowrap align="left">Cnt : <span id="counter"><?=$count?></span></th>
						 <th width="1%">&nbsp;</th>
						 <th width="*" nowrap align="center">&nbsp;&nbsp;Ward</th>
					 </tr>
			 </thead>
			<tbody>
				<?php
					 if ($count==0){
							 echo '<tr><td colspan=4>Ward list is currently empty...</td></tr>';
					 }else{
							 $rows=array();
							 while ($row=$result->FetchRow()) {
									$rows[] = $row;
							 }
							 foreach ($rows as $i=>$row) {
									if ($row) {
										 $count++;
										 $alt = ($count%2)+1;

										 $src .= '
															<tr class="wardlistrow'.$alt.'" id="row'.$row['ward_nr'].'">
																 <input type="hidden" name="wardlist[]" id="rowWard'.$row['ward_nr'].'" value="'.$row['ward_nr'].'" />
																 <td class="centerAlign"><a href="javascript:removeWard('.$row['ward_nr'].')"><img src="../../images/btn_delitem.gif" border="0"/></a></td></td>
																 <td>&nbsp;</td>
																 <td width="*" id="ward_name'.$row['ward_nr'].'">'.$row['name'].'</td>
															</tr>
														';
									}
								}
								echo $src;

					 }
				?>
			</tbody>
		</table>
 </td>
</tr>
<!-- added by justin 9/30/2015 -->
<?php

	require_once($root_path.'include/care_api_classes/class_inventory.php');
	$inv_obj=new Inventory;
	$inv_list = $inv_obj->getInventoryArea();
	$invOptions="";

	$invOptions="<option value='0'>-No Inventory Area-</option>";

	while ($x = $inv_list->FetchRow()){
		$invOptions .= "<option ".(($x['is_deleted'] == 1) ?"title='".$x['area_name']." area has deactivated.'" : "")." value=\"".$x['area_code']."\" ".(($x['is_deleted'] == 1) ?"disabled" : "").">".$x['area_name']."</option>";
		} # end while loop
?>
<tr id="area_inv_row">
	<td class="adm_item">
		Inventory Area :
	</td>
	<td colspan=2 class="adm_input">
	<input type="hidden" id="group_action" name="group_action" value="<?php echo date("Y-HIA")."-".rand(1,100)."-".rand(1,100000); ?>">
		<select id="inv_nr" name="inv_nr">
			<?=$invOptions?>
		</select>
		<img id="btn_inv" src='../../images/btn_add.gif' border=0 onclick="addInventory();" align="absmiddle" style="cursor:pointer; margin-left: 1em">
	</td>
</tr>
<tr id="area_inv_row2">
	<td class="adm_item">
		List of Inventory Area:
	</td>
	<?php
		$result = $inv_obj->getInventoryAreaByPersonnel($personell_nr);
		$count =  $inv_obj->count;
	?>
	<td colspan=2 class="adm_item">
		<table id="inv-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
			<thead>
				<tr id="inv-list-header">
					<th width="5%" nowrap align="left">Cnt : <span id="inv-counter"><?=$count?></span></th>
					<th width="1%"></th>
					<th width="*" nowrap align="center">Inventory Area</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if ($count==0){
						echo '<tr><td colspan=3>Inventory area list is currently empty...</td></tr>';
					}else{
						$rows = array();
						while ($row=$result->FetchRow()) {
							$count++;
							$alt = ($count%2)+1;

							$src .= '
								<tr class="wardlistrow'.$alt.'" id="row'.$row['area_code'].'">
									<input type="hidden" name="invarealist[]" id="rowInv'.$row['area_code'].'" value="'.$row['area_code'].'" />
									<td class="centerAlign">
										<a href="javascript:removeInvArea(\''.$row['area_code'].'\')">
											<img src="../../images/btn_delitem.gif" border="0"/>
										</a>
									</td>
									<td></td>
									<td width="*" id="inv_area_name'.$row['area_code'].'">'.$row['area_name'].'</td>
								</tr>
							';
						}
						echo $src;
					}
				?>
			</tbody>
		</table>
	</td>
</tr>
<!-- added by VAN 06-14-08 -->
<tr>
<td class="adm_item">Position :
</td>
<td colspan=2 class="adm_input">

<input name="job_position" id="job_position" type="text" size="30" value="<?php echo $job_position; ?>">
</td>
</tr>
<!--- added by justin 04/6/2016 -->
<tr>
<td class="adm_item">RIS ID Number :
</td>
<td colspan=2 class="adm_input">

<input name="ris_id" id="ris_id" type="number" size="25" style="-moz-appearance:textfield; -webkit-appearance: none;" value="<?php echo $ris_id; ?>">
<span id="risWarning" style="display: none; color: red;"> *Duplicate RIS ID Number</span>
</td>
</tr>
<!-- end -->
<!--- added by syboy 05/14/2015 -->
<tr>
<td class="adm_item">ID Number :
</td>
<td colspan=2 class="adm_input">

<input name="id_nr" id="id_nr" type="text" size="30" value="<?php echo $id_nr; ?>">
</td>
</tr>
<!-- end -->
<!--- added by: syboy 01/17/2016 : meow -->
<tr>
<td class="adm_item">Biometric Number :
</td>
<td colspan=2 class="adm_input">

<input name="bio_nr" id="bio_nr" type="text" size="30" value="<?php echo $bio_nr; ?>">
</td>
</tr>
<!-- end -->

<!----- added by VAN 11-17-09 -->
<tr>
<td class="adm_item">Other Title :
</td>
<td colspan=2 class="adm_input">

<input name="other_title" id="other_title" type="text" size="30" value="<?php echo $other_title; ?>">
</td>
</tr>


<tr id="license_row">
<td class="adm_item">License No. :
</td>
<td colspan=2 class="adm_input">

<input name="license_nr" id="license_nr" type="text" size="30" value="<?php echo $license_nr; ?>">
</td>
</tr>

<!--<tr style="display:none" id="plicense_row">
<td class="adm_item">Prescription License No. :
</td>
<td colspan=2 class="adm_input">

<input name="prescription_license_nr" id="prescription_license_nr" type="text" size="30" value="<?php echo $prescription_license_nr; ?>">
</td>
</tr>-->

<tr style="display:none" id="plicense_row">
<td class="adm_item">PTR No. :
</td>
<td colspan=2 class="adm_input">

<input name="ptr_nr" id="ptr_nr" type="text" size="30" value="<?php echo $ptr_nr; ?>">
</td>
</tr>

<tr style="display:none" id="plicense_row2">
<td class="adm_item">S2 No. :
</td>
<td colspan=2 class="adm_input">

<input name="s2_nr" id="s2_nr" type="text" size="30" value="<?php echo $s2_nr; ?>">
</td>
</tr>

<tr style="display:none" id="phic_row">
		<td colspan="3">
		<table width="100%" border="0">
				<tr class="adm_item">
						<td width="30%">&nbsp;</td>
						<td colspan=3 width="*" align="right"><a href="javascript:void(0);"
																										onclick="return overlib(
																																				 OLiframeContent('<?= $root_path ?>modules/personell_admin/seg-insurance-accre-tray.php?personell_nr=<?=$personell_nr?>', 600, 340, 'fOrderTray', 1, 'auto'),
																																				 WIDTH,600, TEXTPADDING,0, BORDER,0,
																																				 STICKY, SCROLL, CLOSECLICK, MODAL,
																																				 CLOSETEXT, '<img src=<?= $root_path ?>images/close.gif border=0 >',
																																				 CAPTIONPADDING,4,
																																				 CAPTION,'Add accreditation number tray',
																																				 MIDX,0, MIDY,0,
																																				 STATUS,'Add accreditation number tray');"
																										onmouseout="nd();">
																										<img name="btninsurance" id="btninsurance" src="<?= $root_path ?>images/btn_additems.gif" border="0"></a>
						</td>
				</tr>
				<tr class="adm_item">
					<td class="adm_item">Accreditation No. :</td>
					<td colspan=3 class="adm_input" width="*">
					<!-- <input type="hidden" id="group_action" name="group_action" value="<?php echo date("Y-HIA")."-".rand(1,100)."-".rand(1,100000); ?>"> -->
					<!--<input name="phic_nr" id="phic_nr" type="text" size="30" value="<?php echo $phic_nr; ?>">-->
					<table id="order-list" class="segList" border="0" cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr id="order-list-header">
								<th width="4%" nowrap></th>
								<th width="*" nowrap align="left">&nbsp;&nbsp;Insurance Company</th>
								<th width="20%" nowrap align="right">&nbsp;&nbsp;Accreditation No.</th>
								<th width="10%" nowrap align="right">&nbsp;&nbsp;Expiration Date</th>
								<th width="5%"></th>
							</tr>
						<thead>
						<tbody>
							<!--<td colspan="4">Accreditation list is currently empty...</td>-->
							<?php
							$result = $personell_obj->get_Doctor_Accreditation($personell_nr);
							$count =  $personell_obj->count;
							#echo "c = ".$count;
							if ($count==0){
								echo '<tr><td colspan=5>Accreditation list is currently empty...</td></tr>';
							}else{
								$rows=array();
								while ($row=$result->FetchRow()) {
									$rows[] = $row;
								}
								foreach ($rows as $i=>$row) {
									if ($row) {
										$count++;
										$alt = ($count%2)+1;
										$src .= '
										<tr class="wardlistrow'.$alt.'" id="row'.$row['hcare_id'].'">
											<input type="hidden" name="items[]" id="rowID'.$row['hcare_id'].'" value="'.$row['hcare_id'].'" />
											<input type="hidden" name="accre_nr[]" id="rowNr'.$row['hcare_id'].'" value="'.$row['accreditation_nr'].'" />
											<input type="hidden" name="expiration[]" id="rowDate'.$row['hcare_id'].'" value="'.$row['expiration'].'" />
											<td class="centerAlign"><a href="javascript:removeItem(\''.$row['hcare_id'].'\')"><img src="../../images/btn_delitem.gif" border="0"/></a></td>
											<td id="name'.$row['hcare_id'].'">'.$row['firm_id'].'</td>
											<td width="25%" align="right" id="inspin'.$row['hcare_id'].'">'.$row['accreditation_nr'].'</td>
											<td width="25%" align="right" id="expiration'.$row['hcare_id'].'">'.@formatDate2Local($row['expiration'],$date_format).'</td>
											<td></td>
										</tr>';
									}
								}
								echo $src;
							}
							?>
								
							</tbody>
						</table>
					</td>
				</tr>
		</table>
		</td>
</tr>

<tr style="display:none" id="tin_row">
<td class="adm_item">TIN :
</td>
<td colspan=2 class="adm_input">

<input name="tin" id="tin" type="text" size="30" value="<?php echo $tin; ?>">
</td>
</tr>

<tr style="display:none" id="resident_row">
<td class="adm_item">Is resident doctor? :
</td>
<td colspan=2 class="adm_input">
<?php
		if ($is_resident_dr)
				$check = 'checked';
		else
				$check = '';
?>
<input type="checkbox" id="is_resident_dr" name="is_resident_dr" value="1" <?php echo $check?>>
</td>
</tr>
<tr style="display:none" id="tier_row">
<td class="adm_item">Level :
</td>
<td colspan=2 class="adm_input">
<?php
		$result = $personell_obj2->getRoleTypeLevels();
		$ntier_nr = 1;
		$stiers = '';
		$count = 0;
		if ($result) {
				while($row=$result->FetchRow()) {
						$checked=($row['tier_nr'] == $tier_nr) ? 'selected="selected"' : "";
						$stiers .= "<option value=\"".$row['tier_nr']."\" $checked>".$row['tier_desc']."</option>\n";

						if ($checked || ($count == 0)) $ntier_nr = $row['tier_nr'];
						if ($checked) $index = $count;
						$count++;
				}
		}
		else
				$stiers = "<option value=\"0\" $checked>- Select Level -</option>\n";
		$stiers = '<select class="segInput" name="tier_nr" id="tier_nr">'."\n".$stiers."</select>\n";
		echo $stiers;
?>
</td>
</tr>

<!--
<tr>
<td class="adm_item">Department :
</td>
<td colspan=2 class="adm_input">
	<select>
	</select>

</td>
</tr>
-->
<!-- -->

<tr>
<td class="adm_item"><?php echo $LDDateJoin; ?>:
</td>
<td colspan=2 class="adm_input"><input name="date_join" id="date_text" type="text"  size="12" maxlength=10  value="<?php  if(isset($date_join)&&$date_join!=DBF_NODATE)   echo formatDate2Local($date_join,$date_format); ?>"
 onFocus="this.select();"  onBlur="IsValidDate(this,'<?php echo $date_format ?>')" onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
<!--<a href="javascript:show_calendar('aufnahmeform.date_join','<?php echo $date_format ?>')">-->
 <img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?> id="date_trigger" style="cursor:pointer ">
 <font size=1>[ <?php
 $dfbuffer="LD_".strtr($date_format,".-/","phs");
	echo $$dfbuffer;
 ?> ] </font>

		<!--EDITED: SEGWORKS -->
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "date_text", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger", singleClick : true, step : 1

	});
</script>

</td>
</tr>
<tr>
<td class="adm_item"><?php echo $LDDateExit ?>:
</td>
<td colspan=2 class="adm_input"><input name="date_exit" id="date_exit" type="text"  size="12" maxlength=10  value="<?php if(isset($date_exit)&&$date_exit!=DBF_NODATE)   echo formatDate2Local($date_exit,$date_format); ?>"
 onFocus="this.select();"  onBlur="IsValidDate(this,'<?php echo $date_format ?>')" onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
<!--<a href="javascript:show_calendar('aufnahmeform.date_exit','<?php echo $date_format ?>')"> -->
 <img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?> id="date_trigger_exit" style="cursor:pointer ">
 <font size=1>[ <?php
 $dfbuffer="LD_".strtr($date_format,".-/","phs");
	echo $$dfbuffer;
 ?> ] </font>

		<!--EDITED: SEGWORKS -->
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "date_exit", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_exit", singleClick : true, step : 1

	});
</script>

</td>
</tr>
<tr>
<td class="adm_item"><?php echo "Remarks" ?>:
</td>
<td colspan=2 class="adm_input"><input name="contract_class" type="text" size="30" maxlength="35" value="<?php if($jobcatdes) echo $jobcatdes; ?>">
</td>
</tr>
<tr>
<td class="adm_item"><?php echo $LDContractStart ?>:
</td>
<td colspan=2 class="adm_input"><input name="contract_start" id="date_cstart" type="text"  size="12" maxlength=10  value="<?php  if(isset($contract_start)&&$contract_start!=DBF_NODATE)   echo formatDate2Local($contract_start,$date_format); ?>"
 onFocus="this.select();"  onBlur="IsValidDate(this,'<?php echo $date_format ?>')" onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
<!--<a href="javascript:show_calendar('aufnahmeform.contract_start','<?php echo $date_format ?>')">-->
 <img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?> id="date_trigger_cstart" style="cursor:pointer ">
 <font size=1>[ <?php
 $dfbuffer="LD_".strtr($date_format,".-/","phs");
	echo $$dfbuffer;
 ?> ] </font>

		<!--EDITED: SEGWORKS -->
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "date_cstart", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_cstart", singleClick : true, step : 1

	});
</script>

 </td>
</tr>
<tr>
<td class="adm_item"><?php echo $LDContractEnd ?>:
</td>
<td colspan=2 class="adm_input"><input name="contract_end" id="date_cend" type="text" size="12" maxlength=10 value="<?php  if(isset($contract_end)&&$contract_end!=DBF_NODATE)   echo formatDate2Local($contract_end,$date_format); ?>"
 onFocus="this.select();"  onBlur="IsValidDate(this,'<?php echo $date_format ?>')" onKeyUp="setDate(this,'<?php echo $date_format ?>','<?php echo $lang ?>')">
<!--<a href="javascript:show_calendar('aufnahmeform.contract_end','<?php echo $date_format ?>')">-->
 <img <?php echo createComIcon($root_path,'show-calendar.gif','0','absmiddle'); ?> id="date_trigger_cend" style="cursor:pointer ">
 <font size=1>[ <?php
 $dfbuffer="LD_".strtr($date_format,".-/","phs");
	echo $$dfbuffer;
 ?> ] </font>

		<!--EDITED: SEGWORKS -->
	<script type="text/javascript">
	Calendar.setup ({
		inputField : "date_cend", ifFormat : "<?php echo $phpfd?>", showsTime : false, button : "date_trigger_cend", singleClick : true, step : 1

	});
</script>

 </td>
</tr>
<!-- Commented by: syboy 02/03/2016 : meow -->
<!-- <tr>
<td class="adm_item"><?php// echo $LDPayClass ?>:
</td>
<td colspan=2 class="adm_input"><input name="pay_class" type="text" size="30" maxlength="25" value="<?php// echo $pay_class; ?>" >
</td>
</tr>
<tr>
<td class="adm_item"><?php// echo $LDPaySubClass ?>:
</td>
<td colspan=2 class="adm_input"><input name="pay_class_sub" type="text" size="30" maxlength="25" value="<?php //echo $pay_class_sub; ?>" >
</td>
</tr>
<tr>
<td class="adm_item"><?php// echo $LDLocalPremiumID ?>:
</td>
<td colspan=2 class="adm_input"><input name="local_premium_id" type="text" size="30" maxlength="5" value="<?php //echo $local_premium_id; ?>" >
</td>
</tr> -->
<!--
<tr>
<td class="adm_item"><?php// echo $LDTaxAccountNr ?>:
</td>
<td colspan=2 class="adm_input"><input name="tax_account_nr" type="text" size="30" maxlength="60" value="<?php //echo $tax_account_nr; ?>" >
</td>
</tr>
-->
<!-- <tr>
<td class="adm_item"><?php// echo $LDInternalRevenueCode ?>:
</td>
<td colspan=2 class="adm_input"><input name="ir_code" type="text" size="30" maxlength="25" value="<?php// echo $ir_code; ?>" >
</td>
</tr>
<tr>
<td class="adm_item"><?php// echo $LDNrWorkDay ?>:
</td>
<td colspan=2 class="adm_input">
<select name="nr_workday"> -->
	<?php //for($x=0; $x<11;$x++){echo "<option value=\"$x\" ";if($nr_workday==$x) echo 'selected';echo "> $x </option>";} ?>
<!-- </select> -->

<!-- <input name="nr_workday" type="text" size="30" value="<?php // if($nr_workday) echo $nr_workday; ?>" > -->
<!-- </td>
</tr>
<tr>
<td class="adm_item"><?php // echo $LDNrWeekHour ?>:
</td>
<td colspan=2 class="adm_input">
<select name="nr_weekhour"> -->
	<?php // for($x=0; $x<61;$x++){echo "<option value=\"$x\" ";if($nr_weekhour==$x) echo 'selected';echo "> $x </option>";} ?>
<!-- </select> -->

<!-- <input name="nr_weekhour" type="text" size="30" value="<?php //if($nr_weekhour>0) echo $nr_weekhour; ?>" > -->
<!-- </td>
</tr>
<tr>
<td class="adm_item"><?php echo $LDNrVacationDay ?>:
</td>
<td colspan=2 class="adm_input">
<select name="nr_vacation_day"> -->
	<?php // for($x=0; $x<60;$x++){echo "<option value=\"$x\" ";if($nr_vacation_day==$x) echo 'selected';echo "> $x </option>";} ?>
<!-- </select> -->

<!-- <input name="nr_vacation_day" type="text" size="30" value="<?php // if($nr_vacation_day) echo $nr_vacation_day; ?>" > -->
<!-- </td>
</tr> -->
<!-- ended syboy -->
<tr>
<td class="adm_item"><?php echo $LDNrDependent ?>:
</td>
<td colspan=2 class="adm_input">
<!-- <select name="nr_dependent"> -->
	<?php 
		# added by: syboy 02/04/2016 : meow
		require_once($root_path.'include/care_api_classes/class_personell.php');
	 	$objPersonell = new Personell();
	 	$dependent = $objPersonell->getEmployeeDependents($pid);
	 	echo $dependent;
	 ?>
<!-- </select> -->

<!-- <input name="nr_dependent" type="text" size="30" value="<?php //if($nr_dependent) echo $nr_dependent; ?>" > -->
</td>
</tr>

<tr>
<td class="adm_item"><?php echo $LDMultipleEmployer ?>:
</td>
<td colspan=2 class="adm_input">

<input name="multiple_employer" type="radio"  value="1"  <?php  if($multiple_employer) echo 'checked'; ?>><?php  echo $LDYes; ?>
<input name="multiple_employer" type="radio"  value="0"  <?php  if(!$multiple_employer)  echo 'checked'; ?>><?php  echo $LDNo; ?>

</td>
</tr>
<?php 
	if ($pcreated_id == null || $pcreated_id == "") {
		$record_id = $HTTP_SESSION_VARS['sess_user_name'];
	}else if($pcreated_id != null || $pcreated_id != ""){
		$record_id = $create_id;
	}else{
		$record_id = $pcreated_id;
	}
?>
<tr>
<td class="adm_item"><?php echo $LDRecordedBy ?>:
</td>
<td colspan=2 class="adm_input"><input  name="recordby" type="text" value="<?php  echo $record_id; ?>" size="30" readonly="true">
</nobr>
</td>
</tr>

<!-- added by: syboy; 05/14/2015 -->
<tr>
<td class="adm_item"><?php echo $LDModifiedBy ?>:
</td>
<td colspan=2 class="adm_input"><input  name="encoder" type="text" value="<?php  echo $HTTP_SESSION_VARS['sess_user_name']; ?>" size="30" readonly="true">
</nobr>
</td>
</tr>
<!-- end -->

<!--signature-->
<tr>
	<td class="adm_item"><?php echo $LDSigBy ?>: </td>
	<td colspan=2 class="adm_input">
		<!--added by Nick 4-4-2015 -->
		<div id="req-max-file-size">2mb Max File Size</div>
		<div id="req-resolution">50 x 200 Pixels Recommended Image Resolution</div>
		<div id="req-file-type">Acceptable File Types: JPEG</div>
		<div id="req-note"></div>
		<div id="upload-file-container">
			<?php
			$signaturePath = Personell::getPersonnelSignature($personell_nr,$root_path);
			?>
			<img id="signature" alt="" height="50" width="200" src="<?=$signaturePath?>"/><br/>
			<img id="loading" src="../../images/ajax_bar.gif" width="200"/>
			<button id="btn-browse-file" type="button">...</button>
			<button id="btn-delete-file" type="button">&times;</button>
		</div>
		<br/>
		<input id="upload-file" name="upload-file" accept=".jpg" type="file" style="display: none;"/>
	
	</td>
</tr>

<!--end-->

</table>
<p>
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="personell_nr" value="<?php echo $personell_nr; ?>">
<input type="hidden" name="sid" value="<?php echo $sid; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<input type="hidden" name="mode" value="save">
<input type="hidden" name="insurance_show" value="<?php echo $insurance_show; ?>">



<?php if($update) echo '<input type="hidden" name=update value=1>'; ?>
<input  type="image" <?php echo createLDImgSrc($root_path,'savedisc.gif','0') ?>  alt="<?php echo $LDSaveData ?>" align="absmiddle">
<a href="<?php echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?> alt="<?php echo $LDResetData ?>"   align="absmiddle"></a>
<!-- Note: uncomment the ff: line if you want to have a reset button  -->
<!--
<a href="javascript:document.aufnahmeform.reset()"><img <?php echo createLDImgSrc($root_path,'reset.gif','0') ?> alt="<?php echo $LDResetData ?>"   align="absmiddle"></a>
-->
<?php if($error==1)
echo '<input type="hidden" name="forcesave" value="1">
								<input  type="submit" value="'.$LDForceSave.'">';
 ?>
</form>

<?php if (!($newdata)) : ?>

<form action=<?php echo $thisfile; ?> method=post>
<input type="hidden" name="sid" value=<?php echo $sid; ?>>
<input type="hidden" name="personell_nr" value="<?php echo $personell_nr; ?>">
<input type="hidden" name="lang" value="<?php echo $lang; ?>">
<input type=submit value="<?php echo $LDNewForm ?>" >
</form>
<?php endif; ?>
<p>

<?php
}  // end of if !isset($pid...
?>
</ul>

<p>
</td>
</tr>
</table>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign page output to the mainframe template

$smarty->assign('sMainFrameBlockData',$sTemp);
 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');

?>
