
<?php 


error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');


require($root_path."modules/supply_office/ajax/issue-acknowledge-common.php");

/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
0* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','products.php');
$local_user='ck_prod_order_user';  

global $db;


require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');

$glob_obj=new GlobalConfig($GLOBAL_CONFIG);
if($glob_obj->getConfig('date_format')) $date_format=$GLOBAL_CONFIG['date_format'];
$date_format=$GLOBAL_CONFIG['date_format'];
$phpfd=$date_format;
$phpfd=str_replace("dd", "%d", strtolower($phpfd));
$phpfd=str_replace("mm", "%m", strtolower($phpfd));
$phpfd=str_replace("yyyy","%Y", strtolower($phpfd));
$phpfd=str_replace("yy","%y", strtolower($phpfd));

$php_date_format = strtolower($date_format);
$php_date_format = str_replace("dd","d",$php_date_format);
$php_date_format = str_replace("mm","m",$php_date_format);
$php_date_format = str_replace("yyyy","Y",$php_date_format);
$php_date_format = str_replace("yy","y",$php_date_format);

$title=$LDPharmacy;
if (!$_GET['from'])
    $breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
else {
    if ($_GET['from']=='CLOSE_WINDOW')
        $breakfile = "javascript:if (window.parent.myClick) window.parent.myClick(); else window.parent.cClick();";
    else
        $breakfile = $root_path.'modules/supply_office/seg-issuance-acknowledge.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-issue-details.php';


$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

# Start Smarty templating here
 /**                          
 * LOAD Smarty
 */
 
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
include_once($root_path."include/care_api_classes/class_order.php");
$order_obj = new SegOrder("pharma");

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
    
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

$smarty->assign('bHideTitleBar',TRUE);    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::Issuance::Acknowledge");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Supplies::Issuance::Acknowledge");

$user_location = $HTTP_SESSION_VARS['sess_user_personell_nr'];

if($HTTP_SESSION_VARS['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$HTTP_SESSION_VARS['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
}


# Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

//$smarty->assign('sHeaderRef',$_REQUEST['refno']);

# Collect javascript code
ob_start();
     # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>


 
<!-- <script type="text/javascript" src="js/issue-acknowledge-gui.js"></script> -->

<script type="text/javascript" src="js/issue-acknowledge.js"></script>  

<!-- Core module and plugins:
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/setdatetime.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/checkdate.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>

<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">




<!-- START for setting the DATE (NOTE: should be IN this ORDER) -->
<script type="text/javascript" language="javascript">
<?php
    require_once($root_path.'include/inc_checkdate_lang.php'); 
?>
</script>
<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/shortcut.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>


<script type="text/javascript" language="javascript">
<!--
    var trayItems = 0;
    
    function init() {
<?php
    if (!$_REQUEST['viewonly']) {
?>
        // Edit/Submit shortcuts
        shortcut.add('F2', keyF2,
            {
                'type':'keydown',
                'propagate':false,
            }
        );
        shortcut.add('F3', keyF3,
            {
                'type':'keydown',
                'propagate':false,
            }
        );
        shortcut.add('F9', keyF9,
            {
                'type':'keydown',
                'propagate':false,
            }
        );
        shortcut.add('F12', keyF12,
            {
                'type':'keydown',
                'propagate':false,
            }
        );
<?php
    }
?>
      //  refreshDiscount();
    }
    
    function keyF2() {
        openOrderTray();
    }
    
    function keyF3() {
        if (confirm('Clear the issue list?'))    emptyTray();
    }
    
    function keyF9() {

        if (warnClear()) { 
            emptyTray(); overlib(
        OLiframeContent('issue-select-personnel.php',
                700, 400, 'select_personnel', 0, 'no'),
        WIDTH,700, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2, 
                CAPTION,'Select registered personnel',
        MIDX,0, MIDY,0, 
        STATUS,'Select registered personnel'); 
        } 
        return false;
    }
    
    function keyF10() {

        if (warnClear()) { 
            emptyTray(); overlib(
        OLiframeContent('issue-select-personnel2.php',
                700, 400, 'select_personnel', 0, 'no'),
        WIDTH,700, TEXTPADDING,0, BORDER,0,
                STICKY, SCROLL, CLOSECLICK, MODAL,
                CLOSETEXT, '<img src=<?= $root_path ?>/images/close_red.gif border=0 >',
        CAPTIONPADDING,2,              
                CAPTION,'Select registered personnel',
        MIDX,0, MIDY,0, 
        STATUS,'Select registered personnel'); 
        } 
        return false;
    }

    function keyF12() {
        if (validate()) document.inputform.submit()
    }
    function openOrderTray() {
        var area = "ALL";
        var url = 'seg-issue-tray.php';
        overlib(
            OLiframeContent(url, 660, 420, 'fOrderTray', 0, 'no'),
            WIDTH,660, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'Add Item for Issuance tray',
            MIDX,0, MIDY,0, 
            STATUS,'Add Item for Issuance tray');
        return false
    }
    
    function validate() {
        if (!$('refno').value) {
            alert("Please enter the reference no.");
            $('refno').focus();
            return false;
        }
        if (!$('authorizing_id').value) {
            alert("Please select a registered person for authorization using the person search function...");
            return false;
        }
        if (!$('issuing_id').value) {
            alert("Please select a registered person for issuance using the person search function...");
            return false;
        }
        if (document.getElementsByName('items[]').length==0) {
            alert("Item list is empty...");
            return false;
        }
        return confirm('Process this supply issuance?');
    }
   function openIssueDetails(refno,area) {
        //var area = "ALL";
        var url = 'seg-issue-details.php?refno='+refno+'&destination='+area;
        overlib(
            OLiframeContent(url, 660, 420, 'fOrderTray', 0, 'no'),
            WIDTH,660, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'View Issuance Details for Acknowledgement',
            MIDX,0, MIDY,0, 
            STATUS,'View Issuance Details for Acknowledgement');
        return false
    }
-->
</script>

<?php

$xajax->printJavascript($root_path.'classes/xajax_0.5');
$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);

