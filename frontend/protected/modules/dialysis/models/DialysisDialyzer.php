<?php
namespace SegHis\modules\dialysis\models;
/**
 * This is the model class for table "seg_dialysis_dialyzer".
 *
 * The followings are the available columns in table 'seg_dialysis_dialyzer':
 * @property string $dialyzer_serial_nr
 * @property string $dialyzer_id
 * @property string $dialyzer_type
 *
 * The followings are the available model relations:
 * @property DialysisTransaction[] $dialysisTransactions
 * @property DialysisMiscService $dialyzerInfo
 */
class DialysisDialyzer extends \CareActiveRecord
{

    /**
     * TODO refactor
     */
    const DIALYZER_TYPE_HIGH = '201400002960';//High Flux Dialyzer

    /**
     * TODO refactor
     */
    public static function getDialyzerType($dialyzerId)
    {
        switch ($dialyzerId) {
            case self::DIALYZER_TYPE_HIGH:
                return 'high';
                break;
            default:
                return 'low';
                break;
        }
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_dialysis_dialyzer';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('dialyzer_serial_nr', 'required'),
            array('dialyzer_id', 'length', 'max' => 12),
            array('dialyzer_type', 'length', 'max' => 4),
            array('dialyzer_serial_nr', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('dialyzer_serial_nr, dialyzer_id, dialyzer_type', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'dialysisTransactions' => array(self::HAS_MANY, 'DialysisTransaction', 'dialyzer_serial_nr'),
            'dialyzerInfo' => array(self::HAS_ONE, 'SegHis\modules\dialysis\models\DialysisMiscService', array('alt_service_code' => 'dialyzer_id')),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'dialyzer_serial_nr' => 'Dialyzer Serial Nr',
            'dialyzer_id' => 'Dialyzer',
            'dialyzer_type' => 'Dialyzer Type',
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

        $criteria->compare('dialyzer_serial_nr', $this->dialyzer_serial_nr, true);
        $criteria->compare('dialyzer_id', $this->dialyzer_id, true);
        $criteria->compare('dialyzer_type', $this->dialyzer_type, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DialysisDialyzer the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }


}
