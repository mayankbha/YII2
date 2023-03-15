<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class AliasRelationshipForm extends Model
{
    public $ParentTable;
    public $ParentField;
    public $ChildTable;
    public $ChildField;
    public $pk;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['ParentTable', 'ParentField', 'ChildTable', 'ChildField'], 'required'],
            [['ParentTable', 'ParentField', 'ChildTable', 'ChildField'], 'string']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'ParentTable' => Yii::t('app', 'Parent Table'),
            "ParentField" => Yii::t('app', 'Parent Field'),
            "ChildTable" => Yii::t('app', 'Child Table'),
            "ChildField" => Yii::t('app', 'Child Field')
        ];
    }

}