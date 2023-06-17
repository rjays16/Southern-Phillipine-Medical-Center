<?php
Yii::app()->clientScript->registerScript("loadJs", <<<JS
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
JS
    , CClientScript::POS_BEGIN);    
?>    
<div class="row">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'cash-tab',
        'type' => 'horizontal'
    )); ?>
    <table>
        <tr>
            <td>&nbsp;</td>
            <td valign="center" width="25%">
                <strong style="width:15px;color:#86592d; font:italic bold 15px Arial;">Qty:&nbsp;</strong>
                <input id="_qty" name="_qty" class="clear" type="text" style="width:30px;color:#000000; font:bold 16px Arial;" onchange="handleQty(<?php echo $uprice; ?>, <?php echo $discount; ?>, <?php echo $is_percent; ?>)">
            </td>
            <td valign="center" width="20%" style="text-align: left">
                <p class="small" style="margin-top:10px">
                    <label for="_total" style="width:15px;color:#86592d; font:italic bold 15px Arial;">Total</label><br>
                    <label for="_discount" style="width:15px;color:#86592d; font:italic bold 15px Arial; line-height: 1.6">Discount</label><br>
                    <label for="_net" style="width:15px;color:#86592d; font:italic bold 15px Arial;">Net</label>              
                </p>
            </td>            
            <td valign="center" style="width:100px; background-color: #e0e0e0; text-align: right">
                <input id="_total" name="_total" type="text" style="width:100px; text-align:right; color:#000000; font:bold 16px Arial;" value="" readonly><br>                
                <input id="_discount" name="_discount" type="text" style="width:100px; text-align:right; color:#000000; font:bold 16px Arial;" value="" readonly><br>                
                <input id="_net" name="_net" type="text" style="width:100px; text-align:right; color:#000000; font:bold 16px Arial;" value="" readonly>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?php
    $this->endWidget();    
    ?>    
</div>