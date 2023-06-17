<?php
$this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'Package Details',
    'headerIcon' => 'fa fa-list',
    'htmlOptions' => array(
        'style' => 'margin-left: 15px; margin-right: 15px;'
    ),
));

$this->widget('bootstrap.widgets.TbGridView', array(
    'dataProvider' => $packageDetails,
    'type' => 'bordered condensed hover',
    'columns' => array(
        array(
            'name' => 'item_code',
            'header' => 'Item Code',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center;',
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
        ),
        array(
            'name' => 'item_name',
            'header' => 'Item Name',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center;',
            ),
        ),
        array(
            'name' => 'item_purpose',
            'header' => 'Location',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center;',
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
            'value' => function($data){
                switch($data->item_purpose){
                    case 'LB': $loc = 'Laboratory'; break;
                    case 'RD': $loc = 'Radiology'; break;
                    case 'PH': $loc = 'Pharmacy'; break;
                    case 'MISC': $loc = 'Miscellaneous'; break;
                }
                return $loc;
            }
        ),
        array(
            'name' => 'quantity',
            'header' => 'Qty',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center;',
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center; width: 50px;'
            ),
        ),
        array(
            'name' => 'price_cash',
            'header' => 'Cash Price',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center;',
            ),
            'htmlOptions' => array(
                'style' => 'text-align: right; width: 100px;'
            ),
            'value' => function($data){
                return number_format($data->price_cash, 2);
            }
        ),
        array(
            'name' => 'price_charge',
            'header' => 'Charge Price',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center;',
            ),
            'htmlOptions' => array(
                'style' => 'text-align: right; width: 100px;'
            ),
            'value' => function($data){
                return number_format($data->price_charge, 2);
            }
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'template' => '{delete}',
        ),
    ),
));
?>
    <div class="pull-right">
        <table class="table-condensed">
            <tr>
                <td style="text-align: right">Total Package Cash Price:</td>
                <td style="text-align: right; font-weight: bold;"><?php echo number_format($totalCash, 2) ?></td>
            </tr>
            <tr>
                <td style="text-align: right">Total Package Charge Price:</td>
                <td style="text-align: right; font-weight: bold;"><?php echo number_format($totalCharge, 2) ?></td>
            </tr>
        </table>
    </div>

<?php
$this->endWidget();
?>