<?php
/**
 *
 */

Yii::import('application.models.address.AddressBarangay');
Yii::import('application.models.address.AddressMunicipality');

/**
 * This is the model class for table "care_person".
 *
 * @property  date_reg
 */
class Person extends CareActiveRecord
{

    /**
     * What is this doing here???
     */
	const PHIC_HCARE_ID = 18;

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
            array('pid, name_first, name_middle, name_last, suffix, date_birth, sex', 'safe', 'on' => 'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
    public function relations()
    {
		return array(
			'encounter'=>array(self::HAS_MANY, 'Encounter', 'pid', 'order' => 'encounter.encounter_nr DESC'),
            'currentEncounter' => array(self::HAS_ONE, 'Encounter', 'pid',
                'condition' => 'currentEncounter.is_discharged IS NULL OR currentEncounter.is_discharged = 0', 
                'order' => 'currentEncounter.encounter_date DESC'
            ),
            'barangay'=> array(self::BELONGS_TO, 'AddressBarangay', 'brgy_nr'),
            'municipality'=> array(self::BELONGS_TO, 'AddressMunicipality', 'mun_nr'),
            'work' => array(self::HAS_ONE,'Occupation', array('occupation_nr' => 'occupation')),
            'country' => array(self::HAS_ONE,'AddressCountry',array('country_code'=>'citizenship'))
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
    public function attributeLabels()
    {

		return array(
			'pid' => 'HRN',
			'date_reg' => 'Date Reg',
			'name_first' => 'First Name',
			'name_middle' => 'Middle Name',
			'name_last' => 'Last Name',
			'suffix' => 'Suffix',
			'name_maiden' => 'Maiden Name',
			'name_others' => 'Other Names',
			'title' => 'Title',
            'contact_nos' => 'Contact Number',
			'date_birth' => 'Date of Birth',
			'birth_time' => 'Time of Birth',
			'place_birth' => 'Place of Birth',
			'blood_group' => 'Blood Group',
			'street_name' => 'Street Name',
			'brgy_nr' => 'Barangay',
			'mun_nr' => 'Municipality',
			'citizenship' => 'Citizenship',
			'occupation' => 'Occupation',
            'employer' => 'Employer',
			'sex' => 'Sex',
		);
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

    /**
     *
     * @return string
     */
    public function getFullName()
    {
        $name = '';
        if ($this->name_last) {
            $name .= $this->name_last;
        }

        if ($this->name_first) {
            $name .= ', ' . $this->getNameFirst();
        }

        if ($this->name_middle) {
            $name .= ' ' . substr($this->name_middle,0,1) . '.';
        }

        if ($this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        if ($name) {
            /* not sure?? */
            if(mb_detect_encoding($name) == 'UTF-8') {
                // return strtoupper(utf8_encode($name));
            }
            return strtoupper($name);
        } else {
            return null;
        }
	}

    /**
     *
     * @return string
     */
    public function getFullNameSuffix()
    {
        $name = '';
        if ($this->name_last) {
            $name .= $this->name_last;
        }

        if ($this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        
        if ($this->name_first) {
            $name .= ', ' . $this->getNameFirst();
        }

        if ($this->name_middle) {
            $name .= ' ' . substr($this->name_middle,0,1) . '.';
        }

        if ($name) {
            /* not sure?? */
            if(mb_detect_encoding($name) == 'UTF-8') {
                // return strtoupper(utf8_encode($name));
            }
            return strtoupper($name);
        } else {
            return null;
        }
    }

    /**
     *
     * @return string
     */
    public function getSex()
    {
		if (strtoupper($this->sex)=='F'){
			return 'Female';
		} else if (strtoupper($this->sex)=='M'){
			return 'Male';
		} else {
            return null;
        }
	}

    /**
     * @author syboy 01/10/2016 : meow
     * @return string
     */
    public function getOccupation()
    {
        return $this->work->occupation_name;
    }

    /**
     *
     * @return int
     */
    public function getPID()
    {
		if(empty($this->pid)){
			return null;
		} else{
			return $this->pid;
		}
	}

    /**
     * Returns the current age of the person in years
     * @return int
     */
    public function getAge()
    {
		$datetime1 = new DateTime($this->date_birth);
		$datetime2 = new DateTime();
		$diff = $datetime1->diff($datetime2);
		if ($diff->y) {
            return $diff->y . ' year/s old';
        } elseif ($diff->m) {
            return $diff->m . ' month/s old';
        } else {
            return $diff->d . ' day/s old';
        }
	}

    /**
     * Returns the full address of the member based on the assigned
     * barangay or municipality.
     *
     * @return string|null
     */
    public function getFullAddress()
    {
        if ($this->street_name) {
            $address = $this->street_name . ' ';
        } else {
            $address = '';
        }
        if ($this->barangay) {
            return $address . $this->barangay->getFullName();
        } elseif ($this->municipality) {
            if($this->municipality->getFullName() == 'NOT PROVIDED, NOT PROVIDED, NOT PROVIDED'){
                return $address;
            }else {
                return $address . $this->municipality->getFullName();
            }
        } else {
            return $address;
        }
    }

    /**
     * @param $name String
     * @author Jolly Caralos
     */
    private static function toArrayNameSuffix($name)
    {
        if(!empty($name)) {
            $splitName = preg_split('/^([^,]+)/', $name,
                -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        }
        return empty($splitName) ? array() : $splitName;
    }

    /**
     * @author Jolly Caralos
     */
    public function getNameFirst()
    {
        // added by carriane 08/20/18
        if($this->suffix){
            $this->name_first = str_replace(' '.$this->suffix, '', $this->name_first);
        }
        // end carriane

        if(!empty($this->name_first)) {
            /* Is suffix exists in firstname? */
            $splitName = self::toArrayNameSuffix($this->name_first);

            return empty($splitName[0]) ? $this->name_first : $splitName[0];
        }
        return $this->name_first;
    }

    public function getSuffix()
    {
        /* Is suffix exists in firstname? */
        /* Mod by jeff 03-23-18 */
        if ($this->suffix != NULL || !(empty($this->suffix))){
                $suffixText = $this->suffix;
                $suffixId = str_replace(".", "", $suffixText);   
            return $suffixId;
        }else{
            $splitName = self::toArrayNameSuffix($this->name_first);
            return empty($splitName[1]) ? '' : $splitName[1];
        }
    }

    /**
     *
     * @param string $time
     * @param string $format
     * @return string
     */
    protected function formatDateValue($time = 'now', $format = 'YmdHis')
    {
        $dt = new DateTime($time);
        return $dt->format($format);
    }

    /**
     * Returns 'T' if patient is dead else 'F'
     * @return string
     */
    public function getDeathStatus()
    {
        if($this->death_encounter_nr != '0') {
            return 'T';
        } else {
            return 'F';
        }
    }

    /**
     * Determine if the patient is dead.
     *
     * @return bool True if dead, otherwise false.
     */
    public function isDead()
    {
        return $this->getDeathStatus() == 'T';
    }

    /**
    * Created By Alvin Jay Cosare
    * Created On 06/14/2014
    * Get Patient Full First Name
    * @return string full first name
    **/
    public function getFullFirstName()
    {

        if($this->name_first) {
            $name = $this->name_first;
        } elseif($this->name_2) {
            $name .= $name.' '.$this->name_2;
        } elseif($this->name_3) {
            $name = $name.' '.$this->name_3;
        }

        return $name;
    }

    /**
     * Returns match search patient
     * @author michelle 03-02-15
     * @return CActiveDataProvider
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        if (isset($this->name_last)) {
            $criteria->addSearchCondition('name_last', $this->name_last . '%', false);
        }
        if (isset($this->name_first)) {
            $criteria->addSearchCondition('name_first', $this->name_first . '%', false);
        }
        if (isset($this->pid)) {
            $criteria->addColumnCondition(array(
                'pid' => $this->pid
            ));
        }
        $dp = new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
              'pageSize' => 10,
            )
        ));
        return $dp;
    }

    /**
     * Checks if a given query string is in the recommended format:
     *     LASTNAME, FIRSTNAME or
     *     HRN
     *
     * @param  string $query [description]
     * @return boolean [description]
     */
    public static function isValidQuery($query) {
        if (is_numeric($query)) {
            return true;
        } else {
            return preg_match('~^[[:alpha:]]{2}[[:alpha:]\-\. ]*\s*,\s*[[:alpha:]]{2}[[:alpha:]\-\. ]*$~', $query);
        }
    }

    /**
    * Created By Jarel
    * Created On 03/12/2014
    * Get Patient Death date
    * @param string enc
    * @return string death date
    **/
    function getDeathDate($enc)
    {
        global $db;
        $strSQL = $db->Prepare("SELECT CONCAT(p.death_date,' ',p.death_time) as deathdate
            FROM care_person p
            WHERE death_encounter_nr = ?");

        if($result=$db->Execute($strSQL,$enc)) {
             $row = $result->FetchRow();
                return $row['deathdate'];
        } else {
            return false;
        }
    }


}
