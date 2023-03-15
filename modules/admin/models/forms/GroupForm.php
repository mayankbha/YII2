<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class GroupForm extends Model
{
    public $pk;
    public $id;
    public $group_name;
    public $group_description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['group_name', 'group_description'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['id'], 'integer'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'group_name' => Yii::t('app', 'Group name'),
            'group_description' => Yii::t('app', 'Group description'),
        ];
    }
}