<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\GroupScreenForm;

class GroupScreen extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminGroupScreen.dll';
    public static $dataAction = 'GetGroupScreenList';
    public static $formClass = GroupScreenForm::class;

    //Prepare data for update and create
    protected static function prepareData($attributes, $method = null) {
        if ($method === 'setModel') $attributes['screen_name'] = preg_replace ("/[^a-zA-Z0-9]+/", "_", $attributes['screen_text']);
        return parent::prepareData($attributes);
    }
}