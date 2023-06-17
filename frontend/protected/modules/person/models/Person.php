<?php
namespace SegHis\modules\person\models;

\Yii::import('billing.models.HospitalBill');
\Yii::import('application.models.address.AddressBarangay');
\Yii::import('application.models.address.AddressMunicipality');

/**
 * This is the model class for table "care_person".
 *
 * The followings are the available columns in table 'care_person':
 * @property string $pid
 * @property string $date_reg
 * @property string $name_first
 * @property string $name_2
 * @property string $name_3
 * @property string $name_middle
 * @property string $name_last
 * @property string $custom_middle_initial
 * @property string $suffix
 * @property string $name_maiden
 * @property string $name_others
 * @property string $title
 * @property string $date_birth
 * @property string $birth_time
 * @property string $place_birth
 * @property string $blood_group
 * @property string $addr_str
 * @property string $addr_str_nr
 * @property string $addr_zip
 * @property integer $addr_citytown_nr
 * @property integer $addr_is_valid
 * @property string $street_name
 * @property string $brgy_nr
 * @property string $mun_nr
 * @property string $citizenship
 * @property string $occupation
 * @property string $employer
 * @property string $phone_1_code
 * @property string $phone_1_nr
 * @property string $phone_2_code
 * @property string $phone_2_nr
 * @property string $cellphone_1_nr
 * @property string $cellphone_2_nr
 * @property string $fax
 * @property string $email
 * @property string $civil_status
 * @property string $sex
 * @property string $photo
 * @property string $photo_filename
 * @property string $fpimage_filename
 * @property integer $ethnic_orig
 * @property string $org_id
 * @property string $sss_nr
 * @property string $nat_id_nr
 * @property string $religion
 * @property string $mother_pid
 * @property string $mother_fname
 * @property string $mother_maidenname
 * @property string $mother_mname
 * @property string $mother_lname
 * @property string $father_pid
 * @property string $father_fname
 * @property string $father_mname
 * @property string $father_lname
 * @property string $spouse_name
 * @property string $guardian_name
 * @property string $contact_person
 * @property string $contact_pid
 * @property string $contact_relation
 * @property string $death_date
 * @property string $death_time
 * @property string $death_encounter_nr
 * @property string $death_cause
 * @property string $death_cause_code
 * @property string $date_update
 * @property string $status
 * @property string $history
 * @property string $modify_id
 * @property string $modify_time
 * @property string $create_id
 * @property string $create_time
 * @property integer $fromtemp
 * @property integer $admitted_baby
 * @property string $senior_ID
 * @property string $veteran_ID
 * @property integer $is_indigent
 * @property string $DOH_ID
 * @property integer $age
 * @property string $name_search
 * @property string $soundex_namelast
 * @property string $soundex_namefirst
 * @property integer $is_temp_bdate
 *
 * @property Encounter $activeEncounter
 * @property Encounter[] $encounters
 *
 * @property \AddressBarangay $barangay
 * @property \AddressMunicipality $municipality
 * @property \AddressCountry $country
 */
