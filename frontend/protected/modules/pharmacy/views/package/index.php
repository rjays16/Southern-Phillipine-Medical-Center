<?php


Yii::import('bootstrap.components.Bootstrap');
Yii::import('bootstrap.widgets.TbSelect2');
Yii::import('bootstrap.widgets.TbButton');
Yii::import('bootstrap.widgets.TbGridView');
Yii::import('bootstrap.widgets.TbActiveForm');

Yii::app()->clientScript->registerScript('package-form', <<<JAVASCRIPT
        var val = "";
        var selected = 0;
        
        $('select.med-select').change(function(){
            
            var id = $(this).data('id');
            var id2 = id ? 0 : 1;
            var val = $(this).val();
            $('#pndf').val('');
            $('#pndf').removeAttr("disabled");
            $('#brand_name').val('');
            $('#brand_name').removeAttr("disabled");
            var pid_previous = $('#pid').val();
            var encounter_nr_previous = $('#encounter_nr').val();
            var location = window.location;
            var baseUrlPrevious = location.protocol + "//" + location.host + "/" + location.pathname.split('/')[1]+'/index.php?r=pharmacy/package/previous/';
            var supplyClass = 'S';
            
            $.ajax({
                type:"POST",
                dataType:"json",
                url: baseUrlPrevious,
                data:{
                    pid: pid_previous,
                    encounter_nr: encounter_nr_previous,
                    bestellnum: val
                },
                success: function(result){
                    if(result.prodClass == supplyClass) {
                        $('#dosage').val('N/A');
                        $('#frequency').val('N/A');
                        $('#route').val('N/A');
                        $('#dosage').attr('disabled', true);
                        $('#frequency').attr('disabled', true);
                        $('#route').attr('disabled', true);
                    } else {
                        $('#dosage').val(result.dosage);
                        $('#frequency').val(result.frequency);
                        $('#route').val(result.route);
                        $('#dosage').attr('disabled', false);
                        $('#frequency').attr('disabled', false);
                        $('#route').attr('disabled', false);
                    }
                    
                },
                error: function (errorMsg) {
                    console.log(errorMsg);
                }}
            ); 
            
            if(val != ''){
                $($('select.med-select')[id2]).select2("val","");
                $('#pndf').attr("disabled","disabled");
                $('#pndf').val('');
                if(!id){
                    $('#brand_name').attr("disabled","disabled");
                    $('#brand_name').val('');
                }
            }else{
                var select = $('select.med-select[value!=""]');
                if(select.length > 0){
                    id = select.data('id');
                    $('#pndf').attr("disabled","disabled");
                    $('#pndf').val('');
                    if(!id){
                        $('#brand_name').attr("disabled","disabled");
                        $('#brand_name').val('');
                    }
                }else{
                    $('#brand_name').attr("disabled","disabled");
                    $('#brand_name').val('');
                }
                
                $('#dosage').val('');
                $('#frequency').val('');
                $('#route').val('');
            }

           
        });
            
        $('#packageSelectBtn').on('click', function(e){
            e.preventDefault();
            var price = $('#price').val();
            var quantity = $('#quantity').val();
            var generic_code = $('#packageSelect').val();
            var dosage = $('#dosage').val();
            var route = $('#route').val();
            var frequency = $('#frequency').val();
            var pid_previous = $('#pid').val();
            var encounter_nr_previous = $('#encounter_nr').val();
            var location = window.location;
            var baseUrlPrevious = location.protocol + "//" + location.host + "/" + location.pathname.split('/')[1]+'/index.php?r=pharmacy/package/previous/';

            $.ajax({
                type:"POST",
                dataType:"json",
                url: baseUrlPrevious,
                data:{
                    pid: pid_previous,
                    encounter_nr: encounter_nr_previous,
                    bestellnum: val
                },
                success: function(result){
                    if((result.route || result.frequency) && result.dosage) {
                    $('#dosage').val(result.dosage);
                    $('#frequency').val(result.frequency);
                    $('#route').val(result.route);
                }
                },
                error: function (errorMsg) {
                    console.log(errorMsg);
                }}
            ); 
            
            if($($('select.med-select')[0]).val() != ''){
                var pndf = "";
            }else if($($('select.med-select')[1]).val() != ''){
                var pndf = $('#brand_name').val();
            }else{
                var pndf = $('#pndf').val();
                if( pndf == ""){
                    alert("Please input Generic Name.");
                    return false;
                }
            }

            if($.trim(dosage) == ""){
                alert("Dosage is Required!");
                return false;
            }
            
            if($.trim(frequency) == ""){
                alert("Frequency is Required!");
                return false;
            }

            if($.trim(route) == ""){
                alert("Route is Required!");
                return false;
            }

            if(quantity <= 0 ){
                alert("Quantity less than 0");
                return false;
            }

            if($.trim(price)==""){
                alert("Enter total price.");
                return false;
            }
            var isPhiLib = 0;
            if($($('select.med-select')[1]).val() != ''){
                isPhiLib = 1;
            }
            updateTable(isPhiLib);
        });
       
       var total = $('#total').val();
       

       $("#listOrderTotalPrice").text(total);

        function updateTable(isPhiLib=0){
            
            var  encounter_nr = $('#packageSelectBtn').data('encounter');
            if(!isPhiLib){
                var generic_code = $('#packageSelect').val();
            }else{
                var generic_code = $('#packageNewSelect').val();
            }
            var l = window.location;
            var baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1]+'/index.php?r=pharmacy/package/save/';
            var quantity = $('#quantity').val();
            var price = $('#price').val();
            var dosage = $('#dosage').val();
            var route = $('#route').val();
            var frequency = $('#frequency').val();
            var textTime = $('#textTime').val();
            var textDate= $('#textDate').val();
            var order_dt = textDate + " " + textTime + ":00";
            if(!isPhiLib){
                var brand_name  = $('#pndf').val();
            }else{
                var brand_name  = $('#brand_name').val();
            }
            
            $.ajax({
                type:"POST",
                dataType:"json",
                url: baseUrl+"/save/",
                data:{
                     encounter_nr : encounter_nr,
                     gen_code : generic_code,
                     price : price,
                     quantity : quantity,
                     order_dt : order_dt,
                     dosage : dosage,
                     route : route,
                     frequency : frequency,
                     brand_name : brand_name,
                     isPhiLib : isPhiLib
                },
                success: function(result){
                 alert(result.msg);
                 $('#pndf').removeAttr("disabled");
                 $('#pndf').val('');
                 $('#brand_name').attr("disabled","disabled");
                 $('#brand_name').val('');
                 $('#quantity').val('');
                 $('#price').val('0');
                 $('#dosage').val('');
                 $('#route').val('');
                 $('#frequency').val('');
                 $($("select.med-select")[0]).select2('val', '');
                 $($("select.med-select")[1]).select2('val', '');
                 $('#dosage').attr('disabled', false);
                 $('#frequency').attr('disabled', false);
                 $('#route').attr('disabled', false);
                 $("#listOrderTotalPrice").text(result.total.toFixed(2));
                 $.fn.yiiGridView.update('package_details-grid');
                }}
            );
        }
      
 $('#quantity,.quantity').on('keypress',function (e) {    
    $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((e.which < 48 || e.which > 57)) {
            e.preventDefault();
        }
});

