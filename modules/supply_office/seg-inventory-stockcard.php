<script language="javascript" type="text/javascript"> 
function openRpt(scpt, apnd){
        window.open('pdf_'+scpt+'.php'+apnd,null,'height=600,width=870,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');
    }
</script>

<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
require('./roots.php');
require($root_path.'include/inc_environment_global.php');
/**
* CARE2X Integrated Hospital Information System Deployment 2.1 - 2004-10-02
* GNU General Public License
* Copyright 2002,2003,2004,2005 Elpidio Latorilla
* elpidio@care2x.org
*
* See the file "copy_notice.txt" for the licence notice
*/
define('NO_2LEVEL_CHK',1);
define('LANG_FILE','cashier.php');
$local_user='ck_cashier_user';
require_once($root_path.'include/inc_front_chain_lang.php');

# Create products object
$dbtable='care_config_global'; // Table name for global configurations
$GLOBAL_CONFIG=array();
$new_date_ok=0;
# Create global config object
require_once($root_path.'include/care_api_classes/class_globalconfig.php');
require_once($root_path.'include/inc_date_format_functions.php');
require_once($root_path.'include/care_api_classes/class_department.php');
$dept_obj = new Department();               

require_once($root_path.'include/care_api_classes/class_pharma_product.php');
$prod_obj = new SegPharmaProduct();

require_once($root_path.'include/care_api_classes/inventory/class_adjustment.php');
$adj_obj = new SegAdjustment();

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

$breakfile=$root_path."modules/supply_office/seg-supply-functions.php".URL_APPEND."&userck=$userck";
$imgpath=$root_path."pharma/img/";
$thisfile='seg-inventory-stockcard.php';

# Start Smarty templating here
 /**
 * LOAD Smarty
 */

 # Note: it is advisable to load this after the inc_front_chain_lang.php so
 # that the smarty script can use the user configured template theme

 require_once($root_path.'gui/smarty_template/smarty_care.class.php');
 $smarty = new smarty_care('common');

 # Title in the title bar
 $smarty->assign('sToolbarTitle',"Inventory::Stock Card");

 # href for the back button
// $smarty->assign('pbBack',$returnfile);

 # href for the help button
 $smarty->assign('pbHelp',"javascript:gethelp('products_db.php','search','$from','$cat')");

 # href for the close button
 $smarty->assign('breakfile',$breakfile);

 # Window bar title
 $smarty->assign('sWindowTitle',"Inventory::Stock Card");

 # Assign Body Onload javascript code
$onLoadJS='';
if ($_POST['item_id_hidden']) {
        $append = "?area=".$_POST["list_position_area"]."&date=".$_POST["dt"]."&item=".$_POST["item_id_hidden"];  
        $report = "stockcard";
        $onLoadJS.="<script language='javascript' type='text/javascript'> openRpt('$report','$append') </script";
    }
 $smarty->assign('sOnLoadJs',$onLoadJS);

 # Collect javascript code
 ob_start()

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

<script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/fat/fat.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?= $root_path ?>js/jscalendar/calendar-win2k-cold-1.css" />
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/lang/calendar-en.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/jscalendar/calendar-setup_3.js"></script>
<script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
<script language="javascript" type="text/javascript">
<!--
    var URL_FORWARD = "<?= URL_APPEND."&clear_ck_sid=$clear_ck_sid" ?>";

    function pSearchClose() {
        cClick();
    }
    
    function selOnChange() {
        var optSelected = $('selreport').options[$('selreport').selectedIndex];
        var spans = document.getElementsByName('selOptions');
        for (var i=0; i<=spans.length; i++) {
            if (optSelected) {
                if (spans[i].getAttribute("segOption") == optSelected.value) {
                    spans[i].style.display = "";
                }
                else
                    spans[i].style.display = "none";
            }
        }
    }
    
    function openItemsTray() {

        var url = 'seg-inventory-stockcard-tray.php';
        overlib(
            OLiframeContent(url, 660, 397, 'fOrderTray', 0, 'no'),
            WIDTH,600, TEXTPADDING,0, BORDER,0, 
            STICKY, SCROLL, CLOSECLICK, MODAL,
            CLOSETEXT, '<img src=<?=$root_path?>images/close_red.gif border=0 >',
            CAPTIONPADDING,2, 
            CAPTION,'Select Item',
            MIDX,0, MIDY,0, 
            STATUS,'Select Item');
        return false
    }

