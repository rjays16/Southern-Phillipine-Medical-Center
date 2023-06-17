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
         var prov_nr = $("#prov_nr");
         var mun_nr = $("#mun_nr");
         var brgy_nr = $("#brgy_nr");
        var loc = window.location;
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online';
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
                                text: '-Select a Municipality Name-'
                            })
                        );
                   brgy_nr.append(
                            $('<option>', {
                                value: '0',
                                text: '-Select a Barangay Name-'
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
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online';
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
                        );;
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
        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online';
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
    'OPD'                          => $baseUrl . '/modules/opd/seg-opd-functions.php?ntid=false&lang=en',
    'Online Consultation Requests' => 'index.php?r=onlineConsult/online',
    'Update Patient'
);

$this->pageTitle = '';

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'                   => 'update-patient-form',
    'type'                 => 'inline',
    'enableAjaxValidation' => false,
    'htmlOptions'          => array(
        'data-url' => $this->createUrl('/onlineConsult/online/updatePatient')
    )
));


// \CVarDumper::dump($personInfo,10,true);die();
?>
<div class="container" style="margin-left: 10px;">
    <h4 id="patientdetails-header">Personal Details</h4>
    <hr>
    <div class="row-fluid">
        <div class="span2">
            <font color="#ff0000"><?php echo CHtml::label('Registration Date and Time ', 'registrationDateTime'); ?></font>
        </div>
        <div class="span3">
            <?php
            echo $form->textField($model, 'date_reg',
                array(
                    'id'    => "date_reg",
                    'name'  => 'date_reg',
                    'value' => date("Y-m-d h:i A", strtotime($personInfo->date_reg)),
                    'readonly' => 'readonly'
                )
            );
            ?>
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
                    'value' => $personInfo->name_last
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

            $name_first = $personInfo->name_first;
            
            if($personInfo->suffix)
                $name_first = str_replace(' '.$personInfo->suffix, ', '.$personInfo->suffix, $personInfo->name_first);

            echo $form->textField($model, 'name_first',
                array(
                    'id'    => "name_first",
                    'name'  => 'name_first',
                    'value' => $name_first
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
                    'value' => $personInfo->name_middle
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
                        'class'       => 'dateTimeField',

                    ),
                    'options'     => array(
                        'format'       => 'yyyy-mm-dd',
                        'startDate'    => date('m/d/Y'),
                        'todayBtn'     => 'linked',
                        'autoclose'    => true,
                        'showMeridian' => true

                    ),
                    'value'       => date('m/d/Y', strtotime($personInfo->date_birth))
                )
            );
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font>Place of Birth</font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'place_birth',
                array(
                    'id'    => "place_birth",
                    'name'  => 'place_birth',
                    'value' => $personInfo->place_birth
                )
            );
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font color="#ff0000">Sex</font></label>
        </div>
        <div class="span1 radiobtn">
            <?php
            echo CHtml::radioButton('sex', ($personInfo->sex == 'm' ? true : false), array(
                'value'        => 'm',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1 radiobtnlabel"><?php echo 'Male'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('sex', ($personInfo->sex == 'f' ? true : false), array(
                'value'        => 'f',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1"><?php echo 'Female'; ?></div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font>Civil Status</font></label>
        </div>
        <div class="span1 radiobtn">
            <?php
            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'child' ? true : false), array(
                'value'        => 'child',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1 radiobtnlabel"><?php echo 'Child'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'single' ? true : false), array(
                'value'        => 'single',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1" style="margin-right: -30px"><?php echo 'Single'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'married' ? true : false), array(
                'value'        => 'married',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1" style="margin-right: -20px"><?php echo 'Married'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'divorced' ? true : false), array(
                'value'        => 'divorced',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1" style="margin-right: -10px"><?php echo 'Divorced'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'widowed' ? true : false), array(
                'value'        => 'widowed',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1" style="margin-right: -10px"><?php echo 'Widowed'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'separated' ? true : false), array(
                'value'        => 'separated',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1"><?php echo 'Separated'; ?></div>
        <div class="span1 radiobtn">
            <?php

            echo CHtml::radioButton('civil_status', ($personInfo->civil_status == 'annulled' ? true : false), array(
                'value'        => 'annulled',
                'uncheckValue' => null
            ));
            ?>
        </div>
        <div class="span1"><?php echo 'Annulled'; ?></div>
    </div>
      <div class="row-fluid">
        <div class="span2"><label><font color="#ff0000">Religion</label></font></div>
        <div class="span2">
            <?php
            echo CHtml::dropDownList('religion', $religionInfo, $religionlist, array(
                    'prompt' => '-Select a Religion-',
                    'id'     => 'religion_nr'
                )
            )
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font>Contact Number</font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'cellphone_1_nr',
                array(
                    'id'    => "contact_no",
                    'name'  => 'contact_no',
                    'value' => $personInfo->cellphone_1_nr
                )
            );

            ?>
        </div>
    </div>
    <br>
    <h4 id="patientdetails-header">Address</h4>
    <hr />
    <div class="row-fluid">
        <div class="span2"><label><font>Region's Name</font></label></div>
        <div class="span1">
          <div class="span2">
            <?php
            echo CHtml::dropDownList('region',$xregion->region_nr, $regions, array(
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
            echo CHtml::dropDownList('prov_nr',$xprovince->prov_nr, $provinces, array(
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
            echo CHtml::dropDownList('mun_nr',$personInfo->mun_nr, $municities, array(
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
            echo CHtml::dropDownList('brgy_nr',$personInfo->brgy_nr, $barangays, array(
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
                    'name'  => 'street_name',
                    'value'=>$personInfo->street_name
                )
            );
            ?>
        </div>
    </div>
   
     <br>
      <hr />
    <h4 id="patientdetails-header">Family Background:</h4>
    <div class="row-fluid">
        <div class="span2">
            <label><font >Father's Name </font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'father_fname',
                array(
                    'id'    => "father_fname",
                    'name'  => 'father_fname',
                    'value'=>$personInfo->father_fname
                )
            );
            ?>
        </div>
    </div>
     <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font >Mother's Name </font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'mother_fname',
                array(
                    'id'    => "mother_fname",
                    'name'  => 'mother_fname',
                    'value'=>$personInfo->mother_fname
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
                    'value'=>$personInfo->spouse_name
                )
            );
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span2">
            <label><font >Guardian's Name </font></label>
        </div>
        <div class="span1">
            <?php
            echo $form->textField($model, 'guardian_name',
                array(
                    'id'    => "guardian_name",
                    'name'  => 'guardian_name',
                    'value'=>$personInfo->guardian_name
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
            echo CHtml::dropDownList('occupation', $personInfo->occupation, $occupation, array(
                'id'     => 'occupation'
            ));
                
            ?>
       
        </div>
    </div>
    <br>
   <div class="row-fluid">
        <div class="span2"><label><font>Country of Nationality</font></label></div>
        <div class="span4">
            <?php
            echo CHtml::dropDownList('citizenship', $personInfo->citizenship, $citizenship, array(
                'id'     => 'citizenship'
            ))
            ?>
        </div>
    </div>
    <br>
    <div class="row-fluid">
        <div class="span1">
            <?php
            $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'buttonType'  => 'submit',
                    'type'        => 'primary',
                    'label'       => 'Update',
                    'url'         => '',
                    'htmlOptions' => array(
                        'id'    => 'btnSavePatient',
                        'class' => 'pull-left'
                    ),
                )
            );
            ?>
        </div>
        <div class="span1">
            <?php
            $this->widget(
                'bootstrap.widgets.TbButton',
                array(
                    'buttonType'  => 'ajaxButton',
                    'type'        => 'danger',
                    'label'       => 'Cancel',
                    'url'         => '',
                    'htmlOptions' => array(
                        'class'   => 'pull-left',
                        'onclick' => 'cancel()'
                    ),
                )
            );

            ?>
        </div>
    </div>
</div>

<script type="text/javascript">

    $('#update-patient-form').submit(function (e) {
        e.preventDefault();
        var $this = $(this);
        var data = $(this).serialize();
        var loc = window.location;
        var consult_id = "<?=$consult_id?>";
        var baseUrl = loc.protocol + "//" + loc.host + "/" + loc.pathname.split('/')[1] + '/index.php?r=onlineConsult/online/view_history';
        Alerts.confirm({
            title: "Are you sure you want to update changes?",
            content: "Patient's details will be updated",
            callback: function (result) {
                if (result) {
                    $.ajax({
                        url: $this.data('url'),
                        type: 'POST',
                        data: data,
                        dataType: 'json',
                        beforeSend: function () {
                            Alerts.loading({content: "Updating Patient's Details. Please wait..."});
                        },
                        success: function (data) {
                            if (data.success === true) {
                                Alerts.warn({
                                    title: 'Success!',
                                    content: "Patient's Details was successfully updated",
                                    icon: 'fa-check-circle-o',
                                    iconColor: '#2DCC70', actions: ''
                                });
                                window.location.href = baseUrl + "&pid=" + data.pid + "&id=" + consult_id;
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

    function cancel() {
        var $this = $(this);
        var data = $(this).serialize();
        var loc = window.location;
        var pid = $('#pid').val();
        var consult_id = "<?=$consult_id?>";
        var baseUrl = loc.protocol + "//" + loc.host + "/" + loc.pathname.split('/')[1] + '/index.php?r=onlineConsult/online/view_history';
        Alerts.confirm({
            title: "Are you sure you want to cancel changes?",
            content: "Changes on this form will be cancelled",
            callback: function (result) {
                if (result) {
                    window.location.href = baseUrl + "&pid=" + pid + "&id=" + consult_id;
                }
            }
        })


    }


</script>
<?php
echo \CHtml::hiddenField('pid', $personInfo->pid);
echo \CHtml::hiddenField('consult_id', $consult_id);
$this->endWidget();

?>

