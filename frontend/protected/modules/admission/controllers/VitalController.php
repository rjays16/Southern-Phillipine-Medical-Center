<?php
Yii::import('admission.models.vital.*');
class VitalController extends Controller
{
    public $layout = '/layouts/main';

    public function actionIndex()
    {
        $VitalSignModel = EncounterVitalSign::model()->getVitalSigns($_GET);
        $VitalSignBMIModel = EncounterVitalSignBmi::model()->getVitalSignsBMI($_GET);

        $dataProviderOrder =  new CArrayDataProvider(
            $VitalSignModel,
            array(
                'pagination' => array(
                    'pageSize' => 5
                ),
                'keyField' => false
            )
        );

        $dataProviderBMI =  new CArrayDataProvider(
            $VitalSignBMIModel,
            array(
                'pagination' => array(
                    'pageSize' => 5
                ),
                'keyField' => false
            )
        );


        $this->render(
            'index', array(
                'vitalList'       => $dataProviderOrder,
                'encounter_nr'    => $_GET['encounter_nr'],
                'pid'             => $_GET['pid'],
                'bmiList'         => $dataProviderBMI,
            )
        );
    }


    public function actionSave(){

        include _DIR_ . './../include/care_api_classes/ehrhisservice/Ehr.php';
//        CVarDumper::dump( _DIR_ . '/../../../../../..' . '/include/care_api_classes/ehrhisservice/Ehr.php',10,true);
//        CVarDumper::dump( _DIR_ ,10,true);
        $ehr = Ehr::instance();


        /*
     * patient_add_vital_sign => [
     *      spin => String,
     *      ref_id => String mysql UUID,
     *      encounter_nr => String ,
     *      create_dt => timestamp,
     *      PatientPreassessment => [
     *          vital_date => '2019-03-22 10:03 AM'
     *          vital_temperature => 3
                vital_pulserate => 3
                vital_respiratory => 3
                vital_systolic => 3
                vital_diastolic => 3
                vital_oxysat => 3
     *      ]
     * ]
     * */
        $transaction = Yii::app()->getDb()->beginTransaction();
//
        $uuid =  Yii::app()->db->createCommand('Select UUID()')->queryScalar();
        $EncounterVitalSign = new EncounterVitalSign();
        $EncounterVitalSign->uuid = $uuid;
        $EncounterVitalSign->encounter_nr = $_REQUEST['encounter_nr'];
        $EncounterVitalSign->systolic = $_REQUEST['ststolic'];
        $EncounterVitalSign->diastolic =  $_REQUEST['diastolic'];
        $EncounterVitalSign->pulse_rate=  $_REQUEST['pulse_rate'];
        $EncounterVitalSign->temperature=  $_REQUEST['temperature'];
        $EncounterVitalSign->respiratory= $_REQUEST['respiratory'];
        $EncounterVitalSign->date_monitor = $_REQUEST['textDate']." ".$_REQUEST['textTime'];
        $EncounterVitalSign->create_dt = date('Y-m-d H:m:n');
        if($EncounterVitalSign->save()){
            $response = $ehr->patient_addvitalsign(array(
                'patient_add_vital_sign' => array(
                    'spin' => $_REQUEST['pid'],
                    'ref_id' => $uuid,
                    'encounter_nr' => $EncounterVitalSign->encounter_nr,
                    'create_dt' => $EncounterVitalSign->create_dt,
                    'PatientPreassessment' => array(
                        'vital_date' => $_REQUEST['textDate']." ".$_REQUEST['textTime'],
                        'vital_temperature'=>$_REQUEST['temperature'],
                        'vital_pulserate'=>$_REQUEST['pulse_rate'],
                        'vital_respiratory'=> $_REQUEST['respiratory'],
                        'vital_systolic'=>$_REQUEST['ststolic'],
                        'vital_diastolic' => $_REQUEST['diastolic'],
                        'vital_oxysat' => '0',
                    )
                )
            ));

//          var_dump($ehr->getResponseData());
            if(!$response->saved || $response === false){
                $transaction->rollBack();
                $msg = "Failed to Saved"; // append message here from here $EncounterVitalSign->getErrors()
            }else{
                $transaction->commit();
                $msg = "Successfully Saved";
            }
        }else{
            $msg = "Failed to Saved"; // append message here from here $EncounterVitalSign->getErrors()
            $transaction->rollBack();
        }
        echo CJSON::encode(array('msg'=>$msg));

    }

