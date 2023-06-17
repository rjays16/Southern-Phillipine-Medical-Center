<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
//added by Macoy July 5,2014
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
//end
require($root_path.'include/inc_environment_global.php');
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
$lang_tables=array('personell.php');
define('LANG_FILE','aufnahme.php');
$local_user='aufnahme_user';
require_once($root_path.'include/inc_front_chain_lang.php');
require_once($root_path.'include/inc_date_format_functions.php');
// $HTTP_SESSION_VARS['sess_searchkey'] = '';
//$db->debug=true;

# If a forwarded nr is available, convert it to searchkey and set mode to "search"
if(isset($fwd_nr)&&$fwd_nr){
	$searchkey=$fwd_nr;
	$mode='search';
}else{
	# Translate *? wildcards	
	$searchkey=strtr($searchkey,'*?','%_');
}
$thisfile=basename(__FILE__);
$toggle=0;
// var_dump($_GET['department']); die();
if($HTTP_COOKIE_VARS['ck_login_logged'.$sid]){
	$breakfile=$root_path.'main/spediens.php'.URL_APPEND;
}else {
	$breakfile='personell_admin_pass.php'.URL_APPEND.'&target='.$target;
}

// check for valid permissions; added by: syboy 01/12/2016 : meow
require_once $root_path.'include/care_api_classes/class_user.php';
$user = SegUser::getCurrentUser();

/*$permissionSet = array('_a_1_searchempdependent');
$allow = $user->hasPermission($permissionSet);
if (!$allow)
{
	header('Location:'.$root_path.'main/login.php?'.
		'forward='.urlencode('modules/sponsor/'.$thisfile).
		'&break='.urlencode('modules/sponsor/seg-sponsor-functions.php'));
	exit;
}*/

 /* Set color values for the search mask */
$searchmask_bgcolor='#f3f3f3';
$searchprompt=$LDEnterEmployeeSearchKeyNew;
$entry_block_bgcolor='#fff3f3';
$entry_border_bgcolor='#6666ee';
$entry_body_bgcolor='#ffffff';

if(!isset($searchkey)) $searchkey='';
if(!isset($mode)) $mode='';


# Initialize page´s control variables
if($mode=='paginate'){
	$searchkey=$HTTP_SESSION_VARS['sess_searchkey'];

}else{
	# Reset paginator variables
	$pgx=0;
	$totalcount=0;
	$odir='';
	$oitem='';
}
#Load and create paginator object
require_once($root_path.'include/care_api_classes/class_paginator.php');
$pagen=new Paginator($pgx,$thisfile,$HTTP_SESSION_VARS['sess_searchkey'],$root_path);