$('#price').on('keypress', function(e){
  var regex = new RegExp("^[0-9.]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$(document).on('change','.med-select',function(){
    var select = $(this);
    var id = select.data('id');
    if(select.val() != ""){
        $($(".select2-container")[id]).children().children('abbr').css({'display' : 'inline-block'});
    }else{
        $($(".select2-container")[id]).children().children('abbr').css({'display' : 'none'});
    }
});

$(document).ready(function(){

    $(document).on('click','#editItemList',function(e){
        e.preventDefault();
        var btnEdit = $(this);

        $('#package_details-grid input,#package_details-grid select').attr('disabled',false);
        $('#cancelEdit').parent().show();
        btnEdit.parent().hide();
        
    });

    $(document).on('click','#cancelEdit',function(e){
        e.preventDefault();
        var btnCancel = $(this);
       
        $.fn.yiiGridView.update('package_details-grid');
        $('#package_details-grid input,#package_details-grid select').attr('disabled',true);
        $('#cancelEdit').parent().hide();
        $('#editItemList').parent().show();
    });

    $(document).on('click','#saveItemList',function(e){
        e.preventDefault();
        var btn = $(this);
        var data = [];
        var dosageEmpty = [];
        var frequencyEmpty = [];
        var routeEmpty = [];
        var gridQty = [];
            
        var tr = $('#package_details-grid table tr');
        tr.each(function(i,e){
            if(i != 0){
                var input = $(e).find('td select, td input');
                var grid_qty = $(input[1]).val();
                var dosage = $(input[2]).val();
                var frequency = $(input[3]).val();
                var route = $(input[4]).val();
                    
                if(grid_qty < 0 || !$.isNumeric(grid_qty)) {
                    gridQty.push('Invalid');
                }
                
                if(dosage.trim() === '') {
                    dosageEmpty.push('EmptyDosage');
                }
                    
                if(frequency.trim() === '') {
                    frequencyEmpty.push('EmptyFrequency');
                }
    
                if(route.trim() === '') {
                    routeEmpty.push('EmptyRoute');
                }
                
                data[i-1] = {
                    'id' : $(input[0]).val(),
                    "quantity" : grid_qty,
                    "dosage" : dosage,
                    "frequency" : frequency,
                    "route" : route
                }
            }
        });

        if (gridQty.length > 0) {
            alert("Invalid Quantity!");
            return false;
        } else if (dosageEmpty.length > 0) {
            alert("Dosage is Required!");
            return false;
        } else if (frequencyEmpty.length > 0) {
            alert("Frequency is Required!");
            return false;
        } else if (routeEmpty.length > 0) {
            alert("Route is Required!");
            return false;
        } else {
            if(confirm('save changes?')){
                $('#cancelEdit').parent().hide();
                $('#editItemList').parent().show();
                var l = window.location;
                var baseUrl = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1]+'/index.php?r=pharmacy/package/savelist/';
                    
                $.ajax({
                    type:"POST",
                    dataType:"json",
                    url: baseUrl+"/savelist/",
                    data:{
                        data:data
                    },
                    success: function(result){
                        alert(result);
                        $('#package_details-grid input,#package_details-grid select').attr('disabled',true);
                        $.fn.yiiGridView.update('package_details-grid');
                    }}
                );
            }
        }
        
    });

    $('#dosage').on('keypress', function(e){
      var val = $(this).val().length;
      if(val == 500) {
        alert('You have reached the maximum number of Characters!');
      }
    });
    
    $('#frequency').on('keypress', function(e){
      var val = $(this).val().length;
      if(val == 50) {
        alert('You have reached the maximum number of Characters!');
      }
    });
    
    $('#route').on('keypress', function(e){
      var val = $(this).val().length;
      if(val == 500) {
        alert('You have reached the maximum number of Characters!');
      }
    });
});