class Person extends \CareActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'care_person';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('pid, name_first, name_last, name_others, blood_group, addr_str, addr_str_nr, addr_zip, mun_nr, civil_status, sex, photo_filename, status, history, modify_id, create_id, name_search, soundex_namelast, soundex_namefirst', 'required'),
            array('addr_citytown_nr, addr_is_valid, ethnic_orig, fromtemp, admitted_baby, is_indigent, age, is_temp_bdate', 'numerical', 'integerOnly' => true),
            array('pid', 'length', 'max' => 12),
            array('name_first, name_2, name_3, name_middle, name_last, name_maiden, place_birth, addr_str, email, photo_filename, fpimage_filename, org_id, sss_nr, nat_id_nr', 'length', 'max' => 60),
            array('custom_middle_initial, citizenship', 'length', 'max' => 5),
            array('suffix, addr_str_nr, death_encounter_nr', 'length', 'max' => 10),
            array('title, occupation, contact_relation, senior_ID, veteran_ID, DOH_ID', 'length', 'max' => 25),
            array('blood_group', 'length', 'max' => 2),
            array('addr_zip, phone_1_code, phone_2_code, death_cause_code', 'length', 'max' => 15),
            array('street_name', 'length', 'max' => 100),
            array('brgy_nr, mun_nr, mother_pid, father_pid, contact_pid', 'length', 'max' => 11),
            array('employer, mother_fname, mother_maidenname, mother_mname, mother_lname, father_fname, father_mname, father_lname, spouse_name, guardian_name', 'length', 'max' => 50),
            array('phone_1_nr, phone_2_nr, cellphone_1_nr, cellphone_2_nr, fax, civil_status, modify_id, create_id', 'length', 'max' => 35),
            array('sex', 'length', 'max' => 1),
            array('religion', 'length', 'max' => 125),
            array('contact_person', 'length', 'max' => 255),
            array('status', 'length', 'max' => 20),
            array('name_search, soundex_namelast, soundex_namefirst', 'length', 'max' => 150),
            array('date_reg, date_birth, birth_time, photo, death_date, death_time, death_cause, date_update, modify_time, create_time', 'safe'),
            array('pid, date_reg, name_first, name_2, name_3, name_middle, name_last, custom_middle_initial, suffix, name_maiden, name_others, title, date_birth, birth_time, place_birth, blood_group, addr_str, addr_str_nr, addr_zip, addr_citytown_nr, addr_is_valid, street_name, brgy_nr, mun_nr, citizenship, occupation, employer, phone_1_code, phone_1_nr, phone_2_code, phone_2_nr, cellphone_1_nr, cellphone_2_nr, fax, email, civil_status, sex, photo, photo_filename, fpimage_filename, ethnic_orig, org_id, sss_nr, nat_id_nr, religion, mother_pid, mother_fname, mother_maidenname, mother_mname, mother_lname, father_pid, father_fname, father_mname, father_lname, spouse_name, guardian_name, contact_person, contact_pid, contact_relation, death_date, death_time, death_encounter_nr, death_cause, death_cause_code, date_update, status, history, modify_id, modify_time, create_id, create_time, fromtemp, admitted_baby, senior_ID, veteran_ID, is_indigent, DOH_ID, age, name_search, soundex_namelast, soundex_namefirst, is_temp_bdate', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'activeEncounter' => array(self::HAS_ONE, 'SegHis\modules\person\models\Encounter', 'pid',
                'on' => 'is_discharged=0',
                'order' => 'encounter_date DESC'
            ),
            'encounters' => array(self::HAS_MANY, 'SegHis\modules\person\models\Encounter', 'pid', 'order' => 'encounter_date DESC',
                'condition' => 'status NOT IN ("'.implode('","',Encounter::$inActiveStatusCodes).'") AND is_discharged=0',
                'select' => 'pid,encounter_nr, encounter_type, encounter_date, status, current_ward_nr, er_opd_diagnosis, is_discharged, current_dept_nr'),
            'barangay'=> array(self::BELONGS_TO, 'AddressBarangay', 'brgy_nr'),
            'municipality'=> array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr'),
            'country' => array(self::HAS_ONE,'AddressCountry',array('country_code'=>'citizenship'))
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pid' => 'Pid',
            'date_reg' => 'Date Reg',
            'name_first' => 'Name First',
            'name_2' => 'Name 2',
            'name_3' => 'Name 3',
            'name_middle' => 'Name Middle',
            'name_last' => 'Name Last',
            'custom_middle_initial' => 'Custom Middle Initial',
            'suffix' => 'Suffix',
            'name_maiden' => 'Name Maiden',
            'name_others' => 'Name Others',
            'title' => 'Title',
            'date_birth' => 'Date Birth',
            'birth_time' => 'Birth Time',
            'place_birth' => 'Place Birth',
            'blood_group' => 'Blood Group',
            'addr_str' => 'Addr Str',
            'addr_str_nr' => 'Addr Str Nr',
            'addr_zip' => 'Addr Zip',
            'addr_citytown_nr' => 'Addr Citytown Nr',
            'addr_is_valid' => 'Addr Is Valid',
            'street_name' => 'Street Name',
            'brgy_nr' => 'Brgy Nr',
            'mun_nr' => 'Mun Nr',
            'citizenship' => 'Citizenship',
            'occupation' => 'Occupation',
            'employer' => 'Employer',
            'phone_1_code' => 'Phone 1 Code',
            'phone_1_nr' => 'Phone 1 Nr',
            'phone_2_code' => 'Phone 2 Code',
            'phone_2_nr' => 'Phone 2 Nr',
            'cellphone_1_nr' => 'Cellphone 1 Nr',
            'cellphone_2_nr' => 'Cellphone 2 Nr',
            'fax' => 'Fax',
            'email' => 'Email',
            'civil_status' => 'Civil Status',
            'sex' => 'Sex',
            'photo' => 'Photo',
            'photo_filename' => 'Photo Filename',
            'fpimage_filename' => 'Fpimage Filename',
            'ethnic_orig' => 'Ethnic Orig',
            'org_id' => 'Org',
            'sss_nr' => 'Sss Nr',
            'nat_id_nr' => 'Nat Id Nr',
            'religion' => 'Religion',
            'mother_pid' => 'Mother Pid',
            'mother_fname' => 'Mother Fname',
            'mother_maidenname' => 'Mother Maidenname',
            'mother_mname' => 'Mother Mname',
            'mother_lname' => 'Mother Lname',
            'father_pid' => 'Father Pid',
            'father_fname' => 'Father Fname',
            'father_mname' => 'Father Mname',
            'father_lname' => 'Father Lname',
            'spouse_name' => 'Spouse Name',
            'guardian_name' => 'Guardian Name',
            'contact_person' => 'Contact Person',
            'contact_pid' => 'Contact Pid',
            'contact_relation' => 'Contact Relation',
            'death_date' => 'Death Date',
            'death_time' => 'Death Time',
            'death_encounter_nr' => 'Death Encounter Nr',
            'death_cause' => 'Death Cause',
            'death_cause_code' => 'Death Cause Code',
            'date_update' => 'Date Update',
            'status' => 'Status',
            'history' => 'History',
            'modify_id' => 'Modify',
            'modify_time' => 'Modify Time',
            'create_id' => 'Create',
            'create_time' => 'Create Time',
            'fromtemp' => 'Fromtemp',
            'admitted_baby' => 'Admitted Baby',
            'senior_ID' => 'Senior',
            'veteran_ID' => 'Veteran',
            'is_indigent' => 'Is Indigent',
            'DOH_ID' => 'Doh',
            'age' => 'Age',
            'name_search' => 'Name Search',
            'soundex_namelast' => 'Soundex Namelast',
            'soundex_namefirst' => 'Soundex Namefirst',
            'is_temp_bdate' => 'Is Temp Bdate',
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
        $criteria = new \CDbCriteria;

        /* hahay pastilan, dapat kono dili iload ang data sa pag load sa page */
        if(!\Yii::app()->request->isAjaxRequest)
            $criteria->addCondition('1=0');

        $criteria->order = 't.name_last, t.name_first, t.create_time DESC';
        $criteria->select = 't.pid,t.name_first,t.name_last,t.name_middle,t.suffix,t.sex';
        $criteria->with = array('activeEncounter' => array(
            'select' => 'encounter_nr, encounter_date, encounter_type, current_ward_nr, er_opd_diagnosis, current_dept_nr',
            'joinType' => 'LEFT JOIN'
        ));

        if($searchNameHrn = $_GET['search-name-hrn']) {
            if($_GET['bloodbank'] == 1){
                $criteria->addColumnCondition(array(
                    't.pid' => $searchNameHrn
                ));
            }elseif($_GET['credit_collection']){
                if(is_numeric($searchNameHrn)) {
                    $criteria->addColumnCondition(array(
                        't.pid' => $searchNameHrn
                    ));
                    $criteria->addCondition('activeEncounter.encounter_nr IS NOT NULL');
                } else {
                    $names = explode(',',$searchNameHrn,2);
                    $criteria->addCondition('t.name_last LIKE \''. trim($names[0] . '%\''));
                    $criteria->addCondition('t.name_first LIKE \''. trim($names[1] . '%\''));
                    $criteria->addCondition('activeEncounter.encounter_nr IS NOT NULL');
                    $criteria->group = "t.pid";
                }
            }elseif($_GET['pdpup']==1){
                $criteria->addCondition('encounter_type = 3');
                if(is_numeric($searchNameHrn)) {
                    $criteria->addColumnCondition(array(
                        't.pid' => $searchNameHrn
                    ));
                }else {
                    $names = explode(',',$searchNameHrn,2);

                    $criteria->addCondition('t.name_last LIKE \''. trim($names[0] . '%\''));
                    $criteria->addCondition('t.name_first LIKE \''. trim($names[1] . '%\''));
                }
            }else{
                if(is_numeric($searchNameHrn)) {
                    $criteria->addColumnCondition(array(
                        't.pid' => $searchNameHrn
                    ));
                } else {
                    $names = explode(',',$searchNameHrn,2);
                    //$criteria->compare('t.name_last',trim($names[0]),true, 'AND', true);
                    //$criteria->compare('t.name_first',trim($names[1]),true, 'AND', true);
                    $criteria->addCondition('t.name_last LIKE \''. trim($names[0] . '%\''));
                    $criteria->addCondition('t.name_first LIKE \''. trim($names[1] . '%\''));
                }
            }
        }

        $criteria->with = array('activeEncounter' => array(
            'select' => 'encounter_nr, encounter_date, encounter_type, current_dept_nr',
            'joinType' => 'LEFT JOIN'
        ));

        if($searchCaseNr = $_GET['search-case-number']) {
            $criteria->addColumnCondition(array(
                'activeEncounter.encounter_nr' => $searchCaseNr
            ));
        }

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Person the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    // removed suffix by carriane 08/23/28
    public function getFullName($format = '{last_name}, {first_name} {middle_initial}', $upperCase = true)
    {
        $fullName = strtr($format, array(
            '{first_name}' => $this->name_first ? $this->name_first : '',
            '{middle_name}' => $this->name_middle ? $this->name_middle : '',
            '{middle_initial}' => $this->name_middle ? $this->name_middle[0] . '.' : '',
            '{last_name}' => $this->name_last ? $this->name_last : '',
            /*'{suffix}' => $this->suffix ? $this->suffix : '',*/
        ));
        return trim($upperCase ? strtoupper($fullName) : $fullName);
    }

    public function getFullAddress($format="{street}{barangay}{municipality}")
    {
        $street = $this->street_name;
        $barangay = $this->barangay;
        $municipality = $this->municipality;
        if($this->mun_nr == 0 && $this->brgy_nr == 0){
            $barangay = '';
            $municipality = '';
        }
        return strtr($format, array(
            '{street}' => $street ? $street.', ' : '',
            '{barangay}' => $barangay ? $barangay.', ' : '',
            '{municipality}' => $municipality ? $municipality : '',
        ));
    }
}
