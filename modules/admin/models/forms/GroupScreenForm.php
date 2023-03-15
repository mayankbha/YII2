<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class GroupScreenForm extends Model
{
    public static $defaultSort = [
        'group_name' => SORT_ASC,
        'weight' => SORT_ASC
    ];

    public $pk;
    public $id;
    public $add;
    public $delete;
    public $edit;
    public $execute;
    public $group_name;
    public $inquire;
    public $menu_name;
    public $screen_name;
    public $screen_text;
    public $weight;
    public $copy;

    public $add_show;
    public $delete_show;
    public $edit_show;
    public $execute_show;
    public $inquire_show;
    public $copy_show;

    const BOOL_API_TRUE = 'Y';
    const BOOL_API_FALSE = 'N';

    public static $boolProperty = [
        self::BOOL_API_TRUE => 'Yes',
        self::BOOL_API_FALSE => 'No',
    ];

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['group_name', 'menu_name', 'screen_text', 'weight'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['id', 'weight'], 'integer'],
            [['screen_name'], 'string'],
            [['add', 'delete', 'edit', 'execute', 'inquire', 'copy'], 'boolean', 'trueValue' => self::BOOL_API_TRUE, 'falseValue' => self::BOOL_API_FALSE, 'strict' => false],
            [['add_show', 'delete_show', 'edit_show', 'execute_show', 'inquire_show', 'copy_show'], 'boolean', 'trueValue' => self::BOOL_API_TRUE, 'falseValue' => self::BOOL_API_FALSE, 'strict' => false]
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'add' => Yii::t('app', 'Add'),
            'delete' => Yii::t('app', 'Delete'),
            'edit' => Yii::t('app', 'Edit'),
            'group_name' => Yii::t('app', 'Group name'),
            'inquire' => Yii::t('app', 'Inquire'),
            'menu_name' => Yii::t('app', 'Menu name'),
            'screen_name' => Yii::t('app', 'Screen name'),
            'screen_text' => Yii::t('app', 'Screen text'),
            'weight' => Yii::t('app', 'Weight'),
            'execute' => Yii::t('app', 'Execute'),
            'copy'  => Yii::t('app', 'Copy'),
            'add_show' => Yii::t('app', 'Show add button'),
            'delete_show' => Yii::t('app', 'Show delete button'),
            'edit_show' => Yii::t('app', 'Show edit button'),
            'inquire_show' => Yii::t('app', 'Show inquire button'),
            'execute_show' => Yii::t('app', 'Show execute button'),
            'copy_show'  => Yii::t('app', 'Show copy button')
        ];
    }
}