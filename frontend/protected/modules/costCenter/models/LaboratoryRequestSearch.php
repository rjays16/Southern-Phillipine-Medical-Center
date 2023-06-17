<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\Bill;
use SegHis\models\LoggedInUser;
use SegHis\modules\laboratory\models\LaboratoryRequest;

/**
 * Class LaboratoryCostCenter
 *
 * @property LaboratoryRequest $target
 *
 * @package SegHis\modules\costCenter\models
 * @author Nick B. Alcala 3-28-2016
 */
class LaboratoryRequestSearch extends CostCenter
{
    public function init($parameters)
    {
        $this->target = LaboratoryRequest::model()->findByPk($parameters['referenceNo']);
    }

    public function isServed()
    {
        return ($this->target->with(array('items'))->find(array(
            'condition' => 'items.is_served=1 AND items.refno=:refno',
            'params' => array(':refno' => $this->parameters['referenceNo'])
        )) == true);
    }

    public function isFinalBill()
    {
        return Bill::hasFinalBill($this->target->encounter_nr);
    }

    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_laboratory_edit_delete_served_request');
    }

}