if(isset($mode)&&($mode=='search'||$mode=='paginate')&&isset($searchkey)&&($searchkey)){
	
	include_once($root_path.'include/inc_date_format_functions.php');

	if($mode!='paginate'){
		$HTTP_SESSION_VARS['sess_searchkey']=$searchkey;
	}	
		# convert * and ? to % and &
		$searchkey=strtr($searchkey,'*?','%_');
						
		$GLOBAL_CONFIG=array();
			
		include_once($root_path.'include/care_api_classes/class_globalconfig.php');
		$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
		$glob_obj->getConfig('personell_nr_adder');
		
		# Get the max nr of rows from global config
		$glob_obj->getConfig('pagin_personell_search_max_block_rows');
		if(empty($GLOBAL_CONFIG['pagin_personell_search_max_block_rows'])) $pagen->setMaxCount(MAX_BLOCK_ROWS); # Last resort, use the default defined at the start of this page
			else $pagen->setMaxCount($GLOBAL_CONFIG['pagin_personell_search_max_block_rows']);		
		
			$searchkey=trim($searchkey);
		$suchwort=$searchkey;
		
		if(is_numeric($suchwort)) {
						$suchwort=(int) $suchwort;
			$numeric=1;
			if($suchwort<$GLOBAL_CONFIG['personell_nr_adderr']){
					 $suchbuffer=(int) ($suchwort + $GLOBAL_CONFIG['personell_nr_adder']) ; 
			}
			
			if(empty($oitem)) $oitem='pid';			
			if(empty($odir)) $odir='DESC'; # default, latest pid at top
			
			$sql2=" WHERE ( ps.pid='$suchwort'  OR ps.nr = '$suchbuffer' )";
			
			} else {
			# Try to detect if searchkey is composite of first name + last name
			if(stristr($searchkey,',')){
				$lastnamefirst=TRUE;
			}else{
				$lastnamefirst=FALSE;
			}
			
			$searchkey=strtr($searchkey,' ',' ');
				
			$cbuffer=explode(',',$searchkey);
			

			# Remove empty variables
			for($x=0;$x<sizeof($cbuffer);$x++){
				$cbuffer[$x]=trim($cbuffer[$x]);
				if($cbuffer[$x]!='') $comp[]=$cbuffer[$x];
			}
			
			# Arrange the values, ln= lastname, fn=first name, bd = birthday
			if($lastnamefirst){
				$fn=$comp[1];
				$ln=$comp[0];
				$bd=$comp[2];
			}else{
				$fn=$comp[0];
				$ln=$comp[1];
				$bd=$comp[2];
			}
			
			if(empty($oitem)) $oitem='name_last';
			
			# Check the size of the comp
			if(sizeof($comp)>1){
				
				$DOB=formatDate2STD($suchwort,$date_format);
				if(strpos($ln,'-') == true){
					$sql2=" WHERE ( p.name_last $sql_LIKE '%".strtr($ln,'+',' ')."%'
													AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%'";
													
				}else{
					$sql2=" WHERE ( p.name_last $sql_LIKE '".strtr($ln,'+',' ')."%'
													AND p.name_first $sql_LIKE '".strtr($fn,'+',' ')."%'";
												

				}
				
				if($bd && $DOB){ $sql2.=" AND p.date_birth = '$DOB' )";
				}else{
					$sql2.=')';
				}

				if(empty($odir)) $odir='DESC'; # default, latest birth at top

			}else{
				// if(strpos($suchwort,'') !== true){
					if(strlen($suchwort)==2){
						$sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'
													OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%'";
												
					}else{
					$sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
													OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%'";
													
						}
					
				
				// 	var_dump("s");exit();

				// }
				// else{
				// 	// $sql2=" WHERE (p.name_last $sql_LIKE '".strtr($suchwort,'+',' ')."%'
				// 	// 								OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%'";
				// 	var_dump("ss");exit();
				// }
				// $sql2=" WHERE (p.name_last $sql_LIKE '%".strtr($suchwort,'+',' ')."%'
				// 									OR p.name_first $sql_LIKE '".strtr($suchwort,'+',' ')."%'";
				if($DOB) $sql2.=" OR p.date_birth = '$DOB' ";
					else $sql2.=')';
				if(empty($odir)) $odir='ASC'; # default, ascending alphabetic
			}
		}
			#commented by VAN 11-04-09
			$sql2.=" /*AND ps.status NOT IN ('void','hidden','deleted','inactive')
						AND ps.is_discharged IN ('',0)*/
							AND ps.pid=p.pid ";
			# Filter if it is personnel nr
			if($oitem=='pid') $sql3.='ORDER BY ps.'.$oitem.' '.$odir;
				else $sql3 ='GROUP BY ps.pid ORDER BY p.'.$oitem.' '.$odir;

			$dbtable='FROM care_personell as ps LEFT JOIN seg_orientation_list AS sol 
    					ON ps.`nr` = sol.`employee_number` AND sol.is_deleted="0", care_person as p ';

			$sql='SELECT ps.status,ps.nr, ps.pid, ps.is_discharged, p.name_last, p.name_first, p.date_birth, sol.orientation_list_id, p.sex,p.photo_filename '.$dbtable.$sql2.$sql3;
			#echo $sql;
			
			 //var_dump($sql);exit();
			if($ergebnis=$db->SelectLimit($sql,$pagen->MaxCount(),$pagen->BlockStartIndex()))
					{
				if ($linecount=$ergebnis->RecordCount()) 
				{ 
					if(($linecount==1)&&$numeric)
					{
						if($_GET['from']=='medocs' || $_GET['target']=='personell_search'){
							$zeile=$ergebnis->FetchRow();
							header("location:personell_register_show.php".URL_APPEND.'&personell_nr='.$zeile['nr'].'&target=personell_search&from=medocs&department='.$_GET['department'].'');
							exit;
						}
						else {
							$zeile=$ergebnis->FetchRow();
							header("location:personell_register_show.php".URL_REDIRECT_APPEND."&target=personell_search&personell_nr=".$zeile['nr']."&sem=".(!$zeile['is_discharged']));
							exit;
						}
					}
					# Set the object to actual nr of rows
					$pagen->setTotalBlockCount($linecount);
					
					# If more than one count all available
					if(isset($totalcount)&&$totalcount){
						$pagen->setTotalDataCount($totalcount);
					}else{

						# Count total available data
						$sql='SELECT COUNT(ps.nr) AS count '.$dbtable.$sql2;
						
						if($result=$db->Execute($sql)){
							if ($result->RecordCount()) {
								$rescount=$result->FetchRow();
										$totalcount=$rescount['count'];
								}
						}
						$pagen->setTotalDataCount($totalcount);
					}
					# Set the sort parameters
					$pagen->setSortItem($oitem);
					$pagen->setSortDirection($odir);
				}
			}
			 else {echo "<p>".$sql."<p>$LDDbNoRead";};

} else { 
		$mode='';
}

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme


require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('system_admin');

# Title in toolbar
//added by Macoy July 5,2014
 if($_GET['from']=='medocs'){
 		# added by: syboy 12/18/2015 : meow
		$LDPersonnelManagement = $_GET['department'];

		if ($LDPersonnelManagement == 'Medical Records') {
			$breakfile=$root_path.'modules/medocs/seg-medocs-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Admitting') {
			$breakfile=$root_path.'modules/ipd/seg-ipd-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Emergency Room') {
			$breakfile=$root_path.'modules/er/seg-er-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Outpatient') {
			$breakfile=$root_path.'modules/opd/seg-opd-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Personnel Health Station') {
			$breakfile=$root_path.'modules/phs/seg-phs-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Doctors') {
			$breakfile=$root_path.'modules/doctors/doctors.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Nursing') {
			$breakfile=$root_path.'modules/nursing/nursing.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Operating Room') {
			$breakfile=$root_path.'main/op-doku.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Laboratories') {
			$breakfile=$root_path.'modules/laboratory/labor.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Radiology') {
			$breakfile=$root_path.'modules/radiology/radiolog.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Dialysis') {
			$breakfile=$root_path.'modules/dialysis/seg-dialysis-menu.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Pharmacy') {
			$breakfile=$root_path.'modules/pharmacy/seg-pharma-order-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Social Service') {
			$breakfile=$root_path.'modules/social_service/social_service_main.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Billing Section') {
			$breakfile=$root_path.'modules/billing/bill-main-menu.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'PIAD') {
			$breakfile=$root_path.'modules/sponsor/seg-sponsor-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Inventory') {
			$breakfile=$root_path.'modules/supply_office/seg-supply-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Cashier') {
			$breakfile=$root_path.'modules/cashier/seg-cashier-functions.php'.URL_APPEND;
		}else if ($LDPersonnelManagement == 'Health Service and Specialty Clinic') {
			$breakfile=$root_path.'modules/industrial_clinic/seg-industrial_clinic-functions.php'.URL_APPEND;
		}
		# ended syboy
			
 }
//end

 $smarty->assign('sToolbarTitle',"$LDPersonnelManagement :: $LDPersonellData :: $LDSearch");

 # hide return button
 $smarty->assign('pbBack',FALSE);

 # href for help button
 $smarty->assign('pbHelp',"javascript:gethelp('employee_search.php')");

 # href for close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"$LDPersonnelManagement :: $LDPersonellData :: $LDSearch");

 # Body onLoad Javascript code
 $smarty->assign('sOnLoadJs','onLoad="document.searchform.searchkey.select()"');

# Colllect javascript code

ob_start();

?>

<table width=100% border=0 cellspacing="0" cellpadding=0>

