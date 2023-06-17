<?php

$baseUrl = Yii::app()->request->baseUrl;

$cs = Yii::app()->clientScript;
$cs->registerCss('headCss', <<<CSS
         body{
            padding-top: 0;
         }
         body{
            padding-top: 0;
            box-sizing: border-box;
         }
        .button-submit-consult {
            background-color: #24A0ED;
            border: none;
            color: white;
            padding: 10px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }
        .column {
            float: left;
            width: 18%;
            padding: 10px;
            height: auto;
            margin-left: 10px;
        }
        .column-two {
            float: left;
            width: 20%;
            padding: 10px;
            height: auto;
        }
        .row:after {
          content: "";
          display: table;
          clear: both;
        }
        .first-row {
            padding: 10px;
            margin-left: 20px;
            font-family: "Times New Roman", Times, serif;
        }
        .second-row {
            padding: 8px;
        }

        .radiobtn{
            margin-right: -60px
        }
        .radiobtnlabel{
            margin-right: -35px
        }

         hr {
            height: 2px;
            color: #0a0a0a;
            background-color: #333;
            width: 460px;
         }
         
CSS
);

$js = <<<JAVASCRIPT

 $(document).ready(function(){
 
    $("#region_nr").change(function () {
        var region_nr = $( "#region_nr" ).val();
        var prov_nr   = $("#prov_nr");
        var mun_nr    = $("#mun_nr");
        var brgy_nr   = $("#brgy_nr");
        var loc       = window.location;
        var baseUrl   = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';

        $.ajax({
            url:baseUrl+"/ProvinceList",
            data:{
                region_nr : region_nr    
            },
            success: function(data){
                var obj = JSON.parse(data);
                $("#prov_nr option").remove();
                $("#mun_nr option").remove();
                $("#brgy_nr option").remove();

                prov_nr.append(
                    $('<option>', {
                        value: '',
                        text: '-Select a Province-'
                    })
                ).append(
                    $.map(obj.results, function(value,key) {
                        return $('<option>', {
                            value: value.prov_nr,
                            text:  value.prov_name,
                        });
                    })
                );

                mun_nr.append(
                    $('<option>', {
                        value: '0',
                        text: '-Select a Municipality-'
                    })
                );

               brgy_nr.append(
                    $('<option>', {
                        value: '0',
                        text: '-Select a Barangay-'
                    })
                );

                if(region_nr!=''){
                    $("#mun_nr").prop("disabled", true); 
                    $("#brgy_nr").prop("disabled", true); 
                    $("#prov_nr").prop("disabled", false); 
                }else{
                    $("#mun_nr").prop("disabled", true); 
                    $("#brgy_nr").prop("disabled", true); 
                    $("#prov_nr").prop("disabled", true); 
                }
            }
        });
    });

    $("#prov_nr").change(function () {
        var prov_nr = $("#prov_nr").val();
        var mun_nr = $("#mun_nr");
        var brgy_nr = $("#brgy_nr");
        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';

        $.ajax({
            url:baseUrl+"/MunicipalityList",
            data:{
                prov_nr : prov_nr    
            },
            success: function(data){
                var obj = JSON.parse(data);
                $("#mun_nr option").remove();
                $("#brgy_nr option").remove();

                mun_nr.append(
                    $('<option>', {
                        value: '',
                        text: '-Select a Municipality Name-'
                    })
                ).append(
                    $.map(obj.results, function(value,key) {
                        return $('<option>', {
                            value: value.mun_nr,
                            text:  value.mun_name,
                        });
                    })
                );

                brgy_nr.append(
                    $('<option>', {
                        value: '0',
                        text: '-Select a Barangay Name-'
                    })
                );

                if(prov_nr !=''){
                    $("#mun_nr").prop("disabled", false); 
                    $("#brgy_nr").prop("disabled", true); 
                }else{
                    $("#mun_nr").prop("disabled", true); 
                    $("#brgy_nr").prop("disabled", true); 
                }
            }
        });
    });

     $("#mun_nr").change(function () {
        var mun_nr = $("#mun_nr").val();
        var brgy_nr = $("#brgy_nr");
        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';

        $.ajax({
            url:baseUrl+"/BarangayList",
            data:{
                mun_nr : mun_nr    
            },
            success: function(data){
                var obj = JSON.parse(data);
                $("#brgy_nr option").remove();

                brgy_nr.append(
                    $('<option>', {
                        value: '0',
                        text: '-Select a Barangay Name-'
                    })
                ).append(
                    $.map(obj.results, function(value,key) {
                        return $('<option>', {
                            value: value.brgy_nr,
                            text:  value.brgy_name,
                        });
                    })
                );

                if(mun_nr!=''){
                    $("#brgy_nr").prop("disabled", false); 
                }else{
                    $("#brgy_nr").prop("disabled", true); 
                }
            }
        });
    });
    
});
   
   
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);

