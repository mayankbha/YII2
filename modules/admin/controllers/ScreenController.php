<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\assets\ScreenBuilderAsset;
use app\models\TemplateList;
use app\modules\admin\models\BaseSearch;
use app\modules\admin\models\CommonArea;
use app\modules\admin\models\CustomQuery;
use app\modules\admin\models\Menu;
use app\modules\admin\services\JsTemplatesService;
use http\Exception\InvalidArgumentException;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use app\modules\admin\models\Screen;
use app\modules\admin\models\forms\ScreenForm;
use app\models\CustomLibs;
use app\models\GetListList;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use app\models\ExtensionsList;
use yii\web\View;

class ScreenController extends BaseController
{
    public $model = Screen::class;
    public $modelForm = ScreenForm::class;

    public function actionBuilder($id = null)
    {
        if(!Yii::$app->request->isAjax) {
            $this->view->registerAssetBundle(ScreenBuilderAsset::class);
        }

        if ($id) {
            $renderTemplate = 'update';
            $model = Screen::getModel($id);
        } else {
            $renderTemplate = 'create';
            $model = new ScreenForm();
        }

        if (!$model) {
            return $this->redirect(Url::toRoute(['/admin/screen']));
        }

        $request = Yii::$app->request->post();
        if ($model->load($request) && $model->validate()) {
            $screenTabTemplate = ScreenForm::getDefaultScreenTabTemplate();
            $screenTabTemplate->template_layout = $model->screen_tab_template->template_layout;
            $screenTabTemplate->layout_type = isset($request['screen_tab_type']) ? $request['screen_tab_type'] : null;

            //Search configuration
            if (!empty($request['search-configuration-radio'])) {
                switch ($request['search-configuration-radio']) {
                    case ScreenForm::SIMPLE_SEARCH_TYPE:
                        $screenTabTemplate->search_configuration = ($request['search_function_config']) ? json_decode($request['search_function_config']) : null;
                        $screenTabTemplate->search_custom_query = null;
                        break;
                    case ScreenForm::CUSTOM_SEARCH_TYPE:
                        $screenTabTemplate->search_configuration = null;
                        $screenTabTemplate->search_custom_query = ($request['search_custom_query']) ? json_decode($request['search_custom_query']) : null;
                        break;
                }
            }

            //Step screen
            $screenTabTemplate->step_screen->enable = isset($request['is_step_screen']);
            $screenTabTemplate->step_screen->icon = (isset($request['screen_step_icon']) && ($request['screen_step_icon'] != 'empty')) ? $request['screen_step_icon'] : null;

            //alias framework
            $screenTabTemplate->alias_framework->enable = isset($request['is_use_alias_framework']);
            $screenTabTemplate->alias_framework->request_primary_table = isset($request['request_primary_table']) ? $request['request_primary_table'] : null;
            $screenTabTemplate->alias_framework->data_source_insert = isset($request['alias_framework_func_insert']) ? $request['alias_framework_func_insert'] : null;
            $screenTabTemplate->alias_framework->data_source_update = isset($request['alias_framework_func_update']) ? $request['alias_framework_func_update'] : null;
            $screenTabTemplate->alias_framework->data_source_delete = isset($request['alias_framework_func_delete']) ? $request['alias_framework_func_delete'] : null;

            //Extensions
            $screenTabTemplate->screen_extensions->add->pre = isset($request['add_pre']) ? json_decode($request['add_pre']) : null;
            $screenTabTemplate->screen_extensions->add->post = isset($request['add_post']) ? json_decode($request['add_post']) : null;

            $screenTabTemplate->screen_extensions->delete->pre= isset($request['delete_pre']) ? json_decode($request['delete_pre']) : null;
            $screenTabTemplate->screen_extensions->delete->post = isset($request['delete_post']) ? json_decode($request['delete_post']) : null;

            $screenTabTemplate->screen_extensions->edit->pre = isset($request['edit_pre']) ? json_decode($request['edit_pre']) : null;
            $screenTabTemplate->screen_extensions->edit->post = isset($request['edit_post']) ? json_decode($request['edit_post']) : null;

            $screenTabTemplate->screen_extensions->inquire->pre = isset($request['inquire_pre']) ? json_decode($request['inquire_pre']) : null;
            $screenTabTemplate->screen_extensions->inquire->post = isset($request['inquire_post']) ? json_decode($request['inquire_post']) : null;

            $screenTabTemplate->screen_extensions->execute->pre = isset($request['execute_pre']) ? json_decode($request['execute_pre']) : null;
            $screenTabTemplate->screen_extensions->execute->post = isset($request['execute_post']) ? json_decode($request['execute_post']) : null;

            $screenTabTemplate->screen_extensions->executeFunction->library = isset($request['execute_library_input']) ? json_decode($request['execute_library_input']) : null;
            $screenTabTemplate->screen_extensions->executeFunction->function = isset($request['execute_function_input']) ? json_decode($request['execute_function_input']) : null;
            $screenTabTemplate->screen_extensions->executeFunction->custom = isset($request['execute_custom_input']) ? json_decode($request['execute_custom_input']) : null;

            $model->screen_tab_template = $screenTabTemplate;

            $listList = GetListList::getData()->list;
            $jsTemplates = JsTemplatesService::getSelect();
            $jsArray = JsTemplatesService::getTemplates();

//            foreach ($listList as $key => $list) {
//                if ($list['list_name'] == 'JavaScripts') {
//                    $jsTemplates[] = ['id' => $key, 'entry_name' => $list['entry_name']];
//                    $jsArray[$key] =  base64_decode($list['description']);
//                    $jsArray[$key]['code'] =  base64_decode($list['description']);
//                    $jsArray[$key]['html'] =  base64_decode($list['description']);
//                }
//            }

            $pullUpList = [];
            foreach(array_unique(ArrayHelper::getColumn($listList, 'list_name')) as $list) {
                $pullUpList[] = ['list_name' => $list];
            };

            $this->view->registerJs("screenCreator.registerJsTemplate(" . json_encode($jsArray) . ")");

            return $this->render('builder', [
                'model' => $model,
                'pullUpList' => new ArrayDataProvider([
                    'key' => 'list_name',
                    'allModels' => $pullUpList,
                    'sort' => false,
                    'pagination' => false
                ]),
                'jsTemplates' => $jsTemplates
            ]);
        }

        $customQueryParams = [];
        if (($customQueries = CustomQuery::getData()) && !empty($customQueries->list)) {
            $customQueryParams = ArrayHelper::map($customQueries->list, 'pk', function ($data) {
                return [
                    'query_params' => $data['query_params'],
                    'query_pks' => $data['query_pks'],
                    'pk' => $data['pk']
                ];
            });
            $customQueries = ArrayHelper::map($customQueries->list, 'pk', function ($data) {
                return $data['query_name'] . (!empty($data['description']) ? ' - ' . $data['description'] : '');
            });
        }

        $this->view->registerJs('firstStepConfig.registerCustomQueryParams(' . json_encode($customQueryParams) . ');', View::POS_HEAD);

        return $this->render($renderTemplate, [
            'model' => $model,
            'customQueryList' => $customQueries,
            'builder' => true
        ]);
    }

