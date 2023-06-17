<?php
/* @var $this MedicalChartFollowUpController */
/* @var $dataProvider CActiveDataProvider */
/* @var $model MedicalChartFollowUp */

// $this->breadcrumbs=array(
// 	'Medical Chart Follow Ups',
// );

// $this->menu=array(
// 	array('label'=>'Create MedicalChartFollowUp', 'url'=>array('create')),
// 	array('label'=>'Manage MedicalChartFollowUp', 'url'=>array('admin')),
// );
$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl; 

$cs = Yii::app()->clientScript;
$css = <<<CSS
    body { padding-top: 0; }
CSS;

$headJs = <<<JS
function printMedChart(pid, encounter_nr){
    // alert(pid); 
    var url = "modules/industrial_clinic/reports/seg-ic-medchart-follow-up-form-pdf.php?pid="+pid+"&enc="+encounter_nr;
    // alert(url);
    window.open(url, "Medical Chart","modal, width=600,height=500,menubar=no,resizable=yes,scrollbars=no");
}
JS;

$cs->registerCss('css',$css);
$cs->registerScript('headJs',$headJs,CClientScript::POS_HEAD);
//var_dump($cs);
$enc = $_GET['enc'];
if ($model->encounter_nr == null) {
    echo '<em class="warn">Sorry but the page cannot be displayed! <br> Invalid Case Number!</em>';
    exit();
}
?>
<h2 align="center" class="span12">MEDICAL EXAMINATION CHART(Follow Up Form)</h2>
<?php
// added by Kenneth 04-06-2016
$age="";
if($model->person->date_birth=="0000-00-00"){
    $age=$model->person->age." year/s old";
}
else{
    $age=$model->person->getAge();
}
// end Kenneth
echo CHtml::tag('div', array('class' => 'span6'));
    $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'General Records'
        )
    );
    $this->widget('bootstrap.widgets.TbDetailView', array(
        'data' => array(
            'pid' => $model->person->getPID(),
            'fullName' => $model->person->getFullName(),
            'age' => $age, // added by Kenneth 04-06-2016
            'sex' => $model->person->getSex(),
            'full_address' => $model->person->getFullAddress(),
            'occupation_name' => $model->person->getOccupation(),
        )
    ));

    $this->endWidget();//TbBox
echo CHtml::closeTag('div');
// var_dump($model->person); die();    

echo CHtml::tag('div', array('class' => 'span6'));
    $box = $this->beginWidget(
        'bootstrap.widgets.TbBox',
        array(
            'title' => 'New Follow Up'
        )
    );
        /* @var $form TbActiveForm */
        $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
            'id' => 'medical-follow-up-form',
            'enableAjaxValidation' => true,
            'type' => 'horizontal'
        ));

        echo $form->hiddenField($model,'pid',array('value'=>$model->person->pid));
        echo $form->datePickerRow($model,'date_request', array('options' => array('autoclose' => true),'htmlOptions' => array('value' => date('m/d/Y'))));
        echo $form->textAreaRow($model,'vshtwt');
        echo $form->textAreaRow($model,'hxpe');
        echo $form->textAreaRow($model,'remarks');

        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'submit',
            'label' => 'Submit',
            'type' => 'success'
        ));

        $this->endWidget();//TbActiveForm
    $this->endWidget();//TbBox
echo CHtml::closeTag('div');

$this->beginWidget(
    'bootstrap.widgets.TbBox',
    array(
        'title' => 'List of Entry'
    )
);


$this->widget('bootstrap.widgets.TbGridView', array(
	'dataProvider'=>$dataProvider,
	'columns' => array(
		array(
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'name' => 'date_request',
            'editable' => array(
                'options' => array(
                    'mode' => 'popup',
                    'inputclass' => 'span1',
                    'type' => 'date',
                    'placement' => 'right',
                    'url' => Yii::app()->createUrl('industrialClinic/medicalChartFollowUp/updateDateRequest'),
                )
            ),
            'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all;')
        ),
        array(
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'name' => 'vshtwt',
            'editable' => array(
                'options' => array(
                    'mode' => 'popup',
                    'inputclass' => 'span3',
                    'type' => 'textarea',
                    'placement' => 'top',
                    'url' => Yii::app()->createUrl('industrialClinic/medicalChartFollowUp/updateVitalSign'),
                    'rows' => 5
                )
            ),
            'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all;')
        ),
        array(
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'name' => 'hxpe',
            'editable' => array(
                'options' => array(
                    'mode' => 'popup',
                    'inputclass' => 'span3',
                    'type' => 'textarea',
                    'placement' => 'top',
                    'url' => Yii::app()->createUrl('industrialClinic/medicalChartFollowUp/updateHxpe'),
                    'rows' => 5
                )
            ),
            'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all;')
        ),
        array(
            'class' => 'bootstrap.widgets.TbEditableColumn',
            'name' => 'remarks',
            'editable' => array(
                'options' => array(
                    'mode' => 'popup',
                    'inputclass' => 'span3',
                    'type' => 'textarea',
                    'placement' => 'left',
                    'url' => Yii::app()->createUrl('industrialClinic/medicalChartFollowUp/updateRemarks'),
                    'rows' => 5
                )
            ),
            'htmlOptions' => array('style' => 'max-width:100px; overflow-x: hidden; word-break: break-all;')
        ),
        array(
            'class' => 'bootstrap.widgets.TbButtonColumn',
            'deleteConfirmation' => 'Do you want to delete this data?',
            'template' => '{delete}',
            'buttons'=>array(
                'delete' => array(
                    'visible' => 'true',
                    'type' => ''
                )
            ),
        ),
	)
)); 

$this->widget('bootstrap.widgets.TbButton',array(
    'buttonType' => 'button',
    'type' => 'info',
    'icon' => 'fa fa-print',
    'label' => 'Print',
    'htmlOptions' => array(
        'onclick' => 'printMedChart('.$model->person->pid.', '.$model->encounter_nr.');'
    )

));

$this->endWidget();//TbBox

?>
