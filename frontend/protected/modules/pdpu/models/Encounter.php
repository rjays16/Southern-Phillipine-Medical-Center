<?php

/**
 * This is the model class for table "care_encounter".
 *
 * The followings are the available columns in table 'care_encounter':
 * @property string $encounter_nr
 * @property string $pid
 * @property string $encounter_date
 * @property integer $encounter_class_nr
 * @property integer $encounter_type
 * @property string $encounter_status
 * @property string $official_receipt_nr
 * @property string $er_opd_diagnosis
 * @property integer $consulting_dept_nr
 * @property integer $consulting_dr_nr
 * @property string $referrer_diagnosis
 * @property string $referrer_recom_therapy
 * @property string $referrer_dr
 * @property string $referrer_dr_other
 * @property string $reason_dr
 * @property string $reason_dr_other
 * @property string $referrer_dept
 * @property string $referrer_institution
 * @property string $referrer_notes
 * @property integer $financial_class_nr
 * @property string $insurance_nr
 * @property string $insurance_firm_id
 * @property integer $insurance_class_nr
 * @property string $insurance_2_nr
 * @property string $insurance_2_firm_id
 * @property integer $guarantor_pid
 * @property integer $contact_pid
 * @property string $contact_relation
 * @property integer $current_ward_nr
 * @property integer $current_room_nr
 * @property integer $in_ward
 * @property string $area
 * @property integer $current_dept_nr
 * @property integer $in_dept
 * @property integer $current_firm_nr
 * @property integer $current_att_dr_nr
 * @property string $consulting_dr
 * @property string $extra_service
 * @property string $admission_dt
 * @property integer $is_discharged
 * @property string $discharge_date
 * @property string $discharge_time
 * @property string $followup_date
 * @property string $followup_responsibility
 * @property string $post_encounter_notes
 * @property string $informant_name
 * @property string $info_address
 * @property string $relation_informant
 * @property string $occupation
 * @property string $source_income
 * @property integer $monthly_income
 * @property integer $nr_dependents
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $is_medico
 * @property integer $is_confidential
 * @property string $POI
 * @property string $TOI
 * @property string $DOI
 * @property integer $is_DOA
 * @property string $is_DOA_reason
 * @property integer $category
 * @property integer $is_served
 * @property string $date_served
 * @property string $clerk_served_by
 * @property string $clerk_served_date
 * @property string $clerk_served_history
 * @property integer $is_maygohome
 * @property string $mgh_setdte
 * @property string $parent_encounter_nr
 * @property string $chief_complaint
 * @property string $received_date
 * @property string $smoker_history
 * @property string $drinker_history
 *
 * The followings are the available model relations:
 * @property Person $p
 * @property EncounterDiagnosis[] $encounterDiagnosises
 * @property EncounterDiagnosticsReport[] $encounterDiagnosticsReports
 * @property EncounterDrgIntern[] $encounterDrgInterns
 * @property EncounterEventSignaller $encounterEventSignaller
 * @property EncounterFinancialClass[] $encounterFinancialClasses
 * @property EncounterImage[] $encounterImages
 * @property EncounterImmunization[] $encounterImmunizations
 * @property EncounterLocation[] $encounterLocations
 * @property EncounterMeasurement[] $encounterMeasurements
 * @property EncounterNotes[] $encounterNotes
 * @property EncounterObstetric $encounterObstetric
 * @property EncounterOp[] $encounterOps
 * @property EncounterPrescription[] $encounterPrescriptions
 * @property EncounterProcedure[] $encounterProcedures
 * @property EncounterSickconfirm[] $encounterSickconfirms
 */
