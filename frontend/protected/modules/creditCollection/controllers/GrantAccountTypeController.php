<?php

class GrantAccountTypeController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	// public $layout='//layouts/column1';

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
		$model=new GrantAccountType;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['GrantAccountType']))
		{

			$model->attributes=$_POST['GrantAccountType'];
			$model->date_created = date("Y-m-d H:i:s");
			$model->created_id = Yii::app()->SESSION['sess_user_name'];
			if ($model->save()) {
				Yii::app()->user->setFlash('success', "Successfully added!");
				$this->redirect(Yii::app()->createUrl('creditCollection/grantAccountType/admin'));
			}
			// else{
			// 	Yii::app()->setFlash('success', "<strong>An error occured.</strong>");
			// }
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

		if(isset($_POST['GrantAccountType']))
		{
			$model->attributes=$_POST['GrantAccountType'];
			$model->date_modified = date("Y-m-d H:i:s");
			$model->modify_id = Yii::app()->SESSION['sess_user_name'];
			if($model->save())
				Yii::app()->user->setFlash('success', "Successfully updated!");
				$this->redirect(Yii::app()->createUrl('creditCollection/grantAccountType/admin'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/*public function actionUpdateTypeName(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = GrantAccountType::model()->findByPk($pk);
		$model->type_name = $value;
		if (!$model->save()) {
			throw new CHttpException(400, 'Type name was not successfully update!');
		}
	}

	public function actionUpdateAltName(){
		$pk = $_POST['pk'];
		$value = $_POST['value'];
		$model = GrantAccountType::model()->findByPk($pk);
		$model->alt_name = $value;
		if (!$model->save()) {
			throw new CHttpException(400, 'Alt name was not successfully update!');
		}
	}*/

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		// $this->loadModel($id)->delete();

		// // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		// if(!isset($_GET['ajax']))
		// 	$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		$model = GrantAccountType::model()->findByPk($id);
		$model->deleted = 1;
		$model->date_modified = date("Y-m-d H:i:s");
		$model->modified_id = Yii::app()->SESSION['sess_user_name'];
		if (!$model->save()) {
			throw new CHttpException(400, 'Type name was not successfully deleted!');
		}
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('GrantAccountType');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new GrantAccountType('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['GrantAccountType']))
			$model->attributes=$_GET['GrantAccountType'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return GrantAccountType the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=GrantAccountType::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param GrantAccountType $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='grant-account-type-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
