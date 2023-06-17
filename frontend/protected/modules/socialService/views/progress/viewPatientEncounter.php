<?php
/* @var $this ProgressController */
/* @var $model PdpuProgressNotes */
$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;
$cs->registerCss('headCss',<<<CSS
         body{
            padding-top: 0;
         }
CSS
);

$js = <<<JAVASCRIPT
    function search() {
        $('#people-grid-view').yiiGridView('update', {
            data: $('#form-search').serialize()
        });
    }
    
    $("document").ready(function() {
        $(".view").click(function(e) {
             
             e.preventDefault();
             var _this = $(this);
             var id=$(this).closest('tr').children('td:first').text();
             $.getJSON('{$baseUrl}/index.php?r=socialService/progress/caseInformation/id/'+id,
            {},
            function(response){
                window.parent.loadPerson(response);
                
            });
        });
    });
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);


$this->setPageTitle('');

$this->widget('bootstrap.widgets.TbGridView', array(
        'id' => 'people-grid-view',
        'enableSorting' => false,
        'dataProvider' => $model->search(),
        'columns' => array(
            array(
                'header' => 'notes_id',
                'name' => 'notes_id',
                'headerHtmlOptions' => array('style' => 'display:none'),

                'htmlOptions' => array('style' => 'display:none'),
            ),
            array(
                'header' => 'Date Created',
                'name' => 'create_dt'
            ),
            array(
                'header' => 'Informant',
                'name' => 'informant'
            ),
            array(
                'header' => 'Purpose/ Reasons/ Objectives',
                'name' => 'purpose_reasons'
            ),
            array(
                'header' => 'Action Taken',
                'name' => 'action_taken'
            ),
            array(
                'header' => 'Result/ Problem Encountered Date',
                'name' => 'problem_encountered'
            ),
            array(
                'header' => 'Plan',
                'name' => 'plan'
            ),
            array(
                'header' => 'Created By',
                'name' => 'create_id'
            ),
            array(
                'class' => 'pdpu.widgets.CustomButton',
                'header' => 'Actions',
                'template' => '{view}',
                'buttons' => array(
                    'view' => array(
                        'maderpaker' => 'test',
                        'icon' => 'fa fa-pencil',
                        array(
                            'class' => 'btn btn-small'
                        )
                    )
                )
            )
        )
));