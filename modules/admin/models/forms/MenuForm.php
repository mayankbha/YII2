<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class MenuForm extends Model
{
    public static $defaultSort = [
        'weight' => SORT_ASC,
        'menu_text' => SORT_ASC
    ];

    public $pk;
    public $id;
    public $group_name;
    public $menu_description;
    public $menu_name;
    public $menu_text;
    public $weight;
    public $menu_image;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['group_name', 'menu_description', 'menu_text', 'weight'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['id', 'weight'], 'integer'],
            [['menu_name', 'menu_image'], 'string']
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
            'menu_description' => Yii::t('app', 'Description'),
            'menu_name' => Yii::t('app', 'Menu name'),
            'menu_text' => Yii::t('app', 'Menu text'),
            'menu_image' => Yii::t('app', 'Menu icon'),
            'weight' => Yii::t('app', 'Weight'),
        ];
    }
}