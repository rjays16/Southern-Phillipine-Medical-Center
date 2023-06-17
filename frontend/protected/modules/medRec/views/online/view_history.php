<?php
require_once($root_path . 'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_login_userid']);
// $_a_1_opdonlinerequest = $objAcl->checkPermissionRaw('_a_1_opdonlinerequest');
$_a_2_opdonlinecreateconsult = $objAcl->checkPermissionRaw('_a_2_opdonlinecreateconsult');
$_a_2_opdonlineregister = $objAcl->checkPermissionRaw('_a_2_opdonlineregister');

// if(($_a_1_opdonlinerequest && !($_a_2_opdonlineregister)  ||  $_a_2_opdonlinecreateconsult)){
if ($_a_2_opdonlineregister  ||  $_a_2_opdonlinecreateconsult) {
    $access_consult = true;
   
}else{
     $access_consult = false;
}


$baseUrl = Yii::app()->request->baseUrl;


$cs = Yii::app()->clientScript;
$cs->RegisterCss(
    'antibiotic-css', <<<CSS
    body ul.breadcrumb {
        margin-top: 0px;
    }
    .online-class {
        width: 25%;
        float: left;
    }
    hr {
        height: 2px;
        color: #0a0a0a;
        background-color: #333;
    }
    .sansserif {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 1.2em;
  }
  .sansserif-header {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 1.2em;
      font-weight: bold;
  }

  .split {
      height: 100%;
      width: 50%;
      position: fixed;
      z-index: 1;
      margin-left: 50px;
  }

  .left {
      left: 0;
  }

  .split-right {
      height: 100%;
      width: 50%;
      position: fixed;
      z-index: 1;
      margin-left: 50px;
  }

  .right {
      right: 30px;
  }

  .button-update {
    background-color: #24A0ED;
    border: none;
    color: white;
    padding: 5px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}

CSS
);


