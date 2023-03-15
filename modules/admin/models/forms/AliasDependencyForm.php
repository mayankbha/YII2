<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

use app\models\GetAliasList;

class AliasDependencyForm extends Model
{
    public $AliasType;
    public $AliasDatabaseTable = '';
    public $AliasTable = '';
    public $RequestTable = '';
    public $DependencyType = '';
    public $pk;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['AliasType', 'AliasDatabaseTable', 'RequestTable'], 'required'],
            [['AliasType', 'AliasDatabaseTable', 'AliasTable', 'RequestTable', 'DependencyType'], 'string']
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'AliasType' => Yii::t('app', 'Type'),
            "AliasDatabaseTable" => Yii::t('app', 'Database Table'),
            "RequestTable" => Yii::t('app', 'Request Table'),
            "DependencyType" => Yii::t('app', 'Dependency Type')
        ];
    }

}