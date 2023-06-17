<?php 
Yii::app()->clientScript->registerScript("tabClick", "$('li').click(function() 
    {           
        if ($('li').index(this) === 1) {
            var bchargetypes_loaded = $('#bfilled').val();                
            if ( !Number(bchargetypes_loaded) ) {
                $.ajax({
                    type: 'POST',
                    url: 'index.php?r=poc/order/getChargeTypes',
                    success: function(data) {
                                $('#pr_indicator').hide();
                                $('#charge_options').html(data);
                                $('#bfilled').val(1);
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
    })");
?>
<div class="row">
    <?php
    $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id' => 'charge-tab',
        'type' => 'horizontal'
    )); ?>
    <table>
        <tr>
            <td colspan="2">
                <div id="pr_indicator" style="display: none;" class="text-center"><i class="fa fa-spin fa-spinner"></i> Please wait...</div>
            </td>
        </tr>
        <tr>
            <td width="40%" style="text-align:right">&nbsp;Charge To:</td>
            <td id="charge_options" valign="center" align="right">
                <select width="50%">
                    <option value="none">&nbsp;</option>
               </select>                  
            </td> 
        </tr>        
    </table>
    <input type="hidden" id="bfilled" name="bfilled" value="0">
    <?php
    $this->endWidget();    
    ?>    
</div>