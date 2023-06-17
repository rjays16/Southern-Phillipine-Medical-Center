<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path.'modules/radiology/rad-define-variable.php');
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
define('NO_2LEVEL_CHK',1);

#added by VAN 11-17-2011
global $allow_accessCT, $allow_accessUTZ, $allow_accessOBGYNEUTZ, $allow_accessMRI, $allow_accessXRAY, $is_system_admin;

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;
#Added by Matsuu 03042018
require_once($root_path . 'include/care_api_classes/class_acl.php');
require_once($root_path . 'include/care_api_classes/class_permission.php');
$permission = new Permission();
$login_id = $permission->getPermssionId($_SESSION['sess_login_personell_nr']);
$objAcl = new Acl($login_id);
$allow_accessOBC = $objAcl->checkPermissionRaw('_a_1_radioONC');
#Ended by Matusu 03042018

if (!empty($HTTP_SESSION_VARS['sess_login_userid']))
   $seg_user_name = $HTTP_SESSION_VARS['sess_login_userid'];
else
   $seg_user_name = $HTTP_SESSION_VARS['sess_temp_userid'];
  
$dept_belong = $dept_obj->getUserDeptInfo($seg_user_name); 

#--------------------


$lang_tables[]='search.php';
$lang_tables[]='actions.php';
define('LANG_FILE','lab.php');
#$local_user='ck_lab_user';
$local_user='ck_radio_user';   # burn added : September 24, 2007
require_once($root_path.'include/inc_front_chain_lang.php');

$toggle=0;

#$append=URL_APPEND."&target=$target&noresize=1&user_origin=$user_origin";
$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Oct. 3, 2006
if($_GET['ob']){
  $obgy = "&ob=OB";
}
$breakfile="radiolog.php$append".$obgy;   # burn added: Oct. 2, 2006
$entry_block_bgcolor="#efefef";   # burn added: Oct. 2, 2006
$entry_border_bgcolor="#fcfcfc";   # burn added: Oct. 2, 2006
$entry_body_bgcolor="#ffffff";   # burn added: Oct. 2, 2006

$breakfile=$root_path.'modules/radiology/'.$breakfile;   # burn added: Oct. 2, 2006
# $breakfile=$root_path.'modules/nursing/'.$breakfile;   
$thisfile=basename(__FILE__);
# Data to append to url
$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

#echo "radiology_done_request.php : target = '".$target."' <br> \n";
//echo "radiology/radiology_undone_request.php : mode = '".$mode."' <br> \n";
require($root_path.'modules/radiology/ajax/radio-done-request.common.php');

//echo $target;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('nursing');

# Title in toolbar
# $smarty->assign('sToolbarTitle', $LDTestRequest." - ".$LDSearchPatient);
 $smarty->assign('sToolbarTitle',($_GET['ob']=='OB' ? "OB-GYN :: Archive of Done Requests" : "Radiology  :: Archive of Done Requests"));


	# hide back button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('request_search.php')");

 # href for close button
/*   burn commented ; September 19, 2007
 if($HTTP_COOKIE_VARS["ck_login_logged".$sid]) $smarty->assign('breakfile',$root_path.'main/startframe.php'.URL_APPEND);
	else  $smarty->assign('breakfile',$breakfile);
*/
$smarty->assign('breakfile',$breakfile);

 # Window bar title
# $smarty->assign('sWindowTitle',$LDTestRequest." - ".$LDSearchPatient);
 $smarty->assign('sWindowTitle',($_GET['ob']=='OB' ? "OB-GYN :: Archive of Done Requests" : "Radiology  :: Archive of Done Requests"));

# Body onload javascript code
$smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select();DisabledSearch();"');

ob_start();

echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/radio-done-request-gui.js\"></script>";
?>

<!---------added by VAN----------->
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
.olfgleft {background-color:#cceecc; text-align: left;}

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
<!--  Dojo script function for undone request -->
<script language="javascript">
 //load eventOnClick
 dojo.addOnLoad(eventOnClick);
</script>

<?php

$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$tmp1 = ob_get_contents();
ob_end_clean();
$smarty->assign('yhScript', $tmp1);

# Collect extra javascript code

ob_start();

?>
<!-- commented by VAN 06-28-08 -->
<!--<ul>-->
	<table width=100% border=0 cellpadding="0" cellspacing="0">
		<tr bgcolor="<?php echo $entry_block_bgcolor ?>" >
			<td>
				<p><br>			
				<ul>
					<table width="474" border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
						<tr>
							<td>
<?php
#								$searchmask_bgcolor="#f3f3f3";
#								include($root_path.'include/inc_test_request_searchmask.php');
?>
								<table border=0 cellspacing=5 cellpadding=5 width="105%">			
									<tr bgcolor="#f3f3f3">
										<td>Enter the search key
										<form name="searchform" onSubmit="return false;">
											<!--<input type="text" name="searchkey" id="searchkey" size=40 maxlength=40 onChange="trimStringSearchMask();" onKeyUp="if (this.value.length >= 3){chkSearch();}" value="">-->
											<!--<input type="text" name="searchkey" id="searchkey" style="width:60%; background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" onChange="trimStringSearchMask();" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('searchkey').value))) chkSearch(); " onBlur="DisabledSearch();">-->
                                            <input class="segInput" id="searchkey" name="searchkey" type="text" size="30" onChange="trimStringSearchMask(this);" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('searchkey').value))){$('skey').value=$('searchkey').value;  chkSearch();} " onBlur="DisabledSearch();"/>
											<br>
											 <?php if($_GET['ob']=='OB'){?>
            <span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              (RID, HRN, Name)
            </span>
            <?php  }else{ ?>
            <span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
              (Batch No., RID, HRN, Name, Case no., Date of request(MM/DD/YYYY), Birthdate)
            </span>
            <?php }?>
