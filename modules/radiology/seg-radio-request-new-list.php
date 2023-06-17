<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
#include xajax common
require($root_path.'modules/radiology/ajax/radio-request-list.common.php');
require($root_path.'modules/radiology/rad-define-variable.php');
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
define('MAX_BLOCK_ROWS',30);


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

#$local_user='ck_prod_db_user';
$local_user='ck_radio_user';   # burn added : September 24, 2007
require_once($root_path.'include/inc_front_chain_lang.php');
//$db->debug=1;
#$append=URL_APPEND."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;

$append=URL_APPEND."&status=".$status."&target=".$target."&noresize=1&user_origin=".$user_origin."&dept_nr=".$dept_nr;   # burn added: Septmeber 19, 2007
if($_GET['ob']){
	$obgy = "&ob=OB";
}

$breakfile="radiolog.php$append".$obgy;   # burn added: Septmeber 19, 2007

$breakfile=$root_path.'modules/radiology/'.$breakfile;

$append='&status='.$status.'&target='.$target.'&user_origin='.$user_origin."&dept_nr=".$dept_nr;

$thisfile=basename(__FILE__);

$toggle=0;

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',($_GET['ob']=='OB' ? "OB-GYN Ultrasound :: Service Request List" : "Radiology :: Service Request List"));

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',($_GET['ob']=='OB' ? "OB-GYN Ultrasound :: Service Request List" : "Radiology :: Service Request List"));

 # Assign Body Onload javascript code
# $smarty->assign('sOnLoadJs','onLoad="document.suchform.keyword.select()"');

	#$smarty->assign('sOnLoadJs','onLoad="preSet();"');
	// $smarty->assign('sOnLoadJs','onLoad="DisabledSearch();"');

 # Collect javascript code
 ob_start();

echo "<!--Include dojo toolkit -->";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/dojo/dojo.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"".$root_path."js/jsprototype/prototype1.5.js\"></script>"."\n \r";
echo "<script type=\"text/javascript\" src=\"js/radio-request-list.js\"></script>";

if(isset($_GET['from_notif']))
	$smarty->assign('sOnLoadJs','onLoad="preSet('.$_GET['from_notif'].');DisabledSearch();"');
else
	$smarty->assign('sOnLoadJs','onLoad="DisabledSearch();"');
$patient_type = isset($_GET['patient_type'])?$_GET['patient_type']:null;

?>
<!-- Include dojoTab Dependencies -->
<script type="text/javascript">
	dojo.require("dojo.widget.TabContainer");
	dojo.require("dojo.widget.LinkPane");
	dojo.require("dojo.widget.ContentPane");
	dojo.require("dojo.widget.LayoutContainer");
	dojo.require("dojo.event.*");
</script>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<!-- Core module and plugins: -->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<!--  Overlib stylesheet -->
<!--<link rel="stylesheet" type="text/css" href="js/overlib-radio-list.css">-->

<!--  For Dojo -->
<style type="text/css">
	body{font-family : sans-serif;}
	dojoTabPaneWrapper{ padding : 10px 10px 10px;}
</style>

<script language="javascript">
	dojo.addOnLoad(evtOnClick);

	function preSet(from_notif){
		if(from_notif){
			$('skey').value = "<?php echo date('m/d/Y')?>";
			jsOnClick();
		}else
			document.getElementById('search-refno').focus();
	    
	    dojo.addOnLoad(eventOnClick);
	}
</script>

<?php
//Print xajax script
$xajax->printJavascript($root_path.'classes/xajax-0.2.5');

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

ob_start();
?>
<a name="pagetop"></a>
<br>
<div style="padding-left:10px">
<!-- <form action="<?php echo $thisfile?>" method="post" name="suchform" onSubmit="">  -->
		<div align="center" style="display:">
			<table width="100%" cellpadding="4">
				<tr>
					<td width="30%" align="center">
						Enter the search key
						<!--<input class="segInput" id="search-refno" name="search-refno" type="text" size="30" onChange="trimStringSearchMask(this);" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" onKeyUp="if (this.value.length >= 3){ $('skey').value=$('search-refno').value; jsOnClick();}" onKeyPress="checkEnter(event)"/>-->
						<input class="segInput" id="search-refno" name="search-refno" type="text" size="30" onChange="trimStringSearchMask(this);" style="background-color:#e2eaf3; border-width:thin; font:bold 13px Arial" onKeyUp="DisabledSearch(); if ((event.keyCode == 13)&&(isValidSearch(document.getElementById('search-refno').value))){$('skey').value=$('search-refno').value; jsOnClick();} " onBlur="DisabledSearch();"/>
						<!--<input type="image" src="<?= $root_path ?>images/his_searchbtn.gif" onclick="startAJAXSearch('search',0,0);return false;" align="absmiddle" /><br /> -->
						<select id="patient_type_filter" name="patient_type_filter" onChange="DisabledSearch(); if(isValidSearch($('search-refno').value)){$('skey').value=$('search-refno').value; jsOnClick();} ">
							<?php
								$patient_types = array("All", "ER", "OPD", "IPD","IPBM - IPD", "IPBM - OPD");
								foreach ($patient_types as $key => $value){
									$option = "<option value = \"$key\">$value</option>";
									$to_be_selected = '';

									if($patient_type == $key){
										$to_be_selected = 'selected';
									}
									elseif($patient_type == '4' && $key == 3){
										$to_be_selected = 'selected';
									}
									
									echo "<option value = \"$key\" $to_be_selected >$value</option>";
								}
							?>
						</select>
						<input type="image" class="jedInput"  id="search-btn" src="<?=$root_path?>images/his_searchbtn.gif" align="absmiddle" onClick="$('skey').value=$('search-refno').value; jsOnClick();" disabled="disabled">
						<br>
						<?php if($_GET['ob']=='OB'){?>
						<span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
							(RID,Case no. ,HRN, Name)
						</span>
						<?php  }else{ ?>
						<span style="font-family:Arial, Helvetica, sans-serif; font-size:11px">
							(Batch No., RID, HRN, Name, Case no., Date of request(MM/DD/YYYY), Birthdate)
						</span>
						<?php }?>
					</td>
				</tr>
			</table>
		</div>
