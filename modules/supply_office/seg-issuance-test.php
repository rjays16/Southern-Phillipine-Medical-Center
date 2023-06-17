<?php
                                                                
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
require($root_path."modules/supply_office/ajax/issue.common.php");

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
        $breakfile = $root_path.'modules/supply_office/seg-issuance-test.php'.URL_APPEND."&userck=$userck&target=".$_GET['from'];
}

$imgpath=$root_path."pharma/img/";
$thisfile='seg-issuance-test.php';


$enc = array("1"=>"ER PATIENT", "2"=>'OUTPATIENT', "3"=>'INPATIENT (ER)', "4"=>'INPATIENT (OPD)');

# Start Smarty templating here
 /**
 * LOAD Smarty
 */
 
# Note: it is advisable to load this after the inc_front_chain_lang.php so
# that the smarty script can use the user configured template theme
        
include_once($root_path."include/care_api_classes/class_order.php");
$order_obj = new SegOrder("pharma");

include_once($root_path."include/care_api_classes/inventory/class_issuance.php");
$issue_obj = new Issuance();

include_once($root_path."include/care_api_classes/class_personell.php");
$persnl_obj = new Personell();

include_once($root_path."include/care_api_classes/inventory/class_expiry.php");
$expiry_obj = new Expiry();

include_once($root_path."include/care_api_classes/inventory/class_eodinventory.php");
$eod_obj = new EODInventory();

global $db;

require_once($root_path.'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');
    
if ($_GET["from"]=="CLOSE_WINDOW") {
 $smarty->assign('bHideTitleBar',TRUE);
 $smarty->assign('bHideCopyright',TRUE);
}

    
# Title in the title bar
$smarty->assign('sToolbarTitle',"Supplies::Issuance::New");

# href for the help button
$smarty->assign('pbHelp',"javascript:gethelp('products_db.php','input','$mode','$cat')");

# href for the close button
$smarty->assign('breakfile',$breakfile);

# Window bar title
$smarty->assign('sWindowTitle',"Supplies::Issuance::New");

$user_location = $HTTP_SESSION_VARS['sess_user_personell_nr'];



if($HTTP_SESSION_VARS['sess_user_personell_nr']) {
    $sqlLOC = "SELECT location_nr FROM care_personell_assignment WHERE personell_nr=".$HTTP_SESSION_VARS['sess_user_personell_nr'];  
    $resultLOC = $db->Execute($sqlLOC);                                                            
    $rowLOC = $resultLOC->FetchRow();
    
    $persnl = $persnl_obj->getPersonellInfo($HTTP_SESSION_VARS['sess_user_personell_nr']); 
}