<!--
											<img <?php echo createLDImgSrc($root_path,'searchlamp.gif','0','absmiddle') ?> onClick="chkSearch();">
-->
											<!--<input type="image" class="jedInput" id="search-btn" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="chkSearch();" disabled="disabled">-->
                                            <input type="image" class="jedInput"  id="search-btn" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="$('skey').value=$('searchkey').value; handleOnclick();" disabled="disabled">
										</form>
										</td>
									</tr>				
								</table>
							</td>
						</tr>
					</table>

					<p>
					<a href="<?php	echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
					<p>
					<span id='textResult'></span>
<?php
 $obgyne = $_GET['ob'];
  #added by VAN 11-17-2011  
  #echo "he = ".$allow_accessCT." = ".$allow_accessUTZ." = ".$allow_accessXRAY." = ".$allow_accessOBGYNEUTZ;
 $dept_nr_list = "";
  $waccess = 0;

        $session = $_SESSION['sess_login_personell_nr'];
        $strSQL = "SELECT permission,login_id from care_users WHERE personell_nr=".$db->qstr($session);
        $login_id = "";
        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){
                    $login_id = $row['login_id'];
                }
            }
        }
        $objAcl = new Acl($login_id);
       $allow_accessOBGYNE = $objAcl->checkPermissionRaw('_a_1_OBGyneOBGYNEUTZ');
    // Editado por Matsuu for radiology and obgyne 03042017
  if($obgyne=='OB'){
    $dept_nr_list .= ",".OB_GYNE_Dept.",";
    $waccess = $waccess + 1;
  }
    else{
      if ($allow_accessSysAd){
       $allow_accessCT = 1; 
       $allow_accessUTZ = 1;
       $allow_accessOBGYNEUTZ = 1;
       $allow_accessMRI = 1;
       $allow_accessXRAY = 1;
       $allow_accessMAMO = 1; # added by: syboy 07/27/2015 -> Mamography department
     $allow_accessOBC = 1; #Added by Matsuu 03042018 -> Oncology Department
    }
    if ($allow_accessCT){
       $dept_nr_list .= ",".Computed_Tomography.","; 
       $waccess = $waccess + 1;
    }
    if ($allow_accessUTZ){
       $dept_nr_list .= ",".Ultrasound.",";
       $waccess = $waccess + 1;
    } 
    if ($allow_accessOBGYNEUTZ){
       $dept_nr_list .= ",".OB_GYNE_Dept.",";
       $waccess = $waccess + 1;
    }
    if ($allow_accessMRI){
       $dept_nr_list .= ",".MRI.",";
       $waccess = $waccess + 1;
    }
    if ($allow_accessXRAY){
       $dept_nr_list .= ",".General_Radiography.",".Special_Procedures.",";   
       $waccess = $waccess + 1;
    } 
    # added by: syboy 07/27/2015 -> Mamography department
    if ($allow_accessMAMO){
       $dept_nr_list .= ",".MAMO.",";
       $waccess = $waccess + 1;
    }
 
 #Added by Matsuu 03042018 -> Oncology Department
  if($allow_accessOBC){
    $dept_nr_list .= ",'".ONCOLOGY."',";
     $waccess = $waccess + 1;
  }
  #Ended here..
   }
  
  if (($dept_belong['dept_nr'])&&($waccess==0)){
    $dept_nr_list = "'".$dept_belong['dept_nr']."'";
    $waccess = $waccess + 1;
    #die('h - '.$dept_belong['dept_nr']);
  }
      
  $dept_nr_list = substr($dept_nr_list,1,strlen($dept_nr_list)-2);
  $dept_nr_list = str_replace(",,",",",$dept_nr_list);
?>
	
