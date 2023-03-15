<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\modules\files\models\AsyncModel;
use Yii;
use app\controllers\ModuleController;
use app\models\TablesInfo;
use app\modules\admin\assets\AutoFillAsset;
use app\modules\admin\models\AutoFill;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class AutofillController extends ModuleController
{
    public function beforeAction($action)
    {
        $this->view->registerAssetBundle(AutoFillAsset::class);

        return parent::beforeAction($action);
    }

    public function actionIndex() {
        $tablesInfo = TablesInfo::getData();
        $tablesInfo = !empty($tablesInfo->list) ?  ArrayHelper::index($tablesInfo->list, 'table_name') : [];
        return $this->render('index', ['tablesInfo' => $tablesInfo]);
    }

    public function actionFillTables() {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $tables = \Yii::$app->request->post('tables', []);
        $repopulate = \Yii::$app->request->post('repopulate', null);

        try {
            $result = AutoFill::fillTables($tables, $repopulate);
            if (!empty($result['async_job_pk'])) {
                return [
                    'status' => 'success',
                    'response' => [
                        'job_pk' => $result['async_job_pk'],
                        'job_status' => 'Init'
                    ]
                ];
            }
            throw new \Exception("Can't autofill tables");
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionExecuteCheckStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $pk = Yii::$app->request->post('pk', false);
            $result = AsyncModel::SearchJobStatus($pk);

            if (!empty($result[0]) && $result[0]['status'] != 'failed') {
                return ['status' => $result[0]['status']];
            }

            throw new \Exception("Error execute");
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}