<?php
    error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
    require('./roots.php');

    require($root_path.'include/inc_environment_global.php');
    require($root_path."modules/radiology/ajax/radio-readers-fee-common.php");

    define('LANG_FILE','lab.php');
    define('NO_2LEVEL_CHK',1);
    $local_user='ck_radio_user';
    require_once($root_path.'include/inc_front_chain_lang.php');
    $thisfile=basename(__FILE__);
    $title=$LDLab;
    $breakfile=$root_path."modules/radiology/seg-close-window.php".URL_APPEND."&userck=$userck";

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

    # href for the help button
    $smarty->assign('pbHelp',"javascript:gethelp('submenu1.php','$LDLab')");

    # href for the close button
    $smarty->assign('breakfile',$breakfile);

    # Window bar title
    $smarty->assign('sWindowTitle',"$title $LDLabDb $LDSearch");

    ob_start();

    require_once($root_path . '/frontend/bootstrap.php');
    $sonoligist_list = Config::get('all_sonologist');

    global $db, $dbf_nodate;
    //added by: Borj Radiology Readers Fee 2014-10-17
    $pid = $_GET['pid'];
    $type = $_GET['code']. " " .$_GET['type'];
    $batch_nr = $_GET['batch_nr'];
    $service = $_GET['code'];

    $sql ="SELECT 
        ce.`encounter_nr` AS sbeencounternr, ce.encounter_date AS encdate
        FROM care_test_request_radio ctr
        INNER JOIN seg_radio_serv srs ON srs.`refno`=ctr.`refno`
        LEFT JOIN care_encounter AS ce ON ce.encounter_nr=srs.`encounter_nr`
        LEFT JOIN seg_billing_encounter AS sbe ON sbe.encounter_nr=ce.encounter_nr
        WHERE ctr.`refno`=".$db->qstr($batch_nr)."
        GROUP BY sbe.`encounter_nr`";

        $echo_data = $db->GetRow($sql);
        $encounter_nr2 = $echo_data['sbeencounternr'];
        $encdate2 = $echo_data['encdate'];


    $sql_services = "SELECT srs.pf FROM seg_radio_services as srs WHERE srs.service_code=".$db->qstr($service);
    if($_GET['dept']=='OB'){
        $pf_amount = $db->GetOne($sql_services);
    }

    $sql_radio_serv = "SELECT r.is_cash,r.fromdept,r.request_date FROM seg_radio_serv AS r WHERE r.refno=".$db->qstr($batch_nr);
    $fetch_data = $db->GetRow($sql_radio_serv);
    $is_cash = $fetch_data['is_cash'];
    $fromdept = $fetch_data['fromdept'];
    $request_dt = $fetch_data['request_date'];
   

?>
    <script type="text/javascript">
    <!--
    // -->
 
// alert(fromdept);
  
    </script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery-1.8.2.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jsprototype/prototype.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/NumberFormat154.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
    <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.11.0/themes/smoothness/jquery-ui.css"> -->
    <link rel="stylesheet" href="/resources/demos/style.css">

    <link rel="stylesheet" href="<?= $root_path ?>js/jquery/themes/seg-ui/jquery.ui.all.css" type="text/css" />
    <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui-1.9.1.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/jquery/jquery.maskedinput.js"></script>

    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_draggable.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_filter.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_overtwo.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_scroll.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_shadow.js"></script>
    <script type="text/javascript" src="<?=$root_path?>js/overlibmws/overlibmws_modal.js"></script>
    <!-- <link rel="stylesheet" href="<?= $root_path ?>css/bootstrap/bootstrap.min.css" type="text/css" /> -->

<?php
    $xajax->printJavascript($root_path.'classes/xajax');
?> 

<script type="text/javascript">
    $j(document).ready(function(){
        var fromdept = "<?= $fromdept?>";
        // alert("xx");
    if(fromdept =='OBGUSD'){
    $j('#amount').prop('readonly', true);
    var request_dt =  "<?= $request_dt ?>";
    var arr = request_dt.split("-");
    var dt_enc = new Date(request_dt);
    var date_enc = dt_enc.getDate();
    var month_enc = dt_enc.getMonth();
    var year_enc = dt_enc.getFullYear();
    var minDate = new Date(year_enc, month_enc, date_enc);
    // alert(minDate);
     $j("#datepickers").datepicker({
            minDate : minDate,
             maxDate: 0,
        });
    }else{
         $j("#datepickers").datepicker();
    }


        $j('#btnsave').click(function(){
            var encounter_nr3 = "<?= $encounter_nr2 ?>";
            var doc_list = $j('#doc_list option:selected').val();
            var datepickers = $j('#datepickers').val();
            var amount = $j('#amount').val();
            var dr_role_type_nr = 3;
            var create_dt = "<?= $encdate2 ?>";
            var service = "<?= $service ?>"
            var is_cash = "<?= $is_cash ?>";
            var fromdept = "<?= $fromdept?>";
            var accomodation_type = 2;
            var batch_nr = "<?= $batch_nr?>";
            var from_ob = 0;
            if(amount <= 0){
                alert("Invalid amount");
                return false;
            }
            if(datepickers.trim()==''){
                alert("Invalid Date");
                return false;
            }
            if(doc_list=='name'){
                 alert("Please select physician");
                return false;

            }
            if((fromdept=='RD') || (fromdept=='OBGUSD' && is_cash==0)){
                    if(fromdept=='OBGUSD'){
                        xajax_savereadersOB(encounter_nr3, doc_list, datepickers, amount, dr_role_type_nr, create_dt, service, is_cash);
                      }else{
                        xajax_savereaders(encounter_nr3, doc_list, datepickers, amount, dr_role_type_nr, create_dt, service, is_cash);
                      }
                
            }
                xajax_savedoctorspf(batch_nr, doc_list, amount, accomodation_type,service,datepickers);
            $j(this).attr('disabled','disabled');
        });


        $j('#amount').keypress(function(e){
            var x=e.which||e.keycode;
                if((x>=48 && x<=57) || x==8 ||
                    (x>=35 && x<=40)|| (x==46 && this.value.split('.').length === 1))
                    return true;
                else
                    return false;
        });



    });
