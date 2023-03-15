<?php

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\models\GetListList;
use app\modules\admin\models\forms\SecurityFilterForm;

class SecurityQuestions extends GetListList
{
    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        $fieldList = (empty($fieldList)) ? ['list_name' => [self::BASE_NAME_SECURITY_QUESTIONS]] : $fieldList;
        return parent::getData($fieldList, $postData, $additionallyParam);
    }
}