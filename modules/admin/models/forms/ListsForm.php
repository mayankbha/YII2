<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class ListsForm extends Model
{
    public $description;
    public $entry_name;
    public $groups = '';
    public $list_name;
    public $note = '';
    public $products = '';
    public $restrict_code = '';
    public $pk;
    public $weight;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['entry_name', 'list_name'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['groups', 'note', 'products', 'restrict_code', 'description'], 'string'],
            [['weight'], 'integer']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'description' => Yii::t('app', 'Description'),
            'entry_name' => Yii::t('app', 'Entry name'),
            'list_name' => Yii::t('app', 'List name'),
            'groups' => Yii::t('app', 'Groups'),
            'note' => Yii::t('app', 'Note'),
            'products' => Yii::t('app', 'Products'),
            'restrict_code' => Yii::t('app', 'Restrict code'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }
}