<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models;

use app\models\BaseModel;
use app\modules\admin\models\forms\ScreenForm;
use yii\helpers\ArrayHelper;

class Screen extends BaseModel
{
    public static $dataLib = 'CodiacSDK.AdminScreens.dll';
    public static $dataAction = 'GetScreenList';
    public static $formClass = ScreenForm::class;

    public static function getData($fieldList = [], $postData = [], $additionallyParam = [])
    {
        if (($model = parent::getData($fieldList, $postData, $additionallyParam)) && !empty($model->list)) {
            if (($groupScreens = GroupScreen::getData()) && !empty($groupScreens->list)) {
                $mapper = ArrayHelper::map($groupScreens->list, 'screen_name', 'menu_name');
                foreach($model->list as $key => $item) {
                    $model->list[$key]['menu_name'] = !empty($mapper[$item['screen_name']]) ? $mapper[$item['screen_name']] : null;
                }
            }
        }

        return $model;
    }

    public static function getModel($id)
    {
        if ($data = parent::getModel($id)) {
            $data->screen_tab_devices = explode(';', $data->screen_tab_devices);

            $data->screen_tab_template = base64_decode($data->screen_tab_template);
            $data->screen_tab_template = json_decode($data->screen_tab_template);
        }
        return $data;
    }


    //Prepare data for update and create
    protected static function prepareData($attributes, $method = null) {
        if ($method === 'setModel') {
            $attributes['screen_tab_name'] = preg_replace ("/[^a-zA-Z0-9]+/", "_", $attributes['screen_tab_text']);
        }

        $attributes['screen_tab_devices'] = implode(';', $attributes['screen_tab_devices']);
        $attributes['screen_tab_template'] = base64_encode($attributes['screen_tab_template']);

        ArrayHelper::remove($attributes, 'menu_name');

        return parent::prepareData($attributes);
    }

    /**
     * @param $template
     * @return mixed
     */
    public static function decodeTemplate($template)
    {
        $templateJson = base64_decode($template);
        $templateArray = json_decode($templateJson, true);

        return $templateArray;
    }
}