<?php
namespace SegHis\modules\admission\models;
/**
 * This is the model class for table "seg_accomodation_type".
 *
 * The followings are the available columns in table 'seg_accomodation_type':
 * @property integer $accomodation_nr
 * @property string $accomodation_name
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 */
class AccommodationType extends \CareActiveRecord
{
	const ACCOMMODATION_TYPE_SERVICE = 1;
	const ACCOMMODATION_TYPE_PAY = 2;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_accomodation_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('accomodation_name', 'required'),
			array('accomodation_name', 'length', 'max'=>100),
			array('status', 'length', 'max'=>20),
			array('modify_id, create_id', 'length', 'max'=>50),
			array('history, modify_dt, create_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('accomodation_nr, accomodation_name, status, history, modify_id, modify_dt, create_id, create_dt', 'safe', 'on'=>'search'),
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
			'accomodation_nr' => 'Accomodation Nr',
			'accomodation_name' => 'Accomodation Name',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
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
	 * @return \CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new \CDbCriteria;

		$criteria->compare('accomodation_nr',$this->accomodation_nr);
		$criteria->compare('accomodation_name',$this->accomodation_name,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AccommodationType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
