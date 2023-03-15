<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\ErrorManagementForm;

class ErrorManagement extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'Read_ErrCodes';
    public static $formClass = ErrorManagementForm::class;
}