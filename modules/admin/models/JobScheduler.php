<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\JobSchedulerForm;
use yii\helpers\ArrayHelper;

use app\components\_TemplateHelper;
use app\components\RenderTabHelper;

use Yii;

class JobScheduler extends BaseModel
{
    public static $dataLib = 'CodiacSDK.zJobsScheduler.dll';
    public static $dataAction = 'GetJobScheduleList';
    public static $formClass = JobSchedulerForm::class;

	//Prepare data for update and create
   protected static function prepareData($attributes, $method = null) {
		echo '<pre> In prepareData function json :: '; print_r($attributes['jobs_params']);

		$jobs_params_arr = json_decode($attributes['jobs_params']);
		echo '<pre> in prepareData jobs_params_arr :: '; print_r($jobs_params_arr);

		$template_layout_arr = json_decode($attributes['template_layout']);

		//$func_param_arr = json_decode($jobs_params_arr->function_extensions_job_params->search_function_info->config->data);
		//echo '<pre> in prepareData func_param_arr :: '; print_r($func_param_arr);

		/*$attributes['jobs_params'][$jobs_params_arr->function_extensions_job_params->lib_name] = json_encode($attributes['jobs_params']);
		$attributes['attributes'] = base64_encode($attributes['jobs_params']);
		ArrayHelper::remove($attributes, 'template_layout');*/

		$libName = $jobs_params_arr->function_extensions_job_params->lib_name;

		$attributes['jobs_params'] = ['function_extensions_job_params' => [$libName => $jobs_params_arr->function_extensions_job_params]];

		$attributes['template_layout'] = [$libName => $template_layout_arr[0]];

		if(!empty($jobs_params_arr->function_extensions_job_params->search_function_info->config)) {
			$funcName = $jobs_params_arr->function_extensions_job_params->search_function_info->config->data_source_get;
			$attributes['jobs_params']['function_extensions_job_params'][$libName]->func_name = $funcName;

			//$attributes['jobs_params']['function_extensions_job_params'][$libName]->search_function_info = ['data' => $jobs_params_arr->function_extensions_job_params->func_param];

			if($libName != '') {
				if($template_layout_arr[0]->alias_framework_function == 'view') {
					$field_name_list = $jobs_params_arr->function_extensions_job_params->search_function_info->config->pk_configuration;

					$field_value_list_arr = json_decode($jobs_params_arr->function_extensions_job_params->search_function_info->data->id);
					$field_value_list = array();

					foreach($field_value_list_arr as $colName => $colVal) {
						$field_value_list[$colName][] = $colVal;
					}

					$field_out_list = array();

					if(!empty($template_layout_arr)) {
						foreach($template_layout_arr[0]->layout_fields as $layout_fields) {
							foreach($layout_fields as $layout_field) {
								if($layout_field->name == 'data_field')
									$field_out_list[] = $layout_field->value;
							}
						}
					}

					//set func_params parameters
					$attributes['jobs_params']['function_extensions_job_params'][$libName]->func_param = ['field_name_list' => $field_name_list, 'field_value_list' => $field_value_list, 'field_out_list' => $field_out_list, 'account_security_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_security_type, 'account_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_type, 'lock_id' => '', 'security1' => '', 'security2' => '', 'tenant_code' => Yii::$app->session['screenData']['app\models\UserAccount']->tenant_code, 'user_document_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->document_group, 'user_emails' => Yii::$app->session['screenData']['app\models\UserAccount']->email, 'user_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->group_area, 'user_name' => Yii::$app->session['screenData']['app\models\UserAccount']->user_name, 'user_phone_number' => ''];
				} else {
					$patch_json = array();

					foreach($template_layout_arr[0]->form_values as $form_value) {
						$patch_json[$form_value->name] = $form_value->value;
					}

					$attributes['jobs_params']['function_extensions_job_params'][$libName]->search_function_info->pk_configuration = $jobs_params_arr->function_extensions_job_params->search_function_info->config->pk_configuration;

					//set func_params parameters
					$attributes['jobs_params']['function_extensions_job_params'][$libName]->func_param = ['patch_json' => $patch_json, 'account_security_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_security_type, 'account_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_type, 'lock_id' => '', 'security1' => '', 'security2' => '', 'tenant_code' => Yii::$app->session['screenData']['app\models\UserAccount']->tenant_code, 'user_document_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->document_group, 'user_emails' => Yii::$app->session['screenData']['app\models\UserAccount']->email, 'user_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->group_area, 'user_name' => Yii::$app->session['screenData']['app\models\UserAccount']->user_name, 'user_phone_number' => ''];
				}
			}
		} else {
			$attributes['jobs_params']['function_extensions_job_params'][$libName]->search_function_info->config = $jobs_params_arr->function_extensions_job_params->search_custom_query;
			$attributes['jobs_params']['function_extensions_job_params'][$libName]->search_custom_query = '';

			if($libName != '') {
				if($template_layout_arr[0]->alias_framework_function == 'view') {
					$field_name_list = array();

					$field_value_list_arr = $jobs_params_arr->function_extensions_job_params->search_function_info->data->id;
					$field_value_list = array();

					foreach($field_value_list_arr as $colName => $colVal) {
						$field_name_list[] = $colName;
						$field_value_list[$colName][] = $colVal;
					}

					$field_out_list = array();

					if(!empty($template_layout_arr)) {
						foreach($template_layout_arr[0]->layout_fields as $layout_fields) {
							foreach($layout_fields as $layout_field) {
								if($layout_field->name == 'data_field')
									$field_out_list[] = $layout_field->value;
							}
						}
					}

					//set func_params parameters
					$attributes['jobs_params']['function_extensions_job_params'][$libName]->func_param = ['field_name_list' => $field_name_list, 'field_value_list' => $field_value_list, 'field_out_list' => $field_out_list, 'account_security_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_security_type, 'account_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_type, 'lock_id' => '', 'security1' => '', 'security2' => '', 'tenant_code' => Yii::$app->session['screenData']['app\models\UserAccount']->tenant_code, 'user_document_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->document_group, 'user_emails' => Yii::$app->session['screenData']['app\models\UserAccount']->email, 'user_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->group_area, 'user_name' => Yii::$app->session['screenData']['app\models\UserAccount']->user_name, 'user_phone_number' => ''];
				} else {
					$patch_json = array();

					foreach($template_layout_arr[0]->form_values as $form_value) {
						$patch_json[$form_value->name] = $form_value->value;
					}

					//set func_params parameters
					$attributes['jobs_params']['function_extensions_job_params'][$libName]->func_param = ['patch_json' => $patch_json, 'account_security_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_security_type, 'account_type' => Yii::$app->session['screenData']['app\models\UserAccount']->account_type, 'lock_id' => '', 'security1' => '', 'security2' => '', 'tenant_code' => Yii::$app->session['screenData']['app\models\UserAccount']->tenant_code, 'user_document_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->document_group, 'user_emails' => Yii::$app->session['screenData']['app\models\UserAccount']->email, 'user_groups' => Yii::$app->session['screenData']['app\models\UserAccount']->group_area, 'user_name' => Yii::$app->session['screenData']['app\models\UserAccount']->user_name, 'user_phone_number' => ''];
				}
			}
		}

		//$attributes['jobs_params'] = base64_encode(json_encode($attributes['jobs_params']));
		//$attributes['template_layout'] = base64_encode(json_encode($attributes['template_layout']));

		/*$temp = array();
		$temp[$jobs_params_arr->function_extensions_job_params->lib_name] = json_encode($attributes['jobs_params']);
		ArrayHelper::remove($temp, 'template_layout');

		echo 'temp json :: '. stripslashes(json_encode($temp));*/
		//echo "<pre> temp array :: "; print_r($temp);

		echo '<pre> In prepareData function array :: '; print_r($attributes);

        return parent::prepareData($attributes);
    }

