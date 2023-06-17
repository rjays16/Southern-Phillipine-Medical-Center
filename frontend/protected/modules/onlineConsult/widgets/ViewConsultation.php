<?php

class ViewConsultation
{

    public $last_name;

    public $first_name;

    public $middle_name;

    public $date_birth;

    public $consult_id;

    public $view = 'onlineConsult.views.common.search.consultation_details';


    public function init(){}

    public function run()
    {
        $modelDepartment = new CareDepartment;
        $departmentlist  = $modelDepartment->getAllOPDepartment();
        $departments = CHtml::listData(
            $departmentlist,
            'nr',
            'name_formal');
        if ($this->consult_id) {
            $consult = ConsultRequest::model()->findByPk($this->consult_id);

            $this->consult_id  = $consult->consult_id;
            $this->last_name   = $consult->name_last;
            $this->first_name  = $consult->name_first;
            $this->middle_name = $consult->name_middle;
            $this->date_birth  = $consult->date_birth;
        }

        Yii::app()->getController()->renderPartial($this->view,
            array('modelDepartment'=>$departments)
        );       
    }

}
