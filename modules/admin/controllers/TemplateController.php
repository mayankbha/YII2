<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\forms\TemplateForm;
use app\modules\admin\models\Template;

class TemplateController extends BaseController
{
    public $model = Template::class;
    public $modelForm = TemplateForm::class;
}