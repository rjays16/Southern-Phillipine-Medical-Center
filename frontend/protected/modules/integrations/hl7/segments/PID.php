<?php

/**
 * PID.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\helpers\HL7;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of PID
 *
 */

class PID extends Segment
{
    const INDEX_PATIENT_ID = 2;
    const INDEX_PATIENT_IDENTIFIER_LIST = 3;
    const INDEX_ALTERNATE_PATIENT_ID = 4;
    const INDEX_PATIENT_NAME = 5;
    const INDEX_MOTHER_MAIDEN_NAME = 6;
    const INDEX_DATE_TIME_OF_BIRTH = 7;
    const INDEX_SEX = 8;
    const INDEX_PATIENT_ALIAS = 9;
    const INDEX_RACE = 10;
    const INDEX_PATIENT_ADDRESS = 11;
    const INDEX_COUNTRY_CODE = 12;
    const INDEX_HOME_PHONE_NUMBER = 13;
    const INDEX_BUSINESS_PHONE_NUMBER = 14;
    const INDEX_PRIMARY_LANGUAGE = 15;
    const INDEX_MARITAL_STATUS = 16;
    const INDEX_RELIGION = 17;
    const INDEX_PATIENT_ACCOUNT_NUMBER = 18;
    const INDEX_SSN_NUMBER = 19;
    const INDEX_DRIVERS_LICENSE_NUMBER = 20;
    const INDEX_MOTHER_IDENTIFIER = 21;
    const INDEX_ETHNIC_GROUP = 22;
    const INDEX_BIRTH_PLACE = 23;
    const INDEX_MULTIPLE_BIRTH_INDICATOR = 24;
    const INDEX_BIRTH_ORDER = 25;
    const INDEX_CITIZENSHIP = 26;
    const INDEX_VETERAN_MILITARY_STATUS = 27;
    const INDEX_NATIONALITY = 28;
    const INDEX_PATIENT_DEATH_AND_TIME = 29;
    const INDEX_PATIENT_DEATH_INDICATOR = 30;
    const INDEX_IDENTITY_UNKNOWN_INDICATOR = 31;
    const INDEX_IDENTITY_RELIABILITY_CODE = 32;
    const INDEX_LAST_UPDATE_DATE_TIME = 33;
    const INDEX_LAST_UPDATE_FACILITY = 34;
    const INDEX_TAXONOMIC_CLASSIFICATION_CODE = 35;
    const INDEX_BREED_CODE = 36;
    const INDEX_STRAIN = 37;
    const INDEX_PRODUCTION_CLASS_CODE = 38;
    const INDEX_TRIBAL_CITIZENSHIP = 39;
    const INDEX_PATIENT_TELECOMMUNICATION_INFORMATION = 40;

