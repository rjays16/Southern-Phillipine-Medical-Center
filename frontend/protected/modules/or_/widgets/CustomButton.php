<?php
Yii::import('bootstrap.widgets.TbButtonColumn');

class CustomButton extends TbButtonColumn
{
    protected function renderButton($id, $button, $row, $data)
    {
        if (isset($button['visible']) && !$this->evaluateExpression(
                $button['visible'],
                array('row' => $row, 'data' => $data)
            )
        ) {
            return;
        }

        $label = isset($button['label']) ? $button['label'] : $id;
        $url = '#';
        $options = isset($button['options']) ? $button['options'] : array();

        //my add starts here
        if(isset($options['id'])){
            $package_id = $this->evaluateExpression($options['id'],array('data' => $data, 'row' => $row));
            if($options['function']=='submitPackage'){
                $options['package_id']=$package_id;
            }
        }
        //my add ends here

        if (!isset($options['title'])) {
            $options['title'] = $label;
        }

        if (!isset($options['data-toggle'])) {
            $options['data-toggle'] = 'tooltip';
        }

        if (isset($button['icon'])) {
            if (strpos($button['icon'], 'fa') === false) {
                $button['icon'] = 'fa-' . implode(' fa-', explode(' ', $button['icon']));
            }

            echo CHtml::link('<i class="' . $button['icon'] . '"></i>', $url, $options);
        } else if (isset($button['imageUrl']) && is_string($button['imageUrl'])) {
            echo CHtml::link(CHtml::image($button['imageUrl'], $label), $url, $options);
        } else {
            echo CHtml::link($label, $url, $options);
        }
    }
}