 <?php

require_once($root_path.'include/care_api_classes/class_request_source.php');

/**
 * This is the model class for table "care_encounter_referrals".
 *
 * The followings are the available columns in table 'care_encounter_referrals':
 * @property integer $id
 * @property string $encounter_nr
 * @property string $entry_date
 * @property string $account
 * @property string $sub_account
 * @property string $control_no
 * @property string $amount
 * @property string $remarks
 * @property string $history
 * @property string $create_id
 * @property string $create_time
 * @property string $modify_id
 * @property string $modify_time
 * @property integer $is_deleted
 *
 * The followings are the available model relations:
 * @property SegGrantAccounts $subAccount
 * @property SegGrantAccountType $account0
 * @property CareEncounter $encounterNr
 */
class EncounterReferrals extends CActiveRecord
{
    public $alt_name;
    public $sub_account;
    public $amount;
    public $account_referrals;
    public $subAccountid;
    public $amountformat;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_encounter_referrals';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('encounter_nr', 'required'),
            array('is_deleted', 'numerical', 'integerOnly'=>true),
            array('encounter_nr', 'length', 'max'=>12),
            array('account, sub_account', 'length', 'max'=>10),
            array('control_no', 'length', 'max'=>100),
            array('amount', 'length', 'max'=>18),
            array('create_id, modify_id', 'length', 'max'=>35),
            array('entry_date, remarks, history, create_time, modify_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, encounter_nr, entry_date, account, sub_account, control_no, amount, remarks, history, create_id, create_time, modify_id, modify_time, is_deleted', 'safe', 'on'=>'search'),
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
            'subAccount' => array(self::BELONGS_TO, 'GrantAccount', 'sub_account'),
            'account0' => array(self::BELONGS_TO, 'GrantAccountType', 'account'),
            'encounterNr' => array(self::BELONGS_TO, 'Encounter', 'encounter_nr'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'encounter_nr' => 'Encounter Nr',
            'entry_date' => 'Entry Date',
            'account' => 'Account',
            'sub_account' => 'Sub Account',
            'control_no' => 'Control No',
            'amount' => 'Amount',
            'remarks' => 'Remarks',
            'history' => 'History',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
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

        $criteria->compare('id',$this->id);
        $criteria->compare('encounter_nr',$this->encounter_nr,true);
        $criteria->compare('entry_date',$this->entry_date,true);
        $criteria->compare('account',$this->account,true);
        $criteria->compare('sub_account',$this->sub_account,true);
        $criteria->compare('control_no',$this->control_no,true);
        $criteria->compare('amount',$this->amount,true);
        $criteria->compare('remarks',$this->remarks,true);
        $criteria->compare('history',$this->history,true);
        $criteria->compare('create_id',$this->create_id,true);
        $criteria->compare('create_time',$this->create_time,true);
        $criteria->compare('modify_id',$this->modify_id,true);
        $criteria->compare('modify_time',$this->modify_time,true);
        $criteria->compare('is_deleted',$this->is_deleted);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CareEncounterReferrals the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function scopes() {
        return array(
            'byentrydate' => array('order' => 'entry_date DESC'),
        );
    }

    public function getEncounterReferrals($encounter_nr='',$type_id=''){
        $criteria = new CDbCriteria();
        $criteria->select = array("t.*", "UPPER(alt_name) AS alt_name", "UPPER(name) AS sub_account", "FORMAT(amount, 2) AS amount", "t.sub_account as subAccountid", "amount AS amountformat");
        $criteria->order = 'entry_date DESC, create_time DESC';
        
        if($encounter_nr != '')
            $enc = $encounter_nr;
        else $enc = $_GET['encounter_nr'];

        $condition = array(
                        't.encounter_nr' => $enc,
                        't.is_deleted' => 0
                    );

        if($type_id != ''){
            $condition['account'] = $type_id;
            $criteria->group = "sub_account";
            $criteria->addCondition("sub_account IS NOT NULL");
        }

        $criteria->addColumnCondition($condition);

        $criteria->with = array(
            'account0' => array(
                'select' => array('alt_name','type_name','id'),
                'joinType' => 'LEFT JOIN'/*,
                'together' => true*/
            ), 
            'subAccount' => array(
                'select' => 'name',
                'joinType' => 'LEFT JOIN'
            )
        );

        if($encounter_nr != ''){
            return $this->findAll($criteria);
        }else{
            return new \CActiveDataProvider($this, array(
                'criteria' => $criteria,
            ));
        }
    }

    public function getAccountReferrals($type_id, $id='',$encounter_nr=''){
        $criteria = new CDbCriteria();
        
        if($encounter_nr == ''){
            if($id){
                $condition = "account = ".$type_id." AND sub_account = ".$id." AND is_deleted <> 1";
            }else{
                if($type_id != ''){
                    $condition = "account = ".$type_id." AND (sub_account IS NULL OR sub_account = '') AND is_deleted <> 1";
                }else{
                    $condition = "account IS NULL AND (sub_account IS NULL OR sub_account = '') AND is_deleted <> 1";
                }
            }
        }else{
            $condition = "encounter_nr = '".$encounter_nr."' AND is_deleted <> 1";
            $criteria->group = "account, sub_account";
            $criteria->order = "account ASC, sub_account ASC";
            $addtlSelect = ",account,sub_account";
        }

        $condition .= " AND balance IS NOT NULL";
        
        $criteria->select = "SUM(amount) as account_referrals".$addtlSelect;
        $criteria->condition = $condition;
        $model =  $this->findAll($criteria);

        return $model;
    }

    public function getCostCenterList(){
        $objReqsource = new SegRequestSource;
        $list = $objReqsource->getAllCostCenterList();
        $costcenters = array();

        foreach ($list as $key => $value) {
            $costcenters[$key]['id'] = $value['id'];
            $costcenters[$key]['name'] = $value['source_name'];
        }
        
        return $costcenters;
    }

} 