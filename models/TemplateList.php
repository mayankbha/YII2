<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

class TemplateList extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetTemplateList';

    //Override method for prepare returned from API server values
    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        $additionallyParam = ['field_out_list' => ['alias_field']];
        if ($data = parent::getData($fieldList, $postData, $additionallyParam)) {
            foreach ($data->list as $key => $list) {
                unset($data->list[$key]['pk']);
            }
        }

        return $data;
    }
}