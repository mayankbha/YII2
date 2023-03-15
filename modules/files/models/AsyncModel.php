<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\files\models;

use app\modules\files\models\FileContainerForm;
use Yii;
use app\models\BaseModel;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AsyncModel extends BaseModel
{
    const FILE_BASIC_DIRECTORY = '/upload/';

    const FILE_PDF_EXTENSION = 'pdf';
    const FILE_DOC_EXTENSION = 'odt';
    const FILE_XLS_EXTENSION = 'ods';
    const FILE_PPT_EXTENSION = 'odp';
    const FILE_TXT_EXTENSION = 'txt';

    const UPLOAD_STATUS_PREPARING = 'preparing';
    const UPLOAD_STATUS_ALLOCATED = 'allocated';
    const UPLOAD_STATUS_FOR_DELETE = 'for_delete';
    const UPLOAD_STATUS_IN_PROGRESS = 'in_progress';
    const UPLOAD_STATUS_COMPLETED = 'completed';

    const ASYNC_FUNC_INIT = 'Init';

    const CHUNK_SIZE = 51200;

    public static $dataLib = 'CodiacSDK.FileProcessor.dll';

    public static $fileConvertExtensions = [
        self::FILE_DOC_EXTENSION => ['doc', 'docx'],
        self::FILE_XLS_EXTENSION => ['ppt', 'pptx'],
        self::FILE_PPT_EXTENSION => ['xls', 'xlsx']
    ];

    public static function getDirectory($alias = '@web', $separator = DIRECTORY_SEPARATOR)
    {
        $path = Yii::getAlias($alias . self::FILE_BASIC_DIRECTORY) . $separator . Yii::$app->session->id;
        return FileHelper::normalizePath($path, $separator);
    }

    public static function encodeFileName($library, $fieldName)
    {
        return md5($library . '_' . $fieldName);
    }

    public static function getFileInfo($fileName, $family, $category)
    {
        $directory = self::getDirectory('@webroot');
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($filePath) && is_readable($filePath)) {
            return [
                'chunk_size' => (string)self::CHUNK_SIZE,
                'original_file_attributes' => '32',
                'original_file_hash' => base64_encode(hash_file('sha256', $filePath, true)),
                'original_file_size' => (string)filesize($filePath),
                'original_file_name' => $fileName,
                "document_category" => $category,
                "document_family" => $family,
                'document_key' => 'key'
            ];
        }

        throw new NotFoundHttpException("File '$fileName' is not found in the directory for this user");
    }

    public static function initUploadAsync($fileName, $family, $category)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if ($fileInfo = self::getFileInfo($fileName, $family, $category)) {
            $postData = [
                'func_name' => 'Upload_Init_Async',
                'func_param' => [
                    'patch_json' => $fileInfo
                ],
                'lib_name' => self::$dataLib
            ];

            $model = new static();
            if (($result = $model->processData($postData)) && !empty($result['record_list'])) {
                return $result['record_list'];
            }
        }

        throw new \Exception("Can't start file upload function");
    }

    public static function uploadFileChunk($pk, $chunkData, $chunkNum)
    {
        $postData = [
            'func_name' => 'Upload_NextChunk_Async',
            'func_param' => [
                'PK' => (string)$pk,
                'patch_json' => [
                    'chunk_data' => base64_encode($chunkData),
                    'chunk_num' => $chunkNum
                ]
            ],
            'lib_name' => self::$dataLib
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && !empty($result['record_list'])) {
            return $result['record_list'];
        }

        throw new \Exception("Can't upload chunk of file at number $chunkNum: {$result['extendedinfo']}");
    }

    public static function finishUploadAsync($pk)
    {
        $postData = [
            'func_name' => 'Upload_Finish_Async',
            'func_param' => [
                'PK' => (string)$pk,
            ],
            'lib_name' => self::$dataLib
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && !empty($result['record_list'])) {
            return true;
        }

        try {
            self::deleteUploadAsync($pk);
            throw new \Exception();
        } catch (\Exception $e) {
            throw new \Exception("Can't finish upload file: {$result['extendedinfo']}");
        }
    }

    public static function deleteUploadAsync($pk)
    {
        $postData = [
            'func_name' => 'Upload_Delete_Async',
            'func_param' => [
                'PK' => (string)$pk,
            ],
            'lib_name' => self::$dataLib
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && !empty($result['record_list'])) {
            return true;
        }

        throw new \Exception("Can't delete uploaded file from API server: {$result['extendedinfo']}");
    }

    public static function setFileChunk($pk, $filePath, $offset = 0, $chunk = 0)
    {
        if ($fragment = file_get_contents($filePath, NULL, NULL, $offset, self::CHUNK_SIZE)) {
            if (self::uploadFileChunk($pk, $fragment, $chunk)) {
                return [
                    'offset' => self::CHUNK_SIZE + $offset,
                    'chunk' => $chunk + 1,
                    'size' => filesize($filePath)
                ];
            }

            self::deleteUploadAsync($pk);
            throw new \Exception("Can't upload one of file fragment");
        }

        self::deleteUploadAsync($pk);
        throw new \Exception("Can't read one of file fragment for upload");
    }

    public static function getFileContainer($id)
    {
        self::$dataAction = 'GetFileContainerList';
        self::$formClass = FileContainerForm::class;
        if ($result = self::getModel($id)) {
            return $result;
        }

        return null;
    }

    public static function getFileList($status)
    {
        $postData = [
            'func_name' => 'SearchFileContainer',
            'func_param' => [
                'field_name_list' => ["upload_status"],
                'field_out_list' => [],
                'search_mask_list' => [
                    'upload_status' => [$status]
                ]
            ],
            'lib_name' => self::$dataLib
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        throw new \Exception("Can't get file list: {$result['extendedinfo']}");
    }

    public static function GetJobStatusList()
    {
        self::$dataAction = 'GetJobStatusList';
        $result = self::getData(['async_func_name' => ['Upload_Init_Async']], null,
            ['field_out_list' => ["id", "status", "async_lib_name", "async_func_name"]]);

        if (!empty($result->list)) {
            return $result->list;
        }

        throw new \Exception("Can't get job status list");
    }

    public static function SearchJobStatus($id)
    {
        $postData = [
            'func_name' => 'SearchJobStatus',
            'func_param' => [
                'field_name_list' => ["id"],
                'field_out_list' => [],
                'search_mask_list' => [
                    'id' => [$id]
                ]
            ],
            'lib_name' => self::$dataLib
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        throw new \Exception("Can't get job status list: {$result['extendedinfo']}");
    }

    public static function executeSQL($pk)
    {
        $postData = [
            'func_name' => 'ExecuteSQL_Async',
            'func_param' => [
                'file_container_pk' => $pk
            ],
            'lib_name' => 'CodiacSDK.FileImport.dll'
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        return null;
    }

    public static function importFile($pk)
    {
        $postData = [
            'func_name' => 'Import_Async',
            'func_param' => [
                'file_container_pk' => $pk
            ],
            'lib_name' => 'CodiacSDK.FileImport.dll'
        ];

        $model = new static();
        if (($result = $model->processData($postData)) && isset($result['record_list'])) {
            return $result['record_list'];
        }

        return null;
    }
}