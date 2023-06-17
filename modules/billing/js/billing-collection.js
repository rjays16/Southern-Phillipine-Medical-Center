var dialogSelEncCollection, counter = 0;
$j(function () {

    $j("#addB").button({text: true, icons: {primary: "ui-icon-plusthick"}});
    $j("#saveB").button({text: true, icons: {primary: "ui-icon-disk"}});
    $j("#resetB").button({text: true, icons: {primary: "ui-icon-refresh"}});
    $j(".del").button({text: true, icons: {primary: "ui-icon-close"}});

    // Insert new item (no saving yet)
    $j("#addB").on('click', function () {
        addEmptyElements();
        return false;
    });

    // Saving
    $j("#saveB").click(function () {

        var encounter = $j('#pEncrNoInput').val();
        var billnr = $j('#pBillNrInput').val();
        var res = '';

        var balance = $j('#balance').val();

        var form = $j('#collectionForm');
        form.validate();
        if (form.valid()) {

            if (confirm("Are you sure to process this entries?") == true) {

                 if (balance != 0) {
                    $j.ajax({
                        url: "../../index.php?r=collections/index/create&" + 'encounterNr=' + encounter + '&billNr=' + billnr,
                        type: 'GET',
                        async: true,
                        data: form.serializeArray(),
                        dataType: 'json',
                        success: function (data) {
                            res = data;
                        },
                        complete: function (i) {
                            var deleteIcon = '';
                            if (res) {

                                $j('#collectionGrid tbody').empty();
                                loadData(0);
                                populateBill();
                                alert('Successfully added enties!');

                            }
                            /*else {
                                alert('This entry has been already paid. Please coordinate to billing. Thank you.');
                            }*/
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert(jqXHR.responseText);
                        }
                    });
                 } else {
                    alert('This entry is already paid. Thank you.');
                 }

            }

        } else {
            alert('Please provide correct inputs. Thank you.');
        }

        return false;
    });


    // Handles reset operation
    $j("#resetB").click(function() {
        $j('#collectionGrid tbody').empty();
        loadData(0);
        populateBill();

        return false;
    });

    preSet();

});

function clearItems() {
  window.location.href=window.location.href;
}

/**
 * Load modal no operation yet
 * @returns {boolean}
 */
function preSet() {
    var pageSelEnc = "../../modules/billing/billing-select-isFinal.php";
    dialogSelEncCollection = $j('<div id="collectionDialog"></div>')
        .html('<iframe style="border: 0px; " src="' + pageSelEnc + '" width="100%" height=400px></iframe>')
        .dialog({
            autoOpen: false,
            modal: true,
            show: 'fade',
            hide: 'fade',
            height: 'auto',
            width: '830',
            title: 'Select Registered Person',
            position: 'top'
        });

    dialogSelEncCollection.dialog('open');

    return false;
}

/**
 * Close Select patient modal
 */
function closeSelEncCollectionDialog() {
    dialogSelEncCollection.dialog('close');
    populateBill();
    loadData(0);
}

function onChangeCalculate(item) {
    var collections = 0;
    var balance = 0;
    balance = new Number($j('#balance').val());
    $j('#collectionGrid [data-amount]').each(function(i, v) {
       var self = $j(this);
       var amount = self.data('amount');

       if (!isNaN(parseFloat(amount))) {
         collections += parseFloat(amount); // already added collections
       }
    });

    var tot = 0;
    $j('.emptynewelement').each(function() {
        var amountForEmptyEl = this.value;
        if (!isNaN(parseFloat(amountForEmptyEl))) {
           tot += parseFloat(amountForEmptyEl);  // not yet saved items
        }
    });
    var total = (tot + collections);
}

/**
 * Display billing information
 */
function populateBill() {
    //showLoading();
    var encounter = $j('#pEncrNoInput').val();
    var billnr = $j('#pBillNrInput').val();
    var bill_dte = $j('#pBillDateInput').val();
    var frm_dte = $j('#pBillFrmDateInput').val();
    var result;

    $j.ajax({
        url: "../../index.php?r=collections/index/calculateBill",
        async: true,
        type: 'get',
        dataType: 'json',
        data: { 'encounter': encounter, 'billnr': billnr },
        success: function (data) {
            result = data;
        },
        complete: function (data) {
            if (result) {
                $j('#pGrossAmount').val(result.gross);
                $j('#pCoverage').val(result.coverage);
                $j('#pDiscount').val(result.discounts);
                $j('#pNetTotal').val(result.net);
                $j('#pDeposit').val(result.deposit);
                $j('#pTotalGrants').val(result.less);
                $j('#balance').val(result.balance);
            }
        }
    });
}

