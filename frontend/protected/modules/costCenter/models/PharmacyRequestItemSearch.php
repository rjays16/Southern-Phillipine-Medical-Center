<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\Bill;
use SegHis\models\LoggedInUser;
use SegHis\modules\pharmacy\models\PharmacyRequestItem;

/**
 * Class PharmacyRequestSearch
 * @property PharmacyRequest $target
 * @package SegHis\modules\costCenter\models
 */
class PharmacyRequestItemSearch extends CostCenter
{
    public function init($parameters)
    {
        $this->parameters = $parameters;
        $this->target = PharmacyRequestItem::model()->findByAttributes(array(
            'refno' => $parameters['referenceNo'],
            'bestellnum' => $parameters['itemCode']
        ));
    }

    public function isServed()
    {
        return $this->target->serve_status == "S";
    }

    public function isFinalBill()
    {
        return Bill::hasFinalBill($this->target->request->encounter_nr);
    }

    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_pharmacyy_edit_delete_served_request');
    }
}