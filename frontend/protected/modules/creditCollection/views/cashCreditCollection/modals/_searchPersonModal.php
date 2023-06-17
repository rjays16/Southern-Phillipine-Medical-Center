<?php 
$this->setPageTitle('');
$this->showfooter = false;
$baseUrl = Yii::app()->baseUrl;
$cs = Yii::app()->clientScript;
Yii::import('bootstrap.components.Bootstrap');
Yii::import('bootstrap.widgets.TbSelect2');
Yii::import('bootstrap.widgets.TbButton');
Yii::import('bootstrap.widgets.TbGridView');
Yii::import('bootstrap.widgets.TbActiveForm');

$model2 = new FreeFormModel();
$form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id' => 'search-patient-form',
    'type' => 'horizontal',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'service-form'
    )
));
	echo 'HRN / Name: ';
	echo $form->textField($model2, '',
        array(
            'id' => "search-name-hrn",
            'name' => 'search-name-hrn',
            'class' => 'search_box',
            'style' => 'width:200px;height:15px;margin-right:15px',
            'placeholder' => 'HRN / Name'
        )
    );

    echo 'Case no: ';
	echo $form->textField($model2, '',
        array(
            'id' => "search-case-number",
            'name' => 'search-case-number',
            'class' => 'search_box',
            'style' => 'width:150px;height:15px;',
            'placeholder' => 'Case Number'
        )
    );

    echo $form->hiddenField($model2, '',
        array(
            'id' => "collapseclicked",
            'name' => 'collapseclicked'
        )
    );

    $this->widget(
	    'bootstrap.widgets.TbButton',
	    array(
	        'label' => 'Search',
	        'type' => 'info',
	        'size' => 'small',
	        'url' => '#',
	        'disabled' => '',
	        'icon' => 'fa fa-search',
	        'id' => 'btn_modal_search',
	        'htmlOptions' => array(
                'title' => 'Search Patient',
                'style' => 'margin-left: 10px;'
            ),
              
	    )
	);

	$this->widget('person.widgets.PersonCustomGridView', array(
	    'afterAjaxUpdate' => 'function(id,data){
	        initCollapse();
	        initCaseNumberClick();
	    }',
	    'id' => 'people-grid-view',
	    'enableSorting' => false,
	    'dataProvider' => $model->search(),
	    'columns' => array(
	        array('name' => 'pid', 'header' => 'HRN'),
	        array(
	            'header' => 'Sex',
	            'class' => 'person.widgets.PersonCustomColumn',
	            'value' => function ($row, $data) {
	                if ($data->sex == 'f')
	                    return '<i class="fa fa-female fa-lg" style="color:#ff1493;"></i>';
	                else
	                    return '<i class="fa fa-male fa-lg" style="color:#0000cd;"></i>';
	            }
	        ),
	        'fullName',
	        array(
	            'header' => 'Case #',
	            'class' => 'person.widgets.PersonCustomColumn',
	            'value' => function ($row, $data) {
	                if ($data->activeEncounter) {
	                    return CHtml::link($data->activeEncounter->encounter_nr, '#', array(
	                        'class' => 'case-number-link',
	                        'data-case_number' => $data->activeEncounter->encounter_nr,
	                        'data-hrn' => $data->pid
	                    ));
	                } else {
	                    return '';
	                }
	            }
	        ),
			array(
				'header' => 'Department',
				'class' => 'person.widgets.PersonCustomColumn',
				'value' => function ($row, $data) {
					if ($data->activeEncounter) {
						return $data->activeEncounter->dept->name_formal;
					} else {
						return 'Walk-in';
					}
				}
			),
	        array(
	            'header' => 'Patient Type',
	            'class' => 'person.widgets.PersonCustomColumn',
	            'value' => function ($row, $data) {
	                if ($data->activeEncounter) {
	                    return $data->activeEncounter->getEncounterTypeDescription();
	                } else {
	                    return 'Walk-in';
	                }
	            }
	        ),
	        array(
	            'header' => 'Options',
	            'class' => 'person.widgets.PersonCustomColumn',
	            'value' => function ($row, $data) {
	                if (!$data->activeEncounter) {
	                    return CHtml::link($data->pid, '#', array(
	                        'class' => 'case-number-link',
	                        'data-case_number' => $data->activeEncounter->encounter_nr,
	                        'data-hrn' => $data->pid
	                    ));
	                } else {
	                    return CHtml::link('<i class="fa fa-bars fa-lg"></i>', '#', array(
	                        'data-toggle' => 'collapse',
	                        'data-target' => '.encounter_list_' . $data->pid
	                    ));
	                }
	            }
	        )
	    )
	));
$this->endWidget();
?>
            