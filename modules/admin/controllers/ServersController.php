<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\forms\ServersForm;
use app\modules\admin\models\Servers;

class ServersController extends BaseController
{
    public $model = Servers::class;
    public $modelForm = ServersForm::class;
}