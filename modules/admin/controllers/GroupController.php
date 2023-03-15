<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\Group;
use app\modules\admin\models\forms\GroupForm;

class GroupController extends BaseController
{
    public $model = Group::class;
    public $modelForm = GroupForm::class;
}