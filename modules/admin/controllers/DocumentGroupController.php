<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\DocumentFamily;
use Yii;
use app\modules\admin\models\DocumentGroup;
use app\modules\admin\models\forms\DocumentGroupForm;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class DocumentGroupController extends BaseController
{
    public $model = DocumentGroup::class;
    public $modelForm = DocumentGroupForm::class;

    public function actionUpdate($id)
    {
        /* @var $modelClass DocumentGroup */
        $modelClass = $this->model;
        $modelForm = $this->modelForm;

        $models = $modelClass::getModels($id);

        if (!$models) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        $families = [];
        $categories = [];
        if (($familyModels = DocumentFamily::getParentData()) && !empty($familyModels->list)) {
            $families = ArrayHelper::map($familyModels->list, 'family_name', 'family_description');
            $categories = ArrayHelper::map($familyModels->list, 'category', 'description', 'family_name');
        }

        /** @var $models DocumentGroupForm $post */
        if ($post = Yii::$app->request->post($models[0]->formName(), false)) {
            foreach ($models as $key => $item) {
                $modelClass::deleteModel($item['pk']);
            }

            $models = [];
            foreach ($post['document_category'] as $key => $item) {
                $modelLoadArray = $post;
                $modelLoadArray['document_category'] = $item;

                $models[$key] = new $modelForm;
                if ($models[$key]->load($modelLoadArray, '') && $models[$key]->validate()) {
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
        return $this->render('update', ['models' => $models, 'families' => $families, 'categories' => $categories]);
    }

    public function actionCreate()
    {
        /* @var $modelClass DocumentFamily */
        $modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $models = [new $modelClassForm()];

        $families = [];
        $categories = [];
        if (($familyModels = DocumentFamily::getParentData()) && !empty($familyModels->list)) {
            $families = ArrayHelper::map($familyModels->list, 'family_name', 'family_description');
            $categories = ArrayHelper::map($familyModels->list, 'category', 'description', 'family_name');
        }

        if (($post = Yii::$app->request->post($models[0]->formName(), false))) {
            if (empty($post['document_category'])) {
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Document category can\'t be empty'));
            } else {
                $modelLoadArray = $post;
                foreach ($post['document_category'] as $item) {
                    $modelLoadArray['document_category'] = $item;

                    /** @var DocumentGroupForm $model */
                    $model = new $modelClassForm;
                    if ($model->load($modelLoadArray, '') && $model->validate()) {
                        if (!$modelClass::setModel($model)) {
                            Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
                            break;
                        }
                    }
                }

                if (!Yii::$app->getSession()->getFlash('danger')) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
                    return $this->redirect(['index']);
                }
            }
        }

        return $this->render('create', ['models' => $models, 'families' => $families, 'categories' => $categories]);
    }

    public function actionDelete($id)
    {
        /* @var $modelClass DocumentGroup */
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