<?php

/**
 * This is the model class for table "seg_consult_request".
 *
 * The followings are the available columns in table 'seg_consult_request':
 * @property string $consult_id
 * @property string $name_last
 * @property string $name_first
 * @property string $name_middle
 * @property string $date_birth
 * @property string $contact_no
 * @property string $sex
 * @property string $religion
 * @property string $create_dt
 * @property string $onesignal_player_id
 * @property string $onesignal_push_token
 * @property string $device_model
 * @property string $device_uuid
 * @property string $device_unique_id
 * @property string $device_platform
 * @property string $access_token
 * @property integer $is_expired
 */
class ConsultRequest extends CareActiveRecord
{
    public $encounter_nr;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'seg_consult_request';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('consult_id', 'required'),
            array('is_expired', 'numerical', 'integerOnly' => true),
            array('consult_id', 'length', 'max' => 40),
            array('name_last, name_first, name_middle, religion, device_model, device_uuid, device_unique_id, device_platform', 'length', 'max' => 60),
            array('contact_no', 'length', 'max' => 15),
            array('sex', 'length', 'max' => 1),
            array('onesignal_player_id', 'length', 'max' => 100),
            array('date_birth, create_dt, onesignal_push_token, access_token', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('consult_id, name_last, name_first, name_middle, date_birth, contact_no, sex, religion, create_dt, onesignal_player_id, onesignal_push_token, device_model, device_uuid, device_unique_id, device_platform, access_token, is_expired', 'safe', 'on' => 'search'),
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
            'meeting' => array(self::BELONGS_TO, 'ConsultMeeting', 'consult_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'consult_id'           => 'Consult',
            'name_last'            => 'Name Last',
            'name_first'           => 'Name First',
            'name_middle'          => 'Name Middle',
            'date_birth'           => 'Date Birth',
            'contact_no'           => 'Contact No',
            'sex'                  => 'Sex',
            'religion'             => 'Religion',
            'create_dt'            => 'Create Dt',
            'onesignal_player_id'  => 'Onesignal Player',
            'onesignal_push_token' => 'Onesignal Push Token',
            'device_model'         => 'Device Model',
            'device_uuid'          => 'Device Uuid',
            'device_unique_id'     => 'Device Unique',
            'device_platform'      => 'Device Platform',
            'access_token'         => 'Access Token',
            'is_expired'           => 'Is Expired',
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

        $criteria = new CDbCriteria;

        $criteria->compare('consult_id', $this->consult_id, true);
        $criteria->compare('name_last', $this->name_last, true);
        $criteria->compare('name_first', $this->name_first, true);
        $criteria->compare('name_middle', $this->name_middle, true);
        $criteria->compare('date_birth', $this->date_birth, true);
        $criteria->compare('contact_no', $this->contact_no, true);
        $criteria->compare('sex', $this->sex, true);
        $criteria->compare('religion', $this->religion, true);
        $criteria->compare('create_dt', $this->create_dt, true);
        $criteria->compare('onesignal_player_id', $this->onesignal_player_id, true);
        $criteria->compare('onesignal_push_token', $this->onesignal_push_token, true);
        $criteria->compare('device_model', $this->device_model, true);
        $criteria->compare('device_uuid', $this->device_uuid, true);
        $criteria->compare('device_unique_id', $this->device_unique_id, true);
        $criteria->compare('device_platform', $this->device_platform, true);
        $criteria->compare('access_token', $this->access_token, true);
        $criteria->compare('is_expired', $this->is_expired);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ConsultRequest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getConsultInfromation()
    {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.consult_id = "' . $_GET['id'] . '"');

        $results = $this->find($criteria);

        return $results;
    }

    /***
     * 
     */
    public function getOnlineRequest(){
        $criteria = new CDbCriteria();
        $criteria->select = array("t.*", "scm.encounter_nr AS encounter_nr");


        require_once($root_path . 'include/care_api_classes/class_acl.php');
        $objAcl = new \Acl(Yii::app()->SESSION['sess_temp_userid']);
   
        $_a_2_opdonlinerequest = $objAcl->checkPermissionRaw('_a_2_opdonlinerequest');
        // $_a_2_opdonlineregister = $objAcl->checkPermissionRaw('_a_2_opdonlineregister');
        // $_a_2_opdonlinecreateconsult = $objAcl->checkPermissionRaw('_a_2_opdonlinecreateconsult');
        // $all_access = ($_a_1_opdonlinerequest && !($_a_2_opdonlineregister || $_a_2_opdonlinecreateconsult ));

        // if(!$_a_1_opdonlinerequest){
        //     if($_a_2_opdonlineregister && !$_a_2_opdonlinecreateconsult){
        //         $criteria->addCondition('cp.pid IS NULL');
        //     }
        //     if(!$_a_2_opdonlineregister && $_a_2_opdonlinecreateconsult){
        //         $criteria->addCondition('cp.pid IS NOT NULL');
        //     }

        // }

        $criteria->order = "t.create_dt ASC";
        $criteria->addCondition('encounter_nr IS NULL');

		$cur_dt = date("Y-m-d");
		$criteria->join = "LEFT JOIN seg_consult_meeting scm ON t.consult_id = scm.consult_id AND scm.create_dt BETWEEN '".$cur_dt." 00:00:00' AND '".$cur_dt." 23:59:59'";

        // $criteria->join = "LEFT JOIN seg_consult_meeting scm ON t.consult_id = scm.consult_id AND DATE(scm.create_dt) = '".date("Y-m-d")."'  
        //                 LEFT JOIN `care_person` AS cp ON cp.`name_last` = t.`name_last`  AND cp.`name_middle` = t.`name_middle` AND cp.`name_first` = t.`name_first` "; 

        // $criteria->addCondition('DATE(t.create_dt)="'.date("Y-m-d").'"');
        $criteria->addCondition("t.create_dt BETWEEN '".$cur_dt." 00:00:00' AND '".$cur_dt." 23:59:59'");
        $criteria->addCondition("t.request_status IS NULL");

        $results = $this->findAll($criteria);
       
        return $results;
    }

}
