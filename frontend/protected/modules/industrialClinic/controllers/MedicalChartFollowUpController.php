<?php

class MedicalChartFollowUpController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			array('bootstrap.filters.BootstrapFilter')
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new MedicalChartFollowUp;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['MedicalChartFollowUp']))
		{
			$model->attributes=$_POST['MedicalChartFollowUp'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['MedicalChartFollowUp']))
		{
			$model->attributes=$_POST['MedicalChartFollowUp'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionUpdateDateRequest(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = MedicalChartFollowUp::model()->findByPk($pk);
		$model->date_request = date('Y-m-d',strtotime($value));
		if(!$model->save()){
			throw new CHttpException(400, 'Date request was not sucessfully updated ...');
		}
	}

	public function actionUpdateVitalSign(){
		$pk = $_POST['pk'];
        $value = $_POST['value'];
        $model = MedicalChartFollowUp::model()->findByPk($pk);
        $model->vshtwt = $value;
        if(!$model->save()){
            throw new CHttpException(400, 'VS/HT/WT was not sucessfully updated ...');
        }
	}

	public function actionUpdateHxpe(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = MedicalChartFollowUp::model()->findByPk($pk);
		$model->hxpe = $value;
		if(!$model->save()){
			throw new CHttpException(400, 'HX/PE was not sucessfully updated ...');
		}
	}

	public function actionUpdateRemarks(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = MedicalChartFollowUp::model()->findByPk($pk);
		$model->remarks = $value;
		if(!$model->save()){
			throw new CHttpException(400, 'Remarks was not sucessfully updated ...');
		}
	}



	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// $this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		// if(!isset($_GET['ajax']))
		// 	$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		// $pk = $_POST['pk'];
		// $value = $_POST['value'];
		$model = MedicalChartFollowUp::model()->findByPk($id);
		$model->is_deleted = 1;
		if(!$model->save()){
			throw new CHttpException(400, 'Successfully deleted!');
		}
	}

	/**
	 * Lists all models.
	 * @param $encounterNr
	 */
	public function actionIndex($caseNr)
	{
		if($_POST['MedicalChartFollowUp']){
			$model = new MedicalChartFollowUp();
			$model->attributes = $_POST['MedicalChartFollowUp'];
			$model->date_request = date('Y-m-d',strtotime($_POST['MedicalChartFollowUp']['date_request']));
			$model->encounter_nr = $caseNr;

			$this->performAjaxValidation($model);
			if($model->save()){
				Yii::app()->user->setFlash('success',"<strong>Successfully saved.</strong>");
				$this->redirect(Yii::app()->createUrl('industrialClinic/medicalChartFollowUp/index/caseNr/'.$model->encounter_nr));
			}else{
				if ($model->attributes['date_request']=="") {
					Yii::app()->user->setFlash('error',"<strong>Date Request</strong>");
				}else if ($model->attributes['vshtwt']=="") {
					Yii::app()->user->setFlash('error',"<strong>VS/HT/WT</strong>");
				}else if ($model->attributes['hxpe']=="") {
					Yii::app()->user->setFlash('error',"<strong>HX/PE</strong>");
				}else if ($model->attributes['remarks']=="") {
					Yii::app()->user->setFlash('error',"<strong>Remarks</strong>");
				}else{
					Yii::app()->user->setFlash('error',"<strong>An error occured.</strong>");
				}
			}
		}

		$model = new MedicalChartFollowUp('search');
		$model->encounter_nr = $caseNr;

		$this->render('index',array(
			'model' => $model,
			'dataProvider'=>$model->search(),
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new MedicalChartFollowUp('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['MedicalChartFollowUp']))
			$model->attributes=$_GET['MedicalChartFollowUp'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return MedicalChartFollowUp the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=MedicalChartFollowUp::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param MedicalChartFollowUp $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='medical-follow-up-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
