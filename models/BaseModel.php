<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use app\modules\admin\models\forms\DocumentFamilyForm;
use app\modules\admin\models\forms\DocumentGroupForm;
use app\modules\admin\models\forms\GroupForm;
use app\modules\admin\models\forms\GroupScreenForm;
use app\modules\admin\models\forms\MenuForm;
use app\modules\admin\models\forms\ScreenForm;
use app\modules\admin\models\forms\UserForm;
use Yii;

class BaseModel extends AccountModel
{
    const INSERT_FUNC_TYPE = 'Create';
    const UPDATE_FUNC_TYPE = 'Update';
    const DELETE_FUNC_TYPE = 'Delete';
    const SEARCH_FUNC_TYPE = 'Search';
    const GET_FUNC_TYPE = 'GetList';

    public static $functionTypes = [
        self::INSERT_FUNC_TYPE,
        self::UPDATE_FUNC_TYPE,
        self::DELETE_FUNC_TYPE,
        self::SEARCH_FUNC_TYPE,
        self::GET_FUNC_TYPE
    ];

    public $list;
    public static $dataLib = '';
    public static $dataAction = '';
    public static $formClass = __CLASS__;
    public static $defaultSort;

    //Change controller of API server
    protected static function getSourceLink()
    {
        if (!empty(Yii::$app->session['apiEndpointCustom'])) {
            return Yii::$app->session['apiEndpointCustom'];
        }
        return (YII_ENV == 'dev') ? Yii::$app->params['apiEndpointCustomDev'] : Yii::$app->params['apiEndpointCustom'];
    }

    /**
     * Getting data from API server
     * @param array $fieldList - Data searching by this parameter
     * @param array $postData - Optional parameter. Changing library name and function name
     * @param array $additionallyParam - Example: ['field_out_list' => ['out_param_1', 'out_param_2']]
     * @return null|static
     */
    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        $isNotAssoc = count($fieldList, COUNT_RECURSIVE) == count($fieldList);

        if (empty($postData)) $postData = [
            'lib_name' => static::$dataLib,
            'func_name' => static::$dataAction
        ];

        if (empty($fieldList)) {
            $funcParam = [
                'field_name_list' => [],
                'field_value_list' => []
            ];
        } else if ($isNotAssoc) {
            $funcParam = ['field_name_list' => $fieldList];
        } else {
            $funcParam = [
                'field_name_list' => array_keys($fieldList),
                'field_value_list' => $fieldList
            ];
        }
        $funcParam += $additionallyParam;
        $postData += ['func_param' => $funcParam];

        $pkList = [];
        if (!empty($postData['lib_name']) && !empty($postData['func_name'])) {
            $pkList = CustomLibs::getPK($postData['lib_name'], $postData['func_name']);
        }

        $model = new static();
        $attributes = $model->processData($postData);
        $model->list = $attributes['record_list'];

        if (!empty($model->list)) {
            if (!empty($pkList) && !empty($postData['lib_name']) && !empty($postData['func_name'])) {
                foreach ($model->list as $key => $value) {
                    $pk = [];
                    foreach ($pkList as $item) {
                        if (!empty($value[$item])) $pk[] = $value[$item];
                    }
                    if ($pk = implode(';', $pk)) {
                        $model->list[$key]['pk'] = $pk;
                    }
                }
            }
            return $model;
        }
        return null;
    }

    /**
     * @param $pk
     * @return GroupForm|GroupScreenForm|MenuForm|ScreenForm|UserForm|null
     */
    public static function getModel($pk)
    {
        $fieldList = [];
        $pk = explode(';', $pk);
        $pkList = CustomLibs::getPK(static::$dataLib, static::$dataAction);
        if (!empty($pkList)) {
            foreach ($pkList as $key => $item) {
                if (empty($pk[$key])) {
                    continue;
                }

                $fieldList[$item] = [$pk[$key]];
            }

            /* @var $model  GroupForm|GroupScreenForm|MenuForm|ScreenForm|UserForm */
            if ($selfModel = static::getData($fieldList)) {
                $model = new static::$formClass();
                if ($model->load($selfModel->list[0], '')) {
                    return $model;
                }
            }
        }

        return null;
    }

    /**
     * @param $model GroupForm|GroupScreenForm|MenuForm|ScreenForm|UserForm|DocumentGroupForm|DocumentFamilyForm
     * @return bool|mixed|null|string
     */
    public static function setModel($model)
    {
        $attributes = static::prepareData($model->attributes, __FUNCTION__);
        $functionName = CustomLibs::getFunctionName(static::$dataLib, static::$dataAction, self::INSERT_FUNC_TYPE);

		//echo "<pre> in set model function json :: "; print_r(json_encode($attributes['jobs_params']));
		//echo "<pre> in set model function array :: "; print_r($attributes); die;

        return (new static())->processData([
            'lib_name' => static::$dataLib,
            'func_name' => $functionName,
            'func_param' => [
                'patch_json' => $attributes
            ]
        ]);
    }

    /**
     * @param $id
     * @param $model GroupForm|GroupScreenForm|MenuForm|ScreenForm|UserForm
     * @return bool|mixed|null|string
     */
    public static function updateModel($id, $model)
    {
        $attributes = static::prepareData($model->attributes, __FUNCTION__);
        $functionName = CustomLibs::getFunctionName(static::$dataLib, static::$dataAction, self::UPDATE_FUNC_TYPE);

        return (new static())->processData([
            'lib_name' => static::$dataLib,
            'func_name' => $functionName,
            'func_param' => [
                'PK' => (string) $id,
                'patch_json' => $attributes
            ]
        ]);
    }

    public static function deleteModel($id)
    {
        $functionName = CustomLibs::getFunctionName(static::$dataLib, static::$dataAction, self::DELETE_FUNC_TYPE);
        return (new static())->processData([
            'lib_name' => static::$dataLib,
            'func_name' => $functionName,
            'func_param' => [
                'PK' => (string)$id
            ]
        ]);
    }

    /**
     * @param $attributes
     * @param string|null $method
     * @return mixed
     */
    protected static function prepareData($attributes, $method = null)
    {
        unset($attributes['id']);
        unset($attributes['pk']);
        return $attributes;
    }
}