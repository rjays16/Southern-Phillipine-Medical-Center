<?php
Yii::import('bootstrap.components.Bootstrap');
Yii::import('bootstrap.widgets.TbSelect2');
Yii::import('bootstrap.widgets.TbButton');
Yii::import('bootstrap.widgets.TbGridView');
Yii::import('bootstrap.widgets.TbActiveForm');


Yii::app()->clientScript->registerScript('vital-form', <<<JAVASCRIPT

$('#AddBtnVital').on('click',function(e){
    var ststolic = $('#ststolic').val();
    var diastolic = $('#diastolic').val();
    var pulse_rate = $('#pulse_rate').val();
    var respiratory = $('#respiratory').val();
    var temperature = $('#temperature').val();
    var textTime = $('#textTime').val();
    var textDate= $('#textDate').val();
    var chkDateFormat = new Date(textDate+' '+textTime);

    if(chkDateFormat !='Invalid Date'){
        saveVitalSign();
    }else{
        alert("Your browser is not yet updated, please contact IHOMP");
    }
});

$('#ststolic').on('keypress', function(e){
  var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$('#diastolic').on('keypress', function(e){
  var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$('#pulse_rate').on('keypress', function(e){
  var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$('#respiratory').on('keypress', function(e){
  var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$('#temperature').on('keypress', function(e){
  var regex = new RegExp("^[0-9.]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

function validateFloatKeyPress(el, evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    var number = el.value.split('.');
    if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    //just one dot
    if(number.length>1 && charCode == 46){
         return false;
    }
    //get the carat position
    var caratPos = getSelectionStart(el);
    var dotPos = el.value.indexOf(".");
    if( caratPos > dotPos && dotPos>-1 && (number[1].length > 1)){
        return false;
    }
    return true;
}

function getSelectionStart(o) {
	if (o.createTextRange) {
		var r = document.selection.createRange().duplicate()
		r.moveEnd('character', o.value.length)
		if (r.text == '') return o.value.length
		return o.value.lastIndexOf(r.text)
	} else return o.selectionStart
}

$('#weight').on('keypress', function(e){
    return validateFloatKeyPress(this, event);
});

$('#height').on('keypress', function(e){
    return validateFloatKeyPress(this, event);
});

$('#hip_line').on('keypress', function(e){
    return validateFloatKeyPress(this, event);
});

$('#waist_line').on('keypress', function(e){
    return validateFloatKeyPress(this, event);
});

$('#abdominal_girth').on('keypress', function(e){
    return validateFloatKeyPress(this, event);
});

function saveVitalSign(){
    var encounter_nr = $('#AddBtnVital').data('encounter');
    var pid = $('#AddBtnVital').data('pid');
    var ststolic = $('#ststolic').val();
    var diastolic = $('#diastolic').val();
    var pulse_rate = $('#pulse_rate').val();
    var respiratory = $('#respiratory').val();
    var temperature = $('#temperature').val();
    var textTime = $('#textTime').val();
    var textDate= $('#textDate').val();
    var loc = window.location;
    let saveRequest = true;
    let listdate = $('.vital_date');
    if (listdate.length) {
        let inputDate = new Date(textDate+' '+textTime);
        listdate.each(function(k,v){
            var prevInputDate = new Date($(v).text());
            if (inputDate.getTime() === prevInputDate.getTime()) {
                alert("\\tFailed to Saved\\n Date & Time already exist");
                saveRequest = false;
                return false;            
            }
        });
        
    }
    
    var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=admission/vital';
    if (saveRequest)
        $.ajax({
            url:baseUrl+"/save",
            data:{
                    encounter_nr : encounter_nr,
                    pid : pid,
                    ststolic : ststolic,
                    diastolic : diastolic,
                    pulse_rate : pulse_rate,
                    respiratory : respiratory,
                    temperature : temperature,
                    textDate : textDate,
                    textTime : textTime    
            },
            success: function(result){
                var data = JSON.parse(result);
                alert(data.msg);
                $('#ststolic').val('');
                $('#diastolic').val('');
                $('#pulse_rate').val('');
                $('#respiratory').val('');
                $('#temperature').val('');
                $('#textTime').val('');
                $('#textDate').val('');
                window.location.reload();
            }
        });
}

$('#AddBtnBMI').on('click',function(e){
    let bmiDate = $('#bmiDate').val();
    let bmiTime = $('#bmiTime').val();
    let weight = $('#weight').val();
    let height = $('#height').val();
    let hip_line = $('#hip_line').val();
    let waist_line = $('#waist_line').val();
    let abdominal_girth= $('#abdominal_girth').val();
    let encounter_nr = $('#AddBtnBMI').data('encounter');
    let pid = $('#AddBtnBMI').data('pid');
    
    let dateFormat = new Date(bmiDate+' '+bmiTime);
    
    if(!weight){
        alert("Weight is required!");
            $('#weight').focus();
             return false;
    }
    
    if(!height){
        alert("Height is required!");
            $('#height').focus();
             return false;
    }

    if(weight == 0) {
        alert("Invalid input(Weight)");
            $('#weight').focus();
             return false;
    }
    
    if(height == 0) {
        alert("Invalid input(Height)");
            $('#height').focus();
             return false;
    }
    
    if(dateFormat !='Invalid Date'){
        let check = confirm('Are you sure you want to save your changes?');
        if (check) {
            saveBMI();
        }
    } else {
        alert("Incorrect date format!");
    }
    
    function saveBMI () {
        let details = {
            'pid': pid,
            'encounter_nr': encounter_nr,
            'bmi_date': bmiDate,
            'bmi_time': bmiTime,
            'weight': weight,
            'height': height,
            'hip_line': hip_line,
            'waist_line': waist_line,
            'abdominal_girth': abdominal_girth
        };
                
        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=admission/vital';
        
        $.ajax({
            url: baseUrl+"/saveBmi",
            data: {
                details : JSON.stringify(details)
            },
            success: function(results){
                let result = JSON.parse(results);
                if (result.code === 200) {
                    alert(result.msg);
                    $('#weight').val('');
                    $('#height').val('');
                    $('#hip_line').val('');
                    $('#waist_line').val('');
                    $('#abdominal_girth').val('');
                    $('#AddBtnBMI').css('display', 'none');
                    window.location.reload();
                } else {
                    alert('Please contact the administrator!');
                }
            }
        });
    }
});

$('#bmi-grid').on('click', '.btn-edit', function() {
    let id = $(this).val();
    let loc = window.location;
    let baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=admission/vital';
    
    let check = confirm('Are you sure you want to delete this data?');
    
    if (check) {
        $.ajax({
            url: baseUrl+"/deleteBmi",
            data: {
                id : id
            },
            success: function(results){
                let result = JSON.parse(results);
                console.log(result);
                if (result.code === 200) {
                    alert(result.msg);
                    window.location.reload();
                } else if (result.code === 201) {
                    alert(result.msg);
                } else if (result.code === 202) {
                    alert(result.msg);
                } else {
                    alert('Please contact the administrator!');
                }
            }
        });
    }
});

JAVASCRIPT
    , CClientScript::POS_READY);

$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'vital-form',
        'method' => 'post',
    )
);

?>
<div>
    <?php
    $model2 = new FreeFormModel();
    echo $form->datepickerRow(
        $model2, '',
        array(
            'options'     =>
                array(
                    'format'          => 'yyyy-mm-dd',
                    'autoclose'       => true,
                    'showButtonPanel' => true,

                ),
            'htmlOptions' => array(
                'style'       => 'width:85px;height:30px',
                'id'          => "textDate",
                'name'        => 'textDate',
                'placeholder' => 'yyyy-mm-dd',
                'value'       => date('Y-m-d')
            )
        )
    );

    echo $form->timeField(
        $model2, '',
        array(
            'size'  => 2,
            'id'    => "textTime",
            'name'  => 'textTime',
            'style' => ' width:98px;height:30px;margin-left:2px',
            'value' => date('H:i')

        )
    );

    echo "&nbsp&nbsp&nbsp BP:" . $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "ststolic",
                'name'  => 'ststolic',
                'style' => 'width:35px;height:30px;'
            )
        );

    echo "/";

    echo $form->textField(
        $model2, '',
        array('size'  => 2,
              'id'    => "diastolic",
              'name'  => 'diastolic',
              'style' => 'width:35px;height:30px;'
        )
    );

    echo "&nbsp mmHg";
    echo "&nbsp";

    echo "HR:" . $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "pulse_rate",
                'name'  => 'pulse_rate',
                'style' => 'width:35px;height:30px;'
            )
        );

    echo "/min";
    echo "&nbsp";

    echo "RR: " . $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "respiratory",
                'name'  => 'respiratory',
                'style' => 'width:35px;height:30px;'
            )
        );

    echo "/min";
    echo "&nbsp&nbsp";
    echo "Temperature: " . $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "temperature",
                'name'  => 'temperature',
                'style' => 'width:35px;height:30px;'
            )
        );

    echo "°C";
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'buttonType'  => TbButton::BUTTON_BUTTON,
            'id'          => 'AddBtnVital',
            'label'       => 'Add',
            'url'         => '#',
            'size'        => 'small',
            'htmlOptions' => array(
                'style'          => 'margin-left: 1em; margin-bottom:1em;border-radius:5px;color:#FFFFFF;background-color:#13D4FA;width:60px',
                'data-encounter' => $encounter_nr,
                'data-pid'       => $pid,
            )
        )
    );
    ?>
