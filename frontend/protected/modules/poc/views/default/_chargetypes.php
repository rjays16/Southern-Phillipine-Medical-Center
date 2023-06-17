<?php
    echo CHtml::dropDownList('chargeTypes', 'id', 
                  $chargelist,
                  array('empty' => 'PERSONAL')); 