$js = <<<JAVASCRIPT

    var loc = window.location;

    function assignRadioValue(name, value, checked){
        $("input[name='"+name+"'][value='"+value+"']").prop("checked", checked);
    }

    $('.olencdetails').live("click", function(e){

        var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';

        $.ajax({
            url: baseUrl+"/openConsultationModal",
            type: 'GET',
            data: {encounter_nr: $(this).attr('id')},
            beforeSend: function () {
                Alerts.loading({content: 'Opening Consultation Data. Please wait...'});
            },
            success: function (data) {
                var obj = JSON.parse(data);

                if (obj.success === true) {
                    var encdetails = obj.details;

                    $("#consultationDateTime").attr("style", "display:none");

                    $("#enc_date").attr("style","");
                    $("#enc_date").val(encdetails.encounter_date);

                    $("#chief_complaint").val(encdetails.chief_complaint);
                    $('#Encounter_is_confidential').prop('checked', ((encdetails.is_confidential != 0 && encdetails.is_confidential != null) ? true : false));

                    $('.radioField').prop('checked', false);

                    assignRadioValue("Encounter[smoker_history]", encdetails.smoker_history, true);
                    assignRadioValue("Encounter[drinker_history]", encdetails.drinker_history, true);

                    $("#consult_dept").val(encdetails.consulting_dept_nr);

                    $("#consult_dr_nr option").remove();

                    var select = $("#consult_dr_nr");

                    select.append(
                        $('<option>', {
                            value: '',
                            text: '-Select a Doctor-'
                        })
                    ).append(
                        $.map(obj.doctorslist, function(value,key) {

                            var name = value.name_last+", "+value.name_first;

                            var convertedName = name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                    return letter.toUpperCase();
                                });

                            convertedName = convertedName + " " + value.mid_initial;

                            return $('<option>', {
                                value: value.personell_nr,
                                text: convertedName,
                                "data-haswebex" : value.haswebexid
                            });
                        })
                    );

                    $("#consult_dr_nr").val(encdetails.consulting_dr_nr);

                    var haswebex = $("#consult_dr_nr").find(':selected').attr('data-haswebex');

                    $("#docHaswebex").val(haswebex);

                    $("#btnSaveConsult").html("Update");
                    $("#btnSaveConsult").attr("data-encounter_nr", encdetails.encounter_nr);
                    $("#create-consultation-modal").modal();
                    $('#consultation-modal-header').html('Consultation Data');
                    Alerts.close();
                } else {
                    Alerts.warn({
                        title: 'Error!',
                        content: "There's an error retrieving patient's data",
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

        return false;

    });


    $("#btn_createconsult").on('click', function(){

        var chief_complaint = "$chief_complaint";

        $('#consultation-modal-header').html('New Online Consultation');
        $("#consultationDateTime").attr("style", "");

        $("#enc_date").attr("style","display:none");
        $("#enc_date").val('');

        $("#chief_complaint").val(chief_complaint);
        $('#Encounter_is_confidential').prop('checked', false);
        $('.radioField').prop('checked', false);
        $("#consult_dept").val('$selected_dept');

        $("#docHaswebex").val(0);
        $("#consult_dr_nr option").remove();

        var select = $("#consult_dr_nr");

        select.append(
            $('<option>', {
                value: '',
                text: '-Select a Doctor-'
            })
        );

        $("#btnSaveConsult").html("Save");
        $("#btnSaveConsult").attr("data-encounter_nr", '');
    });


    $('#btn_assign').on('click',function(){
        disableDoneRegister();
        
        var consult_id = $('#consult_id').val();
        var pid = $('#pid').val();
            var loc = window.location;
            var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';
                Alerts.confirm({
            title: "Are you sure you want to assign the selected consultation request to this patient?",
            content: "Details of assigned patient will be saved.",
            callback: function(result) {
                if(result){
                    $.ajax({
                        url: baseUrl+"/AssignPatient",
                        type: 'POST',
                        data:{
                            consult_id : consult_id,
                            pid : pid
                        },
                        dataType: 'json',
                        beforeSend: function () {
                            Alerts.loading({content: "Assigning Patient Please wait..."});
                        },
                        success: function (data) {
                            if (data.success === true) {
                                Alerts.warn({
                                    title: 'Success!',
                                    content: "Patient was successfully assign",
                                    icon: 'fa-check-circle-o',
                                    iconColor: '#2DCC70', actions: ''
                                });                                
                                window.location.href = baseUrl + "/view_history&pid="+data.pid+"&id="+consult_id;
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
    });

    $('#btn_assigned').on('click',function(){
        var consult_id = $('#consult_id').val();
        var pid = '';
         var loc = window.location;
         var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';
              Alerts.confirm({
            title: "Are you sure you want to cancel?",
            content: "Details will be saved",
            callback: function(result) {
                if(result){
                    $.ajax({
                        url: baseUrl+"/AssignPatient",
                        type: 'POST',
                        data:{
                            consult_id : consult_id,
                            pid : pid
                        },
                        dataType: 'json',
                        beforeSend: function () {
                            Alerts.loading({content: "Cancelling of assigning patient Please wait..."});
                        },
                        success: function (data) {
                            if (data.success === true) {
                                Alerts.warn({
                                    title: 'Success!',
                                    content: "Patient was successfully cancelled",
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
   });
 

JAVASCRIPT;




$cs->registerScript('js', $js, CClientScript::POS_READY);
$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css',
    CClientScript::POS_END
);
$cs->registerScriptFile(
    Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js',
    CClientScript::POS_END
);


$this->breadcrumbs = array(
    'Medical Records' => $baseUrl . '/modules/medocs/seg-medocs-functions.php?ntid=false&lang=en',
    'Triaged Online Consultation Requests' => 'index.php?r=medRec/online',
    'Patient Information'    
);
$this->pageTitle   = '';

?>

<input type="hidden" name="pid" id="pid" value='<?php  echo $personInfo->pid ?>'>
<input type="hidden" name="consult_id" id="consult_id" value='<?php  echo $_GET['id'] ?>'>

<h4 align="left">HRN : <?php echo $personInfo->pid;

if($is_assign){
        if($is_assign == $personInfo->pid){
               $this->widget(
                    'bootstrap.widgets.TbButton',
                    array(
                        'label'       => 'Cancel Assign',
                        'type'        => 'danger',
                        'url'         => '#',
                        'id'          => 'btn_assigned',
                        'disabled'    => '',
                        'htmlOptions' => array(
                            'data-tooltip' => 'tooltip',
                            'style'        => 'margin-left: 10px;'


                        ),

                    )
                );

        }else{
              $this->widget(
                    'bootstrap.widgets.TbButton',
                    array(
                        'label'       => 'Assigned',
                        'type'        => 'warning',
                        'url'         => '#',
                        'disabled'    => 'disabled',
                        'htmlOptions' => array(
                            'data-tooltip' => 'tooltip',
                            'style'        => 'margin-left: 10px;',

                        ),

                    )
                );

        }
      

}else{

    $this->widget(
                    'bootstrap.widgets.TbButton',
                    array(
                        'label'       => 'Assign',
                        'type'        => 'info',
                        'url'         => '#',
                        'disabled'    => '',
                        'id'          => 'btn_assign',
                        'htmlOptions' => array(
                            'data-toggle'  => 'modal',
                            'data-tooltip' => 'tooltip',
                            'style'        => 'margin-left: 10px;'
                        ),

                    )
                );
}
?>
</h4>
<h5>Date & Time Registered : <?php echo date("Y-m-d h:i A", strtotime($personInfo->date_reg)); ?> </h5>


<div class="container-fluid">
    <div class="row-fluid">
        <div class="span5">
            <div class="row-fluid">
                <hr/>
                <label class="sansserif-header">Personal Details</label>
                <?php 

                ?>
                <div class="span4">
                    <label>Family Name</label>
                    <label>Given Name</label>
                    <label>Middle Name</label>
                    <label>Date of Birth</label>
                    <label>Place of Birth</label>
                    <label>Sex</label>
                    <label>Civil Status</label>
                    <label>Religion</label>
                    <label>Contact Number</label>
                </div>
                <div class="span7">
                    <label>: <b><?php echo strtoupper($personInfo->name_last) ?></b></label>
                    <label>: <b>
                        <?php 
                        $name_first = $personInfo->name_first;
                        if($personInfo->suffix)
                            $name_first = str_replace(' '.$personInfo->suffix, ', '.$personInfo->suffix, $personInfo->name_first);

                        echo strtoupper($name_first); 
                        ?>   
                    </b>
                </label>
                <label>: <b><?php echo strtoupper($personInfo->name_middle)?></b></label>
                <label>: <b><?php echo $personInfo->date_birth ?></b></label>
                <label>: <b><?php echo strtoupper($personInfo->place_birth) ?></b></label>
                <label>: <b><?php echo ($personInfo->sex == 'f' ? 'FEMALE' : 'MALE') ?></b></label>
                <label>: <b><?php echo strtoupper($personInfo->civil_status) ?></b></label>
                <label>: <b><?php echo strtoupper($personInfo->religiondata->religion_name) ?></b></label>
                <label>: <b><?php echo $personInfo->cellphone_1_nr ?></b></label>
            </div>
        </div>
        <div class="row-fluid">
            <hr>
            <label class="sansserif-header">Address</label>
            <div class="span4">
                <label>House No/Street</label>
                <label>Barangay Name</label>
                <label>Municipality / City</label>
                <label>Province</label>
                <label>Region</label>
                <label>Zip Code</label>
            </div>
            <div class="span7">
                <label>: <b><?php echo strtoupper($personInfo->street_name) ?></b></label>
                <label>: <b><?php echo $personInfo->barangay->brgy_name ?></b></label>
                <label>: <b><?php echo $personInfo->municipality->mun_name ?></b></label>
                <label>: <b><?php echo $personInfo->municipality->parent->prov_name ?></b></label>
                <label>: <b><?php echo strtoupper($personInfo->municipality->parent->parent->region_name) ?></b></label>
                <label>: <b><?php echo $personInfo->municipality->zipcode ?></b></label>
            </div>
        </div>
        <div class="row-fluid">
            <hr>
            <label class="sansserif-header">Family Backgroud</label>
            <div class="span4">
                <label>Father's Name</label>
                <label>Mother's Name</label>
                <label>Spouse's Name</label>
                <label>Guardian's Name</label>
            </div>
            <div class="span7">
                <label>: <b><?php echo strtoupper($personInfo->father_fname) ?></b></label>
                <label>: <b><?php echo strtoupper($personInfo->mother_fname) ?></b></label>
                 <label>: <b><?php echo strtoupper($personInfo->spouse_name) ?></b></label>
                  <label>: <b><?php echo strtoupper($personInfo->guardian_name) ?></b></label>
            </div>
        </div>
        <div class="row-fluid">
            <hr>
            <label class="sansserif-header">Other Personal Details</label>
            <div class="span4">
                <label>Occupation</label>
                <label>Country of Nationality</label>
            </div>
            <div class="span7">
                <label>: <b><?php echo strtoupper($personInfo->work->occupation_name) ?></b></label>
                <label>: <b><?php echo $citizenship?></b></label>
            </div>
        </div>
        <hr>
    </div>
    <div class="span7">
        <div class="row-fluid">
            <div class="span6"><h4>Transaction History</h4></div>
            <div class="span6">
                <?php 
                if($access_consult && $is_assign == $personInfo->pid){  
                    $this->widget(
                        'bootstrap.widgets.TbButton',
                        array(
                            'label'       => 'Create Consultation',
                            'type'        => 'info',
                            'url'         => '#',
                            'disabled'    => '',
                            'id'          => 'btn_createconsult',
                            'htmlOptions' => array(
                                'data-toggle'  => 'modal',
                                'data-target'  => '#create-consultation-modal',
                                'data-tooltip' => 'tooltip',
                                'style'        => 'margin-left: 10px;float: right;'
                            ),

                        )
                    );

                }else{
                    $this->widget(
                        'bootstrap.widgets.TbButton',
                        array(
                            'label'       => 'Create Consultation',
                            'type'        => 'info',
                            'url'         => '#',
                            'disabled'    => '',
                            'id'          => 'btn_createconsult',
                            'htmlOptions' => array(
                                'data-toggle'  => 'modal',
                                'data-tooltip' => 'tooltip',
                                'style'        => 'margin-left: 10px;float: right;',
                                'disabled'=>'disabled',
                                'title'=>"No Access Permission"
                            ),

                        )
                    );

                }
              
                ?>
            </div>
        </div>
        <div class="row-fluid">
            <?php
            $this->widget('bootstrap.widgets.TbGridView', array(
                'id'            => 'patient-history-grid',
                'enableSorting' => false,
                'dataProvider'  => $patientHistory,
                'type'          => 'bordered',
                'columns'       => array(
                    array(
                        'header' => 'Date',
                        'value'  => function($data){
                            $date = ($data['admission_dt'] != NULL ? $data['admission_dt'] : $data['encounter_date']);

                            return date("Y-m-d h:i A", strtotime($date));
                        }

                    ),
                    array(
                        'header' => 'Case no',
                        'value'  => function($data){
                            if($data->meeting_id != NULL){
                                return CHtml::link($data['encounter_nr'], '#', array('class'=>'olencdetails', 'id' => $data['encounter_nr']));
                            }
                            else
                                return $data['encounter_nr'];
                        },
                        'type'    => 'raw'
                    ),
                    array(
                        'header' => 'Admission',
                        'value'  => function($data){
                            $ext = '';
                            if($data->meeting_id != NULL)
                                $ext = "-Online";

                            if($data->encounter_type == 2 && $data->official_receipt_nr==12)
                                return "OPD Online Consult";
                            else return $data->enctype->name.$ext;
                        }
                    ),
                    array(
                        'header' => 'Department',
                        'value'  => function($data){
                            return $data->department->name_formal;
                        }
                    ), 
                    array(
                        'header' => 'Discharge Date',
                        'value'  => function($data){
                            if($data['discharge_date'])
                                return date("Y-m-d", strtotime($data['discharge_date']));
                            else return $data['discharge_date'];
                        }
                    )
                )
            )
        );
        ?>
    </div>
</div>
</div>
<?php 
$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'label'       => 'Update',
        'type'        => 'info',
        'url'         =>  Yii::app()->createUrl("medRec/online/updateInfoPatient",array('pid'=> $personInfo->pid,'consultId'=>$consultId)),
        'disabled'    => '',
        'id'          => 'btn_updateconsult',
        'htmlOptions'=> array('onclick' => 'disableDoneRegister()'),
    )
);
?>
</div>

<?php

$this->renderPartial('consultation', array(
    'pid'             => $personInfo->pid,
    'model'           => $model,
    'consultId'       => $consultId,
    'departmentList'  => $departmentlist,
    'selected_dept'   => $selected_dept,
    'selected_doctor' => $selected_doctor    
));


?>