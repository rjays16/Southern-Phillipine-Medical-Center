<?php

use SegHis\modules\person\models\Person;
use \SegHis\models\HospitalInfo;
use SegHis\modules\onlineConsult\services\PersonService;
use SegHis\modules\onlineConsult\services\ConsultationService;

class OnlineController extends Controller
{
    /**
     * @return array action filters
     */
    public $defaultController = 'online';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            //'postOnly + delete', // we only allow deletion via POST request
            array('bootstrap.filters.BootstrapFilter'),
        );
    }

    public function accessRules()
    {
        return array();
    }

    public function actionIndex()
    {
        $model = ConsultRequest::model()->getOnlineRequest();

        global $db;

        $dataProviderOnline = new CArrayDataProvider(
            $model,
            array(
                'pagination' => array(
                    'pageSize' => 15,
                ),
                'keyField'   => false
            )
        );

        if (($_GET['ajax'] == 'person-list-grid')) {
            $name = explode(",", $_GET['searchName']);

            $this->widget('onlineConsult.widgets.SearchPersonList', array(
                'last_name'  => $name[0],
                'first_name' => $name[1],
                'consult_id' => $_GET['consult_id'],
                'search'     => $_GET['search'],
                'id'         => $_REQUEST['id']

            ));

        } else {
            $this->render('index', array(
                    'model'              => $model,
                    'dataProviderOnline' => $dataProviderOnline
                )
            );
        }
    }

    public function actionView_history($pid, $id)
    {
        $modelDepartment = new CareDepartment;
        $model           = new CarePerson;
        $personInfo      = CarePerson::model()->findByPk($pid);
        $model           = new Encounter;
        $departmentlist  = $modelDepartment->getAllOPDepartment();
        $consult_id      = $id;

        $patientHistory = CareEncounter::model()->getTransactionHistory($pid);

        $departments = CHtml::listData(
            $departmentlist,
            'nr',
            'name_formal'
        );

        $citizenship      = Country::model()->findByPk($personInfo->citizenship)->country_name;
        $getRequest = ConsultRequest::model()->findByPk($consult_id);

        $this->render('view_history', array(
                'personInfo'      => $personInfo,
                'model'           => $model,
                'consultId'       => $consult_id,
                'departmentlist'  => $departments,
                'patientHistory'  => $patientHistory,
                'chief_complaint' => $getRequest->chief_complaint,
                'citizenship'=> $citizenship,
                'is_assign'=> $getRequest->pid,
                'is_allowed_cancel'=> $getRequest->is_allowed_cancel

            )
        );
    }

    public function actionView()
    {

        $models = new ConsultRequest;
        $pModel = new CarePerson();

        $model = $models->getConsultInfromation();

        $arrPerson = array(
            'name_last'   => $model->name_last,
            'name_first'  => $model->name_first,
            'name_middle' => $model->name_middle
        );

        $results = $pModel->getPersonDetails($arrPerson);

        $dataProvider = new CArrayDataProvider(
            $results,
            array(
                'pagination' => false,
                'keyField'   => false
            )
        );

        $this->render('view_consult', array(
                'results' => $dataProvider,
                'id'      => $_REQUEST['id']
            )
        );
    }

    public function actionConsultation()
    {
        $modelDepartment = new CareDepartment;
        $departmentlist  = $modelDepartment->getAllOPDepartment();


        $this->render('consultation', array(
                'modelDepartment' => $modelDepartment,
                'pid'             => $_GET['pid'],
                'departmentlist'  => $departmentlist
            )
        );
    }

    public function actionOpenConsultationModal($encounter_nr=''){
        $encDetails = CareEncounter::model()->findByPk($encounter_nr);

        $doctorslist = $this->actionDoctorList($encDetails->consulting_dept_nr,1);

        $encDetails->encounter_date = date('Y-m-d h:i A', strtotime($encDetails->encounter_date));
        
        $request = ConsultMeeting::model()->findAllByAttributes(array('encounter_nr' => $encounter_nr));

        echo CJSON::encode(array(
            'details'       => $encDetails,
            'doctorslist'   => $doctorslist,
            'consultId'     => $request->consult_id,
            'success'       => (($encDetails && count($encDetails)) < 1 ? false : true)
        ));
    }

    public function actionDoctorList($dept_nr,$update=0)
    {
        $modelDepartment = new CareDepartment;

        $add_where = " OR pa.location_nr IN ((SELECT 
                        nr 
                      FROM
                        care_department 
                      WHERE parent_dept_nr = " . $dept_nr . ")) OR pa.location_nr = (SELECT parent_dept_nr FROM care_department WHERE nr = " . $dept_nr . ")";

        $query = " SELECT 
          pa.personell_nr,
          pa.location_nr,
          p.name_last,
          p.name_middle,
          IF(p.name_middle IS NOT NULL, CONCAT(SUBSTRING(UPPER(p.name_middle),1,1),'.'), '') mid_initial,
          p.name_first,
          p.name_2,
          IF(
            (SELECT 
              doctor_id 
            FROM
              seg_doctor_meeting 
            WHERE doctor_id = pr.`nr`) IS NOT NULL,
            1,
            0
          ) haswebexid
        FROM
          care_personell_assignment AS pa,
          care_personell AS pr,
          care_person AS p,
          care_department AS d
        WHERE pa.personell_nr = pr.nr 
          AND pa.location_nr = d.nr 
          AND pr.pid = p.pid 
          AND pr.short_id LIKE 'D%' 
          AND pa.location_type_nr = 1 
          AND (
            pa.date_end = '0000-00-00' 
            OR pa.date_end >= '2020-05-29'
          ) 
          AND pa.status NOT IN (
            'deleted',
            'hidden',
            'inactive',
            'void'
          ) 
          AND d.admit_outpatient = 1 
          AND (
            pa.location_nr = " . $dept_nr . "
          $add_where
          ) 
        ORDER BY p.name_last ";

        $command = Yii::app()->db->createCommand($query);
        $results = $command->queryAll();

        if(!$update){
            echo CJSON::encode(array(
                'status'  => true,
                'results' => $results,
                'code'    => 200
            ));
        }else return $results;
    }


    public function actionCreatePatient()
    {
      
        $consultData   = ConsultRequest::model()->findByPk($_REQUEST['id']);
        $model         = new CarePerson;
        $modelReligion = new Religion;
        $modelRegion = new Regions;
        $modelProvince = new Provinces;
        $modelMunicipality = new Municity;
        $modelBarangay = new Barangays;
        $modelOccupation = new Occupation;
        $modelCountry = new Country;

        $religionlist  = $modelReligion->getAllReligion();

        $religions = CHtml::listData(
            $religionlist,
            'religion_nr',
            'religion_name'
        );

        $occupationlist  = $modelOccupation->getAllOccupation();

        $occupation = CHtml::listData(
            $occupationlist,
            'occupation_nr',
            'occupation_name'
        );




        $hospinfo = HospitalInfo::model()->find();

        $barangayList = Barangays::model()->findAllByAttributes( array(
                                                                'mun_nr'=>$hospinfo->default_city
                                                                ), 
                                                                array('order' => 'brgy_name ASC')
                                                            );

        $barangays = CHtml::listData(
             $barangayList,
            'brgy_nr',
            'brgy_name'
        );


        $countrylist  = $modelCountry->getAllCountry();
        $country = CHtml::listData(
            $countrylist,
            'country_code',
            'country_name'
        );


        $municitydata = Municity::model()->findByPk($hospinfo->default_city);
    
        $municitylist  = Municity::model()->findAllByAttributes( array(
                                                                'prov_nr'=>$municitydata->provNr->prov_nr
                                                                ), 
                                                                array('order' => 'mun_name ASC')
                                                             );

        $municities = CHtml::listData(
             $municitylist,
            'mun_nr',
            'mun_name'
        );


        $provinceslist  = Provinces::model()->findAllByAttributes( array(
                                                                'region_nr' =>$municitydata->provNr->region_nr
                                                                ),
                                                                array('order' => 'prov_name ASC')
                                                             );
        $provinces = CHtml::listData(
             $provinceslist,
            'prov_nr',
            'prov_name'
        );



        $regionlist = $modelRegion->getAllRegion();
        $regions = CHtml::listData(
             $regionlist,
            'region_nr',
            'region_name'
        );

        $this->render('create_patient',
            array(
                'consultData' => $consultData,
                'religions'   => $religions,
                'model'       => $model,
                'consult_id'  => $_REQUEST['id'],
                'defregion'   => $municitydata->provNr->region_nr,
                'regions'     => $regions,
                'provinces'   => $provinces,
                'municities'  => $municities,
                'barangays'   => $barangays,
                'occupation'  => $occupation,
                'citizenship'     => $country
            )
        );

    }

    public function actionSaveNewPatient()
    {
        $personService = new PersonService();

        $request     = $_POST;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $success = $personService->createPerson($request);
            $transaction->commit();

            echo \CJSON::encode($success);
        } catch (Exception $e) {
            $transaction->rollBack();
            echo \CJSON::encode(array('errors' => $e->getMessage()));
        }
    }

    public function actionUpdatePatient()
    {
        $personService = new PersonService();

        $request = $_POST;
        // \CVarDumper::dump($request,10,true);die();
        $transaction = Yii::app()->db->beginTransaction();

        try {
            $success = $personService->updatePerson($request);
            if($success){
                $success = $personService->updatePersonEHR($request);
            }
            $transaction->commit();

            echo \CJSON::encode($success);
        } catch (Exception $e) {
            $transaction->rollBack();
            echo \CJSON::encode(array('errors' => $e->getMessage()));
        }
    }

    public function actionUpdateInfoPatient()
    {
        $model         = new CarePerson;
        $modelReligion = new Religion;
        $modelRegion = new Regions;
        $modelProvince = new Provinces;
        $modelMunicipality = new Municity;
        $modelBarangay = new Barangays;
        $modelCountry = new Country;
        $modelOccupation = new Occupation;

        $personInfo    = CarePerson::model()->findByPk($_REQUEST['pid']);
        $religionInfo  = Religion::model()->findByPk($personInfo['religion']);
        
        $religionlist  = $modelReligion->getAllReligion();
        $religions = CHtml::listData(
            $religionlist,
            'religion_nr',
            'religion_name');
        
        // \CVarDumper::dump($religionInfo,10,true);die();
        /* Municipality */
        $mun_nr = $personInfo->mun_nr;

        $xmunicipality = Municity::model()->findByPk($mun_nr);

        $xregion = $xmunicipality->provNr->regionNr;
        $xprovince = $xmunicipality->provNr;
        $xbarangay = $xmunicipality->barangays;
        /* Region */
        $regionList = $modelRegion->getAllRegion();
        $regions = CHtml::listData(
             $regionList,
            'region_nr',
            'region_name'
        );

        /* Province */
        $provinceList = $modelProvince->getAllProvinces();
        $provinces = CHtml::listData(
             $provinceList,
            'prov_nr',
            'prov_name'
        );

        /* Municity */
        $municitylist  = Municity::model()->findAllByAttributes(array('prov_nr'=>$xprovince->prov_nr));
        $municities = CHtml::listData(
             $municitylist,
            'mun_nr',
            'mun_name'
        );


        /* Barangay */
       $barangays = CHtml::listData(
             $xbarangay,
            'brgy_nr',
            'brgy_name'
        );

        $countrylist = $modelCountry->getAllCountry();
         $country = CHtml::listData(
            $countrylist,
            'country_code',
            'country_name');

        $occupationlist  = $modelOccupation->getAllOccupation();

        $occupation = CHtml::listData(
            $occupationlist,
            'occupation_nr',
            'occupation_name'
        );

        $this->render('update_patient', array(
                'personInfo'   => $personInfo,
                'model'        => $model,
                'religionInfo' => $religionInfo,
                'religionlist' => $religions,
                'consult_id'   => $_REQUEST['consultId'],
                'regions'      => $regions,
                'provinces'    => $provinces,
                'municities'   => $municities,
                'barangays'    => $barangays,
                'xregion'      => $xregion,
                'xprovince'    => $xprovince,
                'citizenship'  => $country,
                'occupation'   => $occupation
            )
        );
    }

    public function actionProvinceList($region_nr){

        $provinceslist  = Provinces::model()->findAllByAttributes( array(
                                                                'region_nr' =>$region_nr
                                                                )
                                                             );
        echo CJSON::encode(array(
            'status'  => true,
            'results' => $provinceslist,
            'code'    => 200
        ));
    }

    public function actionMunicipalityList($prov_nr){

        $municitylist  = Municity::model()->findAllByAttributes( array(
                                                                'prov_nr'=>$prov_nr
                                                                )
                                                             );

        echo CJSON::encode(array(
            'status'  => true,
            'results' => $municitylist,
            'code'    => 200
        ));
    }

    public function actionBarangayList($mun_nr){

        $barangayList  = Barangays::model()->findAllByAttributes( array(
                                                                'mun_nr'=>$mun_nr
                                                                )
                                                             );

        echo CJSON::encode(array(
            'status'  => true,
            'results' => $barangayList,
            'code'    => 200
        ));
    }

    public function actionAssignPatient(){

        $personService = new PersonService();
        $request     = $_POST;
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $success = $personService->assignPersonHRN($request);
            $transaction->commit();
            echo \CJSON::encode($success);
        } catch (Exception $e) {
            $transaction->rollBack();
            echo \CJSON::encode(array('errors' => $e->getMessage()));
        }

    }

     public function actionIsHRN($consult_id)
    {
        $modelDepartment = new CareDepartment;

    
        $query = "SELECT pid FROM seg_consult_request WHERE consult_id='".$consult_id."' AND (pid IS NOT NULL AND pid!='')";

        $command = Yii::app()->db->createCommand($query);
        $results = $command->queryScalar();
       
        echo CJSON::encode(array(
                'status'  => true,
                'pid' => $results,
                'code'    => 200
            ));
    }

    public function actionUpdateConsultationStatus($consult_id,$dept_nr,$consult_dr_nr,$status)
    {
        $query = "UPDATE seg_consult_request SET request_status='".$status."', dept_nr='".$dept_nr."',doctor_id='".$consult_dr_nr."' WHERE consult_id='".$consult_id."'";
        $command = Yii::app()->db->createCommand($query);
        $results = $command->execute();
        echo CJSON::encode(array(
            'status'  => true,
            'code'    => 200
        ));
    }

    /***
     * 
     */
    public function actionUpdatePersonnelChatId()
    {
        $service = new ConsultationService();
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $request     = $_POST;
            extract($request);
            $service->updatePersonnelChatId($personnel_id, $fb_userid);
            $transaction->commit();
            echo \CJSON::encode(
                array('success' => true)
            ); 
        } catch (Exception $e) {
            $transaction->rollBack();
            echo \CJSON::encode(array(
                'success' => false,
                'errors' => $e->getMessage()
            ));
        }
    }
}
