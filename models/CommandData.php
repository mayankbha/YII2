<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use app\models\services\RecordAccess;
use app\models\services\RecordAccessAliasFramework;
use app\models\services\RecordData;
use app\models\services\RecordSubData;
use app\models\services\RecordManager;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class CommandData extends BaseModel2
{
    const SEARCH_LIMIT = 15;
    const SEARCH_TYPE_DEFAULT = 1;
    const SEARCH_TYPE_CUSTOM_QUERY = 2;

    const SEARCH_CONFIG_CACHE_NAME = 'searchConfig';

    const SEARCH_FUNC_TYPE = 'Search';
    const INSERT_FUNC_TYPE = 'Create';
    const UPDATE_FUNC_TYPE = 'Update';
    const DELETE_FUNC_TYPE = 'Delete';

    public static $insertSubFuncName = 'CreateSub';
    public static $updateSubFuncName = 'UpdateSub';
    public static $deleteSubFuncName = 'DeleteSub';

    public static function search($library, $queries, $search_function_info, $search_custom_query)
    {
        if (!empty($search_custom_query)) {
            return self::searchCustom($queries, $search_custom_query);
        } elseif (!empty($search_function_info)) {
            return self::searchDefault($library, $queries, $search_function_info);
        }

        return [];
    }

    public static function searchDefault($library, array $queries, $config, array $aliasFrameworkInfo = [], array $additionalParams = [])
    { //echo $library; echo "<pre>"; print_r($queries); echo "<pre>"; print_r($config);
        if (empty($config->func_inparam_configuration) || empty($config->data_source_get)) {
            return [];
        }

        $outParams = [];
        $searchMaskList = [];
        $isAliasFramework = isset($aliasFrameworkInfo['enable']) && $aliasFrameworkInfo['enable'];

        foreach ($queries as $key => $mask) {
            if (!is_array($mask)) {
                $mask = [$mask];
            }

            $outParams[] = $key;
            $searchMaskList[$key] = $mask;
        }

        if ($isAliasFramework && !empty($config->pk_configuration) && is_array($config->pk_configuration)) {
            $primaryKeys = $config->pk_configuration;
        } else {
            $primaryKeys = CustomLibs::getPK($library, $config->data_source_get);
        }

		//echo "<pre>"; print_r($primaryKeys);

        foreach ($config->func_inparam_configuration as $key => $value) {
            $config->func_inparam_configuration[$key] = self::fixedApiResult($value, $isAliasFramework);
        }

        if (!empty($primaryKeys)) {
            $outParams = array_merge($outParams, $primaryKeys);
        }

		//echo "<pre>"; print_r($outParams);

        $outParams = array_values(array_unique($outParams));

        $additionalParams = array_merge([
            'limitnum' => self::SEARCH_LIMIT,
            'search_mask_list' => $searchMaskList,
            'field_out_list' => $outParams
        ], $additionalParams);

        $postData = [
            'lib_name' => $library,
            'func_name' => $config->data_source_get
        ];

        if ($isAliasFramework) {
            $postData['alias_framework_info'] = $aliasFrameworkInfo;
        }

        $response = self::getData(array_keys($queries), $postData, $additionalParams);

		//echo "<pre>"; print_r($response);

        if (!empty($response->list) && !empty($primaryKeys)) {
            foreach ($response->list as $listKey => $item) {
                $response->list[$listKey]['id'] = [];
                foreach ($primaryKeys as $pk) {
                    $response->list[$listKey]['id'][$pk] = $item[$pk];
                }

                $response->list[$listKey]['id'] = json_encode($response->list[$listKey]['id']);
            }

			//echo "<pre>"; print_r($response);

            return $response->list;
        }

		//echo "<pre>"; print_r($response); die;

		//die;

		return [];
    }

    public static function searchCustom(array $queries, $config, $returnAllRecords = false)
    { //echo "<pre>"; print_r($queries); echo "<pre>"; print_r($config);
        if (empty($config->query_pk)) {
            return [];
        }
        $library = 'CodiacSDK.CommonArea.dll';
        $getQueriesAction = 'GetCustomQueryList';
        $executeAction = 'ExecuteCustomQuery';

        $model = self::getModel($config->query_pk, [
            'lib_name' => $library,
            'func_name' => $getQueriesAction
        ]);

		//echo "<pre>"; print_r($model);

        if (!empty($model['query_params'])) {
            $queryParams = explode(',', $model['query_params']);
            foreach ($queryParams as $param) {
                $param = trim($param);
                if (empty($queries[$param])) {
                    $queries[$param] = '';
                }
            }
        }

        $sqlParams = [];
        foreach ($queries as $key => $item) {
            $sqlParams[":$key"] = $item;
        }

        if (empty($sqlParams)) {
            $sqlParams['empty'] = 'true';
        }

        $response = (new static())->processData([
            "lib_name" => $library,
            'func_name' => $executeAction,
            "func_param" => [
                "PK" => $config->query_pk,
                "sql_params" => $sqlParams
            ]
        ]);

		//echo "<pre>"; print_r($response);

        $result = [];

        if (!empty($response['record_list'])) {
            if (!$returnAllRecords) {
                foreach ($response['record_list'] as $key => $item) {
                    if (!empty($config->alias_query_pk) && !empty($item['id'])) {
                        $result[$key]['id'] = ['id' => $item['id']];
                    } else {
                        $pkList = [];
                        foreach ($config->alias_query_pk as $pkName) {
                            if ($dotPosition = strrpos($pkName, '.')) {
                                $pkName = substr($pkName, $dotPosition + 1, strlen($pkName));
                            }
                            $pkList[$pkName] = (isset($item[$pkName])) ? $item[$pkName] : null;
                        }
                        $result[$key]['id'] = $pkList;
                    }

                    foreach ($queries as $param => $value) {
                        $pkName = $param;
                        if ($dotPosition = strrpos($param, '.')) {
                            $pkName = substr($param, $dotPosition + 1, strlen($param));
                        }

                        $result[$key][$param] = isset($item[$pkName]) ? $item[$pkName] : null;
                    }
                }
            } else {
                $result = $response['record_list'];
            }
        }

		//echo "<pre>"; print_r($result); die;

		return $result;
    }

    /**
     * Update data on API server
     *
     * @param RecordData $data
     * @param RecordSubData $updateSubData
     * @param RecordSubData $deleteSubData
     * @param RecordSubData $insertSubData
     *
     * @return mixed
     */
    public static function update(RecordData $data, RecordSubData $updateSubData, RecordSubData $insertSubData, RecordSubData $deleteSubData)
    {
        $model = new static();
        $tpl = Yii::$app->session['tabData']->getSelectTpl();

        $mainData = $data->getData();
        if ($data->recordManager->isUseAliasFramework()) {
            $updateSubData->setMainData($mainData);
            $insertSubData->setMainData($mainData);
            $deleteSubData->setMainData($mainData);

            $mainData = array_merge_recursive(
                $mainData,
                $updateSubData->getUpdateDataAF(RecordManager::ITEM_ATTR_UPDATE_FUNC),
                $insertSubData->getUpdateDataAF(RecordManager::ITEM_ATTR_UPDATE_FUNC),
                $deleteSubData->getUpdateDataAF(RecordManager::ITEM_ATTR_UPDATE_FUNC)
            );
        }

        foreach ($mainData as $function => $items) {
            $request = [
                "lib_name" => $data->recordManager->getLibrary(),
                "func_param" => [
                    "patch_json" => $items
                ]
            ];

            if ($data->recordManager->isUseAliasFramework()) {
                $request['alias_framework_info'] = $data->recordManager->getAliasFrameworkInfo();
                $request['func_name'] = $function;
                $request['func_param']['PK'] = $data->recordManager->getAliasFrameworkPK();
            } else {
                $request['func_name'] = CustomLibs::getFunctionName($data->recordManager->getLibrary(), $function, self::UPDATE_FUNC_TYPE);
                $request['func_param']['PK'] = $data->recordManager->getPK();
            }

            if (!empty($tpl['tpl']->screen_extensions['add']['pre'][$function])) {
                $request['func_param']['func_extensions_pre'] = [$tpl['tpl']->screen_extensions['edit']['pre'][$function]];
            }
            if (!empty($tpl['tpl']->screen_extensions['add']['post'][$function])) {
                $request['func_param']['func_extensions_post'] = [$tpl['tpl']->screen_extensions['edit']['post'][$function]];
            }

            $result = $model->processData($request);

            if ($data->recordManager->isUseAliasFramework()) {
                RecordAccessAliasFramework::unlock($data->recordManager);
            } else {
                self::updateSub($updateSubData);
                self::insertSub($insertSubData);
                self::deleteSub($deleteSubData);
                RecordAccess::unlock($data->recordManager, $function);
            }

            if ($result['requestresult'] != 'successfully') {
                return null;
            }
        }

        return true;
    }

    /**
     * Insert data to library on API server
     *
     * @param RecordData $data
     * @param RecordSubData $subData
     *
     * @return array|null
     */
    public static function insert(RecordData $data, RecordSubData $subData)
    {
        $model = new static();
        $tpl = Yii::$app->session['tabData']->getSelectTpl();

        $mainData = $data->getData();
        if ($data->recordManager->isUseAliasFramework()) {
            $subData->setMainData($mainData);
            $mainData = array_merge_recursive($mainData, $subData->getUpdateDataAF(RecordManager::ITEM_ATTR_CREATE_FUNC));
        }

        foreach ($mainData as $function => $items) {
            $request = [
                "lib_name" => $data->recordManager->getLibrary(),
                "func_param" => [
                    "patch_json" => $items
                ]
            ];

            if ($data->recordManager->isUseAliasFramework()) {
                $request['alias_framework_info'] = $data->recordManager->getAliasFrameworkInfo();
                $request['func_name'] = $function;

                if (!empty($tpl['tpl']->search_configuration)) {
                    $request['search_function_info'] = $tpl['tpl']->search_configuration;
                }
            } else {
                $request['func_name'] = CustomLibs::getFunctionName($data->recordManager->getLibrary(), $function, self::INSERT_FUNC_TYPE);
            }

            if (!empty($tpl['tpl']->screen_extensions['add']['pre'][$function])) {
                $request['func_param']['func_extensions_pre'] = [$tpl['tpl']->screen_extensions['add']['pre'][$function]];
            }
            if (!empty($tpl['tpl']->screen_extensions['add']['post'][$function])) {
                $request['func_param']['func_extensions_post'] = [$tpl['tpl']->screen_extensions['add']['post'][$function]];
            }

            $result = $model->processData($request);
            if ($result['requestresult'] != 'successfully') {
                return null;
            }
        }

        if (!empty($result['record_list']['PK'])) {
            $insertedPK = explode(RecordManager::PK_DELIMITER, $result['record_list']['PK']);
            if (!$data->recordManager->isUseAliasFramework()) {
                $subData->recordManager->setPK($insertedPK);
                self::insertSub($subData);
                $pkFields = CustomLibs::getPK($data->recordManager->getLibrary(), $function);
            } else {
                $pkFields = $tpl['tpl']->search_configuration->pk_configuration;
            }

            if (isset($function) && !empty($pkFields)) {
                $pk = [];
                foreach ($pkFields as $i => $fieldName) {
                    $pk[$fieldName] = $insertedPK[$i];
                }

                return $pk;
            }
        }

        return null;
    }

    /**
     * Delete data from api server
     *
     * @param RecordManager $recordManager
     * @param array $functionInfo
     *
     * @return mixed
     */
    public static function delete(RecordManager $recordManager, array $functionInfo)
    {
        if (empty($functionInfo['get']) || ($recordManager->isUseAliasFramework() && empty($functionInfo['delete']))) {
            return false;
        }

        $model = new self();
        $tpl = Yii::$app->session['tabData']->getSelectTpl();
        $request = [
            "lib_name" => $recordManager->getLibrary()
        ];

        if ($recordManager->isUseAliasFramework()) {
            $request['func_name'] = $functionInfo['delete'];
            $request['func_param']['PK'] = $recordManager->getAliasFrameworkPK();
            $request['alias_framework_info'] = $recordManager->getAliasFrameworkInfo();
        } else {
            $request['func_name'] = CustomLibs::getFunctionName($recordManager->getLibrary(), $functionInfo['get'], self::DELETE_FUNC_TYPE);
            $request['func_param']['PK'] = $recordManager->getPK();
        }

        if (!empty($tpl['tpl']->screen_extensions['delete']['pre'][$request['func_name']])) {
            $request['func_param']['func_extensions_pre'] = [$tpl['tpl']->screen_extensions['delete']['pre'][$request['func_name']]];
        }
        if (!empty($tpl['tpl']->screen_extensions['delete']['post'][$request['func_name']])) {
            $request['func_param']['func_extensions_post'] = [$tpl['tpl']->screen_extensions['delete']['post'][$request['func_name']]];
        }

        $result = $model->processData($request);
        if ($result['requestresult'] != 'successfully') {
            return null;
        }

        return true;
    }

    /**
     * Update children elements of data on API server
     *
     * @param RecordSubData $data
     *
     * @return mixed
     */
    public static function updateSub(RecordSubData $data)
    {
        $subData = $data->getData();
        if ($data->recordManager->isUseAliasFramework() || empty($subData) || empty($data->recordManager->getPK())) {
            return false;
        }

        $model = new self();
        $tpl = Yii::$app->session['tabData']->getSelectTpl();

        foreach ($subData as $getFunction => $row) {
            foreach ($row as $subPK => $items) {
                $updateFunction = CustomLibs::getFunctionName($data->recordManager->getLibrary(), $getFunction, self::UPDATE_FUNC_TYPE);
                if ($relatedField = CustomLibs::getRelated($data->recordManager->getLibrary(), $getFunction)) {
                    $items[$relatedField] = $data->recordManager->getPK();
                }

                $request = [
                    "lib_name" => $data->recordManager->getLibrary(),
                    'func_name' => $updateFunction,
                    "func_param" => [
                        "PK" => (string)$subPK,
                        "patch_json" => $items
                    ]
                ];

                if (!empty($tpl['tpl']->screen_extensions['add']['pre'][$getFunction])) {
                    $request['func_param']['func_extensions_pre'] = [$tpl['tpl']->screen_extensions['edit']['pre'][$getFunction]];
                }
                if (!empty($tpl['tpl']->screen_extensions['add']['post'][$getFunction])) {
                    $request['func_param']['func_extensions_post'] = [$tpl['tpl']->screen_extensions['edit']['post'][$getFunction]];
                }

                $result = $model->processData($request);
                if ($result['requestresult'] != 'successfully') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Insert children elements of data on API server
     *
     * @param RecordSubData $data
     *
     * @return boolean
     */
    public static function insertSub(RecordSubData $data)
    {
        $subData = $data->getData();
        if ($data->recordManager->isUseAliasFramework() || empty($subData) || empty($data->recordManager->getPK())) {
            return false;
        }

        $model = new self();
        $tpl = Yii::$app->session['tabData']->getSelectTpl();

        foreach ($subData as $getFunction => $row) {
            foreach ($row as $items) {
                $createFunction = CustomLibs::getFunctionName($data->recordManager->getLibrary(), $getFunction, self::INSERT_FUNC_TYPE);
                if ($relatedField = CustomLibs::getRelated($data->recordManager->getLibrary(), $getFunction)) {
                    $items[$relatedField] = $data->recordManager->getPK();
                }

                $request = [
                    "lib_name" => $data->recordManager->getLibrary(),
                    'func_name' => $createFunction,
                    "func_param" => [
                        "patch_json" => $items
                    ]
                ];

                if (!empty($tpl['tpl']->screen_extensions['add']['pre'][$getFunction])) {
                    $request['func_param']['func_extensions_pre'] = [$tpl['tpl']->screen_extensions['add']['pre'][$getFunction]];
                }
                if (!empty($tpl['tpl']->screen_extensions['add']['post'][$getFunction])) {
                    $request['func_param']['func_extensions_post'] = [$tpl['tpl']->screen_extensions['add']['post'][$getFunction]];
                }

                $result = $model->processData($request);
                if ($result['requestresult'] != 'successfully') {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Delete children elements of data on API server
     *
     * @param RecordSubData $data
     *
     * @return mixed
     */
    public static function deleteSub(RecordSubData $data)
    {
        $subData = $data->getDeleteData();
        if ($data->recordManager->isUseAliasFramework() || empty($subData)) {
            return false;
        }

        $model = new self();
        $tpl = Yii::$app->session['tabData']->getSelectTpl();

        foreach ($subData as $getFunction => $items) {
            foreach ($items as $pk) {
                $deleteFunction = CustomLibs::getFunctionName($data->recordManager->getLibrary(), $getFunction, self::DELETE_FUNC_TYPE);
                $request = [
                    "lib_name" => $data->recordManager->getLibrary(),
                    'func_name' => $deleteFunction,
                    "func_param" => [
                        "PK" => (string)$pk
                    ]
                ];

                if (!empty($tpl['tpl']->screen_extensions['add']['pre'][$getFunction])) {
                    $request['func_param']['func_extensions_pre'] = [$tpl['tpl']->screen_extensions['delete']['pre'][$getFunction]];
                }
                if (!empty($tpl['tpl']->screen_extensions['add']['post'][$getFunction])) {
                    $request['func_param']['func_extensions_post'] = [$tpl['tpl']->screen_extensions['delete']['post'][$getFunction]];
                }

                $result = $model->processData($request);
                if ($result['requestresult'] != 'successfully') {
                    return false;
                }
            }
        }

        return true;
    }

    public static function fixedApiResult($str, $isAliasFramework = false)
    {
        $position = strrpos($str, '.');
        if (substr_count($str, '.') > 1 && $isAliasFramework) {
            $position = false;
        }

        if ($position !== false) {
            return substr($str, $position + 1, strlen($str));
        }

        return str_replace(['search_mask_list:', 'field_name_list:', 'record_list:'], '', $str);
    }

    public static function getFieldListForQuery($id, $relatedField = null)
    {
        $fieldList = [];
        $pk = (is_array($id)) ? $id : Json::decode($id, true);

        if (!empty($id)) {
            if ($relatedField) {
                $pk = implode(RecordManager::PK_DELIMITER, $pk);
                $fieldList[$relatedField] = [$pk];
            } elseif (is_array($pk)) {
                foreach ($pk as $key => $item) {
                    $fieldList[$key] = [$item];
                }
            }
        }

        return $fieldList;
    }

    public static function getFieldOutListForQuery($libName, $functionName, array $subDataPK = [])
    {
        $additionalParam = [];
        if (isset(Yii::$app->session['tabData'])) {
            $additionalParam['field_out_list'] = Yii::$app->session['tabData']->fieldsData[$libName][$functionName];
            foreach ($subDataPK as $item) {
                if (!in_array($item, $additionalParam['field_out_list'])) {
                    $additionalParam['field_out_list'][] = $item;
                }
            }
        }

        return $additionalParam;
    }

    public static function getGridFieldOutListForQuery(\stdClass $configuration, array $subDataPK = [])
    {
        $additionalParam = [];
        if (!empty($configuration->layout_configuration->params)) {
            $additionalParam['field_out_list'] = $configuration->layout_configuration->params;
        }
        foreach ($subDataPK as $item) {
            if (!in_array($item, $additionalParam['field_out_list'])) {
                $additionalParam['field_out_list'][] = $item;
            }
        }

        //Fix for button type column
        foreach($additionalParam['field_out_list'] as $key => $field) {
            if (preg_match('/__[\w]+/', $field)) {
                unset($additionalParam['field_out_list'][$key]);
            }
        }
        $additionalParam['field_out_list'] = array_values($additionalParam['field_out_list']);

        return $additionalParam;
    }
}