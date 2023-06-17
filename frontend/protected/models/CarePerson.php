<?php

/**
 * This is the model class for table "care_person".
 *
 * The followings are the available columns in table 'care_person':
 *
 * @property string                   $pid
 * @property string                   $date_reg
 * @property string                   $name_first
 * @property string                   $name_2
 * @property string                   $name_3
 * @property string                   $name_middle
 * @property string                   $name_last
 * @property string                   $custom_middle_initial
 * @property string                   $suffix
 * @property string                   $name_maiden
 * @property string                   $name_others
 * @property string                   $title
 * @property string                   $date_birth
 * @property string                   $birth_time
 * @property string                   $place_birth
 * @property string                   $blood_group
 * @property string                   $addr_str
 * @property string                   $addr_str_nr
 * @property string                   $addr_zip
 * @property integer                  $addr_citytown_nr
 * @property integer                  $addr_is_valid
 * @property string                   $street_name
 * @property string                   $brgy_nr
 * @property string                   $mun_nr
 * @property string                   $citizenship
 * @property string                   $occupation
 * @property string                   $employer
 * @property string                   $phone_1_code
 * @property string                   $phone_1_nr
 * @property string                   $phone_2_code
 * @property string                   $phone_2_nr
 * @property string                   $cellphone_1_nr
 * @property string                   $cellphone_2_nr
 * @property string                   $fax
 * @property string                   $email
 * @property string                   $civil_status
 * @property string                   $sex
 * @property string                   $photo
 * @property string                   $photo_filename
 * @property string                   $fpimage_filename
 * @property integer                  $ethnic_orig
 * @property string                   $org_id
 * @property string                   $sss_nr
 * @property string                   $nat_id_nr
 * @property string                   $religion
 * @property string                   $mother_pid
 * @property string                   $mother_fname
 * @property string                   $mother_maidenname
 * @property string                   $mother_mname
 * @property string                   $mother_lname
 * @property string                   $father_pid
 * @property string                   $father_fname
 * @property string                   $father_mname
 * @property string                   $father_lname
 * @property string                   $spouse_name
 * @property string                   $guardian_name
 * @property string                   $contact_person
 * @property string                   $contact_pid
 * @property string                   $contact_relation
 * @property string                   $death_date
 * @property string                   $death_time
 * @property string                   $death_encounter_nr
 * @property string                   $death_cause
 * @property string                   $death_cause_code
 * @property string                   $date_update
 * @property string                   $status
 * @property string                   $history
 * @property string                   $modify_id
 * @property string                   $modify_time
 * @property string                   $create_id
 * @property string                   $create_time
 * @property integer                  $fromtemp
 * @property integer                  $admitted_baby
 * @property string                   $senior_ID
 * @property string                   $veteran_ID
 * @property integer                  $is_indigent
 * @property string                   $DOH_ID
 * @property integer                  $age
 * @property string                   $name_search
 * @property string                   $soundex_namelast
 * @property string                   $soundex_namefirst
 * @property integer                  $is_temp_bdate
 * @property string                   $source
 * @property string                   $homis_id
 *
 * The followings are the available model relations:
 * @property BloodWaiverDetails[]     $bloodWaiverDetails
 * @property CbgReading[]             $cbgReadings
 * @property CertBirth                $certBirth
 * @property CertDeath                $certDeath
 * @property CertDeathFetal           $certDeathFetal
 * @property CharityGrantsExpiryPid[] $charityGrantsExpiryPs
 * @property CharityGrantsPid[]       $charityGrantsPs
 * @property CmapEntriesBill[]        $cmapEntriesBills
 * @property CmapEntriesLaboratory[]  $cmapEntriesLaboratories
 * @property CmapEntriesMisc[]        $cmapEntriesMiscs
 * @property CmapEntriesObgyne[]      $cmapEntriesObgynes
 * @property CmapEntriesPharmacy[]    $cmapEntriesPharmacies
 * @property CmapEntriesRadiology[]   $cmapEntriesRadiologies
 * @property CmapLedgerPatient[]      $cmapLedgerPatients
 * @property CmapPatientBalance[]     $cmapPatientBalances
 * @property CmapReferrals[]          $cmapReferrals
 * @property ConfinementTracker[]     $confinementTrackers
 * @property CreditMemos[]            $creditMemoses
 * @property DoctorsSoap[]            $doctorsSoaps
 * @property EncounterMedico[]        $encounterMedicos
 * @property IndustrialCompany[]      $segIndustrialCompanies
 * @property IndustrialTransaction[]  $industrialTransactions
 * @property InsuranceMemberInfo[]    $insuranceMemberInfos
 * @property LabResults[]             $labResults
 * @property LingapEntries[]          $lingapEntries
 * @property OpsServ[]                $opsServs
 * @property PdpuProgressNotes[]      $pdpuProgressNotes
 * @property PersonFingerprint        $personFingerprint
 * @property PersonLedger             $personLedger
 * @property PharmaOrders[]           $pharmaOrders
 * @property PidMemcategory           $pidMemcategory
 * @property PocOrder[]               $pocOrders
 * @property Pay[]                    $segPays
 * @property RadioId[]                $radios
 * @property SocialExpiry[]           $socialExpiries
 * @property SocialPatient[]          $socialPatients
 */