</div>
<?php

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
        'id'            => 'vital_details-grid',
        'type'          => 'striped',
        'dataProvider'  => $vitalList,
        'template'      => $template,
        'pagerCssClass' => 'pagination pull-right',
        'columns'       => array(
            array(
                'name'   => 'date_monitor',
                'header' => 'Date/Time',
                'value'  => 'Yii::app()->dateFormatter->format("d MMM y h:mm a ",strtotime($data->date_monitor))',
                'cssClassExpression' => 'vital_date'
            ),
            array(
                'name'   => 'systolic',
                'header' => 'Systole'
            ),
            array(
                'name'   => 'diastolic',
                'header' => 'Diastole'
            ),
            array(
                'name'   => 'respiratory',
                'header' => 'Resp Rate'
            ),
            array(
                'name'   => 'pulse_rate',
                'header' => 'Pulse Rate'
            ),
            array(
                'name'   => 'temperature',
                'header' => 'Temperature'
            ),
            array(
                'name'   => 'create_id',
                'header' => 'Encoder'
            ),
            array(
                'header'      => 'Action',
                'class'       => 'CButtonColumn',
                'template'    => '{delete}',
                'afterDelete' => 'function(){ 
                      window.location.reload();   
                }',
            ),
        ),
    )
);
$this->endWidget();


