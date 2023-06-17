<?php

/**
 * This is the model class for table "care_encounter_notes".
 *
 * The followings are the available columns in table 'care_encounter_notes':
 * @property string $nr
 * @property string $encounter_nr
 * @property integer $type_nr
 * @property string $notes
 * @property string $code
 * @property string $short_notes
 * @property string $aux_notes
 * @property string $ref_notes_nr
 * @property string $personell_nr
 * @property string $personell_name
 * @property integer $send_to_pid
 * @property string $send_to_name
 * @property string $date
 * @property string $time
 * @property string $location_type
 * @property integer $location_type_nr
 * @property integer $location_nr
 * @property string $location_id
 * @property string $ack_short_id
 * @property string $date_ack
 * @property string $date_checked
 * @property string $date_printed
 * @property integer $send_by_mail
 * @property integer $send_by_email
 * @property integer $send_by_fax
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property string $nRemarks
 * @property string $nIVF
 * @property double $nHeight
 * @property double $nWeight
 * @property string $nDiet
 * @property integer $is_deleted
 * @property double $nBmi
 * @property integer $is_vital
 */

class CareEncounterNotes extends CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_encounter_notes';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('type_nr, send_to_pid, location_type_nr, location_nr, send_by_mail, send_by_email, send_by_fax, is_deleted, is_vital', 'numerical', 'integerOnly'=>true),
            array('nHeight, nWeight, nBmi', 'numerical'),
            array('encounter_nr', 'length', 'max'=>12),
            array('code, ref_notes_nr, personell_nr, ack_short_id', 'length', 'max'=>10),
            array('short_notes, status, nDiet', 'length', 'max'=>25),
            array('aux_notes', 'length', 'max'=>255),
            array('personell_name, send_to_name, location_id', 'length', 'max'=>60),
            array('location_type, modify_id, create_id', 'length', 'max'=>35),
            array('date, time, date_ack, date_checked, date_printed, create_time, nRemarks, nIVF', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('nr, encounter_nr, type_nr, notes, code, short_notes, aux_notes, ref_notes_nr, personell_nr, personell_name, send_to_pid, send_to_name, date, time, location_type, location_type_nr, location_nr, location_id, ack_short_id, date_ack, date_checked, date_printed, send_by_mail, send_by_email, send_by_fax, status, history, modify_id, modify_time, create_id, create_time, nRemarks, nIVF, nHeight, nWeight, nDiet, is_deleted, nBmi, is_vital', 'safe', 'on'=>'search'),
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
            'nr' => 'Nr',
            'encounter_nr' => 'Encounter Nr',
            'type_nr' => 'Type Nr',
            'notes' => 'Notes',
            'code' => 'Code',
            'short_notes' => 'Short Notes',
            'aux_notes' => 'Aux Notes',
            'ref_notes_nr' => 'Ref Notes Nr',
            'personell_nr' => 'Personell Nr',
            'personell_name' => 'Personell Name',
            'send_to_pid' => 'Send To Pid',
            'send_to_name' => 'Send To Name',
            'date' => 'Date',
            'time' => 'Time',
            'location_type' => 'Location Type',
            'location_type_nr' => 'Location Type Nr',
            'location_nr' => 'Location Nr',
            'location_id' => 'Location',
            'ack_short_id' => 'Ack Short',
            'date_ack' => 'Date Ack',
            'date_checked' => 'Date Checked',
            'date_printed' => 'Date Printed',
            'send_by_mail' => 'Send By Mail',
            'send_by_email' => 'Send By Email',
            'send_by_fax' => 'Send By Fax',
            'status' => 'Status',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'nRemarks' => 'N Remarks',
            'nIVF' => 'N Ivf',
            'nHeight' => 'N Height',
            'nWeight' => 'N Weight',
            'nDiet' => 'N Diet',
            'is_deleted' => 'Is Deleted',
            'nBmi' => 'N Bmi',
            'is_vital' => 'Is Vital',
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

        $criteria->compare('nr',$this->nr,true);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('type_nr',$this->type_nr);
        $criteria->compare('notes',$this->notes,true);
        $criteria->compare('code',$this->code,true);
        $criteria->compare('short_notes',$this->short_notes,true);
        $criteria->compare('aux_notes',$this->aux_notes,true);
        $criteria->compare('ref_notes_nr',$this->ref_notes_nr,true);
        $criteria->compare('personell_nr',$this->personell_nr,true);
        $criteria->compare('personell_name',$this->personell_name,true);
        $criteria->compare('send_to_pid',$this->send_to_pid);
        $criteria->compare('send_to_name',$this->send_to_name,true);
        $criteria->compare('date',$this->date,true);
        $criteria->compare('time',$this->time,true);
        $criteria->compare('location_type',$this->location_type,true);
        $criteria->compare('location_type_nr',$this->location_type_nr);
        $criteria->compare('location_nr',$this->location_nr);
        $criteria->compare('location_id',$this->location_id,true);
        $criteria->compare('ack_short_id',$this->ack_short_id,true);
        $criteria->compare('date_ack',$this->date_ack,true);
        $criteria->compare('date_checked',$this->date_checked,true);
        $criteria->compare('date_printed',$this->date_printed,true);
        $criteria->compare('send_by_mail',$this->send_by_mail);
        $criteria->compare('send_by_email',$this->send_by_email);
        $criteria->compare('send_by_fax',$this->send_by_fax);
        $criteria->compare('status',$this->status,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('nRemarks',$this->nRemarks,true);
        $criteria->compare('nIVF',$this->nIVF,true);
        $criteria->compare('nHeight',$this->nHeight);
        $criteria->compare('nWeight',$this->nWeight);
        $criteria->compare('nDiet',$this->nDiet,true);
        $criteria->compare('is_deleted',$this->is_deleted);
        $criteria->compare('nBmi',$this->nBmi);
        $criteria->compare('is_vital',$this->is_vital);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CareEncounterNotes the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}