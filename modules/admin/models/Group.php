<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\GroupForm;
use yii\helpers\ArrayHelper;

class Group extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminGroups.dll';
    public static $dataAction = 'GetGroupList';
    public static $formClass = GroupForm::class;

    public static function getListName()
    {
        $data = self::getData();
        if (!empty($data->list)) {
            return ArrayHelper::map($data->list, 'group_name', function ($item) {
                return $item['group_name'] . ': ' . $item['group_description'];
            });
        }

        return [];
    }
}