<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\TemplateForm;

class Template extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetTemplateList';
    public static $formClass = TemplateForm::class;
}