<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\models;

use phpseclib\Crypt\RSA;
use Yii;

abstract class AccountModel
{
    protected static $_defaultAttributes = array();
    public static $dataAction = '';

    //Change controller of API server
    protected static function getSourceLink()
    {
        if (!empty(Yii::$app->session['apiEndpoint'])) {
            return Yii::$app->session['apiEndpoint'];
        }
        return (YII_ENV == 'dev') ? Yii::$app->params['apiEndpointDev'] : Yii::$app->params['apiEndpoint'];
    }

    protected static function checkSourceLink() {
        if (YII_ENV == 'dev' && !empty(Yii::$app->params['apiBalancerDev'])) {
            $balancerUrl = Yii::$app->params['apiBalancerDev'];
        } else if (!empty(Yii::$app->params['apiBalancer'])) {
            $balancerUrl = Yii::$app->params['apiBalancer'];
        }

        if (!empty($balancerUrl) && (empty(Yii::$app->session['apiEndpoint']) || empty(Yii::$app->session['apiEndpointCustom']))) {
            $curl = curl_init($balancerUrl);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt( $curl, CURLOPT_POSTFIELDS, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            if (($json = curl_exec($curl)) && ($response = json_decode($json, true))) {
                if (!empty($response['resultbody']['server_info']['address']) && !empty($response['resultbody']['server_info']['port'])) {
                    $url = 'http://' . $response['resultbody']['server_info']['address'] . ':' . $response['resultbody']['server_info']['port'];
                    Yii::$app->session['apiEndpoint'] = $url . '/' . Yii::$app->params['apiControllersRoute']['common'];
                    Yii::$app->session['apiEndpointCustom'] = $url . '/' . Yii::$app->params['apiControllersRoute']['custom'];
                }
            }

            curl_close($curl);
        }
    }

    /**
     * AES decryption
     * @param string $sValue - Value for decryption
     * @param string $sSecretKey - Secret key for decryption
     * @param string $sIv - Iv key
     * @return string
     */
    public static function AesDecrypt($sValue, $sSecretKey, $sIv)
    {
        return (!is_null($sSecretKey) && !is_null($sIv)) ? mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $sSecretKey, base64_decode($sValue), MCRYPT_MODE_CBC, $sIv) : $sValue;
    }

