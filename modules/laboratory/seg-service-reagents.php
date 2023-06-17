<?php
	# Start Smarty templating here
 /**
 * LOAD Smarty
 */
	
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme
 	error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
	require('./roots.php');
	

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
	$lang_tables[] = 'departments.php';
	define('LANG_FILE','lab.php');
	$local_user='ck_lab_user';
	define('NO_2LEVEL_CHK',1);

	require($root_path.'include/inc_environment_global.php');
	require_once($root_path.'include/inc_front_chain_lang.php');
	require($root_path.'modules/laboratory/ajax/lab-admin.common.php');

	# Create global config object
	require_once($root_path.'include/care_api_classes/class_globalconfig.php');
	require_once($root_path.'include/inc_date_format_functions.php');

	$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
	$glob_obj->getConfig('refno_%');
	if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
	$date_format=$GLOBAL_CONFIG['date_format'];

	$phpfd=$date_format;
	$phpfd=str_replace("dd", "%d", strtolower($phpfd));
	$phpfd=str_replace("mm", "%m", strtolower($phpfd));
	$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));

	$title="Laboratory";

	#$breakfile = "labor.php";
	#$breakfile = $root_path."modules/laboratory/labor.php";
	$thisfile=basename(__FILE__);

	# Create radiology object
	require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
    $srvObj=new SegLab();
	
		
	require_once($root_path.'gui/smarty_template/smarty_care.class.php');
	$smarty = new smarty_care('common');

	if (!isset($popUp) || !$popUp){
		if (isset($_GET['popUp']) && $_GET['popUp']){
			$popUp = $_GET['popUp'];
		}
		if (isset($_POST['popUp']) && $_POST['popUp']){
			$popUp = $_POST['popUp'];
		}
	}
	
	
	if ($_GET['service_code'])
		$service_code = $_GET['service_code'];
		#echo " service_code = ".$service_code;
    /*
	$_POST['create_id'] = $HTTP_SESSION_VARS['sess_user_name'];
	$_POST['create_dt'] = date('Y-m-d H:i:s');
	
	$_POST['modify_id'] = $HTTP_SESSION_VARS['sess_user_name'];
	$_POST['modify_dt'] = date('Y-m-d H:i:s');
	
	$_POST['history'] = "Create ".date('Y-m-d H:i:s')." ".$HTTP_SESSION_VARS['sess_user_name']." \n";
	
	*/
	
	if ($_POST["reagent_code"]!=NULL){
				
		$reagent_list = array();
		foreach ($_POST["reagent_code"] as $i=>$v) {
			$reagent_list[] = array($_POST["reagent_code"][$i],$_POST["item_qty"][$i],$_POST["unit_id"][$i],$_POST["is_unitperpc"][$i]);		
		}
	}			
	#print_r($reagent_list);
	
	$srvObj->clearReagentList($_POST["service_code"]);
	$srvObj->addReagent($_POST,$reagent_list);
	
	$saveOK = $srvObj->saveOK;
	#echo "cmode = ".$dependent_Obj->saveOK;
	
	if ($saveOK){
		if ($mode=='save'){
			$smarty->assign('sysInfoMessage',"Reagents in this laboratory examination are successfully added.");
		}elseif ($mode=='update'){
			$smarty->assign('sysInfoMessage',"Reagents in this laboratory examination are successfully updated.");				
		}
		#echo "<br>mode = ".$mode;
	}else
		$errorMsg='<font style="color:#FF0000">'.$srvObj->getErrorMsg().'</font>';
	
 # Title in the title bar
 $smarty->assign('sToolbarTitle',"$title ::  Reagents");

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# CLOSE button for pop-ups
 $smarty->assign('breakfile','javascript:window.parent.cClick();');
 $smarty->assign('pbBack','');

 # Window bar title
 $smarty->assign('sWindowTitle',"$title :: Reagents");

 # Assign Body Onload javascript code
 
 $onLoadJS='onLoad="preset();"';
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code
ob_start();
	 # Load the javascript code
    $xajax->printJavascript($root_path.'classes/xajax-0.2.5');	 
?>


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

<script type="text/javascript">
<!--
OLpageDefaults(BGCLASS,'olbg', CGCLASS,'olcg', FGCLASS,'olfg',
 CAPTIONFONTCLASS,'olcap', CLOSEFONTCLASS,'olclo', TEXTFONTCLASS,'oltxt');
//-->
</script>

