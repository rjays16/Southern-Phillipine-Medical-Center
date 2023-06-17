<?php
/* @var $this ProgressController */
/* @var $model PdpuProgressNotes */

$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;
$cs->RegisterCss('progress-notes-css', <<<CSS
                body ul.breadcrumb {
                    margin-top: -48px;
                }
CSS
);

$js = <<<JAVASCRIPT
    function auditTrail(noteId) {
        jQueryDialogSearch = jQuery('#search-dialog-progress')
            .dialog({
                modal: true,
                title: 'Audit Trail',
                width: '80%',
                height: 500,
                position: 'center',
                open: function() {
                    jQuery('#progress-dialog-frame').attr('src','index.php?r=socialService/progress/audit_trail&id='+noteId);
                    jQuery('.ui-dialog .ui-dialog-content').css({
                            overflow : 'hidden'
                        });
                }
            })
    }

    // $("document").ready(function(){

    // $("#PdpuProgressNotes_fullname").keyup(function(){
       
    // });
    // }

JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);


$this->breadcrumbs = array(
    'Social Service' => $baseUrl . '/modules/social_service/social_service_main.php',
    'Progress Notes'
);
$this->pageTitle = '';
?>

    <h3 align="center">Progress Notes</h3>
    <hr>

    <div id="search-dialog-progress" style="display: none;">
        <iframe id="progress-dialog-frame" src="" style="height:100%;width:100%;border:none;">
        </iframe>
    </div>

<?php
$this->beginWidget('application.widgets.SegBox', array(
    'title' => 'List of Progress Notes',
    'headerIcon' => 'fa fa-files-o',
    'headerButtons' => array(
        array(
            'class' => 'bootstrap.widgets.TbButton',
            'label' => 'New Progress Notes',
            'type' => 'success',
            'icon' => 'fa fa-file-o',
            'url' => 'index.php?r=socialService/progress/create'

        ),
    ),

));


$this->widget('bootstrap.widgets.TbGridView', array(
    'dataProvider' => $model->search(),
    'filter' => $model,
    'type' => 'bordered',
    'columns' => array(
        array(
            'name' => 'progress_date_time',
            'header' => 'Date Created',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center;',
                'id' => 'progress_date_time'
            ),
            'value' => function($data){
                return date('Y-m-d h:i A', strtotime($data['progress_date_time']));
            }
        ),
        array(
            'name' => 'pid',
            'header' => 'HRN',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; width: 150px;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center;',
                'id'    => 'pid'
            )
        ),
        array(
            'name' => 'encounter_nr',
            'header' => 'Encounter #',
            'headerHtmlOptions' => array(
                'style' => 'text-align: center; width: 200px;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center;'
            )
        ),
        array(
            'name' => 'fullname',
            'header' => 'Name of Patient',
            'headerHtmlOptions' => array(
                'style' => 'text-align:center;'
            ),
            'htmlOptions' => array(
                'style' => 'text-align: center;'
         
            )
            // 'value' => 'hi'
        ),
        array(
            'class' => 'pdpu.widgets.CustomButton',
            'header' => 'Actions',
            'template' => '{view}{audit_trail}',
            'buttons' => array(
                'view' => array(
                    'icon' => 'fa fa-pencil',
                    'label' => 'Edit',
                    'options' => array(
                        'class' => 'btn btn-small',
                        'style' => 'margin-right: 5px;'
                    )
                ),
                'audit_trail' => array(
                    'icon' => 'fa fa-search',
                    'label' => 'Audit Trail',
                    'options' => array(
                        'class' => 'btn btn-small',
                        'onclick' => "auditTrail($data->notes_id)",
                        'id' => '$data->notes_id',
                        'enc' => '$data->encounter_nr',
                        'function' => 'auditTrail',
                        'style' => 'margin-right: 5px;'

                    )
                )
            ),
            'headerHtmlOptions' => array(
                    'style' => 'text-align: center;'
            ),
            'htmlOptions' => array(
                    'style' => 'text-align: center; width: 110px;'
            )
        ),

    )
));

$this->endWidget();
?>