# Assign prompt messages

$lastnr = $order_obj->getLastNr(date("Y-m-d"));


if ($_REQUEST['encounterset']) {
    $person = $order_obj->getPersonInfoFromEncounter($_REQUEST['encounterset']);
}

if($HTTP_SESSION_VARS['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$HTTP_SESSION_VARS['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
}
/*
$smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
 */   
include_once($root_path."include/care_api_classes/inventory/class_issuance.php");
$issue_obj = new Issuance();
//echo "user: ".$HTTP_SESSION_VARS['sess_user_personell_nr']."<br>destination: ".$_REQUEST['destination']."<br>";
 
if (isset($_POST["submitted"])) {
    
    foreach ($_REQUEST['itemcode'] as $i=>$v){
            
            //echo "submitted.<br>".$_REQUEST['itemcode'][$i];
            //echo $_REQUEST['acknowledge'][$i]."<br>";
            if($_REQUEST['acknowledge'][$i] == 'approve'){
                
                //echo "since on ang checkbox, eto ang value ng itemcode: ".$_REQUEST['itemcode'][$i];
                $issue_obj->acknowledgeIssuance($_REQUEST['refno'], $HTTP_SESSION_VARS['sess_user_personell_nr'], $_REQUEST['itemcode'][$i]);
                //$items_table[] = "<tr><td>".$_REQUEST['items'][$i]."</td><td>".$_REQUEST['name'][$i]."</td><td>". $_REQUEST['pending'][$i] ."</td><td>". $_REQUEST['unitdesc'][$i] ."</td><td>". $_REQUEST['serial'][$i] ."</td><td>". $_REQUEST['expdate'][$i] ."</td></tr>";
                include_once($root_path."include/care_api_classes/inventory/class_inventory.php");
                $inventory_obj = new Inventory();
                $inventory_obj->setInventoryParams($_REQUEST['itemcode'][$i],$_REQUEST['destination']);
 
                if($_REQUEST['expiry'][$i]=="-" && $_REQUEST['serial'][$i]!="-"){
                    $inventory_obj->setSerialArray('00',0,'0000-00-00','1');
                    $inventory_obj->addInventory($_REQUEST['qty'][$i], $_REQUEST['unitid'][$i], NULL, $_REQUEST['serial'][$i]);
                }
                else if($_REQUEST['serial'][$i]=="-" && $_REQUEST['expiry'][$i]!="-"){
                    $inventory_obj->addInventory($_REQUEST['qty'][$i], $_REQUEST['unitid'][$i], $_REQUEST['expiry'][$i], NULL);
                }
                else{
                    $inventory_obj->addInventory($_REQUEST['qty'][$i], $_REQUEST['unitid'][$i], NULL, NULL);
                }
            }
        
        }    
}            
        

$i = 0;
$script = '<script type="text/javascript" language="javascript">'; 
$itemcodeAdd = array();
$qtyAdd = array();
$unitidAdd = array();
$perpcAdd = array();
$serialAdd = array();
$expiryAdd = array();

$itemnameAdd = array();
$unitnameAdd = array();
 

$resultDetails = $issue_obj->getIssuanceDetailsbyRef($_REQUEST['refno']);
//echo "paif na<br>".$_REQUEST['refno'].",".$resultDetails; 
if($resultDetails!=NULL){
    //echo "dili null ang result<br>";
    while($issueRow = $resultDetails->fetchRow()){
        $itemcodeAdd[$i] = $issueRow['item_code'];
        
        $sql = "SELECT artikelname FROM care_pharma_products_main WHERE bestellnum='".$issueRow['item_code']."'";
        $result = $db->Execute($sql);
        $row = $result->FetchRow();
        
        $itemnameAdd[$i] = $row['artikelname'];
        
        $qtyAdd[$i] = $issueRow['item_qty'];
        $unitidAdd[$i] = $issueRow['unit_id'];
        
        $sql = "SELECT unit_name FROM seg_unit WHERE unit_id='".$issueRow['unit_id']."'";
        $result = $db->Execute($sql);
        $row = $result->FetchRow();
        
        $unitnameAdd[$i] = $row['unit_name'];
        
        $perpcAdd[$i] = $issueRow['is_unitperpc'];
        if($issueRow['serial_no']!= "") $serialAdd[$i] = $issueRow['serial_no'];
        else $serialAdd[$i] = "-";
        if($issueRow['expiry_date']!= "0000-00-00") $expiryAdd[$i] = $issueRow['expiry_date'];
        else $expiryAdd[$i] = "-";
        //echo "yeah".$itemcodeAdd[$i].",".$qtyAdd[$i].",".$unitidAdd[$i].",                                                                                                                                                                                                                                                                                 ".$perpcAdd[$i].",".$serialAdd[$i].",".$expiryAdd[$i]."<br>";
        $i++;
    }

$script .= "var itemcode0 = ['" .implode("','",$itemcodeAdd)."'];";
$script .= "var qty0= [" .implode(",",$qtyAdd)."];";
$script .= "var unitid0 = [" .implode(",",$unitidAdd). "];";
$script .= "var perpc0 = [" .implode(",",$perpcAdd). "];";
$script .= "var serial0= ['" .implode("','",$serialAdd). "'];";
$script .= "var expiry0= ['" .implode("','",$expiryAdd). "'];";

$script .= "var itemname0= ['" .implode("','",$itemnameAdd). "'];";
$script .= "var unitname0= ['" .implode("','",$unitnameAdd). "'];"; 

/*
$script .= "alert(itemcode0);"; 
$script .= "alert(qty0);";
$script .= "alert(unitid0);";
$script .= "alert(perpc0);";
$script .= "alert(serial0);";
$script .= "alert(expiry0);";
*/
$script .= "xajax_populateIssueDetailsAck(itemcode0, qty0, unitid0, perpc0, serial0, expiry0, itemname0, unitname0);";
$script .= "</script>";
$src = $script;

if ($src) $smarty->assign('sIssueItems',$src);
    if($i == 0){
        $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
    
    }

}
else {
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
}


$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.URL_APPEND."&clear_ck_sid=".$clear_ck_sid.$qs.'&from='.$_GET['from'].'" method="POST" id="orderForm" name="inputform" onSubmit="return validate()">');
$smarty->assign('sFormEnd','</form>');