    public function actionLibFunctionParams()
    {
        $request = Yii::$app->request;
        if ($request->isAjax && $data = $request->post()) {
            if (!empty($data['library']) && !empty($data['function'])) {
                $response = Yii::$app->response;
                $funcParams = TemplateList::getData([
                    'lib_name' => [$data['library']],
                    'data_source' => [$data['function']]
                ]);

                if (!empty($funcParams->list)) {
                    $response->format = Response::FORMAT_JSON;
                    $response->data = $funcParams->list;
                }

                return $response;
            }
        }
    }

    public function actionLibFunctions()
    {
        $request = Yii::$app->request;
        if ($request->isAjax && $data = $request->post()) {
            if (!empty($data['library'])) {
                if (empty($data['direction'])) {
                    $data['direction'] = CustomLibs::LAYOUT_TYPE_MULTI_SEARCH;
                }

                $type = !empty($data['type']) ? $data['type'] : null;
                $libFunctions = CustomLibs::getLibFuncList($data['library'], $data['direction'], $type);

                $response = Yii::$app->response;
                if (!empty($libFunctions)) {
                    $response->format = Response::FORMAT_JSON;
                    $response->data = $libFunctions;
                }

                return $response;
            }
        }
    }

    public function actionLibFunctionExtension()
    {
        $request = Yii::$app->request;
        if ($request->isAjax && $data = $request->post()) {
            if (!empty($data['library']) && !empty($data['funcName'])) {
                $response = Yii::$app->response;
                $funcExtensions = ExtensionsList::getList($data['library'], $data['funcName']);

                if (!empty($funcExtensions)) {
                    $response->format = Response::FORMAT_JSON;
                    $response->data = $funcExtensions;
                }

                return $response;
            }
        }
    }

    public function actionGetFunctionExtension()
    {
        $model = Menu::class;
        $searchModel = new BaseSearch($model);

        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            if ($data = $request->post()) {
                if (!empty($data['screenId'])) {
                    $model = Screen::getModel($data['screenId']);
                    if (!empty($model['screen_tab_template']->search_configuration->data_source_get)) {
                        $libName = $model['screen_lib'];
                        $funcName = $model['screen_tab_template']->search_configuration->data_source_get;
                        $response->data = TemplateList::getData([
                            'lib_name' => [$libName],
                            'data_source' => [$funcName]
                        ])->list;
                    }
                } else {
                    if ($data['get'] == 'GroupScreen') {
                        $result = CommonArea::getData([
                            'menu_name' => [$data['menu_name']]
                        ]);
                        if ($result) {
                            $response->data['data'] = $result->list;
                            $response->data['lib'] = $result::$dataLib;
                            $response->data['dataAction'] = $result::$dataAction;
                        } else {
                            $response->data['error'] = true;
                        }
                    } else if ($data['get'] == 'Screen') {
                        $response->data = Screen::getData([
                            'screen_name' => [$data['screen_name']]
                        ])->list;
                        if (empty($response->data)) {
                            $response->data['error'] = true;
                        }
                    }
                }
            } else {
                $response->data = $searchModel->getData();
            }
            return $response;
        }
    }

    public function actionGetScreenSearchParams()
    {
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($screenId = Yii::$app->request->post('screen_id', false)) {
            $model = Screen::getModel($screenId);
            if (!empty($model->screen_tab_template)) {
                if (!empty($model->screen_tab_template->search_custom_query->query_params)) {
                    return ArrayHelper::getColumn($model->screen_tab_template->search_custom_query->query_params, 'name');
                }

                if (!empty($model->screen_tab_template->search_configuration->data_source_get)) {
                    $aliases =  TemplateList::getData([
                        'lib_name' => [$model['screen_lib']],
                        'data_source' => [$model->screen_tab_template->search_configuration->data_source_get]
                    ]);

                    if (!empty($aliases->list)) {
                        return ArrayHelper::getColumn($aliases->list, 'alias_field');
                    }
                }
            }
        }

        throw new InvalidArgumentException();
    }
}