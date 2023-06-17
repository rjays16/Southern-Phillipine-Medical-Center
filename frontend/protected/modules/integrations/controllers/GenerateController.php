<?php

/**
 * GenerateController.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\controllers;
use SegHEIRS\components\web\Controller;
use SegHEIRS\helpers\Inflector;
use SegHEIRS\modules\integrations\hl7\services\segments\MshFactory;

/**
 *
 * Description of GenerateController
 *
 */

class GenerateController extends Controller
{

    /**
     *
     */
    public function actionMsh()
    {


        $fields = [
            4 => 'sendingApplication',
            'sendingFacility',
            'receivingApplication',
            'receivingFacility',
            'dateTimeOfMessage',
            'security',
            'messageType',
            'messageControlId',
            'processingId',
            'versionId',
            'sequenceNumber',
            'continuationPointer',
            'acceptAcknowledgementType',
            'applicationAcknowledgmentType',
            'countryCode',
            'characterSet',
        ];

        echo $this->generate($fields);

    }

    /**
     *
     */
    public function actionPid()
    {
        echo $this->generate([
            2 => 'patientId',
            'patientIdentifierList',
            'alternatePatientId',
            'patientName',
            'motherMaidenName',
            'dateTimeOfBirth',
            'patientAlias',
            'race',
            'patientAddress',
            'countryCode',
            'homePhoneNumber',
            'businessPhoneNumber',
            'primaryLanguage',
            'maritalStatus',
            'religion',
            'patientAccountNumber',
            'ssnNumber',
            'driversLicenseNumber',
            'motherIdentifier',
            'ethnicGroup',
            'birthPlace',
            'multipleBirthIndicator',
            'birthOrder',
            'citizenship',
            'veteranMilitaryStatus',
            'nationality',
            'patientDeathAndTime',
            'patientDeathIndicator',
            'identityUnknownIndicator',
            'identityReliabilityCode',
            'lastUpdateDateTime',
            'lastUpdateFacility',
            'taxonomicClassificationCode',
            'breedCode',
            'strain',
            'productionClassCode',
            'tribalCitizenship',
            'patientTelecommunicationInformation'
        ]);
    }

    /**
     *
     */
    public function actionEvn()
    {
        echo $this->generate([
            1 => 'eventTypeCode',
            'recordedDateTime',
            'dateTimePlannedEvent',
            'eventReasonCode',
            'operatorID',
            'eventOccurred',
            'eventFacility'
        ]);
    }


