<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\Bill;
use SegHis\models\LoggedInUser;
use SegHis\modules\laboratory\models\LaboratoryRequestItem;

/**
 * Class LaboratoryCostCenter
 *
 * @property LaboratoryRequestItem $target
 *
 * @package SegHis\modules\costCenter\models
 * @author Nick B. Alcala 3-28-2016
 */
class LaboratoryRequestItemSearch extends CostCenter
{

    public $messageHasServedNoPermission = "The item is served.";

    public function init($parameters)
    {
        $this->parameters = $parameters;
        $this->target = LaboratoryRequestItem::model()->findByAttributes(array(
            'refno' => $parameters['referenceNo'],
            'service_code' => $parameters['serviceCode'],
        ));
    }

    public function isServed()
    {
        return $this->target->is_served==1;
    }

    public function isFinalBill()
    {
        return Bill::hasFinalBill($this->target->request->encounter_nr);
    }

    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_laboratory_edit_delete_served_request');
    }

}