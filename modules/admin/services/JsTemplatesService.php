<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\services;

class JsTemplatesService
{

    const CHECK_MAX_TEMPLATE_ID = 221;
    const COMPARE_FIELDS_TEMPLATE_ID = 222;
    const UPPER_CASE_TEMPLATE_ID = 223;
    const CHECK_MIN_TEMPLATE_ID = 224;
    const LOWER_CASE_TEMPLATE_ID = 225;

    public static function getTemplates()
    {
        return [
            self::CHECK_MAX_TEMPLATE_ID => self::getCheckMaxTemplate(),
            self::COMPARE_FIELDS_TEMPLATE_ID => self::getCompareFieldsTemplate(),
            self::UPPER_CASE_TEMPLATE_ID => self::getToUpperCaseTemplate(),
            self::CHECK_MIN_TEMPLATE_ID => self::getCheckMinTemplate(),
            self::LOWER_CASE_TEMPLATE_ID => self::getToLowerCaseTemplate(),
        ];
    }

    public static function getSelect()
    {
        return [
            self::CHECK_MAX_TEMPLATE_ID => 'Check max value',
            self::COMPARE_FIELDS_TEMPLATE_ID => 'Compare fields',
            self::UPPER_CASE_TEMPLATE_ID => 'To upper case',
            self::CHECK_MIN_TEMPLATE_ID => 'Check min value',
            self::LOWER_CASE_TEMPLATE_ID => 'To lower case',
        ];
    }

    protected static function getCheckMaxTemplate()
    {
        return /** @lang JavaScript */ '     
var maxValue = 100;
if (parseFloat(this.value) >= maxValue) {
  throw new Error(\'Maximum value does not match\');
}';
    }

    protected static function getCheckMinTemplate()
    {
        return /** @lang JavaScript */ '     
var minValue = 20;
if (parseFloat(this.value) <= minValue) {
  throw new Error(\'Minimum value does not match\');
}';
    }

    protected static function getCompareFieldsTemplate()
    {
        return /** @lang JavaScript */ '     
var anotherInput = document.getElementById(\'\');
if (this.value != anotherInput.value) {
  throw new Error(\'Values ​​are not equal\');
}';
    }

    protected static function getToLowerCaseTemplate()
    {
        return /** @lang JavaScript */ '     
this.value = this.value.toLowerCase();';
    }

    protected static function getToUpperCaseTemplate()
    {
        return /** @lang JavaScript */ '     
this.value = this.value.toUpperCase();';
    }
}