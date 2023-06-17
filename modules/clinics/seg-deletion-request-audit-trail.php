<?php
/**
 * @author Mary 06/22/2016
 *
 * Audit trail for Deletion of SOA
 */

require('./roots.php');
require($root_path.'include/inc_environment_global.php');

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
$smarty->assign('sToolbarTitle',"Deletion Request Audit Trail");

# href for the back button
// $smarty->assign('pbBack',$returnfile);

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Deletion Request Audit Trail");

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
        global $db;
        $enc = $_GET['encounter_nr'];
        $labrefno = '';
        $radrefno = '';
        $bloodrefno = '';
        $SPLrefno = '';

                echo "<tr>";
                echo "<th align='center' colspan='6'><strong>Laboratory Requests</strong></th>";
                echo "</tr>";

                echo "<tr>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Ref No.</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Request Date and Time</th>";
              //  echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Source</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Item</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted By</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted Date & Time</th>";
                echo "</tr>";

                //modified by Julius 01-09-2017
        $str = "SELECT refno FROM seg_lab_serv  WHERE ref_source='LB' AND encounter_nr=".$db->qstr($enc);

        if ($result1 = $db->Execute($str)) {
            if ($result1->RecordCount()) {
                while ($row1 = $result1->FetchRow()){

                         $labrefno = $row1['refno'];

        $strSQL = "SELECT date_changed,login,old_value, pk_value FROM seg_audit_trail WHERE Action_type ='delete' AND table_name ='seg_lab_servdetails' AND pk_value=".$db->qstr($labrefno);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){

                $details = explode("+", $row['old_value']);

                echo "<tr>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['pk_value'] .  "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($details[0])). "</td>";
               // echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[1] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[2] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['login']. "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($row['date_changed'])) . "</td>";
                echo "</tr>";

               
                }
            }
        }
           }
            }
        }
        //bloodbank added julius-01-09-2017
        
         echo "<tr>";
                echo "<th align='center' colspan='6'><strong>Blood Bank Requests</strong></th>";
                echo "</tr>";

                echo "<tr>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Ref No.</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Request Date and Time</th>";
               // echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Source</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Item</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted By</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted Date & Time</th>";
                echo "</tr>";


        $str = "SELECT refno FROM seg_lab_serv  WHERE ref_source='BB' AND encounter_nr=".$db->qstr($enc);

        if ($result1 = $db->Execute($str)) {
            if ($result1->RecordCount()) {
                while ($row1 = $result1->FetchRow()){

        $bloodrefno = $row1['refno'];
       
        $strSQL = "SELECT date_changed,login,old_value, pk_value FROM seg_audit_trail WHERE Action_type ='delete' AND table_name ='seg_lab_servdetails' AND pk_value=".$db->qstr($bloodrefno);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){

                $details = explode("+", $row['old_value']);

                echo "<tr>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['pk_value'] .  "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($details[0])). "</td>";
               // echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[1] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[2] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['login']. "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($row['date_changed'])). "</td>";
                echo "</tr>";
       
             }
            }
        }
                 }
            }
        }
        //Special Laboratory added julius-01-09-2017
        
        echo "<tr>";
                echo "<th align='center' colspan='6'><strong>Special Laboratory Requests</strong></th>";
                echo "</tr>";

                echo "<tr>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Ref No.</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Request Date and Time</th>";
              //  echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Source</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Item</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted By</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted Date & Time</th>";
                echo "</tr>";


        $str = "SELECT refno FROM seg_lab_serv  WHERE ref_source='SPL' AND encounter_nr=".$db->qstr($enc);

        if ($result1 = $db->Execute($str)) {
            if ($result1->RecordCount()) {
                while ($row1 = $result1->FetchRow()){

        $SPLrefno = $row1['refno'];
        $strSQL = "SELECT date_changed, login, old_value, pk_value FROM seg_audit_trail WHERE Action_type ='delete' AND table_name ='seg_lab_servdetails' AND pk_value=".$db->qstr($SPLrefno);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){

                $details = explode("+", $row['old_value']);

                echo "<tr>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['pk_value'] .  "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($details[0])). "</td>";
               // echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[1] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[2] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['login']. "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($row['date_changed'])) . "</td>";
                echo "</tr>";

               
       
                 }
            }
        }
               }
            }
        }

        //radiology

                echo "<tr>";
                echo "<th align='center' colspan='6'><strong>Radiology Requests</strong></th>";
                echo "</tr>";

                echo "<tr>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Ref No.</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Request Date and Time</th>";
              //  echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Source</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Item</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted By</th>";
                echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted Date & Time</th>";
                echo "</tr>";


        $str = "SELECT refno FROM seg_radio_serv  WHERE encounter_nr=".$db->qstr($enc) ."AND fromdept='RD'";

        if ($result1 = $db->Execute($str)) {
            if ($result1->RecordCount()) {
                while ($row1 = $result1->FetchRow()){

        $radrefno = $row1['refno'];
        $strSQL = "SELECT date_changed, login, old_value, pk_value FROM seg_audit_trail WHERE Action_type ='delete' AND table_name ='care_test_request_radio' AND pk_value=".$db->qstr($radrefno);

        if ($result = $db->Execute($strSQL)) {
            if ($result->RecordCount()) {
                while ($row = $result->FetchRow()){

                $details = explode("+", $row['old_value']);

                echo "<tr>";
            echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['pk_value'] . "</td>";  
            echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($details[0])). "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[2] . "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['login']. "</td>";
                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($row['date_changed'])) . "</td>";
                echo "</tr>";

                }
            }
        }
                }
            }
        }


        //OB Gyne

        echo "<tr>";
        echo "<th align='center' colspan='6'><strong>OB GYNE Requests</strong></th>";
        echo "</tr>";

        echo "<tr>";
        echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Ref No.</th>";
        echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Request Date and Time</th>";
        //  echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Source</th>";
        echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Item</th>";
        echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted By</th>";
        echo "<th align = 'center' style='padding-left: 30px; padding-right: 30px;'>Deleted Date & Time</th>";
        echo "</tr>";


        $str = "SELECT refno FROM seg_radio_serv  WHERE encounter_nr=".$db->qstr($enc)."AND fromdept='OBGUSD'";

        if ($result1 = $db->Execute($str)) {
            if ($result1->RecordCount()) {
                while ($row1 = $result1->FetchRow()){

                    $radrefno = $row1['refno'];
                    $strSQL = "SELECT date_changed, login, old_value, pk_value FROM seg_audit_trail WHERE Action_type ='delete' AND table_name ='care_test_request_radio' AND pk_value=".$db->qstr($radrefno);

                    if ($result = $db->Execute($strSQL)) {
                        if ($result->RecordCount()) {
                            while ($row = $result->FetchRow()){

                                $details = explode("+", $row['old_value']);

                                echo "<tr>";
                                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['pk_value'] . "</td>";
                                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($details[0])). "</td>";
                                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $details[2] . "</td>";
                                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . $row['login']. "</td>";
                                echo "<td align = 'center' style='padding-left: 30px; padding-right: 30px;'>" . date('Y-m-d h:i A',strtotime($row['date_changed'])) . "</td>";
                                echo "</tr>";

                            }
                        }
                    }
                }
            }
        }


        ?>
        </tbody>
    </table>
</div>

<?php
// echo $strSQL;die();
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

