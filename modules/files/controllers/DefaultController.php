<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\files\controllers;

use app\modules\admin\models\DocumentGroup;
use app\modules\files\assets\FilesAsset;
use yii\filters\AccessControl;
use yii\web\Controller;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    public function beforeAction($action)
    {
        $this->view->registerAssetBundle(FilesAsset::class);

        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        $documentGroupInfo = [
            'family' => 'system',
            'category' => 'system'
        ];

        if (DocumentGroup::getAccessPermission($documentGroupInfo['family'], $documentGroupInfo['category']) != DocumentGroup::ACCESS_RIGHT_FULL) {
            $documentGroupInfo = [
                'family' => null,
                'category' => null
            ];
        }

        return $this->render('index', ['documentGroupInfo' => $documentGroupInfo]);
    }
}
