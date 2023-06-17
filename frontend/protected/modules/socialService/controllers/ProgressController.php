<?php
use SegHis\modules\person\models\Person;
use SegHis\modules\person\models\Encounter;
use SegHis\modules\admission\models\assignment\Ward;


class ProgressController extends Controller
{
    /**
     * @return array action filters
     */
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
        $model = new PdpuProgressNotes;
        global $db;
        if($_GET['deleted_id'] != null) {
            $progressnotes = PdpuProgressNotes::model()->findByPk($_GET['deleted_id']);
            $encoder = utf8_encode(Yii::app()->SESSION['sess_user_name']);
            $progressnotes->history .= "\nDeleted ".date("Y-m-d H:i:s")." by ".$encoder;
             $progressnotes->modify_id = $encoder;
                $progressnotes->modify_dt = date('Y-m-d H:i:s');
            $progressnotes->is_deleted = 1;

            if($progressnotes->save())
                Yii::app()->user->setFlash('success', 'Progress notes has been successfully deleted');
            else Yii::app()->user->setFlash('error', "There was an error encountered upon deleting patient’s progress notes");

        }

        $model->setAttributes($_GET['PdpuProgressNotes']);
        $this->render('index', array(
            'model' => $model,
        ));

    }

    public function actionCreate()
    {
        $audit = new PdpuProgressNAuditTrail;
        $model = new PdpuProgressNotes;
        $ward = new Ward;
        if(isset($_POST['PdpuProgressNotes'])) {
            $encoder = utf8_encode($_SESSION['sess_login_username']);
            $_POST['PdpuProgressNotes']['create_id'] = $encoder;
            $_POST['PdpuProgressNotes']['history'] = 'Created ' . date('Y-m-d H:i:s') . ' by ' . $encoder . "\n";

            $model->attributes = $_POST['PdpuProgressNotes'];
            
            if($model->save()) {

                $audit->notes_id = $model->notes_id;
                $audit->date_changed = date('Y-m-d H:i:s');
                $audit->action_type = "C";
                $audit->login = $encoder;

                $audit->save();
                Yii::app()->user->setFlash('success', 'Successfully created new progress notes');
                $this->redirect(array('index'));
            }else {
                Yii::app()->user->setFlash('error', 'Error creating patient’s Progress Notes. Please fill-out all mandatory fields');
            }
        }

        $this->render('create', array(
            'model' => $model,
            'ward' => $ward,
        ));
    }

    public function actionView($id)
    {
        $model=$this->loadModel($id);

        $dataModel = Encounter::model()->findAllByAttributes(array('encounter_nr' => $model->encounter_nr));

        foreach($dataModel as $enc){
            $persondata = $enc->person;
            $final_diagnosis = $enc->soadiagnosis->final_diagnosis;
            $ward_id = $enc->current_ward_nr;

            if($enc->admission_dt)
                $date_admission = $enc->admission_dt;
            else $date_admission = $enc->encounter_date;
        }

        $ward = new Ward;
        $model->notes_id = $id;
        $name = '';

        if($persondata){
            $name = $persondata->name_last.", ".$persondata->name_first;
            $name .= ($persondata->name_middle ? " ".substr($persondata->name_middle,0,1)."." : '');
        }

        $patientdata['patientname'] = $name;
        $patientdata['datetime'] = date("Y-m-d h:i A",strtotime($model->progress_date_time));
        $patientdata['date_admission'] = date("Y-m-d h:i A",strtotime($date_admission));
        $patientdata['final_diagnosis'] = $final_diagnosis;
        $patientdata['ward'] = $ward_id;
        
        if(isset($_POST['PdpuProgressNotes'])){
            $notes_id = $_POST['PdpuProgressNotes']['notes_id'];
            $informant = $_POST['PdpuProgressNotes']['informant'];
            $venue = $_POST['PdpuProgressNotes']['venue'];
            $purpose_reasons = $_POST['PdpuProgressNotes']['purpose_reasons'];
            $action_taken = $_POST['PdpuProgressNotes']['action_taken'];
            $problem_encountered = $_POST['PdpuProgressNotes']['problem_encountered'];
            $plan = $_POST['PdpuProgressNotes']['plan'];

            $audit = new PdpuProgressNAuditTrail;
            $audit->notes_id = $notes_id;
            $audit->date_changed = date('Y-m-d H:i:s');
            $audit->action_type = 'M';
            $encoder = utf8_encode($_SESSION['sess_login_username']);
            $audit->login = $encoder;

            if($audit->save()){
                $progressnotes = PdpuProgressNotes::model()->findByPk($notes_id);
                $progressnotes->informant = $informant;
                $progressnotes->venue = $venue;
                $progressnotes->purpose_reasons = $purpose_reasons;
                $progressnotes->action_taken = $action_taken;
                $progressnotes->problem_encountered = $problem_encountered;
                $progressnotes->plan = $plan;
                $progressnotes->modify_id = $encoder;
                $progressnotes->modify_dt = date('Y-m-d H:i:s');
                $progressnotes->history .= "\nUpdated ".date("Y-m-d H:i:s")." by ".$encoder;
            
                if($progressnotes->save())
                    Yii::app()->user->setFlash('success', 'Successfully updated patient\'s progress notes form.');
                else Yii::app()->user->setFlash('error', "There was an error encountered upon updating patient’s progress notes");
            }else{
                Yii::app()->user->setFlash('error', "There was an error encountered upon updating patient’s progress notes");
            }

            $model = $progressnotes;

        }

        $this->render('view', array(
            'model' => $model,
            'encmodel' => $encmodel,
            'patientdata' => $patientdata,
            'ward' => $ward
        ));
    }

    public function actionAudit_trail() {
        $id = $_GET['id'];
        $audit = new PdpuProgressNAuditTrail;
        $audit->setAttributes(array('notes_id'=>$id));
        $this->render('audit_trail', array(
            'model' => $audit,
        ));
    }

    public function loadModel($id) {
        $model = PdpuProgressNotes::model()->findByPk($id);
        if($model === null)
            throw new CHttpException(404, 'The requested page does not exist');
        return $model;
    }

    public function actionViewPatientEncounter() {
        $model = new PdpuProgressNotes;

        $this->render('viewPatientEncounter', array(
            'model' => $model,
        ));
    }

    public function actionCaseInformation($id){

        $model = PdpuProgressNotes::model()->findByPk($id);
        $dataModel = Encounter::model()->findAllByAttributes(array('encounter_nr' => $model->encounter_nr));
        foreach($dataModel as $enc){
            $final_diagnosis = $enc->soadiagnosis->final_diagnosis;
        }

        header('Content-Type: application/json');
        echo CJSON::encode(array(
            'notes_id' => $model->notes_id,
            'pid' => $model->pid,
            'encounter_nr' => $model->encounter_nr,
            'progress_date_time' => $model->progress_date_time,
            /*'name' => $model->name,
            'ward' => $model->ward,
            'date_admission' => $model->date_admission,*/
            'final_diagnosis' => $final_diagnosis,
            'informant' => $model->informant,
            'purpose_reasons' => $model->purpose_reasons,
            'action_taken' => $model->action_taken,
            'problem_encountered' => $model->problem_encountered,
            'plan' => $model->plan

        ));

    }

}