-->
</script>

<?php

$sTemp = ob_get_contents();
ob_end_clean();
$smarty->append('JavaScript',$sTemp);
# Buffer page output
#include($root_path."include/care_api_classes/class_order.php");
#$order = new SegOrder('pharma');
//dito
$smarty->assign('sFormStart','<form ENCTYPE="multipart/form-data" action="'.$thisfile.'" method="post" name="inputform" >');
$smarty->assign('sFormEnd','</form>');
 
$dbtime_format = "Y-m-d H:i";
$fulltime_format = "F j, Y g:ia";
if ($_REQUEST['dateset']) {
    $curDate = date($dbtime_format,$_REQUEST['dateset']);
    $curDate_show = date($fulltime_format, $_REQUEST['dateset']);
}
else {
    $curDate = date($dbtime_format);
    $curDate_show = date($fulltime_format);
} 
 
$options="";
$options2="";

$options2="<option value=''>-All areas-</option>";
if($result = $dept_obj->getAllAreas()){
    while($row=$result->FetchRow()){
        $options2.="<option value=\"".$row['area_code']."\">".$row['area_name']." </option>\n";
    }
}
$smarty->assign('sSCSelectArea',"<select name=\"list_position_area\" id=\"list_position_area\" class='jedInput'>
$options2
</select>");


$smarty->assign('sGenerateButton',"<input type='button' value='View report' class='jedButton' onclick='return prufformreport(this)'/>");
/*
$smarty->assign('sSCDateInput','<input type="text" name="dt" id="date" value="">');
$smarty->assign('sSCDateIcon','<img ' . createComIcon($root_path,'show-calendar.gif','0') . ' id="date_trigger" align="absmiddle" style="cursor:pointer">[YYYY-mm-dd]'); 
*/
$smarty->assign('sSCItemInput','<input type="text" id="item_id" name="item_id" readonly value=""><input type="hidden" id="item_id_hidden" name="item_id_hidden" value="">');
$smarty->assign('sSCItemIcon',' <img id="select-item" ' . createComIcon($root_path,'btn_encounter_smalls.gif','0') . ' align="absmiddle" style="cursor:pointer" onclick="openItemsTray();"/>');

$jsCalScript = "<script type=\"text/javascript\">
    
Calendar.setup (
{
    inputField : \"date\", 
    ifFormat : \"%Y-%m-%d\", 
    showsTime : false, 
    button : \"date_trigger\", 
    singleClick : true, 
    step : 1
}
);

function prufformreport(d){
    var x = document.getElementById('item_id_hidden');
    var y = document.getElementById('list_position_area');
    //alert(x.value);
      if (x.value=='') {
            alert('Select item first..');
            x.focus();
            return false;
      
     /*}else if (x.value==5) {
            alert('Enter the starting date.');
            d.from_date.focus();
            return false;
     }else if (x.value==5 ) {
            alert('Enter the end date.');
            d.to_date.focus();
            return false;*/
     }
     else if(y.value==''){
            alert('Select area first..');
            y.focus();
            return false;     
     }
     else{
        //openReport();
        document.inputform.submit();
        //window.open('seg-invreport-list_expiry.php',null,'height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,resizable=yes');
        return true;
     }    
}

    function openReport() {
        /*var rep = $('selreport').options[$('selreport').selectedIndex].value
        var url = 'seg-invreport-'+rep+'.php?'
        var query = new Array()
        var params = document.getElementsByName('param')
        for (var i=0; i<params.length; i++) {
            if (params[i].getAttribute('segOption') == rep) {
                var mit;
                if (params[i].type=='checkbox') mit=params[i].checked;
                else if (params[i].type=='radio') mit=params[i].checked;
                else mit=params[i].value;
                if (mit) query.push(params[i].getAttribute('paramName')+'='+params[i].value)
            }
        }
        //alert(url+query.join('&'))
        window.open(url+query.join('&'),rep,'width=800,height=600,menubar=no,resizable=yes,scrollbars=no');*/
    }
    
    
</script>
";    
    $smarty->assign('jsCalendarSetup', $jsCalScript);

ob_start();
?>

<script type="text/javascript">
    
    function DisplayDept(rep_nr){
        document.getElementById('area_row').style.display='none';    
        document.getElementById('fromdate_row').style.display='none';
        document.getElementById('todate_row').style.display='none';
        document.getElementById('date_row').style.display='none';
        document.getElementById('expdate_row').style.display='none';
        document.getElementById('percent_row').style.display='none';
        document.getElementById('itemtype_row').style.display='none'; 
        if ((rep_nr==1)||(rep_nr==2)||(rep_nr==3)||(rep_nr==4)||(rep_nr==5)||(rep_nr==6)||(rep_nr==7)){
            document.getElementById('area_row').style.display='';
        }
        if((rep_nr==1)||(rep_nr==3)||(rep_nr==4)||(rep_nr==6)||(rep_nr==7)){
            document.getElementById('date_row').style.display='';
        }
        if((rep_nr==2)){
            document.getElementById('expdate_row').style.display='';
        }
        if((rep_nr==4)){
            document.getElementById('itemtype_row').style.display='';
        }
        if((rep_nr==5)){
            document.getElementById('fromdate_row').style.display='';
            document.getElementById('todate_row').style.display='';
            document.getElementById('percent_row').style.display='';
        }
        if((rep_nr==7)){
            document.getElementById('adjusttype_row').style.display='';
        } 
    }
    
    
</script>

<br>

<form action="<?= $thisfile.URL_APPEND."&target=list&clear_ck_sid=".$clear_ck_sid ?>" method="post" name="suchform" onSubmit="return validate()">
<div style="width:500px">
    <table width="100%" border="0" style="font-size:12px; margin-top:5px" cellspacing="2" cellpadding="2">    
        <tbody>
        </tbody>
    </table>
</div>

<?php

# Workaround to force display of results form
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

<input type="hidden" name="sid" value="<?php echo $sid ?>">
<input type="hidden" name="lang" value="<?php echo $lang ?>">
<input type="hidden" name="userck" value="<?php echo $userck ?>">
<input type="hidden" name="cat" value="<?php echo $cat?>">
<input type="hidden" name="userck" value="<?php echo $userck?>">  
<input type="hidden" name="dstamp" value="<?php echo  str_replace("_",".",date(Y_m_d))?>">  
<input type="hidden" name="tstamp" value="<?php echo  str_replace("_",".",date(H_i))?>">  
<input type="hidden" name="lockflag" value="<?php echo  $lockflag?>">

<input type="hidden" id="delete" name="delete" value="" />
<input type="hidden" id="page" name="page" value="<?= $current_page ?>" />
<input type="hidden" id="lastpage" name="lastpage"  value="<?= $last_page ?>" />
<input type="hidden" id="jump" name="jump">



</form>
<?php

$sTemp = ob_get_contents();
ob_end_clean();

$sBreakImg ='cancel.gif';
$smarty->assign('sHiddenInputs',$sTemp);
$smarty->assign('sBreakButton','<input class="segInput" type="button" align="center" value="Cancel payment">');

# Assign the form template to mainframe

 $smarty->assign('sMainFrameBlockData',$sTemp);
 $smarty->assign('sMainBlockIncludeFile','supply_office/inventory-stockcard.tpl');

 /**
 * show Template
 */
 $smarty->display('common/mainframe.tpl');
?>