$form = $this->beginWidget(
    'bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'bmi-form',
        'method' => 'post',
    )
);
?>

<div>
    <?php
    $form_model = new FreeFormModel();

    echo $form->datepickerRow(
        $form_model, '',
        array(
            'options'     =>
                array(
                    'format'          => 'yyyy-mm-dd',
                    'autoclose'       => true,
                    'showButtonPanel' => true,

                ),
            'htmlOptions' => array(
                'style'       => 'width:85px;height:30px;border-radius:5px;',
                'id'          => "bmiDate",
                'name'        => 'bmiDate',
                'placeholder' => 'yyyy-mm-dd',
                'value'       => date('Y-m-d')
            )
        )
    );

    echo $form->timeField(
        $form_model, '',
        array(
            'size'  => 2,
            'id'    => "bmiTime",
            'name'  => 'bmiTime',
            'style' => ' width:98px;height:30px;margin-left:2px;border-radius:5px;',
            'value' => date('H:i')

        )
    );

    echo "&nbsp Weight: " .
        $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "weight",
                'name'  => 'weight',
                'style' => 'width:35px;height:30px;border-radius:5px;'
            )
        ) . " kg";

    echo "&nbsp Height: " .
        $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "height",
                'name'  => 'height',
                'style' => 'width:35px;height:30px;border-radius:5px;'
            )
        ) . " cm";

    echo "&nbsp Hip line: " .
        $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "hip_line",
                'name'  => 'hip_line',
                'style' => 'width:35px;height:30px;border-radius:5px;'
            )
        ) . " cm";

    echo "&nbsp Waist line: " .
        $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "waist_line",
                'name'  => 'waist_line',
                'style' => 'width:35px;height:30px;border-radius:5px;'
            )
        ) . " cm";

    echo "&nbsp Abdominal girth: " .
        $form->textField(
            $model2, '',
            array(
                'size'  => 2,
                'id'    => "abdominal_girth",
                'name'  => 'abdominal_girth',
                'style' => 'width:35px;height:30px;border-radius:5px;'
            )
        ) . " cm";

    ?>
    <div style="float: right;">
    <?php
    $this->widget(
        'bootstrap.widgets.TbButton',
        array(
            'buttonType'  => TbButton::BUTTON_BUTTON,
            'id'          => 'AddBtnBMI',
            'label'       => 'Add',
            'url'         => '#',
            'size'        => 'small',
            'htmlOptions' => array(
                'style'          => 'margin-left: 1em; margin-bottom:1em;border-radius:5px;color:#FFFFFF;background-color:#13D4FA;width:60px;',
                'data-encounter' => $encounter_nr,
                'data-pid'       => $pid,
            )
        )
    );
    ?>
    </div>
