<?php
	echo $this->pageTitle = false;

	$baseurl = Yii::app()->baseUrl;
	$cs = Yii::app()->clientScript;

	$js = <<<JAVASCRIPT

	$(document).ready(function(){
		if($('#SegIndustrialCertMedLto_physical_fit_1').is(':checked')){
			$('#not_fit').show();
		}

		$('#SegIndustrialCertMedLto_physical_fit_1').click(function(){
			$('#not_fit').show();
		});

		$('#SegIndustrialCertMedLto_physical_fit_0').click(function(){
			$('#not_fit').hide();
		});

		// eyesight
		if($('#SegIndustrialCertMedLto_clear_eyesight_1').is(':checked')){
			$('#eyesight').show();
		}

		$('#SegIndustrialCertMedLto_clear_eyesight_1').click(function(){
			$('#eyesight').show();
		});

		$('#SegIndustrialCertMedLto_clear_eyesight_0').click(function(){
			$('#eyesight').hide();
		});

		// hearing
		if($('#SegIndustrialCertMedLto_clear_hearing_1').is(':checked')){
			$('#hearing').show();
		}

		$('#SegIndustrialCertMedLto_clear_hearing_1').click(function(){
			$('#hearing').show();
		});

		$('#SegIndustrialCertMedLto_clear_hearing_0').click(function(){
			$('#hearing').hide();
		});
	});

	function printCertificate(){
		var encounter_nr = $('#SegIndustrialCertMedLto_encounter_nr').val();
		window.open('modules/reports/reports/IC_Lto_MedCert.php?encounter='+encounter_nr);
	}

JAVASCRIPT;

	$cs->registerScript('js',$js,CClientScript::POS_HEAD);
	$cs->registerCssFile($baseUrl . '/css/frontend/application.css')
            ->registerCssFile($baseUrl . '/css/frontend/alerts.css')
            ->registerCssFile($baseUrl . '/css/frontend/animate.css')
            ->registerScriptFile($baseUrl . '/js/mustache.js')
            ->registerScriptFile($baseUrl . '/js/jquery/jquery.blockUI.js')
            ->registerScriptFile($baseUrl . '/js/frontend/alert.js');

	//echo CVarDumper::dump($person);

	$this->beginWidget('application.widgets.SegBox', array(
			'title' => 'Patient Information',
			'headerIcon' => 'fa fa-folder-open-o'
		));

?>

	<table class="table table-condensed">
		<tr>
			<th style="text-align: right; width: 150px;">Name:</th>
			<td style="width: 200px;"><?php echo $person->getFullName(); ?></td>
			<th style="text-align: right; width: 150px;">HRN:</th>
			<td><?php echo $person->getPid(); ?></td>
		</tr>
		<tr>
			<th style="text-align: right;">Age:</th>
			<td><?php echo $person->getAge(); ?></td>
			<th style="text-align: right;">Sex:</th>
			<td><?php echo $person->getSex(); ?></td>
		</tr>
		<tr>
			<th style="text-align: right;">Date of Birth:</th>
			<td><?php echo $person->getBirthDate(); ?></td>
			<th style="text-align: right;">Civil Status:</th>
			<td><?php echo $person->getCivilStatus(); ?></td>
		</tr>
	</table>

<?php
	$this->endWidget();
	//echo CVarDumper::dump($model);

	$form = $this->beginWidget('CActiveForm', array(
			'id' => 'certificateForm',
			'enableAjaxValidation' => false,
			'htmlOptions' => array(
					'class' => 'well',
					'style' => 'color: #000',
				),
		));

	echo $form->errorSummary($model);
