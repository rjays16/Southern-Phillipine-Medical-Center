<?php

use SegHis\modules\onlineConsult\services\SearchPersonService;

class SearchPersonList
{

    public $last_name;

    public $first_name;

    public $middle_name;

    public $date_birth;

    public $consult_id;

    public $search;

    public $view = 'onlineConsult.views.common.search.list';


    public function init()
    {

        if (strlen($this->last_name) < 2) {
            return new CException('Searching for last name should be minimum of 2 characters');
        }

        if (strlen($this->first_name) < 2) {
            return new CException('Searching for first name should be minimum of 2 characters');
        }

    }

    public function run()
    {
        if (Yii::app()->request->isAjaxRequest) {
            Yii::app()->clientScript->scriptMap['jquery.ba-bbq.js']      = false;
            Yii::app()->clientScript->scriptMap['jquery.livequery.js']   = false;
            Yii::app()->clientScript->scriptMap['jquery.js']             = false;
            Yii::app()->clientScript->scriptMap['jquery.yiigridview.js'] = false;
        }

        if ($this->consult_id && !$this->search) {
            $consult = ConsultRequest::model()->findByPk($this->consult_id);

            $this->consult_id  = $consult->consult_id;
            $this->last_name   = $consult->name_last;
            $this->first_name  = $consult->name_first;
            $this->middle_name = $consult->name_middle;
            $this->date_birth  = $consult->date_birth;
        }

        $service = new SearchPersonService(
            $this->last_name,
            $this->first_name,
            $this->middle_name,
            $this->date_birth,
            $this->search, $this->consult_id);

        Yii::app()->getController()->renderPartial($this->view,
            array(
                'service' => $service,
                'consult_id' => $this->consult_id,

            )
        );
    }

}
