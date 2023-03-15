<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\NotificationForm;

class Notification extends BaseModel
{
    public static $dataLib = 'CodiacSDK.NotifyProcessor.dll';
    public static $dataAction = 'GetNotifyRecordList';
    public static $formClass = NotificationForm::class;

    public static function getModel($pk)
    {
        /** @var $model NotificationForm */
        if (($model = parent::getModel($pk)) && !empty($model)) {
            $model->body = base64_decode($model->body);
        }

        return $model;
    }

    protected static function prepareData($attributes, $method = null)
    {
        $attributes['body'] = base64_encode($attributes['body']);
        return parent::prepareData($attributes, $method);
    }
}