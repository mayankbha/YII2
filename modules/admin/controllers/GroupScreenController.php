<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\GroupScreen;
use app\modules\admin\models\forms\GroupScreenForm;
use Yii;
use yii\web\Response;

class GroupScreenController extends BaseController
{
    public $model = GroupScreen::class;
    public $modelForm = GroupScreenForm::class;

    public function actionGetGroupScreen()
    {
        $request = Yii::$app->request;
        if (!$request->isAjax) {
            $response = Yii::$app->response;
            $response->format = Response::FORMAT_JSON;
            $response->data = GroupScreen::getData();
            return $response;
        }
    }
}