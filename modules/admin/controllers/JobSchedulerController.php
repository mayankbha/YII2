<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\assets\JobSchedulerAsset;
use app\assets\JobScreenBuilderAsset;

use app\modules\admin\models\JobScheduler;
use app\modules\admin\models\forms\JobSchedulerForm;

use Yii;
use yii\web\Response;
use app\models\ExtensionsList;

use app\models\CustomLibs;
use app\modules\admin\models\CustomQuery;
use yii\helpers\ArrayHelper;
use app\models\GetListList;
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\web\BadRequestHttpException;
use http\Exception\InvalidArgumentException;
use app\modules\admin\services\JsTemplatesService;
use app\models\TemplateList;
use app\modules\admin\models\BaseSearch;
use app\modules\admin\models\CommonArea;

use app\models\CommandData;

class JobSchedulerController extends BaseController
{
    public $model = JobScheduler::class;
    public $modelForm = JobSchedulerForm::class;

    public function beforeAction($action)
    {
        $this->view->registerAssetBundle(JobSchedulerAsset::class);

        return parent::beforeAction($action);
    }

	public function actionBuilder()
	{ echo 'in create function';
		 if(!Yii::$app->request->isAjax) {
            $this->view->registerAssetBundle(JobScreenBuilderAsset::class);
        }

		$request = Yii::$app->request->post();

        /*$modelClassForm = $this->modelForm;
        $modelClass = $this->model;
        $model = new $modelClassForm();*/

		$model = new JobSchedulerForm();

		//echo "<pre>"; print_r($model); die;

		if ($model->load($request) && $model->validate()) {
			$jobsParams = JobSchedulerForm::getDefaultJobParams();

			//$jobsParams->function_extensions_job_params->template_layout = $model->jobs_params->function_extensions_job_params->template_layout;

			$jobsParams->function_extensions_job_params->lib_name = $request['lib_name'];

			//Search configuration
            if (!empty($request['search-configuration-radio'])) {
                switch ($request['search-configuration-radio']) {
                    case JobSchedulerForm::SIMPLE_SEARCH_TYPE:
                        $jobsParams->function_extensions_job_params->search_function_info->config = ($request['config']) ? json_decode($request['config']) : null;
						$jobsParams->function_extensions_job_params->search_custom_query = null;
                        break;
                    case JobSchedulerForm::CUSTOM_SEARCH_TYPE:
                        $jobsParams->function_extensions_job_params->search_function_info->config = null;
                        $jobsParams->function_extensions_job_params->search_custom_query = ($request['search_custom_query']) ? json_decode($request['search_custom_query']) : null;
                        break;
                }
            }

			//alias framework
            $jobsParams->function_extensions_job_params->alias_framework_info->enable = isset($request['is_use_alias_framework']);
            $jobsParams->function_extensions_job_params->alias_framework_info->request_primary_table = isset($request['request_primary_table']) ? $request['request_primary_table'] : null;
            $jobsParams->function_extensions_job_params->alias_framework_info->data_source_insert = isset($request['alias_framework_func_insert']) ? $request['alias_framework_func_insert'] : null;
            $jobsParams->function_extensions_job_params->alias_framework_info->data_source_update = isset($request['alias_framework_func_update']) ? $request['alias_framework_func_update'] : null;
            $jobsParams->function_extensions_job_params->alias_framework_info->data_source_delete = isset($request['alias_framework_func_delete']) ? $request['alias_framework_func_delete'] : null;

			$model->jobs_params = $jobsParams;

			//echo "<pre>"; print_r($model); die;

			$listList = GetListList::getData()->list;
			$jsTemplates = JsTemplatesService::getSelect();
			$jsArray = JsTemplatesService::getTemplates();

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

			/*if ($modelClass::setModel($model)) {
				//Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
				//$this->redirect(['index']);
			} else {
				//Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Error create'));
			}*/
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

		//echo "<pre>"; print_r($model); die;

		$this->view->registerJs('firstStepConfig.registerCustomQueryParams(' . json_encode($customQueryParams) . ');', View::POS_HEAD);

		return $this->render('create', [
			'model' => $model,
			'request' => $request,
			'builder' => true,
			'customQueryList' => $customQueries
			]
		);
	}

    public function actionLibFunctionJob()
    {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            $response = Yii::$app->response;
            $funcJobs = ExtensionsList::getJobList();

            if (!empty($funcJobs)) {
                $response->format = Response::FORMAT_JSON;
                $response->data = $funcJobs;
            }

            return $response;
        }
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
                    $model = JobScheduler::getModel($data['screenId']);
                    if (!empty($model['jobs_params']->search_function_info->data_source_get)) {
                        $libName = $model['job_params']->library;
                        $funcName = $model['job_params']->search_function_info->data_source_get;
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
            $model = JobScheduler::getModel($screenId);
            if (!empty($model->jobs_params)) {
                if (!empty($model->jobs_params->search_custom_query->query_params)) {
                    return ArrayHelper::getColumn($model->jobs_params->search_custom_query->query_params, 'name');
                }

                if (!empty($model->jobs_params->search_function_info->data_source_get)) {
                    $aliases =  TemplateList::getData([
                        'lib_name' => [$model->jobs_params->library],
                        'data_source' => [$model->jobs_params->search_function_info->data_source_get]
                    ]);

                    if (!empty($aliases->list)) {
                        return ArrayHelper::getColumn($aliases->list, 'alias_field');
                    }
                }
            }
        }

        throw new InvalidArgumentException();
    }

