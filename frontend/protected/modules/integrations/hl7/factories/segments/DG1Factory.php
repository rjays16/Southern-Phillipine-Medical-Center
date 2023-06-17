<?php

/**
 * EVNFactory.php
 *
 * @author Alvin Quinones <ajmquinones@gmail.com>
 * @copyright (c) 2016, Segworks Technologies Corporation
 *
 */

namespace SegHEIRS\modules\integrations\hl7\factories\segments;
use DateTime;
use EncounterDxpr;
use SegHEIRS\modules\integrations\hl7\segments\DG1;

/**
 *
 * Description of EVNFactory
 *
 */

class DG1Factory
{

    /**
     * @param EncounterDxpr $dxpr
     *
     * @return DG1
     */
    public function create(EncounterDxpr $dxpr)
    {
        $dg1 = new DG1();
        $dg1->setDiagnosisCode(
            $dxpr->alt_diagnosis,
            $dxpr->icdCode->icd_code,
            $dxpr->icdCode->icd_desc,
            'I10'
        );

        $dt = new DateTime($dxpr->create_dt);
        if ($dt) {
            $dg1->setDiagnosisDateTime($dt->format('YmdHis'));
        }

        if ($dxpr->is_final) {
            $dg1->setDiagnosisType('F');
        } else {
            $dg1->setDiagnosisType('A');
        }

        return $dg1;
    }

}
