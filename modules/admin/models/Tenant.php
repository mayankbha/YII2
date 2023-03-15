<?php
namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\ChatSettingsForm;
use app\modules\admin\models\forms\UserStyleTemplateForm;
use app\modules\admin\models\forms\TenantForm;
use yii\helpers\ArrayHelper;

class Tenant extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminUsers.dll';
    public static $dataAction = 'GetTenantList';
    public static $formClass = TenantForm::class;

    /**
     * @param $pk
     * @return TenantForm|null
     */
    public static function getModel($pk)
    {
        /** @var $model TenantForm */
        if (($model = parent::getModel($pk)) && !empty($model)) {
            $model->Logos = explode(';', $model->Logos);
            $model->ChatSettings = new ChatSettingsForm(Screen::decodeTemplate($model->ChatSettings));
            $model->StyleTemplate = new UserStyleTemplateForm(Screen::decodeTemplate($model->StyleTemplate));
        }

        return $model;
    }

    //Prepare data for update and create
    protected static function prepareData($attributes, $method = null) {
        $attributes['Logos'] = implode(";", $attributes['Logos']);

        if (!empty($attributes['StyleTemplate']->attributes)) {
            $attributes['StyleTemplate'] = $attributes['StyleTemplate']->attributes;
            $attributes['StyleTemplate'] = base64_encode(json_encode($attributes['StyleTemplate']));
        } else {
            ArrayHelper::remove($attributes, 'StyleTemplate');
        }

        if (!empty($attributes['ChatSettings']->attributes)) {
            $attributes['ChatSettings'] = $attributes['ChatSettings']->attributes;
            $attributes['ChatSettings'] = base64_encode(json_encode($attributes['ChatSettings']));
        } else {
            ArrayHelper::remove($attributes, 'ChatSettings');
        }

        return parent::prepareData($attributes);
    }
}