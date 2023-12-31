<?php
#edited by VAN 03-18-08
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
#require($root_path."modules/laboratory/ajax/lab-new.common.php");
#require($root_path.'modules/nursing/ajax/nursing-station-radio-common.php');

require($root_path.'modules/special_lab/ajax/splab-service-tray.common.php');

require($root_path.'include/inc_environment_global.php');

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_radio_user';
require_once($root_path.'include/inc_front_chain_lang.php');
$thisfile=basename(__FILE__);
$title=$LDLab;
$breakfile=$root_path."modules/radiology/seg-close-window.php".URL_APPEND."&userck=$userck";

# Create radiology object
require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
$srvObj=new SegLab();

#added by VAN 03-10-2011
require_once($root_path.'include/care_api_classes/class_encounter.php');
$enc_obj=new Encounter;

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
 $smarty->assign('sToolbarTitle',"$title $LDLabDb $LDSearch");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$title $LDLabDb $LDSearch");

 # Assign Body Onload javascript code
 #$smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

	$smarty->assign('sOnLoadJs','onLoad="preset();"');
 # Collect javascript code
 ob_start();

	$area = $_GET['area'];

#added by VAN 03-09-2011
	global $db;
	$encounter_nr = $_GET['encounter_nr'];
	$pid = $_GET['pid'];

	if (!$impression) {
		$impression = '';

		$impression = $enc_obj->getLatestImpression($pid, $encounter_nr);

	}
#----------


?>
<script type="text/javascript">
<!--
function preset(){
	//xajax_populateLabServiceList('search','*',0);
	//var dr_nr = '<?=$dr_nr?>';
//	var dept_nr = '<?=$dept_nr?>';
	var dr_nr, dept_nr, is_dr;
	var is_dr_get = '<?=$is_dr?>';
	var pdr_nr = window.parent.$('current_att_dr_nr').value;
	var pdept_nr = window.parent.$('current_dept_nr').value;

	//added by VAN 05-26-2011
	var login_user = window.parent.$('login_user').value;
	var is_dr_post = window.parent.$('is_dr').value;

	if (is_dr_get==1)
		is_dr = is_dr_get;
	else
		is_dr = is_dr_post;
	//---------------------

	// edited by VAN 03-09-2011
	var impression_prev = '<?=str_replace("\r\n",'',$impression);?>';
	var impression;

	if (impression_prev=='')
		impression = window.parent.$('impression').value;
	else
		impression = impression_prev;

	var user_origin = window.parent.$('user_origin').value;

	startAJAXSearch('search',0);
	if (user_origin=='lab' || user_origin == 'splab') {
        $('section').style.display = "";
    }
	else{
		$('section').style.display = "none";
	}

	if (!is_dr){
		 dr_nr = '';
		 dept_nr = '';
	}else{
		 if (is_dr_post==1)
				dr_nr = login_user;
		 else
				dr_nr = '<?=$_GET["dr_nr"]?>';

		 dept_nr = '<?=$dept_nr?>';
	}

	if (dr_nr==""){
		if (pdr_nr=="")
			dr_nr = 0
		else
			dr_nr = pdr_nr
	}//else
		//dr_nr = 0

	if (dept_nr==""){
		if (pdept_nr=="")
			dept_nr = 0
		else
			dept_nr = pdept_nr
	}else
		dept_nr = 0

 $('clinical_info').value = impression;
 //alert(dr_nr+" - "+dept_nr);
	//xajax_setALLDepartment(0);	//set the list of ALL departments
	//xajax_setALLDepartment(dept_nr);	//set the list of ALL departments
	//xajax_setDoctors(dept_nr,dr_nr);
	//xajax_setDoctors(0,0);	//set the list of ALL doctors from ALL departments
	//xajax_setDoctors(0,dr_nr);	//set the list of ALL doctors from ALL departments
	
	//added by jane 10/24/2013
	var ward_nr = window.parent.$('ward_nr').value;
	if(ward_nr==""||ward_nr==0){
		$('tr_requesting_dept').style.display = '';
		$('tr_requesting_loc').style.display = 'none';
		xajax_setALLDepartment(dept_nr);
		xajax_setDoctors(dept_nr,dr_nr);
	}
	else{
		$('tr_requesting_dept').style.display = 'none';
		$('tr_requesting_loc').style.display = '';
		xajax_setAllLocation(ward_nr);
		xajax_setDoctors(0,dr_nr);
	}
	//added by jane 11/11/2013
	$('request_doctor_out').style.display = '';
	if(dr_nr!=0){
		$('request_doctor_out').style.display = 'none';
	}
		

	
}
// -->
</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/splab-service-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>

<?php
$xajax->printJavascript($root_path.'classes/xajax');
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

if ($_GET['ref_source'])
	$ref_source = $_GET['ref_source'];

switch ($ref_source){
	case 'LB' :
							$caption = 'No such laboratory service exists...';
							break;
	case 'BB' :
							$caption = 'No such blood bank service exists...';
							break;
	case 'SPL' :
							$caption = 'No such special laboratory service exists...';
							break;
	case 'IC' :
							$caption = 'No such industrial clinic laboratory service exists...';
							break;
	default :
							$caption = 'No such laboratory service exists...';
							break;
}

ob_start();

