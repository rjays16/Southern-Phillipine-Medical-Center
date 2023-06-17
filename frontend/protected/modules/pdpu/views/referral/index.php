<?php
/* @var $this ReferralController */
/* @var $model SocialReferrals */

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
CSS
	);

$js = <<<JAVASCRIPT

function printReferral(id, enc){
	window.open("modules/reports/reports/PDPU_Assessment_Tool.php?id="+id+"&enc_nr="+enc);
}

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
/*added By MArk 2016-07-16-*/
function auditTrail(refId) {
        jQueryDialogSearch = jQuery('#search-dialog-audit')
                .dialog({
                    modal: true,
                    title: 'Audit Trail',
                    width: '80%',
                    height: 500,
                    position: 'center',
                    open: function(){
                        jQuery('#audit-dialog-frame').attr('src','index.php?r=pdpu/referral/audit_trail&id='+refId);
                        jQuery('.ui-dialog .ui-dialog-content').css({
                            overflow : 'hidden'
                        });
                    }
                });

        return false;
    }
/*END By MArk 2016-07-16-*/

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
	'Assessment and Referral Form'
);
$this->pageTitle = 'PDPU - Assessment and Referral Form';
?>

<h3 align="center">Assessment and Referral Form</h3>
<hr/>
	<div id="search-dialog" style="display: none;">
        <iframe id="search-dialog-frame" src="" style="height:100%;width:100%;border:none;">
        </iframe>
    </div>

    <div id="search-dialog-audit" style="display: none;">
        <iframe id="audit-dialog-frame" src="" style="height:100%;width:100%;border:none;">
        </iframe>
    </div>

<?php
$this->beginWidget('application.widgets.SegBox', array(
	'title' => 'List of Referrals',
	'headerIcon' => 'fa fa-files-o',
	'headerButtons' => array(
		array(
			'class' => 'bootstrap.widgets.TbButton',
			'label' => 'New Referral',
			'type' => 'success',
			'icon' => 'fa fa-file-o',
			'url' => 'index.php?r=pdpu/referral/create',
		),
	),
));

//$data = $model->search();
$this->widget('bootstrap.widgets.TbGridView', array(
	'dataProvider' => $model->search(),
	'filter' => $model,
	'type' => 'bordered',
	'columns' => array(
		array(
			'name' => 'refer_dt',
			'header' => 'Date Referred',
			'value'=> 'Yii::app()->dateFormatter->format("MM/dd/yyyy",strtotime($data->refer_dt))',
			'headerHtmlOptions' => array(
				'style' => 'text-align: center;width: 150px;'
			),
			'htmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
		array(
			'name' => 'pid',
			'header' => 'HRN',
			'headerHtmlOptions' => array(
				'style' => 'text-align: center; width: 150px;'
			),
			'htmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
		array(
			'name' => 'encounter_nr',
			'header' => 'Encounter #',
			'headerHtmlOptions' => array(
				'style' => 'text-align: center;width: 200px;'
			),
			'htmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
		array(
			'name' => 'person.fullName',
			'header' => 'Name of Patient',
			'headerHtmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
		array(
			'class' => 'pdpu.widgets.CustomButton',
			'header' => 'Actions',
			'template' => '{Print}{view}{audit_trail}',
			'buttons' => array(
				'Print' => array(
					'icon' => 'fa fa-eye',
					'label' => 'Assessment Form',
					// 'visible' => '($data->mss_no !== "0")',
					'options' => array(
						'class' => 'btn btn-small',
						'onclick' => '',
						'id' => '$data->refer_id',
						'enc' => '$data->encounter_nr',
						'function' => 'printReferral',
						'style' => 'margin-right: 5px;',
					),
				),
				'view' => array(
					'icon' => 'fa fa-pencil',
					'label' => 'Referral Form',
					'options' => array(
						'class' => 'btn btn-small',
					),
				),
				'audit_trail' => array(
					'icon' => 'fa fa-search',
					'label' => 'Audit Trail of Recommended Interventions/Remarks',
					'options' => array(
						'class' => 'btn btn-small',
						'onclick' => '',
						'id' => '$data->refer_id',
						'enc' => '$data->encounter_nr',
						'function' => 'auditTrail',
						'style' => 'margin-right: 5px;',
					),
				),
			),
			'htmlOptions'=>array(
				'style'=>'width: 110px; text-align: center',
			),
			'headerHtmlOptions' => array(
				'style' => 'text-align: center;'
			),
		),
	)
));

$this->endWidget();