    /**
     * AES encryption
     * @param array $sValue - Value for encryption
     * @param string $sSecretKey - Secret key for encryption
     * @param string $sIv - Iv key
     * @return string
     */
    public static function AesEncrypt($sValue, $sSecretKey, $sIv)
    {
        if (!is_null($sSecretKey) && !is_null($sIv)) {
            $data = json_encode($sValue);
            $block_size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $pad = $block_size - (strlen($data) % $block_size);
            $data .= str_repeat(chr($pad), $pad);

            $result = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $sSecretKey, $data, MCRYPT_MODE_CBC, $sIv));
        } else {
            $result = $sValue;
        }

        return $result;
    }

    /**
     * Getting key pair
     * @return array
     */
    public static function generatePair()
    {
        defined('CRYPT_RSA_PRIVATE_FORMAT_PKCS1') or define('CRYPT_RSA_PRIVATE_FORMAT_PKCS1', 'CRYPT_RSA_PRIVATE_FORMAT_PKCS1');
        defined('CRYPT_RSA_PUBLIC_FORMAT_PKCS8') or define('CRYPT_RSA_PUBLIC_FORMAT_PKCS8', 'CRYPT_RSA_PUBLIC_FORMAT_PKCS8');

        $rsa = new RSA();
        $rsa->setPrivateKeyFormat(CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_PKCS8);

        return $rsa->createKey(2048);
    }

    /**
     * Encoding RSA data
     * @param string $data - Encoding data
     * @return string
     */
    public static function encodeRSAData($data)
    {
        defined('CRYPT_RSA_ENCRYPTION_PKCS8') or define('CRYPT_RSA_ENCRYPTION_PKCS8', 'CRYPT_RSA_ENCRYPTION_PKCS8');

        $rsa = new RSA();
        $rsa->loadKey(self::getPublicKey());
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS8);
        return $rsa->encrypt($data);
    }

    /**
     * Decode RSA data
     * @param string $data - Data for decode
     * @param string $privateKey - Key for decode
     * @return string
     */
    public static function decodeRSAData($data, $privateKey)
    {
        defined('CRYPT_RSA_ENCRYPTION_OAEP') or define('CRYPT_RSA_ENCRYPTION_OAEP', 'CRYPT_RSA_ENCRYPTION_OAEP');

        $privateKey = str_replace("\r\n", '', $privateKey);
        $rsa = new RSA();
        $rsa->loadKey($privateKey);
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_OAEP);
        $dataDecoded = base64_decode($data);
        $result = $rsa->decrypt($dataDecoded);
        return $result;
    }

    /**
     * Getting static model of API result
     * @param null|string $subKey - Name of model (if is NULL, then data is not saved in session)
     * @param array $postData - Data, for getting API result
     * @return null|static
     */
    public static function getModelInstance($subKey = null, $postData = array())
    {
        $session = Yii::$app->session;
        $screenData = $session['screenData'];
        $calledClass = get_called_class();

        if (empty($subKey)) {
            if (!isset($screenData[$calledClass])) {
                self::addToSession(array($calledClass => static::getData(null, $postData)));
            }
        } else {
            if (!isset($screenData[$calledClass]) || !isset($screenData[$calledClass][$subKey])) {
                $subData = isset(\Yii::$app->session['screenData'][$calledClass]) ? \Yii::$app->session['screenData'][$calledClass] : [];
                $subDataResult = static::getData($subKey, $postData);
                if (!is_null($subDataResult)) {
                    $subData[$subKey] = $subDataResult;
                    self::addToSession(array($calledClass => $subData));
                }
            }
        }

        $result = !empty($subKey) ? Yii::$app->session['screenData'][$calledClass][$subKey] : Yii::$app->session['screenData'][$calledClass];
        if ($calledClass != 'app\models\UserAccount' && $calledClass != 'app\models\Menu') {
            $sessionData = Yii::$app->session['screenData'];
            if (!empty($subKey) && isset($sessionData[$calledClass][$subKey])) {
                unset($sessionData[$calledClass][$subKey]);
            } elseif (isset($sessionData[$calledClass])) {
                unset($sessionData[$calledClass]);
            }
            Yii::$app->session['screenData'] = $sessionData;
        }
        return $result;
    }

    private function __clone()
    {
    }

    /**
     * Returned information about current session with API server, for getting result from API
     * @return array
     */
    public static function processSessionData()
    {
        $encryptionPair = AccountModel::generatePair();
        $publicKey = $encryptionPair['publickey'];
        $privateKey = $encryptionPair['privatekey'];
        $serverResult = self::requestToApi([
            'publickey' => $publicKey,
            'requestbody' => [
                'func_name' => 'createsession'
            ]
        ], true);

        if (isset($serverResult['sessionkey']) && $serverResult['sessioniv']) {
            $secretKey = self::decodeRSAData($serverResult['sessionkey'], $privateKey);
            $secretIv = self::decodeRSAData($serverResult['sessioniv'], $privateKey);
            $secretKey = substr_replace($secretKey, "", -1);
            $secretIv = substr_replace($secretIv, "", -1);
            $decodedKey = base64_decode($secretKey);
            $decodedIv = base64_decode($secretIv);
            $resultBody = (string)self::AesDecrypt($serverResult['resultbody'], $decodedKey, $decodedIv);
            $strEnd = strrpos($resultBody, '}');
            $resultBody = substr($resultBody, 0, $strEnd + 1);
            $result = json_decode($resultBody, true);
            $sessionHandle = $result['sessionhandle'];
        } else {
            $decodedKey = null;
            $decodedIv = null;
            $sessionHandle = isset($serverResult['resultbody']['sessionhandle']) ? $serverResult['resultbody']['sessionhandle'] : null;
        }

        return array('secretKey' => $decodedKey, 'secretIv' => $decodedIv, 'sessionhandle' => $sessionHandle);
    }

    public static function getSessionData()
    {
        if (empty(Yii::$app->session['screenData']['sessionData']['sessionhandle'])) {
            self::addToSession(['sessionData' => self::processSessionData()]);
        }

        return Yii::$app->session['screenData']['sessionData'];
    }

    /**
     * Getting result from API server
     * @param array $postData - Data, for getting API result
     * @param bool $skipEncryption - Set TRUE, if you want skip Encryption
     * @return bool|mixed|string
     */
    public static function requestToApi($postData, $skipEncryption = false)
    { //echo "in requestToApi <pre>"; echo json_encode($postData);
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, static::getSourceLink());

        $headers = array('Content-Type: application/json', 'Accept-Encoding: gzip,deflate');

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($curl, CURLOPT_VERBOSE, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($curl);

		//var_dump($response);

        if (empty($requestData)) {
            $response = substr($response, strpos($response, '{'));
            $response = json_decode($response, true);
        }
        curl_close($curl);

        $body = isset($response['resultbody']) ? $response['resultbody'] : null;
        if (!empty(Yii::$app->session['screenData']['sessionData']['sessionhandle'])) {
            $sessionData = Yii::$app->session['screenData']['sessionData'];

            if (!$skipEncryption && isset($body) && is_string($body)) {
                $secretKey = $sessionData['secretKey'];
                $secretIv = $sessionData['secretIv'];
                if ((!is_null($secretKey) && !is_null($secretIv))) {
                    $body = self::AesDecrypt($body, $secretKey, $secretIv);
                    $strEnd = strrpos($body, '}');
                    $body = substr($body, 0, $strEnd + 1);
                    $body = json_decode($body, true);
                    $response = $body;
                }
            } else {
                if (!isset($sessionData['secretKey']) || empty($sessionData['secretKey'])) {
                    $response = isset($response['resultbody']) ? $response['resultbody'] : $response;
                }
            }
        }

        if (isset($body['requestresult']) && strtolower($body['requestresult']) == 'unsuccessfully') {
            if (isset($body['extendedinfo']) && ($body['extendedinfo'] == 'Session Not Found') || ($body['extendedinfo'] == 'Invalid Session Handle')) {
                unset(Yii::$app->session['screenData']);
                Yii::$app->user->logout();
            }
            $response = null;
        }

		if (!empty($response['extendedinfo'])) {
            $session = Yii::$app->session;
            $extendedinfo = $session->get('extendedinfo') ? $session->get('extendedinfo') : [];
            if (is_array($response['extendedinfo']) && gettype($response['extendedinfo']) != 'string') {
                $result = array_merge($extendedinfo, $response['extendedinfo']);
                $session->set('extendedinfo',$result );
            }
        }

        return $response;
    }

    /**
     * Request to API server with sessionhandle
     * @param array $postData - Data, for getting API result
     * @return bool|mixed|string
     */
    public function processData($postData = [])
    {
        if (($sessionData = self::getSessionData()) && !empty($sessionData['sessionhandle'])) {
            if (isset($sessionData['secretKey']) && isset($sessionData['secretIv'])) {
                $postData = self::AesEncrypt($postData, $sessionData['secretKey'], $sessionData['secretIv']);
            }

            return self::requestToApi(['requestbody' => $postData, 'sessionhandle' => $sessionData['sessionhandle']]);
        }

        return null;
    }

    /**
     * Request to API server with prepare data
     * @param null|string $subKey
     * @param array $postData
     * @return null|static
     */
    protected static function getData($subKey = null, $postData = array())
    {
        $model = new static();

        $postData = array_merge(['func_name' => $model::$dataAction], $postData);

        $attributes = $model->processData($model::preparePostData($postData));
        if (!empty($attributes)) {
            foreach ($attributes as $attribute => $value) {
                $model->$attribute = $value;
            }
        } else {
            $model = null;
        }

        return $model;
    }

    /**
     * Prepare data for send request to API server
     * @param array $additionalPostData - additional data for request
     * @param null $funcName - function fo request
     * @return array
     */
    protected static function preparePostData($additionalPostData = array(), $funcName = null)
    {
        if ($funcName == UserAccount::$dataAction) {
            /** @var UserAccount $userData */
            $userData = UserAccount::getModelInstance();
            $postData = array(
                "uid" => (string)$userData->id,
                "upassword" => $userData->account_password,
            );
        } else {
            $postData = array();
        }

        return array_merge($postData, $additionalPostData);
    }

    /**
     * Cached data
     * @param $data
     */
    protected static function addToSession($data)
    {
        $existsData = isset(\Yii::$app->session['screenData']) ? \Yii::$app->session['screenData'] : [];
        \Yii::$app->session['screenData'] = array_merge($existsData, $data);
    }
}