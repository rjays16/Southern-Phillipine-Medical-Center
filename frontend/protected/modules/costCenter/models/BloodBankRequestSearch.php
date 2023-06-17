<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\LoggedInUser;

/**
 * Class BloodBankRequestSearch
 * @package SegHis\modules\costCenter\models
 * @author Nick B. Alcala 3-28-2016
 */
class BloodBankRequestSearch extends LaboratoryRequestSearch
{
    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_blood_bank_edit_delete_served_request');
    }
}