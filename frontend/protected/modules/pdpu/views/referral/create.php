<?php
/* @var $this ReferralController */

$baseUrl = Yii::app()->request->baseUrl;
$cs = Yii::app()->clientScript;
$cs->registerCss('sservice-added-css',<<<CSS
				body ul.breadcrumb{
                    margin-top: -48px;
                }
                body div#padding{
                    padding:10px;
                }

                table tbody tr td, table thead tr th{
                    font-size: 12px;
                }

                #search-person{
                    position:absolute;
                    margin-left:410px;
                    margin-top:-46px;
                }
CSS
);

$js = <<<JAVASCRIPT

    function searchPerson() {
        jQueryDialogSearch = jQuery('#search-dialog')
                .dialog({
                    modal: true,
                    title: 'Select a Person',
                    width: '80%',
                    height: 500,
                    position: 'center',
                    open: function(){
                        jQuery('#search-dialog-frame').attr('src','index.php?r=person/search');
                        jQuery('.ui-dialog .ui-dialog-content').css({
                            overflow : 'hidden'
                        });
                    }
                });

        return false;
    }

    function loadPerson(response){
        var d = new Date();

        var month = d.getMonth()+1;
        var day = d.getDate();

        var output = d.getFullYear() + "-" +
            (month<10 ? "0" : "") + month + "-" +
            (day<10 ? "0" : "") + day;

        $("#SocialReferrals_refer_dt").val(output);
        $("#SocialReferrals_pid").val(response.pid);
        $("#SocialReferrals_name").val(response.ordername);
        $("#SocialReferrals_encounter_nr").val(response.encounter_nr);

        jQuery('#search-dialog').dialog('close');
        $('#showAssessmentBtn').show();


        enc_nr = $('#SocialReferrals_encounter_nr').val();

        if(enc_nr != '')
        {
            $.getJSON('{$baseUrl}/index.php?r=pdpu/referral/checkMss/enc/'+enc_nr, function(response){
                if(response.length > 0)
                {
                    $('#showAssessmentBtn').attr('disabled', false);
                }
                else
                {
                    $('#showAssessmentBtn').attr('disabled', true);
                }
            });
        }
        else
        {
           $('#showAssessmentBtn').attr('disabled', true); 
        }
    }

    function showAssessment(){
        enc_nr = $('#SocialReferrals_encounter_nr').val();
        window.open("modules/reports/reports/PDPU_Assessment_Tool.php?enc_nr="+enc_nr);
    }
JAVASCRIPT;

$cs->registerScript('js', $js, CClientScript::POS_HEAD);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/themes/seg-ui/jquery.ui.all.css', CClientScript::POS_END);
$cs->registerScriptFile(Yii::app()->baseUrl . '/js/jquery/ui/jquery-ui-1.9.1.js', CClientScript::POS_END);


$this->breadcrumbs=array(
    'PDPU' => $baseUrl . '/modules/pdpu/pdpu_main.php',
    'Assessment and Referral Form' => 'index.php?r=pdpu/referral',
    'New Referral',
);
$this->pageTitle = 'PDPU - Assessment and Referral Form';
?>

<h3 align="center">Assessment and Referral Form</h3>
<hr/>

<button id="showAssessmentBtn" class="btn btn-success" style="float: right; display: none" onclick="showAssessment()">View Assessment Form</button>

<?php
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm',
    array(
        'id' => 'horizonralForm',
        'type' => 'horizontal',
    ));

$form->errorSummary($model);
?>
<fieldset>

    <legend>New Referral</legend>

    <?php echo $form->datePickerRow($model, 'refer_dt',
        array(
            'options' => array(
                'clearBtn' => true,
                'todayHighlight' => true,
                'autoclose' => true,
                'format' => 'yyyy-mm-dd'
            )
        ),
        array(
            'prepend' => '<i class="fa fa-calendar"></i>'
        )); ?>
    <?php echo $form->textFieldRow($model, 'pid',
        array(
            'readonly' => 'readonly'
        ));
     ?>
     <div id="search-person"><img id="select-enc" src="<?php $baseUrl ?>images/btn_encounter_small.gif" border="0" style="cursor:pointer" onclick="searchPerson()" /></div>
    <?php echo $form->hiddenField($model, 'encounter_nr',
        array(
            'readonly' => 'readonly',
            'type' => 'hidden'
        ));
    ?>
    <!-- <div class="control-group">
        <label class="control-label required" for="SocialReferrals_name">Name</label>
        <div class="controls">
            <?php echo CHtml::textField('SocialReferrals_name', '', array(
                'readonly' => 'readonly',
                'style' => 'width: 300px;',
                'required' => 'required',
            )); ?>
        </div>
    </div> -->
    <?php echo $form->textFieldRow($model, 'name',
        array(
            'readonly' => 'readonly',
            'style' => 'width: 300px'
        ));
    ?>
    <?php echo $form->textAreaRow($model, 'refer_to',
        array(
            'class' => 'span4'
        ));?>
    <?php echo $form->textAreaRow($model, 'refer_diagnosis',
        array(
            'class' => 'span4'
        ));?>
    <?php echo $form->textAreaRow($model, 'refer_reason',
        array(
            'class' => 'span4'
        ));?>
    <?php echo $form->textAreaRow($model, 'refer_assessment',
        array(
            'class' => 'span4'
        ));?>
    <?php echo $form->textAreaRow($model, 'refer_intervention',
        array(
            'class' => 'span4'
        ));?>

</fieldset>

    <div class="form-actions">
        <?php $this->widget(
            'bootstrap.widgets.TbButton',
            array(
                'buttonType' => 'submit',
                'type' => 'primary',
                'label' => 'Submit'
            )
        ); ?>
    </div>

    <div id="search-dialog" style="display: none;">
        <iframe id="search-dialog-frame" src="" style="height:100%;width:100%;border:none;">
        </iframe>
    </div>

<?php
$this->endWidget();