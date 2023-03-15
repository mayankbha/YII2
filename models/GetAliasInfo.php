<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use app\modules\admin\models\forms\ListsForm;
use yii\helpers\ArrayHelper;

class GetAliasInfo extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AliasScreens.dll';
    public static $dataAction = 'GetAliasList';
}