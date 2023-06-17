<?php
/**
 * @author Gervie 12/23/2015
 *
 * Audit trail for Deletion of SOA
 */

require('./roots.php');
require($root_path.'include/inc_environment_global.php');
include_once($root_path . 'include/care_api_classes/class_encounter.php');

$encObj = new Encounter();

define('LANG_FILE','lab.php');
define('NO_2LEVEL_CHK',1);
$local_user='ck_prod_db_user';
require_once($root_path.'include/inc_front_chain_lang.php');

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
$smarty->assign('sToolbarTitle',"Accommodation Audit Trail");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Accommodation Audit Trail");

ob_start();
?>

<link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css"/>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
<script type='text/javascript' src="<?= $root_path ?>js/jquery/ui/jquery-ui-1.9.1.js"></script>

<?php
$sTemp = ob_get_contents();
ob_end_clean();

$smarty->append('JavaScript',$sTemp);

# Buffer page output

ob_start();

?>

<div style="display:block; border:1px solid #8cadc0; overflow-y:hidden;overflow-x:hidden; width:99%; background-color:#e5e5e5">
    <table border="1" class="segPanel" cellspacing="2" cellpadding="2" width="99%" align="center">
        <tbody>
        <?php
        $x = 0;
        $result = $encObj->getAccommodationTrail($_GET['encounter_nr']);
// var_dump($_GET['encounter_nr']);die;
        foreach($result as $key => $value){
            $history = explode("\n", $value['history']);

            if($value['history'] != "") {
                for ($i = 0; $i < count($history); $i++) {
                    echo "<tr>";
                    echo "<td align = 'left' style='padding-left: 30px; padding-right: 30px;'>" . $history[$i] . "</td>";
                    echo "</tr>";
                }
            }
        }
        ?>
        </tbody>
    </table>
</div>

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

