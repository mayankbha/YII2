<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class DocumentGroupForm extends Model
{
    const ACCESS_RIGHT_U = 'U';
    const ACCESS_RIGHT_R = 'R';
    const ACCESS_RIGHT_N = 'N';

    public static $access_list = [
        self::ACCESS_RIGHT_U => 'Full access',
        self::ACCESS_RIGHT_R => 'Read only',
        self::ACCESS_RIGHT_N => 'Access is denied'
    ];

    public $pk;
    public $access_right;
    public $document_category;
    public $document_family;
    public $group_description;
    public $group_name;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['group_name', 'access_right', 'document_category'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['group_name', 'document_family', 'document_category'], 'string', 'max' => 128],
            [['group_description'], 'string', 'max' => 400],
            [['access_right'], 'string', 'max' => 1],
            [['pk'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'group_name' => Yii::t('app', 'Group name'),
            'group_description' => Yii::t('app', 'Group description'),
            'document_family' => Yii::t('app', 'Document family'),
            'document_category' => Yii::t('app', 'Document category'),
            'access_right' => Yii::t('app', 'Access right'),
        ];
    }
}