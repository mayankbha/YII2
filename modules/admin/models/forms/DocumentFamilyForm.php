<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class DocumentFamilyForm extends Model
{
    public $pk;
    public $family_name;
    public $family_description;
    public $category;
    public $category_description;
    public $key_part_1;
    public $key_part_2;
    public $key_part_3;
    public $key_part_4;
    public $key_part_5;
    public $search_text;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['family_name', 'category'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['family_name', 'category'], 'string', 'max' => 128],
            [['family_description', 'category_description'], 'string', 'max' => 400],
            [['key_part_1', 'key_part_2', 'key_part_3', 'key_part_4', 'key_part_5'], 'string', 'max' => 40],
            [['search_text'], 'string', 'max' => 255],
            [['pk'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'family_name' => Yii::t('app', 'Family Name'),
            'family_description' => Yii::t('app', 'Family Description'),
            'category' => Yii::t('app', 'Category'),
            'category_description' => Yii::t('app', 'Description'),
            'key_part_1' => Yii::t('app', 'K.P. 1'),
            'key_part_2' => Yii::t('app', 'K.P. 2'),
            'key_part_3' => Yii::t('app', 'K.P. 3'),
            'key_part_4' => Yii::t('app', 'K.P. 4'),
            'key_part_5' => Yii::t('app', 'K.P. 5'),
            'search_text' => Yii::t('app', 'Search text'),
        ];
    }
}