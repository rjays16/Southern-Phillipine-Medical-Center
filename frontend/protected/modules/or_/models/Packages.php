<?php

/**
 * This is the model class for table "seg_packages".
 *
 * The followings are the available columns in table 'seg_packages':
 * @property integer $package_id
 * @property string $package_name
 * @property double $package_price
 * @property integer $is_surgical
 * @property string $pkg_phiccode
 * @property integer $is_zpackage
 * @property string $create_id
 * @property string $modify_id
 * @property string $create_time
 * @property string $modify_time
 * @property string $history
 * @property integer $clinic_id
 *
 * The followings are the available model relations:
 * @property BillingPkg[] $billingPkgs
 * @property HcareBsked[] $segHcareBskeds
 * @property PackageDetails[] $packageDetails
 * @property PackagesClinics[] $packagesClinics
 */
class Packages extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'seg_packages';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pkg_phiccode', 'required'),
			array('is_surgical, is_zpackage, clinic_id', 'numerical', 'integerOnly'=>true),
			array('package_price', 'numerical'),
			array('package_name', 'length', 'max'=>100),
			array('pkg_phiccode', 'length', 'max'=>10),
			array('create_id, modify_id', 'length', 'max'=>60),
			array('create_time, modify_time, history', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('package_id, package_name, package_price, is_surgical, pkg_phiccode, is_zpackage, create_id, modify_id, create_time, modify_time, history, clinic_id', 'safe', 'on'=>'search'),
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
			'billingPkgs' => array(self::HAS_MANY, 'BillingPkg', 'package_id'),
			'segHcareBskeds' => array(self::MANY_MANY, 'HcareBsked', 'seg_hcare_packages(package_id, bsked_id)'),
			'packageDetails' => array(self::HAS_MANY, 'PackageDetails', 'package_id'),
			'packagesClinics' => array(self::HAS_MANY, 'PackagesClinics', 'package_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'package_id' => 'Package',
			'package_name' => 'Package Name',
			'package_price' => 'Package Price',
			'is_surgical' => 'Is Surgical',
			'pkg_phiccode' => 'Pkg Phiccode',
			'is_zpackage' => 'Is Zpackage',
			'create_id' => 'Create',
			'modify_id' => 'Modify',
			'create_time' => 'Create Time',
			'modify_time' => 'Modify Time',
			'history' => 'History',
			'clinic_id' => 'Clinic',
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

		$criteria->compare('package_id',$this->package_id);
		$criteria->compare('package_name',$this->package_name,true);
		$criteria->compare('package_price',$this->package_price);
		$criteria->compare('is_surgical',$this->is_surgical);
		$criteria->compare('pkg_phiccode',$this->pkg_phiccode,true);
		$criteria->compare('is_zpackage',$this->is_zpackage);
		$criteria->compare('create_id',$this->create_id,true);
		$criteria->compare('modify_id',$this->modify_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_time',$this->modify_time,true);
		$criteria->compare('history',$this->history,true);
		$criteria->compare('clinic_id',$this->clinic_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Packages the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
     * Returns a list of
     * @param type $term
     */
    public static function searchByName($name, $limit=20) {
        $criteria = new CDbCriteria();
        $criteria->limit =  $limit;

        // No lastname and firstname in the search query
        if (empty($name)) {
            return array();
        }

        $criteria->addCondition('package_name LIKE :name');
        $criteria->params = array('name' => '%'.trim($name).'%');

        return static::model()->findAll($criteria);
    }

    public function findAllByPatientType($enc_no){
    	$modelEnc = Encounter::model()->findByPk($enc_no);
    	
        $criteria = new CDbCriteria();

        $criteria->with=array(
        	'packageDetails'=>array(
        		'joinType'
        	)
        );

    	if(empty($modelEnc->encounter_type)){
    		$criteria->condition = "is_er = 1 AND is_opd = 1 AND is_ipd = 1 AND is_hssc =1 AND is_dialysis = 1 AND is_deleted = 0";
    	}else{
    		switch ($modelEnc->encounter_type) {
    			case 1:
    				$criteria->condition = "is_er = 1 AND is_deleted = 0";
    				break;
    			case 2:
    				$criteria->condition = "is_opd = 1 AND is_deleted = 0";
    				break;
    			case 3:
    			case 4:
    				$criteria->condition = "is_ipd = 1 AND is_deleted = 0";
    				break;
    				/*added By MARK*/
    			//added by Carriane 06/27/17
    			case 5:
    				$criteria->condition = 'is_dialysis = 1 AND is_deleted = 0';
    				break;
    			//end carriane
    			case 6:
    				$criteria->condition = "is_hssc = 1 AND is_deleted = 0";
    				break;
    			default:
    				$criteria->condition = "is_er = 1 AND is_opd = 1 AND is_ipd = 1 AND is_hssc =1 AND is_dialysis = 1 AND is_deleted = 0";
    				break;
    		}
    	}

        return $this->findAll($criteria);
    }
}
