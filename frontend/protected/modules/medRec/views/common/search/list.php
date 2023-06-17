<?php
require_once($root_path . 'include/care_api_classes/class_acl.php');
$objAcl = new Acl($_SESSION['sess_login_userid']);
// $_a_1_opdonlinerequest = $objAcl->checkPermissionRaw('_a_1_opdonlinerequest');
$_a_2_opdonlinecreateconsult = $objAcl->checkPermissionRaw('_a_2_opdonlinecreateconsult');
$_a_2_opdonlineregister = $objAcl->checkPermissionRaw('_a_2_opdonlineregister');
// if(($_a_1_opdonlinerequest && !($_a_2_opdonlinecreateconsult)  ||  $_a_2_opdonlineregister)){
if ( $_a_2_opdonlinecreateconsult || $_a_2_opdonlineregister ) {
    $access_create = true;
}else{
     $access_create = false;
}

$restrict_create = ($service->displayPerson()->getTotalItemCount() == 1);

$cs = Yii::app()->clientScript;

Yii::app()->getClientScript()->registerScript('searchEncounter', <<<JAVASCRIPT


$("#searchpatientform").submit(function (event) {
	event.preventDefault();

	$.fn.yiiGridView.update("person-list-grid", {
		data: {'searchName': $("#person_search").val(), 'search': 1 },
	});
});

$( "#new-patient-btn" ).click(function() {
    var consult_id = $('#consult_id').val();
    var urls = $(this).data('param-url');
    var loc = window.location;
    var baseUrl = loc.protocol + "//"+ loc.host + "/" + loc.pathname.split('/')[1]+'/index.php?r=medRec/online';


        $.ajax({
                    url:baseUrl+"/IsHRN",
                data:{
                    consult_id : consult_id    
                },
                success: function(data){
              var obj = JSON.parse(data);
              if(obj.pid!=''){
                  Alerts.warn({
                                    title: 'Patient has HRN already.',
                                    icon: 'fa fa-times-circle-o',
                                    iconColor: '#EC1F13',
                                    callback: function (result) {
                                        Alerts.close();
                                    }
                                });
              }else{
                window.location.href=urls+"&id="+consult_id;
              }
                }
            });

   
   
});

$("#search-patient-modal").on("hidden", function () {
    let consultid = $('#consult_id').val();
    var loc = window.location;
    $.ajax({            
        url : '/' + loc.pathname.split('/')[1]+'/index.php?r=medRec/consultation/signalDoneConsultRegister',
        type: 'POST',
        data: {consultId: consultid},
        dataType : 'json',
        async : false,
        success : function(response) {
            console.log('Registration of consult request done!');
        }
    });
});

JAVASCRIPT
    , CClientScript::POS_READY);

echo CHtml::tag('div');

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id'          => 'search-patient-modal',
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


<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h5 id="search-patient-header">Search Patient</h5>
</div>

<div class="modal-body" style="max-height:80%;">
    <div class="gg" style="margin-left: 20px;">
        <div class="row-fluid">
            <?php
            $model2 = new FreeFormModel();

            $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                'id'                   => 'searchpatientform',
                'type'                 => 'horizontal',
                'enableAjaxValidation' => false,
                'htmlOptions'          => array(
                    'class' => 'searchEncounter',
                ),
            ));

            ?>

            <div class="row-fluid">
                <div class="span8">

                    <?php
                    echo $form->textFieldGroup($model2, '', array(
                        'widgetOptions'      => array(
                            'id' => 'person_search',
                        ),
                        'placeholder'        => 'Search last name, first name',
                        'append'             => ' <i class="fa fa-search"></i>',
                        'wrapperHtmlOptions' => array(
                            'class' => 'col-md-7'
                        ),
                        'labelOptions'       => array(
                            'label' => '', 'class' => 'hidden',
                        ),
                        'htmlOptions'        => array(
                            'id' => 'person_search'
                        )
                    ));

                    $this->endWidget();
                    ?>
                </div>
                <div class="span4">
                    <?php
                 
                    if($access_create && !$restrict_create){
                          $this->widget('bootstrap.widgets.TbButton', array(
                                        'id'    => 'new-patient-btn',
                                        'label' => 'Create New Patient',
                                        'type'  => 'primary',
                                        'htmlOptions' => array(
                                            'class' => 'pull-right',
                                            'data-param-url' => Yii::app()->createUrl("medRec/online/createPatient")
                                        )
                            ));
                    }else{
                           $this->widget('bootstrap.widgets.TbButton', array(
                                        'id'    => 'new-patient-btns',
                                        'label' => 'Create New Patient',
                                        'type'  => 'primary',
                                        'htmlOptions' => array(
                                            'class' => 'pull-right',
                                            'disabled'=>'disabled',
                                            'title'=>"No Access Permission"
                                        )
                            ));

                    
                    }
                  
                    ?>
                </div>
            </div>

            <?php

            $this->widget('bootstrap.widgets.TbGridView', array(
                    'id'            => 'person-list-grid',
                    'enableSorting' => false,
                    'dataProvider'  => $service->displayPerson(),
                    'type'          => 'bordered',
                    'columns'       => array(
                        array(
                            'header' => 'HRN',
                            'name'   => 'pid',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            )
                        ),
                        array(
                            'header' => 'Last Name',
                            'name'   => 'name_last',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            )
                        ),
                        array(
                            'header' => 'First Name',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            ),
                            'value'  => function ($data) {
                                $name_first = $data['name_first'];
                                
                                if($data['suffix'])
                                    $name_first = str_replace(' '.$data['suffix'], ', '.$data['suffix'], $data['name_first']);

                                return $name_first;
                            }
                        ),
                        array(
                            'header' => 'Middle Name',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            ),
                            'name'   => 'name_middle'
                        ),
                        array(
                            'header'      => 'Date of Birth',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            ),
                            'htmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;',
                                'id'    => 'date_birth'
                            ),
                            'value'       => function ($data) {
                                return $data['date_birth'];
                            }
                        ),
                          array(
                            'header'      => 'PHS/Dependent',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            ),
                            'htmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;',
                                'id'    => 'date_birth'
                            ),
                            'value'       => function ($data) {
                                return CarePerson::isPHS($data['pid']);
                            }
                        ),
                        array(
                            'class'             => 'bootstrap.widgets.MyButtonColumn',
                            'header'            => 'Actions',
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center; vertical-align: middle;'
                            ),
                            'template'          => '{viewHistory}',
                            'buttons'           => array(
                                'viewHistory' => array(
                                    'icon'    => 'fa fa-mail-forward',
                                    'label'   => 'View Details',
                                    'options' => array(
                                        'class'                => 'btn btn-small skusta-clee',
                                        'style'                => 'margin-right: 5px;',
                                        'data-param-id'        => '$data->pid',
                                        'data-param-url'       => Yii::app()->createUrl("medRec/online/view_history")
                                    ),
                                )
                            ),
                            'headerHtmlOptions' => array(
                                'style' => 'text-align: center;'
                            ),
                            'htmlOptions'       => array(
                                'style' => 'text-align: center; width: 110px;'
                            )
                        )
                    )
                )
            );

            ?>

        </div>
    </div>
</div>

<?php
$this->endWidget();

echo CHtml::closeTag('div');
?>


<script>
    $(".skusta-clee").live("click", function (e) {
        e.preventDefault();
        var x = document.getElementById("consult_id").value;
        console.log(x);
        var pid = $(this).data('param-id');
        var url = $(this).data('param-url') + "&pid=" + pid + "&id=" + x;
        window.location.href = url;
    });
</script>



