<?php

/**
 * ORC.php
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
 * Description of ORC
 *
 */

class ORC extends Segment
{

    const INDEX_ORDER_CONTROL = 1;
    const INDEX_PLACER_ORDER_NUMBER = 2;
    const INDEX_FILLER_ORDER_NUMBER = 3;
    const INDEX_PLACER_GROUP_NUMBER = 4;
    const INDEX_ORDER_STATUS = 5;
    const INDEX_RESPONSE_FLAG = 6;
    const INDEX_QUANTITY_TIMING = 7;
    const INDEX_PARENT_ORDER = 8;
    const INDEX_TRANSACTION_DATE_TIME = 9;
    const INDEX_ENTERED_BY = 10;
    const INDEX_VERIFIED_BY = 11;
    const INDEX_ORDERING_PROVIDER = 12;
    const INDEX_ENTERER_LOCATION = 13;
    const INDEX_CALL_BACK_PHONE_NUMBER = 14;
    const INDEX_ORDER_EFFECTIVE_DATE_TIME = 15;
    const INDEX_ORDER_CONTROL_CODE_REASON = 16;
    const INDEX_ENTERING_ORGANIZATION = 17;
    const INDEX_ENTERING_DEVICE = 18;
    const INDEX_ACTION_BY = 19;
    const INDEX_ADVANCED_BENEFICIARY_NOTICE_CODE = 20;
    const INDEX_ORDERING_FACILITY_NAME = 21;
    const INDEX_ORDERING_FACILITY_ADDRESS = 22;
    const INDEX_ORDERING_FACILITY_PHONE_NUMBER = 23;
    const INDEX_ORDERING_PROVIDER_ADDRESS = 24;
    const INDEX_ORDER_STATUS_MODIFIER = 25;
    const INDEX_ADVANCED_BENEFICIARY_NOTICE_OVERRIDE_REASON = 26;
    const INDEX_FILLER_EXPECTED_AVAILABILITY_DATE_TIME = 27;
    const INDEX_CONFIDENTIALITY_CODE = 28;
    const INDEX_ORDER_TYPE = 29;
    const INDEX_ENTERER_AUTHORIZATION_MODE = 30;
    const INDEX_PARENT_UNIVERSAL_SERVICE_IDENTIFIER = 31;
    const INDEX_ADVANCED_BENEFICIARY_NOTICE_DATE = 32;
    const INDEX_ALTERNATE_PLACER_ORDER_NUMBER = 33;
    const INDEX_ORDER_WORKFLOW_PROFILE = 34;

