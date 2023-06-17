<?php
namespace SegHis\modules\phic\models;
/**
 * This is the model class for table "seg_additional_limit".
 *
 * The followings are the available columns in table 'seg_additional_limit':
 * @property string $encounter_nr
 * @property double $amountmed
 * @property double $amountxlo
 * @property string $create_id
 * @property string $create_dt
 * @property integer $is_deleted
 */
class AdditionalLimit extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_additional_limit';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr, create_id, create_dt', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('amountmed, amountxlo', 'numerical'),
            array('encounter_nr', 'length', 'max'=>12),
            array('create_id', 'length', 'max'=>35),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('encounter_nr, amountmed, amountxlo, create_id, create_dt, is_deleted', 'safe', 'on'=>'search'),
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
            'encounter_nr' => 'Encounter Nr',
            'amountmed' => 'Amountmed',
            'amountxlo' => 'Amountxlo',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
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

        $criteria=new \CDbCriteria;

        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('amountmed',$this->amountmed);
        $criteria->compare('amountxlo',$this->amountxlo);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_dt',$this->create_dt,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdditionalLimit the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /***
     * 
     */
    public static function getXLOLimitAdded($encounter_nr)
    {
        $coverage = \Yii::app()->db->createCommand()
            ->select('SUM(amountxlo) t_xlo')
            ->from('seg_additional_limit sal')
            ->where('is_deleted IS NULL AND encounter_nr = :enc_nr', array(':enc_nr' => $encounter_nr))
            ->queryRow();
        return (!empty($coverage) && isset($coverage['t_xlo'])) ? $coverage['t_xlo'] : 0.00;
    }
}
