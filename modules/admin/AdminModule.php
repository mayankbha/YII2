<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin;

use yii\base\Module;

class AdminModule extends Module
{
    public $controllerNamespace = 'app\modules\admin\controllers';
    public $defaultRoute = 'user/index';
}