	public function actionGetTableData()
	{
		$post = Yii::$app->request->post();

		if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Invalid request');
        }

		$tableData = ExtensionsList::getTableData($post);

		if($post['searchType'] == 'simple') {
			$cols = $post['func_param']['func_inparam_configuration'];

			$final_result = array();

			foreach($tableData as $key => $row) {
				foreach($cols as $col)
					$final_result[] = $row[$col];
			}
		} else if($post['searchType'] == 'multi') {
			$sql_params = $post['func_param']['query_params'][0]['name'];

			$final_result = array();

			foreach($tableData as $key => $row) {
				$final_result[] = $row[$sql_params];
			}
		}

		//echo "<pre>"; print_r($tableData);

		//echo "<pre>"; print_r($final_result);

		$response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $final_result;

		return $response;
	}

	public function actionSearchData()
    {
        $post = Yii::$app->request->post();

        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Invalid request');
        }

		$col_val = $post['value'];
		$jobs_params = json_decode($post['jobs_params']);

		$library = $jobs_params->function_extensions_job_params->lib_name;

		$alias_framework_info = $jobs_params->function_extensions_job_params->alias_framework_info;

		$search_function_info = $jobs_params->function_extensions_job_params->search_function_info->config;

		$search_custom_query = $jobs_params->function_extensions_job_params->search_custom_query;

		if(!empty($search_function_info)) {
			foreach($search_function_info->func_inparam_configuration as $key => $col_name) {
				$post['queries'][$key]['name'] = $col_name;
				$post['queries'][$key]['value'] = $col_val;
			}
		} else if(!empty($search_custom_query)) {
			foreach($search_custom_query->query_params as $key => $col_name) {
				$post['queries'][$key]['name'] = $col_name->name;
				$post['queries'][$key]['value'] = $col_val;
			}
		}

		//echo "<pre>"; print_r($post); die;

		if (empty($library) || empty($post['queries']) || !is_array($post['queries'])) {
            throw new BadRequestHttpException('Invalid request');
        }

		$queries = ArrayHelper::map($post['queries'], 'name', 'value');

		$search_result = CommandData::search($library, $queries, $search_function_info, $search_custom_query);

		/*$post['library'] = 'CodiacSDK.Universal.dll';
		$post['queries'][] = array('name' => 'RiderType', 'value' => '1');
		//$post['queries'][] = array('name' => 'Description', 'value' => 'BLAH7');

		//echo "<pre>"; print_r($post);

        $queries = ArrayHelper::map($post['queries'], 'name', 'value');

		//echo "<pre>"; print_r($queries);

		//$search_function_info = '';
		$search_function_info = '{
									"data_source_get": "Search_RiderMaster",
									"field_label":"",
									"func_inparam_configuration":["RiderType"],
									"pk_configuration":["Company","RiderCode","Tenant"]
								}';

		$search_custom_query = '';
		$search_custom_query = '{
									"alias_query_pk": ["Portfolio.UniqueId"],
									"query_params":[{"name":"Description", "value":"Description"}],
									"query_pk": "PortfolioMultisearch"
								}';

		//echo "<pre> json decode search_function_info :: "; print_r(json_decode($search_function_info));
		//echo "<pre> json decode search_custom_query :: "; print_r(json_decode($search_custom_query)); die;

		$search_result = CommandData::search($post['library'], $queries, json_decode($search_function_info), json_decode($search_custom_query));*/

		//$response = Yii::$app->response;
		//$response->format = Response::FORMAT_JSON;
		//$response->data = $search_result[0];

		//echo json_encode($response[0]);

		//echo "<pre>"; print_r($response);

		//echo "<pre>"; print_r($post);

		$template_layout_arr = json_decode($post['template_layout']);

		//echo '<pre>'; print_r($template_layout_arr);

		//echo '<pre>'; print_r($search_result[0]);

		//echo json_encode($search_result[0]);

		$html = '';

		$field_name = '';
		$field_value = '';

		if($template_layout_arr[0]->data_source_get == 'GetAliasFramework' && !empty($template_layout_arr[0]->layout_fields)) {
			foreach($template_layout_arr[0]->layout_fields as $field) {
				if($field[0]->name == 'data_field') {
					$field_name_complete = $field[0]->value;

					$field_name_exlode = explode('.', $field_name_complete);
					$field_name = end($field_name_exlode);

					if(isset($search_result[0])) {
						foreach($search_result[0] as $field_key => $field_val) {
							if($field_key == $field_name) {
								$field_value = $field_val;
							} else {
								if(isset($search_result[0]['id'])) {
									if(!empty($search_function_info)) {
										$data = json_decode($search_result[0]['id']);
									} else if(!empty($search_custom_query)) {
										$data = $search_result[0]['id'];
									}

									foreach($data as $field_id_key => $field_id_val) {
										if($field_id_key == $field_name)
											$field_value = $field_id_val;
									}
								}
							}
						}
					}

					if(($field_name != '') && ($field_value != '')) {
						$html .= '<div class="row">
							<div class="col-sm-3">
								<label class="control-label">'.$field_name.'</label>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control common-alias-function-inputs" name="'.$field_name_complete.'" value="'.$field_value.'" readonly />
							</div>
						</div><br>';
					}
				}
			}
		}

		$final_response = array('search_result' => $search_result[0], 'html' => $html);

		echo json_encode($final_response); die;
	}

	public function actionShowOutputFields()
    {
        $post = Yii::$app->request->post();

		$template_layout_arr = json_decode($post['template_layout']);

		$html = '';

		if($template_layout_arr[0]->data_source_get == 'GetAliasFramework' && !empty($template_layout_arr[0]->layout_fields)) {
			foreach($template_layout_arr[0]->layout_fields as $field) {
				if($field[0]->name == 'data_field') {
					$field_label = $field[1]->value;
					$field_name_complete = $field[0]->value;

					$html .= '<form id="alias_framework_create_form" action="" method="post"><div class="row">
							<div class="col-sm-3">
								<label class="control-label">'.$field_label.'</label>
							</div>
							<div class="col-sm-6">
								<input type="text" class="form-control common-alias-function-inputs" name="'.$field_name_complete.'" value="" />
							</div>
						</div></form><br>';
				}
			}
		}

		$final_response = array('html' => $html);

		echo json_encode($final_response); die;
	}

	public function actionTemplateData() {
		//$tabModel = ExtensionsList::getTableData();

		$result = TemplateList::getData([
                    'lib_name' => ['CodiacSDK.Universal.dll'],
                    'data_source' => ['Search_RiderMaster']
                ]);

		echo "<pre>"; print_r($result);

		die;

		/*echo 'in template data controller';

		$templateLayout = '[{"row_num":"1","col_num":"1","data_source_get":"GetList_AccountingDaily","layout_type":"LIST","layout_label":"test","layout_label_internationalization":null,"layout_fields":[[{"name":"data_field","value":"AcctAmount"},{"name":"field_label","value":"amount"},{"name":"field_type","value":"Numeric"},{"name":"id-dependent-field","value":""},{"name":"days","value":"0"},{"name":"hours","value":"0"},{"name":"minutes","value":"0"},{"name":"seconds","value":"0"},{"name":"label_orientation","value":"LEFT"},{"name":"format_type","value":""},{"name":"field_length","value":"40"},{"name":"field_width_type","value":""},{"name":"field_tooltip","value":""},{"name":"label_width","value":""},{"name":"block_width","value":"12"},{"name":"block_height","value":"2"},{"name":"block_row","value":""},{"name":"block_col","value":"0"},{"name":"notification_pk[]","value":""},{"name":"label_text_color","value":""},{"name":"field_text_color","value":""},{"name":"label_bg_color","value":""},{"name":"field_bg_color","value":""},{"name":"label_font_family","value":""},{"name":"field_font_family","value":""},{"name":"label_font_size","value":""},{"name":"field_font_size","value":""},{"name":"label_text_align","value":"left"},{"name":"js_event_edit","value":""},{"name":"js_event_insert","value":""},{"name":"js_event_change","value":""},{"name":"js_event_edit_maxValue","value":""},{"name":"common_js_event_edit_maxValueError","value":""},{"name":"js_event_edit_minValue","value":""},{"name":"common_js_event_edit_minValueError","value":""},{"name":"js_event_edit_inputId","value":""},{"name":"common_js_event_edit_compareError","value":""},{"name":"js_event_edit_toUpperCase","value":""},{"name":"js_event_edit_toLowerCase","value":""},{"name":"js_event_insert_maxValue","value":""},{"name":"common_js_event_insert_maxValueError","value":""},{"name":"js_event_insert_minValue","value":""},{"name":"common_js_event_insert_minValueError","value":""},{"name":"js_event_insert_inputId","value":""},{"name":"common_js_event_insert_compareError","value":""},{"name":"js_event_insert_toUpperCase","value":""},{"name":"js_event_insert_toLowerCase","value":""},{"name":"js_event_change_maxValue","value":""},{"name":"common_js_event_change_maxValueError","value":""},{"name":"js_event_change_minValue","value":""},{"name":"common_js_event_change_minValueError","value":""},{"name":"js_event_change_inputId","value":""},{"name":"common_js_event_change_compareError","value":""},{"name":"js_event_change_toUpperCase","value":""},{"name":"js_event_change_toLowerCase","value":""},{"name":"access_view","value":[]},{"name":"access_update","value":[]}]],"layout_table":{"count":null,"show_type":null,"label_orientation":null,"column_configuration":{}},"layout_configuration":{"params":[],"labels":{},"format_type":{}},"layout_formatting":null}]';

		$tabModel = JobScheduler::getTemplateData($templateLayout);

		//echo '<pre>'; print_r($tabModel);

		Yii::$app->session['tabData'] = $tabModel;

		return $this->render('element_tab', ['selfTab' => $tabModel]);*/

		//return $this->renderAjax('element_tab', ['selfTab' => $tabModel]);
	}
}