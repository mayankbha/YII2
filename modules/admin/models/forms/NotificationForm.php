<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 */

namespace app\modules\admin\models\forms;

use Yii;
use yii\base\Model;

class NotificationForm extends Model
{
    public $notify_name;
    public $notify_type;
    public $recipient_type;
    public $recipient_list;
    public $params;
    public $body;
    public $description;
    public $note;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['notify_name', 'notify_type', 'recipient_type', 'recipient_list', 'body'], 'required', 'message'=> Yii::t('app', 'Please fill out this field.')],
            [['params', 'description', 'note'], 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'notify_name' => Yii::t('app', 'Notify name'),
            'notify_type' => Yii::t('app', 'Notify type'),
            'recipient_type' => Yii::t('app', 'Recipient type'),
            'recipient_list' => Yii::t('app', 'Recipient list'),
            'body' => Yii::t('app', 'Body'),
            'params' => Yii::t('app', 'Parameters'),
            'description' => Yii::t('app', 'Description'),
            'note' => Yii::t('app', 'Note'),
        ];
    }
}