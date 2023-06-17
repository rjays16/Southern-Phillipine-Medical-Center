<?php
Yii::import('bootstrap.widgets.TbDataColumn');

class PersonPopoverColumn extends TbDataColumn
{

    public $afterAjaxUpdate;

    public $value = '';

    public $name = 'pid';

    public function init()
    {
        parent::init();
        $this->registerClientScript();
    }

    /**
     * @param int $row
     * @param \SegHis\modules\person\models\Person $data
     */
    protected function renderDataCellContent($row, $data)
    {
        $encounters = $data->encounters;
        if(!empty($encounters)) {
            echo CHtml::link('<i class="fa fa-bars fa-lg"></i>', '#', array(
                'data-toggle' => 'popover',
                'data-placement' => 'left',
                'data-content' => self::createPopoverContent($encounters),
                'data-html' => true
            ));
        } else {
            echo CHtml::link('<i class="fa fa-child fa-lg"></i> Walk-in', '#', array(
                'class' => 'case-number-link',
                'data-case_number' => 0,
                'data-hrn' => $data->pid,
                'title' => 'Click to select as walk-in'
            ));
        }
    }

    /**
     * @param $encounters Encounter[]
     * @return string
     */
    private static function createPopoverContent($encounters)
    {
        $links = array();
        foreach ($encounters as $key => $encounter) {
            $links[] = CHtml::link($encounter->encounter_nr, '#', array(
                'class' => 'case-number-link',
                'data-case_number' => $encounter->encounter_nr,
                'data-hrn' => $encounter->pid
            ));
        }

        return '<ul><li>'.implode('</li><li>',$links).'</li></ul>';
    }

    protected function registerClientScript()
    {
        $afterAjaxUpdate = $this->grid->afterAjaxUpdate;

        $this->grid->afterAjaxUpdate = <<<JS
        function(id, data){
            jQuery('[data-toggle="popover"]').popover().on('click', function(e){
                e.preventDefault();
                jQuery('[data-toggle="popover"]').not(this).popover('hide');
            });
            var js = {$afterAjaxUpdate}(id, data);
        }
JS;

        /* @var $clientScript CClientScript */
        $clientScript = Yii::app()->clientScript;
        $clientScript->registerScript(__CLASS__ . '#' . $this->id, <<<JS
        jQuery(function(){
            jQuery('[data-toggle="popover"]').popover().on('click', function(e){
                e.preventDefault();
                jQuery('[data-toggle="popover"]').not(this).popover('hide');
            });
        });
JS
        );
    }

}