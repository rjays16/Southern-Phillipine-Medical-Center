<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path."modules/registration_admission/ajax/order-psearch.common.php");
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org,
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

//$db->debug=1;

$thisfile=basename(__FILE__);

$cat = "pharma";
$title="Patient Records::Select patient";
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title");

 # Assign Body Onload javascript code
 $smarty->assign('sOnLoadJs','onLoad="init()"');

# Collect javascript code
ob_start();

#added by VAN 02-06-2012
#added by shandy for casenumber in cashier. 12/11/2013
$ref_source = $_GET['ref_source'];
$getcase = $_GET['var_target'];

if ($getcase)
{
	$case = "Case no:";
	//$shandy2 = "<input id='search_caseno' class='segInput' type='text' style='width:25%'; font: bold 12px Arial align='absmiddle' value='nooo' onBlur='DisabledSearch();' />";
	$case_no ='<input id="search_caseno" class="segInput" type="text" style="width:25%; font: bold 12px Arial" align="absmiddle" onBlur="DisabledSearch();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById(\'search_caseno\').value))) startAJAXSearch(\'search\',0); return false;;"/>';
}// end bby shandy

global $lang, $allow_updateBloodData;
?>


<script language="javascript" >
<!--
var AJAXTimerID=0;
var lastSearch="";

function init() {
	shortcut.add('ESC', closeMe,
		{
			'type':'keydown',
			'propagate':false,
		}
	);

	setTimeout("$('search').focus()",100);
}

function closeMe() {
	window.parent.cClick();
}

<?php
	$varArray = array(
		'var_pid'=>'',
		'var_rid'=>'',
		'var_encounter_nr'=>'',
		'var_discountid'=>'',
		'var_parent_discountid'=>'',
		'var_discount'=>'',
		'var_name'=>'',
		'var_addr'=>'',
		'var_clear'=>'',
		'var_history'=>'',
		'var_other'=>'',       # added by VAN 05-20-20101
		//'var_enctype'=>'',
		'var_enctype_show'=>'',
		'var_include_enc'=>'0',
		#added by VAN
		//'var_enctype'=>'',
		'var_location'=>'',
		'var_medico'=>'0',
		#added by Omick, January 15, 2009
		'var_gender'=>'',
		'var_age'=>'',
		#end omick
		#added by Omick, May 26, 2009
		'var_date_admitted'=>'',
		'var_room_ward'=>'',
		#end omick
		'var_adm_diagnosis'=>'',

		#added by cha, may 19,2010
		'var_dept_nr'=>'',
		'var_ward_nr'=>'',
		'var_room_nr'=>'',
		'var_exclude_mgh'=>'0',	#added June 1, 2010
		#end cha
	);

	foreach ($varArray as $i=>$v) {
		$value = $_REQUEST[$i];
		if (!$value) $value = $v;
		if (!is_numeric($value)) $value = "'$value'";
		echo "var $i=$value;\n";
	}
?>

function startAJAXSearch(searchID, page, e) {
		
		var includeEnc = var_include_enc ? '1' : '0';
		var searchEL = $(searchID);
		var excludeMGH = var_exclude_mgh ? 1 : 0;	//added by cha, june 1,2010
		var searchLastname = 0;
		var paramGetCase = '<?=$getcase?>';
		var searchText = searchEL.value;	

	if (paramGetCase) {
		
		var includeEnc = var_include_enc ? '1' : '0';
		var searchEL = $(searchID);
		var excludeMGH = var_exclude_mgh ? 1 : 0;	//added by cha, june 1,2010
		var searchLastname = 0;
		var includeEnc = '1';
	    var searchText = searchEL.value;
	   // var searchLastname = $('firstname-too').checked ? '1' : '0';
	    var characterCode;
		
		var scase_no = $('search_caseno').value;

		if(!scase_no) scase_no = '';

		if(searchEL.value == ''){
			if(scase_no != '') searchEL.value = '';
		}

		if(typeof e == 'undefined')
			characterCode = 13
			else {
				if(e && e.which) {
					characterCode = e.which;
				} else {
					characterCode = e.keyCode;
				}
			}
		//var searchText = searchEL.value;
    	if (searchEL && (characterCode == 13)) {
        	searchEL.style.color = "#0000ff";
	        if (AJAXTimerID) clearTimeout(AJAXTimerID);
	       		 $("ajax-loading").style.display = "";
	        	 $("person-list-body").style.display = "none";
	        
		    searchText = searchText.replace("'","\\'");
		if (scase_no == '')

            AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+","+includeEnc+")", 100);
        else
            AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchEL.value+"',"+page+","+searchLastname+","+includeEnc+","+scase_no+")", 100);
       		
	       // lastSearch = searchEL.value;
	       
   		}

	}else if (searchEL && searchEL.value.length >= 3) {
		
				searchEL.style.color = "#0000ff";
				if (AJAXTimerID) clearTimeout(AJAXTimerID);
					$("ajax-loading").style.display = "";
					$("person-list-body").style.display = "none";
					searchText = searchText.replace("'","\\'");

			AJAXTimerID = setTimeout("xajax_populatePersonList('"+searchID+"','"+searchText+"',"+page+","+searchLastname+","+includeEnc+")",100);
			lastSearch = searchEL.value;
		}
}


