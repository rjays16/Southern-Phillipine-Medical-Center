<?php

/**
 * This is the model class for table "seg_industrial_med_chart_follow_up".
 *
 * The followings are the available columns in table 'seg_industrial_med_chart_follow_up':
 * @property integer $id
 * @property string $pid
 * @property string $encounter_nr
 * @property string $refno
 * @property string $date_request
 * @property string $vshtwt
 * @property string $hxpe
 * @property string $remarks
 * @property string $created_id
 * @property string $created_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property integer $is_deleted
 * @property Encounter $encounter
 * @property Person $person
 */
class MedicalChartFollowUp extends CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_industrial_med_chart_follow_up';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_deleted', 'numerical', 'integerOnly'=>true),
			array('pid, encounter_nr, refno', 'length', 'max'=>12),
			// array('vshtwt, hxpe', 'length', 'max'=>25),
			array('create_id, modify_id', 'length', 'max'=>100),
			array('date_request, remarks, create_dt, modify_dt', 'safe'),
			array('vshtwt, hxpe, date_request, remarks',  'required'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pid, encounter_nr, refno, date_request, vshtwt, hxpe, remarks, create_id, create_dt, modify_id, modify_dt, is_deleted', 'safe', 'on'=>'search'),
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
			'encounter' => array(self::BELONGS_TO,'Encounter','encounter_nr'),
			'person' => array(self::BELONGS_TO,'Person','pid','through' => 'encounter')			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'pid' => 'Pid',
			'encounter_nr' => 'Encounter Nr',
			'refno' => 'Refno',
			'date_request' => 'Date Request',
			'vshtwt' => 'VS/HT/WT',
			'hxpe' => 'HX/PE',
			'remarks' => 'Remarks/Diagnosis',
			'created_id' => 'Created',
			'created_dt' => 'Created Dt',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'is_deleted' => 'is_deleted',
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

//		$criteria->compare('id',$this->id);
//		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
//		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('date_request',$this->date_request,true);
//		$criteria->compare('vshtwt',$this->vshtwt,true);
//		$criteria->compare('hxpe',$this->hxpe,true);
//		$criteria->compare('remarks',$this->remarks,true);
//		$criteria->compare('created_id',$this->created_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
//		$criteria->compare('modify_id',$this->modify_id,true);
//		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('is_deleted',0);
		$criteria->order = 'date_request'; //added by Kenneth 04-07-2016
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>false,//added by Kenneth 04-07-2016
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MedicalChartFollowUp the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
