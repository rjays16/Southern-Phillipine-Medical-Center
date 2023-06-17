
<?php

$this->beginWidget(
    'bootstrap.widgets.TbModal',
    array(
        'id' => 'member-form-modal',
        'fade' => false,
        'htmlOptions' => array(
            'data-backdrop' => 'static',
            // 'data-dismiss' => false,
            'style' => 'width:500px;height:380px;text-align:center;overflow-y: scroll'
        ),

    )
);
?>
<div class="modal-header">
    <a class="close" data-dismiss="modal"><i class="fa fa-times-circle"></i></a>
    <h5>Add New Medicine</h5>
</div>
<?php



$this->widget(
    'bootstrap.widgets.TbSelect2',
    array(
        'name' => 'packageNewSelect',
        'data' => $newMedsList,
        'options' => array(
            'minimumInputLength' => '3',
            'placeholder' => 'Enter the Drug name.'
        ),
       'htmlOptions' => array(
                        'style' => 'width:200px',
                        'data-encounter' => $encounter_nr,
                    )
    )
);


echo "<br>Date";
echo $form->datepickerRow($model2, '',
    array(
        'options' =>
            array(
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'showButtonPanel' => true,

            ),
        'htmlOptions'=>array(
            'style' => 'width:85px;height:30px',
            'id' => "textDate2",
            'name' => 'textDate2',
            'placeholder'=>'yyyy-mm-dd',
            'value' => date('Y-m-d')
        )
    ));

echo $form->timeField($model2, '',
    array(
        'size' => 2,
        'id' => "textTime2",
        'name' => 'textTime2',
        'style' => ' width:98px;height:30px;margin-left:2px',
        'value' => date('H:i')

    )
);
echo $form->textFieldGroup($model2, '', array(
    'widgetOptions' => array(
        'htmlOptions' => array(
            'id' => 'brand_name',
            'style'=>'width:200px',
        )
    ),
    'min' => 0,
    'label' => 'Brand Name',
    'labelOptions' => array(
        'class' => 'col-md-6',
    ),
    'wrapperHtmlOptions' => array(
        'class' => 'col-md-6',

    ),

));
   ?>
  <p>Route </p>

   <?php
  echo $form->textArea($model2,'route_new',
                                    array('rows'=>5, 
                                          'cols'=>50,
                                          'size'=>200,
                                          'maxlength'=>600,
                                          'style'=>'width:250px'));
  ?> 
  <p>Dosage & Frequency<p>
<?php
 echo $form->textArea($model2,'frequency_new',
                                    array('rows'=>5, 
                                          'cols'=>50,
                                          'size'=>200,
                                          'maxlength'=>600,
                                          'style'=>'width:250px'));
// echo $form->textFieldGroup($model2, '', array(
//     'widgetOptions' => array(
//         'htmlOptions' => array(
//             'id' => 'route_new',
//             'style'=>'width:200px',
//         )
//     ),
//     'min' => 0,
//     'label' => 'Route',
//     'labelOptions' => array(
//         'class' => 'col-md-6',
//     ),
//     'wrapperHtmlOptions' => array(
//         'class' => 'col-md-6',

//     ),

// ));
// echo $form->textFieldGroup($model2, '', array(
//     'widgetOptions' => array(
//         'htmlOptions' => array(
//             'id' => 'frequency_new',
//             'style'=>'width:200px',
//         )
//     ),
//     'min' => 0,
//     'label' => 'Frequency',
//     'labelOptions' => array(
//         'class' => 'col-md-6',
//     ),
//     'wrapperHtmlOptions' => array(
//         'class' => 'col-md-6',

//     ),

// ));

echo $form->textFieldGroup($model2, '', array(
    'widgetOptions' => array(
        'htmlOptions' => array(
            'id' => 'quantity_new'
        )
    ),
    'min' => 0,
    'label' => 'Quantity',
    'labelOptions' => array(
        'class' => 'col-md-6',
    ),
    'wrapperHtmlOptions' => array(
        'class' => 'col-md-6',

    ),

));
echo $form->textFieldGroup($model2, '', array(
    'widgetOptions' => array(
        'htmlOptions' => array(
            'id' => 'price_new'
        )
    ),
    'min' => 0,
    'label' => 'Total Price',
    'labelOptions' => array(
        'class' => 'col-md-6',
    ),
    'wrapperHtmlOptions' => array(
        'class' => 'col-md-6',

    ),

));


$this->widget(
    'bootstrap.widgets.TbButton',
    array(
        'id' => 'AddNewBtn',
        'label' => 'Add',
        'url' => '#',
        'size' => 'small',
        'htmlOptions' => array(
            'style' => 'margin-left: 1em',
            'data-encounter' => $encounter_nr,
        )
    )
);




?>



<?php $this->endWidget(); ?>