function updateControls() {
	var s = $('search').value;
	//var s =$('caseno').value;
	$('search-btn').disabled = (s.length < 3);
}

//added by VAN 01-29-10
function isValidSearch(key) {
        //added by VAN 02-06-2012
       // //for bloodbank only as per Mrs Angie Balayon's request
       var paramGetCase = '<?=$getcase?>';
       if(paramGetCase) {// -- CASE Number only ---------------------

       		if (typeof(key)=='undefined') return false;
		    var s=key.toUpperCase();
		    var skey =$('search').value;
		    if (skey=='')
		        {return (
		        /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
		        /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
		        /^\d{10,}$/.test(s)
		        );}
		    return (
			    /^[A-Z?\-\.]{2}[A-Z?\-\. ]*\s*,\s*[A-Z?\-\.]{2}[A-Z?\-\. ]*$/.test(s) ||
			    /^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
			    /^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
			    /^\d+$/.test(s)
		    );//--- end case number --------------------

       }else {// HRN/ NAME Patient only
	       	var ref_source = $('ref_source').value;
			if (typeof(key)=='undefined') return false;
			var s=key.toUpperCase();
	        
	        if (ref_source=='BB'){
	            return (
	                        /^\d+$/.test(s)
	            );
	        }else{
				return (
								/^[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*\s*,\s*[A-ZÑ\-\.]{2}[A-ZÑ\-\. ]*$/.test(s) ||
								/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(s) ||
								/^\d{1,2}\-\d{1,2}\-\d{4}$/.test(s) ||
								/^\d+$/.test(s)
						);
			}
       } //---------------- end CASE Number ------------------  
}



function DisabledSearch(){
	 var paramGetCase = '<?=$getcase?>';
	
	 var skey =$('search').value;
		if (paramGetCase) {
			
			var scase_no = $('search_caseno').value;
			    if (scase_no == '')
			        {var b=isValidSearch(document.getElementById('search').value);}
			    else if (skey == '')
			        {var b=isValidSearch(document.getElementById('search_caseno').value);}

			    document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
			    document.getElementById("search-btn").disabled = !b;

			}else {
				
				
			var b=isValidSearch(document.getElementById('search').value);
			document.getElementById("search-btn").style.cursor=(b?"pointer":"default");
			document.getElementById("search-btn").disabled = !b;
			}
}


//--------------------------

function endAJAXSearch(searchID) {
	var searchEL = $(searchID);
	if (searchEL) {
		$("ajax-loading").style.display = "none";
		$("person-list-body").style.display = "";
		searchEL.style.color = "";
	}
}

//added by VAN 03-03-08
function checkEnter(e,searchID){
	//alert('e = '+e);
	var characterCode; //literal character code will be stored in this variable

	if(e && e.which){ //if which property of event object is supported (NN4)
		e = e;
		characterCode = e.which; //character code is contained in NN4's which property
	}else{
		characterCode = e.keyCode; //character code is contained in IE's keyCode property
	}

	if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
		startAJAXSearch(searchID,0);
	}else{
		return true;
	}
}

