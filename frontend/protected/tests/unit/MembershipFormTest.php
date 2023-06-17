<?php

Yii::import('phic.models.MembershipForm');
Yii::import('phic.models.Pmrf');

class MembershipFormTest extends CDbTestCase
{

    public function testPmrfValidator()
    {
        $model = new Pmrf;
        $model->member_info_id = "";
        $model->purpose = "";
        $model->membership_category = "";
        $model->membership_other = "";
        $model->membership_income = "";
        $model->membership_effective_date = "";
        $model->tin = "";
        $this->assertFalse($model->validate());
        $model->member_info_id = "1";
        $model->purpose = "1";
        $this->assertTrue($model->validate());
    }

}