<?php
namespace SegHis\modules\laboratory\models;

/**
 * This is the model class for table "seg_lab_serv".
 *
 * The followings are the available columns in table 'seg_lab_serv':
 * @property string $refno
 * @property string $serv_dt
 * @property string $serv_tm
 * @property string $encounter_nr
 * @property string $pid
 * @property integer $is_cash
 * @property integer $type_charge
 * @property integer $is_urgent
 * @property integer $is_tpl
 * @property integer $is_approved
 * @property string $modify_id
 * @property string $modify_dt
 * @property string $create_id
 * @property string $create_dt
 * @property string $history
 * @property string $comments
 * @property string $ordername
 * @property string $orderaddress
 * @property string $status
 * @property string $discountid
 * @property string $loc_code
 * @property string $parent_refno
 * @property string $approved_by_head
 * @property string $remarks
 * @property string $headID
 * @property string $headpasswd
 * @property string $discount
 * @property integer $fromBB
 * @property string $walkin_pid
 * @property string $source_req
 * @property integer $is_repeat
 * @property integer $is_rdu
 * @property integer $is_walkin
 * @property integer $is_pe
 * @property string $area_type
 * @property string $grant_type
 * @property string $ref_source
 * @property string $emr_orderno
 * @property integer $still_in_er
 * @property string $custom_ptype
 *
 * The followings are the available model relations:
 * @property LaboratoryRequestItem[] $items
 */
class LaboratoryRequest extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_lab_serv';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('refno, serv_dt, serv_tm, is_cash, modify_id, create_id', 'required'),
            array('is_cash, type_charge, is_urgent, is_tpl, is_approved, fromBB, is_repeat, is_rdu, is_walkin, is_pe, still_in_er', 'numerical', 'integerOnly' => true),
            array('refno, encounter_nr, pid, parent_refno, walkin_pid, emr_orderno', 'length', 'max' => 12),
            array('modify_id, create_id, status, headID', 'length', 'max' => 35),
            array('comments, ordername', 'length', 'max' => 200),
            array('orderaddress', 'length', 'max' => 300),
            array('discountid, loc_code, discount, source_req, area_type, grant_type', 'length', 'max' => 10),
            array('approved_by_head', 'length', 'max' => 50),
            array('headpasswd', 'length', 'max' => 255),
            array('ref_source', 'length', 'max' => 3),
            array('custom_ptype', 'length', 'max' => 4),
            array('modify_dt, create_dt, history, remarks', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('refno, serv_dt, serv_tm, encounter_nr, pid, is_cash, type_charge, is_urgent, is_tpl, is_approved, modify_id, modify_dt, create_id, create_dt, history, comments, ordername, orderaddress, status, discountid, loc_code, parent_refno, approved_by_head, remarks, headID, headpasswd, discount, fromBB, walkin_pid, source_req, is_repeat, is_rdu, is_walkin, is_pe, area_type, grant_type, ref_source, emr_orderno, still_in_er, custom_ptype', 'safe', 'on' => 'search'),
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
            'items' => array(self::HAS_MANY, 'SegHis\modules\laboratory\models\LaboratoryRequestItem', 'refno'),
            'encounter' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
            'person' => array(self::BELONGS_TO, 'Person', 'pid')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'refno' => 'Refno',
            'serv_dt' => 'Serv Dt',
            'serv_tm' => 'Serv Tm',
            'encounter_nr' => 'Encounter Nr',
            'pid' => 'Pid',
            'is_cash' => 'Is Cash',
            'type_charge' => 'Type Charge',
            'is_urgent' => 'Is Urgent',
            'is_tpl' => 'Is Tpl',
            'is_approved' => 'Is Approved',
            'modify_id' => 'Modify',
            'modify_dt' => 'Modify Dt',
            'create_id' => 'Create',
            'create_dt' => 'Create Dt',
            'history' => 'History',
            'comments' => 'Comments',
            'ordername' => 'Ordername',
            'orderaddress' => 'Orderaddress',
            'status' => 'Status',
            'discountid' => 'Discountid',
            'loc_code' => 'Loc Code',
            'parent_refno' => 'Parent Refno',
            'approved_by_head' => 'Approved By Head',
            'remarks' => 'Remarks',
            'headID' => 'Head',
            'headpasswd' => 'Headpasswd',
            'discount' => 'Discount',
            'fromBB' => 'From Bb',
            'walkin_pid' => 'Walkin Pid',
            'source_req' => 'Source Req',
            'is_repeat' => 'Is Repeat',
            'is_rdu' => 'Is Rdu',
            'is_walkin' => 'Is Walkin',
            'is_pe' => 'Is Pe',
            'area_type' => 'Area Type',
            'grant_type' => 'Grant Type',
            'ref_source' => 'Ref Source',
            'emr_orderno' => 'Emr Orderno',
            'still_in_er' => 'Still In Er',
            'custom_ptype' => 'Custom Ptype',
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
        $criteria->compare('serv_dt', $this->serv_dt, true);
        $criteria->compare('serv_tm', $this->serv_tm, true);
        $criteria->compare('encounter_nr', $this->encounter_nr, true);
        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('is_cash', $this->is_cash);
        $criteria->compare('type_charge', $this->type_charge);
        $criteria->compare('is_urgent', $this->is_urgent);
        $criteria->compare('is_tpl', $this->is_tpl);
        $criteria->compare('is_approved', $this->is_approved);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_dt', $this->modify_dt, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_dt', $this->create_dt, true);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('comments', $this->comments, true);
        $criteria->compare('ordername', $this->ordername, true);
        $criteria->compare('orderaddress', $this->orderaddress, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('discountid', $this->discountid, true);
        $criteria->compare('loc_code', $this->loc_code, true);
        $criteria->compare('parent_refno', $this->parent_refno, true);
        $criteria->compare('approved_by_head', $this->approved_by_head, true);
        $criteria->compare('remarks', $this->remarks, true);
        $criteria->compare('headID', $this->headID, true);
        $criteria->compare('headpasswd', $this->headpasswd, true);
        $criteria->compare('discount', $this->discount, true);
        $criteria->compare('fromBB', $this->fromBB);
        $criteria->compare('walkin_pid', $this->walkin_pid, true);
        $criteria->compare('source_req', $this->source_req, true);
        $criteria->compare('is_repeat', $this->is_repeat);
        $criteria->compare('is_rdu', $this->is_rdu);
        $criteria->compare('is_walkin', $this->is_walkin);
        $criteria->compare('is_pe', $this->is_pe);
        $criteria->compare('area_type', $this->area_type, true);
        $criteria->compare('grant_type', $this->grant_type, true);
        $criteria->compare('ref_source', $this->ref_source, true);
        $criteria->compare('emr_orderno', $this->emr_orderno, true);
        $criteria->compare('still_in_er', $this->still_in_er);
        $criteria->compare('custom_ptype', $this->custom_ptype, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return LaboratoryRequest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
    
    public static $inActiveStatusCodes = array(
        'deleted',
        'sent-out'
    );    
    
    /**
     * @param $refno
     * @return null|LaboratoryRequestItem
     */
    public function getLabDetails($refno)
    {
        $criteria = new \CDbCriteria();
        $criteria->addColumnCondition(array(
            'refno' => $refno
        ));
        $criteria->addNotInCondition('status', static::$inActiveStatusCodes);
        return LaboratoryRequestItem::model()->findAll($criteria);
    }    
}
