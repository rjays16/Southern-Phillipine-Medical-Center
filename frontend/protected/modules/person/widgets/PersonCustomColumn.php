<?php
Yii::import('bootstrap.widgets.TbDataColumn');

class PersonCustomColumn extends TbDataColumn
{
    protected function renderDataCellContent($row, $data)
    {
        if(is_callable($this->value)) {
            echo call_user_func($this->value, $row, $data);
        } else {
            parent::renderDataCellContent($row, $data);
        }
    }
}