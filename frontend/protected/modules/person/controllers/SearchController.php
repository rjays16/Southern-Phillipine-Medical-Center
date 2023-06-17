<?php
use \SegHis\modules\person\models\Person;
use SegHis\modules\person\models\Encounter;
use SegHis\modules\socialService\models\CharityGrant;
use SegHis\modules\admission\models\AccommodationType;
use SegHis\modules\phic\models\EncounterInsurance;
use SegHis\modules\socialService\models\PersonCharityGrant;
use SegHis\modules\admission\models\assignment\Ward;
use SegHis\modules\socialService\models\EncounterCharityGrant;

class SearchController extends Controller
{

    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    public function actionIndex()
    {
        $model = new Person('search');
        $this->render('index', array(
            'model' => $model
        ));
    }

    public function actionCaseInformation($hrn, $caseNumber)
    {
        Yii::import('billing.models.HospitalBill');

        /* @var $encounter Encounter */
        $encounter = Encounter::model()->findByPk($caseNumber);

        /* @var $bill HospitalBill */
        $bill = HospitalBill::model()->findByAttributes(array(
            'encounter_nr' => $caseNumber,
            'is_deleted' => null
        ));

        /* @var $memberCategory EncounterMemcategory */
        $memberCategory = EncounterMemcategory::model()->with('memcategory')->findByPk($caseNumber);
        $memberCategoryDesc = $memberCategory->memcategory->memcategory_desc;

        /* @var $person Person */
        $person = Person::model()->findByPk($hrn);;

//        $insurance = PersonCaseInsurance::find($encounter->encounter_nr, $person->pid);

        $discount = CharityGrant::getDiscount($encounter->pid, $encounter->encounter_nr);

        $isCharge = self::isCharge($encounter);
        $isAccomodation = self::isAccomodation($encounter);
        $isDiscounted = self::isDiscounted($hrn);
        $disountID = self::getClassification($caseNumber);
        /* @var $encounterInsurance EncounterInsurance */
        $encounterInsurance = EncounterInsurance::model()->findByAttributes(array('encounter_nr' => $encounter->encounter_nr));

        $insuranceNumber = $encounterInsurance->insuranceInfo->insurance_nr;
        // var_dump($isCharge); die;
        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'pid' => $person->pid,
            'encounter_nr' => $encounter->encounter_nr,
            'encounter_date' => $encounter->encounter_date,
            'phic_nr' => $insuranceNumber ? $insuranceNumber : 'None',
            'mem_category' => $memberCategoryDesc ? $memberCategoryDesc : 'N/A',
            'ordername' => $person ? $person->getFullName() : null,
            'encounter_type' => $encounter->encounter_type,
            'is_maygohome' => ($encounter->is_discharged || $bill->is_final==1 ? 1 : 0),
            'bill_nr' => $bill->bill_nr,
            'hasfinal_bill' => $bill->is_final==1 ? 1 : 0,
            'warningcaption' => ($bill->bill_nr && $bill->is_final  ? 'This patient has a saved billing and already advised to go home...' : ''),
            'encounter_type_show' => $encounter ? $encounter->getEncounterTypeDescription() : 'WALK-IN',
            'orderaddress' => rtrim($person->getFullAddress(), ', ') ,
            'discount' => $discount['percentage'],
            'discountid' => $discount['id'],
            'issc' => $discount['id'] == CharityGrant::DISCOUNT_SENIOR_CITIZEN,
            'sw-class' => $discount['id'] ? $discount['id'] : 'None',
            'iscash0' => $isCharge,
            'iscash1' => !$isCharge,
            'uiDiscount' => $isDiscounted,
            'ward' => $encounter->current_ward_nr,
            'diagnosis' => ($encounter ? utf8_encode($encounter->getDiagnosis($caseNumber)) : ""),
            'date_birth' => $person->date_birth,
            'sex' => $person->sex,
            'civil_status' => $person->civil_status,
            'attending_physician' => $encounter->consulting_dr_nr,
            'classification' => $disountID,
            'accomodation' => $isAccomodation
        ));
    }

    /**
     * @param $encounter Encounter
     * @return bool
     */
    private static function isCharge($encounter)
    {
        $accommodationType = $encounter->ward->accommodationType->accomodation_nr;
        $isChargeToCompany = $encounter ? $encounter->isChargedToCompany() : false;
        return
            $accommodationType == AccommodationType::ACCOMMODATION_TYPE_PAY ||
            $encounter->encounter_type == Encounter::ENCOUNTER_TYPE_DIRECT_INPATIENT || //added by rnel
            $encounter->encounter_type == Encounter::ENCOUNTER_TYPE_ER_INPATIENT || // added by rnel
            $encounter->encounter_type == Encounter::ENCOUNTER_TYPE_DIALYSIS ||
            $isChargeToCompany;
    }

    public function isAccomodation($encounter){

        $model = new Ward();

        $criteria = new CDBCriteria();

        $criteria->with = array(
            'accommodationType'
        );

        $criteria->params = array(':ward' => $encounter->current_ward_nr);

        $criteria->addCondition('t.nr = :ward');

        $data = $model->find($criteria);

        return $data->accomodation_type;

    }

    public function actionCaseNumbers($pid)
    {
        /* @var $person Person */
        $getpdpu = Yii::app()->getRequest()->getParam('pdpup');
        $person = Person::model()->findByPk($pid);
        $encounters = $person->encounters;
        $response = array();
        if(!empty($encounters)) {
            foreach ($encounters as $encounter) {
                if($getpdpu == 1){
                    if($encounter->encounter_type == 3) {
                        $response[] = array(
                            'pid' => $encounter->pid,
                            'encounter_nr' => $encounter->encounter_nr,
                            'encounter_date' => date('F j, Y', strtotime($encounter->encounter_date)),
                            'encounter_type' => Encounter::_getEncounterTypeDescription($encounter->encounter_type),
                            'department'     => $encounter->dept->name_formal,
                        );
                    }
                }else if($encounter->is_discharged == 0){
                    $response[] = array(
                        'pid' => $encounter->pid,
                        'encounter_nr' => $encounter->encounter_nr,
                        'encounter_date' => date('F j, Y', strtotime($encounter->encounter_date)),
                        'encounter_type' => Encounter::_getEncounterTypeDescription($encounter->encounter_type),
                        'department'     => $encounter->dept->name_formal,
                    );
                }

            }
        }

        header('Content-Type: application/json');
        echo CJSON::encode($response);
    }

    public function isDiscounted($pid)
    {
        $model = new PersonCharityGrant();
        $criteria = new CDBCriteria();

        $criteria->params = array(':pid' => $pid);
        $criteria->addCondition('t.pid = :pid');
        $criteria->order = 't.grant_dte DESC';

        $data = $model->find($criteria);
        return $data->discountid;
    }

    public function getClassification($caseNumber)
    {
        $model = new EncounterCharityGrant();
        $criteria = new CDBCriteria();

        $criteria->params = array(':encounter_nr' => $caseNumber);
        $criteria->addCondition('t.encounter_nr = :encounter_nr');

        $data = $model->find($criteria);
        return $data->discountid;
    }

}