<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class TemplateForm extends Model
{
    public $id;
    public $lib_name;
    public $data_source;
    public $alias_table;
    public $alias_field;
    public $field_type;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['lib_name', 'data_source', 'alias_table', 'alias_field', 'field_type'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'lib_name' => Yii::t('app', 'Library name'),
            'data_source' => Yii::t('app', 'Function'),
            'alias_table' => Yii::t('app', 'Table name'),
            'alias_field' => Yii::t('app', 'Table column'),
            'field_type' => Yii::t('app', 'Column type'),
        ];
    }
}