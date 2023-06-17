<?php
# Created by EJ - 11/13/214 ---- to allow user at billing department to view audit trails.
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
#require_once($root_path.'include/care_api_classes/class_labservices_transaction.php');
#$srvObj=new SegLab();

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org, 
*
* See the file "copy_notice.txt" for the licence notice
*/
define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

$thisfile=basename(__FILE__);

$title=$LDLab;
$breakfile=$root_path."modules/registration_admission/seg-close-window.php".URL_APPEND."&userck=$userck";
#$imgpath=$root_path."pharma/img/";

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
$smarty->assign('sOnLoadJs','onLoad="preSet();"');

require_once($root_path.'include/care_api_classes/class_insurance.php');
$ins_obj =new PersonInsurance;

# Collect javascript code
ob_start()

?>
<!-- MemberInfo Form CSS file -->
<link type='text/css' href='../../modules/registration_admission/css/memberinfo.css' rel='stylesheet' media='screen' />
<link rel="stylesheet" href="<?=$root_path?>js/jquery/css/jquery-ui.css" />
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jquery/jquery.simplemodal.js"></script>
<script type='text/javascript' src="<?=$root_path?>js/jsobj2phpobj.js"></script> 

<script type="text/javascript" src="js/dataTables/jquery-1.12.3.js"></script>
<script type="text/javascript" src="js/dataTables/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="css/dataTables/jquery.dataTables.min.css"></style>

<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>
<script type="text/javascript">

	jQuery(document).on('ready', function() {     
		jQuery('#example').DataTable({ "searching": false,
				"bLengthChange": true,

			});
	} );
</script>
<!-- <div style="display:block; border:1px solid #8cadc0; overflow-y:hidden;overflow-x:hidden; height:650px; width:99%; background-color:#e5e5e5">
 -->
<table id="example"border="1" class="display" cellspacing="2" cellpadding="2" width="100%" align="center">
	<tbody>
		<?php
		$x = 0;
		$result = $ins_obj->getHistoryInsurance($_GET['encounter_nr']);
		while ($row = $result->FetchRow()) {
			$history = explode("\n", $row['history']);

			$count = count($history)/3;

			for ($i=0; $i <$count ; $i++) {
			echo "<tr>";
			echo "<td align = 'center'>".$history[$x]."</td>";
			echo "</tr>";
			$x = $x +3;
			}
		}
		?>
	</tbody>
</table>

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

<?php

$sTemp = ob_get_contents();
ob_end_clean();

# Assign the form template to mainframe

$smarty->assign('sMainFrameBlockData',$sTemp);

/**
* show Template
*/

$smarty->display('common/mainframe.tpl');