if (isset($_POST["submitted"])) {
        
    $bulk = array();
    $total = 0;

   $sql =  "INSERT INTO seg_issuance (refno,issue_date,src_area_code,area_code,authorizing_id,issuing_id,issue_type) VALUES ('".$_POST['refno']."','".$_POST['issue_date']."','".$_POST['area_issued']."','".$_POST['area_dest']."',".$_POST['authorizing_id_hidden'].",".$_POST['issuing_id_hidden'].",'".$_POST['iss_type']."')";  

   $result = $db->Execute($sql);
   $error = $db->ErrorMsg();
   $okba = $db->Affected_Rows();

   include_once($root_path."include/care_api_classes/inventory/class_inventory.php");
   $inventory_obj = new Inventory();

    if ($okba) {
        
        $counter=0;
        $allqty=0;
        foreach($_POST["items"] as $i) {
            
             
            $inventory_obj->setInventoryParams($i,$_REQUEST['area_issued']);
            
            $allqty = $_REQUEST['pending'][$counter];
            
            $thissql = "SELECT is_unit_per_pc FROM seg_unit WHERE unit_id=".$_POST['unitid'][$counter];
            $thisresult = $db->Execute($thissql); 
            $rowsql = $thisresult->FetchRow();
            
            if($rowsql['is_unit_per_pc']=='0') {
                $sqlSTR="SELECT qty_per_pack FROM seg_item_extended WHERE item_code = '$i'";
                $myresult = $db->Execute($sqlSTR);
                $myrow=$myresult->FetchRow();
                $allqty = $allqty * $myrow["qty_per_pack"];  
            }
            $isscounters = $allqty;
           
            $fetchRequestedRefno = "SELECT DISTINCT a.refno FROM seg_internal_request_details as a JOIN seg_internal_request as b ON a.refno=b.refno WHERE (a.item_code = '$i' AND b.area_code_dest='".$_REQUEST['area_issued']."' AND b.area_code='".$_REQUEST['area_dest']."') ORDER BY b.request_date ASC"; 
            $resultRequestedRefno = $db->Execute($fetchRequestedRefno);
            while($rowRequestedRefno=$resultRequestedRefno->FetchRow()) {
            
                $sqlqty = "SELECT * from seg_internal_request as a
                        JOIN seg_internal_request_details as b ON a.refno=b.refno
                        JOIN seg_requests_served as c ON a.refno=c.request_refno
                        JOIN seg_item_extended as d ON d.item_code=b.item_code
                        WHERE (b.item_code='$i' AND b.item_code=c.item_code AND a.area_code_dest='".$_REQUEST['area_issued']."' AND a.area_code='".$_REQUEST['area_dest']."' AND a.refno='".$rowRequestedRefno['refno']."')
                        ORDER BY a.request_date ASC";
                $resultqty = $db->Execute($sqlqty);
                
                if(!$resultqty->EOF){
                    if($rowqty = $resultqty->FetchRow()){
                       
                        if($allqty>0){
                            
                            $requested_qty = $rowqty["item_qty"];
                            $totalserved_qty = 0;
                            
                            if($rowqty["is_unitperpc"]=='0'){
                                $requested_qty = $requested_qty * $rowqty["qty_per_pack"]; 
                            }
                            
                            $fetchAllServed="SELECT served_qty from seg_requests_served WHERE (request_refno='".$rowqty['request_refno']."' AND item_code='".$rowqty['item_code']."')";
                    
                            $resultAllServed = $db->Execute($fetchAllServed);
                            while($rowAllServed = $resultAllServed->FetchRow()){
                                $totalserved_qty += $rowAllServed['served_qty']; 
                            }
                            #start db trans                    
                            $db->StartTrans();
                            $bSuccess = FALSE;
                            
                            $kulang = $requested_qty - $totalserved_qty;
                        
                            if($kulang <= $allqty)
                            {
                                 if($rowqty['issue_refno']!='' && $kulang!=0){
                                    $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowqty['request_refno']."','".$_POST['refno']."','".$rowqty['item_code']."',$kulang)";
                                    $bSuccess = $db->Execute($sql101);
                                 }
                                 else {
                                     if($kulang!=0)
                                     {
                                         $sql100 = "UPDATE seg_requests_served SET served_qty=$kulang,issue_refno='".$_POST['refno']."' WHERE (item_code='".$rowqty['item_code']."' AND request_refno='".$rowqty['request_refno']."')"; 
                                         $bSuccess = $db->Execute($sql100);
                                     }
                                 }
                                 $allqty = $allqty - $kulang;
                            }
                            else
                            {
                                 if($rowqty['issue_refno']!='' && $allqty!=0){
                                     $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowqty['request_refno']."','".$_POST['refno']."','".$rowqty['item_code']."',$allqty)";
                                     $bSuccess = $db->Execute($sql101);
                                 }
                                 else {
                                     if($allqty!=0)
                                     {
                                         $sql100 = "UPDATE seg_requests_served SET served_qty=$allqty,issue_refno='".$_POST['refno']."' WHERE (item_code='".$rowqty['item_code']."' AND request_refno='".$rowqty['request_refno']."')"; 
                                         $bSuccess = $db->Execute($sql100);
                                     }
                                 }
                                 $allqty = $allqty - $allqty;
                            }
                            
                            if ($bSuccess){
                                $db->CompleteTrans();
                            }
                            else {
                                $db->FailTrans();
                                $db->CompleteTrans(); 
                            }

                        }    
                    }
                }
                else {
                    if($allqty > 0)
                    {
                        $db->StartTrans();
                        $bSuccess = FALSE;
                        $fetchRequested = "select * from seg_internal_request as a JOIN seg_internal_request_details as b on a.refno=b.refno WHERE a.refno='".$rowRequestedRefno['refno']."'";
                        $resultRequested = $db->Execute($fetchRequested);
                        if($rowRequested = $resultRequested->Fetchrow())
                        {
                            $requested_qty = $rowRequested["item_qty"];
                            
                            if($rowqty["is_unitperpc"]=='0'){
                                $requested_qty = $requested_qty * $rowRequested["qty_per_pack"]; 
                            }
                            
                            if($allqty >= $requested_qty) 
                            {
                                $toadd = $requested_qty;
                            
                                $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowRequestedRefno['refno']."','".$_POST['refno']."','$i',$toadd)";
           
                                $bSuccess = $db->Execute($sql101);
                                $allqty = $allqty - $requested_qty;
                            }
                            else
                            {
                                $toadd = $allqty;
                            
                                $sql101 = "INSERT INTO seg_requests_served (request_refno,issue_refno,item_code,served_qty) VALUES ('".$rowRequestedRefno['refno']."','".$_POST['refno']."','$i',$toadd)";
           
                                $bSuccess = $db->Execute($sql101);
                                $allqty = $allqty - $allqty;   
                            }
                            
                        }
                        
                        if ($bSuccess){
                            $db->CompleteTrans();
                        }
                        else {
                            $db->FailTrans();
                            $db->CompleteTrans(); 
                        }
                    } 
                } 
            }
            $globalexp = '-';
            $isscounters;
            //echo "before";
            if($_REQUEST['expdate'][$counter]!="-"){
                //echo "naay exp";
                $resultExp = $expiry_obj->getExpiriesofItem($i, $_REQUEST['area_issued']);
                //echo "item is:".$i." area is:".$_REQUEST['area_issued'];
                if($resultExp){
                    //echo "before while issuedate=".$_POST['issue_date'];
                    while($rowExp = $resultExp->FetchRow()){
                        if($isscounters>0){
                            //echo $rowExp['expiry_date'];
                            $expiryqty = $eod_obj->getCurrentEODQty($i, $_REQUEST['area_issued'], $_POST['issue_date'], $rowExp['expiry_date']);
                            //echo "exdate=".$rowExp['expiry_date']." expiryqty=".$expiryqty." issounters=".$isscounters;
                            if($expiryqty >= $isscounters){
                                //echo "if1";
                                $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,expiry_date,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",'".$rowExp['expiry_date']."',".$_POST['avg'][$counter].")";  
                                $inventory_obj->remInventory($isscounters, $_REQUEST['unitid'][$counter], $rowExp['expiry_date'], NULL,$_REQUEST['issue_date']);
                                $isscounters = 0;
                            }
                            else{
                                //echo "else2";
                                $sqlSTR="SELECT pc_unit_id FROM seg_item_extended WHERE item_code = '$i'";
                                $myresult2 = $db->Execute($sqlSTR);
                                $myrow2=$myresult2->FetchRow();
                                
                                $sqlexp = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,expiry_date,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$expiryqty.",".$myrow2['pc_unit_id'].",".$_POST['perpc'][$counter].",'".$rowExp['expiry_date']."',".$_POST['avg'][$counter].")";  
                                $inventory_obj->remInventory($expiryqty, $myrow2['pc_unit_id'], $rowExp['expiry_date'], NULL,$_REQUEST['issue_date']);
                            
                                $isscounters = $isscounters - $expiryqty;
                                
                                $db->StartTrans();
                               $bSuccess = FALSE;
                     
                               $bSuccess = $db->Execute($sqlexp);
                               
                                if ($bSuccess){

                                    $db->CompleteTrans();
                                }
                                else {

                                    $db->FailTrans();
                                    $db->CompleteTrans();
                                }
                            }
                        } 
                    }
                }
                 
            }  
            else if($_REQUEST['serial'][$counter]!="-"){
                
                $inventory_obj->setSerialObject('1',0.00,'2008-01-01',100);
                $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,serial_no,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",'".$_REQUEST['serial'][$counter]."',".$_POST['avg'][$counter].")";
    
                $inventory_obj->remInventory($_REQUEST['pending'][$counter], $_REQUEST['unitid'][$counter], NULL, $_REQUEST['serial'][$counter],$_REQUEST['issue_date']);     
            }  
            else { 
                $sql2 = "INSERT INTO seg_issuance_details (refno,item_code,item_qty,unit_id,is_unitperpc,avg_cost) VALUES ('".$_POST['refno']."','".$_POST['items'][$counter]."',".$_POST['pending'][$counter].",".$_POST['unitid'][$counter].",".$_POST['perpc'][$counter].",".$_POST['avg'][$counter].")";
                $inventory_obj->remInventory($_REQUEST['pending'][$counter], $_REQUEST['unitid'][$counter], NULL, NULL,$_REQUEST['issue_date']);
            }
           
           $db->StartTrans();
           $bSuccess = FALSE;
 
           $bSuccess = $db->Execute($sql2);
           
            if ($bSuccess){

                $db->CompleteTrans();
            }
            else {

                $db->FailTrans();
                $db->CompleteTrans();
            }
          
           $error2 = $db->ErrorMsg();
           $okba2 = $db->Affected_Rows();
           $counter++;
           
            
        }
        
        $smarty->assign('sMsgTitle','Supply issuance successfully saved!');
        $smarty->assign('sMsgBody','The issue details have been saved into the database...');
        $sBreakImg ='close2.gif';
        $smarty->assign('sBreakButton','<img class="segSimulatedLink" '.createLDImgSrc($root_path,$sBreakImg,'0','absmiddle').' alt="'.$LDBack2Menu.'" onclick="window.location=\''.$breakfile.'\'" onsubmit="return false;" style="cursor:pointer">');
        
        # Assign submitted form values
        $smarty->assign('sIssueDate', $_REQUEST['issue_date']);

        $smarty->assign('sRefNo', $_REQUEST['refno']);
        
        $smarty->assign('sAuthBy', $_REQUEST['authorizing_id']);
        $smarty->assign('sIssBy', $_REQUEST['issuing_id']);
        
        # need to edit
        $fetchAreaFromDepartment = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_issued']."'";
        $areaResult = $db->Execute($fetchAreaFromDepartment);
        $areaRow = $areaResult->FetchRow();
        
        $smarty->assign('sArea', $areaRow['area_name']);
        
        # need to edit
        $fetchAreaFromDepartment = "SELECT area_name FROM seg_areas WHERE area_code='".$_POST['area_dest']."'";
        $areaResult = $db->Execute($fetchAreaFromDepartment);
        $areaRow = $areaResult->FetchRow();
        
        $smarty->assign('sSrcArea', $areaRow['area_name']);
          
        foreach ($_REQUEST['items'] as $i=>$v){
            
            $items_table[] = "<tr><td>".$_REQUEST['items'][$i]."</td><td>".$_REQUEST['name'][$i]."</td><td>". $_REQUEST['pending'][$i] ."</td><td>". $_REQUEST['unitdesc'][$i] ."</td><td>". $_REQUEST['serial'][$i] ."</td><td>". $_REQUEST['expdate'][$i] ."</td></tr>";
            
        
        }

        $show_items = implode("",$items_table);
        $smarty->assign('sItems',$show_items);
        
        $smarty->assign('sMainBlockIncludeFile','supply_office/oksave.tpl');
        $smarty->display('common/mainframe.tpl');
        exit;
    }
    else {
        $errorMsg = $db->ErrorMsg();
        if (strpos(strtolower($errorMsg), "duplicate entry") !== FALSE)
            $smarty->assign('sysErrorMessage','<strong>Error:</strong> An Issuance with the same Ref number already exists in the database.');
        else
            $smarty->assign('sysErrorMessage',"<strong>Error:</strong> $errorMsg");
    }

}

