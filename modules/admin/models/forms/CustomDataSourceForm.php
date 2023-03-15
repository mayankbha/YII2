<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class CustomDataSourceForm extends Model
{
    const RELATED_FUNCTION_DELIMITER = ';';
    const MAX_RELATED_FUNCTIONS = 3;

    public $id;
    public $func_name;
    public $func_type;
    public $func_table;
    public $func_descr;
    public $func_layout_type;
    public $func_direction_type;
    public $related_func;
    public $related_field;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['func_name', 'func_type', 'func_table', 'func_direction_type'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['func_descr', 'func_layout_type', 'related_func', 'related_field'], 'string'],
            [['related_field', 'related_func'], 'validateMaxRelatedField'],
            [['related_field', 'related_func'], 'validateCountRelatedField']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'func_name' => Yii::t('app', 'Name'),
            'func_type' => Yii::t('app', 'Function type'),
            'func_table' => Yii::t('app', 'Table'),
            'func_direction_type' => Yii::t('app', 'Direction type'),
            'func_descr' => Yii::t('app', 'Description'),
            'func_layout_type' => Yii::t('app', 'Layout type'),
            'related_func' => Yii::t('app', 'Related functions'),
            'related_field' => Yii::t('app', 'Related fields')
        ];
    }

    public function validateMaxRelatedField($attribute, $params, $validator)
    {
        $checkList = explode(self::RELATED_FUNCTION_DELIMITER, $this->$attribute);
        if (count($checkList) > self::MAX_RELATED_FUNCTIONS) {
            $validator->addError($this, $attribute, 'Maximum ' . self::MAX_RELATED_FUNCTIONS . ' {attribute}');
        }
    }

    public function validateCountRelatedField($attribute, $params, $validator)
    {
        $fieldList = explode(self::RELATED_FUNCTION_DELIMITER, $this->related_field);
        $funcList = explode(self::RELATED_FUNCTION_DELIMITER, $this->related_func);

        if (count($funcList) !== count($fieldList)) {
            $this->addError($attribute, 'The number of related functions does not match the number of related fields');
        }
    }
}