?>

	<table class="table table-condensed">
		<tr>
			<td style="width: 250px; text-align: right;">
				<?php echo $form->labelEx($model, 'physical_fit'); ?>
			</td>
			<td>
				<?php 
					$physical = array('yes' => 'YES', 'no' => 'NO');
					echo $form->radioButtonList($model, 'physical_fit', $physical, array(
																					'separator' => '&nbsp;&nbsp;', 
																					'labelOptions' => array(
																						'style' => 'display: inline; margin-right: 10px;'
																						),
																					'style' => 'margin-top: -2px;'
																					)
					);
				?>
				<div id="not_fit" style="margin-top: 20px;" hidden>
					<table>
						<tr style="border-top: hidden;">
							<td style="text-align: right; width: 160px;">Upper Limbs - Amputated</td>
							<td style="vertical-align: middle; padding-left: 20px;">
								<?php 
									$upper_limbs = array('left' => 'LEFT', 'right' => 'RIGHT');
									echo $form->radioButtonList($model, 'upper_limbs', $upper_limbs, array(
																									'separator' => '&nbsp;&nbsp;', 
																									'labelOptions' => array(
																										'style' => 'display: inline; margin-right: 10px;'
																										),
																									'style' => 'margin-top: -2px;'
																									)
									); 
								?>
							</td>
						</tr>
						<tr style="border-top: hidden;">
							<td style="text-align: right;">Lower Limbs - Amputated</td>
							<td style="vertical-align: middle; padding-left: 20px;">
								<?php 
									$lower_limbs = array('left' => 'LEFT', 'right' => 'RIGHT');
									echo $form->radioButtonList($model, 'lower_limbs', $lower_limbs, array(
																									'separator' => '&nbsp;&nbsp;', 
																									'labelOptions' => array(
																										'style' => 'display: inline; margin-right: 10px;'
																										),
																									'style' => 'margin-top: -2px;'
																									)
									); 
								?>
							</td>
						</tr>
						<tr style="border-top: hidden;">
							<td style="text-align: right;">Post-Poliomyelitis - With one paralyzed leg</td>
							<td style="vertical-align: middle; padding-left: 20px;">
								<?php 
									$paralyzed_leg = array('left' => 'LEFT', 'right' => 'RIGHT');
									echo $form->radioButtonList($model, 'paralyzed_leg', $paralyzed_leg, array(
																									'separator' => '&nbsp;&nbsp;', 
																									'labelOptions' => array(
																										'style' => 'display: inline; margin-right: 10px;'
																										),
																									'style' => 'margin-top: -2px;'
																									)
									); 
								?>
							</td>
						</tr>
						<tr style="border-top: hidden;">
							<td style="text-align: right;">Paraplegic - Paralyzed from waist down</td>
							<td style="vertical-align: middle; padding-left: 20px;">
								<?php echo $form->checkBox($model, 'paraplegic') ?>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">
				<?php echo $form->labelEx($model, 'clear_eyesight'); ?>
			</td>
			<td>
				<?php 
					$eyesight = array('yes' => 'YES', 'no' => 'NO');
					echo $form->radioButtonList($model, 'clear_eyesight', $eyesight, array(
																					'separator' => '&nbsp;&nbsp;', 
																					'labelOptions' => array(
																						'style' => 'display: inline; margin-right: 10px;'
																						),
																					'style' => 'margin-top: -2px;'
																					)
					);
				?>

				<div id="eyesight" style="margin-top: 20px;" hidden>
					<?php 
						$eye_defect = array('partial' => 'PARTIALLY BLIND', 'color' => 'COLOR BLIND', 'glass' => 'NEEDS PROPER CORRECTIVE GLASSES');
						echo $form->radioButtonList($model, 'eye_defect', $eye_defect, array(
																						'separator' => '<br/>', 
																						'labelOptions' => array(
																							'style' => 'display: inline; margin-right: 10px;'
																							),
																						'style' => 'margin-top: -2px;'
																						)
						); 
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">
				<?php echo $form->labelEx($model, 'clear_hearing'); ?>
			</td>
			<td>
				<?php 
					$hearing = array('yes' => 'YES', 'no' => 'NO');
					echo $form->radioButtonList($model, 'clear_hearing', $hearing, array(
																					'separator' => '&nbsp;&nbsp;', 
																					'labelOptions' => array(
																						'style' => 'display: inline; margin-right: 10px;'
																						),
																					'style' => 'margin-top: -2px;'
																					)
					);
				?>

				<div id="hearing" style="margin-top: 20px;" hidden>
					<?php 
						$hearing_defect = array('speech' => 'SPEECH/HEARING IMPAIRED', 'device' => 'NEEDS HEARING DEVICE');
						echo $form->radioButtonList($model, 'hearing_defect', $hearing_defect, array(
																						'separator' => '<br/>', 
																						'labelOptions' => array(
																							'style' => 'display: inline; margin-right: 10px;'
																							),
																						'style' => 'margin-top: -2px;'
																						)
						); 
					?>
				</div>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">
				<?php echo $form->labelEx($model, 'other_findings'); ?>
			</td>
			<td>
				<?php echo $form->textArea($model, 'other_findings', array('rows' => 4, 'class' => 'span6')); ?>
				<?php echo $form->hiddenField($model, 'encounter_nr'); ?>
			</td>
		</tr>
		<tr>
			<td style="text-align: right; vertical-align: middle;">Physician</td>
			<td>
				<?php
					/*$url = Yii::app()->createUrl('industrialClinic/certificate/findDoctors'); 
					$this->widget('bootstrap.widgets.TbSelect2', array(
					            'asDropDownList' => false,
					            'model' => $model,
					            'attribute' => 'physician',
					            'options' => array(
					                'width' => 300,
					                'placeholder' => 'Search Doctor',
					                'dataType' => 'json',
					                'id' => 'js:function(data){return data.personell_nr;}',
					                'ajax' => array(
					                    'url' => $url,
					                    'data' => 'js:function(term) {
					                            return {
					                                t: term
					                            };
					                        }',
					                    'results' => 'js:function(data,page) { return {results: data}; }',
					                ),
					                'allowClear' => false,
					                'escapeMarkup' => 'js:function (markup) { return markup; }',
					                'minimumInputLength' => 3,
					                'initSelection' => 'js:function(element, callback){
					                        var id = $(element).val();
					                        if(id !== "") {
					                            $.ajax("' . $url . '", {
					                                data: {id: id},
					                                dataType: "json"
					                            }).done(function(data) {
					                                callback(data);
					                            });
					                        }
					                    }',
					                'formatResult' => 'js:function(data, container, query){
					                        return "<span class=\'label label-success\'><i class=\'fa fa-user-md\'></i> "+ data.personell_nr +"</span>&nbsp;" +
					                               "<span class=\'label label-info\'><i class=\'fa fa-stethoscope\'></i> " + data.name_formal + "</span><br/>" +
					                               "<span>"+data.doctor_name+"</span>";
					                    }',
					                'formatSelection' => 'js:function(data, container){
					                    return data.doctor_name;
					                }'
					            ),
					        ));*/
			        echo $form->dropDownList($model, 'physician', CHtml::listData($doctor, 'personell_nr', 'doctor_name'), array('class' => 'span6', 'style' => 'margin-top: 10px;'));
				?>
			</td>
		</tr>
		<tr>
			<td style="text-align: right;">
				<?php echo $form->labelEx($model, 'control_num'); ?>
			</td>
			<td>
				<?php echo $form->textField($model, 'control_num', array('placeholder' => 'Control Number')); ?>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<?php
		            $this->widget('bootstrap.widgets.TbButton', array(
		                'label' => 'Update',
		                'type' => 'success',
		                'icon' => 'fa fa-send',
		                'buttonType' => 'submit',
		                'htmlOptions' => array(
		                    'style' => 'margin-top: 10px;',
		                    'class' => 'pull-right'
		                ),
		            ));

		            $this->widget('bootstrap.widgets.TbButton', array(
		                'label' => 'Print',
		                'type' => 'button',
		                'icon' => 'fa fa-print',
		                'htmlOptions' => array(
		                    'style' => 'margin-top: 10px; margin-right: 10px;',
		                    'class' => 'pull-right',
		                    'onclick' => 'printCertificate()'
		                ),
		            ));
	            ?>
			</td>
		</tr>
	</table>

<?php
	$this->endWidget();
	unset($form);