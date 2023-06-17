<?php

/**
 * This is the model class for table "seg_industrial_cert_med_lto".
 *
 * The followings are the available columns in table 'seg_industrial_cert_med_lto':
 * @property integer $id
 * @property string $control_num
 * @property string $encounter_nr
 * @property string $pid
 * @property string $physician
 * @property string $physical_fit
 * @property string $upper_limbs
 * @property string $lower_limbs
 * @property string $paralyzed_leg
 * @property integer $paraplegic
 * @property string $clear_eyesight
 * @property string $eye_defect
 * @property string $clear_hearing
 * @property string $hearing_defect
 * @property string $other_findings
 * @property string $create_dt
 * @property string $create_id
 * @property string $modify_dt
 * @property string $modify_id
 */
class SegIndustrialCertMedLto extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_industrial_cert_med_lto';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, pid, physician, control_num', 'required'),
			array('paraplegic', 'numerical', 'integerOnly'=>true),
			array('encounter_nr, control_num', 'length', 'max'=>20),
			array('pid, physician', 'length', 'max'=>12),
			array('physical_fit, clear_eyesight, clear_hearing', 'length', 'max'=>3),
			array('upper_limbs, lower_limbs, paralyzed_leg', 'length', 'max'=>5),
			array('eye_defect', 'length', 'max'=>7),
			array('hearing_defect', 'length', 'max'=>6),
			array('create_id, modify_id', 'length', 'max'=>50),
			array('other_findings, create_dt, modify_dt', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, control_num, encounter_nr, pid, physician, physical_fit, upper_limbs, lower_limbs, paralyzed_leg, paraplegic, clear_eyesight, eye_defect, clear_hearing, hearing_defect, other_findings, create_dt, create_id, modify_dt, modify_id', 'safe', 'on'=>'search'),
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
			'control_num' => 'Control Number',
			'encounter_nr' => 'Encounter Nr',
			'pid' => 'Pid',
			'physician' => 'Physician',
			'physical_fit' => 'Physical and mentally fit to drive?',
			'upper_limbs' => 'Upper Limbs',
			'lower_limbs' => 'Lower Limbs',
			'paralyzed_leg' => 'Paralyzed Leg',
			'paraplegic' => 'Paraplegic',
			'clear_eyesight' => 'Has clear eyesight?',
			'eye_defect' => 'Eye Defect',
			'clear_hearing' => 'Has clear hearing?',
			'hearing_defect' => 'Hearing Defect',
			'other_findings' => 'Other Findings',
			'create_dt' => 'Create Dt',
			'create_id' => 'Create',
			'modify_dt' => 'Modify Dt',
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

		$criteria->compare('id',$this->id);
		$criteria->compare('control_num',$this->control_num,true);
		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('physician',$this->physician,true);
		$criteria->compare('physical_fit',$this->physical_fit,true);
		$criteria->compare('upper_limbs',$this->upper_limbs,true);
		$criteria->compare('lower_limbs',$this->lower_limbs,true);
		$criteria->compare('paralyzed_leg',$this->paralyzed_leg,true);
		$criteria->compare('paraplegic',$this->paraplegic);
		$criteria->compare('clear_eyesight',$this->clear_eyesight,true);
		$criteria->compare('eye_defect',$this->eye_defect,true);
		$criteria->compare('clear_hearing',$this->clear_hearing,true);
		$criteria->compare('hearing_defect',$this->hearing_defect,true);
		$criteria->compare('other_findings',$this->other_findings,true);
		$criteria->compare('create_dt',$this->create_dt,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('modify_dt',$this->modify_dt,true);
		$criteria->compare('modify_id',$this->modify_id,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SegIndustrialCertMedLto the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function getDoctors(){
		$sql = "SELECT DISTINCT
				  UPPER(fn_get_personellname_lastfirstmi (a.personell_nr)) AS doctor_name,
				  a.personell_nr,
				  d.name_formal
				FROM
				  care_personell_assignment AS a
				  INNER JOIN care_personell AS ps ON ps.nr = a.personell_nr
				  INNER JOIN care_person AS p ON ps.pid = p.pid
				  INNER JOIN care_department AS d ON a.location_nr = d.nr
				WHERE a.location_type_nr = 1 
				  AND d.admit_inpatient = 1 
				  AND (ps.short_id LIKE 'D%') 
				  AND a.status NOT IN ('hidden', 'inactive', 'void') 
				  ORDER BY p.name_last ASC";

		return Yii::app()->db->createCommand($sql)->queryAll();
	}
}