</div>
	<input type="hidden" name="sid" id="sid" value="<?php echo $sid?>">
	<input type="hidden" name="lang" id="lang" value="<?php echo $lang?>">
	<input type="hidden" name="cat" value="<?php echo $cat?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" value="search">
<!-- </form> -->
<br>
<span id='textResult' style="text-align:left"></span>
<br><br>

<?php
  #added by VAN 11-17-2011  
  #echo "he = ".$allow_accessCT." = ".$allow_accessUTZ." = ".$allow_accessXRAY." = ".$allow_accessOBGYNEUTZ;
// Edited by Matsuu for radiology and obgyne 03042017
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
       // var_dump($allow_accessOBGYNE);exit();
$obgyne = $_GET['ob'];
 $dept_nr_list = "";
  $waccess = 0;
	if($obgyne=='OB'){
	 	$dept_nr_list .= ",".OB_GYNE_Dept.",";
	 	 $waccess = $waccess + 1;
  	}else{
  		if($allow_accessSysAd){
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
  }
  #Ended by  Matsuu for radiology and obgyne 03042017
  
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
<input type="hidden" id="dp_nr" name="dp_nr" value="<?=$dept_belong['dept_nr']?>">
<div id="rlistContainer"  dojoType="TabContainer" style="width:100%; height:28.3em; display:block; border:1px" align="center">
<?php   #añadido por Matsu for radiology and obgyne 03042017?>
    <div dojoType="ContentPane" widgetId="tab0" label="All" style="display:none;overflow:auto;display:block; border:1px solid #8cadc0;">
		<!--  Table:list of request -->
		<table id="Ttab0" class="segList" border="0" cellpadding="0" cellspacing="0">
			<!-- List of all radiology request -->
		</table>
		<img id="ajax-loading" src="<?= $root_path ?>images/loading6.gif" align="absmiddle" border="0" style="display:none"/>
	</div>
	<!-- tabcontent for radiology sub-department -->
<?php
// } #Terminado por Matsuu for radiology and obgyne 03042017                                                   
#Department object

include_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department;

#$radio_sub_dept=$dept_obj->getSubDept($dept_nr);
if($obgyne=='OB'){
$radio_sub_dept=$dept_obj->getDeptServCode($dept_nr_list);
}else{
	$dept_obj->ob_parent_nr='209';
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
        // var_dump($rowSubDept['nr']);exit();
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
	<input type="hidden" name="skey" id="skey" value="<?php echo $HTTP_SESSION_VARS['sess_searchkey']; ?>">
-->
	<!--<input type="hidden" name="skey" id="skey" value="*"> -->
	<input type="hidden" name="skey" id="skey" value="">
	<input type="hidden" name="smode" id="smode" value="<?php echo $mode; ?>">
	<input type="hidden" name="starget" id="starget" value="<?php echo $target; ?>">
	<input type="hidden" name="thisfile" id="thisfile" value="<?php echo $thisfile; ?>">
	<input type="hidden" name="rpath" id="rpath" value="<?php echo $root_path; ?>">
	<input type="hidden" name="pgx" id="pgx" value="<?php echo $pgx; ?>">
	<input type="hidden" name="oitem" id="oitem" value="<?= $oitem? $oitem:'create_dt' ?>">
	<input type="hidden" name="odir" id="odir" value="<?= $odir? $odir:'ASC' ?>">
	<input type="hidden" name="totalcount" id="totalcount" value="<?php echo $totalcount; ?>">

<script language="javascript">
	document.getElementById('skey').value = 'null';
	jsOnClick( <?php if($obgyne=='OB'){ ?> $('OB_defaulter').value <?php } ?> );
</script>

<br />
<hr>

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
	<input type="hidden" name="searchkey" id="searchkey" onchange="trimStringSearchMask(this)" >
	<input type="hidden" name="sid" value="<?php echo $sid ?>">
	<input type="hidden" name="lang" value="<?php echo $lang ?>">
	<input type="hidden" name="userck" value="<?php echo $userck ?>">
	<input type="hidden" name="mode" id="mode" value="search">
</form>


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
