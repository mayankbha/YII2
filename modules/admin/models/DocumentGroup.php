<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use Yii;
use app\models\BaseModel;
use app\modules\admin\models\forms\DocumentGroupForm;
use yii\helpers\ArrayHelper;

class DocumentGroup extends BaseModel
{
    const ACCESS_RIGHT_FULL = 'U';
    const ACCESS_RIGHT_READ = 'R';
    const ACCESS_RIGHT_DENIED = 'N';

    public static $dataLib = 'CodiacSDK.AdminGroups.dll';
    public static $dataAction = 'GetDocumentGroupList';
    public static $formClass = DocumentGroupForm::class;

    const CACHE_KEY = 'documentGroups';
    const CACHE_KEY_FAMILY = 'documentFamilies';

    public static function getAccessPermission($family, $category)
    {
        if (($groupsData = self::getGroups()) && !empty($groupsData[$family][$category])) {
            return $groupsData[$family][$category]['access_right'];
        }

        return self::ACCESS_RIGHT_DENIED;
    }

    public static function getGroups()
    {
        $session = Yii::$app->session;
        if (empty($session[self::CACHE_KEY])) {
            if (($settings = Yii::$app->getUser()->getIdentity()) && !empty($settings->document_group)) {
                $userGroups = explode(';', $settings->document_group);
                if (($serverGroups = self::getData(['group_name' => $userGroups])) && !empty($serverGroups->list)) {
                    $session[self::CACHE_KEY] = ArrayHelper::index($serverGroups->list, 'document_category', ['document_family']);
                }
            }
        }

        return !empty($session[self::CACHE_KEY]) ? $session[self::CACHE_KEY] : null;
    }

    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        $temp_key_data = [];
        $temp_data = [];

        if ($model = parent::getData($fieldList = [], $postData = [], $additionallyParam = [])) {
            if (!empty($model->list)) {
                foreach ($model->list as $key => $value) {
                    if (!empty($value['group_name'])) {
                        $model->list[$key]['pk'] = $value['group_name'];
                    }
                }

                foreach ($model->list as $i => $val) {
                    if (!in_array($val['group_name'], $temp_key_data)) {
                        $temp_key_data[$i] = $val['group_name'];
                        $temp_data[$i] = $val;
                    }
                }
            }
            $model->list = $temp_data;
        }

        return $model;
    }

    public static function getModels($pk)
    {
        $result = [];
        $model = parent::getData(['group_name' => [$pk]]);
        if (!empty($model->list)) {
            foreach ($model->list as $item) {
                $resultModel = new static::$formClass();
                if ($resultModel->load($item, '')) {
                    $result[] = $resultModel;
                }
            }
        }

        return $result;
    }
}