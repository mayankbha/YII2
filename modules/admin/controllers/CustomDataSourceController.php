<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\CustomDataSource;
use app\modules\admin\models\forms\CustomDataSourceForm;

class CustomDataSourceController extends BaseController
{
    public $model = CustomDataSource::class;
    public $modelForm = CustomDataSourceForm::class;
}