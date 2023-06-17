<?php
#Added by Borj 2014-08-8
#Special Lab Certificate
#CHOLANGIOPANCREATOGRAPHY REPORT
#ENDOSCOPY REPORT

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');

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

	global $db;
	$encounter_nr = $_GET['encounter_nr'];
	$pid = $_GET['pid'];

$sql = $db->Prepare("SELECT 
					fn_get_person_name(cp.pid) AS 'lnamefname',
                    CONCAT(cp.`street_name`,' ',sb.`brgy_name`,' ',sm.`mun_name`) AS address,
                    cp.`pid` AS hrn,
                    CONCAT(cp.age,'/',cp.sex) AS agesex
    				FROM care_person cp
    				INNER JOIN `seg_barangays` sb ON cp.`brgy_nr` = sb.`brgy_nr`
					INNER JOIN `seg_municity` sm ON cp.`mun_nr` = sm.`mun_nr`
    				WHERE cp.status  NOT IN (
    				'deleted',
    				'hidden',
    				'inactive',
    				'void'
  					)
  					AND cp.`pid`='$pid'");
	
$vac_data = $db->Execute($sql);
while ($row = $vac_data->FetchRow()) {
    $lnamefname = $row['lnamefname'];
    $address = $row['address'];
    $hrn = $row['hrn'];
    $agesex = $row['agesex'];

}

?>
<script type="text/javascript">
<!--

}
// -->
</script>
<script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="js/splab-service-tray.js?t=<?=time()?>"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript" src="//code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css">
					  				<link rel="stylesheet" href="/resources/demos/style.css">
					  				<script>
									$(function() {
									$( "#datepicker" ).datepicker();
									});
									</script>

<?php

#seg_gastroenterologist_doctor
$blood_gastro = $row_i['blood_gastro'];
                
                $sql_blood_gastro = 'SELECT * FROM seg_gastroenterologist_doctor';
                $rs_blood_gastro = $db->Execute($sql_blood_gastro);
                $blood_gastro_option="<option value='name'>-Select Doctor-</option>";

                if (is_object($rs_blood_gastro)){
                    while ($row_blood_gastro=$rs_blood_gastro->FetchRow()) {
                        $selected='';
                        if ($blood_gastro==$row_blood_gastro['name'])
                            $selected='selected';
                        
                        $blood_gastro_option.='<option '.$selected.' value="'.$row_blood_gastro['name'].'">'.ucwords($row_blood_gastro['name']).'</option>';
                    }

                }
                $blood_gastro_col = '<select id="blood_gastro" name="blood_gastro" class="segInput">
                                        '.$blood_gastro_option.' 
                                   </select>';

#seg_endoscopist_doctor
$blood_endoscopist = $row_i['blood_endoscopist'];
                
                $sql_blood_endoscopist = 'SELECT * FROM seg_endoscopist_doctor';
                $rs_blood_endoscopist = $db->Execute($sql_blood_endoscopist);
                $blood_endoscopist_option="<option value='name'>-Select Doctor-</option>";

                if (is_object($rs_blood_endoscopist)){
                    while ($row_blood_endoscopist=$rs_blood_endoscopist->FetchRow()) {
                        $selected='';
                        if ($blood_endoscopist==$row_blood_endoscopist['name'])
                            $selected='selected';
                        
                        $blood_endoscopist_option.='<option '.$selected.' value="'.$row_blood_endoscopist['name'].'">'.ucwords($row_blood_endoscopist['name']).'</option>';
                    }

                }
                $blood_endoscopist_col = '<select id="blood_endoscopist" name="blood_endoscopist" class="segInput">
                                        '.$blood_endoscopist_option.'
                                   </select>';




ob_start();

?>

