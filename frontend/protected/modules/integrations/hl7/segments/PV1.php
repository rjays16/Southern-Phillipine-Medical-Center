<?php

/**
 * PV1.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of PV1
 *
 */

class PV1 extends Segment
{

    const INDEX_SEQUENCE_ID = 1;
    const INDEX_PATIENT_CLASS = 2;
    const INDEX_ASSIGNED_PATIENT_LOCATION = 3;
    const INDEX_ADMISSION_TYPE = 4;
    const INDEX_PREADMIT_NUMBER = 5;
    const INDEX_PRIOR_PATIENT_LOCATION = 6;
    const INDEX_ATTENDING_DOCTOR = 7;
    const INDEX_REFERRING_DOCTOR = 8;
    const INDEX_CONSULTING_DOCTOR = 9;
    const INDEX_HOSPITAL_SERVICE = 10;
    const INDEX_TEMPORARY_LOCATION = 11;
    const INDEX_PREADMIT_TEST_INDICATOR = 12;
    const INDEX_READMISSION_INDICATOR = 13;
    const INDEX_ADMIT_SOURCE = 14;
    const INDEX_AMBULATORY_STATUS = 15;
    const INDEX_VIP_INDICATOR = 16;
    const INDEX_ADMITTING_DOCTOR = 17;
    const INDEX_PATIENT_TYPE = 18;
    const INDEX_VISIT_NUMBER = 19;
    const INDEX_FINANCIAL_CLASS = 20;
    const INDEX_CHARGE_PRICE_INDICATOR = 21;
    const INDEX_COURTESY_CODE = 22;
    const INDEX_CREDIT_RATING = 23;
    const INDEX_CONTRACT_CODE = 24;
    const INDEX_CONTRACT_EFFECTIVE_DATE = 25;
    const INDEX_CONTRACT_AMOUNT = 26;
    const INDEX_CONTRACT_PERIOD = 27;
    const INDEX_INTEREST_CODE = 28;
    const INDEX_TRANSFER_TO_BAD_DEBT_CODE = 29;
    const INDEX_TRANSFER_TO_BAD_DEBT_DATE = 30;
    const INDEX_BAD_DEBT_AGENCY_CODE = 31;
    const INDEX_BAD_DEBT_TRANSFER_AMOUNT = 32;
    const INDEX_BAD_DEBT_RECOVERY_AMOUNT = 33;
    const INDEX_DELETE_ACCOUNT_INDICATOR = 34;
    const INDEX_DELETE_ACCOUNT_DATE = 35;
    const INDEX_DISCHARGE_DISPOSITION = 36;
    const INDEX_DISCHARGED_TO_LOCATION = 37;
    const INDEX_DIET_TYPE = 38;
    const INDEX_SERVICING_FACILITY = 39;
    const INDEX_BED_STATUS = 40;
    const INDEX_ACCOUNT_STATUS = 41;
    const INDEX_PENDING_LOCATION = 42;
    const INDEX_PRIOR_TEMPORARY_LOCATION = 43;
    const INDEX_ADMIT_DATE_TIME = 44;
    const INDEX_DISCHARGE_DATE_TIME = 45;
    const INDEX_CURRENT_PATIENT_BALANCE = 46;
    const INDEX_TOTAL_CHARGES = 47;
    const INDEX_TOTAL_ADJUSTMENTS = 48;
    const INDEX_TOTAL_PAYMENTS = 49;
    const INDEX_ALTERNATE_VISIT_I_D = 50;
    const INDEX_VISIT_INDICATOR = 51;
    const INDEX_OTHER_HEALTHCARE_PROVIDER = 52;
    const INDEX_SERVICE_EPISODE_DESCRIPTION = 53;
    const INDEX_SERVICE_EPISODE_IDENTIFIER = 54;

