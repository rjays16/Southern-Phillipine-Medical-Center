<?php

namespace SegHis\modules\medRec\services;

use CarePerson;
use ConsultRequest;
use CDBCriteria;
use CActiveDataProvider;

class SearchPersonService
{
    public $name_last;
    public $name_first;
    public $name_middle;
    public $date_birth;
    public $search;
    public $consult_id;

    public function __construct($name_last, $name_first, $name_middle, $date_birth, $search, $consult_id)
    {

        $this->name_last = $name_last;

        $this->name_first  = $name_first;
        $this->name_middle = $name_middle;
        $this->date_birth  = $date_birth;
        $this->search      = $search;
        $this->consult_id  = $consult_id;
    }

    public function displayPerson()
    {

        $model = new CarePerson();

        $criteria = new CDBCriteria();

        if(strpos(addslashes($this->name_first), ', ') !== false)
            $this->name_first = str_replace(', ', ' ', $this->name_first);


        $criteria->addCondition('name_last LIKE "' . $this->name_last . '%"', "AND");
        $criteria->addCondition('name_first LIKE "' . $this->name_first . '%"', "AND");

        if (!$this->search) {
            if($this->name_middle)
                $criteria->addCondition('name_middle LIKE "' . $this->name_middle . '%"', "AND");

            $criteria->addCondition('date_birth = "' . $this->date_birth . '"', "AND");

        }

        $criteria->order  = 't.name_last DESC, t.name_first DESC';
        $model->consultId = $this->consult_id;


        return new CActiveDataProvider($model, array(
            'criteria'   => $criteria,
            'pagination' => array(
                'pageSize' => 5,
            )
        ));
    }

}