    /**
     *
     */
    public function actionPv1()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'patientClass',
            'assignedPatientLocation',
            'admissionType',
            'preadmitNumber',
            'priorPatientLocation',
            'attendingDoctor',
            'referringDoctor',
            'consultingDoctor',
            'hospitalService',
            'temporaryLocation',
            'preadmitTestIndicator',
            'readmissionIndicator',
            'admitSource',
            'ambulatoryStatus',
            'vipIndicator',
            'admittingDoctor',
            'patientType',
            'visitNumber',
            'financialClass',
            'chargePriceIndicator',
            'courtesyCode',
            'creditRating',
            'contractCode',
            'contractEffectiveDate',
            'contractAmount',
            'contractPeriod',
            'interestCode',
            'transferToBadDebtCode',
            'transferToBadDebtDate',
            'badDebtAgencyCode',
            'badDebtTransferAmount',
            'badDebtRecoveryAmount',
            'deleteAccountIndicator',
            'deleteAccountDate',
            'dischargeDisposition',
            'dischargedToLocation',
            'dietType',
            'servicingFacility',
            'bedStatus',
            'accountStatus',
            'pendingLocation',
            'priorTemporaryLocation',
            'admitDateTime',
            'dischargeDateTime',
            'currentPatientBalance',
            'totalCharges',
            'totalAdjustments',
            'totalPayments',
            'alternateVisitID',
            'visitIndicator',
            'otherHealthcareProvider',
            'serviceEpisodeDescription',
            'serviceEpisodeIdentifier'
        ]);
    }

    /**
     *
     */
    public function actionAl1()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'allergenTypeCode',
            'allergenCodeMnemonicDescription',
            'allergySeverityCode',
            'allergyReactionCode',
            'identificationDate'
        ]);
    }

    /**
     *
     */
    public function actionMsa()
    {
        echo $this->generate([
            1 => 'acknowledgmentCode',
            'messageControlId',
            'textMessage',
            'expectedSequenceNumber',
            'delayedAcknowledgmentType',
            'errorCondition',
            'messageWaitingNumber',
            'messageWaitingPriority'
        ]);
    }

    /**
     *
     */
    public function actionErr()
    {
        echo $this->generate([
            1 => 'errorCodeAndLocation',
            'errorLocation',
            'hl7ErrorCode',
            'severity',
            'applicationErrorCode',
            'applicationErrorParameter',
            'diagnosticInformation',
            'userMessage',
            'informPersonIndicator',
            'overrideType',
            'overrideReasonCode',
            'helpDeskContactPoint'
        ]);
    }

    /**
     *
     */
    public function actionDg1()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'diagnosisCodingMethod',
            'diagnosisCode',
            'diagnosisDescription',
            'diagnosisDateTime',
            'diagnosisType',
            'majorDiagnosticCategory',
            'diagnosticRelatedGroup',
            'drgApprovalIndicator',
            'drgGrouperReviewCode',
            'outlierType',
            'outlierDays',
            'outlierCost',
            'grouperVersionAndType',
            'diagnosisPriority',
            'diagnosingClinician',
            'diagnosisClassification',
            'confidentialIndicator',
            'attestationDateTime',
            'diagnosisIdentifier',
            'diagnosisActionCode',
            'parentDiagnosis',
            'drgCclValueCode',
            'drgGroupingUsage',
            'drgDiagnosisDeterminationStatus',
            'presentOnAdmissionIndicator'
        ]);
    }

    /**
     *
     */
    public function actionIn1()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'healthPlanId',
            'insuranceCompanyId',
            'insuranceCompanyName',
            'insuranceCompanyAddress',
            'insuranceCompanyContactPerson',
            'insuranceCompanyPhoneNumber',
            'groupNumber',
            'groupName',
            'insuredGroupEmployeeId',
            'insuredGroupEmployeeName',
            'planEffectiveDate',
            'planExpirationDate',
            'authorizationInformation',
            'planType',
            'nameOfInsured',
            'insuredRelationshipToPatient',
            'insuredDateOfBirth',
            'insuredAddress',
            'assignmentOfBenefits',
            'coordinationOfBenefits',
            'coordinationOfBenefitsPriority',
            'noticeOfAdmissionFlag',
            'noticeOfAdmissionDate',
            'reportOfEligibilityFlag',
            'reportOfEligibilityDate',
            'releaseInformationCode',
            'preadmitCertificate',
            'verificationDateTime',
            'verificationBy',
            'typeOfAgreementCode',
            'billingStatus',
            'lifetimeReserveDays',
            'belayBeforeLifetimeReserveDay',
            'companyPlanCode',
            'policyNumber',
            'policyDeductible',
            'policyLimitAmount',
            'policyLimitDays',
            'roomRateSemiPrivate',
            'roomRatePrivate',
            'insuredEmploymentStatus',
            'insuredAdministrativeSex',
            'insuredEmployerAddress',
            'verificationStatus',
            'priorInsurancePlanId',
            'coverageType',
            'handicap',
            'insuredIdNumber',
            'signatureCode',
            'signatureCodeDate',
            'insuredBirthPlace',
            'vipIndicator',
            'externalHealthPlanIdentifiers',
            'insuranceActionCode'
        ]);
    }

    /**
     *
     */
    public function actionPr1()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'procedureCodingMethod',
            'procedureCode',
            'procedureDescription',
            'procedureDateTime',
            'procedureFunctionalType',
            'procedureMinutes',
            'anesthesiologist',
            'anesthesiaCode',
            'anesthesiaMinutes',
            'surgeon',
            'procedurePractitioner',
            'consentCode',
            'procedurePriority',
            'associatedDiagnosisCode',
            'procedureCodeModifier',
            'procedureDrgType',
            'tissueTypeCode',
            'procedureIdentifier',
            'procedureActionCode',
            'drgProcedureDeterminationStatus',
            'drgProcedureRelevance',
            'treatingOrganizationalUnit',
            'respiratoryWithinSurgery',
            'parentProcedureId'
        ]);
    }

    public function actionPv2()
    {
        echo $this->generate([
            1 => 'priorPendingLocation',
            'accommodationCode',
            'admitReason',
            'transferReason',
            'patientValuables',
            'patientValuablesLocation',
            'visitUserCode',
            'expectedAdmitDateTime',
            'expectedDischargeDateTime',
            'estimatedLengthOfInpatientStay',
            'actualLengthOInpatientStay',
            'visitDescription',
            'referralSourceCode',
            'previousServiceDate',
            'employmentIllnessRelatedIndicator',
            'purgeStatusCode',
            'purgeStatusDate',
            'specialProgramCode',
            'retentionIndicator',
            'expectedNumberOfInsurancePlans',
            'visitPublicityCode',
            'visitProtectionIndicator',
            'clinicOrganizationName',
            'patientStatusCode',
            'visitPriorityCode',
            'previousTreatmentDate',
            'expectedDischargeDisposition',
            'signatureOnFileDate',
            'firstSimilarIllnessDate',
            'patientChargeAdjustmentCode',
            'recurringServiceCode',
            'billingMediaCode',
            'expectedSurgeryDateTime',
            'militaryPartnershipCode',
            'militaryNonAvailabilityCode',
            'newbornBabyIndicator',
            'babyDetainedIndicator',
            'modeOfArrivalCode',
            'recreationalDrugUseCode',
            'admissionLevelOfCareCode',
            'precautionCode',
            'patientConditionCode',
            'livingWillCode',
            'organDonorCode',
            'advanceDirectiveCode',
            'patientStatusEffectiveDate',
            'expectedLoaReturnDateTime',
            'expectedPreadmissionTestingDateTime',
            'notifyClergyCode',
            'advanceDirectiveLastVerifiedDate'
        ]);
    }

    /**
     *
     */
    public function actionObx()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'valueType',
            'observationIdentifier',
            'observationSubId',
            'observationValue',
            'units',
            'referencesRange',
            'interpretationCodes',
            'probability',
            'natureOfAbnormalTest',
            'observationResultStatus',
            'effectiveDateOfReferenceRange',
            'userDefinedAccessChecks',
            'observationDateTime',
            'producerId',
            'responsibleObserver',
            'observationMethod',
            'equipmentInstanceIdentifier',
            'analysisDateTime',
            'observationSite',
            'observationInstanceIdentifier',
            'moodCode',
            'performingOrganizationName',
            'performingOrganizationAddress',
            'performingOrganizationMedicalDirector',
            'patientResultsReleaseCategory',
            'rootCause',
            'localProcessControl',
            'observationType',
            'observationSubType'
        ]);
    }

    /**
     *
     */
    public function actionOrc()
    {
        echo $this->generate([
            1 => 'OrderControl',
            'PlacerOrderNumber',
            'FillerOrderNumber',
            'PlacerGroupNumber',
            'OrderStatus',
            'ResponseFlag',
            'QuantityTiming',
            'ParentOrder',
            'TransactionDateTime',
            'EnteredBy',
            'VerifiedBy',
            'OrderingProvider',
            'EntererLocation',
            'CallBackPhoneNumber',
            'OrderEffectiveDateTime',
            'OrderControlCodeReason',
            'EnteringOrganization',
            'EnteringDevice',
            'ActionBy',
            'AdvancedBeneficiaryNoticeCode',
            'OrderingFacilityName',
            'OrderingFacilityAddress',
            'OrderingFacilityPhoneNumber',
            'OrderingProviderAddress',
            'OrderStatusModifier',
            'AdvancedBeneficiaryNoticeOverrideReason',
            'FillerExpectedAvailabilityDateTime',
            'ConfidentialityCode',
            'OrderType',
            'EntererAuthorizationMode',
            'ParentUniversalServiceIdentifier',
            'AdvancedBeneficiaryNoticeDate',
            'AlternatePlacerOrderNumber',
            'OrderWorkflowProfile'
        ]);
    }

    /**
     *
     */
    public function actionObr()
    {
        echo $this->generate([
            1 => 'SequenceId',
            'PlacerOrderNumber',
            'FillerOrderNumber',
            'UniversalServiceIdentifier',
            'Priority',
            'RequestedDateTime',
            'ObservationDateTime',
            'ObservationEndDateTime',
            'CollectionVolume',
            'CollectorIdentifier',
            'SpecimenActionCode',
            'DangerCode',
            'RelevantClinicalInformation',
            'SpecimenReceivedDateTime',
            'SpecimenSource',
            'OrderingProvider',
            'CallbackPhoneNumber',
            'PlacerField1',
            'PlacerField2',
            'FillerField1',
            'FillerField2',
            'ResultDateTime',
            'ChargeToPractice',
            'DiagnosticServiceSectionId',
            'ResultStatus',
            'ParentResult',
            'QuantityTiming',
            'ResultCopiesTo',
            'ParentResultsObservationIdentifier',
            'TransportationMode',
            'ReasonForStudy',
            'PrincipalResultInterpreter',
            'AssistantResultInterpreter',
            'Technician',
            'Transcriptionist',
            'ScheduledDateTime',
            'NumberOfSampleContainers',
            'TransportLogisticsOfCollectedSample',
            'CollectorComment',
            'TransportArrangementResponsibility',
            'TransportArranged',
            'EscortRequired',
            'PlannedPatientTransportComment',
            'ProcedureCode',
            'ProcedureCodeModifier',
            'PlacerSupplementalServiceInformation',
            'FillerSupplementalServiceInformation',
            'MedicallyNecessaryDuplicateProcedureReason',
            'ResultHandling',
            'ParentUniversalServiceIdentifier',
            'ObservationGroupId',
            'ParentObservationGroupId',
            'AlternatePlacerOrderNumber',
            'ParentOrder'
        ]);
    }

    /**
     *
     */
    public function actionNte()
    {
        echo $this->generate([
            1 => 'sequenceId',
            'SourceOfComment',
            'Comment',
            'CommentType',
            'EnteredBy',
            'EnteredDateTime',
            'EffectiveStartDate',
            'ExpirationDate',
        ]);
    }


    /**
     * @param $fields
     *
     * @return string
     */
    protected function generate($fields)
    {
        $constants = <<<TPL
const INDEX_{constant} = {index};

TPL;

        $methods = <<<'TPL'

    /**
     * @param string $value
     *
     * @return static
     */
    public function set{object}($value)
    {
        $this->setField(self::INDEX_{constant}, $value);
        return $this;
    }

    /**
     * @return string
     */
    public function get{object}()
    {
        return $this->getField(self::INDEX_{constant});
    }

TPL;


        $constantsStr = '';
        $methodsStr = '';
        foreach ($fields as $index => $field) {


            $constantsStr .= strtr($constants, [
                '{constant}' => strtoupper(Inflector::tableize($field)),
                '{index}' => $index
            ]);


            $methodsStr .= strtr($methods, [
                '{object}' => ucfirst($field),
                '{constant}' => strtoupper(Inflector::tableize($field)),
                '{index}' => $index
            ]);
        }

        return $constantsStr . $methodsStr;
    }

}
