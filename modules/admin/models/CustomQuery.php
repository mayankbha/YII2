<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use Yii;
use app\models\BaseModel;
use app\modules\admin\models\forms\CustomQueryForm;

class CustomQuery extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.Dll';
    public static $dataAction = 'GetCustomQueryList';
    public static $formClass = CustomQueryForm::class;

    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        if (($data = parent::getData($fieldList, $postData, $additionallyParam)) && !empty($data->list)) {
            foreach($data->list as $key => $item) {
                $data->list[$key]['query_value'] = base64_decode($item['query_value']);
            }
        }

        return $data;
    }

    protected static function prepareData($attributes, $method = null) {
        $attributes['query_value'] = base64_encode($attributes['query_value']);
        return parent::prepareData($attributes);
    }
}