JAVASCRIPT
    , CClientScript::POS_READY);

$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'package-form',
        'method' => 'post',
    )
);

define('SUPPLY', 'S');
?>

<div class="row-fluid">
    <div class="col-md-12">
        <?php
        $fo = new TbActiveForm();
        $model2 = new FreeFormModel();
        echo CHtml::tag('h6',
            array(),
            'Pharmacy Library'
        );
        $this->widget(
            'bootstrap.widgets.TbSelect2',
            array(
                'name' => 'packageSelect',
                'id' =>  'packageSelect',
                'data' => $packageList,
                'options' => array(
                    'minimumInputLength' => '3',
                    'placeholder' => 'Enter the medicine name.',
                    'allowClear' => true
                ),
                'htmlOptions' => array(
                    'data-id' => '0',
                    'class' => 'med-select',
                ),

            )           
        );
        
        ?>

    </div>
   
    <div class="row-fluid">
        <div class="pull-left">
            <?php
            echo CHtml::tag('span',
                array(
                    'style' => 'font-weight:bold;'
                ),
                'PhilHealth Library'
            );
            echo "<br>";
            $this->widget(
                'bootstrap.widgets.TbSelect2',
                array(
                    'name' => 'packageNewSelect',
                    'data' => $newMedsList,
                    'options' => array(
                        'minimumInputLength' => '3',
                        'placeholder' => 'Enter the Drug name.'
                        ),
                    'htmlOptions' => array(
                        'data-encounter' => $encounter_nr,
                        'class' => 'med-select',
                        'data-id' => '1',
                    )
                )
            );
            ?>
        </div>

        <div class="pull-left " style="margin-left:10px;">
            <?php
            echo $form->textFieldGroup(
                $model2,
                '',
                array(
                    'widgetOptions' => array(
                        'htmlOptions' => array(
                            'id'       => 'brand_name',
                            'class'    => '',
                            'disabled' => 'disabled',
                            'style'    => 'height:25px; border-radius:7px',
                        )
                    ),
                    'min'           => 0,
                    'label'         => 'Brand Name',
                )
            );
            ?>
        </div>
    </div>

    <div class="col-md-4">
        <?php
            echo CHtml::tag('h6',
                array("style"=>"color: red;"),
                '*Note: If Medicine is not available in the list, kindly input the drug description as required'
            );
            echo CHtml::tag('h6',
                array(),
                'Generic Name/Salt/Strength/Form/Unit/Package '
            );
        ?>
    </div>
   
    <?php
    
    $model2->quantity = "";
    $model2->price = "";
    echo $form->textFieldGroup(
        $model2,
        '',
        array(
            'widgetOptions'      => array(
                'htmlOptions' => array(
                    'id'    => 'pndf',
                    'style' => 'width:500px;height:25px; border-radius:7px',
                ),
            ),
            'min'                => 0,
            'labelOptions'       => array(
                'class' => 'col-md-8',
            ),
            'wrapperHtmlOptions' => array(
                'class' => 'col-md-8',
            ),
        )
    );
    ?>

    <div class="row-fluid">
        <div class="pull-left">
            <div style="color: red;">
                Dosage *
            </div>
            <?php
            echo '<input 
                   id="dosage" name="dosage" 
                   list="dosages" 
                   style=" width:250px; height:24px;font-size:9pt;border-radius:7px;"
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
            ?>
        </div>
        
        <div class="pull-left " style="margin-left:10px;">
            <div style="color: red;">
                Frequency *
            </div>
            <?php
            echo '<input 
                   id="frequency" name="frequency" 
                   list="frequencys" 
                   style=" width:250px; height:24px;font-size:9pt;border-radius:7px;"
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
            ?>
        </div>
        
        <div class="pull-left " style="margin-left:10px;">
            <div style="color: red;">
                Route *
            </div>
            <?php
            echo '<input 
                   id="route" name="route" 
                   list="routes" 
                   style=" width:250px; height:24px;font-size:9pt;border-radius:7px;"
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
            ?>
        </div>
    </div>
    <div class="row-fluid">
        <div class="pull-left">
            <?php
            echo $form->textFieldGroup(
                $model2,
                '',
                array(
                    'widgetOptions'      => array(
                        'htmlOptions' => array(
                            'id'    => 'quantity',
                            'style' => 'height:24px;font-size:9pt;border-radius:7px;',
                        ),
                    ),
                    'min'                => 0,
                    'label'              => 'Quantity *',
                    'labelOptions'       => array(
                        'class' => 'col-md-6',
                        'style' => 'color:red',
                    ),
                    'wrapperHtmlOptions' => array(
                        'class' => 'col-md-6',

                    ),

                )
            );
            ?>
        </div>
        <div class="pull-left" style="display: none;">
            <?php
            echo $form->textFieldGroup(
                $model2,
                '',
                array(
                    'widgetOptions'      => array(
                        'htmlOptions' => array(
                            'value' => 0,
                            'id'    => 'price',
                        ),
                    ),
                    'min'                => 0,
                    'label'              => 'Total Price *',
                    'labelOptions'       => array(
                        'class' => 'col-md-6',
                        'style' => 'color:red;'
                    ),
                    'wrapperHtmlOptions' => array(
                        'class' => 'col-md-6',

                    ),

                )
            );
            ?>
        </div>
        <div class="pull-left" style="margin-left:23px;">
            Date
            <?php
            echo $form->datepickerRow(
                $model2,
                '',
                array(
                    'options' =>
                        array(
                            'format'          => 'yyyy-mm-dd',
                            'autoclose'       => true,
                            'showButtonPanel' => true,

                        ),
                    'htmlOptions' => array(
                        'style'       => 'width:100px;height:24px;font-size:12pt;border-radius:7px;',
                        'id'          => "textDate",
                        'name'        => 'textDate',
                        'placeholder' => 'yyyy-mm-dd',
                        'value'       => date('Y-m-d'),
                    ),
                )
            );
            echo $form->timeField(
                $model2,
                '',
                array(
                    'size'  => 2,
                    'id'    => "textTime",
                    'name'  => 'textTime',
                    'style' => 'margin-left:2px;width:120px;height:30px;font-size:12pt;border-radius:7px;',
                    'value' => date('H:i'),
                )
            );
            ?>
        </div>
    </div>
    <div class="pull-left" style="margin-top:10px;">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'id'          => 'packageSelectBtn',
                'label'       => 'Add',
                'url'         => '#',
                'size'        => 'small',
                'htmlOptions' => array(
                    'style'          => 'font-size:12pt;width:80px;border-radius:7px;background-color:#1BF6F6;',
                    'data-encounter' => $encounter_nr,
                ),
            )
        );
        ?>
    </div>
