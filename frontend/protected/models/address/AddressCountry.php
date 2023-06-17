<?php
Yii::import('application.models.address.AddressNode');
/**
 * This is the model class for table "seg_country".
 *
 * The followings are the available columns in table 'seg_country':
 * @property string $country_code
 * @property string $country_name
 * @property string $citizenship
 * @property string $modify_id
 * @property string $modify_date
 * @property string $create_id
 * @property string $create_date
 */
class AddressCountry extends AddressNode
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_country';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('country_code, country_name', 'required'),
			array('country_code', 'length', 'max'=>3),
			array('country_name', 'length', 'max'=>150),
			array('citizenship', 'length', 'max'=>50),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('modify_date, create_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('country_code, country_name, citizenship, modify_id, modify_date, create_id, create_date', 'safe', 'on'=>'search'),
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
			'country_code' => 'Country Code',
			'country_name' => 'Country Name',
			'citizenship' => 'Citizenship',
			'modify_id' => 'Modify',
			'modify_date' => 'Modify Date',
			'create_id' => 'Create',
			'create_date' => 'Create Date',
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

		$criteria->compare('country_code',$this->country_code,true);
		$criteria->compare('country_name',$this->country_name,true);
		$criteria->compare('citizenship',$this->citizenship,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_date',$this->modify_date,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_date',$this->create_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AddressCountry the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getNameAttribute() {
		return 'country_name';
	}
}
