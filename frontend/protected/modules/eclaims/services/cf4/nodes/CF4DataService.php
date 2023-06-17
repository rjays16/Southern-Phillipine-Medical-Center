<?php

/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/30/2019
 * Time: 6:35 PM
 */

namespace SegHis\modules\eclaims\services\cf4\nodes;

use CMap;
use SegHis\modules\eclaims\helpers\cf4\CF4Helper;
use SegHis\modules\eclaims\models\EclaimsPharmaOrderItems;
use SegHis\modules\person\models\Encounter;


class CF4DataService
{

  const MEDICINE_TYPE = 'M';

  public $document;

  public $encounter;

  /* Initializes Class for SOAP Service*/
  public function __construct(
    \EclaimsEncounter $encounter
  ) {
    $this->encounter = $encounter;
  }


  /* Generation of Vital Signs of Patient*/
  public function getVitalSigns()
  {
    $command = \Yii::app()->db->createCommand();

    $command->select('
            t.temperature,
            t.pulse_rate,
            t.systolic,
            t.diastolic,
            t.respiratory,
            t.oxygen_saturation
        ');

    $command->from('seg_encounter_vital_sign t');
    $command->where('t.encounter_nr = :encounter AND t.is_deleted != 1');
    $command->params[':encounter'] = $this->encounter->encounter_nr;
    $result = $command->queryRow();
    $bmi = $this->getBmi();

    $data = array();

    if (!$bmi && $result) {
      $data[] = $result;
    }
    if ($bmi && !$result) {
      $data[] = $bmi;
    }
    if ($bmi && $result) {
      $data[] = $bmi + $result;
    }
    return $data;
  }


  // Query for getting the bmi of the patient
  public function getBmi()
  {
    $command = \Yii::app()->db->createCommand();
    $command->select('t.height,t.weight,t.bmi_date');
    $command->from('seg_encounter_vital_sign_bmi t');
    $command->where('t.encounter_nr = :encounter AND t.is_deleted != 1');
    $command->order = array('t.create_dt ASC');
    $command->params[':encounter'] = $this->encounter->encounter_nr;
    return $command->queryRow();
  }


  public function getPeMiscIteration($data)
  {

    $chest = $this->getPEMISCData($data, 'Chest/Lungs', 'pChestId');
    $heent = $this->getPEMISCData($data, 'HEENT', 'pHeentId');
    $skin = $this->getPEMISCData($data, 'SKIN/EXTREMITIES', 'pSkinId');
    $abdomen = $this->getPEMISCData($data, 'ABDOMEN', 'pAbdomenId');
    $neuro = $this->getPEMISCData($data, 'NEURO-EXAM', 'pNeuroId');
    $rectal = $this->getPEMISCData($data, 'RECTAL', 'pRectalId');
    $gu = $this->getPEMISCData($data, 'GU (IE)', 'pGuId');
    $cvs = $this->getPEMISCData($data, 'CVS', 'pHeartId');

    $count = array(
      'HEENT' => count($heent),
      'SKIN/EXTREMITIES' => count($skin),
      'Chest/Lungs' => count($chest),
      'ABDOMEN' => count($abdomen),
      'NEURO-EXAM' => count($neuro),
      'RECTAL' => count($rectal),
      'GU (IE)' => count($gu),
      'CVS' => count($cvs)
    );

    $b = max($count);
    $data = range(1, $b);


    if ($b == 0) {
      $data = array(0);
    } else {
      $data = range(1, $b);
    }


    return array(
      'data' => $data,
      'chest' => $chest,
      'heent' => $heent,
      'skin' => $skin,
      'abdomen' => $abdomen,
      'neuro' => $neuro,
      'rectal' => $rectal,
      'gu' => $gu,
      'cvs' => $cvs
    );
  }


  public function getPEMISCData($data, $category, $attr)
  {

    $results = $data->{$category};
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

  public function getPESPECData($data, $category, $id, $remarks)
  {
    $results = $data->{$category};
    $text = '';

    if (!empty($results)) {
      foreach ($results as $key => $result) {
        if ($result->$id == CF4Helper::getOthersPemisc()) {
          $text = $result->$remarks;
        }
      }
    }
    return $text;
  }

  public function getMedsOrder($encounterNo)
  {
    $command = \Yii::app()->db->createCommand();

    $command->select("
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                pharma.drug_code,
                ''
            ) AS drug_code,
            items.quantity,
            (
                CASE WHEN orders.is_cash 
                THEN items.pricecash 
                ELSE items.pricecharge END
            ) as unitPrice, 
            items.serve_dt as dateAdded,
            pharma.generic,
            items.refno,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                med.gen_code,
                ''
            ) AS gen_code,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                med.form_code,
                ''
            ) AS form_code,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                med.salt_code,
                ''
            ) AS salt_code,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                med.package_code,
                ''
            ) AS package_code,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                med.strength_code,
                ''
            ) AS strength_code,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                med.unit_code,
                ''
            ) AS unit_code,
            items.bestellnum,
            IF(
                spm_strength.`strength_disc` = spi_cf.`dosage`,
                1,
                0
            ) AS is_drug,
            IF(
                spi_cf.`dosage`,
                CONCAT(
                    pharma.`generic`,
                    ' / ',
                    spi_cf.`dosage`
                ),
                pharma.`generic`
            ) new_generic,
            spi_cf.`dosage` AS new_dosage_phic,
            items.bestellnum AS genCode
        ");


    $command->from('seg_pharma_order_items items');
    $command->leftJoin('seg_pharma_orders orders', 'items.refno=orders.refno');
    $command->leftJoin('care_pharma_products_main pharma', 'pharma.bestellnum=items.bestellnum');
    $command->leftJoin('seg_phil_medicine med', 'med.drug_code = pharma.drug_code');
    $command->where("orders.encounter_nr=:encounter AND items.serve_status = :served AND pharma.prod_class= :medicine
                        AND (((items.quantity - p_return_items.quantity) > 0) 
                           OR p_return_items.quantity IS NULL)");

    /*Excludee pharma returns */
    $command->leftJoin(
      'seg_pharma_return_items p_return_items',
      'orders.refno = p_return_items.ref_no AND items.bestellnum = p_return_items.bestellnum '
    );
    $command->leftJoin('seg_pharma_returns p_returns', 'p_return_items.return_nr = p_returns.return_nr');
    $command->leftJoin('seg_phil_medicine sp_medicines', 'sp_medicines.drug_code = pharma.drug_code');
    $command->leftJoin('seg_phil_medicine_strength spm_strength', 'spm_strength.strength_code = sp_medicines.strength_code');
    $command->leftJoin('seg_pharma_items_cf4 spi_cf', 'spi_cf.refno = items.refno AND spi_cf.bestellnum = items.bestellnum');


    $command->params = array(
      ':encounter' => $encounterNo,
      ':served' => EclaimsPharmaOrderItems::STATUS_SERVED,
      ':medicine' => self::MEDICINE_TYPE,
    );
    //\CVarDumper::dump($command->query());die;
    $medicines = $command->queryAll();


    return $medicines;
  }

  public function getGenMeds($genCode)
  {
    $command = \Yii::app()->db->createCommand();

    $command->select("gen_description , gen_code");
    $command->from('seg_phil_medecine_generic t');

    $command->params = array(
      ':genCode' => $genCode
    );
    $command->where("t.gen_code = :genCode");

    return $command->queryRow();
  }


  public function getOutsideMeds($encounterNo)
  {
    $command = \Yii::app()->db->createCommand();

    $command->select("
            IF(
                spms.`strength_disc` = t.`dosage`,
                IFNULL(`t`.drug_code, cppm.`drug_code`),
                ''
            ) AS drug_code,
            med.gen_code,
            t.gen_code as genCode,
            t.order_dt as dateAdded,
            IF(
                spms.`strength_disc` = t.`dosage`,
                med.form_code,
                ''
            ) AS form_code,
            IF(
                spms.`strength_disc` = t.`dosage`,
                med.package_code,
                ''
            ) AS package_code,
            IF(
                spms.`strength_disc` = t.`dosage`,
                med.strength_code,
                ''
            ) AS strength_code,
            IF(
                spms.`strength_disc` = t.`dosage`,
                med.unit_code,
                ''
            ) AS unit_code,
            IF(
                spms.`strength_disc` = t.`dosage`,
                med.salt_code,
                ''
            ) AS salt_code,
            t.price as totalPrice,
            t.quantity,
            t.route,
            t.brand_name AS generic,
            t.frequency,
            IF(
                t.`dosage`,
                CONCAT(
                    IF(
                        cppm.`generic` = '',
                        t.`brand_name`,
                        IFNULL(med.`description`,cppm.`generic`)
                    ),
                    ' / ',
                    t.`dosage`
                ),
                IF(
                    cppm.`generic` = '',
                    t.`brand_name`,
                    IFNULL(med.`description`,cppm.`generic`)
                )
            ) AS new_generic
        ");

    $command->from('care_pharma_outside_order t');
    $command->where("t.encounter_nr=:encounter AND t.is_deleted = 0");
    $command->leftJoin('care_pharma_products_main cppm', 'cppm.bestellnum = t.gen_code AND cppm.prod_class = :medicine');
    $command->leftJoin('seg_phil_medicine med', '(med.drug_code = t.drug_code OR cppm.drug_code = med.drug_code)');
    $command->leftJoin('seg_phil_medicine_strength spms', 'med.strength_code = spms.strength_code');

    $command->params = array(
      ':encounter' => $encounterNo,
      ':medicine' => self::MEDICINE_TYPE,
    );

    $result = $command->queryAll();
    return $result;
  }

  public function getMedicines($encounterNo)
  {

    $orders = $this->getMedsOrder($encounterNo);

    $outsideMeds = $this->getOutsideMeds($encounterNo);

    $data = \CMap::mergeArray($orders, $outsideMeds);

    return $data;
  }


  public function getPatientGender()
  {
    $command = \Yii::app()->db->createCommand();

    $command->select("t.sex");

    $command->params = array(
      ':pid' => $this->encounter->person->pid,
    );

    $command->from('care_person t');
    $command->where('t.pid = :pid');
    $result = $command->queryRow();

    return $result['sex'];
  }


  public function getParentEncounter($parentEncounter)
  {
    $command = \Yii::app()->db->createCommand();

    $command->select('t.encounter_nr');
    $command->from('care_encounter t');
    $command->leftJoin('seg_billing_encounter bill', 't.encounter_nr = bill.encounter_nr');
    $command->where('t.encounter_nr =:encounter');
    $command->andWhere('bill.is_deleted is NULL OR bill.is_deleted = 0 AND bill.is_final = 0 OR bill.bill_nr is NULL');

    $command->params = array(
      ':encounter' => $parentEncounter,
    );
    $result = $command->queryRow();

    return $result['encounter_nr'];
  }
}