class CarePerson extends CareActiveRecord
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
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('pid, name_first, name_last, name_others, blood_group, addr_str, addr_str_nr, addr_zip, mun_nr, civil_status, sex, photo_filename, status, history, modify_id, create_id, name_search, soundex_namelast, soundex_namefirst',
                  'required'),
            array('addr_citytown_nr, addr_is_valid, ethnic_orig, fromtemp, admitted_baby, is_indigent, age, is_temp_bdate',
                  'numerical', 'integerOnly' => true),
            array('pid, death_encounter_nr', 'length', 'max' => 12),
            array('name_first, name_2, name_3, name_middle, name_last, name_maiden, place_birth, addr_str, email, photo_filename, fpimage_filename, org_id, sss_nr, nat_id_nr',
                  'length', 'max' => 60),
            array('custom_middle_initial, citizenship', 'length', 'max' => 5),
            array('suffix, addr_str_nr', 'length', 'max' => 10),
            array('title, occupation, contact_relation, senior_ID, veteran_ID, DOH_ID',
                  'length', 'max' => 25),
            array('blood_group', 'length', 'max' => 2),
            array('addr_zip, phone_1_code, phone_2_code, death_cause_code',
                  'length', 'max' => 15),
            array('street_name', 'length', 'max' => 100),
            array('brgy_nr, mun_nr, mother_pid, father_pid, contact_pid',
                  'length', 'max' => 11),
            array('employer, mother_fname, mother_maidenname, mother_mname, mother_lname, father_fname, father_mname, father_lname, spouse_name, guardian_name, modify_id, create_id',
                  'length', 'max' => 50),
            array('phone_1_nr, phone_2_nr, cellphone_1_nr, cellphone_2_nr, fax, civil_status',
                  'length', 'max' => 35),
            array('sex', 'length', 'max' => 1),
            array('religion', 'length', 'max' => 125),
            array('contact_person', 'length', 'max' => 255),
            array('status, source, homis_id', 'length', 'max' => 20),
            array('name_search, soundex_namelast, soundex_namefirst', 'length',
                  'max' => 150),
            array('date_reg, date_birth, birth_time, photo, death_date, death_time, death_cause, date_update, modify_time, create_time',
                  'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('pid, date_reg, name_first, name_2, name_3, name_middle, name_last, custom_middle_initial, suffix, name_maiden, name_others, title, date_birth, birth_time, place_birth, blood_group, addr_str, addr_str_nr, addr_zip, addr_citytown_nr, addr_is_valid, street_name, brgy_nr, mun_nr, citizenship, occupation, employer, phone_1_code, phone_1_nr, phone_2_code, phone_2_nr, cellphone_1_nr, cellphone_2_nr, fax, email, civil_status, sex, photo, photo_filename, fpimage_filename, ethnic_orig, org_id, sss_nr, nat_id_nr, religion, mother_pid, mother_fname, mother_maidenname, mother_mname, mother_lname, father_pid, father_fname, father_mname, father_lname, spouse_name, guardian_name, contact_person, contact_pid, contact_relation, death_date, death_time, death_encounter_nr, death_cause, death_cause_code, date_update, status, history, modify_id, modify_time, create_id, create_time, fromtemp, admitted_baby, senior_ID, veteran_ID, is_indigent, DOH_ID, age, name_search, soundex_namelast, soundex_namefirst, is_temp_bdate, source, homis_id',
                  'safe', 'on' => 'search'),
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
            'bloodWaiverDetails'      => array(self::HAS_MANY,
                                               'BloodWaiverDetails', 'pid'),
            'cbgReadings'             => array(self::HAS_MANY, 'CbgReading',
                                               'pid'),
            'certBirth'               => array(self::HAS_ONE, 'CertBirth',
                                               'pid'),
            'certDeath'               => array(self::HAS_ONE, 'CertDeath',
                                               'pid'),
            'certDeathFetal'          => array(self::HAS_ONE, 'CertDeathFetal',
                                               'pid'),
            'charityGrantsExpiryPs'   => array(self::HAS_MANY,
                                               'CharityGrantsExpiryPid', 'pid'),
            'charityGrantsPs'         => array(self::HAS_MANY,
                                               'CharityGrantsPid', 'pid'),
            'cmapEntriesBills'        => array(self::HAS_MANY,
                                               'CmapEntriesBill', 'pid'),
            'cmapEntriesLaboratories' => array(self::HAS_MANY,
                                               'CmapEntriesLaboratory', 'pid'),
            'cmapEntriesMiscs'        => array(self::HAS_MANY,
                                               'CmapEntriesMisc', 'pid'),
            'cmapEntriesObgynes'      => array(self::HAS_MANY,
                                               'CmapEntriesObgyne', 'pid'),
            'cmapEntriesPharmacies'   => array(self::HAS_MANY,
                                               'CmapEntriesPharmacy', 'pid'),
            'cmapEntriesRadiologies'  => array(self::HAS_MANY,
                                               'CmapEntriesRadiology', 'pid'),
            'cmapLedgerPatients'      => array(self::HAS_MANY,
                                               'CmapLedgerPatient', 'pid'),
            'cmapPatientBalances'     => array(self::HAS_MANY,
                                               'CmapPatientBalance', 'pid'),
            'cmapReferrals'           => array(self::HAS_MANY, 'CmapReferrals',
                                               'pid'),
            'confinementTrackers'     => array(self::HAS_MANY,
                                               'ConfinementTracker', 'pid'),
            'creditMemoses'           => array(self::HAS_MANY, 'CreditMemos',
                                               'pid'),
            'doctorsSoaps'            => array(self::HAS_MANY, 'DoctorsSoap',
                                               'pid'),
            'encounterMedicos'        => array(self::HAS_MANY,
                                               'EncounterMedico', 'pid'),
            'segIndustrialCompanies'  => array(self::MANY_MANY,
                                               'IndustrialCompany',
                                               'seg_industrial_comp_emp(pid, company_id)'),
            'industrialTransactions'  => array(self::HAS_MANY,
                                               'IndustrialTransaction', 'pid'),
            'insuranceMemberInfos'    => array(self::HAS_MANY,
                                               'InsuranceMemberInfo', 'pid'),
            'labResults'              => array(self::HAS_MANY, 'LabResults',
                                               'pid'),
            'lingapEntries'           => array(self::HAS_MANY, 'LingapEntries',
                                               'pid'),
            'opsServs'                => array(self::HAS_MANY, 'OpsServ',
                                               'pid'),
            'pdpuProgressNotes'       => array(self::HAS_MANY,
                                               'PdpuProgressNotes', 'pid'),
            'personFingerprint'       => array(self::HAS_ONE,
                                               'PersonFingerprint', 'pid'),
            'personLedger'            => array(self::HAS_ONE, 'PersonLedger',
                                               'pid'),
            'pharmaOrders'            => array(self::HAS_MANY, 'PharmaOrders',
                                               'pid'),
            'pidMemcategory'          => array(self::HAS_ONE, 'PidMemcategory',
                                               'pid'),
            'pocOrders'               => array(self::HAS_MANY, 'PocOrder',
                                               'pid'),
            'segPays'                 => array(self::MANY_MANY, 'Pay',
                                               'seg_prepaid_consultation(pid, or_no)'),
            'radios'                  => array(self::HAS_MANY, 'RadioId',
                                               'pid'),
            'socialExpiries'          => array(self::HAS_MANY, 'SocialExpiry',
                                               'pid'),
            'socialPatients'          => array(self::HAS_MANY, 'SocialPatient',
                                               'pid'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'pid'                   => 'Pid',
            'date_reg'              => 'Date Reg',
            'name_first'            => 'Name First',
            'name_2'                => 'Name 2',
            'name_3'                => 'Name 3',
            'name_middle'           => 'Name Middle',
            'name_last'             => 'Name Last',
            'custom_middle_initial' => 'Custom Middle Initial',
            'suffix'                => 'Suffix',
            'name_maiden'           => 'Name Maiden',
            'name_others'           => 'Name Others',
            'title'                 => 'Title',
            'date_birth'            => 'Date Birth',
            'birth_time'            => 'Birth Time',
            'place_birth'           => 'Place Birth',
            'blood_group'           => 'Blood Group',
            'addr_str'              => 'Addr Str',
            'addr_str_nr'           => 'Addr Str Nr',
            'addr_zip'              => 'Addr Zip',
            'addr_citytown_nr'      => 'Addr Citytown Nr',
            'addr_is_valid'         => 'Addr Is Valid',
            'street_name'           => 'Street Name',
            'brgy_nr'               => 'Brgy Nr',
            'mun_nr'                => 'Mun Nr',
            'citizenship'           => 'Citizenship',
            'occupation'            => 'Occupation',
            'employer'              => 'Employer',
            'phone_1_code'          => 'Phone 1 Code',
            'phone_1_nr'            => 'Phone 1 Nr',
            'phone_2_code'          => 'Phone 2 Code',
            'phone_2_nr'            => 'Phone 2 Nr',
            'cellphone_1_nr'        => 'Cellphone 1 Nr',
            'cellphone_2_nr'        => 'Cellphone 2 Nr',
            'fax'                   => 'Fax',
            'email'                 => 'Email',
            'civil_status'          => 'Civil Status',
            'sex'                   => 'Sex',
            'photo'                 => 'Photo',
            'photo_filename'        => 'Photo Filename',
            'fpimage_filename'      => 'Fpimage Filename',
            'ethnic_orig'           => 'Ethnic Orig',
            'org_id'                => 'Org',
            'sss_nr'                => 'Sss Nr',
            'nat_id_nr'             => 'Nat Id Nr',
            'religion'              => 'Religion',
            'mother_pid'            => 'Mother Pid',
            'mother_fname'          => 'Mother Fname',
            'mother_maidenname'     => 'Mother Maidenname',
            'mother_mname'          => 'Mother Mname',
            'mother_lname'          => 'Mother Lname',
            'father_pid'            => 'Father Pid',
            'father_fname'          => 'Father Fname',
            'father_mname'          => 'Father Mname',
            'father_lname'          => 'Father Lname',
            'spouse_name'           => 'Spouse Name',
            'guardian_name'         => 'Guardian Name',
            'contact_person'        => 'Contact Person',
            'contact_pid'           => 'Contact Pid',
            'contact_relation'      => 'Contact Relation',
            'death_date'            => 'Death Date',
            'death_time'            => 'Death Time',
            'death_encounter_nr'    => 'Death Encounter Nr',
            'death_cause'           => 'Death Cause',
            'death_cause_code'      => 'Death Cause Code',
            'date_update'           => 'Date Update',
            'status'                => 'Status',
            'history'               => 'History',
            'modify_id'             => 'Modify',
            'modify_time'           => 'Modify Time',
            'create_id'             => 'Create',
            'create_time'           => 'Create Time',
            'fromtemp'              => 'Fromtemp',
            'admitted_baby'         => 'Admitted Baby',
            'senior_ID'             => 'Senior',
            'veteran_ID'            => 'Veteran',
            'is_indigent'           => 'Is Indigent',
            'DOH_ID'                => 'Doh',
            'age'                   => 'Age',
            'name_search'           => 'Name Search',
            'soundex_namelast'      => 'Soundex Namelast',
            'soundex_namefirst'     => 'Soundex Namefirst',
            'is_temp_bdate'         => 'Is Temp Bdate',
            'source'                => 'Source',
            'homis_id'              => 'Homis',
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

        $criteria->compare('pid', $this->pid, true);
        $criteria->compare('date_reg', $this->date_reg, true);
        $criteria->compare('name_first', $this->name_first, true);
        $criteria->compare('name_2', $this->name_2, true);
        $criteria->compare('name_3', $this->name_3, true);
        $criteria->compare('name_middle', $this->name_middle, true);
        $criteria->compare('name_last', $this->name_last, true);
        $criteria->compare(
            'custom_middle_initial', $this->custom_middle_initial, true
        );
        $criteria->compare('suffix', $this->suffix, true);
        $criteria->compare('name_maiden', $this->name_maiden, true);
        $criteria->compare('name_others', $this->name_others, true);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('date_birth', $this->date_birth, true);
        $criteria->compare('birth_time', $this->birth_time, true);
        $criteria->compare('place_birth', $this->place_birth, true);
        $criteria->compare('blood_group', $this->blood_group, true);
        $criteria->compare('addr_str', $this->addr_str, true);
        $criteria->compare('addr_str_nr', $this->addr_str_nr, true);
        $criteria->compare('addr_zip', $this->addr_zip, true);
        $criteria->compare('addr_citytown_nr', $this->addr_citytown_nr);
        $criteria->compare('addr_is_valid', $this->addr_is_valid);
        $criteria->compare('street_name', $this->street_name, true);
        $criteria->compare('brgy_nr', $this->brgy_nr, true);
        $criteria->compare('mun_nr', $this->mun_nr, true);
        $criteria->compare('citizenship', $this->citizenship, true);
        $criteria->compare('occupation', $this->occupation, true);
        $criteria->compare('employer', $this->employer, true);
        $criteria->compare('phone_1_code', $this->phone_1_code, true);
        $criteria->compare('phone_1_nr', $this->phone_1_nr, true);
        $criteria->compare('phone_2_code', $this->phone_2_code, true);
        $criteria->compare('phone_2_nr', $this->phone_2_nr, true);
        $criteria->compare('cellphone_1_nr', $this->cellphone_1_nr, true);
        $criteria->compare('cellphone_2_nr', $this->cellphone_2_nr, true);
        $criteria->compare('fax', $this->fax, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('civil_status', $this->civil_status, true);
        $criteria->compare('sex', $this->sex, true);
        $criteria->compare('photo', $this->photo, true);
        $criteria->compare('photo_filename', $this->photo_filename, true);
        $criteria->compare('fpimage_filename', $this->fpimage_filename, true);
        $criteria->compare('ethnic_orig', $this->ethnic_orig);
        $criteria->compare('org_id', $this->org_id, true);
        $criteria->compare('sss_nr', $this->sss_nr, true);
        $criteria->compare('nat_id_nr', $this->nat_id_nr, true);
        $criteria->compare('religion', $this->religion, true);
        $criteria->compare('mother_pid', $this->mother_pid, true);
        $criteria->compare('mother_fname', $this->mother_fname, true);
        $criteria->compare('mother_maidenname', $this->mother_maidenname, true);
        $criteria->compare('mother_mname', $this->mother_mname, true);
        $criteria->compare('mother_lname', $this->mother_lname, true);
        $criteria->compare('father_pid', $this->father_pid, true);
        $criteria->compare('father_fname', $this->father_fname, true);
        $criteria->compare('father_mname', $this->father_mname, true);
        $criteria->compare('father_lname', $this->father_lname, true);
        $criteria->compare('spouse_name', $this->spouse_name, true);
        $criteria->compare('guardian_name', $this->guardian_name, true);
        $criteria->compare('contact_person', $this->contact_person, true);
        $criteria->compare('contact_pid', $this->contact_pid, true);
        $criteria->compare('contact_relation', $this->contact_relation, true);
        $criteria->compare('death_date', $this->death_date, true);
        $criteria->compare('death_time', $this->death_time, true);
        $criteria->compare(
            'death_encounter_nr', $this->death_encounter_nr, true
        );
        $criteria->compare('death_cause', $this->death_cause, true);
        $criteria->compare('death_cause_code', $this->death_cause_code, true);
        $criteria->compare('date_update', $this->date_update, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('history', $this->history, true);
        $criteria->compare('modify_id', $this->modify_id, true);
        $criteria->compare('modify_time', $this->modify_time, true);
        $criteria->compare('create_id', $this->create_id, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('fromtemp', $this->fromtemp);
        $criteria->compare('admitted_baby', $this->admitted_baby);
        $criteria->compare('senior_ID', $this->senior_ID, true);
        $criteria->compare('veteran_ID', $this->veteran_ID, true);
        $criteria->compare('is_indigent', $this->is_indigent);
        $criteria->compare('DOH_ID', $this->DOH_ID, true);
        $criteria->compare('age', $this->age);
        $criteria->compare('name_search', $this->name_search, true);
        $criteria->compare('soundex_namelast', $this->soundex_namelast, true);
        $criteria->compare('soundex_namefirst', $this->soundex_namefirst, true);
        $criteria->compare('is_temp_bdate', $this->is_temp_bdate);
        $criteria->compare('source', $this->source, true);
        $criteria->compare('homis_id', $this->homis_id, true);

        return new CActiveDataProvider(
            $this, array(
                'criteria' => $criteria,
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className active record class name.
     *
     * @return CarePerson the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function getBMICategory($pid, $height, $weight)
    {
        $criteria = new CDbCriteria();
        $criteria->select = 't.date_birth';
        $criteria->addCondition('t.pid=:pid');
        $criteria->params = array(
            ':pid' => $pid
        );

        $data =  $this->find($criteria);
        $birthDate = strtotime($data->date_birth);
        $currentDate = strtotime(date('Y-m-d'));

        $diff = $currentDate - $birthDate;
        $months = floor(floatval($diff) / (60 * 60 * 24 * 365 / 12));

        $h = (int)$height;
        $w = (int)$weight;

        if($h || $w) {
            $height = number_format($height, 2);
            $weight = number_format($weight, 2);
            $metric = ($weight / ($height * $height) * 10000);
        } else {
            $metric = 0;
        }

        $val = round($metric,2);

        $eighteen = 216;
        $seventeen = 215;
        $five = 60;

        if ($months >= $eighteen) {
            $bmi = $val;
            $category = EncounterVitalSignBmi::model()->getCategory($val);
            $category = $bmi. ' - '.$category;
        } elseif ($months >= $five && $months <= $seventeen) {
            $category = $val;
        } else {
            $category = 'No results';
        }

        return $category;
    }
}