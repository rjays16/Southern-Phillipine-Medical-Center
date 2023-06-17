<?php
/* @var $this Controller */
/* @var $form TbActiveForm */
/* @var $model \SegHis\modules\article\models\Article|CModel */
/* @var $cs CClientScript */

$this->breadcrumbs = array(
    'List' => $this->createUrl('admin/list'),
    $this->pageSubTitle
);

$cs = Yii::app()->clientScript;
$cs->registerCss('headCss', "body{padding-top:0}");

//haha libog ang docs sa ckeditor -_-
$js = <<<JS
$(function(){

Alerts.loading({
    content: 'Loading editor...'
});

    setTimeout(function(){
        $('#cke_12').hide();
        $('#cke_19').hide();
        $('#cke_27').hide();
        $('#cke_32').hide();
        $('#cke_63').hide();
        //$('#cke_67').hide();

        $('#cke_68').hide();
        $('#cke_69').hide();
        $('#cke_74').hide();
        $('#cke_75').hide();

        $('#cke_80').hide();
        $('#cke_83').hide();
        Alerts.close();
    }, 1000);
});
JS;

$cs->registerScript('onLoad', $js, CClientScript::POS_END);

$baseUrl = Yii::app()->baseUrl;

$cs->registerScriptFile($baseUrl . '/modules/news/js/article.js', CClientScript::POS_END);

$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'type' => 'vertical',
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data'
    )
));

?>

    <div class="clearfix">
        <div class="span5">
            <?php

            echo $form->errorSummary($model);

            echo $form->dropDownListRow($model, 'art_num', array(
                1 => "One",
                2 => "Two",
                3 => "Three"
            ), array(
                'placeholder' => false,
                'class' => 'input-block-level'
            ));

            echo $form->textFieldRow($model, 'title', array(
                'class' => 'input-block-level'
            ));

            echo $form->textAreaRow($model, 'preface', array(
                'class' => 'input-block-level'
            ));

            echo $form->textFieldRow($model, 'author', array(
                'class' => 'input-block-level'
            ));

            echo $form->datePickerRow($model, 'publish_date', array('options' => array(
                'autoclose' => true
            )));


            ?>
        </div>
        <div class="span6">
            <?php

            $fileName = null;
            $DocfileName = null;

            if (!$model->getIsNewRecord()) {
                $fileName = $model->getPictureFileFullPath();
            }
            if (!$model->getIsNewRecord()) {  /*added By MARK 2016-09-11*/
                $DocfileName = $model->getDOcFileFullPath();
            }

            echo CHtml::image($fileName, 'No Image', array(
                'style' => 'width:160px;height:160px;',
                'id' => 'imagePreview'
            ));

            echo $form->fileFieldRow($model, 'pic_file', array(
                'id' => 'imageFileInput'
            ));
            /*added By MARK 2016-09-11*/
           echo CHtml::tag('br');
           echo CHtml::tag('br');
           echo CHtml::tag('br');
           echo "allow file type : jpg, gif, png, txt, pdf, doc, docx";
            echo $form->fileFieldRow($model, 'file_name', array(
                'id' => 'DocuMentFile'
            ));
            if ($DocfileName != null) {
                echo "Current File: ".CHtml::link($model->getDocFile(), $DocfileName, array('target' => '_blank'));;
                // echo CHtml::link($model->getDocFile(),array($DocfileName), array('target'=>'_blank')); 
                 // echo "<a href=".addslashes($DocfileName)."  target=\"_blank\">".$model->getDocFile()."</a>";
          
            }
              /*END added By MARK 2016-09-11*/
           
            ?>
        </div>
    </div>

<?php

echo $form->ckEditorRow($model, 'body');

$buttonLabel = 'Post';

if (!$model->getIsNewRecord()) {
    $buttonLabel = 'Update';
}

echo CHtml::tag('br');

$this->widget('bootstrap.widgets.TbButton', array(
    'buttonType' => 'submit',
    'type' => 'success',
    'size' => 'large',
    'icon' => 'fa fa-save',
    'label' => $buttonLabel,
    'block' => true,
));

$this->endWidget();// TbActiveForm