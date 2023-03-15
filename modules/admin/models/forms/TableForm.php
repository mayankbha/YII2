<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class TableForm extends Model
{
    public $database_name;
    public $schema_name;
    public $table_name;
    public $type;
    public $name;
    public $ref_columns;
    public $ref_table_name;
    public $on_delete;
    public $on_update;
    public $check;
    public $pk;

    public $columns;
    public $constraints;

    public function init()
    {
        $this->columns = '';
        $this->constraints = '';
        parent::init();
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['table_name'], 'required', 'message' => Yii::t('app', 'Please fill out this field.')],
            [['database_name', 'schema_name', 'table_name', 'type', 'on_delete', 'on_update', 'check'], 'string'],
            [['pk'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'database_name' => Yii::t('app', 'Database Name'),
            'schema_name' => Yii::t('app', 'Schema Name'),
            'table_name' => Yii::t('app', 'Table Name'),
            'name' => Yii::t('app', 'Name'),
            'type' => Yii::t('app', 'Type'),
            'length' => Yii::t('app', 'Length'),
            'scale' => Yii::t('app', 'Scale'),
            'nullable' => Yii::t('app', 'Nullable'),
            'type' => Yii::t('app', 'Constraint Type'),
            'name' => Yii::t('app', 'Constraint Name'),
            'columns' => Yii::t('app', 'Columns'),
            'column' => Yii::t('app', 'Column'),
            'ref_columns' => Yii::t('app', 'Ref Columns'),
            'ref_table_name' => Yii::t('app', 'Reference Table Name'),
            //'ref_column_name' => Yii::t('app', 'Reference Column Name'),
            'on_delete' => Yii::t('app', 'On Delete'),
            'on_update' => Yii::t('app', 'On Update'),
            'check' => Yii::t('app', 'Check'),
        ];
    }
}