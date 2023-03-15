<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\ErrorManagement;
use app\modules\admin\models\forms\ErrorManagementForm;

class ErrorManagementController extends BaseController
{
    public $model = ErrorManagement::class;
    public $modelForm = ErrorManagementForm::class;
}