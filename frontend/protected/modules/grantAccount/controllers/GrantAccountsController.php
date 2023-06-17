<?php

class GrantAccountsController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	#public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
			array('bootstrap.filters.BootstrapFilter'),
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
			// array('allow',  // allow all users to perform 'index' and 'view' actions
			// 	'actions'=>array('index','view'),
			// 	'users'=>array('*'),
			// ),
			// array('allow', // allow authenticated user to perform 'create' and 'update' actions
			// 	'actions'=>array('create','update'),
			// 	'users'=>array('@'),
			// ),
			// array('allow', // allow admin user to perform 'admin' and 'delete' actions
			// 	'actions'=>array('admin','delete'),
			// 	'users'=>array('admin'),
			// ),
			// array('deny',  // deny all users
			// 	'users'=>array('*'),
			// ),
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
		$model=new GrantAccounts;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		$models_2 = GrantAccountType::model()->findAll('deleted IN (0)');
		$option_grants = array();
		$grantAcc = CHtml::listData($models_2, 'id', 'alt_name');
		$model->created = date("Y-m-d H:i:s");
		$model->created_by = Yii::app()->SESSION['sess_user_name'];

		if(isset($_POST['GrantAccounts']))
		{
			$model->attributes=$_POST['GrantAccounts'];
			if($model->save())
				Yii::app()->user->setFlash('success', "Successfully added!");
				$this->redirect(Yii::app()->createUrl('grantAccount/grantAccounts/admin'));
		}

		$this->render('create',array(
			'model'=>$model,
			'option_grants'=>$grantAcc,
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

		$models_2 = GrantAccountType::model()->findAll('deleted IN (0)');
		$option_grants = array();
		$grantAcc = CHtml::listData($models_2, 'id', 'alt_name');
		$model->modified = date("Y-m-d H:i:s");
		$model->modified_by = Yii::app()->SESSION['sess_user_name'];

		if(isset($_POST['GrantAccounts']))
		{
			$model->attributes=$_POST['GrantAccounts'];
			if($model->save())
				Yii::app()->user->setFlash('success', "Successfully updated!");
				$this->redirect(Yii::app()->createUrl('grantAccount/grantAccounts/admin'));
		}

		$this->render('update',array(
			'model'=>$model,
			'option_grants'=>$grantAcc,
		));
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
		$model = GrantAccounts::model()->findByPk($id);
		$model->deleted = 1;
		$model->modified = date("Y-m-d H:i:s");
		$model->modified_by = Yii::app()->SESSION['sess_user_name'];
		if (!$model->save()) {
			throw new CHttpException(400, 'Grant account was not successfully deleted!');
		}
	}

	public function actionUpdateName(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = GrantAccounts::model()->findByPk($pk);
		$model->name = $value;
		if (!$model->save()) {
			throw new CHttpException(400, 'Name was not successfully update!');
		}
	}

	public function actionUpdateTitle(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = GrantAccounts::model()->findByPk($pk);
		$model->title = $value;
		if (!$model->save()) {
			throw new CHttpException(400, 'Title was not successfully update!');
		}
	}

	public function actionUpdateAddress(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = GrantAccounts::model()->findByPk($pk);
		$model->address = $value;
		if (!$model->save()) {
			throw new CHttpException(400, 'Address was not successfully update!');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('GrantAccounts');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model = new GrantAccounts('search');
		$model->unsetAttributes();  // clear any default values

		if(isset($_GET['GrantAccounts'])) {
			$model->attributes=$_GET['GrantAccounts'];
			$model->accountTypeName = $_GET['GrantAccounts']['accountTypeName'];
		}

		$this->render('admin',array(
			'model'=>$model,
		));

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return GrantAccounts the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=GrantAccounts::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param GrantAccounts $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='grant-accounts-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
