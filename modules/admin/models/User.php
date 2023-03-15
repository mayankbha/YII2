<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\models\CustomLibs;
use app\modules\admin\models\forms\UserStyleTemplateForm;
use app\modules\admin\models\forms\UserForm;
use app\models\UserAccount;
use yii\helpers\ArrayHelper;

class User extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminUsers.dll';
    public static $dataAction = 'GetUserList';
    public static $formClass = UserForm::class;

    //Override method. Getting data from current library by id and prepare group_area field for form
    public static function getModel($pk)
    {
        /** @var $model UserForm */
        if (($model = parent::getModel($pk)) && !empty($model)) {
            $model->group_area = explode(';', $model->group_area);
            $model->document_group = explode(';', $model->document_group);

            $model->last_login = (!empty($model->last_login)) ? date('Y-m-d\TH:i:s', strtotime($model->last_login)) : $model->last_login;
            $model->style_template = new UserStyleTemplateForm(Screen::decodeTemplate($model->style_template));
        }

        return $model;
    }

    //Prepare data for update and create
    protected static function prepareData($attributes, $method = null) {
        if (!empty($attributes['account_password'])) {
            $attributes['account_password'] = UserAccount::encodePassword($attributes['account_password']);
        } else {
            unset($attributes['account_password']);
        }

        if (!empty($attributes['style_template'])) {
            $styleTemplate = array_filter($attributes['style_template']->getAttributes());
            $attributes['style_template'] = base64_encode(json_encode($styleTemplate));
        } else {
            unset($attributes['style_template']);
        }

        if (is_array($attributes['group_area'])) $attributes['group_area'] = implode(';', $attributes['group_area']);
        if (is_array($attributes['document_group'])) $attributes['document_group'] = implode(';', $attributes['document_group']);

        unset($attributes['last_login']);
        unset($attributes['security1_length']);
        unset($attributes['security2_length']);

        $attributes = array_filter($attributes);
        return parent::prepareData($attributes);
    }

    public static function getDefaultSettings() {
        $model = new parent();
        $attributes = $model->processData([
            "func_name" => "GetDefaultColors",
            "func_param" => null,
            "lib_name" => "CodiacSDK.CommonArea.Dll"
        ]);

        $attributes['defcolors'];

        if (!empty($attributes['defcolors']['style_template'])) {
            return Screen::decodeTemplate($attributes['defcolors']['style_template']);
        }

        return [];
    }
}