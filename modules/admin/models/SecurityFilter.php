<?php

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\SecurityFilterForm;

class SecurityFilter extends BaseModel
{
    public static $dataLib = 'CodiacSDK.SecurityFilter.dll';
    public static $dataAction = 'GetSecurityFilterList';
    public static $formClass = SecurityFilterForm::class;

    public static function getModel($pk)
    {
        /** @var SecurityFilterForm|null $model */
        if ($model = parent::getModel($pk)) {
            $model->secret_questions = explode(';', $model->secret_questions);
            $model->auth_types = explode(';', $model->auth_types);
        }

        return $model;
    }

    protected static function prepareData($attributes, $method = null)
    {
        $attributes['secret_questions'] = implode(';', $attributes['secret_questions']);
        $attributes['auth_types'] = implode(';', $attributes['auth_types']);

        return parent::prepareData($attributes, $method);
    }
}