    public function actionDelete(){
        $transaction = Yii::app()->getDb()->beginTransaction();
        $model = EncounterVitalSign::model()->find(
            'uuid = :id', array('id' => $_REQUEST['id'])
        );
        $model->is_deleted = 1;
        $model->modify_dt = date('Y-m-d H:m:s');
        if ($model->save()) {
            include _DIR_
                . './../include/care_api_classes/ehrhisservice/Ehr.php';
            $ehr = Ehr::instance();

            $response = $ehr->patient_removevitalsign(
                array(
                    'patient_remove_vital_sign ' => array(
                        'ref_id'    => $_REQUEST['id'],
                        'modify_dt' => $model->modify_dt,
                    )
                )
            );
            if (!$response->saved || $response === false) {
                var_dump($ehr->getResponseData());
                $transaction->rollBack();
                $msg = "Failed to delete";
            } else {
                $transaction->commit();
                $msg = "Success to delete";
            }
        } else {
            $transaction->rollBack();
            $msg = "Failed to delete";
        }
        echo CJSON::encode(array('msg' => $msg));
    }

    public function actionSaveBmi()
    {
        $data = json_decode($_REQUEST['details'], true);

        $id =  Yii::app()->db->createCommand('Select UUID()')->queryScalar();

        $user = Yii::app()->user->id;
        $dt = date('Y-m-d H:i:s', strtotime($data['bmi_date']. ' '. $data['bmi_time']));

        $model = new EncounterVitalSignBmi();

        $model->id = $id;
        $model->pid = $data['pid'];
        $model->encounter_nr = $data['encounter_nr'];
        $model->bmi_date = $dt;
        $model->weight = $data['weight'];
        $model->height = $data['height'];
        $model->hip_line = $data['hip_line'];
        $model->waist_line = $data['waist_line'];
        $model->abdominal_girth = $data['abdominal_girth'];
        $model->history = 'Created By: User ID(' . $user
            . ') - ' . date('Y-m-d H:i:s');
        $model->create_dt = date('Y-m-d H:i:s');

        if (!$model->save()) {
            echo CJSON::encode(array('msg' => $model->getErrors()));
        } else {
            echo CJSON::encode(array('msg' => 'Successfully Added', 'code' => 200));
        }
    }

    public function actionDeleteBmi()
    {
        $id = $_REQUEST['id'];

        $model = EncounterVitalSignBmi::model()->findByPk($id);
        $model_all = EncounterVitalSignBmi::model()->findAllByAttributes(
            array(
                'encounter_nr' => $model->encounter_nr,
                'is_deleted'   => 0
            )
        );

        $model_notes = CareEncounterNotes::model()->findByAttributes(
            array(
                'encounter_nr' => $model->encounter_nr,
                'is_deleted'   => 0
            )
        );

        $user = Yii::app()->user->id;

        $bmi_count = true;
        if (count($model_all) <= 1) {
            $bmi_count = false;
        }

        if(!$model_notes || $bmi_count) {
            if ($model) {
                $model->is_deleted = 1;
                $model->history = 'Deleted By: User ID (' . $user . ') - '
                    . date('Y-m-d H:i:s') . "\n" . $model->history;
            }

            if (!$model->save()) {
                echo CJSON::encode(array('msg' => $model->getErrors()));
            } else {
                echo CJSON::encode(array('msg' => 'Successfully Delete!', 'code' => 200));
            }
        } else {
            echo CJSON::encode(array('msg' => 'Unable to delete. Diet is already encoded!', 'code' => 201));
        }

    }
}