    public $name = 'PV1';

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
    public function setPatientClass($value)
    {
        $this->setField(self::INDEX_PATIENT_CLASS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientClass()
    {
        return $this->getField(self::INDEX_PATIENT_CLASS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAssignedPatientLocation($value)
    {
        $this->setField(self::INDEX_ASSIGNED_PATIENT_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAssignedPatientLocation()
    {
        return $this->getField(self::INDEX_ASSIGNED_PATIENT_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdmissionType($value)
    {
        $this->setField(self::INDEX_ADMISSION_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdmissionType()
    {
        return $this->getField(self::INDEX_ADMISSION_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPreadmitNumber($value)
    {
        $this->setField(self::INDEX_PREADMIT_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPreadmitNumber()
    {
        return $this->getField(self::INDEX_PREADMIT_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPriorPatientLocation($value)
    {
        $this->setField(self::INDEX_PRIOR_PATIENT_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPriorPatientLocation()
    {
        return $this->getField(self::INDEX_PRIOR_PATIENT_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAttendingDoctor($value)
    {
        $this->setField(self::INDEX_ATTENDING_DOCTOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAttendingDoctor()
    {
        return $this->getField(self::INDEX_ATTENDING_DOCTOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReferringDoctor($value)
    {
        $this->setField(self::INDEX_REFERRING_DOCTOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReferringDoctor()
    {
        return $this->getField(self::INDEX_REFERRING_DOCTOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setConsultingDoctor($value)
    {
        $this->setField(self::INDEX_CONSULTING_DOCTOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getConsultingDoctor()
    {
        return $this->getField(self::INDEX_CONSULTING_DOCTOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setHospitalService($value)
    {
        $this->setField(self::INDEX_HOSPITAL_SERVICE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getHospitalService()
    {
        return $this->getField(self::INDEX_HOSPITAL_SERVICE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTemporaryLocation($value)
    {
        $this->setField(self::INDEX_TEMPORARY_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTemporaryLocation()
    {
        return $this->getField(self::INDEX_TEMPORARY_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPreadmitTestIndicator($value)
    {
        $this->setField(self::INDEX_PREADMIT_TEST_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPreadmitTestIndicator()
    {
        return $this->getField(self::INDEX_PREADMIT_TEST_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReadmissionIndicator($value)
    {
        $this->setField(self::INDEX_READMISSION_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReadmissionIndicator()
    {
        return $this->getField(self::INDEX_READMISSION_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdmitSource($value)
    {
        $this->setField(self::INDEX_ADMIT_SOURCE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdmitSource()
    {
        return $this->getField(self::INDEX_ADMIT_SOURCE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAmbulatoryStatus($value)
    {
        $this->setField(self::INDEX_AMBULATORY_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAmbulatoryStatus()
    {
        return $this->getField(self::INDEX_AMBULATORY_STATUS);
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
    public function setAdmittingDoctor($value)
    {
        $this->setField(self::INDEX_ADMITTING_DOCTOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdmittingDoctor()
    {
        return $this->getField(self::INDEX_ADMITTING_DOCTOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientType($value)
    {
        $this->setField(self::INDEX_PATIENT_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientType()
    {
        return $this->getField(self::INDEX_PATIENT_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitNumber($value)
    {
        $this->setField(self::INDEX_VISIT_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitNumber()
    {
        return $this->getField(self::INDEX_VISIT_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFinancialClass($value)
    {
        $this->setField(self::INDEX_FINANCIAL_CLASS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFinancialClass()
    {
        return $this->getField(self::INDEX_FINANCIAL_CLASS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setChargePriceIndicator($value)
    {
        $this->setField(self::INDEX_CHARGE_PRICE_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getChargePriceIndicator()
    {
        return $this->getField(self::INDEX_CHARGE_PRICE_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCourtesyCode($value)
    {
        $this->setField(self::INDEX_COURTESY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCourtesyCode()
    {
        return $this->getField(self::INDEX_COURTESY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCreditRating($value)
    {
        $this->setField(self::INDEX_CREDIT_RATING, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCreditRating()
    {
        return $this->getField(self::INDEX_CREDIT_RATING);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setContractCode($value)
    {
        $this->setField(self::INDEX_CONTRACT_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getContractCode()
    {
        return $this->getField(self::INDEX_CONTRACT_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setContractEffectiveDate($value)
    {
        $this->setField(self::INDEX_CONTRACT_EFFECTIVE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getContractEffectiveDate()
    {
        return $this->getField(self::INDEX_CONTRACT_EFFECTIVE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setContractAmount($value)
    {
        $this->setField(self::INDEX_CONTRACT_AMOUNT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getContractAmount()
    {
        return $this->getField(self::INDEX_CONTRACT_AMOUNT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setContractPeriod($value)
    {
        $this->setField(self::INDEX_CONTRACT_PERIOD, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getContractPeriod()
    {
        return $this->getField(self::INDEX_CONTRACT_PERIOD);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setInterestCode($value)
    {
        $this->setField(self::INDEX_INTEREST_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getInterestCode()
    {
        return $this->getField(self::INDEX_INTEREST_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransferToBadDebtCode($value)
    {
        $this->setField(self::INDEX_TRANSFER_TO_BAD_DEBT_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransferToBadDebtCode()
    {
        return $this->getField(self::INDEX_TRANSFER_TO_BAD_DEBT_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransferToBadDebtDate($value)
    {
        $this->setField(self::INDEX_TRANSFER_TO_BAD_DEBT_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransferToBadDebtDate()
    {
        return $this->getField(self::INDEX_TRANSFER_TO_BAD_DEBT_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBadDebtAgencyCode($value)
    {
        $this->setField(self::INDEX_BAD_DEBT_AGENCY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBadDebtAgencyCode()
    {
        return $this->getField(self::INDEX_BAD_DEBT_AGENCY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBadDebtTransferAmount($value)
    {
        $this->setField(self::INDEX_BAD_DEBT_TRANSFER_AMOUNT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBadDebtTransferAmount()
    {
        return $this->getField(self::INDEX_BAD_DEBT_TRANSFER_AMOUNT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBadDebtRecoveryAmount($value)
    {
        $this->setField(self::INDEX_BAD_DEBT_RECOVERY_AMOUNT, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBadDebtRecoveryAmount()
    {
        return $this->getField(self::INDEX_BAD_DEBT_RECOVERY_AMOUNT);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDeleteAccountIndicator($value)
    {
        $this->setField(self::INDEX_DELETE_ACCOUNT_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDeleteAccountIndicator()
    {
        return $this->getField(self::INDEX_DELETE_ACCOUNT_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDeleteAccountDate($value)
    {
        $this->setField(self::INDEX_DELETE_ACCOUNT_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDeleteAccountDate()
    {
        return $this->getField(self::INDEX_DELETE_ACCOUNT_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDischargeDisposition($value)
    {
        $this->setField(self::INDEX_DISCHARGE_DISPOSITION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDischargeDisposition()
    {
        return $this->getField(self::INDEX_DISCHARGE_DISPOSITION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDischargedToLocation($value)
    {
        $this->setField(self::INDEX_DISCHARGED_TO_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDischargedToLocation()
    {
        return $this->getField(self::INDEX_DISCHARGED_TO_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDietType($value)
    {
        $this->setField(self::INDEX_DIET_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDietType()
    {
        return $this->getField(self::INDEX_DIET_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setServicingFacility($value)
    {
        $this->setField(self::INDEX_SERVICING_FACILITY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getServicingFacility()
    {
        return $this->getField(self::INDEX_SERVICING_FACILITY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBedStatus($value)
    {
        $this->setField(self::INDEX_BED_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBedStatus()
    {
        return $this->getField(self::INDEX_BED_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAccountStatus($value)
    {
        $this->setField(self::INDEX_ACCOUNT_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountStatus()
    {
        return $this->getField(self::INDEX_ACCOUNT_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPendingLocation($value)
    {
        $this->setField(self::INDEX_PENDING_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPendingLocation()
    {
        return $this->getField(self::INDEX_PENDING_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPriorTemporaryLocation($value)
    {
        $this->setField(self::INDEX_PRIOR_TEMPORARY_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPriorTemporaryLocation()
    {
        return $this->getField(self::INDEX_PRIOR_TEMPORARY_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdmitDateTime($value)
    {
        $this->setField(self::INDEX_ADMIT_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdmitDateTime()
    {
        return $this->getField(self::INDEX_ADMIT_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setDischargeDateTime($value)
    {
        $this->setField(self::INDEX_DISCHARGE_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getDischargeDateTime()
    {
        return $this->getField(self::INDEX_DISCHARGE_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCurrentPatientBalance($value)
    {
        $this->setField(self::INDEX_CURRENT_PATIENT_BALANCE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentPatientBalance()
    {
        return $this->getField(self::INDEX_CURRENT_PATIENT_BALANCE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTotalCharges($value)
    {
        $this->setField(self::INDEX_TOTAL_CHARGES, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTotalCharges()
    {
        return $this->getField(self::INDEX_TOTAL_CHARGES);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTotalAdjustments($value)
    {
        $this->setField(self::INDEX_TOTAL_ADJUSTMENTS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTotalAdjustments()
    {
        return $this->getField(self::INDEX_TOTAL_ADJUSTMENTS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTotalPayments($value)
    {
        $this->setField(self::INDEX_TOTAL_PAYMENTS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTotalPayments()
    {
        return $this->getField(self::INDEX_TOTAL_PAYMENTS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAlternateVisitID($value)
    {
        $this->setField(self::INDEX_ALTERNATE_VISIT_I_D, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAlternateVisitID()
    {
        return $this->getField(self::INDEX_ALTERNATE_VISIT_I_D);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitIndicator($value)
    {
        $this->setField(self::INDEX_VISIT_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitIndicator()
    {
        return $this->getField(self::INDEX_VISIT_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOtherHealthcareProvider($value)
    {
        $this->setField(self::INDEX_OTHER_HEALTHCARE_PROVIDER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOtherHealthcareProvider()
    {
        return $this->getField(self::INDEX_OTHER_HEALTHCARE_PROVIDER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setServiceEpisodeDescription($value)
    {
        $this->setField(self::INDEX_SERVICE_EPISODE_DESCRIPTION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceEpisodeDescription()
    {
        return $this->getField(self::INDEX_SERVICE_EPISODE_DESCRIPTION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setServiceEpisodeIdentifier($value)
    {
        $this->setField(self::INDEX_SERVICE_EPISODE_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceEpisodeIdentifier()
    {
        return $this->getField(self::INDEX_SERVICE_EPISODE_IDENTIFIER);
    }


}
