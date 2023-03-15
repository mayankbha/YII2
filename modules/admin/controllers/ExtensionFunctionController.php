<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\ExtensionFunction;
use app\modules\admin\models\forms\ExtensionFunctionForm;

class ExtensionFunctionController extends BaseController
{
    public $model = ExtensionFunction::class;
    public $modelForm = ExtensionFunctionForm::class;
}