?>

	<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>

			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
							<tr>
								<td class="segPanelHeader" colspan="2">
									Request Details
								</td>
							</tr>
							<tr id="tr_requesting_dept" ><!--added id to be used in jquery by jane 10/24/2013-->
								<td valign="top" width="30%" align="right"><strong>Requesting Dept</strong></td>
								<td align="left">
									<select name="request_dept" id="request_dept" onChange="jsSetDoctorsOfDept();">
									</select>
								</td>
							<tr id="tr_requesting_loc" ><!-- added table row by jane 10/24/2013-->
								<td valign="top" width="30%" align="right"><strong>Location Ward</strong></td>
								<td align="left">
									<select name="request_loc" id="request_loc">
									</select>
								</td>
							<tr>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right"><strong>Requesting Doctor</strong></td>
								<td align="left">
									<select name="request_doctor_in" id="request_doctor_in" onChange="jsSetDepartmentOfDoc();">
									</select>
									<br>
									<input type="text" name="request_doctor_out" id="request_doctor_out" size=40 onBlur="trimString(this);" value="">
									<input type="hidden" name="request_doctor" id="request_doctor" value="">
									<input type="hidden" name="request_doctor_name" id="request_doctor_name" value="">
									<input type="hidden" name="is_in_house" id="is_in_house" value="">

									<script language="javascript">
										//var dr_nr = '<?=$dr_nr?>';
//										var dept_nr = '<?=$dept_nr?>';

//										if (dr_nr=="")
//											dr_nr = 0;

//										if (dept_nr=="")
//											dept_nr = 0;

										//xajax_setALLDepartment(0);	//set the list of ALL departments
//										xajax_setALLDepartment(dept_nr);	//set the list of ALL departments
										//xajax_setDoctors(0,0);	//set the list of ALL doctors from ALL departments
//										xajax_setDoctors(0,dr_nr);	//set the list of ALL doctors from ALL departments
									</script>
								</td>
							</tr>
							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Clinical Impression</strong>
								</td>
								<td align="left">
									<textarea name="clinical_info" id="clinical_info" cols=30 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"><?=$impression?></textarea>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr id="section">
				<td  style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Laboratory Service Section &nbsp;&nbsp;&nbsp;&nbsp;<select name="parameterselect2" id="parameterselect2" onChange="document.getElementById('search').value=''; startAJAXSearch('search',0)">
								<?php
                                        if($_GET['ref_source'] == 'LB') {
                                            echo '<option value="0">All Laboratory Service Section</option>';
                                            $cond = "group_code NOT IN ('B','SPL','IC','SPC','CATH','ECHO','POC')";
                                        }
                                        else {
                                            echo '<option value="0">All Special Laboratory Service Section</option>';
                                            $cond = "group_code IN ('SPC','SPL','CATH','ECHO','POC')";
                                        }

										$all_labgrp=&$srvObj->getLabServiceGroups2('', $cond);    
										if(!empty($all_labgrp)&&$all_labgrp->RecordCount()){
											while($result=$all_labgrp->FetchRow()){
												if(isset($parameterselect)&&($parameterselect==$result['group_code'])){
													echo "<option value=\"".$result['group_code']."\" selected>".$result['name']." \n";
																		 }else{
																			 echo "<option value=\"".$result['group_code']."\">".$result['name']." \n";
																		 }
											}
										}
								?>
					</select>
						<img src="../../gui/img/common/default/redpfeil_l.gif">
					</div>
				</td>
			</tr>
			<tr>
				<td style="font:bold 12px Arial; background-color:#e5e5e5; color: #2d2d2d" >
					<div style="padding:4px 2px; padding-left:10px; ">
						Search Laboratory Test <input id="search" name="search" class="segInput" type="text" style="width:55%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="$('search2').value=this.value; if (this.value.length >= 3) startAJAXSearch(this.id,0)" onKeyPress="checkEnter(event,this.id)"/>
                        <input id="search2" name="search2" class="segInput" type="hidden" style="width:55%; margin-left:10px; font: bold 12px Arial" align="absmiddle"/>
						<input type="image" id="search_img" name="search_img" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0);return false;" align="absmiddle" />
						<!--Search Request <input id="search" name="search" class="segInput" type="text" disabled style="width:51.5%; margin-left:10px; font: bold 12px Arial" align="absmiddle" onkeyup="startAJAXSearch(this.id)" />
						<img src="../../gui/img/common/default/redpfeil_l.gif">-->
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden; width:100%; background-color:#e5e5e5">
						<table class="segList" cellpadding="1" cellspacing="1" width="100%">
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
									<th width="*" align="left">&nbsp;&nbsp;Name/Description</th>
									<th width="23%" align="right">&nbsp;&nbsp;Code (<font style="font-size:11px">Group Code</font>)</th>
									<th style="font-size:11px" width="15%" align="right">Cash&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<th style="font-size:11px" width="15%" align="right">Charge&nbsp;&nbsp;&nbsp;&nbsp;</th>
									<!--<th width="15%">Discount Type</th>-->
									<th width="8%"></th>
								</tr>
							</thead>
						</table>
						</div>
						<div style="display:block; border:1px solid #8cadc0; overflow-y:scroll; height:160px; width:100%; background-color:#e5e5e5">
							<table id="request-list" class="segList" cellpadding="1" cellspacing="1" width="100%">
								<tbody>
									<tr>
										<td colspan="6" style="font-weight:bold"><?=$caption?></td>
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

	<input type="hidden" name="area" id="area" value="<?=$area?>">
	<input type="hidden" name="dr_nr" id="dr_nr" value="<?=$_GET['dr_nr']?>" />

	<input type="hidden" name="caption" id="caption" value="<?=$caption?>">

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