    /** @var string  */
    public $name = 'ORC';

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderControl($value)
    {
        $this->setField(self::INDEX_ORDER_CONTROL, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderControl()
    {
        return $this->getField(self::INDEX_ORDER_CONTROL);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlacerOrderNumber($value)
    {
        $this->setField(self::INDEX_PLACER_ORDER_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacerOrderNumber()
    {
        return $this->getField(self::INDEX_PLACER_ORDER_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFillerOrderNumber($value)
    {
        $this->setField(self::INDEX_FILLER_ORDER_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFillerOrderNumber()
    {
        return $this->getField(self::INDEX_FILLER_ORDER_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setPlacerGroupNumber($value)
    {
        $this->setField(self::INDEX_PLACER_GROUP_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getPlacerGroupNumber()
    {
        return $this->getField(self::INDEX_PLACER_GROUP_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderStatus($value)
    {
        $this->setField(self::INDEX_ORDER_STATUS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->getField(self::INDEX_ORDER_STATUS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setResponseFlag($value)
    {
        $this->setField(self::INDEX_RESPONSE_FLAG, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getResponseFlag()
    {
        return $this->getField(self::INDEX_RESPONSE_FLAG);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setQuantityTiming(
        $quantity='',
        $interval='',
        $duration='',
        $startDateTime='',
        $endDateTime='',
        $priority=''
    )
    {
        $values = [
            $quantity,
            $interval,
            $duration,
            $startDateTime,
            $endDateTime,
            $priority
        ];

        $this->setField(self::INDEX_QUANTITY_TIMING, HL7::encodeValues($values));
        return $this;
    }

    /**
     * @return string
     */
    public function getQuantityTiming()
    {
        return $this->getField(self::INDEX_QUANTITY_TIMING);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentOrder($value)
    {
        $this->setField(self::INDEX_PARENT_ORDER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentOrder()
    {
        return $this->getField(self::INDEX_PARENT_ORDER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setTransactionDateTime($value)
    {
        $this->setField(self::INDEX_TRANSACTION_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getTransactionDateTime()
    {
        return $this->getField(self::INDEX_TRANSACTION_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEnteredBy($value)
    {
        $this->setField(self::INDEX_ENTERED_BY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEnteredBy()
    {
        return $this->getField(self::INDEX_ENTERED_BY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setVerifiedBy($value)
    {
        $this->setField(self::INDEX_VERIFIED_BY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getVerifiedBy()
    {
        return $this->getField(self::INDEX_VERIFIED_BY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderingProvider($value)
    {
        $this->setField(self::INDEX_ORDERING_PROVIDER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderingProvider()
    {
        return $this->getField(self::INDEX_ORDERING_PROVIDER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEntererLocation($value)
    {
        $this->setField(self::INDEX_ENTERER_LOCATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEntererLocation()
    {
        return $this->getField(self::INDEX_ENTERER_LOCATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setCallBackPhoneNumber($value)
    {
        $this->setField(self::INDEX_CALL_BACK_PHONE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getCallBackPhoneNumber()
    {
        return $this->getField(self::INDEX_CALL_BACK_PHONE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderEffectiveDateTime($value)
    {
        $this->setField(self::INDEX_ORDER_EFFECTIVE_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderEffectiveDateTime()
    {
        return $this->getField(self::INDEX_ORDER_EFFECTIVE_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderControlCodeReason($value)
    {
        $this->setField(self::INDEX_ORDER_CONTROL_CODE_REASON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderControlCodeReason()
    {
        return $this->getField(self::INDEX_ORDER_CONTROL_CODE_REASON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEnteringOrganization($value)
    {
        $this->setField(self::INDEX_ENTERING_ORGANIZATION, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEnteringOrganization()
    {
        return $this->getField(self::INDEX_ENTERING_ORGANIZATION);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEnteringDevice($value)
    {
        $this->setField(self::INDEX_ENTERING_DEVICE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEnteringDevice()
    {
        return $this->getField(self::INDEX_ENTERING_DEVICE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setActionBy($value)
    {
        $this->setField(self::INDEX_ACTION_BY, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getActionBy()
    {
        return $this->getField(self::INDEX_ACTION_BY);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdvancedBeneficiaryNoticeCode($value)
    {
        $this->setField(self::INDEX_ADVANCED_BENEFICIARY_NOTICE_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdvancedBeneficiaryNoticeCode()
    {
        return $this->getField(self::INDEX_ADVANCED_BENEFICIARY_NOTICE_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderingFacilityName($value)
    {
        $this->setField(self::INDEX_ORDERING_FACILITY_NAME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderingFacilityName()
    {
        return $this->getField(self::INDEX_ORDERING_FACILITY_NAME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderingFacilityAddress($value)
    {
        $this->setField(self::INDEX_ORDERING_FACILITY_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderingFacilityAddress()
    {
        return $this->getField(self::INDEX_ORDERING_FACILITY_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderingFacilityPhoneNumber($value)
    {
        $this->setField(self::INDEX_ORDERING_FACILITY_PHONE_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderingFacilityPhoneNumber()
    {
        return $this->getField(self::INDEX_ORDERING_FACILITY_PHONE_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderingProviderAddress($value)
    {
        $this->setField(self::INDEX_ORDERING_PROVIDER_ADDRESS, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderingProviderAddress()
    {
        return $this->getField(self::INDEX_ORDERING_PROVIDER_ADDRESS);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderStatusModifier($value)
    {
        $this->setField(self::INDEX_ORDER_STATUS_MODIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderStatusModifier()
    {
        return $this->getField(self::INDEX_ORDER_STATUS_MODIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdvancedBeneficiaryNoticeOverrideReason($value)
    {
        $this->setField(self::INDEX_ADVANCED_BENEFICIARY_NOTICE_OVERRIDE_REASON, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdvancedBeneficiaryNoticeOverrideReason()
    {
        return $this->getField(self::INDEX_ADVANCED_BENEFICIARY_NOTICE_OVERRIDE_REASON);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setFillerExpectedAvailabilityDateTime($value)
    {
        $this->setField(self::INDEX_FILLER_EXPECTED_AVAILABILITY_DATE_TIME, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getFillerExpectedAvailabilityDateTime()
    {
        return $this->getField(self::INDEX_FILLER_EXPECTED_AVAILABILITY_DATE_TIME);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setConfidentialityCode($value)
    {
        $this->setField(self::INDEX_CONFIDENTIALITY_CODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getConfidentialityCode()
    {
        return $this->getField(self::INDEX_CONFIDENTIALITY_CODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderType($value)
    {
        $this->setField(self::INDEX_ORDER_TYPE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderType()
    {
        return $this->getField(self::INDEX_ORDER_TYPE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setEntererAuthorizationMode($value)
    {
        $this->setField(self::INDEX_ENTERER_AUTHORIZATION_MODE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getEntererAuthorizationMode()
    {
        return $this->getField(self::INDEX_ENTERER_AUTHORIZATION_MODE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setParentUniversalServiceIdentifier($value)
    {
        $this->setField(self::INDEX_PARENT_UNIVERSAL_SERVICE_IDENTIFIER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getParentUniversalServiceIdentifier()
    {
        return $this->getField(self::INDEX_PARENT_UNIVERSAL_SERVICE_IDENTIFIER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAdvancedBeneficiaryNoticeDate($value)
    {
        $this->setField(self::INDEX_ADVANCED_BENEFICIARY_NOTICE_DATE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAdvancedBeneficiaryNoticeDate()
    {
        return $this->getField(self::INDEX_ADVANCED_BENEFICIARY_NOTICE_DATE);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setAlternatePlacerOrderNumber($value)
    {
        $this->setField(self::INDEX_ALTERNATE_PLACER_ORDER_NUMBER, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getAlternatePlacerOrderNumber()
    {
        return $this->getField(self::INDEX_ALTERNATE_PLACER_ORDER_NUMBER);
    }

    /**
     * @param string $value
     *
     * @return static
     */
    public function setOrderWorkflowProfile($value)
    {
        $this->setField(self::INDEX_ORDER_WORKFLOW_PROFILE, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function getOrderWorkflowProfile()
    {
        return $this->getField(self::INDEX_ORDER_WORKFLOW_PROFILE);
    }


}