<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\models\BaseModel;
use app\modules\admin\models\forms\ImageForm;
use app\modules\admin\models\Image;
use app\modules\admin\models\SecurityFilter;
use app\modules\admin\models\User;
use app\modules\admin\models\forms\UserForm;
use yii\helpers\ArrayHelper;
use app\modules\admin\services\TenantSettingsService;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\web\View;
use app\assets\AppAsset;
use Yii;

class UserController extends BaseController
{
    public $model = User::class;
    public $modelForm = UserForm::class;

    public function actionUpdate($id)
    {
        $this->view->registerJsFile(Url::toRoute(['/js/admin/user.js']), [
            'position' => View::POS_END,
            'depends' => AppAsset::class
        ]);

        /* @var $modelClass BaseModel */
        $modelClass = $this->model;
        $model = $modelClass::getModel($id);

        if ($model) {
			if(Yii::$app->getRequest()->getQueryParam('action') == 'copy_user') {
				$model->user_name = '';
				$model->account_password = '';
				$model->account_name = '';
			}

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->style_template->load(Yii::$app->request->post());

                $arrayList = [
                    'avatar_array' => UploadedFile::getInstancesByName('avatar_array'),
                    'background_image_array' => UploadedFile::getInstancesByName('background_image_array'),
                    'menu_background_image_array' => UploadedFile::getInstancesByName('menu_background_image_array')
                ];
                foreach($arrayList as $key => $items) {
                    $pk = [];
                    foreach($items as $file) {
                        $image = new ImageForm();

                        $image->list_name = Yii::$app->getUser()->getIdentity()->user_name;
                        $image->entry_name = "{$key}_" . str_replace(".", "", microtime(true));
                        $image->type = ImageForm::TYPE_IMAGE;
                        $image->logo_image_body = $file;

                        $result = Image::setModel($image);
                        if ($result['record_list']['PK']) {
                            $pk[] = $result['record_list']['PK'];
                        }
                    }

                    $model->style_template->$key = array_merge(ArrayHelper::getColumn($model->style_template->$key, 'pk'), $pk);
                }

                if ($modelClass::updateModel($id, $model)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
                    $model = $modelClass::getModel($id);
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
                }
            }

            return $this->render('update', ['model' => $model]);
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCreate()
    {
        $this->view->registerJsFile(Url::toRoute(['/js/admin/user.js']), [
            'position' => View::POS_END,
            'depends' => AppAsset::class
        ]);

        /* @var $model UserForm */
        /* @var $modelClass BaseModel */
        $modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $model = new $modelClassForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->style_template->load(Yii::$app->request->post());
            if ($modelClass::setModel($model)) {
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
                return $this->redirect(['index']);
            } else {
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error create'));
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionGetUserFormList()
    {
        $tenant = Yii::$app->request->post('tenant');
        $account_type = Yii::$app->request->post('account_type');
        $selected = Yii::$app->request->post('selected');
        if (!is_null($tenant) && !is_null($account_type)) {
            $filters = SecurityFilter::getData(['tenant' => [$tenant], 'account_type' => [$account_type]]);
            $html = '';
            if (!is_null($filters)) {
                $html = $html . '<option disabled selected value> -- Select user type -- </option>\n';
                foreach ($filters->list as $e) {
                    $s = $e['user_type']===$selected ? 'selected' : '';
                    $html = $html . sprintf("<option %s data-filter1='%s' data-filter2='%s' data-filter1_length='%s' data-filter2_length='%s' value='%s'>%s - %s</option>\n",
                            $s,
                            $e['filter1'],
                            $e['filter2'],
                            $e['filter1_length'],
                            $e['filter2_length'],
                            $e['user_type'],
                            $e['user_type'],
                            $e['description']
                        );
                }
            }
            echo $html;
        }
    }

    public function actionReset($id)
    {
        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionSetTenantSettings()
    {
        $request = Yii::$app->request;
        if ($request->isAjax && $data = $request->post()) {
            $responseData = TenantSettingsService::prepareSettingsForNewUser($data);
            if (!empty($responseData)) {
                $response = Yii::$app->response;
                $response->format = Response::FORMAT_JSON;
                $response->data = $responseData;

                return $response;
            }
        }
        return $this->goHome();
    }

    public function actionDeleteImage()
    {
        $request = Yii::$app->request;
        if ($request->isAjax && $requestData = $request->post()) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = false;

            /* @var $modelClass BaseModel */
            $modelClass = $this->model;
            $id = (int)$requestData['id'];
            $selectedImageAttr = $requestData['attribute'];
            $attribute = $requestData['attribute'] .'_array';
            $imagePK = $requestData['image'];


            if ($model = $modelClass::getModel($id)) {
                if (isset($model->style_template->{$attribute})) {
                    foreach ($model->style_template->{$attribute} as $key => $item) {
                        if ($item['pk'] == $imagePK) {
                            if($model->style_template->{$selectedImageAttr} == $imagePK) {
                                $model->style_template->{$selectedImageAttr} = null;
                            }
                            unset($model->style_template->{$attribute}[$key]);
                            Image::deleteModel($imagePK);
                        }
                    }
                    $model->style_template->prepareImageAttributes();
                    $model->account_password = null;
                }

                if ($modelClass::updateModel($id, $model)) {
                    $response->data = true;
                }
            }

            return $response;
        }

        return $this->goHome();
    }
}