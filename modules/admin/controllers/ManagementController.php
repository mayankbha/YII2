<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\admin\models\Management;
use yii\helpers\Url;
use app\controllers\ModuleController;

class ManagementController extends ModuleController
{
    public function actionIndex() {
        return $this->render('index');
    }

    public function actionGetInfo() {
        $info = Management::getData();
        return $this->renderAjax('data', $info);
    }

    public function actionResetSoft() {
        if (\Yii::$app->request->isPost && Management::resetSoft()) {
            return $this->redirect(Url::toRoute('/logout'));
        }

        \Yii::$app->session->setFlash('danger', 'Reset error');
        return $this->redirect(Url::toRoute('index'));
    }
}