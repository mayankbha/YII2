<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\components;

use Yii;
use app\models\UserAccount;
use yii\helpers\ArrayHelper;

class AuthHelper
{
    const AUTH_TYPE_LOGIN = 'AuthType.L';
    const AUTH_TYPE_QUESTIONS = 'AuthType.SQ';
    const AUTH_TYPE_EMAIL = 'AuthType.E';
    const AUTH_TYPE_SMS = 'AuthType.S';
	const AUTH_TYPE_LDAP = 'AuthType.LD';

    const STATUS_NOT_INIT = 'NI';
    const STATUS_PROGRESS = 'PROGRESS';
    const STATUS_COMPLETED = 'COMPLETED';

    const CACHE_CONST = 'authType';

    protected static function setCacheData($types, $status = self::STATUS_PROGRESS)
    {
        Yii::$app->session[self::CACHE_CONST] = [
            'types' => $types,
            'status' => $status
        ];
    }

    public static function init()
    {
        $userModel = Yii::$app->session['screenData'][UserAccount::class];

        if (!empty($userModel->auth_required)) {
            $authTypes = explode(';', $userModel->auth_required);
        }

        if (!empty($authTypes) && is_array($authTypes)) {
            self::setCacheData($authTypes);

            return true;
        }

        return false;
    }

    /**
     * @return bool|array
     */
    public static function getTypes()
    {
        if (self::getStatus() == self::STATUS_PROGRESS && !empty(Yii::$app->session[self::CACHE_CONST]['types'])) {
            return Yii::$app->session[self::CACHE_CONST]['types'];
        }

        return false;
    }

    public static function getStatus() {
        if (!empty(Yii::$app->session[self::CACHE_CONST]['status'])) {
            return Yii::$app->session[self::CACHE_CONST]['status'];
        }

        return self::STATUS_NOT_INIT;
    }

    public static function getCurrentType()
    {
        if (self::getStatus() == self::STATUS_PROGRESS && $types = self::getTypes()) {
            return current($types);
        }

        return false;
    }

    public static function completeType($type)
    {
        if (($authTypes = self::getTypes()) && in_array($type, [self::AUTH_TYPE_EMAIL, self::AUTH_TYPE_LOGIN, self::AUTH_TYPE_QUESTIONS, self::AUTH_TYPE_SMS, self::AUTH_TYPE_SMS, self::AUTH_TYPE_LDAP])) {
            ArrayHelper::removeValue($authTypes, $type);
            $status = empty($authTypes) ? self::STATUS_COMPLETED : self::STATUS_PROGRESS;

            self::setCacheData($authTypes, $status);
            return true;
        }

        return false;
    }
}