<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\forms\ListsForm;
use app\modules\admin\models\SecurityQuestions;

class SecurityQuestionsController extends BaseController
{
    public $model = SecurityQuestions::class;
    public $modelForm = ListsForm::class;
}