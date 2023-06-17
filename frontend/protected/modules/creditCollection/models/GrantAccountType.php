<?php

/**
 * This is the model class for table "seg_grant_account_type".
 *
 * The followings are the available columns in table 'seg_grant_account_type':
 * @property string $id
 * @property string $type_name
 * @property string $alt_name
 * @property double $discount
 * @property integer $deleted
 * @property string $date_created
 * @property string $date_modified
 */
class GrantAccountType extends CActiveRecord
{
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
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type_name', 'required'),
			array('with_budget, deleted', 'numerical', 'integerOnly'=>true),
			array('deleted', 'numerical', 'integerOnly'=>true),
			array('discount', 'numerical'),
			array('type_name, alt_name', 'length', 'max'=>30),
			array('date_created, date_modified', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type_name, alt_name, discount, deleted, date_created, date_modified', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
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
            'alt_name' => 'Alt Name',
            'discount' => 'Discount',
            'with_budget' => 'With Budget',
            'deleted' => 'Deleted',
            'date_created' => 'Date Created',
            'created_id' => 'Created',
            'date_modified' => 'Date Modified',
            'modify_id' => 'Modify',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		// $criteria->compare('id',$this->id,true);
		$criteria->compare('type_name',$this->type_name,true);
		$criteria->compare('alt_name',$this->alt_name,true);
		// $criteria->compare('discount',$this->discount);
		$criteria->compare('deleted',0);
		// $criteria->compare('date_created',$this->date_created,true);
		// $criteria->compare('date_modified',$this->date_modified,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return GrantAccountType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getAllGrantAccountType(){
        $criteria = new CDbCriteria();

        $criteria->addCondition('deleted <> 1');
        $criteria->order = "alt_name ASC";
        $criteria->params = array('alt_name' => trim($alt_name).'%');

        return $this->findAll($criteria);
    }
}