# Assign Body Onload javascript code
$onLoadJS="onload=\"init()\"";
$smarty->assign('sOnLoadJs',$onLoadJS);
#$smarty->assign('bShowQuickKeys',!$_REQUEST['viewonly']);
$smarty->assign('bShowQuickKeys',FALSE);

# Collect javascript code
ob_start();
     # Load the javascript code
?>
<!-- OLiframeContent(src, width, height) script:
 (include WIDTH with its parameter equal to width, and TEXTPADDING,0, in the overlib call)
-->
<script type="text/javascript" src="<?=$root_path?>js/overlibmws/iframecontentmws.js"></script>

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

<!-- YU Library -->
<script type="text/javascript" src="<?=$root_path?>js/yui/yahoo/yahoo.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/event/event.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dom/dom.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/connection/connection.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/dragdrop/dragdrop.js" ></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container_core.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/yui/container/container.js"></script>
<link type="text/css" rel="stylesheet" href="<?=$root_path?>js/yui/container/assets/container.css">
<script type="text/javascript" src="js/issue-gui.js?t=<?=time()?>"></script>



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
 <script type="text/javascript" src="<?=$root_path?>js/gen_routines.js"></script>

<script type="text/javascript" language="javascript">
<!--
    var trayItems = 0;
    
    function init() {
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
        
        
        $('issuing_id').setAttribute('value',''); 
       
        
        callback = self.setInterval("checker()", 1);
        
         $('issuing_id_hidden').setAttribute('value','');
        

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
        var area_destination ="ALL";
        area = $('area_issued').value;
        area_destination = $('area_dest').value; 
        //alert(area);
        var url = 'seg-issue-tray.php?arealimit='+area+'&arealimitdest='+area_destination;
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

# Render form values
if (isset($_POST["submitted"]) && !$okba) {
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
                
    if (is_array($_POST['items'])) {
        include_once($root_path."include/care_api_classes/class_product.php");
        $prod_obj = new Product();
        $items_name_array = $prod_obj->getProductName($_REQUEST['items']);
        
        $script = '<script type="text/javascript" language="javascript">';

        $items = $_POST['items'];
        $pendingAdd = array();                                           
        $descAdd = array();
        $unitidAdd = array();
        $unitdescAdd = array();
        $perpcAdd = array();
        $expdateAdd = array();
        $serialAdd = array();
        //
        $avgAdd = array();
        
        #$ihap = 0;
        foreach ($items as $i=>$item) {
            $pendingAdd[$i] = $_POST['pending'][$i];
            $descAdd[$i] = $_POST['desc'][$i];
            $unitidAdd[$i] = $_POST['unitid'][$i];
            $unitdescAdd[$i] = $_POST['unitdesc'][$i];
            $perpcAdd[$i] = $_POST['perpc'][$i];
            $expdateAdd[$i] = $_POST['expdate'][$i];
            $serialAdd[$i] = $_POST['serial'][$i];
            //
            $avgAdd[$i] = $_POST['avg'][$i];
            #$items_namesAdd[$i] = $items_name_array[$i];
            #echo $items[$ihap].",".$items_name_array[$ihap].",";
            #$ihap++;
        }
        #/*
        $script .= "var item0 = ['" .implode("','",$items)."'];";
        $script .= "var item_name0= ['" .implode("','",$items_name_array)."'];";
        $script .= "var desc0 = ['" .implode("','",$descAdd). "'];";
        $script .= "var pending0 = [" .implode(",",$pendingAdd). "];";
        $script .= "var unitid0= [" .implode(",",$unitidAdd). "];";
        $script .= "var unitdesc0= ['" .implode("','",$unitdescAdd). "'];";
        $script .= "var perpc0 = [" .implode(",",$perpcAdd). "];";
        $script .= "var expdate0= ['" .implode("','",$expdateAdd). "'];";
        $script .= "var serial0 = ['" .implode("','",$serialAdd). "'];";
        $script .= "var avg0 = [" .implode(",",$avgAdd). "];";
        #*/
        /*
        $script .= "var item0= ".$_POST['items'].";";
        $script .= "var item_name0= ".$items_name_array.";";
        $script .= "var desc0 = ".$_POST['desc'].";";
        $script .= "var pending0 = ".$_POST['pending'].";";
        $script .= "var unitid0= ".$_POST['unitid'].";";
        $script .= "var perpc0 = ".$_POST['perpc'].";";
        */
        $script .= "xajax_add_item(item0, item_name0, desc0, pending0, unitid0, perpc0, unitdesc0, expdate0, serial0, avg0);";
        $script .= "</script>";
        $src = $script;
    }
    if ($src) $smarty->assign('sIssueItems',$src);
}
else {
    $smarty->assign('sIssueItems',"
                <tr>
                    <td colspan=\"8\">Issue list is currently empty...</td>
                </tr>");
}


# Render form elements
    $submitted = isset($_POST["submitted"]);
    $readOnly = ($submitted && (!$_POST['iscash'] || $_POST['pid'])) ? 'readonly="readonly"' : "";

    if ($person) {
        $_POST['pid'] = $person['pid'];
        $_POST['encounter_nr'] = $person['encounter_nr'];
        $_POST['ordername'] = $person['name_first']." ".$person['name_last'];
        
        $addr = implode(", ",array_filter(array($person['street_name'], $person["brgy_name"], $person["mun_name"])));
        if ($person["zipcode"])
            $addr.=" ".$person["zipcode"];
        if ($person["prov_name"])
            $addr.=" ".$person["prov_name"];
        $_POST['orderaddress'] = $addr;
        $_POST['discount_id'] = $person['discount_id'];
        $_POST['discount'] = $person['discount'];
    }
    
    require_once($root_path.'include/care_api_classes/class_product.php');
    $prod_obj=new Product;
    $prod=$prod_obj->getAllPharmaAreas();
    $disabled = (strtolower($_GET['area']) != 'all') ? ' disabled="disabled"' : '';
    $index = 0;
    $count = 0;
    $select_area = '';
    while($row=$prod->FetchRow()){
        $checked=strtolower($row['area_code'])==strtolower($_GET['area']) ? 'selected="selected"' : "";
        $select_area .= "    <option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
        if ($checked) $index = $count;
        $count++;
    }
    
    $smarty->assign('sRefno','<input id="refno" name="refno" type="text" value="'.$_POST['refno'].'"/>');
      
    $dbtime_format = "Y-m-d H:i";
    $fulltime_format = "F j, Y g:ia";
    if ($_REQUEST['dateset']) {
        //$curDate = date($dbtime_format,$_REQUEST['dateset']);
        //$curDate_show = date($fulltime_format, $_REQUEST['dateset']);
        $curDate = date($dbtime_format, strtotime($_REQUEST['dateset']));
        $curDate_show = date($fulltime_format, strtotime($_REQUEST['dateset']));
    }
    else {
        $curDate = date($dbtime_format);
        $curDate_show = date($fulltime_format);
    }
    
    $smarty->assign('sIssueDate','<span id="show_issuedate" class="jedInput" style="margin-left:0px; margin-top:3px; font-weight:bold; color:#0000c0; padding:0px 2px;width:80px; height:24px">'.($submitted ? date($fulltime_format,strtotime($_POST['orderdate'])) : $curDate_show).'</span><input class="jedInput" name="issue_date" id="issue_date" type="hidden" value="'.($submitted ? date($dbtime_format,strtotime($_POST['orderdate'])) : $curDate).'" style="font:bold 12px Arial">');
    $smarty->assign('sIssueCalendar','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="issuedate_trigger" class="segSimulatedLink" align="absmiddle" style="margin-left:0px;cursor:pointer">');
        $jsCalScript = "<script type=\"text/javascript\">
            Calendar.setup ({
                displayArea : \"show_issuedate\",
                inputField : \"issue_date\",
                ifFormat : \"%Y-%m-%d %H:%M\", 
                daFormat : \"    %B %e, %Y %I:%M%P\", 
                showsTime : true, 
                button : \"issuedate_trigger\", 
                singleClick : true,
                step : 1
            });
        </script>";
    $smarty->assign('jsCalendarSetup', $jsCalScript); 
    

    ############################################
    
    require_once($root_path.'include/care_api_classes/class_access.php');        
    require_once($root_path.'include/care_api_classes/class_department.php');
    
    $obj = new Access();    
    $dept_nr = $obj->getDeptNr($_SESSION['sess_temp_userid']);
    
    $objdept = new Department();
    $result = $objdept->getAreasInDept($dept_nr);
    
    $count = 0;    
    $s_areacode = '';
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['ori_area'])) || (strtolower($row['area_code']) == strtolower($_POST['area_issued'])) ? 'selected="selected"' : "";
            $ori_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            
            if ($checked || ($count == 0)) $s_areacode = $row['area_code'];                                    
            if ($checked) $index = $count;
            $count++;            
        }
    }
    else
        $ori_area = "<option value=\"\" $checked>- Select Requesting Area -</option>\n";
    
    $ori_area = '<select class="jedInput" id="area_issued" name="area_issued" onchange="jsRqstngAreaOptionChngIss(this, this.options[this.selectedIndex].value);">'."\n".$ori_area."</select>\n".
    //$ori_area = '<select class="jedInput" id="area_issued" name="area_issued" onchange="alert(this.options[this.selectedIndex].value);">'."\n".$ori_area."</select>\n".
                "<input type=\"hidden\" id=\"area2\" name=\"area2\" value=\"".$_GET['area_issued']."\"/>";
    $smarty->assign('sAreaIssued',$ori_area);    
    
    //dest    
    $result = $objdept->getAllAreas($s_areacode);
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['area_code'])==strtolower($_GET['area_dest'])) || (strtolower($row['area_code']) == strtolower($_POST['area_dest'])) ? 'selected="selected"' : "";
            $dest_area .= "<option value=\"".$row['area_code']."\" $checked>".$row['area_name']."</option>\n";
            if ($checked) $index = $count;
            $count++;
        }
        $dest_area = '<select class="jedInput" id="area_dest" name="area_dest" onchange="openOrderTray();">'."\n".$dest_area."</select>\n".
            "<input type=\"hidden\" id=\"area3\" name=\"area3\" value=\"".$_GET['area_dest']."\"/>";
        $smarty->assign('sAreaDest',$dest_area);
    }
    
    //issuance type
    $result = $issue_obj->getIssueType();
    $iss = "";
    if ($result) {
        while($row=$result->FetchRow()){
            $checked=(strtolower($row['iss_type_id'])==strtolower($_GET['iss_type'])) || (strtolower($row['area_code']) == strtolower($_POST['iss_type'])) ? 'selected="selected"' : "";
            $iss .= "<option value=\"".$row['iss_type_id']."\" $checked>".$row['iss_type_name']."</option>\n";
            if ($checked) $index = $count;
            $count++;
        }
        $issuetypes = '<select class="jedInput" id="iss_type" name="iss_type" >'."\n".$iss."</select>\n";
        $smarty->assign('sIssuanceType',$issuetypes);
    }
    
    
    ############################################
       
    $smarty->assign('sAuthorizedId','<input id="authorizing_id" name="authorizing_id" readonly="readonly" type="text" value="'.$_POST['authorizing_id'].'" size="20"/>');
    $smarty->assign('sAuthorizedButton','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer"
       onclick="keyF9()"
       onmouseout="nd();" />');  
    
    $smarty->assign('sIssuingId','<input id="issuing_id" name="issuing_id"  readonly="readonly" valign="absmiddle" type="text" value="'.$HTTP_SESSION_VARS['sess_login_username'].'" size="20" /> ');
    /*commented out by bryan on feb 20,2009
    $smarty->assign('sIssueButton','<img id="select-enc1" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer;"
       onclick="keyF10()"
       onmouseout="nd();" />');  
    */
    

# LINGAP/CMAP
if (true) {
    $sponsorHTML = '<select class="jedInput" name="sponsor" id="sponsor">
<option value="" style="font-weight:bold">No coverage</option>
';
    include_once($root_path."include/care_api_classes/class_sponsor.php");
    $sc = new SegSponsor();
    $sponsors = $sc->get();
    while($row=$sponsors->FetchRow()){
        $sponsorHTML .= "                                    <option value=\"".$row['sp_id']."\">".$row['sp_name']."</option>\n";
    }
    $sponsorHTML .= "                    </select>";
    $smarty->assign('sSponsor',$sponsorHTML);
}

$smarty->assign('sSWClass',($_POST['discountid'] ? $_POST['discountid'] : 'None'));
$smarty->assign('sNormalPriority','<input class="jedInput" type="radio" name="priority" id="p0" value="0" '.(($_POST["priority"]!="1")?'checked="checked" ':'').'/><label class="jedInput" for="p0">Normal</label>');
$smarty->assign('sUrgentPriority','<input class="jedInput" type="radio" name="priority" id="p1" value="1" '.(($_POST["priority"]=="1")?'checked="checked" ':'').'/><label class="jedInput" for="p1">Urgent</label>');
$smarty->assign('sComments','<textarea class="jedInput" name="comments" cols="14" rows="2" style="float:left; margin-left:3px;margin-top:3px">'.$_POST['comment'].'</textarea>');
/*
    if ($_REQUEST['billing'])
        $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="opacity:0.2"/>');
    else
        $smarty->assign('sSelectEnc','<img id="select-enc" src="../../images/btn_encounter_small.gif" border="0" style="cursor:pointer" onclick="keyF9()" onmouseout="nd();" />');
*/
$smarty->assign('sRootPath',$root_path);
$smarty->assign('sBtnAddItem','<img class="segSimulatedLink" id="add-item" src="'.$root_path.'images/btn_additems.gif" border="0" onclick="return openOrderTray();">');
$smarty->assign('sBtnEmptyList','<img class="segSimulatedLink" id="clear-list" src="'.$root_path.'images/btn_emptylist.gif" border="0" onclick="if (confirm(\'Clear the issuance list?\')) emptyTray()"/>');
$smarty->assign('sDiscountShow','<input type="checkbox" name="issc" id="issc" '.(($_POST["issc"])?'checked="checked" ':'').' onclick="seniorCitizen()"><label class="jedInput" for="issc" style="font:bold 11px Tahoma;">Senior citizen</label>');
    
if($error=="refno_exists"){
    $smarty->assign('sMascotImg',"<img ".createMascot($root_path,'mascot1_r.gif','0','absmiddle').">");
    $smarty->assign('LDOrderNrExists',"The reference no. entered already exists.");
}

$qs = "";
if ( $_GET['billing'] ) $qs .= "&billing=".$_GET['billing'];
if ( $_GET['pid'] ) $qs .= "&pid=".$_GET['pid'];
if ( $_GET['encounterset'] ) $qs .= "&encounterset=".$_GET['encounterset'];

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
    <input id="issuing_id_hidden" name="issuing_id_hidden" type="hidden" value="<?= $_SESSION['sess_user_personell_nr'] ?>"/>
    
    <input type="hidden" name="editpencnum"   id="editpencnum"   value="">    
    <input type="hidden" name="editpentrynum" id="editpentrynum" value="">
    <input type="hidden" name="editpname" id="editpname" value="">
    <input type="hidden" name="editpqty"  id="editpqty"  value="">
    <input type="hidden" name="editppk"   id="editppk"   value="">
    <input type="hidden" name="editppack" id="editppack" value="">
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
$smarty->assign('sContinueButton','<img src="'.$root_path.'images/btn_submitorder.gif" align="center" onclick="if (validate()) document.inputform.submit()"  style="cursor:pointer" />');

# Assign the form template to mainframe
$smarty->assign('sMainBlockIncludeFile','supply_office/supply-issuance-form.tpl');
$smarty->display('common/mainframe.tpl');

?>

<script>
function checker() {
  var name = $('issuing_id_hidden').value;
  if (name != '') {
    self.clearInterval(callback);
    
    jsAreaSRCOptionChngIss(name);
    
  }
}

var callback = self.setInterval("checker()", 100000);
</script>
