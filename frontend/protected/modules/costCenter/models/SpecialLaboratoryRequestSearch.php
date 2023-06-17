<?php
namespace SegHis\modules\costCenter\models;

use SegHis\models\LoggedInUser;

class SpecialLaboratoryRequestSearch extends LaboratoryRequestSearch
{
    public function hasPermission()
    {
        return LoggedInUser::isPermittedTo('_a_1_special_lab_edit_delete_served_request');
    }
}