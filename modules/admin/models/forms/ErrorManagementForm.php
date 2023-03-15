<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class ErrorManagementForm extends Model
{
    public $err_code;
    public $lib_name;
    public $func_name;
    public $language;
    public $body;
    public $description = '';
    public $note = '';
    public $params = '';
    public $pk;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['err_code', 'lib_name', 'func_name', 'language', 'body'], 'required'],
            [['description', 'note', 'params'], 'string']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'err_code' => Yii::t('app', 'Error Code'),
            'lib_name' => Yii::t('app', 'Library Name'),
            "func_name" => Yii::t('app', 'Function Name'),
            "language" => Yii::t('app', 'Language'),
            "body" => Yii::t('app', 'Error Message Body'),
            "description" => Yii::t('app', 'Description'),
            "note" => Yii::t('app', 'Notes/Remarks'),
            "params" => Yii::t('app', 'Parameters'),
        ];
    }
}