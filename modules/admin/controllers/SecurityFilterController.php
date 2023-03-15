<?php
namespace app\modules\admin\controllers;

use app\modules\admin\models\SecurityFilter;
use app\modules\admin\models\forms\SecurityFilterForm;
use Yii;

class SecurityFilterController extends BaseController
{
    public $model = SecurityFilter::class;
    public $modelForm = SecurityFilterForm::class;
}