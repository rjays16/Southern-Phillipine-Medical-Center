<?php
namespace SegHis\modules\industrialClinic\models;
/**
 * This is the model class for table "seg_industrial_transaction".
 *
 * The followings are the available columns in table 'seg_industrial_transaction':
 * @property string $refno
 * @property string $trxn_date
 * @property string $encounter_nr
 * @property string $pid
 * @property string $purpose_exam
 * @property string $remarks
 * @property integer $agency_charged
 * @property string $agency_id
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $history
 * @property string $status
 */
class IndustrialClinicTransaction extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_industrial_transaction';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('refno', 'required'),
			array('agency_charged', 'numerical', 'integerOnly'=>true),
			array('refno, encounter_nr, pid, agency_id', 'length', 'max'=>12),
			array('purpose_exam', 'length', 'max'=>10),
			array('modify_id, create_id', 'length', 'max'=>35),
			array('status', 'length', 'max'=>1),
			array('trxn_date, remarks, modify_dt, create_dt, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('refno, trxn_date, encounter_nr, pid, purpose_exam, remarks, agency_charged, agency_id, modify_id, modify_dt, create_id, create_dt, history, status', 'safe', 'on'=>'search'),
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
			'refno' => 'Refno',
			'trxn_date' => 'Trxn Date',
			'encounter_nr' => 'Encounter Nr',
			'pid' => 'Pid',
			'purpose_exam' => 'Purpose Exam',
			'remarks' => 'Remarks',
			'agency_charged' => 'Agency Charged',
			'agency_id' => 'Agency',
			'modify_id' => 'Modify',
			'modify_dt' => 'Modify Dt',
			'create_id' => 'Create',
			'create_dt' => 'Create Dt',
			'history' => 'History',
			'status' => 'Status',
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

		$criteria->compare('refno',$this->refno,true);
		$criteria->compare('trxn_date',$this->trxn_date,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('purpose_exam',$this->purpose_exam,true);
		$criteria->compare('remarks',$this->remarks,true);
		$criteria->compare('agency_charged',$this->agency_charged);
		$criteria->compare('agency_id',$this->agency_id,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('status',$this->status,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return IndustrialClinicTransaction the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
