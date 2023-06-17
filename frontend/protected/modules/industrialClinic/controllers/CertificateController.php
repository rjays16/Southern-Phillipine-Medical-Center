<?php

class CertificateController extends Controller
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

	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionCreate()
	{
		$pid = $_GET['pid'];

		$person = IndustrialPerson::model()->findByPk($pid);
		$model = new SegIndustrialCertMedLto;
		$model->unsetAttributes();

		$hasCert = $model->findByAttributes(array('encounter_nr' => $_GET['encounter_nr']));

		if($hasCert) {
			$this->redirect(array('view', 'id' => $hasCert->id));
		}
		else {
			if(isset($_POST['SegIndustrialCertMedLto'])) {

				$_POST['SegIndustrialCertMedLto']['encounter_nr'] = $_GET['encounter_nr'];
				$_POST['SegIndustrialCertMedLto']['pid'] = $pid;

				if($_POST['SegIndustrialCertMedLto']['physical_fit'] == 'yes') {
					$_POST['SegIndustrialCertMedLto']['upper_limbs'] = NULL;
					$_POST['SegIndustrialCertMedLto']['lower_limbs'] = NULL;
					$_POST['SegIndustrialCertMedLto']['paralyzed_leg'] = NULL;
					$_POST['SegIndustrialCertMedLto']['paraplegic'] = '0';
				}

				if($_POST['SegIndustrialCertMedLto']['clear_eyesight'] == 'yes') {
					$_POST['SegIndustrialCertMedLto']['eye_defect'] = NULL;
				}

				if($_POST['SegIndustrialCertMedLto']['clear_hearing'] == 'yes') {
					$_POST['SegIndustrialCertMedLto']['hearing_defect'] = NULL;
				}

				$_POST['SegIndustrialCertMedLto']['create_id'] = $_SESSION['sess_user_name'];
				$_POST['SegIndustrialCertMedLto']['create_dt'] = date('Y-m-d H:i:s');
				$model->attributes = $_POST['SegIndustrialCertMedLto'];

				if($model->save()){
					Yii::app()->user->setFlash('success', 'Medical Certificate was successfully created.');
					$this->redirect(array('view', 'id' => $model->id));
				}
				else{
					Yii::app()->user->setFlash('error', 'Failed to create medical certificate.');
				}
			}
		}

		$this->render('create', array(
				'person' => $person,
				'model' => $model,
			));
	}

	public function loadModel($id)
	{
		$medCert = SegIndustrialCertMedLto::model()->findByPk($id);

		if($medCert===null){
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $medCert;
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$person = IndustrialPerson::model()->findByPk($model->pid);

		$doctor = SegIndustrialCertMedLto::getDoctors();

		//CVarDumper::dump($_POST, 10, true);
		if($_POST['SegIndustrialCertMedLto']) {

			if($_POST['SegIndustrialCertMedLto']['physical_fit'] == 'yes') {
				$_POST['SegIndustrialCertMedLto']['upper_limbs'] = NULL;
				$_POST['SegIndustrialCertMedLto']['lower_limbs'] = NULL;
				$_POST['SegIndustrialCertMedLto']['paralyzed_leg'] = NULL;
				$_POST['SegIndustrialCertMedLto']['paraplegic'] = '0';
			}

			if($_POST['SegIndustrialCertMedLto']['clear_eyesight'] == 'yes') {
				$_POST['SegIndustrialCertMedLto']['eye_defect'] = NULL;
			}

			if($_POST['SegIndustrialCertMedLto']['clear_hearing'] == 'yes') {
				$_POST['SegIndustrialCertMedLto']['hearing_defect'] = NULL;
			}

			$_POST['SegIndustrialCertMedLto']['modify_id'] = $_SESSION['sess_user_name'];
			$_POST['SegIndustrialCertMedLto']['modify_dt'] = date('Y-m-d H:i:s');
			$model->attributes = $_POST['SegIndustrialCertMedLto'];

			if($model->save()){
				Yii::app()->user->setFlash('success', 'Medical Certificate was successfully updated.');
			}
			else{
				Yii::app()->user->setFlash('error', 'Failed to update medical certificate.');
			}
		}

		$this->render('view', array(
				'person' => $person,
				'model' => $model,
				'doctor' => $doctor,
			));
	}

	public function actionFindDoctors()
	{
		$sql = "SELECT * FROM (SELECT 
				  fn_get_personellname_lastfirstmi (a.personell_nr) AS doctor_name,
				  a.personell_nr,
				  d.name_formal
				FROM
				  care_personell_assignment AS a,
				  care_personell AS ps,
				  care_person AS p,
				  care_department AS d 
				WHERE a.location_type_nr = 1 
				  AND d.admit_inpatient = 1 
				  AND (ps.short_id LIKE 'D%') 
				  AND a.status NOT IN ('hidden', 'inactive', 'void') 
				  AND ps.status NOT IN ('deleted')
				  AND a.personell_nr = ps.nr 
				  AND ps.pid = p.pid 
				  AND a.location_nr = d.nr) t WHERE t.doctor_name LIKE '%". $_GET['t'] ."%'";

		$doctor = Yii::app()->db->createCommand($sql)->queryAll();

		echo CJSON::encode($doctor);
	}
}