/**
 * Display ledger info
 * @param action
 */
function loadData(action) {
    var res = '';
    var encounter = $j("#pEncrNoInput").val();
    var billnr = $j("#pBillNrInput").val();

    $j.ajax({
        url: "../../index.php?r=collections/default",
        async: true,
        type: 'GET',
        data: {encounter: encounter, billNr: billnr},
        dataType: 'json',
        success: function (data) {
            res = data;
            console.log(res);
        },
        complete: function (item) {
            //alert(res);
            var index = 0;
            if (res) {
                var data = res;

                var preRow = '<tr>' +
                    '<td></td>' +
                    '<td>Category</td>' +
                    '<td>Amount</td>' +
                    '<td>Control #</td>' +
                    '<td>Date</td>' +
                    '<td>Remarks</td>'
                    '</tr>';

                $j('#collectionGrid tbody').append(preRow);

                var cmapAccounts = [];

                $j.each(data, function (k, v) {

                    var classIdentifer = 'row_' + v.id;
                    var l = window.location;
                    var deleteIconUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1] + '/images/cashier_delete_small.gif';
                    var deleteIcon = '<img src=' + deleteIconUrl + ' />';

                    // added by: syboy 10/15/2015 : meow 
                    var access = v.alias.split('|');
                    var access2 = v.alias2.split('|');
                    var accessper = access.concat(access2);
                    for (var i in accessper) {
                        if ((accessper[i] == v.category) == true) {
                             deleteIcon = '<img src=' + deleteIconUrl + ' />';
                             break;
                        }else{
                            deleteIcon = '';
                        }  
                    }
                    // ended

                    if (v.category == 'PAID' || v.category == 'SS' || v.category == 'COH' || v.category == 'PARTIAL')
                        deleteIcon = '';

                    if (v.amount == 0.00) {
                        deleteIcon = '';
                    }

                //added by gelie 10-10-2015
                    var rowDate = v.approved_date;
                    if(v.category == 'PAID' || v.category == 'PARTIAL')
                        rowDate = v.create_time;
                //end gelie

                    var row = '<tr class=' + classIdentifer + '>' +
                        '<td align="center"><a href=# data-category="' + v.category +
                        '" data-approved_date="'+v.approved_date_raw+
                        '" data-control_nr="'+v.control_nr+
                        '" onclick="javascript:onDelete(this);">' + deleteIcon + '</a></td>' +

                        '<td align="center" display="text-align: center">' + v.category + '</td>' +
                        '<td align="center">' + v.amount + '</td>' +
                        '<td align="center">'+v.control_nr+'</td>' +
                        '<td align="center">'+rowDate+'</td>' +
                        '<td align="center" class="remarks">' + v.remarks + '</td>' +
                        '</tr>';

                    if (action) {
                        $j('#collectionGrid tbody').empty();
                        $j('#collectionGrid tbody').append(row);
                    } else {
                        $j('#collectionGrid tbody').append(row);
                    }

                });
            }

            addEmptyElements(counter);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText);
        }
    });

}

/**
 * Handles delete feature
 * @param obj
 * @returns {boolean}
 */
