<?php

/**
 * IN1.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of IN1
 *
 */

class IN1 extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_HEALTH_PLAN_ID = 2;
    const INDEX_INSURANCE_COMPANY_ID = 3;
    const INDEX_INSURANCE_COMPANY_NAME = 4;
    const INDEX_INSURANCE_COMPANY_ADDRESS = 5;
    const INDEX_INSURANCE_COMPANY_CONTACT_PERSON = 6;
    const INDEX_INSURANCE_COMPANY_PHONE_NUMBER = 7;
    const INDEX_GROUP_NUMBER = 8;
    const INDEX_GROUP_NAME = 9;
    const INDEX_INSURED_GROUP_EMPLOYEE_ID = 10;
    const INDEX_INSURED_GROUP_EMPLOYEE_NAME = 11;
    const INDEX_PLAN_EFFECTIVE_DATE = 12;
    const INDEX_PLAN_EXPIRATION_DATE = 13;
    const INDEX_AUTHORIZATION_INFORMATION = 14;
    const INDEX_PLAN_TYPE = 15;
    const INDEX_NAME_OF_INSURED = 16;
    const INDEX_INSURED_RELATIONSHIP_TO_PATIENT = 17;
    const INDEX_INSURED_DATE_OF_BIRTH = 18;
    const INDEX_INSURED_ADDRESS = 19;
    const INDEX_ASSIGNMENT_OF_BENEFITS = 20;
    const INDEX_COORDINATION_OF_BENEFITS = 21;
    const INDEX_COORDINATION_OF_BENEFITS_PRIORITY = 22;
    const INDEX_NOTICE_OF_ADMISSION_FLAG = 23;
    const INDEX_NOTICE_OF_ADMISSION_DATE = 24;
    const INDEX_REPORT_OF_ELIGIBILITY_FLAG = 25;
    const INDEX_REPORT_OF_ELIGIBILITY_DATE = 26;
    const INDEX_RELEASE_INFORMATION_CODE = 27;
    const INDEX_PREADMIT_CERTIFICATE = 28;
    const INDEX_VERIFICATION_DATE_TIME = 29;
    const INDEX_VERIFICATION_BY = 30;
    const INDEX_TYPE_OF_AGREEMENT_CODE = 31;
    const INDEX_BILLING_STATUS = 32;
    const INDEX_LIFETIME_RESERVE_DAYS = 33;
    const INDEX_BELAY_BEFORE_LIFETIME_RESERVE_DAY = 34;
    const INDEX_COMPANY_PLAN_CODE = 35;
    const INDEX_POLICY_NUMBER = 36;
    const INDEX_POLICY_DEDUCTIBLE = 37;
    const INDEX_POLICY_LIMIT_AMOUNT = 38;
    const INDEX_POLICY_LIMIT_DAYS = 39;
    const INDEX_ROOM_RATE_SEMI_PRIVATE = 40;
    const INDEX_ROOM_RATE_PRIVATE = 41;
    const INDEX_INSURED_EMPLOYMENT_STATUS = 42;
    const INDEX_INSURED_ADMINISTRATIVE_SEX = 43;
    const INDEX_INSURED_EMPLOYER_ADDRESS = 44;
    const INDEX_VERIFICATION_STATUS = 45;
    const INDEX_PRIOR_INSURANCE_PLAN_ID = 46;
    const INDEX_COVERAGE_TYPE = 47;
    const INDEX_HANDICAP = 48;
    const INDEX_INSURED_ID_NUMBER = 49;
    const INDEX_SIGNATURE_CODE = 50;
    const INDEX_SIGNATURE_CODE_DATE = 51;
    const INDEX_INSURED_BIRTH_PLACE = 52;
    const INDEX_VIP_INDICATOR = 53;
    const INDEX_EXTERNAL_HEALTH_PLAN_IDENTIFIERS = 54;
    const INDEX_INSURANCE_ACTION_CODE = 55;

    /** @var string */
    public $name = 'IN1';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSequenceId($value)
    {
        $this->setField(self::INDEX_SEQUENCE_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSequenceId()
    {
        return $this->getField(self::INDEX_SEQUENCE_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setHealthPlanId($value)
    {
        $this->setField(self::INDEX_HEALTH_PLAN_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getHealthPlanId()
    {
        return $this->getField(self::INDEX_HEALTH_PLAN_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuranceCompanyId($value)
    {
        $this->setField(self::INDEX_INSURANCE_COMPANY_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceCompanyId()
    {
        return $this->getField(self::INDEX_INSURANCE_COMPANY_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuranceCompanyName($value)
    {
        $this->setField(self::INDEX_INSURANCE_COMPANY_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceCompanyName()
    {
        return $this->getField(self::INDEX_INSURANCE_COMPANY_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuranceCompanyAddress($value)
    {
        $this->setField(self::INDEX_INSURANCE_COMPANY_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceCompanyAddress()
    {
        return $this->getField(self::INDEX_INSURANCE_COMPANY_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuranceCompanyContactPerson($value)
    {
        $this->setField(self::INDEX_INSURANCE_COMPANY_CONTACT_PERSON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceCompanyContactPerson()
    {
        return $this->getField(self::INDEX_INSURANCE_COMPANY_CONTACT_PERSON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuranceCompanyPhoneNumber($value)
    {
        $this->setField(self::INDEX_INSURANCE_COMPANY_PHONE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceCompanyPhoneNumber()
    {
        return $this->getField(self::INDEX_INSURANCE_COMPANY_PHONE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setGroupNumber($value)
    {
        $this->setField(self::INDEX_GROUP_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupNumber()
    {
        return $this->getField(self::INDEX_GROUP_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setGroupName($value)
    {
        $this->setField(self::INDEX_GROUP_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getGroupName()
    {
        return $this->getField(self::INDEX_GROUP_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredGroupEmployeeId($value)
    {
        $this->setField(self::INDEX_INSURED_GROUP_EMPLOYEE_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredGroupEmployeeId()
    {
        return $this->getField(self::INDEX_INSURED_GROUP_EMPLOYEE_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredGroupEmployeeName($value)
    {
        $this->setField(self::INDEX_INSURED_GROUP_EMPLOYEE_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredGroupEmployeeName()
    {
        return $this->getField(self::INDEX_INSURED_GROUP_EMPLOYEE_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlanEffectiveDate($value)
    {
        $this->setField(self::INDEX_PLAN_EFFECTIVE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlanEffectiveDate()
    {
        return $this->getField(self::INDEX_PLAN_EFFECTIVE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlanExpirationDate($value)
    {
        $this->setField(self::INDEX_PLAN_EXPIRATION_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlanExpirationDate()
    {
        return $this->getField(self::INDEX_PLAN_EXPIRATION_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAuthorizationInformation($value)
    {
        $this->setField(self::INDEX_AUTHORIZATION_INFORMATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorizationInformation()
    {
        return $this->getField(self::INDEX_AUTHORIZATION_INFORMATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlanType($value)
    {
        $this->setField(self::INDEX_PLAN_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlanType()
    {
        return $this->getField(self::INDEX_PLAN_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNameOfInsured($value)
    {
        $this->setField(self::INDEX_NAME_OF_INSURED, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNameOfInsured()
    {
        return $this->getField(self::INDEX_NAME_OF_INSURED);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredRelationshipToPatient($value)
    {
        $this->setField(self::INDEX_INSURED_RELATIONSHIP_TO_PATIENT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredRelationshipToPatient()
    {
        return $this->getField(self::INDEX_INSURED_RELATIONSHIP_TO_PATIENT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredDateOfBirth($value)
    {
        $this->setField(self::INDEX_INSURED_DATE_OF_BIRTH, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredDateOfBirth()
    {
        return $this->getField(self::INDEX_INSURED_DATE_OF_BIRTH);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredAddress($value)
    {
        $this->setField(self::INDEX_INSURED_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredAddress()
    {
        return $this->getField(self::INDEX_INSURED_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAssignmentOfBenefits($value)
    {
        $this->setField(self::INDEX_ASSIGNMENT_OF_BENEFITS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignmentOfBenefits()
    {
        return $this->getField(self::INDEX_ASSIGNMENT_OF_BENEFITS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCoordinationOfBenefits($value)
    {
        $this->setField(self::INDEX_COORDINATION_OF_BENEFITS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCoordinationOfBenefits()
    {
        return $this->getField(self::INDEX_COORDINATION_OF_BENEFITS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCoordinationOfBenefitsPriority($value)
    {
        $this->setField(self::INDEX_COORDINATION_OF_BENEFITS_PRIORITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCoordinationOfBenefitsPriority()
    {
        return $this->getField(self::INDEX_COORDINATION_OF_BENEFITS_PRIORITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNoticeOfAdmissionFlag($value)
    {
        $this->setField(self::INDEX_NOTICE_OF_ADMISSION_FLAG, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNoticeOfAdmissionFlag()
    {
        return $this->getField(self::INDEX_NOTICE_OF_ADMISSION_FLAG);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNoticeOfAdmissionDate($value)
    {
        $this->setField(self::INDEX_NOTICE_OF_ADMISSION_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNoticeOfAdmissionDate()
    {
        return $this->getField(self::INDEX_NOTICE_OF_ADMISSION_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReportOfEligibilityFlag($value)
    {
        $this->setField(self::INDEX_REPORT_OF_ELIGIBILITY_FLAG, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReportOfEligibilityFlag()
    {
        return $this->getField(self::INDEX_REPORT_OF_ELIGIBILITY_FLAG);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReportOfEligibilityDate($value)
    {
        $this->setField(self::INDEX_REPORT_OF_ELIGIBILITY_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReportOfEligibilityDate()
    {
        return $this->getField(self::INDEX_REPORT_OF_ELIGIBILITY_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReleaseInformationCode($value)
    {
        $this->setField(self::INDEX_RELEASE_INFORMATION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReleaseInformationCode()
    {
        return $this->getField(self::INDEX_RELEASE_INFORMATION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPreadmitCertificate($value)
    {
        $this->setField(self::INDEX_PREADMIT_CERTIFICATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPreadmitCertificate()
    {
        return $this->getField(self::INDEX_PREADMIT_CERTIFICATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVerificationDateTime($value)
    {
        $this->setField(self::INDEX_VERIFICATION_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationDateTime()
    {
        return $this->getField(self::INDEX_VERIFICATION_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVerificationBy($value)
    {
        $this->setField(self::INDEX_VERIFICATION_BY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationBy()
    {
        return $this->getField(self::INDEX_VERIFICATION_BY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTypeOfAgreementCode($value)
    {
        $this->setField(self::INDEX_TYPE_OF_AGREEMENT_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeOfAgreementCode()
    {
        return $this->getField(self::INDEX_TYPE_OF_AGREEMENT_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBillingStatus($value)
    {
        $this->setField(self::INDEX_BILLING_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingStatus()
    {
        return $this->getField(self::INDEX_BILLING_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setLifetimeReserveDays($value)
    {
        $this->setField(self::INDEX_LIFETIME_RESERVE_DAYS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getLifetimeReserveDays()
    {
        return $this->getField(self::INDEX_LIFETIME_RESERVE_DAYS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBelayBeforeLifetimeReserveDay($value)
    {
        $this->setField(self::INDEX_BELAY_BEFORE_LIFETIME_RESERVE_DAY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBelayBeforeLifetimeReserveDay()
    {
        return $this->getField(self::INDEX_BELAY_BEFORE_LIFETIME_RESERVE_DAY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCompanyPlanCode($value)
    {
        $this->setField(self::INDEX_COMPANY_PLAN_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyPlanCode()
    {
        return $this->getField(self::INDEX_COMPANY_PLAN_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPolicyNumber($value)
    {
        $this->setField(self::INDEX_POLICY_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->getField(self::INDEX_POLICY_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPolicyDeductible($value)
    {
        $this->setField(self::INDEX_POLICY_DEDUCTIBLE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPolicyDeductible()
    {
        return $this->getField(self::INDEX_POLICY_DEDUCTIBLE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPolicyLimitAmount($value)
    {
        $this->setField(self::INDEX_POLICY_LIMIT_AMOUNT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPolicyLimitAmount()
    {
        return $this->getField(self::INDEX_POLICY_LIMIT_AMOUNT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPolicyLimitDays($value)
    {
        $this->setField(self::INDEX_POLICY_LIMIT_DAYS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPolicyLimitDays()
    {
        return $this->getField(self::INDEX_POLICY_LIMIT_DAYS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRoomRateSemiPrivate($value)
    {
        $this->setField(self::INDEX_ROOM_RATE_SEMI_PRIVATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRoomRateSemiPrivate()
    {
        return $this->getField(self::INDEX_ROOM_RATE_SEMI_PRIVATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRoomRatePrivate($value)
    {
        $this->setField(self::INDEX_ROOM_RATE_PRIVATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRoomRatePrivate()
    {
        return $this->getField(self::INDEX_ROOM_RATE_PRIVATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredEmploymentStatus($value)
    {
        $this->setField(self::INDEX_INSURED_EMPLOYMENT_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredEmploymentStatus()
    {
        return $this->getField(self::INDEX_INSURED_EMPLOYMENT_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredAdministrativeSex($value)
    {
        $this->setField(self::INDEX_INSURED_ADMINISTRATIVE_SEX, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredAdministrativeSex()
    {
        return $this->getField(self::INDEX_INSURED_ADMINISTRATIVE_SEX);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredEmployerAddress($value)
    {
        $this->setField(self::INDEX_INSURED_EMPLOYER_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredEmployerAddress()
    {
        return $this->getField(self::INDEX_INSURED_EMPLOYER_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVerificationStatus($value)
    {
        $this->setField(self::INDEX_VERIFICATION_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVerificationStatus()
    {
        return $this->getField(self::INDEX_VERIFICATION_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPriorInsurancePlanId($value)
    {
        $this->setField(self::INDEX_PRIOR_INSURANCE_PLAN_ID, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPriorInsurancePlanId()
    {
        return $this->getField(self::INDEX_PRIOR_INSURANCE_PLAN_ID);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCoverageType($value)
    {
        $this->setField(self::INDEX_COVERAGE_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCoverageType()
    {
        return $this->getField(self::INDEX_COVERAGE_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setHandicap($value)
    {
        $this->setField(self::INDEX_HANDICAP, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getHandicap()
    {
        return $this->getField(self::INDEX_HANDICAP);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredIdNumber($value)
    {
        $this->setField(self::INDEX_INSURED_ID_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredIdNumber()
    {
        return $this->getField(self::INDEX_INSURED_ID_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSignatureCode($value)
    {
        $this->setField(self::INDEX_SIGNATURE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureCode()
    {
        return $this->getField(self::INDEX_SIGNATURE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSignatureCodeDate($value)
    {
        $this->setField(self::INDEX_SIGNATURE_CODE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureCodeDate()
    {
        return $this->getField(self::INDEX_SIGNATURE_CODE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuredBirthPlace($value)
    {
        $this->setField(self::INDEX_INSURED_BIRTH_PLACE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuredBirthPlace()
    {
        return $this->getField(self::INDEX_INSURED_BIRTH_PLACE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVipIndicator($value)
    {
        $this->setField(self::INDEX_VIP_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVipIndicator()
    {
        return $this->getField(self::INDEX_VIP_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExternalHealthPlanIdentifiers($value)
    {
        $this->setField(self::INDEX_EXTERNAL_HEALTH_PLAN_IDENTIFIERS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExternalHealthPlanIdentifiers()
    {
        return $this->getField(self::INDEX_EXTERNAL_HEALTH_PLAN_IDENTIFIERS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInsuranceActionCode($value)
    {
        $this->setField(self::INDEX_INSURANCE_ACTION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInsuranceActionCode()
    {
        return $this->getField(self::INDEX_INSURANCE_ACTION_CODE);
    }


}