	public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        if (empty($fieldList)) {
            $fieldList = ['is_active' => [JobSchedulerForm::TYPE_ACTIVE, JobSchedulerForm::TYPE_INACTIVE]];
        }

        return parent::getData($fieldList, $postData, $additionallyParam);
    }

	public static function getModel($id)
    {
        if ($data = parent::getModel($id)) {
            $data->jobs_params = base64_decode($data->jobs_params);
            $data->jobs_params = json_decode($data->jobs_params);

			$data->template_layout = base64_decode($data->template_layout);
            $data->template_layout = json_decode($data->template_layout);
        }

        return $data;
    }

	/**
     * Getting data from API server
     * @param array $fieldList
     * @param array $postData
     * @param array $additionallyParam
     * @return static
     */
    public static function getTemplateData($templateLayout)
    {
		//echo $templateLayout;

        $fieldsData = [];
        $listItem = [];
		$fieldsList = [];

		//$listItem = array('screen_lib' => 'CodiacSDK.Universal.dll');
		//$listItem['screen_lib'] = 'CodiacSDK.Universal.dll';

        if (!empty($templateLayout)) {
			$listItem = [
				'screen_lib' => 'CodiacSDK.Universal.dll',
				'lib' => 'CodiacSDK.Universal.dll',
				'tpl' => (object) ['template_layout' => empty($templateLayout) ? null : (object) self::decodeTemplate($templateLayout, false)]
			];

			/*if (!isset($fieldsData[$listItem['screen_lib']])) {
				$fieldsData[$listItem['screen_lib']] = [];
			}*/

			//echo "<pre>"; print_r($listItem['tpl']); die;

			//echo 'Try :: '.$listItem['screen_lib'];

			//self::getFieldsArrayFromTpl($listItem['tpl'], $fieldsData[$listItem['screen_lib']]);

			//echo "<pre>"; print_r($fieldsList); die;
		}

        /*if (!empty($model) && property_exists($model, 'fieldsData')) {
            $model->fieldsData = $fieldsData;
            $model->fieldsAccess = self::getFieldsAccess($fieldsData);
        }*/

        return $listItem['tpl']->template_layout;
    }

	/**
     * Getting fields for render from template
     * @param _TemplateHelper $tpl
     * @param array $fieldsList
     */
    private static function getFieldsArrayFromTpl($tpl, &$fieldsList)
    {
		//echo '<pre> in getFieldsArrayFromTpl :: '; print_r($tpl);

		//die;

        //if (!empty($tpl) && property_exists($tpl, 'template_layout')) {

            foreach ($tpl->template_layout as $layout) {
				$funcName = $layout['data_source_get'];
                //$funcName = 'funcName';

                if (!isset($fieldsList[$funcName])) $fieldsList[$funcName] = [];

                if ($layout['layout_type'] == RenderTabHelper::SECTION_TYPE_LIST && !empty($layoutp['layout_fields'])) {
                    foreach ($layout['layout_fields'] as $fld) {
                        $fieldName = null;
                        foreach ($fld as $fldAttr) {
                            if ($fldAttr['name'] == 'data_field') {
                                $fieldName = CommandData::fixedApiResult($fldAttr['value'], true);
                                if ($fieldName != '' && !in_array($fieldName, $fieldsList[$funcName])) {
                                    $fieldsList[$funcName][] = $fieldName;
                                }
                            }
                        }
                    }
                } elseif (in_array($layout['layout_type'], [RenderTabHelper::SECTION_TYPE_GRID, RenderTabHelper::SECTION_TYPE_CHART_PIE, RenderTabHelper::SECTION_TYPE_CHART_DOUGHNUT])) {
                    foreach ($layout['layout_configuration']['params'] as $fld) {
                        $fieldName = CommandData::fixedApiResult($fld, true);
                        if ($fieldName != '' && !in_array($fieldName, $fieldsList[$funcName])) {
                            $fieldsList[$funcName][] = $fieldName;
                        }
                    }
                } elseif (in_array($layout['layout_type'], [RenderTabHelper::SECTION_TYPE_CHART_LINE, RenderTabHelper::SECTION_TYPE_CHART_BAR_HORIZONTAL, RenderTabHelper::SECTION_TYPE_CHART_BAR_VERTICAL])) {
                    foreach ($layout['layout_configuration']['params'] as $fldGroup) {
                        foreach ($fldGroup as $fld) {
                            $fieldName = CommandData::fixedApiResult($fld, true);
                            if ($fieldName != '' && !in_array($fieldName, $fieldsList[$funcName])) {
                                $fieldsList[$funcName][] = $fieldName;
                            }
                        }
                    }
                }
            }

        //}
    }

    /**
     * Getting rights for fields
     * @param array $fieldsList
     * @return array
     */
    private static function getFieldsAccess($fieldsList) {
        $access = [];
        if (!empty($fieldsList)) {
            foreach($fieldsList as $lib => $functions) {
                $access[$lib] = [];
                foreach($functions as $funcName => $fields) {
                    $access[$lib][$funcName] = AliasList::getAlias($lib,$funcName, $fields);
                }
            }
        }
        return $access;
    }

    /**
     * Decode template of tab
     * @param string $template
     * @param bool $getObject
     * @return _TemplateHelper|array
     */
    public static function decodeTemplate($template, $getObject = false)
    {
        //$templateJson = base64_decode($template);
        //$templateArray = json_decode($templateJson, true);

        $templateArray = json_decode($template, true);

        return ($getObject) ? _TemplateHelper::run($templateArray) : (object) $templateArray;
    }
}