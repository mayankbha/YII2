<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\CustomDataSourceForm;
use app\modules\admin\models\forms\ServersForm;

class Servers extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetServerRecordList';
    public static $formClass = ServersForm::class;
}