<!-- edited by Macoy July 5,2014 -->
<?php
if ($_GET['from']!="medocs"){
 	include('./gui_bridge/default/gui_tabs_personell_reg.php');
}
?>
<!-- end -->

<!-- Load tabs -->
<?php
 $employee_search = 1;
$target='personell_search';

?>


</table>
<ul>
	 <table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
		 <tr>
			 <td>
		 <?php

						include($root_path.'include/inc_patient_searchmask.php');
			 
		 ?>
</td>
		 </tr>
	 </table>

<p>
<a href="<?php  echo $breakfile; ?>"><img <?php echo createLDImgSrc($root_path,'cancel.gif','0') ?>></a>
<p>

<?php
if($mode=='search'||$mode=='paginate'){

	if ($linecount) echo '<hr width=80% align=left>'.str_replace("~no.~",$totalcount,$LDSearchFound).' '.$LDShowing.' '.$pagen->BlockStartNr().' '.$LDTo.' '.$pagen->BlockEndNr().'.';
		else echo str_replace('~no.~','0',$LDSearchFound); 
			
	if ($linecount) { 

	# Load the common icons
	$img_options=createComIcon($root_path,'statbel2.gif','0','',TRUE);
	$img_male=createComIcon($root_path,'spm.gif','0','',TRUE);
	$img_female=createComIcon($root_path,'spf.gif','0','',TRUE);
	$img_accept =createComIcon($root_path, 'accept.png', '0', '', TRUE);
	$img_cancel =createComIcon($root_path, 'cancel.png', '0', '', TRUE); 
	echo '
			<table border=0 cellpadding=3 cellspacing=1> <tr class="wardlisttitlerow">';
			
?>

			<!-- <td><b>
		<?php 
			if($oitem=='pid') $flag=TRUE;
			else $flag=FALSE; 
		echo $pagen->SortLink($LDHrn,'pid',$odir,$flag); 
			 ?></b></td> -->
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDHrn; ?>
		 	</td>
			<!-- <td><b>
		<?php 
			if($oitem=='sex') $flag=TRUE;
			else $flag=FALSE; 
		echo $pagen->SortLink($LDSex,'sex',$odir,$flag); 
			?></b></td> -->
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDSex; ?>
		 	</td>
			<!-- <td><b>
		<?php 
			if($oitem=='name_last') $flag=TRUE;
			else $flag=FALSE; 
		echo $pagen->SortLink($LDLastName,'name_last',$odir,$flag); 
			 ?></b></td> -->
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDLastName; ?>
		 	</td>
			<!-- <td><b>
		<?php 
			if($oitem=='name_first') $flag=TRUE;
			else $flag=FALSE; 
		echo $pagen->SortLink($LDFirstName,'name_first',$odir,$flag); 
			 ?></b></td> -->
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDFirstName; ?>
		 	</td>
			<!-- <td><b>
		<?php 
			if($oitem=='date_birth') $flag=TRUE;
			else $flag=FALSE; 
		echo $pagen->SortLink($LDBday,'date_birth',$odir,$flag); 
			 ?></b></td> -->
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDBday; ?>
		 	</td>
			<!-- <td><b>
		<?php 
			if($oitem=='orientation_list_id') $flag=TRUE;
			else $flag=FALSE;
		 echo $pagen->SortLink($LDOrientationList,'orientation_list_id',$odir,$flag); 
			
		?></b></td> -->
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDStatus; ?>
		 	</td>

		 	<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>">
				<font color="#ffffff"><b><?php echo $LDOrientationList; ?>
		 	</td>
	<!-- 	?></b></td> -->
		
		<!-- edited by Macoy July 5,2014; Edited by : syboy 12/17/2015 : meow -->
		<?php
			if($_GET['from'] == 'medocs' || $_GET['target']=='personell_search'){ 
		?>	
			<td background="<?php echo createBgSkin($root_path,'tableHeaderbg.gif'); ?>"><font color="#ffffff"><b><?php echo $LDOptions; ?></td>
		<?php } ?>
		<!-- end -->

<?php
					echo"</tr>";

					while($zeile=$ergebnis->FetchRow())
					{
						
						echo "
							<tr class=";
						if($toggle) { echo "wardlistrow2>"; $toggle=0;} else {echo "wardlistrow1>"; $toggle=1;};
					echo"<td>";
											 // echo '&nbsp;'.($zeile['nr']+$GLOBAL_CONFIG['personell_nr_adder']);
					echo "<b>";
												 echo '&nbsp;'.$zeile['pid'];
												 echo "</b>";
											 echo "</td>";	
						 		
						echo '<td><a href="javascript:popPic(\''.$zeile['name_last'].', '.$bed['name_first'].' '.formatDate2Local($zeile['date_birth'],$date_format).'\',\''.$zeile['photo_filename'].'\')">';
						switch($zeile['sex']){
							case 'f': echo '<img '.$img_female.'>'; break;
							case 'm': echo '<img '.$img_male.'>'; break;
							default: echo '&nbsp;'; break;
						}
						
												echo '</a></td>
						';	
						 
						echo"<td>";
						echo "&nbsp;".ucfirst($zeile['name_last']);
												echo "</td>";	
						echo"<td>";
						echo "&nbsp;".ucfirst($zeile['name_first']);
												echo "</td>";	
							
						echo"<td>";
						echo "&nbsp;".formatDate2Local($zeile['date_birth'],$date_format);
						echo "</td>";	
					
						echo"<td>";
						if (empty($zeile['status']))
							$status = 'ACTIVE';
						else
							#$status = mb_strtoupper($zeile['status']);
							$status = 'INACTIVE';
							
						echo "&nbsp;".$status;
						
						echo "</td>";	

					
						if($zeile['orientation_list_id'] == "" || $zeile['orientation_list_id'] == "NULL"){
										$zeile['orientation_list_id'] = '<img '.$img_cancel.'>';
						}else{
										$zeile['orientation_list_id'] = '<img '.$img_accept.'>';
						}
										echo '</td>
						<td align=right>&nbsp; &nbsp;'.$zeile['orientation_list_id'].'</td>';

					//edited by Macoy July 5,2014; Edited by : syboy 12/17/2015 : meow
					if($_GET['from']=='medocs' || $_GET['target']=='personell_search'){
						// if($HTTP_COOKIE_VARS[$local_user.$sid])
						echo '<td>&nbsp;
								<a href="personell_register_show.php'.URL_APPEND.'&from=such&personell_nr='.$zeile['nr'].'&target=personell_search&from=medocs&department='.$_GET['department'].'">
								<img '.$img_options.' alt="'.$LDShowData.'"></a>&nbsp;';
						
						if(!file_exists($root_path.'cache/barcodes/en_'.$zeile['nr'].'.png')){
					 		echo "<img src='".$root_path."classes/barcode/image.php?code=".$zeile['nr']."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>";
						}
						echo '</td>';
					}
					else{
						echo '<td>&nbsp;
								<a href="personell_register_show.php'.URL_REDIRECT_APPEND."&target=personell_search&personell_nr=".$zeile['nr']."&sem=".(!$zeile['is_discharged']).'">
								<img '.$img_options.' alt="'.$LDShowData.'"></a>&nbsp;';
						
						if(!file_exists($root_path.'cache/barcodes/en_'.$zeile['nr'].'.png')){
					 		echo "<img src='".$root_path."classes/barcode/image.php?code=".$zeile['nr']."&style=68&type=I25&width=180&height=50&xres=2&font=5&label=2&form_file=en' border=0 width=0 height=0>";
						}
						echo '</td>';
					}
						echo '</tr>';
					
					//end

					}
					echo '
						<tr><td colspan=6>'.$pagen->makePrevLink($LDPrevious).'</td>
						<td align=right>'.$pagen->makeNextLink($LDNext).'</td>
						</tr>
						</table>';
					if($linecount>$pagen->MaxCount())
					{
							/* Set the appending nr for the searchform */
							$searchform_count=2;
					?>
			<p>
		 <table border=0 cellpadding=10 bgcolor="<?php echo $entry_border_bgcolor ?>">
		 <tr>
			 <td>
		 <?php
						include($root_path.'include/inc_patient_searchmask.php');
		 ?>
</td>
		 </tr>
	 </table>
					<?php
					}
	}
}
?>

</ul>

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
