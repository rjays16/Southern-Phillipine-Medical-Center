<?php

/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/16/2019
 * Time: 6:29 AM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;

use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\services\cf4\CF4Service;
use SegHis\modules\eclaims\services\cf4\XmlWriter;

class MedicineService extends XmlWriter
{

    public $document;

    public $encounter;

    public function __construct(
        \DOMDocument $document,
        \EclaimsEncounter $encounter
    ) {
        $this->document = $document;
        $this->encounter = $encounter;
    }


    public function generateHeader()
    {
        $header = $this->_createNode(
            $this->document,
            'MEDICINES',
            array()
        );

        return $header;
    }

    public function generateNode()
    {
        $header = $this->generateHeader();

        $service = new CF4DataService($this->encounter);

        $orders = $service->getMedicines($this->encounter->encounter_nr);

        if (!empty($this->encounter->parent_encounter_nr)) {
            $parentEncounter = $service->getParentEncounter($this->encounter->parent_encounter_nr);
            $parentMeds = $service->getMedicines($parentEncounter);

            $orders = \CMap::mergeArray($orders, $parentMeds);
        }


        $pApplicable = 'Y';

        if (empty($orders)) {
            $orders[] = array();
            $pApplicable = 'N';
        }

        foreach ($orders as $order) {
            $frequency = $order['frequency'];

            $route = $order['route'];

            if (!empty($order['refno'])) {
                $route = $this->getRoute($order['refno'], $order['bestellnum']);
                $frequency = $this->getFrequency($order['refno'], $order['bestellnum']);
            }

            $generic = $order['is_drug'] ? $this->getGenericName($order) : $order['new_generic'];

            $new_generic = $order['is_drug'] ? $generic . ' / ' . $order['new_dosage_phic'] : $generic;


            $pTotalAmountPrice = !empty($order['totalPrice']) ? $order['totalPrice'] : $order['unitPrice'] * $order['quantity'];
            $this->appendNode(
                $header,
                $meds,
                'MEDICINE',
                array(
                    'pModule' => 'CF4',
                    'pHciCaseNo' => $this->encounter->encounter_nr,
                    'pHciTransNo' => CF4Service::getpHciTransNo($this->encounter->encounter_nr),
                    'pDrugCode' => !empty($order['drug_code']) ? $order['drug_code'] : (!empty($new_generic) && empty($order['drug_code']) ? "" : CF4Helper::getNomedsDrugCode()),
                    'pGenericCode' => !empty($order['gen_code']) ? $order['gen_code'] : (!empty($new_generic) && empty($order['drug_code']) ? "" : CF4Helper::getNomedsGenericCode()),
                    'pGenericName' => !empty($new_generic) ? $new_generic : CF4Helper::getNomedsGeneric(),
                    'pStrengthCode' => !empty($order['strength_code']) ? $order['strength_code'] : "00000",
                    'pFormCode' => !empty($order['form_code']) ? $order['form_code'] : "00000",
                    'pPackageCode' => !empty($order['package_code']) ? $order['package_code'] : "00000",
                    'pQuantity' => !empty($order['quantity']) ? $order['quantity'] : 0,
                    'pUnitCode' => !empty($order['unit_code']) ? $order['unit_code'] : "00000",
                    'pRoute' => !empty($route) ? $route : "NA",
                    'pSaltCode' => !empty($order['salt_code']) ? $order['salt_code'] : "00000",
                    'pActualUnitPrice' => null,
                    'pCoPayment' => "",
                    'pTotalAmtPrice' => $pTotalAmountPrice === 0 ? "0.00" : $pTotalAmountPrice,
                    'pInstructionQuantity' => "",
                    'pInstructionStrength' => "",
                    'pInstructionFrequency' => !empty($frequency) ? $frequency : "NA",
                    'pPrescPhysician' => "",
                    'pIsApplicable' => $pApplicable,
                    'pDateAdded' => !empty($order['dateAdded']) ? date('Y-m-d', strtotime($order['dateAdded'])) : null,
                    'pReportStatus' => CF4Helper::getDefaultReportStatus(),
                    'pDeficiencyRemarks' => "",
                )
            );
        }

        return $header;
    }


    public function getRoute($refno, $bestellnum)
    {
        $command = \Yii::app()->db->createCommand();

        $command->select('t.route');
        $command->from('seg_pharma_items_cf4 t');

        $command->where('t.bestellnum = :bestellnum AND t.refno = :refno ');
        $command->params[':bestellnum'] = $bestellnum;
        $command->params[':refno'] = $refno;

        $result = $command->queryRow();

        return $result['route'];
    }


    public function getFrequency($refno, $bestellnum)
    {
        $command = \Yii::app()->db->createCommand();

        $command->select('t.frequency');
        $command->from('seg_pharma_items_cf4 t');

        $command->where('t.bestellnum = :bestellnum AND t.refno = :refno ');
        $command->params[':bestellnum'] = $bestellnum;
        $command->params[':refno'] = $refno;

        $result = $command->queryRow();

        return $result['frequency'];
    }

    public function getGenericName($order)
    {
        if (!empty($order['generic']) && empty($order['drug_code'])) {
            return $order['generic'];
        }

        if (!empty($order['genCode'])) {
            $command = \Yii::app()->db->createCommand();

            $command->select("t.generic");
            $command->from('care_pharma_products_main t');

            $command->where('t.bestellnum = :gen_code');
            $command->params[':gen_code'] = $order['genCode'];

            $result = $command->queryRow();

            return $result['generic'];
        }
    }

    public static function getDrugDetails($code)
    {
        $command = \Yii::app()->db->createCommand();

        $command->select('t.gen_code,t.form_code,t.unit_code,t.package_code,t.strength_code');

        $command->from('seg_phil_medicine t');
        $command->where('t.drug_code = :drug_code');
        $command->params[':drug_code'] = $code;

        return $command->queryRow();
    }
}