    public $name = 'PID';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientId($value)
    {
        $this->setField(self::INDEX_PATIENT_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientId()
    {
        return $this->getField(self::INDEX_PATIENT_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientIdentifierList($value)
    {
        $this->setField(self::INDEX_PATIENT_IDENTIFIER_LIST, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientIdentifierList()
    {
        return $this->getField(self::INDEX_PATIENT_IDENTIFIER_LIST);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAlternatePatientId($value)
    {
        $this->setField(self::INDEX_ALTERNATE_PATIENT_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAlternatePatientId()
    {
        return $this->getField(self::INDEX_ALTERNATE_PATIENT_ID);
    }

    /**
     * @param $familyName
     * @param string $givenName
     * @param string $middleName
     * @param string $suffix
     * @param string $prefix
     *
     * @return static
     */
    public function setPatientName($familyName, $givenName='', $middleName=null, $suffix=null, $prefix=null)
    {
        $values = array(
            $familyName,
            $givenName,
            $middleName,
            $suffix,
            $prefix
        );
        $this->setField(self::INDEX_PATIENT_NAME, HL7::encodeValues($values));
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientName()
    {
        return $this->getField(self::INDEX_PATIENT_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMotherMaidenName($value)
    {
        $this->setField(self::INDEX_MOTHER_MAIDEN_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMotherMaidenName()
    {
        return $this->getField(self::INDEX_MOTHER_MAIDEN_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDateTimeOfBirth($value)
    {
        $this->setField(self::INDEX_DATE_TIME_OF_BIRTH, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSex()
    {
        return $this->getField(self::INDEX_SEX);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSex($value)
    {
        $this->setField(self::INDEX_SEX, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDateTimeOfBirth()
    {
        return $this->getField(self::INDEX_DATE_TIME_OF_BIRTH);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientAlias($value)
    {
        $this->setField(self::INDEX_PATIENT_ALIAS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientAlias()
    {
        return $this->getField(self::INDEX_PATIENT_ALIAS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRace($value)
    {
        $this->setField(self::INDEX_RACE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRace()
    {
        return $this->getField(self::INDEX_RACE);
    }

    /**
     * @param string $streetAddress
     * @param string $otherDesignation
     * @param string $city
     * @param string $stateProvince
     * @param string $zipPostalCode
     * @param string $country
     * @param string $addressType
     * @param string $otherGeographicDesignation
     *
     * @return static
     */
    public function setPatientAddress(
        $streetAddress='',
        $otherDesignation='',
        $city='',
        $stateProvince='',
        $zipPostalCode='',
        $country='',
        $addressType='',
        $otherGeographicDesignation=''
    )
    {
        $values = array(
            $streetAddress,
            $otherDesignation,
            $city,
            $stateProvince,
            $zipPostalCode,
            $country,
            $addressType,
            $otherGeographicDesignation
        );               

        $this->setField(self::INDEX_PATIENT_ADDRESS, HL7::encodeValues($values));
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientAddress()
    {
        return $this->getField(self::INDEX_PATIENT_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCountryCode($value)
    {
        $this->setField(self::INDEX_COUNTRY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->getField(self::INDEX_COUNTRY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setHomePhoneNumber($value)
    {
        $this->setField(self::INDEX_HOME_PHONE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getHomePhoneNumber()
    {
        return $this->getField(self::INDEX_HOME_PHONE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBusinessPhoneNumber($value)
    {
        $this->setField(self::INDEX_BUSINESS_PHONE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessPhoneNumber()
    {
        return $this->getField(self::INDEX_BUSINESS_PHONE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPrimaryLanguage($value)
    {
        $this->setField(self::INDEX_PRIMARY_LANGUAGE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrimaryLanguage()
    {
        return $this->getField(self::INDEX_PRIMARY_LANGUAGE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMaritalStatus($value)
    {
        $this->setField(self::INDEX_MARITAL_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->getField(self::INDEX_MARITAL_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReligion($value)
    {
        $this->setField(self::INDEX_RELIGION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReligion()
    {
        return $this->getField(self::INDEX_RELIGION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientAccountNumber($value)
    {
        $this->setField(self::INDEX_PATIENT_ACCOUNT_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientAccountNumber()
    {
        return $this->getField(self::INDEX_PATIENT_ACCOUNT_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSsnNumber($value)
    {
        $this->setField(self::INDEX_SSN_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSsnNumber()
    {
        return $this->getField(self::INDEX_SSN_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDriversLicenseNumber($value)
    {
        $this->setField(self::INDEX_DRIVERS_LICENSE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDriversLicenseNumber()
    {
        return $this->getField(self::INDEX_DRIVERS_LICENSE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMotherIdentifier($value)
    {
        $this->setField(self::INDEX_MOTHER_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMotherIdentifier()
    {
        return $this->getField(self::INDEX_MOTHER_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEthnicGroup($value)
    {
        $this->setField(self::INDEX_ETHNIC_GROUP, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEthnicGroup()
    {
        return $this->getField(self::INDEX_ETHNIC_GROUP);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBirthPlace($value)
    {
        $this->setField(self::INDEX_BIRTH_PLACE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBirthPlace()
    {
        return $this->getField(self::INDEX_BIRTH_PLACE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMultipleBirthIndicator($value)
    {
        $this->setField(self::INDEX_MULTIPLE_BIRTH_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMultipleBirthIndicator()
    {
        return $this->getField(self::INDEX_MULTIPLE_BIRTH_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBirthOrder($value)
    {
        $this->setField(self::INDEX_BIRTH_ORDER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBirthOrder()
    {
        return $this->getField(self::INDEX_BIRTH_ORDER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCitizenship($value)
    {
        $this->setField(self::INDEX_CITIZENSHIP, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCitizenship()
    {
        return $this->getField(self::INDEX_CITIZENSHIP);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVeteranMilitaryStatus($value)
    {
        $this->setField(self::INDEX_VETERAN_MILITARY_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVeteranMilitaryStatus()
    {
        return $this->getField(self::INDEX_VETERAN_MILITARY_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNationality($value)
    {
        $this->setField(self::INDEX_NATIONALITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNationality()
    {
        return $this->getField(self::INDEX_NATIONALITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientDeathAndTime($value)
    {
        $this->setField(self::INDEX_PATIENT_DEATH_AND_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientDeathAndTime()
    {
        return $this->getField(self::INDEX_PATIENT_DEATH_AND_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientDeathIndicator($value)
    {
        $this->setField(self::INDEX_PATIENT_DEATH_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientDeathIndicator()
    {
        return $this->getField(self::INDEX_PATIENT_DEATH_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setIdentityUnknownIndicator($value)
    {
        $this->setField(self::INDEX_IDENTITY_UNKNOWN_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentityUnknownIndicator()
    {
        return $this->getField(self::INDEX_IDENTITY_UNKNOWN_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setIdentityReliabilityCode($value)
    {
        $this->setField(self::INDEX_IDENTITY_RELIABILITY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getIdentityReliabilityCode()
    {
        return $this->getField(self::INDEX_IDENTITY_RELIABILITY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setLastUpdateDateTime($value)
    {
        $this->setField(self::INDEX_LAST_UPDATE_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdateDateTime()
    {
        return $this->getField(self::INDEX_LAST_UPDATE_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setLastUpdateFacility($value)
    {
        $this->setField(self::INDEX_LAST_UPDATE_FACILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getLastUpdateFacility()
    {
        return $this->getField(self::INDEX_LAST_UPDATE_FACILITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTaxonomicClassificationCode($value)
    {
        $this->setField(self::INDEX_TAXONOMIC_CLASSIFICATION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxonomicClassificationCode()
    {
        return $this->getField(self::INDEX_TAXONOMIC_CLASSIFICATION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBreedCode($value)
    {
        $this->setField(self::INDEX_BREED_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBreedCode()
    {
        return $this->getField(self::INDEX_BREED_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setStrain($value)
    {
        $this->setField(self::INDEX_STRAIN, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getStrain()
    {
        return $this->getField(self::INDEX_STRAIN);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setProductionClassCode($value)
    {
        $this->setField(self::INDEX_PRODUCTION_CLASS_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getProductionClassCode()
    {
        return $this->getField(self::INDEX_PRODUCTION_CLASS_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTribalCitizenship($value)
    {
        $this->setField(self::INDEX_TRIBAL_CITIZENSHIP, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTribalCitizenship()
    {
        return $this->getField(self::INDEX_TRIBAL_CITIZENSHIP);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientTelecommunicationInformation($value)
    {
        $this->setField(self::INDEX_PATIENT_TELECOMMUNICATION_INFORMATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientTelecommunicationInformation()
    {
        return $this->getField(self::INDEX_PATIENT_TELECOMMUNICATION_INFORMATION);
    }

}
