<?php

namespace app\modules\chat\models\forms;

use app\modules\admin\models\Group;
use app\modules\chat\models\ChatRules;
use app\modules\chat\models\Room;
use Yii;
use yii\base\Model;
use yii\web\NotFoundHttpException;


class MessageForm extends Model
{
    public $roomId;
    public $message;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['roomId'], 'integer'],
            [['message'], 'string'],
            [['message'], 'required'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'message' => 'message',
            'roomId' => 'room id'
        ];
    }
}