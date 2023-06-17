<?php

/* @var $insurance EncounterInsurance */
/* @var $pharmacyAreas PharmacyArea */
/* @var $dosageDataList Dosage*/

    Yii::import('bootstrap.components.Bootstrap');
    Yii::import('bootstrap.widgets.TbSelect2');
    Yii::import('bootstrap.widgets.TbButton');
    Yii::import('bootstrap.widgets.TbGridView');
    Yii::import('bootstrap.widgets.TbActiveForm');
    
    Yii::app()->clientScript->registerScript('package-form',<<<JAVASCRIPT
        var val = "";
        var selected = 0;
        $('#packageSelectBtn').on('click', function(e){
            e.preventDefault();

            updateTable();
        });

        $('#packageSelect').on('change', function(){
            updateTable();
        });
        
        $('#btnSubmit').on('click', function(e){
        
            $('input[name^="dosage"]').each(function() {
                if($(this).val().trim()===''){
                    alert("Dosage is Required!");
                    e.preventDefault();
                    return false;
                }
            });
            
            $('input[name^="frequency"]').each(function() {
                if($(this).val().trim()===''){
                    alert("Frequency is Required!");
                    e.preventDefault();
                    return false;
               }
            });
            
            $('input[name^="route"]').each(function() {
                if($(this).val().trim()===''){
                    alert("Route is Required!");
                    e.preventDefault();
                    return false;
               }
            });
        });
        
        
        $('input[name="trans_type"]').on('change', function(){
            selected = $(this).val();

            if(selected == 1){
                $('#change_type').prop("disabled", true);
            }
            else{
                $('#change_type').prop("disabled", false);
            }

            if(val != "")
                updateTable();
        });

        function updateTable(){
            var l = window.location;
            var baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1]+'/index.php?r=or/package';
            val = $('#packageSelect').val();
            var enc = $('#encounter_nr').val();
            $('#hiddenPackageId').val(val);

            $.fn.yiiGridView.update('package_details-grid', {
                type: 'GET',
                data: {'search': val, 'is_cash': selected, 'encounter_nr': enc},
                url: baseUrl,
                complete: function(){
                    $('#packageTotalPrice').html($('#package_details-grid').data('package-price'));
                } 
            })
        }

JAVASCRIPT
, CClientScript::POS_READY);

    $form = $this->beginWidget(
        'bootstrap.widgets.TbActiveForm',
        array(
            'id'=>'package-form',
            'method' => 'post',
        )
    );
    define('MISC', 'MISC');
    define('SUPPLY', 'S');
