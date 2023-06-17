<?php

/**
 * This is the model class for table "seg_other_services_new".
 *
 * The followings are the available columns in table 'seg_other_services_new':
 * @property string $service_code
 * @property string $alt_service_code
 * @property string $name
 * @property string $name_short
 * @property string $description
 * @property string $price
 * @property integer $account_type
 * @property integer $is_billing_related
 * @property integer $lockflag
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 */
class OtherServicesNew extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_other_services_new';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('service_code, alt_service_code, name, history, modify_id, create_id', 'required'),
			array('account_type, is_billing_related, lockflag', 'numerical', 'integerOnly'=>true),
			array('service_code, alt_service_code', 'length', 'max'=>12),
			array('name', 'length', 'max'=>150),
			array('name_short', 'length', 'max'=>15),
			array('description', 'length', 'max'=>200),
			array('price', 'length', 'max'=>10),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_time, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('service_code, alt_service_code, name, name_short, description, price, account_type, is_billing_related, lockflag, history, modify_id, modify_time, create_id, create_time', 'safe', 'on'=>'search'),
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
			'service_code' => 'Service Code',
			'alt_service_code' => 'Alt Service Code',
			'name' => 'Name',
			'name_short' => 'Name Short',
			'description' => 'Description',
			'price' => 'Price',
			'account_type' => 'Account Type',
			'is_billing_related' => 'Is Billing Related',
			'lockflag' => 'Lockflag',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
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

		$criteria->compare('service_code',$this->service_code,true);
		$criteria->compare('alt_service_code',$this->alt_service_code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('name_short',$this->name_short,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('account_type',$this->account_type);
		$criteria->compare('is_billing_related',$this->is_billing_related);
		$criteria->compare('lockflag',$this->lockflag);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return OtherServicesNew the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
