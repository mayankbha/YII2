<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use Yii;
use app\modules\admin\models\DocumentFamily;
use app\modules\admin\models\forms\DocumentFamilyForm;
use yii\web\NotFoundHttpException;

class DocumentFamilyController extends BaseController
{
    public $model = DocumentFamily::class;
    public $modelForm = DocumentFamilyForm::class;

    public function actionUpdate($id)
    {
        /* @var $modelClass DocumentFamily */
        $modelClass = $this->model;
        $modelForm = $this->modelForm;
        $models = $modelClass::getModels($id);

        if (!$models) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        /** @var $models DocumentFamilyForm $post */
        if ($post = Yii::$app->request->post($models[0]->formName(), false)) {
            foreach ($models as $key => $item) {
                $modelClass::deleteModel($item['pk']);
            }

            $models = [];
            foreach ($post as $key => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $item['family_name'] = $post['family_name'];
                $item['family_description'] = $post['family_description'];
                $models[$key] = new $modelForm;
                if ($models[$key]->load($item, '') && $models[$key]->validate()) {
                    if (!$modelClass::setModel($models[$key])) {
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
                        break;
                    }
                }
            }

            if (!Yii::$app->getSession()->getFlash('danger')) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
            }
        }
        return $this->render('update', ['models' => $models]);
    }

    public function actionCreate()
    {
        /* @var $modelClass DocumentFamily */
        $modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $models = [new $modelClassForm()];

        if ($post = Yii::$app->request->post($models[0]->formName(), false)) {
            foreach ($post as $item) {
                if (!is_array($item)) {
                    continue;
                }

                $item['family_name'] = $post['family_name'];
                $item['family_description'] = $post['family_description'];

                /** @var DocumentFamilyForm $model */
                $model = new $modelClassForm();
                if ($model->load($item, '') && $model->validate()) {
                    if (!$modelClass::setModel($model)) {
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error create'));
                        break;
                    }
                }
            }

            if (!Yii::$app->getSession()->getFlash('danger')) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', ['models' => $models]);
    }

    public function actionDelete($id)
    {
        /* @var $modelClass DocumentFamily */
        $modelClass = $this->model;
        $models = $modelClass::getModels($id);

        if (!empty($models)) {
            foreach ($models as $key => $item) {
                if (!$modelClass::deleteModel($item['pk'])) {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error delete'));
                }
            }

            if (!Yii::$app->getSession()->getFlash('danger')) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully delete'));
                return $this->redirect(['index']);
            }
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}