<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use app\modules\admin\models\forms\ListsForm;
use yii\helpers\ArrayHelper;

class GetListList extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetListList';
    public static $formClass = ListsForm::class;

    const BASE_NAME_DATE = 'Date';
    const BASE_NAME_TIME = 'Time';
    const BASE_NAME_TIMEZONE = 'TimeZone';
    const BASE_NAME_CURRENCY = 'Currency';
    const BASE_NAME_CURRENCY_TYPE = 'CurrencyType';
    const BASE_NAME_MENU_TYPE = 'MenuType';
    const BASE_NAME_USER_TYPE = 'UserType';
    const BASE_NAME_BUTTON_TYPE = 'ButtonType';
    //const BASE_NAME_LANGUAGE = 'Language';
    const BASE_NAME_LANGUAGE = 'LanguageCode';
    const BASE_NAME_COUNTRY = 'CountryList';
    const BASE_NAME_STATE = 'StatesList';
    const BASE_NAME_CITY = 'CityList';
    const BASE_NAME_SECURITY_QUESTIONS = 'SQuestions';
    const BASE_NAME_AUTHORIZATION_TYPE = 'AuthType';
    const BASE_NAME_TRANSACTION_REQUEST = 'TransRequest';
    const BASE_NAME_JOB_SCHEDULER_TYPE = 'JobLaunchType';

    const BASE_NAME_EXTENSION = 'Extensions';

    const BASE_NAME_NOTIFY_TYPE = 'NotifyType';
    const BASE_NAME_NOTIFY_RECIPIENT_TYPE = 'NotifyRecipientType';

    public static $baseListNames = [
        self::BASE_NAME_DATE,
        self::BASE_NAME_TIME,
        self::BASE_NAME_TIMEZONE,
        self::BASE_NAME_CURRENCY,
        self::BASE_NAME_CURRENCY_TYPE,
        self::BASE_NAME_MENU_TYPE,
        self::BASE_NAME_USER_TYPE,
        self::BASE_NAME_BUTTON_TYPE,
        self::BASE_NAME_LANGUAGE,
        self::BASE_NAME_JOB_SCHEDULER_TYPE
    ];

    public static $addressListName = [
        self::BASE_NAME_COUNTRY,
        self::BASE_NAME_STATE,
        self::BASE_NAME_CITY,
    ];

    public static $notifyListName = [
        self::BASE_NAME_LANGUAGE,
        self::BASE_NAME_NOTIFY_RECIPIENT_TYPE,
        self::BASE_NAME_NOTIFY_TYPE,
    ];

    public static function getByNames(array $names)
    {
        $lists = [];
        if (($model = self::getData(['list_name' => $names])) && !empty($model->list)) {
            $lists = ArrayHelper::index($model->list, null, 'list_name');
        }

        return array_merge(array_fill_keys($names, []), $lists);
    }

    public static function getArrayForSelectByNames(array $names, $concatListName = true, $concatDescription = true)
    {
        $lists = self::getByNames($names);
        foreach($lists as $listName => $data) {
            $tmp = [];
            usort($data, function($a, $b) {
                $a = $a['weight'];
                $b = $b['weight'];

                if ($a == $b) return 0;
                return ($a > $b) ? -1 : 1;
            });
            foreach($data as $listData) {
                $key = ($concatListName) ? "$listName.{$listData['entry_name']}" : $listData['entry_name'];
                $value = ($concatDescription && !empty($listData['description'])) ? "{$listData['entry_name']}: {$listData['description']}" : $listData['entry_name'];

                $tmp[$key] = $value;
            }
            $lists[$listName] = $tmp;
        }

        return $lists;
    }

    public static function getModels($pk)
    {
        $result = [];
        $model = parent::getData(['list_name' => [$pk]]);
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

}