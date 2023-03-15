<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use Yii;

use app\models\GetAliasList;

use app\modules\admin\models\AliasDependency;
use app\modules\admin\models\forms\AliasDependencyForm;

use yii\web\NotFoundHttpException;

use yii\web\Response;
use app\models\ExtensionsList;

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class AliasDependencyController extends BaseController
{
    public $model = AliasDependency::class;
    public $modelForm = AliasDependencyForm::class;

	public function actionCreate() {
		$data['action'] = 'Create Alias Dependency';

		if ($post = Yii::$app->request->post()) {
			$response = AliasDependency::createAliasDependency($post);

			if ($response['requestresult'] == 'unsuccessfully') {
				Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
			} else {
				Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
			}
		}

		return $this->render('alias-dependency-form', ['data' => $data]);
	}

	public function actionUpdate($id) {
		$data['action'] = 'Update Alias Dependency';

		$data['data'] = AliasDependency::getAliasTableDependency($id);

		//echo "<pre>"; print_r($data);

		if ($post = Yii::$app->request->post()) {
			$response = AliasDependency::updateAliasDependency($id, $post);

			if ($response['requestresult'] == 'unsuccessfully') {
				Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
			} else {
				Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
			}
		}

		return $this->render('alias-dependency-form', ['data' => $data]);
	}

	public function actionDelete($id) {
		$response = AliasDependency::deleteAliasDependency($id);

		if($response['requestresult'] == 'unsuccessfully') {
			Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
		} else {
			Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully delete'));
		}

		return $this->redirect(['/admin/alias-dependency']);
    }

}