</div>
<hr>
</hr>
<div class="row-fluid">
    <div class="pull-right" style="">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'id'          => 'saveItemList',
                'label'       => 'Save',
                'url'         => '#',
                'size'        => 'small',
                'htmlOptions' => array(
                    'style'          => 'font-size:12pt;width:80px;border-radius:7px;background-color:#1BF6F6;',
                    'data-encounter' => $encounter_nr,
                ),
            )
        );
        ?>
    </div>
    <div class="pull-right" style="display:none;">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'id'          => 'cancelEdit',
                'label'       => 'Cancel',
                'url'         => '#',
                'size'        => 'small',
                'htmlOptions' => array(
                    'style'          => 'font-size:12pt;width:80px;border-radius:7px;background-color:#1BF6F6;',
                    'data-encounter' => $encounter_nr,
                ),
            )
        );
        ?>
    </div>
    <div class="pull-right" style="">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'id'          => 'editItemList',
                'label'       => 'Edit',
                'url'         => '#',
                'size'        => 'small',
                'htmlOptions' => array(
                    'style'          => 'font-size:12pt;width:80px;border-radius:7px;background-color:#1BF6F6;',
                    'data-encounter' => $encounter_nr,
                ),
            )
        );
        ?>
    </div>
</div>
<?php
$template = "
        <div class='row-fluid'>
            <div class='pull-left'>{summary}</div>
            {items}
            <div class='pull-right'>{pager}</div>
        </div>
    ";