<!--  Tab Container for radiology request list -->
<input type="hidden" id="waccess" name="waccess" value="<?=$waccess?>">
<input type="hidden" id="obgyne" name="obgyne" value="<?=$_GET['ob']?>">
<input type="hidden" id="obdept" name="obdept" value="<?=OB_GYNE_Dept?>">
<input type="hidden" id="dp_nr" name="dp_nr" value="<?=$dept_belong['dept_nr']?>">
<div id="tbContainer"  dojoType="TabContainer" style="width:100%; height:28.3em; display:block; border:1px" align="center">
<?php  if ($obgyne!='OB'){#Added by Matsu for radiology and obgyne 03042017?>
    <div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none;overflow:auto;display:block; border:1px solid #8cadc0;">
        <!--  Table:list of request -->
        <table id="Ttab0" class="segList" border="0" cellpadding="0" cellspacing="0">
            <!-- List of all radiology request -->
        </table>
        <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
    </div>
    <!-- tabcontent for radiology sub-department -->
<?php
} #Ended by Matsuu for radiology and obgyne 03042017                                                       
#Department object

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

#$radio_sub_dept=$dept_obj->getSubDept($dept_nr);
if($obgyne=='OB'){
$radio_sub_dept=$dept_obj->getDeptServCode($dept_nr_list);
}else{
  $radio_sub_dept=$dept_obj->getSubDept2($dept_nr,$dept_nr_list);
}
// var_dump($dept_obj->sql);exit();

if($dept_obj->rec_count || ($obgyne=='OB')){
    $dept_counter=2;
    while ($rowSubDept = $radio_sub_dept->FetchRow()){
        #commented by VAN 03-03-08
        /*
        if (trim($rowSubDept['name_short'])!=''){
            $text_name = trim($rowSubDept['name_short']);
        }elseif (trim($rowSubDept['id'])!=''){
            $text_name = trim($rowSubDept['id']);
        }else{
            $text_name = trim($rowSubDept['name_formal']);
        }
        */
        #$text_name = trim($rowSubDept['name_formal']);
         if (trim($rowSubDept['name_short'])!=''){        
            $text_name = trim($rowSubDept['name_short']);
        }elseif (trim($rowSubDept['id'])!=''){
            $text_name = trim($rowSubDept['id']);
        }else{
            #$text_name = trim($rowSubDept['name_formal']);
            $text_name = trim($rowSubDept['name_formal']);
        }
?>
<div dojoType="ContentPane" widgetId="tab<?=$rowSubDept['nr']?>" label="<?=$text_name?>" style="display:none;overflow:auto; border:1px solid #8cadc0;" >
        <table id="Ttab<?=$rowSubDept['nr']?>" cellpadding="0" cellspacing="0" class="segList">
            <!-- List of Radiology Requests  -->
        </table>
        <img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
        <?php if($dept_counter==2){ ?> <input type="hidden" name="OB_defaulter" id="OB_defaulter" value="tab<?=$rowSubDept['nr']?>"> <?php } ?>
</div>
<?php
        $dept_counter++;
    } # end of while loop
}   # end of if-stmt 'if ($dept_obj->rec_count)'
?>
</div>

<!--			
	<input type="hidden" name="skey" id="skey" value="<?= $HTTP_SESSION_VARS['sess_searchkey']? $HTTP_SESSION_VARS['sess_searchkey']:'*'?>"> 
-->
	<input type="hidden" name="skey" id="skey" value="*"> 
	<input type="hidden" name="smode" id="smode" value="<?= $mode? $mode:'search' ?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">

	<input type="hidden" name="sid" id="sid" value="<?php echo $sid; ?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
	<input type="hidden" name="noresize" id="noresize" value="<?php echo $noresize; ?>">
	<input type="hidden" name="target"  id="target" value="<?php echo $target; ?>">
	<input type="hidden" name="user_origin" id="user_origin" value="<?php echo $user_origin; ?>">
	<input type="hidden" name="mode" id="mode" value="search">
	
	<input type="hidden" name="is_doctor" id="is_doctor" value="0">
	<input type="hidden" name="encounter_nr" id="encounter_nr" value="">
	<input type="hidden" name="is_perpatient" id="is_perpatient" value="0">
		 <!--
	<table>
		<tr align="center" style="width:auto">
			<td>
				<?php 
					$requestFileForward = $root_path."modules/laboratory/labor_test_request_pass.php".URL_APPEND."&target=radio_test&user_origin=lab";
					echo '<a href="'.$requestFileForward.'"><img '.createLDImgSrc($root_path,'newrequest.gif','0','left').' border=0 alt="Enter New Service Request"></a>';
				?>
			</td>
		<tr>
	</table>
		-->
</ul>
<p>
<script language="javascript">
	document.getElementById('skey').value = 'null';
	handleOnclick(<?php if($obgyne=='OB'){ ?> $('OB_defaulter').value <?php } ?>);
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