</div>
<?php

$bmi_template = "
        <div class='row-fluid'>
            <div class='pull-left'>{summary}</div>
            {items}
            <div class='pull-right'>{pager}</div>
        </div>
    ";

Yii::import('bootstrap.widgets.TbButton');

$that = $this;

$this->widget(
    'bootstrap.widgets.TbGridView',
    array(
        'id'            => 'bmi-grid',
        'type'          => 'stripped',
        'dataProvider'  => $bmiList,
        'template'      => $bmi_template,
        'pagerCssClass' => 'pagination pull-right',
        'columns'       => array(
            array(
                'name'              => 'bmi_date',
                'header'            => 'Date/Time',
                'headerHtmlOptions' => array(
                    'style' => 'text-align: center;'
                ),
                'value'             => 'Yii::app()->dateFormatter->format("d MMM y h:mm a ",strtotime($data->bmi_date))'
            ),
            array(
                'name'        => 'weight',
                'header'      => 'Weight',
                'htmlOptions' => array('style' => 'text-align:center'),
            ),
            array(
                'name'        => 'height',
                'header'      => 'Height',
                'htmlOptions' => array('style' => 'text-align:center'),
            ),
            array(
                'name'        => 'hip_line',
                'header'      => 'Hip line',
                'htmlOptions' => array('style' => 'text-align:center'),
            ),
            array(
                'name'        => 'waist_line',
                'header'      => 'Waist line',
                'htmlOptions' => array('style' => 'text-align:center'),
            ),
            array(
                'name'        => 'abdominal_girth',
                'header'      => 'Abdominal girth',
                'htmlOptions' => array('style' => 'text-align:center'),
            ),
            array(
                'name'              => 'bmi',
                'header'            => 'BMI',
                'headerHtmlOptions' => array(
                    'style' => 'text-align: center;'
                ),
                'htmlOptions'       => array('style' => 'text-align:center'),
                'value'             => function ($data) use ($pid) {
                    $model = new CarePerson();
                    $getCategory = $model->getBMICategory(
                        $pid, $data['height'], $data['weight']
                    );

                    return $getCategory;
                }
            ),
            array(
                'name'   => 'create_id',
                'header' => 'Encoder'
            ),
            array(
                'name'              => 'action',
                'header'            => 'Action',
                'headerHtmlOptions' => array(
                    'style' => 'width:100px;text-align: center;'
                ),
                'htmlOptions'       => array('style' => 'text-align:center'),
                'class'             => 'person.widgets.PersonCustomColumn',
                'value'             => function ($row, $data) use ($row, $that
                ) {
                    return "<div><button style='height: 26px;' class='btn-edit' type='button' value="
                        . $data->id
                        . "><i class='fa fa-times' style='color:red'/></button></div>";
                }
            )
        ),
    )
);
$this->endWidget();
?>


