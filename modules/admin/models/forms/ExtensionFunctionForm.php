<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class ExtensionFunctionForm extends Model
{
    public $id;
    public $datasource_lib;
    public $datasource_func;
    public $extension_lib;
    public $extension_func;
    public $extension_freemem_func;
    public $extension_type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['datasource_lib', 'datasource_func', 'extension_lib', 'extension_func', 'extension_freemem_func', 'extension_type'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'datasource_lib' => Yii::t('app', 'Data source library'),
            'datasource_func' => Yii::t('app', 'Data source function'),
            'extension_lib' => Yii::t('app', 'Extension library'),
            'extension_func' => Yii::t('app', 'Extension function'),
            'extension_freemem_func' => Yii::t('app', 'Extension free memory function'),
            'extension_type' => Yii::t('app', 'Extension type')
        ];
    }
}