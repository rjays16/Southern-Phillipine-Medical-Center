<?php
/* @var $this DialysisTransactionController */
/* @var $cs CClientScript */
/* @var $model \SegHis\modules\dialysis\models\DialysisTransactionForm */
/* @var $form DialysisActiveForm */
/* @var $machines \SegHis\modules\dialysis\models\DialysisMachine */
/* @var $dialyzers \SegHis\modules\dialysis\models\DialysisMiscService */
/* @var $defaults [] */
/* TODO Refactor, too much logic in view */

$this->setPageTitle('Dialysis Transaction - ' . $model->transactionNr);
$cs = Yii::app()->clientScript;

$style = <<<STYLE
body{padding-top:0;}
select.readonly{border-color:#bdbdbd;background-color:#eeeeee;}
STYLE;

$modelName = CHtml::modelName($model);

$js = <<<JS
var idPrefix = '{$modelName}';
JS;

$cs->registerCss('headCss', $style);
$cs->registerScript('headJs', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/modules/dialysis/js/dialysis-transaction.js', CClientScript::POS_END);

$form = $this->beginWidget('dialysis.widgets.DialysisActiveForm', array(
    'id' => 'dialysis-transaction-form',
    'type' => 'horizontal'
));

echo $form->errorSummary($model);

$this->renderPartial('_detailView', array('model' => $model, 'form' => $form));

$this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'Dialyzer Information'
));

$transactionTypes = array(
    1 => 'Old Dialyzer',
    2 => 'New Dialyzer',
    3 => 'Reuse'
);

//should not be able to select re-use when there is no
//previous transaction/machine used
if (!$model->previousTransaction && !$model->hasPastDialysisEncounters()) {
    unset($transactionTypes[3]);
}

if ($model->previousTransaction/* || $model->hasPastDialysisEncounters()*/) {
    unset($transactionTypes[1]);
}

ob_start();

if (!$model->transaction) {
    echo $form->dropDownListRow($model, 'transactionType', $transactionTypes, array(
        'placeholder' => false
    ));
}

$isPhicOptions = array(
    0 => 'No',
    1 => 'Yes'
);

if (!$model->transaction) {
    echo $form->dropDownListRow($model, 'isPhilHealth', $isPhicOptions, array(
        'placeholder' => true
    ));
} else {
    echo $form->displayTextFieldRow($model, $isPhicOptions[$model->isPhilHealth], $model->isPhilHealth, 'isPhilHealth', array(
        'readonly' => true
    ));
}

/*added MARK transaction_date Dec 15, 2016*/
echo "<font  style='margin-left:34px;'>Admitted date & time<font style='color:red;'>*</font></font>";
$this->widget('CMaskedTextField',
                    array(
                         'mask'=> "99/99/9999 99:99 aa",
                         'model'=>$model,
                         'attribute'=>'transactionDateTime',
                            'htmlOptions' => array(
                                'style'=>'margin-left:175px;',
                           /* 'value' => date('m/d/Y h:i A'),*/
                             'placeholder'=>"m/d/Y HH:mm"
                        )));
echo "<br>";
echo "<br>";
echo "<font  style='margin-left:30px;'>Discharge date & time<font style='color:red;'>*</font></font>";
$this->widget('CMaskedTextField',
                    array(
                         'mask'=> "99/99/9999 99:99 aa",
                         'model'=>$model,
                         'attribute'=>'dateTimeOut',
                            'htmlOptions' => array(
                                'style'=>'margin-left:175px;',
                        /*    'value' => date('m/d/Y h:i A'),*/
                             'placeholder'=>"m/d/Y HH:mm"
                        )));

/* END added MARK transaction_date Dec 15, 2016*/

