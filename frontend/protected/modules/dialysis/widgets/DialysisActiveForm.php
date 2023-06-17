<?php
Yii::import('bootstrap.widgets.TbActiveForm');

class DialysisActiveForm extends TbActiveForm {

	public function displayTextFieldRow($model, $displayValue, $hiddenValue, $attribute, $htmlOptions = array(), $rowOptions = array()){

		$tempHtmlOptions = $htmlOptions;
		$tempAttribute = $attribute . '_display';
		CHtml::resolveNameID($model,$tempAttribute,$tempHtmlOptions);

		return $this->textFieldRow($model, $attribute,array_merge($htmlOptions,array(
			'id' => $tempHtmlOptions['id'],
			'name' => $tempHtmlOptions['name'],
			'value' => $displayValue
		)), array_merge($rowOptions,array(
			'append' => $this->hiddenField($model, $attribute, array('value' => $hiddenValue)),
			'appendOptions' => array('isRaw' => true)
		)));

	}

	public function dateTimePickerSlider($model, $attribute, $widgetOptions = array(), $rowOptions = array())
	{
		return $this->widgetRowInternal('dialysis.widgets.DateTimePickerSlider', $model, $attribute, $widgetOptions, $rowOptions);
	}

}