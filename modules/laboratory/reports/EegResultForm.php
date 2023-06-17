<?php
require './roots.php';
require_once $root_path . 'include/inc_environment_global.php';
require_once $root_path . 'classes/tcpdf/tcpdf.php';
require_once($root_path . 'include/care_api_classes/class_personell.php');
require_once($root_path . 'include/care_api_classes/class_special_lab.php');
require_once($root_path . 'include/care_api_classes/class_labservices_transaction.php');
require_once $root_path . 'frontend/bootstrap.php';

use \SegHis\models\HospitalInfo;

class EegResultForm extends TCPDF
{

    protected $margins = array();

    public function __construct()
    {
        parent::__construct();
        $this->AddPage();
        $this->setAutoPageBreak(true, 40);
        $this->SetMargins(10, 10, 10, true);
        $this->margins = $this->getMargins();
    }

    public function header()
    {
        $margin = $this->getMargins();
        $this->SetY($margin['top']);

        $dimension = 25;
        $imgHorizontalMargin = 15;

        $this->SetFont('', '', 11);
        $hospitalInfo = HospitalInfo::getReportHeaderInfo();
        $this->Cell(0, 6, $hospitalInfo->hosp_country, 0, 1, 'C');
        $this->Cell(0, 6, $hospitalInfo->hosp_agency, 0, 1, 'C');
        $this->Cell(0, 6, 'Center for Health Development Davao Region', 0, 1, 'C');
        $this->SetFont('', 'B', 11);
        $this->Cell(0, 6, $hospitalInfo->hosp_name, 0, 1, 'C');
        $this->SetFont('', '', 11);
        $this->Cell(0, 6, $hospitalInfo->hosp_addr1, 0, 1, 'C');
        $this->Image('../../../img/doh.png', $margin['left'] + $imgHorizontalMargin, $margin['top'], $dimension, $dimension);
        $this->Image('../../../gui/img/logos/dmc_logo.jpg', ($this->getPageWidth() - $dimension - $margin['left']) - $imgHorizontalMargin, $margin['top'], $dimension, $dimension);

        $this->Ln(5);

        $this->SetFont('', 'B', 11);
        $this->Cell(0, 6, 'Electroencephalogram Result Form', 0, 1, 'C');
        $this->SetFont('', '', 11);

        $this->Ln(2);

        $this->body();
    }

    public function body()
    {
        $this->patientDetails();
    }

    public function patientDetails()
    {
        /* from modules/reports/reports/EEG_Result_Form.php */
        global $db;
        $refno = $_GET['refno'];
        $service_code = $_GET['service_code'];

        $srv_obj = new SegLab;
        $spl_obj = new SegSpecialLab;
        $pers_obj = new Personell;

        $ref_info = $srv_obj->getLabItemInfo($refno, $service_code)->FetchRow();
        $result = $spl_obj->getAllInfoEEGResult($refno, $service_code);

        $pid = $ref_info['pid'];
        $name = stripslashes(strtoupper($ref_info['name_first'])) . ' ' . stripslashes(strtoupper($ref_info['name_middle'])) . ' ' . stripslashes(strtoupper($ref_info['name_last']));
        $address = trim($db->GetOne("SELECT fn_get_complete_address(?) AS address", $ref_info['pid']));
        $age = floor((time() - strtotime($ref_info['date_birth'])) / 31556926) . ' years old';
        $clinicalData = $ref_info['clinical_info'];
        $date = strtoupper(date("m/d/Y", strtotime($result['perform_dt'])));
        if ($ref_info['manual_doctor']) {
            $request_physician = 'DR. ' . $ref_info['manual_doctor'];
        } else {
            $request_physician = 'DR. ' . $ref_info['request_doctor_name'];
        }

        $html = <<<HTML
<table style="border: solid 1px #000;padding-top: 5px;">
<tr>
    <td>&nbsp;&nbsp;Name: <b>{$name}</b></td>
    <td>&nbsp;&nbsp;HRN: <b>{$pid}</b></td>
</tr>
<tr>
    <td>&nbsp;&nbsp;Address: <b>{$address}</b></td>
    <td>&nbsp;&nbsp;Age: {$age}&nbsp;&nbsp;&nbsp;Sex: M</td>
</tr>
<tr>
    <td>&nbsp;&nbsp;Clinical Data: {$clinicalData}</td>
    <td>&nbsp;&nbsp;Requesting MD: {$request_physician}</td>
</tr>
<tr>
    <td>&nbsp;&nbsp;Medications: {$result['medication']}</td>
    <td>&nbsp;&nbsp;Date Performed: {$date}</td>
</tr>
</table>
HTML;
        $this->writeHTML($html, 1);
        $this->result('Technical Summary', $result['summary']);
        $this->result('Interpretation', $result['interpretation']);

        $doctor = $pers_obj->get_Person_name3($result['consult_doctor']);
        $dr = $doctor->FetchRow();
        $doctor_name = 'DR. ' . mb_strtoupper($dr['name_last']) . ", MD, " . $result['doctor_title'];
        $this->sign1($doctor_name);
        $this->sign2(mb_strtoupper($result['create_id']));
    }

    public function result($title, $content)
    {
        $this->SetFont('', 'B', 11);
        $this->Cell(0, 6, $title, 0, 1, 'L');
        $this->SetFont('', '', 9);
        $this->MultiCell(0, 50, $content, 0, 'L', false, 1);
        $this->SetFont('', '', 11);
    }

    public function sign1($name)
    {
        $this->Ln(10);
        $margin = $this->getMargins();

        $width = $this->getPageWidth() * .4;
        $padding = ($this->getPageWidth() * .6) - $margin['left'] - $margin['right'];

        $this->Cell($padding, 6, '', 0, 0, 'C');
        $this->Cell($width, 6, $name, 0, 1, 'C');
        $this->Cell($padding, 6, '', 0, 0, 'C');
        $this->SetFont('', 'B', 11);
        $this->Cell($width, 6, 'Electroencephalographer', 'T', 1, 'C');
        $this->SetFont('', '', 11);
    }

    public function sign2($name)
    {
        $this->Ln(10);
        $width = $this->getPageWidth() * .4;
        $this->Cell($width, 6, $name, 0, 1, 'C');
        $this->SetFont('', 'B', 11);
        $this->Cell($width, 6, 'EEG Unit In-Charge', 'T', 1, 'C');
    }

    public function footer()
    {
        $this->SetY(-10);

        $margin = $this->getMargins();
        $pageWidth = ($this->getPageWidth() - $margin['left'] - $margin['right']);

        $this->SetFont('', '', 10);
        $this->Cell($pageWidth * .3, 6, 'SPMC-F-SLS-06 ', 0, 0, 'L');
        $this->Cell($pageWidth * .3, 6, 'Effectivity: October 1, 2013', 0, 0, 'C');
        $this->Cell($pageWidth * .2, 6, 'Rev: 0 ', 0, 0, 'C');
        $this->Cell($pageWidth * .2, 6, 'Page ' . $this->PageNo() . ' of ' . $this->getNumPages(), 0, 0, 'C');
    }

}

$pdf = new EegResultForm();
$pdf->Output('12345.pdf', 'I');