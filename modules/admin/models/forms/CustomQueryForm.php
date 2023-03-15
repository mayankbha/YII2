<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class CustomQueryForm extends Model
{
    public $pk;
    public $query_name;
    public $query_value;
    public $query_params;
    public $query_pks;
    public $description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['query_name'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['query_value', 'description', 'query_params', 'query_pks'], 'string'],
            [['query_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'query_name' => Yii::t('app', 'Query name'),
            'query_value' => Yii::t('app', 'Query value'),
            'query_params' => Yii::t('app', 'Query params'),
            'query_pks' => Yii::t('app', 'Query PKs'),
            'description' => Yii::t('app', 'Description'),
        ];
    }
}