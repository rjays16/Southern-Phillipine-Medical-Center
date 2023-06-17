<?php
    Yii::import('bootstrap.components.Bootstrap');
    Yii::import('bootstrap.widgets.TbButton');

    if($save)
        echo "<div id='alert-success'><h3>Saving Done</h3></div>";
    else
        echo "<div id='alert-failed'><h3>Saving Failed</h3></div>";

    if(isset($_SESSION['tempDetails'])){
    	$details = $_SESSION['tempDetails'];
    	unset($_SESSION['tempDetails']);
    }

    if(!empty($details)){
        $form = $this->beginWidget(
            'bootstrap.widgets.TbActiveForm',
            array(
                'id'=>'package-form',
                'method' => 'post',
            )
        );

        echo CHtml::hiddenField('packageSelect' , $packageSelect);
        echo CHtml::hiddenField('trans_type'    , $trans_type);
        echo CHtml::hiddenField('pharma_refno'  , $pharma_refno);

    	echo "Some items are missing/lacking from inventory:";
    	echo "<br/>";
        echo "<table>";
    	foreach ($details as $key => $value) {
            echo "<tr>";
                echo "<td>";
                    echo ($key+1) . ". " . $value['bestellnum'] . " - " . $value['name'] . " ";
                echo "</td>";
                echo "<td>";
                    echo CHtml::hiddenField('item_code[]', $value['bestellnum']);
                    echo CHtml::dropDownList('pharmacy_area['.$value['bestellnum'].']', '', CHtml::listData($pharmacyAreas,'area_code','areaCode.area_name'), array('style' => 'margin-left: 5px;'));
                echo "</td>";
            echo "</tr>";
    	}
        echo "<tr>";
            echo "<td colspan='2'>";
                $this->widget(
                    'bootstrap.widgets.TbButton',
                    array(
                        'buttonType' => 'submit',
                        'type' => 'primary',
                        'label' => 'Submit'
                    )
                );
            echo "</td>";
        echo "</tr>";

        echo "</table>";

        
        $this->endWidget();
    }
?>