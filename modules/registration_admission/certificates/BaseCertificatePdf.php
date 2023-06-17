<?php

include("roots.php");
define('FPDF_FONTPATH', 'font/');
require($root_path . "/classes/fpdf/fpdf.php");

class BaseCertificatePdf extends FPDF
{

    public $encoder;

    public $code;

    public $fontStyle = 'Arial';

    public $fontSize = 12;

    public function footer()
    {
        $this->SetY(-24);
        $this->setFont($this->fontStyle, "B", $this->fontSize);
        $this->Cell(2, 3, '', "", 0, '');
        $this->Cell(0, 1, 'NOT VALID ', 0, 1, 'L');
        $this->SetY(-18);
        $this->Cell(2, 3, '', "", 0, '');
        $this->Cell(0, 1, 'WITHOUT SPMC SEAL ', 0, 1, 'L');

        $this->SetY(-15);
        $this->SetX(12);
        $this->SetFont($this->fontStyle, 'I', 8);
        $this->Cell(0, 10, 'Encoded by : ' . $this->encoder, 0, 1, 'L');
        $this->AliasNbPages();
        $this->SetY(-10);

        $this->SetFont($this->fontStyle, '', 8);
        if ($this->code)
            $this->Cell(60, 8, $this->code, 0, 0, 'L');
        $this->Cell(80, 8, 'Effectivity : October 1, 2013', 0, 0, 'C');
        $this->Cell(50, 8, 'Revision : 0', 0, 0, 'R');
    }

}