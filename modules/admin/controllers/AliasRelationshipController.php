<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\controllers;

use Yii;

use app\models\GetAliasList;

use app\modules\admin\models\AliasRelationship;
use app\modules\admin\models\forms\AliasRelationshipForm;

use yii\web\NotFoundHttpException;

use yii\web\Response;
use app\models\ExtensionsList;

use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class AliasRelationshipController extends BaseController
{
    public $model = AliasRelationship::class;
    public $modelForm = AliasRelationshipForm::class;

	public function actionCreate() {
		$data['action'] = 'Create Alias Relationship';

		if ($post = Yii::$app->request->post()) {
			$response = AliasRelationship::createAliasRelationship($post);

			if ($response['requestresult'] == 'unsuccessfully') {
				Yii::$app->getSession()->setFlash('danger', Yii::t('app',  $response['extendedinfo']));
			} else {
				Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully create'));
			}
		}

		return $this->render('alias-relationship-form', ['data' => $data]);
	}

	public function actionUpdate($id) {
		$data['action'] = 'Update Alias Relationship';

		if ($post = Yii::$app->request->post()) {
			$response = AliasRelationship::updateAliasRelationship($post, $id);

			//var_dump($response); die;

			if ($response['requestresult'] == 'unsuccessfully') {
				Yii::$app->getSession()->setFlash('danger', Yii::t('app',  $response['extendedinfo']));
			} else {
				Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully update'));
			}
		}

		$data['data'] = AliasRelationship::getAliasRelationship($id);

		return $this->render('alias-relationship-form', ['data' => $data]);
	}

	public function actionDelete($id) {
		$response = AliasRelationship::deleteAliasRelationship($id);

		var_dump($response); die;

		/*if($response == null || (isset($response['extendedinfo']) && $response['extendedinfo'] != '') || $response['requestresult'] == 'unsuccessfully') {
			Yii::$app->getSession()->setFlash('danger', Yii::t('app','Error Delete'));
			//Yii::$app->getSession()->setFlash('danger', Yii::t('app', $response['extendedinfo']));
		} else {
			Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Successfully delete'));
		}

		return $this->redirect(['/admin/alias-relationship']);*/
    }

}