// -->
</script>
<script type="text/javascript" src="<?=$root_path?>modules/registration_admission/js/person-search-gui.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
	<table width="98%" cellspacing="2" cellpadding="2" style="margin:1%">
		<tbody>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<table width="95%" border="0" cellpadding="0" cellspacing="0" style="font:bold 12px Arial; color:#2d2d2d; margin:1%">
						<tr>
							<td width="15%">
								Search person<br />
								<!--<a href="javascript:gethelp('person_search_tips.php')" style="text-decoration:underline">Tips & tricks</a>-->
							</td>
							<td valign="middle" width="*">

								<!--<input id="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="if (this.value.length >= 3) startAJAXSearch(this.id,0)"  onKeyPress="checkEnter(event,this.id)"/>-->
								<!--<input id="search" class="segInput" type="text" style="width:60%; font: bold 12px Arial" align="absmiddle" onkeyup="updateControls(); if (event.keyCode == 13) startAJAXSearch(this.id,0)" onclick="updateControls()"/>-->
								<input id="search" class="segInput" type="text" style="width:50%; font: bold 12px Arial" align="absmiddle" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('search').value))) startAJAXSearch(this.id,0); " onBlur="DisabledSearch();"/>
								<!--added by shandy-->
								<?php echo $case ?>	<?php echo $case_no ?>

								<input class="jedInput" id="search-btn" type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" disabled="disabled" /><br />
								
							</td>
						</tr>
						<!-- <tr>
							<td></td>
							<td><input type="checkbox" id="firstname-too" checked> Search for first names too.</td>
						</tr> -->	<!-- commented out in accordance with search code changes; aug.5,2008; pet -->
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; width:100%; background-color:#e5e5e5">
						<table id="person-list" class="segList" cellpadding="0" cellspacing="0" width="100%">
							<thead>
								<tr class="nav">
									<th colspan="9">
										<div id="pageFirst" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,FIRST_PAGE)">
											<img title="First" src="<?= $root_path ?>images/start.gif" border="0" align="absmiddle"/>
											<span title="First">First</span>
										</div>
										<div id="pagePrev" class="segDisabledLink" style="float:left" onclick="jumpToPage(this,PREV_PAGE)">
											<img title="Previous" src="<?= $root_path ?>images/previous.gif" border="0" align="absmiddle"/>
											<span title="Previous">Previous</span>
										</div>
										<div id="pageShow" style="float:left; margin-left:10px">
											<span></span>
										</div>
										<div id="pageLast" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,LAST_PAGE)">
											<span title="Last">Last</span>
											<img title="Last" src="<?= $root_path ?>images/end.gif" border="0" align="absmiddle"/>
										</div>
										<div id="pageNext" class="segDisabledLink" style="float:right" onclick="jumpToPage(this,NEXT_PAGE)">
											<span title="Next">Next</span>
											<img title="Next" src="<?= $root_path ?>images/next.gif" border="0" align="absmiddle"/>
										</div>
									</th>
								</tr>
								<tr>
									<th width="8%">HRN</th>
									<th width="4%">Sex</th>
									<th width="18%">Lastname</th>
									<th width="18%">Firstname</th>
									<th width="18%">Middlename</th>
									<th width="10%" style="font-size:11px" nowrap="nowrap">Date of Birth</th>
									<th width="10%">Confinement</th>
									<th width="10%">Class</th>
									<th width="1%">Options</th>
								
								</tr>
							</thead>
							<tbody id="person-list-body">
								<tr>
									<td colspan="9">No such person exists...</td>
								</tr>
							</tbody>
						</table>
						<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
					</div>
				</td>
			</tr>
		</tbody>
	</table>


	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
	<input type="hidden" name="from_dialysis" id="from_dialysis" value="<?=$_GET["from_dialysis"]?>">
    <input type="hidden" name="ref_source" id="ref_source" value="<?=$ref_source?>">
    <input type="hidden" name="allow_updateBloodData" id="allow_updateBloodData" value="<?=$allow_updateBloodData?>">

<?php

# Workaround to force display of results  form
$bShowThisForm = TRUE;

# If smarty object is not available create one
if(!isset($smarty)){
	/**
 * LOAD Smarty
 * param 2 = FALSE = dont initialize
 * param 3 = FALSE = show no copyright
 * param 4 = FALSE = load no javascript code
 */
	include_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common',FALSE,FALSE,FALSE);

	# Set a flag to display this page as standalone
	$bShowThisForm=TRUE;
}

?>

<form action="<?php echo $breakfile?>" method="post">
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
</form>
<?php if ($from=="multiple")
echo '
<form name=backbut onSubmit="return false">
<input type="hidden" name="sid" value="'.$sid.'">
<input type="hidden" name="lang" value="'.$lang.'">
<input type="hidden" name="userck" value="'.$userck.'">
</form>
';
?>
</div>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>
