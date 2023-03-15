<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\AccountModel;

class Management extends AccountModel
{
    public static function getData($subKey = null, $postData = array())
    {
        $model = new static();
        return (($result = $model->processData(['func_name' => 'GetSDKInfo'])) && !empty($result)) ? $result['server_info'] : [];
    }

    public static function resetSoft() {
        $model = new static();
        return $model->processData(['func_name' => 'SoftReset']);
    }

    public static function getServiceInfo() {
        $model = new static();
        return $model->processData(['func_name' => 'GetSDKInfo']);
    }
}