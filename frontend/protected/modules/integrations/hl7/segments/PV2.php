<?php

/**
 * PV2.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\segments;
use SegHEIRS\modules\integrations\hl7\Segment;

/**
 *
 * Description of PV2
 *
 */

class PV2 extends Segment
{

    const INDEX_PRIOR_PENDING_LOCATION = 1;
    const INDEX_ACCOMMODATION_CODE = 2;
    const INDEX_ADMIT_REASON = 3;
    const INDEX_TRANSFER_REASON = 4;
    const INDEX_PATIENT_VALUABLES = 5;
    const INDEX_PATIENT_VALUABLES_LOCATION = 6;
    const INDEX_VISIT_USER_CODE = 7;
    const INDEX_EXPECTED_ADMIT_DATE_TIME = 8;
    const INDEX_EXPECTED_DISCHARGE_DATE_TIME = 9;
    const INDEX_ESTIMATED_LENGTH_OF_INPATIENT_STAY = 10;
    const INDEX_ACTUAL_LENGTH_O_INPATIENT_STAY = 11;
    const INDEX_VISIT_DESCRIPTION = 12;
    const INDEX_REFERRAL_SOURCE_CODE = 13;
    const INDEX_PREVIOUS_SERVICE_DATE = 14;
    const INDEX_EMPLOYMENT_ILLNESS_RELATED_INDICATOR = 15;
    const INDEX_PURGE_STATUS_CODE = 16;
    const INDEX_PURGE_STATUS_DATE = 17;
    const INDEX_SPECIAL_PROGRAM_CODE = 18;
    const INDEX_RETENTION_INDICATOR = 19;
    const INDEX_EXPECTED_NUMBER_OF_INSURANCE_PLANS = 20;
    const INDEX_VISIT_PUBLICITY_CODE = 21;
    const INDEX_VISIT_PROTECTION_INDICATOR = 22;
    const INDEX_CLINIC_ORGANIZATION_NAME = 23;
    const INDEX_PATIENT_STATUS_CODE = 24;
    const INDEX_VISIT_PRIORITY_CODE = 25;
    const INDEX_PREVIOUS_TREATMENT_DATE = 26;
    const INDEX_EXPECTED_DISCHARGE_DISPOSITION = 27;
    const INDEX_SIGNATURE_ON_FILE_DATE = 28;
    const INDEX_FIRST_SIMILAR_ILLNESS_DATE = 29;
    const INDEX_PATIENT_CHARGE_ADJUSTMENT_CODE = 30;
    const INDEX_RECURRING_SERVICE_CODE = 31;
    const INDEX_BILLING_MEDIA_CODE = 32;
    const INDEX_EXPECTED_SURGERY_DATE_TIME = 33;
    const INDEX_MILITARY_PARTNERSHIP_CODE = 34;
    const INDEX_MILITARY_NON_AVAILABILITY_CODE = 35;
    const INDEX_NEWBORN_BABY_INDICATOR = 36;
    const INDEX_BABY_DETAINED_INDICATOR = 37;
    const INDEX_MODE_OF_ARRIVAL_CODE = 38;
    const INDEX_RECREATIONAL_DRUG_USE_CODE = 39;
    const INDEX_ADMISSION_LEVEL_OF_CARE_CODE = 40;
    const INDEX_PRECAUTION_CODE = 41;
    const INDEX_PATIENT_CONDITION_CODE = 42;
    const INDEX_LIVING_WILL_CODE = 43;
    const INDEX_ORGAN_DONOR_CODE = 44;
    const INDEX_ADVANCE_DIRECTIVE_CODE = 45;
    const INDEX_PATIENT_STATUS_EFFECTIVE_DATE = 46;
    const INDEX_EXPECTED_LOA_RETURN_DATE_TIME = 47;
    const INDEX_EXPECTED_PREADMISSION_TESTING_DATE_TIME = 48;
    const INDEX_NOTIFY_CLERGY_CODE = 49;
    const INDEX_ADVANCE_DIRECTIVE_LAST_VERIFIED_DATE = 50;