$this->breadcrumbs = array(
    'OPD' => $baseUrl . '/modules/opd/seg-opd-functions.php?ntid=false&lang=en',
    'Online Consultation Requests' => 'index.php?r=medRec/online',
    'New Patient'
);

$this->pageTitle = '';

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'                   => 'create-patient-form',
    'type'                 => 'inline',
    'enableAjaxValidation' => false,
    'htmlOptions'          => array(
        'data-url' => $this->createUrl('/medRec/online/saveNewPatient')
    )
));

?>
<div class="container" style="margin-left: 10px;">
    <h4 id="allotment-header">Personal Details</h4>
    <hr>
    <div class="row-fluid">
        <div class="span2">
            <font color="#ff0000"><?php echo CHtml::label('Registration Date and Time ', 'registrationDateTime'); ?></font>
        </div>
        <div class="span1">
            <div>
                <?php
                 $this->widget(
                        'bootstrap.widgets.TbDateTimePicker',
                        array(
                            'name'        => 'date_reg',
                            'htmlOptions' => array(
                                'placeholder' => 'Select Date and Time',
                                'class'       => 'dateTimeField',
                                'id'          => 'date_reg'
                            ),
                            'options'     => array(
                                'format'       => 'yyyy-mm-dd HH:ii P',
                                'startDate'    => date('Y-m-d'),
                                'todayBtn'     => 'linked',
                                'autoclose'    => true,
                                'showMeridian' => true

                            ),
                            'value'       => date('Y-m-d h:i A')
                        )
                    );

                ?>

            </div>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font color="#ff0000">Family Name</font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'name_last',
                array(
                    'id'    => "name_last",
                    'name'  => 'name_last',
                    'value' => $consultData->name_last
                )
            );

            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font color="#ff0000">Given Name</font></label>
        </div>
        <div class="span1">
            <?php

            echo $form->textField($model, 'name_first',
                array(
                    'id'    => "name_first",
                    'name'  => 'name_first',
                    'value' => $consultData->name_first
                )
            );
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font>Middle Name</font></label>
        </div>
        <div class="span1">
            <?php

            echo $form->textField($model, 'name_first',
                array(
                    'id'    => "name_middle",
                    'name'  => 'name_middle',
                    'value' => $consultData->name_middle
                )
            );
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font color="#ff0000">Date of Birth</font></label>
        </div>
        <div class="span1">
            <?php
                 $this->widget(
                        'bootstrap.widgets.TbDatePicker',
                        array(
                            'name'        => 'date_birth',
                            'htmlOptions' => array(
                                'placeholder' => 'Select Date and Time',
                                'class'       => 'dateTimeField'
                            ),
                            'options'     => array(
                                'format'       => 'yyyy-mm-dd',
                                'startDate'    => date('Y-m-d'),
                                'todayBtn'     => 'linked',
                                'autoclose'    => true,
                                'showMeridian' => true

                            ),
                            'value'       => date('Y-m-d', strtotime($consultData->date_birth))
                        )
                    );
                    ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font >Place of Birth</font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'place_birth',
                array(
                    'id'    => "place_birth",
                    'name'  => 'place_birth'
                )
            );
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font  color="#ff0000">Sex</font></label>
        </div>
        <div class="span1 radiobtn">
                <?php
                    echo CHtml::radioButton('sex', ($consultData->sex == 'M' ? true : false), array(
                        'value'        => 'm',
                        'uncheckValue' => null
                    ));
                 ?>
        </div>
        <div class="span1 radiobtnlabel"><?php echo 'Male'; ?></div>
        <div class="span1 radiobtn">
            <?php

                echo CHtml::radioButton('sex', ($consultData->sex == 'F' ? true : false), array(
                    'value'        => 'f',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1" ><?php echo 'Female'; ?></div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font>Civil Status</font></label>
        </div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status', '', array(
                    'value'        => 'child',
                    'uncheckValue' => null
                ));
             ?>
        </div>
        <div class="span1 radiobtnlabel"><?php echo 'Child'; ?></div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status','', array(
                    'value'        => 'single',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1" style="margin-right: -30px"><?php echo 'Single'; ?></div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status','', array(
                    'value'        => 'married',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1" style="margin-right: -20px"><?php echo 'Married'; ?></div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status','', array(
                    'value'        => 'divorced',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1" style="margin-right: -10px"><?php echo 'Divorced'; ?></div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status','', array(
                    'value'        => 'widowed',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1" style="margin-right: -10px"><?php echo 'Widowed'; ?></div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status','', array(
                    'value'        => 'separated',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1"><?php echo 'Separated'; ?></div>
        <div class="span1 radiobtn">
            <?php
                echo CHtml::radioButton('civil_status','', array(
                    'value'        => 'annulled',
                    'uncheckValue' => null
                ));
            ?>
        </div>
        <div class="span1"><?php echo 'Annulled'; ?></div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2"><label><font color="#ff0000">Religion</label></font></div> 
        <div class="span4" style="margin-right: 10px">
            <?php
            echo CHtml::dropDownList('religion', '', $religions, array(
                'prompt' => '-Select a Religion-',
                'id'     => 'religion_nr'
            ));
                
            ?>
           <i> <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$consultData->religion ?> </i>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2"><label><font>Contact Number</font></label></div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'cellphone_1_nr',
               array(
                   'id'    => "contact_no",
                   'name'  => 'contact_no',
                   'value' => $consultData->contact_no
               )
            );

            ?>
        </div>
    </div>
    <br>
    <hr />

    <div class="row-fluid">
        <div class="span2"><label><i>Address</i></label></div>
        <div class="span6">
          <div class="span7">
            <i>
            <?php
            echo trim($consultData->address);
            ?>
            </i>
        </div>
        </div>
    </div>
    <br>

   <div class="row-fluid">
        <div class="span2"><label><font>Region's Name</font></label></div>
        <div class="span1">
          <div class="span2">
            <?php
            echo CHtml::dropDownList('region', $defregion, $regions, array(
                'prompt' => '-Select a Region-',
                'id'     => 'region_nr'
            ))
            ?>
        </div>
        </div>
    </div>
    <br>
   <div class="row-fluid">
        <div class="span2"><label><font>Province's Name</font></label></div>
        <div class="span1">
          <div class="span2">
            <?php
            echo CHtml::dropDownList('prov_nr', '3', $provinces, array(
                'prompt' => '-Select a Province-',
                'id'     => 'prov_nr'
            ))
            ?>
        </div>
        </div>
    </div>
