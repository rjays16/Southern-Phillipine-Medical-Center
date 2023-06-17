<?php
Yii::app()->clientScript->registerScript("tabClick", "$('#pocTab li').click(function() 
    {   
        if ($('#current_order').val() != 'START') {
            return;
        }
        else if ($('#is_opd').val() == 1) {
            $('#is_charge').val(0);
            if ( !document.getElementById('cashTab').classList.contains('active-tab') ) {
                document.getElementById('chargeTab').classList.remove('active-tab');
                document.getElementById('cashTab').classList.add('active-tab');            
            }
            return;
        }
        if ($('#pocTab li').index(this) === 1) {
            $('#is_charge').val(1);
            document.getElementById('cashTab').classList.remove('active-tab');
            document.getElementById('chargeTab').classList.add('active-tab');    

            var bchargetypes_loaded = $('#bfilled').val();
            if ( !Number(bchargetypes_loaded) ) {
                $.ajax({
                    type: 'GET',
                    url: '../../index.php?r=poc/order/getChargeTypes',
                    success: function(data) {
                                $('#pr_indicator').hide();
                                $('#charge_options').html(data);
                                $('#bfilled').val(1);
                                
                                $('#chargeTypes').on('change', function(){
                                    if ($('#chargeTypes  option:selected').val() == 'phic') {                                    
                                        let enc_nr = $('#encounter_nr').val();
                                        $.ajax({
                                            type: 'POST',
                                            url: '../../index.php?r=poc/charge/getPhicCoverage',
                                            data: { encNo: JSON.stringify(enc_nr) },
                                            success: function(data) { 
                                                        $('#co_indicator').hide();
                                                        if (data != 'NULL') {                                                            
                                                        $('#coverage').val(data);

                                                        $('#cov_type').show();
                                                        $('#cov_amount').show();
                                                        
                                                        $('#cov_type').text('PHIC Coverage:');
                                                        
                                                        let namount = parseFloat(data);
                                                        $('#cov_amount').text(currencyFormat(namount));
                                                        }
                                                        else {
                                                            alert(\"No PHIC Coverage\");                                                            
                                                            $('#chargeTypes').val('empty');
                                                        }
                                                    },
                                            error: function(jqXHR, exception) {
                                                        $('#co_indicator').hide();
                                                        $('#err_msg').text(jqXHR.responseText);
                                                        $('#error_msg').fadeIn(800);
                                                    },
                                            dataType: 'json',    
                                            beforeSend: function(){
                                                $('#co_indicator').show();
                                            }                    
                                        });
                                    }
                                    else {
                                        $('#coverage').val(-1);
                                        $('#cov_type').text('');
                                        $('#cov_amount').text('');
                                        
                                        $('#cov_type').hide();
                                        $('#cov_amount').hide();
                                    }
                                });    
                            },
                    error: function(data) {
                                $('#pr_indicator').hide();
                                alert(\"Error occured.please try again\");
                            },
                    dataType:'json',
                    beforeSend: function(){
                        $('#pr_indicator').show();
                    }                    
                });
            }
        }
        else {
            $('#is_charge').val(0);
            if ( !document.getElementById('cashTab').classList.contains('active-tab') ) {
                document.getElementById('chargeTab').classList.remove('active-tab');
                document.getElementById('cashTab').classList.add('active-tab');            
            }
        }
    })");

Yii::app()->clientScript->registerScript("loadJs", <<<JS
    function currencyFormat(num) {
        let cnum = new Intl.NumberFormat('en', {
                        style: 'currency',
                        currency: 'USD',
                        signDisplay: 'exceptZero',
                        currencySign: 'accounting',
                      }).format(num);
        return cnum.replace('$', '');
    }    
        
    function handleQty(uprice, discount, inpercent) {    
        let qty = $('#_qty').val();  
        let ntotal = parseFloat(qty) * parseFloat(uprice);
        let ndiscount = parseFloat(discount)
        $('#_total').val(ntotal.toFixed(2));            
        if (inpercent) {
            ndiscount = ndiscount * ntotal;
        }
        $('#_discount').val(ndiscount.toFixed(2));
        let nnet = ntotal - ndiscount;
        $('#_net').val(nnet.toFixed(2));        
    }
        
    function savePocOrder(encounter_nr, pid) { 
        var order = {};    
            
        order.encounter_nr = encounter_nr;
        order.pid          = pid;            
    
        order.is_cash      = $('#is_charge').val() == '0';
    
        order.settlement_type = (!order.is_cash) ? $('#chargeTypes  option:selected').val() : '';       
        order.order_type   = $('#current_order').val();
        order.ward_id      = $('#ward_id').val();
        order.source_req   = $('#request_source').val();
        order.discountid   = $('#discountid').val();
        order.discount     = $('#_discount').val();
    
        var orderdetail = {};
    
        orderdetail.service_code = $('#poc_code').val();
        orderdetail.unit_price = $('#uprice').val();
        orderdetail.quantity = order.is_cash ? $('#_qty').val() : '1';    
        
        if(order.is_cash == 1 && ($('#_qty').val() == '' || $('#_qty').val() == 0)){
            alert('Quantity cannot be empty or 0');
        }else{
            $.ajax({
                type: 'POST',
                url: '../../index.php?r=poc/order/savePocOrder',
                data: { pocH: JSON.stringify(order), pocD: JSON.stringify(orderdetail) },  
                success: function(data) {
                            $('#saving_indicator').hide();
                            closePocModal();
                        },
                error: function(jqXHR, exception) {
                            $('#saving_indicator').hide();
                            $('#err_msg').text(jqXHR.responseText);
                            $('#error_msg').fadeIn(800);
                        },
                dataType: 'json',    
                beforeSend: function(){
                    $('#saving_indicator').show();
                }                    
            });
        }
    }       
JS
    , CClientScript::POS_BEGIN);  
?>

<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="far fa-times-circle"></i></a>
    <h4 class="modal-title">POINT OF CARE</h4>    
</div>

<div class="modal-body">
    <table>
        <tr>
            <td style="color:#404040; font:bold 14px Arial;">HRN: <input id="_pid" name="_pid" class="clear" type="text" value="<?php echo $encounter->person->pid; ?>" readonly="readonly" style="color:#006600; font:13px Arial;"></td>
            <td style="color:#404040; font:bold 14px Arial;">Name: <input id="_name" name="_name" class="clear" type="text" size="60" value="<?php echo $encounter->person->getFullName(); ?>" readonly="" style="color:#404040; font:13px Arial;"></td>
            <td style="color:#404040; font:bold 14px Arial;">Patient Type: <input id="_encounter_type" name="_encounter_type" class="clear" type="text" value="<?php echo $encounter->type->name; ?>" readonly="readonly" style="color:#404040; font:13px Arial;"></td>
        </tr>     
    </table>
    <table>
        <tbody>            
            <tr>
                <td width="30%">
                    <div style="height: 152px; padding-top: 60px; overflow:hidden;">
                        <p class="withBreaks" style="width:300px; color:#86592d; font:bold 17px Arial;"><?php echo $poc_service->name; ?></p>
                    </div>
                </td>
                <td valign="middle" align="left">
                    <div>                                               
                        <ul class="nav nav-tabs" id="pocTab" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active-tab" id="cashTab" data-toggle="tab" href="#cash-tab" role="tab" aria-controls="cash-tab" aria-selected="<?php echo ($pocOrder == null) ? 'true' : ($pocOrder->is_cash) ? 'true' : 'false'; ?>">Cash</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="chargeTab" data-toggle="tab" href="#charge-tab" role="tab" aria-controls="charge-tab" aria-selected="<?php echo ($pocOrder == null) ? 'false' : ($pocOrder->is_cash) ? 'false' : 'true'; ?>">Charge</a>
                          </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="cash-tab" role="tabpanel" aria-labelledby="cashTab">
                                <table>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td valign="center" width="25%">
                                            <strong style="width:15px;color:#86592d; font:italic bold 15px Arial;">Qty:&nbsp;&nbsp;</strong>
                                            <input id="_qty" name="_qty" type="text" style="width:30px;color:#000000; font:bold 16px Arial;" value="<?php echo $quantity; ?>" onchange="handleQty(<?php echo ($pocOrder == null) ? $poc_service->price_cash : $pocOrder->pocOrderDetails[0]->unit_price; ?>, <?php echo $discount; ?>, <?php echo $is_percent; ?>)" <?php echo ($current_order === 'START') ? '' : "readonly"; ?>>
                                        </td>
                                        <td>&nbsp;</td>
                                        <td valign="center" width="30%" style="text-align: left">
                                            <p class="small" style="margin-top:20px">
                                                <label for="_total" style="width:15px;color:#86592d; font:italic bold 15px Arial;">Total</label><br>
                                                <label for="_discount" style="width:15px;color:#86592d; font:italic bold 15px Arial;">Discount</label><br>
                                                <label for="_net" style="width:15px;color:#86592d; font:italic bold 15px Arial;">Net</label>              
                                            </p>
                                        </td>            
                                        <td valign="center" style="width:100px; text-align: right">
                                            <p class="small" style="margin-top:10px">
                                                <input id="_total" name="_total" type="text" style="width:100px; text-align:right; color:#000000; font:bold 16px Arial;" value="" readonly><br>                
                                                <input id="_discount" name="_discount" type="text" style="width:100px; text-align:right; color:#000000; font:bold 16px Arial;" value="" readonly><br>                
                                                <input id="_net" name="_net" type="text" style="width:100px; text-align:right; color:#000000; font:bold 16px Arial;" value="" readonly>
                                            </p>                                            
                                        </td>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>                                
                            </div>
                            <div class="tab-pane" id="charge-tab" role="tabpanel" aria-labelledby="chargeTab">                                
                                <table>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr>
                                        <td colspan="2">
                                            <div id="pr_indicator" style="display: none;" class="alert alert-info"​><i class="fa fa-spin fa-spinner"></i> Please wait...</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%" style="text-align:right">&nbsp;Charge To:&nbsp;&nbsp;</td>
                                        <td id="charge_options" valign="center" align="right">
                                            <select id="chargeTypes" width="50%" <?php echo ($current_order === 'START') ? '' : "disabled=\"\""; ?>>
                                                <option value="">&nbsp;</option>
                                           </select>                  
                                        </td> 
                                    </tr>
                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
                                    <tr>
                                        <td colspan="2">
                                            <div id="co_indicator" style="display: none;" class="alert alert-info"​><i class="fa fa-spin fa-spinner"></i> Retrieving coverage. Please wait ...</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input id="coverage" type="hidden" value="-1" />
                                            <span id="cov_type" style="font:bold 12px Tahoma"></span>
                                            <span id="cov_amount" style="font:bold 12px Tahoma;color:#000044"></span>                                        
                                        </td>
                                    </tr>
                                </table>
                                <input type="hidden" id="bfilled" name="bfilled" value="0">                                                                
                            </div>
                        </div>
                        <input type="hidden" id="is_charge" name="is_charge" value="0">
                    </div>
                </td>             
            </tr>         
        </tbody>
    </table>
    <input type="hidden" id="encounter_nr" name="encounter_nr" value="<?php echo $encounter->encounter_nr; ?>">
    <input type="hidden" id="ward_id" name="ward_id" value="<?php echo $ward_id; ?>">
    <input type="hidden" id="discountid" name="discountid" value="<?php echo $discountid; ?>">
    <input type="hidden" id="poc_code" name="poc_code" value="<?php echo $poc_service->service_code; ?>">
    <input type="hidden" id="uprice" name="uprice" value="<?php echo $poc_service->price_cash; ?>">
    <input type="hidden" id="current_order" name="current_order" value="<?php echo $current_order; ?>">
    <input type="hidden" id="is_opd" name="is_opd" value="<?php echo ($encounter->type->type_nr == EncounterType::TYPE_OUTPATIENT) ? 1 : 0; ?>">    
    <div id="saving_indicator" style="display: none;" class="alert alert-info"​><i class="fa fa-spin fa-spinner"></i><strong>Saving ... Please wait ...</strong></div>
    <div id="error_msg" class="alert alert-warning" style="display:none">  
        <p><strong>Warning!</strong> An error has occurred.</p>
        <span id="err_msg"></span>
    </div>
</div>
<div class="modal-footer">
    <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'id' => 'modal-open-poc',
            'label' => ($current_order === 'START') ? 'Start' : 'Stop',
            'type' => ($current_order === 'START') ? 'success' : 'danger',
            'htmlOptions'=> array('onclick' => 'savePocOrder(\''.$encounter->encounter_nr.'\', \''.$encounter->pid.'\')'),
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'id' => 'modal-close-poc',
            'label' => 'Close',
            'type' => 'warning',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        ));
    ?>