    /** @var string */
    public $name = 'PV2';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPriorPendingLocation($value)
    {
        $this->setField(self::INDEX_PRIOR_PENDING_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPriorPendingLocation()
    {
        return $this->getField(self::INDEX_PRIOR_PENDING_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAccommodationCode($value)
    {
        $this->setField(self::INDEX_ACCOMMODATION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAccommodationCode()
    {
        return $this->getField(self::INDEX_ACCOMMODATION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdmitReason($value)
    {
        $this->setField(self::INDEX_ADMIT_REASON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdmitReason()
    {
        return $this->getField(self::INDEX_ADMIT_REASON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransferReason($value)
    {
        $this->setField(self::INDEX_TRANSFER_REASON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransferReason()
    {
        return $this->getField(self::INDEX_TRANSFER_REASON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientValuables($value)
    {
        $this->setField(self::INDEX_PATIENT_VALUABLES, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientValuables()
    {
        return $this->getField(self::INDEX_PATIENT_VALUABLES);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientValuablesLocation($value)
    {
        $this->setField(self::INDEX_PATIENT_VALUABLES_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientValuablesLocation()
    {
        return $this->getField(self::INDEX_PATIENT_VALUABLES_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitUserCode($value)
    {
        $this->setField(self::INDEX_VISIT_USER_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitUserCode()
    {
        return $this->getField(self::INDEX_VISIT_USER_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedAdmitDateTime($value)
    {
        $this->setField(self::INDEX_EXPECTED_ADMIT_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedAdmitDateTime()
    {
        return $this->getField(self::INDEX_EXPECTED_ADMIT_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedDischargeDateTime($value)
    {
        $this->setField(self::INDEX_EXPECTED_DISCHARGE_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedDischargeDateTime()
    {
        return $this->getField(self::INDEX_EXPECTED_DISCHARGE_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEstimatedLengthOfInpatientStay($value)
    {
        $this->setField(self::INDEX_ESTIMATED_LENGTH_OF_INPATIENT_STAY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEstimatedLengthOfInpatientStay()
    {
        return $this->getField(self::INDEX_ESTIMATED_LENGTH_OF_INPATIENT_STAY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setActualLengthOInpatientStay($value)
    {
        $this->setField(self::INDEX_ACTUAL_LENGTH_O_INPATIENT_STAY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getActualLengthOInpatientStay()
    {
        return $this->getField(self::INDEX_ACTUAL_LENGTH_O_INPATIENT_STAY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitDescription($value)
    {
        $this->setField(self::INDEX_VISIT_DESCRIPTION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitDescription()
    {
        return $this->getField(self::INDEX_VISIT_DESCRIPTION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setReferralSourceCode($value)
    {
        $this->setField(self::INDEX_REFERRAL_SOURCE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getReferralSourceCode()
    {
        return $this->getField(self::INDEX_REFERRAL_SOURCE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPreviousServiceDate($value)
    {
        $this->setField(self::INDEX_PREVIOUS_SERVICE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPreviousServiceDate()
    {
        return $this->getField(self::INDEX_PREVIOUS_SERVICE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEmploymentIllnessRelatedIndicator($value)
    {
        $this->setField(self::INDEX_EMPLOYMENT_ILLNESS_RELATED_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEmploymentIllnessRelatedIndicator()
    {
        return $this->getField(self::INDEX_EMPLOYMENT_ILLNESS_RELATED_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPurgeStatusCode($value)
    {
        $this->setField(self::INDEX_PURGE_STATUS_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPurgeStatusCode()
    {
        return $this->getField(self::INDEX_PURGE_STATUS_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPurgeStatusDate($value)
    {
        $this->setField(self::INDEX_PURGE_STATUS_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPurgeStatusDate()
    {
        return $this->getField(self::INDEX_PURGE_STATUS_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSpecialProgramCode($value)
    {
        $this->setField(self::INDEX_SPECIAL_PROGRAM_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecialProgramCode()
    {
        return $this->getField(self::INDEX_SPECIAL_PROGRAM_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRetentionIndicator($value)
    {
        $this->setField(self::INDEX_RETENTION_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRetentionIndicator()
    {
        return $this->getField(self::INDEX_RETENTION_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedNumberOfInsurancePlans($value)
    {
        $this->setField(self::INDEX_EXPECTED_NUMBER_OF_INSURANCE_PLANS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedNumberOfInsurancePlans()
    {
        return $this->getField(self::INDEX_EXPECTED_NUMBER_OF_INSURANCE_PLANS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitPublicityCode($value)
    {
        $this->setField(self::INDEX_VISIT_PUBLICITY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitPublicityCode()
    {
        return $this->getField(self::INDEX_VISIT_PUBLICITY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitProtectionIndicator($value)
    {
        $this->setField(self::INDEX_VISIT_PROTECTION_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitProtectionIndicator()
    {
        return $this->getField(self::INDEX_VISIT_PROTECTION_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setClinicOrganizationName($value)
    {
        $this->setField(self::INDEX_CLINIC_ORGANIZATION_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getClinicOrganizationName()
    {
        return $this->getField(self::INDEX_CLINIC_ORGANIZATION_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientStatusCode($value)
    {
        $this->setField(self::INDEX_PATIENT_STATUS_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientStatusCode()
    {
        return $this->getField(self::INDEX_PATIENT_STATUS_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVisitPriorityCode($value)
    {
        $this->setField(self::INDEX_VISIT_PRIORITY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVisitPriorityCode()
    {
        return $this->getField(self::INDEX_VISIT_PRIORITY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPreviousTreatmentDate($value)
    {
        $this->setField(self::INDEX_PREVIOUS_TREATMENT_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPreviousTreatmentDate()
    {
        return $this->getField(self::INDEX_PREVIOUS_TREATMENT_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedDischargeDisposition($value)
    {
        $this->setField(self::INDEX_EXPECTED_DISCHARGE_DISPOSITION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedDischargeDisposition()
    {
        return $this->getField(self::INDEX_EXPECTED_DISCHARGE_DISPOSITION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setSignatureOnFileDate($value)
    {
        $this->setField(self::INDEX_SIGNATURE_ON_FILE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getSignatureOnFileDate()
    {
        return $this->getField(self::INDEX_SIGNATURE_ON_FILE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFirstSimilarIllnessDate($value)
    {
        $this->setField(self::INDEX_FIRST_SIMILAR_ILLNESS_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstSimilarIllnessDate()
    {
        return $this->getField(self::INDEX_FIRST_SIMILAR_ILLNESS_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientChargeAdjustmentCode($value)
    {
        $this->setField(self::INDEX_PATIENT_CHARGE_ADJUSTMENT_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientChargeAdjustmentCode()
    {
        return $this->getField(self::INDEX_PATIENT_CHARGE_ADJUSTMENT_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRecurringServiceCode($value)
    {
        $this->setField(self::INDEX_RECURRING_SERVICE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRecurringServiceCode()
    {
        return $this->getField(self::INDEX_RECURRING_SERVICE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBillingMediaCode($value)
    {
        $this->setField(self::INDEX_BILLING_MEDIA_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBillingMediaCode()
    {
        return $this->getField(self::INDEX_BILLING_MEDIA_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedSurgeryDateTime($value)
    {
        $this->setField(self::INDEX_EXPECTED_SURGERY_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedSurgeryDateTime()
    {
        return $this->getField(self::INDEX_EXPECTED_SURGERY_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMilitaryPartnershipCode($value)
    {
        $this->setField(self::INDEX_MILITARY_PARTNERSHIP_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMilitaryPartnershipCode()
    {
        return $this->getField(self::INDEX_MILITARY_PARTNERSHIP_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setMilitaryNonAvailabilityCode($value)
    {
        $this->setField(self::INDEX_MILITARY_NON_AVAILABILITY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getMilitaryNonAvailabilityCode()
    {
        return $this->getField(self::INDEX_MILITARY_NON_AVAILABILITY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNewbornBabyIndicator($value)
    {
        $this->setField(self::INDEX_NEWBORN_BABY_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNewbornBabyIndicator()
    {
        return $this->getField(self::INDEX_NEWBORN_BABY_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setBabyDetainedIndicator($value)
    {
        $this->setField(self::INDEX_BABY_DETAINED_INDICATOR, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getBabyDetainedIndicator()
    {
        return $this->getField(self::INDEX_BABY_DETAINED_INDICATOR);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setModeOfArrivalCode($value)
    {
        $this->setField(self::INDEX_MODE_OF_ARRIVAL_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getModeOfArrivalCode()
    {
        return $this->getField(self::INDEX_MODE_OF_ARRIVAL_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setRecreationalDrugUseCode($value)
    {
        $this->setField(self::INDEX_RECREATIONAL_DRUG_USE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getRecreationalDrugUseCode()
    {
        return $this->getField(self::INDEX_RECREATIONAL_DRUG_USE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdmissionLevelOfCareCode($value)
    {
        $this->setField(self::INDEX_ADMISSION_LEVEL_OF_CARE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdmissionLevelOfCareCode()
    {
        return $this->getField(self::INDEX_ADMISSION_LEVEL_OF_CARE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPrecautionCode($value)
    {
        $this->setField(self::INDEX_PRECAUTION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPrecautionCode()
    {
        return $this->getField(self::INDEX_PRECAUTION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientConditionCode($value)
    {
        $this->setField(self::INDEX_PATIENT_CONDITION_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientConditionCode()
    {
        return $this->getField(self::INDEX_PATIENT_CONDITION_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setLivingWillCode($value)
    {
        $this->setField(self::INDEX_LIVING_WILL_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getLivingWillCode()
    {
        return $this->getField(self::INDEX_LIVING_WILL_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrganDonorCode($value)
    {
        $this->setField(self::INDEX_ORGAN_DONOR_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganDonorCode()
    {
        return $this->getField(self::INDEX_ORGAN_DONOR_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdvanceDirectiveCode($value)
    {
        $this->setField(self::INDEX_ADVANCE_DIRECTIVE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdvanceDirectiveCode()
    {
        return $this->getField(self::INDEX_ADVANCE_DIRECTIVE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPatientStatusEffectiveDate($value)
    {
        $this->setField(self::INDEX_PATIENT_STATUS_EFFECTIVE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPatientStatusEffectiveDate()
    {
        return $this->getField(self::INDEX_PATIENT_STATUS_EFFECTIVE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedLoaReturnDateTime($value)
    {
        $this->setField(self::INDEX_EXPECTED_LOA_RETURN_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedLoaReturnDateTime()
    {
        return $this->getField(self::INDEX_EXPECTED_LOA_RETURN_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setExpectedPreadmissionTestingDateTime($value)
    {
        $this->setField(self::INDEX_EXPECTED_PREADMISSION_TESTING_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getExpectedPreadmissionTestingDateTime()
    {
        return $this->getField(self::INDEX_EXPECTED_PREADMISSION_TESTING_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setNotifyClergyCode($value)
    {
        $this->setField(self::INDEX_NOTIFY_CLERGY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getNotifyClergyCode()
    {
        return $this->getField(self::INDEX_NOTIFY_CLERGY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdvanceDirectiveLastVerifiedDate($value)
    {
        $this->setField(self::INDEX_ADVANCE_DIRECTIVE_LAST_VERIFIED_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdvanceDirectiveLastVerifiedDate()
    {
        return $this->getField(self::INDEX_ADVANCE_DIRECTIVE_LAST_VERIFIED_DATE);
    }

}