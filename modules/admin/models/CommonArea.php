<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\MenuForm;

class CommonArea extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.Dll';
    public static $dataAction = 'ScreenList';

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


        $model = new static();
        $attributes = $model->processData($postData);
        $model->list = $attributes['record_list'];

        if (!empty($model->list)) {
            return $model;
        }
        return null;
    }
}