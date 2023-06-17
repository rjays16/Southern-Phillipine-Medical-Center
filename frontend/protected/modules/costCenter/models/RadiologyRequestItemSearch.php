<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\Bill;
use SegHis\models\LoggedInUser;
use SegHis\modules\radiology\models\RadiologyRequestItem;

/**
 * Class RadiologyRequestItemSearch
 *
 * @property RadiologyRequestItem $target
 *
 * @package SegHis\modules\costCenter\models
 * @author Nick B. Alcala 3-28-2016
 */
class RadiologyRequestItemSearch extends CostCenter
{
    public function init($parameters)
    {
        $this->parameters = $parameters;
        $this->target = RadiologyRequestItem::model()->findByAttributes(array(
            'refno' => $parameters['referenceNo'],
            'service_code' => $parameters['serviceCode']
        ));
    }

    public function isServed()
    {
        return $this->target->is_served == 1;
    }

    public function isFinalBill()
    {
        return Bill::hasFinalBill($this->target->request->encounter_nr);
    }

    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_radiology_edit_delete_served_request');
    }
}