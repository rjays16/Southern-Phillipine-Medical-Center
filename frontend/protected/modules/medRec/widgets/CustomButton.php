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
        $url = isset($button['url']) ? $this->evaluateExpression($button['url'], array('data' => $data, 'row' => $row))
            : '#';
        $options = isset($button['options']) ? $button['options'] : array();

        //my add starts here
        if(isset($options['name_last'])){
            $name_last = $this->evaluateExpression($options['name_last'],array('data' => $data, 'row' => $row));
             $name_first = $this->evaluateExpression($options['name_first'],array('data' => $data, 'row' => $row));
              $name_middle = $this->evaluateExpression($options['name_middle'],array('data' => $data, 'row' => $row));
              $date_birth = $this->evaluateExpression($options['date_birth'],array('data' => $data, 'row' => $row));
              $pid = $this->evaluateExpression($options['pid'],array('data' => $data, 'row' => $row));

              // var_dump($pid);die;

            // die($id)
             if($options['function']=='viewInfo'){
                $options['onclick']="{viewInfo('".$name_last."','".$name_first."','".$name_middle."','".$date_birth."');}";
            }

             if($options['function']=='viewConsultHistory'){
                $options['onclick']="{viewConsultHistory('".$pid."');}";
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