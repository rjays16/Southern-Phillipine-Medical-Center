<?php

/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/15/2019
 * Time: 3:27 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;


use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class SOAPService extends XmlWriter
{

    public $document;

    public $encounter;

    public $data;

    /* Initializes Class for SOAP Service*/
    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter,
        $data
    ) {
        $this->document = $document;
        $this->encounter = $encounter;
        $this->data = $data;
    }


    public function getSOAPHeader()
    {
        $header = $this->_createNode(
            $this->document,
            'SOAPS',
            array()
        );
        return $header;
    }


    public function getSOAPDetail()
    {

        $detail = $this->_createNode(
            $this->document,
            'SOAP',
            $this->getSOAPData()
        );

        return $detail;
    }

    public function createSOAP()
    {
        /* GENERATES <SOAPS> HEADER */
        $header = $this->getSOAPHeader();
        /* GENERATES <SOAP> HEADER */
        $detail = $this->getSOAPDetail();

        /* GENERATES SUBJECTIVE NODE UNDER <SOAP> HEADER */
        $this->getSUBJECTIVE($detail);
        /* GENERATES PEPERT NODE UNDER <SOAP> HEADER */
        $this->getPEPERT($detail);
        /* GENERATES PEMISC NODE UNDER <SOAP> HEADER */
        $this->getPEMISC($detail);
        /* GENERATES PESPECIFIC NODE UNDER <SOAP> HEADER */
        $this->getPESPECIFIC($detail);
        /* GENERATES ICDS NODE UNDER <SOAP> HEADER */
        $this->getICDS($detail);
        /* GENERATES DIAGNOSTIC NODE UNDER <SOAP> HEADER */
        $this->getDIAGNOSTIC($detail);
        /* GENERATES MANAGEMENT NODE UNDER <SOAP> HEADER */
        $this->getMANAGEMENT($detail);
        /* GENERATES ADVICE NODE UNDER <SOAP> HEADER */
        $this->getADVICE($detail);


        /* Appending the SOAP NODE Under SOAPS HEADER*/
        $header->appendChild($detail);
        /* Adding the whole SOAP INTO XML*/
        $this->document->appendChild($header);

        return $header;
    }


    public function getSOAPData()
    {
        return array(
            'pHciCaseNo' => $this->encounter->encounter_nr,
            'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
            'pPatientPin' => CF4Service::getPatientPin($this->encounter->encounter_nr),
            'pPatientType' => CF4Service::getPatientType($this->encounter->encounter_nr),
            'pMemPin' => null,
            'pSoapDate' => date('Y-m-d', strtotime($this->encounter->discharge_date)),
            'pEffYear' => CF4Helper::getYear(),
            'pSoapATC' => 'CF4',
            'pReportStatus' => CF4Helper::getDefaultReportStatus(),
            'pDeficiencyRemarks' => ''
        );
    }

    public function getSUBJECTIVE($detail)
    {
        $data = $this->data;

        if (empty($data)) {
            $data[] = array();
        }


        $chiefComplaint = '';

        $pertSigns = '';

        $painSite = '';

        $otherComplaint = '';

        $assoc = $data->assessmentdata->chief_complaint->assoc;

        $symptoms = $data->pertinentSigns->data_phic;

        $pains = $data->pertinentSigns->opt_2;

        $others = $data->pertinentSigns->opt_3;


        if (!empty($others)) {
            foreach ($others as $other) {
                $otherComplaint .= $other . ',';
            }
        }


        if (!empty($pains)) {
            foreach ($pains as $pain) {
                $painSite .= $pain . ',';
            }
        }

        if (!empty($assoc)) {
            foreach ($assoc as $datum) {
                /*Create multiple chief complaint values as 1 string*/
                $chiefComplaint .= $datum . ',';
            }
        }


        if (!empty($symptoms)) {
            foreach ($symptoms as $symptom) {
                /*Create multiple symptoms values as 1 string*/
                $pertSigns .= $symptom . ';';
            }
        }


        $chiefComplaint = substr($chiefComplaint, 0, -1);

        if (!empty($otherComplaint)) {
            $pertSigns .= 'X;';
        }

        $history = $this->data->presentIllness->history;

        $this->appendNode(
            $detail,
            $details,
            'SUBJECTIVE',
            array(
                'pChiefComplaint' => $chiefComplaint . ', ' . $this->data->assessmentdata->chief_complaint->others,
                'pSignsSymptoms' => $pertSigns,
                'pIllnessHistory' => empty($history) ? null : $history,
                'pOtherComplaint' => $otherComplaint,
                'pPainSite' => $painSite,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => ''
            )
        );
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
                $details,
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
                    'pDeficiencyRemarks' => ''
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
                            'pDeficiencyRemarks' => ''
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
                'pRectalRem' => null,
                'pGuRem' => $gu,
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => ''
            )
        );
    }


    public function getICDS($detail)
    {
        $this->appendNode(
            $detail,
            $icd,
            'ICDS',
            array(
                'pIcdCode' => CF4Helper::getDefaultIcd(),
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => ''
            )
        );
    }

    public function getDIAGNOSTIC($detail)
    {
        $this->appendNode(
            $detail,
            $diagnostic,
            'DIAGNOSTIC',
            array(
                'pDiagnosticId' => '0',
                'pOthRemarks' => '',
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => ''
            )
        );
    }

    public function getMANAGEMENT($detail)
    {
        $this->appendNode(
            $detail,
            $management,
            'MANAGEMENT',
            array(
                'pManagementId' => '0',
                'pOthRemarks' => '',
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => ''
            )
        );
    }

    public function getADVICE($detail)
    {

        $data = $this->data;

        $this->appendNode(
            $detail,
            $advice,
            'ADVICE',
            array(
                'pRemarks' => CF4Helper::getDefaultNAstatus(),
                'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                'pDeficiencyRemarks' => ''
            )
        );
    }
}