</div>
<div>   
    <?php if ($current_order != 'START')  { 
            if ($pocOrder->is_cash) {
    ?>
            <script>
                $('#charge-tab').removeClass("active show");
                $('#cash-tab').addClass("active show");
                
                $('#cashTab').removeAttr("data-toggle");
                $('#chargeTab').removeAttr("data-toggle");
                
                $('#_qty').change();                
            </script>
    <?php 
            }
            else {
    ?>
            <script>
                $('#cash-tab').removeClass("active show");
                $('#charge-tab').addClass("active show");
                
                $('#cashTab').removeAttr("data-toggle");
                $('#chargeTab').removeAttr("data-toggle");                
                
                $('#is_charge').val(1);
                
                document.getElementById('cashTab').classList.remove('active-tab');
                document.getElementById('chargeTab').classList.add('active-tab');                    
                
                $.ajax({
                    type: 'GET',
                    url: '../../index.php?r=poc/order/getChargeTypes',
                    success: function(data) {
                                $('#pr_indicator').hide();
                                $('#charge_options').html(data);
                                $('#bfilled').val(1);
                                
                                $('select#chargeTypes').val('<?php echo $pocOrder->settlement_type ?>');
                                $('select#chargeTypes').prop('disabled', 'disabled');
                            },
                    error: function(data) {
                                $('#pr_indicator').hide();
                                alert("Error occured.please try again");
                            },
                    dataType:'json',
                    beforeSend: function(){
                        $('#pr_indicator').show();
                    }                    
                });                             
            </script>                        
    <?php
            }            
        }
        else {
            if ($encounter->type->type_nr == EncounterType::TYPE_OUTPATIENT) {
    ?>            
                <script>
                    $('#charge-tab').removeClass("active show");
                    $('#cash-tab').addClass("active show");

                    $('#cashTab').removeAttr("data-toggle");
                    $('#chargeTab').removeAttr("data-toggle");                
                </script>                        
    <?php        
            }
        }
    ?>    
</div>