<style type="text/css">
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
.olfgright {text-align: right;}
.olfgjustify {background-color:#cceecc; text-align: justify;}

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>

			<!-- START for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->
<script type="text/javascript" language="javascript">
<?php
	require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css">
<script language="javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script language="javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script language="javascript" src="<?=$root_path?>js/dtpick_care2x.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
			<!-- END for setting the DATE (NOTE: should be IN this ORDER...i think soo..) -->

<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="js/seg-service-reagents.js?t=<?=time()?>"></script>

<script type="text/javascript" language="javascript">
<!--
	var trayItems = 0;
	
	function preset(){
		//alert('preset = '+$('service_code').value);
		xajax_populateServiceReagentsList($('service_code').value);
	}
-->
</script>

<?php
	if ($popUp=='1'){
		echo $reloadParentWindow;
	}
	$sTemp = ob_get_contents();
	ob_end_clean();
	$smarty->append('JavaScript',$sTemp);
	
	if ($_GET['service_code'])
		$service_code = $_GET['service_code'];
	else
		$service_code = $_POST['service_code'];	
	#echo "code = ".$service_code;
	
	$row = $srvObj->getServiceInfo($service_code);
	#echo "sql = ".$srvObj->sql;
	
	$smarty->assign('sServiceName','<input class="segInput" id="ordername" name="ordername" type="text" size="50" value="'.$row['name'].'" style="font:bold 12px Arial;" readonly>');
	$smarty->assign('sServiceCode','<input class="segInput" id="service_code" name="service_code" type="text" size="50" value="'.$row['service_code'].'" style="font:bold 12px Arial;" readonly>');
	
	
    $smarty->assign('sBtnAddItem','<img type="image" name="btnAdd" id="btnAdd" src="'.$root_path.'images/btn_add_reagent.gif" border="0" style="cursor:pointer;"
			onclick="return overlib(
				OLiframeContent(\'../../modules/laboratory/seg-select-reagents.php\', 600, 410, \'fOrderTray\', 1, \'auto\'),
					WIDTH,435, TEXTPADDING,0, BORDER,0, 
					STICKY, SCROLL, CLOSECLICK, MODAL, 
					CLOSETEXT, \'<img src='.$root_path.'/images/close.gif border=0 >\',
					CAPTIONPADDING,4, 
					CAPTION,\'Add reagents\',
					MIDX,0, MIDY,0, 
					STATUS,\'Add reagents\');"
			onmouseout="nd();">');

$smarty->assign('sBtnEmptyList','<img type="image" name="btnEmpty" id="btnEmpty" src="'.$root_path.'images/btn_emptylist.gif" border="0" style="cursor:pointer;" onclick="emptyTray();"></a>');


 $smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'?popUp='.$popUp.'" method="POST" name="inputform" id="inputform" onSubmit="return checkRequestForm()">');
 $smarty->assign('sFormEnd','</form>');
 
?>
<?php
ob_start();
$sTemp='';

$sTemp = ob_get_contents();
ob_end_clean();
/*
if ($mode=='update'){
	$smarty->assign('sIntialRequestList',$sTemp);
}
*/
ob_start();
$sTemp='';
?>
	<input type="hidden" name="submitted" value="1">
	<input type="hidden" name="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck?>">  
<!--  
	<input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
-->
	<input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
	<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
	<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
	<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
	<input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
	
	
	<input type="hidden" name="mode" id="mode" value="<?=$mode?$mode:'save'?>">
	<input type="hidden" name="popUp" id="popUp" value="<?=$popUp?$popUp:'0'?>">
	<input type="hidden" name="encoder_id" id="encoder_id" value="<?php echo $HTTP_SESSION_VARS['sess_login_personell_nr']; ?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

$smarty->assign('sHiddenInputs',$sTemp);
#if (($mode=="update") && ($popUp!='1')){
	$sBreakImg ='cancel.gif';
	$smarty->assign('sBreakButton','<img type="image" '.createLDImgSrc($root_path,$sBreakImg,'0','center').' align="center" alt="'.$LDBack2Menu.'" onclick="window.parent.cClick();" style="cursor:pointer">');
/*}elseif ($popUp!='1'){
	$sBreakImg ='close2.gif';	
	$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
}*/

$smarty->assign('sContinueButton','<img type="image" name="btnSubmit" id="btnSubmit" src="'.$root_path.'images/btn_submitorder.gif" align="center" style="cursor:pointer" onclick="if (confirm(\'Process this transaction?\')) if (checkRequestForm()) document.inputform.submit()">');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','laboratory/service_reagents.tpl');
$smarty->display('common/mainframe.tpl');

?>