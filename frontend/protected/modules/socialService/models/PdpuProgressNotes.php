<?php

/**
 * This is the model class for table "seg_pdpu_progress_notes".
 *
 * The followings are the available columns in table 'seg_pdpu_progress_notes':
 * @property integer $notes_id
 * @property string $pid
 * @property string $encounter_nr
 * @property string $progress_date_time
 * @property string $informant
 * @property string $venue
 * @property string $purpose_reasons
 * @property string $action_taken
 * @property string $problem_encountered
 * @property string $plan
 * @property string $create_id
 * @property string $create_dt
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $history
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property SegPdpuProgressNAuditTrail[] $segPdpuProgressNAuditTrails
 * @property CareEncounter $encounterNr
 * @property CarePerson $p
 */
class PdpuProgressNotes extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public $fullname;

    public function tableName()
    {
        return 'seg_pdpu_progress_notes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pid, progress_date_time, informant, venue, purpose_reasons, action_taken, problem_encountered, plan', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('pid, encounter_nr', 'length', 'max'=>12),
            array('informant, venue', 'length', 'max'=>50),
            array('create_id, modify_id', 'length', 'max'=>35),
            array('progress_date_time, purpose_reasons, action_taken, problem_encountered, plan, create_dt, modify_dt, history', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('notes_id, pid, encounter_nr, progress_date_time, informant, venue, purpose_reasons, action_taken, problem_encountered, plan, create_id, create_dt, modify_id, modify_dt, history, is_deleted', 'safe', 'on'=>'search'),
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
            'segPdpuProgressNAuditTrails' => array(self::HAS_MANY, 'SegPdpuProgressNAuditTrail', 'notes_id'),
            'encounters' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'person' => array(self::BELONGS_TO, 'Person', 'pid'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'notes_id' => 'Notes',
            'pid' => 'HRN',
            'encounter_nr' => 'Encounter No',
            'progress_date_time' => 'Date and Time',
            'informant' => 'Informant',
            'venue' => 'Venue',
            'purpose_reasons' => 'Purpose/Objectives',
            'action_taken' => 'Action Taken',
            'problem_encountered' => 'Problem Encountered',
            'plan' => 'Plan',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'history' => 'History',
            'is_deleted' => 'Is Deleted',
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

        $criteria->compare('notes_id',$this->notes_id);
        $criteria->compare('t.pid',$this->pid,true);
        $criteria->compare('t.encounter_nr',$this->encounter_nr,true);
        // $criteria->compare('t.progress_date_time',$this->progress_date_time,true);
        $criteria->compare('informant',$this->informant,true);
        $criteria->compare('venue',$this->venue,true);
        $criteria->compare('purpose_reasons',$this->purpose_reasons,true);
        $criteria->compare('action_taken',$this->action_taken,true);
        $criteria->compare('problem_encountered',$this->problem_encountered,true);
        $criteria->compare('plan',$this->plan,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_dt',$this->modify_dt,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->select=array("t.*","IF(name_middle='' OR name_middle=null,CONCAT(name_last,',',name_first,' ',UPPER(SUBSTRING(name_middle,1,1)),'.'),CONCAT(name_last,',',name_first)) AS fullname");
        // $criteria->join="INNER JOIN seg_soa_diagnosis sd on t.encounter_nr=sd.encounter_nr";
    
        $criteria->addCondition('is_deleted = 0');
        if(!empty($_GET['PdpuProgressNotes']['progress_date_time'])) {
            $time = strtotime($_GET['PdpuProgressNotes']['progress_date_time']);
            $newformat = date('Y-m-d H:i',$time);
            $temp=explode(' ',$newformat);
            if($temp[1]=="00:00") {       //Search Date Only
                $newformat = date('Y-m-d',$time);
                }   
            else {                          //Search Date & time.
                $dateTime = $_GET['PdpuProgressNotes']['progress_date_time'];
                $dateTime = explode(' ', $dateTime);
                if(empty($dateTime[2])){
                    if (strtotime($dateTime[1]) >= strtotime("12:00") || strtotime($dateTime[1]) < strtotime("23:59")) {
                        $convertPMFormat = strtotime("+12 hours",strtotime($newformat));
                        $isPMFormat = date('Y-m-d H:i',$convertPMFormat);
                        $criteria->addCondition('progress_date_time LIKE "%'.$isPMFormat.'%" ');
                         
                    }

                }

            }

            $con = $isPMFormat ? 'OR' : 'AND';
            $criteria->addCondition('progress_date_time LIKE "%'.$newformat.'%"', $con);
        }
        

        $criteria->with = array('person' => 
            array(
                'select' => 'name_first,name_last,name_middle',
                'joinType' => 'INNER JOIN'
            )
        );
      
        if(!empty($_GET['PdpuProgressNotes']['fullname'])){
            $name=array();
            $name=explode(",",$_GET['PdpuProgressNotes']['fullname']);
            $criteria->addCondition('name_last LIKE "'.$name[0].'%"', "AND");
            $criteria->addCondition('name_first LIKE "'.$name[1].'%"', "AND");
        }

       
        if($_GET['pdpup'] == 1){
            $criteria->addCondition("t.encounter_nr = '".$_GET['encounter_nr']."'");
        }
     
        $criteria->order = "progress_date_time DESC";

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PdpuProgressNotes the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}