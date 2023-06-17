<?php
namespace SegHis\modules\socialService\models;

require_once($root_path.'include/care_api_classes/class_social_service.php');

/**
 * This is the model class for table "seg_charity_grants".
 *
 * The followings are the available columns in table 'seg_charity_grants':
 * @property string $encounter_nr
 * @property string $grant_dte
 * @property integer $sw_nr
 * @property string $discountid
 * @property string $discount
 * @property string $discount_amnt
 * @property string $notes
 * @property string $personal_circumstance
 * @property string $community_situation
 * @property string $nature_of_disease
 * @property string $reason
 * @property string $other_name
 * @property string $id_number
 * @property string $status
 */
class EncounterCharityGrant extends \CareActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_charity_grants';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('encounter_nr, grant_dte, sw_nr, discountid, discount', 'required'),
			array('sw_nr', 'numerical', 'integerOnly'=>true),
			array('encounter_nr', 'length', 'max'=>12),
			array('discountid, discount, discount_amnt, reason, other_name', 'length', 'max'=>10),
			array('id_number', 'length', 'max'=>20),
			array('status', 'length', 'max'=>9),
			array('notes, personal_circumstance, community_situation, nature_of_disease', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('encounter_nr, grant_dte, sw_nr, discountid, discount, discount_amnt, notes, personal_circumstance, community_situation, nature_of_disease, reason, other_name, id_number, status', 'safe', 'on'=>'search'),
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
			'segdiscounts' => array(self::BELONGS_TO, 'SegHis\modules\socialService\models\SegDiscount', 'discountid')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'encounter_nr' => 'Encounter Nr',
			'grant_dte' => 'Grant Dte',
			'sw_nr' => 'Sw Nr',
			'discountid' => 'Discountid',
			'discount' => 'Discount',
			'discount_amnt' => 'Discount Amnt',
			'notes' => 'Notes',
			'personal_circumstance' => 'Personal Circumstance',
			'community_situation' => 'Community Situation',
			'nature_of_disease' => 'Nature Of Disease',
			'reason' => 'Reason',
			'other_name' => 'Other Name',
			'id_number' => 'Id Number',
			'status' => 'Status',
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

		$criteria->compare('encounter_nr',$this->encounter_nr,true);
		$criteria->compare('grant_dte',$this->grant_dte,true);
		$criteria->compare('sw_nr',$this->sw_nr);
		$criteria->compare('discountid',$this->discountid,true);
		$criteria->compare('discount',$this->discount,true);
		$criteria->compare('discount_amnt',$this->discount_amnt,true);
		$criteria->compare('notes',$this->notes,true);
		$criteria->compare('personal_circumstance',$this->personal_circumstance,true);
		$criteria->compare('community_situation',$this->community_situation,true);
		$criteria->compare('nature_of_disease',$this->nature_of_disease,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('other_name',$this->other_name,true);
		$criteria->compare('id_number',$this->id_number,true);
		$criteria->compare('status',$this->status,true);

		return new \CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return EncounterCharityGrant the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
        
    /***
     * 
     */
    public static function getRecentCharityGrant($encounter_nr) 
    {
        $criteria = new \CDbCriteria();
        $criteria->condition = "encounter_nr = '{$encounter_nr}'";
        $criteria->order = "grant_dte DESC";
        $criteria->limit = 1;            
        
        $result = self::model()->findAll($criteria);
        return $result;
    }   

    public function getPatientClassification($pid='', $encounter_nr='')
    {
    	$details = \Yii::app()->db->createCommand("SELECT 
													  IF(ps.nr IS NOT NULL,
													    IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),'PHS',''),
													    IF(dep.dependent_pid IS NOT NULL,IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20),'PHSDep',''),
													      IF(SUBSTRING(MAX(CONCAT(scp.grant_dte, scp.discountid)),20) = 'SC',
													        IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20
													          ),'SC','SC'),
													        IF(SUBSTRING(MAX(CONCAT(enc.encounter_date,enc.encounter_nr)),20) IS NULL,'',
													          IF(SUBSTRING(MAX(CONCAT( enc.encounter_date,enc.encounter_type)),20) = 2,
													            SUBSTRING(MAX(CONCAT(scp.grant_dte, scp.discountid)),20),
													            SUBSTRING(MAX(CONCAT(se.grant_dte, se.discountid)),20)))))
													  ) AS discountid 
													FROM
													  care_person AS cp 
													  LEFT JOIN care_encounter AS enc 
													    ON enc.pid = cp.pid 
													    AND enc.is_discharged = 0 
													    AND enc.encounter_status <> 'cancelled' 
													    AND enc.status NOT IN (
													      'deleted',
													      'hidden',
													      'inactive',
													      'void'
													    ) 
													    AND enc.encounter_type NOT IN ('5', '12') 
													  LEFT JOIN seg_charity_grants_pid AS scp 
													    ON scp.pid = cp.pid 
													    AND scp.status = 'valid' 
													    AND scp.discountid NOT IN ('LINGAP') 
													  LEFT JOIN seg_charity_grants_expiry_pid AS scgep 
													    ON scgep.pid = scp.pid 
													    AND scgep.grant_dte = scp.grant_dte 
													  LEFT JOIN seg_charity_grants AS se 
													    ON se.encounter_nr = enc.encounter_nr 
													    AND se.status = 'valid' 
													    AND se.discountid NOT IN ('LINGAP') 
													  LEFT JOIN seg_charity_grants_expiry AS scge 
													    ON scge.encounter_nr = se.encounter_nr 
													    AND scge.grant_dte = se.grant_dte 
													  LEFT JOIN care_personell AS ps 
													    ON cp.pid = ps.pid 
													    AND (
													      (
													        date_exit NOT IN (DATE(NOW())) 
													        AND date_exit > DATE(NOW())
													      ) 
													      OR date_exit = '0000-00-00' 
													      OR date_exit IS NULL
													    ) 
													    AND (
													      (
													        contract_end NOT IN (DATE(NOW())) 
													        AND contract_end > DATE(NOW())
													      ) 
													      OR contract_end = '0000-00-00' 
													      OR contract_end IS NULL
													    ) 
													  LEFT JOIN seg_dependents AS dep 
													    ON dep.dependent_pid = cp.pid 
													    AND dep.status = 'member' 
													WHERE cp.pid = '$pid' 
													GROUP BY cp.pid 
													ORDER BY name_last ASC,
													  name_first ASC ");

    	$result = $details->queryRow();
    	$discountid = '';
    	
    	$objSS = new \SocialService;

    	$expiryInfo = $objSS->getExpiryInfo($pid);
				
		$ExistNonSocial = array("B-PWD","A-PWD","C1-PWD","C2-PWD","C3-PWD");
        if(!empty($expiryInfo['pid']) && in_array($expiryInfo['discountid'],$ExistNonSocial)){
            $pwd_expiry_dt =  strtotime($expiryInfo['pwd_expiry']);
            $now = strtotime(date("Y-m-d"));
            if ($pwd_expiry_dt >= $now || empty($expiryInfo['pwd_expiry'])) {
                $result['discountid'] = $expiryInfo['discountid'];
            }else{
            	$result['discountid'] = 'None';
            }
        }

        $getclassification = $objSS->getClassification($pid);

		$existDependent = $objSS->getExistDependent($pid);
		$existEmployee = $objSS->getExistEmployee($pid);
		
		if($getclassification['discountid']=='DMCDep'){
			if(!empty($existDependent['dependent_pid'])  && !empty($encounter_nr)){
    			$discountid = $result["discountid"];
    		}elseif(!empty($existEmployee['pid'])  && !empty($encounter_nr)){
    			$discountid = $result["discountid"];
    		}else{
    			$discountid = 'None';
    		}
    	}else{
    		$discountid = $result["discountid"];
    	}

    	return $discountid;
    }   
}
