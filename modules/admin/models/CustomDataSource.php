<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\CustomDataSourceForm;

class CustomDataSource extends BaseModel
{
    public static $dataLib = 'CodiacSDK.Universal.dll';
    public static $dataAction = 'GetList';
    public static $formClass = CustomDataSourceForm::class;
}