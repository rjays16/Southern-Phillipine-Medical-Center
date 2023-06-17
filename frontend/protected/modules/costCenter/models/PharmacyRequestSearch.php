<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\Bill;
use SegHis\models\LoggedInUser;
use SegHis\modules\pharmacy\models\PharmacyRequest;

/**
 * Class PharmacyRequestSearch
 * @property PharmacyRequest $target
 * @package SegHis\modules\costCenter\models
 */
class PharmacyRequestSearch extends CostCenter
{
    public function init($parameters)
    {
        $this->parameters = $parameters;
        $this->target = PharmacyRequest::model()->findByPk($parameters['referenceNo']);
    }

    public function isServed()
    {
        return ($this->target->with(array('items'))->find(array(
                'condition' => 'items.serve_status="S" AND items.refno=:refno',
                'params' => array(':refno' => $this->parameters['referenceNo'])
            )) == true);
    }

    public function isFinalBill()
    {
        return Bill::hasFinalBill($this->target->encounter_nr);
    }

    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_pharmacyy_edit_delete_served_request');
    }
}