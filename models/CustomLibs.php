<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

class CustomLibs extends AccountModel
{
    const DIRECTION_SETTER = 'SETTER';
    const DIRECTION_GETTER = 'GETTER';
    const LAYOUT_TYPE_MULTI_SEARCH = 'MULTI-SEARCH';

    public static $disabled_layout_types = [
        self::LAYOUT_TYPE_MULTI_SEARCH
    ];

    public static $directionTypes = [
        self::DIRECTION_SETTER,
        self::DIRECTION_GETTER,
        self::LAYOUT_TYPE_MULTI_SEARCH
    ];

    public static $dataAction = 'GetSDKList';

    /**
     * Getting function list of library
     * @param string $libName
     * @param bool $direction - Type of function for searching
     * @param string|bool $type
     *
     * @return array
     */
    public static function getLibFuncList($libName, $direction = false, $type = false)
    {
        $result = [];
        $libData = self::getModelInstance();

        if (!empty($libData) && !empty($libData->lib_list)) {
            foreach ($libData->lib_list as $val) {
                if (strtolower($val['lib_name']) == strtolower($libName)) {
                    if ($direction === self::DIRECTION_SETTER || $direction === self::DIRECTION_GETTER) {
                        foreach ($val['lib_func_list'] as $key => $item) {
                            if ($item['func_direction_type'] != $direction) unset($val['lib_func_list'][$key]);
                            else if (in_array($item['func_layout_type'], self::$disabled_layout_types)) unset($val['lib_func_list'][$key]);
                        }
                    } else if ($direction === self::LAYOUT_TYPE_MULTI_SEARCH) {
                        foreach ($val['lib_func_list'] as $key => $item) {
                            if ($item['func_layout_type'] != $direction) unset($val['lib_func_list'][$key]);
                        }
                    }
                    $result = $val['lib_func_list'];
                    break;
                }
            }

            if (!empty($result) && $type) {
                $result = array_filter($result, function($v, $k) use ($type) {
                    return $v['func_type'] === $type;
                }, ARRAY_FILTER_USE_BOTH);
            }
        }
        return $result;
    }

    public static function getTableName($libName, $functionGetName)
    {
        $libFunctionList = self::getLibFuncList($libName);

        foreach ($libFunctionList as $libInfo) {
            if ($libInfo['func_name'] == $functionGetName) {
                return $libInfo['func_table'];
            }
        }

        return null;
    }

    public static function getFunctionName($libName, $functionGetName, $type)
    {
        $tableName = self::getTableName($libName, $functionGetName);

        $libFunctionList = CustomLibs::getLibFuncList($libName, CustomLibs::DIRECTION_SETTER);

        foreach ($libFunctionList as $libInfo) {
            if ($libInfo['func_type'] == $type && $libInfo['func_table'] == $tableName) {
                return $libInfo['func_name'];
            }
        }

        return null;
    }

    public static function getPK($libName, $functionName) {
        $tableName = self::getTableName($libName, $functionName);
        $postData = [
            "func_name" => "GetPKS",
            "func_param" => [
                "table_name" => $tableName
            ]
        ];

        $result = (new static())->processData($postData);

        if (!empty($result['record_list']['PK'])) return explode(';', $result['record_list']['PK']);
        return null;
    }
}