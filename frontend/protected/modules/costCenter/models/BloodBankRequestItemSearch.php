<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\LoggedInUser;

/**
 * Class BloodBankRequestItemSearch
 * @package SegHis\modules\costCenter\models
 * @author Nick B. Alcala 3-28-2016
 */
class BloodBankRequestItemSearch extends LaboratoryRequestItemSearch
{
	 public $messageHasServedNoPermission = "The item is served.";

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
        return LoggedInUser::isPermittedTo('_a_1_blood_bank_edit_delete_served_request');
    }
}