<?php

/**
 * This is the model class for table "seg_social_referrals_audit_trail".
 *
 * The followings are the available columns in table 'seg_social_referrals_audit_trail':
 * @property integer $id
 * @property integer $refer_id
 * @property string $date_changed
 * @property string $action_type
 * @property string $login
 * @property string $field_c
 * @property string $old_value
 * @property string $new_value
 * @property integer $is_visible
 */
class SocialReferralsAuditTrail extends CareActiveRecord
{
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
			array('refer_id, date_changed', 'required'),
			array('refer_id', 'numerical', 'integerOnly'=>true),
			array('action_type', 'length', 'max'=>50),
			array('login', 'length', 'max'=>25),
			array('remarks_value', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, refer_id, date_changed, action_type, login, remarks_value', 'safe', 'on'=>'search'),
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
			'refer_id' => 'Refer',
			'date_changed' => 'Date Changed',
			'action_type' => 'Action Type',
			'login' => 'Login',
			'remarks_value' => 'Field C',
			'is_visible' => 'Is Visible',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('refer_id',$this->refer_id,true);
		$criteria->compare('date_changed',$this->date_changed,true);
		$criteria->compare('action_type',$this->action_type,true);
		$criteria->compare('login',$this->login,true);
		$criteria->compare('remarks_value',$this->remarks_value,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SegSocialReferralsAuditTrail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
