<?php
/**
 * Credit and Collection entry point
 * @author michelle 03-02-15
 */
define('LANG_FILE', 'lab.php');
define('NO_2LEVEL_CHK', 1);
require('./roots.php');
require($root_path . 'include/inc_environment_global.php');
require_once($root_path . 'include/inc_front_chain_lang.php');
//require_once($root_path.'include/care_api_classes/class_acl.php');

#$breakfile = $root_path . 'main/startframe.php' . URL_APPEND;
$thisfile = basename(__FILE__);


require_once($root_path . 'gui/smarty_template/smarty_care.class.php');
$smarty = new smarty_care('common');

# Module title in the toolbar
$smarty->assign('bHideTitleBar', true);
$smarty->assign('bHideCopyright', true);
header('Content-type: text/html; charset=utf-8');

?>
    <script type='text/javascript' src="<?= $root_path ?>js/jquery/jquery-1.8.2.js"></script>
    <script type="text/javascript">
        var enc = "<?php echo $_GET['encounter']; ?>";
        var bill_nr = "<?php echo $_GET['billNr']; ?>";
        var data = '';
        $(function () {
            var url =  "../../index.php?r=collections/index/calculateBill";
            $.ajax({
                url: url,
                data: {encounter: enc, bill_nr: bill_nr, view: 1},
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    data = res;
                },
                complete: function(e) {
                    $('#sPid').val(data.person.pid);
                    $('#sPName').val(data.person.fullname);
                    //$('#sAddress').val(data.person.address);
                    $('#sCase').val(data.person.caseNo);
                    $('#sBillNr').val(data.person.bill_nr);
                    $('#sGross').val(data.person.gross);
                    $('#sCoverage').val(data.person.coverage);
                    $('#sDiscount').val(data.person.discounts);
                    $('#sDeposit').val(data.person.deposit);
                    $('#sNet').val(data.person.net);
                    $('#sLess').val(data.person.less);
                    $('#sBalance').val(data.person.balance);
                    var row;
                    var header = '<tr>' +
                        '<td class="segPanel" width="1%" nowrap="nowrap">Financial Assistance from: </td>' +
                        '<td class="segPanel" width="1%" nowrap="nowrap">Amount</td>' +
                    '</tr>';
                    $('#collectionsTable tbody').append(header);
                    $.each(data.collections, function(k,v) {
                        console.log(v.pay_type + ' = ' + v.amount);
                        var row = '<tr>' +
                                '<td>' + v.pay_type+ '</td>' +
                                '<td>' + v.amount+ '</td>' +
                            '</tr>';

                        $('#collectionsTable tbody').append(row);
                    });
                }
            });

        });
    </script>

<?php
$smarty->assign('sPid', '<input id="sPid" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sPName', '<input id="sPName" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
//$smarty->assign('sAddress', '<textare id="sAddress" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right" readonly />');
$smarty->assign('sCase', '<input id="sCase" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sBillNr', '<input id="sBillNr" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sGross', '<input id="sGross" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sCoverage', '<input id="sCoverage" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sDiscount', '<input id="sDiscount" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sDeposit', '<input id="sDeposit" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sNet', '<input id="sNet" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sLess', '<input id="sLess" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');
$smarty->assign('sBalance', '<input id="sBalance" type="text" value="" style="font:bold 12px Arial; float:left; text-align: right;" readonly />');


$smarty->assign('sMainBlockIncludeFile', 'billing/billing-discounts-collections.tpl');
$smarty->display('common/mainframe.tpl');