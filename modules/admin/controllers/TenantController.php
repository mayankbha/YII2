<?php
namespace app\modules\admin\controllers;

use app\modules\admin\models\forms\TenantForm;
use app\modules\admin\models\Tenant;
use yii\web\NotFoundHttpException;
use Yii;

class TenantController extends BaseController
{
    public $model = Tenant::class;
    public $modelForm = TenantForm::class;

    public function actionUpdate($id)
    {
        /* @var $modelClass Tenant */
        $modelClass = $this->model;
        $model = $modelClass::getModel($id);

        if ($model) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->StyleTemplate->load(Yii::$app->request->post());
                $model->ChatSettings->load(Yii::$app->request->post());
                $model->Logos = Yii::$app->request->post('Logos', ['', '']);

                if ($modelClass::updateModel($id, $model)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
                    $model = $modelClass::getModel($id);
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
                }

                if (!$model) {
                    return $this->redirect('index');
                }
            }

            return $this->render('update', ['model' => $model]);
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCreate()
    {
        /* @var $model TenantForm */
        /* @var $modelClass Tenant */
        $modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $model = new $modelClassForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->StyleTemplate->load(Yii::$app->request->post());
            $model->ChatSettings->load(Yii::$app->request->post());
            $model->Logos = Yii::$app->request->post('Logos', ['', '']);

            if ($modelClass::setModel($model)) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
                return $this->redirect(['index']);
            }

            Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error create'));
        }

        return $this->render('create', ['model' => $model]);
    }
}