?>
    <div class="row-fluid">
        <div class="span6" style="margin-bottom:5px">
            <?php
            echo CHtml::tag('label', array('for' => 'trans_type', 'style' => 'display: inline; margin-right: 5px;'), ' Transaction Type: ');
            echo CHtml::tag('input', array('type'=>'radio', 'name' => 'trans_type', 'value' => 1, 'style' => 'margin:0px'), ' cash ');
            echo CHtml::tag('input', array('type'=>'radio', 'name' => 'trans_type', 'value' => 0, 'style' => 'margin:0px', 'checked' => true), ' charge ');
            $charges = array("PERSONAL" => 'TPL', "LINGAP" => 'LINGAP', "CMAP" => 'MAP', "MISSION" => 'MISSION', "PCSO" => 'PCSO');
            if($insurance){
                $charges['PHIC'] = 'PHIC';
            }
            echo CHtml::dropDownList('charge_type', '', $charges, array('class'=>'span3', 'style' => 'margin-left: 5px;'));

            echo CHtml::tag('label', array('for' => 'trans_type', 'style' => 'display: inline; margin-right: 5px; margin-left: 5px'), ' Pharmacy Area: ');
            $htmlOptions =  array('style' => 'margin-left: 5px;',
                                    'options'=> array('A1'=>array("disabled" =>"disabled")));
            //echo CHtml::dropDownList('pharmacy_area', '', CHtml::listData($pharmacyAreas,'area_code','areaCode.area_name'), $htmlOptions);
            ?>
            <select name="pharmacy_area" id="pharmacy_area">
                <?php foreach($pharmacyAreas as $key => $value) { ?>
                <option value="<?php echo $value->area_code; ?>" <?php echo ($value->areaCode->is_deleted == 1) ? 'title="'.$value->areaCode->area_name.' area has deactivated" disabled' : '' ?>><?php echo $value->areaCode->area_name ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span6">
            <?php
                $this->widget(
                    'bootstrap.widgets.TbSelect2',
                    array(
                        'name'      => 'packageSelect',
                        'data'      => $packageList,
                        'options'   => array(
                            'minimumInputLength' => '3',
                            'placeholder' => 'Enter the package name.'
                        )
                    )
                );
                $this->widget(
                    'bootstrap.widgets.TbButton',
                    array(
                        'id'          => 'packageSelectBtn',
                        'label'       => 'Go',
                        'url'         => '#',
                        'size'        => 'small',
                        'htmlOptions' => array(
                            'style' => 'margin-left: 1em'
                        )
                    )
                );
            ?>
        </div>
        <div class="span6">
            <?php
                echo CHtml::tag('h5', 
                    array('class' => 'pull-right'), 
                    'Total Price: <span id="packageTotalPrice">0.00</span>'
                );
            ?>
        </div>
    </div>
    <hr/>
<?php
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'buttonType' => 'submit',
            'type' => 'primary',
            'label' => 'Submit',
            'htmlOptions' => array(
                'class' => 'pull-right',
                'id' => 'btnSubmit'
            )
        )
    );

    $template = "
        <div class='row-fluid'>
            <div class='pull-left'>{summary}</div>
            {items}
            <div class='pull-right'>{pager}</div>
        </div>
    ";

    $this->widget(
        'bootstrap.widgets.TbGridView',
        array(
            'id' => 'package_details-grid',
            'type' => 'striped',
            'dataProvider' => $dataProvider,
            'template' => $template,
            'pagerCssClass' => 'pagination pull-right',
            'columns' => array(
                array(
                    'name' => 'item_code',
                    'header' => 'Item Code',
                    'value'  => function ($data) {
                        echo '<input type="hidden" id="item_code" value="'.$data['item_code'].'"/>';
                        return $data['item_code'];
                    }
                ),
                array(
                    'name' => 'item_name', 
                    'header' => 'Item Name'
                ),
                array(
                    'name' => 'dosage',
                    'header' => 'Dosage',
                    'value'  => function ($data) use ($dosageDataList, $encounter_nr) {
                        $prod_class = PackageDetails::model()->ProdClass($data['item_code']);
                        $disabled = ($prod_class == SUPPLY || $data['item_purpose'] == MISC) ? 'disabled' : '';
                        $prevDosage = PackageDetails::model()->DoFreRoute($data['item_code'], $encounter_nr);
                        $default_dosage = PackageDetails::model()->DefaultDosagePHIC($data['item_code']);
                        $dosage_value = $prevDosage[0]['dosage'] ? $prevDosage[0]['dosage'] : $default_dosage;
                        $dosageVal = ($prod_class == SUPPLY || $data['item_purpose'] == MISC) ? 'N/A' : $dosage_value;
                        echo '<input 
                            id="dosage[]" name="dosage[]" 
                            class="dosageText"
                            '.$disabled.'
                            list="dosages" 
                            value="' . $dosageVal . '"
                            style=" width:150px; height:24px;font-size:9pt;" 
                            maxlength="500"
                          >
                          <datalist id="dosages">';
                        foreach ($dosageDataList as $dosage) {
                            $dosage = $dosage['strength_disc'];
                            ?>
                            <option value="<?php echo $dosage; ?>"/>
                            <?php
                        }
                        echo '</datalist>';
                    },
                ),
                array(
                    'name' => 'frequency',
                    'header' => 'Frequency',
                    'value'  => function ($data) use ($frequencyDataList, $encounter_nr) {
                        $prod_class = PackageDetails::model()->ProdClass($data['item_code']);
                        $disabled = ($prod_class == SUPPLY || $data['item_purpose'] == MISC) ? 'disabled' : '';
                        $prevFrequency = PackageDetails::model()->DoFreRoute($data['item_code'], $encounter_nr);
                        $frequencyVal = ($prod_class == SUPPLY || $data['item_purpose'] == MISC) ? 'N/A' : $prevFrequency[0]['frequency'];
                        echo '<input 
                            id="frequency[]" name="frequency[]" 
                            list="frequencys" 
                            class="frequencyText"
                            '.$disabled.'
                            value="'.$frequencyVal.'"
                            style=" width:150px; height:24px;font-size:9pt;" 
                            maxlength="50"
                          >
                          <datalist id="frequencys">';
                        foreach ($frequencyDataList as $frequency) {
                            $frequency = $frequency['frequency_disc'];
                            ?>
                            <option value="<?php echo $frequency; ?>"/>
                            <?php
                        }
                        echo '</datalist>';
                    },
                ),
                array(
                    'name' => 'route',
                    'header' => 'Route',
                    'value'  => function ($data) use ($routeDataList, $encounter_nr) {
                        $prod_class = PackageDetails::model()->ProdClass($data['item_code']);
                        $disabled = ($prod_class == SUPPLY || $data['item_purpose'] == MISC) ? 'disabled' : '';
                        $prevRoute = PackageDetails::model()->DoFreRoute($data['item_code'], $encounter_nr);
                        $routeVal = ($prod_class == SUPPLY || $data['item_purpose'] == MISC) ? 'N/A' : $prevRoute[0]['route'];
                        echo '<input 
                            id="route[]" name="route[]" 
                            list="routes" 
                            class="routeText"
                            '.$disabled.'
                            value="'.$routeVal.'"
                            style=" width:150px; height:24px;font-size:9pt;" 
                            maxlength="500"
                          >
                          <datalist id="routes">';
                        foreach ($routeDataList as $route) {
                            $route = $route['route_disc'];
                            ?>
                            <option value="<?php echo $route; ?>"/>
                            <?php
                        }
                        echo '</datalist>';
                    },
                ),
                array(
                    'name' => 'quantity', 
                    'header' => 'Quantity'
                ),
                array(
                    'value' => function($data, $row){
                        return number_format($data['price'], 2);
                    },
                    'header' => 'Unit Price',
                    'headerHtmlOptions' => array(
                        'style' => 'text-align: right;'
                    ),
                ),
                array(
                    'value' => function($data, $row){
                        return number_format(($data['price'] * $data['quantity']), 2);
                    },
                    'header' => 'Total Price',
                    'headerHtmlOptions' => array(
                        'style' => 'text-align: right;'
                    ),
                ),
            ),
            'htmlOptions' => array(
                'data-package-price' => number_format($package_price, 2)
            )
        )
    );
    echo CHtml::hiddenField('encounter_nr', $encounter_nr);
    $this->endWidget();
?>

<script>
    $(document).ready(function() {
        var dosage = '.dosageText';
        var frequency = '.frequencyText';
        var route = '.routeText';

        $(document).off('keydown.yiiGridView change.yiiGridView').on('keyup', dosage, function() {
            var input = $(this).val();
            if(input.length === 500) {
                alert('You have reached the maximum number of Characters!');
            }
        });

        $(document).off('keydown.yiiGridView change.yiiGridView').on('keyup', frequency, function() {
            var input = $(this).val();
            if(input.length === 50) {
                alert('You have reached the maximum number of Characters!');
            }
        });

        $(document).off('keydown.yiiGridView change.yiiGridView').on('keyup', route, function() {
            var input = $(this).val();
            if(input.length === 500) {
                alert('You have reached the maximum number of Characters!');
            }
        });

    });
</script>
