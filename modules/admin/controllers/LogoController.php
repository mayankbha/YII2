<?php
namespace app\modules\admin\controllers;

use app\models\BaseModel;
use app\modules\admin\models\forms\ImageForm;
use app\modules\admin\models\Image;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\View;
use app\assets\AppAsset;
use Yii;

class LogoController extends BaseController
{
    public $model = Image::class;
    public $modelForm = ImageForm::class;

    public function actionCreate()
    {
        /* @var $model ImageForm */
        /* @var $modelClass Image */
        $modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $model = new $modelClassForm();

        if ($model->load(Yii::$app->request->post())) {
            $model->logo_image_body = UploadedFile::getInstance($model,'logo_image_body');
            if($model->validate()){
                if ($modelClass::setModel($model)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
                    $this->redirect(['index']);
                } else Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error create'));
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        /* @var $model ImageForm */
        /* @var $modelClass Image */
        $modelClass = $this->model;
        $model = $modelClass::getModel($id);

        if ($model) {
            if ($model->load(Yii::$app->request->post())) {
                $model->logo_image_body = UploadedFile::getInstance($model,'logo_image_body');
                if($model->validate()){
                    if ($modelClass::updateModel($id, $model)) {
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));

                        //Get model with new attributes
                        $model = $modelClass::getModel($id);
                    } else Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
                }
            }

            return $this->render('update', ['model' => $model]);
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}