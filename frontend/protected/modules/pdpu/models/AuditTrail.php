<?php

/**
 * This is the model class for table "seg_social_referrals".
 *
 * The followings are the available columns in table 'seg_social_referrals':
 * @property integer $refer_id
 * @property integer $pid
 * @property integer $encounter_nr
 * @property string $refer_dt
 * @property string $refer_to
 * @property string $refer_diagnosis
 * @property string $refer_reason
 * @property string $refer_assessment
 * @property string $refer_intervention
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $history
 * @property string $name
 */
class AuditTrail extends CActiveRecord
{
	public $mss_no;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_social_referrals_audit_trail';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refer_id, date_changed, action_type, login, field_c, old_value, new_value', 'required'),
			array('refer_id', 'integerOnly'=>true),
			array('refer_id, date_changed, action_type, login, field_c, old_value, new_value', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refer_id, date_changed, action_type, login, field_c, old_value, new_value', 'safe', 'on'=>'search'),
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
			'person' => array(self::BELONGS_TO,'Person','pid')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Refer',
			'refer_id' => 'Refer',
			'date_changed' => 'Date Changed',
			'action_type' => 'Action Type',
			'login' => 'User ID',
			'field_c' => 'Field Updated',
			'old_value' => 'Old Value',
			'new_value' => 'New Value',
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

		$criteria->compare('t.refer_id',$this->refer_id,true);
		$criteria->compare('t.date_changed',$this->date_changed,true);
		$criteria->compare('t.action_type',$this->action_type,true);
		//$criteria->compare('refer_dt',$this->refer_dt,true);
		$criteria->compare('t.action_type',$this->action_type,true);
		$criteria->compare('t.login',$this->login,true);
		$criteria->compare('t.field_c',$this->field_c,true);
		$criteria->compare('t.old_value',$this->old_value,true);
		$criteria->compare('t.new_value',$this->new_value,true);

		$criteria->select = 'id, refer_id, date_changed, action_type, login, field_c, old_value, new_value';

		$criteria->order = "refer_dt DESC";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SocialReferrals the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
