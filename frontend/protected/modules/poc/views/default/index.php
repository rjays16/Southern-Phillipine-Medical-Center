<?php
$css = <<<CSS
.modal-header {
    padding:9px 15px;
    border-bottom:1px solid #eee;
    background-color: #0480be;
    -webkit-border-top-left-radius: 5px;
    -webkit-border-top-right-radius: 5px;
    -moz-border-radius-topleft: 5px;
    -moz-border-radius-topright: 5px;
     border-top-left-radius: 5px;
     border-top-right-radius: 5px;
 }
    
.modal  {
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px; 
}    
    
.active a {
   color: white !important;
   background-color: blue !important;
}

.withBreaks { 
    word-wrap:break-word; 
}
    
p.small {
    line-height: 0.2;
}       
CSS;

Yii::app()->clientScript->registerCss('css',$css);
Yii::app()->clientScript->registerScript('showModal', "
    $(window).on('load',function(){
        $('#view').modal('show');
    });
");
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

$this->beginWidget('bootstrap.widgets.TbModal', array('id' => 'view',
    'htmlOptions' => array('style' => 'width: 800px; margin-left:-450px;'))
);
?>

<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h4 class="modal-title">POINT OF CARE</h4>
</div>

<div class="modal-body">
    <table>
        <tr>
            <td style="color:#404040; font:bold 14px Arial;">PID: <input id="_pid" name="_pid" class="clear" type="text" value="<?php echo $encounter->person->pid; ?>" readonly="readonly" style="color:#006600; font:13px Arial;"></td>
            <td style="color:#404040; font:bold 14px Arial;">Name: <input id="_name" name="_name" class="clear" type="text" size="100" value="<?php echo $encounter->person->getFullName(); ?>" readonly="" style="color:#404040; font:13px Arial;"></td>
            <td style="color:#404040; font:bold 14px Arial;">Patient Type: <input id="_encounter_type" name="_encounter_type" class="clear" type="text" value="<?php echo $encounter->type->name; ?>" readonly="readonly" style="color:#404040; font:13px Arial;"></td>
        </tr>     
    </table>
    <table>
        <tbody>            
            <tr>
                <td width="30%">
                    <p class="withBreaks" style="width:300px; color:#86592d; font:bold 17px Arial;"><?php echo $poc_service->name; ?></p>
                </td>
                <td valign="middle" align="left">
                    <div>
                    <?php
                        $this->widget('bootstrap.widgets.TbTabs', array(
                            'type'=>'tabs', 
                            'placement'=>'above', // 'above', 'right', 'below' or 'left'
                            'tabs'=>array(
                                array('id'=>'cashTab', 'label'=>'Cash', 'active'=>true, 'content'=>$this->renderPartial('/default/_poc_cash', array('uprice' => $poc_service->price_cash, 'discount' => $discount, 'is_percent' => $is_percent), true, true)),
                                array('id'=>'chargeTab', 'label'=>'Charge', 'content'=>$this->renderPartial('/default/_poc_charge', array(), true, true)),
                            ),
                        ));                     
                    ?>
                    </div>
                </td>             
            </tr>         
        </tbody>
    </table>           
</div>

<div class="modal-footer">
    <?php
        $this->widget('bootstrap.widgets.TbButton', array(
            'id' => 'modal-open-transmittal',
            'label' => 'Save',
            'type' => 'primary',
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => 'Close',
            'type' => '',
            'htmlOptions' => array('data-dismiss' => 'modal'),
        ));
    ?>
</div>

<?php 
$this->endWidget();
