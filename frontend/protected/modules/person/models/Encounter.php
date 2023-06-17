<?php
namespace SegHis\modules\person\models;

use SegHis\modules\industrialClinic\models\IndustrialClinicTransaction;

\Yii::import('billing.models.SoaDiagnosis');

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
 * @property Person $person
 * @property \Department $department
 * @property \EncounterType $type
 * @property \EncounterDisposition $disposition
 * @property \EncounterResult $result
 * @property \EncounterMemcategory $encounterMemCategory
 * @property \SegHis\modules\admission\models\assignment\Ward $ward
 */
class Encounter extends \CareActiveRecord
{

    const ENCOUNTER_TYPE_ER = 1;
    const ENCOUNTER_TYPE_OPD = 2;
    const ENCOUNTER_TYPE_DIRECT_INPATIENT = 3;
    const ENCOUNTER_TYPE_ER_INPATIENT = 4;
    const ENCOUNTER_TYPE_DIALYSIS = 5;
    const ENCOUNTER_TYPE_IC = 6;

    public static $inActiveStatusCodes = array(
        'deleted',
        'hidden',
        'inactive',
        'void'
    );

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
            array('encounter_class_nr, encounter_type, consulting_dept_nr, consulting_dr_nr, financial_class_nr, insurance_class_nr, guarantor_pid, contact_pid, current_ward_nr, current_room_nr, in_ward, current_dept_nr, in_dept, current_firm_nr, current_att_dr_nr, is_discharged, monthly_income, nr_dependents, is_medico, is_confidential, is_DOA, category, is_served, is_maygohome', 'numerical', 'integerOnly' => true),
            array('encounter_nr, pid, occupation, parent_encounter_nr', 'length', 'max' => 12),
            array('encounter_status, contact_relation, source_income, modify_id, create_id', 'length', 'max' => 35),
            array('official_receipt_nr', 'length', 'max' => 200),
            array('referrer_dr, referrer_dr_other, reason_dr, reason_dr_other, insurance_firm_id, insurance_2_firm_id, area, info_address, clerk_served_by', 'length', 'max' => 100),
            array('referrer_dept, referrer_institution, followup_responsibility', 'length', 'max' => 255),
            array('insurance_nr, insurance_2_nr, extra_service, status', 'length', 'max' => 25),
            array('consulting_dr, informant_name', 'length', 'max' => 60),
            array('relation_informant', 'length', 'max' => 30),
            array('POI', 'length', 'max' => 300),
            array('smoker_history, drinker_history', 'length', 'max' => 3),
            array('encounter_date, er_opd_diagnosis, referrer_recom_therapy, admission_dt, discharge_date, discharge_time, followup_date, create_time, TOI, DOI, is_DOA_reason, date_served, clerk_served_date, clerk_served_history, chief_complaint, received_date', 'safe'),
            array('encounter_nr, pid, encounter_date, encounter_class_nr, encounter_type, encounter_status, official_receipt_nr, er_opd_diagnosis, consulting_dept_nr, consulting_dr_nr, referrer_diagnosis, referrer_recom_therapy, referrer_dr, referrer_dr_other, reason_dr, reason_dr_other, referrer_dept, referrer_institution, referrer_notes, financial_class_nr, insurance_nr, insurance_firm_id, insurance_class_nr, insurance_2_nr, insurance_2_firm_id, guarantor_pid, contact_pid, contact_relation, current_ward_nr, current_room_nr, in_ward, area, current_dept_nr, in_dept, current_firm_nr, current_att_dr_nr, consulting_dr, extra_service, admission_dt, is_discharged, discharge_date, discharge_time, followup_date, followup_responsibility, post_encounter_notes, informant_name, info_address, relation_informant, occupation, source_income, monthly_income, nr_dependents, status, history, modify_id, modify_time, create_id, create_time, is_medico, is_confidential, POI, TOI, DOI, is_DOA, is_DOA_reason, category, is_served, date_served, clerk_served_by, clerk_served_date, clerk_served_history, is_maygohome, mgh_setdte, parent_encounter_nr, chief_complaint, received_date, smoker_history, drinker_history', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'person' => array(self::BELONGS_TO, 'SegHis\modules\person\models\Person', 'pid'),
            'department' => array(self::BELONGS_TO, 'Department', 'consulting_dept_nr'),
            'type' => array(self::BELONGS_TO, 'EncounterType', 'encounter_type'),
            'disposition' => array(self::HAS_ONE, 'EncounterDisposition', 'encounter_nr'),
            'result' => array(self::HAS_ONE, 'EncounterResult', 'encounter_nr'),
            'encounterMemCategory' => array(self::HAS_ONE, 'EncounterMemcategory', 'encounter_nr'),
            'ward' => array(self::HAS_ONE, 'SegHis\modules\admission\models\assignment\Ward', array('nr' => 'current_ward_nr')),
            'soadiagnosis' => array(self::HAS_ONE, 'SoaDiagnosis', 'encounter_nr'),
            'classification' => array(self::HAS_ONE, 'SegHis\modules\socialService\models\EncounterCharityGrant', 'encounter_nr'),
            'dept' => array(self::BELONGS_TO, 'SegHis\modules\person\models\Department', 'current_dept_nr')
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
     * @return \CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new \CDbCriteria;
        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Encounter the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getEncounterTypeDescription()
    {
        return self::_getEncounterTypeDescription($this->encounter_type);
    }

    /**
     * @param $encounterType
     * @return string|null
     */
    # updated by carriane 10/24/17; included ipbm encoutner types
    public static function _getEncounterTypeDescription($encounterType)
    {
        define('IPBMOPD_enc', 14);
        define('IPBMIPD_enc', 13);

        $encounterTypes = array(
            1 => 'ER',
            2 => 'OPD',
            3 => 'INPATIENT (ER)',
            4 => 'INPATIENT (ER)',
            5 => 'RDU',
            6 => 'HSSC',
            12 => 'Well-Baby',
            IPBMIPD_enc => 'IPBM - IPD',
            IPBMOPD_enc => 'IPBM - OPD',

        );
        if (array_key_exists($encounterType, $encounterTypes)) {
            return $encounterTypes[$encounterType];
        } else {
            return null;
        }
    }

    /**
     * @param $pid
     * @param null $columns
     * @return null|Encounter
     */
    public static function findActiveEncounterNrByPid($pid, $columns = null)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'pid' => $pid,
            'is_discharged' => 0
        ));
        $criteria->addCondition('encounter_status <> "cancelled"');
        $criteria->addNotInCondition('status', static::$inActiveStatusCodes);
        if ($columns)
            $criteria->select = $columns;
        return Encounter::model()->findAll($criteria);
    }

    public function isChargedToCompany()
    {
        if ($this->encounter_type == Encounter::ENCOUNTER_TYPE_IC)
            return false;

        /* @var $icTransaction IndustrialClinicTransaction */
        $icTransaction = IndustrialClinicTransaction::model()->findByAttributes(array(
            'encounter_nr' => $this->encounter_nr
        ));
        return $icTransaction->agency_charged == 1;
    }

    public function isInPatient()
    {
        return $this->encounter_type == self::ENCOUNTER_TYPE_DIRECT_INPATIENT ||
        $this->encounter_type == self::ENCOUNTER_TYPE_ER_INPATIENT;
    }

    public function getDiagnosis($caseNumber) {
        global $db;
        $sql = "SELECT `final_diagnosis` FROM `seg_soa_diagnosis` WHERE `encounter_nr`= '".$caseNumber."' ";
        if($result=$db->Execute($sql)) {
            if($result->RecordCount()) {
                if ($row = $result->FetchRow()) {
                        $final_diagnosis = $row["final_diagnosis"];

                }
            }
        }
        return $final_diagnosis;
    }

}