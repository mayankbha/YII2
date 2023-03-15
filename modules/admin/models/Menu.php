<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\MenuForm;

class Menu extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminMenu.dll';
    public static $dataAction = 'GetMenuList';
    public static $formClass = MenuForm::class;

    //Prepare data for update and create
    protected static function prepareData($attributes, $method = null) {
        if ($method === 'setModel') $attributes['menu_name'] = preg_replace ("/[^a-zA-Z0-9]+/", "_", $attributes['menu_text']);
        if (!isset($attributes['weight']) || $attributes['weight'] < 0) $attributes['weight'] = 0;

        return parent::prepareData($attributes);
    }
}