<?php
namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\ImageForm;
use yii\helpers\ArrayHelper;

class Image extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminUsers.dll';
    public static $dataAction = 'GetLogoList';
    public static $formClass = ImageForm::class;

    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        if (empty($fieldList)) {
            $fieldList = ['type' => [ImageForm::TYPE_LOGO_HEADER, ImageForm::TYPE_LOGO_MAIN]];
        }

        return parent::getData($fieldList, $postData, $additionallyParam);
    }

    protected static function prepareData($attributes, $method = null) {
        if (!empty($attributes['logo_image_body'])) {
            $attributes['logo_image_body'] = base64_encode(file_get_contents($attributes['logo_image_body']->tempName));
        } else {
            ArrayHelper::remove($attributes, 'logo_image_body');
        }

        return parent::prepareData($attributes);
    }
}