function onDelete(obj) {
    var category = $j(obj).data('category');
    var approvedDate = $j(obj).data('approved_date');
    var controlNr = $j(obj).data('control_nr');
    var encounter = $j('#pEncrNoInput').val();

    var balance = $j('#balance').val();

    var res;
    if (confirm("Are you sure to process this entries?") == true) {
        var remarks = prompt('Please provide reason for deletion.');
        if (remarks) {
            $j.ajax({
                type: 'GET',
                url: '../../index.php?r=collections/index/delete',
                data: {
                    category: category, 
                    encounter: encounter, 
                    approvedDate : approvedDate,
                    controlNr : controlNr,
                    remarks: remarks
                },
                async: true,
                dataType: 'json',
                success: function (data) {
                    res = data;
                },
                complete: function (i) {
                    if (res) {
                        $j('#collectionGrid tbody').empty();
                        loadData(0);
                        populateBill();
                        alert('Successfully Deleted.');
                    } else {
                        alert('This entry is already paid. Thank you.');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                  alert(jqXHR.responseText);
                }
            });
        } else {
            alert('Failed to continue this transaction. Unable to provide reason for deletion');
        }
    } else {
        return false;
    }

}

/**
 * Load CMAP Guarantors
 * @param element
 */
function displayGuarantor(element) {

    var guarantor_id = '#guarantor_' + element;
    var hidFlag = '#hid_guarantor_' + element; //flag for cmap
    var status = document.getElementsByName('payType[]');

    for(i=0;i<status.length;i++) {
      var type = status[i].value;

      $j.get( "../../index.php?r=collections/default/guarantors", {type: type}, function( data ) {
        if (data) {
          $j(guarantor_id).prop('disabled', false);
             $j.each(data, function (k, v) {
                 $j(guarantor_id)
                   .find('option')
                   .remove();
             });

             $j(guarantor_id).append($j("<option value=''>- Select Guarantor -</option>"));
                   
             $j.each(data, function (k, v) {
               $j(guarantor_id)
               .append($j("<option></option>")
               .attr("value", v.nr)
               .text(v.name));
             });

       } else {
          $j(guarantor_id).prop('disabled', true);
          $j(guarantor_id).empty().append('<option>- Select Guarantor -</option>');
          var controlNo = '#control_no_' + element;
          var hidGFlag = '#hid_guarantor_' + element;
          $j(controlNo).val("");
          $j(hidGFlag).val("");
       }

       }, 'json');
    }
}

/**
 * Set selected guarantor
 * @param element
 */
function getGuarantor(element) {
    var hidFlag = '#hid_guarantor_' + element; //flag for cmap
    $j(hidFlag).val($j('#guarantor_' + element).val()); //set value for hidden guarantor
    var guarantor = '#guarantor_' + element;

    var text = $j(guarantor +' option:selected').text();
    if ($j('#guarantor_' + element).val()) {
        $j('#control_no_' + element).val(text);
    }

}

/**
 * Insert new row
 * @param index
 */
function addEmptyElements(index) {
    counter++;
    var selectId = 'type_' + counter;
    var selectorId = '#type_' + counter;
    var emptySelect = '<select name="payType[]" onchange="displayGuarantor(counter)"  required="true" id="'+selectId+'"></select>';

    $j.get( "../../index.php?r=collections/default/paytypes", function( data ) {
      if (data) {
        $j.each(data, function (k, v) {
            if (v.deleted == 0) {
                $j(selectorId)
                .append($j("<option></option>")
                .attr("value", v.type)
                .text(v.alias));
            }
        });
      }
    }, 'json');

    var guarantor_id = 'guarantor_' + counter;
    var control_id = 'control_no_' + counter;

    var emptyInputAmount = '<input class="emptynewelement" type="text" name="amount[]" onchange="onChangeCalculate(index)" required="true" number="true" />';
    var emptyInputControlNo = '<input type="text" name="control_no[]" id="' + control_id + '"/>';
    var selectG = '<select onchange="getGuarantor(counter)" disabled name="guarantor[]" required="true" id="'+guarantor_id+'">' +
                     '<option value="0">'+ '- Select Guarantor -' + '</option>' +
                  '</select>';
    /*var emptyRemarks = '<input type="text" name="remarks[]" size="100" /></input>';
    var emptyDateEncoded = '<input type="type" name="date_encoded[]" size="12" disabled="disabled" />';
    var emptyEncodedBy = '<input type="input" name="encoded_by[]" size="12" disabled="disabled" />';
    var emptyDateDeleted = '<input type="input" name="date_deleted[]" size="12" disabled="disabled" />';
    var emptyDeletedBy = '<input type="input" name="deleted_by[]" size="12" disabled="disabled" />';*/
    var hiddenGuarantor = '<input type="hidden" name="hidGuarantor[]" id="'+ 'hid_'+guarantor_id+'"/>';

    var classIdentifer = 'row_' + index;
    classIdentifer += ' empty';

    var emptyRow = '<tr class=' + 'empty' + '>' +
        '<td align="center"></td>' +
        '<td>' + emptySelect + selectG + hiddenGuarantor + '</td>' +
        '<td>' + emptyInputAmount + '</td>' +
        '<td>'+emptyInputControlNo+'</td>' +
        '<td><input class="calendar" type="text" name="approved_date[]" value="" readonly></td>' +
        '<td></td>' +
        '</tr>';

    $j('#collectionGrid tbody').append(emptyRow);
    $j('.calendar').datepicker({
        changeMonth:true,
        changeYear:true,
        showAnim:false
    });
}

function showLoading() {
    isComputing = true;
    return overlib('Please Wait ...<br><img src="../../images/ajax_bar.gif">',
        WIDTH, 300, TEXTPADDING, 5, BORDER, 0,
        STICKY, SCROLL, CLOSECLICK, MODAL,
        NOCLOSE, CAPTION, '',
        MIDX, 0, MIDY, 0,
        STATUS, '');
}