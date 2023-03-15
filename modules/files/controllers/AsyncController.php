<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\files\controllers;

use Yii;
use app\modules\files\models\AsyncModel;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class AsyncController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
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
        if (!Yii::$app->request->isAjax) {
            throw new BadRequestHttpException('Your browser sent a request that this server could not understand');
        }

        if (Yii::$app->user->isGuest) {
            Yii::$app->response->redirect(Url::toRoute(['/login']));
            return false;
        }

        return parent::beforeAction($action);
    }

    public function actionUpload()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $directory = AsyncModel::getDirectory('@webroot');
            $fileAttribute = 'file';

            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }

            if ($file = UploadedFile::getInstanceByName($fileAttribute)) {
                $filePath = FileHelper::normalizePath($directory . DIRECTORY_SEPARATOR . $file->name);
                if ($file->saveAs($filePath)) {
                    return $file;
                }
            }
            throw new \Exception("Can't upload file to the server");
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(400);
            return $e->getMessage();
        }
    }

    public function actionDelete()
    {
        $fileName = Yii::$app->request->post('file_name', 'null');
        $fileName = str_replace(['/', '\\'], "", $fileName);

        $directory = AsyncModel::getDirectory('@webroot');
        $filePath = FileHelper::normalizePath($directory . DIRECTORY_SEPARATOR . $fileName);
        if (is_file($filePath)) {
            unlink($filePath);
        }
    }

    public function actionInitExecute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $pk = Yii::$app->request->post('pk', false);
            $fileContainer = AsyncModel::getFileContainer($pk);
            $fileNameInfo = pathinfo($fileContainer->original_file_name);

            if ($fileNameInfo['extension'] == 'sql') {
                $result = AsyncModel::executeSQL($pk);
            } else {
                $result = AsyncModel::importFile($pk);
            }

            if ($result) {
                return [
                    'status' => 'success',
                    'response' => [
                        'job_pk' => $result['async_job_pk'],
                        'job_status' => 'Init'
                    ]
                ];
            }

            throw new \Exception("Can't execute file on server");
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

    public function actionInitUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $fileName = Yii::$app->request->post('file_name', false);
            $family = Yii::$app->request->post('family', false);
            $category = Yii::$app->request->post('category', false);

            if (!$fileName || !$family || !$category) {
                throw new BadRequestHttpException('Has no required params for upload file');
            }

            $post['file_name'] = str_replace(['/', '\\'], "", $fileName);

            return [
                'status' => 'success',
                'response' => AsyncModel::initUploadAsync($fileName, $family, $category)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionUploadFragment()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            $pk = Yii::$app->request->post('pk', false);
            $fileName = Yii::$app->request->post('file_name', false);
            $offset = Yii::$app->request->post('offset', null);
            $chunk = Yii::$app->request->post('chunk', null);

            if (!$pk || !$fileName || !isset($chunk) || !isset($offset)) {
                throw new BadRequestHttpException('Has no required params for upload file');
            }

            $fileName = str_replace(['/', '\\'], "", $fileName);
            $filePath = AsyncModel::getDirectory('@webroot') . DIRECTORY_SEPARATOR . $fileName;

            if (filesize($filePath) < $offset) {
                return ['status' => 'success'];
            }

            return [
                'status' => 'completed',
                'response' => AsyncModel::setFileChunk($pk, $filePath, $offset, $chunk)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionFinishUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            if (!($pk = Yii::$app->request->post('pk', false))) {
                throw new BadRequestHttpException('Has no required params for  finish upload file');
            }

            return [
                'status' => 'success',
                'response' => AsyncModel::finishUploadAsync($pk)
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