//CVarDumper::dump($listOrder,10,true);

$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'id'            => 'package_details-grid',
        'type'          => 'striped',
        'dataProvider'  => $listOrder,
        'template'      => $template,
        'pagerCssClass' => 'pagination pull-right',
        'columns' => array(
            array(
                'name'   => 'order_dt',
                'header' => 'Date',
                'value'  => 'Yii::app()->dateFormatter->format("d MMM y h:mm a ",strtotime($data->order_dt))',
            ),
            array(
                'name'        => '',
                'header'      => '',
                'type'        => 'raw',
                'htmlOptions' => array('width:0px;'),
                'value'       => function ($data) {
                    return CHTML::hiddenField(
                        "",
                        $data->id,
                        array('type' => 'hidden', 'disabled' => 'disabled')
                    );
                },
            ),
            array(
                'name'   => 'gen_code',
                'header' => 'Item Code',
            ),
            array(
                'value'  => function ($data) {
                    if (empty($data['gen_code'])) {
                        return $data['drug_code'];
                    }
                    if (empty($data['drug_code'])) {
                        $model = CarePharmaProductsMain::model()
                            ->findByAttributes(
                                array('bestellnum' => $data['gen_code'])
                            )->drug_code;

                        return $model;
                    }
                },
                'header' => 'Drug Code',
            ),
            array(
                'value'  => function ($data) {
                    if (empty($data['gen_code'])) {
                        if (is_null($data['description'])) {
                            return $data['brand_name'];
                        } else {
                            return $data['description'];
                        }
                    } else {
                        if (empty($data['description'])) {
                            return $data['generic'];
                        } else {
                            return $data['description'];
                        }
                    }

                },
                'header' => 'Generic Description',
            ),
            array(
            'name' => 'quantity',
            'header'=>'Qty',
            'value' => function ($data) {
                return '<input id="quantity" name="quantity" value="'
                    . $data->quantity
                    . '" style=" width:50px; height:24px;font-size:9pt;" disabled />';
            },
            'type' => 'raw',
            ),
            array(
                'type'   => 'raw',
                'header' => 'Dosage',
                'value'  => function ($data) use ($dosageDataList) {
                    $prodClass = CarePharmaProductsMain::model()->ProdClass($data['gen_code']);
                    $disabled = $prodClass == SUPPLY ? 'disabled' : '';
                    $dosageVal = $prodClass == SUPPLY ? 'N/A' : $data->dosage;
                    echo '<input 
                            id="dosage" name="dosage" 
                            list="dosages" 
                            '.$disabled.'
                            value="'.$dosageVal.'"
                            style=" width:250px; height:24px;font-size:9pt;" 
                            maxlength="50"
                            disabled
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
                'type'   => 'raw',
                'header' => 'Frequency',
                'value'  => function ($data) use ($frequencyDataList) {
                    $prodClass = CarePharmaProductsMain::model()->ProdClass($data['gen_code']);
                    $disabled = $prodClass == SUPPLY ? 'disabled' : '';
                    $frequencyVal = $prodClass == SUPPLY ? 'N/A' : $data->frequency;
                    echo '<input 
                            id="frequency" name="frequency" 
                            list="frequencys" 
                            '.$disabled.'
                            value="'.$frequencyVal.'"
                            style=" width:250px; height:24px;font-size:9pt;" 
                            maxlength="50"
                            disabled
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
                'type'   => 'raw',
                'header' => 'Route',
                'value'  => function ($data) use ($routeDataList) {
                    $prodClass = CarePharmaProductsMain::model()->ProdClass($data['gen_code']);
                    $disabled = $prodClass == SUPPLY ? 'disabled' : '';
                    $routeVal = $prodClass == SUPPLY ? 'N/A' : $data->route;
                    echo '<input 
                            id="route" name="route" 
                            list="routes" 
                            '.$disabled.'
                            value="'.$routeVal.'"
                            style=" width:250px; height:24px;font-size:9pt;" 
                            maxlength="50"
                            disabled
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
                'value'  => function ($data, $row) {
                    return number_format(($data['price']), 2);
                },
                'header' => 'Total',
            ),
            array(
                'name'   => 'create_id',
                'header' => 'Encoder',
            ),
            array(
                'header'      => '[-]',
                'class'       => 'CButtonColumn',
                'template'    => '{delete}',
                'afterDelete' => 'function(){
                        window.location.reload();
                }',
                'htmlOptions' => array(
                    'style' => 'width:50px;text-align:center;',
                ),
            ),
        ),
    )
);
echo CHtml::hiddenField('encounter_nr', $encounter_nr);
echo CHtml::hiddenField('pid', $pid);
echo CHtml::hiddenField('total', number_format($listOrder_price, 2));

$this->endWidget();
?>