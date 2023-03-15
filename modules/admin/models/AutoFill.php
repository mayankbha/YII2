<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\GroupForm;

class AutoFill extends BaseModel
{
    public static $dataLib = 'CodiacSDK.FileImport.dll';
    public static $dataAction = 'FillDataSources_Async';


    public static function fillTables($tables, $repopulate)
    {
        $postData = [
            'lib_name' => self::$dataLib,
            'func_name' => self::$dataAction,
            'func_param' => [
                'repopulate' => $repopulate,
                'tables' => $tables
            ],
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        return null;
    }
}