class Encounter extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'care_encounter';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, encounter_type, referrer_diagnosis, referrer_dr, referrer_dr_other, reason_dr, reason_dr_other, referrer_notes, consulting_dr, extra_service, post_encounter_notes, status, history, modify_id, modify_time, create_id, mgh_setdte', 'required'),
			array('encounter_class_nr, encounter_type, consulting_dept_nr, consulting_dr_nr, financial_class_nr, insurance_class_nr, guarantor_pid, contact_pid, current_ward_nr, current_room_nr, in_ward, current_dept_nr, in_dept, current_firm_nr, current_att_dr_nr, is_discharged, monthly_income, nr_dependents, is_medico, is_confidential, is_DOA, category, is_served, is_maygohome', 'numerical', 'integerOnly'=>true),
			array('encounter_nr, pid, occupation, parent_encounter_nr', 'length', 'max'=>12),
			array('encounter_status, contact_relation, source_income, modify_id, create_id', 'length', 'max'=>35),
			array('official_receipt_nr', 'length', 'max'=>200),
			array('referrer_dr, referrer_dr_other, reason_dr, reason_dr_other, insurance_firm_id, insurance_2_firm_id, area, info_address, clerk_served_by', 'length', 'max'=>100),
			array('referrer_dept, referrer_institution, followup_responsibility', 'length', 'max'=>255),
			array('insurance_nr, insurance_2_nr, extra_service, status', 'length', 'max'=>25),
			array('consulting_dr, informant_name', 'length', 'max'=>60),
			array('relation_informant', 'length', 'max'=>30),
			array('POI', 'length', 'max'=>300),
			array('smoker_history, drinker_history', 'length', 'max'=>3),
			array('encounter_date, er_opd_diagnosis, referrer_recom_therapy, admission_dt, discharge_date, discharge_time, followup_date, create_time, TOI, DOI, is_DOA_reason, date_served, clerk_served_date, clerk_served_history, chief_complaint, received_date', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('encounter_nr, pid, encounter_date, encounter_class_nr, encounter_type, encounter_status, official_receipt_nr, er_opd_diagnosis, consulting_dept_nr, consulting_dr_nr, referrer_diagnosis, referrer_recom_therapy, referrer_dr, referrer_dr_other, reason_dr, reason_dr_other, referrer_dept, referrer_institution, referrer_notes, financial_class_nr, insurance_nr, insurance_firm_id, insurance_class_nr, insurance_2_nr, insurance_2_firm_id, guarantor_pid, contact_pid, contact_relation, current_ward_nr, current_room_nr, in_ward, area, current_dept_nr, in_dept, current_firm_nr, current_att_dr_nr, consulting_dr, extra_service, admission_dt, is_discharged, discharge_date, discharge_time, followup_date, followup_responsibility, post_encounter_notes, informant_name, info_address, relation_informant, occupation, source_income, monthly_income, nr_dependents, status, history, modify_id, modify_time, create_id, create_time, is_medico, is_confidential, POI, TOI, DOI, is_DOA, is_DOA_reason, category, is_served, date_served, clerk_served_by, clerk_served_date, clerk_served_history, is_maygohome, mgh_setdte, parent_encounter_nr, chief_complaint, received_date, smoker_history, drinker_history', 'safe', 'on'=>'search'),
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
			'p' => array(self::BELONGS_TO, 'Person', 'pid'),
			'encounterDiagnosises' => array(self::HAS_MANY, 'EncounterDiagnosis', 'encounter_nr'),
			'encounterDiagnosticsReports' => array(self::HAS_MANY, 'EncounterDiagnosticsReport', 'encounter_nr'),
			'encounterDrgInterns' => array(self::HAS_MANY, 'EncounterDrgIntern', 'encounter_nr'),
			'encounterEventSignaller' => array(self::HAS_ONE, 'EncounterEventSignaller', 'encounter_nr'),
			'encounterFinancialClasses' => array(self::HAS_MANY, 'EncounterFinancialClass', 'encounter_nr'),
			'encounterImages' => array(self::HAS_MANY, 'EncounterImage', 'encounter_nr'),
			'encounterImmunizations' => array(self::HAS_MANY, 'EncounterImmunization', 'encounter_nr'),
			'encounterLocations' => array(self::HAS_MANY, 'EncounterLocation', 'encounter_nr'),
			'encounterMeasurements' => array(self::HAS_MANY, 'EncounterMeasurement', 'encounter_nr'),
			'encounterNotes' => array(self::HAS_MANY, 'EncounterNotes', 'encounter_nr'),
			'encounterObstetric' => array(self::HAS_ONE, 'EncounterObstetric', 'encounter_nr'),
			'encounterOps' => array(self::HAS_MANY, 'EncounterOp', 'encounter_nr'),
			'encounterPrescriptions' => array(self::HAS_MANY, 'EncounterPrescription', 'encounter_nr'),
			'encounterProcedures' => array(self::HAS_MANY, 'EncounterProcedure', 'encounter_nr'),
			'encounterSickconfirms' => array(self::HAS_MANY, 'EncounterSickconfirm', 'encounter_nr'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'encounter_nr' => 'Encounter Nr',
			'pid' => 'Pid',
			'encounter_date' => 'Encounter Date',
			'encounter_class_nr' => 'Encounter Class Nr',
			'encounter_type' => 'Encounter Type',
			'encounter_status' => 'Encounter Status',
			'official_receipt_nr' => 'Official Receipt Nr',
			'er_opd_diagnosis' => 'Er Opd Diagnosis',
			'consulting_dept_nr' => 'Consulting Dept Nr',
			'consulting_dr_nr' => 'Consulting Dr Nr',
			'referrer_diagnosis' => 'Referrer Diagnosis',
			'referrer_recom_therapy' => 'Referrer Recom Therapy',
			'referrer_dr' => 'Referrer Dr',
			'referrer_dr_other' => 'Referrer Dr Other',
			'reason_dr' => 'Reason Dr',
			'reason_dr_other' => 'Reason Dr Other',
			'referrer_dept' => 'Referrer Dept',
			'referrer_institution' => 'Referrer Institution',
			'referrer_notes' => 'Referrer Notes',
			'financial_class_nr' => 'Financial Class Nr',
			'insurance_nr' => 'Insurance Nr',
			'insurance_firm_id' => 'Insurance Firm',
			'insurance_class_nr' => 'Insurance Class Nr',
			'insurance_2_nr' => 'Insurance 2 Nr',
			'insurance_2_firm_id' => 'Insurance 2 Firm',
			'guarantor_pid' => 'Guarantor Pid',
			'contact_pid' => 'Contact Pid',
			'contact_relation' => 'Contact Relation',
			'current_ward_nr' => 'Current Ward Nr',
			'current_room_nr' => 'Current Room Nr',
			'in_ward' => 'In Ward',
			'area' => 'Area',
			'current_dept_nr' => 'Current Dept Nr',
			'in_dept' => 'In Dept',
			'current_firm_nr' => 'Current Firm Nr',
			'current_att_dr_nr' => 'Current Att Dr Nr',
			'consulting_dr' => 'Consulting Dr',
			'extra_service' => 'Extra Service',
			'admission_dt' => 'Admission Dt',
			'is_discharged' => 'Is Discharged',
			'discharge_date' => 'Discharge Date',
			'discharge_time' => 'Discharge Time',
			'followup_date' => 'Followup Date',
			'followup_responsibility' => 'Followup Responsibility',
			'post_encounter_notes' => 'Post Encounter Notes',
			'informant_name' => 'Informant Name',
			'info_address' => 'Info Address',
			'relation_informant' => 'Relation Informant',
			'occupation' => 'Occupation',
			'source_income' => 'Source Income',
			'monthly_income' => 'Monthly Income',
			'nr_dependents' => 'Nr Dependents',
			'status' => 'Status',
			'history' => 'History',
			'modify_id' => 'Modify',
			'modify_time' => 'Modify Time',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'is_medico' => 'Is Medico',
			'is_confidential' => 'Is Confidential',
			'POI' => 'Poi',
			'TOI' => 'Toi',
			'DOI' => 'Doi',
			'is_DOA' => 'Is Doa',
			'is_DOA_reason' => 'Is Doa Reason',
			'category' => 'Category',
			'is_served' => 'Is Served',
			'date_served' => 'Date Served',
			'clerk_served_by' => 'Clerk Served By',
			'clerk_served_date' => 'Clerk Served Date',
			'clerk_served_history' => 'Clerk Served History',
			'is_maygohome' => 'Is Maygohome',
			'mgh_setdte' => 'Mgh Setdte',
			'parent_encounter_nr' => 'Parent Encounter Nr',
			'chief_complaint' => 'Chief Complaint',
			'received_date' => 'Received Date',
			'smoker_history' => 'Smoker History',
			'drinker_history' => 'Drinker History',
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

		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('pid',$this->pid,true);
		$criteria->compare('encounter_date',$this->encounter_date,true);
		$criteria->compare('encounter_class_nr',$this->encounter_class_nr);
		$criteria->compare('encounter_type',$this->encounter_type);
		$criteria->compare('encounter_status',$this->encounter_status,true);
		$criteria->compare('official_receipt_nr',$this->official_receipt_nr,true);
		$criteria->compare('er_opd_diagnosis',$this->er_opd_diagnosis,true);
		$criteria->compare('consulting_dept_nr',$this->consulting_dept_nr);
		$criteria->compare('consulting_dr_nr',$this->consulting_dr_nr);
		$criteria->compare('referrer_diagnosis',$this->referrer_diagnosis,true);
		$criteria->compare('referrer_recom_therapy',$this->referrer_recom_therapy,true);
		$criteria->compare('referrer_dr',$this->referrer_dr,true);
		$criteria->compare('referrer_dr_other',$this->referrer_dr_other,true);
		$criteria->compare('reason_dr',$this->reason_dr,true);
		$criteria->compare('reason_dr_other',$this->reason_dr_other,true);
		$criteria->compare('referrer_dept',$this->referrer_dept,true);
		$criteria->compare('referrer_institution',$this->referrer_institution,true);
		$criteria->compare('referrer_notes',$this->referrer_notes,true);
		$criteria->compare('financial_class_nr',$this->financial_class_nr);
		$criteria->compare('insurance_nr',$this->insurance_nr,true);
		$criteria->compare('insurance_firm_id',$this->insurance_firm_id,true);
		$criteria->compare('insurance_class_nr',$this->insurance_class_nr);
		$criteria->compare('insurance_2_nr',$this->insurance_2_nr,true);
		$criteria->compare('insurance_2_firm_id',$this->insurance_2_firm_id,true);
		$criteria->compare('guarantor_pid',$this->guarantor_pid);
		$criteria->compare('contact_pid',$this->contact_pid);
		$criteria->compare('contact_relation',$this->contact_relation,true);
		$criteria->compare('current_ward_nr',$this->current_ward_nr);
		$criteria->compare('current_room_nr',$this->current_room_nr);
		$criteria->compare('in_ward',$this->in_ward);
		$criteria->compare('area',$this->area,true);
		$criteria->compare('current_dept_nr',$this->current_dept_nr);
		$criteria->compare('in_dept',$this->in_dept);
		$criteria->compare('current_firm_nr',$this->current_firm_nr);
		$criteria->compare('current_att_dr_nr',$this->current_att_dr_nr);
		$criteria->compare('consulting_dr',$this->consulting_dr,true);
		$criteria->compare('extra_service',$this->extra_service,true);
		$criteria->compare('admission_dt',$this->admission_dt,true);
		$criteria->compare('is_discharged',$this->is_discharged);
		$criteria->compare('discharge_date',$this->discharge_date,true);
		$criteria->compare('discharge_time',$this->discharge_time,true);
		$criteria->compare('followup_date',$this->followup_date,true);
		$criteria->compare('followup_responsibility',$this->followup_responsibility,true);
		$criteria->compare('post_encounter_notes',$this->post_encounter_notes,true);
		$criteria->compare('informant_name',$this->informant_name,true);
		$criteria->compare('info_address',$this->info_address,true);
		$criteria->compare('relation_informant',$this->relation_informant,true);
		$criteria->compare('occupation',$this->occupation,true);
		$criteria->compare('source_income',$this->source_income,true);
		$criteria->compare('monthly_income',$this->monthly_income);
		$criteria->compare('nr_dependents',$this->nr_dependents);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('is_medico',$this->is_medico);
		$criteria->compare('is_confidential',$this->is_confidential);
		$criteria->compare('POI',$this->POI,true);
		$criteria->compare('TOI',$this->TOI,true);
		$criteria->compare('DOI',$this->DOI,true);
		$criteria->compare('is_DOA',$this->is_DOA);
		$criteria->compare('is_DOA_reason',$this->is_DOA_reason,true);
		$criteria->compare('category',$this->category);
		$criteria->compare('is_served',$this->is_served);
		$criteria->compare('date_served',$this->date_served,true);
		$criteria->compare('clerk_served_by',$this->clerk_served_by,true);
		$criteria->compare('clerk_served_date',$this->clerk_served_date,true);
		$criteria->compare('clerk_served_history',$this->clerk_served_history,true);
		$criteria->compare('is_maygohome',$this->is_maygohome);
		$criteria->compare('mgh_setdte',$this->mgh_setdte,true);
		$criteria->compare('parent_encounter_nr',$this->parent_encounter_nr,true);
		$criteria->compare('chief_complaint',$this->chief_complaint,true);
		$criteria->compare('received_date',$this->received_date,true);
		$criteria->compare('smoker_history',$this->smoker_history,true);
		$criteria->compare('drinker_history',$this->drinker_history,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Encounter the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
