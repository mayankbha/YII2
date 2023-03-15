<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\models\CustomLibs;
use app\modules\admin\models\forms\DocumentFamilyForm;
use app\modules\admin\models\forms\GroupForm;
use yii\helpers\ArrayHelper;

class DocumentFamily extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminGroups.dll';
    public static $dataAction = 'GetDocumentFamilyList';
    public static $formClass = DocumentFamilyForm::class;

    public static function getParentData()
    {
        return parent::getData();
    }

    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        $temp_key_data = [];
        $temp_data = [];

        if ($model = parent::getData($fieldList = [], $postData = [], $additionallyParam = [])) {
            if (!empty($model->list)) {
                foreach ($model->list as $key => $value) {
                    if (!empty($value['family_name'])) {
                        $model->list[$key]['pk'] = $value['family_name'];
                    }
                }

                foreach ($model->list as $i => $val) {
                    if (!in_array($val['family_name'], $temp_key_data)) {
                        $temp_key_data[$i] = $val['family_name'];
                        $temp_data[$i] = $val;
                    }
                }
            }
            $model->list = $temp_data;
        }

        return $model;
    }

    public static function getModels($pk)
    {
        $result = [];
        $model = parent::getData(['family_name' => [$pk]]);
        if (!empty($model->list)) {
            foreach ($model->list as $item) {
                $resultModel = new static::$formClass();
                if ($resultModel->load($item, '')) {
                    $result[] = $resultModel;
                }
            }
        }

        return $result;
    }

    public static function getDistinctFamilies()
    {
        $model = parent::getData();
        $result = [];

        if (!empty($model->list)) {
            foreach($model->list as $item) {
                $result[$item['family_name']] = [
                    'family_name' => $item['family_name'],
                    'family_description' => $item['family_description'],
                    'categories' => []
                ];
                foreach($model->list as $subItem) {
                    $result[$subItem['family_name']]['categories'][$subItem['category']] = [
                        'category' => $subItem['category'],
                        'category_description' => $subItem['category_description']
                    ];
                }
            }
        }

        return $result;
    }
}