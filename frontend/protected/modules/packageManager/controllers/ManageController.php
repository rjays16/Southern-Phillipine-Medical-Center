<?php

class ManageController extends Controller
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

	public function actionIndex()
	{
		$model = new Packages;
		$model->unsetAttributes();
		$data = Packages::model()->search();

		if(isset($_GET['Packages']))
			$model->attributes = $_GET['Packages'];

		if(isset($_POST['Packages'])){
			$_POST['Packages']['is_deleted'] = 0;
			$_POST['Packages']['is_dialysis'] = 0;
			$_POST['Packages']['create_id'] =  $_SESSION['sess_login_username'];
			$_POST['Packages']['create_time'] = date('Y-m-d H:i:s');
			$_POST['Packages']['history'] = 'Created ' . date('Y-m-d H:i:s') . ' | ' . $_SESSION['sess_login_username'] . "\n";
			$model->attributes = $_POST['Packages'];

			if($model->save()){
				Yii::app()->user->setFlash('success', 'Package <strong>' . $_POST['Packages']['package_name'] . '</strong> was successfully added.');
			}
			else{
				Yii::app()->user->setFlash('error', 'Package was not successfully added.');
			}
		}

		$this->render('index',
						array(
							'model' => $model,
							'data' => $data,
						));
	}

	public function loadModel($id)
	{
		$package = Packages::model()->findByPk($id);

		if($package===null){
			throw new CHttpException(404, 'The requested page does not exist.');
		}

		return $package;
	}

	public function actionView($id)
	{
		$totalCash = 0;
		$totalCharge = 0;
		$detailsModel = PackageDetails::model()->getPackageDetailsById($id);
		$model = $this->loadModel($id);
		$dt_model = new PackageDetails();
		$checker = true;

		foreach($detailsModel as $key => $value) {
			$totalCharge += ($value->price_charge * $value->quantity);
			$totalCash += ($value->price_cash * $value->quantity);
		}

		if(!empty($_POST['serv_code'])){
			$serv_code = $_POST['serv_code'];
			for($i = 0; $i < count($serv_code); $i++){
				$p_model = $this->loadModel($id);
				$d_model = new PackageDetails();
				$d_model->package_id = $id;
				$d_model->item_code = $serv_code[$i];
				$d_model->item_name = $_POST['serv_desc'][$i];
				$d_model->item_purpose = $_POST['serv_loc'][$i];
				$d_model->quantity = $_POST['serv_qty'][$i];
				$d_model->price_cash = str_replace(',','',$_POST['serv_cash'][$i]);
				$d_model->price_charge = str_replace(',','',$_POST['serv_charge'][$i]);
				$p_model->modify_id = $_SESSION['sess_login_username'];
				$p_model->modify_time = date('Y-m-d H:i:s');
				$d_model->is_fs = $_POST['is_fs'][$i];
				$p_model->history .= 'Added Item['.$serv_code[$i].'][Qty: '.$_POST['serv_qty'][$i].'] ' . date('Y-m-d H:i:s') . ' | ' . $_SESSION['sess_login_username'] . "\n";

				if(!$d_model->save()){
					$d_model->getErrors();
					$checker = false;
				}

				if(!$p_model->save()){
					$p_model->getErrors();
					$checker = false;
				}
			}

			if(!$checker){
				Yii::app()->user->setFlash('error', '<strong>Error!</strong> Items was not added for this package.');
			}
			else{
				Yii::app()->user->setFlash('success', 'Items was successfully added to this package.');
				$this->redirect(array('view', 'id' => $id));
			}
		}

		if($_POST['items']){
			foreach($_POST['items'] as $key => $item){

				$d_model = PackageDetails::model()->findByPk($key);
				$d_model->quantity = $item['quantity'];
				$d_model->price_cash = str_replace(',','',$item['price_cash']);
				$d_model->price_charge = str_replace(',','',$item['price_charge']);
				$p_model->modify_id = $_SESSION['sess_login_username'];
				$p_model->modify_time = date('Y-m-d H:i:s');

				if(!$d_model->save()){
					$d_model->getErrors();
					$checker = false;
				}
			}

			if(!$checker){
				Yii::app()->user->setFlash('error', '<strong>Error!</strong> Items was not updated for this package.');
			}
			else{
				Yii::app()->user->setFlash('success', 'Item/s was successfully updated to this package.');
				$this->redirect(array('view', 'id' => $id));
			}
		}

		if($_POST['Packages']){
			$_POST['Packages']['modify_id'] =  $_SESSION['sess_login_username'];
			$_POST['Packages']['modify_time'] = date('Y-m-d H:i:s');
			$model->history .= 'Updated ' . date('Y-m-d H:i:s') . ' | ' . $_SESSION['sess_login_username'] . "\n";
			$model->attributes = $_POST['Packages'];

			if($model->save()){
				Yii::app()->user->setFlash('success', '<strong>Package: ' . $_POST['Packages']['package_name'] . '</strong> was successfully updated.');
				$this->redirect(array('view', 'id' => $model->package_id));
			}
			else{
				Yii::app()->user->setFlash('error', '<strong>Error! Package: ' . $_POST['Packages']['package_name'] . '</strong> was not updated.');
			}
		}

		$this->render('view', array(
			'model' => $model,
			'totalCash' => $totalCash,
			'totalCharge' => $totalCharge,
			'dt_model' => $dt_model,
			'details_model' => $detailsModel,
		));
	}

	public function actionItems()
	{
		$sql = "SELECT cp.bestellnum AS serv_code, cp.artikelname AS serv_desc,
  					   cp.price_cash AS serv_cash, cp.price_charge AS serv_charge,
  					   'Pharmacy' AS serv_loc, 'PH' AS serv_type, cp.is_fs AS is_fs
				FROM
					   care_pharma_products_main cp
				WHERE cp.is_deleted = 0 AND cp.artikelname LIKE '%". $_GET['t'] ."%'
				UNION ALL
				SELECT ms.service_code AS serv_code, ms.name AS serv_desc,
  					   ms.price AS serv_cash, ms.price AS serv_charge,
  					   'Miscellaneous' AS serv_loc, 'MISC' AS serv_type, ms.is_fs AS is_fs
				FROM
					   seg_other_services ms
				WHERE ms.lockflag = 0 AND ms.name LIKE '%". $_GET['t'] ."%'";

		$service = Yii::app()->db->createCommand($sql)->queryAll();

		echo CJSON::encode($service);
	}

	// Deleting items from a package
	public function actionDelete($id,$pid,$code){
		$del = PackageDetails::model()->deleteAll("item_id = :id", array('id'=>$id));

		$p_model = $this->loadModel($pid);
		$p_model->modify_id = $_SESSION['sess_login_username'];
		$p_model->modify_time = date('Y-m-d H:i:s');
		$p_model->history .= 'Deleted Item['.$code.'] ' . date('Y-m-d H:i:s') . ' | ' . $_SESSION['sess_login_username'] . "\n";

		if(!$p_model->save()){
			$p_model->getErrors();
		}

		if($del){
			echo CJSON::encode(array('result' => true));
			//echo CJSON::encode(array('redirect' => Yii::app()->request->baseUrl . '/index.php?r=packageManager/manage/view&id=' . $pid));
		}else{
			echo CJSON::encode(array('result' => false));
		}
	}

}