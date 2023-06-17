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
 */
class SocialReferrals extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_social_referrals';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refer_dt, refer_to, refer_diagnosis, refer_reason, refer_assessment, refer_intervention', 'required'),
			array('pid, encounter_nr', 'numerical', 'integerOnly'=>true),
			array('create_id, modify_id', 'length', 'max'=>100),
			array('refer_dt, refer_to, refer_diagnosis, refer_reason, refer_assessment, refer_intervention, create_dt, modify_dt, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refer_id, pid, encounter_nr, refer_dt, refer_to, refer_diagnosis, refer_reason, create_id, create_dt, modify_id, modify_dt, history', 'safe', 'on'=>'search'),
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
			'refer_id' => 'Refer',
			'pid' => 'HRN',
			'encounter_nr' => 'Encounter #',
			'refer_dt' => 'Refer Date',
			'refer_to' => 'Refer To',
			'refer_diagnosis' => 'Final Diagnosis',
			'refer_reason' => 'Reason for Referral',
			'refer_assessment' => 'Assessment',
			'refer_intervention' => 'Recommended Interventions/Remarks',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'history' => 'History',
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

		$criteria->compare('refer_id',$this->refer_id);
		$criteria->compare('pid',$this->pid);
		$criteria->compare('encounter_nr',$this->encounter_nr);
		//$criteria->compare('refer_dt',$this->refer_dt,true);
		$criteria->compare('refer_to',$this->refer_to,true);
		$criteria->compare('refer_diagnosis',$this->refer_diagnosis,true);
		$criteria->compare('refer_reason',$this->refer_reason,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('history',$this->history,true);

		if(!empty($_GET['SocialReferrals']['refer_dt'])) {
			$criteria->addCondition('refer_dt = :refer_dt');
			$criteria->params = array_merge($criteria->params, array(':refer_dt' => date('Y-m-d', strtotime($this->refer_dt))));
			var_dump($criteria->params);
		}
		else{
			$criteria->compare('refer_dt',$this->refer_dt,true);
		}

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
