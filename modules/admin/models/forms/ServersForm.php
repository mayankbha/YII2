<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class ServersForm extends Model
{
    public $address;
    public $port;
    public $description;
    public $note;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['address', 'port'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['description', 'note'], 'string'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'address' => Yii::t('app', 'IP Address'),
            'port' => Yii::t('app', 'Port'),
            'description' => Yii::t('app', 'Description'),
            'note' => Yii::t('app', 'Note'),
        ];
    }
}