<form action="<?php echo '../../modules/special_lab/certificates/seg-splab-endoscopic-certificates.php?'?>" method="get" target="_blank">
<table width="98%" cellspacing="2" cellpadding="2" style="margin:0.7%">
		<tbody>

			<tr>
				<td style="font:bold 12px Arial; bgcolor="#ffffee" color: #2d2d2d" >
						
					<div style="padding:4px 2px; padding-left:10px; ">
						<table width="95%" border="0" cellpadding="2" cellspacing="0" style="margin-top:8px">
			
							<tr>
								<td valign="top" width="30%" align="right"><strong>DATE</strong></td>
								<td>
									<input type="text" class="segInput" id="datepicker" name="datepicker" value="<? echo $datepicker;?>">
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right">
									<strong>HRN</strong>
								</td>
								<td align="left">
									<input disable type="text" class="segInput" name="endo_hrn" id="endo_hrn" value="<? echo $pid;?>" readonly size=40 onBlur="trimString(this);">
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right"><strong>Name</strong></td>
								<td align="left">
									<input disable type="text" class="segInput" name="endo_name" id="endo_name" value="<? echo $lnamefname;?>" readonly size=40 onBlur="trimString(this);">
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Age/Sex</strong>
								</td>
								<td align="left">
									<input disable type="text" class="segInput" name="endo_age" id="endo_age" value="<? echo $agesex;?>" readonly size=40 onBlur="trimString(this);">
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right"><strong>Address</strong></td>
								<td align="left">
									<input disable type="text" class="segInput" name="endo_address" id="endo_address" value="<? echo $address;?>" readonly size=40 onBlur="trimString(this);">
								</td>
							</tr>


							<tr>
								<td valign="top" width="30%" align="right"><strong>Indication</strong></td>
								<td align="left">
									<input type="text" class="segInput" name="endo_indication" id="endo_indication" size=40 onBlur="trimString(this);">
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right">
									<strong>RUV</strong>
								</td>
								<td align="left">
									<input type="text" class="segInput" name="endo_ruv" id="endo_ruv" size=40 onBlur="trimString(this);">
								</td>
							</tr>
								<td valign="top" width="30%" align="right">
									<strong>PHIC</strong></td>
								<td align="left" >
									<select name="select_phic">
                                <option value="YES"  "selected='selected'"?>YES</option>
                                <option value="NO"  "selected='selected'"?>NO</option>
                            	    </select> 
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right"><strong>Anesthesiologist</strong></td>
								<td align="left">
									<?php echo $blood_gastro_col ?>
								<script language="javascript">
									</script>
								</td>
							</tr>
				
							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Pre-endoscopy Impression</strong>
								</td>
								<td align="left">
									<textarea class="segInput" name="endo_endoscopy" id="endo_endoscopy" cols=100 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"></textarea>
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Findings</strong>
								</td>
								<td align="left">
									<textarea class="segInput" name="endo_findings" id="endo_findings" cols=100 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"></textarea>
								</td>
							</tr>
							
							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Impression</strong>
								</td>
								<td align="left">
									<textarea class="segInput" name="endo_impressions" id="endo_impressions" cols=100 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"></textarea>
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Biopsy</strong>
								</td>
								<td align="left">
									<textarea class="segInput" name="endo_biopsy" id="endo_biopsy" cols=100 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"></textarea>
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right">
									<strong>Suggestions</strong>
								</td>
								<td align="left">
									<textarea class="segInput" name="endo_suggestions" id="endo_suggestions" cols=100 rows=2 wrap="physical" onChange="trimString(this);" onBlur="trimString(this);"></textarea>
								</td>
							</tr>

							<tr>
								<td valign="top" width="30%" align="right"><strong>Endoscopist</strong></td>
								<td align="left">
									<?php echo $blood_endoscopist_col ?>
								<script language="javascript">
									</script>
								</td>
							</tr>
													
							<tr>
								<td valign="top" width="30%" align="right"><strong>Select Reports</strong></td>
								<td align="left" >
									<select name="select">
                                <option value="report1"  "selected='selected'"?>CHOLANGIOPANCREATOGRAPHY REPORT</option>
                                <option value="report2"  "selected='selected'"?>ENDOSCOPY REPORT</option>
                            	    </select> 
								</td>
							</tr>

								<td valign="top" width="30%" align="right"></td>
								<td align="right">
						        <input class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary" type="submit"  value="Print" style="margin-left: 4px; height: 30px; cursor: pointer; font:bold 12px Arial;">
						       
  							</tr>

						</table>
					</div>
				</td>
			</tr>
			
		</tbody>
	</table>
	
</form>
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

