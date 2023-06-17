<?php
namespace SegHis\modules\socialService\models;

/**
 * This is the model class for table "seg_discount".
 *
 * The followings are the available columns in table 'seg_discount':
 * @property string $discountid
 * @property string $discountdesc
 * @property string $discount
 * @property string $area_used
 * @property integer $is_forall
 * @property integer $is_charity
 * @property integer $lockflag
 * @property string $parentid
 * @property string $billareas_applied
 * @property string $modify_id
 * @property string $modify_timestamp
 * @property string $create_id
 * @property string $create_timestamp
 * @property integer $allow_walkin
 * @property integer $is_visible
 * @property integer $is_additional_support
 * @property string $non_social_discount
 *
 * The followings are the available model relations:
 * @property SegBillingEncounter[] $segBillingEncounters
 * @property SegCharityGrants[] $segCharityGrants
 * @property SegPay[] $segPays
 * @property SegPharmaOrders[] $segPharmaOrders
 * @property SegPocOrder[] $segPocOrders
 * @property SegSocialExpiry[] $segSocialExpiries
 */
class SegDiscount extends \CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_discount';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('discountid, discount, parentid, billareas_applied', 'required'),
            array('is_forall, is_charity, lockflag, allow_walkin, is_visible, is_additional_support', 'numerical', 'integerOnly'=>true),
            array('discountid, discount, parentid, non_social_discount', 'length', 'max'=>10),
            array('discountdesc', 'length', 'max'=>80),
            array('area_used', 'length', 'max'=>1),
            array('modify_id, create_id', 'length', 'max'=>35),
            array('modify_timestamp, create_timestamp', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('discountid, discountdesc, discount, area_used, is_forall, is_charity, lockflag, parentid, billareas_applied, modify_id, modify_timestamp, create_id, create_timestamp, allow_walkin, is_visible, is_additional_support, non_social_discount', 'safe', 'on'=>'search'),
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
            'segdiscounts' => array(self::BELONGS_TO, 'SegDiscount', 'discountid')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'discountid' => 'Discountid',
            'discountdesc' => 'Discountdesc',
            'discount' => 'Discount',
            'area_used' => 'Area Used',
            'is_forall' => 'Is Forall',
            'is_charity' => 'Is Charity',
            'lockflag' => 'Lockflag',
            'parentid' => 'Parentid',
            'billareas_applied' => 'Billareas Applied',
            'modify_id' => 'Modify',
            'modify_timestamp' => 'Modify Timestamp',
            'create_id' => 'Create',
            'create_timestamp' => 'Create Timestamp',
            'allow_walkin' => 'Allow Walkin',
            'is_visible' => 'Is Visible',
            'is_additional_support' => 'Is Additional Support',
            'non_social_discount' => 'Non Social Discount',
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

        $criteria->compare('discountid',$this->discountid,true);
        $criteria->compare('discountdesc',$this->discountdesc,true);
        $criteria->compare('discount',$this->discount,true);
        $criteria->compare('area_used',$this->area_used,true);
        $criteria->compare('is_forall',$this->is_forall);
        $criteria->compare('is_charity',$this->is_charity);
        $criteria->compare('lockflag',$this->lockflag);
        $criteria->compare('parentid',$this->parentid,true);
        $criteria->compare('billareas_applied',$this->billareas_applied,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_timestamp',$this->modify_timestamp,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_timestamp',$this->create_timestamp,true);
        $criteria->compare('allow_walkin',$this->allow_walkin);
        $criteria->compare('is_visible',$this->is_visible);
        $criteria->compare('is_additional_support',$this->is_additional_support);
        $criteria->compare('non_social_discount',$this->non_social_discount,true);

        return new \CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return SegDiscount the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
} 