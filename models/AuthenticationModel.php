<?php

namespace app\models;

use Yii;

class AuthenticationModel extends AccountModel
{
    const SEND_AUTH_TYPE_CODE = 'Authenticate';
    const CHECK_AUTH_TYPE_CODE = 'CheckAuthCode';

    public static function sendAuthTypeCode($type) {
        $postData = [
            'func_name' => self::SEND_AUTH_TYPE_CODE,
            'func_param' => [
                'authentication_source' => $type
            ]
        ];

        $model = new static();
        $result = $model->processData($postData);

        if (!empty($result['requestresult'])) {
            if (!empty($result['squestion'])) {
                return $result['squestion'];
            }

            return true;
        }

        return false;
    }

    public static function checkAuthTypeCode($type, $code) {
        $postData = [
            'func_name' => self::CHECK_AUTH_TYPE_CODE,
            'func_param' => [
                'authentication_code' => $code,
                'authentication_source' => $type
            ]
        ];

        $model = new static();
        $result = $model->processData($postData);

        return !empty($result['requestresult']);
    }

    public function processData($postData = array())
    {
        $session = Yii::$app->session;
        $screenData = $session['screenData'];
        if (!isset($screenData['sessionData'])) {
            $sessionRequestResult = self::processSessionData();
            self::addToSession(array('sessionData' => $sessionRequestResult));
        }

        return parent::processData($postData);
    }
}