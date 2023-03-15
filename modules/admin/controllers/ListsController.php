<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\controllers\ModuleController;
use app\models\BaseModel;
use app\modules\admin\models\BaseSearch;
use Yii;
use yii\web\NotFoundHttpException;

use app\models\GetListList;
use app\modules\admin\models\forms\ListsForm;

class ListsController extends BaseController
{
    public $model = GetListList::class;
    public $modelForm = ListsForm::class;

    public function actionUpdateBulk($id)
    {
        $modelClass = $this->model;
        $modelForm = $this->modelForm;
        $models = $modelClass::getModels($id);

        if (!$models) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }

        /** @var $models ListsForm $post */
        if ($post = Yii::$app->request->post($models[0]->formName(), false)) {
            $error = false;

            foreach ($post as $key => $item) {
                if (!is_array($item)) {
                    continue;
                }

                $update_id = $item['list_name'] . ';' . $item['entry_name'];

                $model = $modelClass::getModel($update_id);

                if ($model) {
                    if ($model->load($item, '') && $model->validate()) {
                        if ($modelClass::updateModel($update_id, $model)) {
                            $error = false;
                        }
                    } else {
                        $error = true;
                    }
                } else {
                    $model_new = new $modelForm();

                    if ($model_new->load($item, '') && $model_new->validate()) {
                        if ($modelClass::setModel($model_new)) {
                            $error = false;
                        } else {
                            $error = true;
                        }
                    }
                }
            }

            if ($error) {
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
            } else {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
            }

            $models = $modelClass::getModels($id);
        }

        return $this->render('update-bulk', [
            'model' => $modelForm,
            'models' => $models
        ]);
    }

    public function actionDeleteAjax()
    {
        if (Yii::$app->request->isAjax && $id = urldecode(Yii::$app->request->post('id', false))) {
            $modelClass = $this->model;
            $model = $modelClass::getModel($id);

            if ($model) {
                if ($modelClass::deleteModel($id)) {
                    return 0;
                } else {
                    return 1;
                }
            } else {
                return 1;
            }
        } else {
            return 2; //Invalid Request
        }
    }
}