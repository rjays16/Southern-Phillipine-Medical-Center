//created by art 09/10/2014
    function preset(){
        var enc_nr = $j('#enc_nr').val();
        var curr_nr = $j('#curr_nr').val();
        xajax_getUnpaidPrebill_ajx(enc_nr,curr_nr);
        xajax_getPrebillPayments_ajx(curr_nr);

    }

    function selectBillnr(){
            var bill_nr = $j('#bill_nr option:selected').val();
            xajax_getBillNrDetails_ajx(bill_nr);
            xajax_getPrebillPayments_ajx(bill_nr);
    }

    function cancel(){
        var bill_nr = $j('#bill_nr option:selected').val();
        xajax_getPrebillPayments_ajx(bill_nr);
    }

    function applyPay_js(bill_nr){
        xajax_applyPay_ajx(bill_nr);
    }

    function refreshPage(){
       $j('#curr_nr').val('');
       window.parent.refreshHistory();
    }

    function appendRow(){
        var rowCount = $j('#paytbody tr').length;
        xajax_appendTbl_ajx(rowCount);
        $j('.error').hide();
    }

    function appendtbl(str){
        $j('#paytbody').append(str);
    }

    function removeRow(ref_no){
        var rowCount = $j('#paytbody tr').length;
        var tbl = '<tr class="error">'+
                        '<td></td>'+
                        '<td><i class="icon-warning-sign" ></i>   No payments yet</td>'+
                        '<td></td><td></td><td></td>'+
                        '</tr>';
        $j('#delete'+ref_no).val(1);
        $j('.'+ref_no).hide();
        if (rowCount == 1) {
            $j('#paytbody').append(tbl);
        }
    }

