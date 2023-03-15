<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\services;

use app\modules\admin\models\Screen;

class TenantSettingsService
{
    public static function prepareSettingsForNewUser($requestData)
    {
        $tenantSettings = Screen::decodeTemplate($requestData['StyleTemplate']);
        $tenantSettings['currencyformat_code'] = $requestData['DefaultCurrency'];
        $tenantSettings['currencytype_code'] = $requestData['DefaultCurrencyType'];
        $tenantSettings['language'] = $requestData['DataLanguage'];
        $tenantSettings['dateformat_code'] = $requestData['DefaultDate'];
        $tenantSettings['timeformat_code'] = $requestData['DefaultTimeFormat'];
        $tenantSettings['timezone_code'] = $requestData['DefaultTimeZone'];
        $tenantSettings['button_style_code'] = $requestData['DefaultButtonStyle'];
        $tenantSettings['menutype_code'] = $requestData['DefaultMenuType'];
        $tenantSettings['email'] = $requestData['Email'];
        $tenantSettings['phone'] = $requestData['Phone'];

        return $tenantSettings;
    }
}