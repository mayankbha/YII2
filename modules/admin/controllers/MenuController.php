<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\Menu;
use app\modules\admin\models\forms\MenuForm;
use yii\web\Response;
use Yii;

class MenuController extends BaseController
{
    public $model = Menu::class;
    public $modelForm = MenuForm::class;

}