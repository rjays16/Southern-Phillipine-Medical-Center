<?php

/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/15/2019
 * Time: 3:29 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;

use Composer\Downloader\VcsDownloader;
use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\models\BloodTypePatient;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class ProfilingService extends XmlWriter
{

    public $document;
    public $encounter;
    public $data;

    const PROFILE_FEMALE = 'f';

    /* Initializes Class for Profiling Service*/
    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter,
        $data
    ) {
        $this->document = $document;
        $this->encounter = $encounter;
        $this->data = $data;
    }

    public function getHeader()
    {
        $header = $this->_createNode(
            $this->document,
            'PROFILING',
            array()
        );
        return $header;
    }

    public function generateNode()
    {

        $header = $this->getHeader();


        $detail = $this->_createNode(
            $this->document,
            'PROFILE',
            $this->getProfileData()
        );

        /* GENERATES OINFO NODE UNDER <SOAP> PROFILE */
        $this->getOINFO($detail);
        /* GENERATES MEDHIST NODE UNDER <SOAP> PROFILE */
        $this->getMEDHIST($detail);
        /* GENERATES MHSPECIFIC NODE UNDER <SOAP> PROFILE */
        $this->getMHSPECIFIC($detail);
        /* GENERATES SURGHIST NODE UNDER <SOAP> PROFILE */
        $this->getSURGHIST($detail);
        /* GENERATES FAMHIST NODE UNDER <SOAP> PROFILE */
        $this->getFAMHIST($detail);
        /* GENERATES FHSPECIFIC NODE UNDER <SOAP> PROFILE */
        $this->getFHSPECIFIC($detail);
        /* GENERATES SOCHIST NODE UNDER <SOAP> PROFILE */
        $this->getSOCHIST($detail);
        /* GENERATES IMMUNIZATION NODE UNDER <SOAP> PROFILE */
        $this->getIMMUNIZATION($detail);
        /* GENERATES MENSHIST NODE UNDER <SOAP> PROFILE */
        $this->getMENSHIST($detail);
        /* GENERATES PREGHIST NODE UNDER <SOAP> PROFILE */
        $this->getPREGHIST($detail);
        /* GENERATES PEPERT NODE UNDER <SOAP> PROFILE */
        $this->getPEPERT($detail);
        /* GENERATES BLOODTYPE NODE UNDER <SOAP> PROFILE */
        $this->getBLOODTYPE($detail);
        /* GENERATES PEGENSURVEY NODE UNDER <SOAP> PROFILE */
        $this->getPEGENSURVEY($detail);
        /* GENERATES PEMISC NODE UNDER <SOAP> PROFILE */
        $this->getPEMISC($detail);
        /* GENERATES PESPECIFIC NODE UNDER <SOAP> PROFILE */
        $this->getPESPECIFIC($detail);
        /* GENERATES DIAGNOSTIC NODE UNDER <SOAP> PROFILE */
        $this->getDIAGNOSTIC($detail);
        /* GENERATES MANAGEMENT NODE UNDER <SOAP> PROFILE */
        $this->getMANAGEMENT($detail);
        /* GENERATES ADVICE NODE UNDER <SOAP> PROFILE */
        $this->getADVICE($detail);
        /* GENERATES NCDQANS NODE UNDER <SOAP> PROFILE */
        $this->getNCDQANS($detail);

        $header->appendChild($detail);
        $this->document->appendChild($header);
        return $header;
    }

    public function getOINFO($detail)
    {

        $this->appendNode(
            $detail,
            $enlistment,
            'OINFO',
            array(
                'pPatientPob' => null,
                'pPatientAge' => null,
                'pPatientOccupation' => null,
                'pPatientEducation' => null,
                'pPatientReligion' => null,
                'pPatientMotherMnln' => null,
                'pPatientMotherMnmi' => null,
                'pPatientMotherFn' => null,
                'pPatientMotherExtn' => null,
                'pPatientMotherBday' => null,
                'pPatientFatherLn' => null,
                'pPatientFatherMi' => null,
                'pPatientFatherFn' => null,
                'pPatientFatherExtn' => null,
                'pPatientFatherBday' => null,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null,
            )
        );
    }

    public function getMEDHIST($detail)
    {
        $data = $this->data->medHistory;
        if (empty($data)) {
            $data[] = (object)array();
        }

        foreach ($data as $datum) {
            $this->appendNode(
                $detail,
                $enlistment,
                'MEDHIST',
                array(
                    'pMdiseaseCode' => null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null,
                )
            );
        }
    }

    public function getMHSPECIFIC($detail)
    {
        $data = $this->data->medHistorySpecific;

        if (empty($data)) {
            $data[] = (object)array();
        }

        $description = "";

        foreach ($data as $datum) {
            $description .= $datum->mdisease_description . ' ' . $datum->specific_disease_description . " ,";
        }


        $this->appendNode(
            $detail,
            $enlistment,
            'MHSPECIFIC',
            array(
                'pMdiseaseCode' => null,
                'pSpecificDesc' => rtrim($description, ","),
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null,
            )
        );
    }

    public function getSURGHIST($detail)
    {
        $data = $this->data->surgicalHistory;

        if (empty($data)) {
            $data[] = (object)array();
        }
        foreach ($data as $datum) {
            $this->appendNode(
                $detail,
                $enlistment,
                'SURGHIST',
                array(
                    'pSurgDesc' => null,
                    'pSurgDate' => null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null
                )
            );
        }
    }

    public function getFAMHIST($detail)
    {

        $data = $this->data->famHistory;

        if (empty($data)) {
            $data[] = (object)array();
        }

        foreach ($data as $datum) {
            $this->appendNode(
                $detail,
                $enlistment,
                'FAMHIST',
                array(
                    'pMdiseaseCode' => !empty($detail->mdisease_code) ? $detail->mdisease_code : null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null,
                )
            );
        }
    }

    public function getFHSPECIFIC($detail)
    {
        $data = $this->data->famHistorySpecific;

        if (empty($data)) {
            $data[] = (object)array();
        }

        foreach ($data as $datum) {
            $this->appendNode(
                $detail,
                $enlistment,
                'FHSPECIFIC',
                array(
                    'pMdiseaseCode' => null,
                    'pSpecificDesc' => null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null
                )
            );
        }
    }

    public function getSOCHIST($detail)
    {

        $data = $this->data->socialHistory;

        if (empty($data)) {
            $data[] = (object)array();
        }


        foreach ($data as $datum) {
            $this->appendNode(
                $detail,
                $enlistment,
                'SOCHIST',
                array(
                    'pIsSmoker' => null,
                    'pNoCigpk' => null,
                    'pIsAdrinker' => null,
                    'pNoBottles' => null,
                    'pIllDrugUser' => null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null
                )
            );
        }
    }

    public function getIMMUNIZATION($detail)
    {

        $data = $this->data->immunizationHistory;

        $this->appendNode(
            $detail,
            $enlistment,
            'IMMUNIZATION',
            array(
                'pChildImmcode' => null,
                'pYoungwImmcode' => null,
                'pPregwImmcode' => null,
                'pElderlyImmcode' => null,
                'pOtherImm' => null,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getMENSHIST($detail)
    {
        $data = $this->data->menstrualHistory;

        if (empty($data)) {
            $data[] = (object)array();
        }

        foreach ($data as $datum) {
            $applicable = $datum->is_applicable_menstrual;

            $this->appendNode(
                $detail,
                $enlistment,
                'MENSHIST',
                array(
                    'pMenarchePeriod' => null,
                    'pLastMensPeriod' =>  empty($applicable) || $applicable == 'N'  ? "" : $datum->last_period_menstrual,
                    'pPeriodDuration' => null,
                    'pMensInterval' => null,
                    'pPadsPerDay' => null,
                    'pOnsetSexIc' => null,
                    'pBirthCtrlMethod' => null,
                    'pIsMenopause' => null,
                    'pIsApplicable' => empty($applicable) ? "N" : $applicable,
                    'pMenopauseAge' => null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null,
                )
            );
        }
    }

    public function getPREGHIST($detail)
    {

        $data = $this->data->pregnantHistory;

        if (empty($data)) {
            $data[] = (object)array();
        }

        foreach ($data as $datum) {

            $this->appendNode(
                $detail,
                $enlistment,
                'PREGHIST',
                array(
                    'pPregCnt' => $datum->date_gravidity,
                    'pDeliveryCnt' => $datum->date_parity,
                    'pDeliveryTyp' => null,
                    'pFullTermCnt' => $datum->no_full_term_preg,
                    'pPrematureCnt' => $datum->no_premature,
                    'pAbortionCnt' => $datum->no_abortion,
                    'pLivChildrenCnt' => $datum->no_living_children,
                    'pWPregIndhyp' => null,
                    'pWFamPlan' => null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null
                )
            );
        }
    }

    public function getPEPERT($detail)
    {

        $service = new CF4DataService($this->encounter);
        $data = $service->getVitalSigns();



        if (empty($data)) {
            $data[] = array();
        }

        foreach ($data as $datum) {


            $this->appendNode(
                $detail,
                $enlistment,
                'PEPERT',
                array(
                    'pSystolic' => empty($datum['systolic']) ? "0" : $datum['systolic'],
                    'pDiastolic' => empty($datum['systolic']) ? "0" : $datum['diastolic'],
                    'pHr' => empty($datum['pulse_rate']) ? "0" : $datum['pulse_rate'],
                    'pRr' => empty($datum['respiratory']) ? "0" : $datum['respiratory'],
                    'pHeight' => empty($datum['height']) ? "0" : (int)$datum['height'],
                    'pWeight' => empty($datum['weight']) ? "0" : (int)$datum['weight'],
                    'pTemp' => empty($datum['temperature']) ? "0" : $datum['temperature'],
                    'pVision' => '',
                    'pLength' => '',
                    'pHeadCirc' => '',
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => ''
                )
            );
        }
    }

    public function getBLOODTYPE($detail)
    {
        $bloodType = BloodTypePatient::model()->findByPk($this->encounter->person->pid);

        $this->appendNode(
            $detail,
            $enlistment,
            'BLOODTYPE',
            array(
                'pBloodType' => $bloodType->bloodType->group,
                'pBloodRh' => null,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getPEGENSURVEY($detail)
    {
        $notes = $this->data->notes->physicalExaminationDetailed->{"General Survey"};

        $this->appendNode(
            $detail,
            $enlistment,
            'PEGENSURVEY',
            array(
                'pGenSurveyId' => $notes[0]->pGenSurveyId,
                'pGenSurveyRem' => $notes[0]->pGenSurveyRem,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getPEMISC($detail)
    {
        $service = new CF4DataService($this->encounter);

        $exam = $this->data->notes->physicalExaminationDetailed;
        $pemisc = $service->getPeMiscIteration($exam);

        $physicalExaminations = array('skin', 'chest', 'heent', 'abdomen', 'neuro', 'rectal', 'gu', 'cvs');


        if (empty($pemisc['data'])) {
            $data[] = (object)array();
        }

        if ($pemisc['data'][0] == 0) {
            $this->appendNode(
                $detail,
                $enlistment,
                'PEMISC',
                array(
                    'pSkinId' => " ",
                    'pHeentId' => " ",
                    'pChestId' => " ",
                    'pHeartId' => " ",
                    'pAbdomenId' => " ",
                    'pNeuroId' => " ",
                    'pRectalId' => " ",
                    'pGuId' => " ",
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => null
                )
            );
        }


        foreach ($physicalExaminations as $physicalExamination) {
            if (!empty($pemisc[$physicalExamination])) {
                foreach ($pemisc[$physicalExamination] as $e => $exam) {
                    $this->appendNode(
                        $detail,
                        $enlistment,
                        'PEMISC',
                        array(
                            'pSkinId' => $pemisc[$physicalExamination][$e]->pSkinId,
                            'pHeentId' => $pemisc[$physicalExamination][$e]->pHeentId,
                            'pChestId' => $pemisc[$physicalExamination][$e]->pChestId,
                            'pHeartId' => $pemisc[$physicalExamination][$e]->pHeartId,
                            'pAbdomenId' => $pemisc[$physicalExamination][$e]->pAbdomenId,
                            'pNeuroId' => $pemisc[$physicalExamination][$e]->pNeuroId,
                            'pRectalId' => $pemisc[$physicalExamination][$e]->pRectalId,
                            'pGuId' => $pemisc[$physicalExamination][$e]->pGuId,
                            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                            'pDeficiencyRemarks' => null
                        )
                    );
                }
            }
        }
    }


    public function getPEMISCData($category, $attr)
    {
        $results = $this->data->notes->physicalExaminationDetailed->{$category};

        $data = array();
        if (!empty($results)) {
            foreach ($results as $key => $result) {
                if (!empty($result->$attr)) {
                    $data[] = $result;
                }
            }
        }
        return $data;
    }

    public function getPESPECIFIC($detail)
    {
        $service = new CF4DataService($this->encounter);

        $data = $this->data->notes->physicalExaminationDetailed;

        $chest = $service->getPESPECData($data, 'Chest/Lungs', 'pChestId', 'pChestRem');
        $heent = $service->getPESPECData($data, 'HEENT', 'pHeentId', "pHeentRem");
        $skin = $service->getPESPECData($data, 'SKIN/EXTREMITIES', 'pSkinId', "pSkinRem");
        $abdomen = $service->getPESPECData($data, 'ABDOMEN', 'pAbdomenId', "pAbdomenRem");
        $neuro = $service->getPESPECData($data, 'NEURO-EXAM', 'pNeuroId', "pNeuroRem");
        $rectal = $service->getPESPECData($data, 'RECTAL', 'pRectalId', "pRectalRem");
        $gu = $service->getPESPECData($data, 'GU (IE)', 'pGuId', "pGuRem");
        $cvs = $service->getPESPECData($data, 'CVS', 'pHeartId', "pHeartRem");


        $this->appendNode(
            $detail,
            $enlistment,
            'PESPECIFIC',
            array(
                'pSkinRem' => $skin,
                'pHeentRem' => $heent,
                'pChestRem' => $chest,
                'pHeartRem' => $cvs,
                'pAbdomenRem' => $abdomen,
                'pNeuroRem' => $neuro,
                'pRectalRem' => $rectal,
                'pGuRem' => $gu,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }


    public function getDIAGNOSTIC($detail)
    {

        $this->appendNode(
            $detail,
            $enlistment,
            'DIAGNOSTIC',
            array(
                'pDiagnosticId' => '0',
                'pOthRemarks' => null,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getMANAGEMENT($detail)
    {
        $this->appendNode(
            $detail,
            $enlistment,
            'MANAGEMENT',
            array(
                'pManagementId' => '0',
                'pOthRemarks' => null,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getADVICE($detail)
    {

        $data = $this->data;


        $this->appendNode(
            $detail,
            $enlistment,
            'ADVICE',
            array(
                'pRemarks' => CF4Helper::getDefaultNAstatus(),
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getNCDQANS($detail)
    {

        $this->appendNode(
            $detail,
            $enlistment,
            'NCDQANS',
            array(
                'pQid1_Yn' => null,
                'pQid2_Yn' => null,
                'pQid3_Yn' => null,
                'pQid4_Yn' => null,
                'pQid5_Ynx' => null,
                'pQid6_Yn' => null,
                'pQid7_Yn' => null,
                'pQid8_Yn' => null,
                'pQid9_Yn' => null,
                'pQid10_Yn' => null,
                'pQid11_Yn' => null,
                'pQid12_Yn' => null,
                'pQid13_Yn' => null,
                'pQid14_Yn' => null,
                'pQid15_Yn' => null,
                'pQid16_Yn' => null,
                'pQid17_Abcde' => null,
                'pQid18_Yn' => null,
                'pQid19_Yn' => null,
                'pQid19_Fbsmg' => null,
                'pQid19_Fbsmmol' => null,
                'pQid19_Fbsdate' => null,
                'pQid20_Yn' => null,
                'pQid20_Choleval' => null,
                'pQid20_Choledate' => null,
                'pQid21_Yn' => null,
                'pQid21_Ketonval' => null,
                'pQid21_Ketondate' => null,
                'pQid22_Yn' => null,
                'pQid22_Proteinval' => null,
                'pQid22_Proteindate' => null,
                'pQid23_Yn' => null,
                'pQid24_Yn' => null,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => null
            )
        );
    }

    public function getProfileData()
    {
        return array(
            'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
            'pDeficiencyRemarks' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pProfileATC' => 'CF4',
            'pEffYear' => date('Y'),
            'pRemarks' => null,
            'pProfDate' => date('Y-m-d', strtotime($this->encounter->encounter_date)),
            'pMemPin' => null,
            'pPatientType' => null,
            'pPatientPin' => CF4Service::getPatientPin($this->encounter->encounter_nr),
            'pHciCaseNo' => $this->encounter->encounter_nr,
        );
    }

    public function getOInfoData()
    {
        return array(
            'pPatientPob' => null,
            'pPatientAge' => null,
            'pPatientOccupation' => null,
            'pPatientEducation' => null,
            'pPatientReligion' => null,
            'pPatientMotherMnln' => null,
            'pPatientMotherMnmi' => null,
            'pPatientMotherFn' => null,
            'pPatientMotherExtn' => null,
            'pPatientMotherBday' => null,
            'pPatientFatherLn' => null,
            'pPatientFatherMi' => null,
            'pPatientFatherFn' => null,
            'pPatientFatherExtn' => null,
            'pPatientFatherBday' => null,
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => null,
        );
    }
}