ob_start();
$sTemp='';

?>
    <input type="hidden" name="submitted" value="1" />
  <input type="hidden" name="sid" value="<?php echo $sid?>">
  <input type="hidden" name="lang" value="<?php echo $lang?>">
  <input type="hidden" name="cat" value="<?php echo $cat?>">
  <input type="hidden" name="userck" value="<?php echo $userck?>">  
  <input type="hidden" name="mode" id="modeval" value="<?php if($saveok) echo "update"; else echo "save"; ?>">
  <input type="hidden" name="encoder" value="<?php echo  str_replace(" ","+",$HTTP_COOKIES_VARS[$local_user.$sid])?>">
  <input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">
  <input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">
  <input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">
  <input type="hidden" name="update" value="<?php if($saveok) echo "1"; else echo $update;?>">
  <input type="hidden" name="target" value="<?php echo $target ?>">
  
  <input id="discount" name="discount" type="hidden" value="'.$_POST["discount"].'"/>
  <input id="encounter_nr" name="encounter_nr" type="hidden" value="'.$_POST["encounter_nr"].'"/>
    
    <input id="authorizing_id_hidden" name="authorizing_id_hidden" type="hidden" value="<?= $_REQUEST['authorizing_id_hidden'] ?>"/>
    <input id="issuing_id_hidden" name="issuing_id_hidden" type="hidden" value="<?= $_REQUEST['issuing_id_hidden'] ?>"/>
    
    <input type="hidden" name="editpencnum"   id="editpencnum"   value="">    
    <input type="hidden" name="editpentrynum" id="editpentrynum" value="">
    <input type="hidden" name="editpname" id="editpname" value="">
    <input type="hidden" name="editpqty"  id="editpqty"  value="">
    <input type="hidden" name="editppk"   id="editppk"   value="">
    <input type="hidden" name="editppack" id="editppack" value="">
    <input type="hidden" name="refno" id="refno" value="<?= $_REQUEST['refno'] ?>">
    <input type="hidden" name="destination" id="destination" value="<?= $_REQUEST['destination'] ?>">
    <input type="hidden" name="billing" id="billing" value="<?= $_REQUEST['billing'] ?>">
    <input type="hidden" name="dateset" id="dateset" value="<?= $_REQUEST['dateset'] ?>">
    <input type="hidden" name="encounterset" id="encounterset" value="<?= $_REQUEST['encounterset'] ?>">
<?php 

$sTemp = ob_get_contents();
ob_end_clean();

/*
global $GPC;
echo $GPC;
echo "<hr>sid:$sid;clear:$clear_ck_sid";
*/

$sBreakImg ='close2.gif';    
$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<img '.createLDImgSrc($root_path,$sBreakImg,'0','center').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-acknowledge-details.tpl');
$smarty->display('common/mainframe.tpl');

?>
