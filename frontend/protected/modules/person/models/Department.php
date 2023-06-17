<?php
namespace SegHis\modules\person\models;

/**
 * This is the model class for table "care_department".
 *
 * The followings are the available columns in table 'care_department':
 * @property integer $nr
 * @property string $id
 * @property string $type
 * @property string $name_formal
 * @property string $name_short
 * @property string $name_alternate
 * @property string $LD_var
 * @property string $description
 * @property integer $admit_inpatient
 * @property integer $admit_outpatient
 * @property integer $has_oncall_doc
 * @property integer $has_oncall_nurse
 * @property integer $does_surgery
 * @property integer $this_institution
 * @property integer $is_sub_dept
 * @property integer $parent_dept_nr
 * @property string $work_hours
 * @property string $consult_hours
 * @property integer $is_inactive
 * @property integer $sort_order
 * @property string $address
 * @property string $sig_line
 * @property string $sig_stamp
 * @property string $logo_mime_type
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $for_male_only
 * @property integer $for_female_only
 * @property integer $for_child_only
 * @property integer $child_age_limit
 * @property integer $clinic_visibility
 *
 */

class Department extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_department';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type, description, modify_time', 'required'),
            array('admit_inpatient, admit_outpatient, has_oncall_doc, has_oncall_nurse, does_surgery, this_institution, is_sub_dept, parent_dept_nr, is_inactive, sort_order, for_male_only, for_female_only, for_child_only, child_age_limit, clinic_visibility', 'numerical', 'integerOnly'=>true),
            array('id, name_formal, sig_line', 'length', 'max'=>60),
            array('type, status', 'length', 'max'=>25),
            array('name_short, LD_var, modify_id, create_id', 'length', 'max'=>35),
            array('name_alternate', 'length', 'max'=>225),
            array('work_hours, consult_hours', 'length', 'max'=>100),
            array('logo_mime_type', 'length', 'max'=>5),
            array('address, sig_stamp, history, create_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('nr, id, type, name_formal, name_short, name_alternate, LD_var, description, admit_inpatient, admit_outpatient, has_oncall_doc, has_oncall_nurse, does_surgery, this_institution, is_sub_dept, parent_dept_nr, work_hours, consult_hours, is_inactive, sort_order, address, sig_line, sig_stamp, logo_mime_type, status, history, modify_id, modify_time, create_id, create_time, for_male_only, for_female_only, for_child_only, child_age_limit, clinic_visibility', 'safe', 'on'=>'search'),
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
            'areases' => array(self::HAS_MANY, 'Areas', 'dept_nr'),
            'hospEods' => array(self::HAS_MANY, 'HospEod', 'area_nr'),
            'hospInventories' => array(self::HAS_MANY, 'HospInventory', 'area_nr'),
            'orRequests' => array(self::HAS_MANY, 'OrRequest', 'dept_nr'),
            'orSchedulerDeckingLimit' => array(self::HAS_ONE, 'OrSchedulerDeckingLimit', 'dept_nr'),
            'reptbls' => array(self::HAS_MANY, 'Reptbl', 'rep_dept_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'nr' => 'Nr',
            'id' => 'ID',
            'type' => 'Type',
            'name_formal' => 'Name Formal',
            'name_short' => 'Name Short',
            'name_alternate' => 'Name Alternate',
            'LD_var' => 'Ld Var',
            'description' => 'Description',
            'admit_inpatient' => 'Admit Inpatient',
            'admit_outpatient' => 'Admit Outpatient',
            'has_oncall_doc' => 'Has Oncall Doc',
            'has_oncall_nurse' => 'Has Oncall Nurse',
            'does_surgery' => 'Does Surgery',
            'this_institution' => 'This Institution',
            'is_sub_dept' => 'Is Sub Dept',
            'parent_dept_nr' => 'Parent Dept Nr',
            'work_hours' => 'Work Hours',
            'consult_hours' => 'Consult Hours',
            'is_inactive' => 'Is Inactive',
            'sort_order' => 'Sort Order',
            'address' => 'Address',
            'sig_line' => 'Sig Line',
            'sig_stamp' => 'Sig Stamp',
            'logo_mime_type' => 'Logo Mime Type',
            'status' => 'Status',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'for_male_only' => 'For Male Only',
            'for_female_only' => 'For Female Only',
            'for_child_only' => 'For Child Only',
            'child_age_limit' => 'Child Age Limit',
            'clinic_visibility' => 'Clinic Visibility',
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

        $criteria->compare('nr',$this->nr);
        $criteria->compare('id',$this->id,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('name_formal',$this->name_formal,true);
        $criteria->compare('name_short',$this->name_short,true);
        $criteria->compare('name_alternate',$this->name_alternate,true);
        $criteria->compare('LD_var',$this->LD_var,true);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('admit_inpatient',$this->admit_inpatient);
        $criteria->compare('admit_outpatient',$this->admit_outpatient);
        $criteria->compare('has_oncall_doc',$this->has_oncall_doc);
        $criteria->compare('has_oncall_nurse',$this->has_oncall_nurse);
        $criteria->compare('does_surgery',$this->does_surgery);
        $criteria->compare('this_institution',$this->this_institution);
        $criteria->compare('is_sub_dept',$this->is_sub_dept);
        $criteria->compare('parent_dept_nr',$this->parent_dept_nr);
        $criteria->compare('work_hours',$this->work_hours,true);
        $criteria->compare('consult_hours',$this->consult_hours,true);
        $criteria->compare('is_inactive',$this->is_inactive);
        $criteria->compare('sort_order',$this->sort_order);
        $criteria->compare('address',$this->address,true);
        $criteria->compare('sig_line',$this->sig_line,true);
        $criteria->compare('sig_stamp',$this->sig_stamp,true);
        $criteria->compare('logo_mime_type',$this->logo_mime_type,true);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('for_male_only',$this->for_male_only);
        $criteria->compare('for_female_only',$this->for_female_only);
        $criteria->compare('for_child_only',$this->for_child_only);
        $criteria->compare('child_age_limit',$this->child_age_limit);
        $criteria->compare('clinic_visibility',$this->clinic_visibility);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Department the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getDepartment($dept_nr)
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('nr=:dept_nr');
        $criteria->params = array('dept_nr' => $dept_nr);

        $model = $this->find($criteria);

        return $model->name_formal;
    }
}