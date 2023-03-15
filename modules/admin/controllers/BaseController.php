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

use app\modules\admin\models\forms\GroupScreenForm;
use app\modules\admin\models\forms\GroupForm;
use app\modules\admin\models\forms\MenuForm;
use app\modules\admin\models\forms\ScreenForm;
use app\modules\admin\models\forms\UserForm;

use app\models\GetAliasList;

class BaseController extends ModuleController
{
    public $model;
    public $modelForm;

    public function actionIndex()
    {
        $model = $this->model;

        $searchModel = new BaseSearch($model);
        $fullData = $searchModel->getData();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'fullData' => $fullData
        ]);
    }


    public function actionAjax()
    {
        $model = $this->model;

        $searchModel = new BaseSearch($model);
        $fullData = $searchModel->getData();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('ajax', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'fullData' => $fullData
        ]);
    }

    public function actionApi($id)
    {
        $result = [];
        $subresult = [];
        $model = $this->model;
        $request = Yii::$app->request;

        $searchModel = new BaseSearch($model);
        $fullData = $searchModel->getData();
        $dataProvider = $searchModel->search($request->queryParams);
        $aliasTypes = GetAliasList::getAliasTypes();

        switch ($id) {
            case 'checkLogin':
                $session_handle = $_SESSION['screenData']['sessionData']['sessionhandle'];
                if (Yii::$app->user->isGuest) {
                    $result = "false";
                } else {
                    $result = $session_handle;
                }
                //if(isset())
                break;
            case 'getAliasCodes':
                foreach ($aliasTypes as $AliasType) {
                    $data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetAliasList", $AliasType, 1, 99999999,
                        ["AliasCode", "AliasFormatType"]));
                    foreach ($data as $record) {
                        if ($request->post('aliasCode') != $record['AliasCode']) {
                            $result[$record['AliasCode']] = $record['AliasFormatType'];
                        }
                    }
                }
                //ksort($result);
                break;
            case 'getDependents':
                $data = GetAliasList::jsonToArray(GetAliasList::callAPI("SearchAliasDependency",
                    $request->post('aliasCode'), 1, 99999999, "", "AliasCode"));
                $result = $data;
                sort($result);
                //print_r($result);exit;
                break;
            case 'reloadAliases':
                $data = GetAliasList::jsonToArray(GetAliasList::callAPI("ReloadAliases", ""));
                $result = $data;
                /*if($data)
                    $result = ['status'=>'success'];*/
                break;
            case 'getTables':
                $data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetTablesInfo", ""));
                foreach ($data as $record) {
                    $result[] = $record['table_name'];
                }
                sort($result);
                break;
            case 'getFunctions':
                $data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetFuncList", "", 1, 1, "", "", $request->post('sdkLib')));
                foreach ($data as $record) {
                    $result[] = $record['table_name'];
                }
                sort($result);
                break;
            case 'getFields':
                $r = 0;
                $data = GetAliasList::jsonToArray(GetAliasList::callAPI("GetTablesInfo", ""));
                foreach ($data as $record) {
                    if ($record['table_name'] == $request->post('table')) {
                        $result[$r] = $record['fields'];
                        $rr = 0;
                        foreach ($result[$r] as $rec) {
                            $result[$r][$rr] = is_object($rec) ? get_object_vars($rec) : $rec;
                            $rr++;
                        }
                        foreach ($result[$r] as $k => $v) {
                            foreach ($v as $kk => $vv) {
                                $subresult[$kk] = $vv;
                            }
                        }
                        $r++;
                    }
                }
                $result = $subresult;
                //print_r($result);exit;
                ksort($result);
                break;
            case 'CreateAliasDependency':
                if ($request->post('AliasDependencyForm') != null) {
                    $save = GetAliasList::jsonToArray(GetAliasList::callAPI("CreateAliasDependency",
                        $request->post('AliasDependencyForm')));
                    if ($save) {
                        $result = ['status' => 'Success'];
                    } else {
                        $result = ['status' => 'Failure'];
                    }
                }
                break;
            case 'CreateAliasSecuritySpec':
                if ($request->post('AliasSecuritySpecForm') != null) {
                    $save = GetAliasList::jsonToArray(GetAliasList::callAPI("CreateAliasSecuritySpec",
                        $request->post('AliasSecuritySpecForm')));
                    if ($save) {
                        $result = ['status' => 'Success'];
                    } else {
                        $result = ['status' => 'Failure'];
                    }
                }
                break;
            case 'checkExistingRecord':
                if ($request->post()) {
                    if ($request->post('customAPI')) {
                        GetAliasList::jsonToArray(GetAliaslist::callAPI($request->post('searchType'), $id));
                    }
                }
                break;
        }

        return $this->render('api', [
            'data' => $result
        ]);
    }

    public function actionRequests()
    {
        $model = $this->model;

        $searchModel = new BaseSearch($model);
        $fullData = $searchModel->getData();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('requests', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'fullData' => $fullData,
            'requests' => Yii::$app->request->queryParams
        ]);
    }

    public function actionUpdate($id)
    {
        /* @var $modelClass BaseModel */
        $request = Yii::$app->request;

        if (isset($_REQUEST['customAPI'])) {
            //$request->post('AliasForm')['AliasCode']
            $model = GetAliasList::jsonToArray(GetAliasList::callAPI("SearchAliasById", $id));
            if ($model) {
                if ($request->post()) {

                    $init_save = GetAliasList::callAPI("UpdateAlias", $request->post());
                    $sd_save = GetAliaslist::handleAliasSubdetails($request, 'mixed');
                    $save = GetAliasList::jsonToArray($init_save);
                    if (strstr($init_save, 'unsuccessfully') == false) {
                        Yii::$app->getSession()->setFlash('success',
                            Yii::t('app', 'Successfully updated <b>' . $id . '</b>'));
                    } else {
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error updating <b>' . $id . '</b>'));
                    }

                    //return $this->redirect($id . '?customAPI=1');
                }
                return $this->render('update', ['model' => $model, 'request' => $request]);
            }
        } else {
            $modelClass = $this->model;
            $model = $modelClass::getModel($id);
            //echo("<pre>".print_r($this->model)."</pre>");exit;
            if ($model) {
                if ($model->load($request->post()) && $model->validate()) {
					//echo "<pre> In update model function :: "; print_r(json_decode($model->screen_tab_template)); die;

                    if ($modelClass::updateModel($id, $model)) {
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
                    } else {
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error update'));
                    }

                    //Get model with new attributes
                    $model = $modelClass::getModel($id);

                    if (!$model) {
                        return $this->redirect(['index']);
                    }
                }

                return $this->render('update', ['model' => $model]);
            }
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionDelete($id)
    {
        /* @var $modelClass BaseModel */
        $request = Yii::$app->request;
        $modelClass = $this->model;
        $model = $modelClass::getModel($id);
        //echo "test ".$id;exit; 
        if ($request->get('customAPI') == 1) {
            $model = GetAliasList::callAPI("DeleteAlias", $id);

            if ($model) {
                GetAliaslist::handleAliasSubdetails($request, 'delete', $id);
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully delete'));
            } else {
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error delete'));
            }
            return $this->redirect(['index']);
        } else {
            if ($model) {
                if ($modelClass::deleteModel($id)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully delete'));
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error delete'));
                }

                return $this->redirect(['index']);
            }
        }
        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionCreate()
    {
        /* @var $model GroupForm|GroupScreenForm|MenuForm|ScreenForm|UserForm */
        /* @var $modelClass BaseModel */
        $request = Yii::$app->request;
        //print_r($request->post());exit;
        $modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $model = new $modelClassForm();
        $callAPI = false;

        //echo "<pre>".print_r(Yii::$app->request->post())."</pre>";exit;
        if ($request->post('customAPI') != null) {
            if($request->post('AliasForm')){
                $callAPI = GetAliasList::jsonToArray(GetAliasList::callAPI("CreateAlias", $request->post('AliasForm')));
            }
            GetAliaslist::handleAliasSubdetails($request, 'create');

            if ($callAPI) {
                Yii::$app->getSession()->setFlash('success',
                    Yii::t('app', 'Successfully created <b>' . $callAPI[0] . '</b>'));
            } else {
                Yii::$app->getSession()->setFlash('danger',
                    Yii::t('app', 'Error creating record. Record already exists.'));
            }
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
				//echo "<pre> In create model function :: "; print_r(Yii::$app->request->post());

                if ($modelClass::setModel($model)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
                    $this->redirect(['index']);
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error create'));
                }
            }
        }

        return $this->render('create', ['model' => $model, 'request' => $request]);
    }
}