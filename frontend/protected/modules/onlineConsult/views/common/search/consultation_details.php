<?php
require_once($root_path . 'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_login_userid']);
$_a_2_opdonlinerequest = $objAcl->checkPermissionRaw('_a_2_opdonlinerequest');
//$_a_2_opdonlinecreateconsult = $objAcl->checkPermissionRaw('_a_2_opdonlinecreateconsult');
//$_a_2_opdonlineregister = $objAcl->checkPermissionRaw('_a_2_opdonlineregister');
//if(($_a_1_opdonlinerequest && !($_a_2_opdonlinecreateconsult)  ||  $_a_2_opdonlineregister)){
if ($_a_2_opdonlinerequest) {
    $access_create = true;
}else{
    $access_create = false;
}

$cs = Yii::app()->clientScript;

Yii::app()->getClientScript()->registerScript('searchEncounter', <<<JAVASCRIPT


$("#department").live("change", function(e){
    let urls = $(this).data('param-url');
    let loc = window.location;
    let baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online';
    let dept_nr = $(this).val();

        $.ajax({
                url:baseUrl+"/DoctorList",
                data:{
                    dept_nr : dept_nr,    
                },
                success: function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === true){
                        $("#consult_dr_nr option").remove();
                        var select = $("#consult_dr_nr");
                        select.append(
                            $('<option>', {
                                value: '',
                                text: '-Select Doctor-'
                            })
                        );
                        
                        obj.results.forEach(function(value,key){
                            var name = value.name_last+", "+value.name_first;
                            var convertedName = name.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                                        return letter.toUpperCase();
                                    });
                            select.append(
                                $('<option>', {
                                    value: value.personell_nr,
                                    text: convertedName,
                                })
                            );
                            
                        });
                        
                       
                    }
                }
                
            });
});

$(".btn-submit").live("click", function(e){
    let status = $(this).data('status');
    let msg = (status == "TRIAGED") ? "Confirm Refer to MedRec?" : "Cancel Consultation Request?";
    if(confirm(msg)){
        if (checkFields() === false){
            alert('Please fill out all fields.');
            return;
        }
        let urls = $(this).data('param-url');
        let loc = window.location;
        let baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/online';
        let consult_id = $("#consult_id").val();
        let consult_dr_nr = $("#consult_dr_nr").val();
        let dept_nr = $("#department").val();
        
        $.ajax({
            url:baseUrl+"/UpdateConsultationStatus",
            data:{
                consult_id : consult_id,
                dept_nr : dept_nr,    
                consult_dr_nr : consult_dr_nr,
                status : status
            },
            success: function(data){
                var obj = JSON.parse(data);
                if(obj.status === true) {
                    // Send notification that triaging of consultation request is done.
                    let loc = window.location;
                    $.ajax({            
                        url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/signalDoneConsultRequest',
                        type: 'POST',
                        data: {consultId: consult_id},
                        dataType : 'json',
                        success : function(response) {
                            if (status == "TRIAGED") {
                                // Send notification to MedRec that a triaged consultation request is ready for serving ...
                                $.ajax({            
                                    url : '/' + loc.pathname.split('/')[1]+'/index.php?r=onlineConsult/consultation/notifyConsultMedRec',
                                    type: 'POST',
                                    data: {consultId: consult_id},
                                    dataType : 'json',
                                    success : function(response) {
                                        console.log('Medical Records notified!');
                                    }
                                });
                            }
                        }
                    });

                    if (status == "TRIAGED")
                        var msg = "Patient's consultation request is referred to Medical Records!";
                    else
                        var msg = "Patient's consultation request is cancelled!";
                    Alerts.warn({
                        title: msg,
                        icon: 'fa fa-times-circle-o',
                        iconColor: '#EC1F13',
                        callback: function (result) {
                            Alerts.close();
                            $("#view-consultation-modal").modal("hide");
                            $('#online-grid').yiiGridView('update');
                        }
                    });
                }                           
            }
            
        });
    }
        
});

var checkFields = function(){
    let consult_dr_nr = $("#consult_dr_nr").val();
    let dept_nr = $("#department").val();
//    if (consult_dr_nr && dept_nr)
    if (dept_nr)
        return true;
    else
        return false;
}

JAVASCRIPT
    , CClientScript::POS_READY);

echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id'          => 'view-consultation-modal',
        'fade'        => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            'style'         => 'height:80%;width:55%',
        )
    )
);

Yii::import('bootstrap.widgets.ButtonColumn');
Yii::import('bootstrap.widgets.MyButtonColumn');


?>

<style>
    .form-input {
        border:0 !important;border-bottom:1px solid black !important;
    }
</style>

<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
</div>

<div class="modal-body" style="max-height:80%;">

        <div class="row-fluid">

            <table>
                <tr>
                    <td>
                    <?php
                        echo \CHtml::hiddenField('consult_id', '');
                        echo Chtml::label("Patient's Name:","patient_name");
                    ?>
                    </td>
                    <td>
                    <?php
                        echo CHtml::telField("patient_name","",array("id"=>"patient-name","readonly"=>"readonly","class"=>"form-input"));
                    ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("Address:","address");
                        ?>
                    </td>
                    <td>
                        <?php
                        echo CHtml::telField("address","",array("id"=>"address","readonly"=>"readonly","class"=>"form-input"));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("Contact No:","contact_no");
                        ?>
                    </td>
                    <td>
                        <?php
                        echo CHtml::telField("contact_no","",array("id"=>"contact-no","readonly"=>"readonly","class"=>"form-input"));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("Date of Birth:","birth_date");
                        ?>
                    </td>
                    <td>
                        <?php
                        echo CHtml::telField("birth_date","",array("id"=>"birth-date","readonly"=>"readonly","class"=>"form-input"));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("FB Messenger ID:","fb_id");
                        ?>
                    </td>
                    <td>
                        <?php
                        echo CHtml::link("", 'https://www.messenger.com/t/', array("id"=>"fb-id", "target"=>"_blank", "name"=>"fb_id"));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("Complaint:","complaint");
                        ?>
                    </td>
                    <td>
                        <?php
                        echo CHtml::telField("complaint","",array("id"=>"complaint","readonly"=>"readonly","class"=>"form-input"));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("*Department:","department");
                        ?>
                    </td>
                    <td>
                        <?php

                        echo CHtml::dropDownList('dept_nr', '', $modelDepartment, array(
                            'prompt' => '-Select Department-',
                            'id'     => 'department'
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        echo Chtml::label("*Doctor:","consult_dr_nr");
                        ?>
                    </td>
                    <td>
                        <?php

                        echo CHtml::dropDownList('consult_dr_nr', '', '', array(
                            'prompt' => '-Select Doctor-',
                            'id'     => 'consult_dr_nr'
                        ));
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                        <?php
                            echo CHtml::button("Refer to MedRec",array("class"=>"btn btn-primary btn-submit","data-status"=>"TRIAGED"));
                        ?>
                    </td>
                    <td>
                        <?php
                            echo CHtml::button("Cancel Consultation",array("class"=>"btn btn-success btn-submit","data-status"=>"CANCELLED"));
                        ?>
                    </td>
                </tr>
            </table>
        </div>

</div>

<?php
$this->endWidget();

echo CHtml::closeTag('div');
?>


<script>

</script>