<br>
   <div class="row-fluid">
        <div class="span2"><label><font>Municipality/City's Name </font></label></div>
        <div class="span1">
          <div class="span2">
            <?php
            echo CHtml::dropDownList('mun_nr', '24', $municities, array(
                'prompt' => '-Select a Municipality/City-',
                'id'     => 'mun_nr'
            ))
            ?>
        </div>
        </div>
    </div>
    <br>
   <div class="row-fluid">
        <div class="span2"><label><font>Barangay's Name </font></label></div>
        <div class="span1">
          <div class="span2">
            <?php
            echo CHtml::dropDownList('brgy_nr','', $barangays, array(
                'prompt' => '-Select a Barangay-',
                'id'     => 'brgy_nr'
            ))
            ?>
        </div>
        </div>
    </div>
    <br>
     <div class="row-fluid">
        <div class="span2">
            <label><font >House No./Street </font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'street_name',
                array(
                    'id'    => "street_name",
                    'name'  => 'street_name'
                )
            );
            ?>
        </div>
    </div>
    <br>
    <hr/>
     <div class="row-fluid">
        <div class="span2"><label><h6>Family Background:</h6></label></div>
        <div class="span6">
          <div class="span7">
        </div>
        </div>
    </div>
    <br>
     <div class="row-fluid">
        <div class="span2">
            <label><font >Father's Name </font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'father_fname',
                array(
                    'id'    => "fathers_fname",
                    'name'  => 'fathers_fname',
                    'value' => $consultData->fathers_name
                )
            );
            ?>
        </div>
    </div>
    <br>
     <div class="row-fluid">
        <div class="span2">
            <label><font >Mother's Name</font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'mother_fname',
                array(
                    'id'    => "mother_fname",
                    'name'  => 'mother_fname',
                    'value' => $consultData->mothers_name
                )
            );
            ?>
        </div>
    </div>
    <br>
     <div class="row-fluid">
        <div class="span2">
            <label><font >Spouse's Name </font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'spouse_name',
                array(
                    'id'    => "spouse_name",
                    'name'  => 'spouse_name',
                    'value' => $consultData->spouse_name
                )
            );
            ?>
        </div>
    </div>
      <br>
     <div class="row-fluid">
        <div class="span2">
            <label><font >Guardian's Name</font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'guardian_name',
                array(
                    'id'    => "guardian_name",
                    'name'  => 'guardian_name',
                    'value' => $consultData->guardians_name
                )
            );
            ?>
        </div>
    </div>
    <br>
     <hr/>
     <div class="row-fluid">
        <div class="span2"><label><h6>Other Personal Details:</h6></label></div>
        <div class="span6">
          <div class="span7">
        </div>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2"><label>Occupation</label></div> 
        <div class="span4" style="margin-right: 10px">
            <?php
            echo CHtml::dropDownList('occupation', '', $occupation, array(
                'id'     => 'occupation'
            ));
                
            ?>
           <i> <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$consultData->occupation ?> </i>
        </div>
    </div>
    <br>
   <div class="row-fluid">
        <div class="span2"><label><font>Country of Nationality</font></label></div>
        <div class="span4">
         
            <?php
            echo CHtml::dropDownList('citizenship', 'PH', $citizenship, array(
                'id'     => 'citizenship'
            ))
            ?>
        <i> <?php echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$consultData->citizenship ?> </i>
        </div>
    </div>
    <br>
   
    <div class="row-fluid">
        <?php
        $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType'  => 'submit',
                'type'        => 'primary',
                'label'       => 'Save',
                'url'         => '',
                'htmlOptions' => array(
                    'id' => 'btnSavePatient',
                   'class'=>'pull-left'
                ),
            )
        ); ?>
    </div>
</div>

<script type="text/javascript">
    $('#create-patient-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var data = $(this).serialize();
        var consult_id = "<?=$consult_id?>";
        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online/view_history';
        Alerts.confirm({
            title: "Are you sure you want to save changes?",
            content: "Details will be saved as new patient",
            callback: function(result) {
                if(result){
                    $.ajax({
                        url: $this.data('url'),
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            Alerts.loading({content: "Saving Patient's Details. Please wait..."});
                        },
                        success: function (data) {
                            if (data.success === true) {
                                Alerts.warn({
                                    title: 'Success!',
                                    content: "Patient's Details was successfully saved",
                                    icon: 'fa-check-circle-o',
                                    iconColor: '#2DCC70', actions: ''
                                });
                                window.location.href = baseUrl + "&pid="+data.pid+"&id="+consult_id;
                            } else {
                                Alerts.warn({
                                    title: 'Error!',
                                    content: data.errors,
                                    icon: 'fa fa-times-circle-o',
                                    iconColor: '#EC1F13',
                                    callback: function (result) {
                                        Alerts.close();
                                    }
                                });
                                return false;
                            }
                        }
                    });
                }
            }
        })

        return false;
    });

</script>
<?php
$this->endWidget();

?>

