<?php

/**
 * Class GrantAccountType
 * This is the model class for table "seg_grant_account_type".
 *
 * @author MM
 */
class GrantAccountType extends ActiveRecord
{
    /**
     * @return SegGrantAccountType the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_grant_account_type';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('type_name', 'required'),
            array('with_budget, deleted', 'numerical', 'integerOnly'=>true),
            array('deleted', 'numerical', 'integerOnly'=>true),
            array('discount', 'numerical'),
            array('type_name', 'length', 'max'=>30),
            array('date_created, date_modified, alt_name', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'accounts' => array(self::HAS_MANY, 'GrantAccounts', 'account_type_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type_name' => 'Type Name',
            'alt_name' => 'Alternate Name',
            'discount' => 'Discount',
            'with_budget' => 'With Budget',
            'deleted' => 'Deleted',
            'date_created' => 'Date Created',
            'date_modified' => 'Date Modified',
            'modify_id' => 'Modify',
            'created_id' => 'Created',
        );
    }

    /**
     * Criteria for list
     *
     * @return CActiveDataProvider
     */
    public function search()
    {
        $criteria=new CDbCriteria;
        // $criteria->addColumnCondition(array('deleted' => 1));
        // $criteria->addCondition("deleted = 0");
        $dp = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));
        return $dp;
    }

    public function findTypeById($id)
    {
        $model = self::model()->findByPk($id);
        return $model->type_name;
    }


}