// * commented by MARK transaction_date Dec 15, 2016*/
// echo $form->dateTimePickerSlider($model, 'transactionDateTime', array('options' => array(
//     'changeMonth' => true,
//     'changeYear' => true,
//     'dateFormat' => 'mm/dd/yy',
//     'timeFormat' => 'hh:mm TT'
// ), 'htmlOptions' => array(
//     'value' => date('m/d/Y H:i A')
// )));
// echo $form->dateTimePickerSlider($model, 'dateTimeOut', array('options' => array(
//     'changeMonth' => true,
//     'changeYear' => true,
//     'dateFormat' => 'mm/dd/yy',
//     'timeFormat' => 'hh:mm TT'
// ), 'htmlOptions' => array(
//     'value' => date('m/d/Y H:i A')
// )));
// *END commented by MARK transaction_date Dec 15, 2016*/


$col1 = ob_get_clean();

ob_start();
if (!$model->transaction) {
    echo $form->dropDownListRow($model, 'machineNr', CHtml::listData($machines, 'id', 'machine_nr'), array('placeholder' => false));
} else {
    $list = CHtml::listData($machines, 'id', 'machine_nr');
    echo $form->displayTextFieldRow($model, $machines[$model->machineNr], $model->machineNr, 'machineNr');
}

$dialyzerList = CHtml::listData($dialyzers, 'service_code', 'name');
if (!$model->transaction) {
    echo $form->dropDownListRow($model, 'dialyzerId', $dialyzerList, array('placeholder' => false));
    echo $form->textFieldRow($model, 'numberOfReuse');
} else {
    echo $form->displayTextFieldRow($model, $dialyzerList[$model->dialyzerId], $model->dialyzerId, 'dialyzerId', array('readonly' => true));
    echo $form->displayTextFieldRow($model, $model->numberOfReuse, $model->numberOfReuse, 'numberOfReuse', array('readonly' => true));
}
$col2 = ob_get_clean();

echo CHtml::tag('div', array('class' => 'clearfix'));
echo CHtml::tag('div', array('class' => 'span6'), $col1, true);
echo CHtml::tag('div', array('class' => 'span6'), $col2, true);
echo CHtml::closetag('div');//clearfix

$this->widget('bootstrap.widgets.TbButton', array(
    'label' => 'Save',
    'buttonType' => 'submit',
    'type' => 'success',
    'size' => 'large',
    'icon' => 'fa fa-save'
));

//if the current transaction is still not saved and has a previous transaction
//and reuse is lesss than 8 (max reuse = 8)
$dialyzerNameOptions = array();

if (!$model->transaction && $model->lastTransaction && $model->lastTransaction->dialyzer_reuse < 8) {
    // new transaction && has last transaction && last transaction reuse < 8
    $model->numberOfReuse = $model->lastTransaction->dialyzer_reuse + 1;
    $dialyzerNameOptions = array('value' => $model->lastTransaction->dialyzer->dialyzerInfo->name);
}/* else if (!$model->transaction && $model->previousTransaction && $model->previousTransaction->dialyzer_reuse < 8) {
    // new transaction && has previous transaction && previous transaction reuse < 8
    $model->numberOfReuse = $model->previousTransaction->dialyzer_reuse + 1;//reuse
    $dialyzerNameOptions = array('value' => $model->previousTransaction->dialyzer->dialyzerInfo->name);
} */else if ($model->transaction) { // update mode
    $dialyzerNameOptions = array('value' => $dialyzerList[$model->dialyzerId]);
} else
    $model->numberOfReuse = 0;//no transaction save, default to 0


echo $form->hiddenField($model, 'dialyzerName', $dialyzerNameOptions);
echo CHtml::hiddenField('hasPreviousTransaction', $model->previousTransaction == true);

echo CHtml::hiddenField('orig-reuse', $model->numberOfReuse);
echo CHtml::hiddenField('orig-machine-nr', $defaults['machineNr']);
echo CHtml::hiddenField('orig-dialyzer-id', $defaults['dialyzerId']);
echo CHtml::hiddenField('orig-dialyzer-name', $model->previousTransaction->dialyzer->dialyzerInfo->name);
echo CHtml::hiddenField('orig-dialyzer-serial', $model->previousTransaction->dialyzer->dialyzer_serial_nr);

$this->endWidget();//TbBox
$this->endWidget();//TbActiveForm