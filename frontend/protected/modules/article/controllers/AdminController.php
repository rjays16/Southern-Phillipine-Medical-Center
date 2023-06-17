<?php
use \SegHis\modules\article\models\Article;

class AdminController extends Controller
{

    public function filters()
    {
        return array(
            array('bootstrap.filters.BootstrapFilter')
        );
    }

    public function actionCreate()
    {
        $model = new Article();
        $model->publish_date = date('m/d/Y');
        $model->submit_date = date('m/d/Y');
        $model->category = 1;
        $model->dept_nr = 1;

        if (Yii::app()->request->isPostRequest) {

            $model->setAttributes($_POST[CHtml::modelName($model)]);
            $model->publish_date = date('Y-m-d', strtotime($model->publish_date));

            if (!empty($_FILES)) {
                $fileName = $_FILES[CHtml::modelName($model)]['name']['pic_file'];
                $fileNameDocs = $_FILES[CHtml::modelName($model)]['name']['file_name'];
                $model->pic_file = substr($fileName, 0, strpos($fileName, '.'));
                $model->pic_mime = pathinfo($fileName, PATHINFO_EXTENSION);
                $model->file_name = $fileNameDocs;
            }

            if ($model->validate()) {
                $model->save();
                  self::saveDocs($model);
                self::saveImage($model);
                Yii::app()->user->setFlash('success', '<strong>Success</strong> New article was created successfully.');
                $this->redirect(array('admin/update&id=' . $model->nr), true);
            }

        }

        $this->render('create', array(
            'model' => $model
        ));
    }

    public function actionUpdate($id)
    {

        /* @var $model Article */
        $model = Article::model()->findByPk($id);

        if (Yii::app()->request->isPostRequest) {

            $fileName = $_FILES[CHtml::modelName($model)]['name']['pic_file'];
            $fileNameDocs = $_FILES[CHtml::modelName($model)]['name']['file_name'];
            $model->setAttributes($_POST[CHtml::modelName($model)]);
            $model->publish_date = date('Y-m-d', strtotime($model->publish_date));

            if ($fileName != '') {
                $model->pic_file = substr($fileName, 0, strpos($fileName, '.'));
               
                $model->pic_mime = pathinfo($fileName, PATHINFO_EXTENSION);
              
            }
            if ($fileNameDocs !='') {
                 $model->file_name = $fileNameDocs;
            }

            if ($model->validate()) {
                $model->save();
                self::saveImage($model);
                self::saveDocs($model);
                Yii::app()->user->setFlash('success', '<strong>Success</strong> Article was updated successfully.');
                $this->redirect(array('admin/update&id=' . $model->nr), true);
            }

        }

        $model->publish_date = date('m/d/Y', strtotime($model->publish_date));

        $this->render('update', array(
            'model' => $model
        ));

    }

    private static function saveImage(Article $model)
    {
        $files = $_FILES[CHtml::modelName($model)];

        $fileName = $files['name']['pic_file'];
        $tmpFile = $files['tmp_name']['pic_file'];


        return move_uploaded_file($tmpFile, Config::get('news_fotos_path') . $fileName);
    }
     /*added By MARK 2016-09-11*/
    private static function saveDocs(Article $model)
    {
        $filess = $_FILES[CHtml::modelName($model)];
        $fileNames = $filess['name']['file_name'];
        $tmpFiles = $filess['tmp_name']['file_name'];
        return move_uploaded_file($tmpFiles, Config::get('news_fotos_path') . $fileNames);
    }

    public function actionList()
    {

        $model = new Article();
        $model->unsetAttributes();

        if (Yii::app()->request->isAjaxRequest) {
            $model->setAttributes($_GET[CHtml::modelName($model)]);
        }

        $this->render('list', array(
            'model' => $model
        ));
    }

    public function actionDelete($id)
    {
        echo Article::model()->findByPk($id)->delete();
    }

}