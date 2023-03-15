<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\forms\NotificationForm;
use app\modules\admin\models\Notification;

class NotificationController extends BaseController
{
    public $model = Notification::class;
    public $modelForm = NotificationForm::class;
}