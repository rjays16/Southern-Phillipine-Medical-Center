<?php

echo CHtml::tag('div');
	$this->beginWidget(
		'bootstrap.widgets.TbBox',
		array(
			'title' => 'Patient Details'
		)	
	);

		$model2 = new FreeFormModel();
		$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	        'id' => 'walkin-form',
	        'type' => 'horizontal',
	        'enableAjaxValidation' => false,
	        'htmlOptions' => array(
	            'class' => 'service-form'
	        )
	    ));

			echo CHtml::tag('div', array('style' => ''));
			    echo 'HRN: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium indextbox',
			        'id' => 'pid',
			        'readonly' => 'readonly'
			    ));
		    echo CHtml::closeTag('div');

		    echo CHtml::tag('div', array('style' => 'margin-top: -28px; margin-left: 450px'));
			    echo 'Patient Type: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium indextbox',
			        'id' => 'encounter_type',
			        'readonly' => 'readonly'
			    ));
		    echo CHtml::closeTag('div');

		    echo CHtml::tag('div', array('style' => 'margin-top: -28px; margin-left: 750px'));
			    echo 'Classification: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium indextbox',
			        'id' => 'classification',
			        'readonly' => 'readonly'
			    ));
		    echo CHtml::closeTag('div');

		    echo CHtml::tag('div', array('style' => ''));
			    echo 'Case No: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium indextbox',
			        'id' => 'encounter_nr',
			        'readonly' => 'readonly'

			    ));
		    echo CHtml::closeTag('div');

		    echo CHtml::tag('div', array('style' => 'margin-top: -30px;margin-left: 450px;'));
			    echo 'Actual Balance: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium',
			        'id' => 'actualBalance',
			        'style' => 'height: 20px;border-style: dashed; text-align: right;font-weight:bold',
			        'readonly' => 'readonly'

			    ));
		    echo CHtml::closeTag('div');

		    echo CHtml::tag('div', array('style' => 'margin-top: -30px;margin-left: 750px;'));
			    echo 'Remaining Balance: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium',
			        'id' => 'remainBalance',
			        'style' => 'height: 20px;border-style: dashed; text-align: right;font-weight:bold',
			        'readonly' => 'readonly'

			    ));
		    echo CHtml::closeTag('div');

		    echo CHtml::tag('div');
			    echo 'Patient Name: ';
			    echo $form->textField($model2, '', array(
			        'class' => 'input-medium',
			        'id' => 'patient_name',
			        'style' => 'height: 15px;width:18%;font-weight:bold',
			        'readonly' => 'readonly'

			    ));

			    $this->widget(
				    'bootstrap.widgets.TbButton',
				    array(
				        'label' => 'Search',
				        'type' => 'info',
				        'size' => 'small',
				        'url' => '#',
				        'disabled' => '',
				        'icon' => 'fa fa-search',
				        'id' => 'btn_search',
				        'htmlOptions' => array(
			                'data-toggle' => 'modal',
			                'data-target' => '#search-patient-modal',
			                'data-tooltip' => 'tooltip',
			                'title' => 'Search Patient',
			                'style' => 'margin-left: 10px;'
			            ),
			              
				    )
				);
		    echo CHtml::closeTag('div');

		    echo $form->hiddenField($model2, '',
		        array(
		            'id' => "date_today",
		            'name' => 'date_today',
		            'value' => date("Y-m-d")
		        )
		    );
		$this->endWidget();

	$this->endWidget();

echo CHtml::closeTag('div');

echo CHtml::tag('div');
	$this->beginWidget(
		'bootstrap.widgets.TbBox',
		array(
			'title' => "Patient's Data",
	        'headerButtons' => array(
	            array(
	                'class' => 'bootstrap.widgets.TbButton',
	                'label' => 'Referral',
	                'type'  => 'success',
	                'icon'  => 'fa fa-user',
	                'url'   => '',
	                'id'    => 'btn_referral',
	                'htmlOptions' => array(
	                    'data-alert-message' => 'Please select a patient first'
	                )
	            ),

	        ),
		)	
	);

		$this->widget(
            'bootstrap.widgets.TbTabs', array(
                'tabs' => array(
                    array(
                        'label'       => 'Referrals',
                        'active'      => 1,
                        'itemOptions' => array(
                            'id' => 'referral-tab',
                        ),
                        'content'     => $this->renderPartial(
                            'tabs/referrals', array(
                            	'referrals' => $referrals
                       		), true
                        )
                    ),
                    array(
                        'label'       => 'Requests',
                        'active'      => 0,
                        'itemOptions' => array(
                            'id' => 'request-tab',
                        ),
                        'content'     => $this->renderPartial(
                            'tabs/requests', array(
	                            'costCentersList'	=> $costCentersList,
	                            'requestStatus'		=> $requestStatus,
	                            'modelGrants'		=> $modelGrants
                       		), true
                        )
                    ),
                ),
            )
        );

	$this->endWidget();

echo CHtml::closeTag('div');

$this->renderPartial('_mainModal', array('model' => $model));

?>