</script>

<?php
#doc_list  
if($_GET['dept']=='OB'){
    $ob_doc_list_sql = 'AND ps.`nr` IN('.$sonoligist_list.')';
    $ob_status = " AND ps.status NOT IN ('hidden','inactive','void','deleted')";
}
    $sql_doc_list ="SELECT DISTINCT
        p.name_last, p.name_middle, p.name_first,ps.nr
        FROM care_personell_assignment AS a,
             care_personell AS ps,
             care_person AS p,
             care_department AS d
        WHERE a.location_type_nr=1
        AND (ps.short_id LIKE 'D%')
        AND (a.date_end=".$db->qstr($dbf_nodate)." OR a.date_end>=".$db->qstr(date('Y-m-d')).")
        AND a.status NOT IN ('hidden','inactive','void')
        $ob_status
        AND a.personell_nr = ps.nr
        AND ps.pid = p.pid
        AND a.location_nr = d.nr
        AND p.name_first <> '-' $ob_doc_list_sql
        ORDER BY p.name_last;";
    $rs_doc_list = $db->Execute($sql_doc_list);
    $doc_list_option="<option value='name'>-SELECT DOCTOR-</option>";

    if (is_object($rs_doc_list)){
        while ($row_doc_list=$rs_doc_list->FetchRow()) {
            if($_GET['dept']!='OB'){
            $selected='';
            if ($doc_list==$row_doc_list['name'])
                $selected='selected';
            }
            $slast = $row_doc_list['name_last'];
            $sfirst = $row_doc_list['name_first'];
            $smid = $row_doc_list['name_middle'];
            $stmp = "";
            if (!empty($slast)) $stmp .= $slast;
            if (!empty($sfirst)) {
                if (!empty($stmp)) $stmp .= ", ";
                $stmp .= $sfirst;
            }
            if (!empty($smid)) {
                if (!empty($stmp)) $stmp .= " ";
                $stmp .= $smid;
            }

            $doc_list_option.='<option '.$selected.' value="'.$row_doc_list['nr'].'">'.ucwords(strtolower($stmp)).'</option>';
        }

    }
    $doc_list_col = '<select id="doc_list" name="doc_list" class="segInput" style="width: 100%;">'.$doc_list_option.'</select>';
    ob_start();
?>
    <style type="text/css">
        table {
          margin: 15px 0;
          table-layout: fixed;
          width: 100%; /* must have this set */
        }

        .fixtable td:nth-child(1) {
          width: 18%;
        }
        .fixtable td:nth-child(2) {
          width: 82%;
        }
    </style>

    <table class="fixtable">
        <tr>
            <td><strong>Case Date</strong></td>
            <td>
                <input disable type="text" class="segInput" name="type" id="casedate" value="<? echo $encdate2;?>" readonly size=40 onBlur="trimString(this);" style="width: 100%;">
            </td>
        </tr>
        <tr>
            <td><strong>Case No.</strong></td>
            <td>
                <input disable type="text" class="segInput" name="type" id="caseno" value="<? echo $encounter_nr2;?>" readonly size=40 onBlur="trimString(this);" style="width: 100%;">
            </td>
        </tr>
        <tr>
            <td><strong>Type</strong></td>
            <td>
                <input disable type="text" class="segInput" name="type" id="type" value="<? echo $type;?>" readonly size=40 onBlur="trimString(this);" style="width: 100%;">
            </td>
        </tr>
        <tr>
            <td><strong>Physician</strong></td>
            <td>
                <?php echo $doc_list_col ?>
            </td>
        </tr>
        <tr>
            <td><strong>Date</strong></td>
            <td>
                <input type="text" class="segInput" id="datepickers" name="datepickers" value="<? echo $datepickers;?>" style="width: 100%;">
            </td>
        </tr>
        <tr>
            <td><strong>Amount</strong></td>
            <td>
                <input type="text" class="segInput" name="amount" id="amount"  value="<? echo $pf_amount;?>" size=40 onBlur="trimString(this);" style="width: 100%;">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:right">
                <button aria-disabled="false" id="btnsave" role="button"
                    class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" type="button">
                    <span class="ui-button-text">
                        Submit
                    </span>
                </button>
            </td>
        </tr>
    </table>

    <form action="<?php echo $breakfile?>" method="post">
        <input type="hidden" name="sid" value="<?php echo $sid ?>">
        <input type="hidden" name="lang" value="<?php echo $lang ?>">
        <input type="hidden" name="userck" value="<?php echo $userck ?>">
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

