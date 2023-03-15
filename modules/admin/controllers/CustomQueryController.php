<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use app\assets\CustomQueryAsset;
use app\models\TablesInfo;
use app\modules\admin\models\CustomQuery;
use app\modules\admin\models\forms\CustomQueryForm;
use conquer\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class CustomQueryController extends BaseController
{
    public $model = CustomQuery::class;
    public $modelForm = CustomQueryForm::class;

    public function actionBuilder($id = null)
    {
        $this->view->registerAssetBundle(CustomQueryAsset::class);

        if ($id) {
            $model = CustomQuery::getModel($id);
            $template = 'update';
        } else {
            $model = new CustomQueryForm();
            $template = 'create';
        }

        if (!$model) {
            return $this->redirect(Url::toRoute('index'));
        }

        $tablesInfo = TablesInfo::getData();
        $tablesInfo = !empty($tablesInfo->list) ?  ArrayHelper::index($tablesInfo->list, 'table_name') : [];

        $this->view->registerJs('CustomQueryBuilder.setTablesInfo(' . Json::encode($tablesInfo) . ')');

        return $this->render($template, ['model' => $model, 'builder' => true, 'tablesInfo' => $tablesInfo]);
    }
}