<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use Yii;

use app\models\BaseModel;
use app\modules\admin\models\forms\TableForm;
use yii\helpers\ArrayHelper;

class Table extends BaseModel
{
    public static $dataLib = 'CodiacSDK.CommonArea.dll';
    public static $dataAction = 'GetTablesInfo';
    public static $formClass = TableForm::class;

    public static function createTable($postData)
    {
        $processData = [
            "func_name" => "CreateDBTable",
            "func_param" => $postData,
            "lib_name" => "CodiacSDK.FileImport.dll"
        ];

        $result = (new static())->processData($processData);

        return $result;
    }

    public static function updateTable($postData, $skipEncryption = false)
    {
        $model = new static();

        $sessionData = $model->getSessionData();

        $requestbody = [
            "func_name" => "AlterDBTable",
            "func_param" => $postData,
            "lib_name" => "CodiacSDK.FileImport.dll"
        ];

        if ($sessionData['secretKey'] && $sessionData['secretIv']) {
            $requestbody = $model->AesEncrypt($requestbody, $sessionData['secretKey'], $sessionData['secretIv']);
        }

        $data_string = [
            "requestbody" => $requestbody,
            "sessionhandle" => $sessionData['sessionhandle']
        ];

        echo json_encode($data_string); die;

        $ch = curl_init(static::getSourceLink());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data_string)),
            'User-Agent: Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip,deflate'
        ));

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_string));

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);

        $body = isset($response['resultbody']) ? $response['resultbody'] : null;

        if (!empty(Yii::$app->session['screenData']['sessionData']['sessionhandle'])) {
            $sessionData = Yii::$app->session['screenData']['sessionData'];

            if (!$skipEncryption && isset($body) && is_string($body)) {
                $secretKey = $sessionData['secretKey'];
                $secretIv = $sessionData['secretIv'];
                if ((!is_null($secretKey) && !is_null($secretIv))) {
                    $response = $model->AesDecrypt($body, $secretKey, $secretIv);
                }
            } else {
                if (!isset($sessionData['secretKey']) || empty($sessionData['secretKey'])) {
                    $response = isset($response['resultbody']) ? $response['resultbody'] : $response;
                }
            }
        }

        return $response;
    }

    public static function GetTableInfo($id)
    {
        $processData = [
            "func_name" => "GetAlterTableInfo",
            "func_param" => [
                "field_name_list" => [],
                "field_value_list" => [],
                "table_name" => $id
            ],
            "lib_name" => "CodiacSDK.FileImport.dll"
        ];

        $result = (new static())->processData($processData);

        return (!empty($result['record_list'])) ? (object)$result['record_list'] : null;
    }

    public static function deleteTable($table, $skipEncryption = false)
    {
        $model = new static();

        $sessionData = $model->getSessionData();

        $requestbody = [
            "func_name" => "DeleteDBTable",
            "func_param" => [
                "field_name_list" => [],
                "field_value_list" => [],
                "table_name" => $table
            ],
            "lib_name" => "CodiacSDK.FileImport.dll"
        ];

        if ($sessionData['secretKey'] && $sessionData['secretIv']) {
            $requestbody = $model->AesEncrypt($requestbody, $sessionData['secretKey'], $sessionData['secretIv']);
        }

        $data_string = [
            "requestbody" => $requestbody,
            "sessionhandle" => $sessionData['sessionhandle']
        ];

        $ch = curl_init(static::getSourceLink());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data_string)),
            'User-Agent: Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip,deflate'
        ));

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_string));

        $response = curl_exec($ch);
        $response = json_decode($response, true);
        curl_close($ch);

        $body = isset($response['resultbody']) ? $response['resultbody'] : null;

        if (!empty(Yii::$app->session['screenData']['sessionData']['sessionhandle'])) {
            $sessionData = Yii::$app->session['screenData']['sessionData'];

            if (!$skipEncryption && isset($body) && is_string($body)) {
                $secretKey = $sessionData['secretKey'];
                $secretIv = $sessionData['secretIv'];
                if ((!is_null($secretKey) && !is_null($secretIv))) {
                    $response = $model->AesDecrypt($body, $secretKey, $secretIv);
                }
            } else {
                if (!isset($sessionData['secretKey']) || empty($sessionData['secretKey'])) {
                    $response = isset($response['resultbody']) ? $response['resultbody'] : $response;
                }
            }
        }

        return $response;
    }

}