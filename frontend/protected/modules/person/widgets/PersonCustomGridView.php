<?php
Yii::import('bootstrap.widgets.TbGridView');

class PersonCustomGridView extends TbGridView
{
    public function renderTableRow($row)
    {
        $htmlOptions = array();
        if ($this->rowHtmlOptionsExpression !== null) {
            $data = $this->dataProvider->data[$row];
            $options = $this->evaluateExpression($this->rowHtmlOptionsExpression, array('row' => $row, 'data' => $data));
            if (is_array($options))
                $htmlOptions = $options;
        }

        if ($this->rowCssClassExpression !== null) {
            $data = $this->dataProvider->data[$row];
            $class = $this->evaluateExpression($this->rowCssClassExpression, array('row' => $row, 'data' => $data));
        } elseif (is_array($this->rowCssClass) && ($n = count($this->rowCssClass)) > 0)
            $class = $this->rowCssClass[$row % $n];

        if (!empty($class)) {
            if (isset($htmlOptions['class']))
                $htmlOptions['class'] .= ' ' . $class;
            else
                $htmlOptions['class'] = $class;
        }

        echo CHtml::openTag('tr', $htmlOptions) . "\n";
        foreach ($this->columns as $column) {
            /* @var $column CDataColumn */
            $column->renderDataCell($row);
        }
        echo "</tr>\n";

        $pid = $this->dataProvider->data[$row]["pid"];
        echo CHtml::tag('tr', array(
        ), "<td colspan='7' style='padding: 0; border-top: none; background-color: rgba(0, 107, 219, 0.16);'>
                <div class='encounter_list_{$pid} collapse' data-hrn='{$pid}'>
                    <div id='loading_{$pid}' style='float: right; padding: 5px; margin-right: 10px;'>
                        <i class='fa fa-spinner fa-spin fa-3x'></i>
                        <span>Loading case numbers...</span>
                    </div>
                    <table id='encounter_table_{$pid}' style='display: none;' class='patient-encounter-list'>
                        <thead>
                        <tr>
                            <th>Case #</th>
                            <th>Date</th>
                            <th>Department</th>
                            <th>Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </td>", true);
    }
}