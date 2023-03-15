<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use Yii;
use app\models\GetAliasList;
use app\modules\admin\models\forms\AliasForm;
use yii\web\NotFoundHttpException;

use yii\web\Response;
use app\models\ExtensionsList;

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class AliasController extends BaseController
{
    public $model = GetAliasList::class;
    public $modelForm = AliasForm::class;

	public function actionCopy($id) {
        /* @var $modelClass BaseModel */
        $request = Yii::$app->request;

        if (isset($_REQUEST['customAPI'])) {
            $model = GetAliasList::jsonToArray(GetAliasList::callAPI("SearchAliasById", $id));

            if ($model) {
                if(Yii::$app->getRequest()->getQueryParam('action') == 'copy_alias') {
					$model[0]['AliasCode'] = '';
					$model[0]['AliasDatabaseField'] = '';
				}
                return $this->render('update', ['model' => $model, 'request' => $request]);
            }
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

}
