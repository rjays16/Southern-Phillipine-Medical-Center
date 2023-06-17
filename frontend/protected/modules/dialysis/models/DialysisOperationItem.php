<?php
namespace SegHis\modules\dialysis\models;

use SegHis\models\CaseRatePackage;
use SegHis\models\Hospital;
use SegHis\models\Operation;

/**
 * This is the model class for table "seg_misc_ops_details".
 *
 * The followings are the available columns in table 'seg_misc_ops_details':
 * @property string $refno
 * @property string $ops_code
 * @property integer $entry_no
 * @property string $op_date
 * @property string $rvu
 * @property double $multiplier
 * @property double $chrg_amnt
 * @property string $group_code
 * @property string $laterality
 * @property string $cataract_code
 * @property integer $num_sessions
 * @property string $special_dates
 * @property string $description
 * @property string $lmp_date
 * @property string $prenatal_dates
 *
 * The followings are the available model relations:
 */
class DialysisOperationItem extends \CareActiveRecord
{

    const CODE_HEMODIALYSIS = '90935';
    const HOUSE_CASE_PCF = 40;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_misc_ops_details';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno, ops_code, entry_no, op_date, rvu, multiplier, chrg_amnt', 'required'),
            array('entry_no, num_sessions', 'numerical', 'integerOnly' => true),
            array('multiplier, chrg_amnt', 'numerical'),
            array('refno, ops_code', 'length', 'max' => 12),
            array('rvu', 'length', 'max' => 10),
            array('group_code', 'length', 'max' => 4),
            array('laterality', 'length', 'max' => 1),
            array('cataract_code', 'length', 'max' => 20),
            array('special_dates, description, prenatal_dates, group_code, laterality, cataract_code, lmp_date', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('refno, ops_code, entry_no, op_date, rvu, multiplier, chrg_amnt, group_code, laterality, cataract_code, num_sessions, special_dates, description, lmp_date, prenatal_dates', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'refno' => 'Refno',
            'ops_code' => 'Ops Code',
            'entry_no' => 'Entry No',
            'op_date' => 'Op Date',
            'rvu' => 'Rvu',
            'multiplier' => 'Multiplier',
            'chrg_amnt' => 'Chrg Amnt',
            'group_code' => 'Group Code',
            'laterality' => 'Laterality',
            'cataract_code' => 'Cataract Code',
            'num_sessions' => 'Num Sessions',
            'special_dates' => 'Special Dates',
            'description' => 'Description',
            'lmp_date' => 'Lmp Date',
            'prenatal_dates' => 'Prenatal Dates',
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

        $criteria->compare('refno', $this->refno, true);
        $criteria->compare('ops_code', $this->ops_code, true);
        $criteria->compare('entry_no', $this->entry_no);
        $criteria->compare('op_date', $this->op_date, true);
        $criteria->compare('rvu', $this->rvu, true);
        $criteria->compare('multiplier', $this->multiplier);
        $criteria->compare('chrg_amnt', $this->chrg_amnt);
        $criteria->compare('group_code', $this->group_code, true);
        $criteria->compare('laterality', $this->laterality, true);
        $criteria->compare('cataract_code', $this->cataract_code, true);
        $criteria->compare('num_sessions', $this->num_sessions);
        $criteria->compare('special_dates', $this->special_dates, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('lmp_date', $this->lmp_date, true);
        $criteria->compare('prenatal_dates', $this->prenatal_dates, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DialysisOperationItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return CaseRatePackage
     */
    public static function getHemodialysisInfo($encounter_dt)
    {
        return CaseRatePackage::model()->findByAttributes(array(
            'code' => self::CODE_HEMODIALYSIS
        ),
        "STR_TO_DATE(date_from, '%Y-%m-%d') <= STR_TO_DATE('".$encounter_dt."', '%Y-%m-%d')
        AND STR_TO_DATE(date_to, '%Y-%m-%d') >= STR_TO_DATE('".$encounter_dt."', '%Y-%m-%d')");
    }

    public static function getHemodialysisOperationInfo()
    {
        return Operation::model()->findByPk(self::CODE_HEMODIALYSIS);
    }

    /**
     * @param $referenceNr
     * @return DialysisOperationItem
     */
    public static function findLastEntryByReferenceNo($referenceNr)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'refno' => $referenceNr
        ));
        $criteria->order = 'entry_no DESC';
        $criteria->limit = 1;
        return DialysisOperationItem::model()->find($criteria);
    }

}